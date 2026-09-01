<?php
/**
 * The default phone field output template.
 *
 * @global \GV\Template_Context $gravityview
 * @since 2.0
 */

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', array( 'file' => __FILE__ ) );
	return;
}

$value          = $gravityview->value;
$field_settings = $gravityview->field->as_configuration();

$link_number = $value;

// The International (formatted) phone format (Gravity Forms 3.0+) stores JSON; display the formatted number and link its E.164 form.
$decoded = is_string( $value ) ? json_decode( $value, true ) : null;

if ( is_array( $decoded ) ) {
	$value       = \GV\Utils::get( $decoded, 'formatted', \GV\Utils::get( $decoded, 'e164', '' ) );
	$link_number = \GV\Utils::get( $decoded, 'e164', '' );
}

$value = esc_attr( $value );

if ( ! empty( $field_settings['link_phone'] ) && ! empty( $value ) && ! empty( $link_number ) ) {
	echo gravityview_get_link( 'tel:' . esc_attr( $link_number ), $value );
} else {
	echo $value;
}
