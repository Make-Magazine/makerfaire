<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mapifypro.com/
 * @since      1.0.0
 *
 * @package    Acf_Mapifylite
 * @subpackage Acf_Mapifylite/admin
 */

use \Acf_Mapifylite\Model\Maptiles_Uploader;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Acf_Mapifylite
 * @subpackage Acf_Mapifylite/admin
 * @author     Haris Ainur Rozak <harisrozak@gmail.com>
 */
class Acf_Mapifylite_Admin {

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
			'url'     => ACF_MAPIFYLITE_DIR_URL,
			'path'    => ACF_MAPIFYLITE_DIR_PATH,
		);
	}

	/**
	 * This method will include the ACF fields type class
	 *
	 * @since	1.0.0
	 * @param	int    $acf_version
	 */	
	public function include_fields( $acf_version = false ) {		
		// include
		include_once( 'class-acf-mapifylite-locator-field.php' );
		include_once( 'class-acf-mapifylite-selector-field.php' );
		include_once( 'class-acf-mapifylite-mapify-status-field.php' );
		include_once( 'class-acf-mapifylite-maptiles-uploader-field.php' );
		include_once( 'class-acf-mapifylite-gallery-uploader-field.php' );
		include_once( 'class-acf-mapifylite-pin-uploader-field.php' );
		include_once( 'class-acf-mapifylite-map-dropdown-field.php' );

		// initialize the field classes
		new Acf_Mapifylite_Locator_Field( $this->settings );
		new Acf_Mapifylite_Selector_Field( $this->settings );
		new Acf_Mapifylite_Status_Field( $this->settings );
		new Acf_Mapifylite_Maptiles_Uploader_Field( $this->settings );
		new Acf_Mapifylite_Gallery_Uploader_Field( $this->settings );
		new Acf_Mapifylite_Pin_Uploader_Field( $this->settings );
		new Acf_Mapifylite_Map_Dropdown_Field( $this->settings );
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
					'message' => esc_html__( 'Creating the cloud job..', 'acf-mapifylite' ),
				);
				break;

			case 'cloud_job_created':
				$response = array(
					'action'  => 'check_cloud_job_status',					
					'message' => esc_html__( 'Checking the cloud job status..', 'acf-mapifylite' ),
				);
				break;

			case 'cloud_job_completed':
				$response = array(
					'action'  => 'download_maptiles',
					'message' => esc_html__( 'Continue downloading the map tiles..', 'acf-mapifylite' ),
				);
				break;

			case 'tiles_download_completed':
				$response = array(
					'action'  => null,
					'message' => esc_html__( 'All tiles has been downloaded and ready to use.', 'acf-mapifylite' ),
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
		$url               = 'http://72.14.186.12/service.php?image_url=' . $image_url . '&action=create';
		$wp_response       = wp_remote_get( $url, array( 'sslverify' => false ) );
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
		$url               = 'http://72.14.186.12/service.php?job_id=' . $job_id . '&action=status';
		$wp_response       = wp_remote_get( $url, array( 'sslverify' => false ) );
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
			wp_die( __( 'Error getting uploads folder data', 'acf-mapifylite' ) );
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

}
