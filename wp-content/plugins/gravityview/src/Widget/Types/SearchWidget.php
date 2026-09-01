<?php
/**
 * Search Bar widget type.
 *
 * PSR-4 migration of the legacy GravityView_Widget_Search class.
 *
 * @package GravityKit\GravityView\Widget\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Widget\Types;

use Closure;
use GF_Field;
use GF_Field_Date;
use GF_Query;
use GF_Query_Condition;
use GFFormsModel;
use GravityKit\GravityView\QueryFilters\Condition\Global_Search_Condition;
use GravityKit\GravityView\QueryFilters\QueryFilters;
use GravityKit\GravityView\Utils\Assets;
use GravityKit\GravityView\Utils\Path;
use GravityView_Admin_Views;
use GravityView_Ajax;
use GravityView_frontend;
use GravityView_Render_Settings;
use GravityView_View;
use GravityView_Widget_Search;
use GV\Form;
use GV\Frontend_Request;
use GV\GF_Form;
use GV\Grid;
use GV\Search\Fields\Search_Field;
use GV\Search\Fields\Search_Field_All;
use GV\Search\Fields\Search_Field_Gravity_Forms;
use GV\Search\Fields\Search_Field_Search_Mode;
use GV\Search\Fields\Search_Field_Submit;
use GravityKit\GravityView\Search\Querying\SearchFilterBuilder;
use GravityKit\GravityView\Search\Querying\SearchRequest;
use GravityKit\GravityView\Search\Querying\SearchScope;
use GV\Search\Search_Field_Collection;
use GravityKit\GravityView\Search\SearchPolicy;
use GV\View;
use RGCurrency;

use function gravityview;
use function gravityview_get_form_fields;
use function gravityview_get_form_id;
use function gravityview_get_input_id_from_id;
use function gravityview_get_permalink_query_args;
use function gravityview_get_view_id;
use function gravityview_sanitize_html_class;
use function gv_empty;
use function gv_map_deep;
use function rgar;

class SearchWidget extends \GV\Widget {

	protected $shortcode_name = 'gravityview_widget_search';

	public        $icon           = 'dashicons-search';

	public static $file;

	public static $instance;

	/**
	 * Holds the recorded areas for rendering the settings.
	 *
	 * @since 2.44
	 *
	 * @var array
	 */
	private array $area_settings = [];

	/**
	 * Contains the context for the search fields to render.
	 *
	 * @since 2.42
	 *
	 * @var array{template_id: string, form_id: int}
	 */
	private array $search_fields_context = [];

	/**
	 * Whether hooks have been added.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	private static $hooks_added = false;

	public function __construct() {
		$this->widget_id          = 'search_bar';
		$this->widget_description = esc_html__( 'Search form for searching entries.', 'gk-gravityview' );
		$this->widget_subtitle    = '';

		self::$instance = &$this;
		self::$file = Path::dir( 'Widget/Search' );

		$settings = [
			'search_fields_section' => [
				'type' => 'html',
				'desc' => Closure::fromCallable( [ $this, 'get_search_sections' ] ),
			],
		];

		if ( ! $this->is_registered() && ! self::$hooks_added ) {
			self::$hooks_added = true;

			// frontend - filter entries
			add_filter( 'gravityview_fe_search_criteria', [ $this, 'filter_entries' ], 10, 3 );
			add_action( 'gravityview/view/query', [ $this, 'gf_query_filter' ], 10, 3 );

			// frontend - add template path
			add_filter( 'gravityview_template_paths', [ $this, 'add_template_path' ] );

			// admin - add scripts - run at 1100 to make sure GravityView_Admin_Views::add_scripts_and_styles() runs first at 999
			add_action( 'admin_enqueue_scripts', [ $this, 'add_scripts_and_styles' ], 1100 );
			add_filter( 'gravityview_noconflict_scripts', [ $this, 'register_no_conflict' ] );

			// ajax - get the searchable fields
			add_action( 'wp_ajax_gv_searchable_fields', [ 'GravityView_Widget_Search', 'get_searchable_fields' ] );
			add_action( 'wp_ajax_nopriv_gv_searchable_fields', [ 'GravityView_Widget_Search', 'get_searchable_fields' ] );

			add_action( 'gravityview_search_widget_fields_after', [ $this, 'add_preview_inputs' ] );

			add_filter( 'gravityview/api/reserved_query_args', [ $this, 'add_reserved_args' ] );

			add_filter( 'gk/gravityview/search/available-fields', [ $this, 'add_form_search_fields' ], 0, 2 );
			add_filter( 'gk/gravityview/template/options/pre-context', [ $this, 'filter_search_field_options' ], 10, 7 );

			add_action( 'gravityview_render_search_active_areas', [ $this, 'render_search_active_areas' ], 10, 3 );
			add_action( 'gravityview_render_available_search_fields', [ $this, 'render_available_search_fields' ], 10, 2 );
			add_action( 'gk/gravityview/template/before-field-render', [ $this, 'record_search_field_context' ], 9, 5 );

			add_action( 'gk/gravityview/admin-views/row/before', [ $this, 'reset_area_recording' ], 10, 4 );
			add_action( 'gk/gravityview/admin-views/row/after', [ $this, 'render_area_settings' ], 10, 5 );
			add_action( 'gk/gravityview/admin-views/area/actions', [ $this, 'add_search_area_settings_button' ], 10, 6 );
			add_filter( 'gk/gravityview/template/options/pre-context', [ $this, 'filter_search_area_options' ], 10, 7 );

			add_filter( 'gk/gravityview/admin/widget-info', [ $this, 'add_widget_summary_info' ], 10, 4 );
		}

		parent::__construct( esc_html__( 'Search Bar', 'gk-gravityview' ), null, [], $settings );
	}

	/**
	 * @return GravityView_Widget_Search
	 */
	public static function getInstance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new GravityView_Widget_Search();
		}

		return self::$instance;
	}

	/**
	 * Add reserved arguments for $_GET specifically for links.
	 *
	 * @since 2.10
	 *
	 * @param array $args The existing arguments.
	 *
	 * @return array The reserved arguments.
	 */
	public function add_reserved_args( $args ) {
		$get = (array) ( $_GET ?? [] );

		/**
		 * Add additional reserved arguments for the search widget.
		 *
		 * @since 2.42
		 *
		 * @param array $args The reserved arguments.
		 */
		$additional_args = apply_filters( 'gk/gravityview/search/additional-reserved-args', [] );

		// Maintain required arguments and add additional arguments.
		$args = array_unique(
			array_merge(
				$args,
				SearchRequest::get_reserved_keys( $get ),
				$additional_args
			)
		);

		return $args;
	}

	/**
	 * Returns the search method
	 *
	 * @since 1.16.4
	 * @return string
	 */
	public function get_search_method() {
		return SearchRequest::method();
	}

	/**
	 * Get the input types available for different field types
	 *
	 * @since 1.17.5
	 *
	 * @return array [field type name] => (array|string) search bar input types
	 */
	public static function get_input_types_by_field_type() {
		/**
		 * Input Type groups
		 *
		 * @see admin-search-widget.js (getSelectInput)
		 */
		$input_types = [
			'text'        => [ 'input_text' ],
			'address'     => [ 'input_text' ],
			'number'      => [ 'input_text', 'number_range' ],
			'date'        => [ 'date', 'date_range' ],
			'entry_date'  => [ 'date_range', 'date' ], // `date_range` is the default for backwards compatibility.
			'boolean'     => [ 'single_checkbox' ],
			'select'      => [ 'select', 'radio', 'link' ],
			'multi'       => [ 'select', 'multiselect', 'radio', 'checkbox', 'link' ],
			'multiselect' => [ 'select', 'multiselect', 'radio', 'checkbox', 'link' ],
			'checkbox'    => [ 'select', 'multiselect', 'radio', 'checkbox', 'link' ],
			'submit'      => [ 'submit' ],
			'search_mode' => [ 'hidden', 'radio' ],

			// hybrids
			'created_by'  => [ 'select', 'radio', 'checkbox', 'multiselect', 'link', 'input_text' ],
			'multi_text'  => [ 'select', 'radio', 'checkbox', 'multiselect', 'link', 'input_text' ],
			'product'     => [ 'select', 'radio', 'link', 'input_text', 'number_range' ],
		];

		/**
		 * Change the types of search fields available to a field type.
		 *
		 * @see GravityView_Widget_Search::get_search_input_labels() for the available input types
		 *
		 * @param array $input_types Associative array: key is field `name`, value is array of GravityView input types (note: use `input_text` for `text`)
		 */
		$input_types = apply_filters( 'gravityview/search/input_types', $input_types );

		return $input_types;
	}

	public static function get_input_types_by_gf_field( $gf_field ) {
		if ( ! $gf_field instanceof GF_Field ) {
			return [ 'input_text' ];
		}

		$field_type = $gf_field->get_input_type();

		$input_types = self::get_input_types_by_field_type();

		// If the field type is not in the array, use the default input type
		if ( ! isset( $input_types[ $field_type ] ) ) {
			$field_type = 'input_text';
		}

		return $input_types[ $field_type ] ?? [ 'input_text' ];
	}

	/**
	 * Get labels for different types of search bar inputs
	 *
	 * @since 1.17.5
	 *
	 * @return array [input type] => input type label
	 */
	public static function get_search_input_labels() {
		/**
		 * Input Type labels l10n
		 *
		 * @see admin-search-widget.js (getSelectInput)
		 */
		$input_labels = [
			'input_text'      => esc_html__( 'Text', 'gk-gravityview' ),
			'date'            => esc_html__( 'Date', 'gk-gravityview' ),
			'select'          => esc_html__( 'Select', 'gk-gravityview' ),
			'multiselect'     => esc_html__( 'Select (multiple values)', 'gk-gravityview' ),
			'radio'           => esc_html__( 'Radio', 'gk-gravityview' ),
			'checkbox'        => esc_html__( 'Checkbox', 'gk-gravityview' ),
			'single_checkbox' => esc_html__( 'Checkbox', 'gk-gravityview' ),
			'link'            => esc_html__( 'Links', 'gk-gravityview' ),
			'date_range'      => esc_html__( 'Date range', 'gk-gravityview' ),
			'number_range'    => esc_html__( 'Number range', 'gk-gravityview' ),
			'submit'          => esc_html__( 'Submit Button', 'gk-gravityview' ),
			'hidden'          => esc_html__( 'Hidden Field', 'gk-gravityview' ),
		];

		/**
		 * Change the label of search field input types.
		 *
		 * @param array $input_types Associative array: key is input type name, value is label
		 */
		$input_labels = apply_filters( 'gravityview/search/input_labels', $input_labels );

		return $input_labels;
	}

	public static function get_search_input_label( $input_type ) {
		$labels = self::get_search_input_labels();

		return \GV\Utils::get( $labels, $input_type, false );
	}

	/**
	 * Add script to Views edit screen (admin)
	 *
	 * @param mixed $hook
	 */
	public function add_scripts_and_styles( $hook ) {
		global $pagenow;

		// Don't process any scripts below here if it's not a GravityView page or the widgets screen
		if ( ! gravityview()->request->is_admin( $hook, 'single' ) && ( 'widgets.php' !== $pagenow ) ) {
			return;
		}

		$script_min      = Assets::min();
		$script_source   = empty( $script_min ) ? '/source' : '';
		$script_relative = 'widgets/search-widget/js' . $script_source . '/admin-search-widget' . $script_min . '.js';

		wp_enqueue_script( 'gravityview_searchwidget_admin',
			Assets::url( $script_relative ),
			[ 'jquery', 'gravityview_views_scripts' ],
			filemtime( Assets::path( $script_relative ) ) );

		wp_localize_script(
			'gravityview_searchwidget_admin',
			'gvSearchVar',
			[
				'nonce'             => wp_create_nonce( 'gravityview_ajaxsearchwidget' ),
				'label_nofields'    => esc_html__( 'No search fields configured yet.', 'gk-gravityview' ),
				'label_addfield'    => esc_html__( 'Add Search Field', 'gk-gravityview' ),
				'label_label'       => esc_html__( 'Label', 'gk-gravityview' ),
				'label_searchfield' => esc_html__( 'Search Field', 'gk-gravityview' ),
				'label_inputtype'   => esc_html__( 'Input Type', 'gk-gravityview' ),
				'label_ajaxerror'   => esc_html__( 'There was an error loading searchable fields. Save the View or refresh the page to fix this issue.',
					'gk-gravityview' ),
				'input_labels'      => wp_json_encode( self::get_search_input_labels() ),
				'input_types'       => wp_json_encode( self::get_input_types_by_field_type() ),
			]
		);

		wp_localize_script(
			'gravityview_searchwidget_admin',
			'gvSearchWidgetText',
			[
				'global_search'             => esc_html__( 'Global search', 'gk-gravityview' ),
				'global_search_plus_field'  => esc_html__( 'Global search +1 field', 'gk-gravityview' ),
				// translators: %d is the number of fields.
				'global_search_plus_fields' => esc_html__( 'Global search +%d fields', 'gk-gravityview' ),
				'one_field'                 => esc_html__( '1 field', 'gk-gravityview' ),
				// translators: %d is the number of fields (1).
				'n_fields'                  => esc_html__( '%d fields', 'gk-gravityview' ),
				'matches_all'               => esc_html__( 'Matches All', 'gk-gravityview' ),
				'matches_any'               => esc_html__( 'Matches Any', 'gk-gravityview' ),
				'advanced'                  => esc_html__( 'Advanced', 'gk-gravityview' ),
				'separator'                 => esc_html_x( ' • ', 'Separator between search bar summary items', 'gk-gravityview' ),
				'is_rtl'                    => is_rtl(),
				// translators: %s is the search bar summary (e.g., "3 fields • Matches Any").
				'search_bar_config_label'   => esc_html__( 'Search Bar configuration: %s', 'gk-gravityview' ),
				'needs_configuration'       => esc_html__( '⚠️ Needs configuration', 'gk-gravityview' ),
			]
		);
	}

	/**
	 * Add admin script to the no-conflict scripts allowlist
	 *
	 * @param array $allowed Scripts allowed in no-conflict mode
	 *
	 * @return array Scripts allowed in no-conflict mode, plus the search widget script
	 */
	public function register_no_conflict( $allowed ) {
		$allowed[] = 'gravityview_searchwidget_admin';

		return $allowed;
	}

	/**
	 * Ajax
	 * Returns the form fields ( only the searchable ones )
	 *
	 * @return void
	 */
	public static function get_searchable_fields() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_ajaxsearchwidget' ) ) {
			exit( '0' );
		}

		$form = '';

		// Fetch the form for the current View
		if ( ! empty( $_POST['view_id'] ) ) {
			$form = gravityview_get_form_id( $_POST['view_id'] );
		} elseif ( ! empty( $_POST['formid'] ) ) {
			$form = (int) $_POST['formid'];
		} elseif ( ! empty( $_POST['template_id'] ) && class_exists( \GravityKit\GravityView\AJAX\AJAX::class ) ) {
			$form = \GravityKit\GravityView\AJAX\AJAX::pre_get_form_fields( $_POST['template_id'] );
		}

		// fetch form id assigned to the view
		$response = self::render_searchable_fields( $form );

		exit( $response );
	}

	/**
	 * Generates html for the available Search Fields dropdown
	 *
	 * @param int|array $form
	 * @param string    $current (for future use)
	 *
	 * @return string
	 */
	public static function render_searchable_fields( $form = null, $current = '' ) {
		if ( is_null( $form ) ) {
			return '';
		}

		$form_id = (int) ( $form['id'] ?? $form );
		// start building output

		$output = '<select class="gv-search-fields">';

		$search_fields = Search_Field_Collection::available_fields( $form_id );

		foreach ( $search_fields->all() as $field ) {
			if (
				$field instanceof Search_Field_Submit
				|| $field instanceof Search_Field_Search_Mode
			) {
				continue;
			}
			$custom_field = $field->to_legacy_format();

			$output .= sprintf(
				'<option value="%s" %s data-inputtypes="%s" data-placeholder="%s">%s</option>',
				$custom_field['field'],
				selected( $custom_field['field'], $current, false ),
				$custom_field['input'],
				$field->get_frontend_label(),
				$custom_field['title'],
			);
		}

		$output .= '</select>';

		return $output;
	}

	/**
	 * Assign an input type according to the form field type
	 *
	 * @see admin-search-widget.js
	 *
	 * @param string|int|float $field_id   Gravity Forms field ID
	 * @param string           $field_type Gravity Forms field type (also the `name` parameter of GravityView_Field
	 *                                     classes)
	 *
	 * @return string GV field search input type ('multi', 'boolean', 'select', 'date', 'text')
	 */
	public static function get_search_input_types( $field_id = '', $field_type = null ) {
		// @todo - This needs to be improved - many fields have . including products and addresses
		if ( false !== strpos( (string) $field_id, '.' ) && in_array( $field_type,
				[ 'checkbox' ] ) || in_array( $field_id, [ 'is_fulfilled' ] ) ) {
			$input_type = 'boolean'; // on/off checkbox
		} elseif ( in_array( $field_type,
			[ 'checkbox', 'post_category', 'multiselect', 'image_choice', 'multi_choice' ] ) ) {
			$input_type = 'multi'; // multiselect
		} elseif ( in_array( $field_id, [ 'payment_status' ] ) ) {
			$input_type = 'multi_text';
		} elseif ( in_array( $field_type, [ 'select', 'radio' ] ) ) {
			$input_type = 'select';
		} elseif ( in_array( $field_type, [ 'date' ] ) || in_array( $field_id, [ 'payment_date' ] ) ) {
			$input_type = 'date';
		} elseif ( in_array( $field_type, [ 'number', 'quantity', 'total' ] ) || in_array( $field_id,
				[ 'payment_amount' ] ) ) {
			$input_type = 'number';
		} elseif ( in_array( $field_type, [ 'product' ] ) ) {
			$input_type = 'product';
		} else {
			$input_type = 'text';
		}

		/**
		 * Modify the search form input type based on field type.
		 *
		 * @since 1.2
		 * @since 1.19.2 Added $field_id parameter
		 *
		 * @param string           $input_type Assign an input type according to the form field type. Defaults: `boolean`, `multi`, `select`, `date`, `text`
		 * @param string           $field_type Gravity Forms field type (also the `name` parameter of GravityView_Field classes)
		 * @param string|int|float $field_id   ID of the field being processed
		 */
		$input_type = apply_filters( 'gravityview/extension/search/input_type', $input_type, $field_type, $field_id );

		return $input_type;
	}

	/**
	 * Display hidden fields to add support for sites using Default permalink structure
	 *
	 * @since 1.8
	 * @return array Search fields, modified if not using permalinks
	 */
	public function add_no_permalink_fields( $search_fields, $object, $widget_args = [] ) {
		/** @global WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		// Support default permalink structure
		if ( false === $wp_rewrite->using_permalinks() ) {
			// By default, use current post.
			$post_id = 0;

			// We're in the WordPress Widget context, and an overriding post ID has been set.
			if ( ! empty( $widget_args['post_id'] ) ) {
				$post_id = absint( $widget_args['post_id'] );
			} // We're in the WordPress Widget context, and the base View ID should be used
			elseif ( ! empty( $widget_args['view_id'] ) ) {
				$post_id = absint( $widget_args['view_id'] );
			}

			$args = gravityview_get_permalink_query_args( $post_id );

			// Add hidden fields to the search form
			foreach ( $args as $key => $value ) {
				$search_fields[] = [
					'name'  => $key,
					'input' => 'hidden',
					'value' => $value,
				];
			}
		}

		return $search_fields;
	}

	/** --- Frontend --- */

	/**
	 * Calculate the search criteria to filter entries
	 *
	 * @param array $search_criteria       The search criteria
	 * @param int   $form_id               The form ID
	 * @param array $args                  Some args
	 *
	 * @param bool  $force_search_criteria Whether to suppress GF_Query filter, internally used in self::gf_query_filter
	 *
	 * @return array
	 */
	public function filter_entries( $search_criteria, $form_id = null, $args = [], $force_search_criteria = false ) {
		if ( ! $force_search_criteria && gravityview()->plugin->supports( \GV\Plugin::FEATURE_GFQUERY ) ) {
			/**
			 * If GF_Query is available, we can construct custom conditions with nested
			 * booleans on the query, giving up the old ways of flat search_criteria field_filters.
			 */

			return $search_criteria; // Return the original criteria, GF_Query modification kicks in later
		}

		$view          = \GV\View::by_id( \GV\Utils::get( $args, 'id' ) );
		$search_method = SearchRequest::method();

		gravityview()->log->debug(
			'Requested $_{method}: ',
			[
				'method' => $search_method,
				'data'   => 'post' === $search_method ? $_POST : $_GET,
			]
		);

		return $this->get_search_criteria( $view, $search_criteria );
	}

	/**
	 * Returns and updates the search criteria for a View.
	 *
	 * @since 2.55.0
	 *
	 * @param View|null $view            The View.
	 * @param array     $search_criteria The existing search criteria.
	 *
	 * @return array The updated search criteria.
	 */
	private function get_search_criteria( ?View $view, array $search_criteria = [] ): array {
		// A search applies only to the View it was performed on; another View on
		// the page keeps its own (unsearched) criteria.
		if ( $view && ! SearchScope::matches( $view ) ) {
			return $search_criteria;
		}

		$search_request = SearchRequest::from_request( new Frontend_Request(), $view );
		if ( ! $search_request ) {
			return $search_criteria;
		}

		return SearchFilterBuilder::to_search_criteria( $search_request, $view, $search_criteria );
	}

	/**
	 * Filters the \GF_Query with advanced logic.
	 *
	 * Drop-in for the legacy flat filters when \GF_Query is available.
	 *
	 * @param \GF_Query   $query   The current query object reference
	 * @param \GV\View    $this    The current view object
	 * @param \GV\Request $request The request object
	 */
	public function gf_query_filter( &$query, $view, $request ) {
		// A search is applied only to the View it was performed on, so a search
		// in one View on the page does not filter the others.
		if ( $view instanceof View && ! SearchScope::matches( $view ) ) {
			return;
		}

		// Check if this View is currently in the rendering process.
		// This helps identify Views embedded via [gravityview] shortcode.
		$is_view_rendering = \GV\View::is_rendering( $view->ID );

		// Handle Mock_Request (used for Views rendered via shortcode).
		if ( $request instanceof \GV\Mock_Request ) {
			// Mock requests indicate shortcode-rendered Views, allow filters.
			$is_view_rendering = true;
		}

		// For Views being rendered (e.g., via shortcode in Single Entry), always apply filters.
		if ( $is_view_rendering ) {
			// Continue processing filters below.
		} elseif ( $request && $request->is_entry() ) {
			// Don't apply search filters when viewing a single entry with a valid (non-mock) request.
			return;
		} elseif ( ! $request && gravityview()->request && gravityview()->request->is_entry() ) {
			// When $request is null but context is single entry, check the main View.
			$main_view = gravityview()->request->is_view();

			// Suppress filters if no main View, or if this is the main View.
			if ( ! $main_view || $view->ID === $main_view->ID ) {
				return;
			}
			// Otherwise it's a different (embedded) View -> continue processing filters.
		}

		$search_criteria = $this->get_search_criteria( $view );
		$form_id         = $view->form ? $view->form->ID : 0;
		$form_ids        = self::get_view_form_ids( $view ?? null );

		$search_conditions = [];
		$extra_conditions  = [];

		remove_filter( 'gravityview_fe_search_criteria', [ $this, 'filter_entries' ], 10 );

		$should_use_search_criteria = $this->should_use_search_criteria();
		if ( $should_use_search_criteria ) {
			/**
			 * Modifies the search criteria before entries are filtered.
			 *
			 * @since      1.0
			 * @deprecated 2.55.0 Use the {@see 'gravityview/view/query'} action to modify GF_Query conditions directly.
			 *
			 * @param array $search_criteria The search criteria array.
			 * @param int   $form_id         The form ID.
			 * @param array $atts            View settings as attributes.
			 */
			$search_criteria_filtered = \GravityView_Deprecated_Hook_Notices::apply_filters(
				'gravityview_fe_search_criteria',
				[ $search_criteria, $form_id, $view->settings->as_atts() ],
				'2.55',
				'gravityview/view/query'
			);

			if ( ! is_array( $search_criteria_filtered ) ) {
				// Invalid return from filter; ignore it.
				$should_use_search_criteria = false;
			} elseif ( $search_criteria_filtered === $search_criteria ) {
				// The filter didn't change anything.
				$should_use_search_criteria = false;
			} else {
				$search_criteria = $search_criteria_filtered;
			}
		}

		add_filter( 'gravityview_fe_search_criteria', [ $this, 'filter_entries' ], 10, 3 );

		if ( empty( $search_criteria['field_filters'] ) && empty( $search_criteria['start_date'] ) && empty( $search_criteria['end_date'] ) ) {
			return;
		}

		$search_request = SearchRequest::from_request( new Frontend_Request(), $view );
		if ( ! $search_request ) {
			return;
		}

		$global_words = $this->extract_global_search_words( $search_criteria );

		if ( ! $should_use_search_criteria ) {
			$query_filters = SearchFilterBuilder::to_query_filters( $search_request, $view );
		} else {
			// Legacy: Convert the filtered search_criteria through the Query Filters pipeline.
			$query_filters = SearchFilterBuilder::from_search_criteria( $search_criteria, $view );
		}

		$include_words = $global_words['include'];
		$exclude_words = $global_words['exclude'];

		if ( $include_words ) {
			$include_conditions = [];
			foreach ( $form_ids as $form_id ) {
				$include_conditions [] = Global_Search_Condition::include( $form_id, $include_words );
			}
			$extra_conditions[] = \GF_Query_Condition::_or( ...$include_conditions );
		}

		if ( $exclude_words ) {
			$exclude_conditions = [];
			foreach ( $form_ids as $form_id ) {
				$exclude_conditions[] = Global_Search_Condition::exclude( $form_id, $exclude_words );
			}
			$extra_conditions[] = \GF_Query_Condition::_and( ...$exclude_conditions );
		}

		if ( $query_filters ) {
			$condition = $query_filters->get_query_conditions();
			if ( $condition ) {
				$search_conditions[] = $condition;
			}
		}

		// Combine all conditions into the existing WHERE clause.
		$all_conditions = array_merge( $search_conditions, $extra_conditions );
		if ( ! $all_conditions ) {
			return;
		}

		$query_parts = $query->_introspect();
		$where       = GF_Query_Condition::_and(
			$query_parts['where'],
			...$all_conditions
		);
		$query->where( $where );
	}

	/**
	 * Get the Field Format form GravityForms
	 *
	 * @since 1.10
	 *
	 * @param GF_Field_Date $field The field object
	 *
	 * @return string Format of the date in the database
	 */
	public static function get_date_field_format( GF_Field_Date $field ) {
		$format     = 'm/d/Y';
		$datepicker = [
			'mdy'       => 'm/d/Y',
			'dmy'       => 'd/m/Y',
			'dmy_dash'  => 'd-m-Y',
			'dmy_dot'   => 'd.m.Y',
			'ymd_slash' => 'Y/m/d',
			'ymd_dash'  => 'Y-m-d',
			'ymd_dot'   => 'Y.m.d',
		];

		if ( ! empty( $field->dateFormat ) && isset( $datepicker[ $field->dateFormat ] ) ) {
			$format = $datepicker[ $field->dateFormat ];
		}

		return $format;
	}

	/**
	 * Format a date value
	 *
	 * @since 2.1.2
	 *
	 * @param string $format       Wanted formatted date
	 *
	 * @param string $value        Date value input
	 * @param string $value_format The value format. Default: Y-m-d
	 *
	 * @return string
	 */
	public static function get_formatted_date( $value = '', $format = 'Y-m-d', $value_format = 'Y-m-d' ) {
		$date = date_create_from_format( $value_format, $value );

		if ( empty( $date ) ) {
			gravityview()->log->debug( 'Date format not valid: {value}', [ 'value' => $value ] );

			return '';
		}

		return $date->format( $format );
	}

	/**
	 * Include this extension templates path
	 *
	 * @param array $file_paths List of template paths ordered
	 */
	public function add_template_path( $file_paths ) {
		// Index 100 is the default GravityView template path.
		$file_paths[102] = self::$file . 'templates/';

		return $file_paths;
	}

	/**
	 * Retrieves the search fields based on the (legacy) configuration.
	 *
	 * @since 2.42
	 *
	 * @param array     $widget_args        The widget's configuration.
	 * @param View|null $view               The View.
	 * @param array     $additional_context Any additional context.
	 *
	 * @return Search_Field_Collection The Search Field Collection.
	 */
	private function get_search_field_collection(
		array $widget_args,
		?View $view,
		array $additional_context = []
	): Search_Field_Collection {
		if ( isset( $widget_args['search_fields_section'] ) ) {
			return Search_Field_Collection::from_configuration(
				(array) $widget_args['search_fields_section'],
				$view,
				$additional_context
			);
		}

		return Search_Field_Collection::from_legacy_configuration(
			$widget_args,
			$view,
			$additional_context
		);
	}
	/**
	 * Renders the Search Widget
	 *
	 * @param array                       $widget_args
	 * @param string                      $content
	 * @param string|\GV\Template_Context $context
	 *
	 * @return void
	 */
	public function render_frontend( $widget_args, $content = '', $context = '' ) {
		if ( $context instanceof \GV\Template_Context ) {
			$view_id = $context->view->ID;
			$view    = $context->view;
		} else {
			$view_id = \GV\Utils::get( $widget_args, 'view_id', 0 );
			$view    = \GV\View::by_id( $view_id );
		}

		$additional_context           = compact( 'context', 'widget_args' );
		$additional_context['widget'] = $this;

		if ( ! $view ) {
			gravityview()->log->error( 'View not found', [ 'data' => $widget_args ] );

			return;
		}

		$search_fields = $this->get_search_field_collection( $widget_args, $view, $additional_context );

		if ( ! $search_fields->count() ) {
			gravityview()->log->debug( 'No search fields configured for widget:', [ 'data' => $widget_args ] );
			return;
		}

		$submit_field = $search_fields->by_type( Search_Field_Submit::class )->first();
		$search_clear = $submit_field && ( $submit_field->to_configuration()['search_clear'] ?? false );

		$search_mode_field = $search_fields->by_type( Search_Field_Search_Mode::class )->first();
		$search_mode       = $search_mode_field && ( $search_mode_field->to_configuration()['mode'] ?? 'any' );

		// Before rendering, we want to make sure the submit and search mode field are added.
		$search_fields = $search_fields->ensure_required_search_fields();

		if ( $search_fields->has_date_picker_field() ) {
			$this->enqueue_datepicker();
		}

		if ( $search_fields->has_date_range_field() ) {
			$this->enqueue_date_range_picker();
		}

		$search_layout = ( ! empty( $widget_args['search_layout'] ) ? $widget_args['search_layout'] . ' gv-search-rows' : 'rows' );
		$custom_class  = ! empty( $widget_args['custom_class'] ) ? $widget_args['custom_class'] : '';

		$data = [
			'datepicker_class'            => $this->get_datepicker_class(),
			'search_method'               => $this->get_search_method(),
			'search_layout'               => $search_layout,
			'search_mode'                 => ( ! empty( $widget_args['search_mode'] ) ? $widget_args['search_mode'] : $search_mode ),
			'search_clear'                => ( ! empty( $widget_args['search_clear'] ) ? $widget_args['search_clear'] : $search_clear ),
			'view_id'                     => $view_id,
			// Attribute the search to this View so it does not filter others on the page.
			'search_scope'                => SearchScope::is_enabled( $view ) ? (int) $view_id : 0,
			'form_id'                     => $view->form ? $view->form->ID : 0,
			'search_class'                => self::get_search_class( $custom_class, $search_layout, $view instanceof View ? $view : null ),
			'permalink_fields'            => $this->add_no_permalink_fields( [], $this, $widget_args ),
			'search_form_action'          => self::get_search_form_action(),
			'search_fields'               => $search_fields,
			'search_rows_search-general'  => Grid::get_rows_from_collection( $search_fields, 'search-general' ),
			'search_rows_search-advanced' => Grid::get_rows_from_collection( $search_fields, 'search-advanced' ),
		];

		GravityView_View::getInstance()->render( 'widget', 'search', false, $data );
	}

	/**
	 * Get the search class for a search form.
	 *
	 * @since 1.5.4
	 * @since 2.42
	 *
	 * @param string    $custom_class  Custom class to add to the search form
	 * @param string    $search_layout Search layout ("horizontal" or "vertical"). Default: "horizontal".
	 * @param View|null $view          The View the form belongs to, so `gv-is-search` reflects a search of this View.
	 *
	 * @return string Sanitized CSS class for the search form
	 */
	public static function get_search_class( $custom_class = '', $search_layout = 'horizontal', ?View $view = null ) {
		$search_class = 'gv-search-' . $search_layout;

		if ( ! empty( $custom_class ) ) {
			$search_class .= ' ' . $custom_class;
		}

		/**
		 * Modify the CSS class for the search form.
		 *
		 * @param string $search_class The CSS class for the search form
		 */
		$search_class = apply_filters( 'gravityview_search_class', $search_class );

		// Is there an active search being performed? Used by fe-views.js. When the
		// View is known, scope to it; only fall back to the legacy global check
		// when there is no View context, so one View's search doesn't light up
		// every search form on the page.
		$is_searching = $view instanceof View
			? gravityview()->request->is_search( $view )
			: ( gravityview()->request->is_search() || GravityView_frontend::getInstance()->isSearch() );

		$search_class .= $is_searching ? ' gv-is-search' : '';

		return gravityview_sanitize_html_class( $search_class );
	}

	/**
	 * Calculate the search form action
	 *
	 * @since 1.6
	 * @since 2.42
	 *
	 * @return string
	 */
	public static function get_search_form_action( $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			$gravityview_view = GravityView_View::getInstance();

			$post_id = $gravityview_view->getPostId() ? $gravityview_view->getPostId() : $gravityview_view->getViewId();
		}

		$url = add_query_arg( [], get_permalink( $post_id ) );

		$view_id = (int) gravityview_get_view_id();

		/**
		 * Override the search URL.
		 *
		 * @since 1.6
		 * @since 2.57.0 Added `$view_id` parameter.
		 *
		 * @param string $url  The search form action URL.
		 * @param int    $view_id The View ID being rendered. 0 if unavailable.
		 */
		return apply_filters( 'gravityview/widget/search/form/action', $url, $view_id );
	}

	/**
	 * Output the Clear Search Results button
	 *
	 * @since 1.5.4
	 */
	public static function the_clear_search_button() {
		_deprecated_function( __METHOD__,
			'2.55',
			'The button is now available in the templates as global $data[\'search_clear\']' );
	}

	/**
	 * Require the datepicker script for the frontend GV script.
	 *
	 * @deprecated 3.0.0 The Search Bar no longer enqueues `jquery-ui-datepicker`; the Query Filters picker is used instead. Method retained as a no-op for backward compatibility with external callers.
	 *
	 * @param array $js_dependencies Array of existing required scripts for the fe-views.js script.
	 *
	 * @return array The unchanged dependencies array.
	 */
	public function add_datepicker_js_dependency( $js_dependencies ) {
		return $js_dependencies;
	}

	/**
	 * Modify the array passed to wp_localize_script().
	 *
	 * @deprecated 3.0.0 The Search Bar no longer localizes a jQuery UI datepicker. Use `gk/query-filters/date-picker/translations` and `gk/query-filters/date-range-picker/translations` to localize the Query Filters picker. Method retained as a no-op for backward compatibility with external callers.
	 *
	 * @param array $js_localization The data padded to the Javascript file.
	 * @param array $view_data       View data array with View settings.
	 *
	 * @return array The unchanged localizations array.
	 */
	public function add_datepicker_localization( $localizations = [], $view_data = [] ) {
		return $localizations;
	}

	/**
	 * Enqueues the Query Filters date-range picker assets and the GravityView glue script.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_date_range_picker(): void {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		$presets_callback = [ $this, 'inject_date_range_presets' ];

		add_filter( 'gk/query-filters/date-range-picker/presets', $presets_callback, 10, 2 );

		QueryFilters::enqueue_date_range_picker(
			[
				'variable_name' => 'gvQueryFiltersDateRangePicker',
				'date_format'   => $this->get_datepicker_format(),
			]
		);

		remove_filter( 'gk/query-filters/date-range-picker/presets', $presets_callback, 10 );

		$this->enqueue_date_picker_glue();
	}

	/**
	 * Enqueues the Query Filters single-date picker assets and the GravityView glue script.
	 *
	 * Runs at most once per request.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_datepicker(): void {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		QueryFilters::enqueue_date_picker(
			[
				'variable_name' => 'gvQueryFiltersDatePicker',
				'date_format'   => $this->get_datepicker_format(),
			]
		);

		$this->enqueue_date_picker_glue();
	}

	/**
	 * Enqueues the shared glue script that mounts QF's date and date-range pickers.
	 *
	 * Both picker variants share a single QF bundle handle, so this method
	 * registers the glue script exactly once with that shared dependency.
	 *
	 * @since 3.0.0
	 */
	private function enqueue_date_picker_glue(): void {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		wp_enqueue_script(
			'gravityview-date-picker',
			Assets::url( 'js/gv-date-range-picker' . Assets::min() . '.js' ),
			[ 'jquery', 'gk-query-filters-pickers' ],
			GV_PLUGIN_VERSION,
			true
		);
	}

	/**
	 * Adds GravityView-specific date-range presets on top of Query Filters' defaults.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed              $presets Existing presets from Query Filters; coerced to an array.
	 * @param \DateTimeInterface $now     Current date-time supplied by Query Filters.
	 *
	 * @return array Presets including the GravityView additions.
	 */
	public function inject_date_range_presets( $presets, \DateTimeInterface $now ): array {
		$presets = is_array( $presets ) ? $presets : [];
		$year    = (int) $now->format( 'Y' );

		$week_start = (int) get_option( 'start_of_week', 0 );
		$now_dow    = (int) $now->format( 'w' );
		$delta      = ( $now_dow - $week_start + 7 ) % 7;
		$week_begin = ( clone $now )->modify( sprintf( '-%d days', $delta ) );
		$week_end   = ( clone $week_begin )->modify( '+6 days' );

		$gv_presets = [
			[
				'label' => esc_html__( 'This Week', 'gk-gravityview' ),
				'range' => [
					'start' => $week_begin->format( 'Y-m-d' ),
					'end'   => $week_end->format( 'Y-m-d' ),
				],
			],
			[
				'label' => esc_html__( 'This Year', 'gk-gravityview' ),
				'range' => [
					'start' => sprintf( '%d-01-01', $year ),
					'end'   => sprintf( '%d-12-31', $year ),
				],
			],
			[
				'label' => esc_html__( 'Last Year', 'gk-gravityview' ),
				'range' => [
					'start' => sprintf( '%d-01-01', $year - 1 ),
					'end'   => sprintf( '%d-12-31', $year - 1 ),
				],
			],
		];

		// The last four completed quarters, oldest first, relative to $now.
		$quarter_ranges  = [
			1 => [ '01-01', '03-31' ],
			2 => [ '04-01', '06-30' ],
			3 => [ '07-01', '09-30' ],
			4 => [ '10-01', '12-31' ],
		];
		$current_quarter = (int) ceil( ( (int) $now->format( 'n' ) ) / 3 );

		for ( $offset = 4; $offset >= 1; $offset-- ) {
			$q_number = $current_quarter - $offset;
			$q_year   = $year;
			while ( $q_number < 1 ) {
				$q_number += 4;
				--$q_year;
			}

			$gv_presets[] = [
				/* translators: 1: quarter number (1-4), 2: four-digit year. */
				'label' => sprintf( esc_html__( 'Q%1$d (%2$d)', 'gk-gravityview' ), $q_number, $q_year ),
				'range' => [
					'start' => sprintf( '%d-%s', $q_year, $quarter_ranges[ $q_number ][0] ),
					'end'   => sprintf( '%d-%s', $q_year, $quarter_ranges[ $q_number ][1] ),
				],
			];
		}

		$merged = array_merge( $presets, $gv_presets );

		/**
		 * Modifies the full date range preset list after GravityView's presets have been merged with the Query Filters defaults.
		 *
		 * Each preset is an array with `label` (string) and `range` (array with `start` and `end` keys, `Y-m-d` formatted).
		 *
		 * @since  3.0.0
		 *
		 * @param array              $merged The merged preset list (Query Filters defaults + GravityView additions).
		 * @param \DateTimeInterface $now    The current date, used to build relative ranges.
		 */
		$filtered = apply_filters( 'gk/gravityview/search/date-range-picker/presets', $merged, $now );

		return is_array( $filtered ) ? $filtered : $merged;
	}

	private function get_datepicker_class() {
		/**
		 * @filter `gravityview_search_datepicker_class`
		 * Modify the CSS class for the datepicker, used by the CSS class is used by Gravity Forms' javascript to determine the format for the date picker. The `gv-datepicker` class is required by the GravityView datepicker javascript.
		 *
		 * @param string $css_class CSS class to use. Default: `gv-datepicker datepicker mdy` \n
		 *                          Options are:
		 *                          - `mdy` (mm/dd/yyyy)
		 *                          - `dmy` (dd/mm/yyyy)
		 *                          - `dmy_dash` (dd-mm-yyyy)
		 *                          - `dmy_dot` (dd.mm.yyyy)
		 *                          - `ymd_slash` (yyyy/mm/dd)
		 *                          - `ymd_dash` (yyyy-mm-dd)
		 *                          - `ymd_dot` (yyyy.mm.dd)
		 */
		$datepicker_class = apply_filters(
			'gravityview_search_datepicker_class',
			'gv-datepicker datepicker ' . $this->get_datepicker_format(),
		);

		return $datepicker_class;
	}

	/**
	 * Retrieve the datepicker format.
	 *
	 * @see https://docs.gravitykit.com/article/115-changing-the-format-of-the-search-widgets-date-picker
	 *
	 * @param bool $date_format Whether to return the PHP date format or the datepicker class name. Default: false.
	 *
	 * @return string The datepicker format placeholder, or the PHP date format.
	 */
	private function get_datepicker_format( $date_format = false ) {
		return $date_format
			? SearchPolicy::get_date_php_format()
			: SearchPolicy::get_date_format_key();
	}

	/**
	 * If previewing a View or page with embedded Views, make the search work properly by adding hidden fields with
	 * query vars
	 *
	 * @since 2.2.1
	 *
	 * @return void
	 */
	public function add_preview_inputs() {
		global $wp;

		if ( ! is_preview() || ! current_user_can( 'publish_gravityviews' ) ) {
			return;
		}

		// Outputs `preview` and `post_id` variables
		foreach ( $wp->query_vars as $key => $value ) {
			printf( '<input type="hidden" name="%s" value="%s" />', esc_attr( $key ), esc_attr( $value ) );
		}
	}

	/**
	 * Adds search fields for a specific form.
	 *
	 * @since 2.42
	 *
	 * @param Search_Field[] $search_fields The fields.
	 * @param int            $form_id       The form ID.
	 *
	 * @return Search_Field[] The update fields.
	 */
	public function add_form_search_fields( array $search_fields, int $form_id ): array {
		if ( ! $form_id ) {
			return $search_fields;
		}

		$fields = gravityview_get_form_fields( $form_id, true, true );

		/**
		 * Modify the fields that are displayed as searchable in the Search Bar dropdown.
		 *
		 * @since 1.17
		 * @see   gravityview_get_form_fields() Used to fetch the fields
		 * @see   GravityView_Widget_Search::get_search_input_types See this method to modify the type of input types allowed for a field
		 *
		 * @param array $fields  Array of searchable fields, as fetched by gravityview_get_form_fields()
		 * @param int   $form_id The form ID.
		 */
		$fields = apply_filters( 'gravityview/search/searchable_fields', $fields, $form_id );

		$blocklist_field_types = apply_filters(
			'gravityview_blocklist_field_types',
			[ 'fileupload', 'post_image', 'post_id', 'section' ]
		);
		$blocklist_sub_fields  = apply_filters(
			'gravityview_blocklist_sub_fields',
			[ 'image_choice', 'multi_choice' ]
		);

		foreach ( $fields as $id => $field ) {
			if (
				in_array( $field['type'], $blocklist_field_types, true )
				|| ( in_array( $field['type'], $blocklist_sub_fields, true ) && null !== $field['parent'] )
			) {
				continue;
			}

			$field['id']      = $id;
			$field['form_id'] = $form_id;

			$field_instance = Search_Field_Gravity_Forms::from_field( $field );
			if ( ! $field_instance ) {
				continue;
			}

			$search_fields[] = $field_instance;
		}

		return $search_fields;
	}

	/**
	 * Returns all the searchable fields for a View in the legacy format.
	 *
	 * @since 2.42
	 *
	 * @param View $view The View.
	 *
	 * @internal Do not depend on this method.
	 *
	 * @return array{field: string, label:string, input_type:string}[] The searchable fields in the legacy format.
	 */
	final public function get_search_fields( View $view ): array {
		$search_fields = [];
		$collection    = $this->get_search_field_collection( $this->configuration->all(), $view );

		foreach ( $collection->all() as $field ) {
			$search_fields[] = $field->to_legacy_format();
		}

		return $search_fields;
	}

	/**
	 * Returns the configured search fields for a View, merged across its Search Bar widgets.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view The View.
	 *
	 * @return Search_Field_Collection The configured search fields, with their saved settings.
	 */
	public static function get_configured_search_fields( View $view ): Search_Field_Collection {
		$widget_id = ( new self() )->get_widget_id();
		$widgets   = $view->widgets->by_id( $widget_id );

		// Embedded Views may have a cleared widget collection; reload from the raw configuration.
		if ( 0 === $widgets->count() ) {
			$config  = (array) \GVCommon::get_directory_widgets( $view->ID );
			$widgets = \GV\Widget_Collection::from_configuration( $config )->by_id( $widget_id );
		}

		$collection = Search_Field_Collection::from_configuration( [] );
		foreach ( $widgets->all() as $widget ) {
			if ( ! $widget instanceof self ) {
				continue;
			}

			foreach ( $widget->get_search_field_collection( $widget->configuration->all(), $view )->all() as $field ) {
				$collection->add( $field );
			}
		}

		return $collection;
	}

	/**
	 * Adds search field settings when search field options are rendered.
	 *
	 * @since 3.1.0
	 *
	 * @param array  $options     The options.
	 * @param string $field_type  The object type (`field`, `widget`, `search`, or a type name).
	 * @param string $template_id The template ID.
	 * @param string $field_id    The field ID.
	 * @param string $context     The context.
	 * @param string $input_type  The input type.
	 * @param int    $form_id     The form ID.
	 *
	 * @return array The filtered options.
	 */
	public function filter_search_field_options( $options, $field_type, $template_id, $field_id, $context, $input_type, $form_id ) {
		if ( 'search' !== $field_type ) {
			return $options;
		}

		return $this->set_search_field_options( $options, $template_id, $field_id, $context, $input_type, $form_id );
	}

	/**
	 * Adds search area settings when area options are rendered.
	 *
	 * @since 3.1.0
	 *
	 * @param array  $options     The options.
	 * @param string $field_type  The object type (`field`, `widget`, `search`, or a type name).
	 * @param string $template_id The template ID.
	 * @param string $field_id    The field ID.
	 * @param string $context     The context.
	 * @param string $input_type  The input type.
	 * @param int    $form_id     The form ID.
	 *
	 * @return array The filtered options.
	 */
	public function filter_search_area_options( $options, $field_type, $template_id, $field_id, $context, $input_type, $form_id ) {
		if ( 'area' !== $field_type && 'area' !== $input_type ) {
			return $options;
		}

		return $this->add_search_area_settings( $options, $template_id, $field_id );
	}

	/**
	 * Add the settings for this field.
	 *
	 * @since 2.42
	 *
	 * @param array      $options     The original options.
	 * @param string     $template_id The template ID.
	 * @param string     $field_id    The field ID.
	 * @param string     $context     The area ID.
	 * @param string     $input_type  The (optional) input type.
	 * @param string|int $form_id     The form ID.
	 *
	 * @return array The updated options.
	 */
	final public function set_search_field_options(
		$options = [],
		$template_id = '',
		$field_id = '',
		$context = '',
		$input_type = '',
		$form_id = 0
	): array {
		$search_field = Search_Field_Collection::get_field_by_field_id( (int) $form_id, (string) $field_id );
		if ( ! $search_field ) {
			return $options;
		}

		return $search_field->merge_options( $options );
	}

	/**
	 * Renders the search areas in the settings field.
	 *
	 * @since 2.42
	 *
	 * @param array $field The field configuration.
	 *
	 * @return string
	 */
	private function get_search_sections( array $field ): string {
		global $post;

		$directory_entries_template = $this->search_fields_context['rendering']['template_id'] ?? 'default_table';

		// If no value is present, check if we have a legacy configuration.
		if ( null === ( $field['value'] ?? null ) ) {
			$search_fields = Search_Field_Collection::from_legacy_configuration(
				$this->search_fields_context['settings'] ?? [],
				View::from_post( $post )
			);

			if ( $search_fields->count() > 0 ) {
				// Set the legacy configuration on the field value as the Search Fields configuration.
				$field['value'] = $search_fields->to_configuration();
			}
		}

		ob_start();
		?>

		<div data-search-fields="<?php echo esc_attr( $field['name'] ?? '' ); ?>">
			<div class="gv-section">
				<h4><?php esc_html_e( 'Search fields shown', 'gk-gravityview' ); ?></h4>

				<div class="search-active-fields">
					<?php
					do_action(
						'gravityview_render_search_active_areas',
						$directory_entries_template,
						'search-general',
						$field
					);
					?>
				</div>

				<h4>
					<?php esc_html_e( 'Advanced Search fields shown', 'gk-gravityview' ); ?>
					<span>
						<?php
						esc_html_e(
							'If any Advanced Search fields exist, a link will show to toggle them.',
							'gk-gravityview'
						);
						?>
					</span>
				</h4>

				<div class="search-advanced-active-fields">
					<?php
					do_action(
						'gravityview_render_search_active_areas',
						$directory_entries_template,
						'search-advanced',
						$field
					);
					?>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render the search areas.
	 *
	 * @since 2.42
	 *
	 * @param string                          $template_id The current slug of the selected View template.
	 * @param string                          $zone        Either 'search-general' or 'search-advanced'.
	 * @param array{name:string, value:mixed} $data        The search field data.
	 */
	public function render_search_active_areas( string $template_id, string $zone, array $data ): void {
		global $post;
		$admin_views = GravityView_Admin_Views::get_instance();

		$fields    = $data['value'] ?? null;
		$rows      = [ Grid::get_row_by_type( '100' ) ];
		$name      = $data['name'] ?? null;
		$has_value = null !== $fields;

		$is_new = 'auto-draft' === get_post_status( $post );

		if ( $has_value ) {
			$collection = Search_Field_Collection::from_configuration( $fields );
			$rows       = Grid::get_rows_from_collection( $collection, $zone );
		} elseif ( $is_new && 'search-general' === $zone ) {
			$area_key = key( $rows[0] );
			$zone_100 = $zone . '_' . ( $rows[0][ $area_key ][0]['areaid'] ?? 'top' );

			$fields = [
				$zone_100 => [
					Grid::uid() => ( new Search_Field_All() )->to_configuration(),
					Grid::uid() => ( new Search_Field_Submit() )->to_configuration(),
					Grid::uid() => ( new Search_Field_Search_Mode() )->to_configuration(),
				],
			];
		}
		?>

		<div data-grid-connect="search" data-grid-context="<?php echo esc_attr($zone); ?>" class="gv-grid gv-grid-pad gv-grid-border" id="search-<?php echo $zone; ?>-fields">
			<?php
			$type       = 'search';
			$is_dynamic = true;

			echo '<div class="gv-grid-rows-container">';
			ob_start();
			$admin_views->render_active_areas( $template_id, $type, $zone, $rows, $fields );
			$content = ob_get_clean();

			// replace input names.
			echo str_replace(
				[ sprintf( 'name="%ss[', $type ), 'name="areas[' ],
				[ sprintf( 'name="%s[', $name ), sprintf( 'name="%s[', $name ) ],
				$content
			);
			echo '</div>';

			/**
			 * Allows additional content after the zone was rendered.
			 *
			 * @filter `gk/gravityview/admin/view/after-zone`
			 *
			 * @param string $template_id Template ID.
			 * @param string $type        The zone type (field or widget).
			 * @param string $zone        Current View zone: `directory`, `single`, `edit`, `search-general`, or `search-advanced`.
			 * @param bool   $is_dynamic  Whether the zone is dynamic.
			 */
			do_action( 'gk/gravityview/admin-views/view/after-zone', $template_id, $type, $zone, $is_dynamic );
			?>
		</div>
		<?php
	}

	/**
	 * Render html for displaying available search fields.
	 *
	 * @since 2.42
	 */
	public function render_available_search_fields( ?int $form_id = 0, ?string $section = null ): void {
		global $post;

		if ( ! $form_id ) {
			$view = View::by_id( $post->ID ?? 0 );
			if ( ! $view instanceof View || ! $view->form instanceof GF_Form ) {
				return;
			}
			$form_id = $view->form->ID ?? 0;
		}

		$search_fields = Search_Field_Collection::available_fields( $form_id, $section );
		if ( ! $search_fields->count() ) {
			return;
		}

		foreach ( $search_fields as $search_field ) {
			echo $search_field;
		}
	}

	/**
	 * Records the rendering context of a search field about to be rendered.
	 *
	 * @since 2.42
	 *
	 * @param string $field_type Either 'widget', 'field' or 'search'.
	 * @param string $key        The key of the settings field.
	 * @param array  $option     The configuration of the settings field.
	 * @param array  $settings   All the values for the current item being rendered.
	 * @param array  $rendering  Extra rendering context added to the action.
	 */
	public function record_search_field_context( string $field_type, string $key, array $option, array $settings, array $rendering ): void {
		if (
			'widget' !== $field_type
			|| 'search_fields_section' !== $key
		) {
			return;
		}

		$this->search_fields_context = [
			'settings'  => $settings,
			'rendering' => $rendering,
		];
	}

	/**
	 * Resets the recorded areas for the next row.
	 *
	 * @since 2.44
	 *
	 * @param bool   $is_dynamic  Whether the area is dynamic.
	 * @param string $template_id The template ID.
	 * @param string $type        The object type (widget or field).
	 * @param string $zone        The render zone.
	 */
	public function reset_area_recording( $is_dynamic, $template_id, $type, $zone ): void {
		if ( 'search' !== $type ) {
			return;
		}

		$this->area_settings = [];
	}

	/**
	 * Renders a "Clear all fields" button in the View configuration.
	 *
	 * @since 2.44
	 *
	 * @param array  $area        The area.
	 * @param string $type        The type.
	 * @param array  $values      The values in the area.
	 * @param bool   $is_dynamic  Whether the zone is dynamic.
	 * @param string $template_id The template ID.
	 * @param string $zone        The zone.
	 */
	public function add_search_area_settings_button( $area, $type, $values, $is_dynamic, $template_id, $zone ): void {
		if ( 'search' !== $type ) {
			return;
		}

		$area['settings'] = $values[ $zone . '_' . $area['areaid'] ]['area_settings'] ?? [];
		// Record the area for rendering the settings after the row.
		$this->area_settings[] = $area;

		printf(
			'<a role="button" href="javascript:void(0);" class="gv-search-area-settings" data-areaid="%s" title="%s"><i class="dashicons dashicons-admin-generic"></i></a>',
			esc_attr( $zone . '_' . $area['areaid'] ),
			esc_attr__( 'Configure Area Settings', 'gk-gravityview' ),
		);
	}

	/**
	 * Registers the area settings for the search fields.
	 *
	 * @since 2.44
	 *
	 * @param array  $settings    The area settings.
	 * @param string $template_id The template ID.
	 * @param string $field_id    The Field ID.
	 *
	 * @return array
	 */
	public function add_search_area_settings( $settings, $template_id, $field_id ): array {
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}

		if ( 'area_settings' !== $field_id ) {
			return $settings;
		}

		$settings['layout'] = [
			'type'    => 'select',
			'label'   => __( 'Arrange Fields:', 'gk-gravityview' ),
			'choices' => [
				'column' => esc_html__( 'Stacked (vertical)', 'gk-gravityview' ),
				'row'    => esc_html__( 'Side by side (horizontal)', 'gk-gravityview' ),
			],
			'value'   => 'column',
		];

		$settings['search_columns'] = [
			'type'    => 'select',
			'label'   => __( 'Number of Columns:', 'gk-gravityview' ),
			'choices' => [
				'0' => esc_html__( 'Auto (flexible)', 'gk-gravityview' ),
				'1' => esc_html__( '1 Column', 'gk-gravityview' ),
				'2' => esc_html__( '2 Columns', 'gk-gravityview' ),
				'3' => esc_html__( '3 Columns', 'gk-gravityview' ),
				'4' => esc_html__( '4 Columns', 'gk-gravityview' ),
				'5' => esc_html__( '5 Columns', 'gk-gravityview' ),
				'6' => esc_html__( '6 Columns', 'gk-gravityview' ),
				'7' => esc_html__( '7 Columns', 'gk-gravityview' ),
				'8' => esc_html__( '8 Columns', 'gk-gravityview' ),
			],
			'value'   => '0',
		];

		return $settings;
	}

	/**
	 * Renders the settings for a search area.
	 *
	 * @since 2.44
	 *
	 * @param bool   $is_dynamic  Whether the area is dynamic.
	 * @param View   $view        The View.
	 * @param string $template_id The template ID.
	 * @param string $type        The object type (widget or field).
	 * @param string $zone        The render zone.
	 */
	public function render_area_settings( $is_dynamic, $view, $template_id, $type, $zone ): void {
		if ( 'search' !== $type || ! $this->area_settings ) {
			return;
		}

		$html = '';

		foreach ( $this->area_settings as $area ) {
			if ( ! isset( $area['areaid'] ) ) {
				continue;
			}

			$settings = GravityView_Render_Settings::render_field_options(
				0,
				'area',
				$template_id,
				'area_settings',
				esc_html__( 'Column', 'gk-gravityview' ),
				$zone . '_' . $area['areaid'],
				null,
				'area_settings',
				$area['settings'] ?? [],
				'area_settings',
				[
					'label' => esc_html__( 'Column Settings', 'gk-gravityview' ),
				]
			);

			// Remove no options indicator to avoid disabling the search widget settings icon.
			$settings = str_replace( GravityView_Render_Settings::NO_OPTIONS, '', $settings );

			$html .= sprintf(
				'<div class="area-settings-container" data-areaid="%s">%s</div>',
				$zone . '_' . $area['areaid'],
				$settings
			);
		}

		if ( $html ) {
			printf( '<div style="display:none;" class="area-settings-wrapper">%s</div>', $html );
		}

		// Reset areas for next rendering.
		$this->area_settings = [];
	}

	/**
	 * Adds summary information placeholder to the Search Bar widget in the admin.
	 *
	 * The actual summary is generated dynamically by JavaScript to ensure real-time
	 * updates when fields are added/removed. This method just provides the placeholder
	 * element that JS will populate.
	 *
	 * @since 2.57.0
	 *
	 * @param array  $field_info_items The current info items.
	 * @param string $widget_id        The widget ID.
	 * @param array  $settings         The widget settings.
	 * @param array  $item             The widget item data.
	 *
	 * @return array The modified info items.
	 */
	public function add_widget_summary_info( array $field_info_items, string $widget_id, array $settings, array $item ): array {
		if ( $this->get_widget_id() !== $widget_id ) {
			return $field_info_items;
		}

		// If $settings is empty, this is a picker widget (not yet added to the View).
		// Keep the original description for picker widgets.
		if ( empty( $settings ) ) {
			return $field_info_items;
		}

		// Return an empty placeholder element - JS will populate the summary dynamically.
		// This ensures real-time updates when fields are added/removed in the dialog.
		return [
			[
				'value' => '',
				'class' => 'gv-search-widget-summary',
			],
		];
	}

	/**
	 * Checks whether userland code has hooked into the deprecated search criteria filter.
	 *
	 * @since 2.55.0
	 *
	 * @return bool Whether userland filters exist on the deprecated hook.
	 */
	private function should_use_search_criteria(): bool {
		return (bool) has_filter( 'gravityview_fe_search_criteria' );
	}

	/**
	 * Extracts global search words (keyless field_filters) from search criteria.
	 *
	 * Matching entries are removed from the `field_filters` array in place.
	 *
	 * @since 2.55.0
	 *
	 * @param array $search_criteria The search criteria containing field_filters.
	 *
	 * @return array{include: string[], exclude: string[]} Include and exclude words.
	 */
	private function extract_global_search_words( array &$search_criteria ): array {
		$include = [];
		$exclude = [];

		foreach ( $search_criteria['field_filters'] ?? [] as $i => $criterion ) {
			if (
				! is_array( $criterion )
				|| ! empty( $criterion['key'] ?? null )
			) {
				continue;
			}

			if ( 'not contains' === ( $criterion['operator'] ?? '' ) ) {
				$exclude[] = $criterion['value'];
				unset( $search_criteria['field_filters'][ $i ] );
				continue;
			}

			if ( true === ( $criterion['required'] ?? false ) ) {
				$include[] = $criterion['value'];
				unset( $search_criteria['field_filters'][ $i ] );
			}
		}

		return [
			'include' => $include,
			'exclude' => $exclude,
		];
	}

	/**
	 * Returns the form IDs associated with a View, including joined forms.
	 *
	 * @since 2.55.0
	 *
	 * @param View|null $view The View instance.
	 *
	 * @return int[] The form IDs.
	 */
	private static function get_view_form_ids( ?View $view ): array {
		if ( ! $view || ! $view->form instanceof Form ) {
			return [];
		}

		$form_ids = [ $view->form->ID ];

		foreach ( View::get_joined_forms( $view->get_post()->ID ) as $form ) {
			$form_ids[] = $form->ID;
		}

		return $form_ids;
	}
} // end class
