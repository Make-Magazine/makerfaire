<?php

namespace GravityKit\GravityImport;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add "Import Entries" links
 */
class GF_Entries_Screen {

	/**
	 * WP_Import_Screen constructor.
	 */
	public function __construct() {
		add_filter( 'gform_toolbar_menu', array( $this, 'add_sub_menu_link' ), 10, 2 );
		add_filter( 'gform_form_actions', array( $this, 'add_sub_menu_link' ), 10, 2 );
	}

	/**
	 * Adds Import Entries submenu link to both the Toolbar and Forms screen "Settings" menu dropdown
	 *
	 * @param array $menu_items   The array of links / tabs
	 * @param int   $form_id      The ID of the form being accessed.
	 */
	function add_sub_menu_link( $menu_items, $form_id = 0 ) {

		if ( empty( $form_id ) ) {
			return $menu_items;
		}

		if( ! isset( $menu_items['entries']['sub_menu_items'] ) ) {

			$entries_menu_item = rgar( $menu_items, 'entries' ); // Copy the settings from the "Entries" menu item

			// We're in trashed forms, which don't show entries
			if( ! $entries_menu_item ) {
				return $menu_items;
			}

			unset( $entries_menu_item['sub_menu_items'] );

			$menu_items['entries']['sub_menu_items'] = array( $entries_menu_item );
		}

		$menu_items['entries']['sub_menu_items'][] = array(
			'url'          => admin_url( "admin.php?page=gv-admin-import-entries#targetForm={$form_id}" ),
			'label'        => esc_html__( 'Import Entries', 'gk-gravityimport' ),
			'capabilities' => array( 'edit_posts' )
		);
		$menu_items['entries']['sub_menu_items'][] = array(
			'url'          => admin_url( "admin.php?page=gf_export&view=export_entry&id={$form_id}" ),
			'label'        => esc_html__( 'Export Entries', 'gk-gravityimport' ),
			'capabilities' => array( 'gravityforms_export_entries' )
		);

		return $menu_items;
	}
}
