<?php
/**
 * The internal Field class wrapper.
 *
 * Used for custom content fields, etc.
 *
 * @package GravityKit\GravityView\Field
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field;

use GravityView_Fields;

use function gravityview;

/**
 * The internal \GF_Field field object wrapper.
 *
 * Used for custom content fields, etc.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Field namespace.
 */
class InternalField extends Field {
	/**
	 * @var \GravityView_Field|false The backing GravityView field (old). False if none exists.
	 */
	public $field;

	/**
	 * The field type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Create self from a configuration array.
	 *
	 * @param array $configuration The configuration array.
	 * @see \GV\Field::as_configuration()
	 * @internal
	 * @since 2.0
	 *
	 * @return \GV\Internal_Field|null The field implementation or null on error.
	 */
	public static function from_configuration( $configuration ) {

		if ( empty( $configuration['id'] ) || ! is_string( $configuration['id'] ) ) {
			gravityview()->log->error(
				'Invalid configuration[id] supplied for internal field: {id}',
				[
					'data' => $configuration,
					'id'   => \GV\Utils::get( $configuration, 'id' ),
				]
			);
			return null;
		}

		$field = self::by_id( $configuration['id'] );

		$field->update_configuration( $configuration );

		return $field;
	}

	/**
	 * Get a \GV\GF_Field from an internal Gravity Forms field ID.
	 *
	 * @param int $field_id The internal Gravity Forms field ID.
	 *
	 * @return \GV\Internal_Field|null The requested field or null if not found.
	 */
	public static function by_id( $field_id ) {
		$field       = new self();
		$field->ID   = $field_id;
		$field->type = $field->ID;

		/**
		 * Retrieve the internal backing field (old for now)
		 *
		 * @todo switch to future subclasses
		 */
		$field->field = GravityView_Fields::get_instance( $field_id );

		return $field;
	}

	/**
	 * Retrieve the label for this field.
	 *
	 * @param \GV\View    $view The view for this context if applicable.
	 * @param \GV\Source  $source The source (form) for this context if applicable.
	 * @param \GV\Entry   $entry The entry for this context if applicable.
	 * @param \GV\Request $request The request for this context if applicable.
	 *
	 * @return string The label for this field.
	 */
	public function get_label( ?\GV\View $view = null, ?\GV\Source $source = null, ?\GV\Entry $entry = null, ?\GV\Request $request = null ) {

		if ( ! $this->show_label ) {
			return '';
		}

		if ( $label = parent::get_label( $view, $source, $entry, $request ) ) {
			return $label;
		}

		if ( $this->label ) {
			return $this->label;
		}

		return $this->field ? $this->field->label : '';
	}

	/**
	 * Retrieve the value for this field.
	 *
	 * Requires the \GV\Entry in this implementation.
	 *
	 * @param \GV\View    $view The view for this context if applicable.
	 * @param \GV\Source  $source The source (form) for this context if applicable.
	 * @param \GV\Entry   $entry The entry for this context if applicable.
	 * @param \GV\Request $request The request for this context if applicable.
	 *
	 * @return mixed The value for this field.
	 */
	public function get_value( ?\GV\View $view = null, ?\GV\Source $source = null, ?\GV\Entry $entry = null, ?\GV\Request $request = null ) {
		if ( ! $entry || ! is_a( $entry, '\GV\Entry' ) ) {
			gravityview()->log->error( '$entry is not a valid \GV\Entry instance' );
			return null;
		}

		/**
		 * @todo Implement in subclasses, once available.
		 *
		 * For example the "content" field will be empty here. It's
		 *  value is actually currently retrieved inside ...
		 *
		 * *drumroll*
		 *
		 * A TEMPLATE :)
		 */
		$value = \GV\Utils::get( $entry->as_entry(), $this->ID );

		/** Apply parent filters. */
		return $this->get_value_filters( $value, $view, $source, $entry, $request );
	}
}
