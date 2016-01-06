<?php
/*
Plugin Name: GravityView - Advanced Filter Extension
Plugin URI: https://gravityview.co/extensions/advanced-filter/
Description: Filter which entries are shown in a View based on their values.
Version: 1.0.11
Author: Katz Web Services, Inc.
Author URI: https://gravityview.co
Text Domain: gravity-view-advanced-filter
Domain Path: /languages/
*/

add_action( 'plugins_loaded', 'gv_extension_advanced_filtering_load' );

/**
 * Wrapper function to make sure GravityView_Extension has loaded
 * @return void
 */
function gv_extension_advanced_filtering_load() {

	if( !class_exists( 'GravityView_Extension' ) ) {

		if( class_exists('GravityView_Plugin') && is_callable(array('GravityView_Plugin', 'include_extension_framework')) ) {
			GravityView_Plugin::include_extension_framework();
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
		}
	}

	class GravityView_Advanced_Filtering extends GravityView_Extension {

		protected $_title = 'Advanced Filtering';

		protected $_version = '1.0.11';

		protected $_min_gravityview_version = '1.7.1';

		/**
		 * @since 1.0.11
		 * @type int
		 */
		protected $_item_id = 30;

		protected $_path = __FILE__;

		function add_hooks() {

			add_action( 'gravityview_metabox_filter_after', array( $this, 'render_metabox' ));

			// Admin_Views::add_scripts_and_styles() runs at 999
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts'), 1100 );

			add_action( 'admin_print_footer_scripts', array( $this, 'print_javascript'), 1100 );

			// Add the magic filter
			add_filter( 'gravityview_search_criteria', array( $this, 'filter_search_criteria' ), 100, 3 );

			add_filter( 'gravityview_noconflict_scripts', array( $this, 'no_conflict_filter') );

		}

		/**
		 * Add the scripts to the no-conflict mode whitelist
		 * @param  array $scripts Array of script keys
		 * @return array          Modified array
		 */
		function no_conflict_filter( $scripts ) {

			$scripts[] = 'gform_tooltip_init';
			$scripts[] = 'gform_field_filter';
			$scripts[] = 'gform_forms';
			$scripts[] = 'gravityview_adv_filter_admin';

			return $scripts;
		}

		/**
		 * Modify search criteria
		 * @param  array $criteria       Existing search criteria array, if any
		 * @param  [type] $form_ids       Form IDs for the search
		 * @param  [type] $passed_view_id (optional)
		 * @return [type]                 [description]
		 */
		function filter_search_criteria( $criteria, $form_ids = null, $passed_view_id = NULL ) {
			global $gravityview_view;

			if( is_admin() && ( !defined('DOING_AJAX') || ( defined('DOING_AJAX') && ! DOING_AJAX ) ) ) {
				return $criteria;
			}

			$view_id = !empty( $passed_view_id ) ? $passed_view_id : GravityView_View::getInstance()->getViewId();

			if( empty( $view_id ) )  {

				do_action('gravityview_log_error', 'GravityView_Advanced_Filtering[filter_search_criteria] Empty View ID.', $gravityview_view);

				$criteria['search_criteria']['field_filters'][] = self::get_lock_filter();
				$criteria['search_criteria']['field_filters']['mode'] = 'all';

				return $criteria;
			}

			$view_filters = self::get_view_filter_vars( $view_id );

			if( !empty( $view_filters ) && is_array( $view_filters ) ) {

				do_action('gravityview_log_debug', 'GravityView_Advanced_Filtering[filter_search_criteria] about to add search criteria', $view_filters );

				//sanitize filters - no empty search values
				foreach( $view_filters as $k => $filter ) {
					// Don't use `empty()` because `0` is a valid value
					if( $k !== 'mode' && ( !isset( $filter['value'] ) || $filter['value'] === '' ) ) {
						unset( $view_filters[ $k ] );
					}
				}

				// add advanced filters if defined
				if ( count( $view_filters ) > 1 ) {

					do_action('gravityview_log_debug', 'GravityView_Advanced_Filtering[filter_search_criteria] Added search criteria', $view_filters );

					foreach( $view_filters as $k => $filter ) {
						if( $k !== 'mode' ) {
							$filter = self::parse_advanced_filters( $filter, $view_id );
							$criteria['search_criteria']['field_filters'][] = $filter;
						} else {
							$criteria['search_criteria']['field_filters']['mode'] = $filter;
						}
					}

				}

			} else {

				do_action('gravityview_log_debug', 'GravityView_Advanced_Filtering[filter_search_criteria] No additional search criteria.' );

			}

			return $criteria;
		}

		/**
		 * For some specific field types prepare the filter value before adding it to search criteria
		 * @param  array  $filter
		 * @return array
		 */
		static function parse_advanced_filters( $filter = array(), $view_id = NULL ) {

			if( empty( $filter['key'] ) || !function_exists('gravityview_get_field_type') || !class_exists('GFCommon') || !class_exists('GravityView_API') ) {
				return $filter;
			}

			if( !empty( $view_id ) ) {
				$form_id = gravityview_get_form_id( $view_id );
				$form = gravityview_get_form( $form_id );
			} else {
				global $gravityview_view;
				$form = $gravityview_view->form;
			}

			// replace merge tags
			$filter['value'] = GravityView_API::replace_variables( $filter['value'], $form, array() );

			$field_type = gravityview_get_field_type( $form, $filter['key'] );

			switch( $field_type ) {

				case 'date':
					$local_timestamp = GFCommon::get_local_timestamp();
					$date = strtotime( $filter['value'], $local_timestamp );

					if( $date ) {
						$filter['value'] = date( 'Y-m-d' , $date );
					} else {
						do_action('gravityview_log_error', __METHOD__.' - Date formatting passed to Advanced Filter is invalid', $filter['value'] );
					}
					break;
			}

			return $filter;
		}

		/**
		 * Creates a filter that should return zero results
		 * @since 1.0.7
		 * @return array
		 */
		public static function get_lock_filter() {
			return array(
				'key' => 'created_by',
				'operator' => 'is',
				'value' => 'Advanced Filter Force Zero Results Filter'
			);
		}


		/**
		 * Store the filter settings in the `_gravityview_filters` post meta
		 * @param  int $post_id Post ID
		 * @return void
		 */
		function save_post( $post_id ) {

			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
				return;
			}

			// validate post_type
			if ( ! isset( $_POST['post_type'] ) || 'gravityview' != $_POST['post_type'] ) {
				return;
			}

			$form_id = !empty( $_POST['gravityview_form_id'] ) ? $_POST['gravityview_form_id'] : '';
			$form = gravityview_get_form( $form_id );

			$filters = GFCommon::get_field_filters_from_post( $form );

			update_post_meta( $post_id, '_gravityview_filters', $filters );

		}

		/**
		 * Enqueue scripts on Views admin
		 * @see advfilter-admin-views.js
		 * @param  [type] $hook [description]
		 * @return [type]       [description]
		 */
		function admin_enqueue_scripts( $hook ) {
			global $post;

			// Don't process any scripts below here if it's not a GravityView page.
			if( !gravityview_is_admin_page($hook) || empty( $post->ID ) ) { return; }

			$form_id = gravityview_get_form_id( $post->ID );

			if( empty( $form_id) ) { return; }

			$filter_settings = self::get_field_filters( $post->ID );

			if( empty( $filter_settings['field_filters'] ) || empty( $filter_settings['init_filter_vars'] )) {
				do_action( 'gravityview_log_error', '[print_javascript] Filter settings were not properly set', $filter_settings );
				return;
			}

			$script_debug = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'gravityview_adv_filter_admin', plugins_url( 'assets/js/advfilter-admin-views'.$script_debug.'.js', __FILE__ ), array( 'jquery', 'gform_field_filter' ), $this->_version );

			wp_localize_script( 'gravityview_adv_filter_admin', 'gvAdvFilterVar', array(
				'gformFieldFilters' => $filter_settings['field_filters'],
				'gformInitFilter' => $filter_settings['init_filter_vars'],

				) );

		}


		function tooltips( $tooltips = array() ) {

			$tooltips['gv_advanced_filter'] = array(
				'title' => __('Advanced Filter', 'gravity-view-advanced-filter' ),
				'value'	=>	wpautop(
					__( 'Limit what entries are visible based on entry values. The entries are filtered before the View is displayed. When users perform a search, results will first be filtered by these settings.', 'gravity-view-advanced-filter' )
					. '<h6>'. __( 'Limit to Logged-in User Entries', 'gravity-view-advanced-filter').'</h6>'
					. __( 'To limit entries to those created by the current user, select "Created By", "is" &amp; "Logged-in User" from the drop-down menus.', 'gravity-view-advanced-filter' ) .' '
					. __( 'If you want to limit entries to those created by the current user, but allow the administrators to view all the entries, select "Logged-in User (disabled for admins)" from the drop-down menu.', 'gravity-view-advanced-filter' )
				),
			);

			return $tooltips;
		}

		/**
		 * Render the HTML container that will be replaced by the Javascript
		 * @return void
		 */
		function render_metabox( $settings = array() ) {
			include plugin_dir_path( __FILE__ ) . 'partials/metabox.php';

		}

		static function get_view_filter_vars( $post_id, $admin_formatting = false ) {

			$init_filter_vars = get_post_meta( $post_id, '_gravityview_filters', true );

			if( empty( $init_filter_vars ) ) { return false; }

			// migration purposes from the old version with JSON_ENCODE
			if( !is_array( $init_filter_vars ) && strpos( $init_filter_vars, '{') === 0 ) {
				$init_filter_vars = json_decode( $init_filter_vars, true );
			}

			// In the Admin, the Javascript requires special formatting.
			if( $admin_formatting && array_key_exists( 'mode', $init_filter_vars ) ) {
				// Re-set this below
				$mode = $init_filter_vars['mode'];
				unset( $init_filter_vars['mode'] );

				// GF stores the field filter data as a flat array.
				// We need to set the `filters` key with the fields
				$init_filter_vars = array(
					'filters' 	=> $init_filter_vars,
					'mode'		=> $mode
				);

				// The Javascript expects the `field` key, not the `key` key.
				foreach ( $init_filter_vars['filters'] as &$filter ) {
					$filter['field'] = $filter['key'];
				}
			}



			// Only show listings created by the current user.
			// This will return no entries if the user is logged out.
			foreach ( $init_filter_vars as $k => &$filter ) {

				if ( isset( $filter['key'] ) && isset( $filter['value'] )
				     && $filter['key'] === 'created_by'
				     && in_array( $filter['value'], array(
						'created_by',
						'created_by_or_admin'
					) )
				) {
					/**
					 * Customise the capabilities that define an Administrator able to view entries in frontend when filtered by Created_by
					 *
					 * @param array|string $capabilities List of admin capabilities
					 * @param int $post_id View ID where the filter is set
					 *
					 * @since 1.0.9
					 */
					$admin_caps = apply_filters( 'gravityview/adv_filter/admin_caps', array( 'manage_options', 'gravityforms_view_entries' ), $post_id );

					if ( $filter['value'] === 'created_by_or_admin' && GFCommon::current_user_can_any( $admin_caps ) ) {
						unset( $init_filter_vars[ $k ] );
					} else {
						$filter['value'] = get_current_user_id();
					}
				}
			}

			return apply_filters( 'gravityview/adv_filter/view_filters', $init_filter_vars, $post_id );
		}

		static function get_field_filters( $post_id ) {

			$form_id = gravityview_get_form_id( $post_id );
			$form = gravityview_get_form( $form_id );

			// Fixes issue on Views screen when deleting a view
			if( empty( $form ) ) { return; }

			$field_filters = GFCommon::get_field_filter_settings($form);

			if( $approved_column = GravityView_Admin_ApproveEntries::get_approved_column( $form ) ) {
				$approved_column = intval( floor( $approved_column ) );
			}

			// Add currently logged in user option
			foreach ( $field_filters as &$filter ) {

				// Add negative match to approval column
				if( $approved_column && $filter['key'] === $approved_column ) {
					$filter['operators'][] = 'isnot';
					continue;
				}

				// Gravity Forms already creates a "User" option.
				// We don't care about specific user, just the logged in status.
				if( $filter['key'] === 'created_by' ) {

					// Update the default label to be more descriptive
					$filter['text'] = esc_attr__( 'Created By', 'gravity-view-advanced-filter' );

					$current_user_filters = array(
						array(
							'text' => __( 'Logged-in User (disabled for Admins)', 'gravity-view-advanced-filter' ),
							'value' => 'created_by_or_admin',
						),
						array(
							'text' => __( 'Logged-in User', 'gravity-view-advanced-filter' ),
							'value' => 'created_by',
						),
					);

					foreach( $current_user_filters as $user_filter ) {
						// Add to the beginning on the value options
						array_unshift( $filter['values'] , $user_filter );
					}
				}

			}

			$init_field_id       = 0;
			$init_field_operator = "contains";
			$default_init_filter_vars = array(
				"mode"    => "all",
				"filters" => array(
					array(
						"field"    => $init_field_id,
						"operator" => $init_field_operator,
						"value"    => ''
					)
				)
			);

			$view_filter_vars = self::get_view_filter_vars( $post_id, true );

			$init_filter_vars = !empty( $view_filter_vars ) ? $view_filter_vars : $default_init_filter_vars;

			/**
			 * allow field filters manipulation
			 * @param array $field_filters configured filters
			 */
			$field_filters = apply_filters( 'gravityview/adv_filter/field_filters', $field_filters, $post_id );

			return array( 'field_filters' => $field_filters, 'init_filter_vars' => $init_filter_vars );

		}

		/**
		 * Output the script necessary for the drop-down to work
		 * @param  string $hook Admin page $pagenow string
		 * @return void
		 */
		function print_javascript( $hook ) {
			global $post;

			// Don't process any scripts below here if it's not a GravityView page.
			if( !gravityview_is_admin_page($hook) || empty( $post->ID ) ) { return; }

			?>
			<style type="text/css">

				#entry_filters_warning { display: none; }

				#gform-no-filters {
					padding: 1em 0;
					cursor: pointer;
					width: 100%;
				}
				#gform-no-filters img {
					float:right;
					margin: 2px .25em;
				}

				#gv-advanced-filter .gform-field-filter {
					margin: .5em 0;
				}

				/** Text input filter */
				#gv-advanced-filter input.gform-filter-value,
				#gv-advanced-filter select {
					margin: 0 .3em;
					width: auto;
					max-width: 32%;
				}
				#gv-advanced-filter .gform-field-filter .gform-add {
					margin: 0 5px 0 2px;
				}
				#gv-advanced-filter h3 {
					margin-bottom: .5em;
					padding-left: 0;
				}
				#gv-advanced-filter .description {
					margin-bottom: .5em;
				}
			</style>

			<?php
		}


	} // end class

	new GravityView_Advanced_Filtering;

}


