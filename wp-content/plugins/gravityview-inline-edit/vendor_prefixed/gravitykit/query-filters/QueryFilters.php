<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 15-November-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\QueryFilters;

class QueryFilters {
	/**
	 * @since 1.0
	 * @var string Version.
	 */
	const VERSION = '1.0';

	/**
	 * @since 1.0
	 * @var array Assets handle.
	 */
	const ASSETS_HANDLE = 'gk-query-filters';

	/**
	 * @since 1.0
	 * @var array Filters.
	 */
	private $filters = array();

	/**
	 * @since 1.0
	 * @var array GF Form.
	 */
	private $form = array();

	/**
	 * @since 1.0
	 * @type array Map of virtual operators to GF_Query operators.
	 */
	private static $_proxy_operators_map = [
		'isempty'    => 'is',
		'isnotempty' => 'isnot',
	];

	/**
	 * Sets form on class instance.
	 *
	 * @since 1.0
	 *
	 * @param array $form GF Form.
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function set_form( array $form ) {
		if ( ! isset( $form['id'] ) || ! isset( $form['fields'] ) ) {
			throw new \Exception( 'Invalid form object provided.' );
		}

		$this->form = $form;
	}

	/**
	 * Sets filters on class instance.
	 *
	 * @since 1.0
	 *
	 * @param array $filters Field filters.
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function set_filters( array $filters ) {
		if ( ! isset( $filters['mode'] ) || ! isset( $filters['conditions'] ) ) {
			throw new \Exception( 'Invalid filter object provided.' );
		}

		$this->filters = $filters;
	}

	/**
	 * Converts filters and returns GF Query conditions.
	 *
	 * @since 1.0
	 *
	 * @throws \Exception
	 *
	 * @return \GF_Query_Condition|null
	 */
	public function get_query_conditions() {
		if ( empty( $this->form ) ) {
			throw new \Exception( 'Missing form object.' );
		}

		if ( empty( $this->filters ) ) {
			return null;
		}

		$filters = $this->filters;

		$filters = $this->augment_filters( $filters );
		$filters = $this->prune_filters( $filters );
		$filters = $this->convert_filters_to_gf_conditions( $filters );

		return $filters;
	}

	/**
	 * Changes supplied filters in place.
	 *
	 * - parse relative dates
	 * - replace create_by IDs
	 * - replace merge tags
	 * - etc.
	 *
	 * @param array $filters Filters.
	 *
	 * @return array $filters Modified filters.
	 */
	private function augment_filters( $filters ) {
		$augment = function ( &$filter ) use ( &$augment ) {
			if ( ! empty( $filter['mode'] ) && isset( $filter['conditions'] ) ) {
				// Logic definition.
				foreach ( $filter['conditions'] as &$condition ) {
					$augment( $condition );
				}
			} else {
				// Filter contents.
				if ( ! isset( $filter['key'] ) ) {
					// Can't match any with empty string.
					$filter = null;

					return;
				}

				if ( isset( $filter['value'] ) ) {
					$filter['value'] = self::process_merge_tags( $filter['value'] );
				}

				$original_filter = $filter;

				if ( $filter && in_array( $filter['key'], array( 'date_created', 'date_updated', 'payment_date' ), true ) ) {
					$filter = $this->detect_and_set_date( $filter, null, true );
				}

				if ( $filter && in_array( $filter['key'], array( 'created_by', 'created_by_user_role' ), true ) ) {
					$filter = $this->detect_and_set_user_id( $filter );
				}

				if ( $filter && 'created_by' !== $filter['key'] ) {
					$filter = $this->modify_additional_filter_values( $filter );
				}

				/**
				 * @filter `gk/query-filters/augment-filters` Modify filter before it is converted a GF Query condition.
				 *
				 * @since  1.0
				 *
				 * @param array $filter          Augmented filter.
				 * @param array $original_filter Original filter.
				 */
				$filter = apply_filters( 'gk/query-filters/augment-filter', $filter, $original_filter );
			}
		};

		$augment( $filters );

		return $filters;
	}

