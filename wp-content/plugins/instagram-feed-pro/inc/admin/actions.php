<?php
/**
 * Includes functions related to actions while in the admin area.
 *
 * - All AJAX related features
 * - Enqueueing of JS and CSS files
 * - Settings link on "Plugins" page
 * - Creation of local avatar image files
 * - Connecting accounts on the "Configure" tab
 * - Displaying admin notices
 * - Clearing caches
 * - License renewal
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function sb_instagram_admin_style() {
	wp_register_style( 'sb_instagram_admin_css', SBI_PLUGIN_URL . 'css/sb-instagram-admin.css', array(), SBIVER );
	wp_enqueue_style( 'sb_instagram_font_awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
	wp_enqueue_style( 'sb_instagram_admin_css' );
	wp_enqueue_style( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'sb_instagram_admin_style' );

function sb_instagram_admin_scripts() {
	wp_enqueue_script( 'sb_instagram_admin_js', SBI_PLUGIN_URL . 'js/sb-instagram-admin.js', array(), SBIVER );
	wp_localize_script( 'sb_instagram_admin_js', 'sbiA', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'sbi_nonce' => wp_create_nonce( 'sbi_nonce' )
		)
	);
	$strings = array(
		'addon_activate'                  => esc_html__( 'Activate', 'instagram-feed' ),
		'addon_activated'                 => esc_html__( 'Activated', 'instagram-feed' ),
		'addon_active'                    => esc_html__( 'Active', 'instagram-feed' ),
		'addon_deactivate'                => esc_html__( 'Deactivate', 'instagram-feed' ),
		'addon_inactive'                  => esc_html__( 'Inactive', 'instagram-feed' ),
		'addon_install'                   => esc_html__( 'Install Addon', 'instagram-feed' ),
		'addon_error'                     => esc_html__( 'Could not install addon. Please download from wpforms.com and install manually.', 'instagram-feed' ),
		'plugin_error'                    => esc_html__( 'Could not install a plugin. Please download from WordPress.org and install manually.', 'instagram-feed' ),
		'addon_search'                    => esc_html__( 'Searching Addons', 'instagram-feed' ),
		'ajax_url'                        => admin_url( 'admin-ajax.php' ),
		'cancel'                          => esc_html__( 'Cancel', 'instagram-feed' ),
		'close'                           => esc_html__( 'Close', 'instagram-feed' ),
		'nonce'                           => wp_create_nonce( 'sbi-admin' ),
		'almost_done'                     => esc_html__( 'Almost Done', 'instagram-feed' ),
		'oops'                            => esc_html__( 'Oops!', 'instagram-feed' ),
		'ok'                              => esc_html__( 'OK', 'instagram-feed' ),
		'plugin_install_activate_btn'     => esc_html__( 'Install and Activate', 'instagram-feed' ),
		'plugin_install_activate_confirm' => esc_html__( 'needs to be installed and activated to import its forms. Would you like us to install and activate it for you?', 'instagram-feed' ),
		'plugin_activate_btn'             => esc_html__( 'Activate', 'instagram-feed' ),
	);
	$strings = apply_filters( 'sbi_admin_strings', $strings );
	wp_localize_script(
		'sb_instagram_admin_js',
		'sbi_admin',
		$strings
	);
	if( !wp_script_is('jquery-ui-draggable') ) {
		wp_enqueue_script(
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-draggable'
			)
		);
	}
	wp_enqueue_script(
		array(
			'hoverIntent',
			'wp-color-picker'
		)
	);
}
add_action( 'admin_enqueue_scripts', 'sb_instagram_admin_scripts' );

// Add a Settings link to the plugin on the Plugins page
$sbi_plugin_file = 'instagram-feed-pro/instagram-feed.php';
add_filter( "plugin_action_links_{$sbi_plugin_file}", 'sbi_add_settings_link', 10, 2 );

//modify the link by unshifting the array
function sbi_add_settings_link( $links, $file ) {
	$sbi_settings_link = '<a href="' . admin_url( 'admin.php?page=sb-instagram-feed' ) . '">' . __( 'Settings', 'instagram-feed' ) . '</a>';
	array_unshift( $links, $sbi_settings_link );

	return $links;
}

/**
 * Called via ajax to automatically save access token and access token secret
 * retrieved with the big blue button
 */
function sbi_auto_save_tokens() {
	$nonce = $_POST['sbi_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'sbi_nonce' ) ) {
		die ( 'You did not do this the right way!' );
	}

	wp_cache_delete ( 'alloptions', 'options' );

	$options = sbi_get_database_settings();
	$new_access_token = isset( $_POST['access_token'] ) ? sanitize_text_field( $_POST['access_token'] ) : false;
	$split_token = $new_access_token ? explode( '.', $new_access_token ) : array();
	$new_user_id = isset( $split_token[0] ) ? $split_token[0] : '';

	$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
	$test_connection_data = sbi_account_data_for_token( $new_access_token );

	$connected_accounts[ $new_user_id ] = array(
		'access_token' => sbi_get_parts( $new_access_token ),
		'user_id' => $test_connection_data['id'],
		'username' => $test_connection_data['username'],
		'is_valid' => true,
		'last_checked' => $test_connection_data['last_checked'],
		'profile_picture' => $test_connection_data['profile_picture'],
	);

	delete_transient( SBI_USE_BACKUP_PREFIX . 'sbi_'  . $new_user_id );

	global $sb_instagram_posts_manager;
	if ( !$sb_instagram_posts_manager->image_resizing_disabled( $test_connection_data['username'] ) ) {
		if ( sbi_create_local_avatar( $test_connection_data['username'], $test_connection_data['profile_picture'] ) ) {
			$connected_accounts[ $new_user_id ]['local_avatar'] = true;
		}
	} else {
		$connected_accounts[ $new_user_id ]['local_avatar'] = false;
	}

	$connected_accounts[ $new_user_id ] = SB_Instagram_Connected_Account::encrypt_connected_account_tokens( $connected_accounts[ $new_user_id ] );


	$options['connected_accounts'] = $connected_accounts;

	update_option( 'sb_instagram_settings', $options );

	echo sbi_json_encode( $connected_accounts[ $new_user_id ] );

	die();
}
add_action( 'wp_ajax_sbi_auto_save_tokens', 'sbi_auto_save_tokens' );
function sbi_delete_local_avatar( $username ) {
	$upload = wp_upload_dir();

	$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( SBI_UPLOADS_NAME ) . $username . '.jpg'  ); // get all matching images
	foreach ( $image_files as $file ) { // iterate files
		if ( is_file( $file ) ) {
			unlink( $file );
		}
	}
}

