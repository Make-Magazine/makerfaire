<?php

/**
 * Manage default template colors and network styling
 *
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 8.x
 */
class ESSB_Share_Button_Styles {
    
    /**
     * Base class names
     */
    private static $base_color_variable = '--essb-s-';
    
    // network based
    private static $base_class_background = 'essb-s-bg-';
    private static $base_class_background_hover = 'essb-s-bgh-';
    private static $base_class_color = 'essb-s-c-';
    private static $base_class_color_hover = 'essb-s-ch-';
    private static $base_class_border_color = 'essb-s-b-';
    private static $base_class_border_color_hover = 'essb-s-bh-';
    
    private static $base_class_network_background = 'essb-s-bg-network';
    private static $base_class_network_background_hover = 'essb-s-bgh-network';
    private static $base_class_network_color = 'essb-s-c-network';
    private static $base_class_network_color_hover = 'essb-s-ch-network';
    private static $base_class_network_border_color = 'essb-s-b-network';
    private static $base_class_network_border_color_hover = 'essb-s-bh-network';
    
    // general
    private static $base_class_light_color = 'essb-s-c-light';
    private static $base_class_light_color_hover = 'essb-s-ch-light';
    private static $base_class_dark_color = 'essb-s-c-dark';
    private static $base_class_dark_color_hover = 'essb-s-ch-dark';
    private static $base_class_hover_effect = 'essb-s-hover-effect';
    private static $base_class_dark_background_hover = 'essb-s-bgh-dark';
    private static $base_class_dark_border_color_hover = 'essb-s-bh-dark';
    