	/**
	 * Cleans up the conditions arrays and modes.
	 *
	 * @since 1.0
	 *
	 * @param array $filters Filters.
	 *
	 * @return array $filters Modified filters.
	 */
	private function prune_filters( $filters ) {
		$prune = function ( &$filter ) use ( &$prune ) {
			if ( ! empty( $filter['mode'] ) && isset( $filter['conditions'] ) ) {
				// Logic definition.
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
			}
		};

		$prune( $filter );

		return $filters;
	}

	/**
	 * Converts filters to a \GF_Query_Condition.
	 *
	 * @param array $filters Filters.
	 *
	 * @return \GF_Query_Condition
	 */
	private function convert_filters_to_gf_conditions( array $filters ) {
		$convert = function ( &$filter ) use ( &$convert ) {
			if ( ! empty( $filter['mode'] ) && isset( $filter['conditions'] ) ) {
				// Logic definition.
				foreach ( $filter['conditions'] as &$condition ) {
					// Map proxy operator to \GF_Query operator.
					if ( ! empty( $condition['operator'] ) && ! empty( self::$_proxy_operators_map[ $condition['operator'] ] ) ) {
						$condition['operator'] = self::$_proxy_operators_map[ $condition['operator'] ];
					}

					$convert( $condition );
				}

				$filter = call_user_func_array( array( 'GF_Query_Condition', $filter['mode'] == 'or' ? '_or' : '_and' ), $filter['conditions'] );
			} else {
				// Filter contents.
				if ( ! is_array( $filter ) || ! isset( $filter['key'] ) || ! isset( $filter['value'] ) ) {
					return;
				}

				unset( $filter['_id'] );

				$key = rgar( $filter, 'key' );

				$_tmp_query       = new \GF_Query( $this->form['id'], array( 'field_filters' => array( 'mode' => 'all', $filter ) ) );
				$_tmp_query_parts = $_tmp_query->_introspect();
				$_filter_value    = $filter['value'];

				$filter = $_tmp_query_parts['where'];

				if ( is_numeric( $key ) && in_array( $filter->operator, array( $filter::NLIKE, $filter::NBETWEEN, $filter::NEQ, $filter::NIN ) ) && '' !== $_filter_value ) {
					global $wpdb;

					$subquery = $wpdb->prepare(
						sprintf(
							"SELECT 1 FROM `%s` WHERE (`meta_key` LIKE %%s OR `meta_key` = %%d) AND `entry_id` = `%s`.`id`",
							\GFFormsModel::get_entry_meta_table_name(),
							$_tmp_query->_alias( null, $this->form['id'] )
						),
						sprintf( '%d.%%', $key ),
						$key
					);

					$filter = \GF_Query_Condition::_or( $filter, new \GF_Query_Condition( new \GF_Query_Call( 'NOT EXISTS', array( $subquery ) ) ) );
				}
			}
		};

		$convert( $filters );

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

		return $filters;
	}

	/**
	 * Sets correct date for date filter.
	 *
	 * @since 1.0
	 *
	 * @param array   $filter      Filter.
	 * @param ?string $date_format (Optional) date format to use.
	 * @param ?bool   $use_gmt     (Optional) whether the value is stored in GMT or not (GF-generated is GMT; datepicker is not).
	 *
	 * @return array $filter Modified filter.
	 */
	private function detect_and_set_date( $filter, $date_format = null, $use_gmt = false ) {
		// Date value should be empty if "is empty" or "is not empty" operators are used.
		if ( '' === $filter['value'] && in_array( $filter['operator'], array( 'isempty', 'isnotempty' ) ) ) {
			return $filter;
		}

		$local_timestamp = \GFCommon::get_local_timestamp();
		$date            = strtotime( $filter['value'], $local_timestamp );

		if ( ! isset( $date_format ) ) {
			$date_format = $this->validate_datetime( $filter['value'] ) ? 'Y-m-d' : 'Y-m-d H:i:s';
		}

		if ( $use_gmt ) {
			$filter['value'] = gmdate( $date_format, $date );
		} else {
			$filter['value'] = date( $date_format, $date );
		}

		return $filter;
	}

