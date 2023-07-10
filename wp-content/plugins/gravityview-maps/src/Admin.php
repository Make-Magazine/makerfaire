<?php

namespace GravityKit\GravityMaps;


use GravityView_Metabox_Tabs;
use GravityView_View_Data;
use GravityView_Metabox_Tab;
use GVCommon;
use GFAPI;


/**
 * Admin logic
 *
 * @since 1.0.0
 */
class Admin extends Component {

	/**
	 * Cache key for the transient that stores the Views that need to be updated to use the REST API.
	 * @since 2.1
	 */
	const TRANSIENT_KEY_MAPS_WITHOUT_REST = 'gk_maps_get_map_views_without_rest';

	const NONCE_REST_UPDATE_ACTION = 'gk-gravitymaps-trigger-update-map-views-rest-api';

	function load() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'admin_notices', [ $this, 'render_notice_map_views_need_rest' ] );

		// Save the form configuration. Run at 14 so that View metadata is already saved (at 10)
		add_action( 'save_post', array( $this, 'save_post' ), 14 );

		// @see \GravityView_View_Data::get_default_args
		add_filter( 'gravityview_default_args', array( $this, 'register_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'gravityview_noconflict_scripts', array( $this, 'register_no_conflict' ) );
		add_filter( 'gravityview_noconflict_styles', array( $this, 'register_no_conflict' ) );

		// ajax - populate address fields based on the selected form
		add_action( 'wp_ajax_gv_address_fields', array( $this, 'get_address_fields' ) );

		// ajax - Enables the REST API for Views that are Maps related.
		add_action( 'wp_ajax_gk_gravitymaps_enable_rest_on_map_views', [ $this, 'handle_update_views_enabling_rest' ] );

		add_filter( 'gravityview/search/input_types', [ $this, 'filter_include_geo_radius_field_type' ] );
		add_filter( 'gravityview/search/searchable_fields', [ $this, 'filter_include_geo_searchable_field' ] );
		add_filter( 'gravityview/search/input_labels', [ $this, 'filter_include_geo_radius_field_label' ] );
		add_filter( 'gravityview/extension/search/input_type', [ $this, 'filter_modify_geo_allowed_input_types' ], 10, 3 );
		add_filter( 'gravityview_search_field_label', [ $this, 'filter_default_label_geolocation_radius' ], 15, 3 );

	}

	/**
	 * Add GravityView Maps metabox
	 */
	function register_metabox() {
		$m = array(
			'id'            => 'maps_settings',
			'title'         => __( 'Maps', 'gk-gravitymaps' ),
			'callback'      => array( $this, 'render_metabox' ),
			'icon-class'    => 'dashicons-location-alt',
			'file'          => '',
			'callback_args' => '',
			'screen'        => 'gravityview',
			'context'       => 'side',
			'priority'      => 'default',
		);

		if ( class_exists( 'GravityView_Metabox_Tab' ) ) {

			$metabox = new GravityView_Metabox_Tab( $m['id'], $m['title'], $m['file'], $m['icon-class'], $m['callback'], $m['callback_args'] );

			GravityView_Metabox_Tabs::add( $metabox );
		} else {
			add_meta_box( 'gravityview_' . $m['id'], $m['title'], $m['callback'], $m['screen'], $m['context'], $m['priority'] );
		}
	}

	/**
	 * Render html for metabox
	 *
	 * @access public
	 *
	 * @param object $post
	 *
	 * @return void
	 */
	function render_metabox( $post ) {
		global $ms, $address_fields_input, $choice_marker_icon_field_input;

		// Use nonce for verification
		wp_nonce_field( 'gravityview_maps_settings', 'gravityview_maps_settings_nonce' );

		// get current form id
		$curr_form = GVCommon::get_meta_form_id( $post->ID );

		// View Map settings
		$ms = self::get_map_settings( $post->ID );

		// Backwards compatibility for pre-1.6 versions where $map_address_field is a string
		$map_address_fields = ( is_array( $ms['map_address_field'] ) ) ? $ms['map_address_field'] : array( $ms['map_address_field'] );

		$address_fields_input = '<select name="gv_maps_settings[map_address_field][]" multiple="multiple" id="gv_maps_se_map_address_field">' . $this->render_address_fields_options( $curr_form, $map_address_fields ) . '</select>';

		$choice_marker_icon_field_input = '<select name="gv_maps_settings[choice_marker_icon_field][]" id="gv_maps_se_choice_marker_icon_field">' . $this->render_choice_marker_icon_fields_options( $curr_form, (array) \GV\Utils::get( $ms, 'choice_marker_icon_field', array() ) ) . '</select>';

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

		if ( 'normalized' !== $mode ) {
			return $settings;
		}

		$defaults = \GV\View_Settings::defaults( false, 'maps' );

		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Render HTML options tags for the dropdown Address Field
	 *
	 * @param string $form_id Current assigned Form ID
	 * @param array  $current Current saved setting value
	 *
	 * @return string HTML markup
	 */
	public function render_address_fields_options( $form_id = '', $current = array() ) {
		$none_option = '<option value="" selected="selected">' . esc_html__( 'None', 'gk-gravitymaps' ) . '</option>';

		if ( empty( $form_id ) ) {
			$output = $none_option;
		} else {
			// Get fields with sub-inputs and no parent
			$fields = GFAPI::get_fields_by_type( GFAPI::get_form( $form_id ), 'address' );

			$output = '';
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					// Select default address if ID matches or if this is the first address field.
					$selected = ( in_array( $field->id, $current ) || ( empty( $current ) && $output === '' ) );

					$label = $field->get_field_label( false, '' );

					$output .= '<option value="' . $field->id . '" ' . selected( true, $selected, false ) . '>' . esc_attr( $label ) . '</option>';
				}
			}

			if ( empty( $output ) ) {
				$output = $none_option;
			}
		}

		return $output;
	}

	/**
	 * Render HTML options tags for the dropdown Custom Map Marker Icon
	 *
	 * @since 1.9
	 *
	 * @param string $form_id Current assigned Form ID
	 * @param array  $current Current saved setting value
	 *
	 * @return string HTML markup
	 */
	public function render_choice_marker_icon_fields_options( $form_id = '', $current = array() ) {

		$none_option = '<option value="">' . esc_html__( 'None', 'gk-gravitymaps' ) . '</option>';
		$empty_output = '<option selected="selected" disabled="disabled" value="">' . esc_html__( 'No form fields have map marker images enabled.', 'gk-gravitymaps' ) . '</option>';

		if ( empty( $form_id ) ) {
			return $empty_output;
		}

		// Get fields with sub-inputs and no parent
		$fields = GFAPI::get_fields_by_type( GFAPI::get_form( $form_id ), array( 'select', 'radio' ) );

		if ( empty( $fields ) ) {
			return $empty_output;
		}

		$option_output = '';
		foreach ( $fields as $field ) {
			// Select default address if ID matches or if this is the first address field.
			$selected = ( in_array( $field->id, $current ) || ( empty( $current ) && $option_output === '' ) );

			if ( empty( $field->gk_custom_map_icon_enabled ) ) {
				continue;
			}

			$label = $field->get_field_label( false, '' );

			$option_output .= '<option value="' . esc_attr( $field->id ) . '" ' . selected( true, $selected, false ) . '>' . esc_html( $label ) . '</option>';
		}

		if ( empty( $option_output ) ) {
			return $empty_output;
		}

		return $none_option . $option_output;
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
			'label'   => __( 'Address Fields', 'gk-gravitymaps' ),
			'type'    => 'select',
			'value'   => '',
			'options' => array(
				'' => __( 'None', 'gk-gravitymaps' ),
			),
			'tooltip' => '',
			'group'   => 'maps'
		);

		$settings['map_type'] = array(
			'label'   => __( 'Map Type', 'gk-gravitymaps' ),
			'type'    => 'select',
			'value'   => 'roadmap',
			'options' => array(
				'roadmap'   => __( 'Street', 'gk-gravitymaps' ),
				'satellite' => __( 'Satellite', 'gk-gravitymaps' ),
				'hybrid'    => __( 'Hybrid', 'gk-gravitymaps' ),
				'terrain'   => __( 'Terrain', 'gk-gravitymaps' ),
			),
			'tooltip' => __( 'Hybrid: This map type displays a transparent layer of major streets on satellite images. Roadmap: This map type displays a normal street map. Satellite: This map type displays satellite images. Terrain: This map type displays maps with physical features such as terrain and vegetation.', 'gk-gravitymaps' ),
			'group'   => 'maps'
		);

		/**
		 * @since 1.1
		 */
		$settings['map_layers'] = array(
			'label'   => __( 'Map Layers', 'gk-gravitymaps' ),
			'type'    => 'radio',
			'value'   => '0',
			'options' => array(
				'0'         => __( 'None', 'gk-gravitymaps' ),
				'traffic'   => __( 'Traffic', 'gk-gravitymaps' ),
				'transit'   => __( 'Transit', 'gk-gravitymaps' ),
				'bicycling' => __( 'Bicycle', 'gk-gravitymaps' ),
			),
			'group'   => 'maps',
			'tooltip' => __( 'The Traffic, Transit and Bicycling layers modify the base map layer to display current traffic conditions, or local Transit and Bicycling route information. These layers are available in select regions.', 'gk-gravitymaps' ),
		);

		$settings['map_default_radius_search'] = [
			'label' => __( 'Default Radius', 'gk-gravitymaps' ),
			'type'  => 'number',
			'tooltip'  => __( 'On the Search field type Geolocation Radius what should be the default value for the radius calculation.', 'gravityview', 'gk-gravitymaps' ),
			'min' => 0.1,
			'step' => 0.1,
			'value' => 15,
			'group' => 'maps',
			'show_in_template' => [
				'map',
				'preset_business_map'
			],
		];

		$settings['map_default_radius_search_unit'] = [
			'label' => __( 'Default Radius Unit', 'gk-gravitymaps' ),
			'type'  => 'radio',
			'tooltip'  => __( 'On the Search field type Geolocation Radius what should be the default unit for the radius calculation.', 'gk-gravitymaps' ),
			'value' => Search_Filter::get_instance()->get_default_radius_unit(),
			'options' => [
				Search_Filter::MILES => __( 'Miles', 'gk-gravitymaps' ),
				Search_Filter::KM => __( 'Kilometers', 'gk-gravitymaps' ),
			],
			'group' => 'maps',
			'show_in_template' => [
				'map',
				'preset_business_map'
			],
		];
		/**
		 * @since 1.3
		 */
		$settings['map_zoom'] = array(
			'type'    => 'select',
			'label'   => __( 'Default Zoom', 'gk-gravitymaps' ),
			'desc'    => __( 'The default zoom for a single-marker map.', 'gk-gravitymaps' ),
			'tooltip' => __( 'Higher numbers are zoomed more.', 'gk-gravitymaps' ) . ' ' . __( 'Maps with multiple markers will zoom out to fit all markers.', 'gk-gravitymaps' ),
			'value'   => 15,
			'group'   => 'maps',
			'options' => array(
				'1' => '1 - ' . esc_html_x( 'Globe-Level', 'Related to how zoomed-in a map is', 'gk-gravitymaps' ),
				'2'  => '2',
				'3'  => '3',
				'4'  => '4',
				'5'  => '5',
				'6'  => '6',
				'7'  => '7',
				'8'  => '8',
				'9'  => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15 - ' . esc_html__( 'Default', 'gk-gravitymaps' ),
				'16' => '16',
				'17' => '17',
				'18' => '18',
				'19' => '19',
				'20' => '20',
				'21' => '21 - ' . esc_html_x( 'Street-Level', 'Related to how zoomed-in a map is', 'gk-gravitymaps' ),
			)
		);

		/**
		 * @since 1.1
		 */
		$settings['map_minzoom'] = array(
			'type'    => 'select',
			'label'   => __( 'Minimum Zoom', 'gk-gravitymaps' ),
			'desc'    => __( 'The farthest out a map can zoom.', 'gk-gravitymaps' ),
			'tooltip' => __( 'Higher numbers are zoomed more.', 'gk-gravitymaps' ) . ' ' . sprintf( _x( 'If "%s", the %s zoom from the current map type will be used.', 'This is to reduce translation strings. The replacement words are "No Minimum/No Maximum" and "minimum/maximum"', 'gk-gravitymaps' ), __( 'No Minimum', 'gk-gravitymaps' ), __( 'minimum', 'gk-gravitymaps' ) ),
			'value'   => 3,
			'group'   => 'maps',
			'options' => array(
				'0'  => __( 'No Minimum', 'gk-gravitymaps' ),
				'1' => '1 - ' . esc_html_x( 'Globe-Level', 'Related to how zoomed-in a map is', 'gk-gravitymaps' ),
				'2'  => '2',
				'3'  => '3 - ' . esc_html__( 'Default', 'gk-gravitymaps' ),
				'4'  => '4',
				'5'  => '5',
				'6'  => '6',
				'7'  => '7',
				'8'  => '8',
				'9'  => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16',
				'17' => '17',
				'18' => '18',
				'19' => '19',
				'20' => '20',
				'21' => '21 - ' . esc_html_x( 'Street-Level', 'Related to how zoomed-in a map is', 'gk-gravitymaps' ),
			)
		);

		/**
		 * @since 1.1
		 */
		$settings['map_maxzoom'] = array(
			'type'    => 'select',
			'label'   => __( 'Maximum Zoom', 'gk-gravitymaps' ),
			'desc'    => __( 'The maximum zoom level which will be displayed on the map.', 'gk-gravitymaps' ),
			'tooltip' => __( 'Higher numbers are zoomed more.', 'gk-gravitymaps' ) . ' ' . sprintf( __( 'If "%s", the %s zoom from the current map type will be used.', 'This is to reduce translation strings. The replacement words are "No Minimum/No Maximum" and "minimum/maximum"', 'gk-gravitymaps' ), __( 'No Maximum', 'gk-gravitymaps' ), __( 'maximum', 'gk-gravitymaps' ) ),
			'options' => array(
				'0'  => __( 'No Maximum', 'gk-gravitymaps' ),
				'1'  => '1',
				'2'  => '2',
				'3'  => '3',
				'4'  => '4',
				'5'  => '5',
				'6'  => '6',
				'7'  => '7',
				'8'  => '8',
				'9'  => '9',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16 - ' . esc_html__( 'Default', 'gk-gravitymaps' ),
				'17' => '17',
				'18' => '18',
			),
			'value'   => 16,
			'group'   => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_draggable'] = array(
			'label'      => __( 'Allow the map to be dragged', 'gk-gravitymaps' ),
			'type'       => 'checkbox',
			'left_label' => __( 'Draggable Map', 'gk-gravitymaps' ),
			'value'      => 1,
			'group'      => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_doubleclick_zoom'] = array(
			'label'      => __( 'Allow double-clicking on the map to zoom and center', 'gk-gravitymaps' ),
			'type'       => 'checkbox',
			'left_label' => __( 'Double-click Zoom', 'gk-gravitymaps' ),
			'value'      => 1,
			'group'      => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_scrollwheel_zoom'] = array(
			'label'      => __( 'Allow scrolling to zoom in and out on the map', 'gk-gravitymaps' ),
			'type'       => 'checkbox',
			'left_label' => __( 'Scroll to Zoom', 'gk-gravitymaps' ),
			'value'      => 1,
			'group'      => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_pan_control'] = array(
			'label'      => __( 'Display the "Pan" control that allows moving the map by clicking arrows', 'gk-gravitymaps' ),
			'type'       => 'checkbox',
			'left_label' => __( 'Pan Control', 'gk-gravitymaps' ),
			'value'      => 1,
			'group'      => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_zoom_control'] = array(
			'type'    => 'select',
			'label'   => __( 'Zoom Control', 'gk-gravitymaps' ),
			'desc'    => __( 'Display the zoom control that allows zooming in and out of the map', 'gk-gravitymaps' ),
			'options' => array(
				'none'    => __( 'None (Don\'t display)', 'gk-gravitymaps' ),
				'small'   => __( 'Small (Small buttons)', 'gk-gravitymaps' ),
				'default' => __( 'Default (Let map decide)', 'gk-gravitymaps' ),
			),
			'tooltip' => '<p>' . __( 'None: Don\'t display the zoom control.', 'gk-gravitymaps' ) . '</p>' .
			             '<p>' . __( 'Small: A small control with buttons to zoom in and out.', 'gk-gravitymaps' ) . '</p>' .
			             '<p>' . __( 'Default: The default zoom control varies according to map size and other factors.', 'gk-gravitymaps' ) . '</p>',
			'value'   => 'default',
			'group'   => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_streetview_control'] = array(
			'label'      => __( 'Display the Street View "Pegman" control', 'gk-gravitymaps' ),
			'type'       => 'checkbox',
			'left_label' => __( 'Street View', 'gk-gravitymaps' ),
			'value'      => 1,
			'group'      => 'maps',
		);

		/**
		 * @since 1.1
		 */
		$settings['map_styles'] = array(
			'label' => __( 'Custom Map Styles', 'gk-gravitymaps' ),
			'desc'  => __( 'Set map styles that override the default styling. See SnazzyMaps.com for styles or use your own.', 'gk-gravitymaps' ),
			'type'  => 'textarea',
			'value' => '',
			'group' => 'maps',
		);

		$settings['map_marker_icon'] = array(
			'label' => __( 'Default Marker Icon', 'gk-gravitymaps' ),
			'type'  => 'hidden',
			// translators: Do not translate the words inside the {} curly brackets; they are replaced.
			'desc'  => strtr( esc_html__( 'This is the default icon. Entries that have a Map Marker Icon field or using the Choice Marker Icon setting will override this value. {link}Learn more about custom icons{/link}.', 'gk-gravitymaps' ), array(
				'{link}'   => '<a href="https://docs.gravitykit.com/article/828-a" target="_blank" rel="noopener noreferrer">',
				'{/link}'  => '<span class="screen-reader-text"> ' . esc_html__( '(This link opens in a new window.)', 'gk-gravitymaps' ) . '</span></a>',
			) ),
			'value' => $this->loader->component_instances['Available_Icons']->get_default_icon_url(),
			'group' => 'maps'
		);

		$settings['choice_marker_icon_field'] = array(
			'label' => __( 'Choice-Based Marker Icon', 'gk-gravitymaps' ),
			'desc'  => esc_html__( 'Select a field that has choice-based marker images enabled. If not set, or if the field is empty, the Default Pin Icon will be used.', 'gk-gravitymaps' ),
			'type'  => 'select',
			'options' => array(
				'' => __( 'None', 'gk-gravitymaps' ),
			),
			'value' => '',
			'group' => 'maps'
		);

		$settings['map_canvas_position'] = array(
			'label'            => __( 'Map Position', 'gk-gravitymaps' ),
			'type'             => 'radio',
			'value'            => 'top',
			'options'          => array(
				'top'    => __( 'Top', 'gk-gravitymaps' ),
				'right'  => __( 'Right', 'gk-gravitymaps' ),
				'bottom' => __( 'Bottom', 'gk-gravitymaps' ),
				'left'   => __( 'Left', 'gk-gravitymaps' ),
			),
			'group'            => 'maps',
			'show_in_template' => array(
				'map',
				'preset_business_map'
			),
		);

		$settings['map_canvas_sticky'] = array(
			'label'            => __( 'Keep the map fixed during page scroll', 'gk-gravitymaps' ),
			'left_label'       => __( 'Pinned Map', 'gk-gravitymaps' ),
			'type'             => 'checkbox',
			'value'            => 1,
			'group'            => 'maps',
			'show_in_template' => array(
				'map',
				'preset_business_map'
			),
		);

		/**
		 * Info windows
		 * @since 1.4
		 */
		$settings['map_info_enable'] = array(
			'label'      => __( 'Show a popup box with additional entry details when clicking a map marker', 'gk-gravitymaps' ),
			'type'       => 'checkbox',
			'left_label' => __( 'Show Info Boxes', 'gk-gravitymaps' ),
			'value'      => 0,
			'group'      => 'maps',
		);

		$settings['map_info_title'] = array(
			'label'           => __( 'Title', 'gk-gravitymaps' ),
			'desc'            => '',
			'type'            => 'text',
			'value'           => '',
			'merge_tags'      => 'force',
			'show_all_fields' => false, // Show the `{all_fields}` and `{pricing_fields}` merge tags
			'group'           => 'maps'
		);

		$settings['map_info_title_link'] = array(
			'left_label' => __( 'Link the Title', 'gk-gravitymaps' ),
			'label'  => esc_html__( 'Convert the info box "Title" into a link to the single entry', 'gk-gravitymaps' ),
			'type'  => 'checkbox',
			'value' => 1,
			'group' => 'maps',
		);

		$settings['map_info_content'] = array(
			'label'           => __( 'Content', 'gk-gravitymaps' ),
			'type'            => 'textarea',
			'value'           => '',
			'merge_tags'      => 'force',
			'show_all_fields' => true, // Show the `{all_fields}` and `{pricing_fields}` merge tags
			'group'           => 'maps',
			'class'           => 'code'
		);

		$settings['map_info_image'] = array(
			'label'      => __( 'Image URL', 'gk-gravitymaps' ),
			'desc'       => esc_html__( 'Insert a Merge Tag for a File Upload field, or a URL to an image. This will be used as the source of a HTML <img> tag.', 'gk-gravitymaps' ),
			'type'       => 'text',
			'value'      => '',
			'merge_tags' => 'force',
			'group'      => 'maps'
		);

		$settings['map_info_image_align'] = array(
			'label'      => __( 'Image Alignment', 'gk-gravitymaps' ),
			'type'       => 'radio',
			'value'      => 0,
			'merge_tags' => false,
			'group'      => 'maps',
			'options'    => array(
				'0'     => __( 'Top', 'gk-gravitymaps' ),
				'left'  => __( 'Left', 'gk-gravitymaps' ),
				'right' => __( 'Right', 'gk-gravitymaps' ),
			),
		);

		/**
		 * Marker clustering
		 *
		 * @since 1.4.3
		 */
		$settings['map_marker_clustering'] = array(
			'label'      => __( 'Group nearby markers together when displaying map beyond a certain zoom level', 'gk-gravitymaps' ),
			'tooltip'    => '<img src="' . esc_url( plugins_url( '/assets/img/admin/cluster-example.png', $this->loader->path ) ) . '" style="max-height: 120px;" alt="' . esc_attr__( 'Marker Clustering', 'gk-gravitymaps' ) . '" />',
			'type'       => 'checkbox',
			'left_label' => __( 'Marker Clustering', 'gk-gravitymaps' ),
			'value'      => 0,
			'group'      => 'maps',
		);

		$settings['map_marker_clustering_maxzoom'] = array(
			'type'     => 'select',
			'label'    => __( 'Clustering Maximum Zoom', 'gk-gravitymaps' ),
			'tooltip'  => wpautop( __( 'Do not display marker clusters beyond this zoom level; show individual markers instead.', 'gk-gravitymaps' ) . ' ' . __( 'Higher numbers are zoomed more.', 'gk-gravitymaps' ) ),
			'requires' => 'map_marker_clustering',
			'options'  => array(
				'0'  => __( 'No Maximum', 'gk-gravitymaps' ),
				'1'  => '1',
				'2'  => '2',
				'3'  => '3',
				'4'  => '4',
				'5'  => '5',
				'6'  => '6',
				'7'  => '7',
				'8'  => '8',
				'9'  => '9',
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
			'value'    => 12,
			'group'    => 'maps',
		);

		$settings['map_international_autocomplete_filter'] = [
			'label'       => __( 'International Autocomplete Filter', 'gk-gravitymaps' ),
			'type'        => 'text',
			'tooltip'     => strtr( esc_html__( 'Enter {link}two-letter country codes{/link}, separated by commas, to limit what countries are used for autocompletion.', 'gk-gravitymaps' ),
				[
					'{link}'  => '<a href="https://wikipedia.org/wiki/ISO_3166-1" target="_blank" rel="noopener noreferrer" title="' . esc_attr__( 'See a list of two-letter country codes', 'gk-gravitymaps' ) . '">' . '<span class="screen-reader-text"> ' . esc_html__( '(This link opens in a new window.)', 'gk-gravitymaps' ) . '</span>',
					'{/link}' => '</a>',
				] ),
			'value'       => '',
			'placeholder' => esc_html__( 'Example: BR, FR, IE', 'gk-gravitymaps' ),
			'class'       => 'widefat',
			'group'       => 'maps',
		];

		return $settings;
	}

	/**
	 * Save settings
	 *
	 * @access public
	 *
	 * @param mixed $post_id
	 *
	 * @return void
	 */
	function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// validate post_type
		if ( ! isset( $_POST['post_type'] ) || 'gravityview' != $_POST['post_type'] ) {
			return;
		}

		// validate user can edit and save post/page
		if ( 'page' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		} else if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// nonce verification
		if ( isset( $_POST['gravityview_maps_settings_nonce'] ) && wp_verify_nonce( $_POST['gravityview_maps_settings_nonce'], 'gravityview_maps_settings' ) ) {
			if ( empty( $_POST['gv_maps_settings'] ) ) {
				$_POST['gv_maps_settings'] = array();
			}

			if ( $this->has_maps( $post_id ) ) {
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
		if ( empty( $view_id ) ) {
			return false;
		}

		if ( 'map' == get_post_meta( $view_id, '_gravityview_directory_template', true ) ) {
			return true;
		}

		$widgets = get_post_meta( $view_id, '_gravityview_directory_widgets', true );
		if ( ! empty( $widgets ) && $this->has_map_object( $widgets, 'map' ) ) {
			return true;
		}

		$fields = get_post_meta( $view_id, '_gravityview_directory_fields', true );
		if ( ! empty( $fields ) && $this->has_map_object( $fields, 'entry_map' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Helper function to search for the map object in the fields or widgets associative array
	 *
	 * @param array  $objects  Associative array $fields or $widgets
	 * @param string $field_id the name of the map field id
	 *
	 * @return bool
	 */
	public function has_map_object( $objects, $field_id ) {
		if ( ! is_array( $objects ) ) {
			return false;
		}

		foreach ( $objects as $areas ) {
			if ( ! is_array( $areas ) ) {
				continue;
			}

			foreach ( $areas as $object ) {
				if ( $field_id === $object['id'] ) {
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
		if ( empty( $_POST['formid'] ) || ! is_numeric( $_POST['formid'] ) ) {
			exit( false );
		}

		// Not valid request
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_maps_admin' ) ) {
			exit( false );
		}

		$form_id = (int) $_POST['formid'];

		// Generate the output `<option>`s
		$response = $this->render_address_fields_options( $form_id );

		exit( $response );
	}

	/**
	 * Checks whether the current Admin screen is a single View or not.
	 *
	 * @since 2.1
	 *
	 * @return bool|\GV\View
	 */
	protected function is_single_view() {
		$current_screen = get_current_screen();

		if( 'gravityview' !== $current_screen->post_type || 'post' !== $current_screen->base ) {
			return false;
		}

		return gravityview()->request->is_view( true );
	}

	/**
	 * Check whether the REST API is disabled for the current View by taking global settings into account.
	 *
	 * @since 2.1
	 *
	 * @param \GV\View $view
	 *
	 * @return bool True: REST API is disabled for the passed View. False: REST API is enabled for the passed View.
	 */
	protected static function is_rest_disabled( \GV\View $view ) {

		// If global REST API is enabled, check for Views with REST API disabled.
		if ( gravityview()->plugin->settings->get( 'rest_api' ) ) {
			return $view->settings->get( 'rest_disable' ) === '1';
		}

		// If global REST API is disabled, check for Views with REST API enabled.
		return $view->settings->get( 'rest_enable' ) !== '1';
	}

	/**
	 * Get all Map Views without REST API enabled.
	 *
	 * This function retrieves all GravityView Map View posts that do not have the REST API enabled.
	 * The results are cached using WordPress Transients API for 12 hours.
	 *
	 * @since 2.1
	 *
	 * @return \GV\View[] \GV\View objects without REST API enabled.
	 */
	protected function get_map_views_without_rest(): array {
	    // Try to get the cached results
	    $cached_views = isset( $_GET['cache'] ) ? false : get_transient( self::TRANSIENT_KEY_MAPS_WITHOUT_REST );

	    // If the cached results are available, return them
	    if ( $cached_views !== false && is_array( $cached_views ) ) {
			return array_map( [ \GV\View::class, 'by_id' ], $cached_views );
	    }

		$args = [
			'post_type' => 'gravityview',
			'posts_per_page' => -1, // Fetch all.
			'fields' => 'ids',
			'meta_query' => [
				'is_map_view' => [
					'key' => '_gravityview_directory_template',
					'value' => 'map',
					'compare' => '=',
				]
			],
		];

		$query = new \WP_Query( $args );
		$query_view_ids = $query->posts; // An array of View IDs.

		$views = array_map( [ \GV\View::class, 'by_id' ], $query_view_ids ); // Array of \GV\View objects.

		// Remove all Views that have rest enabled.
		$views = array_filter( $views, static function ( $view ) {
			return self::is_rest_disabled( $view );
		} );

		$view_ids = array_map( static function( $view ) {
			return $view->ID;
		}, $views );

		// Cache the results for 12 hours.
	    set_transient( self::TRANSIENT_KEY_MAPS_WITHOUT_REST, $view_ids, DAY_IN_SECONDS / 2 );

		return $views;
	}

	/**
	 * Render an admin notice for Map Views that need REST API access.
	 *
	 * This function displays a warning notice in the WordPress admin area when there are
	 * GravityView Map View posts without REST API enabled. The notice includes a button to
	 * trigger the update process for enabling REST API on those Map Views.
	 *
	 * @return void
	 */
	public function render_notice_map_views_need_rest(): void {

		$views = $this->get_map_views_without_rest();

		if ( empty( $views ) ) {
			return;
		}

		$is_single = $this->is_single_view();
		$view_id = $is_single && ! empty( $_GET['post'] ) ? (int) $_GET['post'] : null;

		// When single edit page for view there are special conditions.
		if ( $is_single ) {
			// If the current View is a new view, don't show the notice.
			if ( ! $view_id ) {
				return;
			}

			// Now check if the current View is in the list of Map Views without REST API enabled.
			$map_views_without_rest = wp_list_pluck( $views, 'ID' );
			if ( ! in_array( $view_id, $map_views_without_rest, true )  ) {
				return;
			}
		}

		wp_enqueue_script( 'gravityview_maps_admin' );

		$button_args = [
			'data-js' => self::NONCE_REST_UPDATE_ACTION,
			'data-gk-ajax-nonce' => wp_create_nonce( self::NONCE_REST_UPDATE_ACTION ),
			'data-gk-single-view-id' => null,
		];

		if ( $is_single ) {
			$message = esc_html__( 'This View is using the Map View Type, which needs REST API access to work properly.', 'gk-gravitymaps' );
			$button_text = esc_html__( 'Enable REST API for this View', 'gk-gravitymaps' );
			$button_args['data-gk-single-view-id'] = $view_id;
		} else {
			$message = esc_html__( 'You currently have Views using the Map View Type that need REST API access to work properly.', 'gk-gravitymaps' );
			$button_text = sprintf( _n( 'Enable REST API for %d View', 'Enable REST API for %d Views', count( $views ), 'gk-gravitymaps' ), count( $views ) );
		}

		$message .= PHP_EOL . PHP_EOL;
		$message .= get_submit_button( esc_html( $button_text ), 'primary', 'submit', false, $button_args );
		$message .= strtr( '<a href="{link}" class="{class}" style="{style}">{anchor}</a>', [
			'{link}' => esc_url( admin_url( 'admin.php?page=gk_settings&p=gravityview' ) ),
			'{class}' => 'button button-link',
			'{style}' => 'margin: 0 .5em; padding: 0 .75em;',
			'{anchor}' => esc_html__( 'Go to GravityView Settings', 'gk-gravitymaps' ),
		] );

		$type    = 'warning';

		?>
		<div class="gk-notice notice notice-<?php echo esc_attr( $type ); ?> is-dismissible" role="alert">
			<?php echo wpautop( $message ); ?>
			<div class="gk-gravitymaps-ajax-in-progress"></div>
		</div>
		<?php
	}

	/**
	 * Handles updating the Views to enable REST API permission.
	 *
	 * This function updates all Views to enable REST API permission by setting the 'rest_enable' option to '1' in the '_gravityview_template_settings' meta field.
	 * If the nonce verification fails or is missing, an error message is returned using the WordPress JSON API.
	 * If successful, a success message is returned using the WordPress JSON API.
	 *
	 * @return void
	 */
	public function handle_update_views_enabling_rest(): void {
		if ( empty( $_POST['nonce'] ) ) {
			wp_send_json_error( [ 'text' => esc_html__( 'Error while trying to update the Views, please try again.', 'gk-gravitymaps' ) ] );
		}

		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, self::NONCE_REST_UPDATE_ACTION ) ) {
			wp_send_json_error( [ 'text' => esc_html__( 'Error while trying to update the Views, please try again.', 'gk-gravitymaps' ) ] );
		}

		$is_single_view = false;

		if ( ! empty( $_POST['single_view_id'] ) ) {
			$is_single_view = true;
			$views = [ \GV\View::by_id( (int) $_POST['single_view_id'] ) ];
		} else {
			$views = $this->get_map_views_without_rest();
		}

		if ( empty( $views ) ) {
			wp_send_json_error( [ 'text' => esc_html__( 'No Views to update.', 'gk-gravitymaps' ) ] );
		}

		foreach ( $views as $view ) {

			// Sanity check: `update_post_meta()` returns false if nothing changes, so make extra-sure it's changing.
			if ( ! self::is_rest_disabled( $view ) ) {
				continue;
			}

			$settings = $view->settings->as_atts();

			$settings['rest_disable'] = '0';
			$settings['rest_enable'] = '1';

			$updated = update_post_meta( $view->ID, '_gravityview_template_settings', $settings );

			if ( ! $updated ) {
				wp_send_json_error( [ 'text' => sprintf( esc_html_x( 'Error while trying to update View #%d, please try again.', '%d is replaced by the View ID', 'gk-gravitymaps' ), $view->ID ) ] );
			}
		}

		// This will only be set when there are no errors during the loop above.
		set_transient( self::TRANSIENT_KEY_MAPS_WITHOUT_REST, [], DAY_IN_SECONDS / 2 );

		if( $is_single_view ) {
			$success_message = esc_html__( 'This View now has REST API enabled.', 'gk-gravitymaps' );
		} else {
			$success_message = esc_html__( 'All Views using the Map View Type now have REST API enabled.', 'gk-gravitymaps' );
		}

		wp_send_json_success( [
			'text' => $success_message,
		] );
	}

	/**
	 * Add script to Views edit screen (admin)
	 *
	 * @param mixed $hook
	 */
	public function enqueue_scripts( $hook ) {
		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script( 'gravityview_maps_admin', plugins_url( 'assets/js/admin' . $script_debug . '.js', $this->loader->path ), array( 'jquery' ), $this->loader->plugin_version );
		wp_localize_script( 'gravityview_maps_admin', 'GV_MAPS_ADMIN', array(
			'nonce'                    => wp_create_nonce( 'gravityview_maps_admin' ),
			'labelMapIconUploadTitle'  => __( 'GravityView Maps Custom Map Icon', 'gk-gravitymaps' ),
			'labelMapIconUploadButton' => __( 'Add Icon', 'gk-gravitymaps' ),
			'textModifyRestOnMapChanges' => __( 'The changes that were just made to your View require REST API access. Click OK to enable.', 'gk-gravitymaps' ),
			'textModifyRestOnLoadWithMap' => __( 'This View requires REST API access, but it is currently disabled. Click OK to enable.', 'gk-gravitymaps' ),
		) );

		// Don't process any scripts below here if it's not a GravityView page.
		if ( ! gravityview()->request->is_admin( $hook, 'single' ) ) {
			return;
		}

		// inject the media scripts and styles for handling custom map icons
		wp_enqueue_media();

		wp_enqueue_style( 'gravityview_maps_admin_css', plugins_url( 'assets/css/admin.css', $this->loader->path ), array(), $this->loader->plugin_version );

		wp_enqueue_script( 'gravityview_maps_admin' );
	}

	/**
	 *
	 * Add admin script to the allowlist
	 *
	 * @param $required
	 *
	 * @return array
	 */
	function register_no_conflict( $required ) {
		$filter = current_filter();

		if ( preg_match( '/script/ism', $filter ) ) {
			$required[] = 'gravityview_maps_admin';
		} elseif ( preg_match( '/style/ism', $filter ) ) {
			$required[] = 'gravityview_maps_admin_css';
		}

		return $required;
	}

	/**
	 * Create the field type Geolocation, so we can have a full field area for this field usage on the search.
	 *
	 * @since 2.0
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function filter_include_geo_searchable_field( $fields ) {
		$fields['geolocation'] = [
			'adminLabel' => '',
			'adminOnly' => null,
			'label' => esc_html__( 'Geolocation Radius', 'gk-gravitymaps' ),
			'parent' => null,
			'type' => 'geolocation',
		];

		return $fields;
	}

	/**
	 * Create a base Label for the Geolocation Radius input type.
	 *
	 * @since 2.0
	 *
	 * @param $labels
	 *
	 * @return mixed
	 */
	public function filter_include_geo_radius_field_label( $labels ) {
		$labels['geo_radius'] = esc_html__( 'Geolocation Radius', 'gk-gravitymaps' );

		return $labels;
	}

	/**
	 * Default label for the geolocation radius field.
	 *
	 * @since 2.0
	 *
	 * @param $label
	 * @param $form_field
	 * @param $field
	 *
	 * @return string
	 */
	public function filter_default_label_geolocation_radius( $label, $form_field, $field ) {
		if ( ! isset( $field['field'] ) ) {
			return $label;
		}

		if ( $field['field'] !== 'geolocation' ) {
			return $label;
		}

		return ' ';
	}

	/**
	 * Include Geo Radius as an input type.
	 *
	 * @since 2.0
	 *
	 * @param array $input_types Input types avaiable.
	 *
	 * @return array
	 */
	public function filter_include_geo_radius_field_type( $input_types ) {
		$input_types['geolocation'] = [ 'geo_radius' ];

		return $input_types;
	}

	/**
	 * Add the Input type allowed for the geolocation field id.
	 *
	 * @since 2.0
	 *
	 * @param string|string[] $input_type
	 * @param array $field_type
	 * @param string $field_id
	 *
	 * @return string
	 */
	public function filter_modify_geo_allowed_input_types( $input_type, $field_type, $field_id ) {
		if ( 'geolocation' !== $field_id ) {
			return $input_type;
		}

		return 'geolocation';
	}
}