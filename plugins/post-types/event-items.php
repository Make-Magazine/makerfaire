<?php
function event_items_init() {
	register_post_type( 'event-items', array(
		'hierarchical'        => false,
		'public'              => true,
		'show_in_nav_menus'   => true,
		'show_ui'             => true,
		'supports'            => array( 'thumbnail' ),
		'has_archive'         => true,
		'query_var'           => true,
		'rewrite'             => true,
		'labels'              => array(
			'name'                => __( 'Event Items', 'makerfaire' ),
			'singular_name'       => __( 'Event Items', 'makerfaire' ),
			'add_new'             => __( 'Add a new event item', 'makerfaire' ),
			'all_items'           => __( 'Event items', 'makerfaire' ),
			'add_new_item'        => __( 'Add a new event item', 'makerfaire' ),
			'edit_item'           => __( 'Edit event items', 'makerfaire' ),
			'new_item'            => __( 'New event item', 'makerfaire' ),
			'view_item'           => __( 'View event items', 'makerfaire' ),
			'search_items'        => __( 'Search event items', 'makerfaire' ),
			'not_found'           => __( 'No event items found', 'makerfaire' ),
			'not_found_in_trash'  => __( 'No event items found in trash', 'makerfaire' ),
			'parent_item_colon'   => __( 'Parent event items', 'makerfaire' ),
			'menu_name'           => __( 'Event items', 'makerfaire' ),
		),
	) );

}
add_action( 'init', 'event_items_init' );

function event_items_updated_messages( $messages ) {
	global $post;

	// Get the mfei_record from the post meta
	$meta = get_post_meta( $post->ID, 'mfei_record', true );

	// Get the right permalink for the application post the event is associated to, if it exists of course
	if ( ! empty( $meta ) )
		$permalink = get_permalink( $meta );

	// $messages['event-items'] = array(
	// 	0 => '', // Unused. Messages start at index 1.
	// 	1 => sprintf( __('Event items updated. <a target="_blank" href="%s">View event items</a>', 'makerfaire'), esc_url( $permalink ) ),
	// 	2 => __('Custom field updated.', 'makerfaire'),
	// 	3 => __('Custom field deleted.', 'makerfaire'),
	// 	4 => __('Event items updated.', 'makerfaire'),
	// 	/* translators: %s: date and time of the revision */
	// 	5 => isset($_GET['revision']) ? sprintf( __('Event items restored to revision from %s', 'makerfaire'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	// 	6 => sprintf( __('Event items published. <a href="%s">View event items</a>', 'makerfaire'), esc_url( $permalink ) ),
	// 	7 => __('Event items saved.', 'makerfaire'),
	// 	8 => sprintf( __('Event items submitted. <a target="_blank" href="%s">Preview event items</a>', 'makerfaire'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	// 	9 => sprintf( __('Event items scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event items</a>', 'makerfaire'),
	// 	// translators: Publish box date format, see http://php.net/date
	// 	date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
	// 	10 => sprintf( __('Event items draft updated. <a target="_blank" href="%s">Preview event items</a>', 'makerfaire'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	// );

	return $messages;
}
add_filter( 'post_updated_messages', 'event_items_updated_messages' );


/**
 * Some users like to schedule applications to a stage. When they are done scheduleing, they like to press, "preview changes" on the schedule edit screen.
 * This is no bueno because it tries to load the page template for the event items post type, which is nothing more than a way to connect locations and applications at a certain time.
 * This function will fix that by listening for this post type request and we'll redirect to the application ID assigned to it.
 * @return void
 */
function mf_redirect_to_parent_permalink() {
	global $post;

	if ( get_post_type() == 'event-items' ) {
		$app_id = get_post_meta( $post->ID, 'mfei_record', true );

		if ( ! empty( $app_id ) && absint( $app_id ) ) {
			wp_safe_redirect( get_permalink( $app_id ) );
			exit();
		}
	}
}
add_action( 'template_redirect', 'mf_redirect_to_parent_permalink' );


