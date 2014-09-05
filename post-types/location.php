<?php

function location_post_type_init() {
	register_post_type( 'location', array(
		'hierarchical'      => true,
		'public'	   => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'supports'          => array( 'title', 'editor', 'page-attributes', 'revisions', 'excerpt' ),
		'has_archive'       => true,
		'query_var'         => true,
		'rewrite'           => true,
		'taxonomies'		=> array( 'faire' ),
		'labels'            => array(
			'name'                => __( 'Locations', 'makerfaire' ),
			'singular_name'       => __( 'Locations', 'makerfaire' ),
			'all_items'           => __( 'Locations', 'makerfaire' ),
			'new_item'            => __( 'New Locations', 'makerfaire' ),
			'add_new'             => __( 'Add New', 'makerfaire' ),
			'add_new_item'        => __( 'Add New Locations', 'makerfaire' ),
			'edit_item'           => __( 'Edit Locations', 'makerfaire' ),
			'view_item'           => __( 'View Locations', 'makerfaire' ),
			'search_items'        => __( 'Search Locations', 'makerfaire' ),
			'not_found'           => __( 'No Locations found', 'makerfaire' ),
			'not_found_in_trash'  => __( 'No Locations found in trash', 'makerfaire' ),
			'parent_item_colon'   => __( 'Parent Locations', 'makerfaire' ),
			'menu_name'           => __( 'Locations', 'makerfaire' ),
		),
	) );
}

add_action( 'init', 'location_post_type_init' );

function location_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['location'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Locations updated. <a target="_blank" href="%s">View Locations</a>', 'makerfaire'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'makerfaire'),
		3 => __('Custom field deleted.', 'makerfaire'),
		4 => __('Locations updated.', 'makerfaire'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Locations restored to revision from %s', 'makerfaire'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Locations published. <a href="%s">View Locations</a>', 'makerfaire'), esc_url( $permalink ) ),
		7 => __('Locations saved.', 'makerfaire'),
		8 => sprintf( __('Locations submitted. <a target="_blank" href="%s">Preview Locations</a>', 'makerfaire'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Locations scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Locations</a>', 'makerfaire'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Locations draft updated. <a target="_blank" href="%s">Preview Locations</a>', 'makerfaire'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'location_updated_messages' );


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function mf_add_custom_box() {

	$screens = array( 'mf_form', 'event-items' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'mf_sectionid',
			__( 'Location', 'makerfaire' ),
			'mf_inner_location_box',
			$screen
		);

		// Add a map URL link for the location
		add_meta_box( 'mf-map-url', 'Map', 'mf_map_mb', 'location', 'side', 'default' );
	}
}
add_action( 'add_meta_boxes', 'mf_add_custom_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function mf_inner_location_box( $post ) {

	$faires 		= array();
	$faire_location = array();

	if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'] ) ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'mf_inner_location_box', 'mf_inner_location_box_nonce' );

		// Get all of the faires.
		$terms = $_POST['faire'];

		// Loop through the faires, and get all of the
		foreach ( $terms as $faire ) {
			// Need to attach the entire term object to this...
			$faires[ $faire ] = get_term( absint( $faire ), 'faire' );
		}
	}

	if ( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'mf_inner_location_box', 'mf_inner_location_box_nonce' );

		$faire_location_meta = get_post_meta( $post->ID, 'faire_location', true );

		$faire_location = ( is_array( $faire_location_meta ) ) ? $faire_location_meta : array() ;

		$faires = get_the_terms( $post, 'faire' );

	}

	// Since we have a few, loop through, and get all of the results.
	if ( $faires ) {

		$faire = array();

		foreach ( $faires as $da_faire ) {
			$faire[] = sanitize_title( $da_faire->slug );
		}

		// WP_Query arguments
		$args = array (
			'post_type'	=> 'location',
			'posts_per_page' => 200,
			'order' => 'ASC',
			'orderby' => 'title',
			'tax_query' => array(
				array(
					'taxonomy' 	=> 'faire',
					'field' 	=> 'slug',
					'terms' 	=> $faire,
				)
			)
		);

		// The Query
		$query = new WP_Query( $args );

		echo '<ul class="style="-moz-column-count:3;-moz-column-gap:20px;-webkit-column-count:3;-webkit-column-gap:20px;">';

		// Build the interface.
		foreach ( $query->posts as $location ) {

			// Display only the parent elements first.
			// If the parent has children, we'll handle that below
			if ( $location->post_parent == 0 ) {
				echo '<li style="-moz-column-count:3;-moz-column-gap:20px;-webkit-column-count:3;-webkit-column-gap:20px;"><label class="checkbox">';
				if ( in_array( $location->ID, $faire_location ) ) {
					echo '<input type="checkbox" name="location[]" value="' . absint( $location->ID ) .'" checked>';
				} else {
					echo '<input type="checkbox" name="location[]" value="' . absint( $location->ID ) .'">';
				}
				echo wp_kses_post( $location->post_title );
				echo '</label">';

				// Extract the children if they exist
				$children = get_page_children( absint( $location->ID ), $query->posts );

				if ( ! empty( $children ) ) {
					echo '<ul>';

					foreach ( $children as $child_loc ) {
						echo '<li> <strong>—</strong> &nbsp; <label class="checkbox">';
						if ( in_array( $child_loc->ID, $faire_location ) ) {
							echo '<input type="checkbox" name="location[]" value="' . absint( $child_loc->ID ) .'" checked>';
						} else {
							echo '<input type="checkbox" name="location[]" value="' . absint( $child_loc->ID ) .'">';
						}
						echo wp_kses_post( $child_loc->post_title );
						echo '</label></li>';
					}

					echo '</ul></li>';
				} else {
					echo '</li>';
				}
			}
		}

		echo '</ul>';


	} else {
		echo 'You don\'t have a location created for this event yet. Add one, and the appropriate locations will be added.';
	}

	// Add new locations
	echo '<p><a class="button" target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=location' ) ) . '">Add a New Location</a><p>';

}

