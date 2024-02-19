<?php

if ( ! defined( 'CROWD_PLUGIN_MAX_IMAGES' ) ) {
	define( 'CROWD_PLUGIN_MAX_IMAGES', 5 );
}

/**
 * Ajax location form submission
 */
function crowd_location_form_submission() {
	// verify ajax nonce
	check_ajax_referer( 'ZFVr6pPwmsdhMUVvYc', '_crowd_ajax_nonce' );
	
	// initial variables
	$location_coords = array();
	$images          = array();
	$is_new_user     = false;
	$response        = array(
		'success'     => true,
		'data'        => __( 'There is a problem with the map. Please contact us with error code 500.', 'crowd' ), // error code 500: Internal Server Error
		'new_user_id' => 0,
	);

	// parsing uploaded images
	if ( ! empty( $_POST['location_image'] ) && is_array( $_POST['location_image'] ) ) {
		$images = array_slice( array_unique( array_filter( array_map( 'absint', $_POST['location_image'] ) ) ), 0, CROWD_PLUGIN_MAX_IMAGES );
	}

	// parsing location coordinates
	if ( ! empty( $_POST['location_coords'] ) ) {
		$location_coords = explode( ',', $_POST['location_coords'] );
		$location_coords = array_map( 'floatval', array_slice( $location_coords, 0, 2 ) );
	}

	$data = array(
		'map_id'          => isset( $_POST['map_id'] ) ? absint( $_POST['map_id'] ) : '',
		'images'          => $images,
		'location_coords' => $location_coords,
		'title'           => isset( $_POST['location_title'] ) ? trim( $_POST['location_title'] ) : '',
		'description'     => isset( $_POST['location_description'] ) ? trim( $_POST['location_description'] ) : '',
		'video'           => isset( $_POST['location_video'] ) ? trim( $_POST['location_video'] ) : '',
		'address'         => isset( $_POST['location_address'] ) ? trim( $_POST['location_address'] ) : '',
		'address_2'       => isset( $_POST['location_address_2'] ) ? trim( $_POST['location_address_2'] ) : '',
		'tooltip'         => isset( $_POST['location_tooltip'] ) ? trim( $_POST['location_tooltip'] ) : '',
		'email'           => isset( $_POST['location_email'] ) ? trim( $_POST['location_email'] ) : '',
	);

	// strip tags the data
	$data['title']       = wp_strip_all_tags( $data['title'] );
	$data['description'] = strip_tags( $data['description'], '<p><br><strong><b><em><i><a>' );
	$data['video']       = wp_strip_all_tags( $data['video'] );
	$data['address']     = wp_strip_all_tags( $data['address'] );
	$data['address_2']   = wp_strip_all_tags( $data['address_2'] );
	$data['tooltip']     = wp_strip_all_tags( $data['tooltip'] );
	$data['email']       = wp_strip_all_tags( $data['email'] );

	// get map data
	$map = new Mpfy_Map( $data['map_id'] );

	if ( ! $map->get_id() ) {
		$response['success'] = false;
		$response['data']    = __( 'Invalid Map.', 'crowd' );
	} elseif ( empty( $data['title'] ) ) {
		$response['success'] = false;
		$response['data']    = __( 'Please enter a title for your location.', 'crowd' );
	}

	// continue the progress
	if ( $response['success'] ) {
		$defaults          = crowd_get_default_values();
		$approval_required = mpfy_meta_to_bool( $map->get_id(), '_map_crowd_require_approval', $defaults['map_crowd_require_approval'] );
		
		if ( current_user_can( 'edit_map_locations' ) ) {
			$wp_user = wp_get_current_user();	
			$user_id = $wp_user->ID;
		} else {
			$user_email = sanitize_email( $data['email'] );
			$wp_user    = get_user_by( 'email', $user_email );

			if ( ! $wp_user ) {
				if ( ! is_email( $user_email ) ) {
					$response['success'] = false;
					$response['data']    = __( 'Please enter a valid email address.', 'crowd' );

					echo json_encode( $response );
					wp_die();
				}

				$user_id = wp_insert_user( array(
					'user_login' => $user_email,
					'user_email' => $user_email,
					'user_pass'  => wp_generate_password(), // let WordPress generate the password
					'role'       => 'mpfy_contributor',
				) );

				// set mark for notification
				$is_new_user             = true;							
				$response['new_user_id'] = $user_id;				
			} else {
				// the email is occupied, tell the user to login with it, or use another email instead
				$current_post_id     = @$_REQUEST['current_post_id'];
				$current_permalink   = get_permalink( $current_post_id );
				$response['success'] = false;
				$response['data']    = sprintf( __( "Your email is already registered! %sPlease login here%s.", 'crowd' ), '<a href="' . wp_login_url( $current_permalink ) . '">', '</a>' );
				
				echo json_encode( $response );
				wp_die();
			}
		}

		// must be a valid user
		if ( is_wp_error( $user_id ) ) {
			$response['success'] = false;
			$response['data']    = $user_id->get_error_message();
			
			echo json_encode( $response );
			wp_die();
		}

		// insert map-location
		$post_id = wp_insert_post( array(
			'post_title'   => $data['title'],
			'post_content' => $data['description'],
			'post_type'    => 'map-location',
			'post_status'  => $approval_required ? 'pending' : 'publish',
			'post_author'  => $user_id,
		) );
		
		// if the map-location is successfully inserted
		if ( $post_id ) {
			update_post_meta( $post_id, '_map_location_map', $map->get_id() );
			update_post_meta( $post_id, '_map_location_video_embed', $data['video'] );
			update_post_meta( $post_id, '_map_location_address', $data['address'] );
			update_post_meta( $post_id, '_map_location_address_2', $data['address_2'] );
			update_post_meta( $post_id, '_map_location_tooltip', $data['tooltip'] );
			update_post_meta( $post_id, '_map_crowd_submitted_by', $data['email'] );
			update_post_meta( $post_id, '_map_location_google_location', implode( ',', $data['location_coords'] ) );
			update_post_meta( $post_id, '_map_location_google_location-lat', $data['location_coords'][0] );
			update_post_meta( $post_id, '_map_location_google_location-lng', $data['location_coords'][1] );
			update_post_meta( $post_id, '_map_location_google_location-address', $data['address'] );
			update_post_meta( $post_id, '_map_location_google_location-address', $data['address'] );

			$attachments = array();
			
			foreach ( $images as $attachment_id ) {
				$attachment    = get_post( $attachment_id );
				$is_temp_image = get_post_meta( $attachment_id, '_mpfy_temporary_file', true );
				
				if ( ! $attachment || $attachment->post_type != 'attachment' || $is_temp_image !== 'temporary' ) {
					continue;
				}

				$attachments[] = $attachment_id;
				$image         = wp_get_attachment_image_src( $attachment_id, 'full' );
				
				// set map-locations carousel's image
				add_post_meta( $post_id, '_map_location_gallery_images_-_image_' . ( count( $attachments ) - 1 ), $image[0] );

				/**
				 * Set the attachment's author if it hasn't been set.
				 * This is happen when the user was created along with the map location submission.  
				 */
				if ( ! $attachment->post_author ) {
					wp_update_post( array(
						'ID'          => $attachment->ID,
						'post_author' => $user_id,
					) );
				}
			}

			// email content for the contributor
			if ( $is_new_user ) {
				$email_content = __( 'To access your submitted map locations, please click the button below to create a password for your account.', 'crowd' );
			} else {
				$email_content = sprintf( __( 'To access your submitted map locations, %splease log in to your account.%s', 'crowd' ), '<a href="' . wp_login_url( get_edit_post_link( $post_id ) ) . '">', '</a>' );
			}

			// send email to the contributor based on the approval status
			if ( $approval_required ) {
				crowd_submission_pending_email( $user_id, $email_content, $is_new_user );
			} else {
				crowd_submission_published_email( $post_id, $map->get_id(), $data, $user_id, $email_content, $is_new_user );
			}

			// send email to admin
			crowd_notify_admin_of_submission( $post_id, $map->get_id(), $data, $user_id );
		} else {
			$response['success'] = false;
		}
	}

	echo json_encode( $response );
	wp_die();
}
add_action( 'wp_ajax_crowd_add_location', 'crowd_location_form_submission' );
add_action( 'wp_ajax_nopriv_crowd_add_location', 'crowd_location_form_submission' );

