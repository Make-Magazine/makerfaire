<?php

namespace GravityKit\AdvancedFilter;

use GF_Query_Condition;
use GFAPI;
use GFCommon;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\ProcessMergeTagsVisitor;
use GravityKit\AdvancedFilter\QueryFilters\QueryFilters;
use GravityView_Extension;
use GV\Core as GravityViewCore;
use GV\Template_Context;
use GV\Utils;
use GV\View;
use GVCommon;

/**
 * The core functionality of Advanced Filters.
 * @final
 */
class Core extends GravityView_Extension {
	protected $_title = 'Advanced Filtering';

	protected $_version = GRAVITYKIT_ADVANCED_FILTERING_VERSION;

	protected $_min_gravityview_version = '2.0';

	/**
	 * @since 1.0.11
	 * @type int
	 */
	protected $_item_id = 30;

	protected $_path = GRAVITYKIT_ADVANCED_FILTER_PLUGIN_FILE;

	protected $_text_domain = 'gravityview-advanced-filter';

	/**
	 * @type string AJAX action to add or update entry rating
	 */
	const AJAX_ACTION_GET_FIELD_FILTERS = 'get_field_filters_ajax';

	/**
	 * @type string Field meta name for conditional logic
	 */
	const CONDITIONAL_LOGIC_META = 'conditional_logic';

	/**
	 * @type string Field meta name for output displayed when conditions are not met`
	 */
	const CONDITIONAL_LOGIC_FAIL_OUTPUT_META = 'conditional_logic_fail_output';

	/**
	 * @var GravityViewCore
	 */
	private $gravity_view;

	/**
	 * The singleton instance of this plugin.
	 * @since 3.0.0
	 * @var self
	 */
	private static $singleton;

	/**
	 * @inheritDoc
	 * @since 3.0.0
	 */
	public function __construct( GravityViewCore $gravity_view ) {
		if ( self::$singleton instanceof self ) {
			throw new \RuntimeException( 'Cannot instantiate Active Filters multiple times. Use ::get_instance() instead.' );
		}

		$this->gravity_view = $gravity_view;
		self::$singleton    = $this;

		parent::__construct();
	}

	/**
	 * Returns the Advanced Filters plugin singleton instance.
	 * @since 3.0.0
	 * @return self
	 */
	public static function get_instance(): self {
		if ( ! self::$singleton instanceof self ) {
			throw new \RuntimeException( 'Active Filters was not yet instantiated. Make sure to hook in after `gk/advanced-filters/initialized`.' );
		}

		return self::$singleton;
	}

	public function add_hooks() {

		add_action( 'gravityview_metabox_filter_after', [ $this, 'render_metabox' ] );

		// Admin_Views::add_scripts_and_styles() runs at 999
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 1100 );

		add_filter( 'gravityview_noconflict_scripts', [ $this, 'no_conflict_script_filter' ] );

		add_action( 'gravityview/view/query', [ $this, 'gf_query_filter' ], 10, 2 );

		add_filter( 'gform_filters_get_users', [ $this, 'created_by_get_users_args' ] );

		add_action( 'wp_ajax_' . self::AJAX_ACTION_GET_FIELD_FILTERS, [ __CLASS__, 'get_field_filters_ajax' ] );

		add_filter( 'gravityview_template_field_options', [ $this, 'modify_view_field_settings' ], 999, 5 );

		add_filter( 'gravityview/template/field/output', [ $this, 'conditionally_display_field_output' ], 10, 2 );

		add_filter( 'gk/query-filters/admin-capabilities', [ $this, 'add_admin_capabilities' ] );