add_action( 'wp_ajax_mf_inner_location_box', 'mf_inner_location_box' );

/**
 * Admin Javascript
 */
function location_admin_javascript() { ?>
	<script type="text/javascript" charset="utf-8" async defer>

		jQuery( document ).ready( function( $ ) {

			// Let's get an admin spinny gif...
			var spinner = '<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>';

			// And, let's build that into an image tag.
			var img =  '<img src="' + spinner + '" alt="Loading..." class="alignleft" style="padding-right:5px;">';

			// Let's see if we can get all of the locations.
			$('#fairechecklist').change( function() {

				// Setup the form object
				var form_obj = {};

				// Build an empty array for the locations to be held.
				var locations = [];

				// Loop though all of the options, and get all selected and them the locations array.
				$('#fairechecklist input').each( function() {
					if ( $( this ).is( ':checked' ) ) {
						val = jQuery( this ).attr( 'value' );
						locations.push( val );
					};
				});

				if ( locations.length == 0 ) {
					$('#mf_sectionid .inside').empty();
					return;
				};

				// Clear out the content, and bring the loading gif.
				$('#mf_sectionid .inside').empty().html( img + ' Loading...' );

				// Setup the form array.
				var form_obj = {};

				// Add the action, nonce, and faire to the object.
				form_obj.action 	= 'mf_inner_location_box';
				form_obj.nonce 		= '<?php echo wp_create_nonce( 'mf_inner_location_box' ); ?>';
				form_obj.faire 		= locations;

				// Kick of the AJAX request.
				$.ajax({
					url: ajaxurl,
					data: form_obj,
					type: 'POST',
					success: function( response ) {
						// If we get a response back, kick it into the meta box.
						$('#mf_sectionid .inside').empty().html( response );
					}
				});

			});

		});

	</script>
<?php }

add_filter( 'admin_head', 'location_admin_javascript' );

/**
 * Adds the Map URL meta box to locations
 * @param  obj $post The post object
 * @return void
 */
function mf_map_mb( $post ) {
	$map_url = get_post_meta( absint( $post->ID ), 'location-map', true );
	wp_nonce_field( 'mf_inner_location_box', 'mf_inner_location_box_nonce' );

	echo '<input type="text" name="mf-map-url" id="mf-map-url-field" value="' . ( ! empty( $map_url ) ? esc_url( $map_url ) : '' ) . '" style="width:100%;">';
	echo '<p>Please upload an image of the map with the location highlighted and paste URL here.</p>';
}


