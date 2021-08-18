<?php

/**
 * Defined and manage default network colors
 * 
 * @author AppsCreo
 * @package EasySocialShareButtons
 * @since 8.0
 */

class ESSBSocialFollowersCounterAssets {
    
    /**
     * Control will it load or not the minified styles
     * @var boolean
     */
    private static $debug_mode = false;
    /**
     * Default network colors
     * @var array
     */
    public static $network_colors = array(
        'telegram' => '#0088cc',
        'mailerlite' => '#00a154',
        'itunes' => '#ff573d',
        'deviantart' => '#05cc47',
        'paypal' => '#0070ba',
        'whatsapp' => '#1D9E11',
        'tripadvisor' => '#00a680',
        'snapchat' => '#FFFC00',
        'xing' => '#135a5b',
        'medium' => '#12100e',
        'tiktok' => '#12100e',
        'patreon' => '#fc573b',
        'ok' => '#F4731C',
        'mixer' => '#212c3d',
        'mailchimp' => '#2c9ab7',
        'subscribe' => '#2c9ab7',
        'youtube' => '#CD332D',
        'email' => '#393939',
        'vimeo' => '#1ab7ea',
        'twitter' => '#4099FF', 
        'facebook' => '#3B5998',
        'pinterest' => '#cb2027',
        'linkedin' => '#007bb6',
        'github' => '#171515',
        'instagram' => '#3f729b',
        'soundcloud' => '#ff7700',
        'behance' => '#005cff',
        'delicious' => '#205cc0',
        'foursquare' => '#25a0ca',
        'forrst' => '#5b9a68', 
        'dribbble' => '#ea4c89',
        'envato' => '#82b540',
        'vk' => '#45668e',
        'rss' => '#FF6600',
        'tumblr' => '#32506d',
        'vine' => '#00b488',
        'slideshare' => '#e98325',
        '500px' => '#02adea',
        'flickr' => '#FF0084',
        'wp_posts' => '#c2685f',
        'wp_comments' => '#b8c25f',
        'wp_users' => '#5fa7c2',
        'audioboo' => '#b0006d',
        'steamcommunity' => '#000000',
        'weheartit' => '#ff679d',
        'feedly' => '#02bb24',
        'love' => '#ED1C24',
        'mailpoet' => '#F14176',
        'mymail' => '#28b4e9', 
        'spotify' => '#84bd00',
        'twitch' => '#6441a5',
        'subscribe_form' => '#f47555',
        'total' => '#555',
        'periscope' => '#40a4c4'
    );
    
    public static function init() {
        /**
         * Allow integration of custom colors (or replace the existing)
         */
        if (has_filter('essb_follower_networks_colors')) {
            self::$network_colors = apply_filters('essb_follower_networks_colors', self::$network_colors);
        }
        
        /**
         * Loading module assets
         */
        add_action('wp_enqueue_scripts', array(__CLASS__, 'maybe_load_assets'), 1);
    }
    
    public static function core_stylesheet() {
        return ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/social-profiles'.(!self::$debug_mode ? '.min': '').'.css';
    }
    
