<?php
if ( ! function_exists( 'gppa_is_assoc_array' ) ) {
	function gppa_is_assoc_array( array $array ) {
		return ( array_values( $array ) !== $array );
	}
}

if ( ! function_exists( 'gppa_is_acf_field' ) ) {
	function gppa_is_acf_field( $field_key ) {
		if ( $field_key && str_starts_with( $field_key, 'field_' ) ) {
			return true;
		}

		return false;
	}
}
