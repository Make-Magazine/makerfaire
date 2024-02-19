<?php

/**
 * The model class responsible for maptiles-uploader process
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifylite\Model;

/**
 * class Maptiles_Uploader
 * 
 * @since    1.0.0
 */
class Maptiles_Uploader {

	/**
	 * Post ID of the current maptiles uploader field
	 * 
	 * @since    1.0.0
	 * @var      int
	 */
	public $post_id;

	/**
	 * The image URL for the tiles
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	public $image_url;

	/**
	 * Status of the maptiles uploader
	 * There are 5 statuses: idle, ready_to_upload, cloud_job_created, cloud_job_completed, and tiles_download_completed
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	public $status;

	/**
	 * The cloud job ID
	 * We will get this once the cloud job has been created
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	public $job_id;

	/**
	 * The cloud tiles data
	 * We will get this once the cloud job has been completed
	 * 
	 * @since    1.0.0
	 * @var      array
	 */
	public $tiles_data;

	/**
	 * The array index of downloaded tiles images
	 * This will be counting on tiles downloading progress
	 * 
	 * @since    1.0.0
	 * @var      int
	 */
	public $downloaded_tiles_index;

	/**
	 * How many tiles should be downloaded each batch
	 * 
	 * @since    1.0.0
	 * @var      int
	 */
	public $tiles_count_per_batch;

	/**
	 * Tiles downloaded percentage
	 * 
	 * @since    1.0.0
	 * @var      int
	 */
	public $downloaded_percentage;

	/**
	 * Download folder name
	 * This will be lies under WordPress `uploads` folder
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	public $download_folder_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $post_id ) {
		$this->post_id                = $post_id;
		$this->image_url              = $this->get_image_url();
		$this->status                 = $this->get_status();
		$this->job_id                 = $this->get_job_id();
		$this->tiles_data             = $this->get_tiles_data();
		$this->downloaded_tiles_index = $this->get_downloaded_tiles_index();
		$this->downloaded_percentage  = $this->get_downloaded_percentage();
		$this->download_folder_name   = $this->get_download_folder_name();
		$this->tiles_count_per_batch  = 5;
	}

	/**
	 * Reset maptiles data to begin a new image processing
	 * 
	 * @since    1.0.0
	 */
	public function reset() {
		$this->set_job_id( '' );
		$this->set_tiles_data( '' );
		$this->set_downloaded_tiles_index( 0 );
	}
	
	/**
	 * Get status
	 * 
	 * @since    1.0.0
	 * @return   mixed    null|string
	 */
	public function get_status() {
		$status = get_post_meta( $this->post_id, 'osm_maptiles_status', true );
		return $this->null_on_empty( $status );
	}

	/**
	 * Get image url for the tiles
	 * 
	 * @since    1.0.0
	 * @return   mixed    null|string
	 */
	public function get_image_url() {
		$image_url = get_post_meta( $this->post_id, 'osm_maptiles_image_url', true );
		return $this->null_on_empty( $image_url );
	}

	/**
	 * Get job ID
	 * 
	 * @since    1.0.0
	 * @return   mixed    null|string
	 */
	public function get_job_id() {
		$job_id = get_post_meta( $this->post_id, 'osm_maptiles_job_id', true );
		return $this->null_on_empty( $job_id );
	}

	/**
	 * Get tiles data
	 * 
	 * @since    1.0.0
	 * @return   mixed    null|array
	 */
	public function get_tiles_data() {
		$tiles_data = get_post_meta( $this->post_id, 'osm_maptiles_tiles_data', true );
		return is_array( $tiles_data ) ? $tiles_data : array();
	}

	/**
	 * Get downloaded tiles count
	 * 
	 * @since    1.0.0
	 * @return   int
	 */
	public function get_downloaded_tiles_index() {
		$downloaded_tiles_index = get_post_meta( $this->post_id, 'osm_maptiles_downloaded_tiles_index', true );
		return intval( $downloaded_tiles_index );
	}

	/**
	 * Get downloaded tiles percentage
	 * 
	 * @since    1.0.0
	 * @return   int
	 */
	public function get_downloaded_percentage() {
		$downloaded_count = $this->downloaded_tiles_index + 1;
		$total_tiles      = count( $this->tiles_data );
		$percentage       = $total_tiles > 0 ? round( ( $downloaded_count / $total_tiles ) * 100 ) : 0;

		return $percentage;
	}

	/**
	 * Get download folder name
	 * This will be lies under WordPress `uploads` folder
	 * 
	 * @since    1.0.0
	 * @return   string
	 */
	public function get_download_folder_name() {
		$job_id = get_post_meta( $this->post_id, 'osm_maptiles_download_folder_name', true );
		return $this->null_on_empty( $job_id );
	}

