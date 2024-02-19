<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( ! class_exists( 'Acf_Prettyroutes_Map_Status_Field' ) ) :


class Acf_Prettyroutes_Map_Status_Field extends acf_field {
		
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
		
		$this->name = 'prettyroutes_map_status';
		
		
		/**
		 * label (string) Multiple words, can include spaces, visible when selecting a field type
		 */
		
		$this->label = __( 'PrettyRoutes Map Status', 'acf-prettyroutes' );
		
		
		/**
		 * category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		 */
		
		$this->category = 'PrettyRoutes';
		
		
		/**
		 * defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		 */
		
		$this->defaults = array();
		
		
		/**
		 * l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		 * var message = acf._e('prettyroutes_map_status', 'error');
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
			'class' => "acf-prettyroutes {$field['class']}",
		);

		// Get value
		$value   = isset( $field['value'] ) ? $field['value'] : array();
		$post_id = isset( $value['post_id'] ) ? intval( $value['post_id'] ) : null;
				
		/**
		 * Load field html
		 */
		?>

		<div <?php acf_esc_attr_e( $attrs ); ?>>
			<div class="acf-prettyroutes-map-status">
				<div class="acf-osm-map-id-information">
					<label><?php esc_html_e( 'Map ID', 'acf-mapifypro' ) ?></label>
					<div><code><?php echo esc_html( $post_id ) ?></code></div>
				</div>
				<div class="acf-osm-map-shortcode-information">
					<label><?php esc_html_e( 'Shortcode (use to place your map)', 'acf-mapifypro' ) ?></label>
					<div>
						<a 
							href = "javascript:;" 
							class = "mpfy-copy-click mpfy-shortcode" 
							data-tooltip-text = "Click to copy"
							data-tooltip-text-copied = "✔ Copied to clipboard"
						><?php echo esc_html( "[route-map map_id=\"$post_id\" height=\"400\"]" ) ?></a>
					</div>
					
				</div>
			</div>
		</div>

		<?php

	}
	
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
		
		// set post_id
		$value['post_id']  = $post_id;
		
		return $value;
		
	}
	
}

// class_exists check
endif;

?>