/*
* Callback for adding meta box to EVENT ITEM
* =====================================================================*/
function makerfaire_add_meta_boxes() {
	add_meta_box( 'ei-details', 'Event Item Details', 'makerfaire_meta_box', 'event-items', 'normal', 'default' );
}
/*
* Display EVENT ITEM Form
*
* @param object $post Post being edited
* =====================================================================*/
function makerfaire_meta_box( $post ) {

	// Let's use these as defaults
	$defaults = array(
		'mfei_day'    	=> 'Saturday',
		'mfei_start'  	=> '8:00 AM',
		'mfei_stop'   	=> '8:30 AM',
		'mfei_record' 	=> '',
		'mfei_coverage'	=> '',
	);

	// Get the post meta
	$post_meta = get_post_custom( $post->ID );

	// Merge with defaults.
	$meta = shortcode_atts( $defaults, $post_meta );

	$app_id = ( isset( $meta['mfei_record'][0] ) && ! empty( $meta['mfei_record'][0] ) ) ? $meta['mfei_record'][0] : '';
	$event_scheduled = get_post_meta( absint( $app_id ), '_ef_editorial_meta_checkbox_schedule-completed', true );
	$schedules_emailed = get_post_meta( absint( $app_id ), 'app-emails-sent', true ); ?>

	<style>#ei-details label{font-weight:bold; display:block; margin:15px 0 5px 0;} #ei-details select,#ei-details input[type=text]{width:200px}</style>
	<?php wp_nonce_field('mfei_nonce', 'mfei_submit_nonce'); ?>
	<label>Day</label>
	<select name="mfei_day">
		<option value="Saturday" <?php selected( $meta['mfei_day'][0], 'Saturday' ); ?>>Saturday</option>
		<option value="Sunday" <?php selected( $meta['mfei_day'][0], 'Sunday' ); ?>>Sunday</option>
	</select>
	<label>Start Time</label>
	<select name="mfei_start">
		<?php makerfaire_create_time( $meta['mfei_start'][0] ); ?>
	</select>
	<label>Stop Time</label>
	<select name="mfei_stop">
		<?php makerfaire_create_time( $meta['mfei_stop'][0] ); ?>
	</select>
	<label>Coverage Video Link</label>
	<input type="text" name="mfei_coverage" id="mfei_coverage" value="<?php echo ( !empty( $meta['mfei_coverage'][0] ) ) ? esc_url ( $meta['mfei_coverage'][0] ) : ''; ?>" />
	<label>Record Number - MUST BE VALID APPLICATION ID</label>
	<?php
		// Check if we are loading from a referring post and add that ID to our Record field
		if ( isset( $_GET['refer_id'] ) && ! empty( $_GET['refer_id'] ) ) {
			echo  '<input type="text" name="mfei_record" id="mfei_record" value="' . absint( $_GET['refer_id'] ) . '" />';
		} else {
			$id = ( !empty( $meta['mfei_record'][0] ) ) ? absint( $meta['mfei_record'][0] ) : '';
			echo '<input type="text" name="mfei_record" id="mfei_record" value="' . $id . '" />';
		}
	?>
	<a title="Edit event items" href="#" id="view-application" class="post-edit-link">View Application</a> (opens new window with given application)
	<label>Schedule Completed</label>
	<input name="mfei_schedule_completed" type="checkbox" value="<?php echo ( $event_scheduled === '1' ) ? '1' : '0'; ?>" <?php echo checked( $event_scheduled, '1' ); ?> /> &nbsp; Event is Scheduled
	<p><a href="#" id="mf-email-schedule-button" class="button">Email Presenter Schedule</a></p>
	<?php wp_nonce_field( 'email-presenter-schedulees', 'mf-email-schedule' ); ?>
	<input type="hidden" name="meta-data" id="schedule-id" value="<?php echo absint( $post->ID ); ?>">
	<?php if ( $schedules_emailed ) : ?>
		<div id="email-status" style="color:#468847;background-color:#dff0d8;border:1px solid #d6e9c6;padding:8px 35px 8px 14px;text-shadow:0 1px 0 rgba(255,255,255,.5);border-radius:4px;width:25%;margin-top:10px;">Emails have already been sent!</div>
	<?php endif; ?>
	<script>
		jQuery( document ).ready( function( $ ) {
			// Listen for a call to set the MFEI Record
			$( '#view-application' ).click( function() {
				window.open('/makerfaire/wp-admin/post.php?post=' + jQuery( '#mfei_record' ).val() + '&action=edit', '_blank');
			});

			// Handle the request to email the schedule to the presenters over ajax
			$( '#mf-email-schedule-button' ).click( function( e ) {
				e.preventDefault();

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajaxurl,
					data: {
						'action' : 'mf_email_schedule',
						'nonce'  : $( '#mf-email-schedule' ).val(),
						'meta'   : $( '#schedule-id' ).val(),
					},
					success: function( results ) {
						if ( results.messages_sent ) {
							$( '#email-status' ).remove();
							$( '#mf-email-schedule-button' ).parent().replaceWith( '<div id="email-status" style="color:#468847;background-color:#dff0d8;border:1px solid #d6e9c6;padding:8px 35px 8px 14px;text-shadow:0 1px 0 rgba(255,255,255,.5);border-radius:4px;width:25%;margin-top:10px;">Emails were successfully sent!</div>' );
						} else {
							$( '#email-status' ).remove();
							$( '#mf-email-schedule-button' ).parent().after( '<div id="email-status" style="color:#b94a48;background-color:#f2dede;border:1px solid #eed3d7;padding:8px 35px 8px 14px;text-shadow:0 1px 0 rgba(255,255,255,.5);border-radius:4px;width:50%;margin-top:10px;">Email failed for ' + results.failed_email + '</div>' );
						}
					},
					error: function( jqHXR, textStatus, errorThrown ) {
						console.log( 'ERROR' );
						console.log( textStatus );
						console.log( errorThrown );
					}
				});
			});
		});
	</script>
