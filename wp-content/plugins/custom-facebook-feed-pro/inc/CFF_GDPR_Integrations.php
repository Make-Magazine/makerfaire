<?php
/**
 * Class CFF_GDPR_Integrations
 *
 * Adds GDPR related workarounds for third-party plugins:
 * https://wordpress.org/plugins/cookie-law-info/
 *
 * @since 2.6/3.17
 */
namespace CustomFacebookFeed;

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class CFF_GDPR_Integrations {

	/**
	 * Nothing currently for CFF
	 *
	 * @since 2.6/3.17
	 */
	public static function init() {
		add_action( 'admin_init', array( 'CustomFacebookFeed\CFF_GDPR_Integrations', 'clear_resizing_once' ) );
		add_filter( 'wt_cli_third_party_scripts', array( 'CustomFacebookFeed\CFF_GDPR_Integrations', 'undo_script_blocking' ), 11 );
	}

	/**
	 * Prevents changes made to how JavaScript file is added to
	 * pages.
	 *
	 * @param array $blocking
	 *
	 * @return array
	 *
	 * @since 2.6/3.17
	 */
	public static function undo_script_blocking( $blocking ) {
		$options = get_option( 'cff_style_settings', array() );
		if ( ! CFF_GDPR_Integrations::doing_gdpr( $options ) ) {
			return $blocking;
		}
		remove_filter( 'wt_cli_third_party_scripts', 'wt_cli_facebook_feed_script' );

		return $blocking;
	}

	/**
	 * Whether or not consent plugins that Custom Facebook Feed
	 * is compatible with are active.
	 *
	 * @return bool|string
	 *
	 * @since 2.6/3.17
	 */
	public static function gdpr_plugins_active() {
		if ( class_exists( 'Cookie_Notice' ) ) {
			return 'Cookie Notice by dFactory';
		}
		if ( function_exists( 'run_cookie_law_info' ) || class_exists( 'Cookie_Law_Info' ) ) {
			return 'GDPR Cookie Consent by WebToffee';
		}
		if ( class_exists( 'Cookiebot_WP' ) ) {
			return 'Cookiebot by Cybot A/S';
		}
		if ( class_exists( 'COMPLIANZ' ) ) {
			return 'Complianz by Really Simple Plugins';
		}
		if ( function_exists('BorlabsCookieHelper') ) {
			return 'Borlabs Cookie by Borlabs';
		}

		return false;
	}

	/**
	 * GDPR features can be added automatically, forced enabled,
	 * or forced disabled.
	 *
	 * @param $settings
	 *
	 * @return bool
	 *
	 * @since 2.6/3.17
	 */
	public static function doing_gdpr( $settings ) {
		$gdpr = isset( $settings['gdpr'] ) ? $settings['gdpr'] : 'auto';
		if ( $gdpr === 'no' ) {
			return false;
		}
		if ( $gdpr === 'yes' ) {
			return true;
		}
		return (CFF_GDPR_Integrations::gdpr_plugins_active() !== false);
	}

	/**
	 * If the image resizing and local storage feature isn't working,
	 * CDN images are allowed to be displayed.
	 *
	 * @param $settings
	 *
	 * @return bool
	 *
	 * @since 2.6/3.17
	 */
	public static function blocking_cdn( $settings ) {
		$gdpr = isset( $settings['gdpr'] ) ? $settings['gdpr'] : 'auto';
		if ( $gdpr === 'no' ) {
			return false;
		}
		if ( $gdpr === 'yes' ) {
			return true;
		}
		$cff_statuses_option = get_option( 'cff_statuses', array() );

		if ( $cff_statuses_option['gdpr']['from_update_success'] ) {
			return (CFF_GDPR_Integrations::gdpr_plugins_active() !== false);
		}
		return false;
	}

	/**
	 * GDPR features are reliant on the image resizing features
	 *
	 * @param bool $retest
	 *
	 * @return bool
	 *
	 * @since 2.6/3.17
	 */
	public static function gdpr_tests_successful( $retest = false ) {
		$cff_statuses_option = get_option( 'cff_statuses', array() );

		if ( ! isset( $cff_statuses_option['gdpr']['image_editor'] ) || $retest ) {
			$test_image = trailingslashit( CFF_PLUGIN_URL ) . 'assets/img/placeholder.png';

			$image_editor = wp_get_image_editor( $test_image );

			// not uncommon for the image editor to not work using it this way
			$cff_statuses_option['gdpr']['image_editor'] = false;
			// not uncommon for the image editor to not work using it this way
			if ( ! is_wp_error( $image_editor ) ) {
				$cff_statuses_option['gdpr']['image_editor'] = true;
			} else {
				$image_editor = wp_get_image_editor( 'http://plugin.smashballoon.com/editor-test.png' );
				if ( ! is_wp_error( $image_editor ) ) {
					$cff_statuses_option['gdpr']['image_editor'] = true;
				}
			}

			$upload     = wp_upload_dir();
			$upload_dir = $upload['basedir'];
			$upload_dir = trailingslashit( $upload_dir ) . CFF_UPLOADS_NAME;
			if ( file_exists( $upload_dir ) ) {
				$cff_statuses_option['gdpr']['upload_dir'] = true;
			} else {
				$cff_statuses_option['gdpr']['upload_dir'] = false;
			}

			global $wpdb;
			$table_name = esc_sql( $wpdb->prefix . CFF_POSTS_TABLE );
			$cff_statuses_option['gdpr']['tables'] = true;
			if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
				$cff_statuses_option['gdpr']['tables'] = false;
			}

			$feeds_posts_table_name = esc_sql( $wpdb->prefix . CFF_POSTS_TABLE );
			if ( $wpdb->get_var( "show tables like '$feeds_posts_table_name'" ) != $feeds_posts_table_name ) {
				$cff_statuses_option['gdpr']['tables'] = false;
			}

			update_option( 'cff_statuses', $cff_statuses_option );
		}
		if ( $retest ) {
			\cff_main_pro()->cff_error_reporter->add_action_log( 'Retesting GDPR features.' );
		}

		if ( ! $cff_statuses_option['gdpr']['upload_dir']
		     || ! $cff_statuses_option['gdpr']['tables']
		     || ! $cff_statuses_option['gdpr']['image_editor'] ) {
			return false;
		}

		return true;
	}
	/**
	 * Error message HTML, if any
	 *
	 * @return string
	 *
	 * @since 2.6/3.17
	 */
	public static function gdpr_tests_error_message() {
		$cff_statuses_option = get_option( 'cff_statuses', array() );

		$errors = array();
		if ( ! $cff_statuses_option['gdpr']['upload_dir'] ) {
			$errors[] =  __( 'A folder for storing resized images was not successfully created.' );
		}
		if ( ! $cff_statuses_option['gdpr']['tables'] ) {
			$errors[] = __( 'Tables used for storing information about resized images were not successfully created.' );
		}
		if ( ! $cff_statuses_option['gdpr']['image_editor'] ) {
			$errors[] = sprintf( __( 'An image editor is not available on your server. Facebook Feed is unable to create local resized images. See %sthis FAQ%s for more information' ), '<a href="https://smashballoon.com/doc/the-images-in-my-feed-are-missing-or-showing-errors/?facebook" target="_blank" rel="noopener noreferrer">','</a>' );
		}

		if ( isset( $_GET['tab'] ) && $_GET['tab'] !== 'support' ) {
			$errors[] = '<a href="?page=cff-style&amp;tab=misc&amp;retest=1" class="button button-secondary">' . __( 'Retest', 'custom-facebook-feed' ) . '</a>';
		}

		return implode( '<br>', $errors );
	}

	/**
	 * Only medium sized images are created unless the GDPR features are enabled.
	 * This clears the resizing tables and resets any caches the first time
	 * the GDPR features are active.
	 *
	 * @since 2.6/3.17
	 */
	public static function clear_resizing_once() {
		$cff_statuses_option = get_option( 'cff_statuses', array() );
		$options = get_option( 'cff_style_settings', array() );

		if ( CFF_GDPR_Integrations::doing_gdpr( $options )
			&& ! isset( $cff_statuses_option['gdpr']['clear_resizing_once'] ) ) {
			$cff_statuses_option['gdpr']['clear_resizing_once'] = true;
			CFF_Resizer::delete_resizing_table_and_images();
			CFF_Resizer::create_resizing_table_and_uploads_folder();
			cff_delete_cache();
			update_option( 'cff_statuses', $cff_statuses_option );
		}
	}

}