	/**
	 * Sets user ID for "Created By" filter.
	 *
	 * @param array $filter Filter.
	 *
	 * @return array|null $filter Modified filter.
	 */
	private function detect_and_set_user_id( $filter ) {
		switch ( $filter['key'] ) {
			case 'created_by':
				switch ( $filter['value'] ) {
					case 'created_by': // "Currently Logged-in User" option.
						if ( ! is_user_logged_in() ) {
							return $this->get_zero_results_filter();
						}

						$filter['value'] = get_current_user_id();

						break;
					case 'created_by_or_admin': // "Currently Logged-in User (Disabled for Administrators)" option.
						/**
						 * @filter `gk/query-filters/admin-capabilities` Customise the capabilities that define an Administrator able to view entries in frontend when filtered by "Created By".
						 *
						 * @since  1.0
						 *
						 * @param array $capabilities List of admin capabilities.
						 * @param array $form         GF form.
						 */
						$view_all_entries_caps = apply_filters( 'gk/query-filters/admin-capabilities', array( 'manage_options', 'gravityforms_view_entries' ), $this->form );

						foreach ( $view_all_entries_caps as $cap ) {
							if ( current_user_can( $cap ) ) {
								// Stop checking at first successful response.
								return null;
							}
						}

						if ( ! is_user_logged_in() ) {
							return $this->get_zero_results_filter();
						}

						$filter['value'] = get_current_user_id();

						break;
					case '':
						return $this->get_zero_results_filter();
					default:
						break;
				};

				return $filter;
			case 'created_by_user_role':
				$filter['key'] = 'created_by';

				if ( 'current_user' === $filter['value'] ) {
					$roles = wp_get_current_user()->roles;
				} else {
					$roles = array( $filter['value'] );
				}

				$filter['value'] = array();

				foreach ( $roles as $role ) {
					$filter['value'] = array_merge( $filter['value'], get_users( [
						'role'   => $role,
						'fields' => 'ID',
					] ) );
				}

				if ( empty( $filter['value'] ) ) {
					if ( 'is' === rgar( $filter, 'operator', 'is' ) ) {
						return $this->get_zero_results_filter();
					} else {
						return null;
					}
				}

				if ( count( $filter['value'] ) === 1 ) {
					$filter['value'] = reset( $filter['value'] );
				} else {
					if ( 'is' === rgar( $filter, 'operator', 'is' ) ) {
						$filter['operator'] = 'in';
					} else {
						$filter['operator'] = 'not in';
					}
				}

				return $filter;
		};

		return $filter;
	}

	/**
	 * For some specific field types, prepares the filter value before adding it to search criteria.
	 *
	 * @since 1.0
	 *
	 * @param array $filter Filter.
	 *
	 * @return array Modified filter.
	 */
	private function modify_additional_filter_values( $filter = array() ) {
		// Don't use `empty()` because `0` is a valid value for the key
		if ( ! isset( $filter['key'] ) || '' === $filter['key'] || ! function_exists( 'gravityview_get_field_type' ) || ! class_exists( 'GFCommon' ) || ! class_exists( 'GravityView_API' ) ) {
			return $filter;
		}

		// If it's a numeric value, it's a field.
		if ( is_numeric( $filter['key'] ) ) {
			// The "any form field" key is 0.
			if ( empty( $filter['key'] ) ) {
				return $filter;
			}

			if ( ! $field = \GFFormsModel::get_field( $this->form, $filter['key'] ) ) {
				return $filter;
			}

			$field_type = $field->type;
		} // Otherwise, it's a property or meta search
		else {
			$field_type = $filter['key'];
		}

		switch ( $field_type ) {
			case 'date_created':
				$filter = $this->detect_and_set_date( $filter, null, true );
				break;
			case 'entry_id':
				$filter['key'] = 'id';
				break;
			case 'date':
				$filter = $this->detect_and_set_date( $filter, 'Y-m-d', false );
				break;
			case 'post_category':
				$category_name = get_term_field( 'name', $filter['value'], 'category', 'raw' );
				if ( $category_name && ! is_wp_error( $category_name ) ) {
					$filter['value'] = $category_name . ':' . $filter['value'];
				}
				break;
			case 'workflow_current_status_timestamp':
				$filter = self::detect_and_set_date( $filter, 'U', false );
				break;
			case 'fileupload':
				if ( $field->multipleFiles && in_array( $filter['operator'], array( 'isempty', 'isnotempty' ) ) ) {
					$filter['value'] = 'array()';
				}
				break;
		}

		return $filter;
	}