<?php }


/*
* Saves Event Item Meta as well as the connected MakerFaire Application
*
* @param int $id Updated Post ID
* =====================================================================*/
function makerfaire_create_time( $val ) {

	// Set some variables.
	$start_time = "10:00:00"; // Time to start at...
	$end_time = "10:00:00"; // Not sure why setting at or below 10 gives us the correct time frame...

	while ( strtotime( $start_time ) >= strtotime( $end_time ) ) {
		$time = date( 'g:i A', strtotime( $start_time ) );

		echo '<option value="' . $time . '"' . selected( $val, $time ) . '>' . date( 'g:i A', strtotime( $start_time ) ) . '</option>';

		$start_time = date( 'H:i:s', strtotime( "$start_time 5 minutes" ) );
	}
}
/* ADD FORM TO EVENT ITEM */
add_action( 'add_meta_boxes', 'makerfaire_add_meta_boxes' );



/*
* Saves Event Item Meta as well as the connected MakerFaire Application
*
* @param int $id Updated Post ID
* =====================================================================*/
function makerfaire_update_event( $id ) {

	if ( empty( $_POST ) || get_post_type( absint( $id ) ) != 'event-items' )
		return false;

	if ( ! isset( $_POST['mfei_submit_nonce'] ) || ! wp_verify_nonce( $_POST['mfei_submit_nonce'], 'mfei_nonce' ) )
		return false;

	// Confirm that an application ID is passed
	$is_mf_form = get_post_type( absint( $_POST['mfei_record'] ) ) == 'mf_form';

	$meta = array(
		'mfei_day'    => sanitize_text_field( $_POST['mfei_day'] ),
		'mfei_start'  => sanitize_text_field( $_POST['mfei_start'] ),
		'mfei_stop'   => sanitize_text_field( $_POST['mfei_stop'] ),
		'mfei_record' => $is_mf_form ? absint( $_POST['mfei_record'] ) : 0,
		'mfei_coverage' => esc_url( $_POST['mfei_coverage'] ),
	);

	// Update the post meta for the event
	foreach ( $meta as $meta_key => $meta_value ) {
		update_post_meta( $id, $meta_key, $meta_value );
	}

	// If we are trying to save any presenter promo codes...
	if ( isset( $_POST['presenter-promo-code'] ) && ! empty( $_POST['presenter-promo-code'] ) )
		update_post_meta( absint( $_POST['mfei_record'] ), 'app-presenter-promo-code', sanitize_text_field( $_POST['presenter-promo-code'] ) );


	if ( ! $is_mf_form )
		return;

	// Update scheduled status of connection application
	update_post_meta( absint( $meta['mfei_record'] ), '_ef_editorial_meta_checkbox_schedule-completed', ( isset( $_POST['mfei_schedule_completed'] ) ? 1 : 0 ) );
}
add_action( 'save_post', 'makerfaire_update_event' );