    public static $dynamic_styles_loaded = true;
    public static $dynamic_templates_loaded = false;
    
    
    /**
     * Base network colors for share social networks
     * @var array
     */
    public static $network_colors = array(
        'meneame' => '#FF7D12',
        'line' => '#2CBF13',
        'flipboard' => '#B31F17',
        'comments' => '#444',
        'yummly' => '#e26326',
        'sms' => '#4ea546',
        'viber' => '#7d539d',
        'subscribe' => '#f47555',
        'skype' => '#00aff0',
        'messenger' => '#0d87ff',
        'kakaotalk' => '#FBE600',
        'sharebtn' => '#2B6A94',
        'share' => '#2B6A94',
        'del' => '#3398fc',
        'livejournal' => '#0ca8ec',
        'yammer' => '#3469BA',
        'meetedgar' => '#6cbdc5',
        'fintel' => '#404040',
        'instapaper' => '#404040',
        'mix' => '#ff8226',
        'more' => '#c5c5c5',
        'more_dots' => '#c5c5c5',
        'less' => '#c5c5c5',
        'reddit' => '#333',
        'blogger' => '#f59038',
        'amazon' =>  '#111111',
        'yahoomail' => '#511295',
        'gmail' => '#dd4b39',
        'newsvine' => '#0d642e',
        'hackernews' => '#f08641',
        'evernote' => '#7cbf4b',
        'aol' => '#111111',
        'myspace' => '#3a5998',
        'mailru' => '#FAA519',
        'viadeo' => '#222222',
        'print' => '#404040',
        'mail' => '#404040',
        'copy' => '#404040',
        'buffer' => '#111111',
        'digg' => '#1b5791',
        'flattr' => '#8CB55B',
        'pocket' => '#EE4055',
        'weibo' => '#ED1C24',
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
        'twitter' => '#00abf0',
        'facebook' => '#3a579a',
        'pinterest' => '#cd1c1f',
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
    
    public static function add_new_network_color($network = '', $color = '') {
        self::$network_colors[$network] = $color;
    }
    
    /**
     * Return a list of all network colors after applying the filter for getting custom networks
     * @return array
     */
    public static function get_network_colors() {        
        $current_colors = self::$network_colors;
        
        if (has_filter('essb_share_button_styles_network_colors')) {
            $current_colors = apply_filters('essb_share_button_styles_network_colors', $current_colors);
        }
        
        return $current_colors;
    }
    
    public static function get_single_network_color($network = '') {
        $current_colors = self::get_network_colors();
        
        if (isset($current_colors[$network])) {
            return $current_colors[$network];
        }
        else {
            return '';
        }
    }
    
    public static function get_root_template_classes($template_id = '') {
        $classes = [];
        
        if (has_filter('essb_share_button_styles_root_classes')) {
            $classes = apply_filters('essb_share_button_styles_root_classes', $template_id, $classes);
        }
        
        if (count($classes) > 0) {
            return implode(' ', $classes);
        }
        else {
            return '';
        }
    }
    
    /**
     * Generate the share networks element classes based on the selected template
     * @param string $template_id
     * @param string $network
     * @return string
     */
    public static function get_network_element_classes($template_id = '', $network = '') {
        $classes = [];
        
        switch ($template_id) {
            case 6:
            case 7:
            case 9:
            case 10:
            case 19:
            case 22:
            case 23:
            case 24:
            case 25:
            case 26:
            case 28:
            case 29:
            case 31:
                $classes[] = self::$base_class_background . $network;
                $classes[] = self::$base_class_background_hover . $network;
                $classes[] = self::$base_class_light_color;
                $classes[] = self::$base_class_hover_effect;
                $classes[] = self::$base_class_network_background;
                $classes[] = self::$base_class_network_background_hover;
                break;
            case 32:
            case 38:
            case 49:
            case 50:
            case 52:
            case 54:
            case 59:
                $classes[] = self::$base_class_background . $network;
                $classes[] = self::$base_class_light_color;
                $classes[] = self::$base_class_light_color_hover;
                $classes[] = self::$base_class_dark_background_hover;
                $classes[] = self::$base_class_network_background;
                break;               
            case 8:
            case 46:
            case 47:
                $classes[] = self::$base_class_color . $network;
                $classes[] = self::$base_class_network_color;
                $classes[] = self::$base_class_dark_color_hover;
                break;
            case 11:
            case 20:
            case 51:
            case 53:
            case 57:
            case 58:
                $classes[] = self::$base_class_color . $network;
                $classes[] = self::$base_class_background_hover . $network;
                $classes[] = self::$base_class_light_color_hover;
                $classes[] = self::$base_class_network_color;
                $classes[] = self::$base_class_network_background_hover;
                break;
            case 12:
            case 13:
            case 14:
            case 27:
            case 48:
                $classes[] = self::$base_class_color . $network;
                $classes[] = self::$base_class_border_color . $network;
                $classes[] = self::$base_class_background_hover . $network;
                $classes[] = self::$base_class_light_color_hover;
                $classes[] = self::$base_class_border_color_hover . $network;
                
                $classes[] = self::$base_class_network_color;
                $classes[] = self::$base_class_network_border_color;
                $classes[] = self::$base_class_network_background_hover;
                $classes[] = self::$base_class_network_border_color_hover;
                break;
            case 15:
            case 16:
            case 17:
            case 39:
                $classes[] = self::$base_class_background_hover . $network;
                $classes[] = self::$base_class_light_color_hover;
                $classes[] = self::$base_class_network_background_hover;
                
                if ($template_id == 15) {
                    $classes[] = self::$base_class_light_color;
                }
                break;
            case 40:
            case 41:
                $classes[] = self::$base_class_color . $network;
                $classes[] = self::$base_class_border_color . $network;
                $classes[] = self::$base_class_dark_color_hover;
                $classes[] = self::$base_class_dark_border_color_hover;
                                
                $classes[] = self::$base_class_network_color;
                $classes[] = self::$base_class_network_border_color;
                break;
            case 42:
            case 43:
                $classes[] = self::$base_class_color_hover . $network;
                $classes[] = self::$base_class_border_color_hover . $network;
                
                $classes[] = self::$base_class_network_color_hover;
                $classes[] = self::$base_class_network_border_color_hover;
                break;
                
        }
        
        if (has_filter('essb_share_button_styles_network_element_classes')) {
            $classes = apply_filters('essb_share_button_styles_network_element_classes', $template_id, $network, $classes);
        }
        
        if (count($classes) > 0) {
            return implode(' ', $classes);
        }
        else {
            return '';
        }
    }
    
    public static function get_network_icon_classes($template_id = '', $network = '') {
        $classes = [];
        
        switch ($template_id) {
            case 18:
            case 33:
                $classes[] = self::$base_class_background . $network;
                $classes[] = self::$base_class_light_color;
                $classes[] = self::$base_class_light_color_hover;
                $classes[] = self::$base_class_dark_background_hover;
                $classes[] = self::$base_class_network_background;
            break;
        }
        
        if (has_filter('essb_share_button_styles_network_icon_classes')) {
            $classes = apply_filters('essb_share_button_styles_network_icon_classes', $template_id, $network, $classes);
        }
        
        if (count($classes) > 0) {
            return implode(' ', $classes);
        }
        else {
            return '';
        }
    }
    
    public static function generate_network_css_variable_name($network) {
        return self::$base_color_variable . $network;
    }
    
    public static function generate_network_css_classes_for_variable_code($network = '') {
        $output = '';
        
        $network_color = self::get_single_network_color($network);
        
        if (!empty($network_color)) {
            $output .= '.essb_links { ' . self::generate_network_css_variable_name($network) . ':' . $network_color . '; }';
            
            $output .= '.essb_links .'.self::$base_class_background . $network . ' { background-color: var(' . self::generate_network_css_variable_name($network) . ')  !important; }';
            $output .= '.essb_links .'.self::$base_class_background_hover . $network . ':hover { background-color: var(' . self::generate_network_css_variable_name($network) . ')  !important; }';
            
            $output .= '.essb_links .'.self::$base_class_color . $network . ' { color: var(' . self::generate_network_css_variable_name($network) . ') !important; fill: var(' . self::generate_network_css_variable_name($network) . '); }';
            $output .= '.essb_links .'.self::$base_class_color_hover . $network . ':hover { color: var(' . self::generate_network_css_variable_name($network) . ') !important; fill: var(' . self::generate_network_css_variable_name($network) . '); }';
            
            $output .= '.essb_links .'.self::$base_class_border_color . $network . ' { border-color: var(' . self::generate_network_css_variable_name($network) . '); }';
            $output .= '.essb_links .'.self::$base_class_border_color_hover . $network . ':hover { border-color: var(' . self::generate_network_css_variable_name($network) . ') !important; }';
        }
        
        return $output;
    }
    
    public static function generate_network_css_classes_code($network = '') {
        $output = '';
        
        $network_color = self::get_single_network_color($network);
        
        if (!empty($network_color)) {

            $output .= '.essb_links .essb_link_'.$network.' { --essb-network:' . $network_color . '; }';
            
        }
        
        return $output;
    }
    
    public static function reginster_all_networks_css_classes_code() {
        $code = '';
        foreach (self::$network_colors as $network => $color) {
            $code .= self::generate_network_css_classes_code($network);
        }
        
        if (class_exists('ESSB_Dynamic_CSS_Builder')) {
            ESSB_Dynamic_CSS_Builder::register_dynamic_code('sharing-networks', $code);
        }
    }
    
    public static function register_network_css_classes_code($network = '') {
        $code = self::generate_network_css_classes_code($network);        
        
        if (!empty($code) && class_exists('ESSB_Dynamic_CSS_Builder')) {
            self::register_dynamic_template_styles();
            ESSB_Dynamic_CSS_Builder::register_dynamic_code('sharing-network-' . $network, $code);
        }
    }
    
    public static function generate_dynamic_template_styles() {
        $code = '';

        $code .= '.essb_links .essb-s-c-light, .essb_links .essb-s-ch-light:hover { color: #fff !important; fill: #fff; }';
        $code .= '.essb_links .essb-s-c-dark, .essb_links .essb-s-ch-dark:hover { color: #212121 !important; fill: #212121; } ';
        $code .= '.essb_links .essb-s-bgh-dark { background-color: #212121 !important; }';
        $code .= '.essb_links .essb-s-bh-dark:hover { border-color: #212121; }';
        $code .= '.essb_links .essb_link_svg_icon .essb_icon { display: inline-flex; align-items: center; justify-content: center; }';
        $code .= '.essb_links .essb_link_svg_icon svg { height: 18px; width: auto; fill: currentColor; }';
        $code .= '.essb_links.essb_size_xs .essb_link_svg_icon svg { height: 14px; width: auto; }';
        $code .= '.essb_links.essb_size_s .essb_link_svg_icon svg { height: 16px; width: auto; }';
        $code .= '.essb_links.essb_size_m .essb_link_svg_icon svg { height: 18px; width: auto; }';
        $code .= '.essb_links.essb_size_l .essb_link_svg_icon svg { height: 20px; width: auto; }';
        $code .= '.essb_links.essb_size_xl .essb_link_svg_icon svg { height: 24px; width: auto; }';
        $code .= '.essb_links.essb_size_xxl .essb_link_svg_icon svg { height: 28px; width: auto; }';

        $code .= '.essb_links .'.self::$base_class_network_background  . ' { background-color: var(--essb-network)  !important; }';
        $code .= '.essb_links .'.self::$base_class_network_background_hover  . ':hover { background-color: var(--essb-network)  !important; }';
        
        $code .= '.essb_links .'.self::$base_class_network_color . ' { color: var(--essb-network) !important; fill: var(--essb-network); }';
        $code .= '.essb_links .'.self::$base_class_network_color_hover . ':hover { color: var(--essb-network) !important; fill: var(--essb-network); }';
        
        $code .= '.essb_links .'.self::$base_class_network_border_color . ' { border-color: var(--essb-network); }';
        $code .= '.essb_links .'.self::$base_class_network_border_color_hover . ':hover { border-color: var(--essb-network) !important; }';
        
        return $code;
    }
    
    public static function register_dynamic_template_styles() {
        if (!self::$dynamic_styles_loaded && class_exists('ESSB_Dynamic_CSS_Builder')) {
            self::$dynamic_styles_loaded = true;
            ESSB_Dynamic_CSS_Builder::register_dynamic_code('sharing-dynamic-template-styles', self::generate_dynamic_template_styles());
        }
    }

}