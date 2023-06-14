<?php
/**
 * The template part for GravityView Maps - Gravity Forms Map Icon picker field - frontend form input.
 *
 * @package GravityView_Maps
 * @since 0.1.1
 *
 * @global array $ms
 * @global string $address_fields_input
 */
?>

<?php

$id = intval( $this->id );
$field_id = 'input_' . $this->formId .'_'. $id;
$value = empty( $value ) ? $this->get_default_icon() : $value;
$alt = $value ? basename( $value ) : '';

$logic_event = '';
if( version_compare( GFForms::$version, '2.4', '<' ) ) {
	$logic_event = $this->get_conditional_logic_event( 'change' );
}

/**
 * @filter `gravityview/maps/field/icon_picker/button_text` Change the text of the button
 * @since 1.4
 * @param string $button_text Default: "Select Icon"
 */
$button_text = apply_filters( 'gravityview/maps/field/icon_picker/button_text', __( 'Select Icon', 'gk-gravitymaps' ) );

$tabindex = $this->get_tabindex();
?>

<div class="ginput_container">
	<div class="gvmaps-icon-picker-field">
		<img src="<?php echo esc_url( $value ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<input name="input_<?php echo $id; ?>" id="<?php echo $field_id; ?>" type="hidden" class="gvmaps-select-icon-input" value="<?php echo esc_url( $value ); ?>" <?php echo $tabindex . ' ' . $logic_event; ?>>
		<input type="button" value="<?php echo esc_attr( $button_text ); ?>" class="button gform_button gvmaps-select-icon-button">
	</div>

	<div class="gvmaps-available-icons">
		<?php do_action( 'gravityview/maps/render/available_icons', 'public-available-icons', $id ); ?>
	</div>
</div>