/**
 * Ajax location form images submission
 */
function crowd_location_form_image() {
	// verify ajax nonce
	check_ajax_referer( 'ZFVr6pPwmsdhMUVvYc', '_crowd_ajax_nonce' );

	$response = array(
		'success' => true,
		'data'    => '',
	);

	// the image's author is the current logged-in user
	$attachment = media_handle_upload( 'file', 0 );

	if ( is_wp_error( $attachment ) ) {
		$response['success'] = false;
		$response['data']    = __( 'There is a problem with uploading to this map. Please contact us with error code 400.', 'crowd' ); // error code 400: Bad Request
	} else {
		update_post_meta( $attachment, '_mpfy_temporary_file', 'temporary' );
		
		$response['data'] = array(
			'id'  => $attachment,
			'img' => wp_get_attachment_image( $attachment, 'crowd-preview' ),
		);
	}

	echo json_encode( $response );
	wp_die();
}
add_action( 'wp_ajax_crowd_add_location_image', 'crowd_location_form_image' );
add_action( 'wp_ajax_nopriv_crowd_add_location_image', 'crowd_location_form_image' );

/**
 * Ajax display map location submission form
 */
function crowd_location_form() {
	include( CROWD_PLUGIN_DIR_PATH . '/html/html-location-form.php' );
	wp_die();
}
add_action( 'wp_ajax_crowd_add_location_form', 'crowd_location_form' );
add_action( 'wp_ajax_nopriv_crowd_add_location_form', 'crowd_location_form' );

