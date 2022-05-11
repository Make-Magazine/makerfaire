<?php

/**
 * Loging the share button conversions
 * @author appscreo
 * @package EasySocialShareButtons
 */
class ESSB_Share_Conversions_Pro {
    /**
     * Only those actions are allowed
     * @var array
     */
    private static $allowed_actions = array('view', 'share');
    
    /**
     * mySQL table name for data storage
     * @var string
     */
    private static $table_name = 'essb_share_conversions';
    
    private static $query_period_filter = '';
    
    /**
     * Register the events
     */
    public static function init() {
        if (!is_admin()) {
            self::register();
            self::inject_tracking_code();
        }
    }
    
    public static function register() {
        ESSB_Ajax::register_frontend_action('sharing_conversion_loaded', array(__CLASS__, 'db_log_loaded'));
        ESSB_Ajax::register_frontend_action('sharing_conversion_share', array(__CLASS__, 'db_log_sucess'));
        ESSB_Ajax::register_frontend_action('sharing_conversion_register', array(__CLASS__, 'db_log_register'));
    }
    
    /**
     * Log all share button locations and networks with a single request
     */
    public static function db_log_register() {        
        $params = array();
        
        if (!empty($_POST['post_id'])) { $params['post_id'] = $_POST['post_id']; }
        if (!empty($_POST['conversion'])) { $params['conversion'] = $_POST['conversion']; }
        if (!empty($_POST['mobile'])) { $params['mobile'] = $_POST['mobile']; }
        
        if (!empty($params['post_id']) && !empty($params['conversion']) && !empty($params['mobile'])) {                        
            foreach ($params['conversion'] as $key => $networks) {
                foreach ($networks as $network_id) {
                    self::db_log('view', $params['post_id'], $key, $network_id, $params['mobile']);
                }
            }
        }
    }
    
    public static function db_log_loaded() {
        $params = self::prepare_request_options();
        if (!$params['valid']) {
            self::send_error();
        }
        else {
            self::db_log('view', $params['post_id'], $params['position'], $params['network'], $params['mobile']);
        }
    }
    
    public static function db_log_sucess() {
        $params = self::prepare_request_options();
        if (!$params['valid']) {
            self::send_error();
        }
        else {
            self::db_log('share', $params['post_id'], $params['position'], $params['network'], $params['mobile']);
        }
    }     
    
    private static function get_table_name() {
        global $wpdb;
        $table  = $wpdb->prefix . self::$table_name;
        
        return $table;
    }
    
    private static function send_error() {
        wp_send_json_error( array( 'error' => 'Invalid request' ) );
    }
    
    private static function prepare_request_options() {
        $output = array( 'valid' => true );
        
        if (!empty($_POST['post_id'])) { $output['post_id'] = $_POST['post_id']; }
        if (!empty($_POST['position'])) { $output['position'] = $_POST['position']; }
        if (!empty($_POST['network'])) { $output['network'] = $_POST['network']; }
        if (!empty($_POST['mobile'])) { $output['mobile'] = $_POST['mobile']; }
        
        $all_parameters = array('post_id', 'position', 'network', 'mobile');
        foreach ($all_parameters as $param) {
            if (empty($output[$param])) {
                $output['valid'] = false;
            }
        }        
        
        return $output;
    }
    
    private static function db_log($action, $post_id = '', $position = '', $network = '', $mobile = '0') {
        global $wpdb;
        
        // security: actions should be allowed only, otherwise do nothing
        if (!in_array($action, self::$allowed_actions)) {
            wp_send_json(array('action' => $action, 'allowed' => self::$allowed_actions));
            return;
        }
        
        if ($mobile == 'false' || $mobile == 'true') {
            if ($mobile == 'false') { $mobile = '0'; }
            if ($mobile == 'true') { $mobile = '1'; }
        }
        
        $table_name = $wpdb->prefix.self::$table_name;
        
        $rows_affected = $wpdb->insert ( $table_name,
            array (
                'action' => sanitize_text_field($action),
                'post_id' => sanitize_text_field($post_id),
                'position' => sanitize_text_field($position),
                'network' => sanitize_text_field($network),
                'mobile' => sanitize_text_field($mobile)                 
            )
        );
        
        //wp_send_json(array( 'result' => 'success'));
    }
    
    private static function inject_tracking_code() {
        essb_resource_builder ()->add_static_resource ( ESSB3_PLUGIN_URL . '/lib/modules/conversions-pro/assets/share-conversions-tracker.js', 'essb-share-conversions-tracker', 'js' );        
    }
    
    /**
     * Create the required table for loggin the subscribe form conversions
     */
    public static function install() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix.self::$table_name;
        
        $sql = "
        CREATE TABLE {$table_name} (
  id bigint(32) NOT NULL AUTO_INCREMENT,
  action varchar(24) NOT NULL,
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  post_id varchar(10) NOT NULL,
  position varchar(20) NOT NULL,
  network varchar(30) NOT NULL,
  mobile int(2) NOT NULL,
  PRIMARY KEY (id),
  KEY action (action),
  KEY timestamp (timestamp)
) $charset_collate";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Clear stored table information
     */
    public static function clear_data() {
        global $wpdb;
        $table  = $wpdb->prefix . self::$table_name;
        $delete = $wpdb->query(("TRUNCATE TABLE $table"));
    }
    
    /**
     * Clear stored table information and delete it from the database
     */
    public static function uninstall() {
        global $wpdb;
        $table  = $wpdb->prefix . self::$table_name;
        $wpdb->query( "DROP TABLE IF EXISTS ".$table );
    }
    