/**
 * Generates a configurable/canned email response to the presenter.
 * @param  obj $meta The meta information assigned to our event
 * @return void
 */
function mf_email_presenter_schedule() {
	// Verify we have the right credentials
	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'email-presenter-schedulees' ) )
		wp_die( new WP_Error( 'security-check-failed', 'We couldn\'t validate your request. Try again.' ) );

	// Get the application and the event schedule
	$schedule_meta = get_post_custom( absint( $_POST['meta'] ) );
	$application = get_post( absint( $schedule_meta['mfei_record'][0] ) );

	// Make sure we have an application
	if ( empty( $application ) )
		wp_die( new WP_Error( 'empty-application', 'We couldn\'t find the application #' . absint( $schedule_meta['mfei_record'][0] ) ) );

	// Extract the application data
	$app = json_decode( str_replace( "\'", "'", $application->post_content ) );
	$app_promo_code = get_post_meta( absint( $application->ID ), 'app-presenter-promo-code', true );

	// Fetch the locations and process our data into something useable
	$locations_raw = mf_get_locations( absint( $_POST['meta'] ), true );
	$locations = array();

	// Process the location objects into a comma separated lists
	if ( ! empty( $locations_raw ) ) {
		$locations = '';

		foreach ( $locations_raw as $location ) {
			$map_url = get_post_meta( absint( $location->ID ), 'location-map', true );
			$locations .= '<h2 style="margin-bottom:0;">LOCATION</h2>';
			$locations .= '<p style="margin-top:0;">' . esc_html( $location->post_title ) . '<br /><strong>Map</strong>: <a href="' . esc_url( $map_url ) . '">' . esc_url( $map_url ) . '</a></p>';
			$locations .= '<h2 style="margin-bottom:0;">AUDIO AND VISUAL EQUIPMENT</h2>';
			$locations .= '<p style="margin-top:0;">' . wp_kses_post( $location->post_content ) . '</p>';
		}
	}


	// Set some variables based on what type of application we are using
	$makers = array(
		'names'  => array(),
		'emails' => array(),
	);
	switch ( $app->form_type ) {
		case 'presenter':
			// Save each presenter name
			if ( is_array( $app->presenter_name ) && ! empty( $app->presenter_name ) ) {
				foreach ( $app->presenter_name as $name ) {
					$makers['names'][] = $name;
				}
			} else {
				$makers['names'][] = $app->presenter_name;
			}

			// Save each presenter email
			if ( is_array( $app->presenter_email ) && ! empty( $app->presenter_email ) ) {
				foreach ( $app->presenter_email as $email ) {
					$makers['emails'][] = $email;
				}
			} else {
				$makers['emails'][] = $email;
			}
			break;
	}

	// Send all emails in testing environments to one account.
	if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], array( 'localhost', 'make.com', 'vip.dev', 'staging.makerfaire.com' ) ) ) {
		$makers = array(
			'names' => array( 'Web Master' ),
			'emails' => array( 'webmaster@makerfaire.com' ),
		);
	}

	// Fetch our email template
	$email_path = dirname( __DIR__ ) . '/maker-faire-forms/emails/schedule-event-presenter.html';

	// Prevent Path Traversal
	if ( strpos( $email_path, '../' ) !== false || strpos( $email_path, "..\\" ) !== false || strpos( $email_path, '/..' ) !== false || strpos( $email_path, '\..' ) !== false )
		return;

	// Make sure the file exists...
	if ( ! file_exists( $email_path ) )
		return;

	// get the contents of the email_template as the body
	$email_temp = file_get_contents( $email_path );

	// Update and send out an email for each maker
	$maker_count = count( $makers['names'] );
	for ( $i = 0; $i <= $maker_count - 1; $i++ ) {
		// Save our email template into a new variable so we don't have to fetch it again
		$email_body = $email_temp;

		// Add our single maker name and email and prep them for email
		$maker = array(
			'name' => $makers['names'][ intval( $i ) ],
			'email' => $makers['emails'][ intval( $i ) ],
		);

		// Get full date
		$faire_date = mf_get_faire_date( MF_CURRENT_FAIRE );

		// Pair our custom variables found in the email to actual data. We will run a find and replace on it for a dynamic email
		$find_and_replace = array(
			'$presenter_name' => esc_html( $maker['name'] ),
			'$faire_name' => 'World Maker Faire New York 2014',
			'$app_name' => esc_html( $application->post_title ),
			'$scheduled_date' => date( 'l, F j, Y', strtotime( $faire_date[ $schedule_meta['mfei_day'][0] ] ) ),
			'$scheduled_start_time' => esc_html( $schedule_meta['mfei_start'][0] ),
			'$scheduled_end_time' => esc_html( $schedule_meta['mfei_stop'][0] ),
			'$location_information' => $locations,
			'$app_url' => get_permalink( absint( $application->ID ) ),
			'$app_eb_promo_code' => sanitize_text_field( $app_promo_code ),
		);
		$body = force_balance_tags( str_replace( array_keys( $find_and_replace ), array_values( $find_and_replace ), $email_body ) );

		// Email it baby!
		$results[ $maker['name'] ] = wp_mail( esc_html( $maker['name'] ) . ' <' . sanitize_email( $maker['email'] ) . '>', 'Confirmation and logistics for your presentation @World Maker Faire New York 2014', $body, array( 'Content-Type: text/html', "From: Sabrina Merlo <sabrina@makerfaire.com", "Bcc: sabrina@makerfaire.com" ) );
	}

	// Check each result of the email and see if any failed.
	foreach ( $results as $key => $value ) {
		if ( $value ) {
			$done = array( 'messages_sent' => true );

			// Update the post meta that emails have been sent to the presenters
			update_post_meta( absint( $application->ID ), 'app-emails-sent', 'true' );
		} else {
			$done = array( 'messages_sent' => false, 'failed_email' => esc_html( $key ) );
 		}
	}

	// Return the results
	wp_die( json_encode( $done ) );
}
add_action( 'wp_ajax_mf_email_schedule', 'mf_email_presenter_schedule' );



