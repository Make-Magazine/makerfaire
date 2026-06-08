<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Query_Column;
use GF_Query_Condition;

/**
 * Represents a search condition that allows searching for user by name, instead of ID.
 *
 * @since 2.9.0
 */
class Created_By_Condition extends GF_Query_Condition {
	/**
	 * The value to search.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	protected $value;

	/**
	 * The form ID.
	 *
	 * @since 2.9.0
	 *
	 * @var int|null
	 */
	protected $form_id;

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.9.0
	 */
	public function __construct( string $value, ?int $form_id = null ) {
		$this->value   = $value;
		$this->form_id = $form_id;

		// This is required to register the form ID as an alias.
		parent::__construct( new GF_Query_Column( 'created_by', $form_id ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.9.0
	 */
	public function sql( $query ): string {
		global $wpdb;

		$user_meta_fields = $this->user_meta_fields();
		$user_fields      = $this->user_fields();

		$conditions = [];

		foreach ( $user_fields as $user_field ) {
			$user_field   = preg_replace( '/\W/', '', $user_field );
			$conditions[] = $wpdb->prepare( "`u`.`$user_field` LIKE %s", '%' . $wpdb->esc_like( $this->value ) . '%' );
		}

		foreach ( $user_meta_fields as $meta_field ) {
			$conditions[] = $wpdb->prepare(
				'(`um`.`meta_key` = %s AND `um`.`meta_value` LIKE %s)',
				$meta_field,
				'%' . $wpdb->esc_like( $this->value ) . '%'
			);
		}

		$conditions = '(' . implode( ' OR ', $conditions ) . ')';

		$alias = $query->_alias( null, $this->form_id );

		return "(EXISTS (SELECT 1 FROM $wpdb->users u LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id WHERE (u.ID = `$alias`.`created_by` AND $conditions)))";
	}

	/**
	 * Returns the user meta fields to search.
	 *
	 * @since 2.9.0
	 *
	 * @return array The user meta fields.
	 */
	protected function user_meta_fields(): array {
		$user_meta_fields = [
			'nickname',
			'first_name',
			'last_name',
		];

		$form_id = $this->form_id;

		/**
		 * Modifies the user meta fields to search in the created_by condition.
		 *
		 * @since 2.9.0
		 *
		 * @param array    $user_meta_fields The user meta fields.
		 * @param int|null $form_id          The form ID.
		 */
		return apply_filters(
			'gk/query-filters/condition/created-by/user-meta-fields',
			$user_meta_fields,
			$form_id,
		);
	}

	/**
	 * Returns the user fields to search.
	 *
	 * @since 2.9.0
	 *
	 * @return array The user fields.
	 */
	protected function user_fields(): array {
		$user_fields = [
			'user_nicename',
			'user_login',
			'display_name',
			'user_email',
		];

		$form_id = $this->form_id;

		/**
		 * Modifies the user fields to search in the created_by condition.
		 *
		 * @since  2.9.0
		 *
		 * @param array    $user_fields The user fields.
		 * @param int|null $form_id     The form ID.
		 */
		return apply_filters(
			'gk/query-filters/condition/created-by/user-fields',
			$user_fields,
			$form_id,
		);
	}
}