	/**
	 * Gets user role choices formatted in a way used by GravityView and Gravity Forms input choices.
	 *
	 * @since 1.0
	 *
	 * @return array Multidimensional array with `text` (Role Name) and `value` (Role ID) keys.
	 */
	private function get_user_role_choices() {
		$user_role_choices = array();

		$editable_roles = get_editable_roles();

		$editable_roles['current_user'] = [
			'name' => esc_html__( 'Any Role of Current User', 'gk-gravityedit' ),
		];

		$editable_roles = array_reverse( $editable_roles );

		foreach ( $editable_roles as $role => $details ) {
			$user_role_choices[] = [
				'text'  => translate_user_role( $details['name'] ),
				'value' => esc_attr( $role ),
			];
		}

		return $user_role_choices;
	}

	/**
	 * Validates datetime value.
	 *
	 * @since 1.0
	 *
	 * @param string $datetime        The datetime value to check.
	 * @param string $expected_format Check whether the date is formatted as expected (default: Y-m-d).
	 *
	 * @return bool True: it's a valid datetime, formatted as expected. False: it's not a date formatted as expected.
	 */
	private function validate_datetime( $datetime, $expected_format = 'Y-m-d' ) {
		/**
		 * @var bool|\DateTime False if not a valid date (like a relative date), \DateTime if a date was created.
		 */
		$formatted_date = \DateTime::createFromFormat( 'Y-m-d', $datetime );

		/**
		 * @see http://stackoverflow.com/a/19271434/480856
		 */
		return ( $formatted_date && $formatted_date->format( $expected_format ) === $datetime );
	}

