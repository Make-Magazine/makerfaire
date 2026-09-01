<?php
/**
 * Multiselect input type for admin rendering.
 *
 * PSR-4 migration of the legacy GravityView_FieldType_multiselect class.
 *
 * @package GravityKit\GravityView\Admin\FieldTypes
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin\FieldTypes;

class Multiselect extends \GravityView_FieldType {

	function render_option() {
		?>
		<label for="<?php echo $this->get_field_id(); ?>" class="<?php echo $this->get_label_class(); ?>">
								<?php

								echo '<span class="gv-label">' . $this->get_field_label() . '</span>';

								echo $this->get_tooltip() . $this->get_field_desc();

								$this->render_input();

								?>
		</label>
		<?php
	}

	function render_input( $override_input = null ) {

		if ( isset( $override_input ) ) {
			echo $override_input;
			return;
		}

		?>
		<?php if ( ! empty( \GV\Utils::get( $this->field, 'submit_empty_value', false ) ) ) : ?>
			<input type="hidden" name="<?php echo esc_attr( $this->name ); ?>[]" value="">
		<?php endif; ?>
		<select
			name="<?php echo esc_attr( $this->name ); ?>[]"
			id="<?php echo $this->get_field_id(); ?>"
			class="<?php echo esc_attr( \GV\Utils::get( $this->field, 'class', '' ) ); ?>"
			data-placeholder="<?php echo esc_attr( \GV\Utils::get( $this->field, 'placeholder', '' ) ); ?>"
			<?php echo $this->get_data_attributes(); ?>
			multiple="multiple"
		>
			<?php
			$selected_values = (array) $this->value;
			$options         = $this->get_ordered_options( $selected_values );

			// PHP coerces numeric-string array keys to integers, so a saved value like "87"
			// must be compared as a string against the integer option key. Selected values
			// can arrive as a list of IDs or as an associative map of ID => truthy.
			$is_list         = array_values( $selected_values ) === $selected_values;
			$selected_lookup = $is_list ? array_map( 'strval', $selected_values ) : [];

			foreach ( $options as $value => $label ) :
				$is_selected = $is_list
					? in_array( (string) $value, $selected_lookup, true )
					: ! empty( $selected_values[ $value ] );
				?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $is_selected, true, true ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Returns options with selected numeric-array values first in saved order.
	 *
	 * @since 3.0.0
	 *
	 * @param array $selected_values Selected values.
	 *
	 * @return array
	 */
	private function get_ordered_options( array $selected_values ) {
		$options = (array) $this->field['options'];

		if ( array_values( $selected_values ) !== $selected_values ) {
			return $options;
		}

		$ordered = [];

		foreach ( $selected_values as $selected_value ) {
			if ( array_key_exists( $selected_value, $options ) ) {
				$ordered[ $selected_value ] = $options[ $selected_value ];
			}
		}

		return $ordered + array_diff_key( $options, $ordered );
	}

	/**
	 * Returns configured data attributes for the select element.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function get_data_attributes() {
		$data = \GV\Utils::get( $this->field, 'data', [] );

		if ( empty( $data ) || ! is_array( $data ) ) {
			return '';
		}

		$attributes = [];

		foreach ( $data as $key => $value ) {
			$key = sanitize_key( str_replace( '_', '-', (string) $key ) );

			if ( '' === $key ) {
				continue;
			}

			$attributes[] = sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
		}

		return implode( ' ', $attributes );
	}
}
