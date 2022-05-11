<?php
/**
 * Custom Facebook Feed Caching System
 *
 * @since 3.18
 */
namespace CustomFacebookFeed;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;

class CFF_Cache_System {

	/**
	 * Construct.
	 *
	 * Construct Caching System
	 *
	 * @since 3.18
	 * @access public
	 */
	public function __construct() {
		add_action('wp_ajax_cache_meta', [$this, 'cff_cache_meta']);
		add_action('wp_ajax_nopriv_cache_meta', [$this, 'cff_cache_meta']);
		add_action('wp_ajax_get_meta', [$this, 'cff_get_meta']);
		add_action('wp_ajax_nopriv_get_meta', [$this, 'cff_get_meta']);
	}


	/**
	 * Get Meta.
	 *
	 * Return Array Comment, Like Meta
	 *
	 * @since 3.18
	 * @access public
	 */
	function cff_get_meta() {

	 	$comments_array_ids = isset($_POST['comments_array_ids']) && !empty($_POST['comments_array_ids']) ? $_POST['comments_array_ids'] : false;

		$atts = isset( $_POST['atts'] ) ? $_POST['atts']: array();
		$cache_feed_id = ! empty( $atts['feed'] ) ? (int)$atts['feed'] : false;
		$page = isset( $_POST['page'] ) ? (int)sanitize_text_field( wp_unslash($_POST['page'])) : 1;
		if ( ! empty( $cache_feed_id ) ) {
			$feed_cache = new CFF_Cache( $cache_feed_id, $page );
			$feed_cache->retrieve_and_set();

			$meta_cache = $feed_cache->get( 'meta' );

			if ( ! empty( $meta_cache ) ) {
				$meta_array = json_decode( $meta_cache, true );
				$cff_encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption;


				foreach ( $meta_array as $key => $value ) {
					if ( is_string( $value ) && $cff_encryption->decrypt( $value ) ) {
						$meta_array[ $key ] = $cff_encryption->decrypt( $value ) ? $cff_encryption->decrypt( $value ) : $value;
					}
					if ( strpos( $meta_array[ $key ], '%' ) === 0 ) {
						$meta_array[ $key ] = urldecode( $meta_array[ $key ] );
					}
				}
				$meta_cache = json_encode( $meta_array );

				if ( ! empty( $meta_cache ) ) {
					echo $meta_cache;
					wp_die();
				}
			}


		}
		$cff_encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption;

		$result = [];
		if($comments_array_ids != false):
			foreach ($comments_array_ids as $single_comment_id) {

				$comment_value = $this->get_single_meta( $single_comment_id );
				if ( is_string( $comment_value ) && $cff_encryption->decrypt( $comment_value ) ) {
					$comment_value = $cff_encryption->decrypt( $comment_value ) ? $cff_encryption->decrypt( $comment_value ) : $comment_value;
				}
				if ( strpos( $comment_value, '%' ) === 0 ) {
					$comment_value = urldecode( $comment_value );
				}
				$result[$single_comment_id] = $comment_value;
			}
		endif;

		if ( isset( $feed_cache ) ) {
			$feed_cache->update_or_insert( 'meta', $result, false, false );
		}

		$feed_locator_data_array = isset($_POST['feedLocatorData']) && !empty($_POST['feedLocatorData']) && is_array($_POST['feedLocatorData']) ? $_POST['feedLocatorData'] : false;
	  	if($feed_locator_data_array != false):
	  		foreach ($feed_locator_data_array as $single_feed_locator) {
	  			if(isset($single_feed_locator['feedID'])){
		  			$feed_details = array(
						'feed_id' => $single_feed_locator['feedID'],
						'atts' =>  isset($single_feed_locator['shortCodeAtts']) ? $single_feed_locator['shortCodeAtts'] : null,
						'location' => array(
							'post_id' => $single_feed_locator['postID'],
							'html' => $single_feed_locator['location']
						)
					);
					$locator = new CFF_Feed_Locator( $feed_details );
					$locator->add_or_update_entry();
	  			}
	  		}
	  	endif;
	  	print json_encode($result, true);
	    die();
	}

