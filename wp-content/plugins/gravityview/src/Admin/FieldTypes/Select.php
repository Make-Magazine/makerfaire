<?php
/**
 * Select input type for admin rendering.
 *
 * PSR-4 migration of the legacy GravityView_FieldType_select class.
 *
 * @package GravityKit\GravityView\Admin\FieldTypes
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin\FieldTypes;

class Select extends \GravityView_FieldType {
	function render_setting( $override_input = null ) {
		if ( ! empty( $this->field['full_width'] ) ) { ?>
			<th scope="row" colspan="2">
				<div>
					<?php $this->render_option(); ?>
				</div>
			</th>
		<?php } else {
			parent::render_setting( $override_input );
		}
	}

	function render_option() {
		?>
		<label for="<?php echo $this->get_field_id(); ?>" class="<?php echo $this->get_label_class(); ?>">
								<?php

								echo $this->get_field_label() . $this->get_tooltip() . $this->get_field_desc();

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
		<select name="<?php echo esc_attr( $this->name ); ?>" class="<?php echo \GV\Utils::get( $this->field, 'class', '' ); ?>" id="<?php echo $this->get_field_id(); ?>" <?php echo $this->get_data_attributes(); ?>>
			<?php foreach ( $this->field['options'] as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $this->value, true ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
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
