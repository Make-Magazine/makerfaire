<?php
/**
 * Radio input type for admin rendering.
 *
 * PSR-4 migration of the legacy GravityView_FieldType_radio class.
 *
 * @package GravityKit\GravityView\Admin\FieldTypes
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin\FieldTypes;

class Radio extends \GravityView_FieldType {

	function render_option() {
		?>
		<div class="gv-label">
		<?php
			echo $this->get_field_label();
		?>
		</div>
		<?php
			echo $this->get_tooltip();
			echo $this->get_field_desc() . ' ';

			$this->render_input();
	}

	function render_input( $override_input = null ) {
		if ( isset( $override_input ) ) {
			echo $override_input;
			return;
		}

		$hidden_options = array_map( 'strval', (array) ( $this->field['hidden_options'] ?? [] ) );

		if ( in_array( (string) $this->value, $hidden_options, true ) ) :
			?>
			<input name="<?php echo esc_attr( $this->name ); ?>" type="hidden" value="<?php echo esc_attr( $this->value ); ?>" />
			<?php
		endif;

		foreach ( $this->field['options'] as $value => $label ) :
			if ( in_array( (string) $value, $hidden_options, true ) ) {
				continue;
			}
			?>
		<label class="<?php echo $this->get_label_class(); ?>">
			<input name="<?php echo esc_attr( $this->name ); ?>" id="<?php echo $this->get_field_id(); ?>-<?php echo esc_attr( $value ); ?>" type="radio" value="<?php echo esc_attr( $value ); ?>" <?php checked( $value, $this->value, true ); ?> />&nbsp;<?php echo esc_html( $label ); ?>
		</label>
			<?php
		endforeach;
	}
}
