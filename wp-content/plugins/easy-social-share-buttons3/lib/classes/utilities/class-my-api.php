<?php 

class ESSB_MyAPI {
    
    /**
     * The main API address
     * @var string
     */
    private static $api_url = 'https://my.socialsharingplugin.com/wp-json/my-essb/v1/';
    
    private static $news_types = array(
        'news' => array('title' => 'News', 'color' => '#1D89F9'),
        'promo' => array('title' => 'Promo', 'color' => '#C94670'),
        'update' => array('title' => 'Update', 'color' => '#419C33'),
        'other' => array('title' => 'Other', 'color' => '#CB9C2A'),
        'guide' => array('title' => 'Guide', 'color' => '#57C5EA')
    );
    
    /**
     * Read the API endpoint and retrieve the result
     * @param string $endpoint
     * @param array $options
     * @return mixed
     */
    private static function get($endpoint = '', $options = array()) {
        
        $url = self::$api_url . $endpoint;
        
        if (count($options) > 0) {
            $url = add_query_arg($options, $url);
        }
        
        $response = wp_remote_get( $url );        
        return json_decode( wp_remote_retrieve_body( $response ) );
    }    
    
    private static function post($endpoint = '', $options = array()) {
        $url = self::$api_url . $endpoint;
        
        if (count($options) > 0) {
            $url = add_query_arg($options, $url);
        }
                
        $curl = curl_init();
        
        curl_setopt_array($curl, array (
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array (
                "Content-Type: application/json"
            )
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode( $response );
    }
    
    private static function output_type($type = '') {
        $r = '';
        
        if (isset(self::$news_types[$type])) {
            $r = self::$news_types[$type]['title'];
        }
        
        return $r;
    }
    
    private static function format_date_to_local($date = '') {
        $date = str_replace('-', '/', $date);
        $time = strtotime($date);
        
        return date(get_option('date_format'), $time);        
    }
    
    private static function stylesheet_feed() {
        $output = '';
        
        $output .= '.essb-admin-dashboard-widget .essb-news-typetag-update { background-color: ' . self::$news_types['update']['color'] . '; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-typetag-news { background-color: ' . self::$news_types['news']['color'] . '; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-typetag-promo { background-color: ' . self::$news_types['promo']['color'] . '; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-typetag-other { background-color: ' . self::$news_types['other']['color'] . '; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-typetag-guide { background-color: ' . self::$news_types['guide']['color'] . '; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-typetag {
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
            padding: 2px 4px;
            line-height: 1em;
            border-radius: 3px;
            margin-right: 5px;
            font-weight: 600;
            letter-spacing: 0.1px; 
        }';
        
        $output .= '.essb-admin-dashboard-widget .essb-news-row { margin-top: 10px; margin-bottom: 10px; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-row .essb-news-title a { font-weight: 600; text-decoration: none; line-height: 1.4em; }';
        $output .= '.essb-admin-dashboard-widget .essb-news-row .essb-news-date { margin-left: 5px; }';
        $output .= '.essb-admin-dashboard-widget .essb-admindash-overview-logo { border-radius: 50px; }';
        
        return $output;
    }
    
    public static function get_latest_news() {
        if ( false === ( $news = get_transient('essb_api_latest_news'))) {
            $news = self::get('news');
            delete_transient('essb_api_latest_news');
            set_transient('essb_api_latest_news', $news, DAY_IN_SECONDS);
        }
        
        return $news;
    }
    
    public static function generate_news_output($news = array()) {
        $output = '';
        
        $output .= '<style>' . self::stylesheet_feed() . '</style>';
        
        foreach ($news as $obj) {                    
            $title = $obj->display_title;
            if (empty($title)) {
                $title = $obj->title;
            }
            
            $obj->url = add_query_arg ( array( 'utm_source' => 'news', 'utm_medium' => 'widget', 'utm_campaign' => $obj->type), $obj->url);
            
            $output .= '<div class="essb-news-row">';
            $output .= '<span class="essb-news-typetag essb-news-typetag-' . $obj->type . '">' . self::output_type($obj->type) . '</span>';
            $output .= '<span class="essb-news-title"><a href="'. $obj->url .'" target="_blank">' . $title . '</a></span>';
            if ($obj->type == 'promo') {
                $output .= '<span class="essb-news-date">' . self::format_date_to_local($obj->period_from) . ' - ' . self::format_date_to_local($obj->period_to) . '</span>';
            }
            else {
                $output .= '<span class="essb-news-date">'.self::format_date_to_local($obj->date).'</span>';
            }
            
            $output .= '</div>';
        }        
        
        return $output;
    }
    
    /**
     * Generate an inline code verification snippet
     */
    public static function should_validate_code() {
        if (ESSBActivationManager::isActivated() && true == get_transient('essb-pending-code-validate')) {
            $hash = hash ( 'sha1', ESSBActivationManager::domain() );
            
            $code = "
<script>
jQuery(document).ready(function( $ ) {
	\"use strict\";
    let options = { 'action': 'essb_validate_code', 'hash': '" . $hash . "' };
				$.ajax({
		            type: \"POST\",
		            url: '".esc_url(admin_url ('admin-ajax.php'))."',
		            data: options,
		            success: function (data) {
		            	console.log(data);
		            	if (data['code']) {
                            if (data['code'] == '404') {
                                swal({
                                    title: 'An error occurred while verifying the domain activation',
                                	icon: 'error',
                                	text: 'There is a problem with the domain verification connected with your purchase code. This problem may appear if the code is used on another website (or it was not disconnected from a previous website). You can re-activate the plugin again with this purchase code if all other installations are removed. If you experience a problem or need help you can refer to our support team.',
                                	className: \"essb-swal\",
                                });

                                setTimeout(function() {
                                    window.location.href = '".esc_url(admin_url('admin.php?page=essb_redirect_update&tab=update'))."';
                                }, 5000);
                            }
                            if (data['code'] == '500') {
                                swal({
                                    title: 'Purchase code used for the activation is blocked',
                                	icon: 'error',
                                	text: 'The purchase code you are using is no more valid as it is blocked. The code is blocked usually when a refund request is issued (the code become invalid) or if it was distributed illegally. If that is by mistake you can contact our support team for resolving the issue.',
                                	className: \"essb-swal\",
                                });

                                setTimeout(function() {
                                    window.location.href = '".esc_url(admin_url('admin.php?page=essb_redirect_update&tab=update'))."';
                                }, 5000);

                            }
                        }
		            }
		    	});
});
</script>
";
            echo $code;
        }
    }
    
    public static function refresh_news() {
        $hash = hash ( 'sha1', ESSBActivationManager::domain() );
            
        $code = "
<script>
jQuery(document).ready(function( $ ) {
	\"use strict\";
    let options = { 'action': 'essb_update_latest_news', 'hash': '" . $hash . "' };
				$.ajax({
		            type: \"POST\",
		            url: '".esc_url(admin_url ('admin-ajax.php'))."',
		            data: options,
		            success: function (data) {
		            	console.log(data);		            	
		            }
		    	});
});
</script>
";
        echo $code;
    }
    
    public static function define_validate_action() {
        $essb_settings_access = essb_option_value('essb_access');
        if (empty($essb_settings_access)) {
            $essb_settings_access = 'manage_options';
        }
        
        if (is_admin() && current_user_can($essb_settings_access)) {
            add_action ( 'wp_ajax_essb_validate_code', array ('ESSB_MyAPI', 'validate_code' ) );
        }
    }
    
    public static function define_news_update_action() {
        $essb_settings_access = essb_option_value('essb_access');
        if (empty($essb_settings_access)) {
            $essb_settings_access = 'manage_options';
        }
        
        if (is_admin() && current_user_can($essb_settings_access)) {
            add_action ( 'wp_ajax_essb_update_latest_news', array ('ESSB_MyAPI', 'update_latest_news' ) );
        }
    }
    
    public static function update_latest_news() {
        $current_news = self::get_latest_news();
        
        if (is_array($current_news)) {
            echo '200';
        }
        else {
            echo '404';
        }
        die();
    }
    
    public static function news_update_required() {
        $r = false;
        if (!essb_option_bool_value('deactivate_appscreo')) {
            if ( false === ( $news = get_transient('essb_api_latest_news'))) {
                $r = true;
            }
        }
        
        return $r;
    }
    
    public static function validate_code() {
        $request_hash = isset($_POST['hash']) ? $_POST['hash'] : '';
        $hash = hash ( 'sha1', ESSBActivationManager::domain() );
        
        $output = array();
        
        if ($request_hash == $hash) {
            $options['hash'] = $hash;
            $options['domain'] = ESSBActivationManager::domain();
            $options['code'] = ESSBActivationManager::getPurchaseCode();
            
            $output = self::post('verify-code', $options);              
            
            if (isset($output->code) && $output->code != '') {
                if ($output->code == '404' || $output->code == '500') {
                    // in case of an error the plugin deactivates the license key
                    ESSBActivationManager::deactivate();
                }
            }
        }
        
        echo json_encode($output);
        die();
    }
    
    public static function has_promotion() {
        $news = get_transient('essb_api_latest_news');
        $r = false;
        
        if (is_array($news)) {
            foreach ($news as $obj) {
                if ($obj->type == 'promo') {
                    
                    $period_from = strtotime($obj->period_from);
                    $period_to = strtotime($obj->period_to);
                    
                    if (time() >= $period_from && time() <= $period_to) {
                        if (!$obj->display_title || empty($obj->display_title)) {
                            $obj->display_title = $obj->title;
                        }
                        
                        $period = '<span class="essb-badge essb-badge-running">' . self::format_date_to_local($obj->period_from) . ' - ' . self::format_date_to_local($obj->period_to) . '</span>';
                        $obj->url = add_query_arg ( array( 'utm_source' => 'news', 'utm_medium' => 'settings_banner', 'utm_campaign' => $obj->type), $obj->url);
                        
                        $cache_clear_address = $obj->url;
                        $dismiss_addons_button = '<a href="' . $cache_clear_address . '"  class="status_button essb-btn float_right" style="margin-right: 5px;" target="_blank">' . esc_html__ ( 'Learn More', 'essb' ) . '</a>';
                        essb_display_static_header_message($obj->display_title, $dismiss_addons_button, 'ti-gift', 'essb-options-hint-promo');
                    }
                }
            }
        }
    }
}