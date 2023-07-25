<?php

namespace GravityKit\MultipleForms\Models;

use GravityKit\MultipleForms\Form;
use WP_Error;

/**
 * Class Join.
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms\Models
 */
class Join {
	/**
	 * Stores the base form ID.
	 *
	 * @since 0.3
	 *
	 * @var int
	 */
	protected $base_form_id;

	/**
	 * Stores the Field ID for the Base Form.
	 *
	 * @since 0.3
	 *
	 * @var string
	 */
	protected $base_form_field_id;

	/**
	 * Stores the joined form ID.
	 *
	 * @since 0.3
	 *
	 * @var int
	 */
	protected $join_form_id;

	/**
	 * Stores the Field ID for the Joined Form.
	 *
	 * @since 0.3
	 *
	 * @var string
	 */
	protected $join_form_field_id;

	/**
	 * From a legacy array create a new instance of the Join model.
	 *
	 * @since 0.3
	 *
	 * @param array $join
	 *
	 * @return WP_Error|Join
	 */
	public static function from_legacy_array( array $join ) {
		$is_valid = static::is_valid_legacy_format( $join );
		if ( true !== $is_valid ) {
			return $is_valid;
		}

		$base_form_id       = (int) $join[0];
		$base_form_field_id = (string) $join[1];
		$join_form_id       = (int) $join[2];
		$join_form_field_id = (string) $join[3];

		return new static( $base_form_id, $base_form_field_id, $join_form_id, $join_form_field_id );
	}

	/**
	 * Returns this join in the legacy format.
	 *
	 * @since 0.3
	 *
	 * @return array
	 */
	public function to_legacy_format(): array {
		return [ $this->get_base_form_id(), $this->get_base_form_field_id(), $this->get_join_form_id(), $this->get_join_form_field_id() ];
	}

	/**
	 * Create a new instance of a join.
	 *
	 * @since 0.3
	 *
	 * @param int    $base_form_id
	 * @param string $base_form_field_id
	 * @param int    $join_form_id
	 * @param string $join_form_field_id
	 */
	public function __construct( int $base_form_id, string $base_form_field_id, int $join_form_id, string $join_form_field_id ) {
		$this->set_base_form_id( $base_form_id );
		$this->set_base_form_field_id( $base_form_field_id );
		$this->set_join_form_id( $join_form_id );
		$this->set_join_form_field_id( $join_form_field_id );
	}

	/**
	 * Sets the base form ID.
	 *
	 * @since 0.3
	 *
	 * @param int $value
	 *
	 * @return void
	 */
	public function set_base_form_id( int $value ): void {
		$this->base_form_id = $value;
	}

	/**
	 * Gets the Base form ID.
	 *
	 * @since 0.3
	 *
	 * @return int
	 */
	public function get_base_form_id(): int {
		return $this->base_form_id;
	}

	/**
	 * Sets the Field ID from the Base Form.
	 *
	 * @since 0.3
	 *
	 * @param string $value
	 *
	 * @return void
	 */
	public function set_base_form_field_id( string $value ): void {
		$this->base_form_field_id = $value;
	}

	/**
	 * Gets the Field ID from the Base Form.
	 *
	 * @since 0.3
	 *
	 * @return string
	 */
	public function get_base_form_field_id(): string {
		return $this->base_form_field_id;
	}

	/**
	 * Sets form ID for the joined form.
	 *
	 * @since 0.3
	 *
	 * @param int $value
	 *
	 * @return void
	 */
	public function set_join_form_id( int $value ): void {
		$this->join_form_id = $value;
	}

	/**
	 * Gets form ID for the joined form.
	 *
	 * @since 0.3
	 *
	 * @return int
	 */
	public function get_join_form_id(): int {
		return $this->join_form_id;
	}

	/**
	 * Sets the Field ID from the Joined Form.
	 *
	 * @since 0.3
	 *
	 * @param string $value
	 *
	 * @return void
	 */
	public function set_join_form_field_id( string $value ): void {
		$this->join_form_field_id = $value;
	}

	/**
	 * Gets the Field ID from the Joined Form.
	 *
	 * @since 0.3
	 *
	 * @return string
	 */
	public function get_join_form_field_id(): string {
		return $this->join_form_field_id;
	}

	/**
	 * Determines if a given value is a valid legacy join.
	 *
	 * @since 0.3
	 *
	 * @param mixed $join
	 *
	 * @return bool|WP_Error
	 */
	public static function is_valid_legacy_format( $join ) {
		if ( ! is_array( $join ) ) {
			return new WP_Error( 'gk/multiple-forms/models/join/invalid-join/not-array', 'Invalid Join', [ 'join' => $join ] );
		}

		if (
			empty( $join[0] )
			|| empty( $join[1] )
			|| empty( $join[2] )
			|| empty( $join[3] )
		) {
			return new WP_Error( 'gk/multiple-forms/models/join/invalid-join/missing-params', 'Invalid Join', [ 'join' => $join ] );
		}

		if ( ! is_numeric( $join[0] ) ) {
			return new WP_Error( 'gk/multiple-forms/models/join/invalid-join/base-form-id-not-numeric', 'Invalid Join', [ 'join' => $join ] );
		}

		if ( ! is_numeric( $join[2] ) ) {
			return new WP_Error( 'gk/multiple-forms/models/join/invalid-join/join-form-id-not-numeric', 'Invalid Join', [ 'join' => $join ] );
		}

		return true;
	}

	/**
	 * Gets the Form IDs for this join.
	 *
	 * @since 0.3
	 *
	 * @return array<int>
	 */
	public function get_form_ids(): array {
		return [ $this->get_base_form_id(), $this->get_join_form_id() ];
	}

	/**
	 * Gets the active form IDs for this join.
	 *
	 * @since 0.3
	 *
	 * @return array<int>
	 */
	public function get_active_form_ids(): array {
		return Form::get_active_form_ids( $this->get_form_ids() );
	}

	/**
	 * Determines if the Join has all forms active.
	 *
	 * @since 0.3
	 *
	 * @return bool
	 */
	public function has_active_forms(): bool {
		return count( $this->get_active_form_ids() ) === 2;
	}
}
