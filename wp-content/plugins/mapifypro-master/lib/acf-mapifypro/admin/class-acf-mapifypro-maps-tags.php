<?php

/**
 * The class responsible for `Maps Tags` addition field below `Maps` ACF Relationship field.
 * 
 * @since    1.0.0
 */

/**
 * class Acf_Mapifypro_Maps_Tags
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Maps_Tags {

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post_type;

		$supported_post_types = mpfy_get_supported_post_types();

		// should be loaded only on map locations supported post types
		if ( ! in_array( $post_type , $supported_post_types ) ) return false;

		// register jQuery
		wp_enqueue_script( 'jquery' );
				
		// register & include JS
		wp_register_script( 'acf-mapifypro-maps-tags', plugin_dir_url( __FILE__ ) . "js/script-maps-tags.js", array( 'jquery', 'acf-input' ), $this->version );
		wp_enqueue_script( 'acf-mapifypro-maps-tags' );
	}

	/**
	 * Render additional html after `Maps` field
	 * 
	 * @since    1.0.0
	 * @param    array    $field    The field array containing all settings.
	 */
	public function render_after_maps_field( $field ) {
		// nonce field
		wp_nonce_field( 'tHM5CwZYf5Lwgg', 'acf_mapifypro_maps_tags_nonce' );

		// has any value or empty
		if ( is_array( $field['value'] ) && ! empty( $field['value'] ) ) {
			$map_ids   = $field['value'];
			$tags_data = $this->get_tags_data( $map_ids );
			$maps_tags = $this->print_map_tags( $tags_data );
		} else {
			$maps_tags = '';
		}

		// maps tags container
		printf( 
			'<p class="description">%s</p><div id="acf-mapifypro-maps-tags">%s</div>', 
			__( 
				'Choose the maps where you would like to add this location. Tags associated with the selected maps will appear below.', 
				'acf-mapifypro' 
			),
			$maps_tags
		);

	}

	/**
	 * Ajax response for acf_mapifypro_get_maps_tags calls
	 * 
	 * @since    1.0.0
	 */
	public function ajax_get_maps_tags() {
		// check ajax nonce
		check_ajax_referer( 'tHM5CwZYf5Lwgg', 'ajax_nonce' );

		// variables
		$map_ids   = isset( $_POST['map_ids'] ) ? $_POST['map_ids'] : array();
		$tags_data = $this->get_tags_data( $map_ids );

		echo $this->print_map_tags( $tags_data );
		wp_die();
	}

	/**
	 * Get tags data by map_ids
	 * 
	 * @since    1.0.0
	 * @param    array    $map_ids    Mapify map IDs
	 * @return   array    Formatted tags data
	 */
	public function get_tags_data( $map_ids ) {
		$tags_data = array();

		// get location tags by map_id
		foreach ( $map_ids as $map_id ) {
			$map_name  = get_the_title( $map_id );
			$term_list = wp_get_post_terms( $map_id, 'location-tag', array( 'fields' => 'all' ) );
			$tag_data  = array(
				'name' => $map_name, 
				'tags' => array(),
			);

			// iterate term_list
			foreach ( $term_list as $term ) {
				$tag_data['tags'][ $term->term_id ] = $term->name;
			}

			$tags_data[ $map_id ] = $tag_data;
		}

		return $tags_data;
	}

	/**
	 * Print map tags
	 * 
	 * @since    1.0.0
	 * @param    array    $tag_data   Formatted tags data
	 */
	public function print_map_tags( $tags_data ) {
		$html = '';

		foreach ( $tags_data as $map_id => $tag_data ) {
			$tags  = '';

			foreach ( $tag_data['tags'] as $term_id => $term_name ) {
				$tags .= sprintf( 
					"<a href='javascript:;' class='acf-map-tag-link' data-id='%d' data-name='%s'>%s</a>", 
					esc_attr( $term_id ), esc_attr( $term_name ), 
					esc_html( $term_name )
				);
			}

			$html .= sprintf( "<p>From <i>%s</i> :</p>%s", esc_html( $tag_data['name'] ), $tags );
		}

		return sprintf( '<div class="acf-label"><label>%s:</label></div>%s', __( 'Suggested Map Location Tags', 'acf-mapifypro' ), $html );
	}
}