/*
* Add Custom Event Item Columns
*
* @param array $cs Array of default columns
* =====================================================================*/
function makerfaire_add_column( $cs ) {

	unset( $cs['title'], $cs['date'] );

	$ncs = array(
		'event_id'     => 'Event ID',
		'location'     => 'Location',
		'day'          => 'Day',
		'start_time'   => 'Start Time',
		'stop_time'    => 'Stop Time',
		'project_name' => 'Project Name',
		'project_id'   => 'Project ID'
	);

	return array_merge( $cs, $ncs );
}
/*
* Add data to custom Event Item Column
*
* @param string $column_name Column ID
* @param int $post_ID Row Post ID
* =====================================================================*/
function makerfaire_add_name_to_column( $column_name, $post_ID ) {

	switch( $column_name ) {
		case 'event_id' :

			edit_post_link( 'Event ID : '.$post_ID , '', '', $post_ID );
			break;

		case 'project_id' :

			echo intval( get_post_meta( $post_ID, 'mfei_record', true ) );
			break;

		case 'day' :

			echo esc_html( get_post_meta( $post_ID, 'mfei_day', true ) );
			break;

		case 'project_name' :

			$id = get_post_meta( $post_ID, 'mfei_record', true );
			edit_post_link( get_the_title( $id ), '', '', $id );
			break;

		case 'start_time' :

			echo esc_html( get_post_meta( $post_ID, 'mfei_start', true ) );
			break;

		case 'stop_time' :

			echo esc_html( get_post_meta( $post_ID, 'mfei_stop', true ) );
			break;

		case 'location' :

			echo mf_get_locations( $post_ID );
			break;
	}
}
/* CUSTOM COLUMNS HOOKS */
add_filter( 'manage_event-items_posts_columns',         'makerfaire_add_column', 10);
add_action( 'manage_event-items_posts_custom_column',   'makerfaire_add_name_to_column', 10, 2);




