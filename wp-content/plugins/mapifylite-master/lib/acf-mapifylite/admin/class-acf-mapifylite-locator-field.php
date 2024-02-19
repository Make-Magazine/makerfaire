<?php

use \Acf_Mapifylite\Model\Maptiles_Uploader;
use \Acf_Mapifylite\Model\Mapify_Map_Location;
use \Acf_Mapifylite\Model\Mapify_Map;

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( ! class_exists( 'Acf_Mapifylite_Locator_Field' ) ) :


class Acf_Mapifylite_Locator_Field extends acf_field {
		
	/**
	 * __construct
	 *
	 * This function will setup the field type data
	 *
	 * @type    function
	 * @date    5/03/2014
	 * @since   5.0.0
	 *
	 * @param   n/a
	 * @return  n/a
	 */
	
	function __construct( $settings ) {
		
		/**
		 * name (string) Single word, no spaces. Underscores allowed
		 */
		
		$this->name = 'mapify_map_locator';
		
		
		/**
		 * label (string) Multiple words, can include spaces, visible when selecting a field type
		 */
		
		$this->label = __( 'Mapify Map Locator', 'acf-mapifylite' );
		
		
		/**
		 * category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		 */
		
		$this->category = 'Mapify';
		
		
		/**
		 * defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		 */
		
		$this->defaults = array(
			'lattitude'  => '-1.634541720929286',
			'longitude'  => '119.9030508161835',
			'zoom_level' => 4,
		);
		
		
		/**
		 * l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		 * var message = acf._e('mapify_map_locator', 'error');
		 */
		
		$this->l10n = array(
			'search-results-label' => __( 'Search results', 'acf-mapifylite' ),
			'no-results-label'     => __( 'No results found', 'acf-mapifylite' ),
			'loading-label'        => __( 'Loading..', 'acf-mapifylite' ),
		);
		
		
		/**
		 * settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		 */
		
		$this->settings = $settings;
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/**
	 * render_field_settings()
	 *
	 * Create extra settings for your field. These are visible when editing a field
	 *
	 * @type    action
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   $field (array) the $field being edited
	 * @return  n/a
	 */
	
	function render_field_settings( $field ) {
		
		/**
		 * acf_render_field_setting
		 *
		 * This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		 * The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		 *
		 * More than one setting can be added by copy/paste the above code.
		 * Please note that you must also have a matching $defaults value for the field name (font_size)
		 */
		
		// Default Lattitude
		acf_render_field_setting( $field, array(
			'label'        => __( 'Default Lattitude', 'acf-mapifylite' ),
			'instructions' => __( 'Initial lattitude value for the map', 'acf-mapifylite' ),
			'type'         => 'text',
			'name'         => 'lattitude',
		));

		// Default Longitude
		acf_render_field_setting( $field, array(
			'label'        => __( 'Default Longitude', 'acf-mapifylite' ),
			'instructions' => __( 'Initial longitude value for the map', 'acf-mapifylite' ),
			'type'         => 'text',
			'name'         => 'longitude',
		));

		// Default Zoom Level
		acf_render_field_setting( $field, array(
			'label'        => __( 'Default Zoom Level', 'acf-mapifylite' ),
			'instructions' => __( 'Initial zoom level value for the map', 'acf-mapifylite' ),
			'type'         => 'number',
			'name'         => 'zoom_level',
		));

	}
	
	
	
	/**
	 * render_field()
	 *
	 * Create the HTML interface for your field
	 *
	 * @param   $field (array) the $field being rendered
	 *
	 * @type    action
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   $field (array) the $field being edited
	 * @return  n/a
	 */
	
	function render_field( $field ) {

		// Attrs.
		$attrs = array(
			'id'    => $field['id'],
			'class' => "acf-mapifylite {$field['class']}",
		);

		// Get value
		$value = isset( $field['value'] ) ? $field['value'] : array();

		// Default map attrs.
		$map_canvas_attrs = array(
			'data-centered-lat' => '-1.634541720929286',
			'data-centered-lng' => '119.9030508161835',
			'data-selected-lat' => '-1.634541720929286',
			'data-selected-lng' => '119.9030508161835',
			'data-zoom-level'   => 4,
		);

		/**
		 * Set all required attibutes if we have the post_id
		 */
		if ( isset( $value['post_id'] ) ) {
			$mapify_map_location  = new Mapify_Map_Location( $value['post_id'] );
			$map_details          = $mapify_map_location->get_map_location_details();

			// Set map attrs.
			$map_canvas_attrs = array(
				'data-centered-lat' => $map_details['centered_location'][0],
				'data-centered-lng' => $map_details['centered_location'][1],
				'data-selected-lat' => $map_details['selected_location'][0],
				'data-selected-lng' => $map_details['selected_location'][1],
				'data-zoom-level'   => $map_details['zoom_level'],
			);			
		}
		
		/**
		 * Load admin fields
		 */
		include_once( 'partials/mapify-map-locator-field-display.php' );

	}
	
		
	/**
	 * input_admin_enqueue_scripts()
	 *
	 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	 * Use this action to add CSS + JavaScript to assist your render_field() action.
	 *
	 * @type    action (admin_enqueue_scripts)
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   n/a
	 * @return  n/a
	 */
	
	function input_admin_enqueue_scripts() {
		
		global $post_type, $post;

		// should be loaded only on post type map-location
		if ( 'map-location' !== $post_type ) return false;
				
		// vars
		$url             = $this->settings['url'];
		$version         = $this->settings['version'];
		$wp_upload_dir   = wp_upload_dir();
		$map_location_id = ( $post && isset( $post->ID ) ) ? intval( $post->ID ) : 0;
		$post_id         = 0;
		$has_image_mode  = false;
		$map_mode        = 'map';

		if ( $map_location_id ) {
			$mapify_map_location = new Mapify_Map_Location( $map_location_id );
			$post_id             = $mapify_map_location->get_map_id();
			
			if ( intval( $post_id ) > 0 ) {
				$maptiles_uploader = new Maptiles_Uploader( $post_id );
				$has_image_mode    = 'tiles_download_completed' === $maptiles_uploader->status ? true : false;
				$mapify_map        = new Mapify_Map( $post_id );
				$map_mode          = $mapify_map->get_map_mode();
			}

		}
		
		// register jQuery
		wp_enqueue_script( 'jquery' );

		// register Leafletjs style
		wp_register_style( 'acf-mapifylite-leafletjs', "{$url}admin/vendor/leaflet-1.7.1/leaflet.css", array( 'acf-input' ), '1.7.1' );
		wp_enqueue_style( 'acf-mapifylite-leafletjs' );

		// register Leafletjs script
		wp_register_script( 'acf-mapifylite-leafletjs', "{$url}admin/vendor/leaflet-1.7.1/leaflet.js", array( 'jquery', 'acf-input' ), '1.7.1' );
		wp_enqueue_script( 'acf-mapifylite-leafletjs' );
		
		// register & include js map class
		wp_register_script( 'class-openstreetmap', "{$url}admin/js/class-openstreetmap.js", array( 'jquery', 'acf-input' ), $version );
		wp_enqueue_script( 'class-openstreetmap' );
				
		// register & include JS
		wp_register_script( 'acf-mapifylite-locator', "{$url}admin/js/script-map.js", array( 'jquery', 'acf-input', 'class-openstreetmap' ), $version );
		wp_enqueue_script( 'acf-mapifylite-locator' );
		wp_localize_script( 'acf-mapifylite-locator', 'osm_var', array(
			'upload_dir'     => $wp_upload_dir['baseurl'],
			'post_id'        => $post_id,
			'has_image_mode' => $has_image_mode,
			'map_mode'       => $map_mode,
		) );
				
		// register & include CSS
		wp_register_style( 'acf-mapifylite', "{$url}admin/css/style-map.css", array( 'acf-input' ), $version );
		wp_enqueue_style( 'acf-mapifylite' );
		
	}
	
	
	/**
	 * input_admin_head()
	 *
	 * This action is called in the admin_head action on the edit screen where your field is created.
	 * Use this action to add CSS and JavaScript to assist your render_field() action.
	 *
	 * @type    action (admin_head)
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   n/a
	 * @return  n/a
	 */

	/*
		
	function input_admin_head() {
	
		
		
	}
	
	*/
	
	
	/**
   	 * input_form_data()
   	 *
   	 * This function is called once on the 'input' page between the head and footer
   	 * There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
   	 * 'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	 * seen on comments / user edit forms on the front end. This function will always be called, and includes
   	 * $args that related to the current screen such as $args['post_id']
   	 *
   	 * @type    function
   	 * @date    6/03/2014
   	 * @since   5.0.0
   	 *
   	 * @param   $args (array)
   	 * @return  n/a
   	 */
   	
   	/*
   	
   	function input_form_data( $args ) {
	   	
		
	
   	}
   	
   	*/
	
	
	/**
	 * input_admin_footer()
	 *
	 * This action is called in the admin_footer action on the edit screen where your field is created.
	 * Use this action to add CSS and JavaScript to assist your render_field() action.
	 *
	 * @type    action (admin_footer)
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   n/a
	 * @return  n/a
	 */

	/*
		
	function input_admin_footer() {
	
		
		
	}
	
	*/
	
	
	/**
	 * field_group_admin_enqueue_scripts()
	 *
	 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	 * Use this action to add CSS + JavaScript to assist your render_field_options() action.
	 *
	 * @type    action (admin_enqueue_scripts)
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   n/a
	 * @return  n/a
	 */

	/*
	
	function field_group_admin_enqueue_scripts() {
		
	}
	
	*/

	
	/**
	 * field_group_admin_head()
	 *
	 * This action is called in the admin_head action on the edit screen where your field is edited.
	 * Use this action to add CSS and JavaScript to assist your render_field_options() action.
	 *
	 * @type    action (admin_head)
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   n/a
	 * @return  n/a
	 */

	/*
	
	function field_group_admin_head() {
	
	}
	
	*/


	/**
	 * load_value()
	 *
	 * This filter is applied to the $value after it is loaded from the db
	 *
	 * @type    filter
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   $value (mixed) the value found in the database
	 * @param   $post_id (mixed) the $post_id from which the value was loaded
	 * @param   $field (array) the field array holding all the field options
	 * @return  $value
	 */
	
	function load_value( $value, $post_id, $field ) {
		$value['post_id'] = $post_id;
		
		return $value;
		
	}

	
	/**
	 * update_value()
	 *
	 * This filter is applied to the $value before it is saved in the db
	 *
	 * @type    filter
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   $value (mixed) the value found in the database
	 * @param   $post_id (mixed) the $post_id from which the value was loaded
	 * @param   $field (array) the field array holding all the field options
	 * @return  $value
	 */
		
	function update_value( $value, $post_id, $field ) {
	
		/**
		 * Update mapify item value
		 */
		if ( isset( $value['selected_lat'] ) ) {			
			$mapify_map_location = new Mapify_Map_Location( $post_id );
			$mapify_map_location->set_map_location_details( 
				$value['selected_lat'], 
				$value['selected_lng'], 
				intval( $value['zoom_level'] )
			);
		}
		
		return $value;
		
	}
	
	
	/**
	 * format_value()
	 *
	 * This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	 *
	 * @type    filter
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   $value (mixed) the value which was loaded from the database
	 * @param   $post_id (mixed) the $post_id from which the value was loaded
	 * @param   $field (array) the field array holding all the field options
	 *
	 * @return  $value (mixed) the modified value
	 */
		
	/*
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// apply setting
		if( $field['font_size'] > 12 ) { 
			
			// format the value
			// $value = 'something';
		
		}
		
		
		// return
		return $value;
	}
	
	*/
	
	
	/**
	 * validate_value()
	 *
	 * This filter is used to perform validation on the value prior to saving.
	 * All values are validated regardless of the field's required setting. This allows you to validate and return
	 * messages to the user if the value is not correct
	 *
	 * @type    filter
	 * @date    11/02/2014
	 * @since   5.0.0
	 *
	 * @param   $valid (boolean) validation status based on the value and the field's required setting
	 * @param   $value (mixed) the $_POST value
	 * @param   $field (array) the field array holding all the field options
	 * @param   $input (string) the corresponding input name for $_POST value
	 * @return  $valid
	 */
	
	/*
	
	function validate_value( $valid, $value, $field, $input ){
		
		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}
		
		
		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','acf-mapifylite'),
		}
		
		
		// return
		return $valid;
		
	}
	
	*/
	
	
	/**
	 * delete_value()
	 *
	 * This action is fired after a value has been deleted from the db.
	 * Please note that saving a blank value is treated as an update, not a delete
	 *
	 * @type    action
	 * @date    6/03/2014
	 * @since   5.0.0
	 *
	 * @param   $post_id (mixed) the $post_id from which the value was deleted
	 * @param   $key (string) the $meta_key which the value was deleted
	 * @return  n/a
	 */
	
	/*
	
	function delete_value( $post_id, $key ) {
		
		
		
	}
	
	*/
	
	
	/**
	 * load_field()
	 *
	 * This filter is applied to the $field after it is loaded from the database
	 *
	 * @type    filter
	 * @date    23/01/2013
	 * @since   3.6.0	
	 *
	 * @param   $field (array) the field array holding all the field options
	 * @return  $field
	 */
	
	function load_field( $field ) {

		// set instruction
		$field['instructions'] = __( 'Select a location on the map.', 'acf-mapifylite' );
	
		return $field;
		
	}
	
	
	/**
	 * update_field()
	 *
	 * This filter is applied to the $field before it is saved to the database
	 *
	 * @type    filter
	 * @date    23/01/2013
	 * @since   3.6.0
	 *
	 * @param   $field (array) the field array holding all the field options
	 * @return  $field
	 */
	
	/*
	
	function update_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/**
	 * delete_field()
	 *
	 * This action is fired after a field is deleted from the database
	 *
	 * @type    action
	 * @date    11/02/2014
	 * @since   5.0.0
	 *
	 * @param   $field (array) the field array holding all the field options
	 * @return  n/a
	 */
	
	/*
	
	function delete_field( $field ) {
		
		
		
	}	
	
	*/
	
	
}

// class_exists check
endif;

?>