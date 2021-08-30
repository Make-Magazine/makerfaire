<?php

function maker_init() {
	register_post_type( 'maker', array(
		'hierarchical'        => false,
		'public'              => true,
		'show_in_nav_menus'   => true,
		'show_ui'             => true,
		'supports'            => array( 'title', 'editor' ),
		'has_archive'         => true,
		'query_var'           => true,
		'rewrite'             => true,
		'labels'              => array(
			'name'                => __( 'Makers', 'makerfaire' ),
			'singular_name'       => __( 'Maker', 'makerfaire' ),
			'add_new'             => __( 'Add new maker', 'makerfaire' ),
			'all_items'           => __( 'Makers', 'makerfaire' ),
			'add_new_item'        => __( 'Add new maker', 'makerfaire' ),
			'edit_item'           => __( 'Edit maker', 'makerfaire' ),
			'new_item'            => __( 'New maker', 'makerfaire' ),
			'view_item'           => __( 'View maker', 'makerfaire' ),
			'search_items'        => __( 'Search makers', 'makerfaire' ),
			'not_found'           => __( 'No makers found', 'makerfaire' ),
			'not_found_in_trash'  => __( 'No makers found in trash', 'makerfaire' ),
			'parent_item_colon'   => __( 'Parent maker', 'makerfaire' ),
			'menu_name'           => __( 'Makers', 'makerfaire' ),
		),
	) );

}
//add_action( 'init', 'maker_init' );

function maker_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['maker'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Maker updated. <a target="_blank" href="%s">View maker</a>', 'makerfaire'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'makerfaire'),
		3 => __('Custom field deleted.', 'makerfaire'),
		4 => __('Maker updated.', 'makerfaire'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Maker restored to revision from %s', 'makerfaire'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Maker published. <a href="%s">View maker</a>', 'makerfaire'), esc_url( $permalink ) ),
		7 => __('Maker saved.', 'makerfaire'),
		8 => sprintf( __('Maker submitted. <a target="_blank" href="%s">Preview maker</a>', 'makerfaire'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Maker scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview maker</a>', 'makerfaire'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Maker draft updated. <a target="_blank" href="%s">Preview maker</a>', 'makerfaire'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'maker_updated_messages' );


/**
 * Add a custom meta box to display and handle custom user data
 * @return void
 *
 * @since  SPRINT NAME
 */
function maker_add_meta_boxes() {
	add_meta_box( 'maker-user-data', __( 'User Data', 'makerfaire' ), 'maker_user_data_mb', 'maker' );
	add_meta_box( 'maker-past-faires', 'Past Faires', 'maker_past_faires', 'maker', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'maker_add_meta_boxes' );


/**
 * Output our post meta
 * @param  Object $post the post object
 * @return string
 */
function maker_user_data_mb( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'maker_user_data' );
	$user_data = get_post_custom( $post->ID );

	if ( ! empty( $user_data ) ) {
		foreach ( $user_data as $key => $value ) {
			$mfei = ( $key == 'mfei_record' ) ? ' disabled="disabled"' : '';

			// Hide the WP _edit_lock meta field
			if ( $key != '_edit_lock' ) {
				$output  = '<p><label for="' . esc_attr( $key ) . '" style="width:100px;display:inline-block">' . strtoupper( str_replace( '_', ' ', esc_html( $key ) ) ) . '</label>';
				$output .= '<input type="text" name="user_data[' . esc_attr( $key ) . ']" id="' . esc_attr( $key ) . '" value="' . ( ! empty( $value[0] ) ? esc_attr( $value[0] ) : '' ) . '"' . $mfei . ' style="width:100%;" /></p>';

				echo $output;
			}
		}
	} else {
		echo '<p>No Data Set!</p>';
	}
}


function maker_past_faires( $post ) {
	$maker_id = get_post_meta( absint( $post->ID ), 'guid', true );
	$apps = maker_get_apps_by_id( $maker_id );
	$faire_names = array(
		'2014_newyork' => 'World Maker Faire New York 2014',
		'2014_bayarea' => 'Maker Faire Bay Area 2014',
		'2013_newyork' => 'World Maker Faire New York 2013',
		'2013_bayarea' => 'Maker Faire Bay Area 2013',
	);
	$output = '';

	if ( $apps !== false ) {
		foreach ( $apps as $type => $application ) {
			$output .= '<h2 class="application-type" style="border-bottom:1px solid #ccc;">' . esc_html( ucfirst( $type ) ) . '</h2>';
			$output .= '<table class="widefat fixed"><thead style="text-align:left;background:#ddd;color:#000;"><th>Title</th><th>Status</th><th>App ID</th><th>Faire</th></thead><tbody>';
			$i = 0;
			// Now let's spit out the applications for this type
			foreach ( $application as $app ) {
				$class = ( $i++ % 2 == 1 ) ? ' class="alternate"' : '';
				$output .= '<tr' . $class . '>';
				$output .= '<td><a href="' . esc_url( admin_url( '/post.php?post=' . absint( $app->ID ) . '&action=edit' ) ) . '">' . esc_html( $app->post_title ) . '</a></td>';
				$output .= '<td>' . esc_html( str_replace( '-', ' ', ucfirst( $app->post_status ) ) ) . '</td>';
				$output .= '<td>' . absint( $app->ID ) . '</td>';
				$output .= '<td>' . esc_html( $faire_names[ $app->data->maker_faire ] ) . '</td>';
				$output .= '</tr>';
			}

			$output .= '</tbody></table>';
		}
	} else {
		$output = 'No applications found!';
	}

	echo $output;
}


function maker_save_user_data( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! isset( $_POST['maker_user_data'] ) || ! wp_verify_nonce( $_POST['maker_user_data'], basename( __FILE__ ) ) )
		return;

	if ( ! current_user_can( 'edit_post', $post_id ) )
		return;

	if ( get_post_type() == 'maker' && isset( $_POST['user_data'] ) && ! empty( $_POST['user_data'] ) ) {
		foreach ( $_POST['user_data'] as $key => $value ) {
			update_post_meta( $post_id, sanitize_text_field( $key ), sanitize_text_field( $value ) );
		}
	}
}
add_action( 'save_post', 'maker_save_user_data' );


