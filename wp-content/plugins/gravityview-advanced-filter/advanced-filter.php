<?php
/*
Plugin Name: GravityView - Advanced Filter Extension
Plugin URI: https://gravityview.co/extensions/advanced-filter/?utm_source=advanced-filter&utm_content=plugin_uri&utm_medium=meta&utm_campaign=internal
Description: Filter which entries are shown in a View based on their values.
Version: 2.0.3
Author: GravityView
Author URI: https://gravityview.co/?utm_source=advanced-filter&utm_medium=meta&utm_content=author_uri&utm_campaign=internal
Text Domain: gravityview-advanced-filter
Domain Path: /languages/
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

add_action( 'plugins_loaded', 'gv_extension_advanced_filtering_load' );

/**
 * Wrapper function to make sure GravityView_Extension has loaded
 *
 * @return void
 */
function gv_extension_advanced_filtering_load() {

	if ( ! class_exists( 'GravityView_Extension' ) ) {

		if ( class_exists( 'GravityView_Plugin' ) && is_callable( array( 'GravityView_Plugin', 'include_extension_framework' ) ) ) {
			GravityView_Plugin::include_extension_framework();
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
		}
	}

	class GravityView_Advanced_Filtering extends GravityView_Extension {

		protected $_title = 'Advanced Filtering';

		protected $_version = '2.0.3';

		protected $_min_gravityview_version = '2.0';

		/**
		 * @since 1.0.11
		 * @type int
		 */
		protected $_item_id = 30;

		protected $_path = __FILE__;

		protected $_text_domain = 'gravityview-advanced-filter';

		/**
		 * @type string AJAX action to add or update entry rating
		 */
		const AJAX_ACTION_GET_FIELD_FILTERS = 'get_field_filters_ajax';

		function add_hooks() {

			add_action( 'gravityview_metabox_filter_after', array( $this, 'render_metabox' ) );

			// Admin_Views::add_scripts_and_styles() runs at 999
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 1100 );

			add_filter( 'gravityview_noconflict_scripts', array( $this, 'no_conflict_script_filter' ) );

			add_action( 'gravityview/view/query', array( $this, 'gf_query_filter' ), 10, 3 );

			add_filter( 'gravityview_noconflict_styles', array( $this, 'no_conflict_style_filter' ) );

			add_filter( 'gform_filters_get_users', array( $this, 'created_by_get_users_args' ) );

			add_action( 'wp_ajax_' . self::AJAX_ACTION_GET_FIELD_FILTERS, array( __CLASS__, 'get_field_filters_ajax' ) );
		}

		/**
		 * Increase the number of users displayed in the Advanced Filter Created By dropdown.
		 *
		 * @since 1.0.12
		 *
		 * @see   get_users()
		 * @see   GFCommon::get_entry_info_filter_columns()
		 *
		 * @param array $args Arguments used in get_users() query
		 *
		 * @return array Modified args - bump # of users up to 1000 and limit the fields fetched by query
		 */
		function created_by_get_users_args( $args ) {

			if ( ! function_exists( 'gravityview' ) || ! gravityview()->request->is_admin( '', 'single' ) ) {
				return $args;
			}

			$args['number'] = 1000;
			$args['fields'] = array( 'ID', 'user_login' ); // The only fields needed by GF

			return $args;
		}

		/**
		 * Add the scripts to the no-conflict mode whitelist
		 *
		 * @param array $scripts Array of script keys
		 * @return array          Modified array
		 */
		function no_conflict_script_filter( $scripts ) {

			$scripts[] = 'gform_tooltip_init';
			$scripts[] = 'gform_field_filter';
			$scripts[] = 'gform_forms';
			$scripts[] = 'gravityview_adv_filter_admin';
			$scripts[] = 'gravityview_adv_filter_admin_style';

			return $scripts;
		}

		/**
		 * Add the styles to the no-conflict mode whitelist
		 *
		 * @since 2.0
		 *
		 * @param array $styles Array of style keys
		 * @return array          Modified array
		 */
		function no_conflict_style_filter( $styles ) {

			$styles[] = 'gravityview_adv_filter_admin';

			return $styles;
		}

		/**
		 * Modify search criteria
		 *
		 * @param array $criteria       Existing search criteria array, if any
		 * @param array $form_ids       Form IDs for the search
		 * @param int   $passed_view_id (optional)
		 * @return     [type]                 [description]
		 *
		 * @deprecated 2.0
		 *
		 * Use the 2.0 GF_Query filters instead
		 */
		function filter_search_criteria( $criteria, $form_ids = null, $passed_view_id = null ) {

			gravityview()->log->error( 'The filter_search_criteria method is no longer functional. Should not be used.' );

			return array( 'mode' => 'all', self::get_lock_filter() );
		}

		/**
		 * Convert old style flat filters to new nested filters.
		 *
		 * @param array|string $filters The filters to perhaps convert. Can be empty string as well.
		 *
		 * @return array|null $filters Converted v2 filters or null value when filters are not available
		 */
		public static function convert_filters_to_nested( $filters ) {

			if ( empty( $filters ) ) {
				return null;
			}

			if ( ! is_array( $filters ) ) {
				return null;
			}

			$v2_filters = array(
				'_id'        => wp_generate_password( 9, false ),
				'version'    => 2,
				'mode'       => 'and',
				'conditions' => array(),
			);

			$filters = (array) $filters;

			if ( 2 == \GV\Utils::get( $filters, 'version', 1 ) ) {
				return $filters; // Nothing to convert
			}

			$mode = \GV\Utils::get( $filters, 'mode' ) === 'any' ? 'or' : 'and';

			unset( $filters['mode'] );

			$conditions = array();
			foreach ( $filters as $filter ) {
				$filter['_id'] = wp_generate_password( 9, false );
				$conditions[]  = $filter;
			}

			if ( 'or' === $mode ) {
				// or mode
				$v2_filters['conditions'][] = array(
					'_id'        => wp_generate_password( 9, false ),
					'mode'       => 'or',
					'conditions' => $conditions,
				);
			} else {

				// and mode
				foreach ( $conditions as $condition ) {
					$v2_filters['conditions'][] = array(
						'_id'        => wp_generate_password( 9, false ),
						'mode'       => 'or',
						'conditions' => array( $condition ),
					);
				}
			}

			return $v2_filters;
		}

		/**
		 * Changes the supplied filters in place
		 *
		 * - parse relative dates
		 * - replace create_by IDs
		 * - replace merge tags
		 * - etc.
		 *
		 * @param array    $filter A pointer to the v2 filters
		 * @param \GV\View $view   The View
		 *
		 * @return void
		 */
		public static function augment_filters( &$filter, $view ) {

			if ( ! empty( $filter['mode'] ) && isset( $filter['conditions'] ) ) {
				/** We are in a logic definition */

				foreach ( $filter['conditions'] as &$condition ) {
					self::augment_filters( $condition, $view );
				}

			} else {
				/** We are in a filter */

				if ( ! isset( $filter['key'] ) ) {
					// Can't match any with empty string
					$filter = null;
				}

				if ( $filter && in_array( $filter['key'], array( 'date_created', 'date_updated', 'payment_date' ), true ) ) {
					$filter = self::get_date_filter_value( $filter, null, true );
				}

				if ( $filter && in_array( $filter['key'], array( 'created_by', 'created_by_user_role' ), true ) ) {
					$filter = self::get_user_id_value( $filter, $view );
				}

				if ( $filter && 'created_by' !== $filter['key'] ) {
					$filter = self::parse_advanced_filters( $filter, $view ? $view->ID : null );
				}
			}
		}

		/**
		 * Clean up the conditions arrays and modes.
		 *
		 * @param array $filter A pointer to the v2 filters.
		 *
		 * @return void
		 */
		public static function prune_filters( &$filter ) {

			if ( ! empty( $filter['mode'] ) && isset( $filter['conditions'] ) ) {
				/** We are in a logic definition */

				$filter['conditions'] = array_filter( $filter['conditions'], function ( $c ) {

					return ! is_null( $c );
				} );

				foreach ( $filter['conditions'] as &$condition ) {
					self::prune_filters( $condition );
				}

				$filter['conditions'] = array_filter( $filter['conditions'], function ( $c ) {

					return ! is_null( $c );
				} );

				if ( empty( $filter['conditions'] ) ) {
					$filter = null;
				}

				// @todo can further bubble up single conditions to parent clause
			}
		}

		/**
		 * Filters the \GF_Query with advanced logic.
		 *
		 * Dropin for the legacy flat filters when \GF_Query is available.
		 *
		 * @param \GF_Query   $query   The current query object reference
		 * @param \GV\View    $this    The current view object
		 * @param \GV\Request $request The request object
		 */
		public function gf_query_filter( &$query, $view, $request ) {

			$filters = get_post_meta( $view->ID, '_gravityview_filters', true );

			gravityview()->log->debug( 'Advanced filters raw:', array( 'data' => $filters ) );

			$filters = self::convert_filters_to_nested( $filters ); // Convert to v2
			self::augment_filters( $filters, $view ); // Modify logic as needed
			self::prune_filters( $filters ); // Cleanup

			/**
			 * @filter `gravityview/adv_filter/filters` The filters to be applied to the query.
			 * @param  [in,out] array $filters The filter set.
			 * @param \GV\View $view The View.
			 */
			$filters = apply_filters( 'gravityview/adv_filter/filters', $filters, $view );

			if ( ! $filters ) {
				gravityview()->log->debug( 'No advanced filters.' );

				return; // Nada, sorry
			}

			gravityview()->log->debug( 'Advanced filters:', $filters );

			self::convert_to_gf_conditions( $filters, $view );

			/**
			 * Grab the current clauses. We'll be combining them shortly.
			 */
			$query_parts = $query->_introspect();

			/**
			 * Combine the parts as a new WHERE clause.
			 */
			$query->where( GF_Query_Condition::_and( $query_parts['where'], $filters ) );
		}

		/**
		 * Convert a filter group to GF_Conditions.
		 *
		 * Overwrites the filter. Called recursively.
		 *
		 * @param &array $filter The filter.
		 * @param \GV\View $this The current view object
		 *
		 * @return void
		 * @internal
		 *
		 */
		public static function convert_to_gf_conditions( &$filter, $view ) {

			if ( ! empty( $filter['mode'] ) && isset( $filter['conditions'] ) ) {
				/** We are in a logic definition */

				$_proxy_operators_map = array(
					'isempty'    => 'is',
					'isnotempty' => 'isnot',
				);

				foreach ( $filter['conditions'] as &$condition ) {
					// Map proxy operator to GF_Query operator
					if ( ! empty( $condition['operator'] ) && ! empty( $_proxy_operators_map[ $condition['operator'] ] ) ) {
						$condition['operator'] = $_proxy_operators_map[ $condition['operator'] ];
					}

					self::convert_to_gf_conditions( $condition, $view );
				}

				$filter = call_user_func_array( array( 'GF_Query_Condition', $filter['mode'] == 'or' ? '_or' : '_and' ), $filter['conditions'] );

			} else {
				/** We are in a filter */
				if ( ! is_array( $filter ) || ! isset( $filter['key'] ) || ! isset( $filter['value'] ) ) {
					return;
				}

				$form_id = \GV\Utils::get( $filter, 'form_id', $view->form->ID );

				unset( $filter['_id'] );
				unset( $filter['form_id'] );
				$key = \GV\Utils::get( $filter, 'key' );

				$_tmp_query       = new GF_Query( $form_id, array( 'field_filters' => array( 'mode' => 'all', $filter ) ) );
				$_tmp_query_parts = $_tmp_query->_introspect();
				$_filter_value    = $filter['value'];

				$filter = $_tmp_query_parts['where'];

				if ( is_numeric( $key ) && in_array( $filter->operator, array( $filter::NLIKE, $filter::NBETWEEN, $filter::NEQ, $filter::NIN ) ) && '' !== $_filter_value ) {
					global $wpdb;
					$subquery = $wpdb->prepare( sprintf( "SELECT 1 FROM `%s` WHERE (`meta_key` LIKE %%s OR `meta_key` = %%d) AND `entry_id` = `%s`.`id`",
						GFFormsModel::get_entry_meta_table_name(), $_tmp_query->_alias( null, $view->form->ID ) ),
						sprintf( '%d.%%', $key ), $key );
					$filter   = GF_Query_Condition::_or( $filter, new GF_Query_Condition( new GF_Query_Call( 'NOT EXISTS', array( $subquery ) ) ) );
				}
			}
		}

		/**
		 * Alias of gravityview_is_valid_datetime()
		 *
		 * Check whether a string is a expected date format
		 *
		 * @since 1.0.12
		 *
		 * @see   gravityview_is_valid_datetime
		 * @param string $datetime        The date to check
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

		/**
		 * Set the correct User IDs for the filter.
		 *
		 * @param array    $filter The created by filter.
		 * @param \GV\View $view   The View.
		 *
		 * @return array $filter The modified filter.
		 */
		static function get_user_id_value( $filter, $view ) {

			switch ( $filter['key'] ) {
				case 'created_by':
					switch ( $filter['value'] ) {
						case 'created_by':
							if ( ! is_user_logged_in() ) {
								return self::get_lock_filter(); // Nothing to show for
							}

							$filter['value'] = get_current_user_id();

							break;
						case 'created_by_or_admin':
							/**
							 * Customise the capabilities that define an Administrator able to view entries in frontend when filtered by Created_by
							 *
							 * @since 1.0.9
							 * @param int          $post_id      View ID where the filter is set
							 *
							 * @param array|string $capabilities List of admin capabilities
							 */
							$view_all_entries_caps = apply_filters( 'gravityview/adv_filter/admin_caps', array( 'manage_options', 'gravityforms_view_entries', 'gravityview_edit_others_entries' ), $view->ID );

							if ( GVCommon::has_cap( $view_all_entries_caps ) ) {
								return null; // Administrative role, all good, no created_by filtering at all
							}

							if ( ! is_user_logged_in() ) {
								return self::get_lock_filter(); // Nothing to show for
							}

							$filter['key']   = 'created_by';
							$filter['value'] = get_current_user_id();

							break;
						case '':
							return self::get_lock_filter(); // Empty, wut?
						default:
							break;
					};

					return $filter;
				case 'created_by_user_role':
					$filter['key'] = 'created_by';

					if ( 'current_user' === $filter['value'] ) {
						$current_user = wp_get_current_user();
						$roles        = wp_get_current_user()->roles;
					} else {
						$roles = array( $filter['value'] );
					}

					$filter['value'] = array();

					foreach ( $roles as $role ) {
						$filter['value'] = array_merge( $filter['value'], get_users( array(
							'role'   => $role,
							'fields' => 'ID',
						) ) );
					}

					if ( empty( $filter['value'] ) ) {
						if ( 'is' === \GV\Utils::get( $filter, 'operator', 'is' ) ) {
							return self::get_lock_filter();
						} else {
							return null;
						}
					}

					if ( count( $filter['value'] ) === 1 ) {
						$filter['value'] = reset( $filter['value'] );
					} else {
						if ( 'is' === \GV\Utils::get( $filter, 'operator', 'is' ) ) {
							$filter['operator'] = 'in';
						} else {
							$filter['operator'] = 'not in';
						}
					}

					return $filter;
			};

			return self::get_lock_filter(); // No match
		}

		/**
		 * @since 1.1
		 * @param      $filter
		 * @param null $date_format
		 * @param bool $use_gmt Whether the value is stored in GMT or not (GF-generated is GMT; datepicker is not)
		 *
		 * @return mixed
		 */
		static function get_date_filter_value( $filter, $date_format = null, $use_gmt = false ) {

			// Not a relative date; use the perceived time (local)
			if ( self::is_valid_datetime( $filter['value'] ) ) {
				$local_timestamp = GFCommon::get_local_timestamp();
				$date            = strtotime( $filter['value'], $local_timestamp );
				$date_format     = isset( $date_format ) ? $date_format : 'Y-m-d';
			} // Relative date; use same format as stored in (GMT)
			else {
				// Relative date compares to
				$date        = strtotime( $filter['value'] );
				$date_format = isset( $date_format ) ? $date_format : 'Y-m-d H:i:s';
			}

			if ( $use_gmt ) {
				$filter['value'] = gmdate( $date_format, $date );
			} else {
				$filter['value'] = date( $date_format, $date );
			}

			if ( ! $date ) {
				do_action( 'gravityview_log_error', __METHOD__ . ' - Date formatting passed to Advanced Filter is invalid', $filter['value'] );
			}

			return $filter;
		}

		/**
		 * For some specific field types prepare the filter value before adding it to search criteria
		 *
		 * @param array $filter
		 * @return array
		 */
		static function parse_advanced_filters( $filter = array(), $view_id = null ) {

			// Don't use `empty()` because `0` is a valid value for the key
			if ( ! isset( $filter['key'] ) || '' === $filter['key'] || ! function_exists( 'gravityview_get_field_type' ) || ! class_exists( 'GFCommon' ) || ! class_exists( 'GravityView_API' ) ) {
				return $filter;
			}

			$form = false;

			if ( isset( $filter['form_id'] ) ) {
				$form = GFAPI::get_form( $filter['form_id'] );
			}

			if ( ! $form && ! empty( $view_id ) ) {
				if ( $view = \GV\View::by_id( $view_id ) ) {
					$form = GFAPI::get_form( $view->form->ID );
				}
			}

			if ( ! $form ) {
				$form = GravityView_View::getInstance()->getForm();
			}

			// replace merge tags
			$filter['value'] = GravityView_API::replace_variables( $filter['value'], $form, array() );

			// If it's a numeric value, it's a field
			if ( is_numeric( $filter['key'] ) ) {

				// The "any form field" key is 0
				if ( empty( $filter['key'] ) ) {
					return $filter;
				}

				if ( ! $field = GVCommon::get_field( $form, $filter['key'] ) ) {
					return $filter;
				}

				$field_type = $field->type;
			} // Otherwise, it's a property or meta search
			else {
				$field_type = $filter['key'];
			}

			switch ( $field_type ) {

				/** @since 1.0.12 */
				case 'date_created':
					$filter = self::get_date_filter_value( $filter, null, true );
					break;

				/** @since 1.1 */
				case 'entry_id':
					$filter['key'] = 'id';
					break;

				case 'date':
					$filter = self::get_date_filter_value( $filter, 'Y-m-d', false );
					break;

				/**
				 * @since 1.0.12
				 */
				case 'post_category':
					$category_name = get_term_field( 'name', $filter['value'], 'category', 'raw' );
					if ( $category_name && ! is_wp_error( $category_name ) ) {
						$filter['value'] = $category_name . ':' . $filter['value'];
					}
					break;

				/**
				 * @since 2.0
				 */
				case 'workflow_current_status_timestamp':
					$filter = self::get_date_filter_value( $filter, 'U', false );
					break;
			}

			return $filter;
		}

		/**
		 * Creates a filter that should return zero results
		 *
		 * @since 1.0.7
		 * @return array
		 */
		public static function get_lock_filter() {

			return array(
				'key'      => 'created_by',
				'operator' => 'is',
				'value'    => 'Advanced Filter - This is the "force zero results" filter, designed to not match anything.',
			);
		}

		/**
		 * Store the filter settings in the `_gravityview_filters` post meta
		 *
		 * @param int $post_id Post ID
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

			$conditions = stripslashes_deep( \GV\Utils::_POST( 'gv_af_conditions' ) );
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
		 * @return void
		 */
		function admin_enqueue_scripts( $hook ) {

			global $post;

			// Don't process any scripts below here if it's not a GravityView page.
			if ( ! gravityview()->request->is_admin( $hook ) || empty( $post->ID ) ) {
				return;
			}

			$form_id = gravityview_get_form_id( $post->ID );

			$filter_settings = self::get_field_filters( $post->ID );

			if ( $form_id && empty( $filter_settings['field_filters'] ) ) {
				do_action( 'gravityview_log_error', '[print_javascript] Filter settings were not properly set', $filter_settings );

				return;
			}

			wp_enqueue_script( 'gravityview_adv_filter_admin', plugins_url( 'assets/js/advanced-filter.js', __FILE__ ), array( 'jquery' ), $this->_version );
			wp_enqueue_style( 'gravityview_adv_filter_admin', plugins_url( 'assets/css/advanced-filter.css', __FILE__ ), array(), $this->_version );

			wp_localize_script( 'gravityview_adv_filter_admin', 'gvAdvancedFilter', array(
				'fields'       => $filter_settings['field_filters'],
				'conditions'   => $filter_settings['init_filter_vars'],
				'fetchFields'  => array(
					'action' => self::AJAX_ACTION_GET_FIELD_FILTERS,
					'nonce'  => wp_create_nonce( 'gravityview-advanced-filter' ),
				),
				'translations' => array(
					'internet_explorer_notice' => esc_html__( 'Advanced Filter does not work in Internet Explorer. Please upgrade to another browser.', 'gravityview-advanced-filter' ),
					'fields_not_available'     => esc_html__( 'Form fields are not available. Please try refreshing the page or saving the View.', 'gravityview-advanced-filter' ),
					'add_condition'            => esc_html__( 'Add Condition', 'gravityview-advanced-filter' ),
					'join_and'                 => esc_html_x( 'and', "Join using  operator", 'gravityview-advanced-filter' ),
					'join_or'                  => esc_html_x( 'or', "Join using  operator", 'gravityview-advanced-filter' ),
					'is'                       => esc_html_x( 'is', 'Filter operator (e.g., A is TRUE)', 'gravityview-advanced-filter' ),
					'isnot'                    => esc_html_x( 'is not', 'Filter operator (e.g., A is not TRUE)', 'gravityview-advanced-filter' ),
					'>'                        => esc_html_x( 'greater than', 'Filter operator (e.g., A is greater than B)', 'gravityview-advanced-filter' ),
					'<'                        => esc_html_x( 'less than', 'Filter operator (e.g., A is less than B)', 'gravityview-advanced-filter' ),
					'contains'                 => esc_html_x( 'contains', 'Filter operator (e.g., AB contains B)', 'gravityview-advanced-filter' ),
					'ncontains'                => esc_html_x( 'does not contain', 'Filter operator (e.g., AB contains B)', 'gravityview-advanced-filter' ),
					'starts_with'              => esc_html_x( 'starts with', 'Filter operator (e.g., AB starts with A)', 'gravityview-advanced-filter' ),
					'ends_with'                => esc_html_x( 'ends with', 'Filter operator (e.g., AB ends with B)', 'gravityview-advanced-filter' ),
					'isbefore'                 => esc_html_x( 'is before', 'Filter operator (e.g., A is before date B)', 'gravityview-advanced-filter' ),
					'isafter'                  => esc_html_x( 'is after', 'Filter operator (e.g., A is after date B)', 'gravityview-advanced-filter' ),
					'ison'                     => esc_html_x( 'is on', 'Filter operator (e.g., A is on date B)', 'gravityview-advanced-filter' ),
					'isnoton'                  => esc_html_x( 'is not on', 'Filter operator (e.g., A is not on date B)', 'gravityview-advanced-filter' ),
					'isempty'                  => esc_html_x( 'is empty', 'Filter operator (e.g., A is empty)', 'gravityview-advanced-filter' ),
					'isnotempty'               => esc_html_x( 'is not empty', 'Filter operator (e.g., A is not empty)', 'gravityview-advanced-filter' ),
					'remove_field'             => esc_html__( 'Remove Field', 'gravityview-advanced-filter' ),
				),
			) );
		}

		/**
		 * Add Advanced Filters tooltips to Gravity Forms' localization
		 *
		 * @param array $tooltips
		 *
		 * @return array
		 */
		function tooltips( $tooltips = array() ) {

			$tooltips['gv_advanced_filter'] = array(
				'title' => __( 'Advanced Filter', 'gravityview-advanced-filter' ),
				'value' => wpautop(
					__( 'Limit what entries are visible based on entry values. The entries are filtered before the View is displayed. When users perform a search, results will first be filtered by these settings.', 'gravityview-advanced-filter' )
					. '<h6>' . __( 'Limit to Logged-in User Entries', 'gravityview-advanced-filter' ) . '</h6>'
					. sprintf( _x( 'To limit entries to those created by the current user, select "%s", "is" &amp; "%s" from the drop-down menus.', 'First placeholder is "Created By" and second is "Currently Logged-in User"', 'gravityview-advanced-filter' ), __( 'Created By', 'gravityview-advanced-filter' ), __( 'Currently Logged-in User', 'gravityview-advanced-filter' ) ) . ' '
					. sprintf( _x( 'If you want to limit entries to those created by the current user, but allow the administrators to view all the entries, select "%s" from the drop-down menu.', 'The placeholder is "Currently Logged-in User (Disabled for Administrators)"', 'gravityview-advanced-filter' ), __( 'Currently Logged-in User (Disabled for Administrators)', 'gravityview-advanced-filter' ) )
				),
			);

			return $tooltips;
		}

		/**
		 * Render the HTML container that will be replaced by the Javascript
		 *
		 * @return void
		 */
		function render_metabox( $settings = array() ) {

			include plugin_dir_path( __FILE__ ) . 'partials/metabox.php';

		}

		/**
		 * @deprecated 2.0
		 */
		static function get_view_filter_vars( $post_id, $admin_formatting = false ) {

			gravityview()->log->error( 'The get_view_filter_vars method is no longer functional. Should not be used.' );

			return array( 'mode' => 'all', self::get_lock_filter() );
		}

		/**
		 * Get user role choices formatted in a way used by GravityView and Gravity Forms input choices
		 *
		 * @since 1.2
		 *
		 * @return array Multidimensional array with `text` (Role Name) and `value` (Role ID) keys.
		 */
		protected static function get_user_role_choices() {

			$user_role_choices = array();

			$editable_roles = get_editable_roles();

			$editable_roles['current_user'] = array(
				'name' => esc_html__( 'Any Role of Current User', 'gravityview-advanced-filter' ),
			);

			$editable_roles = array_reverse( $editable_roles );

			foreach ( $editable_roles as $role => $details ) {

				$user_role_choices[] = array(
					'text'  => translate_user_role( $details['name'] ),
					'value' => esc_attr( $role ),
				);

			}

			return $user_role_choices;
		}

		/**
		 * Get field filter options from Gravity Forms and modify them
		 *
		 * @see GFCommon::get_field_filter_settings()
		 *
		 * @param int|string|null $post_id
		 * @param int|string|null $form_id
		 *
		 * @return array|void
		 */
		public static function get_field_filters( $post_id = null, $form_id = null ) {

			$form_id = ( $post_id ) ? gravityview_get_form_id( $post_id ) : $form_id;
			$form    = gravityview_get_form( $form_id );

			// Fixes issue on Views screen when deleting a view
			if ( empty( $form ) ) {
				return;
			}

			$field_filters = GFCommon::get_field_filter_settings( $form );

			$field_filters[] = array(
				'key'       => 'created_by_user_role',
				'text'      => esc_html__( 'Created By User Role', 'gravityview-advanced-filter' ),
				'operators' => array( 'is', 'isnot' ),
				'values'    => self::get_user_role_choices(),
			);

			$field_keys = wp_list_pluck( $field_filters, 'key' );

			if ( ! in_array( 'date_updated', $field_keys, true ) ) {
				$field_filters[] = array(
					'key'       => 'date_updated',
					'text'      => esc_html__( 'Date Updated', 'gravityview-advanced-filter' ),
					'operators' => array( 'is', '>', '<' ),
					'cssClass'  => 'datepicker ymd_dash',
				);
			}

			if ( $approved_column = GravityView_Admin_ApproveEntries::get_approved_column( $form ) ) {
				$approved_column = intval( floor( $approved_column ) );
			}

			$option_fields_ids = $product_fields_ids = $category_field_ids = $boolean_field_ids = $post_category_choices = array();

			/**
			 * @since 1.0.12
			 */
			if ( $boolean_fields = GFAPI::get_fields_by_type( $form, array( 'post_category', 'checkbox', 'radio', 'select' ) ) ) {
				$boolean_field_ids = wp_list_pluck( $boolean_fields, 'id' );
			}

			/**
			 * Get an array of field IDs that are Post Category fields
			 *
			 * @since 1.0.12
			 */
			if ( $category_fields = GFAPI::get_fields_by_type( $form, array( 'post_category' ) ) ) {

				$category_field_ids = wp_list_pluck( $category_fields, 'id' );

				/**
				 * @since 1.0.12
				 */
				$post_category_choices = gravityview_get_terms_choices();
			}

			// 1.0.14
			if ( $option_fields = GFAPI::get_fields_by_type( $form, array( 'option' ) ) ) {
				$option_fields_ids = wp_list_pluck( $option_fields, 'id' );
			}
			// 1.0.14
			if ( $product_fields = GFAPI::get_fields_by_type( $form, array( 'product' ) ) ) {
				$product_fields_ids = wp_list_pluck( $product_fields, 'id' );
			}

			// Add currently logged in user option
			foreach ( $field_filters as &$filter ) {

				// Add negative match to approval column
				if ( $approved_column && $filter['key'] === $approved_column ) {
					$filter['operators'][] = 'isnot';
					continue;
				}

				/**
				 * @since 1.0.12
				 */
				if ( in_array( $filter['key'], $category_field_ids, false ) ) {
					$filter['values'] = $post_category_choices;
				}

				if ( in_array( $filter['key'], $boolean_field_ids, false ) ) {
					$filter['operators'][] = 'isnot';
				}

				/**
				 * GF stores the option values in DB as "label|price" (without currency symbol)
				 * This is a temporary fix until the filter is proper built by GF
				 *
				 * @since 1.0.14
				 */
				if ( in_array( $filter['key'], $option_fields_ids ) && ! empty( $filter['values'] ) && is_array( $filter['values'] ) ) {
					require_once( GFCommon::get_base_path() . '/currency.php' );
					foreach ( $filter['values'] as &$value ) {
						$value['value'] = $value['text'] . '|' . GFCommon::to_number( $value['price'] );
					}
				}

				/**
				 * When saving the filters, GF is changing the operator to 'contains'
				 *
				 * @since 1.0.14
				 * @see   GFCommon::get_field_filters_from_post
				 */
				if ( in_array( $filter['key'], $product_fields_ids ) ) {
					$filter['operators'] = array( 'contains' );
				}

				// Gravity Forms already creates a "User" option.
				// We don't care about specific user, just the logged in status.
				if ( $filter['key'] === 'created_by' ) {

					// Update the default label to be more descriptive
					$filter['text'] = esc_attr__( 'Created By', 'gravityview-advanced-filter' );

					$current_user_filters = array(
						array(
							'text'  => __( 'Currently Logged-in User (Disabled for Administrators)', 'gravityview-advanced-filter' ),
							'value' => 'created_by_or_admin',
						),
						array(
							'text'  => __( 'Currently Logged-in User', 'gravityview-advanced-filter' ),
							'value' => 'created_by',
						),
					);

					foreach ( $current_user_filters as $user_filter ) {
						// Add to the beginning on the value options
						array_unshift( $filter['values'], $user_filter );
					}
				}

				/**
				 * When "is" and "is not" are combined with an empty value, they become "is empty" and "is not empty", respectively.
				 *
				 * Let's add these 2 proxy operators for a better UX. Exclusions: Entry ID and fields with predefined values (e.g., Payment Status).
				 *
				 * @since 2.0.3
				 *
				 * @param array $operators
				 */
				$_add_proxy_operators = function ( $operators ) {

					if ( in_array( 'is', $operators, true ) ) {
						$operators[] = 'isempty';
					}

					if ( in_array( 'isnot', $operators, true ) ) {
						$operators[] = 'isnotempty';
					}

					return $operators;

				};

				if ( ! empty( $filter['filters'] ) ) {
					foreach ( $filter['filters'] as &$data ) {
						$data['operators'] = $_add_proxy_operators( $data['operators'] );
					}
				}

				if ( isset( $filter['operators'] ) && ! isset( $filter['values'] ) && 'entry_id' !== $filter['key'] ) {
					$filter['operators'] = $_add_proxy_operators( $filter['operators'] );

				}
			}

			$field_filters = self::add_approval_status_filter( $field_filters );

			$init_field_id            = 0;
			$init_field_operator      = "contains";
			$default_init_filter_vars = array(
				"mode"    => "all",
				"filters" => array(
					array(
						"field"    => $init_field_id,
						"operator" => $init_field_operator,
						"value"    => '',
					),
				),
			);

			$filters          = get_post_meta( $post_id, '_gravityview_filters', true );
			$init_filter_vars = self::convert_filters_to_nested( $filters ); // Convert to v2

			/**
			 * @filter `gravityview/adv_filter/field_filters` allow field filters manipulation
			 * @param array $field_filters configured filters
			 * @param int   $post_id
			 */
			$field_filters = apply_filters( 'gravityview/adv_filter/field_filters', $field_filters, $post_id );

			return array(
				'field_filters'    => $field_filters,
				'init_filter_vars' => $init_filter_vars,
			);

		}

		/**
		 * Add Entry Approval Status filter option
		 *
		 * @since 1.3
		 *
		 * @return array
		 */
		private static function add_approval_status_filter( array $filters ) {

			if ( ! class_exists( 'GravityView_Entry_Approval_Status' ) ) {
				return $filters;
			}

			$approval_choices = GravityView_Entry_Approval_Status::get_all();

			$approval_values = array();

			foreach ( $approval_choices as & $choice ) {
				$approval_values[] = array(
					'text'  => $choice['label'],
					'value' => $choice['value'],
				);
			}

			$filters[] = array(
				'text'      => __( 'Entry Approval Status', 'gravityview-advanced-filter' ),
				'key'       => 'is_approved',
				'operators' => array( 'is', 'isnot' ),
				'values'    => $approval_values,
			);

			return $filters;
		}

		/**
		 * Get field filter options via an AJAX request
		 *
		 * @since 2.0
		 *
		 * @return void|mixed Returns test data during tests
		 */
		public static function get_field_filters_ajax() {

			// Validate AJAX request
			$is_valid_nonce  = wp_verify_nonce( rgpost( 'nonce' ), 'gravityview-advanced-filter' );
			$is_valid_action = self::AJAX_ACTION_GET_FIELD_FILTERS === rgpost( 'action' );
			$has_permissions = GVCommon::has_cap( 'gravityforms_edit_forms' );
			$form_id         = (int) rgpost( 'form_id' );

			if ( ! $is_valid_action || ! $is_valid_action || ! $form_id || ! $has_permissions ) {
				// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
				$response = array( 'response' => ( ! $is_valid_nonce || ! $has_permissions ) ? 403 : 400 );

				if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
					return $response;
				}

				wp_die( false, false, $response );
			}

			$filters = self::get_field_filters( null, $form_id );

			if ( ! empty( $filters['field_filters'] ) ) {
				wp_send_json_success(
					array(
						'fields' => $filters['field_filters'],
					)
				);
			} else {
				wp_send_json_error();
			}
		}
	} // end class

	new GravityView_Advanced_Filtering;
}
