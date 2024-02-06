<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
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
 * @since 2.0.0
 */
final class DefaultRepository implements FormRepository, UserRepository {
	/**
	 * Micro cache for forms.
	 * @since 2.0.0
	 * @var array<int, array>
	 */
	private $forms = [];

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
	 * @inheritDoc
	 * @return array{key: string, text: string, operators: string[], preventMultiple: bool, values: array, cssClass: string }[] The field filter options.
	 * @since 2.0.0
	 */
	public function get_field_filters( int $form_id ): array {
		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			return [];
		}

		// Adding default pre render hook for plugins to update the fields before choice retrieval.
		$form = gf_apply_filters( [ 'gform_pre_render', $form_id ], $form, false, [] );

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
			$approved_column = intval( floor( $approved_column ) );
		}

		$option_fields_ids = $product_fields_ids = $category_field_ids = $boolean_field_ids = $post_category_choices = [];

		/**
		 * @since 2.0.0
		 */
		if ( $boolean_fields = GFAPI::get_fields_by_type( $form, [
			'post_category',
			'checkbox',
			'radio',
			'select'
		] ) ) {
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
			$post_category_choices = gravityview_get_terms_choices();
		}

		// 1.0.14
		if ( $option_fields = GFAPI::get_fields_by_type( $form, [ 'option' ] ) ) {
			$option_fields_ids = wp_list_pluck( $option_fields, 'id' );
		}

		// 1.0.14
		if ( $product_fields = GFAPI::get_fields_by_type( $form, [ 'product' ] ) ) {
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
			}

			if ( ! empty( $filter['filters'] ) ) {
				foreach ( $filter['filters'] as $i => $data ) {
					$filter['filters'][ $i ]['operators'] = self::add_proxy_operators( $data['operators'], $filter['key'] );
				}
			}

			/**
			 * Add extra operators for all fields except:
			 * 1) those with predefined values
			 * 2) Entry ID (it always exists)
			 * 3) "any form field" ("is empty" does not work: https://github.com/gravityview/Advanced-Filter/issues/91)
			 */
			if ( isset( $filter['operators'] ) && ! isset( $filter['values'] ) && ! in_array( $filter['key'], [
					'entry_id',
					'0'
				] ) ) {
				$filter['operators'] = self::add_proxy_operators( $filter['operators'], $filter['key'] );
			}
		}

		$field_filters = self::add_approval_status_filter( $field_filters );

		usort( $field_filters, function ( $a, $b ) {
			return strcmp( $a['text'], $b['text'] );
		} );

		/**
		 * @filter `gk/query-filters/field-filters` Modify available field filters.
		 *
		 * @param array $field_filters configured filters
		 * @param int $form_id The form ID.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'gk/query-filters/field-filters', $field_filters, $form_id );
	}

	/**
	 * Get user role choices formatted in a way used by GravityView and Gravity Forms input choices
	 *
	 * @return array Multidimensional array with `text` (Role Name) and `value` (Role ID) keys.
	 * @since 2.0.0
	 */
	private function get_user_role_choices(): array {
		$user_role_choices              = [];
		$editable_roles                 = get_editable_roles();
		$editable_roles['current_user'] = [
			'name' => esc_html__( 'Any Role of Current User', 'gravityview-advanced-filter' ),
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
	 * When "is" and "is not" are combined with an empty value, they become "is empty" and "is not empty", respectively.
	 *
	 * Let's add these 2 proxy operators for a better UX. Exclusions: Entry ID and fields with predefined values (e.g., Payment Status).
	 *
	 * @param array $operators The operators.
	 * @param string $filter_key The filter key.
	 *
	 * @since 2.0.0
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

		return $operators;
	}

	/**
	 * Add Entry Approval Status filter option
	 *
	 * @return array
	 * @since 1.4
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
		 * @filter `gk/query-filters/admin-capabilities` Customise the capabilities that define an Administrator able to view entries in frontend when filtered by "Created By".
		 *
		 * @param array $capabilities List of admin capabilities.
		 * @param array $form GF form.
		 *
		 * @since  1.0
		 */
		$view_all_entries_caps = apply_filters(
			'gk/query-filters/admin-capabilities',
			[
				'manage_options',
				'gravityforms_view_entries'
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
}
