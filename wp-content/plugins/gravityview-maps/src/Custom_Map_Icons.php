<?php

namespace GravityKit\GravityMaps;

use \RGFormsModel;

/**
 * Class Custom_Map_Icons
 *
 * @since   1.9.0
 *
 * @package GravityKit\GravityMaps
 */
class Custom_Map_Icons extends Component {

	/**
	 * Determines if we are in script debug mode.
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	protected function is_script_debug() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
	}

	/**
	 * Returns TRUE if the current page is the form editor page. Otherwise, returns FALSE
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function is_form_editor() {
		if ( rgget( 'page' ) == 'gf_edit_forms' && ! rgempty( 'id', $_GET ) && rgempty( 'view', $_GET ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Loads the component
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function load() {
		if ( is_admin() ) {
			add_action( 'gform_field_standard_settings', [ $this, 'icon_option_field_setting' ], 10, 2 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			add_action( 'gform_noconflict_scripts', [ $this, 'register_noconflict_scripts' ], 10, 1 );
			add_filter( 'gform_tooltips', [ $this, 'add_tooltips' ] );
		}
	}

	/**
	 * Register the required non-conflict scripts on Gravity Forms.
	 *
	 * @since 1.9.0
	 *
	 * @param array $scripts
	 *
	 * @return array
	 */
	public function register_noconflict_scripts( $scripts ) {
		return array_merge( $scripts, [ 'media-audiovideo', 'gravitykit_maps_admin_map_icons' ] );
	}

	/**
	 * Register and enqueue al the required scripts for Gravity Maps custom map icons.
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$script_debug       = $this->is_script_debug() ? '' : '.min';
		$plugin_assert_path = plugins_url( 'assets/', $this->loader->path );

		// Register script for all.
		wp_register_script(
			'gravitykit_maps_admin_map_icons',
			$plugin_assert_path . '/js/admin-map-icons' . $script_debug . '.js',
			[ 'jquery' ],
			$this->loader->plugin_version
		);
		wp_register_style(
			'gravitykit_maps_admin_map_icons_styles',
			$plugin_assert_path . '/css/admin-map-icons.css',
			[],
			$this->loader->plugin_version
		);

		if ( ! $this->is_form_editor() ) {
			return;
		}

		wp_enqueue_media();// For Media Library

		wp_enqueue_script( 'gravitykit_maps_admin_map_icons' );

		wp_localize_script( 'gravitykit_maps_admin_map_icons', 'gravitykit_maps_admin_map_icons_data', [
			'uploadIcon' => esc_html__( 'Upload icon', 'gk-gravitymaps' ),
			'removeIcon' => esc_html__( 'Remove this icon', 'gk-gravitymaps' ),
			'useIcon'    => esc_html__( 'Use this icon', 'gk-gravitymaps' ),
		] );

		wp_enqueue_style( 'gravitykit_maps_admin_map_icons_styles' );
	}

	/**
	 * Insert the "Use field choices as map marker images in GravityView" checkbox into this field's settings.
	 *
	 * @since 1.9
	 *
	 * @param int $position
	 * @param int|string $form_id
	 *
	 * @return void
	 */
	public function icon_option_field_setting( $position, $form_id ) {
		$pos = 1350;
		if ( $position !== $pos ) {
			return;
		}

		?>
        <li class="gk-gravitymaps-choice-marker-icons-field-setting field_setting">
            <input type="checkbox" id="gk_gravitymaps_choice_marker_icons_enabled" class="gk_gravitymaps_choice_marker_icons_enabled" />
            <label for="gk_gravitymaps_choice_marker_icons_enabled"><?php
                esc_html_e( 'Use field choices as map marker icons', 'gk-gravitymaps' );
                gform_tooltip( 'gk_gravitymaps_choice_marker_icons' );
            ?></label>
        </li>
		<?php
	}

	/**
     * Add tooltips for the custom map icons field setting.
     *
     * @since 1.9
     *
	 * @param array $tooltips Existing array of Gravity Forms tooltips.
	 *
	 * @return array Tooltips, with `gk_gravitymaps_choice_marker_icons` added.
	 */
	public function add_tooltips( $tooltips ) {

		$tooltip = '<h6>' . esc_html__( 'Choice-Based Marker Icon', 'gravityforms', 'gk-gravitymaps' ) . '</h6>';
		$tooltip .= '<p>' . esc_html__( 'Enabling this setting will allow you to assign an image to each choice in the field. These images will be used as markers on the map, overriding the Default Marker Icon setting for the View.', 'gk-gravitymaps' ) . '</p>';
		$tooltip .= '<p><a href="https://docs.gravitykit.com/article/889-a" rel="external noopener noreferrer" target="_blank">' . esc_html__( 'Learn more about choice-based marker icons.', 'gk-gravitymaps' ) . '<span class="screen-reader-text"> ' . esc_html__( '(This link opens in a new window.)', 'gk-gravitymaps' ) . '</span></a></p>';

		$tooltips['gk_gravitymaps_choice_marker_icons'] = $tooltip;

		return $tooltips;
	}