    public static function read_set_period_filter($type = '') {
        if (!empty($type)) {
            self::$query_period_filter = '';
            
            $today = date("Y-m-d");
            
            if ($type == '1') {
                self::$query_period_filter = 'WHERE DATE_FORMAT(timestamp, "%Y-%m-%d") = "'.$today.'"';
            }
            
            if ($type == '7') {
                $first_date = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( $today ) ) . "-7 days" ) );
                self::$query_period_filter = 'WHERE DATE_FORMAT(timestamp, "%Y-%m-%d") >= "'.$first_date.'" AND DATE_FORMAT(timestamp, "%Y-%m-%d") <= "'.$today.'"';
            }
            
            if ($type == '30') {
                $first_date = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( $today ) ) . "-30 days" ) );
                self::$query_period_filter = 'WHERE DATE_FORMAT(timestamp, "%Y-%m-%d") >= "'.$first_date.'" AND DATE_FORMAT(timestamp, "%Y-%m-%d") <= "'.$today.'"';
            }
        }
    }
    
    public static function read_network_conversions($position = '') {
        global $wpdb;
        $sql = 'SELECT count(action) as count, action, network FROM '.self::get_table_name();
        
        if (!empty(self::$query_period_filter)) {
            $sql .= ' ' . self::$query_period_filter;
            
            if ($position != '') {
                $sql .= ' AND position = "'.$position.'"';
            }
        }
        else {
            if ($position != '') {
                $sql .= ' WHERE position = "'.$position.'"';
            }
        }
        
        $sql .= ' GROUP BY action, network ORDER BY network ASC';        
        
        $db_result = $wpdb->get_results($sql);
        
        $result = array();
        
        foreach ($db_result as $one) {
            $network = $one->network;
            $action = $one->action;
            $count = $one->count;
            
            if (!isset($result[$network])) {
                $result[$network] = array();
            }
            
            if (!isset($result[$network][$action])) {
                $result[$network][$action] = 0;
            }
            
            $result[$network][$action] += intval($count);
        }
        
        return $result;
    }
    
    public static function read_post_conversions() {
        global $wpdb;
        $sql = 'SELECT count(action) as count, action, post_id FROM '.self::get_table_name();
        
        if (!empty(self::$query_period_filter)) {
            $sql .= ' ' . self::$query_period_filter;
        }        
        
        $sql .= ' GROUP BY action, post_id ORDER BY post_id ASC';
        
        $db_result = $wpdb->get_results($sql);
        
        $result = array();
        
        foreach ($db_result as $one) {
            $post_id = $one->post_id;
            $action = $one->action;
            $count = $one->count;
            
            if (!isset($result[$post_id])) {
                $result[$post_id] = array();
            }
            
            if (!isset($result[$post_id][$action])) {
                $result[$post_id][$action] = 0;
            }
            
            $result[$post_id][$action] += intval($count);
        }
        
        return $result;
    }
    
    public static function read_position_conversions() {
        global $wpdb;
        $sql = 'SELECT count(action) as count, action, position FROM '.self::get_table_name();
        
        if (!empty(self::$query_period_filter)) {
            $sql .= ' ' . self::$query_period_filter;
        }
        
        $sql .= ' GROUP BY action, position ORDER BY position ASC';
        
        $db_result = $wpdb->get_results($sql);
        
        $result = array();
        
        foreach ($db_result as $one) {
            $design = $one->position;
            $action = $one->action;
            $count = $one->count;
            
            if (!isset($result[$design])) {
                $result[$design] = array();
            }
            
            if (!isset($result[$design][$action])) {
                $result[$design][$action] = 0;
            }
            
            $result[$design][$action] += intval($count);
        }
        
        return $result;
    }
    
    public static function read_device_conversions() {
        global $wpdb;
        $sql = 'SELECT count(action) as count, action, mobile FROM '.self::get_table_name();
        
        if (!empty(self::$query_period_filter)) {
            $sql .= ' ' . self::$query_period_filter;
        }
        
        $sql .= ' GROUP BY action, mobile';
        
        $db_result = $wpdb->get_results($sql);
        
        $result = array();
        
        foreach ($db_result as $one) {
            $mobile = $one->mobile;
            $action = $one->action;
            $count = $one->count;
            
            $mobile_key = $mobile == 0 ? 'desktop' : 'mobile';
            
            if (!isset($result[$mobile_key])) {
                $result[$mobile_key] = array();
            }
            
            if (!isset($result[$mobile_key][$action])) {
                $result[$mobile_key][$action] = 0;
            }
            
            $result[$mobile_key][$action] += intval($count);
        }
        
        return $result;
    }
    
    public static function data_sort_desc($data, $param) {
        $index = array();
        
        foreach ($data as $key => $values) {
            $value = isset($values[$param]) ? $values[$param] : 0;
            
            $index[$key] = $value;
        }
        
        arsort($index, SORT_NUMERIC);
        
        $r = array();
        
        foreach ($index as $key => $value) {
            $r[$key] = $data[$key];
        }
        
        return $r;
    }
}

ESSB_Share_Conversions_Pro::init();

if (!function_exists('essb_share_conversions_dashboard_report')) {
    function essb_share_conversions_dashboard_report() {
        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/dashboard-share-conversions.php');
    }
}

if (!function_exists('essb_share_conversions_dashboard_report_posts')) {
    function essb_share_conversions_dashboard_report_posts() {
        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/dashboard-share-conversions-post.php');
    }
}