function sbi_connect_basic_account( $new_account_details ) {

	$options = sbi_get_database_settings();
	$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

	$accounts_to_save = array();
	$old_account_user_id = '';
	$ids_to_save = array();
	$user_ids = is_array( $options[ 'sb_instagram_user_id' ] ) ? $options[ 'sb_instagram_user_id' ] : explode( ',', str_replace( ' ', '', $options[ 'sb_instagram_user_id' ] ) );

	$profile_picture = '';

	// do not connect as a basic display account if already connected as a business account
	if ( isset( $connected_accounts[ $new_account_details['user_id'] ] )
         && isset( $connected_accounts[ $new_account_details['user_id'] ]['type'] )
        && $connected_accounts[ $new_account_details['user_id'] ]['type'] === 'business' ) {
	    return $options;
    }

	foreach ( $connected_accounts as $account ) {
	    $account_type = isset( $account['type'] ) ? $account['type'] : 'personal';
	    if ( ($account['username'] !== $new_account_details['username'])
              || $account_type === 'business' ) {
		    $accounts_to_save[ $account['user_id'] ] = $account;
        } else {
		    $old_account_user_id = $account['user_id'];
		    $profile_picture = isset( $account['profile_picture'] ) ? $account['profile_picture'] : '';
	    }
	}

	foreach ( $user_ids as $id ) {
		if ( $id === $old_account_user_id ) {
			$ids_to_save[] = $new_account_details['user_id'];
		} else {
			$ids_to_save[] = $id;
		}
	}

	$accounts_to_save[ $new_account_details['user_id'] ] = array(
		'access_token' => sbi_fixer( $new_account_details['access_token'] ),
		'user_id' => $new_account_details['user_id'],
		'username' => $new_account_details['username'],
		'is_valid' => true,
		'last_checked' => time(),
		'expires_timestamp' => $new_account_details['expires_timestamp'],
		'profile_picture' => $profile_picture,
		'account_type' => strtolower( $new_account_details['account_type'] ),
		'type' => 'basic',
	);

	if ( ! empty( $old_account_user_id ) && $old_account_user_id !== $new_account_details['user_id'] ) {
		$accounts_to_save[ $new_account_details['user_id'] ]['old_user_id'] = $old_account_user_id;

		// get last saved header data
		$fuzzy_matches = sbi_fuzzy_matching_header_data( $old_account_user_id );
		if ( ! empty( $fuzzy_matches[0] ) ) {
		    $header_data = sbi_find_matching_data_from_results( $fuzzy_matches, $old_account_user_id );
            $bio = SB_Instagram_Parse::get_bio( $header_data );
			$accounts_to_save[ $new_account_details['user_id'] ]['bio'] = sbi_sanitize_emoji( $bio );
        }

    }

	if ( ! empty( $profile_picture ) && !$options['sb_instagram_disable_resize'] ) {
		if ( sbi_create_local_avatar( $new_account_details['username'], $profile_picture ) ) {
			$accounts_to_save[ $new_account_details['user_id'] ]['local_avatar'] = true;
		}
	} else {
		$accounts_to_save[ $new_account_details['user_id'] ]['local_avatar'] = false;
	}

	$accounts_to_save[ $new_account_details['user_id'] ] = SB_Instagram_Connected_Account::encrypt_connected_account_tokens( $accounts_to_save[ $new_account_details['user_id'] ] );


	delete_transient( SBI_USE_BACKUP_PREFIX . 'sbi_'  . $new_account_details['user_id'] );
	$refresher = new SB_Instagram_Token_Refresher( $accounts_to_save[ $new_account_details['user_id'] ] );
	$refresher->attempt_token_refresh();

	$error_type = 'all';
	if ( $refresher->get_last_error_code() === 10 ) {
		$accounts_to_save[ $new_account_details['user_id'] ]['private'] = true;
		$error_type = 'api';
    }

	$options['connected_accounts'] = $accounts_to_save;
	$options['sb_instagram_user_id'] = $ids_to_save;

	update_option( 'sb_instagram_settings', $options );
	global $sb_instagram_posts_manager;

	$sb_instagram_posts_manager->remove_connected_account_error( $accounts_to_save[ $new_account_details['user_id'] ], $error_type );
	return $options;
}

function sbi_fuzzy_matching_header_data( $user_id ) {

	if ( empty( $user_id ) || strlen( $user_id ) < 4 ) {
		return array();
	}
	global $wpdb;
	$escaped_id = esc_sql( $user_id );

	$values = $wpdb->get_results( "
    SELECT option_value
    FROM $wpdb->options
    WHERE option_name LIKE ('%!sbi\_header\_".$escaped_id."%')
    LIMIT 10", ARRAY_A );

	$regular_values = $wpdb->get_results( "
    SELECT option_value
    FROM $wpdb->options
    WHERE option_name LIKE ('%sbi\_header\_".$escaped_id."%')
    LIMIT 10", ARRAY_A );

	$values = array_merge( $values, $regular_values );

	return $values;
}

function sbi_find_matching_data_from_results( $results, $user_id ) {

    $match = array();

    $i = 0;

    while( empty( $match ) && isset( $results[ $i ] ) ) {
	    if ( ! empty( $results[ $i ] ) ) {
		    $header_data = json_decode( $results[ $i ]['option_value'], true );
		    if ( isset( $header_data['id'] ) && (string)$header_data['id'] === (string)$user_id ) {
			    $match = $header_data;
		    }
	    }
        $i++;
    }

    return $match;
}

function sbi_matches_existing_personal( $new_account_details ) {

	$options = sbi_get_database_settings();
	$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

	$matches_one_account = false;
	$accounts_to_save = array();
	foreach ( $connected_accounts as $account ) {
	    $account_type = isset( $account['type'] ) ? $account['type'] : 'personal';
		if ( ($account_type === 'personal' || $account_type === 'basic')
            && $account['username'] == $new_account_details['username'] ) {
			$matches_one_account = true;


		}
	}

	return $matches_one_account;

}

function sbi_connect_business_accounts() {
	$nonce = $_POST['sbi_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'sbi_nonce' ) ) {
		die ( 'You did not do this the right way!' );
	}

	$accounts = isset( $_POST['accounts'] ) ? json_decode( stripslashes( $_POST['accounts'] ), true ) : false;
	$options = sbi_get_database_settings();
	$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
	//$new_user_names = array();

    $return = array();
	foreach ( $accounts as $account ) {
		$account['type'] = 'business';

	    $connector = new SBI_Account_Connector();

	    $connector->add_account_data( $account );
	    if ( $connector->update_stored_account() ) {
		    $connector->after_update();

		    $return[ $connector->get_id() ] = $connector->get_account_data();
	    }
		//global $sb_instagram_posts_manager;

		//$sb_instagram_posts_manager->remove_error( 'at_' . $username );
		//delete_transient( SBI_USE_BACKUP_PREFIX . 'sbi_'  . $user_id );
	}
/*
	$accounts_to_save = array();
	foreach ( $connected_accounts as $account ) {
		$account_type = isset( $account['type'] ) ? $account['type'] : 'personal';
		if ( $account_type !== 'personal' || ! in_array( $account['username'], $new_user_names, true ) ) {
			$accounts_to_save[ $account['user_id'] ] = $account;
		}
	}

	$options['connected_accounts'] = $accounts_to_save;

	update_option( 'sb_instagram_settings', $options );
	global $sb_instagram_posts_manager;

	$sb_instagram_posts_manager->remove_all_errors();
*/

// What happens?
	echo sbi_json_encode( $return );

	die();
}
add_action( 'wp_ajax_sbi_connect_business_accounts', 'sbi_connect_business_accounts' );

function sbi_auto_save_id() {
	$nonce = $_POST['sbi_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'sbi_nonce' ) ) {
		die ( 'You did not do this the right way!' );
	}
	if ( current_user_can( 'edit_posts' ) && isset( $_POST['id'] ) ) {
		$options = get_option( 'sb_instagram_settings', array() );

		$options['sb_instagram_user_id'] = array( sanitize_text_field( $_POST['id'] ) );

		update_option( 'sb_instagram_settings', $options );
	}
	die();
}
add_action( 'wp_ajax_sbi_auto_save_id', 'sbi_auto_save_id' );

function sbi_formatted_error( $response ) {
	if ( isset( $response['error'] ) ) {
		$error = '<p>' . sprintf( __( 'API error %s:', 'instagram-feed' ), $response['error']['code'] ) . ' ' . $response['error']['message'] . '</p>';
		$error .= '<p class="sbi-error-directions"><a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on how to resolve this issue', 'instagram-feed' ) . '</a></p>';

		return $error;
	} else {
		$message = '<p>' . sprintf( __( 'Error connecting to %s.', 'instagram-feed' ), $response['url'] ). '</p>';
		if ( isset( $response['response'] ) && isset( $response['response']->errors ) ) {
			foreach ( $response['response']->errors as $key => $item ) {
				'<p>' .$message .= ' '.$key . ' - ' . $item[0] . '</p>';
			}
		}
		$message .= '<p class="sbi-error-directions"><a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on how to resolve this issue', 'instagram-feed' ) . '</a></p>';

		return $message;
	}
}