	/**
	 * Set status
	 * 
	 * @since    1.0.0
	 * @param    string
	 */
	public function set_status( $status ) {
		$this->status = sanitize_text_field( $status );
		update_post_meta( $this->post_id, 'osm_maptiles_status', $this->status );

		// updating frontend image-mode status
		switch ( $this->status ) {
			case 'ready_to_upload':
				$status  = 'processing';
				$message = sprintf( 'Your image has not been processed, yet (code: %s)', $this->status );
				break;

			case 'cloud_job_created':
				$status  = 'processing';
				$message = sprintf( 'Your image has not been processed, yet (code: %s)', $this->status );
				break;

			case 'cloud_job_completed':
				$status  = 'processing';
				$message = sprintf( 'Your image has not been processed, yet (code: %s)', $this->status );
				break;

			case 'tiles_download_completed':
				$status  = 'ready';
				$message = 'Image processed and ready for use.';
				break;

			default:
				$status = null;
				break;
		}
		
		// set status & message
		if ( $status ) {
			update_post_meta( $this->post_id, '_map_tileset_status', $status );
			update_post_meta( $this->post_id, '_map_tileset_message', $message );
		}
	}

	/**
	 * Set image url for the tiles
	 * 
	 * @since    1.0.0
	 * @param    string
	 */
	public function set_image_url( $image_url ) {
		$this->image_url = esc_url_raw( $image_url );
		update_post_meta( $this->post_id, 'osm_maptiles_image_url', $this->image_url );
	}
	
	/**
	 * Set job ID
	 * 
	 * @since    1.0.0
	 * @param    string
	 */
	public function set_job_id( $job_id ) {
		$this->job_id = sanitize_text_field( $job_id );
		update_post_meta( $this->post_id, 'osm_maptiles_job_id', $this->job_id );
	}

	/**
	 * Set tiles data
	 * 
	 * @since    1.0.0
	 * @param    array
	 */
	public function set_tiles_data( $tiles_data ) {
		$this->tiles_data = is_array( $tiles_data ) ? $tiles_data : array();
		update_post_meta( $this->post_id, 'osm_maptiles_tiles_data', $this->tiles_data );
	}

	/**
	 * Set downloaded tiles count
	 * 
	 * @since    1.0.0
	 * @param    int
	 */
	public function set_downloaded_tiles_index( $downloaded_tiles_index ) {
		$this->downloaded_tiles_index = intval( $downloaded_tiles_index );
		$this->downloaded_percentage  = $this->get_downloaded_percentage();

		update_post_meta( $this->post_id, 'osm_maptiles_downloaded_tiles_index', $this->downloaded_tiles_index );
	}

	/**
	 * Set download folder name
	 * This will be lies under WordPress `uploads` folder
	 * 
	 * @since    1.0.0
	 * @param    string
	 */
	public function set_download_folder_name( $string ) {
		$this->download_folder_name = sanitize_text_field( $string );
		update_post_meta( $this->post_id, 'osm_maptiles_download_folder_name', $this->download_folder_name );
	}

	/**
	 * Get tiles to download on current batch
	 * Will be different each batch
	 * 
	 * @since    1.0.0
	 * @return   array 
	 */
	public function get_tiles_to_download() {
		$total_tiles       = count( $this->tiles_data );
		$index_to          = $this->downloaded_tiles_index + $this->tiles_count_per_batch;
		$index_to          = $index_to < $total_tiles ? $index_to : $total_tiles;
		$tiles_to_download = array();

		for ( $i = $this->downloaded_tiles_index; $i < $index_to; $i++ ) { 
			$tiles_to_download[] = $this->tiles_data[ $i ];
		}

		return $tiles_to_download;
	}

	/**
	 * Set null on empty data
	 * 
	 * @since    1.0.0
	 * @param    mixed    $data
	 * @return   mixed    null|mixed
	 */
	private function null_on_empty( $data ) {
		return ( empty( $data ) || ! $data ) ? null : $data;
	}

	/**
	 * Sync status form the old plugin version
	 * Mainly, this function will be executed if we don't have any status yet,
	 * and if there is any remain data form the old plugin
	 * 
	 * @since    1.0.0
	 */
	public function sync_old_plugin_status() {
		// bail out if we already have the status
		if ( null !== $this->get_status() ) return false;

		// get the old plugin status
		$old_plugin_status = get_post_meta( $this->post_id, '_map_tileset_status', true );
		$old_plugin_job_id = get_post_meta( $this->post_id, '_map_tileset_job_id', true );

		switch ( $old_plugin_status ) {
			case 'error':
				$new_status = 'ready_to_upload';
				break;

			case 'processing':
				if ( ! empty( $old_plugin_job_id ) ) {
					/**
					 * On this stage, plugin will check the cloud job,
					 * and if it's completed, then it will get the tiles data from cloud,
					 * set the status to `cloud_job_completed`, and then start downloading the tiles
					 */
					$new_status = 'cloud_job_created';
				} else {
					$new_status = 'ready_to_upload';
				}
				break;

			case 'ready':
				$new_status = 'tiles_download_completed';
				break;
			
			default:
				$new_status = 'idle';
				break;
		}

		// update the plugin status
		$this->set_status( $new_status );
	}
	
}
