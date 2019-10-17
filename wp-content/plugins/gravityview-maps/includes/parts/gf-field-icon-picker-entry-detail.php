<?php
/**
 * The template part for GravityView Maps - Gravity Forms Map Icon picker field - GF Edit Entry screen.
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
$field_id = 'input_'. $id;

$logic_event = $this->get_conditional_logic_event( 'change' );

$tabindex = $this->get_tabindex();

?>

<div class="ginput_container">

	<div class="gvmaps-icon-picker-field">
		<img src="<?php echo esc_url( $value ); ?>" >
		<input name="input_<?php echo $id; ?>" id="<?php echo $field_id; ?>" type="hidden" class="gvmaps-select-icon-input" value="<?php echo esc_url( $value ); ?>" <?php echo $tabindex . ' ' . $logic_event; ?>>
		<input type="button" value="<?php esc_attr_e( 'Select Icon', 'gravityview-maps' ); ?>" class="button gform_button gvmaps-select-icon-button">
	</div>

	<div class="gvmaps-available-icons">
		<?php do_action( 'gravityview/maps/render/available_icons', 'public-available-icons', $id ); ?>
	</div>

</div>