	/**
	 * Get the option fields.
	 *
	 * @since 1.9
	 *
	 * @param \GravityView_View $view
	 *
	 * @return \GF_Field|null
	 */
	public static function get_icon_options_field( $view ) {

		// View Map settings
		$ms = Admin::get_map_settings( $view->getViewId() );

        $icon_field = \GV\Utils::get( $ms, 'choice_marker_icon_field/0' );

		if ( empty( $icon_field ) ) {
            return null;
		}

		$field = gravityview_get_field( $view->getForm(), $icon_field );

		if ( empty( $field ) ) {
            return null;
		}

		if ( ! $field->gk_custom_map_icon_enabled ) {
            return null;
		}

        return $field;
	}

	/**
	 * Given a field and entry get which was the selected choice from the Dropdown.
	 *
	 * @since 1.9
	 *
	 * @param $field
	 * @param $entry
	 *
	 * @return mixed|null
	 */
	public static function get_selected_choice( $field, $entry ) {

		$choice = null;

		if ( ! isset( $field ) || empty( $field ) || ! isset( $entry ) || empty( $entry ) ) {
			return $choice;
		}

		$value      = rgar( $entry, $field->id );
		$real_value = RGFormsModel::get_lead_field_value( $entry, $field );

		foreach ( $field->choices as $this_choice ) {
			if ( $this_choice['value'] !== $real_value ) {
				continue;
			}
			$choice = $this_choice;
		}

		return $choice;

	}

	/**
     * Returns an Icon object given the field and entry.
     *
     * @since 1.9
     *
	 * @param \GF_Field $field Field used to get the choices.
	 * @param array $entry Gravity Forms entry.
	 *
	 * @return Icon
	 */
    public static function get_selected_icon( $field, $entry ) {

	    $icon_settings = array(
		    'url'        => static::get_selected_icon_url( $field, $entry ),
		    'size'       => null,
		    'origin'     => null,
		    'anchor'     => null,
		    'scaledSize' => null,
	    );

	    /**
	     * Filter the icon settings for a given entry.
	     *
	     * @since 1.9
	     *
	     * @param \GF_Field $field Field used to get the choices.
	     * @param array $entry Gravity Forms entry.
	     */
	    $icon_settings = apply_filters( 'gravityview/maps/choice_marker_icon_settings', $icon_settings, $field, $entry );

	    return new Icon(
		    \GV\Utils::get( $icon_settings, 'url' ),
            \GV\Utils::get( $icon_settings, 'size' ),
            \GV\Utils::get( $icon_settings, 'origin' ),
            \GV\Utils::get( $icon_settings, 'anchor' ),
            \GV\Utils::get( $icon_settings, 'scaledSize' )
        );
    }

	/**
	 * Given a Field and Entry determine the Icon URL used.
	 *
	 * @since 1.9
	 *
	 * @param \GF_Field $field
	 * @param array $entry
	 * @param string|array|false $size If false, use the URL of the image. If string or array, pass the $size to {@see wp_get_attachment_image_src}.
	 *
	 * @return string
	 */
	public static function get_selected_icon_url( $field, $entry, $size = false ) {

		$icon = '';

		if ( empty( $field ) || empty( $entry ) ) {
			return $icon;
		}

		if( empty( $field->gk_custom_map_icon_enabled ) ) {
            return $icon;
		}

		$choice = static::get_selected_choice( $field, $entry );

		if ( ! $size ) {
			// no size passed in, get the stored url
			$icon = ( $choice != null && isset( $choice['gk_custom_map_icon'] ) ) ? $choice['gk_custom_map_icon'] : '';
		} else {
			// get the requested size url
			$icon_id    = ( $choice != null && isset( $choice['gk_custom_map_icon_id'] ) ) ? $choice['gk_custom_map_icon_id'] : '';
			$attachment = ( ! empty( $icon_id ) ) ? wp_get_attachment_image_src( $icon_id, $size ) : '';
			if ( ! empty( $attachment ) ) {
				$icon = $attachment[0];
			}
			// fallback to the stored url
			if ( empty( $icon ) && $choice != null && isset( $choice['gk_custom_map_icon'] ) ) {
				$icon = $choice['gk_custom_map_icon'];
			}
		}

		return $icon;
	}

}
