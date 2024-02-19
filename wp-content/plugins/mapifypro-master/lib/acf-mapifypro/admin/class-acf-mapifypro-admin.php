<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mapifypro.com/
 * @since      1.0.0
 *
 * @package    Acf_Mapifypro
 * @subpackage Acf_Mapifypro/admin
 */

use \Acf_Mapifypro\Model\Maptiles_Uploader;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Acf_Mapifypro
 * @subpackage Acf_Mapifypro/admin
 * @author     Haris Ainur Rozak <harisrozak@gmail.com>
 */
class Acf_Mapifypro_Admin {

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
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = array(
			'version' => $version,
			'url'     => ACF_MAPIFYPRO_DIR_URL,
			'path'    => ACF_MAPIFYPRO_DIR_PATH,
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/style-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/script-admin.js', array( 'jquery' ), $this->version );
		
		// get current admin screen
		$sc = function_exists( 'get_current_screen' ) ? get_current_screen() : null; 

		// runs on multi-map admin settings only
		if ( $sc && 'mapifypro_page_mapifypro-multi-map' === $sc->base ) {
			wp_enqueue_script( $this->plugin_name . '-multi-map-admin', plugin_dir_url( __FILE__ ) . 'js/script-multi-map.js', array( 'jquery' ), $this->version );
		}

		// runs on 'post' post-type editor
		if ( $sc && 'post' === $sc->post_type && 'post' === $sc->base ) {
			wp_enqueue_script( $this->plugin_name . '-post-admin', plugin_dir_url( __FILE__ ) . 'js/script-post.js', array( 'jquery' ), $this->version );
		}
	}

	/**
	 * This method will include the ACF fields type class
	 *
	 * @since	1.0.0
	 * @param	int    $acf_version
	 */	
	public function include_fields( $acf_version = false ) {		
		// include
		include_once( 'class-acf-mapifypro-locator-field.php' );
		include_once( 'class-acf-mapifypro-selector-field.php' );
		include_once( 'class-acf-mapifypro-mapify-status-field.php' );
		include_once( 'class-acf-mapifypro-maptiles-uploader-field.php' );
		include_once( 'class-acf-mapifypro-gallery-uploader-field.php' );
		include_once( 'class-acf-mapifypro-pin-uploader-field.php' );
		include_once( 'class-acf-mapifypro-map-dropdown-field.php' );
		include_once( 'class-acf-mapifypro-map-drawer-field.php' );
		include_once( 'class-acf-mapifypro-map-locations-relationship-field.php' );
		include_once( 'class-acf-mapifypro-repeater-field.php' );

		// initialize the field classes
		new Acf_Mapifypro_Locator_Field( $this->settings );
		new Acf_Mapifypro_Selector_Field( $this->settings );
		new Acf_Mapifypro_Status_Field( $this->settings );
		new Acf_Mapifypro_Maptiles_Uploader_Field( $this->settings );
		new Acf_Mapifypro_Gallery_Uploader_Field( $this->settings );
		new Acf_Mapifypro_Pin_Uploader_Field( $this->settings );
		new Acf_Mapifypro_Map_Dropdown_Field( $this->settings );
		new Acf_Mapifypro_Map_Drawer_Field( $this->settings );
		new Acf_Mapifypro_Map_Locations_Relationship_Fields( $this->settings );
		new Acf_Mapifypro_Repeater_Fields( $this->settings );
	}

	/**
	 * Ajax action for maptiles-uploader to check the status
	 * 
	 * @since    1.0.0
	 */
	public function maptiles_check_status() {
		// check ajax nonce
		check_ajax_referer( 'oTZjN9Nkr4qpqX5phAN', 'ajax_nonce' );

		// init the model
		$post_id           = intval( $_POST['post_id'] );
		$maptiles_uploader = new Maptiles_Uploader( $post_id );

		// actions by current status
		switch ( $maptiles_uploader->status ) {
			case 'ready_to_upload':
				$response = array(
					'action'  => 'create_cloud_job',
					'message' => esc_html__( 'Creating the cloud job..', 'acf-mapifypro' ),
				);
				break;

			case 'cloud_job_created':
				$response = array(
					'action'  => 'check_cloud_job_status',					
					'message' => esc_html__( 'Checking the cloud job status..', 'acf-mapifypro' ),
				);
				break;

			case 'cloud_job_completed':
				$response = array(
					'action'  => 'download_maptiles',
					'message' => esc_html__( 'Continue downloading the map tiles..', 'acf-mapifypro' ),
				);
				break;

			case 'tiles_download_completed':
				$response = array(
					'action'  => null,
					'message' => esc_html__( 'All tiles has been downloaded and ready to use.', 'acf-mapifypro' ),
				);
				break;
			
			default:
				$response = array(
					'action'  => null,
					'message' => null,
				);
				break;
		}

		// send response
		wp_send_json( $response );
	}

