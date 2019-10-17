<?php
/**
 * GravityView Maps Extension - Admin class
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */

class GravityView_Maps_Admin extends GravityView_Maps_Component {


	function load() {

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

		// Save the form configuration. Run at 14 so that View metadata is already saved (at 10)
		add_action( 'save_post', array( $this, 'save_post' ), 14 );

		// @see \GravityView_View_Data::get_default_args
		add_filter( 'gravityview_default_args', array( $this, 'register_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'gravityview_noconflict_scripts', array( $this, 'register_no_conflict') );
		add_filter( 'gravityview_noconflict_styles', array( $this, 'register_no_conflict') );

		// ajax - populate address fields based on the selected form
		add_action( 'wp_ajax_gv_address_fields', array( $this, 'get_address_fields' ) );
	}


	/**
	 * Add GravityView Maps metabox
	 */
	function register_metabox() {

		$m = array(
			'id' => 'maps_settings',
			'title' => __( 'Maps', 'gravityview-maps' ),
			'callback' => array( $this, 'render_metabox' ),
			'icon-class' => 'dashicons-location-alt',
			'file' => '',
			'callback_args' => '',
			'screen' => 'gravityview',
			'context' => 'side',
			'priority' => 'default',
		);

		if( class_exists('GravityView_Metabox_Tab') ) {

			$metabox = new GravityView_Metabox_Tab( $m['id'], $m['title'], $m['file'], $m['icon-class'], $m['callback'], $m['callback_args'] );

			GravityView_Metabox_Tabs::add( $metabox );

		} else {

			add_meta_box( 'gravityview_'.$m['id'], $m['title'], $m['callback'], $m['screen'], $m['context'], $m['priority'] );

		}
	}

	/**
	 * Render html for metabox
	 *
	 * @access public
	 * @param object $post
	 * @return void
	 */
	function render_metabox( $post ) {
		global $ms, $address_fields_input;

		// Use nonce for verification
		wp_nonce_field( 'gravityview_maps_settings', 'gravityview_maps_settings_nonce' );

		// get current form id
		$curr_form = GVCommon::get_meta_form_id( $post->ID );

		// View Map settings
		$ms = self::get_map_settings( $post->ID );

		$address_fields_input = '<select name="gv_maps_settings[map_address_field]" id="gv_maps_se_map_address_field">'. $this->render_address_fields_options( $curr_form, $ms['map_address_field'] ).'</select>';

		// render
		require_once $this->loader->includes_dir . 'parts/admin-meta-box.php';

	}

	/**
	 * Get the normalised Map View settings
	 *
	 * @param $view_id string View ID
	 *
	 * @return array Map Settings (Normalised)
	 */
	public static function get_map_settings( $view_id, $mode = 'normalized' ) {
		// get View Map settings
		$settings = get_post_meta( $view_id, '_gravityview_maps_settings', true );

		if( 'normalized' !== $mode ) {
			return $settings;
		}

		$defaults = GravityView_View_Data::get_default_args( false, 'maps' );

		return wp_parse_args( $settings, $defaults );
	}




	/**
	 * Render HTML options tags for the dropdown Address Field
	 *
	 * @param string $form_id Current assigned Form ID
	 * @param string $current Current saved setting value
	 *
	 * @return string HTML markup
	 */
	public function render_address_fields_options( $form_id = '', $current = '' ) {

		$none_option = '<option value="" selected="selected">'. esc_html__( 'None', 'gravityview-maps') .'</option>';
		if( empty( $form_id ) ) {

			$output = $none_option;

		} else {

			// Get fields with sub-inputs and no parent
			$fields = GVCommon::get_form_fields( $form_id, false, true );

			$output = '';
			if( !empty( $fields ) ) {

				foreach( $fields as $id => $field ) {

					if( !in_array( $field['type'], array( 'address' ) ) ) { continue; }

					// Select default address if ID matches or if this is the first address field.
					$selected = ( (int)$id === (int)$current || ( empty( $current ) && $output === '' ) );

					$output .= '<option value="'. $id .'" '. selected( true, $selected, false ).'>'. esc_attr( $field['label'] ) .'</option>';
				}
			}

			if( empty( $output ) ) {
				$output = $none_option;
			}

		}

		return $output;
	}


	/**
	 * Add the extension View settings
	 *
	 * @param array $settings global View settings
	 *
	 * @return array $settings
	 */
	function register_settings( $settings = array() ) {

		$settings['map_address_field'] = array(
			'label' => __( 'Address Field', 'gravityview-maps' ),
			'type' => 'select',
			'value' => '',
			'options' => array(
				'' => __( 'None', 'gravityview-maps' ),
			),
			'tooltip' => '',
			'group' => 'maps'
		);


		$settings['map_type'] = array(
			'label' => __( 'Map Type', 'gravityview-maps' ),
			'type' => 'select',
			'value' => 'roadmap',
			'options' => array(
				'roadmap' => __( 'Street', 'gravityview-maps' ),
				'satellite' => __( 'Satellite', 'gravityview-maps' ),
				'hybrid' => __( 'Hybrid', 'gravityview-maps' ),
				'terrain' => __( 'Terrain', 'gravityview-maps' ),
			),
			'tooltip' => __( 'Hybrid: This map type displays a transparent layer of major streets on satellite images. Roadmap: This map type displays a normal street map. Satellite: This map type displays satellite images. Terrain: This map type displays maps with physical features such as terrain and vegetation.', 'gravityview-maps' ),
			'group' => 'maps'
		);

		/**
		 * @since 1.1
		 */
		$settings['map_layers'] = array(
			'label' => __( 'Map Layers', 'gravityview-maps' ),
			'type' => 'radio',
			'value' => '0',
			'options' => array(
				'0' => __( 'None', 'gravityview-maps' ),
				'traffic' => __( 'Traffic', 'gravityview-maps' ),
				'transit' => __( 'Transit', 'gravityview-maps' ),
				'bicycling' => __( 'Bicycle', 'gravityview-maps' ),
			),
			'group' => 'maps',
			'tooltip' => __( 'The Traffic, Transit and Bicycling layers modify the base map layer to display current traffic conditions, or local Transit and Bicycling route information. These layers are available in select regions.', 'gravityview-maps' ),
		);

		/**
		 * @since 1.3
		 */
		$settings['map_zoom'] = array(
			'type' => 'select',
			'label' => __('Default Zoom', 'gravityview-maps'),
			'desc' => __('The default zoom for a single-marker map.', 'gravityview-maps'),
			'tooltip' => __('Higher numbers are zoomed more.', 'gravityview-maps') . __( 'Maps with multiple markers will zoom out to fit all markers.', 'gravityview-maps' ),
			'value' => 15,
			'group' => 'maps',
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16',
				'17' => '17',
				'18' => '18',
			)
		);

		/**
		 * @since 1.1
		 */
		$settings['map_minzoom'] = array(
			'type' => 'select',
			'label' => __('Minimum Zoom', 'gravityview-maps'),
			'desc' => __('The farthest out a map can zoom.', 'gravityview-maps'),
			'tooltip' => __('Higher numbers are zoomed more.', 'gravityview-maps'). ' '. sprintf( _x('If "%s", the %s zoom from the current map type will be used.', 'This is to reduce translation strings. The replacement words are "No Minimum/No Maximum" and "minimum/maximum"', 'gravityview-maps'), __('No Minimum', 'gravityview-maps'), __('minimum', 'gravityview-maps') ),
			'value' => 3,
			'group' => 'maps',
			'options' => array(
				'0' => __('No Minimum', 'gravityview-maps'),
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16',
				'17' => '17',
				'18' => '18',
			)
		);

		/**
		 * @since 1.1
		 */
		$settings['map_maxzoom'] = array(
			'type' => 'select',
			'label' => __('Maximum Zoom', 'gravityview-maps'),
			'desc' => __('The maximum zoom level which will be displayed on the map.', 'gravityview-maps'),
			'tooltip' => __('Higher numbers are zoomed more.', 'gravityview-maps'). ' '. sprintf( __('If "%s", the %s zoom from the current map type will be used.', 'This is to reduce translation strings. The replacement words are "No Minimum/No Maximum" and "minimum/maximum"', 'gravityview-maps'), __('No Maximum', 'gravityview-maps'), __('maximum', 'gravityview-maps') ),
			'options' => array(
				'0' => __('No Maximum', 'gravityview-maps'),
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16',
				'17' => '17',
				'18' => '18',
			),
			'value' => 16,
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_draggable'] = array(
			'label' => __('Allow the map to be dragged', 'gravityview-maps'),
			'type' => 'checkbox',
			'left_label' => __('Draggable Map', 'gravityview-maps'),
			'value' => 1,
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_doubleclick_zoom'] = array(
			'label' => __('Allow double-clicking on the map to zoom and center', 'gravityview-maps'),
			'type' => 'checkbox',
			'left_label' => __('Double-click Zoom', 'gravityview-maps'),
			'value' => 1,
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_scrollwheel_zoom'] = array(
			'label' => __('Allow scrolling to zoom in and out on the map', 'gravityview-maps'),
			'type' => 'checkbox',
			'left_label' => __('Scroll to Zoom', 'gravityview-maps'),
			'value' => 1,
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_pan_control'] = array(
			'label' => __('Display the "Pan" control that allows moving the map by clicking arrows', 'gravityview-maps'),
			'type' => 'checkbox',
			'left_label' => __('Pan Control', 'gravityview-maps'),
			'value' => 1,
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_zoom_control'] = array(
			'type' => 'select',
			'label' => __('Zoom Control', 'gravityview-maps'),
			'desc' => __('Display the zoom control that allows zooming in and out of the map and displays the current', 'gravityview-maps'),
			'options' => array(
				'none' => __('None (Don\'t display)', 'gravityview-maps'),
				'small' => __('Small (Small buttons)', 'gravityview-maps'),
				'default' => __('Default (Let map decide)', 'gravityview-maps'),
			),
			'tooltip' => '<p>' . __('None: Don\'t display the zoom control.', 'gravityview-maps') . '</p>' .
			             '<p>' . __('Small: A small control with buttons to zoom in and out.', 'gravityview-maps')  . '</p>' .
			             '<p>' . __('Default: The default zoom control varies according to map size and other factors.', 'gravityview-maps') . '</p>',
			'value' => 'default',
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_streetview_control'] = array(
			'label' => __('Display the Street View "Pegman" control', 'gravityview-maps'),
			'type' => 'checkbox',
			'left_label' => __('Street View', 'gravityview-maps'),
			'value' => 1,
			'group' => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_styles'] = array(
			'label' => __( 'Custom Map Styles', 'gravityview-maps' ),
			'desc' => __( 'Set map styles that override the default styling. See SnazzyMaps.com for styles or use your own.', 'gravityview-maps' ),
			'type' => 'textarea',
			'value' => '',
			'group' => 'maps',
		);

		$settings['map_marker_icon'] = array(
			'label' => __( 'Pin Icon', 'gravityview-maps' ),
			'type' => 'hidden',
			'value' => $this->loader->component_instances['available-icons']->get_default_icon_url(),
			'group' => 'maps'
		);

		$settings['map_canvas_position'] = array(
			'label' => __( 'Map Position', 'gravityview-maps' ),
			'type' => 'radio',
			'value' => 'top',
			'options' => array(
				'top' => __( 'Top', 'gravityview-maps' ),
				'right' => __( 'Right', 'gravityview-maps' ),
				'bottom' => __( 'Bottom', 'gravityview-maps' ),
				'left' => __( 'Left', 'gravityview-maps' ),
			),
			'group' => 'maps',
			'show_in_template' => array(
				'map',
				'preset_business_map'
			),
		);

		$settings['map_canvas_sticky'] = array(
			'label' => __( 'Keep the map fixed during page scroll', 'gravityview-maps' ),
			'left_label' => __( 'Pinned Map', 'gravityview-maps' ),
			'type' => 'checkbox',
			'value' => 1,
			'group' => 'maps',
			'show_in_template' => array(
				'map',
				'preset_business_map'
			),
		);

		/**
		 * Infowindows
		 * @since 1.4
		 */
		$settings['map_info_enable'] = array(
			'label' => __('Show a popup box with additional entry details when clicking a map marker', 'gravityview-maps'),
			'type' => 'checkbox',
			'left_label' => __( 'Show Info Boxes', 'gravityview-maps' ),
			'value' => 0,
			'group' => 'maps',
		);

		$settings['map_info_title'] = array(
			'label' => __( 'Title', 'gravityview-maps' ),
			'desc' => __( 'This also serves as a link to the single entry page.', 'gravityview-maps' ),
			'type' => 'text',
			'value' => '',
			'merge_tags' => 'force',
			'show_all_fields' => false, // Show the `{all_fields}` and `{pricing_fields}` merge tags
			'group' => 'maps'
		);

		$settings['map_info_content'] = array(
			'label' => __( 'Content', 'gravityview-maps' ),
			'type' => 'textarea',
			'value' => '',
			'merge_tags' => 'force',
			'show_all_fields' => true, // Show the `{all_fields}` and `{pricing_fields}` merge tags
			'group' => 'maps',
			'class' => 'code'
		);

		$settings['map_info_image'] = array(
			'label' => __( 'Image URL', 'gravityview-maps' ),
			'desc' => esc_html__( 'Insert a Merge Tag for a File Upload field, or a URL to an image. This will be used as the source of a HTML <img> tag.', 'gravityview-maps' ),
			'type' => 'text',
			'value' => '',
			'merge_tags' => 'force',
			'group' => 'maps'
		);

		$settings['map_info_image_align'] = array(
			'label' => __( 'Image Alignment', 'gravityview-maps' ),
			'type' => 'radio',
			'value' => 0,
			'merge_tags' => false,
			'group' => 'maps',
			'options' => array(
				'0' => __( 'Top', 'gravityview-maps' ),
				'left' => __( 'Left', 'gravityview-maps' ),
				'right' => __( 'Right', 'gravityview-maps' ),
			),
		);



		return $settings;
	}


	/**
	 * Save settings
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	function save_post( $post_id ) {

		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		}

		// validate post_type
		if ( ! isset( $_POST['post_type'] ) || 'gravityview' != $_POST['post_type'] ) {
			return;
		}

		// validate user can edit and save post/page
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		// nonce verification
		if ( isset( $_POST['gravityview_maps_settings_nonce'] ) && wp_verify_nonce( $_POST['gravityview_maps_settings_nonce'], 'gravityview_maps_settings' ) ) {

			if( empty( $_POST['gv_maps_settings'] ) ) {
				$_POST['gv_maps_settings'] = array();
			}

			if( $this->has_maps( $post_id ) ) {
				$_POST['gv_maps_settings']['map_exists'] = true;
			}

			update_post_meta( $post_id, '_gravityview_maps_settings', $_POST['gv_maps_settings'] );
		}


	}

	/**
	 * Checks if the current View is a Map template or has any map object configured so that we could speed up decisions on the frontend
	 *
	 * @param $view_id
	 *
	 * @return bool
	 */
	public function has_maps( $view_id ) {
		if( empty( $view_id ) ) {
			return false;
		}

		if( 'map' == get_post_meta( $view_id, '_gravityview_directory_template', true ) ) {
			return true;
		}

		$widgets = get_post_meta( $view_id, '_gravityview_directory_widgets', true );
		if( !empty( $widgets ) && $this->has_map_object( $widgets, 'map' ) ) {
			return true;
		}

		$fields = get_post_meta( $view_id, '_gravityview_directory_fields', true );
		if( !empty( $fields ) && $this->has_map_object( $fields, 'entry_map' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Helper function to search for the map object in the fields or widgets associative array
	 * @param array $objects Associative array $fields or $widgets
	 * @param string $field_id the name of the map field id
	 *
	 * @return bool
	 */
	public function has_map_object( $objects, $field_id ) {

		if ( ! is_array( $objects ) ) {
			return false;
		}

		foreach( $objects as $areas ) {

			if( ! is_array( $areas ) ) {
				continue;
			}

			foreach( $areas as $object ) {
				if( $field_id === $object['id'] ) {
					return true;
				}
			}
		}

		return false;
	}




	/**
	 * Ajax
	 * Given a form ID returns the form fields (only the address fields )
	 * @access public
	 * @return void
	 */
	function get_address_fields() {

		// Not properly formatted request
		if ( empty( $_POST['formid'] ) || !is_numeric( $_POST['formid'] ) ) {
			exit( false );
		}

		// Not valid request
		if( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_maps_admin' ) ) {
			exit( false );
		}

		$form_id = (int)$_POST['formid'];

		// Generate the output `<option>`s
		$response = $this->render_address_fields_options( $form_id );

		exit( $response );
	}





	/**
	 * Add script to Views edit screen (admin)
	 * @param  mixed $hook
	 */
	public function enqueue_scripts( $hook ) {
		// Don't process any scripts below here if it's not a GravityView page.
		if( !gravityview_is_admin_page( $hook ) ) { return; }

		// inject the media scripts and styles for handling custom map icons
		wp_enqueue_media();

		wp_enqueue_style( 'gravityview_maps_admin_css', plugins_url( 'assets/css/admin.css', $this->loader->_path ), array(), $this->loader->plugin_version );

		$script_debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_enqueue_script( 'gravityview_maps_admin', plugins_url( 'assets/js/admin'.$script_debug.'.js', $this->loader->_path ), array( 'jquery' ), $this->loader->plugin_version );

		wp_localize_script( 'gravityview_maps_admin', 'GV_MAPS_ADMIN', array(
			'nonce' => wp_create_nonce( 'gravityview_maps_admin' ),
			'labelMapIconUploadTitle' => __( 'GravityView Maps Custom Map Icon', 'gravityview-maps' ),
			'labelMapIconUploadButton' => __( 'Add Icon', 'gravityview-maps' ),

		));
	}

	/**
	 *
	 * Add admin script to the whitelist
	 *
	 * @param $required
	 *
	 * @return array
	 */
	function register_no_conflict( $required ) {

		$filter = current_filter();

		if( preg_match('/script/ism', $filter ) ) {
			$required[] = 'gravityview_maps_admin';
		} elseif( preg_match('/style/ism', $filter ) ) {
			$required[] = 'gravityview_maps_admin_css';
		}

		return $required;
	}
}