function sbi_test_token() {
	$access_token = isset( $_POST['access_token'] ) ? trim( sanitize_text_field( $_POST['access_token'] ) ) : false;
	$account_id = isset( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : false;

	$return = sbi_connect_new_account( $access_token, $account_id );

	echo $return;
	wp_die();
}
add_action( 'wp_ajax_sbi_test_token', 'sbi_test_token' );

function sbi_delete_account() {
	$nonce = $_POST['sbi_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'sbi_nonce' ) ) {
		die ( 'You did not do this the right way!' );
	}
	$account_id = isset( $_POST['account_id'] ) ? sanitize_text_field( $_POST['account_id'] ) : false;

	sbi_do_account_delete( $account_id );

	die();
}
add_action( 'wp_ajax_sbi_delete_account', 'sbi_delete_account' );

function sbi_do_account_delete( $account_id ) {
	$options = get_option( 'sb_instagram_settings', array() );
	$connected_accounts =  isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->reset_api_errors();
	wp_cache_delete ( 'alloptions', 'options' );
	$username = $connected_accounts[ $account_id ]['username'];
	$sb_instagram_posts_manager->add_action_log( 'Deleting account ' . $username );

	$num_times_used = 0;

	$new_con_accounts = array();
	foreach ( $connected_accounts as $connected_account ) {

		if ( $connected_account['username'] === $username ) {
			$num_times_used++;
		}

		if ( $connected_account['username'] !== '' && $account_id !== $connected_account['user_id'] && ! empty( $connected_account['user_id'] ) ) {
			$new_con_accounts[ $connected_account['user_id'] ] = $connected_account;
		}
	}

	if ( $num_times_used < 2 ) {
		sbi_delete_local_avatar( $username );
	}

	$options['connected_accounts'] = $new_con_accounts;

	update_option( 'sb_instagram_settings', $options );
}

function sbi_account_data_for_token( $access_token ) {
	$return = array(
		'id' => false,
		'username' => false,
		'is_valid' => false,
		'last_checked' => time()
	);
	$url = 'https://api.instagram.com/v1/users/self/?access_token=' . sbi_maybe_clean( $access_token );
	$args = array(
		'timeout' => 30
	);
	if ( version_compare( get_bloginfo( 'version' ), '3.7' , '<' ) ) {
		$args['sslverify'] = false;
	}
	$result = wp_remote_get( $url, $args );

	if ( ! is_wp_error( $result ) ) {
		$data = json_decode( $result['body'] );
	} else {
		$data = array();
	}

	if ( isset( $data->data->id ) ) {
		$return['id'] = $data->data->id;
		$return['username'] = $data->data->username;
		$return['is_valid'] = true;
		$return['profile_picture'] = $data->data->profile_picture;

	} elseif ( isset( $data->error_type ) && $data->error_type === 'OAuthRateLimitException' ) {
		$return['error_message'] = 'This account\'s access token is currently over the rate limit. Try removing this access token from all feeds and wait an hour before reconnecting.';
	} else {
		$return = false;
	}

	$sbi_options = get_option( 'sb_instagram_settings', array() );
	$sbi_options['sb_instagram_at'] = '';
	update_option( 'sb_instagram_settings', $sbi_options );

	return $return;
}

function sbi_get_connected_accounts_data( $sb_instagram_at ) {
	$sbi_options = get_option( 'sb_instagram_settings' );
	$return = array();
	$return['connected_accounts'] = isset( $sbi_options['connected_accounts'] ) ? $sbi_options['connected_accounts'] : array();

	if ( ! empty( $return['connected_accounts'] ) ) {
		$return['access_token'] = '';
	} else {
		$return['access_token'] = $sb_instagram_at;
	}

	if ( ! sbi_is_after_deprecation_deadline() && empty( $connected_accounts ) && ! empty( $sb_instagram_at ) ) {
		$tokens = explode(',', $sb_instagram_at );
		$user_ids = array();

		foreach ( $tokens as $token ) {
			$account = sbi_account_data_for_token( $token );
			if ( isset( $account['is_valid'] ) ) {
				$split = explode( '.', $token );
				$return['connected_accounts'][ $split[0] ] = array(
					'access_token' => sbi_get_parts( $token ),
					'user_id' => $split[0],
					'username' => '',
					'is_valid' => true,
					'last_checked' => time(),
					'profile_picture' => ''
				);
				$user_ids[] = $split[0];
			}

		}

		$sbi_options['connected_accounts'] = $return['connected_accounts'];
		$sbi_options['sb_instagram_at'] = '';
		$sbi_options['sb_instagram_user_id'] = $user_ids;

		$return['user_ids'] = $user_ids;

		update_option( 'sb_instagram_settings', $sbi_options );
	}

	return $return;
}

function sbi_business_account_request( $url, $account, $remove_access_token = true ) {
	$args = array(
		'timeout' => 30
	);
	if ( version_compare( get_bloginfo( 'version' ), '3.7' , '<' ) ) {
		$args['sslverify'] = false;
	}
	$result = wp_remote_get( $url, $args );

	if ( ! is_wp_error( $result ) ) {
		$response_no_at = $remove_access_token ? str_replace( sbi_maybe_clean( $account['access_token'] ), '{accesstoken}', $result['body'] ) : $result['body'];
		return $response_no_at;
	} else {
		return sbi_json_encode( $result );
	}
}

function sbi_after_connection() {

	if ( isset( $_POST['access_token'] ) ) {
		$access_token = sanitize_text_field( $_POST['access_token'] );
		$account_info = 	sbi_account_data_for_token( $access_token );
		echo json_encode( $account_info );
	}

	die();
}
add_action( 'wp_ajax_sbi_after_connection', 'sbi_after_connection' );

function sbi_clear_backups() {
	$nonce = isset( $_POST['sbi_nonce'] ) ? sanitize_text_field( $_POST['sbi_nonce'] ) : '';

	if ( ! wp_verify_nonce( $nonce, 'sbi_nonce' ) ) {
		die ( 'You did not do this the right way!' );
	}

	//Delete all transients
	global $wpdb;
	$table_name = $wpdb->prefix . "options";
	$wpdb->query( "
    DELETE
    FROM $table_name
    WHERE `option_name` LIKE ('%!sbi\_%')
    " );
	$wpdb->query( "
    DELETE
    FROM $table_name
    WHERE `option_name` LIKE ('%\_transient\_&sbi\_%')
    " );
	$wpdb->query( "
    DELETE
    FROM $table_name
    WHERE `option_name` LIKE ('%\_transient\_timeout\_&sbi\_%')
    " );
	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->add_action_log( 'Clearing backup caches.' );
	die();
}
add_action( 'wp_ajax_sbi_clear_backups', 'sbi_clear_backups' );

function sbi_clear_comment_cache() {

	if ( delete_transient( 'sbinst_comment_cache' ) ) {
		return true;
	} elseif ( ! get_transient( 'sbinst_comment_cache' ) ) {
		return true;
	}

	die();
}
add_action( 'wp_ajax_sbi_clear_comment_cache', 'sbi_clear_comment_cache' );

function sbi_reset_resized() {

	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->delete_all_sbi_instagram_posts();
	delete_option( 'sbi_top_api_calls' );

	$sb_instagram_posts_manager->add_action_log( 'Reset resizing tables.' );

	echo "1";

	die();
}
add_action( 'wp_ajax_sbi_reset_resized', 'sbi_reset_resized' );

function sbi_reset_log() {
	global $sb_instagram_posts_manager;

	$sb_instagram_posts_manager->remove_all_errors();

	echo "1";

	die();
}
add_action( 'wp_ajax_sbi_reset_log', 'sbi_reset_log' );

function sbi_reset_api_errors() {
	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->add_action_log( 'View feed and retry button clicked.' );

	$sb_instagram_posts_manager->reset_api_errors();

	echo "1";

	die();
}
add_action( 'wp_ajax_sbi_reset_api_errors', 'sbi_reset_api_errors' );

function sbi_clear_white_lists() {
	global $wpdb;
	$table_name = $wpdb->prefix . "options";
	$result = $wpdb->query("
    DELETE
    FROM $table_name
    WHERE `option_name` LIKE ('%sb_instagram_white_lists_%')
    ");
	$result = $wpdb->query("
    DELETE
    FROM $table_name
    WHERE `option_name` LIKE ('%sb_wlupdated_%')
    ");
	delete_option( 'sb_instagram_white_list_names' );
	delete_option( 'sb_permanent_white_lists' );
	return $result;

	die();
}
add_action( 'wp_ajax_sbi_clear_white_lists', 'sbi_clear_white_lists' );

function sbi_disable_permanent_white_lists() {
	delete_option( 'sb_permanent_white_lists' );
	die();
}
add_action( 'wp_ajax_sbi_disable_permanent_white_lists', 'sbi_disable_permanent_white_lists' );

function sbi_delete_platform_data_listener() {
    if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
        die( 'No!' );
    }
	sbi_delete_all_platform_data();

	wp_send_json_success();
}
add_action( 'wp_ajax_sbi_delete_platform_data', 'sbi_delete_platform_data_listener' );

add_action('admin_notices', 'sbi_admin_error_notices');
function sbi_admin_error_notices() {

	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'sb-instagram-feed' )) ) {

		global $sb_instagram_posts_manager;

		$errors = $sb_instagram_posts_manager->get_errors();
		if ( ! empty( $errors ) && (! empty( $errors['database_create'] ) || ! empty( $errors['upload_dir'] )) ) : ?>
            <div class="notice notice-warning is-dismissible sbi-admin-notice">
	            <?php if ( ! empty( $errors['database_create'] ) ) echo '<p>' . $errors['database_create'] . '</p>'; ?>

				<?php if ( ! empty( $errors['upload_dir'] ) ) echo '<p>' . $errors['upload_dir'] . '</p>'; ?>
                <p><?php _e( sprintf( 'Visit our %s page for help', '<a href="https://smashballoon.com/instagram-feed/support/faq/" target="_blank">FAQ</a>' ), 'instagram-feed' ); ?></p>

            </div>

		<?php endif;
		$errors = $sb_instagram_posts_manager->get_critical_errors();
		if ( $sb_instagram_posts_manager->are_critical_errors() && ! empty( $errors ) ) : ?>
            <div class="notice notice-warning is-dismissible sbi-admin-notice">
                <p><strong><?php echo esc_html__( 'Instagram Feed is encountering an error and your feeds may not be updating due to the following reasons:', 'instagram-feed') ; ?></strong></p>

				<?php echo $errors; ?>

				<?php
				$error_page = $sb_instagram_posts_manager->get_error_page();
				if ( $error_page ) {
					echo '<a href="' . get_the_permalink( $error_page ) . '" class="sbi-clear-errors-visit-page sbi-space-left button button-secondary">' . __( 'View Feed and Retry', 'instagram-feed' ) . '</a>';
				}

            if ( $sb_instagram_posts_manager->was_app_permission_related_error() ) :
                $accounts_revoked = $sb_instagram_posts_manager->get_app_permission_related_error_ids();
                if ( count( $accounts_revoked ) > 1 ) {
                    $accounts_revoked = implode( ', ', $accounts_revoked );
                } else {
	                $accounts_revoked = $accounts_revoked[0];
                }
                ?>
                <p class="sbi_notice"><?php echo sprintf( __('Instagram Feed related data for the account(s) %s was removed due to permission for the Smash Balloon App on Facebook or Instagram being revoked.', 'instagram-feed'), $accounts_revoked ); ?></p>
            <?php endif;?>
            </div>
		<?php endif;
	}

}

add_action('admin_notices', 'sbi_admin_ssl_notice');
function sbi_admin_ssl_notice() {

	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'sb-instagram-feed' )) ) {
		global $current_user;
		$user_id = $current_user->ID;
		$was_dismissed = get_user_meta($user_id, 'sbi_ignore_openssl', true);

		if ( ! $was_dismissed && ! sbi_doing_openssl() ) : ?>
            <div class="notice notice-warning is-dismissible sbi-admin-notice">
                <p><?php _e( sprintf( 'Instagram Feed recommends Open SSL for encrypting access tokens in your database. Contact your host or follow %1$sthese%2$s directions.', '<a href="https://www.php.net/manual/en/openssl.installation.php" target="_blank">', '</a>' ), 'instagram-feed' ); ?> <a href="<?php echo admin_url( 'admin.php?page=sb-instagram-feed&openssldismiss=1' ); ?>"><?php _e('Dismiss', 'instagram-feed' ); ?></a></p>
            </div>
		<?php endif;
	}

}