/*
* Add Sortable Columns to Event Item List
*
* @param array $cs Default Sortable Columns
* =====================================================================*/
function makerfaire_columns_sortable( $cs ) {
	$cs['day']          = 'day';
	$cs['start_time']   = 'start_time';
	$cs['stop_time']    = 'stop_time';
	$cs['project_id']   = 'project_id';

	return $cs;
}
/*
* Filter WP Query with Meta Data / Taxonomy Data
*
* @param array $vars All Default Query Variables
* =====================================================================*/
function makerfaire_columns_orderby( $vars ) {

	if ( is_admin() && isset( $vars['orderby'] ) && $vars['post_type'] == 'event-items' ) {

		switch( $vars['orderby'] ) {
			case 'day' :
				$vars = array_merge( $vars, array(
					'meta_key' => 'mfei_day',
					'orderby'  => 'meta_value'
				) );
			break;
			case 'start_time' :
				$vars = array_merge( $vars, array(
					'meta_key' => 'mfei_start',
					'orderby'  => 'meta_value'
				) );
			break;
			case 'stop_time' :
				$vars = array_merge( $vars, array(
					'meta_key' => 'mfei_stop',
					'orderby'  => 'meta_value'
				) );
			break;
			case 'project_id' :
				$vars = array_merge( $vars, array(
					'meta_key' => 'mfei_record',
					'orderby'  => 'meta_value'
				) );
			break;
		}
	}
	if ( is_admin() && isset( $vars['location'] ) && $vars['location'] != 0 && $vars['post_type'] == 'event-items' ) {
		$loc = get_term( $vars['location'], 'location' );
		$vars['location'] = $loc->name;
	}

	return $vars;
}
/* CUSTOM SORTABLE COLUMNS HOOKS */
add_filter( 'manage_edit-event-items_sortable_columns', 'makerfaire_columns_sortable' );
add_filter( 'request', 'makerfaire_columns_orderby' );




/*
* Add Location Taxonomy filter to posts
* =====================================================================*/
function makerfaire_manage_posts() {
	$location = ( !empty( $_GET['location'] ) ) ? intval( $_GET['location'] ) : '';
	if( !isset( $_GET['post_type'] ) || $_GET['post_type'] != 'event-items' )
		return;

	$args = array(
		'show_option_all' => "View All Locations",
		'taxonomy'        => 'location',
		'name'            => 'location',
		'selected'        => $location,
	);
	wp_dropdown_categories($args);
	echo '<style>select[name="m"]{display:none}</style>';
}
/* CUSTOM FILTER HOOK */
add_action('restrict_manage_posts','makerfaire_manage_posts');


/**
 * Add a type dropdown to the admin screen.
 *
 * Need a way to filter all of the different types of applications in the admin area.
 *
 */
function mf_restrict_listings_by_type( $type ) {
	global $typenow;
	global $wp_query;

	if ($typenow == 'mf_form' ) {
		wp_dropdown_categories(
			array(
				'walker'			=> new SH_Walker_TaxonomyDropdown(),
				'value'				=> 'slug',
				'show_option_all'	=> 'Types',
				'taxonomy'			=> 'type',
				'name'				=> 'type',
				'orderby'			=> 'name',
				'selected'			=> $type,
				'hierarchical'		=> true,
				'hide_empty'		=> false
				)
		);
	}
}

add_action('restrict_manage_posts','mf_restrict_listings_by_type');


