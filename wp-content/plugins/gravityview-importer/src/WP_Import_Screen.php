<?php

namespace GravityKit\GravityImport;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add "Gravity Forms Entries" link to WordPress Importer screen
 * @since 1.1.4
 */
class WP_Import_Screen {

	/**
	 * @var string Key used to identify the importer in the Importer list, used for $_GET['import'] value
	 */
	private $_slug = 'gk-gravityimport';

	/**
	 * WP_Import_Screen constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_importer' ) );
		add_action( 'current_screen', array( $this, 'redirect' ) );
	}

	/**
	 * If on the Importer screen, register an importer to Gravity Forms Entries importer appears where expected.
	 * @since 1.1.4
	 * @return void
	 */
	function register_importer() {

		// Only load on the Importer page to save resources
		if( ! defined('WP_LOAD_IMPORTERS') ) { return; }

		register_importer( $this->_slug, __('Gravity Forms Entries', 'gk-gravityimport' ), __('Import entries into Gravity Forms, by GravityView.', 'gk-gravityimport'), NULL );
	}

	/**
	 * If trying to access the importer from the WP Importer screen, redirect to Gravity Forms screen
	 * @since 1.1.4
	 * @return void
	 */
	function redirect() {
		global $pagenow;

		if( 'admin.php' === $pagenow && isset( $_GET['import'] ) && $this->_slug === $_GET['import'] ) {
			wp_safe_redirect( admin_url( 'admin.php?page=gf_export&view=import_entries' ) );
			exit;
		}
	}
}
