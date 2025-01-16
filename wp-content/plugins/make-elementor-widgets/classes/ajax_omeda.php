<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Make_Widget_Ajax {
    function __construct() {
        // Omeda postal ID
        add_action('wp_ajax_saveOmedaID', array($this, 'ajax_save_omeda_postalID'));
        add_action('wp_ajax_nopriv_saveOmedaID', array($this, 'ajax_save_omeda_postalID'));
    }


    /**
     * Ajax: fetch the fearured products after load more button is triggerred.
     * We render only <li>
     */
    public function ajax_save_omeda_postalID() {

        $ajax   = wp_doing_ajax();

        // # Check if there is any nonce is sent via ajax. If not, throw the error
        if (empty($_POST['nonce'])) {
            $err_msg = __('Insecure form submitted without security token. Plaese contact developer', 'make-elementor-widgets');
            if ($ajax) {
                wp_send_json_error($err_msg);
            }
            return false;
        }

        // # Check if nonce matches what we set at Ajax call. If not, throw the error
        if (!wp_verify_nonce($_POST['nonce'], 'omeda_ajax')) {
            $err_msg = __(
                'Security token did not match. Please contact developer',
                'make-elementor-widgets'
            );
            if ($ajax) {
                wp_send_json_error($err_msg);
            }
            return false;
        }

        //retrieve submitted data here
        $userInfo = wp_get_current_user();

        $fieldUpdated = xprofile_set_field_data("Make: Magazine Account ID", $userInfo->ID, $_POST['postal_id']);
        if($fieldUpdated){
            echo 'Account ID updated';
        }else{
            echo 'Account ID not updated';
        }

        ob_end_flush();
        wp_die();  // using wp_die() when ajax call

    } //end function
}

$Make_Ajax_LoadMore = new Make_Widget_Ajax;