	/**
	 * Ajax action for maptiles-uploader to a create job
	 * 
	 * @since    1.0.0
	 */
	public function maptiles_create_job() {
		// check ajax nonce
		check_ajax_referer( 'oTZjN9Nkr4qpqX5phAN', 'ajax_nonce' );

		$post_id           = intval( $_POST['post_id'] );
		$maptiles_uploader = new Maptiles_Uploader( $post_id );
		$image_url         = $maptiles_uploader->image_url;
		$url               = 'https://eo57vpgjoakniihklzm5qg75pq0rpugj.lambda-url.us-west-1.on.aws/?image_url=' . $image_url . '&action=create';
		$wp_response       = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 90 ) ); // the timeout is in seconds.
		$response          = wp_remote_retrieve_body( $wp_response );
		$response          = json_decode( $response, true );
	
		// set job_id & status on cloud_job_created
		if ( isset( $response['job_id'] ) && $response['job_id'] ) {
			$maptiles_uploader->set_job_id( $response['job_id'] );
			$maptiles_uploader->set_status( 'cloud_job_created' );
		}

		// send response
		wp_send_json( $response );
	}

	/**
	 * Ajax action for maptiles-uploader to check the job status job
	 * 
	 * @since    1.0.0
	 */
	public function maptiles_check_job_status() {
		// check ajax nonce
		check_ajax_referer( 'oTZjN9Nkr4qpqX5phAN', 'ajax_nonce' );

		$post_id           = intval( $_POST['post_id'] );
		$maptiles_uploader = new Maptiles_Uploader( $post_id );
		$job_id            = $maptiles_uploader->job_id;
		$url               = 'https://eo57vpgjoakniihklzm5qg75pq0rpugj.lambda-url.us-west-1.on.aws/?job_id=' . $job_id . '&action=status';
		$wp_response       = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 90 ) ); // the timeout is in seconds.
		$response          = wp_remote_retrieve_body( $wp_response );
		$response          = json_decode( $response, true );

		// set job_id & status on cloud_job_completed
		if ( isset( $response['success'] ) && $response['success'] && $job_id ) {
			$maptiles_uploader->set_status( 'cloud_job_completed' );
			$maptiles_uploader->set_tiles_data( $response['tiles'] );
		}

		// send response
		wp_send_json( $response );
	}

	/**
	 * Ajax action for maptiles-uploader to download the tiles
	 * 
	 * @since    1.0.0
	 */
	public function maptiles_download_tiles() {		
		// check ajax nonce
		check_ajax_referer( 'oTZjN9Nkr4qpqX5phAN', 'ajax_nonce' );
		
		// args	
		$post_id                = intval( $_POST['post_id'] );
		$failed_to_download     = array();
		$wp_upload_dir          = wp_upload_dir();		
		$current_uploaded_index = 0;
		
		// maptiles_uploader model args
		$maptiles_uploader      = new Maptiles_Uploader( $post_id );
		$downloaded_tiles_index = $maptiles_uploader->downloaded_tiles_index;
		$tiles_to_download      = $maptiles_uploader->get_tiles_to_download();
		$download_folder_name   = $maptiles_uploader->download_folder_name;

		// create the local directory if not exist
		if ( ! empty( $wp_upload_dir['basedir'] ) ) {
			$upload_dir = $wp_upload_dir['basedir'] . '/' . $download_folder_name . '/' . $post_id;
				
			if ( ! file_exists( $upload_dir ) ) {
				wp_mkdir_p( $upload_dir );
			}
		}
		else {
			wp_die( __( 'Error getting uploads folder data', 'acf-mapifypro' ) );
		}

		// flush the directory before fill it with the new batch
		if ( $downloaded_tiles_index <= 0 ) {
			$files = scandir( $upload_dir );
			
			foreach ( $files as $file ) {
				/**
				 * Remove unused file
				 * Suppress the warning message if the file didn't exist
				 */
				@unlink( $upload_dir . '/' . $file );
			}
		}

		// download the images
		foreach ( $tiles_to_download as $tile_url ) {
			$destination = $upload_dir . '/' .  basename( $tile_url ); 		
			
			// must be a file path, not a directory
			if ( ! is_dir( $destination ) ) {
				$downloaded = file_put_contents( $destination, file_get_contents( $tile_url ) );

				if ( ! $downloaded ) {
					$failed_to_download[] = $tile_url;
				}
			}

			// indexing
			$current_uploaded_index++;
		}

		// set downloaded index
		$maptiles_uploader->set_downloaded_tiles_index( $downloaded_tiles_index + $current_uploaded_index );

		// set the finished status if it's confirmed
		if ( $maptiles_uploader->downloaded_tiles_index >= count( $maptiles_uploader->tiles_data ) && count( $maptiles_uploader->tiles_data ) > 0 ) {
			$maptiles_uploader->set_status( 'tiles_download_completed' );
		}

		// send response
		wp_send_json( array(
			'status'     => $maptiles_uploader->status, 
			'percentage' => $maptiles_uploader->downloaded_percentage,
		) );
	}

	/**
	 * Remove maptiles downloaded tiles on delete post
	 * 
	 * @since    1.1.0
	 * @param    integer    $post_id    In progress deleting post's ID.
	 * @param    WP_Post    $post       In progress deleting WP_Post object.
	 */
	public function maptiles_delete_downloaded_tiles( $post_id, $post ) {
		// must be the map post type
		if ( 'map' !== $post->post_type ) return;

		$wp_upload_dir     = wp_upload_dir();	
		$maptiles_uploader = new Maptiles_Uploader( $post_id );
		$upload_dir        = $wp_upload_dir['basedir'] . '/' .  $maptiles_uploader->download_folder_name . '/' . $post_id;

		if ( file_exists( $upload_dir ) ) {
			$existed_files = scandir( $upload_dir );

			// flush the directory
			if ( is_array( $existed_files ) ) {
				foreach ( $existed_files as $file ) {
					/**
					 * Remove unused file
					 * Suppress the warning message if the file didn't exist
					 */
					@unlink( $upload_dir . '/' . $file );
				}
			}

			// delete directory
			rmdir( $upload_dir );
		}
	}

}
