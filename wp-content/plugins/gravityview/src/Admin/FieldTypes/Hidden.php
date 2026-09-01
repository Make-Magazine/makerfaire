<?php
/**
 * Hidden input type for admin rendering.
 *
 * PSR-4 migration of the legacy GravityView_FieldType_hidden class.
 *
 * @package GravityKit\GravityView\Admin\FieldTypes
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin\FieldTypes;

class Hidden extends \GravityView_FieldType {

	function render_option() {
		$this->render_input();
	}

	function render_input( $override_input = null ) {

		if ( isset( $override_input ) ) {
			echo $override_input;
			return;
		}

		$class = ! empty( $this->field['class'] ) ? $this->field['class'] : 'widefat';

		?>
		<input name="<?php echo esc_attr( $this->name ); ?>" id="<?php echo $this->get_field_id(); ?>" type="hidden" value="<?php echo esc_attr( $this->value ); ?>" class="<?php echo esc_attr( $class ); ?>" />
		<?php
	}
}
