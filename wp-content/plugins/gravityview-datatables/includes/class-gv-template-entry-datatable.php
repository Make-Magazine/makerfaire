<?php

namespace GV;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * The Entry DataTables Template class .
 *
 * Renders a \GV\Entry using a \GV\Entry_Renderer.
 */
class Entry_DataTable_Template extends Entry_Table_Template {

	/**
	 * @var string The template slug to be loaded (like "table", "list")
	 */
	public static $slug = 'datatable';

	/**
	 * @var string The template configuration slug
	 * Some templates share the same configuration layouts.
	 */
	public static $_configuration_slug = 'table';


	public function __construct( Entry $entry, View $view, Request $request = null ) {
		parent::__construct( $entry, $view, $request );

		$this->plugin_directory = GV_DT_DIR;
	}

}
