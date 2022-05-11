<?php

/**
 * Loging the subscribe form conversions
 * @author appscreo
 * @package EasySocialShareButtons
 */
class ESSB_Subscribe_Conversions_Pro {
    /**
     * Only those actions are allowed
     * @var array
     */
    private static $allowed_actions = array('view', 'subscribe_ok', 'subscribe_fail');
    
    /**
     * mySQL table name for data storage
     * @var string
     */
    private static $table_name = 'essb_subscribe_conversions';
    
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
        ESSB_Ajax::register_frontend_action('subscribe_conversion_loaded', array(__CLASS__, 'db_log_loaded'));
        ESSB_Ajax::register_frontend_action('subscribe_conversion_success', array(__CLASS__, 'db_log_sucess'));
        ESSB_Ajax::register_frontend_action('subscribe_conversion_fail', array(__CLASS__, 'db_log_fail'));
    }
    
    public static function db_log_loaded() {
        $params = self::prepare_request_options();
        if (!$params['valid']) {
            self::send_error();
        }
        else {
            self::db_log('view', $params['post_id'], $params['position'], $params['design'], $params['mobile']);
        }
    }
    
    public static function db_log_sucess() {
        $params = self::prepare_request_options();
        if (!$params['valid']) {
            self::send_error();
        }
        else {
            self::db_log('subscribe_ok', $params['post_id'], $params['position'], $params['design'], $params['mobile']);
        }
    }
    
    public static function db_log_fail() {
        $params = self::prepare_request_options();
        if (!$params['valid']) {
            self::send_error();
        }
        else {
            self::db_log('subscribe_fail', $params['post_id'], $params['position'], $params['design'], $params['mobile']);
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
        if (!empty($_POST['design'])) { $output['design'] = $_POST['design']; }
        if (!empty($_POST['mobile'])) { $output['mobile'] = $_POST['mobile']; }
        
        $all_parameters = array('post_id', 'position', 'design', 'mobile');
        foreach ($all_parameters as $param) {
            if (empty($output[$param])) {
                $output['valid'] = false;
            }
        }        
        
        return $output;
    }
    
    private static function db_log($action, $post_id = '', $position = '', $design = '', $mobile = '0') {
        global $wpdb;
        
        // security: actions should be allowed only, otherwise do nothing
        if (!in_array($action, self::$allowed_actions)) {
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
                'design' => sanitize_text_field($design),
                'mobile' => sanitize_text_field($mobile)                 
            )
        );
        
        wp_send_json(array( 'result' => 'success'));
    }
    
    private static function inject_tracking_code() {
        essb_resource_builder ()->add_static_resource ( ESSB3_PLUGIN_URL . '/lib/modules/conversions-pro/assets/subscribe-conversions-tracker.js', 'essb-subscribe-conversions-tracker', 'js' );        
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
  design varchar(20) NOT NULL,
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
    
    public static function read_design_conversions() {
        global $wpdb;
        $sql = 'SELECT count(action) as count, action, design FROM '.self::get_table_name();
        
        if (!empty(self::$query_period_filter)) {
            $sql .= ' ' . self::$query_period_filter;
        }
        
        $sql .= ' GROUP BY action, design ORDER BY design ASC';        
        
        $db_result = $wpdb->get_results($sql);
        
        $result = array();
        
        foreach ($db_result as $one) {
            $design = $one->design;
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
}

ESSB_Subscribe_Conversions_Pro::init();

if (!function_exists('essb_subscribe_conversions_dashboard_report')) {
    function essb_subscribe_conversions_dashboard_report() {
        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/dashboard-subscribe-conversions.php');
    }
}