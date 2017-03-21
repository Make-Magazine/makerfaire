<?php
/*
Plugin Name: GravityView - Advanced Filter Extension
Plugin URI: https://gravityview.co/extensions/advanced-filter/?utm_source=advanced-filter&utm_content=plugin_uri&utm_medium=meta&utm_campaign=internal
Description: Filter which entries are shown in a View based on their values.
Version: 1.0.18
Author: Katz Web Services, Inc.
Author URI: https://gravityview.co/?utm_source=advanced-filter&utm_medium=meta&utm_content=author_uri&utm_campaign=internal
Text Domain: gravityview-advanced-filter
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

		protected $_version = '1.0.18';

		protected $_min_gravityview_version = '1.15';

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

			add_filter( 'gform_filters_get_users', array( $this, 'created_by_get_users_args' ) );
		}

		/**
		 * Increase the number of users displayed in the Advanced Filter Created By dropdown.
		 *
		 * @since 1.0.12
		 *
		 * @see get_users()
		 * @see GFCommon::get_entry_info_filter_columns()
		 *
		 * @param array $args Arguments used in get_users() query
		 *
		 * @return array Modified args - bump # of users up to 1000 and limit the fields fetched by query
		 */
		function created_by_get_users_args( $args ) {

			if( ! function_exists( 'gravityview_is_admin_page' ) || ! gravityview_is_admin_page( '', 'single' ) ) {
				return $args;
			}

			$args['number'] = 1000;
			$args['fields'] = array( 'ID', 'user_login' ); // The only fields needed by GF

			return $args;
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
		 * @param  array $form_ids       Form IDs for the search
		 * @param  int $passed_view_id (optional)
		 * @return [type]                 [description]
		 */
		function filter_search_criteria( $criteria, $form_ids = null, $passed_view_id = NULL ) {

			$view_id = !empty( $passed_view_id ) ? $passed_view_id : GravityView_View::getInstance()->getViewId();

			if( empty( $view_id ) )  {

				do_action('gravityview_log_error', 'GravityView_Advanced_Filtering[filter_search_criteria] Empty View ID.' );

				$criteria['search_criteria']['field_filters'][] = self::get_lock_filter();
				$criteria['search_criteria']['field_filters']['mode'] = 'all';

				return $criteria;
			}

			$view_filters = self::get_view_filter_vars( $view_id );

			if( !empty( $view_filters ) && is_array( $view_filters ) ) {

				do_action('gravityview_log_debug', __METHOD__ . ': Validating search criteria', $view_filters );

				//sanitize filters - no empty search values
				foreach( $view_filters as $k => $filter ) {
					// Don't use `empty()` because `0` is a valid value
					if( $k !== 'mode' && ! isset( $filter['value'] ) ) {
						unset( $view_filters[ $k ] );
					}
				}

				/**
				 * add advanced filters if defined
				 * The count() checks against > 1 because "mode" will always be set.
				 */
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
				} else {
					do_action('gravityview_log_debug', __METHOD__ . ': Skipping; no filters were defined.' );
				}

			} else {

				do_action('gravityview_log_debug', __METHOD__ . ': No additional search criteria.' );

			}

			return $criteria;
		}

		/**
		 * Alias of gravityview_is_valid_datetime()
		 *
		 * Check whether a string is a expected date format
		 *
		 * @see gravityview_is_valid_datetime
		 * @since 1.0.12
		 *
		 * @param string $datetime The date to check
		 * @param string $expected_format Check whether the date is formatted as expected. Default: Y-m-d
		 *
		 * @return bool True: it's a valid datetime, formatted as expected. False: it's not a date formatted as expected.
		 */
		static function is_valid_datetime( $datetime, $expected_format = 'Y-m-d' ) {

			/**
			 * @var bool|DateTime False if not a valid date, (like a relative date). DateTime if a date was created.
			 */
			$formatted_date = DateTime::createFromFormat( 'Y-m-d', $datetime );

			/**
			 * @see http://stackoverflow.com/a/19271434/480856
			 */
			return ( $formatted_date && $formatted_date->format( $expected_format ) === $datetime );
		}

		static function get_date_filter_value( $filter, $date_format = null ) {

			// Not a relative date; use the perceived time (local)
			if( self::is_valid_datetime( $filter['value'] ) ) {
				$local_timestamp = GFCommon::get_local_timestamp();
				$date = strtotime( $filter['value'], $local_timestamp );
				$date_format = isset( $date_format ) ? $date_format : 'Y-m-d';
			}
			// Relative date; use same format as stored in (GMT)
			else {
				// Relative date compares to
				$date = strtotime( $filter['value'] );
				$date_format = isset( $date_format ) ? $date_format : 'Y-m-d H:i:s';
			}

			$filter['value'] = gmdate( $date_format, $date );

			if( ! $date ) {
				do_action('gravityview_log_error', __METHOD__.' - Date formatting passed to Advanced Filter is invalid', $filter['value'] );
			}

			return $filter;
		}

		/**
		 * For some specific field types prepare the filter value before adding it to search criteria
		 * @param  array  $filter
		 * @return array
		 */
		static function parse_advanced_filters( $filter = array(), $view_id = NULL ) {

			// Don't use `empty()` because `0` is a valid value for the key
			if( ! isset( $filter['key'] ) || '' === $filter['key'] || !function_exists('gravityview_get_field_type') || !class_exists('GFCommon') || !class_exists('GravityView_API') ) {
				return $filter;
			}

			if( !empty( $view_id ) ) {
				$form_id = gravityview_get_form_id( $view_id );
				$form = gravityview_get_form( $form_id );
			} else {
				$form = GravityView_View::getInstance()->getForm();
			}

			// replace merge tags
			$filter['value'] = GravityView_API::replace_variables( $filter['value'], $form, array() );

			// If it's a numeric value, it's a field
			if( is_numeric( $filter['key'] ) ) {

				// The "any form field" key is 0
				if ( empty( $filter['key'] ) ) {
					unset( $filter['key'] );
					return $filter;
				}

				$field = GVCommon::get_field( $form, $filter['key'] );
				$field_type = $field->type;
			}
			// Otherwise, it's a property or meta search
			else {
				$field_type = $filter['key'];
			}

			switch( $field_type ) {

				/** @since 1.0.12 */
				case 'date_created':
					$filter = self::get_date_filter_value( $filter );
					break;

				case 'date':
					$filter = self::get_date_filter_value( $filter, 'Y-m-d' );
					break;

				/**
				 * @since 1.0.12
				 */
				case 'post_category':
					$category_name = get_term_field( 'name', $filter['value'], 'category', 'raw' );
					if( $category_name && ! is_wp_error( $category_name ) ) {
						$filter['value'] = $category_name . ':' . $filter['value'];
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
		 *
		 * @see /assets/js/advfilter-admin-views.js
		 *
		 * @param  string $hook String like "widgets.php" passed by WordPress in the admin_enqueue_scripts filter
		 * @return void
		 */
		function admin_enqueue_scripts( $hook ) {
			global $post;

			// Don't process any scripts below here if it's not a GravityView page.
			if( ! gravityview_is_admin_page( $hook ) || empty( $post->ID ) ) { return; }

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
				'title' => __('Advanced Filter', 'gravityview-advanced-filter' ),
				'value'	=>	wpautop(
					__( 'Limit what entries are visible based on entry values. The entries are filtered before the View is displayed. When users perform a search, results will first be filtered by these settings.', 'gravityview-advanced-filter' )
					. '<h6>'. __( 'Limit to Logged-in User Entries', 'gravityview-advanced-filter').'</h6>'
					. __( 'To limit entries to those created by the current user, select "Created By", "is" &amp; "Logged-in User" from the drop-down menus.', 'gravityview-advanced-filter' ) .' '
					. __( 'If you want to limit entries to those created by the current user, but allow the administrators to view all the entries, select "Logged-in User (disabled for admins)" from the drop-down menu.', 'gravityview-advanced-filter' )
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
			
			foreach ( $init_filter_vars as $k => &$filter ) {

				// Not a filter
				if ( 'mode' === $k || ! isset( $filter['key'] ) || ! isset( $filter['value'] ) ) {
					continue;
				}

				// This is the default filter shown on Edit View screen. Removed because matching "Any form field" to "" makes no sense.
				if( '0' === $filter['key'] && '' === $filter['value'] ) {
					unset( $init_filter_vars[ $k ] );
					continue;
				}

				/**
				 * @since 1.0.12
				 */
				if( 'date_created' === $filter['key'] ) {
					$filter = self::get_date_filter_value( $filter );
				}

				// Only show listings created by the current user.
				// This will return no entries if the user is logged out.
				if ( $filter['key'] === 'created_by'
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
					$view_all_entries_caps = apply_filters( 'gravityview/adv_filter/admin_caps', array( 'manage_options', 'gravityforms_view_entries', 'gravityview_edit_others_entries' ), $post_id );

					if ( $filter['value'] === 'created_by_or_admin' && GVCommon::has_cap( $view_all_entries_caps ) ) {
						unset( $init_filter_vars[ $k ] );
					} else {
						$filter['value'] = get_current_user_id();
					}
				}
			}

			return apply_filters( 'gravityview/adv_filter/view_filters', $init_filter_vars, $post_id );
		}

		/**
		 * Clone of gravityview_get_terms_choices() in GravityView 1.15.3, until that's widely available.
		 *
		 * Get categories formatted in a way used by GravityView and Gravity Forms input choices
		 *
		 * @since 1.0.12
		 *
		 * @see gravityview_get_terms_choices()
		 * @see get_terms()
		 *
		 * @param array $args Arguments array as used by the get_terms() function. Filtered using `gravityview_get_terms_choices_args` filter. Defaults: { \n
		 *   @type string $fields Default: 'id=>name' to only fetch term ID and Name \n
		 *   @type int $number  Limit the total number of terms to fetch. Default: 1000 \n
		 * }
		 *
		 * @return array Multidimensional array with `text` (Category Name) and `value` (Category ID) keys.
		 */
		public static function get_terms_choices( $args = array() ) {

			$defaults = array(
				'type'         => 'post',
				'child_of'     => 0,
				'number'       => 1000, // Set a reasonable max limit
				'orderby'      => 'name',
				'order'        => 'ASC',
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'category',
				'fields'       => 'id=>name',
			);

			$args = wp_parse_args( $args, $defaults );

			/**
			 * @filter `gravityview_get_terms_choices_args` Modify the arguments passed to `get_terms()`
			 * @see get_terms()
			 * @since 1.15.3
			 */
			$args = apply_filters( 'gravityview_get_terms_choices_args', $args );

			$terms = get_terms( $args['taxonomy'], $args );

			$choices = array();

			if ( is_array( $terms ) ) {
				foreach ( $terms as $term_id => $term_name ) {
					$choices[] = array(
						'text'  => $term_name,
						'value' => $term_id
					);
				}
			}

			return $choices;
		}

		/**
		 * Get field filter options from Gravity Forms and modify them
		 *
		 * @see GFCommon::get_field_filter_settings()
		 *
		 * @param $post_id
		 *
		 * @return array|void
		 */
		public static function get_field_filters( $post_id ) {

			$form_id = gravityview_get_form_id( $post_id );
			$form = gravityview_get_form( $form_id );

			// Fixes issue on Views screen when deleting a view
			if( empty( $form ) ) { return; }

			$field_filters = GFCommon::get_field_filter_settings( $form );

			/*foreach( $form['fields'] as $field ) {
				$input_type = GFFormsModel::get_input_type( $field );

			}*/

			if( $approved_column = GravityView_Admin_ApproveEntries::get_approved_column( $form ) ) {
				$approved_column = intval( floor( $approved_column ) );
			}

			$option_fields_ids = $product_fields_ids = $category_field_ids = $boolean_field_ids = $post_category_choices = array();

			/**
			 * @since 1.0.12
			 */
			if( $boolean_fields = GFAPI::get_fields_by_type( $form, array( 'post_category', 'checkbox', 'radio', 'select' ) ) ) {
				$boolean_field_ids = wp_list_pluck( $boolean_fields, 'id' );
			}

			/**
			 * Get an array of field IDs that are Post Category fields
			 * @since 1.0.12
			 */
			if( $category_fields = GFAPI::get_fields_by_type( $form, array( 'post_category' ) ) ) {

				$category_field_ids = wp_list_pluck( $category_fields, 'id' );

				/**
				 * @since 1.0.12
				 */
				$post_category_choices = function_exists('gravityview_get_terms_choices') ? gravityview_get_terms_choices() : self::get_terms_choices();
			}

			// 1.0.14
			if( $option_fields = GFAPI::get_fields_by_type( $form, array( 'option' ) ) ) {
				$option_fields_ids = wp_list_pluck( $option_fields, 'id' );
			}
			// 1.0.14
			if( $product_fields = GFAPI::get_fields_by_type( $form, array( 'product' ) ) ) {
				$product_fields_ids = wp_list_pluck( $product_fields, 'id' );
			}


			// Add currently logged in user option
			foreach ( $field_filters as &$filter ) {

				// Add negative match to approval column
				if( $approved_column && $filter['key'] === $approved_column ) {
					$filter['operators'][] = 'isnot';
					continue;
				}

				/**
				 * @since 1.0.12
				 */
				if( in_array( $filter['key'], $category_field_ids, false ) ) {
					$filter['values'] = $post_category_choices;
				}

				if( in_array( $filter['key'], $boolean_field_ids, false ) ) {
					$filter['operators'][] = 'isnot';
				}

				/**
				 * GF stores the option values in DB as "label|price" (without currency symbol)
				 * This is a temporary fix until the filter is proper built by GF
				 * @since 1.0.14
				 */
				if( in_array( $filter['key'], $option_fields_ids ) && !empty( $filter['values'] ) && is_array( $filter['values'] ) ) {
					require_once( GFCommon::get_base_path() . '/currency.php' );
					foreach( $filter['values'] as &$value ) {
						$value['value'] = $value['text'] . '|'. GFCommon::to_number( $value['price'] );
					}
				}

				/**
				 * When saving the filters, GF is changing the operator to 'contains'
				 * @see: GFCommon::get_field_filters_from_post
				 * @since 1.0.14
				 */
				if( in_array( $filter['key'], $product_fields_ids ) ) {
					$filter['operators'] = array( 'contains' );
				}

				// Gravity Forms already creates a "User" option.
				// We don't care about specific user, just the logged in status.
				if( $filter['key'] === 'created_by' ) {

					// Update the default label to be more descriptive
					$filter['text'] = esc_attr__( 'Created By', 'gravityview-advanced-filter' );

					$current_user_filters = array(
						array(
							'text' => __( 'Logged-in User (disabled for Admins)', 'gravityview-advanced-filter' ),
							'value' => 'created_by_or_admin',
						),
						array(
							'text' => __( 'Logged-in User', 'gravityview-advanced-filter' ),
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
			 * @filter `gravityview/adv_filter/field_filters` allow field filters manipulation
			 * @param array $field_filters configured filters
			 * @param int $post_id
			 */
			$field_filters = apply_filters( 'gravityview/adv_filter/field_filters', $field_filters, $post_id );

			return array(
				'field_filters' => $field_filters,
				'init_filter_vars' => $init_filter_vars
			);

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
