var job_id;
var all_tiles;

jQuery(document).ready(function($){

	var ajax_nonce_value        = $( '#maptiles-uploader-nonce' ).val();
	var post_id                 = $( '#post_ID' ).val();
	var notification            = $( '.acf-osm-maptiles-notification' );
	var error_img_message       = "Please make sure your Image URL is accessible from public, instead of local or private image.";
	var job_on_progress_message = "Job is not complete, yet.";
	
	/**
	 * Run the status check on page load
	 */
	check_status();

	/**
	 * Set the notification display
	 * 
	 * @param string message 
	 * @param string mode 
	 */
	 function set_notification( message, mode ) {		
		var spinner       = notification.children( '.spinner' );
		var success_icon  = notification.children( '.dashicons-yes-alt' );
		var error_icon    = notification.children( '.dashicons-warning' );
		var message_field = notification.children( '.message' );
		
		// set message
		message_field.html( message );
		
		// first, hide all mode 
		spinner.hide();
		success_icon.hide();
		error_icon.hide();

		// notification mode
		switch( mode ) {
			case "spinner":
				spinner.css( 'display', 'inline-block' );
				break;
				  
			case "success":
				success_icon.css( 'display', 'inline-block' );
				break;
			
			case "error":
				error_icon.css( 'display', 'inline-block' );
				break;

			default:
				notification.hide();
		}

		// show notification
		notification.css( 'display', 'block' );
	}
	
	/**
	 * Check the maptiles uploader status
	 * 
	 * @param void
	 */
	function check_status() {		
		var request_data = {
			action     : 'osm_maptiles_check_status',
			post_id    : post_id,
			ajax_nonce : ajax_nonce_value
		};

		// set notification
		set_notification( "Checking maptiles uploader status..", "spinner" );
		
		// ajax check status
		$.post( ajaxurl, request_data, function( response ) {
			// set action and notificationmessage
			if ( 'undefined' !== typeof response.action ) {
				switch( response.action ) {
					case "create_cloud_job":
						create_cloud_job()
					  	break;
						  
					case "check_cloud_job_status":
						check_cloud_job_status()
						break;
					
					case "download_maptiles":
						download_maptiles()
						break;

					default:
						// silent here
				}

				// set notification
				if ( null === response.action && null !== response.message ) {
					set_notification( response.message, "success" );
				}
				else if ( null !== response.action ) {
					set_notification( response.message, "spinner" );
				}
				else {
					notification.hide();
				}
			}
			else {
				// set notification
				set_notification( "There is an error, please try to save this map again later", "error" );
			}
			
		} );
	}

	/**
	 * Create the mapify-cloud job
	 * 
	 * @param void
	 */
	function create_cloud_job() {		
		var request_data = {
			action     : 'osm_maptiles_create_job',
			post_id    : post_id,
			ajax_nonce : ajax_nonce_value
		};
		$.post( ajaxurl, request_data, function( response ) {
			if ( 'undefined' !== typeof response.success && true === response.success ) {
				job_id = response.job_id;
	
				// set notification
				set_notification( "Job created with id: " + job_id + ", now checking the cloud process status...", "spinner" );
				
				// repeately check the status
				check_cloud_job_status();
			}
			else {
				// set notification
				if ( 'undefined' !== typeof response.message ) {
					set_notification( response.message + ' ' + error_img_message, "error" );
				}
				else {
					set_notification( "There is an error, please try to save this map again later", "error" );
				}
			}
		} );
	}

	/**
	 * Repeately check the status
	 * until the cloud has been completely processing the image
	 * 
	 * @param void
	 */
	function check_cloud_job_status() {
		var check_status = setInterval( function() {
			if ( all_tiles ) {
				clearInterval( check_status );
				
				/**
				 * Make sure the all_tiles data is valid
				 */
				if ( 'foo-bar' !== all_tiles ) {
					// set notification
					set_notification( "The cloud processing is done. Now downloading the slashed images from the cloud...", "spinner" );
		
					// download the maptiles
					download_maptiles();
				}
			}
			else {
				check_cloud_job_status_child_function();
			}
		}, 5000 );
	}
	
	/**
	 * The child function of the check_cloud_job_status function
	 * This is the function that repeately call until the colud job process is completed
	 * 
	 * @param void
	 */
	function check_cloud_job_status_child_function() {
		var request_data = {
			action     : 'osm_maptiles_check_job_status',
			post_id    : post_id,
			ajax_nonce : ajax_nonce_value
		};
		
		$.post( ajaxurl, request_data, function( response ) {			
			if ( 'undefined' !== typeof response.success && response.success ) {
				/**
				 * The cloud has been completely processing the image!
				 * the next process is to download those slashed images to local
				 */
				all_tiles = response.tiles;
			}
			else if ( 'undefined' !== typeof response.message && job_on_progress_message !== response.message ) {					
				set_notification( response.message + ' ' + error_img_message, "error" );

				/**
				 * We need to exit the loop because the error is critical
				 * To exit the loop, set the all_tiles to any data
				 */
				all_tiles = 'foo-bar';
			} 
		});
	}

	function download_maptiles() {
		var request_data = {
			action     : 'osm_maptiles_download_tiles',
			post_id    : post_id,
			ajax_nonce : ajax_nonce_value
		};
		
		$.post( ajaxurl, request_data, function( response ) {
			/**
			 * Continue the download process
			 * until all required tiles are downloaded
			 */
			if ( 'undefined' !== response.status && 'tiles_download_completed' === response.status ) {
				// set notification
				set_notification( "All tiles has been downloaded and ready to use.", "success" );

				// Change the map layer on the fly
				if ( $( '#acf-osm-map-mode' ).length ) {
					osm_var.has_image_mode = true;
					$( '#acf-osm-map-mode' ).trigger( 'change' );
				}
			}
			else {
				// set notification
				if ( 'undefined' !== response.percentage ) {
					set_notification( "Tiles downloaded " + response.percentage + "%", "spinner" );
				}

				// continue download the rest of tiles
				download_maptiles();
			}
		});
	}

});