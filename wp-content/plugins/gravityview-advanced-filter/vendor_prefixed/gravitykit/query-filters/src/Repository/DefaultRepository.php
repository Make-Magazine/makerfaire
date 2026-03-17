<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 20-February-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Repository;

use GF_Field;
use GFAPI;
use GFCommon;
use GFFormsModel;
use GravityView_Entry_Approval;
use GravityView_Entry_Approval_Status;
use GravityView_View;
use GV\View;
use WP_User;

/**
 * Default repository backed by WordPress and GravityView.
 *
 * @since 2.0.0
 */
final class DefaultRepository implements FormRepository, UserRepository {
	/**
	 * Micro cache for forms.
	 *
	 * @since 2.0.0
	 * @var array<int, array>
	 */
	private $forms = [];

	/**
	 * Prepends the current user's ID to the field filters.
	 *
	 * @since 2.7.0
	 *
	 * @param array             $field_filters     Original field filters.
	 * @param array<int|string> $pa_user_field_ids The Populate Anything Field IDs with a user source.
	 *
	 * @return array Updated field filters.
	 */
	private static function prepend_pa_current_user_options( array $field_filters, array $pa_user_field_ids ): array {
		foreach ( $field_filters as $i => $filter ) {
			if ( ! in_array( $filter['key'], $pa_user_field_ids, false ) ) {
				continue;
			}

			if ( ! isset( $filter['values'] ) ) {
				$filter['values'] = [];
			}

			array_unshift(
				$filter['values'],
				[
					'value' => '{user:ID}',
					'text'  => esc_html__( 'Currently Logged-in User', 'gravityview-advanced-filter' ),
				],
				[
					'value' => '{user:ID:disabled_admin}',
					'text'  => esc_html__(
						'Currently Logged-in User (Disabled for Administrators)',
						'gravityview-advanced-filter'
					),
				]
			);

			$field_filters[ $i ] = $filter;
		}

		return $field_filters;
	}

	/**
	 * Ensures the form ID is set for all filters.
	 *
	 * @since 2.7.0
	 *
	 * @param array $field_filters The field filters.
	 *
	 * @param int   $form_id       The form ID.
	 *
	 * @return array The field filters with the Form ID.
	 */
	private static function ensure_form_id( array $field_filters, int $form_id ): array {
		return array_map(
			static function ( $filter ) use ( $form_id ) {
				if ( ! isset( $filter['form_id'] ) ) {
					$filter['form_id'] = $form_id;
				}

				if ( isset( $filter['filters'] ) && is_array( $filter['filters'] ) ) {
					$filter['filters'] = self::ensure_form_id( $filter['filters'], $form_id );
				}

				return $filter;
			},
			$field_filters
		);
	}

	/**
	 * (Recursively) Replaces special characters on filter values.
	 *
	 * @since 2.8.0
	 *
	 * @param array $field_filters The field filters.
	 *
	 * @return array The updated filters.
	 */
	private static function fix_special_characters( array $field_filters ): array {
		foreach ( $field_filters as &$field_filter ) {
			// HTML-decode values returned by GFCommon::get_field_filter_settings().
			if ( ! empty( $field_filter['values'] ) && is_array( $field_filter['values'] ) ) {
				foreach ( $field_filter['values'] as $x => $choice ) {
					foreach ( [ 'value', 'text', 'label' ] as $key ) {
						if ( ! isset( $choice[ $key ] ) ) {
							continue;
						}

						$field_filter['values'][ $x ][ $key ] = wp_specialchars_decode( $choice[ $key ], ENT_QUOTES );
					}
				}
			}

			// Recursively fix this for repeater fields.
			if ( isset( $field_filter['filters'] ) && is_array( $field_filter['filters'] ) ) {
				$field_filter['filters'] = self::fix_special_characters( $field_filter['filters'] );
			}
		}
		// Clean up.
		unset( $field_filter );

		return $field_filters;
	}