/**
 * Featches applications based on maker ID
 * @param  String $maker_id The Gigya ID saved to an application
 * @return Array
 *
 * @since Mechani-Kong
 */
function maker_get_apps_by_id( $maker_id ) {
	$applications = array(
		'exhibit' => array(),
		'presenter' => array(),
		'performer' => array(),
	);

	// Find applications for the current maker
	$args = array(
		'post_type' => 'mf_form',
		'post_status' => 'any',
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => 'mf_gigya_id',
				'value' => sanitize_text_field( $maker_id ),
			),
			array(
				'key' => 'mf_additional_user',
				'value' => sanitize_text_field( $maker_id ),
			),
		),
	);
	$apps = new WP_Query( $args );

	if ( $apps->found_posts !== 0 ) {
		// Loop through all the applications
		foreach ( $apps->posts as $app ) {
			$data = json_decode( str_replace( "\'", "'", $app->post_content ) );
			$app->data = $data;
			$app = ( isset( $data->form_type ) && isset( $app->ID ) && ( !empty( $applications[ $data->form_type ][ $app->ID ] ) ) ) ? $applications[ $data->form_type ][ $app->ID ] : '';
		}
	} else {
		$applications = false;
	}

	return $applications;

}


/**
 * Adds the custom column titles to the maker custom post type
 * @param  array $defaults The default column headers
 * @return array
 */
function maker_faire_columns_head_maker( $defaults ) {
	// Remove date so we can move it another location
	unset( $defaults['date'] );

	// Set up our columns
	$defaults = array(
		'title' => 'Maker Name',
		'mid' 	=> 'Maker ID',
		'gid' 	=> 'Gigya ID',
		'email' => 'Email',
		'date' 	=> 'Created',
	);

	return $defaults;
}
add_filter( 'manage_maker_posts_columns', 'maker_faire_columns_head_maker', 10 );


/**
 * Handles the content for each maker column
 * @param  string  $column_name The name of the column being rendered
 * @param  integer $maker_id    The maker id
 * @return string
 */
function maker_faire_columns_content_maker( $column_name, $maker_id ) {

	// Return the data for the maker ID
	switch ( $column_name ) :
		case 'mid' :
			echo absint( $maker_id );
			break;

		case 'gid' :
			$guid = get_post_meta( absint( $maker_id ), 'guid', true );
			echo ( ! empty( $guid) ) ? esc_html( $guid ) : '';
			break;

		case 'email' :
			$email = get_post_meta( absint( $maker_id ), 'email', true );
			echo ( ! empty( $email ) ) ? esc_html( $email ) : '';
			break;

	endswitch;
}
add_filter( 'manage_maker_posts_custom_column', 'maker_faire_columns_content_maker', 10, 2 );