/**
 * Add a faire dropdown to the admin screen.
 *
 * Need a way to filter all of the different faires the admin area.
 *
 */
function mf_restrict_listings_by_faire() {
	global $typenow;
	global $wp_query;

	if ($typenow == 'mf_form' ) {
		wp_dropdown_categories(
			array(
				'walker'			=> new SH_Walker_TaxonomyDropdown(),
				'value'				=> 'slug',
				'show_option_all'	=>  'View All Faires',
				'taxonomy'			=>  'faire',
				'name'				=>  'faire',
				'orderby'			=>  'name',
				'selected'			=> ( !empty( $wp_query->query['faire'] ) ) ? $wp_query->query['faire'] : '',
				'hierarchical'		=>  true,
				'hide_empty'		=>  false
				)
		);
	}
}

add_action('restrict_manage_posts','mf_restrict_listings_by_faire');

/*
 * A walker class to use that extends wp_dropdown_categories and allows it to use the term's slug as a value rather than ID.
 *
 * See http://core.trac.wordpress.org/ticket/13258
 *
 * Usage, as normal:
 * wp_dropdown_categories($args);
 *
 * But specify the custom walker class, and (optionally) a 'id' or 'slug' for the 'value' parameter:
 * $args=array('walker'=> new SH_Walker_TaxonomyDropdown(), 'value'=>'slug', .... );
 * wp_dropdown_categories($args);
 *
 * If the 'value' parameter is not set it will use term ID for categories, and the term's slug for other taxonomies in the value attribute of the term's <option>.
*/

class SH_Walker_TaxonomyDropdown extends Walker_CategoryDropdown {

	function start_el ( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$pad = str_repeat('&nbsp;', $depth * 3);
		$cat_name = apply_filters('list_cats', $category->name, $category);

		if( !isset($args['value']) ){
			$args['value'] = ( $category->taxonomy != 'category' ? 'slug' : 'id' );
		}

		$value = ($args['value']=='slug' ? $category->slug : $category->term_id );

		$output .= "\t<option class=\"level-$depth\" value=\"".$value."\"";
		if ( $value === (string) $args['selected'] ){
			$output .= ' selected="selected"';
		}
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';

		$output .= "</option>\n";
	}

}

/**
 * Wrapper for wp_dropdown_categories to easily add new dropdowns.
 */
function mf_generate_dropdown( $tax, $selected ) {
	wp_dropdown_categories(
		array(
			'show_option_all'	=> ( $tax == 'post_tag' ) ? 'Tag' : ucwords( $tax ),
			'taxonomy'			=> $tax,
			'orderby'			=> 'name',
			'selected'			=> $selected,
			'hierarchical'		=> true,
			'hide_empty'		=> false,
			'name'				=> $tax,
			)
	);
}

/**
 * Based on the faire, output the dates of the event
 * @param  string $faire The faire slug
 *
 * @return array $faire
 */
function mf_get_faire_date( $faire ) {

	$output = array();

	if ( $faire == 'maker-faire-bay-area-2014' ) {
		$output = array(
			'Saturday' 	=> '5/17/2014',
			'Sunday' 	=> '5/18/2014',
			'time_zone' => ' PST',
 		);
	} elseif ( $faire == 'world-maker-faire-new-york-2014' ) {
		$output = array(
			'Saturday'	=> '9/20/2014',
			'Sunday'	=> '9/21/2014',
			'time_zone'	=> ' EST',
		);
	} elseif ( $faire == 'world-maker-faire-new-york-2013' ) { // @todo let's update this to match 2014's object but honestly, we'll need to update a bunch of legacy code. Another time me thinks.
		$output['Saturday'] = '9/21/2013';
		$output['Sunday'] 	= '9/22/2013';
		$output['time_zone']= ' EST';
	} elseif ( $faire == 'maker-faire-bay-area-2013') {
		$output['Saturday'] = '5/18/2013';
		$output['Sunday'] 	= '5/19/2013';
		$output['time_zone']= ' PST';
	}

	return $output;
}