function sbi_get_connect_account_button( $page = 'admin.php?page=sb-instagram-feed' ) {
    $connect_url = 'https://connect.smashballoon.com/auth/ig/?wordpress_user=&v=pro&state=' . admin_url( $page );
	?>
    <a data-personal-basic-api="https://api.instagram.com/oauth/authorize?client_id=423965861585747&redirect_uri=https://api.smashballoon.com/v2/instagram-basic-display-redirect.php&response_type=code&scope=user_profile,user_media&state=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>"
       data-new-api="<?php echo esc_attr( $connect_url ); ?>"
       href="https://api.instagram.com/oauth/authorize?client_id=423965861585747&redirect_uri=https://api.smashballoon.com/v2/instagram-basic-display-redirect.php&response_type=code&scope=user_profile,user_media&state=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>" class="sbi_admin_btn"><i class="fa fa-user-plus" aria-hidden="true" style="font-size: 20px;"></i>&nbsp; <?php _e('Connect an Instagram Account', 'instagram-feed' ); ?></a>
    <?php
}

function sbi_get_business_account_connection_modal($sb_instagram_user_id) {
	$access_token = sbi_maybe_clean(urldecode($_GET['sbi_access_token']));
	$url = 'https://graph.facebook.com/me/accounts?fields=instagram_business_account,access_token&limit=500&access_token='.$access_token;
	$args = array(
		'timeout' => 30
	);
	if ( version_compare( get_bloginfo( 'version' ), '3.7' , '<' ) ) {
		$args['sslverify'] = false;
	}
	$result = wp_remote_get( $url, $args );
	$pages_data = '{}';
	if ( ! is_wp_error( $result ) ) {
		$pages_data = $result['body'];
	} else {
		$page_error = $result;
	}

	$pages_data_arr = json_decode($pages_data);
	$num_accounts = 0;
	if(isset($pages_data_arr)){
		$num_accounts = is_array( $pages_data_arr->data ) ? count( $pages_data_arr->data ) : 0;
	}
	?>
<div id="sbi_config_info" class="sb_list_businesses sbi_num_businesses_<?php echo $num_accounts; ?>">
    <div class="sbi_config_modal">
        <div class="sbi-managed-pages">
			<?php if ( isset( $page_error ) && isset( $page_error->errors ) ) {
				foreach ($page_error->errors as $key => $item) {
					echo '<div class="sbi_user_id_error" style="display:block;"><strong>Connection Error: </strong>' . $key . ': ' . $item[0] . '</div>';
				}
			}
			?>
			<?php if( empty($pages_data_arr->data) ) : ?>
                <span id="sbi-bus-account-error">
                            <p style="margin-top: 5px;"><b style="font-size: 16px">Couldn't find Business Profile</b><br />
                            Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the <a href="https://www.facebook.com/" target="_blank">Facebook account</a> in this browser which is associated with your Instagram Business Profile.</p>
                            <p><b style="font-size: 16px">Why do I need a Business Profile?</b><br />
                            A Business Profile is only required if you are displaying a Hashtag feed. If you want to display a regular User feed then you can do this by selecting to connect a Personal account instead. For directions on how to convert your Personal profile into a Business profile please <a href="https://smashballoon.com/instagram-business-profiles" target="_blank">see here</a>.</p>
                            </span>

			<?php elseif ( $num_accounts === 0 ): ?>
                <span id="sbi-bus-account-error">
                            <p style="margin-top: 5px;"><b style="font-size: 16px">Couldn't find Business Profile</b><br />
                            Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the <a href="https://www.facebook.com/" target="_blank">Facebook account</a> in this browser which is associated with your Instagram Business Profile.</p>
                            <p>If you are, in fact, logged-in to the correct account please make sure you have Instagram accounts connected with your Facebook account by following <a href="https://smashballoon.com/reconnecting-an-instagram-business-profile/" target="_blank">this FAQ</a></p>
                            </span>
			<?php else: ?>
                <p class="sbi-managed-page-intro"><b style="font-size: 16px;">Instagram Business profiles for this account</b><br /><i style="color: #666;">Note: In order to display a Hashtag feed you first need to select a Business profile below.</i></p>
				<?php if ( $num_accounts > 1 ) : ?>
                    <div class="sbi-managed-page-select-all"><input type="checkbox" id="sbi-select-all" class="sbi-select-all"><label for="sbi-select-all">Select All</label></div>
				<?php endif; ?>
                <div class="sbi-scrollable-accounts">

					<?php foreach ( $pages_data_arr->data as $page => $page_data ) : ?>

						<?php if( isset( $page_data->instagram_business_account ) ) :

							$instagram_business_id = $page_data->instagram_business_account->id;

							$page_access_token = isset( $page_data->access_token ) ? $page_data->access_token : '';

							//Make another request to get page info
							$instagram_account_url = 'https://graph.facebook.com/'.$instagram_business_id.'?fields=name,username,profile_picture_url&access_token='.$access_token;

							$args = array(
								'timeout' => 30
							);
							if ( version_compare( get_bloginfo( 'version' ), '3.7' , '<' ) ) {
								$args['sslverify'] = false;
							}
							$result = wp_remote_get( $instagram_account_url, $args );
							$instagram_account_info = '{}';
							if ( ! is_wp_error( $result ) ) {
								$instagram_account_info = $result['body'];
							} else {
								$page_error = $result;
							}

							$instagram_account_data = json_decode($instagram_account_info);

							$instagram_biz_img = isset( $instagram_account_data->profile_picture_url ) ? $instagram_account_data->profile_picture_url : false;
							$selected_class = $instagram_business_id == $sb_instagram_user_id ? ' sbi-page-selected' : '';

							?>
							<?php if ( isset( $page_error ) && isset( $page_error->errors ) ) :
							foreach ($page_error->errors as $key => $item) {
								echo '<div class="sbi_user_id_error" style="display:block;"><strong>Connection Error: </strong>' . $key . ': ' . $item[0] . '</div>';
							}
						else : ?>
                            <div class="sbi-managed-page<?php echo $selected_class; ?>" data-page-token="<?php echo esc_attr( $page_access_token ); ?>" data-token="<?php echo esc_attr( $access_token ); ?>" data-page-id="<?php echo esc_attr( $instagram_business_id ); ?>">
                                <div class="sbi-add-checkbox">
                                    <input id="sbi-<?php echo esc_attr( $instagram_business_id ); ?>" type="checkbox" name="sbi_managed_pages[]" value="<?php echo esc_attr( $instagram_account_info ); ?>">
                                </div>
                                <div class="sbi-managed-page-details">
                                    <label for="sbi-<?php echo esc_attr( $instagram_business_id ); ?>"><img class="sbi-page-avatar" border="0" height="50" width="50" src="<?php echo esc_url( $instagram_biz_img ); ?>"><b style="font-size: 16px;"><?php echo esc_html( $instagram_account_data->name ); ?></b>
                                        <br />@<?php echo esc_html( $instagram_account_data->username); ?><span style="font-size: 11px; margin-left: 5px;">(<?php echo esc_html( $instagram_business_id ); ?>)</span></label>
                                </div>
                            </div>
						<?php endif; ?>

						<?php endif; ?>

					<?php endforeach; ?>

                </div> <!-- end scrollable -->
                <p style="font-size: 11px; line-height: 1.5; margin-bottom: 0;"><i style="color: #666;">*<?php echo sprintf( __( 'Changing the password, updating privacy settings, or removing page admins for the related Facebook page may require %smanually reauthorizing our app%s to reconnect an account.', 'instagram-feed' ), '<a href="https://smashballoon.com/reauthorizing-our-instagram-facebook-app/" target="_blank" rel="noopener noreferrer">', '</a>' ); ?></i></p>

                <button id="sbi-connect-business-accounts" class="button button-primary" disabled="disabled" style="margin-top: 20px;"><?php _e( 'Connect Accounts', 'instagram-feed' ); ?></button>

			<?php endif; ?>

            <a href="JavaScript:void(0);" class="sbi_modal_close"><i class="fa fa-times"></i></a>
        </div>
    </div>
    </div><?php
}

