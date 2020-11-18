<?php
if (! class_exists('ESSB_TinyMCE_Loader')) {

    class ESSB_TinyMCE_Loader {

        public function __construct () {
            if (!essb_option_bool_value('classic_editor_disable_buttons')) {
                add_action('admin_init', array ( $this, 'tinymce_loader' ));
                add_action('admin_enqueue_scripts', array ( $this, 'tinymce_css' ), 10);
            }
        }

        /**
         * load our CSS file
         *
         * @return [type] [description]
         */
        public function tinymce_css () {
            if (!essb_option_bool_value('essb_deactivate_ctt')) {            
                wp_enqueue_style('essb-ctt-admin', ESSB3_PLUGIN_URL . '/assets/admin/tinymce/ctt.css', array (), null, 'all');
            }
            
            if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
                wp_enqueue_style('essb-pp-admin', ESSB3_PLUGIN_URL . '/assets/admin/tinymce/pinpro.css', array (), null, 'all');
            }
        }

        /**
         * load the TinyMCE button
         *
         * @return [type] [description]
         */
        public function tinymce_loader () {
            $can_use = true;
            
            if (essb_option_bool_value('limit_editor_fields') && function_exists('essb_editor_capability_can')) {
                $can_use = essb_editor_capability_can();
            }
            
            if ($can_use && !essb_option_bool_value('essb_deactivate_ctt')) {
                add_filter('mce_external_plugins', array ( __class__, 'essb_ctt_tinymce_core' ));
                add_filter('mce_buttons', array ( __class__, 'essb_ctt_tinymce_buttons' ));
            }
            
            if ($can_use && !essb_option_bool_value('deactivate_module_pinterestpro')) {
                add_filter('mce_external_plugins', array ( __class__, 'essb_pp_tinymce_core' ));
                add_filter('mce_buttons', array ( __class__, 'essb_pp_tinymce_buttons' ));
            }
        }

        /**
         * loader for the required JS
         *
         * @param $plugin_array [type]
         *            [description]
         * @return [type] [description]
         */
        public static function essb_ctt_tinymce_core ($plugin_array) {
            
            // add our JS file
            $plugin_array['essb_ctt'] = ESSB3_PLUGIN_URL . '/assets/admin/tinymce/ctt.js';
            
            // return the array
            return $plugin_array;
        }
        
        /**
         * loader for the required JS
         *
         * @param $plugin_array [type]
         *            [description]
         * @return [type] [description]
         */
        public static function essb_pp_tinymce_core ($plugin_array) {
            
            // add our JS file
            $plugin_array['essb_pp'] = ESSB3_PLUGIN_URL . '/assets/admin/tinymce/pinpro.js';
            
            // return the array
            return $plugin_array;
        }

        /**
         * Add the button key for event link via JS
         *
         * @param $buttons [type]
         *            [description]
         * @return [type] [description]
         */
        public static function essb_ctt_tinymce_buttons ($buttons) {
            
            // push our buttons to the end
            array_push($buttons, 'essb_ctt');
            
            // now add back the sink
            // send them back
            return $buttons;
        }
        
        public static function essb_pp_tinymce_buttons ($buttons) {
            
            // push our buttons to the end
            array_push($buttons, 'essb_pp');
            
            // now add back the sink
            // send them back
            return $buttons;
        }
        
        // end class
    }
    
    // Instance once the class
    if (class_exists('ESSB_Factory_Loader')) {
        ESSB_Factory_Loader::activate('admin-tinymce-loader', 'ESSB_TinyMCE_Loader');
    }
    else {
        new ESSB_TinyMCE_Loader();
    }
}