/**
 * When the post is saved, saves our location data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function mf_save_postdata( $post_id ) {
	/*
	* We need to verify this came from the our screen and with proper authorization,
	* because save_post can be triggered at other times.
	*/

	// Check if our nonce is set.
	if ( ! isset( $_POST['mf_inner_location_box_nonce'] ) )
		return $post_id;

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['mf_inner_location_box_nonce'], 'mf_inner_location_box' ) )
		return $post_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;

	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	/* OK, its safe for us to save the data now. */

	// Sanitize and save the locations
	if ( isset( $_POST['location'] ) ) {
		$locations = array();
		foreach ( $_POST['location'] as $location ) {
			$locations[] = absint( $location );
		}
		update_post_meta( $post_id, 'faire_location', $locations );
	} else {
		delete_post_meta( $post_id, 'faire_location' );
	}

	// Sanitize and save the map url
	if ( isset( $_POST['mf-map-url'] ) )
		update_post_meta( absint( $post_id ), 'location-map', esc_url( $_POST['mf-map-url'] ) );

}
add_action( 'save_post', 'mf_save_postdata' );


function mf_save_mapper( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['location_mapper'] ) )
		return $post_id;


	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['location_mapper'], 'location_mapper' ) )
		return $post_id;

	// Sanitize and save the locations
	if ( isset( $_POST['latitude'] ) ) {
		update_post_meta( $post_id, 'latitude', floatval( $_POST['latitude'] ) );
	} else {
		delete_post_meta( $post_id, 'latitude' );
	}

	// Sanitize and save the locations
	if ( isset( $_POST['longitude'] ) ) {
		update_post_meta( $post_id, 'longitude', floatval( $_POST['longitude'] ) );
	} else {
		delete_post_meta( $post_id, 'longitude' );
	}

}

add_action( 'save_post', 'mf_save_mapper' );

/**
 * Customize the columns for the locations
 * @param  Array $columns The array of column titles
 * @return Array
 */
function mf_location_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Name' ),
		'faire' => __( 'Faire' ),
		'date' => __( 'Date' )
	);

	return $columns;
}
add_filter( 'manage_edit-location_columns', 'mf_location_columns' );


/**
 * Add the content for our custom columns
 * @param  Array $column  The list of columns
 * @return HTML
 */
function mf_location_column_content( $column ) {
	global $post;

	switch ( $column ) {
		case 'faire' :
			$faires = get_the_terms( absint( $post->ID ), 'faire' );

			$i = 0;
			foreach ( $faires as $faire ) {
				if ( $i > 0 )
					echo ', ';

				echo esc_html( $faire->name );

				$i++;
			}

			break;
		default:
			break;
	}
}
add_action( 'manage_location_posts_custom_column', 'mf_location_column_content' );


/**
 * Return the list of locations based on the application ID
 * @param  int    $post_id The post ID to pull the locations from
 * @param  bool   $raw 	   Allows us to return the raw data of a location instead of a list of the titles
 * @return string          Returns the location title
 *
 * @since Optimus Prime
 */
function mf_get_locations( $post_id, $raw = false ) {
	$location_id = get_post_meta( absint( $post_id ), 'faire_location', true );

	if ( ! empty( $location_id ) ) {
		$loc_args = array(
			'post_type'	=> 'location',
			'post_per_page' => 10,
			'order' => 'ASC',
			'orderby' => 'title',
			'post__in' => $location_id,
		);
		$locations = new WP_Query( $loc_args );

		if ( ! $raw ) {
			$loc_end = end( $locations->posts );
			$loc_titles = '';
			foreach( $locations->posts as $location ) {
				$loc_titles .= esc_html( $location->post_title );


				if ( $location != $loc_end )
					$loc_titles .= ', ';
			}

			return $loc_titles;
		} else {
			return $locations->posts;
		}
	}
}


/**
 * Return the a list of all locations for a specific faire
 * This function also will cache the request to save any extended database calls.
 * @param  int    $post_id The post ID to pull the locations from
 * @return object          Returns the locations object
 *
 * @since Optimus Prime
 */