	/**
	 * Gets field filter options from Gravity Forms and modify them
	 *
	 * @see \GFCommon::get_field_filter_settings()
	 * @return array|void
	 */
	private function get_field_filters() {
		$field_filters = \GFCommon::get_field_filter_settings( $this->form );

		$field_filters[] = [
			'key'       => 'created_by_user_role',
			'text'      => esc_html__( 'Created By User Role', 'gk-gravityedit' ),
			'operators' => array( 'is', 'isnot' ),
			'values'    => $this->get_user_role_choices(),
		];

		$field_keys = wp_list_pluck( $field_filters, 'key' );

		if ( ! in_array( 'date_updated', $field_keys, true ) ) {
			$field_filters[] = [
				'key'       => 'date_updated',
				'text'      => esc_html__( 'Date Updated', 'gk-gravityedit' ),
				'operators' => array( 'is', '>', '<' ),
				'cssClass'  => 'datepicker ymd_dash',
			];
		}

		$approved_column = null;

		// See GravityView_Entry_Approval::get_approved_column()
		foreach ( $this->form['fields'] as $key => $field ) {
			$inputs = $field->get_entry_inputs();

			if ( ! empty( $field->gravityview_approved ) ) {
				if ( ! empty( $inputs ) && ! empty( $inputs[0]['id'] ) ) {
					$approved_column = $inputs[0]['id'];
					break;
				}
			}

			if ( 'checkbox' === $field->type && ! empty( $inputs ) ) {
				foreach ( $inputs as $input ) {
					if ( 'approved' === strtolower( $input['label'] ) ) {
						$approved_column = $input['id'];
						break;
					}
				}
			}
		}

		$option_fields_ids = $product_fields_ids = $category_field_ids = $boolean_field_ids = $post_category_choices = array();

		if ( $boolean_fields = \GFAPI::get_fields_by_type( $this->form, array( 'post_category', 'checkbox', 'radio', 'select' ) ) ) {
			$boolean_field_ids = wp_list_pluck( $boolean_fields, 'id' );
		}

		// Get an array of field IDs that are Post Category fields.
		if ( $category_fields = \GFAPI::get_fields_by_type( $this->form, array( 'post_category' ) ) && function_exists( 'gravityview_get_terms_choices' ) ) {
			$category_field_ids    = wp_list_pluck( $category_fields, 'id' );
			$post_category_choices = gravityview_get_terms_choices();
		}

		if ( $option_fields = \GFAPI::get_fields_by_type( $this->form, array( 'option' ) ) ) {
			$option_fields_ids = wp_list_pluck( $option_fields, 'id' );
		}

		if ( $product_fields = \GFAPI::get_fields_by_type( $this->form, array( 'product' ) ) ) {
			$product_fields_ids = wp_list_pluck( $product_fields, 'id' );
		}

		// Add currently logged in user option.
		foreach ( $field_filters as &$filter ) {
			// Add negative match to approval column.
			if ( $approved_column && $filter['key'] === $approved_column ) {
				$filter['operators'][] = 'isnot';
				continue;
			}

			if ( in_array( $filter['key'], $category_field_ids, false ) ) {
				$filter['values'] = $post_category_choices;
			}

			if ( in_array( $filter['key'], $boolean_field_ids, false ) ) {
				$filter['operators'][] = 'isnot';
			}

			/**
			 * GF stores the option values in DB as "label|price" (without currency symbol).
			 * This is a temporary fix until the filter is proper built by GF.
			 **/
			if ( in_array( $filter['key'], $option_fields_ids ) && ! empty( $filter['values'] ) && is_array( $filter['values'] ) ) {
				require_once( \GFCommon::get_base_path() . '/currency.php' );
				foreach ( $filter['values'] as &$value ) {
					$value['value'] = $value['text'] . '|' . \GFCommon::to_number( $value['price'] );
				}
			}

			/**
			 * When saving the filters, GF is changing the operator to 'contains'.
			 *
			 * @see   \GFCommon::get_field_filters_from_post
			 */
			if ( in_array( $filter['key'], $product_fields_ids ) ) {
				$filter['operators'] = array( 'contains' );
			}

			// GF already creates a "User" option. We don't care about specific user, just the logged in status.
			if ( 'created_by' === $filter['key'] ) {
				// Update the default label to be more descriptive.
				$filter['text'] = esc_attr__( 'Created By', 'gk-gravityedit' );

				$current_user_filters = [
					[
						'text'  => __( 'Currently Logged-in User (Disabled for Administrators)', 'gk-gravityedit' ),
						'value' => 'created_by_or_admin',
					],
					[
						'text'  => __( 'Currently Logged-in User', 'gk-gravityedit' ),
						'value' => 'created_by',
					],
				];

				foreach ( $current_user_filters as $user_filter ) {
					// Add to the beginning on the value options.
					array_unshift( $filter['values'], $user_filter );
				}
			}

			/**
			 * When "is" and "is not" are combined with an empty value, they become "is empty" and "is not empty", respectively.
			 * Let's add these 2 proxy operators for a better UX. Exclusions: Entry ID and fields with predefined values (e.g., Payment Status).
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

			/**
			 * Add extra operators for all fields except:
			 * 1) those with predefined values
			 * 2) Entry ID (it always exists)
			 * 3) "any form field" ("is empty" does not work: https://github.com/gravityview/Advanced-Filter/issues/91)
			 */
			if ( isset( $filter['operators'] ) && ! isset( $filter['values'] ) && ! in_array( $filter['key'], array( 'entry_id', '0' ) ) ) {
				$filter['operators'] = $_add_proxy_operators( $filter['operators'] );
			}
		}

		$field_filters = $this->add_approval_status_filter( $field_filters );

		usort( $field_filters, function ( $a, $b ) {
			return strcmp( $a['text'], $b['text'] );
		} );

		/**
		 * @filter `gk/query-filters/field-filters` Modify available field filters.
		 *
		 * @since  1.0
		 *
		 * @param array $field_filters Field filters.
		 */
		$field_filters = apply_filters( 'gk/query-filters/field-filters', $field_filters );

		return $field_filters;
	}

	/**
	 * Adds Entry Approval Status filter option.
	 *
	 * @since 1.4
	 *
	 * @param array $filters
	 *
	 * @return array
	 */
	private static function add_approval_status_filter( array $filters ) {
		if ( ! class_exists( 'GravityView_Entry_Approval_Status' ) ) {
			return $filters;
		}

		$approval_choices = \GravityView_Entry_Approval_Status::get_all();

		$approval_values = array();

		foreach ( $approval_choices as & $choice ) {
			$approval_values[] = array(
				'text'  => $choice['label'],
				'value' => $choice['value'],
			);
		}

		$filters[] = array(
			'text'      => __( 'Entry Approval Status', 'gk-gravityedit' ),
			'key'       => 'is_approved',
			'operators' => array( 'is', 'isnot' ),
			'values'    => $approval_values,
		);

		return $filters;
	}

