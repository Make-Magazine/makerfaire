<?php

use \Acf_Mapifylite\Model\Mapify_Map_Location;

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( ! class_exists( 'Acf_Mapifylite_Gallery_Uploader_Field' ) ) :


class Acf_Mapifylite_Gallery_Uploader_Field extends acf_field {
		
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
		
		$this->name = 'mapify_gallery_uploader';
		
		
		/**
		 * label (string) Multiple words, can include spaces, visible when selecting a field type
		 */
		
		$this->label = __( 'Mapify Gallery Uploader', 'acf-mapifylite' );
		
		
		/**
		 * category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		 */
		
		$this->category = 'Mapify';
		
		
		/**
		 * defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		 */
		
		$this->defaults = array();
		
		
		/**
		 * l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		 * var message = acf._e('mapify_gallery_uploader', 'error');
		 */
		
		$this->l10n = array();
		
		
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
		// Get value & attr
		$value        = isset( $field['value'] ) ? $field['value'] : array();
		$image_ids    = isset( $value['image_id'] ) && is_array( $value['image_id'] ) ? $value['image_id'] : array();
		$image_urls   = isset( $value['image_url'] ) && is_array( $value['image_url'] ) ? $value['image_url'] : array();
		$gallery      = array();
		$empty_notice = sprintf( '<span>%s</span>', __( 'No images yet. Press <b>Add Images</b> button to start.', 'acf-mapifylite' ) );
	
		foreach ( $image_urls as $key => $value) {
			$image_id      = intval( $image_ids[ $key ] );
			$image_url     = esc_url( $image_urls[ $key ] );
			$wp_image_src  = $image_id ? wp_get_attachment_image_src( $image_id, 'medium' )[0] : null;
			$image_thumb   = $wp_image_src ? $wp_image_src : $image_url;
			$is_custom_url = $wp_image_src ? false : true;

			// Set the gallery data
			$gallery[]     = array(
				'image_id'      => $image_id,
				'image_url'     => $image_url,
				'image_thumb'   => $image_thumb,
				'is_custom_url' => $is_custom_url,
			);
		}

		// Init the gallery library
		new Acf_Mapifylite_Fegallery( array(
			'name'           => 'fegallery-image',
			'button'         => '#fegallery-add-slide',
			'container'      => '#fegallery-image-container',
			'empty_notice'   => $empty_notice,
			'acf_field_name' => esc_js( $field['name'] ),
		) );

		/**
		 * Load admin fields
		 */
		include_once( 'partials/mapify-gallery-uploader-field-display.php' );

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
		
		global $post_type;

		// should be loaded only on post type map-location
		if ( 'map-location' !== $post_type ) return false;
		
		// vars
		$url       = $this->settings['url'];
		$version   = $this->settings['version'];
		
		// register jQuery & some jQuery UI libraries
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-dialog' );	

		// register jQuery UI dialog style
    	wp_enqueue_style( 'wp-jquery-ui-dialog' );
		
		// register & include JS
		wp_register_script( 'acf-mapifylite-gallery-uploader', "{$url}admin/js/script-gallery-uploader.js", array( 'jquery', 'acf-input' ), $version );
		wp_enqueue_script( 'acf-mapifylite-gallery-uploader' );
				
		// register & include fegallery CSS
		wp_register_style( 'acf-fegallery', "{$url}admin/css/style-fegallery.css", array( 'acf-input' ), $version );
		wp_enqueue_style( 'acf-fegallery' );
		
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

		$mapify_map_location = new Mapify_Map_Location( $post_id );
		$gallery_images      = $mapify_map_location->get_gallery_images();
		$image_ids           = array();
		$image_urls          = array();

		if ( $gallery_images ) {
			foreach ( $gallery_images as $gallery_image ) {
				$image_ids[]  = 0;
				$image_urls[] = $gallery_image;
			}
		}

		// set value
		$value['image_id']  = $image_ids;
		$value['image_url'] = $image_urls;
	
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
		
		if ( isset( $value['image_url'] ) ) {
			$mapify_map_location = new Mapify_Map_Location( $post_id );
			$mapify_map_location->set_gallery_images( $value['image_url'] );
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
		$field['instructions'] = __( 'Add your desired images from the gallery. You can always edit the image URL after it.', 'acf-mapifylite' );
	
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