<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
global $wt_cli_integration_list;

$wt_cli_integration_list = apply_filters('wt_cli_plugin_integrations', array(

    'facebook-for-wordpress' => array(
        'constant_or_function' => 'FacebookPixelPlugin\\FacebookForWordpress',
        'label'                => 'Official Facebook Pixel',
        'status'               =>  'yes',
        'description'          => 'Official Facebook Pixel',
        'category'             => -1,
        'type'                 =>  1
    ),
    'twitter-feed'   => array(
		'constant_or_function' => 'CTF_VERSION',
        'label'                => 'Smash Balloon Twitter Feed',
        'status'               =>  'yes',
        'description'          => 'Twitter Feed By Smash Baloon',
        'category'             => -1,
        'type'                 =>  1
    ),
    'instagram-feed'   => array(
		'constant_or_function' => 'SBIVER',
        'label'                => 'Smash Balloon Instagram Feed',
        'status'               =>  'yes',
        'description'          => 'Instagram Feed By Smash Baloon',
        'category'             => -1,
        'type'                 =>  1
    ),
));

if (!class_exists('Cookie_Law_Info_Script_Blocker')) {
    class Cookie_Law_Info_Script_Blocker
    {   
        
        protected $script_data;
        public $script_blocker_status;
        public $js_blocking;
        public $third_party_enabled;

        protected $script_table     = 'cli_scripts';
        protected $module_id        = 'script-blocker';


        function __construct()
        {   
            add_action( 'plugins_loaded', array($this, 'load_integrations'), 10);
            add_action( 'template_redirect', array($this, 'start_buffer'));
            add_action( 'shutdown', array($this, 'end_buffer'), 999);
            register_activation_hook( CLI_PLUGIN_FILENAME, array($this, 'activator'));
            add_action( 'activated_plugin', array($this, 'update_integration_data'));
            add_action( 'admin_menu', array( $this, 'register_settings_page' ),20 );
            add_action( 'wp_ajax_wt_cli_change_plugin_status',array($this, 'change_plugin_status'));
            add_action( 'init', array( $this, 'update_script_blocker_status' ));
            add_action( 'wt_cli_after_advanced_settings', array( $this, 'add_blocking_control'));
            add_action( 'wt_cli_ajax_settings_update', array( $this, 'update_js_blocking_status'),10,1);
            $this->script_blocker_status = $this->get_script_blocker_status();
            $this->script_data = $this->get_script_data();
            $this->js_blocking = Cookie_Law_Info::get_js_option();
            $this->third_party_enabled = $this->get_third_party_status();
        }

        /**
         * Get the current status of the integrations
         * @since  1.9.2
         * @access public
         * @return array
         */
        public function get_script_data() {
            global $wpdb;
            $script_table = $wpdb->prefix.$this->script_table;
            $scripts = array();
            if($wpdb->get_var("SHOW TABLES LIKE '$script_table'") == $script_table) {
                $script_data = $wpdb->get_results( "select * from {$script_table}", ARRAY_A);
                foreach( $script_data as $key => $data ) {

                    $id             =   sanitize_text_field( ( isset( $data['id'] ) ? $data['id'] : '' ) );
                    $slug           =   sanitize_text_field( ( isset( $data['cliscript_key'] ) ? $data['cliscript_key'] : '' ) );
                    $title          =   sanitize_text_field( ( isset( $data['cliscript_title'] ) ? $data['cliscript_title'] : '' ) );
                    $description    =   sanitize_text_field( ( isset( $data['cliscript_description'] ) ? $data['cliscript_description'] : '' ) );
                    $category       =   sanitize_text_field( ( isset( $data['cliscript_category'] ) ? $data['cliscript_category'] : '' ) );
                    $status         =   wp_validate_boolean( ( isset( $data['cliscript_status'] ) ? $data['cliscript_status'] : false ) );
                    
                    if( !empty( $id ) ) {
                        $scripts[ $slug ] =  array(
                            'id'            =>  $id,
                            'title'         =>  $title,
                            'description'   =>  $description,
                            'category'      =>  $category,
                            'status'        =>  $status,
                        );
                    }
                }
            }
            return $scripts;
        }
        /**
         * Register admin menu for the plugn
         * @since  1.9.2
         * @access public
         * @return bool
         */
        public function get_script_blocker_status() {
            $status = get_option('cli_script_blocker_status');
            if( isset( $status ) && $status === 'enabled' ) {
                return true;
            }
            return false;
        }
        public function get_third_party_status(){
            $default_settings 	= 	Cookie_Law_Info_Admin::get_non_necessary_defaults();
            $stored_options = get_option('cookielawinfo_thirdparty_settings');
            $wt_cli_non_necessary_enabled  =  isset($stored_options['thirdparty_on_field']) ? $stored_options['thirdparty_on_field'] : $default_settings['thirdparty_on_field'];
            $wt_cli_non_necessary_enabled = Cookie_Law_Info::sanitise_settings('thirdparty_on_field',$wt_cli_non_necessary_enabled);
            return $wt_cli_non_necessary_enabled;
        }
        /**
         * Update script blocker status
         * @since  1.9.2
         * @access public
         */
        public function update_script_blocker_status()
        {	
            
            if( isset( $_POST['cli_update_script_blocker'] ) )
            {	
                if (!current_user_can('manage_options')) 
                {
                    wp_die(__('You do not have sufficient permission to perform this operation', 'cookie-law-info'));
                }
                check_admin_referer( $this->module_id );
                $cli_sb_status = ( isset( $_POST['cli_script_blocker_state'] ) ? sanitize_text_field( $_POST['cli_script_blocker_state'] ) : '' );
                if( $cli_sb_status === 'enabled') {
                    $this->script_blocker_status = true;
                } else {
                    $this->script_blocker_status = false;
                }
                update_option( 'cli_script_blocker_status', $cli_sb_status );
            }	
        }
        /**
         * Register admin menu for the plugn
         * @since  1.9.2
         * @access public
         * @return array
         */
        public function register_settings_page() {

            
            add_submenu_page(
                'edit.php?post_type='.CLI_POST_TYPE,
                __( 'Script Blocker', 'cookie-law-info' ),
                __( 'Script Blocker', 'cookie-law-info' ),
                'manage_options',
                'cli-script-settings',
                array( $this, 'integrations_settings_page')
            );
        }

        /**
         * Admin menu settings page
         * @since  1.9.2
         * @access public
         * @return array
         */
        public function integrations_settings_page() {

            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permission to perform this operation', 'cookie-law-info'));
            }
		    if(isset($_GET['post_type']) && $_GET['post_type']==CLI_POST_TYPE && isset($_GET['page']) && $_GET['page']=='cli-script-settings') {
                
                global $wt_cli_integration_list;
                $script_data            =   $this->script_data;
                $script_blocker_status  =   $this->script_blocker_status;
                $js_blocking            =   $this->js_blocking;
                $messages = array(
                    'success' => __('Status updated','cookie-law-info'),
                );
                $settings = array(
                    'ajax_url'          =>  admin_url( 'admin-ajax.php' ),
                    'nonce'             =>  wp_create_nonce( $this->module_id ),
                    'messages'          =>  $messages
                );
                wp_enqueue_style('cookie-law-info');
                wp_enqueue_script('cookie-law-info-script-blocker',plugin_dir_url( __FILE__ ).'assets/js/script-blocker.js', array( 'jquery','cookie-law-info'),CLI_VERSION,false);
                wp_localize_script( 'cookie-law-info-script-blocker', 'wt_cli_script_blocker_obj', $settings );

            }
            include plugin_dir_path( __FILE__ ).'views/settings.php';
        }
        /**
         * Add option to enable or disable javascript blocking in advanced settings tab
         * @since  1.9.2
         * @access public
         */
        public function add_blocking_control(){

            $js_blocking = $this->js_blocking;
            echo '<table class="form-table">
                    <tr valign="top">
                        <th scope="row">'.__('Advanced script rendering', 'cookie-law-info').'</th>
                        <td>
                            <input type="radio" id="wt_cli_js_blocking_enable_field" name="wt_cli_js_blocking_field" class="styled" value="yes" '.checked( $js_blocking, true, false).' /><label for="wt_cli_js_blocking_enable_field" >'.__('Enable', 'cookie-law-info').'</label>
                            <input type="radio" id="wt_cli_js_blocking_disable_field" name="wt_cli_js_blocking_field" class="styled" value="no" '.checked( $js_blocking, false, false).' /><label for="wt_cli_js_blocking_disable_field" >'.__('Disable', 'cookie-law-info').'</label>
                            <span class="cli_form_help" style="margin-top:10px;">'.__('Advanced script rendering will render the blocked scripts using javascript thus eliminating the need for a page refresh. It is also optimized for caching since there is no server-side processing after obtaining the consent.','cookie-law-info').'</span>
                        </td>
                        </tr>
                 </table>
                 <div style="clear: both;"></div>
                 <div class="cli-plugin-toolbar bottom">
                    <div class="left">
                    </div>
                    <div class="right">
                        <input type="submit" name="update_admin_settings_form" value="'. __('Update Settings', 'cookie-law-info').'" class="button-primary" style="float:right;" onClick="return cli_store_settings_btn_click(this.name)" />
                        <span class="spinner" style="margin-top:9px"></span>
                    </div>
                 </div>';
        }

        /**
         * Enabe or disable javascript blocking
         * @since  1.9.2
         * @access public
         */
        public function update_js_blocking_status( $data ) {

            $js_blocking = 'no';
            if( isset( $data['wt_cli_js_blocking_field'] ) && $data['wt_cli_js_blocking_field'] === 'yes' ) {
                $js_blocking = 'yes';
            }
            $this->js_blocking = $js_blocking;
            update_option('cookielawinfo_js_blocking', $js_blocking );

        }
        /**
         * Fire during plugin activation or deactivaion
         * @since  1.9.2
         * @access public
         */
        public function activator()
        {       
            global $wpdb;
            $activation_transient = wp_validate_boolean( get_transient('_wt_cli_first_time_activation') ); 
            $plugin_settings = get_option(CLI_SETTINGS_FIELD);
            
            if( Cookie_Law_Info::maybe_first_time_install() === true && $activation_transient === true ) {
                $script_blocking = $this->get_script_blocker_status();
                if( $script_blocking === false ) {
                    update_option('cli_script_blocker_status', 'enabled');
                }
            }
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            if (is_multisite()) {
                // Get all blogs in the network and activate plugin on each one
                $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    self::install_tables();
                    restore_current_blog();
                }
            } else {
                self::install_tables();
            }
        }

        /**
         * Install necessary tables for storing integrations data
         * @since  1.9.2
         * @access public
         */
        public static function install_tables()
        {
            global $wpdb;
            $search_query = "SHOW TABLES LIKE %s";
            $charset_collate = $wpdb->get_charset_collate();
            $like = '%' . $wpdb->prefix . 'cli_scripts%';
            $table_name = $wpdb->prefix . 'cli_scripts';
            if (!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) {

                $sql = "CREATE TABLE $table_name(
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `cliscript_title` TEXT NOT NULL,
                    `cliscript_category` VARCHAR(100) NOT NULL,
                    `cliscript_type` INT DEFAULT 0,
                    `cliscript_status` VARCHAR(100) NOT NULL,
                    `cliscript_description` LONGTEXT NOT NULL,
                    `cliscript_key` VARCHAR(100) NOT NULL,
                    `type` INT NOT NULL DEFAULT '0',
                    PRIMARY KEY(`id`)
                ) $charset_collate;";
                dbDelta($sql);
            }
            self::update_table_columns( $table_name );
            self::insert_scripts( $table_name );
        }
        /**
         * Update the status of the plugin based on user option
         * @since  1.9.2
         * @access public
         */
        public function change_plugin_status() {

            if (!current_user_can('manage_options')) 
            {
                wp_die(__('You do not have sufficient permission to perform this operation', 'cookie-law-info'));
            }
            check_ajax_referer( $this->module_id );
            $script_id  =   (int) ( isset( $_POST['script_id'] ) ? $_POST['script_id'] : -1 );
            $status     =   wp_validate_boolean( ( isset( $_POST['status'] ) ? $_POST['status'] : false ) ) ;
            if ( $script_id !== -1 ) {
                $this->update_script_status($script_id, $status );
                wp_send_json_success();
            }
            wp_send_json_error();

        }
        public function update_script_status( $id, $status) {

            global $wpdb;
            $script_table = $wpdb->prefix.$this->script_table;
            $wpdb->query($wpdb->prepare("UPDATE $script_table SET cliscript_status = %d WHERE id = %d", $status, $id));

        }
        /**
         * Load integration if it is currently activated
         * @since  1.9.2
         * @access public
         */
        public static function insert_scripts($table_name)
        {

            global $wpdb;
            global $wt_cli_integration_list;
            foreach ($wt_cli_integration_list as $key => $value) {
                $data = array(
                    'cliscript_key' => isset($key) ? $key : '',
                    'cliscript_title' => isset($value['label']) ? $value['label'] : '',
                    'cliscript_category' => isset($value['category']) ? $value['category'] : 1,
                    'cliscript_type' => isset($value['type']) ? $value['type'] : 0,
                    'cliscript_status' => isset($value['status']) ? $value['status'] : true,
                    'cliscript_description' => isset($value['description']) ? $value['description'] : '',
                );
                $data_exists = $wpdb->get_row("SELECT id FROM `$table_name` WHERE `cliscript_key`='" . $key . "'", ARRAY_A);
                if (!$data_exists) {
                    $wpdb->insert($table_name, $data);
                }
            }
        }
        /**
         *
         * @access private
         * @return void
         * @since  1.9.2
         */
        private static function update_table_columns( $table_name )
        {
            global $wpdb;
            $search_query = "SHOW COLUMNS FROM `$table_name` LIKE 'cliscript_type'";
            if(!$wpdb->get_results($search_query,ARRAY_N)) 
            {
                $wpdb->query("ALTER TABLE `$table_name` ADD `cliscript_type` INT DEFAULT 0 AFTER `cliscript_category`");
            }
        }
        /**
         * Load integration if it is currently activated
         * @since  1.9.2
         * @access public
         */
        public function load_integrations()
        {
            global $wt_cli_integration_list;
            foreach ($wt_cli_integration_list as $plugin => $details) {
                if ( $this->wt_cli_plugin_is_active($plugin) ) {

                    $file = plugin_dir_path( __FILE__ ) . "integrations/$plugin.php";
                    if (file_exists($file)) {
                        require_once($file);
                    } else {
                        error_log("searched for $plugin integration at $file, but did not find it");
                    }
                }
            }
        }

        /**
         * Check and load necessary plugin data if it is in disabled state
         * @since  1.9.2
         * @access public
         */
        public function update_integration_data()
        {

        }
        
        /**
         * Check if the listed integration is active on the website
         * @since  1.9.2
         * @access public
         */
        public function wt_cli_plugin_is_active($plugin)
        {
            global $wt_cli_integration_list;
            $script_data = $this->script_data;
            if( empty( $script_data ) ) {
                return false;
            }
            if (!isset($wt_cli_integration_list[$plugin])) return false;
            $details = $wt_cli_integration_list[$plugin];
            
            $enabled = isset( $script_data[ $plugin ]['status'] ) ? wp_validate_boolean( $script_data[ $plugin ]['status'] )  : false;
            if ( ( defined($details['constant_or_function'])
                    || function_exists($details['constant_or_function'])
                    || class_exists($details['constant_or_function']) ) && $enabled === true ) {
                return true;
            }
            return false;
        }

        /**
         * Start buffering the output for blocking scripts
         * @since  1.9.2
         * @access public
         */

        public function start_buffer()
        {   
            if( $this->script_blocker_status === true && $this->js_blocking === true && $this->third_party_enabled === true ) {
                ob_start(array($this, "init"));
            }
        }
        /**
         * Flush the buffer
         *
         * @access public
         */

        public function end_buffer()
        {   
            if( $this->script_blocker_status === true && $this->js_blocking === true && $this->third_party_enabled === true ) { 
                if (ob_get_length()) {
                    ob_end_flush();
                }
            }
        }

        /**
         * Starts replacing the tags that should be blocked
         *
         * @since  1.9.2
         * @access public
         * @param  string
         * @return string
         */

        public function init($buffer)
        {   
            $buffer = $this->replace_scripts($buffer);
            return $buffer;
        }

        /**
		 * check if there is a partial match between a key of the array and the haystack
		 * We cannot use array_search, as this would not allow partial matches.
		 *
		 * @param string $haystack
		 * @param array  $needle
		 *
		 * @return bool|string
		 */

		private function strpos_arr( $haystack, $needle ) {
			if ( empty( $haystack ) ) {
				return false;
			}

			if ( ! is_array( $needle ) ) {
				$needle = array( $needle );
			}

			foreach ( $needle as $key => $value ) {
				if ( strlen($value) === 0 ) continue;
				if ( ( $pos = strpos( $haystack, $value ) ) !== false ) {
					return ( is_numeric( $key ) ) ? $value : $key;
				}
			}

			return false;
		}
        /**
         * Perform a series of regular expression operation to find and replace the unwanted tags from the output
         *
         * @since  1.9.2
         * @access public
         * @param  string
         * @return string
         */
        
        public function replace_scripts($buffer)
        {   
            $third_party_script_tags = array();
            
            $third_party_script_tags = apply_filters( 'wt_cli_third_party_scripts',$third_party_script_tags );
            
            $script_pattern = '/(<script.*?>)(\X*?)<\/script>/i';
            $index          = 0;
            if (preg_match_all(
                $script_pattern,
                $buffer,
                $matches,
                PREG_PATTERN_ORDER
            )) {
                
                foreach ($matches[1] as $key => $script_open) {
                    //exclude ld+json
                    if (
                        strpos($script_open, 'application/ld+json')
                        !== false
                    ) {
                        continue;
                    }
                    $total_match = $matches[0][$key];
                    $content     = $matches[2][$key];
                   
                    //if there is inline script here, it has some content
                    if (!empty($content)) {
                        $found = $this->strpos_arr(
                            $content,
                            $third_party_script_tags
                        );

                        if ($found !== false) {
                            $new    = $total_match;
                            $new    = $this->replace_script_type_attribute( $new );
                            $buffer = str_replace( $total_match, $new,$buffer );
                        }   
                    }
                    $script_src_pattern
						= '/<script [^>]*?src=[\'"](http:\/\/|https:\/\/|\/\/)([\w.,;@?^=%&:()\/~+#!\-*]*?)[\'"].*?>/i';
                        if ( preg_match_all( $script_src_pattern, $total_match,
						$src_matches, PREG_PATTERN_ORDER )
					) {
                        $script_src_matches = ( isset( $src_matches[2] ) && is_array( $src_matches[2] ) ) ? $src_matches[2] : array();
                        if( !empty( $script_src_matches ) ) {
                            foreach ( $src_matches[2] as $src_key => $script_src ) {
                                $script_src = $src_matches[1][ $src_key ]. $src_matches[2][ $src_key ];
                                $found = $this->strpos_arr( $script_src,
                                    $third_party_script_tags );

                                if ( $found !== false ) {
                                    $new    = $total_match;
                                    $new    = $this->replace_script_type_attribute( $new );
                                    $buffer = str_replace( $total_match, $new,$buffer );
                                }
                            }
                        }
                    }
                }
            }
            return $buffer;
        }

        public function replace_script_type_attribute( $script) {

            $replace = 'data-cli-class="cli-blocker-script"  data-cli-script-type="non-necessary" data-cli-block="true"  data-cli-element-position="head"';
            $script_type = "text/plain";

            if( preg_match( '/<script[^\>]*?\>/m', $script ) ) {
				$changed = true;
				if (preg_match('/<script.*(type=(?:"|\')(.*?)(?:"|\')).*?>/', $script) && preg_match('/<script.*(type=(?:"|\')text\/javascript(.*?)(?:"|\')).*?>/', $script )) {
					preg_match('/<script.*(type=(?:"|\')text\/javascript(.*?)(?:"|\')).*?>/', $script, $output_array);
					$re = preg_quote($output_array[1],'/');
					if(!empty($output_array)) {   
                        
                    	$script = preg_replace('/' .$re .'/', 'type="'.$script_type.'"'.' '.$replace, $script,1);

                    }
				} else {
                    
                    $script = str_replace('<script', '<script type="'.$script_type.'"'.' '.$replace, $script);   
                
                }
				
            }
            return $script;
        }
    }
}
new Cookie_Law_Info_Script_Blocker();
