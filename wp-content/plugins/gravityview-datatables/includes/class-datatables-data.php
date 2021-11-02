<?php
/**
 * GravityView Extension -- DataTables -- Server side data
 *
 * @since     1.0.4
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @package   GravityView
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class GV_Extension_DataTables_Data {

	/**
	 * @var bool True: the file is currently being accessed directly by an AJAX request or otherwise. False: Normal WP load.
	 */
	static $is_direct_access = false;

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
		add_action( 'wp_ajax_gv_datatables_data', array( $this, 'get_datatables_data' ) );
		add_action( 'wp_ajax_nopriv_gv_datatables_data', array( $this, 'get_datatables_data' ) );
	}

	/**
	 * If this file is being accessed directly, then set up WP so we can handle the AJAX request
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 *
	 */
	function maybe_bootstrap_wp() {

		gravityview()->log->notice( 'Accessing the DataTables data directly is no longer possible. Use the WordPress AJAX API.' );
	}

	/**
	 * Create required globals for minimal bootstrap
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 *
	 */
	function bootstrap_setup_globals() {

		gravityview()->log->notice( 'Accessing the DataTables data directly is no longer possible. Use the WordPress AJAX API.' );
	}

	/**
	 * Include Gravity Forms, GravityView, and GravityView Extensions
	 *
	 * @since      1.3
	 * @deprecated Remove direct access altogether.
	 *
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
	 *
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
	 * Verify AJAX request nonce
	 *
	 * @return boolean Whether the nonce is okay or not.
	 */
	function check_ajax_nonce() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_datatables_data' ) ) {
			gravityview()->log->debug( '[DataTables] AJAX request - NONCE check failed' );

			return $this->exit_or_return( false );
		}

		return true;
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

		// Get rid of previously set headers
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
			@header( "Last-Modified: " . gmdate( "D, d M Y H:i:s", strtotime( '-2 months' ) ) . " GMT" );

		} else {

			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( "DONOTCACHEPAGE", "true" );
			}

			@nocache_headers();
		}

		@header( 'HTTP/1.1 200 OK', true, 200 );
		@header( 'X-Robots-Tag:noindex;' );
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

		// Prevent error output
		ob_start();

		// Send correct headers
		$this->do_ajax_headers( 'application/javascript' );

		if ( ! $result = $this->check_ajax_nonce() ) {
			ob_end_clean();

			return $this->exit_or_return( $result );
		}

		if ( empty( $_POST['view_id'] ) ) {
			gravityview()->log->debug( '[DataTables] AJAX request - View ID check failed' );
			ob_end_clean();

			return $this->exit_or_return( false );
		}

		/**
		 * @filter `gravityview/datatables/json/header/content_length` Enable or disable the Content-Length header on the AJAX JSON response
		 *
		 * @param boolean $has_content_length true by default
		 */
		$has_content_length = apply_filters( 'gravityview/datatables/json/header/content_length', true );

		// Prevent emails from being encrypted
		add_filter( 'gravityview_email_prevent_encrypt', '__return_true' );

		gravityview()->log->debug( '[DataTables] AJAX Request ($_POST)', array( 'data' => $_POST ) );

		// Pass $_GET variables to the View functions, since they're relied on heavily
		// for searching and filtering, for example the A-Z widget
		$_GET = isset( $_POST['getData'] ) ? json_decode( stripslashes( $_POST['getData'] ), true ) : array();

		$view_id = intval( $_POST['view_id'] );

		global $post;

		$post = get_post( \GV\Utils::_POST( 'post_id', null ) );

		setup_postdata( $post );

		$view_collection = \GV\View_Collection::from_post( $post );

		$atts = array();
		if ( $view = $view_collection->get( $view_id ) ) {
			$atts = $view->settings->as_atts();
			gravityview()->log->debug( 'View #{view_id} found in $post View Collection', array(
				'view_id' => $view_id,
				'data'    => $atts,
			) );
		} elseif ( ! $view = \GV\View::by_id( $view_id ) ) {
			gravityview()->log->error( 'View #{view_id} not found', array( 'view_id' => $view_id ) );

			return $this->exit_or_return( false );
		}

		// check for order/sorting
		$atts['sort_field']     = array();
		$atts['sort_direction'] = array();
		foreach ( (array) \GV\Utils::_POST( 'order', array() ) as $i => $order ) {

			$order_index = \GV\Utils::get( $order, 'column' );

			if ( null !== $order_index ) {
				if ( ! empty( $_POST['columns'][ $order_index ]['name'] ) ) {
					// remove prefix 'gv_'
					$atts['sort_field'][]     = substr( $_POST['columns'][ $order_index ]['name'], 3 );
					$atts['sort_direction'][] = strtoupper( \GV\Utils::get( $order, 'dir', 'ASC' ) );
				}
			}
		}

		// check for search
		if ( ! empty( $_POST['search']['value'] ) ) {
			// inject DT search
			add_filter( 'gravityview_fe_search_criteria', array( $this, 'add_global_search' ), 5, 1 );
		}

		// Paging/offset
		$atts['page_size'] = isset( $_POST['length'] ) ? intval( $_POST['length'] ) : '';

		$offset = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0;

		// check if someone requested the full filtered data (eg. TableTools print button)
		if ( $atts['page_size'] == '-1' ) {
			$mode              = 'all';
			$atts['page_size'] = PHP_INT_MAX;
		} else {
			// regular mode - get view entries
			$mode = 'page';
		}

		$view->settings->update( $atts );

		// Force shortcode parametrization
		if ( $shortcode_atts = \GV\Utils::_POST( 'shortcode_atts' ) ) {
			foreach ( $shortcode_atts as $att => $value ) {
				if ( in_array( $att, array( 'search_key', 'search_value' ) ) ) {
					$view->settings->update( array( $att => $value ) );
				}
			}
		}

		$atts = $view->settings->as_atts();

		foreach ( $atts as $key => $att ) {
			$atts[ $key ] = \GravityView_Merge_Tags::replace_variables( $att, ( $view->form ? $view->form->form : null ), array(), false, false, false );
		}

		gravityview()->log->debug( 'DataTables final $atts', $atts );

		if ( class_exists( 'GravityView_Cache' ) ) {

			$form_id = $view->form ? $view->form->ID : null;

			// We need to fetch the search criteria and pass it to the Cache so that the search is used when generating the cache transient key.
			$search_criteria = GravityView_frontend::get_search_criteria( $atts, $form_id );

			// make sure to allow late filter ( used on Advanced Filter extension )
			$criteria = apply_filters( 'gravityview_search_criteria', array( 'search_criteria' => $search_criteria ), $form_id, $_POST['view_id'] );

			$atts['search_criteria'] = $criteria['search_criteria'];

			// Cache key should also depend on the View assigned fields
			$atts['directory_table-columns'] = $view->fields->by_position( 'directory_table-columns' )->as_configuration();

			// cache depends on user session
			$atts['user_session'] = $this->get_user_session();

			// cache depends on offset
			$atts['offset'] = $offset;

			// cache depends on offset
			// TODO: Verify the $_GET variables are related to valid field searches
			$atts['getData'] = $_GET;

			$Cache = new GravityView_Cache( $form_id, $atts );

			if ( $output = $Cache->get() ) {
				gravityview()->log->debug( '[DataTables] Cached output found; using cache with key ' . $Cache->get_key() );

				// update DRAW (mr DataTables is very sensitive!)
				$temp         = json_decode( $output, true );
				$temp['draw'] = intval( ( isset( $_POST['draw'] ) && is_numeric( $_POST['draw'] ) ) ? $_POST['draw'] : - 1 );
				$output       = function_exists( 'wp_json_encode' ) ? wp_json_encode( $temp ) : json_encode( $temp );

				if ( $has_content_length ) {
					$strlen = function_exists( 'mb_strlen' ) ? mb_strlen( $output ) : strlen( $output );
					// Fix strange characters before JSON response because of "Transfer-Encoding: chunked" header
					@header( 'Content-Length: ' . $strlen );
				}

				ob_end_clean();

				return $this->exit_or_return( $output );
			}
		}

		$page = ( ( $offset - ( $view->settings->get( 'offset', 0 ) ?: 0 ) ) / ( $view->settings->get( 'page_size', 20 ) ?: 20 ) ) + 1;

		if ( gravityview()->plugin->supports( \GV\Plugin::FEATURE_GFQUERY ) ) {
			add_action( 'gravityview/view/query', $callback = function ( $query ) use ( $page ) {

				$query->page( $page );
			} );
			$entries = $view->get_entries( gravityview()->request );
			remove_action( 'gravityview/view/query', $callback );
		} else {
			$entries = $view->get_entries( gravityview()->request );
			$entries = $entries->page( $page );
		}

		// wrap all
		$output = array(
			'draw'            => intval( ( isset( $_POST['draw'] ) && is_numeric( $_POST['draw'] ) ) ? $_POST['draw'] : - 1 ),
			'recordsTotal'    => $entries->total(),
			'recordsFiltered' => $entries->total(),
			'data'            => $this->get_output_data( $entries, $view, $post ),
		);

		/**
		 * @filter `gravityview/datatables/output` Filter the output returned from the AJAX request
		 * @since  2.3
		 *
		 * @param array                $output
		 * @param \GV\View             $view
		 * @param \GV\Entry_Collection $entries
		 */
		$output = apply_filters( 'gravityview/datatables/output', $output, $view, $entries );

		wp_reset_postdata();

		gravityview()->log->debug( '[DataTables] Ajax request answer', array( 'data' => $output ) );

		$json = function_exists( 'wp_json_encode' ) ? wp_json_encode( $output ) : json_encode( $output );

		if ( class_exists( 'GravityView_Cache' ) ) {

			gravityview()->log->debug( '[DataTables] Setting cache', array( 'data' => $json ) );

			// Cache results
			$Cache->set( $json, 'datatables_output' );

		}

		// End prevent error output
		$errors = ob_get_clean();

		if ( ! empty( $errors ) ) {
			gravityview()->log->error( __METHOD__ . ' Errors generated during DataTables response', array( 'data' => $errors ) );
		}

		if ( $has_content_length ) {
			$strlen = function_exists( 'mb_strlen' ) ? mb_strlen( $json ) : strlen( $json );
			// Fix strange characters before JSON response because of "Transfer-Encoding: chunked" header
			@header( 'Content-Length: ' . $strlen );
		}

		return $this->exit_or_return( $json );
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
	 * Add the generic search to the global get_entries query
	 *
	 * @since 1.3.3
	 *
	 * @param array $search_criteria Search Criteria
	 *
	 * @return mixed
	 */
	function add_global_search( $search_criteria ) {

		if ( empty( $_POST['search']['value'] ) ) {
			return $search_criteria;
		}

		$search_all_value = stripslashes_deep( $_POST['search']['value'] );

		/**
		 * @filter `gravityview/search-all-split-words` Search for each word separately or the whole phrase?
		 * @since  2.1.1
		 *
		 * @param bool $split_words True: split a phrase into words; False: search whole word only [Default: true]
		 */
		$split_words = apply_filters( 'gravityview/search-all-split-words', true );

		if ( $split_words ) {

			// Search for a piece
			$words = explode( ' ', $search_all_value );

			$words = array_filter( $words );

		} else {

			// Replace multiple spaces with one space
			$search_all_value = preg_replace( '/\s+/ism', ' ', $search_all_value );

			$words = array( $search_all_value );
		}

		foreach ( $words as $word ) {
			$search_criteria['field_filters'][] = array(
				'key'      => null, // The field ID to search
				'value'    => $word, // The value to search
				'operator' => 'contains', // What to search in. Options: `is` or `contains`
			);
		}

		if ( ! empty( $search_criteria['field_filters'] ) && empty( $search_criteria['field_filters']['mode'] ) ) {
			$search_criteria['field_filters']['mode'] = 'any';
		}

		return $search_criteria;
	}

	/**
	 * Get the array of entry data
	 *
	 * @since 1.3
	 *
	 * @param \GV\Entry_Collection $entries The collection of entries for the current search
	 * @param \GV\View             $view    The view
	 * @param \WP_Post
	 *
	 * @return array
	 */
	function get_output_data( $entries, $view, $post = null ) {

		if ( ! $view instanceof \GV\View ) {
			gravityview()->log->notice( '\GV_Extension_DataTables_Data::get_output_data now requires a \GV\View and \GV\Entry_Collection object parameters.' );

			return array();
		}

		// build output data
		$data = array();
		if ( $entries->total() ) {

			$fields          = $view->fields->by_position( 'directory_table-columns' )->by_visible()->all();
			$internal_source = new \GV\Internal_Source();
			$renderer        = new \GV\Field_Renderer();

			// For each entry
			foreach ( $entries->all() as $entry ) {
				$temp = array();

				\GV\Mocks\Legacy_Context::push( array( 'view' => $view, 'entry' => $entry, 'post' => $post ) );

				// Loop through each column and set the value of the column to the field value
				foreach ( $fields as $field ) {
					$form = $view->form;

					if ( is_callable( array( $entry, 'is_multi' ) ) && $entry->is_multi() ) {
						$form = \GV\GF_Form::by_id( $field->form_id );
					}

					$source = is_numeric( $field->ID ) ? $form : $internal_source;
					$temp[] = $renderer->render( $field, $view, $source, $entry, gravityview()->request );
				}

				\GV\Mocks\Legacy_Context::pop();

				/**
				 * @filter `gravityview/datatables/output/entry` Modify the entry output before the request is returned
				 * @since  2.3.1
				 *
				 * @param array    $temp  Array of values for the entry, one item per field being rendered by \GV\Field_Renderer()
				 * @param \GV\View $view  Current View being processed
				 * @param array    $entry Current Gravity Forms entry array
				 */
				$temp = apply_filters( 'gravityview/datatables/output/entry', $temp, $view, $entry );

				// Then add the item to the output dataset
				$data[] = $temp;

			}

		}

		return $data;
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

		$width = null;

		if ( ! empty( $field_setting['width'] ) ) {
			$width = absint( $field_setting['width'] );
			$width = $width > 100 ? 100 : $width . '%';
		}

		return $width;
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
	 * @param \GV\View $view  The View we're looking at.
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
	 * Override the template class to the correct one.
	 *
	 * @param string    $class The class to use as the template class.
	 * @param \GV\Entry $entry The Entry we're looking at.
	 * @param \GV\View  $view  The View we're looking at.
	 *
	 * @return string The class
	 */
	public function set_entry_template_class( $class, $entry, $view ) {

		if ( $view->settings->get( 'template' ) == 'datatables_table' ) {
			return '\GV\Entry_DataTable_Template';
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
		for ( $i = 101; ; $i ++ ) {
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
	 * @param \GV\View $view The View
	 *
	 * @return array            2D array formatted for DataTables
	 */
	function get_length_menu( $view ) {

		$page_size = 25;
		if ( ! $view instanceof \GV\View ) {
			if ( $view = \GV\View::by_id( $view['id'] ) ) {
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
		$values[ - 1 ] = _x( 'All', 'Menu label to show all results in DataTables template.', 'gv-datatables' );

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
	 * @param \GV\View $view The View
	 *
	 * @return array Array of strings, as used by the DataTables extension's `language` setting
	 */
	private function get_language( $view = null ) {

		$translations = $this->get_translations();

		$locale = get_locale();

		/**
		 * Change the locale used to fetch translations
		 *
		 * @since 1.2.3
		 */
		$locale = apply_filters( 'gravityview/datatables/config/locale', $locale, $translations );

		// If a translation exists
		if ( isset( $translations[ $locale ] ) ) {

			ob_start();

			// Get the JSON file
			include GV_DT_DIR . 'assets/js/translations/' . $translations[ $locale ] . '.json';

			$json_string = ob_get_clean();

			// If it exists
			if ( ! empty( $json_string ) ) {

				// Parse it into an array
				$json_array = json_decode( $json_string, true );

				// If that worked, use the array as the base
				if ( ! empty( $json_array ) ) {
					$language = $json_array;
				}

			}

		} else {

			// Otherwise, load default English text with filters.
			$language = array(
				/**
				 * @filter `gravityview_datatables_loading_text` Modify the text shown when DataTables is loaded
				 *
				 * @since  2.5 Added $view parameter
				 *
				 * @param string   $loading_text Default: Loading data…
				 * @param \GV\View $view         The View
				 */
				'processing' => esc_html( apply_filters( 'gravityview_datatables_loading_text', __( 'Loading data&hellip;', 'gv-datatables' ), $view ) ),
				'emptyTable' => esc_html( $view->settings->get( 'no_results_text', __( 'No entries match your request.', 'gv-datatables' ) ) ),
			);

		}

		/**
		 * @filter `gravityview/datatables/config/language` Override language settings
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
	 * @return array Key is the WordPress locale string; Value is the name of the file in assets/js/translations/ without .json
	 */
	private function get_translations() {

		$translations = array(
			'af'    => 'Afrikaans',
			'sq'    => 'Albanian',
			'ar'    => 'Arabic',
			'hy'    => 'Armenian',
			'az'    => 'Azerbaijan',
			'bn_BD' => 'Bangla',
			'eu'    => 'Basque',
			'bel'   => 'Belarusian',
			'bg_BG' => 'Bulgarian',
			'ca'    => 'Catalan',
			'zh_CN' => 'Chinese',
			'hr'    => 'Croatian',
			'cs_CZ' => 'Czech',
			'da_DK' => 'Danish',
			'nl_NL' => 'Dutch',
			#'en_US' => 'English',
			'et'    => 'Estonian',
			'fi'    => 'Finnish',
			'fr_FR' => 'French',
			'gl_ES' => 'Galician',
			'ka_GE' => 'Georgian',
			'de_DE' => 'German',
			'el'    => 'Greek',
			'gu'    => 'Gujarati',
			'he_IL' => 'Hebrew',
			'hi_IN' => 'Hindi',
			'hu_HU' => 'Hungarian',
			'is_IS' => 'Icelandic',
			'id_ID' => 'Indonesian',
			'ga'    => 'Irish',
			'it_IT' => 'Italian',
			'ja'    => 'Japanese',
			'ko_KR' => 'Korean',
			'ky_KY' => 'Kyrgyz',
			'lv'    => 'Latvian',
			'lt_LT' => 'Lithuanian',
			'mk_MK' => 'Macedonian',
			'ms_MY' => 'Malay',
			'mm'    => 'Mongolian',
			'nb_NO' => 'Norwegian',
			'fa_IR' => 'Persian',
			'pl_PL' => 'Polish',
			'pt_BR' => 'Portuguese-Brasil',
			'pt_PT' => 'Portuguese',
			'ro_RO' => 'Romanian',
			'ru_RU' => 'Russian',
			'sr_RS' => 'Serbian',
			'si_LK' => 'Sinhala',
			'sk_SK' => 'Slovak',
			'sl_SI' => 'Slovenian',
			'es_ES' => 'Spanish',
			'sw'    => 'Swahili',
			'sv_SE' => 'Swedish',
			'ta_IN' => 'Tamil',
			'th'    => 'Thai',
			'tr_TR' => 'Turkish',
			'uk'    => 'Ukranian',
			'ur'    => 'Urdu',
			'uz_UZ' => 'Uzbek',
			'vi'    => 'Vietnamese',
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
	 * Generate the script configuration array
	 *
	 * @since 1.3.3
	 *
	 * @param WP_Post  $post Current View or post/page where View is embedded
	 * @param \GV\View $view The View
	 *
	 * @return array Array of settings formatted as DataTables options array. {@see https://datatables.net/reference/option/}
	 */
	public function get_datatables_script_configuration( $post, $view ) {

		$ajax_settings = array(
			'action'            => 'gv_datatables_data',
			'view_id'           => $view->ID,
			'post_id'           => $post->ID,
			'nonce'             => wp_create_nonce( 'gravityview_datatables_data' ),
			'getData'           => json_encode( (array) $_GET ), // Pass URL args to $_POST request
			'hideUntilSearched' => $view->settings->get( 'hide_until_searched' ),
			'setUrlOnSearch'    => apply_filters( 'gravityview/search/method', 'get' ) === 'get',
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
			'stateSave'     => gravityview()->request->is_search() ? false : ! isset( $_GET['cache'] ),
			// On refresh (and on single entry view, then clicking "go back"), save the page you were on.
			'stateDuration' => - 1,
			// Only save the state for the session. Use to time in seconds (like the DAY_IN_SECONDS WordPress constant) if you want to modify.
			'lengthMenu'    => $this->get_length_menu( $view ),
			// Dropdown pagination length menu
			'language'      => $this->get_language( $view ),
			'ajax'          => array(
				'url'  => $this->get_ajax_url(),
				'type' => 'POST',
				'data' => $ajax_settings,
			),
		);

		// page size, if defined
		$dt_config['pageLength'] = intval( $view->settings->get( 'page_size', 10 ) );

		/**
		 * Set the columns to be displayed
		 *
		 * @link https://datatables.net/reference/option/columns
		 */
		$columns = array();
		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $field ) {

			if ( 'custom' == $field->type ) {
				$field_id = 'custom_' . $field->UID;
			} else {
				$field_id = $field->ID;
			}

			$field_column = array(
				'name'  => 'gv_' . $field_id,
				'width' => $this->get_column_width( $field->as_configuration() ),
				'className' => gravityview_sanitize_html_class( \GV\Utils::get( $field->as_configuration(), 'custom_class', '' ) ),
			);

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
		$dt_config['columns'] = $columns;

		$sort_field_setting = (array) $view->settings->get( 'sort_field', array() );

		// set default order
		foreach ( $sort_field_setting as $l => $sort_field ) {
			foreach ( $columns as $k => $column ) {
				if ( $column['name'] === 'gv_' . $sort_field ) {
					$dir = (array) $view->settings->get( 'sort_direction', 'asc' );

					$dt_config['order'][] = array(
						$k,
						strtolower( \GV\Utils::get( $dir, $l, 'asc' ) ),
					);
				}
			}
		}

		/**
		 * @filter `gravityview_datatables_js_options` Modify the settings used to render DataTables
		 * @see    https://datatables.net/reference/option/
		 *
		 * @param array   $dt_config The configuration for the current View
		 * @param int     $view_id   The ID of the View being configured
		 * @param WP_Post $post      Current View or post/page where View is embedded
		 */
		$dt_config = apply_filters( 'gravityview_datatables_js_options', $dt_config, $view->ID, $post );

		return $dt_config;
	}

	/**
	 * Enqueue Scripts and Styles for DataTable View Type
	 *
	 * @filter gravityview_datatables_loading_text Modify the text shown while the DataTable is loading
     *
     * @since 2.5 Added $gravityview parameter
     *
     * @param \GV\Template_Context $gravityview The $gravityview object available in templates.
	 */
	public function add_scripts_and_styles( $gravityview ) {

        if ( empty( $gravityview ) || ! $gravityview instanceof \GV\Template_Context ) {
            return;
        }

        if( ! $gravityview->template instanceof \GV\View_DataTable_Template ) {
            return;
        }

		$post = get_post();

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$path = plugins_url( 'assets/datatables/media/js/jquery.dataTables' . $script_debug . '.js', GV_DT_FILE );

		/**
		 * @filter `gravityview_datatables_script_src` Modify the DataTables core script used
		 *
		 * @param string $path Full URL to the jQuery DataTables file
		 */
		$path = apply_filters( 'gravityview_datatables_script_src', $path );

		wp_enqueue_script( 'gv-datatables', $path, array( 'jquery' ), GV_Extension_DataTables::version, true );

		/**
		 * Use your own DataTables stylesheet by using the `gravityview_datatables_style_src` filter
		 */
		wp_enqueue_style( 'gravityview_style_datatables_table' );

		/**
		 * Register the featured entries script so that if active, the Featured Entries extension can use it.
		 */
		wp_register_style( 'gv-datatables-featured-entries', plugins_url( 'assets/css/featured-entries.css', GV_DT_FILE ), array( 'gravityview_style_datatables_table' ), GV_Extension_DataTables::version, 'all' );

		// include DataTables custom script
		wp_enqueue_script( 'gv-datatables-cfg', plugins_url( 'assets/js/datatables-views' . $script_debug . '.js', GV_DT_FILE ), array( 'gv-datatables' ), GV_Extension_DataTables::version, true );

		/**
		 * Extend datatables by including other scripts and styles.
		 *
		 * @deprecated Will no longer give the views on the page.
		 */
		do_action( 'gravityview_datatables_scripts_styles', array(), array(), $post );

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
	 * @param \GV\Template_Context $gravityview The template $gravityview object.
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

		$script_config_json = json_encode( $this->get_datatables_script_configuration( $post, $gravityview->view ) );

		$hash_of_config = sha1( $script_config_json . json_encode( $gravityview->view->settings->all() ) );

		if ( did_action( 'datatables_print_script_' . $hash_of_config ) ) {
			return;
		}
		?>
        <script type="text/javascript">
					if ( ! window.gvDTglobals ) {
						window.gvDTglobals = [];
					}

					window.gvDTglobals.push(<?php echo $script_config_json; ?>);
        </script>
		<?php

		do_action( 'datatables_print_script_' . $hash_of_config );
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

		if ( ! $view = \GV\View::by_id( $view_id ) ) {
			return $settings;
		}

		if ( 'datatables_table' !== $view->settings->get( 'template' ) ) {
			return $settings;
		}

		$settings['disableInitOnLoad'] = false;

		return $settings;
	}

}

new GV_Extension_DataTables_Data;
