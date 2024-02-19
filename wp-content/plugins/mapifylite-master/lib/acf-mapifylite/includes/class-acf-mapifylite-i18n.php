<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://mapifypro.com/
 * @since      1.0.0
 *
 * @package    Acf_Mapifylite
 * @subpackage Acf_Mapifylite/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Acf_Mapifylite
 * @subpackage Acf_Mapifylite/includes
 * @author     Haris Ainur Rozak <harisrozak@gmail.com>
 */
class Acf_Mapifylite_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'acf-mapifylite',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
