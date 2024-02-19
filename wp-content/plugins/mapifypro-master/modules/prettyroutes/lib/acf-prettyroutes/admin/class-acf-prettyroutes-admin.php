<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mapifypro.com/
 * @since      1.0.0
 *
 * @package    Acf_Prettyroutes
 * @subpackage Acf_Prettyroutes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Acf_Prettyroutes
 * @subpackage Acf_Prettyroutes/admin
 * @author     Haris Ainur Rozak <https://support.mapifypro.com/>
 */
class Acf_Prettyroutes_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ACF field base settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array     $settings
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = array(
			'version' => $version,
			'url'     => ACF_PRETTYROUTES_DIR_URL,
			'path'    => ACF_PRETTYROUTES_DIR_PATH,
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/acf-prettyroutes-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/acf-prettyroutes-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * This method will include the ACF fields type class
	 *
	 * @since	1.0.0
	 * @param	int    $acf_version
	 */	
	public function include_fields( $acf_version = false ) {		
		// include
		include_once( 'class-acf-prettyroutes-route-field.php' );
		include_once( 'class-acf-prettyroutes-map-status-field.php' );
		include_once( 'class-acf-prettyroutes-map-location-field.php' );

		// initialize the field classes
		new Acf_Prettyroutes_Route_field( $this->settings );
		new Acf_Prettyroutes_Map_Status_Field( $this->settings );
		new Acf_Prettyroutes_Map_Location_Field( $this->settings );
	}

	/**
	 * Handle ajax action: create_connected_route
	 */
	public function ajax_create_connected_route() {
		check_ajax_referer( 'prettyroutes_acf_admin', 'wpnonce' );

		$post_ID = absint( $_POST['id'] );

		/**
		 * Mark for the next page (route-editor) reload to run the create_connected_route_on_next_reload action
		 */
		update_post_meta( $post_ID, 'prettyroutes_create_connected_route', 'on_next_reload' );
		
		wp_send_json( array(
			'action' => 'ajax_create_connected_route',
			'id'     => $post_ID,
		) );
	}

	/**
	 * Actions to do after the current editor page is reloaded:
	 * - Create a new map route with the same data with the current post (map-route) data
	 * - Edit the waypoints. Remove all of them but leave the last waypoint as a start point
	 * - Redirect to the new map-route editor page
	 */
	public function create_connected_route_on_next_reload() {
		$current_screen = get_current_screen();
		$post_ID        = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : false;
		
		if( "post" !== $current_screen->base || "route" !== $current_screen->id || ! $post_ID ) {
			return false;
		}		
		
		if ( get_post_meta( $post_ID, 'prettyroutes_create_connected_route', true ) !== 'on_next_reload' ) {
			return false;
		}

		// Remove the sign.
		delete_post_meta( $post_ID, 'prettyroutes_create_connected_route' );

		// Create new route.
		$new_route = array(
			'post_type'   => 'route',
			'post_status' => 'publish',
			'post_title'  => $this->get_unique_post_title( get_the_title( $post_ID ) ),
		);

		$new_route_id = wp_insert_post( $new_route );

		if ( is_wp_error( $new_route_id ) ) {
			error_log( $new_route_id->get_error_message() );
			return false;
		}

		// Save the new route ID to the parent route data.
		update_post_meta( $post_ID, 'prettyroutes_connected_route', $new_route_id );

		// Save the parent route ID to the new route data.
		update_post_meta( $new_route_id, 'prettyroutes_origin_route', $post_ID );

		// Set the route field data.
		update_field( '_route_map', get_field( '_route_map', $post_ID ), $new_route_id );
		update_field( '_route_type', get_field( '_route_type', $post_ID ), $new_route_id );
		update_field( '_route_color', get_field( '_route_color', $post_ID ), $new_route_id );
		update_field( '_route_color', get_field( '_route_color', $post_ID ), $new_route_id );

		// Set the map route map data.
		update_post_meta( $new_route_id, '_route_route-lat', get_post_meta( $post_ID, '_route_route-lat', true ) );
		update_post_meta( $new_route_id, '_route_route-lng', get_post_meta( $post_ID, '_route_route-lng', true ) );
		update_post_meta( $new_route_id, '_route_route-zoom', get_post_meta( $post_ID, '_route_route-zoom', true ) );

		$waypoints            = get_post_meta( $post_ID, '_route_route', true );
		$waypoints_array      = explode( '|', $waypoints);
		$last_waypoints_index = count($waypoints_array) < 25 ? count($waypoints_array) - 1 : 24; // Maximum allowed waypoints is 25.
		$last_waypoints       = $waypoints_array[$last_waypoints_index];

		update_post_meta( $new_route_id, '_route_route', $last_waypoints );

		// Duplicate post tags.
		$this->copy_tags_to_post( $post_ID, $new_route_id );

		// Redirect to the new map-route editor page.
		wp_safe_redirect( $this->get_edit_page_url( $new_route_id ) );
		exit;
	}

	/**
	 * Get edit page url
	 * 
	 * @param Integer $post_id
	 * @return String Edit page url.
	 */
	private function get_edit_page_url( $post_id ) {
		return add_query_arg( array( 
			'post'   => absint( $post_id ), 
			'action' => 'edit', 
		), admin_url( 'post.php' ) );
	}

	/**
	 * Get unique post title for a new post
	 * 
	 * @param String $original_title
	 * @return String An unique post title.
	 */
	private function get_unique_post_title( $original_title ) {
		global $wpdb; // Assuming you are working with WordPress, adjust accordingly if not
	
		// Extract the number from the original title using a regular expression
		if ( preg_match( '/\((\d+)\)$/', $original_title, $matches ) ) {
			$number    = intval( $matches[1] );
			$new_title = preg_replace( '/\(\d+\)$/', '(' . ($number + 1) . ')', $original_title );
		} else {
			$new_title = $original_title . ' (2)';
		}
	
		$post_id = 0;
	
		while ( $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s", $new_title ) ) ) {
			// If a post with the new title already exists, increment the number
			if ( preg_match( '/\((\d+)\)$/', $new_title, $matches ) ) {
				$number    = intval( $matches[1] );
				$new_title = preg_replace( '/\(\d+\)$/', '(' . ( $number + 1 ) . ')', $new_title );
			}
		}
	
		return $new_title;
	}

	/**
	 * Copy tags to post
	 * 
	 * @param Integer $source_post_id
	 * @param Integer $target_post_id
	 */
	private function copy_tags_to_post( $source_post_id, $target_post_id ) {
		$tags_args = array(
			'hide_empty' => false,
		);
			
		// Get the tags of the source post
		$source_tags = wp_get_post_terms( $source_post_id, 'location-tag', $tags_args );

		// Exit if there are no tags in the source post
		if ( empty( $source_tags ) ) {
			return;
		}
		
		// Set the tags of the target post to the merged array
		wp_set_post_terms( $target_post_id, wp_list_pluck( $source_tags, 'term_id' ), 'location-tag' );
	}
}