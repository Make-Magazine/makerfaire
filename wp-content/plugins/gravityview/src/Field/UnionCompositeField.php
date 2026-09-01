<?php
/**
 * A composite field whose inputs are sourced from a unioned form.
 *
 * @package GravityKit\GravityView\Field
 * @since   3.2.0
 */

namespace GravityKit\GravityView\Field;

/**
 * Renders a composite field for a unioned form that maps its inputs individually.
 *
 * A union maps a Name or Address field one input at a time, so a slot holding the whole field
 * has no counterpart of its own. This keeps the primary form's field for display and rebuilds
 * the input-keyed value from whichever inputs the union maps; unmapped inputs stay absent.
 *
 * @since 3.2.0
 */
final class UnionCompositeField extends FieldGravityForms {
	/**
	 * The unioned form's field for each of the primary field's inputs, keyed by input ID.
	 *
	 * @since 3.2.0
	 *
	 * @var Field[]
	 */
	private $inputs = [];

	/**
	 * Creates a composite field that reads its inputs from a unioned form.
	 *
	 * @since 3.2.0
	 *
	 * @param FieldGravityForms $field   The primary form's composite field.
	 * @param int|string        $form_id The unioned form ID.
	 * @param Field[]           $inputs  The unioned form's field per primary input ID.
	 *
	 * @return self
	 */
	public static function from_inputs( FieldGravityForms $field, $form_id, array $inputs ): self {
		$composite = new self();

		$composite->ID      = $field->ID;
		$composite->field   = $field->field;
		$composite->form_id = $form_id;
		$composite->inputs  = $inputs;

		return $composite;
	}

	/**
	 * Whether any of the primary field's inputs has a counterpart on the unioned form.
	 *
	 * @since 3.2.0
	 *
	 * @return bool
	 */
	public function has_inputs(): bool {
		return (bool) $this->inputs;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 3.2.0
	 *
	 * @return array The value per input ID of the primary field.
	 */
	public function get_value(
		?\GV\View $view = null,
		?\GV\Source $source = null,
		?\GV\Entry $entry = null,
		?\GV\Request $request = null
	) {
		$value = [];

		foreach ( $this->inputs as $input_id => $field ) {
			$value[ $input_id ] = $this->single_input_value( $field, $view, $source, $entry, $request );
		}

		return $this->get_value_filters( $value, $view, $source, $entry, $request );
	}

	/**
	 * Returns one input's value from a field that may itself address a whole composite field.
	 *
	 * A field ID naming a single input resolves to its parent field, whose value covers every
	 * input, so the requested input is picked back out of it.
	 *
	 * @since 3.2.0
	 *
	 * @param Field            $field   The unioned form's field for one input.
	 * @param \GV\View|null    $view    The view for this context.
	 * @param \GV\Source|null  $source  The source for this context.
	 * @param \GV\Entry|null   $entry   The entry for this context.
	 * @param \GV\Request|null $request The request for this context.
	 *
	 * @return mixed The input's value.
	 */
	private function single_input_value(
		Field $field,
		?\GV\View $view,
		?\GV\Source $source,
		?\GV\Entry $entry,
		?\GV\Request $request
	) {
		$value = $field->get_value( $view, $source, $entry, $request );

		return is_array( $value ) ? ( $value[ $field->ID ] ?? '' ) : $value;
	}
}