    public static function init_profiles() {
        if (essb_is_plugin_deactivated_on() || essb_is_module_deactivated_on('profiles')) {
            return;
        }
        
        essb_resource_builder()->add_static_resource(ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/social-profiles'.(!self::$debug_mode ? '.min': '').'.css', 'essb-social-profiles', 'css');
        
        /**
         * May be load the customizer of colors if enabled
         */
        self::profiles_customizer();        
    }
    
    /**
     * Loading plugin assets
     */
    public static function maybe_load_assets() {
        if (essb_is_plugin_deactivated_on() || essb_is_module_deactivated_on('fanscounter')) {
            return;
        }
                
        essb_resource_builder()->add_static_resource(ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/social-profiles'.(!self::$debug_mode ? '.min': '').'.css', 'essb-social-profiles', 'css');
                
        /**
         * May be load the customizer of colors if enabled
         */
        self::followers_customizer();
    }
    
    /**
     * Generate the custom colors over Profile
     */
    public static function profiles_customizer() {
        if (essb_option_bool_value('activate_profiles_customizer')) {
            $global_color = essb_option_value('profilecustomize_all');
            $global_hover_color = essb_option_value('profilecustomize_hover_all');
            
            $colors = '';
            $hover_colors = '';
            
            $network_list = ESSBSocialProfilesHelper::available_social_networks();
            
            foreach ($network_list as $network => $title) {
                $single_color = essb_option_value('profilecustomize_' . $network);
                $single_hover_color = essb_option_value('profilecustomize_hover_' . $network);
                
                if ($single_color == '' && $global_color != '') {
                    $single_color = $global_color;
                }
                
                if ($single_hover_color == '' && $global_hover_color != '') {
                    $single_hover_color = $global_hover_color;
                }
                
                $social_display = $network;
                if ($social_display == "instgram") {
                    $social_display = "instagram";
                }
                
                if ($single_color != '') {
                    $colors .= self::network_color_variable($social_display) . ':' . $single_color.';';
                }
                
                if ($single_hover_color != '') {
                    $hover_colors .= '.essb-profiles .essb-fc-network-'.esc_attr($social_display).':hover {' . self::network_color_variable($social_display) . ':' . $single_hover_color.';' .'}';
                }
            }
            
            if ($colors != '') {
                $colors = '.essb-profiles { ' .$colors . '}';
                if ($hover_colors != '') {
                    $colors .= $hover_colors;
                }
                essb_resource_builder()->add_css($colors, 'essb-profile-colors-customize');
            }
            else if ($hover_colors != '') {
                essb_resource_builder()->add_css($hover_colors, 'essb-profile-colors-customize');
            }
        }
    }
    
    /**
     * Generate the custom colors over Followers
     */
    public static function followers_customizer() {
        if (essb_option_bool_value('activate_fanscounter_customizer')) {
            $global_color = essb_option_value('fanscustomizer_all');
            $global_hover_color = essb_option_value('fanscustomizer_hover_all');
            
            $colors = '';
            $hover_colors = '';
            
            $network_list = ESSBSocialFollowersCounterHelper::available_social_networks();
            
            foreach ($network_list as $network => $title) {
                $single_color = essb_option_value('fanscustomizer_' . $network);
                $single_hover_color = essb_option_value('fanscustomizer_' . $network);
                
                if ($single_color == '' && $global_color != '') {
                    $single_color = $global_color;
                }
                
                if ($single_hover_color == '' && $global_hover_color != '') {
                    $single_hover_color = $global_hover_color;
                }
                
                $social_display = $network;
                if ($social_display == "instgram") {
                    $social_display = "instagram";
                }
                
                if ($single_color != '') {
                    $colors .= self::network_color_variable($social_display) . ':' . $single_color.';';
                }
                
                if ($single_hover_color != '') {
                    $hover_colors .= '.essb-followers .essb-fc-network-'.esc_attr($social_display).':hover {' . self::network_color_variable($social_display) . ':' . $single_hover_color.';' .'}';
                }
            }
            
            if ($colors != '') {
                $colors = '.essb-followers { ' .$colors . '}';
                if ($hover_colors != '') {
                    $colors .= $hover_colors;
                }
                essb_resource_builder()->add_css($colors, 'essb-followers-colors-customize');
            }
            else if ($hover_colors != '') {
                essb_resource_builder()->add_css($hover_colors, 'essb-followers-colors-customize');
            }
        }
    }
    
    /**
     * Generate the CSS color variable
     * @param string $network
     * @return string
     */
    public static function network_color_variable($network = '') {
        return '--essb-sf-color-'.esc_attr($network);
    }
    
    /**
     * Prepare colors for all networks
     * 
     * @param array $networks
     * @return string
     */
    public static function prepare_active_network_colors($networks = array()) {
        /**
         * Default colors
         * @var string $output
         */
        $output = '.essb-social-followers-variables{';
        
        foreach ($networks as $network) {
            if ($network == 'instgram') { $network = 'instagram'; }
            if (isset(self::$network_colors[$network])) {
                $output .= self::network_color_variable($network) . ':' . self::$network_colors[$network].';';
            }
        }
        
        $output .= '}';        
        
        /*foreach ($networks as $network) {
            if ($network == 'instgram') { $network = 'instagram'; }
            
            $output .= '.essb-fc-bg-'.$network.', .essb-fc-hbg-'.$network.':hover{ background-color: var( '.self::network_color_variable($network). ' );}'."\n";
            $output .= '.essb-fc-c-'.$network.', .essb-fc-hc-'.$network.':hover { color: var( '.self::network_color_variable($network). ' ); fill: var( '.self::network_color_variable($network). ' );}'."\n";
            $output .= '.essb-fc-border-'.$network.', .essb-fc-hborder-'.$network.':hover{ border-color: var( '.self::network_color_variable($network). ' );}'."\n";
        }*/
        
        return $output;
    }
}