<?php 

/**
 * Integrated the custom profile network buttons created by user
 *
 * @author appscreo
 * @since 8.0
 */

class ESSB_Custom_Profile_Networks {
    
    public static $buttons = array();
    
    /**
     * Start the registration
     */
    public static function register_custom_networks() {
        if (! function_exists ( 'essb_get_custom_profile_buttons' )) {
            include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/customprofilebuttons-helper.php');
        }
        
        self::$buttons = essb_get_custom_profile_buttons();
        

        add_filter('essb_svg_icons', array(__CLASS__, 'register_svg_icons'));        
        add_filter('essb4_follower_networks', array(__CLASS__, 'register_profile_networks'));
        add_filter('essb4_follower_networks_settings', array(__CLASS__, 'register_follower_fields'));
        add_filter('essb_followers_counter_values', array(__CLASS__, 'register_custom_followers_value'));
        add_filter('essb_followers_active_networks', array(__CLASS__, 'register_custom_followers_active'));
        
        $color_css = '';
        $additional_css = '';
        foreach (self::$buttons as $key => $data) {
            add_filter("essb4_followers_{$key}_url", array(__CLASS__, 'register_custom_follow_url'));
            $color = isset($data['accent_color']) ? $data['accent_color'] : '';
            
            if (!empty($color)) {
                $color_css .= '--essb-sf-color-'.esc_attr($key).':'.$color.';';
                
                $additional_css .= '.essb-fc-bg-'.esc_attr($key).', .essb-fc-hbg-'.esc_attr($key).':hover{
                    background-color: var( --essb-sf-color-'.esc_attr($key).' );
                }
                .essb-fc-c-'.esc_attr($key).', .essb-fc-hc-'.esc_attr($key).':hover {
                    color: var( --essb-sf-color-'.esc_attr($key).' );
                    fill: var( --essb-sf-color-'.esc_attr($key).' );
                }
                .essb-fc-border-'.esc_attr($key).', .essb-fc-hborder-'.esc_attr($key).':hover{
                    border-color: var( --essb-sf-color-'.esc_attr($key).' );
                }';
                
                $additional_css .= '.essb-fc-network-airbnb svg { max-width: 32px; }';
            }
        }
        
        if (!empty($color_css)) {
            $color_css = '.essb-social-followers-variables { ' .$color_css . '}';
            $color_css .= $additional_css;
            ESSB_Dynamic_CSS_Builder::register_dynamic_code('essb-followers-colors-custom-networks', $color_css);
        }
        
        add_filter('essb_follower_networks_colors', array(__CLASS__, 'register_custom_colors'));
    }
    
    /**
     * Register the custom SVG icons
     * 
     * @param array $icons
     * @return string|array
     */
    public static function register_svg_icons($icons = array()) {
        foreach (self::$buttons as $key => $data) {
            $icon = isset($data['icon']) ? $data['icon'] : '';
            
            if (!empty($icon)) {
                $icon = base64_decode($icon);
                $icons[$key] = $icon;
            }
        }
        
        return $icons;
    }
    
    /**
     * Register the custom networks in the profiles/followers
     * @param array $networks
     * @return string|array
     */
    public static function register_profile_networks($networks = array()) {
        
        foreach (self::$buttons as $key => $data) {
            $name = isset($data['name']) ? $data['name'] : '';
            
            if (!empty($name)) {
                $networks[$key] = $name;
            }
        }
        
        return $networks;
    }
    
    /**
     * Register additional fields for those custom networks in the followers counter
     * @param array $options
     * @return string[]
     */
    public static function register_follower_fields($options = array()) {
        foreach (self::$buttons as $key => $data) {
            $options[$key]['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'default' => 'Followers');
            $options[$key]['uservalue'] = array('type' => 'textbox', 'text' => 'Number of followers or custom text');
            $options[$key]['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
        }
        
        return $options;
    }
    
    /**
     * Number of the followers for the custom networks
     * @param array $counters
     * @return Ambigous
     */
    public static function register_custom_followers_value($counters = array()) {
        
        foreach (self::$buttons as $key => $data) {
            $counters[$key] = ESSBSocialFollowersCounterHelper::get_option($key.'_uservalue');
        }
        
        return $counters;
    }
    
    /**
     * Check if the custom network is configured
     * 
     * @param array $networks
     * @return array|array[]
     */
    public static function register_custom_followers_active($networks = array()) {
        foreach (self::$buttons as $key => $data) {
            $url = ESSBSocialFollowersCounterHelper::get_option ( $key . '_url' );
            if (!empty($url)) {
                
                /**
                 * @since 8.8.2
                 */
                if (!in_array($key, $networks)) {                
                    $networks[] = $key;
                }
            }
        }
        return $networks;
    }
    
    /**
     * Generate the custom network URL (followers counter)
     * @param string $network
     * @return string|Ambigous
     */
    public static function register_custom_follow_url($network = '') {
        $r = '';
        
        if (isset(self::$buttons[$network])) {
            $r = ESSBSocialFollowersCounterHelper::get_option ( $network . '_url' );
        }
        
        return $r;
    }
    
    /**
     * Register the default network colors
     * @param array $colors
     * @return string|array
     */
    public static function register_custom_colors($colors = array()) {
        foreach (self::$buttons as $key => $data) {
            $color = isset($data['accent_color']) ? $data['accent_color'] : '';
            
            if (!empty($color)) {
                $colors[$key] = $color;
            }
        }
        
        return $colors;
    }
}

ESSB_Custom_Profile_Networks::register_custom_networks();