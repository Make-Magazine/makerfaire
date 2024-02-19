<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('Acf_Prettyroutes_Map_Location_Field') ) :


class Acf_Prettyroutes_Map_Location_Field extends acf_field {
		
	/**
	 *  __construct
	 *
	 *  This function will setup the field type data
	 *
	 *  @type	function
	 *  @date	5/03/2014
	 *  @since	5.0.0
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */		
	function __construct( $settings ) {		
		/**
		 * name (string) Single word, no spaces. Underscores allowed
		 */		
		$this->name = 'prettyroutes_map_location';
				
		/**
		 * label (string) Multiple words, can include spaces, visible when selecting a field type
		 */		
		$this->label = __( 'PrettyRoutes Map Location', 'acf-prettyroutes' );
				
		/**
		 * category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		 */		
		$this->category = 'PrettyRoutes';

		/**
		 * defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		 */		
		$this->defaults = array(
			'lattitude'  => '-37.847097612887985',
			'longitude'  => '144.97131142516656',
			'zoom_level' => 7,
		);
				
		/**
		 * settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		 */		
		$this->settings = $settings;
				
		/**
		 * run parent construct method
		 */
    	parent::__construct();    	
	}
		
	/**
	 *  render_field_settings()
	 *
	 *  Create extra settings for your field. These are visible when editing a field
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field (array) the $field being edited
	 *  @return	n/a
	 */	
	function render_field_settings( $field ) {
		
		/**
		 *  acf_render_field_setting
		 *
		 *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		 *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		 *
		 *  More than one setting can be added by copy/paste the above code.
		 *  Please note that you must also have a matching $defaults value for the field name (font_size)
		 */
		
		// Default Lattitude
		acf_render_field_setting( $field, array(
			'label'        => __( 'Default Lattitude', 'acf-prettyroutes' ),
			'instructions' => __( 'Initial lattitude value for the map', 'acf-prettyroutes' ),
			'type'         => 'text',
			'name'         => 'lattitude',
		));

		// Default Longitude
		acf_render_field_setting( $field, array(
			'label'        => __( 'Default Longitude', 'acf-prettyroutes' ),
			'instructions' => __( 'Initial longitude value for the map', 'acf-prettyroutes' ),
			'type'         => 'text',
			'name'         => 'longitude',
		));

		// Default Zoom Level
		acf_render_field_setting( $field, array(
			'label'        => __( 'Default Zoom Level', 'acf-prettyroutes' ),
			'instructions' => __( 'Initial zoom level value for the map', 'acf-prettyroutes' ),
			'type'         => 'number',
			'name'         => 'zoom_level',
		));

	}
			
	/**
	 *  render_field()
	 *
	 *  Create the HTML interface for your field
	 *
	 *  @param	$field (array) the $field being rendered
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field (array) the $field being edited
	 *  @return	n/a
	 */	
	function render_field( $field ) {

		// Attrs.
		$attrs = array(
			'id'    => $field['id'],
			'class' => "acf-prettyroutespro acf-prettyroutes-map-location-field {$field['class']}",
		);

		// Get value
		$value = $field['value'];

		/**
		 * Load field html
		 */
		?>

		<div <?php acf_esc_attr_e( $attrs ); ?>>
			<input 
				type             = "text" 
				name             = "<?php echo esc_attr( $field['name'] . '[coordinates]' ) ?>" 
				value            = "<?php echo esc_attr( $value['coordinates'] ) ?>" 
				class            = "acf-map-field" 
				data-zoom        = "<?php echo esc_attr( $value['zoom_level'] ) ?>" 
				data-default-lat = "<?php echo esc_attr( $value['lattitude'] ) ?>" 
				data-default-lng = "<?php echo esc_attr( $value['longitude'] ) ?>" 
			>
		
			<input type="text" name="<?php echo esc_attr( $field['name'] . '[lattitude]' ) ?>" value="<?php echo esc_attr( $value['lattitude'] ) ?>" id="centered_lat">
			<input type="text" name="<?php echo esc_attr( $field['name'] . '[longitude]' ) ?>" value="<?php echo esc_attr( $value['longitude'] ) ?>" id="centered_lng">
			<input type="text" name="<?php echo esc_attr( $field['name'] . '[zoom_level]' ) ?>" value="<?php echo esc_attr( $value['zoom_level'] ) ?>" id="zoom_level">

			<!-- Search field -->
			<div class="acf-osm-search">
				<div class="acf-osm-search-input">
					<input type="text" class="acf-map-location-search" placeholder="<?php esc_attr_e( 'Enter your search keywords here..', 'acf-prettyroutes' ) ?>">
				</div>
				<div class="acf-osm-search-button">
					<button class="button acf-map-search-btn"><?php esc_html_e( 'Search on Map', 'acf-prettyroutes' ) ?></button>
				</div>
			</div>

			<!-- Map canvas -->
			<div class="acf-map-canvas"></div>
		</div>

		<?php

	}	

	/**
	 *  load_value()
	 *
	 *  This filter is applied to the $value after it is loaded from the db
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value (mixed) the value found in the database
	 *  @param	$post_id (mixed) the $post_id from which the value was loaded
	 *  @param	$field (array) the field array holding all the field options
	 *  @return	$value
	 */	
	function load_value( $value, $post_id, $field ) {
		$lattitude  = get_post_meta( $post_id, '_route_map_center-lat', true );
		$longitude  = get_post_meta( $post_id, '_route_map_center-lng', true );
		$zoom_level = get_post_meta( $post_id, '_route_map_center-zoom', true );
		$values     = array(
			'lattitude'  => empty( $lattitude ) ? $this->defaults['lattitude'] : $lattitude, 
			'longitude'  => empty( $longitude ) ? $this->defaults['longitude'] : $longitude, 
			'zoom_level' => empty( $zoom_level ) ? $this->defaults['zoom_level'] : $zoom_level, 
		);

		// set coordinates
		$values['coordinates'] = sprintf( '%s,%s,%s', $values['lattitude'], $values['longitude'], $values['zoom_level'] );

		return $values;
	}
		
	/**
	 *  update_value()
	 *
	 *  This filter is applied to the $value before it is saved in the db
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value (mixed) the value found in the database
	 *  @param	$post_id (mixed) the $post_id from which the value was loaded
	 *  @param	$field (array) the field array holding all the field options
	 *  @return	$value
	 */	
	function update_value( $value, $post_id, $field ) {
		/**
		 * Update map values
		 */
		if ( isset( $value['coordinates'] ) ) {
			update_post_meta( $post_id, '_route_map_center', $value['coordinates'] );
			update_post_meta( $post_id, '_route_map_center-lat', $value['lattitude'] );
			update_post_meta( $post_id, '_route_map_center-lng', $value['longitude'] );
			update_post_meta( $post_id, '_route_map_center-zoom', $value['zoom_level'] );
		}
		
		return $value;		
	}	
	
}

// class_exists check
endif;

?>