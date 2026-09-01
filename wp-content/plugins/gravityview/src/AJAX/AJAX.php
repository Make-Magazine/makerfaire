<?php
/**
 * GravityView AJAX handler.
 *
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @package GravityKit\GravityView\AJAX
 */

namespace GravityKit\GravityView\AJAX;

use GravityKit\GravityView\Admin\TemplateFieldTransfer;
use GravityKit\GravityView\Permissions\Permissions;
use GV\Grid;
use GFExport;
use GFFormsModel;
use GravityView_Render_Settings;
use WXR_Parser;

use function _gravityview_process_posted_fields;
use function gravityview;
use function gravityview_get_sortable_fields;
use function gravityview_strip_whitespace;
use function rgpost;

/*
 * Each handler verifies the nonce via check_ajax_nonce() before reading $_POST,
 * which PHPCS can't trace across the call. Do NOT add wp_unslash()/sanitize:
 * values are already unslashed downstream (rgpost, stripslashes_deep,
 * _gravityview_process_posted_fields) and doing so would corrupt valid input.
 */
// phpcs:disable WordPress.Security.NonceVerification.Missing
class AJAX {

	/**
	 * Whether hooks have already been added.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	private static $hooks_added = false;

	function __construct() {
		if ( self::$hooks_added ) {
			return;
		}

		self::$hooks_added = true;

		// get field options
		add_action( 'wp_ajax_gv_field_options', [ $this, 'get_field_options' ] );

		// get available fields
		add_action( 'wp_ajax_gv_available_fields', [ $this, 'get_available_fields_html' ] );

		// get active areas
		add_action( 'wp_ajax_gv_get_active_areas', [ $this, 'get_active_areas' ] );

		// Create a new row.
		add_action( 'wp_ajax_gv_create_row', [ $this, 'create_row' ] );

		// get preset fields
		add_action( 'wp_ajax_gv_get_preset_fields', [ $this, 'get_preset_fields_config' ] );

		// get preset fields
		add_action( 'wp_ajax_gv_set_preset_form', [ $this, 'create_preset_form' ] );

		add_action( 'wp_ajax_gv_sortable_fields_form', [ $this, 'get_sortable_fields' ] );
	}

	/**
	 * Handle exiting the script (for unit testing)
	 *
	 * @since 1.15
	 *
	 * @param bool|false $mixed
	 *
	 * @return bool
	 */
	private function _exit( $mixed = null ) {
		/**
		 * Don't exit if we're running test suite.
		 *
		 * @since 1.15
		 */
		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) && DOING_GRAVITYVIEW_TESTS ) {
			return $mixed;
		}

		exit( $mixed );
	}

	/** -------- AJAX ---------- */

	/**
	 * Verify the nonce. Exit if not verified.
	 *
	 * @return bool|null Null if verified, false if not verified (in test mode).
	 */
	function check_ajax_nonce() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_ajaxviews' ) ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, return false.
		}

		return null;
	}

	/**
	 * Whether the current user can use the View builder.
	 *
	 * Gates on the View-editing capabilities via Permissions::can_access_discovery()
	 * — the same gate the View-builder discovery abilities use. A nonce is CSRF
	 * protection, not authorization, so this is checked in addition to the nonce.
	 *
	 * @return bool
	 */
	function user_can_edit_views() {
		return ( new Permissions() )->can_access_discovery();
	}

	/**
	 * AJAX action to get HTML markup for form(s) or template fields
	 * AJAX callback
	 *
	 * @return array|false|void Array of HTML markup, false on validation failure (in test mode), or void.
	 */
	function get_available_fields_html() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		$context = rgpost( 'context' );

		// Return markup for a single or multiple contexts
		if ( $context ) {
			$data = [
				esc_attr( $context ) => '',
			];
		} else {
			$data = [
				'directory'       => '',
				'edit'            => '',
				'single'          => '',
				'search-general'  => '',
				'search-advanced' => '',
			];
		}

		if ( is_array( rgpost( 'form_preset_ids' ) ) ) {
			$form_ids = rgpost( 'form_preset_ids' );
		} else {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		foreach ( $data as $context => $markup ) {
			ob_start();

			do_action( 'gravityview_render_field_pickers', $context, $form_ids );

			$data[ $context ] = trim( ob_get_clean() );
		}

		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) && DOING_GRAVITYVIEW_TESTS ) {
			return $data;
		}

		wp_send_json_success( $data );
	}

	/**
	 * Returns template active areas given a template ID
	 * AJAX callback
	 *
	 * Expects `template_id` and `form_id` in the POST request. When switching
	 * templates with an existing configuration, the request may also carry
	 * `current_template_id` (the outgoing template) and `gv_fields` (the
	 * serialized configuration, in the same format the Save flow posts); the
	 * configuration is then migrated into the new template's zones and the
	 * zones are rendered pre-populated.
	 *
	 * @since 3.0.0 Added `current_template_id` + `gv_fields` migration support and a capability check.
	 *
	 * @return string|false|void JSON response, false on validation failure (in test mode), or void.
	 */
	function get_active_areas() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		if ( empty( $_POST['template_id'] ) ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		$values           = null;
		$current_template = (string) \GV\Utils::_POST( 'current_template_id', '' );

		// isset(), not empty(): an explicitly posted empty configuration means
		// the user blanked the layout, so the new template must render blank
		// ($values = []) instead of falling back to the saved fields (null).
		if ( '' !== $current_template && isset( $_POST['gv_fields'] ) ) {
			$current_fields = _gravityview_process_posted_fields();

			$values = ( new TemplateFieldTransfer() )->transfer(
				(array) $current_fields,
				$current_template,
				(string) \GV\Utils::_POST( 'template_id' )
			);
		}

		ob_start();
		do_action(
			'gravityview_render_directory_active_areas',
			\GV\Utils::_POST( 'template_id' ),
			'directory',
			'',
			true,
			\GV\Utils::_POST( 'form_id', 0 ),
			$values
		);
		$response['directory'] = ob_get_clean();

		ob_start();
		do_action(
			'gravityview_render_directory_active_areas',
			\GV\Utils::_POST( 'template_id' ),
			'single',
			'',
			true,
			\GV\Utils::_POST( 'form_id', 0 ),
			$values
		);
		$response['single'] = ob_get_clean();

		$response = array_map( 'gravityview_strip_whitespace', $response );

		return $this->_exit( wp_json_encode( $response ) );
	}

	/**
	 * Returns the HTML for a new grid row.
	 *
	 * @since 2.31.0
	 *
	 * @return string|false|void JSON response, false on validation failure (in test mode), or void.
	 */
	public function create_row() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		if (
			empty( $_POST['zone'] )
			|| empty( $_POST['type'] )
			|| empty( $_POST['row_type'] )
		) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		$type = $_POST['type'] ?? 'widget';
		if ( 'widget' !== $type && empty( $_POST['template_id'] ?? '' ) ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		$template_id = $_POST['template_id'] ?? '';

		$row = Grid::prefixed(
			'field' === $type ? $template_id : '',
			static fn () => Grid::get_row_by_type( $_POST['row_type'] )
		);

		ob_start();

		do_action(
			'gravityview_render_active_areas',
			$template_id,
			$type,
			$_POST['zone'],
			[ $row ],
			[]
		);
		$response['row'] = ob_get_clean();

		return $this->_exit( wp_json_encode( $response ) );
	}

	/**
	 * Fill in active areas with preset configuration according to the template selected
	 *
	 * @return string|false|void JSON response, false on validation failure (in test mode), or void.
	 */
	function get_preset_fields_config() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		if ( empty( $_POST['template_id'] ) ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		// get the fields xml config file for this specific preset
		$preset_fields_path = apply_filters( 'gravityview_template_fieldsxml', [], $_POST['template_id'] );
		// import fields
		if ( ! empty( $preset_fields_path ) ) {
			$presets = $this->import_fields( $preset_fields_path );
		} else {
			$presets = [
				'widgets' => [],
				'fields'  => [],
			];
		}

		$template_id = esc_attr( $_POST['template_id'] );

		// template areas
		$template_areas_directory = apply_filters( 'gravityview_template_active_areas', [], $template_id, 'directory' );
		$template_areas_single    = apply_filters( 'gravityview_template_active_areas', [], $template_id, 'single' );

		// widget areas
		$default_widget_areas = \GV\Widget::get_default_widget_areas();

		ob_start();
		do_action(
			'gravityview_render_active_areas',
			$template_id,
			'widget',
			'header',
			$default_widget_areas,
			$presets['widgets']
		);
		$response['header'] = ob_get_clean();

		ob_start();
		do_action(
			'gravityview_render_active_areas',
			$template_id,
			'widget',
			'footer',
			$default_widget_areas,
			$presets['widgets']
		);
		$response['footer'] = ob_get_clean();

		ob_start();
		do_action(
			'gravityview_render_active_areas',
			$template_id,
			'field',
			'directory',
			$template_areas_directory,
			$presets['fields']
		);
		$response['directory'] = ob_get_clean();

		ob_start();
		do_action(
			'gravityview_render_active_areas',
			$template_id,
			'field',
			'single',
			$template_areas_single,
			$presets['fields']
		);
		$response['single'] = ob_get_clean();

		$response = array_map( 'gravityview_strip_whitespace', $response );

		gravityview()->log->debug( '[get_preset_fields_config] AJAX Response', [ 'data' => $response ] );

		return $this->_exit( wp_json_encode( $response ) );
	}

	/**
	 * Create the preset form requested before the View save
	 *
	 * @return string|false|void HTML response, false on validation failure (in test mode), or void.
	 */
	function create_preset_form() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		if ( empty( $_POST['template_id'] ) ) {
			gravityview()->log->error( 'Cannot create preset form; the template_id is empty.' );
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		// get the xml for this specific template_id
		$preset_form_xml_path = apply_filters( 'gravityview_template_formxml', '', $_POST['template_id'] );

		// import form
		$form = $this->import_form( $preset_form_xml_path );

		// get the form ID
		if ( false === $form ) {
			// send error to user
			gravityview()->log->error(
				'Error importing form for template id: {template_id}',
				[ 'template_id' => (int) $_POST['template_id'] ]
			);

			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		return $this->_exit( '<option value="' . esc_attr( $form['id'] ) . '" selected="selected">' . esc_html( $form['title'] ) . '</option>' );
	}

	/**
	 * Import Gravity Form XML or JSON
	 *
	 * @param string $xml_or_json_path Path to form XML or JSON file
	 *
	 * @return int|bool       Imported form ID or false
	 */
	function import_form( $xml_or_json_path = '' ) {
		gravityview()->log->debug( '[import_form] Import Preset Form. (File) {path}', [ 'path' => $xml_or_json_path ] );

		if ( empty( $xml_or_json_path ) || ! class_exists( 'GFExport' ) || ! file_exists( $xml_or_json_path ) ) {
			gravityview()->log->error(
				'Class GFExport or file not found. file: {path}',
				[ 'path' => $xml_or_json_path ]
			);

			return false;
		}

		// import form
		$forms = '';
		$result = GFExport::import_file( $xml_or_json_path, $forms );

		// GF <2.9.5 returns an integer while newer versions return an array with "form_ids" and "failed_forms" properties.
		$count = is_array( $result ) ? count( $result['form_ids'] ?? [] ) : $result;

		gravityview()->log->debug( '[import_form] Importing form (Result) {count}', [ 'count' => $count ] );
		gravityview()->log->debug( '[import_form] Importing form (Form) ', [ 'data' => $forms ] );

		if ( 1 != $count || empty( $forms[0]['id'] ) ) {
			gravityview()->log->error( 'Form Import Failed!' );

			return false;
		}

		// import success - return form id
		return $forms[0];
	}

	/**
	 * Returns field options - called by ajax when dropping fields into active areas
	 * AJAX callback
	 *
	 * @return string|false|void HTML response, false on validation failure (in test mode), or void.
	 */
	function get_field_options() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		if ( empty( $_POST['template'] ) || empty( $_POST['area'] ) || empty( $_POST['field_id'] ) || empty( $_POST['field_type'] ) ) {
			gravityview()->log->error( 'Required fields were not set in the $_POST request. ' );
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		// Fix apostrophes added by JSON response
		$_post = array_map( 'stripslashes_deep', $_POST );

		// Sanitize
		$_post = array_map( 'esc_attr', $_post );

		// The GF type of field: `product`, `name`, `creditcard`, `id`, `text`
		$input_type = isset( $_post['input_type'] ) ? esc_attr( $_post['input_type'] ) : null;
		$context    = isset( $_post['context'] ) ? esc_attr( $_post['context'] ) : null;

		$form_id  = empty( $_post['form_id'] ) ? null : $_post['form_id'];
		$response = GravityView_Render_Settings::render_field_options(
			$form_id,
			$_post['field_type'],
			$_post['template'],
			$_post['field_id'],
			$_post['field_label'],
			$_post['area'],
			$input_type,
			'',
			'',
			$context
		);

		$response = gravityview_strip_whitespace( $response );

		return $this->_exit( $response );
	}

	/**
	 * Given a View id, calculates the assigned form, and returns the form fields (only the sortable ones )
	 * AJAX callback
	 *
	 * @return string|false|void HTML response, false on validation failure (in test mode), or void.
	 */
	function get_sortable_fields() {
		if ( false === $this->check_ajax_nonce() ) {
			return false;
		}

		if ( ! $this->user_can_edit_views() ) {
			$this->_exit( false );

			return false; // If inside unit tests, which don't exit, don't continue.
		}

		$form = '';

		// if form id is set, use it, else, get form from preset
		if ( ! empty( $_POST['form_id'] ) ) {
			$form = (int) $_POST['form_id'];
		} // get form from preset
		elseif ( ! empty( $_POST['template_id'] ) ) {
			$form = self::pre_get_form_fields( $_POST['template_id'] );
		}

		$response = gravityview_get_sortable_fields( $form );

		$response = gravityview_strip_whitespace( $response );

		return $this->_exit( $response );
	}

	/**
	 * Get the form fields for a preset (no form created yet)
	 *
	 * @param string $template_id Preset template
	 *
	 * @return array|false
	 */
	static function pre_get_form_fields( $template_id = '' ) {
		if ( empty( $template_id ) ) {
			gravityview()->log->error( 'Template ID not set.' );

			return false;
		} else {
			$form_file = apply_filters( 'gravityview_template_formxml', '', $template_id );
			if ( ! file_exists( $form_file ) ) {
				gravityview()->log->error(
					'[{template_id}] form file does not exist: {path}.',
					[
						'template_id' => $template_id,
						'path'        => $form_file,
					]
				);

				return false;
			}
		}

		// Import logic from https://github.com/gravityforms/gravityforms/blob/11dc114df56e7f5116d7df1adc54000007c13ec5/export.php#L96 & https://github.com/gravityforms/gravityforms/blob/11dc114df56e7f5116d7df1adc54000007c13ec5/export.php#L106
		$forms_json = file_get_contents( $form_file );

		$forms = json_decode( $forms_json, true );

		if ( ! $forms ) {
			gravityview()->log->error( 'Could not read the {path} template file.', [ 'path' => $form_file ] );

			return false;
		}

		$form = GFFormsModel::convert_field_objects( $forms[0] );
		$form = GFFormsModel::sanitize_settings( $form );

		gravityview()->log->debug(
			'[pre_get_form_fields] Importing Form Fields for preset [{template_id}]. (Form)',
			[
				'template_id' => $template_id,
				'data'        => $form,
			]
		);

		return $form;
	}

	/**
	 * Import fields configuration from an exported WordPress View preset
	 *
	 * @param string $file path to file
	 *
	 * @return array       Fields config array (unserialized)
	 */
	function import_fields( $file ) {
		if ( empty( $file ) || ! file_exists( $file ) ) {
			gravityview()->log->error( 'Importing Preset Fields. File not found. (File) {path}', [ 'path' => $file ] );

			return false;
		}

		$parser  = new \WXR_Parser();
		$presets = $parser->parse( $file );

		if ( is_wp_error( $presets ) ) {
			gravityview()->log->error( 'Importing Preset Fields failed. Threw WP_Error.', [ 'data' => $presets ] );

			return false;
		}

		if ( empty( $presets['posts'][0]['postmeta'] ) || ! is_array( $presets['posts'][0]['postmeta'] ) ) {
			gravityview()->log->error(
				'Importing Preset Fields failed. Meta not found in file. {path}',
				[ 'path' => $file ]
			);

			return false;
		}

		gravityview()->log->debug( '[import_fields] postmeta', [ 'data' => $presets['posts'][0]['postmeta'] ] );

		$fields = $widgets = [];
		foreach ( $presets['posts'][0]['postmeta'] as $meta ) {
			switch ( $meta['key'] ) {
				case '_gravityview_directory_fields':
					$fields = maybe_unserialize( $meta['value'] );
					break;
				case '_gravityview_directory_widgets':
					$widgets = maybe_unserialize( $meta['value'] );
					break;
			}
		}

		gravityview()->log->debug( '[import_fields] Imported Preset (Fields)', [ 'data' => $fields ] );
		gravityview()->log->debug( '[import_fields] Imported Preset (Widgets)', [ 'data' => $widgets ] );

		return [
			'fields'  => $fields,
			'widgets' => $widgets,
		];
	}
}
