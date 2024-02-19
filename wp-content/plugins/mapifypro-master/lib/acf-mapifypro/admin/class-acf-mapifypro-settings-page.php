<?php

/**
 * The class responsible for creating and customizing MapifyPro settings page.
 * 
 * @since    1.0.0
 */

/**
 * class Acf_Mapifypro_Settings_Page
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Settings_Page {

	/**
	 * ACF field key
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	public $acf_field_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->acf_field_key = 'mapify_acf_field_607567ff4331b';
	}

	/**
	 * WordPress init hook
	 * 
	 * @since    1.0.0
	 */
	public function init() {
		if( ! function_exists( 'acf_add_options_sub_page' ) ) return;
		
		// Multi Map
		acf_add_options_sub_page( array(
			'page_title'  => __( 'Multi Map', 'acf-mapifypro' ),
			'menu_title'  => mpfy_get_icon( 'multi-map' ) . __( 'Multi Map', 'acf-mapifypro' ),
			'parent_slug' => 'mapify.php',
			'menu_slug'   => 'mapifypro-multi-map',
			'capability'  => 'edit_others_posts',
		) );
		
		// MapifyPro Settings
		acf_add_options_sub_page( array(
			'page_title'  => __( 'MapifyPro Settings', 'acf-mapifypro' ),
			'menu_title'  => mpfy_get_icon( 'settings' ) . __( 'Settings', 'acf-mapifypro' ),
			'parent_slug' => 'mapify.php',
			'menu_slug'   => 'mapifypro-settings',
			'capability'  => 'edit_others_posts',
		) );
	}

	/**
	 * ACF filter acf/load_value
	 * 
	 * @since    1.0.0
	 * @param    mixed    $field_value    The field value.
	 * @param    int      $post_id        The post ID where the value is saved.
	 * @param    array    $field          The field array containing all settings.
	 * @return   mixed
	 */
	public function acf_load_value( $field_value, $post_id, $field ) {
		return get_option( 'mpfy_load_sharethis', '' );
	}

	/**
	 * ACF action acf/save_post
	 * This hook's priority has been set to 5 (< 10) to run before ACF saves the value.
	 * 
	 * @since    1.0.0
	 */
	public function acf_update_value() {
		$screen = get_current_screen();

		// must be from the options page
		if ( ! isset( $screen->id ) || 'mapifypro_page_mapifypro-settings' !== $screen->id ) return;

		// must have the acf field data on the post data
		if ( ! isset( $_POST['acf'][ $this->acf_field_key ] ) ) return;

		// get option data
		$mpfy_load_sharethis = sanitize_text_field( $_POST['acf'][ $this->acf_field_key ] );

		// save option
		update_option( 'mpfy_load_sharethis', $mpfy_load_sharethis );
	}

}