function mf_get_all_locations() {
	$locations = false;//wp_cache_get( 'location-all', 'locations' );
	if ( $locations === false ) {
		$loc_args = array(
			'post_type'	=> 'location',
			'posts_per_page' => 200,
			'order' => 'ASC',
			'orderby' => 'title',
			'tax_query' => array(
				array(
					'taxonomy' 	=> 'faire',
					'field' 	=> 'slug',
					'terms' 	=> sanitize_title( MF_CURRENT_FAIRE ),
				)
			)
		);
		$locations = new WP_Query( $loc_args );

		wp_cache_set( 'location-all', $locations, 'locations', 30 * MINUTE_IN_SECONDS );
	}

	return $locations->posts;
}


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function mf_location_mapper() {

	$screens = array( 'location' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'mf_sectionid',
			__( 'Location', 'makerfaire' ),
			'mf_inner_location_box',
			$screen
		);

		// Add a map URL link for the location
		add_meta_box( 'mf_location_mapper', 'Maker Faire Mapper', 'mf_mapper_inner', 'location', 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes', 'mf_location_mapper' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function mf_mapper_inner( $post ) { ?>
	<?php wp_nonce_field( 'location_mapper', 'location_mapper' ); ?>
	<div id="mapCanvas"></div>
	<div id="infoPanel">
		<b>Marker status:</b>
		<div id="markerStatus">
			<i>Click and drag the marker.</i>
		</div>
		<b>Current position:</b>
		<div id="info"></div>
		<b>Closest matching address:</b>
		<div id="address"></div>
		<label class="latitude form-input">Latitude: </label><input type="text" name="latitude" id="latitude" value="">
		<br>
		<label class="longitude form-input">Longitude: </label><input type="text" name="longitude" id="longitude" value="">
	</div>
	<div class="clear"></div>


<?php }

add_action( 'wp_ajax_mf_inner_location_box', 'mf_mapper_inner' );

/**
 * Admin Javascript for the mapper...
 */
function mapper_admin_javascript() { ?>
	<style>
		.form-input {
			width: 80px;
			display: inline-block;
		}
		#mapCanvas {
			width: 500px;
			height: 400px;
			float: left;
		}
		#infoPanel {
			float: left;
			margin-left: 10px;
		}
		#infoPanel div {
			margin-bottom: 5px;
		}
	</style>
	<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		var geocoder = new google.maps.Geocoder();

		function geocodePosition(pos) {
			geocoder.geocode({
				latLng: pos
			}, function(responses) {
				if (responses && responses.length > 0) {
					updateMarkerAddress(responses[0].formatted_address);
				} else {
					updateMarkerAddress('Cannot determine address at this location.');
				}
			});
		}

	function updateMarkerStatus(str) {
		document.getElementById('markerStatus').innerHTML = str;
	}

	function updateMarkerPosition(latLng) {
		document.getElementById('info').innerHTML = [
			latLng.lat(),
			latLng.lng()
		].join(', ');
		jQuery('#latitude').val( latLng.lat() );
		jQuery('#longitude').val( latLng.lng() );
	}

	function updateMarkerAddress(str) {
		document.getElementById('address').innerHTML = str;
	}

	function initialize() {
		var latLng = new google.maps.LatLng( <?php echo esc_js( mf_default_locations() ); ?> );
		var map = new google.maps.Map(document.getElementById('mapCanvas'), {
			zoom: 16,
			center: latLng,
			mapTypeId: google.maps.MapTypeId.HYBRID
		});
		var marker = new google.maps.Marker({
			position: latLng,
			title: 'Point A',
			map: map,
			draggable: true
		});

		// Update current position info.
		updateMarkerPosition(latLng);
		geocodePosition(latLng);

		// Add dragging event listeners.
		google.maps.event.addListener(marker, 'dragstart', function() {
			updateMarkerAddress('Dragging...');
		});

		google.maps.event.addListener(marker, 'drag', function() {
			updateMarkerStatus('Dragging...');
			updateMarkerPosition(marker.getPosition());
		});

		google.maps.event.addListener(marker, 'dragend', function() {
			updateMarkerStatus('Drag ended');
			geocodePosition(marker.getPosition());
		});
	}

	// Onload handler to fire off the app.
	google.maps.event.addDomListener(window, 'load', initialize);
	</script>
<?php }

add_filter( 'admin_head', 'mapper_admin_javascript' );

function mf_default_locations() {
	global $post;

	// The Defaults
	$gps = array(
		'latitude' => '37.54540624221166',
		'longitude' => '-122.30208400894168',
		);

	// Do we have anything in the DB?
	$db = array();
	$db['latitude']		= get_post_meta( get_the_id(), 'latitude', true );
	$db['longitude']	= get_post_meta( get_the_id(), 'longitude', true );

	if ( ! empty( $db['latitude'] ) && ! empty( $db['longitude'] ) ) {
		return floatval( $db['latitude'] ) . ', ' . floatval( $db['longitude'] );
	} else {
		return floatval( $gps['latitude'] ) . ', ' . floatval( $gps['longitude'] );
	}

}