	/**
	 * Adds current user's role filter.
	 *
	 * @since 2.1.0
	 *
	 * @param array $field_filters The current filters.
	 *
	 * @return array The new filters.
	 */
	private static function add_current_user_roles_filter( array $field_filters ): array {
		$field_filters[] = [
			'key'       => 'current_user_role',
			'text'      => __( 'Current User Role', 'gravityview-advanced-filter' ),
			'operators' => [ 'has_any', 'has_all' ],
			'values'    => self::get_user_role_choices( true ),
		];

		return $field_filters;
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_user_ids_by_any_role( array $roles ): array {
		if ( $roles === [] ) {
			return [];
		}

		return get_users( [ 'role__in' => $roles, 'fields' => 'ID' ] );
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_current_user(): WP_User {
		return wp_get_current_user();
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_form_by_view( View $view ): array {
		return $view->form->form ?? [];
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_form( $form_id = null ): array {
		if ( $form_id ) {
			if ( ! isset( $this->forms[ $form_id ] ) ) {
				$this->forms[ $form_id ] = GFAPI::get_form( (int) $form_id ) ?: [];
			}

			return $this->forms[ $form_id ];
		}

		if ( ! class_exists( GravityView_View::class ) ) {
			return [];
		}

		$form = GravityView_View::getInstance()->getForm() ?: [];
		if ( isset( $form['id'] ) ) {
			// Cache form for follow up queries.
			$this->forms[ $form['id'] ] = $form;
		}

		return $form;
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_field( int $form_id, $field_id ): ?GF_Field {
		$form = $this->get_form( $form_id );

		return GFFormsModel::get_field( $form, $field_id );
	}

	/**
	 * Returns an array of relative date choices for date fields.
	 *
	 * @since 2.6.0
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return array<int, array{text: string, value: string, operators?: string[]}> An array of relative date choices.
	 */
	private static function get_relative_date_choices( int $form_id ): array {
		$start_of_week = (int) get_option( 'start_of_week', 1 ); // 0=Sunday .. 6=Saturday.
		$week_labels   = [
			__( 'Sunday', 'gravityview-advanced-filter' ),
			__( 'Monday', 'gravityview-advanced-filter' ),
			__( 'Tuesday', 'gravityview-advanced-filter' ),
			__( 'Wednesday', 'gravityview-advanced-filter' ),
			__( 'Thursday', 'gravityview-advanced-filter' ),
			__( 'Friday', 'gravityview-advanced-filter' ),
			__( 'Saturday', 'gravityview-advanced-filter' ),
		];

		$week_keywords       = [ 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ];
		$start_of_week_name  = $week_keywords[ $start_of_week ];
		$start_of_week_label = $week_labels[ $start_of_week ];

		$this_week_value = sprintf(
			$start_of_week === 1
				? '%s this week'
				: 'last %s',
			$start_of_week_name
		);

		$this_week_label = sprintf(
			esc_html__( 'This week (starting %s)', 'gravityview-advanced-filter' ),
			$start_of_week_label
		);

		$choices = [
			[
				'text'  => esc_html__( 'Today', 'gravityview-advanced-filter' ),
				'value' => 'today',
			],
			[
				'text'  => esc_html__( 'Yesterday', 'gravityview-advanced-filter' ),
				'value' => 'yesterday',
			],
			[
				'text'  => esc_html__( 'Tomorrow', 'gravityview-advanced-filter' ),
				'value' => 'tomorrow',
			],
			[
				'text'      => esc_html__( 'Past year', 'gravityview-advanced-filter' ),
				'value'     => '-1 year',
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'Past month', 'gravityview-advanced-filter' ),
				'value'     => '-1 month',
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'Past week', 'gravityview-advanced-filter' ),
				'value'     => '-1 week',
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => $this_week_label,
				'value'     => $this_week_value,
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'This month', 'gravityview-advanced-filter' ),
				'value'     => 'first day of this month',
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'This year', 'gravityview-advanced-filter' ),
				'value'     => 'first day of january this year',
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'Next week (start)', 'gravityview-advanced-filter' ),
				// Respect the site's week start.
				'value'     => sprintf( '%s next week', $start_of_week_name ),
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'Next month (start)', 'gravityview-advanced-filter' ),
				'value'     => 'first day of next month',
				'operators' => [ '>', '<' ],
			],
			[
				'text'      => esc_html__( 'Next year (start)', 'gravityview-advanced-filter' ),
				'value'     => 'first day of january next year',
				'operators' => [ '>', '<' ],
			],

		];

		/**
		 * Modify available relative date choices for date fields.
		 *
		 * If the `operators` key is empty for a choice it will be applied to all operators.
		 *
		 * @since  2.6.0
		 *
		 * @param array<int, array{text: string, value: string, operators?: string[]}> $choices Array of relative date choices.
		 *
		 */
		return apply_filters( 'gk/query-filters/relative-date-choices', $choices, $form_id );
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 * @return array{key: string, text: string, operators: string[], preventMultiple: bool, values: array, cssClass: string }[] The field filter options.
	 */
	public function get_field_filters( int $form_id ): array {
		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			return [];
		}