	/**
	 * Return Single Meta.
	 *
	 * Return Single Meta
	 *
	 * @since 3.18
	 * @access public
	 */
	function get_single_meta($metaID){
		$encryption = new SB_Facebook_Data_Encryption();

		$transient_name = 'cff_meta_'.$metaID;
	    $cached_data = '';
	    //If the cache exists then use the data
	    if ( false !== get_transient( $transient_name ) ) {
	        $cached_data = $encryption->maybe_decrypt(get_transient($transient_name));
	    } else {
	    //Else check for a backup cache
	        if ( false !== get_transient( '!cff_backup_'.$transient_name ) ) {
	            $cached_data = $encryption->maybe_decrypt(get_transient( '!cff_backup_'.$transient_name ));
	        }
	    }
	    //If there's an error cached then use the backup cache
	    if( strpos($cached_data, '%22%7B%5C%22error%5C%22:%7B%5C%22message%5C%22:') !== false ){
	        //If there's an error then see if a backup cache exists and use that data
	        if ( false !== get_transient( '!cff_backup_'.$transient_name ) ) {
	            $cached_data = $encryption->maybe_decrypt(get_transient( '!cff_backup_'.$transient_name ));
	        }
	    }

	    return $cached_data;
	}


	/**
	 * Get Cache Seconds
	 *
	 *
	 * @since 3.18
	 * @access public
	 */
	function cff_get_cache_seconds(){
		global $wpdb;

	    $cff_cache_time = get_option('cff_cache_time');
	    $cff_cache_time_unit = get_option('cff_cache_time_unit');

	    //Don't allow cache time to be zero - set to 1 minute instead to minimize API requests
	    if(!isset($cff_cache_time) || $cff_cache_time == '0' || (intval($cff_cache_time) < 15 && $cff_cache_time_unit == 'minutes' ) ){
	        $cff_cache_time = 15;
	        $cff_cache_time_unit = 'minutes';
	    }

	    //Calculate the cache time in seconds
	    if($cff_cache_time_unit == 'minutes') $cff_cache_time_unit = 60;
	    if($cff_cache_time_unit == 'hour' || $cff_cache_time_unit == 'hours') $cff_cache_time_unit = 60*60;
	    if($cff_cache_time_unit == 'days') $cff_cache_time_unit = 60*60*24;
	    $cache_seconds = $cff_cache_time * $cff_cache_time_unit;

	    //Temporarily increase default caching time to be 3 hours
	    if( $cache_seconds == 3600 ) $cache_seconds = 10800;

	    //Extra check to make sure caching isn't set to be less than 2 hours
	    if( $cache_seconds < 7200 || !isset($cache_seconds) ) $cache_seconds = 7200;

	    if($cff_cache_time == 'nocaching') $cache_seconds = 0;
	    return $cache_seconds;
	}

	/**
	 * Save Single Cache Meta
	 *
	 *
	 * @since 3.18
	 * @access public
	 */
	function cff_save_single_meta($metaID, $metaContent){
		$cache_seconds = $this->cff_get_cache_seconds();
		$transient_name = 'cff_meta_'.$metaID;
		$new_data = $metaContent;
		$encryption = new SB_Facebook_Data_Encryption();
		//Check data for error
		if( strpos($new_data, '%22%7B%5C%22error%5C%22:%7B%5C%22message%5C%22:') !== false ){
			//If there's an error then see if a backup cache exists and use that data
			if ( false !== get_transient( '!cff_backup_' . $transient_name ) ) {
				$new_data = get_transient( '!cff_backup_' . $transient_name );
			}
		} else {
			$new_data_encrypted = $encryption->maybe_encrypt($new_data);
			//If no error then use data in backup cache
			set_transient( '!cff_backup_' . $transient_name, $new_data_encrypted, WEEK_IN_SECONDS*2 );
		}

		$new_data_encrypted = $encryption->maybe_encrypt($new_data);
		set_transient( $transient_name, $new_data_encrypted, $cache_seconds );
	}

	/**
	 * Save Cache Meta
	 *
	 *
	 * @since 3.18
	 * @access public
	 */
	function cff_cache_meta() {
	    isset($_POST['metadata']) ? $meta_data = $_POST['metadata'] : $meta_data = '';
	    $comments_array = [];

	    if( !empty( $meta_data ) ){
	    	$comments_array = json_decode( stripcslashes($meta_data) , true );
	    }
	    if(is_array($comments_array)){
		    foreach ($comments_array as $single_comment) {
		    	$this->cff_save_single_meta($single_comment['id_post'], json_encode($single_comment) );
		    }

		    $atts = isset( $_POST['atts'] ) ? $_POST['atts']: array();
		    $cache_feed_id = ! empty( $atts['feed'] ) ? (int)$atts['feed'] : false;
		    $page = isset( $_POST['page'] ) ? (int)$_POST['page'] : 1;
		    if ( ! empty( $cache_feed_id ) ) {
			    $feed_cache = new CFF_Cache( $cache_feed_id, $page );
			    $feed_cache->clear( 'meta' );
		    }

	    }
		die();
	}

}