		add_filter( 'admin_head', [ $this, 'fix_duplicate_page_conflict' ], 20 );
	}

	/**
	 * Add conditional logic fields to field settings
	 *
	 * @since 2.1
	 *
	 * @param string $template_id   Table slug
	 * @param float  $field_id      GF Field ID
	 * @param string $context       Context (e.g., single or directory)
	 * @param string $input_type    Input type (e.g., textarea, list, select, etc.)
	 *
	 * @param array  $field_options Array of field options
	 *
	 * @return array
	 */
	public function modify_view_field_settings( $field_options, $template_id, $field_id, $context, $input_type ) {
		if ( 'edit' === $context ) {
			return $field_options;
		}

		$strings = [
			'conditional_logic_label'             => esc_html__( 'Conditional Logic', 'gravityview-advanced-filter' ),
			'conditional_logic_label_desc'        => esc_html__( 'Only show the field if the configured conditions apply.', 'gravityview-advanced-filter' ),
			'conditional_logic_fail_output_label' => esc_html__( 'Empty Field Content', 'gravityview-advanced-filter' ),
			'conditional_logic_fail_output_desc'  => esc_html__( 'Display custom content when field value does not meet conditional logic', 'gravityview-advanced-filter' ),
		];

		$conditional_logic_container                  = <<<HTML
	<span class="gv-label">{$strings['conditional_logic_label']}</span>
	<span class="howto">{$strings['conditional_logic_label_desc']}</span>
	<div class="gv-field-conditional-logic"></div>
HTML;
		$field_options['conditional_logic_container'] = [
			'type'     => 'html',
			'desc'     => $conditional_logic_container,
			'group'    => 'visibility',
			'priority' => 10000,
		];

		$field_options['conditional_logic_fail_output'] = [
			'type'       => 'textarea',
			'label'      => $strings['conditional_logic_fail_output_label'],
			'desc'       => $strings['conditional_logic_fail_output_desc'],
			'tooltip'    => true,
			'article'    => [
				'id'  => '611420b6766e8844fc34f9a3',
				'url' => 'https://docs.gravitykit.com/article/775-field-conditional-logic-doesnt-hide-empty-fields',
			],
			'merge_tags' => 'force',
			'group'      => 'visibility',
			'priority'   => 10100,
		];

		$field_options[ self::CONDITIONAL_LOGIC_META ] = [
			'type'     => 'hidden',
			'group'    => 'visibility',
			'priority' => 10200,
		];

		return $field_options;
	}

	/**
	 * Increase the number of users displayed in the Advanced Filter Created By dropdown.
	 *
	 * @since 1.0.12
	 *
	 * @see   GFCommon::get_entry_info_filter_columns()
	 *
	 * @see   get_users()
	 *
	 * @param array $args Arguments used in get_users() query
	 *
	 * @return array Modified args - bump # of users up to 1000 and limit the fields fetched by query
	 */
	public function created_by_get_users_args( $args ) {
		if ( ! function_exists( 'gravityview' ) || ! $this->gravity_view->request->is_admin( '', 'single' ) ) {
			return $args;
		}

		$args['number'] = 1000;
		$args['fields'] = [ 'ID', 'user_login' ]; // The only fields needed by GF

		return $args;
	}

	/**
	 * Add the scripts to the no-conflict mode allow-list.
	 *
	 * @param array $scripts Array of script keys
	 *
	 * @return array          Modified array
	 */
	public function no_conflict_script_filter( $scripts ) {
		$scripts[] = 'gform_tooltip_init';
		$scripts[] = 'gform_field_filter';
		$scripts[] = 'gform_forms';

		return $scripts;
	}

	/**
	 * Modify search criteria
	 *
	 * @deprecated since 2.0 Use the 2.0 GF_Query filters instead
	 *
	 * @param int   $passed_view_id (optional)
	 *
	 * @param array $criteria       Existing search criteria array, if any
	 *
	 * @param array $form_ids       Form IDs for the search
	 *
	 * @return void
	 */
	function filter_search_criteria( $criteria, $form_ids = null, $passed_view_id = null ) {
		$this->gravity_view->log->error( 'The filter_search_criteria method is no longer functional. Should not be used.' );
	}

	/**
	 * Filters the \GF_Query with advanced logic.
	 *
	 * Drop-in for the legacy flat filters when \GF_Query is available.
	 *
	 * @param \GF_Query $query The current query object reference
	 * @param View      $view  The current view object
	 */
	public function gf_query_filter( $query, $view ): void {
		$filters = get_post_meta( $view->ID, '_gravityview_filters', true );

		$this->gravity_view->log->debug( 'Advanced filters raw:', [ 'data' => $filters ] );

		if ( ! is_array( $filters ) ) {
			$filters = [ $filters ];
		}

		$this->register_deprecated_filters( $view );

		$filters = apply_filters_deprecated(
			'gravityview/adv_filter/filters',
			[ $filters, $view ],
			'3.0.0',
			'gk/advanced-filters/filters'
		);

		/**
		 * @filter `gk/advanced-filters/filters` The filters to be applied to the query.
		 *
		 * @param  [in,out] array $filters The filter set.
		 * @param View $view The View.
		 */
		$filters = apply_filters( 'gk/advanced-filters/filters', $filters, $view );
		if ( ! $filters ) {
			$this->gravity_view->log->debug( 'No advanced filters.' );

			return;
		}

		$this->gravity_view->log->debug( 'Advanced filters:', $filters );

		try {
			$conditions = QueryFilters::create()
			                          ->with_form( $view->form->form )
			                          ->with_filters( $filters )
			                          ->get_query_conditions();
		} catch ( \Exception $e ) {
			return;
		}

		/**
		 * Grab the current clauses, and combine the parts as a new WHERE clause.
		 */
		$query_parts = $query->_introspect();
		$query->where( GF_Query_Condition::_and( $query_parts['where'], $conditions ) );
	}

	/**
	 * Store the filter settings in the `_gravityview_filters` post meta
	 *
	 * @param int $post_id Post ID
	 *
	 * @return void
	 */
	function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			return;
		}

		// validate post_type
		if ( ! isset( $_POST['post_type'] ) || 'gravityview' != $_POST['post_type'] ) {
			return;
		}

		$conditions = stripslashes_deep( Utils::_POST( 'gk-query-filters' ) );
		$filters    = json_decode( $conditions, true );
		if ( $filters || is_null( $filters ) ) {
			update_post_meta( $post_id, '_gravityview_filters', $filters );
		}
	}

	/**
	 * Enqueue scripts on Views admin
	 *
	 * @see /assets/js/advfilter-admin-views.js
	 *
	 * @param string $hook String like "widgets.php" passed by WordPress in the admin_enqueue_scripts filter
	 *
	 * @return void
	 */
	function admin_enqueue_scripts( $hook ) {
		global $post;

		// Don't process any scripts below here if it's not a GravityView page.
		if ( empty( $post->ID ) || 'gravityview' !== $post->post_type || 'post.php' !== $hook ) {
			return;
		}

		$form_id = gravityview_get_form_id( $post->ID );

		$form = ( new GFAPI() )->get_form( $form_id );

		if ( ! $form ) {
			return;
		}

		$filter_settings = self::get_field_filters( $post->ID );

		if ( $form_id && empty( $filter_settings['field_filters_complete'] ) ) {
			do_action( 'gravityview_log_error', '[print_javascript] Filter settings were not properly set', $filter_settings );

			return;
		}

		QueryFilters::enqueue_styles();
		QueryFilters::create()
		            ->with_form( $form )
		            ->enqueue_scripts( [
			            'fields'                  => rgar( $filter_settings, 'field_filters_complete', [] ),
			            'conditions'              => rgar( $filter_settings, 'init_filter_vars', [] ),
			            'target_element_selector' => '#entry_filters',
			            'variable_name'           => 'gkQueryFilters_advanced_filters',
		            ] );

		wp_enqueue_script( 'gravityview_adv_filter_admin', plugins_url( 'assets/js/advanced-filter.js', GRAVITYKIT_ADVANCED_FILTER_PLUGIN_FILE ), [ 'jquery' ], $this->_version );

		wp_localize_script( 'gravityview_adv_filter_admin', 'gkAdvancedFilter', [
			'fields_conditional_logic' => rgar( $filter_settings, 'field_filters_conditional_logic', [] ),
			'conditions'               => rgar( $filter_settings, 'init_filter_vars', [] ),
			'fetchFields'              => [
				'action' => self::AJAX_ACTION_GET_FIELD_FILTERS,
				'nonce'  => wp_create_nonce( 'gravityview-advanced-filter' ),
			],
		] );
	}

	/**
	 * Resolves conflicts with the Duplicate Page plugin in the View editor.
	 *
	 * This method prevents the Duplicate Page plugin from enqueuing scripts
	 * in the View editor, which were causing JavaScript errors due to React
	 * not being loaded on the page.
	 *
	 * @since 3.0.5
	 */
	public function fix_duplicate_page_conflict() : void {
		global $post;

		// Don't dequeue any scripts below here if it's not a GravityView page.
		if ( empty( $post->ID ) || 'gravityview' !== $post->post_type ) {
			return;
		}

		// Dequeue the Duplicate Page plugin's scripts.
		wp_dequeue_script( 'dt_duplicate_post_script' );
	}

	/**
	 * Render the HTML container that will be replaced by the Javascript
	 *
	 * @return void
	 */
	public function render_metabox( $settings = [] ) {
		include plugin_dir_path( GRAVITYKIT_ADVANCED_FILTER_PLUGIN_FILE ) . 'partials/metabox.php';
	}

	/**
	 * Get field filter options from Gravity Forms and modify them
	 *
	 * @see GFCommon::get_field_filter_settings()
	 *
	 * @param int|string|null $form_id
	 *
	 * @param int|string|null $post_id
	 *
	 * @return array
	 */
	public static function get_field_filters( $post_id = null, $form_id = null ) {
		$form_id = ( $post_id ) ? gravityview_get_form_id( $post_id ) : $form_id;

		$filters = get_post_meta( $post_id, '_gravityview_filters', true );

		$form = ( new GFAPI() )->get_form( $form_id );

		if ( ! $form ) {
			return [];
		}

		try {
			$query_filters = ( new QueryFilters() )
				->with_filters( (array) $filters )
				->with_form( $form );
		} catch ( \Exception $e ) {
			return [];
		}

		$field_filters    = apply_filters_deprecated(
			'gravityview/adv_filter/field_filters',
			[ $query_filters->get_field_filters(), $post_id ],
			'3.0.0',
			'gk/query-filters/field-filters'
		);
		$init_filter_vars = $query_filters->get_filters( true )->to_array();

		// For field conditional logic we use only default meta/properties and form fields
		$field_filters_conditional_logic = [];
		$_meta_and_properties_to_keep    = [
			'ip',
			'is_approved',
			'source_url',
			'date_created',
			'date_updated',
			'is_starred',
			'payment_status',
			'payment_date',
			'payment_amount',
			'transaction_id',
			'created_by',
		];

		foreach ( $field_filters as $filter ) {
			if ( ! in_array( $filter['key'], $_meta_and_properties_to_keep, true ) && ! is_numeric( $filter['key'] ) ) {
				continue;
			}

			$field_filters_conditional_logic[] = $filter;
		}

		return [
			'field_filters_complete'          => $field_filters,
			'field_filters_conditional_logic' => $field_filters_conditional_logic,
			'init_filter_vars'                => $init_filter_vars,
		];
	}

	/**
	 * Get field filter options via an AJAX request
	 *
	 * @since 2.0
	 * @return void|mixed Returns test data during tests
	 */
	public static function get_field_filters_ajax() {
		// Validate AJAX request
		$is_valid_nonce  = wp_verify_nonce( rgpost( 'nonce' ), 'gravityview-advanced-filter' );
		$is_valid_action = self::AJAX_ACTION_GET_FIELD_FILTERS === rgpost( 'action' );
		$has_permissions = GVCommon::has_cap( 'gravityforms_edit_forms' );
		$form_id         = (int) rgpost( 'form_id' );

		if ( ! $is_valid_action || ! $form_id || ! $has_permissions ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			$response = [ 'response' => ( ! $is_valid_nonce || ! $has_permissions ) ? 403 : 400 ];

			if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
				return $response;
			}

			wp_die( false, false, $response );
		}

		$filters = self::get_field_filters( null, $form_id );

		if ( ! empty( $filters['field_filters_complete'] ) ) {
			wp_send_json_success(
				[
					'fields_complete'          => rgar( $filters, 'field_filters_complete', [] ),
					'fields_conditional_logic' => rgar( $filters, 'field_filters_conditional_logic', [] ),
				]
			);
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Display field if conditional logic is met
	 *
	 * @since 2.1
	 *
	 * @param \GV\Template_Context $context      Template context
	 *
	 * @param string               $field_output Field output
	 *
	 * @return string
	 */
	public function conditionally_display_field_output( $field_output, $context ) {
		$filters = rgar( $context->field->as_configuration(), self::CONDITIONAL_LOGIC_META, false );

		if ( ! $filters || 'null' === $filters ) { // Empty conditions are a "null" string
			return $field_output;
		}

		$filters = json_decode( $filters, true );

		try {
			$query_filters = QueryFilters::create()
			                             ->with_form( $context->view->form->form )
			                             ->with_filters( (array) $filters );
		} catch ( \Exception $e ) {
			return $field_output;
		}

		$entry = $context->entry->as_entry();
		if ( $query_filters->meets_filters( $entry ) ) {
			return $field_output;
		}

		$conditional_logic_fail_output = rgar( $context->field->as_configuration(), self::CONDITIONAL_LOGIC_FAIL_OUTPUT_META, false );
		$conditional_logic_fail_output = ProcessMergeTagsVisitor::process_merge_tags( $conditional_logic_fail_output, $context->view->form->form, $entry );
		$conditional_logic_fail_output = $this->apply_short_codes( $conditional_logic_fail_output, $entry );

		$conditional_logic_fail_output = apply_filters_deprecated(
			'gravityview/field/value/empty',
			[
				$conditional_logic_fail_output,
				$context,
			],
			'3.0.0',
			'gk/advanced-filters/field/value/empty'
		);

		/**
		 * @filter `gk/advanced-filters/field/value/empty` What to display when this field is empty.
		 *
		 * @param string           $value   The value to display (Default: empty string)
		 * @param Template_Context $context The template context this is being called from.
		 */
		return apply_filters( 'gk/advanced-filters/field/value/empty', $conditional_logic_fail_output, $context );
	}

	/**
	 * Add additional capabilities to act as administrator.
	 *
	 * @since 3.0.0
	 *
	 * @param array $capabilities The original capabilities.
	 *
	 * @return array
	 */
	public function add_admin_capabilities( array $capabilities ): array {
		$capabilities[] = 'gravityview_edit_others_entries';;

		return array_unique( $capabilities );
	}

	/**
	 * Registers the old capabilities filter with the replacement filter.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view The view.
	 *
	 */
	private function register_deprecated_filters( ?View $view ): void {
		$capabilities_filtered = apply_filters_deprecated(
			'gravityview/adv_filter/admin_caps',
			[
				[
					'manage_options',
					'gravityforms_view_entries',
					'gravityview_edit_others_entries',
				],
				$view ? $view->ID : null,
			],
			'3.0.0',
			'gk/query-filters/admin-capabilities'
		);

		add_filter( 'gk/query-filters/admin-capabilities', function ( array $capabilities ) use ( $capabilities_filtered ): array {
			return array_unique( array_merge( $capabilities, $capabilities_filtered ) );
		} );

		$disabled_filters_filtered = apply_filters_deprecated(
			'gk/advanced-filter/disabled-filters',
			[
				[],
				$view,
			],
			'3.0.0',
			'gk/query-filters/filter/disable-filters'
		);

		add_filter( 'gk/query-filters/filter/disable-filters', function ( array $disabled_filters ) use ( $disabled_filters_filtered ): array {
			return array_unique( array_merge( $disabled_filters, $disabled_filters_filtered ) );
		} );
	}

	/**
	 * Replaces shortcodes in the provided content for a given entry.
	 *
	 * @since 3.0.3
	 *
	 * @param array   $entry   The entry object.
	 * @param ?string $content The content to update.
	 *
	 * @return ?string The updated content.
	 */
	private function apply_short_codes( $content, array $entry ) {
		// Add missing entry ID for Gravity PDF.
		add_filter(
			'gfpdf_gravityforms_shortcode_attributes',
			$callback = static function ( $attributes ) use ( $entry ) {
				if ( empty( $attributes['entry'] ?? '' ) ) {
					$attributes['entry'] = $entry['id'];
				}

				return $attributes;
			} );

		$content = do_shortcode( $content );

		remove_filter( 'gfpdf_gravityforms_shortcode_attributes', $callback );

		return $content;
	}
}