function sbi_get_personal_connection_modal( $connected_accounts, $action_url = 'admin.php?page=sb-instagram-feed' ) {
	$access_token = sanitize_text_field( $_GET['sbi_access_token'] );
	$account_type = sanitize_text_field( $_GET['sbi_account_type'] );
	$user_id = sanitize_text_field( $_GET['sbi_id'] );
	$user_name = sanitize_text_field( $_GET['sbi_username'] );
	$expires_in = (int)$_GET['sbi_expires_in'];
	$expires_timestamp = time() + $expires_in;

	$new_account_details = array(
		'access_token' => $access_token,
		'account_type' => $account_type,
		'user_id' => $user_id,
		'username' => $user_name,
		'expires_timestamp' => $expires_timestamp,
		'profile_picture' => '',
		'type' => 'basic'
	);


	$matches_existing_personal = sbi_matches_existing_personal( $new_account_details );
	$button_text = $matches_existing_personal ? __( 'Update This Account', 'instagram-feed' ) : __( 'Connect This Account', 'instagram-feed' );

	$account_json = sbi_json_encode( $new_account_details );

	$already_connected_as_business_account = (isset( $connected_accounts[ $user_id ] ) && $connected_accounts[ $user_id ]['type'] === 'business');

	?>

    <div id="sbi_config_info" class="sb_get_token">
        <div class="sbi_config_modal">
            <div class="sbi_ca_username"><strong><?php echo esc_html( $user_name ); ?></strong></div>
            <form action="<?php echo admin_url( $action_url ); ?>" method="post">
                <p class="sbi_submit">
					<?php if ( $already_connected_as_business_account ) :
						_e( 'The Instagram account you are logged into is already connected as a "business" account. Remove the business account if you\'d like to connect as a basic account instead (not recommended).', 'instagram-feed' );
						?>
					<?php else : ?>
                        <input type="submit" name="sbi_submit" id="sbi_connect_account" class="button button-primary" value="<?php echo esc_html( $button_text ); ?>">
					<?php  endif; ?>
                    <input type="hidden" name="sbi_account_json" value="<?php echo esc_attr( $account_json ) ; ?>">
                    <input type="hidden" name="sbi_connect_username" value="<?php echo esc_attr( $user_name ); ?>">
                    <a href="JavaScript:void(0);" class="button button-secondary" id="sbi_switch_accounts"><?php esc_html_e( 'Switch Accounts', 'instagram-feed' ); ?></a>
                </p>
            </form>
            <a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>
        </div>
    </div>
	<?php
}

function sbi_account_type_display( $type, $private = false ) {
    if ( $type === 'basic' ) {
        $type = 'personal';
        if ( $private ) {
	        $type .= ' (private)';
        }
    }
    return $type;
}

