<?php

namespace GravityKit\GravityMaps;

use GFCommon;
use GFAPI;

/**
 * Fetches available map icons
 *
 * @since 1.0.0
 *
 */
class Available_Icons extends Component {
	/**
	 * Theme directory where Map Icons may exist
	 *
	 * @var string
	 */
	public $theme_dir = 'gravityview/mapicons';

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @return void
	 */
	public function load() {
		add_action( 'gravityview/maps/render/available_icons', array( $this, 'render_available_icons' ), 10, 2 );
	}

	/**
	 * Get the default map icon url
	 * @return string
	 */
	public function get_default_icon_url() {
		return $this->loader->url . 'assets/img/google-default.svg';
	}

	/**
	 * Get the URL of the icon that is selected for the current entry
	 *
	 * If entry has no value yet, use the default icon URL. Otherwise, get the existing value.
	 *
	 * @since 1.3
	 *
	 * @param int $field_id Key value of the entry that holds the existing icon
	 *
	 * @return string URL of icon, or default icon URL if not set or no entry found
	 */
	public function get_selected_icon_url( $field_id = 0 ) {
		$selected_icon = $this->get_default_icon_url();

		// If no field ID is set yet, use the default icon
		if ( ! empty( $field_id ) ) {

			$entry = false;

			// Are we on the Gravity Forms Edit Entry screen?
			if ( GFCommon::is_entry_detail_edit() ) {
				$entry_id = rgpost( 'entry_id' ) ? absint( rgpost( 'entry_id' ) ) : absint( rgget( 'lid' ) );

				if ( $entry_id ) {
					$entry = GFAPI::get_entry( $entry_id );
				}
			} // Or the GravityView Edit Entry screen?
			else if ( function_exists( 'gravityview_get_context' ) && 'edit' === gravityview_get_context() && class_exists( '\GravityView_Edit_Entry' ) ) {
				/**
				 * Get the entry from the Edit Entry class, otherwise, it doesn't update to reflect the current $_POST value
				 * Otherwise, we would have used `$entry = GravityView_frontend::getInstance()->getEntry();`
				 */
				$entry = \GravityView_Edit_Entry::getInstance()->instances['render']->entry;
			}

			if ( $entry && ! empty( $field_id ) && ! empty( $entry[ $field_id ] ) ) {
				$selected_icon = $entry[ $field_id ];
			}
		}

		return $selected_icon;
	}

	/**
	 * Render available icons html
	 *
	 * @param string $part     HTML template file
	 * @param int    $field_id If set, the key value of the $entry array that holds the icon
	 */
	public function render_available_icons( $part = 'available-icons', $field_id = 0 ) {
		if ( empty( $part ) ) {
			return;
		}

		// 1. prepare default google icon
		$icons['default'][]  = $this->get_default_icon_url();
		$sections['default'] = __( 'Default', 'gk-gravitymaps' );

		// 2. prepare theme map icons
		$icons['theme'] = $this->get_icons_list( get_stylesheet_directory() . '/' . $this->theme_dir, 'theme_url_transform' );

		if ( ! empty( $icons['theme'] ) ) {
			$sections['theme'] = __( 'Theme Map Icons', 'gk-gravitymaps' );
		}

		// 3. prepare default map icons (included in this extension)
		$icons['plugin']    = $this->get_icons_list( $this->loader->dir . 'assets/img/mapicons' );
		$sections['plugin'] = __( 'GravityView Map Icons', 'gk-gravitymaps' );

		// Pass the selected icon to the template
		$selected = $this->get_selected_icon_url( $field_id );

		/**
		 * @filter `gravityview/maps/available_icons/icons` Modify the icons available to the plugin
		 * @since  TODO
		 *
		 * @param int   $field_id If set, the key value of the $entry array that holds the icon
		 * @param array $icons    Array of icons, with keys of `default`, `theme`, and `plugin`.
		 */
		$sections = apply_filters( 'gravityview/maps/available_icons/sections', $sections, $field_id );

		/**
		 * @filter `gravityview/maps/available_icons/icons` Modify the icons available to the plugin
		 * @since  TODO
		 *
		 * @param int   $field_id If set, the key value of the $entry array that holds the icon
		 * @param array $icons    Array of icons, with keys of `default`, `theme`, and `plugin`. The keys must match the array keys used by $sections.
		 */
		$icons = apply_filters( 'gravityview/maps/available_icons/icons', $icons, $field_id );

		// render
		require( $this->loader->includes_dir . 'parts/' . $part . '.php' );
	}

	/**
	 * Get a list of the available map icons on a specific directory
	 *
	 * @param string $dir    Directory path
	 * @param string $filter Filter to replace php file paths by urls paths
	 * @param string $ext    Image file extension
	 *
	 * @return array
	 */
	public function get_icons_list( $dir = '', $filter = 'plugin_url_transform', $ext = '*.png' ) {
		if ( empty( $dir ) ) {
			return array();
		}

		$dir   = trailingslashit( $dir );
		$files = glob( $dir . $ext );

		if ( empty( $files ) ) {
			return array();
		}

		return array_map( array( $this, $filter ), $files );
	}

	/**
	 * Filter to replace php file paths by urls paths (Plugin domain)
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function plugin_url_transform( $value ) {
		return str_replace( $this->loader->dir, $this->loader->url, $value );
	}

	/**
	 * Filter to replace php file paths by urls paths (Theme's domain)
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function theme_url_transform( $value ) {
		return str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $value );
	}
}
