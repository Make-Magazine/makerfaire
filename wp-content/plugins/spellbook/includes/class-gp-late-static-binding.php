<?php
if (!defined('ABSPATH')) exit;

/**
 * Late static binding for dynamic function calls.
 *
 * Provides compatibility with PHP 7.2 (create_function deprecated) and 5.2.
 * So whenever the need for `create_function` arises, use this instead.
 */
class GP_Late_Static_Binding {

	private $args = array();

	public function __construct( $args = array() ) {
		$this->args = wp_parse_args( $args, array(
			'form_id' => 0,
			'message' => '',
			'class'   => '',
			'value'   => '',
		) );
	}

	public function GravityPerks_display_admin_message() {
		GravityPerks::display_admin_message( $this->args['message'], $this->args['class'] );
	}

	public function GravityPerks_maybe_display_admin_message() {

		$screen = get_current_screen();
		if ( $screen->id === 'dashboard' || GravityPerks::is_gravity_page() || GravityPerks::is_gravity_perks_page() || GravityPerks::is_plugins_page() ) {
			GravityPerks::display_admin_message( $this->args['message'], $this->args['class'] );
		}

	}

	public function GWAPI_dummy_func( $return ) {
		return $return;
	}

	public function Perk_array_push( $array ) {
		$array[] = $this->args['value'];
		return $array;
	}

	public function Perk_value_pass_through( $return ) {
		return $this->args['value'];
	}
}