/**
 * Ajax display thank you content after submission 
 */
function crowd_location_thank_you() {
	$map          = new Mpfy_Map( absint( $_REQUEST['map_id'] ) );
	$new_user_id  = absint( $_REQUEST['new_user_id'] );
	$notification = false;

	// if new user
	if ( $new_user_id ) {
		$notification = sprintf( 
			__( "Thank you for joining our map contributor team. To access your account, please check your email or %svisit this page%s to reset your account's password.", 'crowd' ),
			'<strong><a href="'. wp_lostpassword_url() .'" target="_blank">', '</a></strong>'
		);
	} 

	// if has map
	if ( ! $map->get_id() ) {
		$map = false;
	}

	$defaults = crowd_get_default_values();
	$title    = $map ? get_post_meta( $map->get_id(), '_map_crowd_thank_you_title', true ) : $defaults['map_crowd_thank_you_title'];
	$content  = $map ? get_post_meta( $map->get_id(), '_map_crowd_thank_you_content', true ) : $defaults['map_crowd_thank_you_content'];
	
	include( CROWD_PLUGIN_DIR_PATH . '/html/html-location-thankyou.php' );
	wp_die();
}
add_action( 'wp_ajax_crowd_location_thank_you', 'crowd_location_thank_you' );
add_action( 'wp_ajax_nopriv_crowd_location_thank_you', 'crowd_location_thank_you' );

/**
 * Send email to contributor when his pending location has been published
 */
function crowd_submission_pending_to_publish_email( $new_status, $old_status, $post ) {
	if ( 'publish' !== $new_status || 'publish' === $old_status || 'new' === $old_status ) return;
	if ( 'map-location' !== $post->post_type ) return;

	// check the user role
	if ( ! crowd_is_mapifypro_contributor( $post->post_author ) ) return;

	// get map data
	$map_location = new Mpfy_Map_Location( $post->ID );
	$map_ids      = $map_location->get_maps();
	$data         = array(
		'title'           => $map_location->get_title(),
		'address'         => $map_location->get_address(),
		'address_2'       => $map_location->get_address_line_2(),
		'location_coords' => $map_location->get_coordinates(),
	);

	// since contributors can only contribute one map an once, then we only need one map_id
	$map_id       = $map_ids[0];	

	crowd_submission_published_email( $map_location->get_id(), $map_id, $data, $post->post_author );
}
add_action( 'transition_post_status', 'crowd_submission_pending_to_publish_email', 10, 3 );

/**
 * Email notification for admin after map location submission
 */
function crowd_notify_admin_of_submission( $post_id, $map_id, $data, $user_id ) {
	$map         = new Mpfy_Map( $map_id );
	$admin_email = get_bloginfo( 'admin_email' );
	$subject     = __( 'A user has submitted a new CrowdMaps location', 'crowd' );
	$user        = get_user_by( 'id', $user_id );
	
	ob_start();
	include( CROWD_PLUGIN_DIR_PATH . '/html/html-admin-notification-email.php' );
	$body = ob_get_clean();

	wp_mail( $admin_email, $subject, $body, array( 'Content-type: text/html' ) );
}

/**
 * Email notification for user if the map location publish status is pending
 */
function crowd_submission_pending_email( $user_id, $content = '', $with_reset_password = false ) {
	$user    = get_user_by( 'id', $user_id );
	$subject = __( 'Thank you for submitting your map location', 'crowd' );
	
	if ( $user ) {
		ob_start();
		include( CROWD_PLUGIN_DIR_PATH . '/html/html-submission-pending-email.php' );
		$body = ob_get_clean();
		
		wp_mail( $user->user_email, $subject, $body, array( 'Content-type: text/html' ) );
	}
}

/**
 * Email notification for user if the map location is published
 */
function crowd_submission_published_email( $post_id, $map_id, $data, $user_id, $content = '', $with_reset_password = false ) {
	$map     = new Mpfy_Map( $map_id );
	$user    = get_user_by( 'id', $user_id );
	$subject = __( 'Congratulations, your contribution has been published', 'crowd' );
	
	ob_start();
	include( CROWD_PLUGIN_DIR_PATH . '/html/html-submission-published-email.php' );
	$body = ob_get_clean();

	wp_mail( $user->user_email, $subject, $body, array( 'Content-type: text/html' ) );
}

/**
 * Check user role
 */
function crowd_is_mapifypro_contributor( $user_id ) {
	$user = get_userdata( $user_id );

	if ( $user && in_array( 'mpfy_contributor', (array) $user->roles ) ) {
		return true;
	} else {
		return false;
	}
}