	/**
	 * Creates a filter that should return zero results.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_zero_results_filter() {
		return array(
			'key'      => 'created_by',
			'operator' => 'is',
			'value'    => 'Advanced Filter - This is the "force zero results" filter, designed to not match anything.',
		);
	}

	/**
	 * Returns translation strings used in the UI.
	 *
	 * @since 1.0
	 *
	 * @return array $translations Translation strings.
	 */
	private function get_translations() {
		/**
		 * @filter `gk/query-filters/translations` Modify default translation strings.
		 *
		 * @since  1.0
		 *
		 * @param array $translations Translation strings.
		 */
		$translations = apply_filters( 'gk/query-filters/translations', [
			'internet_explorer_notice' => esc_html__( 'Internet Explorer is not supported. Please upgrade to another browser.', 'gk-gravityedit' ),
			'fields_not_available'     => esc_html__( 'Form fields are not available. Please try refreshing the page.', 'gk-gravityedit' ),
			'add_condition'            => esc_html__( 'Add Condition', 'gk-gravityedit' ),
			'join_and'                 => esc_html_x( 'and', 'Join using "and" operator', 'gk-gravityedit' ),
			'join_or'                  => esc_html_x( 'or', 'Join using "or" operator', 'gk-gravityedit' ),
			'is'                       => esc_html_x( 'is', 'Filter operator (e.g., A is TRUE)', 'gk-gravityedit' ),
			'isnot'                    => esc_html_x( 'is not', 'Filter operator (e.g., A is not TRUE)', 'gk-gravityedit' ),
			'>'                        => esc_html_x( 'greater than', 'Filter operator (e.g., A is greater than B)', 'gk-gravityedit' ),
			'<'                        => esc_html_x( 'less than', 'Filter operator (e.g., A is less than B)', 'gk-gravityedit' ),
			'contains'                 => esc_html_x( 'contains', 'Filter operator (e.g., AB contains B)', 'gk-gravityedit' ),
			'ncontains'                => esc_html_x( 'does not contain', 'Filter operator (e.g., AB contains B)', 'gk-gravityedit' ),
			'starts_with'              => esc_html_x( 'starts with', 'Filter operator (e.g., AB starts with A)', 'gk-gravityedit' ),
			'ends_with'                => esc_html_x( 'ends with', 'Filter operator (e.g., AB ends with B)', 'gk-gravityedit' ),
			'isbefore'                 => esc_html_x( 'is before', 'Filter operator (e.g., A is before date B)', 'gk-gravityedit' ),
			'isafter'                  => esc_html_x( 'is after', 'Filter operator (e.g., A is after date B)', 'gk-gravityedit' ),
			'ison'                     => esc_html_x( 'is on', 'Filter operator (e.g., A is on date B)', 'gk-gravityedit' ),
			'isnoton'                  => esc_html_x( 'is not on', 'Filter operator (e.g., A is not on date B)', 'gk-gravityedit' ),
			'isempty'                  => esc_html_x( 'is empty', 'Filter operator (e.g., A is empty)', 'gk-gravityedit' ),
			'isnotempty'               => esc_html_x( 'is not empty', 'Filter operator (e.g., A is not empty)', 'gk-gravityedit' ),
			'remove_field'             => esc_html__( 'Remove Field', 'gk-gravityedit' ),
			'available_choices'        => esc_html__( 'Return to Field Choices', 'gk-gravityedit' ),
			'available_choices_label'  => esc_html__( 'Return to the list of choices defined by the field.', 'gk-gravityedit' ),
			'custom_is_operator_input' => esc_html__( 'Custom Choice', 'gk-gravityedit' ),
			'untitled'                 => esc_html__( 'Untitled', 'gk-gravityedit' ),
			'field_not_available'      => esc_html__( 'Form field ID #%d is no longer available. Please remove this condition.', 'gk-gravityedit' ),
		] );

		return $translations;
	}