function sbi_expiration_notice(){
	//Only display notice to admins
	if( !current_user_can( 'manage_options' ) ) return;

	//If the user is re-checking the license key then use the API below to recheck it
	( isset( $_GET['sbichecklicense'] ) ) ? $sbi_check_license = true : $sbi_check_license = false;

	$sbi_license = trim( get_option( 'sbi_license_key' ) );

	//If there's no license key then don't do anything
	if( empty($sbi_license) || !isset($sbi_license) && !$sbi_check_license ) return;

	//Is there already license data in the db?
	if( get_option( 'sbi_license_data' ) && !$sbi_check_license ){
		//Yes
		//Get license data from the db and convert the object to an array
		$sbi_license_data = (array) get_option( 'sbi_license_data' );
	} else {
		//No
		// data to send in our API request
		$sbi_api_params = array(
			'edd_action'=> 'check_license',
			'license'   => $sbi_license,
			'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

		// decode the license data
		$sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

		//Store license data in db
		update_option( 'sbi_license_data', $sbi_license_data );
	}

	//Number of days until license expires
	$sbi_license_expires_date = isset( $sbi_license_data['expires'] ) ? $sbi_license_data['expires'] : $sbi_license_expires_date = '2036-12-31 23:59:59'; //If expires param isn't set yet then set it to be a date to avoid PHP notice
	if( $sbi_license_expires_date == 'lifetime' ) $sbi_license_expires_date = '2036-12-31 23:59:59';
	$sbi_todays_date = date('Y-m-d');
	$sbi_interval = round(abs(strtotime($sbi_todays_date . ' -1 day')-strtotime($sbi_license_expires_date))/86400); //-1 day to make sure auto-renewal has run before showing expired

	//Is license expired?
    if( $sbi_interval == 0 || strtotime($sbi_license_expires_date) < strtotime($sbi_todays_date) ){

        //If we haven't checked the API again one last time before displaying the expired notice then check it to make sure the license hasn't been renewed
        if( get_option( 'sbi_check_license_api_when_expires' ) == FALSE || get_option( 'sbi_check_license_api_when_expires' ) == 'true' ){

            // Check the API
            $sbi_api_params = array(
                'edd_action'=> 'check_license',
                'license'   => $sbi_license,
                'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
            );
            $sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );
            $sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

            //Check whether it's active
            if( $sbi_license_data['license'] !== 'expired' && ( strtotime( $sbi_license_data['expires'] ) > strtotime($sbi_todays_date) ) ){
                $sbi_license_expired = false;
            } else {
                $sbi_license_expired = true;
                //Set a flag so it doesn't check the API again until the next time it expires
                update_option( 'sbi_check_license_api_when_expires', 'false' );
            }

            //Store license data in db
            update_option( 'sbi_license_data', $sbi_license_data );

        } else {
            //Display the expired notice
            $sbi_license_expired = true;
        }

    } else {
        $sbi_license_expired = false;

        //License is not expired so change the check_api setting to be true so the next time it expires it checks again
        update_option( 'sbi_check_license_api_when_expires', 'true' );
    }

	//If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
	if( $sbi_license_expires_date[0] == '1' ) $sbi_license_expired = false;

	//If there's no expired date then don't show the expired notification
	if( empty($sbi_license_expires_date) || !isset($sbi_license_expires_date) ) $sbi_license_expired = false;

	//Is license missing - ie. on very first check
	if( isset($sbi_license_data['error']) ){
		if( $sbi_license_data['error'] == 'missing' ) $sbi_license_expired = false;
	}

	//If license expires in less than 30 days and it isn't currently expired then show the expire countdown instead of the expiration notice
	if($sbi_interval < 30 && !$sbi_license_expired){
		$sbi_expire_countdown = true;
	} else {
		$sbi_expire_countdown = false;
	}

	//Check whether it was purchased after subscriptions were introduced
    if( isset($sbi_license_data['payment_id']) && intval($sbi_license_data['payment_id']) > 762729 ){
        //Is likely to be renewed on a subscription so don't show countdown
        $sbi_expire_countdown = false;
    }

	global $sbi_download_id;

	//Is the license expired?
	if( ($sbi_license_expired || $sbi_expire_countdown) || $sbi_check_license ) {

		//If they've already dismissed the countdown notice then don't show it here
		global $current_user;
		$user_id = $current_user->ID;
		if( $sbi_expire_countdown && get_user_meta($user_id, 'sbi_ignore_notice') ) return;


		$sbi_license_activation_error = false;
		if( $sbi_license_data["success"] == false ) $sbi_license_activation_error = true;

		$sbi_expired_box_msg = '<svg style="width:16px;height:16px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-exclamation-triangle fa-w-18 fa-2x"><path fill="currentColor" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z" class=""></path></svg>';

		//If expire countdown then add the countdown class to the notice box
		if($sbi_expire_countdown){
			$sbi_expired_box_classes = "sbi-license-expired sbi-license-countdown";
			$sbi_expired_box_msg .= "<b>Important: Your Instagram Feed Pro license key expires in " . $sbi_interval . " days.</b>";
		} else if( $sbi_license_activation_error ){
			$sbi_expired_box_classes = "sbi-license-expired";
			$sbi_expired_box_msg .= "<b>Issue activating license.</b> Please ensure that you entered your license key correctly. If you continue to have an issue please see <a href='https://smashballoon.com/my-license-key-wont-activate/' target='_blank'>here</a>.";
		} else {
			$sbi_expired_box_classes = "sbi-license-expired";
			$sbi_expired_box_msg .= "<b>Important: Your Instagram Feed Pro license key has expired.</b><br /><span>You are no longer receiving updates that protect you against upcoming Instagram changes.</span>";
		}

		//Create the re-check link using the existing query string in the URL
		$sbi_url = '?' . $_SERVER["QUERY_STRING"];
		//Determine the separator
		( !empty($sbi_url) && $sbi_url != '' ) ? $separator = '&' : $separator = '';
		//Add the param to check license if it doesn't already exist in URL
		if( strpos($sbi_url, 'sbichecklicense') === false ) $sbi_url .= $separator . "sbichecklicense=true";

		//Create the notice message
		if( !$sbi_license_activation_error ) $sbi_expired_box_msg .= " &nbsp;<a href='https://smashballoon.com/checkout/?edd_license_key=".$sbi_license."&download_id=".$sbi_download_id."&utm_source=plugin-pro&utm_campaign=sbi&utm_medium=expired-notice-dashboard' target='_blank' class='button button-primary'>Renew License</a><a href='javascript:void(0);' id='sbi-why-renew-show' onclick='sbiShowReasons()' class='button button-secondary'>Why renew?</a><a href='javascript:void(0);' id='sbi-why-renew-hide' onclick='sbiHideReasons()' class='button button-secondary' style='display: none;'>Hide text</a> <a href='".$sbi_url."' class='button button-secondary'>Re-check License</a></p>
            <div id='sbi-why-renew' style='display: none;'>
                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='shield-check' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-shield-check fa-w-16 fa-2x' data-ce-key='470'><path fill='currentColor' d='M466.5 83.7l-192-80a48.15 48.15 0 0 0-36.9 0l-192 80C27.7 91.1 16 108.6 16 128c0 198.5 114.5 335.7 221.5 380.3 11.8 4.9 25.1 4.9 36.9 0C360.1 472.6 496 349.3 496 128c0-19.4-11.7-36.9-29.5-44.3zm-47.2 114.2l-184 184c-6.2 6.2-16.4 6.2-22.6 0l-104-104c-6.2-6.2-6.2-16.4 0-22.6l22.6-22.6c6.2-6.2 16.4-6.2 22.6 0l70.1 70.1 150.1-150.1c6.2-6.2 16.4-6.2 22.6 0l22.6 22.6c6.3 6.3 6.3 16.4 0 22.6z' class='' data-ce-key='471'></path></svg>Protected Against All Upcoming Instagram Platform Updates and API Changes</h4>
                <p>You currently don't need to worry about your Instagram feeds breaking due to constant changes in the Instagram platform. You are currently protected by access to continual plugin updates, giving you peace of mind that the software will always be up to date.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fab' data-icon='wordpress' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-wordpress fa-w-16 fa-2x'><path fill='currentColor' d='M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8.9 0 1.8.1 2.8.2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7.3 13.7.3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z' class=''></path></svg>WordPress Compatability Updates</h4>
                <p>With WordPress updates being released continually, we make sure the plugin is always compatible with the latest version so you can update WordPress without needing to worry.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='far' data-icon='life-ring' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-life-ring fa-w-16 fa-2x' data-ce-key='500'><path fill='currentColor' d='M256 504c136.967 0 248-111.033 248-248S392.967 8 256 8 8 119.033 8 256s111.033 248 248 248zm-103.398-76.72l53.411-53.411c31.806 13.506 68.128 13.522 99.974 0l53.411 53.411c-63.217 38.319-143.579 38.319-206.796 0zM336 256c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80zm91.28 103.398l-53.411-53.411c13.505-31.806 13.522-68.128 0-99.974l53.411-53.411c38.319 63.217 38.319 143.579 0 206.796zM359.397 84.72l-53.411 53.411c-31.806-13.505-68.128-13.522-99.973 0L152.602 84.72c63.217-38.319 143.579-38.319 206.795 0zM84.72 152.602l53.411 53.411c-13.506 31.806-13.522 68.128 0 99.974L84.72 359.398c-38.319-63.217-38.319-143.579 0-206.796z' class='' data-ce-key='501'></path></svg>Expert Technical Support</h4>
                <p>Without a valid license key you will no longer be able to receive updates or support for the Instagram Feed plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='unlock' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' class='svg-inline--fa fa-unlock fa-w-14 fa-2x' data-ce-key='477'><path fill='currentColor' d='M400 256H152V152.9c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v16c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-16C376 68 307.5-.3 223.5 0 139.5.3 72 69.5 72 153.5V256H48c-26.5 0-48 21.5-48 48v160c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V304c0-26.5-21.5-48-48-48z' class='' data-ce-key='478'></path></svg>All Pro Instagram Feed Features</h4>
                <p>Hashtag Feeds, Visual Moderation System, Photo/Video Lightbox, Carousel/Slideshows, Instagram Stories, Tagged Feeds, Create Shoppable Feeds, Filter Posts, Captions/Likes/Comments, Combine Multiple Feed Types, and more!</p>
            </div>";

		if( $sbi_check_license && !$sbi_license_expired && !$sbi_expire_countdown ){
			$sbi_expired_box_classes = "sbi-license-expired sbi-license-valid";
			$sbi_expired_box_msg = "Thanks ".$sbi_license_data["customer_name"].", your Instagram Feed Pro license key is valid.";
		}

		_e("
        <div class='".$sbi_expired_box_classes."'>");
		if( $sbi_expire_countdown ) _e("<a style='float:right; color: #dd3d36; text-decoration: none;' href='" .esc_url( add_query_arg( 'sbi_nag_ignore', '0' ) ). "'>Dismiss</a>");
		_e("<p>".$sbi_expired_box_msg." 
        </div>
        <script type='text/javascript'>
        function sbiShowReasons() {
            document.getElementById('sbi-why-renew').style.display = 'block';
            document.getElementById('sbi-why-renew-show').style.display = 'none';
            document.getElementById('sbi-why-renew-hide').style.display = 'inline-block';
        }
        function sbiHideReasons() {
            document.getElementById('sbi-why-renew').style.display = 'none';
            document.getElementById('sbi-why-renew-show').style.display = 'inline-block';
            document.getElementById('sbi-why-renew-hide').style.display = 'none';
        }
        </script>
        ");
	}

}

/* Display a license expired notice that can be dismissed */
add_action('admin_notices', 'sbi_renew_license_notice');
function sbi_renew_license_notice() {
	//Only display notice to admins
	if( !current_user_can( 'manage_options' ) ) return;

	//Show this notice on every page apart from the Instagram Feed settings pages
	isset($_GET['page'])? $sbi_check_page = $_GET['page'] : $sbi_check_page = '';
	if ( $sbi_check_page !== 'sb-instagram-feed' && $sbi_check_page !== 'sb-instagram-license' ) {

		//If the user is re-checking the license key then use the API below to recheck it
		( isset( $_GET['sbichecklicense'] ) ) ? $sbi_check_license = true : $sbi_check_license = false;

		$sbi_license = trim( get_option( 'sbi_license_key' ) );

		global $current_user;
		$user_id = $current_user->ID;

		// Use this to show notice again
		//delete_user_meta($user_id, 'sbi_ignore_notice');

		/* Check that the license exists and the user hasn't already clicked to ignore the message */
		if( empty($sbi_license) || !isset($sbi_license) || get_user_meta($user_id, 'sbi_ignore_notice') && !$sbi_check_license ) return;

		//Is there already license data in the db?
		if( get_option( 'sbi_license_data' ) && !$sbi_check_license ){
			//Yes
			//Get license data from the db and convert the object to an array
			$sbi_license_data = (array) get_option( 'sbi_license_data' );
		} else {
			//No
			// data to send in our API request
			$sbi_api_params = array(
				'edd_action'=> 'check_license',
				'license'   => $sbi_license,
				'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
			);

			// Call the custom API.
			$sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

			// decode the license data
			$sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

			//Store license data in db
			update_option( 'sbi_license_data', $sbi_license_data );

		}

		//Number of days until license expires
		$sbi_license_expires_date = isset( $sbi_license_data['expires'] ) ? $sbi_license_data['expires'] : $sbi_license_expires_date = '2036-12-31 23:59:59'; //If expires param isn't set yet then set it to be a date to avoid PHP notice
		if( $sbi_license_expires_date == 'lifetime' ) $sbi_license_expires_date = '2036-12-31 23:59:59';
		$sbi_todays_date = date('Y-m-d');
		$sbi_interval = round(abs(strtotime($sbi_todays_date . ' -1 day')-strtotime($sbi_license_expires_date))/86400); //-1 day to make sure auto-renewal has run before showing expired

		//Is license expired?
	    if( $sbi_interval == 0 || strtotime($sbi_license_expires_date) < strtotime($sbi_todays_date) ){

	        //If we haven't checked the API again one last time before displaying the expired notice then check it to make sure the license hasn't been renewed
	        if( get_option( 'sbi_check_license_api_when_expires' ) == FALSE || get_option( 'sbi_check_license_api_when_expires' ) == 'true' ){

	            // Check the API
	            $sbi_api_params = array(
	                'edd_action'=> 'check_license',
	                'license'   => $sbi_license,
	                'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
	            );
	            $sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );
	            $sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

	            //Check whether it's active
	            if( $sbi_license_data['license'] !== 'expired' && ( strtotime( $sbi_license_data['expires'] ) > strtotime($sbi_todays_date) ) ){
	                $sbi_license_expired = false;
	            } else {
	                $sbi_license_expired = true;
	                //Set a flag so it doesn't check the API again until the next time it expires
	                update_option( 'sbi_check_license_api_when_expires', 'false' );
	            }

	            //Store license data in db
	            update_option( 'sbi_license_data', $sbi_license_data );

	        } else {
	            //Display the expired notice
	            $sbi_license_expired = true;
	        }

	    } else {
	        $sbi_license_expired = false;

	        //License is not expired so change the check_api setting to be true so the next time it expires it checks again
	        update_option( 'sbi_check_license_api_when_expires', 'true' );
	    }

		//If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
		if( $sbi_license_expires_date[0] == '1' ) $sbi_license_expired = false;

		//If there's no expired date then don't show the expired notification
		if( empty($sbi_license_expires_date) || !isset($sbi_license_expires_date) ) $sbi_license_expired = false;

		//Is license missing - ie. on very first check
		if( isset($sbi_license_data['error']) ){
			if( $sbi_license_data['error'] == 'missing' ) $sbi_license_expired = false;
		}

		//If license expires in less than 30 days and it isn't currently expired then show the expire countdown instead of the expiration notice
		if($sbi_interval < 30 && !$sbi_license_expired){
			$sbi_expire_countdown = true;
		} else {
			$sbi_expire_countdown = false;
		}

		//Check whether it was purchased after subscriptions were introduced
        if( isset($sbi_license_data['payment_id']) && intval($sbi_license_data['payment_id']) > 762729 ){
            //Is likely to be renewed on a subscription so don't show countdown
            $sbi_expire_countdown = false;
        }

		//Is the license expired?
		if( ($sbi_license_expired || $sbi_expire_countdown) || $sbi_check_license ) {

			global $sbi_download_id;

			$sbi_expired_box_msg = '<svg style="width:16px;height:16px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-exclamation-triangle fa-w-18 fa-2x"><path fill="currentColor" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z" class=""></path></svg>';

			//If expire countdown then add the countdown class to the notice box
			if($sbi_expire_countdown){
				$sbi_expired_box_classes = "sbi-license-expired sbi-license-countdown";
				$sbi_expired_box_msg .= "<b>Important: Your Instagram Feed Pro license key expires in " . $sbi_interval . " days.</b>";
			} else {
				$sbi_expired_box_classes = "sbi-license-expired";
				$sbi_expired_box_msg .= "<b>Important: Your Instagram Feed Pro license key has expired.</b><br /><span>You are no longer receiving updates that protect you against upcoming Instagram changes.</span>";
			}

			//Create the re-check link using the existing query string in the URL
			$sbi_url = '?' . $_SERVER["QUERY_STRING"];
			//Determine the separator
			( !empty($sbi_url) && $sbi_url != '' ) ? $separator = '&' : $separator = '';
			//Add the param to check license if it doesn't already exist in URL
			if( strpos($sbi_url, 'sbichecklicense') === false ) $sbi_url .= $separator . "sbichecklicense=true";

			//Create the notice message
			$sbi_expired_box_msg .= " &nbsp;<a href='https://smashballoon.com/checkout/?edd_license_key=".$sbi_license."&download_id=".$sbi_download_id."&utm_source=plugin-pro&utm_campaign=sbi&utm_medium=expired-notice-dashboard' target='_blank' class='button button-primary'>Renew License</a><a href='javascript:void(0);' id='sbi-why-renew-show' onclick='sbiShowReasons()' class='button button-secondary'>Why renew?</a><a href='javascript:void(0);' id='sbi-why-renew-hide' onclick='sbiHideReasons()' class='button button-secondary' style='display: none;'>Hide text</a> <a href='".$sbi_url."' class='button button-secondary'>Re-check License</a></p>
            <div id='sbi-why-renew' style='display: none;'>
                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='shield-check' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-shield-check fa-w-16 fa-2x' data-ce-key='470'><path fill='currentColor' d='M466.5 83.7l-192-80a48.15 48.15 0 0 0-36.9 0l-192 80C27.7 91.1 16 108.6 16 128c0 198.5 114.5 335.7 221.5 380.3 11.8 4.9 25.1 4.9 36.9 0C360.1 472.6 496 349.3 496 128c0-19.4-11.7-36.9-29.5-44.3zm-47.2 114.2l-184 184c-6.2 6.2-16.4 6.2-22.6 0l-104-104c-6.2-6.2-6.2-16.4 0-22.6l22.6-22.6c6.2-6.2 16.4-6.2 22.6 0l70.1 70.1 150.1-150.1c6.2-6.2 16.4-6.2 22.6 0l22.6 22.6c6.3 6.3 6.3 16.4 0 22.6z' class='' data-ce-key='471'></path></svg>Protected Against All Upcoming Instagram Platform Updates and API Changes</h4>
                <p>You currently don't need to worry about your Instagram feeds breaking due to constant changes in the Instagram platform. You are currently protected by access to continual plugin updates, giving you peace of mind that the software will always be up to date.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fab' data-icon='wordpress' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-wordpress fa-w-16 fa-2x'><path fill='currentColor' d='M61.7 169.4l101.5 278C92.2 413 43.3 340.2 43.3 256c0-30.9 6.6-60.1 18.4-86.6zm337.9 75.9c0-26.3-9.4-44.5-17.5-58.7-10.8-17.5-20.9-32.4-20.9-49.9 0-19.6 14.8-37.8 35.7-37.8.9 0 1.8.1 2.8.2-37.9-34.7-88.3-55.9-143.7-55.9-74.3 0-139.7 38.1-177.8 95.9 5 .2 9.7.3 13.7.3 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l77.5 230.4L249.8 247l-33.1-90.8c-11.5-.7-22.3-2-22.3-2-11.5-.7-10.1-18.2 1.3-17.5 0 0 35.1 2.7 56 2.7 22.2 0 56.7-2.7 56.7-2.7 11.5-.7 12.8 16.2 1.4 17.5 0 0-11.5 1.3-24.3 2l76.9 228.7 21.2-70.9c9-29.4 16-50.5 16-68.7zm-139.9 29.3l-63.8 185.5c19.1 5.6 39.2 8.7 60.1 8.7 24.8 0 48.5-4.3 70.6-12.1-.6-.9-1.1-1.9-1.5-2.9l-65.4-179.2zm183-120.7c.9 6.8 1.4 14 1.4 21.9 0 21.6-4 45.8-16.2 76.2l-65 187.9C426.2 403 468.7 334.5 468.7 256c0-37-9.4-71.8-26-102.1zM504 256c0 136.8-111.3 248-248 248C119.2 504 8 392.7 8 256 8 119.2 119.2 8 256 8c136.7 0 248 111.2 248 248zm-11.4 0c0-130.5-106.2-236.6-236.6-236.6C125.5 19.4 19.4 125.5 19.4 256S125.6 492.6 256 492.6c130.5 0 236.6-106.1 236.6-236.6z' class=''></path></svg>WordPress Compatability Updates</h4>
                <p>With WordPress updates being released continually, we make sure the plugin is always compatible with the latest version so you can update WordPress without needing to worry.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='far' data-icon='life-ring' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' class='svg-inline--fa fa-life-ring fa-w-16 fa-2x' data-ce-key='500'><path fill='currentColor' d='M256 504c136.967 0 248-111.033 248-248S392.967 8 256 8 8 119.033 8 256s111.033 248 248 248zm-103.398-76.72l53.411-53.411c31.806 13.506 68.128 13.522 99.974 0l53.411 53.411c-63.217 38.319-143.579 38.319-206.796 0zM336 256c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80zm91.28 103.398l-53.411-53.411c13.505-31.806 13.522-68.128 0-99.974l53.411-53.411c38.319 63.217 38.319 143.579 0 206.796zM359.397 84.72l-53.411 53.411c-31.806-13.505-68.128-13.522-99.973 0L152.602 84.72c63.217-38.319 143.579-38.319 206.795 0zM84.72 152.602l53.411 53.411c-13.506 31.806-13.522 68.128 0 99.974L84.72 359.398c-38.319-63.217-38.319-143.579 0-206.796z' class='' data-ce-key='501'></path></svg>Expert Technical Support</h4>
                <p>Without a valid license key you will no longer be able to receive updates or support for the Instagram Feed plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>

                <h4><svg style='width:16px;height:16px;' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='unlock' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' class='svg-inline--fa fa-unlock fa-w-14 fa-2x' data-ce-key='477'><path fill='currentColor' d='M400 256H152V152.9c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v16c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-16C376 68 307.5-.3 223.5 0 139.5.3 72 69.5 72 153.5V256H48c-26.5 0-48 21.5-48 48v160c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V304c0-26.5-21.5-48-48-48z' class='' data-ce-key='478'></path></svg>All Pro Instagram Feed Features</h4>
                <p>Hashtag Feeds, Visual Moderation System, Photo/Video Lightbox, Carousel/Slideshows, Instagram Stories, Tagged Feeds, Create Shoppable Feeds, Filter Posts, Captions/Likes/Comments, Combine Multiple Feed Types, and more!</p>
            </div>";

			if( $sbi_check_license && !$sbi_license_expired && !$sbi_expire_countdown ){
				$sbi_expired_box_classes = "sbi-license-expired sbi-license-valid";
				$sbi_expired_box_msg = "Thanks ".$sbi_license_data["customer_name"].", your Instagram Feed Pro license key is valid.";
			}

			_e("
            <div class='".$sbi_expired_box_classes."'>
                <a style='float:right; color: #dd3d36; text-decoration: none;' href='" .esc_url( add_query_arg( 'sbi_nag_ignore', '0' ) ). "'>Dismiss</a>
                <p>".$sbi_expired_box_msg." 
            </div>
            <script type='text/javascript'>
            function sbiShowReasons() {
                document.getElementById('sbi-why-renew').style.display = 'block';
                document.getElementById('sbi-why-renew-show').style.display = 'none';
                document.getElementById('sbi-why-renew-hide').style.display = 'inline-block';
            }
            function sbiHideReasons() {
                document.getElementById('sbi-why-renew').style.display = 'none';
                document.getElementById('sbi-why-renew-show').style.display = 'inline-block';
                document.getElementById('sbi-why-renew-hide').style.display = 'none';
            }
            </script>
            ");
		}

	}
}
add_action('admin_init', 'sbi_nag_ignore');
function sbi_nag_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	if ( isset($_GET['sbi_nag_ignore']) && '0' == $_GET['sbi_nag_ignore'] ) {
		add_user_meta($user_id, 'sbi_ignore_notice', 'true', true);
	}
}

function sbi_disable_welcome() {

	add_user_meta(get_current_user_id(), 'sbi_disable_welcome', 'true', true);

	echo "1";

	die();
}
add_action( 'wp_ajax_sbi_disable_welcome', 'sbi_disable_welcome' );

function sbi_admin_hide_unrelated_notices() {
	
	// Bail if we're not on a sbi screen or page.
	if ( ! isset( $_GET['page'] ) || ( strpos( $_GET['page'], 'sb-instagram-feed') === false && strpos( $_GET['page'], 'sbi-') === false ) ) {
		return;
	}

	// Extra banned classes and callbacks from third-party plugins.
	$blacklist = array(
		'classes'   => array(),
		'callbacks' => array(
			'sbidb_admin_notice', // 'Database for sbi' plugin.
		),
	);

	global $wp_filter;

	foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
		if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
			continue;
		}
		foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
					continue;
				}
				$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
				if (
					! empty( $class ) &&
					strpos( $class, 'sbi' ) !== false &&
					! in_array( $class, $blacklist['classes'], true )
				) {
					continue;
				}
				if (
					! empty( $name ) && (
						strpos( $name, 'sbi' ) === false ||
						in_array( $class, $blacklist['classes'], true ) ||
						in_array( $name, $blacklist['callbacks'], true )
					)
				) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}
