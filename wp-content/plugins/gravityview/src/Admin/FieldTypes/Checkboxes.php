<?php
/**
 * Checkboxes input type for admin rendering.
 *
 * PSR-4 migration of the legacy GravityView_FieldType_checkboxes class.
 *
 * @package GravityKit\GravityView\Admin\FieldTypes
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin\FieldTypes;

class Checkboxes extends \GravityView_FieldType {

	function render_option() {
		?>
		<fieldset class="<?php echo $this->get_label_class(); ?>">
			<legend><span class="gv-label"><?php echo $this->get_field_label(); ?></span></legend>
		<?php

			echo $this->get_tooltip() . $this->get_field_desc();

			$this->render_input();

		?>
		</fieldset>
		<?php
	}

	function render_input( $override_input = null ) {
		if ( isset( $override_input ) ) {
			echo $override_input;
			return;
		}

		?>
		<ul class="gv-setting-list">
		<?php
		foreach ( $this->field['options'] as $value => $label ) {
			?>
			<li
			<?php
			if ( isset( $label['requires'] ) ) {
				printf( 'class="gv-sub-setting" data-requires="%s"', $label['requires'] ); }
			?>
			>
				<label>
				<input name="<?php printf( '%s[%s]', esc_attr( $this->name ), esc_attr( $value ) ); ?>" type="hidden"
						value="0"/>
				<input name="<?php printf( '%s[%s]', esc_attr( $this->name ), esc_attr( $value ) ); ?>"
						id="<?php echo $this->get_field_id(); ?>" type="checkbox"
						value="1" <?php checked( ! empty( $this->value[ $value ] ) ); ?> />
				<?php echo esc_html( $label['label'] ); ?>
				</label>
				<?php
				if ( ! empty( $label['desc'] ) ) {
					printf( '<span class="howto">%s</span>', $label['desc'] );
				}
				?>
			</li>
			<?php
		}

		if ( ! empty( $this->field['after'] ) && is_callable( $this->field['after'] ) ) {
			call_user_func( $this->field['after'], $this );
		}
		?>
		</ul>
		<?php
	}
}
