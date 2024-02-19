<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('Acf_Prettyroutes_Route_field') ) :


class Acf_Prettyroutes_Route_field extends acf_field {
		
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
		$this->name = 'prettyroutes_route';
				
		/**
		 * label (string) Multiple words, can include spaces, visible when selecting a field type
		 */		
		$this->label = __( 'PrettyRoutes Route', 'acf-prettyroutes' );
				
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
		global $post;

		$connected_route_id = absint( get_post_meta( $post->ID, 'prettyroutes_connected_route', true ) );
		$origin_route_id    = absint( get_post_meta( $post->ID, 'prettyroutes_origin_route', true ) );

		// Check the connected_route_id
		if ( is_null( get_post( $connected_route_id ) ) ) {
			$connected_route_id = false;
		}

		// Check the origin_route_id
		if ( is_null( get_post( $origin_route_id ) ) ) {
			$origin_route_id = false;
		}

		// Attrs.
		$attrs = array(
			'id'    => $field['id'],
			'class' => "acf-prettyroutespro acf-prettyroutes-route-field {$field['class']}",
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
				name             = "<?php echo esc_attr( $field['name'] . '[route]' ) ?>" 
				value            = "<?php echo esc_attr( $value['route'] ) ?>" 
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
					<input type="text" id="acf-osm-search-keywords" placeholder="<?php esc_attr_e( 'Search for location (or double-click on map)', 'acf-prettyroutes' ) ?>">
				</div>
				<div class="acf-osm-search-button">
					<button class="button" id="acf-osm-search-button"><?php esc_html_e( 'Search on Map', 'acf-prettyroutes' ) ?></button>
				</div>
			</div>

			<div id='acf-osm-map-search-results'></div>

			<!-- Map canvas -->
			<div class="acf-map-canvas"></div>

			<!-- A bottom bar and a button for handling the waypoints counter -->
			<div class="prettyroutes-waypoints-counter">
				<div class="prettyroutes-bottom-bar-counter"></div>
				
				<!-- Origin route button -->
				<?php if ( $origin_route_id ) : ?>
					<a href="<?php echo get_edit_post_link( $origin_route_id ) ?>" class="button">❮❮ &nbsp;<?php echo esc_html( 'Edit Origin Route', 'acf-prettyroutes' ) ?></a>
				<?php endif; ?>

				<!-- Connected route button -->
				<?php if ( $connected_route_id ) : ?>
					<a href="<?php echo get_edit_post_link( $connected_route_id ) ?>" class="button"><?php echo esc_html( 'Edit Connected Route', 'acf-prettyroutes' ) ?>&nbsp; ❯❯</a>
				<?php else : ?>					
					<div type="button" class="prettyroutes-create-connected-route-button button"><?php echo esc_html( 'Create A New Connected Route', 'acf-prettyroutes' ) ?></div>
				<?php endif; ?>
			</div>
		</div>

		<?php

		// Generate a nonce field.
		wp_nonce_field( 'prettyroutes_acf_admin', 'prettyroutes_acf_admin_nonce' );

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
		$route      = get_post_meta( $post_id, '_route_route', true );
		$lattitude  = get_post_meta( $post_id, '_route_route-lat', true );
		$longitude  = get_post_meta( $post_id, '_route_route-lng', true );
		$zoom_level = get_post_meta( $post_id, '_route_route-zoom', true );

		return array(
			'route'      => empty( $route ) ? '' : $route, 
			'lattitude'  => empty( $lattitude ) ? $this->defaults['lattitude'] : $lattitude, 
			'longitude'  => empty( $longitude ) ? $this->defaults['longitude'] : $longitude, 
			'zoom_level' => empty( $zoom_level ) ? $this->defaults['zoom_level'] : $zoom_level, 
		);		
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
		if ( isset( $value['route'] ) ) {
			update_post_meta( $post_id, '_route_route', $value['route'] );
			update_post_meta( $post_id, '_route_route-lat', $value['lattitude'] );
			update_post_meta( $post_id, '_route_route-lng', $value['longitude'] );
			update_post_meta( $post_id, '_route_route-zoom', $value['zoom_level'] );
		}
		
		return $value;		
	}	
	
}

// class_exists check
endif;

?>