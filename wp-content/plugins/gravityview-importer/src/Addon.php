<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

\GFForms::include_feed_addon_framework();

class Addon extends \GFAddOn {

	/**
	 * @var string Version number of the Add-On
	 */
	protected $_version = GV_IMPORT_ENTRIES_VERSION;

	/**
	 * @var string Gravity Forms minimum version requirement
	 */
	protected $_min_gravityforms_version = GV_IMPORT_ENTRIES_MIN_GF;

	/**
	 * @var string URL-friendly identifier used for form settings, add-on settings, text domain localization...
	 */
	protected $_slug = 'gk-gravityimport';

	/**
	 * @var string Relative path to the plugin from the plugins folder. Example "gravityforms/gravityforms.php"
	 */
	protected $_path = 'gravityview-importer/gravityview-importer.php';

	/**
	 * @var string Full path the plugin. Example: __FILE__
	 */
	protected $_full_path = GV_IMPORT_ENTRIES_FILE;

	/**
	 * @var string URL to the Gravity Forms website. Example: 'http://www.gravityforms.com' OR affiliate link.
	 */
	protected $_url = 'https://www.gravitykit.com';

	/**
	 * @var string Title of the plugin to be used on the settings page, form settings and plugins page. Example: 'Gravity Forms MailChimp Add-On'
	 */
	protected $_title = 'GravityImport';

	/**
	 * @var string Short version of the plugin title to be used on menus and other places where a less verbose string is useful. Example: 'MailChimp'
	 */
	protected $_short_title = 'GravityImport';

	/**
	 * @var array Members plugin integration. List of capabilities to add to roles.
	 */
	protected $_capabilities = array( 'manage_options', 'gravityforms_import_entries' );

	/**
	 * @var string A string or an array of capabilities or roles that have access to the settings page
	 */
	protected $_capabilities_settings_page = 'manage_options';

	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the form settings page
	 */
	protected $_capabilities_form_settings = array( 'manage_options', 'gravityforms_import_entries' );

	/**
	 * @var string The hook suffix for the app menu
	 */
	public $app_hook_suffix = 'gv_import';

	/**
	 * @var bool
	 */
	public $show_settings = true;

	/**
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * @var Addon
	 */
	private static $instance;

	/**
	 * @return Addon
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Get the current version #
	 *
	 * @return string
	 */
	public function get_version() {

		return $this->_version;
	}

	/**
	 * @return string
	 */
	public function get_full_path() {

		return $this->_full_path;
	}

	/**
	 * Register scripts
	 *
	 * @return array
	 */
	public function scripts() {

		$scripts = array();

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$scripts[] = array(
			'handle'  => 'gv_importer',
			'src'     => $this->get_base_url() . '/assets/js/admin{$script_debug}.js',
			'version' => $this->_version,
			'enqueue' => array(
				array(
					'admin_page' => array(
						'form_settings',
						'plugin_page',
					),
					'query'      => 'subview=gravityview-importer',
				),
			),
			'strings' => array(
				'nonce'               => wp_create_nonce( 'gv-import-ajax' ),
				'complete'            => esc_html__( 'Complete', 'gk-gravityimport' ),
				'cancel'              => esc_html__( 'Cancel', 'gk-gravityimport' ),
				'updated'             => esc_html__( 'Updated', 'gk-gravityimport' ),
				'column_header'       => esc_html__( '&hellip;will be added to this form field', 'gk-gravityimport' ),
				'hide_console'        => esc_html__( 'Hide Console', 'gk-gravityimport' ),
				'show_console'        => esc_html__( 'Show Console', 'gk-gravityimport' ),
				'wrapping_up'         => esc_html__( 'Wrapping up&hellip;', 'gk-gravityimport' ),
				'already_mapped'      => esc_html__( 'This field has already been mapped.', 'gk-gravityimport' ),
				'overwrite_posts'     => esc_html__( 'Warning: Existing post content will be overwritten by the imported data. Proceed?', 'gk-gravityimport' ),
				'overwrite_entry'     => esc_html__( 'Warning: Existing entry values will be overwritten by the imported data. Proceed?', 'gk-gravityimport' ),
				'field_mapping_empty' => esc_html__( 'No fields have been mapped. Please configure the field mapping before starting the import.', 'gk-gravityimport' ),
				'error_message'       => sprintf( esc_html__( 'There was an error on row %s.', 'gk-gravityimport' ), '{row}' ),
				'success_message'     => sprintf( esc_html__( 'Created %s from Row %s', 'gk-gravityimport' ), sprintf( esc_html__( 'Entry #%s', 'gk-gravityimport' ), '{entry_id}' ), '{row}' ),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Register styles used by the plugin
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array();

		$styles[] = array(
			'handle'  => $this->_slug . '-admin',
			'src'     => $this->get_base_url() . '/assets/css/admin.css',
			'version' => $this->_version,
			'enqueue' => array(
				array(
					'admin_page' => array(
						'form_settings',
						'plugin_page',
					),
					'query'      => 'subview=gravityview-importer',
				),
			),
		);

		/**
		 * Also enqueue on the Gravity Forms Import/Export page.
		 * Need to do this here because there's no `gf_export` check in Gravity Forms for the Import/Export page
		 *
		 * @see \GFAddon::_page_condition_matches
		 */
		$styles[] = array(
			'handle'  => $this->_slug . '-admin',
			'src'     => $this->get_base_url() . '/assets/css/admin.css',
			'version' => $this->_version,
			'enqueue' => array(
				array(
					'query' => 'page=gf_export&view=import_entries',
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

}

Addon::get_instance();