add_action( 'admin_print_scripts', 'sbi_admin_hide_unrelated_notices' );

function sbi_connect_new_account( $access_token, $account_id ) {
	$split_id = explode( ' ', trim( $account_id ) );
	$account_id = preg_replace("/[^A-Za-z0-9 ]/", '', $split_id[0] );
	if ( ! empty( $account_id ) ) {
		$split_token = explode( ' ', trim( $access_token ) );
		$access_token = preg_replace("/[^A-Za-z0-9 ]/", '', $split_token[0] );
	}

    $account = array(
        'access_token' => $access_token,
        'user_id' => $account_id,
        'type' => 'business'
    );

	if ( sbi_code_check( $access_token ) ) {
		$account['type'] = 'basic';
	}

	$connector = new SBI_Account_Connector();

	$response = $connector->fetch( $account );

	if ( isset( $response['access_token'] ) ) {
		$connector->add_account_data( $response );
		$connector->update_stored_account();
		$connector->after_update();
        return sbi_json_encode( $connector->get_account_data() );
    } else {
	    return $response['error'];
    }
}

function sbi_no_js_connected_account_management() {
    if ( ! current_user_can( 'manage_instagram_feed_options' ) ) {
        return;
    }
    if ( isset( $_POST['sb_manual_at'] ) ) {
	    $access_token = isset( $_POST['sb_manual_at'] ) ? trim( sanitize_text_field( $_POST['sb_manual_at'] ) ) : false;
	    $account_id = isset( $_POST['sb_manual_account_id'] ) ? sanitize_text_field( $_POST['sb_manual_account_id'] ) : false;
	    if ( ! $access_token || ! $account_id ) {
		    return;
	    }
	    sbi_connect_new_account( $access_token, $account_id );
    } elseif ( isset( $_GET['disconnect'] ) && isset( $_GET['page'] ) && $_GET['page'] === 'sb-instagram-feed' ) {
        $account_id = sanitize_text_field( $_GET['disconnect'] );
	    sbi_do_account_delete( $account_id );
    }

}
add_action( 'admin_init', 'sbi_no_js_connected_account_management' );

function sbi_oembed_disable() {
	$nonce = isset( $_POST['sbi_nonce'] ) ? sanitize_text_field( $_POST['sbi_nonce'] ) : '';

	if ( ! wp_verify_nonce( $nonce, 'sbi_nonce' ) ) {
		die ( 'You did not do this the right way!' );
	}

	$oembed_settings = get_option( 'sbi_oembed_token', array() );
	$oembed_settings['access_token'] = '';
	$oembed_settings['disabled'] = true;
	echo '<strong>';
	if ( update_option( 'sbi_oembed_token', $oembed_settings ) ) {
		_e( 'Instagram oEmbeds will no longer be handled by Instagram Feed.', 'instagram-feed' );
	} else {
		_e( 'An error occurred when trying to delete your oEmbed token.', 'instagram-feed' );
	}
	echo '</strong>';

	die();
}
add_action( 'wp_ajax_sbi_oembed_disable', 'sbi_oembed_disable' );