	/**
	 * Enqueues UI scripts.
	 *
	 * @since 1.0
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 */
	public function enqueue_scripts( array $meta = array() ) {
		$handle = rgar( $meta, 'handle', self::ASSETS_HANDLE );
		$ver    = rgar( $meta, 'ver', self::VERSION );
		$src    = rgar( $meta, 'src', plugins_url( 'assets/js/query-filters.js', __FILE__ ) );
		$deps   = rgar( $meta, 'deps', array( 'jquery' ) );

		wp_enqueue_script( $handle, $src, $deps, $ver );

		wp_localize_script(
			$handle,
			sprintf( 'gkQueryFilters_%s', uniqid() ),
			[
				'fields'                    => rgar( $meta, 'fields', $this->get_field_filters() ),
				'conditions'                => rgar( $meta, 'conditions', array() ),
				'targetElementSelector'     => rgar( $meta, 'target_element_selector', '#gk-query-filters' ),
				'autoscrollElementSelector' => rgar( $meta, 'autoscroll_element_selector', '' ),
				'inputElementName'          => rgar( $meta, 'input_element_name', 'gk-query-filters' ),
				'translations'              => rgar( $meta, 'translations', $this->get_translations() ),
			]
		);
	}

	/**
	 * Enqueues UI styles.
	 *
	 * @since 1.0
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 */
	public static function enqueue_styles( array $meta = array() ) {
		$handle = rgar( $meta, 'handle', self::ASSETS_HANDLE );
		$ver    = rgar( $meta, 'ver', self::VERSION );
		$src    = rgar( $meta, 'src', plugins_url( 'assets/css/query-filters.css', __FILE__ ) );
		$deps   = rgar( $meta, 'deps', array() );

		wp_enqueue_style( $handle, $src, $deps, $ver );
	}

	/**
	 * Converts GF conditional logic rules to the object used by Query Filters.
	 *
	 * @since 1.0
	 *
	 * @param array $gf_conditional_logic GF conditional logic object.
	 *
	 * @return array Original or converted object.
	 */
	public function convert_gf_conditional_logic( array $gf_conditional_logic ) {
		if ( ! isset( $gf_conditional_logic['actionType'], $gf_conditional_logic['logicType'], $gf_conditional_logic['rules'] ) ) {
			return $gf_conditional_logic;
		}

		$conditions = array();

		foreach ( $gf_conditional_logic['rules'] as $rule ) {
			$conditions[] = array(
				'_id'      => wp_generate_password( 4, false ),
				'key'      => rgar( $rule, 'fieldId' ),
				'operator' => rgar( $rule, 'operator' ),
				'value'    => rgar( $rule, 'value' ),
			);
		}

		$query_filters_conditional_logic = array(
			'_id'        => wp_generate_password( 4, false ),
			'mode'       => 'and',
			'conditions' => array()
		);

		if ( 'all' === $gf_conditional_logic['logicType'] ) {
			foreach ( $conditions as $condition ) {
				$query_filters_conditional_logic['conditions'][] = array(
					'_id'        => wp_generate_password( 4, false ),
					'mode'       => 'or',
					'conditions' => array(
						$condition
					)
				);
			}
		} else {
			$query_filters_conditional_logic['conditions'] = array(
				array(
					'_id'        => wp_generate_password( 4, false ),
					'mode'       => 'or',
					'conditions' => $conditions
				)
			);
		}

		return $query_filters_conditional_logic;
	}

	/**
	 * Process merge tags in filter value
	 *
	 * @since 1.6
	 *
	 * @param string $filter_value Filter value
	 *
	 * @return string
	 */
	private function process_merge_tags( $filter_value ) {
		preg_match_all( '/{get:(.*?)}/ism', $filter_value, $get_merge_tags, PREG_SET_ORDER );

		$urldecode_get_merge_tag_value = function ( $value ) {
			return urldecode( $value );
		};

		foreach ( $get_merge_tags as $merge_tag ) {
			add_filter( 'gravityview/merge_tags/get/value/' . $merge_tag[1], $urldecode_get_merge_tag_value );
		}

		$processed_filter_value = ( class_exists( 'GravityView_API' ) ) ? \GravityView_API::replace_variables( $filter_value, $this->form ) : \GFCommon::replace_variables( $filter_value, $this->form, $entry = array(), $url_encode = false, $esc_html = true, $nl2br = true, $format = 'html', $aux_data = array() );

		return $processed_filter_value;
	}
}
