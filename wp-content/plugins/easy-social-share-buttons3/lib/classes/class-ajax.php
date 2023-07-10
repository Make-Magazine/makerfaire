<?php
/**
 * Easy Social Share Buttons ESSB_AJAX. AJAX Event Handlers.
 *
 * @class   ESSB_AJAX
 * @package EasySocialShareButtons
 */

defined( 'ABSPATH' ) || exit;

class ESSB_Ajax {
    
    public static function init_frontend() {
        add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
        add_action( 'template_redirect', array( __CLASS__, 'do_ajax' ), 0 );
    }
    
    public static function define_ajax() {
        // phpcs:disable
        if ( ! empty( $_REQUEST['essb-ajax'] ) ) {
            essb_maybe_define_constant( 'DOING_AJAX', true );
            essb_maybe_define_constant( 'ESSB_DOING_AJAX', true );
            if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
                @ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
            }
            $GLOBALS['wpdb']->hide_errors();
        }
        // phpcs:enable
    }
    
    private static function ajax_headers() {
        if ( ! headers_sent() ) {
            send_origin_headers();
            send_nosniff_header();
            nocache_headers();
            header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
            header( 'X-Robots-Tag: noindex' );
            status_header( 200 );
        } elseif ( essb_constant_is_true( 'WP_DEBUG' ) ) {
            headers_sent( $file, $line );
            trigger_error( "ajax_headers cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
        }
    }
    
    public static function do_ajax() {
        if ( ! empty( $_REQUEST['essb-ajax'] ) ) {
            global $wp_query;
            
            // phpcs:disable WordPress.Security.NonceVerification.Recommended
            if ( ! empty( $_REQUEST['essb-ajax'] ) ) {
                $wp_query->set( 'essb-ajax', sanitize_text_field( wp_unslash( $_REQUEST['essb-ajax'] ) ) );
            }
            
            $action = $wp_query->get( 'essb-ajax' );
            
            if ( $action ) {
                self::ajax_headers();
                $action = sanitize_text_field( $action );
                do_action( 'essb_ajax_' . $action );
                wp_die();
            }
        }
    }
    
    public static function register_frontend_action($ajax_event, $callback) {
        add_action('essb_ajax_' . $ajax_event, $callback);
    }
    
    public static function register_action($ajax_event, $callback, $nopriv = false) {
        add_action( 'wp_ajax_essb_' . $ajax_event, $callback );        
        if ($nopriv) {
            add_action( 'wp_ajax_nopriv_essb_' . $ajax_event, $callback);
        }
    }
}
