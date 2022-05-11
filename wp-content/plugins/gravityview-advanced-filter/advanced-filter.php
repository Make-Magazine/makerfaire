<?php
/*
Plugin Name: GravityView - Advanced Filter Extension
Plugin URI: https://gravityview.co/extensions/advanced-filter/?utm_source=advanced-filter&utm_content=plugin_uri&utm_medium=meta&utm_campaign=internal
Description: Filter which entries are shown in a View based on their values.
Version: 2.1.14
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

		protected $_version = '2.1.14';

		protected $_min_gravityview_version = '2.0';

		/**
		 * @since 1.0.11
		 * @type int
		 */
		protected $_item_id = 30;

		protected $_path = __FILE__;

		protected $_text_domain = 'gravityview-advanced-filter';

		/**
		 * @type array Map of virtual operators to GF_Query operators
		 */
		private static $_proxy_operators_map = array(
			'isempty'    => 'is',
			'isnotempty' => 'isnot',
		);

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

		function add_hooks() {

			add_action( 'gravityview_metabox_filter_after', array( $this, 'render_metabox' ) );

			// Admin_Views::add_scripts_and_styles() runs at 999
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 1100 );

			add_filter( 'gravityview_noconflict_scripts', array( $this, 'no_conflict_script_filter' ) );

			add_action( 'gravityview/view/query', array( $this, 'gf_query_filter' ), 10, 3 );

			add_filter( 'gravityview_noconflict_styles', array( $this, 'no_conflict_style_filter' ) );

			add_filter( 'gform_filters_get_users', array( $this, 'created_by_get_users_args' ) );

			add_action( 'wp_ajax_' . self::AJAX_ACTION_GET_FIELD_FILTERS, array( __CLASS__, 'get_field_filters_ajax' ) );

			add_filter( 'gravityview_template_field_options', array( $this, 'modify_view_field_settings' ), 999, 5 );

			add_filter( 'gravityview/template/field/output', array( $this, 'conditionally_display_field_output' ), 10, 2 );
		}

		/**
		 * Add conditional logic fields to field settings
		 *
		 * @since 2.1
		 *
		 * @param array  $field_options Array of field options
		 * @param string $template_id   Table slug
		 * @param float  $field_id      GF Field ID
		 * @param string $context       Context (e.g., single or directory)
		 * @param string $input_type    Input type (e.g., textarea, list, select, etc.)
		 * @param int    $form_id       Form ID
		 *
		 * @return array
		 */
		function modify_view_field_settings( $field_options, $template_id, $field_id, $context, $input_type ) {

			if ( 'edit' === $context ) {
				return $field_options;
			}

			$strings = array(
				'conditional_logic_label'             => esc_html__( 'Conditional Logic', 'gravityview-advanced-filter' ),
				'conditional_logic_label_desc'        => esc_html__( 'Only show the field if the configured conditions apply.', 'gravityview-advanced-filter' ),
				'conditional_logic_fail_output_label' => esc_html__( 'Empty Field Content', 'gravityview-advanced-filter' ),
				'conditional_logic_fail_output_desc'  => esc_html__( 'Display custom content when field value does not meet conditional logic', 'gravityview-advanced-filter' ),
			);

			$conditional_logic_container                  = <<<HTML
	<span class="gv-label">{$strings['conditional_logic_label']}</span>
	<span class="howto">{$strings['conditional_logic_label_desc']}</span>
	<div class="gv-field-conditional-logic"></div>
HTML;
			$field_options['conditional_logic_container'] = array(
				'type'     => 'html',
				'desc'     => $conditional_logic_container,
				'group'    => 'visibility',
				'priority' => 10000,
			);

			$field_options['conditional_logic_fail_output'] = array(
				'type'       => 'textarea',
				'label'      => $strings['conditional_logic_fail_output_label'],
				'desc'       => $strings['conditional_logic_fail_output_desc'],
				'tooltip'    => true,
				'article'    => array(
					'id'  => '611420b6766e8844fc34f9a3',
					'url' => 'https://docs.gravityview.co/article/775-field-conditional-logic-doesnt-hide-empty-fields',
				),
				'merge_tags' => 'force',
				'group'      => 'visibility',
				'priority'   => 10100,
			);

			$field_options[ self::CONDITIONAL_LOGIC_META ] = array(
				'type'     => 'hidden',
				'group'    => 'visibility',
				'priority' => 10200,
			);

			return $field_options;
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
		 *
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
		 *
		 * @return array          Modified array
		 */
		function no_conflict_style_filter( $styles ) {

			$styles[] = 'gravityview_adv_filter_admin';

			return $styles;
		}

		/**
		 * Modify search criteria
		 *
		 * @deprecated 2.0
		 *
		 * Use the 2.0 GF_Query filters instead
		 *
		 * @param array $form_ids       Form IDs for the search
		 * @param int   $passed_view_id (optional)
		 *
		 * @param array $criteria       Existing search criteria array, if any
		 *
		 * @return     [type]                 [description]
		 *
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

				if ( isset( $filter['value'] ) ) {

					$form = array();
					if ( $view instanceof \GV\View ) {
						$form = $view->form->form;
					}

					// Replace merge tags
					$filter['value'] = self::process_merge_tags( $filter['value'], $form );
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
			 *
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

			$empty_date_adjustment = function ( $sql ) {
				// Depending on the database configuration, a statement like "date_updated = ''" may throw an "incorrect DATETIME value" error
				// Also, "date_updated" is always populated with the "date_created" value when an entry is created, so an empty "date_updated" (that is, it was never changed) should equal "date_created"
				// $match[0] = `table_name`.`date_updated|date_created|payment_date` = ''
				// $match[1] = `table_name`.`date_updated|date_created|payment_date`
				// $match[2] = `table_name`
				preg_match( "/((`\w+`)\.`(?:date_updated|date_created|payment_date)`) !?= ''/ism", rgar( $sql, 'where' ), $match );

				if ( empty( $sql['where'] ) || ! $match ) {
					return $sql;
				}

				$operator = strpos( $match[0], '!=' ) !== false ? '!=' : '=';

				$new_condition = sprintf( 'UNIX_TIMESTAMP(%s) %s 0', $match[1], $operator );

				// Change "date_updated = ''" to "UNIX_TIMESTAMP(date_updated) = 0" (or "!= 0) depending on the operator
				$sql['where'] = str_replace( $match[0], $new_condition, $sql['where'] );

				if ( strpos( $match[0], 'date_updated' ) !== false ) {
					// Add "OR date_updated = date_created" condition
					if ( '=' === $operator ) {
						$sql['where'] = str_replace( $new_condition, sprintf( '(%s OR %s = %s.`date_created`)', $new_condition, $match[1], $match[2] ), $sql['where'] );
					} else {
						// Add "AND date_updated != date_created" condition
						$sql['where'] = str_replace( $new_condition, sprintf( '(%s AND %s != %s.`date_created`)', $new_condition, $match[1], $match[2] ), $sql['where'] );
					}
				}

				return $sql;
			};

			add_filter( 'gform_gf_query_sql', $empty_date_adjustment );
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

				foreach ( $filter['conditions'] as &$condition ) {
					// Map proxy operator to GF_Query operator
					if ( ! empty( $condition['operator'] ) && ! empty( self::$_proxy_operators_map[ $condition['operator'] ] ) ) {
						$condition['operator'] = self::$_proxy_operators_map[ $condition['operator'] ];
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
		 *
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
					$lock_filter = self::get_lock_filter();
					$lock_filter['value'] = -1;

					switch ( $filter['value'] ) {
						case 'created_by':
							if ( ! is_user_logged_in() ) {
								return $lock_filter; // Nothing to show for
							}

							$filter['value'] = get_current_user_id();

							break;
						case 'created_by_or_admin':
							/**
							 * Customise the capabilities that define an Administrator able to view entries in frontend when filtered by Created_by
							 *
							 * @since 1.0.9
							 *
							 * @param int          $post_id      View ID where the filter is set
							 *
							 * @param array|string $capabilities List of admin capabilities
							 */
							$view_all_entries_caps = apply_filters( 'gravityview/adv_filter/admin_caps', array( 'manage_options', 'gravityforms_view_entries', 'gravityview_edit_others_entries' ), $view->ID );

							if ( GVCommon::has_cap( $view_all_entries_caps ) ) {
								return null; // Administrative role, all good, no created_by filtering at all
							}

							if ( ! is_user_logged_in() ) {
								return $lock_filter; // Nothing to show for
							}

							$filter['key']   = 'created_by';
							$filter['value'] = get_current_user_id();

							break;
						case '':
							return $lock_filter; // Empty, wut?
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
		 *
		 * @param      $filter
		 * @param null $date_format
		 * @param bool $use_gmt Whether the value is stored in GMT or not (GF-generated is GMT; datepicker is not)
		 *
		 * @return mixed
		 */
		static function get_date_filter_value( $filter, $date_format = null, $use_gmt = false ) {

			// Date value should be empty if "is empty" or "is not empty" operators are used
			if ( '' === $filter['value'] && in_array( $filter['operator'], array( 'isempty', 'isnotempty' ) ) ) {
				return $filter;
			}

			$local_timestamp = GFCommon::get_local_timestamp();
			$date            = strtotime( $filter['value'], $local_timestamp );

			if ( ! isset( $date_format ) ) {
				$date_format = self::is_valid_datetime( $filter['value'] ) ? 'Y-m-d' : 'Y-m-d H:i:s';
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
		 *
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

			// Replace merge tags
			$filter['value'] = self::process_merge_tags( $filter['value'], $form );

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

				/**
				 * Empty multi-file upload field contains '[]' (empty JSON array) as a value
				 *
				 * @since 2.1.10
				 */
				case 'fileupload':
					if ( $field->multipleFiles && in_array( $filter['operator'], array( 'isempty', 'isnotempty' ) ) ) {
						$filter['value'] = '[]';
					}
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
		 *
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

			if ( $form_id && empty( $filter_settings['field_filters_complete'] ) ) {
				do_action( 'gravityview_log_error', '[print_javascript] Filter settings were not properly set', $filter_settings );

				return;
			}

			wp_enqueue_script( 'gravityview_adv_filter_admin', plugins_url( 'assets/js/advanced-filter.js', __FILE__ ), array( 'jquery' ), $this->_version );
			wp_enqueue_style( 'gravityview_adv_filter_admin', plugins_url( 'assets/css/advanced-filter.css', __FILE__ ), array(), $this->_version );

			wp_localize_script( 'gravityview_adv_filter_admin', 'gvAdvancedFilter', array(
				'fields_complete' => rgar( $filter_settings, 'field_filters_complete', array() ),
				'fields_default'  => rgar( $filter_settings, 'field_filters_default', array() ),
				'conditions'      => rgar( $filter_settings, 'init_filter_vars', array() ),
				'fetchFields'     => array(
					'action' => self::AJAX_ACTION_GET_FIELD_FILTERS,
					'nonce'  => wp_create_nonce( 'gravityview-advanced-filter' ),
				),
				'translations'    => array(
					'internet_explorer_notice' => esc_html__( 'Advanced Filter does not work in Internet Explorer. Please upgrade to another browser.', 'gravityview-advanced-filter' ),
					'fields_not_available'     => esc_html__( 'Form fields are not available. Please try refreshing the page or saving the View.', 'gravityview-advanced-filter' ),
					'add_condition'            => esc_html__( 'Add Condition', 'gravityview-advanced-filter' ),
					'join_and'                 => esc_html_x( 'and', 'Join using "and" operator', 'gravityview-advanced-filter' ),
					'join_or'                  => esc_html_x( 'or', 'Join using "or" operator', 'gravityview-advanced-filter' ),
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
					'available_choices'        => esc_html__( 'Return to Field Choices', 'gravityview-advanced-filter' ),
					'available_choices_label'  => esc_html__( 'Return to the list of choices defined by the field.', 'gravityview-advanced-filter' ),
					'custom_is_operator_input' => esc_html__( 'Custom Choice', 'gravityview-advanced-filter' ),
					'untitled'                 => esc_html__( 'Untitled', 'gravityview-advanced-filter' ),
					'field_not_available'      => esc_html__( 'Form field ID #%d is no longer available. Please remove this condition.', 'gravityview-advanced-filter' ),
				),
			) );
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

			if ( $approved_column = GravityView_Entry_Approval::get_approved_column( $form ) ) {
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

			// Add currently logged-in user option
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
				if ( 'created_by' === $filter['key'] ) {

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
				$_add_proxy_operators = function ( $operators, $filter_key ) {
					if ( 'date_created' === $filter_key ) {
						return $operators;
					}

					if ( in_array( 'is', $operators, true ) ) {
						$operators[] = 'isempty';
					}

					if ( 'date_updated' === $filter_key || in_array( 'isnot', $operators, true ) ) {
						$operators[] = 'isnotempty';
					}

					return $operators;
				};

				if ( ! empty( $filter['filters'] ) ) {
					foreach ( $filter['filters'] as &$data ) {
						$data['operators'] = $_add_proxy_operators( $data['operators'], $filter['key'] );
					}
				}

				/**
				 * Add extra operators for all fields except:
				 * 1) those with predefined values
				 * 2) Entry ID (it always exists)
				 * 3) "any form field" ("is empty" does not work: https://github.com/gravityview/Advanced-Filter/issues/91)
				 */
				if ( isset( $filter['operators'] ) && ! isset( $filter['values'] ) && ! in_array( $filter['key'], array( 'entry_id', '0' ) ) ) {
					$filter['operators'] = $_add_proxy_operators( $filter['operators'], $filter['key'] );
				}
			}

			$field_filters    = self::add_approval_status_filter( $field_filters );
			$filters          = get_post_meta( $post_id, '_gravityview_filters', true );
			$init_filter_vars = self::convert_filters_to_nested( $filters ); // Convert to v2

			/**
			 * @filter `gravityview/adv_filter/field_filters` allow field filters manipulation
			 *
			 * @param array $field_filters configured filters
			 * @param int   $post_id
			 */
			$field_filters = apply_filters( 'gravityview/adv_filter/field_filters', $field_filters, $post_id );

			// For field conditional logic we use only default meta/properties and form fields
			$field_filters_default        = array();
			$_meta_and_properties_to_keep = array(
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
			);

			foreach ( $field_filters as &$filter ) {
				if ( ! in_array( $filter['key'], $_meta_and_properties_to_keep, true ) && ! is_numeric( $filter['key'] ) ) {
					continue;
				}

				$field_filters_default[] = $filter;
			}

			return array(
				'field_filters_complete' => $field_filters,
				'field_filters_default'  => $field_filters_default,
				'init_filter_vars'       => $init_filter_vars,
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

			if ( ! empty( $filters['field_filters_complete'] ) ) {
				wp_send_json_success(
					array(
						'fields_complete' => rgar( $filters, 'field_filters_complete', array() ),
						'fields_default'  => rgar( $filters, 'field_filters_default', array() ),
					)
				);
			} else {
				wp_send_json_error();
			}
		}

		/**
		 * Determine if entry meets conditional logic
		 *
		 * @since 2.1
		 * @since 2.1.3 Added $context argument
		 *
		 * @param array               $entry   GV Entry
		 * @param array               $filters Conditional logic filters
		 * @param GV\Template_Context $context Template context {@since 2.1.3}
		 *
		 * @return bool
		 */
		protected function meets_conditional_logic( $entry, $filters, $context ) {

			$_this = $this;

			$test_filter_conditions = function ( $filters, $mode ) use ( &$test_filter_conditions, $entry, $context, $_this ) {

				$results = array();

				foreach ( $filters['conditions'] as &$filter_condition ) {
					if ( ! empty( $filter_condition['conditions'] ) ) {
						$results[] = $test_filter_conditions( $filter_condition, $filter_condition['mode'] );
						continue;
					}

					$field_value         = GravityView_Advanced_Filtering::process_merge_tags( $filter_condition['value'], $context->view->form->form, $entry );
					$comparison_operator = $filter_condition['operator'];

					if ( ! empty( GravityView_Advanced_Filtering::$_proxy_operators_map[ $comparison_operator ] ) ) {
						$comparison_operator = GravityView_Advanced_Filtering::$_proxy_operators_map[ $comparison_operator ];
						$field_value         = '';
					}

					$form = $context->view->form;

					if ( '0' === $filter_condition['key'] ) { // process "any form field" condition
						$_result = false;

						foreach ( $entry as $field_id => $entry_value ) {
							if ( ! is_numeric( $field_id ) ) { // form fields always have numeric IDs
								continue;
							}

							$field = $form::get_field( $form, $field_id );

							if ( $field && 'date' === $field->type && $_this->compare_dates( $entry_value, $field_value, $comparison_operator ) ) {
								$_result = true; // a single match satisfies condition
								break;
							} elseif ( GFFormsModel::matches_operation( $entry_value, $field_value, $comparison_operator ) ) {
								$_result = true; // a single match satisfies condition
								break;
							}
						}

						$results[] = $_result;
					} else {
						$field_id = $filter_condition['key'];

						if ( strpos( $filter_condition['key'], '.' ) !== false ) {
							$field_id = explode( '.', $filter_condition['key'] );
							$field_id = $field_id[0];
						}

						$field = $form::get_field( $form, $field_id );

						$entry_value = '';

						if ( $field && $field->inputs && $field->choices ) {
							$input_id = null;

							foreach ( $field->choices as $i => $choice ) {
								if ( $field_value === $choice['value'] ) {
									$input_id = (string) $field->inputs[ $i ]['id'];
								}
							}

							$entry_value = ( isset( $entry[ $input_id ] ) ) ? $entry[ $input_id ] : '';
						} else if ( isset( $entry[ $filter_condition['key'] ] ) ) {
							$entry_value = $entry[ $filter_condition['key'] ];
						}

						// Empty multi-file upload field contains '[]' (empty JSON array) as a value
						if ( ( $field && 'fileupload' === $field->type ) && '[]' === $entry_value ) {
							$entry_value = '';
						}

						if ( ( $field && 'date' === $field->type ) || in_array( $filter_condition['key'], array( 'date_created', 'date_updated', 'payment_date' ), true ) ) {
							$results[] = $_this->compare_dates( $entry_value, $field_value, $comparison_operator );
						} else {
							$results[] = GFFormsModel::matches_operation( $entry_value, $field_value, $comparison_operator );
						}
					}
				}

				// "and" mode requires all values to be true
				if ( 'and' === $mode ) {
					return ! in_array( false, $results, true );
				} else {
					// "or" mode requires at least one true value
					return in_array( true, $results, true );
				}
			};

			return $test_filter_conditions( $filters, $filters['mode'] );
		}

		/**
		 * Display field if conditional logic is met
		 *
		 * @since 2.1
		 *
		 * @param string              $field_output Field output
		 * @param GV\Template_Context $context      Template context
		 *
		 * @return string
		 */
		function conditionally_display_field_output( $field_output, $context ) {

			$filters = rgar( $context->field->as_configuration(), self::CONDITIONAL_LOGIC_META, false );

			if ( ! $filters || 'null' === $filters ) { // Empty conditions are a "null" string
				return $field_output;
			}

			$filters = json_decode( $filters, true );

			self::augment_filters( $filters, $context->view );
			self::prune_filters( $filters );

			$entry = $context->entry->as_entry();

			if ( empty( $filters ) || $this->meets_conditional_logic( $entry, $filters, $context ) ) {
				return $field_output;
			}

			$conditional_logic_fail_output = rgar( $context->field->as_configuration(), self::CONDITIONAL_LOGIC_FAIL_OUTPUT_META, false );
			$conditional_logic_fail_output = self::process_merge_tags( $conditional_logic_fail_output, $context->view->form->form, $entry );

			/**
			 * @filter `gravityview/field/value/empty` What to display when this field is empty.
			 *
			 * @param string $value The value to display (Default: empty string)
			 * @param \GV\Template_Context The template context this is being called from.
			 */
			return apply_filters( 'gravityview/field/value/empty', $conditional_logic_fail_output, $context );
		}

		/**
		 * Compare two dates
		 *
		 * @since 2.1.5
		 *
		 * @param string $source_date
		 * @param string $target_date
		 * @param string $comparison_operator
		 *
		 * @return bool
		 */
		function compare_dates( $source_date, $target_date, $comparison_operator ) {

			try {
				$source_date = new \DateTime( $source_date );
				$source_date = $source_date->getTimestamp();
			} catch ( Exception $e ) {
				$error_message = sprintf( 'Date comparison: $source_date "%s" could not be converted to a valid DateTime object. Error: %s', $source_date, $e->getMessage() );
				gravityview()->log->notice( $error_message );
			}

			try {
				$target_date = new \DateTime( $target_date );
				$target_date = $target_date->getTimestamp();
			} catch ( Exception $e ) {
				$error_message = sprintf( 'Date comparison: $target_date "%s" could not be converted to a valid DateTime object. Error: %s', $target_date, $e->getMessage() );
				gravityview()->log->notice( $error_message );
			}

			return GFFormsModel::matches_operation( $source_date, $target_date, $comparison_operator );
		}

		/**
		 * Process merge tags in filter values
		 *
		 * @since 2.1.6
		 *
		 * @param string $filter_value Filter value text
		 * @param array  $form         GF Form array
		 * @param array  $entry        GF Entry array
		 *
		 * @return string
		 */
		static function process_merge_tags( $filter_value, $form = array(), $entry = array() ) {

			preg_match_all( "/{get:(.*?)}/ism", $filter_value, $get_merge_tags, PREG_SET_ORDER );

			$urldecode_get_merge_tag_value = function ( $value ) {
				return urldecode( $value );
			};

			foreach ( $get_merge_tags as $merge_tag ) {
				add_filter( 'gravityview/merge_tags/get/value/' . $merge_tag[1], $urldecode_get_merge_tag_value );
			}

			$processed_filter_value = GravityView_API::replace_variables( $filter_value, $form, $entry );

			return $processed_filter_value;
		}
	} // end class

	new GravityView_Advanced_Filtering;
}