		// Adding default pre render hook for plugins to update the fields before choice retrieval.
		$form = gf_apply_filters( [ 'gform_pre_render', $form_id ], $form, false, [] );

		$populate_anything = null;
		// Remove conditional logic filter from populate anything, to allow prefilling of the choices.
		if ( function_exists( 'gp_populate_anything' ) ) {
			$populate_anything = gp_populate_anything();
			remove_filter( 'gform_field_filters', [ $populate_anything, 'conditional_logic_field_filters' ] );
		}

		$field_filters   = GFCommon::get_field_filter_settings( $form );

		$field_filters[] = [
			'key'       => 'created_by_user_role',
			'text'      => esc_html__( 'Created By User Role', 'gravityview-advanced-filter' ),
			'operators' => [ 'is', 'isnot' ],
			'values'    => self::get_user_role_choices(),
		];

		$field_keys = wp_list_pluck( $field_filters, 'key' );

		if ( ! in_array( 'date_updated', $field_keys, true ) ) {
			$field_filters[] = [
				'key'       => 'date_updated',
				'text'      => esc_html__( 'Date Updated', 'gravityview-advanced-filter' ),
				'operators' => [ 'is', '>', '<' ],
				'cssClass'  => 'datepicker ymd_dash',
			];
		}

		$approved_column = null;
		if (
			class_exists( GravityView_Entry_Approval::class )
			&& ( $approved_column = GravityView_Entry_Approval::get_approved_column( $form ) )
		) {
			$approved_column = (int) floor( $approved_column );
		}

		$option_fields_ids = $product_fields_ids = $category_field_ids = $boolean_field_ids = $post_category_choices = $gppa_user_field_ids = [];

		/**
		 * @since 2.0.0
		 */
		if (
			$boolean_fields = GFAPI::get_fields_by_type(
				$form,
				[
					'post_category',
					'checkbox',
					'radio',
					'select',
					'multiselect',
				],
				true
			)
		) {
			$boolean_field_ids = wp_list_pluck( $boolean_fields, 'id' );
		}

		/**
		 * Get an array of field IDs that are Post Category fields
		 *
		 * @since 2.0.0
		 */
		if ( $category_fields = GFAPI::get_fields_by_type( $form, [ 'post_category' ] ) ) {
			$category_field_ids = wp_list_pluck( $category_fields, 'id' );

			/**
			 * @since 1.0.12
			 */
			$post_category_choices = $this->get_term_choices();
		}

		// 1.0.14
		if ( $option_fields = GFAPI::get_fields_by_type( $form, [ 'option' ] ) ) {
			$option_fields_ids = wp_list_pluck( $option_fields, 'id' );
		}

		// 1.0.14
		if ( $product_fields = GFAPI::get_fields_by_type( $form, [ 'product' ] ) ) {
			$product_fields_ids = wp_list_pluck( $product_fields, 'id' );
		}

		// Check for Populate Anything field IDs.
		if ( null !== $populate_anything ) {
			$gppa_user_field_ids = $this->get_populate_anything_user_fields( $form['fields'] ?? [] );
		}

		// Add currently logged-in user option.
		foreach ( $field_filters as &$filter ) {
			// Add negative match to approval column.
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
				$filter['operators'][] = 'isempty';
				$filter['operators'][] = 'isnotempty';
			}

			/**
			 * GF stores the option values in DB as "label|price" (without currency symbol)
			 * This is a temporary fix until the filter is proper built by GF
			 *
			 * @since 1.0.14
			 */
			if (
				in_array( $filter['key'], $option_fields_ids )
				&& ! empty( $filter['values'] )
				&& is_array( $filter['values'] )
			) {
				require_once( GFCommon::get_base_path() . '/currency.php' );
				foreach ( $filter['values'] as $i => $value ) {
					$filter['values'][ $i ] = $value['text'] . '|' . GFCommon::to_number( $value['price'] );
				}
			}

			/**
			 * When saving the filters, GF is changing the operator to 'contains'
			 *
			 * @since 1.0.14
			 * @see   GFCommon::get_field_filters_from_post
			 */
			if ( in_array( $filter['key'], $product_fields_ids, false ) ) {
				$filter['operators'] = [ 'contains' ];
			}

