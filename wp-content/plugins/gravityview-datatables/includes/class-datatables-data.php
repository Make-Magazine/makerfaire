<?php
/**
 * GravityView Extension -- DataTables -- Server side data
 *
 * @since     1.0.4
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @package   GravityView
 */

use GV\Search\Querying\Search_Request;
use GV\Entry_Collection;
use GV\Entry_DataTable_Template;
use GV\Template_Context;
use GV\View;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class GV_Extension_DataTables_Data {

	/**
	 * @var bool True: the file is currently being accessed directly by an AJAX request or otherwise. False: Normal WP load.
	 */
	static $is_direct_access = false;

	/**
	 * Marks the DataTables global search box value while it travels through the search
	 * arguments. Each word is suffixed with its index.
	 *
	 * @since 3.12.0
	 */
	const GLOBAL_SEARCH_KEY = 'dt_global_search';

	/**
	 * Response headers captured instead of sent while tests are running.
	 *
	 * @since 3.12.0
	 *
	 * @var string[]
	 */
	public static $test_headers = array();

	/**
	 * Marks an anchor whose destination the field built out of the value on show, so an export
	 * writes the label and drops the address.
	 *
	 * @since 3.13.0
	 */
	const EXPORT_LABEL_ATTRIBUTE = 'data-gv-export-label';

	/**
	 * How many `get_output_data()` calls are currently rendering rows.
	 *
	 * A View embedded in a cell renders through this same method, and WordPress keeps one
	 * registration per callback, so the inner call must not unhook what the outer one still needs.
	 *
	 * @since 3.13.0
	 *
	 * @var int
	 */
	private static $export_marker_depth = 0;

	/**
	 * Per-column searches resolved for the current Ajax request, keyed by field ID.
	 *
	 * @since 3.9.0
	 *
	 * @var array<string,string>
	 */
	private $column_searches = [];

	/**
	 * The form IDs that own each searched field, keyed the same as {@see self::$column_searches}.
	 *
	 * Resolved from the column's position (see {@see self::get_column_searches()}), not from its bare
	 * field ID, so a joined form's field is never confused with a primary-form field of the same ID.
	 *
	 * A list per field, not a single ID: a Multiple Forms View can show one bare field ID as two
	 * columns owned by different forms, and a visitor can filter both at once.
	 *
	 * @since 3.12.0
	 *
	 * @var array<string,int[]>
	 */
	private $column_search_forms = [];

	/**
	 * The `name_display` formats of the View's created-by columns, when one is searched.
	 *
	 * @since 3.9.0
	 *
	 * @var string[]
	 */
	private $created_by_formats = [];

	/**
	 * The shared instance.
	 *
	 * @since 3.10.0
	 *
	 * @var GV_Extension_DataTables_Data|null
	 */
	private static $instance = null;

	/**
	 * Built script configurations for this request, keyed by "postID:viewID".
	 *
	 * @since 3.10.0
	 *
	 * @var array<string, array>
	 */
	private static $configuration_cache = array();

	/**
	 * Returns the shared instance.
	 *
	 * Constructing this class registers AJAX and template hooks, so reusing
	 * one instance prevents duplicate hook registrations.
	 *
	 * @since 3.10.0
	 *
	 * @return GV_Extension_DataTables_Data
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Clears the per-request configuration cache.
	 *
	 * @since 3.10.0
	 *
	 * @return void
	 */
	public static function flush_configuration_cache() {
		self::$configuration_cache = array();
	}

	public function __construct() {

		$this->add_hooks();
		$this->trigger_ajax();
	}

	/**
	 * Trigger the AJAX response
	 *
	 * @since 1.3
	 */
	private function trigger_ajax() {

		// Reduce query load for DT calls
		add_action( 'admin_init', array( $this, 'reduce_query_load' ) );

		// enable ajax
		add_action( 'gravityview/template/before', array( __CLASS__, 'maybe_enable_fixed_layout' ) );
		add_action( 'wp_ajax_gv_datatables_data', array( $this, 'get_datatables_data' ) );
		add_action( 'wp_ajax_gv_datatables_nonce', array( $this, 'get_datatables_nonce' ) );
		add_action( 'wp_ajax_nopriv_gv_datatables_nonce', array( $this, 'get_datatables_nonce' ) );
		add_action( 'wp_ajax_nopriv_gv_datatables_data', array( $this, 'get_datatables_data' ) );
	}

	/**
	 * If this file is being accessed directly, then set up WP so we can handle the AJAX request
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 */
	function maybe_bootstrap_wp() {

		gravityview()->log->notice( 'Accessing the DataTables data directly is no longer possible. Use the WordPress AJAX API.' );
	}

	/**
	 * Create required globals for minimal bootstrap
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 */
	function bootstrap_setup_globals() {

		gravityview()->log->notice( 'Accessing the DataTables data directly is no longer possible. Use the WordPress AJAX API.' );
	}

	/**
	 * Include Gravity Forms, GravityView, and GravityView Extensions
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 */
	function bootstrap_gv() {

		gravityview()->log->notice( 'Accessing the DataTables data directly is no longer possible. Use the WordPress AJAX API.' );
	}

	/**
	 * Include only the WP files needed
	 *
	 * This brilliant piece of code (cough) is from the dsIDXpress plugin.
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 */
	function bootstrap_wp_for_direct_access() {

		gravityview()->log->notice( 'Accessing the DataTables data directly is no longer possible. Use the WordPress AJAX API.' );
	}

	/**
	 * @since 1.3
	 */
	function add_hooks() {

		/**
		 * Don't fetch entries inline when rendering the View, since we're using AJAX requests to do that.
		 *
		 * Only affects GravityView 1.3+
		 *
		 * @since 1.1
		 */
		add_filter( 'gravityview_get_view_entries_table-dt', '__return_false' );

		// Add template path
		add_filter( 'gravityview_template_paths', array( $this, 'add_template_path' ) );

		// Override the template classes
		add_filter( 'gravityview/template/view/class', array( $this, 'set_view_template_class' ), 10, 2 );
		add_filter( 'gravityview/template/entry/class', array( $this, 'set_entry_template_class' ), 10, 3 );

		if ( ! is_admin() ) {
			// Enqueue scripts and styles
			add_action( 'gravityview/template/after', array( $this, 'add_scripts_and_styles' ) );
		}

		// Extend DataTables view as needed
		add_action( 'gravityview/template/after', array( $this, 'extend_view' ) );

		add_filter( 'gravityview-inline-edit/js-settings', array( $this, 'maybe_modify_inline_edit_settings' ), 10, 2 );
	}

	/**
	 * Sends a response header, or records it when tests are running.
	 *
	 * Tests cannot observe `header()`, so the header text is collected in
	 * {@see self::$test_headers} instead. Suppression is kept for parity with the
	 * rest of the AJAX headers, which fire after WordPress may have emitted output.
	 *
	 * @since 3.12.0
	 *
	 * @param string $header Full header line, e.g. `Content-Length: 128`.
	 *
	 * @return void
	 */
	public static function send_response_header( $header ) {
		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			self::$test_headers[] = $header;

			return;
		}

		@header( $header );
	}

	/**
	 * Verify AJAX request nonce
	 *
	 * @return boolean Whether the nonce is okay or not.
	 */
	function check_ajax_nonce() {

		if ( ! $this->has_valid_ajax_nonce() ) {
			gravityview()->log->debug( '[DataTables] AJAX request - NONCE check failed' );

			return $this->exit_or_return( false );
		}

		return true;
	}

	/**
	 * Merges the live page query the client sent into the render-time snapshot.
	 *
	 * Union, not array_merge(): array_merge() renumbers numeric-string keys, so `?123=abc`
	 * became `$_GET[0]` and a `{get:123}` filter resolved empty. Snapshot values win on
	 * conflict, which is the precedence the previous merge provided.
	 *
	 * @since 3.12.0
	 *
	 * @param array  $get        The current $_GET, taken from the render-time snapshot.
	 * @param string $page_query The live page query, with or without its leading `?`.
	 *
	 * @return array
	 */
	public function merge_page_query( array $get, $page_query ) {
		$parsed = array();

		parse_str( ltrim( (string) $page_query, '?' ), $parsed );

		return $get + $parsed;
	}

	/**
	 * Whether the request carries a valid DataTables nonce.
	 *
	 * The nonce is CSRF proof, not authorization: this endpoint is nopriv-reachable by
	 * design and entry visibility is enforced downstream. `reduce_query_load()` runs the
	 * same verification for its own gate; both must agree.
	 *
	 * @since 3.12.0
	 *
	 * @return bool
	 */
	protected function has_valid_ajax_nonce() {
		$has_nonce = isset( $_POST['nonce'] );

		return $has_nonce && (bool) wp_verify_nonce( $_POST['nonce'], 'gravityview_datatables_data' );
	}

	/**
	 * Answers with a fresh nonce so a page served from a full-page cache past the nonce
	 * tick can retry instead of leaving its tables permanently stuck.
	 *
	 * Exposes nothing: the same nonce is printed into every rendered page carrying a
	 * DataTables View, and it proves only that the request came from this origin.
	 *
	 * @since 3.12.0
	 *
	 * @return string|null JSON body while tests are running, otherwise exits.
	 */
	public function get_datatables_nonce() {
		nocache_headers();

		self::send_response_header( 'Content-Type: application/json; charset=UTF-8' );

		$body = wp_json_encode( array( 'nonce' => wp_create_nonce( 'gravityview_datatables_data' ) ) );

		return $this->exit_or_return( $body );
	}

	/**
	 * Removes the queries caused by `widgets_init` for AJAX calls (and for generating the data)
	 *
	 * @since 2.3.1
	 *
	 * @return void
	 */
	public function reduce_query_load() {

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return;
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_datatables_data' ) ) {
			return;
		}

		remove_all_actions( 'widgets_init' );
	}

	/**
	 * Get AJAX ready by defining AJAX constants and sending proper headers.
	 *
	 * @since  1.1
	 *
	 * @param string  $content_type Type of content to be set in header.
	 * @param boolean $cache        Do you want to cache the results?
	 */
	static function do_ajax_headers( $content_type = 'text/plain', $cache = false ) {

		// Tests are running, don't output any headers.
		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			return;
		}

		// If it's already been defined, that means we don't need to do it again.
		if ( defined( 'GV_AJAX_IS_SETUP' ) ) {
			return;
		} else {
			define( 'GV_AJAX_IS_SETUP', true );
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		// Fix errors thrown by NextGen Gallery
		if ( ! defined( 'NGG_SKIP_LOAD_SCRIPTS' ) ) {
			define( 'NGG_SKIP_LOAD_SCRIPTS', true );
		}

		// Prevent some theoretical random stuff from happening
		if ( ! defined( 'IFRAME_REQUEST' ) ) {
			define( 'IFRAME_REQUEST', true );
		}

		// Anything already queued for this request would otherwise ride along with the JSON.
		if ( function_exists( 'header_remove' ) ) {
			header_remove();
		}

		// Setting the content type actually introduces 200ms of latency for some reason.
		// Give us the option to say no.
		if ( ! empty( $content_type ) ) {
			@header( 'Content-Type: ' . $content_type . '; charset=UTF-8' );
		}

		// @see send_nosniff_header()
		@header( 'X-Content-Type-Options: nosniff' );
		@header( 'Accept-Encoding: gzip, deflate' );

		if ( $cache ) {
			@header( 'Cache-Control: public, store, post-check=10000000, pre-check=100000;' );
			@header( 'Expires: Thu, 15 Apr 2030 20:00:00 GMT;' );
			@header( 'Vary: Accept-Encoding' );
			@header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', strtotime( '-2 months' ) ) . ' GMT' );

		} else {

			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', 'true' );
			}

			@nocache_headers();
		}

		@header( 'HTTP/1.1 200 OK', true, 200 );
		@header( 'X-Robots-Tag:noindex;' );
	}

	/**
	 * Builds a server-side per-column search condition for a DataTables field filter.
	 *
	 * @param string        $field_id_or_meta_name Field ID or entry meta key.
	 * @param int           $form_id               Gravity Forms form ID.
	 * @param GF_Field|null $field                 Gravity Forms field object, if available.
	 * @param string        $search_value          Sanitized search value.
	 * @param string        $query_literal_class   GF query literal class name.
	 *
	 * @return GF_Query_Condition
	 */
	private function get_column_search_condition( $field_id_or_meta_name, $form_id, $field, $search_value, $query_literal_class ) {
		$tokens = $this->get_parent_complex_field_search_tokens( $field_id_or_meta_name, $field, $search_value );

		if ( count( $tokens ) > 1 ) {
			$conditions = array();

			foreach ( $tokens as $token ) {
				$conditions[] = new \GF_Query_Condition(
					new \GF_Query_Column( $field_id_or_meta_name, $form_id ),
					\GF_Query_Condition::LIKE,
					new $query_literal_class( '%' . $token . '%' )
				);
			}

			return call_user_func_array( array( '\GF_Query_Condition', '_and' ), $conditions );
		}

		return new \GF_Query_Condition(
			// Passing the form ID lets GF_Query resolve parent complex fields to their stored entry inputs.
			// Cast to int before calling this method because GF_Query_Column silently ignores non-int/non-array sources.
			new \GF_Query_Column( $field_id_or_meta_name, $form_id ),
			\GF_Query_Condition::LIKE,
			new $query_literal_class( '%' . $search_value . '%' )
		);
	}

	/**
	 * Normalizes a DataTables per-column search value before building query conditions.
	 *
	 * @param string $value Search value.
	 *
	 * @return string
	 */
	private function normalize_column_search_value( $value ) {
		$value = sanitize_text_field( $value );

		// Normalize pasted Unicode separators/invisible format marks (NBSP, ZWSP, BOM, etc.) to spaces so token boundaries survive.
		$normalized = preg_replace( '/[\x{200B}\x{FEFF}\p{Z}\s]+/u', ' ', $value );

		return trim( null === $normalized ? $value : $normalized );
	}

	/**
	 * Splits search text for text-like parent complex fields whose values may span sub-input rows.
	 *
	 * @param string        $field_id_or_meta_name Field ID or entry meta key.
	 * @param GF_Field|null $field                 Gravity Forms field object, if available.
	 * @param string        $search_value          Sanitized search value.
	 *
	 * @return array
	 */
	private function get_parent_complex_field_search_tokens( $field_id_or_meta_name, $field, $search_value ) {
		if ( ! $field || ! in_array( $field->type, array( 'address', 'name' ), true ) ) {
			return array();
		}

		if ( ! is_numeric( $field_id_or_meta_name ) || floor( (float) $field_id_or_meta_name ) !== (float) $field_id_or_meta_name ) {
			return array();
		}

		if ( empty( $search_value ) || ! $field->get_entry_inputs() ) {
			return array();
		}

		return preg_split( '/ +/', $search_value, -1, PREG_SPLIT_NO_EMPTY );
	}

	/**
	 * main AJAX logic to retrieve DataTables data
	 */
	function get_datatables_data() {

		if ( empty( $_POST ) ) {
			gravityview()->log->debug( __METHOD__ . ': no $_POST' );

			return;
		}

		gravityview()->log->debug( __METHOD__ . ': $_POST', array( 'data' => $_POST ) );

		// Owns the output buffer and the fatal handler for the rest of this request.
		$response = GV_Extension_DataTables_Response::begin();

		// An uncaught Error anywhere downstream (a plugin conflict, a type error in a hook)
		// would otherwise become a fatal, and WordPress answers those with HTML that DataTables
		// cannot parse. Catching here keeps the response ours.
		try {

			// Send correct headers
			$this->do_ajax_headers( 'application/json' );

			$has_valid_nonce = $this->has_valid_ajax_nonce();

			if ( ! $has_valid_nonce ) {
				gravityview()->log->debug( '[DataTables] AJAX request - NONCE check failed' );

				return $this->exit_or_return( $response->error( GV_Extension_DataTables_Response::CODE_INVALID_NONCE ) );
			}

			if ( empty( $_POST['view_id'] ) ) {
				gravityview()->log->debug( '[DataTables] AJAX request - View ID check failed' );

				return $this->exit_or_return( $response->error( GV_Extension_DataTables_Response::CODE_INVALID_VIEW ) );
			}

			/**
			 * Enable or disable the Content-Length header on the AJAX JSON response
			 *
			 * @since 2.0
			 *
			 * @param boolean $has_content_length true by default
			 */
			$has_content_length = apply_filters( 'gravityview/datatables/json/header/content_length', true );

			// Prevent emails from being encrypted
			add_filter( 'gravityview_email_prevent_encrypt', '__return_true' );

			gravityview()->log->debug( '[DataTables] AJAX Request ($_POST)', array( 'data' => $_POST ) );

			// Pass $_GET variables to the View functions, since they're relied on heavily
			// for searching and filtering, for example the A-Z widget
			$_GET = ! empty( $_POST['getData'] ) ? json_decode( stripslashes( $_POST['getData'] ), true ) : array();

			if ( ! is_array( $_GET ) ) {
				$_GET = array();
			}

			// `getData` is the URL query snapshotted at render time, so a full-page cache can serve
			// it stale and drop the current params, making `{get:...}` filters resolve empty. Merge
			// the live page query the browser sends as a fallback; snapshot values win on conflict.
			if ( ! empty( $_POST['pageQuery'] ) && is_string( $_POST['pageQuery'] ) ) {
				$_GET = $this->merge_page_query( $_GET, wp_unslash( $_POST['pageQuery'] ) );
			}

			$this->maybe_merge_datatables_search_into_get();

			$view_id = intval( $_POST['view_id'] );

			global $post;

			$post = get_post( \GV\Utils::_POST( 'post_id', null ) );

			// The embedding page can be gone by the time a cached page's table calls back, and
			// View_Collection::from_post() type-errors on null, which reaches the visitor as an
			// HTTP 500 of error HTML. Resolve the View by ID alone in that case.
			$has_embedding_post = $post instanceof WP_Post;

			if ( $has_embedding_post ) {
				setup_postdata( $post );
			}

			$view_collection = $has_embedding_post ? \GV\View_Collection::from_post( $post ) : null;
			$embedded_view   = $view_collection ? $view_collection->get( $view_id ) : null;

			$atts = array();
			if ( $view = $embedded_view ) {
				$atts = $view->settings->as_atts();
				gravityview()->log->debug(
					'View #{view_id} found in $post View Collection',
					array(
						'view_id' => $view_id,
						'data'    => $atts,
					)
				);
			} elseif ( ! $view = View::by_id( $view_id ) ) {
				gravityview()->log->error( 'View #{view_id} not found', array( 'view_id' => $view_id ) );

				return $this->exit_or_return( $response->error( GV_Extension_DataTables_Response::CODE_INVALID_VIEW ) );
			}

			// Reaching a View by ID is not permission to read it. Without this, entries from
			// a draft, private, or password-protected View are served to anyone who posts
			// its ID, which the front end refuses.
			$render_context = ( $post && $post->ID !== $view->ID ) ? array( 'shortcode' ) : array();

			$can_render = $view->can_render( $render_context );

			if ( true !== $can_render ) {
				gravityview()->log->error(
					'View #{view_id} is not accessible for this request: {reason}',
					array(
						'view_id' => $view->ID,
						'reason'  => is_wp_error( $can_render ) ? $can_render->get_error_code() : 'denied',
					)
				);

				return $this->exit_or_return( $response->error( GV_Extension_DataTables_Response::CODE_INVALID_VIEW ) );
			}

			// Rows are shaped by the View's columns as they are now, but the table asking for
			// them was built against the columns of whenever its page was rendered. Serving a
			// mismatch misattributes every value, so refuse instead and let the client say so.
			$posted_signature = (string) \GV\Utils::_POST( 'configHash', '' );

			if ( '' !== $posted_signature && $posted_signature !== $this->get_column_signature( $view ) ) {
				// Both signatures, because a mismatch on a page that was NOT cached is the
				// tell for a filter that varies the visible columns per request, and that
				// is indistinguishable from a stale page without them.
				gravityview()->log->notice(
					'View #{view_id} column layout changed since this page was rendered',
					array(
						'view_id'  => $view->ID,
						'expected' => $this->get_column_signature( $view ),
						'received' => $posted_signature,
					)
				);

				return $this->exit_or_return( $response->error( GV_Extension_DataTables_Response::CODE_STALE_CONFIG ) );
			}

			// The entry filter the SERVER derived: either the embedding post's own attributes,
			// re-parsed by View_Collection::from_post() above, or the View's saved settings.
			// Captured before anything in this handler writes to $view->settings, since the
			// request's own copy of these values must never be able to replace it.
			$trusted_search = array(
				'search_field'    => $view->settings->get( 'search_field', '' ),
				'search_value'    => $view->settings->get( 'search_value', '' ),
				'search_operator' => $view->settings->get( 'search_operator', '' ),
			);

			$dt_settings = get_post_meta( $view->ID, '_gravityview_datatables_settings', true );

			$is_server_side = 'serverSide' === \GV\Utils::get( $dt_settings, 'processing_mode', 'serverSide' );

			// check for order/sorting
			$atts['sort_field']     = array();
			$atts['sort_direction'] = array();
			$joined_sorts           = array();

			foreach ( (array) \GV\Utils::_POST( 'order', array() ) as $i => $order ) {

				$order_index = \GV\Utils::get( $order, 'column' );

				if ( null !== $order_index ) {
					if ( ! empty( $_POST['columns'][ $order_index ]['name'] ) ) {
						// remove prefix 'gv_'
						$posted_sort_field = substr( $_POST['columns'][ $order_index ]['name'], 3 );
						$posted_direction  = strtoupper( \GV\Utils::get( $order, 'dir', 'ASC' ) );

						$sort_column    = self::classify_ajax_sort_column( $view, $posted_sort_field );
						$needs_sourcing = 'primary' !== $sort_column['status'];

						// A joined form's column cannot go through core, which sources every
						// sort column to the View's own form. An unresolvable name is
						// dropped, matching what get_hidden_sort_fields() does.
						if ( $needs_sourcing ) {
							if ( 'joined' === $sort_column['status'] ) {
								$sort_column['direction'] = $posted_direction;
								$joined_sorts[]           = $sort_column;
							}

							continue;
						}

						$atts['sort_field'][]     = $sort_column['field_id'];
						$atts['sort_direction'][] = $posted_direction;
					}
				}
			}

			$this->maybe_set_global_search();

			// Paging/offset
			$atts['page_size'] = isset( $_POST['length'] ) ? intval( $_POST['length'] ) : '';

			$offset = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0;

			// check if someone requested the full filtered data (eg. TableTools print button)
			if ( $atts['page_size'] == '-1' || ! $is_server_side ) {
				$mode              = 'all';
				$atts['page_size'] = PHP_INT_MAX;
			} else {
				// regular mode - get view entries
				$mode = 'page';
			}

			// Set the pagenum in $_GET for the sequence field to work correctly with DataTables pagination.
			if ( $offset > 0 && $atts['page_size'] > 0 && $atts['page_size'] !== PHP_INT_MAX ) {
				$_GET['pagenum'] = intval( $offset / $atts['page_size'] ) + 1;
			}

			$view->settings->update( $atts );

			// Forces shortcode parametrization, but only where it cannot reach entries the View's
			// own configuration excludes. See filter_posted_shortcode_atts().
			$posted_atts = self::filter_posted_shortcode_atts(
				\GV\Utils::_POST( 'shortcode_atts' ),
				$trusted_search
			);

			foreach ( $posted_atts as $att => $value ) {
				$view->settings->update( array( $att => $value ) );
			}

			$atts = $view->settings->as_atts();

			foreach ( $atts as $key => $att ) {
				// Mirrors core's own is_string()/strpos( '{' ) short-circuit
				// (MergeTags::replace_variables()), so behavior is unchanged for anything that
				// could actually carry a merge tag; it just skips the array/bool settings
				// (sort_field, sort_direction, etc.) that logged a notice on every request.
				if ( ! is_string( $att ) || false === strpos( $att, '{' ) ) {
					continue;
				}

				$atts[ $key ] = \GravityView_Merge_Tags::replace_variables( $att, ( $view->form ? $view->form->form : null ), array(), false, false, false );
			}

			gravityview()->log->debug( 'DataTables final $atts', $atts );

			// DataTables' $start counts from 0 at the View's own "Offset entries starting
			// from" setting, not from the form's first entry, so the real database offset is
			// the two added together. GF_Query::page() cannot express this: it recomputes
			// offset from a page number alone, with no way to fold the View's offset in.
			$view_offset = (int) ( $view->settings->get( 'offset', 0 ) ?: 0 );

			add_action(
				'gravityview/view/query',
				$paging = static function ( $query ) use ( $offset, $view_offset ) {
					/** @var GF_Query $query */
					$query->offset( self::get_query_offset( $offset, $view_offset ) );
				}
			);

			$joined_sort_query = null;

			if ( ! empty( $joined_sorts ) ) {
				// True only when the order-classifying loop above also populated a primary-form sort
				// (a saved secondary sort, or another column in the same shift-click multi-sort).
				$has_primary_sort = ! empty( $atts['sort_field'] );

				add_action(
					'gravityview/view/query',
					$joined_sort_query = static function ( $query ) use ( $joined_sorts, $has_primary_sort ) {
						/** @var GF_Query $query */

						// With no primary-form sort requested, $query->order still holds only
						// GF_Query::parse()'s unconditional default ('id' DESC) -- a unique-column
						// tiebreaker, not a real sort. Appending the joined column after it left
						// 'id' deciding row order and the joined sort inert. Clear it the way core's
						// own multisort branch does (View::get_entries()) so the joined column
						// becomes the actual primary sort. When a primary-form sort WAS requested,
						// leave it and append the joined column as a secondary key instead.
						if ( ! $has_primary_sort ) {
							( function () {
								$this->order = array();
							} )->bindTo( $query, $query )();
						}

						$order_aliases = array();

						foreach ( $joined_sorts as $joined_sort ) {
							$is_numeric = GVCommon::is_field_numeric( $joined_sort['form_id'], $joined_sort['field_id'] );
							$column     = new \GF_Query_Column( $joined_sort['field_id'], $joined_sort['form_id'] );

							if ( $is_numeric ) {
								$column = \GF_Query_Call::CAST( $column, defined( 'GF_Query::TYPE_DECIMAL' ) ? \GF_Query::TYPE_DECIMAL : \GF_Query::TYPE_SIGNED );
							}

							$query->order( $column, $joined_sort['direction'] );

							// Same arguments GF_Query::_order_generate() uses, so this is the very alias
							// the ORDER BY will reference.
							$alias = $query->_alias( $joined_sort['field_id'], $joined_sort['form_id'], 'o' );

							$order_aliases[ $alias ] = (string) $joined_sort['field_id'];
						}

						self::repair_joined_order_joins( $order_aliases );
					}
				);
			}

			$this->column_searches     = [];
			$this->column_search_forms = [];
			$this->created_by_formats  = [];

			add_filter( 'gk/gravityview/search/request/search-arguments', [ $this, 'set_column_search_arguments' ], 5, 3 );
			add_filter( 'gk/gravityview/search/request/filters', [ $this, 'split_created_by_search' ], 4 );
			add_filter( 'gk/gravityview/search/request/filters', [ $this, 'group_column_searches' ], 5, 2 );
			add_filter( 'gk/gravityview/search/searchable-fields/allowed', [ $this, 'allow_column_search_fields' ], 5, 3 );
			add_filter( 'gk/query-filters/condition/created-by/user-fields', [ $this, 'restrict_created_by_user_fields' ], 5 );
			add_filter( 'gk/query-filters/condition/created-by/user-meta-fields', [ $this, 'restrict_created_by_user_meta_fields' ], 5 );

			$get_backup = $_GET;

			$this->drop_url_sort_when_visitor_reordered();

			try {
				$entries = $view->get_entries( gravityview()->request );
			} finally {
				$_GET = $get_backup;
			}

			remove_action( 'gravityview/view/query', $paging );

			if ( $joined_sort_query ) {
				remove_action( 'gravityview/view/query', $joined_sort_query );

				self::remove_joined_order_join_repair();
			}

			remove_filter( 'gk/gravityview/search/request/search-arguments', [ $this, 'set_global_search_argument' ], 5 );
			remove_filter( 'gk/gravityview/search/request/filters', [ $this, 'rewrite_global_search' ], 5 );
			remove_filter( 'gk/gravityview/search/searchable-fields/allowed', [ $this, 'allow_search_all' ], 5 );
			remove_filter( 'gk/gravityview/search/request/search-arguments', [ $this, 'set_column_search_arguments' ], 5 );
			remove_filter( 'gk/gravityview/search/request/filters', [ $this, 'split_created_by_search' ], 4 );
			remove_filter( 'gk/gravityview/search/request/filters', [ $this, 'group_column_searches' ], 5 );
			remove_filter( 'gk/gravityview/search/searchable-fields/allowed', [ $this, 'allow_column_search_fields' ], 5 );
			remove_filter( 'gk/query-filters/condition/created-by/user-fields', [ $this, 'restrict_created_by_user_fields' ], 5 );
			remove_filter( 'gk/query-filters/condition/created-by/user-meta-fields', [ $this, 'restrict_created_by_user_meta_fields' ], 5 );

			$filtered_total = $entries->total();

			// wrap all
			$output = array(
				'draw'            => intval( ( isset( $_POST['draw'] ) && is_numeric( $_POST['draw'] ) ) ? $_POST['draw'] : - 1 ),
				'recordsTotal'    => $this->has_search_request() ? $this->get_unfiltered_total( $view ) : $filtered_total,
				'recordsFiltered' => $filtered_total,
				'data'            => $this->get_output_data( $entries, $view, $post ),
			);

			/**
			 * Filter the output returned from the AJAX request
			 *
			 * @since 2.3
			 *
			 * @param array            $output  The output data array.
			 * @param View             $view    The View object.
			 * @param Entry_Collection $entries The entries collection.
			 */
			$output = apply_filters( 'gravityview/datatables/output', $output, $view, $entries );

			wp_reset_postdata();

			gravityview()->log->debug( '[DataTables] Ajax request answer', array( 'data' => $output ) );

			// Closes the buffer, encodes tolerantly, and appends admin-only diagnostics when
			// something wrote to the response.
			$json = $response->finish( $output );

			$is_error_body = false !== strpos( $json, '"gv_error"' );

			if ( $has_content_length && ! $is_error_body ) {
				// Content-Length is a byte count. mb_strlen() would report characters, which
				// under-declares the length of any body containing raw multibyte and truncates its tail.
				self::send_response_header( 'Content-Length: ' . strlen( $json ) );
			}

			return $this->exit_or_return( $json );
		} catch ( \Throwable $throwable ) {
			gravityview()->log->error(
				'[DataTables] Uncaught error during AJAX response',
				array(
					'data' => array(
						'message' => $throwable->getMessage(),
						'file'    => $throwable->getFile(),
						'line'    => $throwable->getLine(),
					),
				)
			);

			// Attributes the failure to the plugin directory the throwable came from, which is
			// the one thing that turns "this table could not load" into something actionable.
			$response->note_error_source( $throwable->getFile() );

			return $this->exit_or_return(
				$response->error(
					GV_Extension_DataTables_Response::CODE_FATAL,
					array(
						'message' => $throwable->getMessage(),
						'file'    => $throwable->getFile(),
						'line'    => $throwable->getLine(),
					)
				)
			);
		}
	}

	/**
	 * Whether the current AJAX request carries any user-initiated search.
	 *
	 * Shortcode attributes and View filters are page-author configuration,
	 * not user searches, so they intentionally do not count: recordsTotal is
	 * the size of the table as configured, before the visitor filters it.
	 *
	 * Not to be confused with {@see self::has_search_request_arguments()} (deprecated, always true).
	 *
	 * @since 3.10.0
	 *
	 * @return bool
	 */
	private function has_search_request() {
		if ( ! empty( $_POST['search']['value'] ) ) {
			return true;
		}

		foreach ( (array) \GV\Utils::_POST( 'columns', array() ) as $column ) {
			if ( ! empty( $column['search']['value'] ) ) {
				return true;
			}
		}

		return $this->has_search_get_params();
	}

	/**
	 * Whether the current request's URL carries any reserved search key with a value.
	 *
	 * Shared with {@see self::has_search_request()} so the initial page render, which has no
	 * `$_POST` search fields to check yet, asks the same question of `$_GET` alone.
	 *
	 * @since 3.12.0
	 *
	 * @return bool
	 */
	private function has_search_get_params(): bool {
		$reserved_search_keys = $this->reserved_search_keys();

		foreach ( (array) $_GET as $key => $value ) {
			if ( '' === $value || array() === $value ) {
				continue;
			}

			if ( in_array( (string) $key, $reserved_search_keys, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Fingerprints the columns a View renders, in order.
	 *
	 * A page carries the column list it was rendered with, in its markup and in its
	 * config; the handler shapes rows from the View's fields as they are now. A full-page
	 * cache can hold the former for far longer than an admin leaves the latter alone, and
	 * the mismatch is silent: extra values are dropped, missing ones blank, and a reorder
	 * puts every value under the wrong heading with nothing raised.
	 *
	 * @since 3.13.1
	 *
	 * @param \GV\View $view The View being rendered or queried.
	 *
	 * @return string
	 */
	private function get_column_signature( $view ): string {
		$identities = array();

		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $field ) {
			// Position matters as much as membership: a reorder is the shape that fails
			// silently, so the signature is over the ordered list, not a set.
			// Keyed by the saved slot, not the field: one field placed twice is two columns
			// whose cells can differ (labels, link settings, custom content), so swapping
			// them misattributes exactly as swapping two different fields does.
			$identities[] = $field->UID . ':' . (int) $field->form_id . ':' . $field->ID;
		}

		// A sort field the table does not display still occupies a column in every row
		// {@see self::get_output_data()}, so changing one changes the row length exactly
		// as adding or removing a visible column does.
		foreach ( self::get_hidden_sort_fields( $view ) as $sort_field ) {
			// Scalars only: the resolved entry also carries a GF_Field object, which has no
			// stable string form.
			$identities[] = 'sort:' . (int) $sort_field['form_id'] . ':' . $sort_field['field_id'];
		}

		return substr( md5( implode( '|', $identities ) ), 0, 12 );
	}

	/**
	 * The GET keys that carry a visitor search, as opposed to page-author
	 * configuration (shortcode atts, View filters) which counts toward recordsTotal.
	 *
	 * Defers to GravityView core's canonical list ({@see Search_Request::get_reserved_keys()}:
	 * gv_*, mode, and the parsed Search Bar field keys, plus anything registered through
	 * `gk/gravityview/search/request/search-arguments`), with a fallback for GravityView
	 * older than 3.0 where that API does not exist. Single source of truth for both
	 * {@see self::has_search_request()} and the pre-search count's key blanking in
	 * {@see self::get_unfiltered_total()}, so the two cannot drift.
	 *
	 * The A-Z Entry Filter widget's letter parameter is appended because core's search
	 * detection does not recognize it. Resolved the way the AZ-Filters extension resolves
	 * it; inert without that extension (no A-Z query modification runs, so the pre-search
	 * count matches the filtered count and the "(filtered from N)" info line stays hidden).
	 *
	 * @since 3.10.0
	 *
	 * @return string[]
	 */
	private function reserved_search_keys(): array {
		if ( method_exists( Search_Request::class, 'get_reserved_keys' ) ) {
			$keys = Search_Request::get_reserved_keys( $_GET );
		} else {
			$keys = array( 'gv_search', 'gv_start', 'gv_end', 'gv_id', 'gv_by', 'mode' );

			foreach ( array_keys( (array) $_GET ) as $key ) {
				if ( 0 === strpos( (string) $key, 'filter_' ) ) {
					$keys[] = (string) $key;
				}
			}
		}

		$az_filter_parameter = (string) apply_filters( 'gravityview_az_filter_parameter', 'letter' );
		if ( '' !== $az_filter_parameter ) {
			$keys[] = $az_filter_parameter;
		}

		return array_values( array_unique( $keys ) );
	}

	/**
	 * Counts the View's entries without the request's search parameters.
	 *
	 * The Search Request and the global/column search hooks all read the
	 * request superglobals at query-build time, so blanking the search keys
	 * for the duration of one extra count query yields the pre-search total
	 * that the DataTables protocol expects in recordsTotal. Runs only when a
	 * search is active (see has_search_request()).
	 *
	 * @since 3.10.0
	 *
	 * @param View $view The View.
	 *
	 * @return int The unfiltered total.
	 */
	private function get_unfiltered_total( $view ) {
		$get_backup  = $_GET;
		$post_backup = $_POST;

		if ( isset( $_POST['search']['value'] ) ) {
			$_POST['search']['value'] = '';
		}

		foreach ( array_keys( (array) \GV\Utils::_POST( 'columns', array() ) ) as $index ) {
			if ( isset( $_POST['columns'][ $index ]['search']['value'] ) ) {
				$_POST['columns'][ $index ]['search']['value'] = '';
			}
		}

		$reserved_search_keys = $this->reserved_search_keys();

		foreach ( array_keys( (array) $_GET ) as $key ) {
			// search/columns are DataTables protocol params mirrored into $_GET for
			// GET-method requests (maybe_merge_datatables_search_into_get()).
			$is_datatables_param = in_array( (string) $key, array( 'search', 'columns' ), true );

			if ( $is_datatables_param || in_array( (string) $key, $reserved_search_keys, true ) ) {
				unset( $_GET[ $key ] );
			}
		}

		try {
			$total = $view->get_entries( gravityview()->request )->total();
		} finally {
			$_GET  = $get_backup;
			$_POST = $post_backup;
		}

		return $total;
	}

	/**
	 * Exit with $value or return it.
	 *
	 * Behavior depends on whether tests are being run or not.
	 *
	 * @param mixed $value The value to return if tests are being run.
	 *
	 * @return mixed|null
	 */
	private function exit_or_return( $value ) {

		defined( 'DOING_GRAVITYVIEW_TESTS' ) || exit( $value );

		return $value;
	}

	/**
	 * Mirrors the DataTables column and global-search parameters into `$_GET` for GET search requests.
	 *
	 * The search-bar fields are forwarded in `getData` and read from `$_GET`, while DataTables sends
	 * its own `columns` and `search` parameters in the POST body. When the Search Request reads GET,
	 * both must live in `$_GET` so column searches and search-bar fields (such as a date range) apply
	 * together. A POST search request already reads the POST body, so nothing is mirrored.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	private function maybe_merge_datatables_search_into_get(): void {
		if ( ! method_exists( Search_Request::class, 'method' ) || 'post' === Search_Request::method() ) {
			return;
		}

		if ( ! is_array( $_GET ) ) {
			$_GET = [];
		}

		foreach ( [ 'columns', 'search' ] as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$_GET[ $key ] = $_POST[ $key ];
			}
		}

		// Reserve the params just merged into `$_GET` so gv_get_query_args() skips them when building links.
		// Otherwise the entire columns array is appended to every entry-link href, overflowing the server's
		// request-line limit on wide tables (a 414 when the link is clicked).
		add_filter( 'gravityview/api/reserved_query_args', [ $this, 'reserve_datatables_query_args' ] );
	}

	/**
	 * Excludes DataTables' own request parameters from generated GravityView links.
	 *
	 * @since 3.9.1
	 *
	 * @param array $args The reserved query argument keys.
	 *
	 * @return array The reserved query argument keys, including DataTables' `columns` and `search`.
	 */
	public function reserve_datatables_query_args( $args ) {
		$args[] = 'columns';
		$args[] = 'search';

		return $args;
	}

	/**
	 * Adds the DataTables global search box value under the temporary `dt_global_search` argument.
	 *
	 * The marker key lets {@see self::rewrite_global_search()} convert it to the canonical `search_all`
	 * filter once the request has been parsed.
	 *
	 * @since 3.9.0
	 *
	 * @param array $search_arguments  The search arguments.
	 * @param array $request_arguments The request arguments.
	 *
	 * @return array The updated search arguments.
	 */
	public function set_global_search_argument( $search_arguments, $request_arguments ) {
		$value = $request_arguments['search']['value'] ?? null;
		if (
			! is_array( $search_arguments )
			|| empty( $value )
			// No overwriting existing search arguments.
			|| array_key_exists( self::GLOBAL_SEARCH_KEY, $search_arguments )
		) {
			return $search_arguments;
		}

		// The phrase stays under one argument key, which is the key the search pipeline
		// expects. It is split into one filter per word in rewrite_global_search().
		$search_arguments[ self::GLOBAL_SEARCH_KEY ] = compact( 'value' );

		return $search_arguments;
	}

	/**
	 * Splits a global search phrase into the words that must all match.
	 *
	 * Each word becomes its own `search_all` filter and they are ANDed together, so
	 * "john smith" requires both words (each may match in any searchable field) instead of
	 * matching every entry. This mirrors DataTables' own client-side smart search, which
	 * keeps Preloaded and server-side modes consistent.
	 *
	 * @since 3.12.0
	 *
	 * @param string $value The raw search value.
	 *
	 * @return string[]
	 */
	private function split_global_search_value( $value ) {
		$normalized = $this->normalize_column_search_value( $value );

		/**
		 * Whether a multi-word global search requires every word.
		 *
		 * Return false to search the phrase whole.
		 *
		 * @since 3.12.0
		 *
		 * @param bool $split Whether to split the phrase into words.
		 */
		$split = apply_filters( 'gk/gravityview/datatables/search/split-words', true );

		if ( ! $split ) {
			return array( $normalized );
		}

		$words = preg_split( '/ /', $normalized, -1, PREG_SPLIT_NO_EMPTY );

		return empty( $words ) ? array( $normalized ) : array_values( $words );
	}

	/**
	 * Rewrites the temporary `dt_global_search` marker into a canonical `search_all` filter.
	 *
	 * The marker key (see {@see self::set_global_search_argument()}) is renamed to `search_all` while
	 * `dt_global_search` is kept as the request key, so the operator allowlist resolves like the native
	 * global search and {@see self::group_column_searches()} can target the row to fold it into the
	 * per-column AND group. Runs whenever the global search box is used, independent of column searches.
	 *
	 * @since 3.9.0
	 *
	 * @param array $filters The normalized search filters.
	 *
	 * @return array The updated filters.
	 */
	public function rewrite_global_search( $filters ): array {
		if ( ! is_array( $filters ) ) {
			return $filters;
		}

		$rewritten = array();

		foreach ( $filters as $filter ) {
			$is_global = is_array( $filter ) && self::GLOBAL_SEARCH_KEY === (string) ( $filter['key'] ?? '' );

			if ( ! $is_global ) {
				$rewritten[] = $filter;

				continue;
			}

			// One filter per word, all carrying the same request key so they fold into the same
			// AND group: every word must match, each in any searchable field.
			foreach ( $this->split_global_search_value( (string) ( $filter['value'] ?? '' ) ) as $word ) {
				$rewritten[] = array_merge(
					$filter,
					array(
						'key'         => 'search_all',
						'field_id'    => 'search_all',
						'request_key' => self::GLOBAL_SEARCH_KEY,
						'value'       => $word,
					)
				);
			}
		}

		return $rewritten;
	}

	/**
	 * Updates the allowed search fields to include `search_all`, if the search request does not have it.
	 *
	 * @since 3.9.0
	 *
	 * @param array    $allowed         The allowed search fields.
	 * @param \GV\View $view            The View being searched.
	 * @param bool     $with_full_field Whether the allowed array contains the full field information.
	 *
	 * @return array|mixed
	 */
	public function allow_search_all( $allowed, $view, $with_full_field ) {
		if ( ! is_array( $allowed ) ) {
			return $allowed;
		}

		if ( ! $with_full_field ) {
			return array_merge( $allowed, [ 'search_all' ] );
		}

		$fields = array_column( $allowed, 'field' );
		if ( ! in_array( 'search_all', $fields, true ) ) {
			$allowed[] = [ 'field' => 'search_all' ];
		}

		return $allowed;
	}

	/**
	 * The `gform_gf_query_sql` callback currently repairing joined-sort ORDER BY joins, if any.
	 *
	 * @since 3.12.0
	 *
	 * @var callable|null
	 */
	private static $joined_order_join_repair = null;

	/**
	 * Restores the `meta_key` constraint that Gravity Forms drops from a joined form's ORDER BY join.
	 *
	 * `GF_Query::_prime_joins()` re-keys an inferred ORDER BY join onto the join alias and rebuilds
	 * the ON clause without its `meta_key` term, so the join matches every meta row of the joined
	 * entry and `ORDER BY <alias>.meta_value` sorts on an arbitrary one of them. The result is silent
	 * and nondeterministic: often entry-id order, occasionally correct by chance. WHERE-side joins are
	 * rewritten identically but survive it, because their `meta_key` predicate is also emitted into
	 * the WHERE clause; an ORDER BY has no second copy.
	 *
	 * Fixed upstream in Gravity Forms 3.0.0.1; remove this once that is the minimum supported version.
	 *
	 * @see https://github.com/gravityforms/gravityforms/pull/3828
	 *
	 * @since 3.12.0
	 *
	 * @param array<string,string> $order_aliases Field ID keyed by the ORDER BY join alias.
	 *
	 * @return void
	 */
	private static function repair_joined_order_joins( array $order_aliases ) {
		self::remove_joined_order_join_repair();

		$order_aliases = array_filter(
			$order_aliases,
			static function ( $field_id, $alias ) {
				// Aliases are generated by GF_Query (`o` + digits). Anything else is not ours to touch.
				return '' !== (string) $field_id && preg_match( '/\A[a-z]\d+\z/', (string) $alias );
			},
			ARRAY_FILTER_USE_BOTH
		);

		if ( ! $order_aliases ) {
			return;
		}

		self::$joined_order_join_repair = static function ( $sql ) use ( $order_aliases ) {
			if ( empty( $sql['join'] ) || ! is_string( $sql['join'] ) ) {
				return $sql;
			}

			global $wpdb;

			foreach ( $order_aliases as $alias => $field_id ) {
				// The rewritten, meta_key-less shape only. `(?! AND)` leaves an already-correct join
				// alone, so this is a no-op once Gravity Forms stops dropping the term.
				$pattern = sprintf( '/`%s`\.`entry_id` = `[a-z]+\d+`\.`entry_id`(?! AND)/', preg_quote( $alias, '/' ) );

				$sql['join'] = preg_replace_callback(
					$pattern,
					static function ( $matches ) use ( $wpdb, $alias, $field_id ) {
						return $matches[0] . $wpdb->prepare( ' AND `' . $alias . '`.`meta_key` = %s', $field_id );
					},
					$sql['join']
				);
			}

			return $sql;
		};

		add_filter( 'gform_gf_query_sql', self::$joined_order_join_repair, 5 );
	}

	/**
	 * Removes the joined-sort ORDER BY join repair registered by {@see self::repair_joined_order_joins()}.
	 *
	 * @since 3.12.0
	 *
	 * @return void
	 */
	private static function remove_joined_order_join_repair() {
		if ( ! self::$joined_order_join_repair ) {
			return;
		}

		remove_filter( 'gform_gf_query_sql', self::$joined_order_join_repair, 5 );

		self::$joined_order_join_repair = null;
	}

	/**
	 * Injects the DataTables per-column searches into the Search Request arguments.
	 *
	 * A joined form's field can share its bare ID with the primary form's (Bug #5); when
	 * {@see self::get_column_searches()} resolved one to a non-primary, joined form, the
	 * `<field_id>:<form_id>` key is the same shape `Search_Request::get_filters_data()` already
	 * parses to attach that form ID to the resulting filter, so the query runs against the right
	 * form's field instead of silently falling back to the primary form's field of the same ID.
	 *
	 * @since 3.9.0
	 *
	 * @param array    $search_arguments  The search arguments.
	 * @param array    $request_arguments The request arguments.
	 * @param \GV\View $view              The View being searched.
	 *
	 * @return array The updated search arguments.
	 */
	public function set_column_search_arguments( $search_arguments, $request_arguments, $view ) {
		if ( ! is_array( $search_arguments ) || ! is_array( $request_arguments ) || ! $view instanceof \GV\View ) {
			return $search_arguments;
		}

		$this->column_searches     = [];
		$this->column_search_forms = [];

		$primary_form_id  = (int) $view->form->ID;
		$sortable_form_ids = self::get_sortable_form_ids( $view );

		foreach ( $this->get_column_searches( $request_arguments, $view ) as $search ) {
			$field_id = $search['field_id'];
			$value    = $search['value'];
			$form_id  = $search['form_id'];

			// Only a form this View actually joins can be posted into the key; the client
			// does not get to widen the query's form surface.
			$is_joined = $form_id && $form_id !== $primary_form_id && in_array( $form_id, $sortable_form_ids, true );

			$key = 'filter_' . str_replace( '.', '_', $field_id ) . ( $is_joined ? ':' . $form_id : '' );

			if ( array_key_exists( $key, $search_arguments ) ) {
				continue;
			}

			$owner_form_id = $is_joined ? $form_id : self::resolve_column_form_id( $view, $field_id );

			$search_arguments[ $key ]           = [ 'value' => $value ];
			$this->column_searches[ $field_id ] = $value;

			if ( ! in_array( $owner_form_id, $this->column_search_forms[ $field_id ] ?? [], true ) ) {
				$this->column_search_forms[ $field_id ][] = $owner_form_id;
			}
		}

		return $search_arguments;
	}

	/**
	 * Adds the DataTables searchable columns to the allowed search fields.
	 *
	 * @since 3.9.0
	 *
	 * @param array    $allowed         The allowed search fields.
	 * @param \GV\View $view            The View being searched.
	 * @param bool     $with_full_field Whether the allowed array contains the full field information.
	 *
	 * @return array|mixed The updated allowed search fields.
	 */
	public function allow_column_search_fields( $allowed, $view, $with_full_field ) {
		if ( ! is_array( $allowed ) || ! $view instanceof \GV\View || ! $view->form ) {
			return $allowed;
		}

		$existing = $with_full_field ? array_column( $allowed, 'field' ) : $allowed;

		foreach ( array_keys( $this->column_searches ) as $field_id ) {
			$field_id = (string) $field_id;
			if ( in_array( $field_id, $existing, true ) ) {
				continue;
			}

			// The forms resolved for this field's own searched columns win (see
			// set_column_search_arguments()); resolve_column_form_id() is the fallback for a
			// field_id reaching this filter some other way (e.g. a third-party integration).
			$form_ids = $this->column_search_forms[ $field_id ] ?? [ self::resolve_column_form_id( $view, $field_id ) ];

			$existing[] = $field_id;

			if ( ! $with_full_field ) {
				$allowed[] = $field_id;

				continue;
			}

			// One entry per owning form: a Multiple Forms View can show the same bare field ID
			// as a primary column and a joined column, and allowing only one of them silently
			// drops the other's filter.
			foreach ( $form_ids as $form_id ) {
				$allowed[] = [ 'field' => $field_id, 'form_id' => $form_id ];
			}
		}

		return $allowed;
	}

	/**
	 * Resolves the searchable DataTables column searches from the request arguments.
	 *
	 * The approval column is searched through its `is_approved` entry meta key, mapped from its
	 * `entry_approval` field ID. Created-by flows through as the `created_by` key, handled by the
	 * Query Filters created-by condition.
	 *
	 * A joined form can share a bare field ID with the primary form (e.g. both have a field "4"),
	 * and the posted column carries only that bare ID. The column's own INDEX disambiguates: it
	 * mirrors the View's field order ({@see self::get_datatables_script_configuration()} builds
	 * the client's `columns` option in this same order, hidden sort columns appended after), so
	 * the configured field at the same index -- not a name lookup -- is the one actually searched.
	 *
	 * @since 3.9.0
	 *
	 * @param array    $request_arguments The request arguments.
	 * @param \GV\View $view              The View being searched.
	 *
	 * @return array<int,array{field_id:string,value:string,form_id:int}> Normalized searches, one per
	 *                                                                   searched column. A list, not
	 *                                                                   keyed by field ID: two columns
	 *                                                                   can share a bare field ID and
	 *                                                                   both be filtered at once.
	 */
	private function get_column_searches( array $request_arguments, \GV\View $view ): array {
		$columns = $request_arguments['columns'] ?? [];
		if ( ! is_array( $columns ) || ! $view->form ) {
			return [];
		}

		$configured = $view->fields->by_visible()->as_configuration();

		// as_configuration() keys its columns by their UID (e.g. "aaa"), not sequential position;
		// the posted columns array is sequential, so the index lookup below needs matching keys.
		$configured_columns = array_values( $configured['directory_table-columns'] ?? [] );
		$configured_ids     = wp_list_pluck( $configured_columns, 'id' );

		$searches = [];
		foreach ( $columns as $index => $column ) {
			if ( ! is_array( $column ) || empty( $column['searchable'] ) ) {
				continue;
			}

			$name = sanitize_text_field( $column['name'] ?? '' );

			$value = $column['search']['value'] ?? '';
			if ( ! is_string( $value ) || '' === $value ) {
				continue;
			}

			$field_id = str_replace( 'gv_', '', $name );

			// A joined column that is currently sorted arrives form-qualified as
			// `gv_<form_id>_<field_id>`: sorting needs the qualifier to pick the right form's
			// column, and the client rewrites the shared `columns` entry the search reads too.
			// Only a numeric leading segment is a form qualifier, so entry meta such as
			// `date_created` is untouched.
			$qualified_form_id = 0;

			if ( ! in_array( $field_id, $configured_ids, true ) && preg_match( '/\A(\d+)_(.+)\z/', $field_id, $qualified ) ) {
				$qualified_form_id = (int) $qualified[1];
				$field_id          = $qualified[2];
			}

			if ( ! in_array( $field_id, $configured_ids, true ) ) {
				continue;
			}

			$configured_column = $configured_columns[ $index ] ?? null;
			$form_id           = ( is_array( $configured_column ) && (string) ( $configured_column['id'] ?? '' ) === $field_id )
				? (int) ( $configured_column['form_id'] ?? 0 )
				: 0;

			// The qualifier names the form outright, so it beats the index lookup.
			if ( $qualified_form_id ) {
				$form_id = $qualified_form_id;
			}

			if ( 'entry_approval' === $field_id ) {
				$field_id = 'is_approved';
			} elseif ( 'created_by' === $field_id ) {
				$this->created_by_formats = $this->created_by_name_displays( $configured );
			}

			$value = $this->normalize_column_search_value( $value );
			if ( '' === $value ) {
				continue;
			}

			if ( 'created_by' === $field_id ) {
				$value = $this->guard_created_by_value( $value );
			}

			$searches[] = [
				'field_id' => $field_id,
				'value'    => $value,
				'form_id'  => $form_id,
			];
		}

		return $searches;
	}

	/**
	 * Returns the `name_display` formats of every created-by column.
	 *
	 * @since 3.9.0
	 *
	 * @param array $configured The View field configuration.
	 *
	 * @return string[] The formats, defaulting to `display_name`.
	 */
	private function created_by_name_displays( array $configured ): array {
		$formats = [];

		foreach ( $configured['directory_table-columns'] ?? [] as $column ) {
			if ( 'created_by' === ( $column['id'] ?? '' ) ) {
				$formats[] = (string) ( $column['name_display'] ?? 'display_name' );
			}
		}

		return array_values( array_unique( $formats ) );
	}

	/**
	 * Replaces a created-by ID value with a sentinel when no column displays the ID.
	 *
	 * A numeric value would resolve to an exact `created_by = N` match; when no created-by
	 * column renders the `ID` format, the sentinel forces a name LIKE that matches no user,
	 * keeping a hidden user ID unsearchable as the legacy rendered-cell match was.
	 *
	 * @since 3.9.0
	 *
	 * @param string $value The normalized created-by search value.
	 *
	 * @return string The value, or a non-numeric sentinel matching no user.
	 */
	private function guard_created_by_value( string $value ): string {
		if ( ctype_digit( $value ) && ! in_array( 'ID', $this->created_by_formats, true ) ) {
			return 'dt-created-by-' . $value . '-not-displayed';
		}

		return $value;
	}

	/**
	 * Maps the created-by `name_display` formats to the user fields and meta keys to search.
	 *
	 * @since 3.9.0
	 *
	 * @return array{fields: string[], meta: string[]}|null The fields and meta keys to search, or null to
	 *                                                       leave the created-by condition unrestricted.
	 */
	private function created_by_format_map(): ?array {
		$fields = [];
		$meta   = [];

		foreach ( $this->created_by_formats as $format ) {
			switch ( $format ) {
				case 'user_login':
				case 'display_name':
				case 'user_email':
				case 'user_registered':
					$fields[] = $format;
					break;
				case 'nickname':
				case 'description':
				case 'first_name':
				case 'last_name':
					$meta[] = $format;
					break;
				case 'first_last_name':
				case 'last_first_name':
					$meta[] = 'first_name';
					$meta[] = 'last_name';
					break;
			}
		}

		if ( ! $fields && ! $meta ) {
			return null;
		}

		return [
			'fields' => array_values( array_unique( $fields ) ),
			'meta'   => array_values( array_unique( $meta ) ),
		];
	}

	/**
	 * Restricts the created-by search to the user fields matching the column display format.
	 *
	 * @since 3.9.0
	 *
	 * @param array $fields The default user fields.
	 *
	 * @return array The user fields to search.
	 */
	public function restrict_created_by_user_fields( $fields ) {
		$map = $this->created_by_format_map();

		return null === $map ? $fields : $map['fields'];
	}

	/**
	 * Restricts the created-by search to the user meta keys matching the column display format.
	 *
	 * @since 3.9.0
	 *
	 * @param array $meta_fields The default user meta keys.
	 *
	 * @return array The user meta keys to search.
	 */
	public function restrict_created_by_user_meta_fields( $meta_fields ) {
		$map = $this->created_by_format_map();

		return null === $map ? $meta_fields : $map['meta'];
	}

	/**
	 * Splits a composite-format created-by search into an AND group of per-word filters.
	 *
	 * Composite formats (first_last_name/last_first_name) render across separate user meta keys, so a
	 * multi-word value cannot match a single key with LIKE. The created-by filter is replaced in place by
	 * an AND group of per-word created-by filters — each word may match either name part.
	 *
	 * @since 3.9.0
	 *
	 * @param array $filters The normalized search filters.
	 *
	 * @return array The updated filters.
	 */
	public function split_created_by_search( $filters ): array {
		if ( ! is_array( $filters ) || ! array_intersect( $this->created_by_formats, [ 'first_last_name', 'last_first_name' ] ) ) {
			return $filters;
		}

		foreach ( $filters as $i => $filter ) {
			if (
				! is_array( $filter )
				|| isset( $filter['conditions'] )
				|| 'created_by' !== ( $filter['key'] ?? '' )
				|| ! is_string( $filter['value'] ?? null )
			) {
				continue;
			}

			$words = preg_split( '/\s+/', trim( $filter['value'] ), -1, PREG_SPLIT_NO_EMPTY );
			if ( count( $words ) < 2 ) {
				continue;
			}

			$conditions = [];
			foreach ( $words as $word ) {
				$conditions[] = array_merge( $filter, [ 'value' => $word ] );
			}

			$filters[ $i ] = [
				'key'        => 'dt_created_by',
				'mode'       => 'AND',
				'conditions' => $conditions,
			];
		}

		return $filters;
	}

	/**
	 * Combines the per-column searches, and the rewritten global search when present, into one AND group,
	 * then ANDs that group against the Search Bar's own filters so a DT filter always narrows.
	 *
	 * `Search_Request::to_filter()` wraps whatever this hook returns in the request's own mode (OR by
	 * default, whenever the Search Bar submits its default "any" mode), so returning the DT group as a
	 * sibling of the Search Bar filters makes it an OR-alternative instead of a conjunct: a Search Bar
	 * match and a disjoint DT filter match union together instead of intersecting, and a DT filter
	 * composed with a non-matching Search Bar term returns the DT filter's own matches instead of zero.
	 * Nesting both sides under one outer AND group -- (Search Bar filters, at the request's own mode)
	 * AND (the DT group) -- leaves the top-level list with a single member, so the outer OR has nothing
	 * left to union with. The Search Bar's own any/all mode still governs how the Search Bar's OWN fields
	 * combine with each other (preserved as the inner mode on that side of the AND).
	 *
	 * The global search row is matched by its `dt_global_search` request key, assigned by
	 * {@see self::rewrite_global_search()}. The composite created-by AND subgroup from
	 * {@see self::split_created_by_search()} is matched by its `dt_created_by` key and folded as a column
	 * member.
	 *
	 * An `entry_date` filter (gv_start/gv_end) is a special case: `Search_Request::create_filter()` gives
	 * it operator-specific handling ( `between`/`>=`/`<=`/day) that runs only on the top-level filter list,
	 * not on filters nested inside a `conditions` array (`SearchFilter::from_array()` has no equivalent
	 * date branch and its leaf path requires a `value` key an `entry_date` filter never carries, so nesting
	 * it as-is would throw). When there is nothing to AND it with (no DT column/global-search group), it is
	 * kept out of the AND-nesting and re-attached as its own top-level sibling, unchanged, so
	 * `create_filter()`'s special-case still runs. When a DT group DOES exist, {@see
	 * self::leafify_entry_date_filter()} converts it into the `operator`/`value` leaf shape
	 * `Search_Request::create_date_filter()` itself produces -- a shape `SearchFilter::from_array()`
	 * already understands with no date branch needed -- and folds it into the group so the date range
	 * actually narrows a column filter under "any" mode instead of unioning with it.
	 *
	 * @since 3.9.0
	 *
	 * @param array              $filters The normalized search filters.
	 * @param Search_Request|null $request The current search request; only its mode() is used, and only
	 *                                     when present (@since 3.0.0 in core, younger than the plugin's
	 *                                     declared 2.57 minimum).
	 *
	 * @return array The updated filters.
	 */
	public function group_column_searches( $filters, $request = null ): array {
		if ( ! is_array( $filters ) ) {
			return $filters;
		}

		$field_ids = array_map( 'strval', array_keys( $this->column_searches ) );
		$group     = [];
		$rest      = [];
		$dates     = [];

		foreach ( $filters as $filter ) {
			if ( is_array( $filter ) ) {
				$key = (string) ( $filter['key'] ?? '' );

				if ( 'dt_created_by' === $key ) {
					$group[] = $filter;
					continue;
				}

				if ( ! isset( $filter['conditions'] ) ) {
					$is_global = 'dt_global_search' === (string) ( $filter['request_key'] ?? '' );

					if ( $is_global || in_array( $key, $field_ids, true ) ) {
						$group[] = $filter;
						continue;
					}
				}

				if ( 'entry_date' === $key ) {
					$dates[] = $filter;
					continue;
				}
			}

			$rest[] = $filter;
		}

		if ( empty( $group ) ) {
			return array_merge( $rest, $dates );
		}

		// A DT group exists, so every date filter must narrow it: fold each one in, leafified into
		// the shape SearchFilter::from_array() can nest.
		foreach ( $dates as $date_filter ) {
			$group[] = $this->leafify_entry_date_filter( $date_filter );
		}

		if ( empty( $rest ) ) {
			$grouped = count( $group ) < 2 ? $group : [ [ 'mode' => 'and', 'conditions' => $group ] ];

			return $grouped;
		}

		$request_mode = ( is_object( $request ) && method_exists( $request, 'mode' ) ) ? $request->mode() : 'or';

		$composite = [
			[
				'mode'       => 'and',
				'conditions' => [
					[ 'mode' => $request_mode, 'conditions' => $rest ],
					[ 'mode' => 'and', 'conditions' => $group ],
				],
			],
		];

		return $composite;
	}

	/**
	 * Converts a raw `entry_date` filter (`start_date`/`end_date`/optional `type`) into the
	 * `operator`/`value` leaf shape `Search_Request::create_date_filter()` produces for the same
	 * data, so it can be nested inside a `conditions` array.
	 *
	 * `SearchFilter::from_array()` has no `start_date`/`end_date` branch -- it only understands a leaf
	 * with a `value` key -- so the raw shape from `Search_Request::get_filters_data()` must be
	 * translated before nesting. Mirrors `Search_Request::create_date_filter()`'s own precedence
	 * (single-day `type` first, then both dates as `between`, then whichever single date is present)
	 * so the resulting range matches what the un-nested top-level path would have produced.
	 *
	 * @since 3.12.0
	 *
	 * @param array $filter The raw `entry_date` filter.
	 *
	 * @return array The `entry_date` filter as an `operator`/`value` leaf.
	 */
	private function leafify_entry_date_filter( array $filter ): array {
		$start_date = $filter['start_date'] ?? null;
		$end_date   = $filter['end_date'] ?? null;

		if ( 'day' === ( $filter['type'] ?? '' ) && ! empty( $start_date ) ) {
			return [ 'key' => 'entry_date', 'value' => $start_date, 'operator' => 'day' ];
		}

		if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
			return [ 'key' => 'entry_date', 'value' => [ $start_date, $end_date ], 'operator' => 'between' ];
		}

		if ( ! empty( $start_date ) ) {
			return [ 'key' => 'entry_date', 'value' => $start_date, 'operator' => '>=' ];
		}

		return [ 'key' => 'entry_date', 'value' => $end_date, 'operator' => '<=' ];
	}

	/**
	 * Get the array of entry data
	 *
	 * @since 1.3
	 *
	 * @param Entry_Collection $entries The collection of entries for the current search.
	 * @param View             $view    The View.
	 * @param WP_Post          $post    Current View or post/page where View is embedded.
	 *
	 * @return array
	 */
	public function get_output_data( $entries, $view, $post = null ) {
		/**
		 * Runs before generating the output data.
		 *
		 * This is helpful for adding filters that will modify the output downstream.
		 * For example, this is used by the GravityView LifterLMS integration.
		 *
		 * @action `gk/gravityview/datatables/output/before`
		 *
		 * @since 2.0
		 *
		 * @param \GV\Entry_Collection $entries The collection of entries for the current search.
		 * @param View                 $view    The View.
		 * @param \WP_Post             $post    The current View or post/page where View is embedded.
		 */
		do_action( 'gk/gravityview/datatables/output/before', $entries, $view, $post );

		if ( ! $view instanceof View ) {
			gravityview()->log->notice( '\GV_Extension_DataTables_Data::get_output_data now requires \GV\Entry_Collection and \GV\View object parameters.' );

			return array();
		}

		// build output data
		$data  = array();
		$_POST = isset( $_POST ) ? (array) $_POST : array();
		if ( $entries->total() ) {

			$fields          = $view->fields->by_position( 'directory_table-columns' )->by_visible()->all();
			$internal_source = new \GV\Internal_Source();
			$renderer        = new \GV\Field_Renderer();

			// Resolved from saved post meta, matching get_hidden_sort_fields()'s own source: this
			// AJAX endpoint is nopriv and $_POST['shortcode_atts'] is caller-controlled, so the
			// hidden-column set a request can render must never be shortcode-aware.
			$hidden_sort_fields = array();

			foreach ( self::get_hidden_sort_fields( $view ) as $hidden_sort_field ) {
				// A joined form's field is stored as `<form_id>_<field_id>`; it has to be built
				// against the form that owns it, or the renderer has nothing to read the joined
				// entry's value from and the cell silently renders empty.
				$owner_form = \GV\GF_Form::by_id( $hidden_sort_field['form_id'] );

				$hidden_field = is_numeric( $hidden_sort_field['field_id'] ) && $owner_form
					? \GV\GF_Field::from_configuration( array(
						'id'      => $hidden_sort_field['field_id'],
						'form_id' => $hidden_sort_field['form_id'],
					) )
					: \GV\Internal_Field::by_id( $hidden_sort_field['field_id'] );

				if ( $hidden_field ) {
					$hidden_sort_fields[] = $hidden_field;
				}
			}

			// Combine visible fields and hidden sort fields for rendering.
			$all_fields = array_merge( $fields, $hidden_sort_fields );

			++self::$export_marker_depth;

			add_filter( 'gravityview/template/field/entry_link', array( __CLASS__, 'mark_entry_link' ), 10, 3 );
			add_filter( 'gravityview/template/field/phone/output', array( __CLASS__, 'mark_phone_link' ), 10, 2 );

			try {
				// For each entry
				foreach ( $entries->all() as $entry ) {
					$temp = array();

					\GV\Mocks\Legacy_Context::push(
						array(
							'view'  => $view,
							'entry' => $entry,
							'post'  => $post,
						)
					);

					foreach ( $all_fields as $field ) {
						$form = $view->form;

						if ( is_callable( array( $entry, 'is_multi' ) ) && $entry->is_multi() ) {
							$form = \GV\GF_Form::by_id( $field->form_id );
						}

						$source = is_numeric( $field->ID ) ? $form : $internal_source;
						$temp[] = $renderer->render( $field, $view, $source, $entry, gravityview()->request );
					}

					\GV\Mocks\Legacy_Context::pop();

					/**
					 * Modify the entry output before the request is returned
					 *
					 * @since 2.3.1
					 *
					 * @param array $temp  Array of values for the entry, one item per field being rendered by \GV\Field_Renderer()
					 * @param View  $view  Current View being processed
					 * @param array $entry Current Gravity Forms entry array
					 */
					$temp = apply_filters( 'gravityview/datatables/output/entry', $temp, $view, $entry );

					// Then add the item to the output dataset
					$data[] = $temp;

				}
			} finally {
				// A View embedded in a cell renders through here too; only the outermost call
				// may unhook, or its remaining rows lose the marker.
				if ( 0 === --self::$export_marker_depth ) {
					remove_filter( 'gravityview/template/field/entry_link', array( __CLASS__, 'mark_entry_link' ), 10 );
					remove_filter( 'gravityview/template/field/phone/output', array( __CLASS__, 'mark_phone_link' ), 10 );
				}
			}
		}

		return $data;
	}

	/**
	 * Marks the anchor that the "Link to single entry" field setting wraps a value in.
	 *
	 * An export appends a link's destination after its label, because that address is the whole
	 * value of a File Upload or Website column. The entry permalink is navigation rather than
	 * data, so the export needs to tell that one anchor apart from the rest.
	 *
	 * The Edit Link field fires this same filter, and there the address is the point of the
	 * column, so only the wrapper the field setting adds is marked.
	 *
	 * @since 3.13.0
	 *
	 * @param string $link    The link HTML.
	 * @param string $href    The link destination.
	 * @param mixed  $context The template context the link was rendered in.
	 *
	 * @return string The link HTML, carrying the marker attribute when it wraps a field value.
	 */
	public static function mark_entry_link( $link, $href = '', $context = null ) {
		if ( ! is_string( $link ) ) {
			return $link;
		}

		if ( ! $context instanceof \GV\Template_Context || empty( $context->field->show_as_link ) ) {
			return $link;
		}

		// Turning on a lightbox turns on `show_as_link` with it (\GV\Field::from_configuration),
		// which is how an Edit Link column reaches a check meant for the value-wrapping anchor.
		if ( 'edit_link' === $context->field->ID ) {
			return $link;
		}

		return preg_replace( '#^(\s*<a)\b#i', '$1 ' . self::EXPORT_LABEL_ATTRIBUTE . '="1"', $link, 1 );
	}

	/**
	 * Marks the anchor a Phone field dials from.
	 *
	 * The address is built out of the number already on show. In the International format the two
	 * are written differently -- the national form reads, the E.164 form dials -- so comparing the
	 * text cannot tell they are one number, and only the field itself knows.
	 *
	 * A deliberate trade: the export then carries the number exactly as the column displays it,
	 * and the country code the E.164 form adds is not written to the file. A phone column is one
	 * number rendered two ways, and a spreadsheet of it should read like the table it came from.
	 *
	 * @since 3.13.0
	 *
	 * @param string $output  The rendered field.
	 * @param mixed  $context The template context the field was rendered in.
	 *
	 * @return string The rendered field, with its dialing anchor marked.
	 */
	public static function mark_phone_link( $output, $context = null ) {
		if ( ! is_string( $output ) ) {
			return $output;
		}

		// The entry-link wrapper can sit outside this one, so the `tel:` anchor is found by its
		// own destination rather than by being first.
		return preg_replace(
			'#<a\b(?=[^>]*\shref="tel:)#i',
			'<a ' . self::EXPORT_LABEL_ATTRIBUTE . '="1"',
			$output,
			1
		);
	}

	/**
	 * The character a Number column writes its decimal part after, where the column fixes one.
	 *
	 * The client orders a Number column on the cell as it was rendered, and cannot tell from the
	 * digits which of `.` and `,` is the decimal point: under Decimal Comma "0,125" is an eighth,
	 * while everywhere else a group of three after the separator is a thousand.
	 *
	 * Only Decimal Comma needs saying. Decimal Dot writes a bare number the client reads on its
	 * own, and Currency fixes nothing at all: Gravity Forms formats each entry with the currency
	 * stored on that entry, so a column whose site currency changed holds both "$1.23" and
	 * "1.596,84 €" at once. Both are read per value, which works because every currency Gravity
	 * Forms carries groups in threes and writes at most two decimals.
	 *
	 * @since 3.13.1
	 *
	 * @param mixed $gf_field The Gravity Forms field, or null where the column has none.
	 *
	 * @return string ',', or '' where the value speaks for itself.
	 */
	private static function get_number_decimal_separator( $gf_field ) {
		return 'decimal_comma' === (string) \GV\Utils::get( $gf_field, 'numberFormat', '' ) ? ',' : '';
	}

	/**
	 * Get column width as a % from the field setting
	 *
	 * @since 1.3
	 *
	 * @param array $field_setting Array of settings for the field
	 *
	 * @return string|null If not empty, string in "{number}%" format. Otherwise, null.
	 */
	private function get_column_width( $field_setting ) {

		$raw_width = rgar( (array) $field_setting, 'width' );

		if ( empty( $raw_width ) ) {
			return null;
		}

		// The unit is mandatory: a bare number is not a valid CSS width and the browser drops
		// the declaration.
		$clamped_width = min( 100, max( 1, absint( $raw_width ) ) );

		return $clamped_width . '%';
	}

	/**
	 * Returns the `sort` URL parameter as a DataTables `order` array, if it applies.
	 *
	 * \GV\View::get_entries() honours `$_GET['sort']` ahead of the View's own sort settings,
	 * but only when column sorting is enabled and the parameter holds a non-empty array.
	 *
	 * @since 3.11.0
	 *
	 * @param View  $view    The View being rendered.
	 * @param array $columns The DataTables column definitions, in column order.
	 *
	 * @return array Order pairs of [ column index, direction ]. Empty when the URL sets no order.
	 */
	private function get_url_sort_order( $view, $columns ) {
		if ( ! $view->settings->get( 'sort_columns' ) ) {
			return array();
		}

		$url_sort = \GV\Utils::_GET( 'sort' );

		if ( ! is_array( $url_sort ) || ! array_filter( $url_sort ) ) {
			return array();
		}

		$order = array();

		foreach ( $url_sort as $field_id => $direction ) {
			$direction = strtolower( (string) $direction );

			// RAND has no DataTables equivalent; leaving it unseeded keeps the configured order
			// in the header rather than advertising an ascending sort the rows will not follow.
			if ( ! in_array( $direction, array( 'asc', 'desc' ), true ) ) {
				continue;
			}

			foreach ( $columns as $index => $column ) {
				// Same form-qualified match the order loop uses, falling back to the bare name
				// for the common case where the URL carries a plain field ID.
				$matches_sort_name = isset( $column['sortName'] ) && $column['sortName'] === (string) $field_id;
				$matches_name      = isset( $column['name'] ) && 'gv_' . $field_id === $column['name'];

				if ( ! $matches_sort_name && ! $matches_name ) {
					continue;
				}

				$order[] = array( $index, $direction );

				break;
			}
		}

		return $order;
	}

	/**
	 * Removes the `sort` URL parameter once the visitor has ordered the table themselves.
	 *
	 * The AJAX handler rebuilds `$_GET` from the page's query string, and
	 * \GV\View::get_entries() prefers `$_GET['sort']` over the sort settings this request just
	 * wrote — replacing them wholesale, not merging. A `sort` parameter left in the address bar
	 * would therefore discard every column the visitor sorts, for as long as it is there.
	 *
	 * Only the visitor's own ordering displaces it. Every other request — the first draw,
	 * paging, searching — still honours the URL, so a `sort` link keeps working even for a
	 * field that is not a column and on pages served from a full-page cache.
	 *
	 * @since 3.11.0
	 *
	 * @return void
	 */
	private function drop_url_sort_when_visitor_reordered() {
		if ( ! \GV\Utils::_POST( 'visitorReordered' ) ) {
			return;
		}

		unset( $_GET['sort'] );
	}

	/**
	 * The SQL OFFSET to apply for server-side AJAX paging: DataTables' own $_POST['start']
	 * plus the View's "Offset entries starting from" setting, a content filter that always
	 * applies on top of pagination.
	 *
	 * @since 3.12.0
	 *
	 * @param int $offset      DataTables' $_POST['start'].
	 * @param int $view_offset The View's "Offset entries starting from" setting.
	 *
	 * @return int
	 */
	public static function get_query_offset( $offset, $view_offset ) {
		return $view_offset + $offset;
	}

	/**
	 * Returns the sort fields that need a hidden column, resolved to the form that owns them.
	 *
	 * DataTables refuses to initialize when the header cell count and the column config
	 * disagree ("Incorrect column count"), so the template and the config must derive their
	 * hidden sort columns from this one list. A joined form's field is stored as
	 * `<form_id>_<field_id>` and does not resolve against the primary form, so a Multiple
	 * Forms View sorted by a joined field needs the owning form to place its header cell and
	 * its column config in agreement.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view The View.
	 *
	 * @return array<int,array{sort_field:string,form_id:int,field_id:string,field:mixed}>
	 */
	public static function get_hidden_sort_fields( $view ) {
		if ( ! $view || ! $view->form || ! class_exists( 'GFAPI' ) ) {
			return array();
		}

		$settings    = get_post_meta( $view->ID, '_gravityview_template_settings', true );
		$sort_fields = array_unique( (array) \GV\Utils::get( $settings, 'sort_field', array() ) );
		$visible_ids = self::get_visible_field_ids( $view );
		$hidden      = array();

		foreach ( $sort_fields as $sort_field ) {
			$is_displayed = '' === (string) $sort_field || in_array( (string) $sort_field, $visible_ids, true );

			if ( $is_displayed ) {
				continue;
			}

			$resolved = self::resolve_sort_field_source( $view, (string) $sort_field );

			if ( null === $resolved ) {
				gravityview()->log->debug(
					'[DataTables] Sort field does not resolve to any form on this View; no hidden column added',
					array( 'data' => array( 'sort_field' => $sort_field, 'view_id' => $view->ID ) )
				);

				continue;
			}

			$hidden[] = $resolved;
		}

		return $hidden;
	}

	/**
	 * Field IDs rendered as visible table columns.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view The View.
	 *
	 * @return string[]
	 */
	public static function get_visible_field_ids( $view ) {
		$ids           = array();
		$primary_form_id = $view->form ? (int) $view->form->ID : 0;

		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $field ) {
			if ( 'custom' === $field->type ) {
				$ids[] = 'custom_' . $field->UID;

				continue;
			}

			$field_form_id = (int) $field->form_id;

			// A joined column's sort_field is form-qualified (`<form_id>_<field_id>`, see
			// resolve_sort_field_source()), but the field's own ID is bare. Recording the bare
			// ID here would never match a joined sort_field, so get_hidden_sort_fields() would
			// treat an already-displayed joined column as hidden and add a duplicate column.
			$ids[] = ( $field_form_id && $field_form_id !== $primary_form_id )
				? $field_form_id . '_' . $field->ID
				: (string) $field->ID;
		}

		return $ids;
	}

	/**
	 * Classifies a sort field name posted by the client for the AJAX order path.
	 *
	 * A bare name (a field ID, an input ID like `1.3`, or an internal field name) is
	 * `primary` and passes through to core untouched. A `<form_id>_` prefixed name is
	 * `joined` once the prefix names a form this View actually joins, or `unresolvable`
	 * otherwise: the client does not get to widen the query's form surface.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view       The View.
	 * @param string   $sort_field The posted sort field, with the `gv_` prefix already removed.
	 *
	 * @return array{status:string,field_id:string,form_id?:int} `status` is primary, joined or unresolvable; `field_id` is empty when unresolvable.
	 */
	public static function classify_ajax_sort_column( $view, $sort_field ) {
		$sort_field      = (string) $sort_field;
		$has_form_prefix = (bool) preg_match( '/^\d+_/', $sort_field );

		if ( ! $has_form_prefix ) {
			return array( 'status' => 'primary', 'field_id' => $sort_field );
		}

		$resolved = self::resolve_sort_field_source( $view, $sort_field );

		if ( null === $resolved ) {
			return array( 'status' => 'unresolvable', 'field_id' => '' );
		}

		$is_primary_form = (int) $view->form->ID === (int) $resolved['form_id'];

		return array(
			'status'   => $is_primary_form ? 'primary' : 'joined',
			'form_id'  => (int) $resolved['form_id'],
			'field_id' => (string) $resolved['field_id'],
		);
	}

	/**
	 * Resolves a sort field to the form that owns it: the primary form, or a joined one.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view       The View.
	 * @param string   $sort_field The stored sort field.
	 *
	 * @return array{sort_field:string,form_id:int,field_id:string,field:mixed}|null
	 */
	private static function resolve_sort_field_source( $view, $sort_field ) {
		$has_form_prefix = (bool) preg_match( '/^(\d+)_(.+)$/', $sort_field, $matches );

		// A Gravity Forms field ID never contains an underscore (inputs use a dot, as in
		// 1.3), so this shape is always a form prefix and must not be looked up on the
		// primary form: GFFormsModel::get_field() compares loosely, and on PHP 7.4
		// `4 == '4_2'` is true, so the primary form's field 4 matches a joined form's name.
		if ( ! $has_form_prefix ) {
			$primary_field     = GFAPI::get_field( $view->form->ID, $sort_field );
			$is_internal_field = class_exists( 'GravityView_Fields' ) && GravityView_Fields::get( $sort_field );

			if ( $primary_field || $is_internal_field ) {
				return array(
					'sort_field' => $sort_field,
					'form_id'    => (int) $view->form->ID,
					'field_id'   => $sort_field,
					'field'      => $primary_field ? $primary_field : null,
				);
			}

			return null;
		}

		$prefixed_form_id = (int) $matches[1];
		$field_id         = $matches[2];
		$allowed_form_ids = self::get_sortable_form_ids( $view );

		if ( ! in_array( $prefixed_form_id, $allowed_form_ids, true ) ) {
			return null;
		}

		$joined_field = GFAPI::get_field( $prefixed_form_id, $field_id );

		if ( ! $joined_field ) {
			return null;
		}

		return array(
			'sort_field' => $sort_field,
			'form_id'    => $prefixed_form_id,
			'field_id'   => $field_id,
			'field'      => $joined_field,
		);
	}

	/**
	 * Form IDs a View may sort by: its own form plus any joined forms.
	 *
	 * Bounds the sort surface, since the sort field also arrives from the AJAX request.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view The View.
	 *
	 * @return int[]
	 */
	public static function get_sortable_form_ids( $view ) {
		$form_ids = array( (int) $view->form->ID );

		foreach ( (array) $view->joins as $join ) {
			if ( ! empty( $join->join->ID ) ) {
				$form_ids[] = (int) $join->join->ID;
			}

			if ( ! empty( $join->join_on->ID ) ) {
				$form_ids[] = (int) $join->join_on->ID;
			}
		}

		return array_values( array_unique( $form_ids ) );
	}

	/**
	 * Writes the normalized widths onto the built columns.
	 *
	 * @since 3.12.0
	 *
	 * @param array    $columns The built column configs.
	 * @param \GV\View $view    The View.
	 *
	 * @return array
	 */
	private function apply_normalized_widths( array $columns, $view ) {
		$normalized   = self::get_normalized_column_widths( $view );
		$column_index = 0;

		foreach ( $columns as $index => $column ) {
			$is_sort_only = false !== strpos( (string) \GV\Utils::get( $column, 'className', '' ), 'gv-hidden-sort-column' );

			if ( $is_sort_only ) {
				continue;
			}

			$width = \GV\Utils::get( $normalized, $column_index );

			$columns[ $index ]['width'] = null === $width ? null : $width . '%';

			++$column_index;
		}

		return $columns;
	}

	/**
	 * Normalized column widths for a View, in visible column order.
	 *
	 * The rendered widths come from the `<th style>` the table template writes, because
	 * DataTables ignores `columns.width` once `autoWidth` is off (it only applies widths
	 * inside `_fnCalculateColumnWidths`, which is gated on that option). The template and the
	 * JS config therefore both read the widths from here, so a clamp or a rescale cannot
	 * apply to one and not the other.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view The View.
	 *
	 * @return array<int,int|null> Percentages keyed by visible column index; null means size by content.
	 */
	public static function get_normalized_column_widths( $view ) {
		$configuration = $view->fields->by_position( 'directory_table-columns' )->by_visible()->as_configuration();
		$fields        = \GV\Utils::get( $configuration, 'directory_table-columns', array() );
		$raw           = array();

		foreach ( array_values( (array) $fields ) as $index => $field_config ) {
			$width = \GV\Utils::get( $field_config, 'width' );

			$raw[ $index ] = empty( $width ) ? null : min( 100, max( 1, absint( $width ) ) );
		}

		return self::normalize_width_values( $raw );
	}

	/**
	 * Clamps a set of column widths into a coherent layout.
	 *
	 * Widths over 100 in total are treated as ratios so the result does not depend on the
	 * browser. Columns without a width take an equal share of what is left, so a partial
	 * configuration remains a coherent set of percentages. When the set widths claim
	 * everything, there is no share to give and every width is dropped so the table sizes by
	 * content instead of rendering unusable columns.
	 *
	 * @since 3.12.0
	 *
	 * @param array<int,int|null> $widths Percentages keyed by column index; null for unset.
	 *
	 * @return array<int,int|null>
	 */
	public static function normalize_width_values( array $widths ) {
		$set     = array_filter( $widths, function ( $width ) { return null !== $width; } );
		$unset   = array_keys( array_diff_key( $widths, $set ) );
		$total   = array_sum( $set );

		if ( empty( $set ) ) {
			return $widths;
		}

		if ( $total > 100 ) {
			$scaled    = array();
			$remaining = 100;

			foreach ( $set as $index => $width ) {
				$scaled[ $index ] = max( 1, (int) floor( $width / $total * 100 ) );
				$remaining       -= $scaled[ $index ];
			}

			if ( $remaining > 0 ) {
				$widest             = array_search( max( $scaled ), $scaled, true );
				$scaled[ $widest ] += $remaining;
			}

			$set   = $scaled;
			$total = array_sum( $set );
		}

		foreach ( $set as $index => $width ) {
			$widths[ $index ] = $width;
		}

		if ( empty( $unset ) ) {
			return $widths;
		}

		$remaining = 100 - $total;

		if ( $remaining < count( $unset ) ) {
			return array_map( function () { return null; }, $widths );
		}

		$share    = (int) floor( $remaining / count( $unset ) );
		$leftover = $remaining - ( $share * count( $unset ) );
		$last     = end( $unset );

		foreach ( $unset as $index ) {
			$widths[ $index ] = $index === $last ? $share + $leftover : $share;
		}

		return $widths;
	}

	/**
	 * The form that owns a searched column, read from the View's stored configuration.
	 *
	 * A Multiple Forms View shows columns from more than one form, and resolving them all
	 * against the View's own form queries the wrong field: wrong results, or none. Complex
	 * fields suffer most, since Name and Address tokenization needs the real field object to
	 * recognize them.
	 *
	 * The stored configuration is authoritative; the client posts a form_id too, but a query
	 * must not be built from it.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view     The View.
	 * @param string   $field_id The searched column's field ID.
	 *
	 * @return int
	 */
	public static function resolve_column_form_id( $view, $field_id ) {
		$primary_form_id = (int) $view->form->ID;
		$configured      = $view->fields->by_position( 'directory_table-columns' )->by_visible()->as_configuration();
		$fields          = \GV\Utils::get( $configured, 'directory_table-columns', array() );

		foreach ( (array) $fields as $field_config ) {
			$matches_column = (string) \GV\Utils::get( $field_config, 'id', '' ) === (string) $field_id;

			if ( ! $matches_column ) {
				continue;
			}

			$configured_form_id = (int) \GV\Utils::get( $field_config, 'form_id', 0 );
			$is_known_form      = in_array( $configured_form_id, self::get_sortable_form_ids( $view ), true );

			if ( $configured_form_id && $is_known_form ) {
				return $configured_form_id;
			}

			if ( $configured_form_id && ! $is_known_form ) {
				gravityview()->log->error(
					'[DataTables] Column configured with a form that is not this View\'s form or a join; falling back to the primary form',
					array( 'data' => array( 'field_id' => $field_id, 'form_id' => $configured_form_id, 'view_id' => $view->ID ) )
				);
			}

			break;
		}

		return $primary_form_id;
	}

	/**
	 * Adds the legacy width-styling hook when a View configures any column width.
	 *
	 * The class name predates the automatic-layout implementation. It now supplies the
	 * border-box and wrapping rules needed by width-configured tables and FixedHeader clones;
	 * it does not select CSS fixed layout.
	 *
	 * Runs before the template prints the `<table>` tag, since the footer config is built
	 * afterwards and a filter registered there would arrive too late.
	 *
	 * @since 3.12.0
	 *
	 * @param object $gravityview The template context.
	 *
	 * @return void
	 */
	public static function maybe_enable_fixed_layout( $gravityview ) {
		if ( empty( $gravityview->view ) ) {
			return;
		}

		// Several Views can render on one page, so the filter must come back off once this
		// one is done or a later width-less View inherits its width-specific cell styling.
		add_action( 'gravityview/template/after', array( __CLASS__, 'disable_fixed_layout' ), 5 );

		$configuration = $gravityview->view->fields->by_position( 'directory_table-columns' )->by_visible()->as_configuration();
		$fields        = \GV\Utils::get( $configuration, 'directory_table-columns', array() );
		$has_widths    = false;

		foreach ( (array) $fields as $field_config ) {
			if ( ! empty( $field_config['width'] ) ) {
				$has_widths = true;

				break;
			}
		}

		if ( ! $has_widths ) {
			return;
		}

		self::$exact_widths = class_exists( 'GV_Extension_DataTables_Column_Widths' )
			&& GV_Extension_DataTables_Column_Widths::is_exact( $gravityview->view->ID );

		add_filter( 'gravityview_datatables_table_class', array( __CLASS__, 'add_fixed_layout_class' ) );
	}

	/**
	 * Adds the legacy class used to style width-configured tables and their header clones.
	 *
	 * @since 3.12.0
	 *
	 * @param string $classes Existing table classes.
	 *
	 * @return string
	 */
	public static function add_fixed_layout_class( $classes ) {
		$classes = trim( $classes . ' gv-dt-fixed-layout' );

		// A View asking for exact percentages opts back into CSS fixed layout, which keeps the
		// configured share of the table whatever the screen does to the values inside it.
		if ( self::$exact_widths ) {
			$classes = trim( $classes . ' ' . GV_Extension_DataTables_Column_Widths::EXACT_CLASS );
		}

		return $classes;
	}

	/**
	 * Whether the View being rendered keeps its percentages exactly.
	 *
	 * @since 3.13.0
	 *
	 * @var bool
	 */
	private static $exact_widths = false;

	/**
	 * Stops applying the width-styling hook once the View that asked for it has rendered.
	 *
	 * @since 3.12.0
	 *
	 * @return void
	 */
	public static function disable_fixed_layout() {
		self::$exact_widths = false;

		remove_filter( 'gravityview_datatables_table_class', array( __CLASS__, 'add_fixed_layout_class' ) );
		remove_action( 'gravityview/template/after', array( __CLASS__, 'disable_fixed_layout' ), 5 );
	}

	/**
	 * Get the original sort_field setting from saved View meta.
	 *
	 * This retrieves the sort_field from the saved post meta rather than from
	 * runtime settings, which may have been modified by AJAX sorting requests.
	 *
	 * @since 3.7.1
	 *
	 * @param View $view The View object.
	 *
	 * @return array The sort_field setting as an array.
	 */
	private function get_original_sort_field_setting( $view ) {
		$original_settings = get_post_meta( $view->ID, '_gravityview_template_settings', true );

		// array_unique() must NOT be followed by array_values() — the original keys must be
		// preserved so that $sort_direction[$index] lookups remain paired with the correct field.
		return array_unique( (array) ( isset( $original_settings['sort_field'] ) ? $original_settings['sort_field'] : [] ) );
	}

	/**
	 * Get the original sort_direction setting from saved View meta.
	 *
	 * This retrieves the sort_direction from the saved post meta rather than from
	 * runtime settings, which may have been modified by shortcode attributes.
	 *
	 * @since 3.7.3
	 *
	 * @param View $view The View object.
	 *
	 * @return array The sort_direction setting as an array.
	 */
	private function get_original_sort_direction_setting( $view ) {
		$original_settings = get_post_meta( $view->ID, '_gravityview_template_settings', true );

		return (array) ( isset( $original_settings['sort_direction'] ) ? $original_settings['sort_direction'] : [] );
	}

	/**
	 * The subset of a request's `shortcode_atts` that is safe to apply to the View's settings.
	 *
	 * This runs on a nopriv endpoint, so the payload is request data from an unauthenticated
	 * caller; the accompanying nonce proves the request's origin, not the caller's authorization
	 * or the payload's contents. Applied values may therefore only ever NARROW the result set.
	 *
	 * An author's entry pre-filter is part of the View's configuration, so a request cannot be
	 * allowed to replace or clear one: when `$trusted['search_value']` is present, the request's
	 * copy is discarded whole. An empty `search_value` is as dangerous as a conflicting one,
	 * because GravityView omits the implicit filter entirely for an empty value.
	 *
	 * With no server-derived filter, the same keys are accepted: they can only add a predicate,
	 * and for a View embedded via `do_shortcode()` or a page builder that stores content outside
	 * `post_content`, the request's copy is the only place the author's filter survives.
	 *
	 * @since 3.12.0
	 *
	 * @param mixed $posted  Raw `shortcode_atts` from the request. Any type; non-arrays yield no atts.
	 * @param array $trusted Server-derived `search_field` / `search_value` / `search_operator`.
	 *
	 * @return array<string,string> Attributes to apply, keyed by setting name. Empty when none qualify.
	 */
	public static function filter_posted_shortcode_atts( $posted, array $trusted = array() ) {
		if ( ! is_array( $posted ) || array() === $posted ) {
			return array();
		}

		if ( '' !== (string) \GV\Utils::get( $trusted, 'search_value', '' ) ) {
			gravityview()->log->debug( 'Discarding posted shortcode_atts: the View already carries a server-derived search_value.' );

			return array();
		}

		$allowed_operators = array( 'is', 'isnot', '>', '<', 'contains' );

		$atts = array();

		foreach ( array( 'search_field', 'search_value', 'search_operator' ) as $key ) {
			if ( ! isset( $posted[ $key ] ) || ! is_scalar( $posted[ $key ] ) ) {
				continue;
			}

			// The operator is matched against a fixed set rather than sanitized: comparison is
			// the stricter check, and sanitize_text_field() would strip the `<` and `>` operators
			// to an empty string.
			if ( 'search_operator' === $key ) {
				if ( in_array( (string) $posted[ $key ], $allowed_operators, true ) ) {
					$atts[ $key ] = (string) $posted[ $key ];
				}

				continue;
			}

			$atts[ $key ] = sanitize_text_field( (string) $posted[ $key ] );
		}

		return $atts;
	}

	/**
	 * Whitelisted sort-related shortcode/block attributes for the current render.
	 *
	 * Core writes every parsed shortcode/block attribute onto the View's runtime settings
	 * before this method ever runs (`GravityViewShortcode::render()`, core
	 * `src/Shortcode/GravityViewShortcode.php:174`), already sanitized against each setting's
	 * declared options. `get_original_sort_field_setting()` / `get_original_sort_direction_setting()`
	 * intentionally bypass that runtime object because the AJAX handler also writes to it
	 * (`:646`); this method reads only the `shortcode_atts` snapshot core placed there at
	 * render time, which the AJAX handler never touches.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view The View being rendered.
	 *
	 * @return array<string,string> Present-and-non-empty overrides, keyed by setting name.
	 */
	private function get_shortcode_sort_overrides( $view ) {
		$shortcode_atts = (array) $view->settings->get( 'shortcode_atts', array() );

		$overrides = array();

		foreach ( array( 'sort_field', 'sort_direction', 'sort_field_2', 'sort_direction_2' ) as $key ) {
			if ( isset( $shortcode_atts[ $key ] ) && '' !== $shortcode_atts[ $key ] ) {
				$overrides[ $key ] = $shortcode_atts[ $key ];
			}
		}

		return $overrides;
	}

	/**
	 * Effective sort field/direction pairs for this render: the saved View meta, overlaid
	 * with validated shortcode/block sort overrides.
	 *
	 * Render-time only. `get_hidden_sort_fields()` and `get_output_data()` (the AJAX path)
	 * must keep computing hidden columns from saved meta alone -- that AJAX endpoint is
	 * nopriv and `$_POST['shortcode_atts']` is attacker-writable, so letting a posted att
	 * create a hidden sort column would give an unauthenticated visitor a read primitive on
	 * any non-displayed field. This method is never called from the AJAX handler.
	 *
	 * An override's `sort_field` only takes effect when it names a column already present in
	 * `$columns` (a visible column or one of `get_hidden_sort_fields()`'s hidden columns); an
	 * override naming anything else is dropped in its entirety -- both field and direction
	 * fall back to the saved pair -- and logged. `sort_direction` (or `sort_direction_2`)
	 * posted without a matching `sort_field` override replaces only that index's direction.
	 *
	 * Shared with `GV_Extension_DataTables_Processing_Mode::add_config()`, so the
	 * "does this render want a random sort" check (F-9) sees exactly the same effective sort
	 * this method's caller turns into `order`, including a `sort_direction=RAND` override
	 * that survived core's sanitization.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view    The View being rendered.
	 * @param array    $columns The columns already built for this render (visible + hidden-sort).
	 *
	 * @return array{fields:array,directions:array} Keyed the same way the `sort_field` / `sort_direction` settings are.
	 */
	public function get_effective_sort( $view, array $columns ) {
		$sort_direction    = $this->get_original_sort_direction_setting( $view );
		$sort_field_setting = $this->get_original_sort_field_setting( $view );

		$sort_overrides = $this->get_shortcode_sort_overrides( $view );

		foreach ( array( 0 => '', 1 => '_2' ) as $index => $suffix ) {
			$override_field     = \GV\Utils::get( $sort_overrides, 'sort_field' . $suffix );
			$override_direction = \GV\Utils::get( $sort_overrides, 'sort_direction' . $suffix );

			if ( null !== $override_field ) {
				$override_column_exists = false;

				foreach ( $columns as $column ) {
					// Same form-qualified match as the order loop in
					// get_datatables_script_configuration(); a bare `name` comparison has the
					// identical blind spot for a shortcode sort_field naming a displayed joined column.
					if ( isset( $column['sortName'] ) && $column['sortName'] === (string) $override_field ) {
						$override_column_exists = true;

						break;
					}
				}

				if ( ! $override_column_exists ) {
					gravityview()->log->debug(
						'[DataTables] Shortcode/block sort_field{suffix} override does not match a visible or hidden-sort column; falling back to the saved sort.',
						array(
							'data' => array(
								'suffix'     => $suffix,
								'sort_field' => $override_field,
								'view_id'    => $view->ID,
							),
						)
					);

					continue;
				}

				$sort_field_setting[ $index ] = $override_field;

				if ( null !== $override_direction ) {
					$sort_direction[ $index ] = $override_direction;
				}
			} elseif ( null !== $override_direction ) {
				$sort_direction[ $index ] = $override_direction;
			}
		}

		return array(
			'fields'     => $sort_field_setting,
			'directions' => $sort_direction,
		);
	}

	/**
	 * Calculates the user ID and Session Token to be used when calculating the Cache Key
	 *
	 * @return string
	 */
	function get_user_session() {

		if ( ! is_user_logged_in() ) {
			return '';
		}

		if ( isset( $_GET['cache'] ) ) {
			return '';
		}

		/**
		 * @see wp_get_session_token()
		 */
		$cookie = wp_parse_auth_cookie( '', 'logged_in' );
		$token  = ! empty( $cookie['token'] ) ? $cookie['token'] : '';

		return get_current_user_id() . '_' . $token;
	}

	/**
	 * Override the template class to the correct one.
	 *
	 * @param string   $class The class to use as the template class.
	 * @param View $view  The View we're looking at.
	 *
	 * @return string The class
	 */
	public function set_view_template_class( $class, $view ) {

		if ( $view->settings->get( 'template' ) == 'datatables_table' ) {
			return '\GV\View_DataTable_Template';
		}

		return $class;
	}

	/**
	 * Override the template class to use the correct one.
	 *
	 * @param string    $class The class to use as the template class.
	 * @param \GV\Entry $entry The Entry we're looking at.
	 * @param View  $view  The View we're looking at.
	 *
	 * @return string The class
	 */
	public function set_entry_template_class( $class, $entry, $view ) {

		$template = $view->settings->get( 'template' );

		// Get the single entry template, with a fallback to the regular template for older versions of GravityView.
		$template = $view->settings->get( 'template_single_entry', $template );

		if ( 'datatables_table' === $template ) {
			return Entry_DataTable_Template::class;
		}

		return $class;
	}

	/**
	 * Include this extension templates path
	 *
	 * @param array $file_paths List of template paths ordered
	 */
	function add_template_path( $file_paths ) {

		// Index 100 is the default GravityView template path.
		// Find an empty spot afterwards and avoid overwriting other registered spots
		for ( $i = 101; ; $i++ ) {
			if ( ! empty( $file_paths[ $i ] ) ) {
				continue;
			}
			$file_paths[ $i ] = GV_DT_DIR . 'templates/deprecated';
			break;
		}

		return $file_paths;
	}

	/**
	 * Generate the values for the page length menu
	 *
	 * @filter  gravityview_datatables_lengthmenu Modify the values shown in the page length menu. Key is the # of results, value is the label for the results.
	 *
	 * @param View $view The View
	 *
	 * @return array            2D array formatted for DataTables
	 */
	function get_length_menu( $view ) {

		$page_size = 25;
		if ( ! $view instanceof View ) {
			if ( $view = View::by_id( $view['id'] ) ) {
				$page_size = $view->settings->get( 'page_size' );
			}
		} else {
			$page_size = $view->settings->get( 'page_size' );
		}

		// Create the array of values for the drop-down page menu
		$values = array(
			(int) $page_size => $page_size,
			10               => 10,
			25               => 25,
			50               => 50,
		);

		// no duplicate values
		$values = array_unique( $values );

		// Sort by the # of results per page
		ksort( $values );

		// Add the "All" option after the rest of them have been sorted by value
		$values[- 1] = _x( 'All', 'Menu label to show all results in DataTables template.', 'gv-datatables' );

		/**
		 * Modifies the values shown in the page length menu.
		 *
		 * Key is the # of results, value is the label for the results.
		 *
		 * @since 3.3.5
		 *
		 * @param array $values Array of values and labels for the page length menu.
		 * @param array $view_data The View data as an array {@see \GV\View::as_data()}.
		 */
		$values = apply_filters( 'gravityview_datatables_lengthmenu', $values, $view->as_data() );

		/**
		 * Prepare a 2D array for the dropdown.
		 *
		 * @link https://datatables.net/examples/advanced_init/length_menu.html
		 */
		$lengthMenu = array(
			array_keys( $values ),
			array_values( $values ),
		);

		return $lengthMenu;
	}

	/**
	 * Get the data for the language parameter used by DataTables
	 *
	 * @since 1.2.3
	 * @since 2.4.4 Added $view parameter; made private
	 *
	 * @param View $view The View
	 *
	 * @return array Array of strings, as used by the DataTables extension's `language` setting
	 */
	private function get_language( $view = null ) {

		$translations = $this->get_translations();

		$locale = get_locale();

		/**
		 * Change the locale used to fetch translations.
		 *
		 * @since 1.2.3
		 *
		 * @param string $locale       The current locale.
		 * @param array  $translations The translations mapping array.
		 */
		$locale = apply_filters( 'gravityview/datatables/config/locale', $locale, $translations );

		$no_entries_text = $view->settings->get( 'no_results_text', __( 'No entries match your request.', 'gv-datatables' ) );
		$no_entries_text = ! $no_entries_text ? __( 'No entries match your request.', 'gv-datatables' ) : $no_entries_text;

		$no_results_text = $view->settings->get( 'no_search_results_text', __( 'This search returned no results.', 'gv-datatables' ) );
		$no_results_text = ! $no_results_text ? __( 'This search returned no results.', 'gv-datatables' ) : $no_results_text;

		/**
		 * Modify the text shown when DataTables is loaded
		 *
		 * @since  2.5 Added $view parameter
		 *
		 * @param string $loading_text Default: Loading data…
		 * @param View $view The View
		 */
		$loading_text = apply_filters( 'gravityview_datatables_loading_text', esc_html__( 'Loading data&hellip;', 'gv-datatables' ), $view );

		// These come from the View's own settings (or this release's error state), so they
		// must win over a bundled locale translation's own words for the same keys, not the
		// other way around.
		$language = [
			'processing'          => $loading_text,
			'zeroRecords'         => esc_html( $no_entries_text ),
			'emptyTable'          => esc_html( $no_results_text ),
			'emptyServerResponse' => esc_html__( 'The server returned an empty response. Check the server logs for more details.', 'gv-datatables' ),
			// These three are inserted client-side via jQuery's `.text()` property (see
			// assets/js/datatables-views.js), which does not decode HTML entities, unlike
			// zeroRecords/emptyTable above (decoded via the JS's own <div>.html().text() step)
			// or emptyServerResponse/processing (inserted as HTML by DataTables). esc_html()
			// here would render as a literal "&#039;"/"&amp;" in the failure box.
			'loadError'           => __( 'This table could not load. Please try again.', 'gv-datatables' ),
			'retry'               => _x( 'Retry', 'Button to retry loading the table', 'gv-datatables' ),
			'errorDetails'        => __( 'Error details (visible to admins only)', 'gv-datatables' ),
		];

		// If a translation exists
		if ( isset( $translations[ $locale ] ) ) {

			ob_start();

			// Get the JSON file
			include GV_DT_DIR . 'assets/js/third-party/datatables/translations/' . $translations[ $locale ] . '.json';

			$json_string = ob_get_clean();

			// If it exists
			if ( ! empty( $json_string ) ) {

				// Parse it into an array
				$json_array = json_decode( $json_string, true );

				// If that worked, layer it under $language: the bundle supplies everything
				// it carries that $language does not (pagination, search, aria labels, …),
				// while $language's own keys are left alone.
				if ( ! empty( $json_array ) ) {
					$language = array_merge( $json_array, $language );
				}
			}
		}

		/**
		 * Override language settings
		 * @since  1.2.2
		 *
		 * {@link https://github.com/DataTables/Plugins/blob/master/i18n/English.lang}
		 * @param array  $translations The translations mapping array from `GV_Extension_DataTables_Data::get_translations()`
		 * @param string $locale       The blog's locale, fetched from `get_locale()`
		 *                             Override language settings
		 *
		 * @param array  $language     The language settings array.\n
		 *                             [See a sample file with all available translations and their matching keys](https://github.com/DataTables/Plugins/blob/master/i18n/English.lang)
		 */
		$language = apply_filters( 'gravityview/datatables/config/language', $language, $translations, $locale );

		return $language;
	}

	/**
	 * Match the DataTables translation file to the WordPress locale setting
	 *
	 * @since 1.2.3
	 *
	 * @return array Key is the WordPress locale string; Value is the name of the file in assets/js/third-party/datatables/translations/ without .json
	 */
	private function get_translations() {
		$translations = array(
			'af'    => 'af', // Afrikaans
			'sq'    => 'sq', // Albanian
			'ar'    => 'ar', // Arabic
			'hy'    => 'hy', // Armenian
			'az'    => 'az-AZ', // Azerbaijani
			'bn_BD' => 'bn', // Bengali
			'eu'    => 'eu', // Basque
			'bel'   => 'be', // Belarusian
			'bs_BA' => 'bs-BA', // Bosnian
			'bg_BG' => 'bg', // Bulgarian
			'ca'    => 'ca', // Catalan
			'zh_CN' => 'zh', // Chinese
			'co'    => 'co', // Corsican
			'hr'    => 'hr', // Croatian
			'cs_CZ' => 'Czech', // Czech
			'da_DK' => 'da', // Danish
			'nl_NL' => 'nl-NL', // Dutch
			'en-GB' => 'en-GB', // British English
			'eo'    => 'eo', // Esperanto
			'et'    => 'et', // Estonian
			// Filipino (not in WP)
			'fi'    => 'fi', // Finnish
			'fr_FR' => 'fr-FR', // French
			'gl_ES' => 'gl', // Galician
			'ka_GE' => 'ka', // Georgian
			'de_DE' => 'de-DE', // German
			'el'    => 'el', // Greek
			'gu'    => 'gu', // Gujarati
			'he_IL' => 'he', // Hebrew
			'hi_IN' => 'hi', // Hindi
			'hu_HU' => 'hu', // Hungarian
			'is_IS' => 'is', // Icelandic
			'id_ID' => 'id', // Indonesian
			'ga'    => 'ga', // Irish
			'it_IT' => 'it-IT', // Italian
			'ja'    => 'ja', // Japanese
			'jv_ID' => 'jv', // Javanese
			'kn'    => 'kn', // Kannada
			'kk'    => 'kk', // Kazakh
			'km'    => 'km', // Khmer
			'ko_KR' => 'ko', // Korean
			'kmr'   => 'ku', // Kurdish
			'kir'   => 'ky', // Kyrgyz
			'lo'    => 'lao', // Lao
			'lv'    => 'lv', // Latvian
			'lt_LT' => 'lt', // Lithuanian
			'lug'   => 'ug', // Luganda
			'mk_MK' => 'mk', // Macedonian
			'ms_MY' => 'ms', // Malay
			'mr'    => 'mr', // Marathi
			'mn'    => 'mn', // Mongolian
			'ne_NP' => 'ne', // Nepali
			'nb_NO' => 'no-NB', // Norwegian Bokmål
			'nn_NO' => 'no-NO', // Norwegian Nynorsk
			'ps'    => 'ps', // Pashto
			'fa_IR' => 'fa', // Persian
			'pl_PL' => 'pl', // Polish
			'pt_PT' => 'pt-PT', // Portuguese
			'pt_BR' => 'pt-BR', // Brazilian Portuguese
			'pa_IN' => 'pa', // Punjabi
			'ro_RO' => 'ro', // Romanian
			'roh'   => 'rm', // Romansh
			'ru_RU' => 'ru', // Russian
			'sr_RS' => 'sr', // Serbian
			// Serbian (Latin) (not in WP)
			'sd_PK' => 'snd', // Sindhi
			'si_LK' => 'si', // Sinhala
			'sk_SK' => 'sk', // Slovak
			'sl_SI' => 'sl', // Slovenian
			'es_ES' => 'es-ES', // Spanish
			'es_AR' => 'es-AR', // Spanish (Argentina)
			'es_CL' => 'es-CL', // Spanish (Chile)
			'es_CO' => 'es-CO', // Spanish (Colombia)
			'es_MX' => 'es-MX', // Spanish (Mexico)
			'sw'    => 'sq', // Swahili
			'sv_SE' => 'sv-SE', // Swedish
			'tg'    => 'tg', // Tajik
			'ta_IN' => 'ta', // Tamil
			'te'    => 'te', // Telugu
			'th'    => 'th', // Thai
			'tr_TR' => 'tr', // Turkish
			'tuk'   => 'tk', // Turkmen
			'uk'    => 'uk', // Ukrainian
			'ur'    => 'ur', // Urdu
			'uz_UZ' => 'uz', // Uzbek
			// Uzbek - Cryllic (not in WP)
			'vi'    => 'vi', // Vietnamese
			'cy'    => 'cy', // Welsh
		);

		return $translations;
	}

	/**
	 * Get the url to the AJAX endpoint in use
	 *
	 * @return string If direct access, it's this file. otherwise, admin-ajax.php
	 */
	function get_ajax_url() {
		return admin_url( 'admin-ajax.php' );
	}

	/**
	 * Generates a unique state key for DataTables based on view configuration.
	 *
	 * This ensures that when view settings change (especially sort fields), the cached
	 * state is invalidated and a fresh state is used.
	 *
	 * @since 3.7.1
	 *
	 * @param View $view The View object
	 *
	 * @return string A unique hash representing the current view configuration
	 */
	private function generate_state_key( $view ) {
		$config_data = array(
			'view_id'        => $view->ID,
			'sort_field'     => $view->settings->get( 'sort_field', array() ),
			'sort_direction' => $view->settings->get( 'sort_direction', array() ),
			'page_size'      => $view->settings->get( 'page_size', 10 ),
			'view_modified'  => get_post_modified_time( 'U', true, $view->ID ),
		);

		// Include visible field IDs to detect column changes
		$visible_field_ids = array();
		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $field ) {
			$visible_field_ids[] = ( 'custom' === $field->type ) ? 'custom_' . $field->UID : $field->ID;
		}
		$config_data['visible_fields'] = $visible_field_ids;

		// Include search parameters so that switching filters resets DataTables pagination state.
		$search_params = array_filter(
			$_GET ?? [],
			static function ( $key ) {
				return 0 === strpos( $key, 'filter_' ) || 'mode' === $key;
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( ! empty( $search_params ) ) {
			ksort( $search_params );
			$config_data['search_params'] = $search_params;
		}

		return substr( md5( wp_json_encode( $config_data ) ), 0, 8 );
	}

	/**
	 * Generate the script configuration array
	 *
	 * @since 1.3.3
	 *
	 * @param WP_Post $post Current View or post/page where View is embedded
	 * @param View    $view The View
	 *
	 * @return array Array of settings formatted as DataTables options array. {@see https://datatables.net/reference/option/}
	 */
	public function get_datatables_script_configuration( $post, $view ) {
		$cache_key = $post->ID . ':' . $view->ID;

		if ( isset( self::$configuration_cache[ $cache_key ] ) ) {
			return self::$configuration_cache[ $cache_key ];
		}

		// The DataTables silo lives on the View post. $post is the embedding page when the
		// View is shortcoded or blocked in, which almost every production View is, and that
		// page never carries this meta key.
		$dt_settings = get_post_meta( $view->ID, '_gravityview_datatables_settings', true );

		$ajax_settings = array(
			'action'            => 'gv_datatables_data',
			'view_id'           => $view->ID,
			'post_id'           => $post->ID ?? $view->get_post()->ID,
			'nonce'             => wp_create_nonce( 'gravityview_datatables_data' ),
			// The client reads a truthy getData as "the visitor is searching" and skips the
			// whole no-entries branch, so a campaign or share link's params must not set it.
			'getData'           => $this->has_search_get_params() ? json_encode( (array) $_GET ) : false,
			'hideUntilSearched' => $view->settings->get( 'hide_until_searched' ),
			'setUrlOnSearch'    => apply_filters( 'gravityview/search/method', 'get' ) === 'get',
			'noEntriesOption'   => (int) $view->settings->get( 'no_entries_options', '0' ),
			'redirectURL'       => $view->settings->get( 'no_entries_redirect', '' ),
			'configHash'        => $this->get_column_signature( $view ),
		);

		// apply shortcode atts
		if ( $view->settings->get( 'shortcode_atts' ) ) {
			$ajax_settings['shortcode_atts'] = $view->settings->get( 'shortcode_atts' );
		}

		// Prepare DataTables init config
		$dt_config = array(
			'processing'    => true,
			'deferRender'   => true,
			// Improves performance https://datatables.net/reference/option/deferRender
			'serverSide'    => true,
			'retrieve'      => true,
			// Only initialize each table once
			'stateSave' => ( 1 === (int) ( $dt_settings['save_state'] ?? 1 ) ) && ! isset( $_GET['cache'] ),
			// On refresh (and on single entry view, then clicking "go back"), save the page you were on.
			'stateDuration' => - 1,
			// Only save the state for the session. Use to time in seconds (like the DAY_IN_SECONDS WordPress constant) if you want to modify.
			'lengthMenu'    => $this->get_length_menu( $view ),
			// Dropdown pagination length menu
			'language'      => $this->get_language( $view ),
			// A transport failure never reaches the server, so no response can carry the
			// admin-only diagnostics; this is how the client knows it may show its own.
			'canSeeDiagnostics' => current_user_can( 'manage_options' ),
			// Kept outside `language`, which is replaced wholesale by the locale file.
			'gvExportError' => array(
				'title'   => __( 'Export failed', 'gv-datatables' ),
				'message' => __( 'The entries could not be loaded, so no file was created. Please try again.', 'gv-datatables' ),
			),
			'ajax'          => array(
				'url'  => $this->get_ajax_url(),
				'type' => 'POST',
				'data' => $ajax_settings,
			),
		);

		// Generates a unique state key based on view configuration to ensure cache invalidation when settings change
		if ( $dt_config['stateSave'] ) {
			$dt_config['stateKey'] = $this->generate_state_key( $view );
		}

		// page size, if defined
		$dt_config['pageLength'] = intval( $view->settings->get( 'page_size', 10 ) );

		// The JS order path needs to know which form is primary to qualify a joined column's
		// posted sort name; guessing it from columns[0].form_id fails when a joined form's
		// column is the first visible column.
		$dt_config['primaryFormId'] = $view->form ? (int) $view->form->ID : 0;

		/**
		 * Set the columns to be displayed
		 *
		 * @link https://datatables.net/reference/option/columns
		 */
		$columns = array();
		$visible_field_ids = array();
		$primary_form_id   = $view->form ? (int) $view->form->ID : 0;

		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $field ) {

			if ( 'custom' == $field->type ) {
				$field_id = 'custom_' . $field->UID;
			} else {
				$field_id = $field->ID;
			}

			$visible_field_ids[] = $field_id;

			$field_config = $field->as_configuration();

			$type       = 'string'; // See https://datatables.net/reference/option/columns.type.
			$field_type = $field->type; // This is the GF/GV field type that we use in the UI to configure filters/etc.

			if ( in_array( $field_type, [ 'date', 'date_created', 'date_updated', 'payment_date' ] ) ) {
				$field_type = 'date';
				$type       = 'num';
			}

			$decimal_separator = '';

			if ( 'number' === $field_type ) {
				$type              = 'num';
				$decimal_separator = self::get_number_decimal_separator( $field->field );
			}

			// A clock reading compared as text puts the afternoon ahead of the morning
			// ("01:30 PM" before "08:05 AM"); the client turns it into seconds instead. Only the
			// whole field: a Time input on its own renders one part of the reading, and "PM" is
			// not a number.
			if ( 'time' === $field_type && false === strpos( (string) $field->ID, '.' ) ) {
				$type = 'num';
			}

			// `name` stays bare (it is also the per-column search key on the wire, see
			// get_column_searches()); `sortName` is form-qualified like a joined column's saved
			// sort_field, so two columns sharing a bare field ID across forms (e.g. both "gv_2")
			// resolve unambiguously. Mirrors get_visible_field_ids()'s expression.
			$field_form_id = 'custom' === $field->type ? 0 : (int) $field->form_id;
			$sort_name     = ( $field_form_id && $field_form_id !== $primary_form_id )
				? $field_form_id . '_' . $field->ID
				: (string) $field_id;

			$field_column = [
				'name'       => 'gv_' . $field_id,
				'sortName'   => $sort_name,
				'width'      => $this->get_column_width( $field_config ),
				'form_id'    => rgar( $field_config, 'form_id' ),
				'className'  => gravityview_sanitize_html_class( \GV\Utils::get( $field_config, 'custom_class', '' ) ),
				'type'       => $type,
				'field_type' => $field_type,
			];

			if ( '' !== $decimal_separator ) {
				$field_column['decimal_separator'] = $decimal_separator;
			}

			/**
			 * Check if fields are sortable. If not, set `orderable` to false.
			 *
			 * @since 1.3.3
			 */
			if ( $view->form && class_exists( 'GravityView_Fields' ) && class_exists( 'GFFormsModel' ) ) {
				$gf_field = $field->field;
				$type     = \GV\Utils::get( $gf_field, 'type', $field->ID );
				$gv_field = GravityView_Fields::get( $type );

				// If the field does exist, use the field's sortability setting
				if ( $gv_field && ! $gv_field->is_sortable ) {
					$field_column['orderable'] = false;
				}
			}

			$columns[] = $field_column;
		}

		// Shared with the table template, so the header cells and this column list cannot
		// disagree; DataTables refuses to initialize when they do.
		$hidden_sort_fields   = self::get_hidden_sort_fields( $view );
		$hidden_column_indices = [];

		foreach ( $hidden_sort_fields as $hidden_sort_field ) {
			$sort_field = $hidden_sort_field['sort_field'];
			$gf_field   = $hidden_sort_field['field'];

			$field_type = $gf_field ? $gf_field->type : $hidden_sort_field['field_id'];
			$type = 'string';

			if ( in_array( $field_type, [ 'date', 'date_created', 'date_updated', 'payment_date' ] ) ) {
				$type = 'num';
			}

			$decimal_separator = '';

			if ( 'number' === $field_type ) {
				$type              = 'num';
				$decimal_separator = self::get_number_decimal_separator( $gf_field );
			}

			if ( 'time' === $field_type && false === strpos( (string) $hidden_sort_field['field_id'], '.' ) ) {
				$type = 'num';
			}

			$hidden_column_index = count( $columns );
			$hidden_column_indices[] = $hidden_column_index;

			$hidden_column = [
				'name'       => 'gv_' . $sort_field,
				'sortName'   => (string) $sort_field, // Already form-qualified; this IS the sort_field.
				'width'      => null,
				'form_id'    => $hidden_sort_field['form_id'],
				'className'  => 'gv-hidden-sort-column',
				'type'       => $type,
				'field_type' => $field_type,
				'orderable'  => true, // Hidden sort columns should be orderable.
			];

			if ( '' !== $decimal_separator ) {
				$hidden_column['decimal_separator'] = $decimal_separator;
			}

			$columns[] = $hidden_column;
		}

		$columns = $this->apply_normalized_widths( $columns, $view );

		/**
		 * Modifies the column widths after normalization.
		 *
		 * @since 3.12.0
		 *
		 * @param array    $widths_by_column_index Width values, keyed by column index.
		 * @param \GV\View $view                   The View being rendered.
		 */
		$filtered_widths = apply_filters(
			'gk/gravityview/datatables/columns/widths',
			wp_list_pluck( $columns, 'width' ),
			$view
		);

		foreach ( (array) $filtered_widths as $index => $width ) {
			if ( isset( $columns[ $index ] ) ) {
				$columns[ $index ]['width'] = $width;
			}
		}

		$configured_widths = array_filter(
			wp_list_pluck( $columns, 'width' ),
			function ( $width ) {
				return null !== $width && '' !== $width;
			}
		);

		// Keep DataTables from replacing the configured percentages with measured pixels on
		// every draw. The browser may still widen a column to its content minimum under auto
		// layout, which is intentional: readability wins when the two constraints conflict.
		if ( ! empty( $configured_widths ) ) {
			$dt_config['autoWidth']   = false;
			$dt_config['gvHasWidths'] = true;

		}

		$dt_config['columns'] = $columns;
		if ( ! empty( $hidden_column_indices ) ) {
			if ( ! isset( $dt_config['columnDefs'] ) ) {
				$dt_config['columnDefs'] = array();
			}

			$dt_config['columnDefs'][] = [
				'visible' => false,
				'targets' => $hidden_column_indices,
			];
		}

		// Effective sort: saved View meta overlaid with validated shortcode/block sort
		// overrides (F-8) -- see get_effective_sort().
		$effective_sort     = $this->get_effective_sort( $view, $columns );
		$sort_field_setting = $effective_sort['fields'];
		$sort_direction     = $effective_sort['directions'];

		// A direction of Random cannot page coherently: DataTables passes 'rand' back on
		// every request and core re-randomizes the whole result set each time, so pages
		// duplicate and drop rows (F-9). Such an order entry is omitted; if any direction was
		// random, `order` is forced to an explicit empty array below.
		$has_random_sort = false;

		foreach ( $sort_field_setting as $l => $sort_field ) {
			$direction = strtolower( \GV\Utils::get( $sort_direction, $l, 'asc' ) );

			if ( 'rand' === $direction ) {
				$has_random_sort = true;

				continue;
			}

			foreach ( $columns as $k => $column ) {
				// Matches on the form-qualified sortName, not the bare name: a joined column's
				// saved sort_field is form-qualified and can share a bare name with a column
				// from a different form (see the comment where sortName is built, above).
				if ( isset( $column['sortName'] ) && $column['sortName'] === (string) $sort_field ) {
					$dt_config['order'][] = array( $k, $direction );

					break;
				}
			}
		}

		if ( $has_random_sort ) {
			// With `order` absent DataTables defaults to [[0,'asc']] and would re-sort the
			// already-shuffled data by column 0; an explicit empty array is load-bearing.
			$dt_config['order'] = array();
		}

		// A `sort` URL parameter outranks the View's sort settings in \GV\View::get_entries(),
		// so the table must open on that order too — otherwise the header advertises one order
		// while the rows arrive in another.
		$url_order = $this->get_url_sort_order( $view, $columns );

		if ( $url_order && ! $has_random_sort ) {
			$dt_config['order'] = $url_order;
		}

		/**
		 * Modify the settings used to render DataTables.
		 *
		 * @link https://datatables.net/reference/option/ Official DataTables documentation.
		 * @link https://docs.gravitykit.com/article/243-how-to-disable-the-loading-data-message How to disable the "Loading data..." message.
		 * @link https://docs.gravitykit.com/article/201-how-to-disable-the-datatables-search-filter A document on how to disable the DataTables search filter.
		 * @link https://docs.gravitykit.com/article/665-datatables-sorting Modifying and clearing the way DataTables stores sorting across sessions.
		 * @link https://docs.gravitykit.com/article/249-how-to-customize-the-csv-field-separator Converting the CSV export separator to a tab character for TSV format.
		 *
		 * @since 1.0
		 * @since 3.3 Added `$this` parameter.
		 *
		 * @param array                        $dt_config The configuration for the current View.
		 * @param int                          $view_id   The ID of the View being configured.
		 * @param WP_Post                      $post      Current View or post/page where View is embedded.
		 * @param GV_Extension_DataTables_Data $this      The current instance of the class.
		 */
		$dt_config = apply_filters( 'gravityview_datatables_js_options', $dt_config, $view->ID, $post, $this );

		return self::$configuration_cache[ $cache_key ] = $dt_config;
	}

	/**
	 * Enqueue Scripts and Styles for DataTable View Type
	 *
	 * @filter gravityview_datatables_loading_text Modify the text shown while the DataTable is loading
	 *
	 * @since 2.5 Added $gravityview parameter
	 *
	 * @param Template_Context $gravityview The $gravityview object available in templates.
	 */
	public function add_scripts_and_styles( $gravityview ) {

		if ( empty( $gravityview ) || ! $gravityview instanceof Template_Context ) {
			return;
		}

		if ( ! $gravityview->template instanceof \GV\View_DataTable_Template ) {
			return;
		}

		$post = get_post();

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Conditionally enqueue Entry Notes scripts.
		$has_workflow_approval_links = false;
		$notes_scripts_registered    = false;

		if ( $gravityview instanceof Template_Context && $gravityview->view instanceof View ) {
			// Two independent decisions live in this one pass over the columns: register Notes
			// scripts once, and detect Workflow Approval Links wherever it sits. Neither may
			// short-circuit the other, or a column ordered after the wrong one is never seen.
			foreach ( $gravityview->view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $field ) {
				if ( 'workflow_approval_links' === $field->ID ) {
					$has_workflow_approval_links = true;
				}

				if ( 'notes' === $field->type && ! $notes_scripts_registered ) {
					( new GravityView_Field_Notes )->register_scripts();
					( new GravityView_Field_Notes )->enqueue_scripts();

					$notes_scripts_registered = true;
				}
			}
		}

		/**
		 * Modify the DataTables core script used by the plugin.
		 *
		 * If you have another DataTables script on the page, you can use this filter to enqueue that script instead.
		 *
		 * @param string $path Full URL to the jQuery DataTables file
		 */
		wp_enqueue_script(
			'gv-datatables',
			apply_filters(
				'gravityview_datatables_script_src',
				plugins_url( 'assets/js/third-party/datatables/jquery.dataTables' . $script_debug . '.js', GV_DT_FILE )
			),
			array( 'jquery', 'wp-hooks' ),
			GV_Extension_DataTables::version,
			true
		);

		/**
		 * Use your own DataTables stylesheet by using the `gravityview_datatables_style_src` filter
		 */
		wp_enqueue_style( 'gravityview_style_datatables_table' );

		// Enqueue Gravity Flow styles if Workflow Approval Links field is present.
		if ( $has_workflow_approval_links ) {
			$this->enqueue_gravityflow_styles();
		}

		/**
		 * Register the featured entries script so that if active, the Featured Entries extension can use it.
		 */
		wp_register_style( 'gv-datatables-featured-entries', plugins_url( 'assets/css/featured-entries.css', GV_DT_FILE ), array( 'gravityview_style_datatables_table' ), GV_Extension_DataTables::version, 'all' );

		// include DataTables custom script
		wp_enqueue_script( 'gv-datatables-cfg', plugins_url( 'assets/js/datatables-views' . $script_debug . '.js', GV_DT_FILE ), array( 'gv-datatables' ), GV_Extension_DataTables::version, true );

		// Extensions detect the View via View_Collection::from_post(), which fails when do_shortcode()
		// embeds it in a page whose content has no [gravityview] shortcode; passing the rendered View's
		// own post resolves it directly, per View (the hook fires once per render).
		$scripts_post = $gravityview->view instanceof View ? get_post( $gravityview->view->ID ) : $post;

		/**
		 * Extend datatables by including other scripts and styles.
		 *
		 * @since 1.0
		 * @deprecated Will no longer give the views on the page.
		 *
		 * @param array   $empty_array_1 Empty array (deprecated).
		 * @param array   $empty_array_2 Empty array (deprecated).
		 * @param WP_Post $post          Current View or post/page where View is embedded.
		 */
		do_action( 'gravityview_datatables_scripts_styles', array(), array(), $scripts_post );
	}

	/**
	 * @deprecated 2.3
	 * @internal
	 */
	public function output_dt_config() {

		_deprecated_function( 'GV_Extension_DataTables_Data::output_dt_config', '2.3', 'This method was not intended to be called.' );
	}

	/**
	 * Extend DT view by outputting additional configuration, enqueuing scripts, etc.
	 *
	 * @since 2.3 Renamed from output_dt_config() to extend_view()
	 *
	 * @param Template_Context $gravityview The template $gravityview object.
	 *
	 * @return void
	 * @internal
	 */
	public function extend_view( $gravityview ) {
		if ( 'datatables_table' != $gravityview->view->settings->get( 'template' ) ) {
			return;
		}

		// enqueue scripts for registered/visible fields
		$fields = $gravityview->view->fields->by_position( 'directory_table-columns' );

		foreach ( $fields->by_visible()->all() as $field ) {

			wp_enqueue_script( 'gv-inline-edit-' . $field->type );

			if ( 'notes' === $field->type ) {
				do_action( 'gravityview/field/notes/scripts', $gravityview );
			}
		}

		global $post;

		if ( ! $post ) {
			return;
		}

		$script_config_json = wp_json_encode( $this->get_datatables_script_configuration( $post, $gravityview->view ) );

		?>
		<script type="text/javascript">
			(function() {
				if ( ! window.gvDTglobals ) {
					window.gvDTglobals = [];
				}

				const config = <?php echo $script_config_json; ?>;
				const configStr = JSON.stringify( config );

				// Only push if not already in the array.
				if ( ! window.gvDTglobals.some( existing => JSON.stringify( existing ) === configStr ) ) {
					window.gvDTglobals.push( config );
				}
			})();
		</script>
		<?php
	}

	/**
	 * Modify Inline Edit settings
	 *
	 * @since 2.3
	 *
	 * @param array $item_id Array with `form_id` or `view_id` set
	 *
	 * @param array $settings
	 *
	 * @return array Array with Inline Edit settings
	 */
	function maybe_modify_inline_edit_settings( $settings, $item_id = array() ) {

		if ( ! class_exists( '\GV\View' ) ) {
			return $settings;
		}

		$view_id = \GV\Utils::get( $item_id, 'view_id', false );

		if ( empty( $view_id ) ) {
			return $settings;
		}

		if ( ! $view = View::by_id( $view_id ) ) {
			return $settings;
		}

		if ( 'datatables_table' !== $view->settings->get( 'template' ) ) {
			return $settings;
		}

		$settings['disableInitOnLoad'] = false;

		return $settings;
	}

	/**
	 * Enqueues Gravity Flow styles for Workflow Approval Links field.
	 *
	 * @since 3.7.0
	 *
	 * @return void
	 */
	private function enqueue_gravityflow_styles() {
		$custom_css = '
			.gv-datatables-container .flow_approval_links,
			table.dataTable .flow_approval_links {
				padding-right: 1em;
			}
			.gv-datatables-container .fa,
			table.dataTable .fa {
				display: inline-block;
				font: normal normal normal 14px/1 GFFontAwesome;
				font-size: inherit;
				text-rendering: auto;
				-webkit-font-smoothing: antialiased;
			}
			.gv-datatables-container .fa-check:before,
			table.dataTable .fa-check:before {
				content: "\f00c";
				color: green;
			}
			.gv-datatables-container .fa-times:before,
			table.dataTable .fa-times:before {
				content: "\f00d";
				color: red;
			}
		';

		// Register our own style handle to ensure CSS is always output.
		wp_register_style(
			'gv-datatables-gravityflow-approval-links',
			false, // No external file, inline only.
			[], // No dependencies to avoid issues.
			GV_Extension_DataTables::version
		);

		wp_add_inline_style( 'gv-datatables-gravityflow-approval-links', $custom_css );
		wp_enqueue_style( 'gv-datatables-gravityflow-approval-links' );
	}

	/**
	 * Whether the installed GravityView supports Search Request arguments.
	 *
	 * @since 3.9.0
	 * @deprecated 3.12.0 GravityView 2.57 is this plugin's declared minimum
	 *             ({@see GV_Extension_DataTables::$_min_gravityview_version}), so every
	 *             supported install satisfies this check and it always returns true. The
	 *             pre-2.57 paths it gated (the legacy Created By column-search regex and the
	 *             legacy global-search filter) are gone. Kept because the method is `public`
	 *             and dates to 3.9.0.
	 *
	 * @return bool Always true on a supported (>= 2.57) install.
	 */
	public static function has_search_request_arguments(): bool {
		return true;
	}

	/**
	 * Hooks into the search filters to inject the DataTables global search value.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	private function maybe_set_global_search() {
		if ( empty( $_POST['search']['value'] ) ) {
			return;
		}

		add_filter( 'gk/gravityview/search/request/search-arguments', [ $this, 'set_global_search_argument' ], 5, 2 );
		add_filter( 'gk/gravityview/search/request/filters', [ $this, 'rewrite_global_search' ], 5 );
		add_filter( 'gk/gravityview/search/searchable-fields/allowed', [ $this, 'allow_search_all' ], 5, 3 );
	}
}

GV_Extension_DataTables_Data::get_instance();
