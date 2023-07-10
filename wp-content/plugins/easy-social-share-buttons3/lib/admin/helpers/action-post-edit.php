<?php

if (!function_exists('essb_admin_ajax_helper_post_actions')) {
    function essb_admin_ajax_helper_post_actions() {
        if (! isset( $_REQUEST['essb_admin_post_action_token'] ) || !wp_verify_nonce( $_REQUEST['essb_admin_post_action_token'], 'essb_admin_post_action' )) {
            print 'Sorry, your nonce did not verify.';
            wp_die();
        }
        
        $status = array('code' => 0);
        
        if (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'clear_short') {
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
                    
            if (!empty($post_id) && class_exists('ESSB_Short_URL')) {
                ESSB_Short_URL::clear_post_cached_urls($post_id);
                $status['code'] = 200;
            }
        }
        
        if (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'clear_share_counters') {
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
            
            if (!empty($post_id) && class_exists('ESSB_Short_URL')) {
                delete_post_meta($post_id, 'essb_cache_expire');
                $networks = essb_available_social_networks();
                
                foreach ($networks as $key => $data) {
                    delete_post_meta($post_id, 'essb_c_'.$key);
                }
                
                delete_post_meta($post_id, 'essb_c_total');
                $status['code'] = 200;
            }
        }
        
        wp_send_json($status);
        die();        
    }
}