			// Gravity Forms already creates a "User" option.
			// We don't care about specific user, just the logged in status.
			if ( 'created_by' === $filter['key'] ) {
				// Update the default label to be more descriptive
				$filter['text'] = esc_attr__( 'Created By', 'gravityview-advanced-filter' );

				$current_user_filters = [
					[
						'text'  => __( 'Currently Logged-in User (Disabled for Administrators)', 'gravityview-advanced-filter' ),
						'value' => 'created_by_or_admin',
					],
					[
						'text'  => __( 'Currently Logged-in User', 'gravityview-advanced-filter' ),
						'value' => 'created_by',
					],
				];

				foreach ( $current_user_filters as $user_filter ) {
					// Add to the beginning on the value options
					array_unshift( $filter['values'], $user_filter );
				}

				$filter['operators'][] = 'isempty';
				$filter['operators'][] = 'isnotempty';
			}

			// Process filter operators (and nested filters recursively).
			$filter = self::process_filter_operators( $filter );

			// Add relative date choices to date fields.
			if ( isset( $filter['cssClass'] ) && strpos( $filter['cssClass'], 'datepicker' ) !== false ) {
				$filter['values'] = self::get_relative_date_choices( $form_id );
			}

			// Filter out duplicate operators.
			if ( isset( $filter['operators'] ) ) {
				$filter['operators'] = array_values( array_unique( $filter['operators'] ) );
			}
		}
		unset( $filter );

		$field_filters = self::add_approval_status_filter( $field_filters );
		$field_filters = self::add_current_user_roles_filter( $field_filters );
		if ( null !== $populate_anything && ! empty( $gppa_user_field_ids ) ) {
			$field_filters = self::prepend_pa_current_user_options( $field_filters, $gppa_user_field_ids );
		}

		usort( $field_filters, function ( $a, $b ) {
			return strcmp( $a['text'], $b['text'] );
		} );

		/**
		 * Modify available field filters.
		 *
		 * @since  2.0.0
		 *
		 * @param int   $form_id       The form ID.
		 *
		 * @param array $field_filters configured filters
		 */
		$field_filters = (array) apply_filters( 'gk/query-filters/field-filters', $field_filters, $form_id );

		$field_filters = self::fix_special_characters( $field_filters );
		return self::ensure_form_id( $field_filters, $form_id );
	}

	/**
	 * Get user role choices formatted in a way used by GravityView and Gravity Forms input choices
	 *
	 * @since 2.0.0
	 *
	 * @param bool $exclude_any_role Whether to exclude the "any role" option.
	 *
	 * @return array Multidimensional array with `text` (Role Name) and `value` (Role ID) keys.
	 */
	private static function get_user_role_choices( bool $exclude_any_role = false ): array {
		$user_role_choices = [];
		$editable_roles    = get_editable_roles();

		if ( ! $exclude_any_role ) {
			$editable_roles['current_user'] = [
				'name' => esc_html__( 'Any Role of Current User', 'gravityview-advanced-filter' ),
			];
		}

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
	 * Processes filter operators and recursively handles nested filters.
	 *
	 * Adds proxy operators to a filter (if applicable) and recursively processes
	 * any nested filters for Repeater fields and their sub-fields.
	 *
	 * @since 2.8.0
	 *
	 * @param array $filter Filter configuration with potential nesting.
	 *
	 * @return array The processed filter with updated operators at all nesting levels.
	 */
	private static function process_filter_operators( array $filter ): array {
		// Recursively process nested filters.
		if ( ! empty( $filter['filters'] ) && is_array( $filter['filters'] ) ) {
			foreach ( $filter['filters'] as $i => $sub_filter ) {
				$filter['filters'][ $i ] = self::process_filter_operators( $sub_filter );
			}
		}

		/**
		 * Add extra operators for all fields except:
		 * 1) those with predefined values
		 * 2) Entry ID (it always exists)
		 * 3) "any form field" ("is empty" does not work: https://github.com/gravityview/Advanced-Filter/issues/91)
		 */
		if (
			isset( $filter['operators'] )
			&& ! isset( $filter['values'] )
			&& ! in_array( $filter['key'], [ 'entry_id', '0' ], false )
		) {
			$filter['operators'] = self::add_proxy_operators( $filter['operators'], $filter['key'] );
		}

		return $filter;
	}

	/**
	 * When "is" and "is not" are combined with an empty value, they become "is empty" and "is not empty",
	 * respectively.
	 *
	 * Let's add these 2 proxy operators for a better UX. Exclusions: Entry ID and fields with predefined values (e.g.,
	 * Payment Status).
	 *
	 * @since 2.0.0
	 *
	 * @param string $filter_key The filter key.
	 *
	 * @param array  $operators  The operators.
	 */
	private static function add_proxy_operators( array $operators, string $filter_key ): array {
		if ( 'date_created' === $filter_key ) {
			return $operators;
		}

		if ( in_array( 'is', $operators, true ) ) {
			$operators[] = 'isempty';
		}

		if ( 'date_updated' === $filter_key || in_array( 'isnot', $operators, true ) ) {
			$operators[] = 'isnotempty';
		}

		// Add "does not contain" operator when "contains" is present.
		if ( in_array( 'contains', $operators, true ) && ! in_array( 'ncontains', $operators, true ) ) {
			$offset = array_search( 'contains', $operators, true );
			// Insert "does not contain" operator directly after "contains" operator.
			array_splice( $operators, $offset + 1, 0, [ 'ncontains' ] );
		}

		return $operators;
	}

	/**
	 * Add Entry Approval Status filter option
	 *
	 * @since 1.4
	 *
	 * @param array $filters The filters to update.
	 *
	 * @return array The updated filters.
	 */
	private static function add_approval_status_filter( array $filters ): array {
		if ( ! class_exists( GravityView_Entry_Approval_Status::class ) ) {
			return $filters;
		}

		$approval_choices = GravityView_Entry_Approval_Status::get_all();
		$approval_values  = [];

		foreach ( $approval_choices as $choice ) {
			$approval_values[] = [
				'text'  => $choice['label'],
				'value' => $choice['value'],
			];
		}

		$filters[] = [
			'text'      => __( 'Entry Approval Status', 'gravityview-advanced-filter' ),
			'key'       => 'is_approved',
			'operators' => [ 'is', 'isnot' ],
			'values'    => $approval_values,
		];

		return $filters;
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function is_user_admin( ?WP_User $user = null, array $form = [] ): bool {
		if ( ! $user ) {
			$user = $this->get_current_user();
		}

		if ( ! $form ) {
			$form = $this->get_form();
		}

		/**
		 * Customise the capabilities that define an Administrator able to view entries in frontend when filtered by "Created By".
		 *
		 * @since  1.0
		 *
		 * @param array $form         GF form.
		 *
		 * @param array $capabilities List of admin capabilities.
		 */
		$view_all_entries_caps = apply_filters(
			'gk/query-filters/admin-capabilities',
			[
				'manage_options',
				'gravityforms_view_entries',
			],
			$form
		);

		foreach ( $view_all_entries_caps as $cap ) {
			if ( user_can( $user, $cap ) ) {
				// Stop checking at first successful response.
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns fields that have Populate Anything enabled with user IDs.
	 *
	 * @since 2.7.0
	 *
	 * @param array $fields The form fields.
	 *
	 * @return array<string|int> The field IDs.
	 */
	private function get_populate_anything_user_fields( array $fields ): array {
		$gppa_user_field_ids = [];
		foreach ( $fields as $field ) {
			// Only Populate Anything enabled fields.
			if (
				empty( $field->{'gppa-choices-enabled'} )
				&& empty( $field->{'gppa-values-enabled'} )
			) {
				continue;
			}

			if (
				// Only user sources.
				'user' !== ( $field->{'gppa-choices-object-type'} ?? null )
				// Only values stored as user ID.
				|| 'ID' !== ( $field->{'gppa-choices-templates'}['value'] ?? null )
			) {
				continue;
			}

			$gppa_user_field_ids[] = $field->id;
		}

		return $gppa_user_field_ids;
	}

	/**
	 * Get categories formatted in a way used by GravityView and Gravity Forms input choices
	 *
	 * @since 2.7.1
	 *
	 * @param array $args Arguments array as used by the get_terms() function.
	 *
	 * @return array{text:string, value:string}[] The choices.
	 */
	private function get_term_choices( array $args = [] ): array {
		if ( function_exists( 'gravityview_get_terms_choices' ) ) {
			return gravityview_get_terms_choices( $args );
		}

		$defaults = [
			'type'         => 'post',
			'child_of'     => 0,
			'number'       => 1000, // Set a reasonable max limit
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 0,
			'hierarchical' => 1,
			'taxonomy'     => 'category',
			'fields'       => 'id=>name',
		];

		$args  = wp_parse_args( $args, $defaults );
		$terms = get_terms( $args['taxonomy'], $args );

		$choices = [];

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term_id => $term_name ) {
				$choices[] = [
					'text'  => $term_name,
					'value' => $term_id,
				];
			}
		}

		return $choices;
	}
}
