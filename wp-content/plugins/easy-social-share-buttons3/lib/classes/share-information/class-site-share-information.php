<?php

/**
 * Detect current running WordPress conditionals
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 7.1
 */
class ESSB_Site_Share_Information {

    /**
     * Return type of current content
     *
     * @return string
     */
    public static function type () {
        $r = 'other';
        
        if (is_front_page()) {
            $r = 'front';
        }
        else if (is_search()) {
            $r = 'search';
        }
        else if (is_category()) {
            $r = 'category';
        }
        else if (is_tag()) {
            $r = 'tag';
        }
        else if (is_tax()) {
            $r = 'tax';
        }
        else if (is_author()) {
            $r = 'author';
        }
        else if (is_single() || is_page()) {
            $r = 'single';
        }
        
        /**
         * @since 7.7.5
         */
        if (has_filter('essb_site_share_information_type')) {
            $r = apply_filters('essb_site_share_information_type', $r);
        }
        
        return $r;
    }

    /**
     * @param string $type
     * @return string
     */
    public static function get_title_by_type ($type = '') {
        $title = '';
        
        if ($type == 'front') {
            $title = essb_option_value('sso_frontpage_title');
            
            if (empty($title)) {
                $title = get_bloginfo('name');
            }
        }
        else if ($type == 'search') {
            $title = sprintf(esc_html__('Search for "%s"', 'essb'), esc_html(get_search_query()));
        }
        else if ($type == 'category') {
            $title = single_cat_title('', false);
        }
        else if ($type == 'tag') {
            $title = single_tag_title('', false);
        }
        else if ($type == 'tax') {
            $title = single_term_title('', false);
            if ($title === '') {
                $term = $GLOBALS['wp_query']->get_queried_object();
                $title = $term->name;
            }
        }
        else if ($type == 'author') {
            $title = get_the_author_meta('display_name', get_query_var('author'));
        }
        else if ($type != 'single') {
            $title = get_bloginfo('name');
        }
        
        /**
         * @since 7.7.5
         */
        if (has_filter('essb_site_share_information_title')) {
            $title = apply_filters('essb_site_share_information_title', $title, $type);
        }
        
        return $title;
    }

    /**
     * @param string $type
     * @return string
     */
    public static function get_description_by_type ($type = '') {
        $description = '';
        
        if ($type == 'front') {
            $description = essb_option_value('sso_frontpage_description');
            
            if (empty($description)) {
                $description = get_bloginfo('description');
            }
        }
        else if ($type == 'category' || $type == 'tag' || $type == 'tax') {
            $description = strip_tags(term_description());
            
            $custom = self::get_term_custom_data('description');
            if ($custom != '') {
                $description = $custom;
            }
        }
        else if ($type == 'author') {
            $description = get_the_author_meta('description', get_query_var('author'));
        }
        else if ($type != 'single') {
            $description = get_bloginfo('description');
        }
        
        /**
         * @since 7.7.5
         */
        if (has_filter('essb_site_share_information_title')) {
            $description = apply_filters('essb_site_share_information_description', $description, $type);
        }
        
        return $description;
    }

    /**
     * @param string $type
     */
    public static function get_image_by_type ($type = '') {
        $image = '';
        
        if ($type != 'single') {
            $image = essb_option_value('sso_frontpage_image');
            
            if ($type == 'category' || $type == 'tag' || $type == 'tax') {
                $custom = self::get_term_custom_data('image');
                if ($custom != '') {
                    $image = $custom;
                }
            }
        }
        
        /**
         * @since 7.7.5
         */
        if (has_filter('essb_site_share_information_image')) {
            $image = apply_filters('essb_site_share_information_image', $image, $type);
        }
        
        return $image;
    }

    /**
     * @param string $type
     * @return string
     */
    public static function get_url_by_type ($type = '') {
        $url = '';
        
        if ($type == 'front') {
            $url = get_bloginfo('url');
        }
        else if ($type == 'search') {
            $search_query = get_search_query();
            
            // Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
            if (! empty($search_query) && ! preg_match('|^page/\d+$|', $search_query)) {
                $url = get_search_link();
            }
        }
        else if ($type == 'category' || $type == 'tag' || $type == 'tax') {
            
            $term = get_queried_object();
            
            if (! empty($term)) {
                
                $term_link = get_term_link($term, $term->taxonomy);
                
                if (! is_wp_error($term_link)) {
                    $url = $term_link;
                }
                else {
                    $url = get_bloginfo('url');
                }
            }
        }
        else if ($type == 'author') {
            $url = get_author_posts_url(get_query_var('author'), get_query_var('author_name'));
        }
        else if ($type == 'home') {
            $url = get_permalink(get_option('page_for_posts'));
        }
        else if ($type != 'single') {
            $url = get_bloginfo('url');
        }
        
        /**
         * @since 7.7.5
         */
        if (has_filter('essb_site_share_information_url')) {
            $url = apply_filters('essb_site_share_information_url', $url, $type);
        }
        
        return $url;
    }
    
    public static function get_term_custom_data($data = 'title') {
        $term = get_queried_object();
        $r = '';
        $field = 'sso_title';
        
        if ($data == 'description') {
            $field = 'sso_desc';
        }
        else if ($data == 'image') {
            $field = 'sso_image';
        }
        
        if ( ! empty( $term ) ) {
            $r = htmlspecialchars(stripcslashes(get_term_meta($term->term_id, $field, true)));
        }
        
        return $r;
    }
    
    /**
     * @param string $url
     * @return string|unknown
     */
    public static function attach_affiliate_to_url($url = '') {
        // Affiliate links if needed
        if (essb_option_bool_value('affwp_active')) {
            essb_helper_maybe_load_feature('integration-affiliatewp');
            $url = essb_generate_affiliatewp_referral_link($url);
        }
        
        if (essb_option_bool_value('affs_active')) {
            $url = do_shortcode('[affiliates_url]'.$url.'[/affiliates_url]');
        }
        
        if (essb_option_bool_value('mycred_referral_activate') && function_exists('mycred_render_affiliate_link')) {
            $url = mycred_render_affiliate_link( array( 'url' => $url ) );
        }
        
        return $url;
    }
    
    public static function compile_share_object($title = '', $description = '', $image = '', $url = '') {
        
        $url = self::attach_affiliate_to_url($url);
        
        return array (
            'url' => $url,
            'title' => self::prepare_text_value($title),
            'image' => $image,
            'description' => $description,
            'twitter_user' => essb_sanitize_option_value('twitteruser'),
            'twitter_hashtags' => essb_sanitize_option_value('twitterhashtags'),
            'twitter_tweet' => self::prepare_text_value($title),
            'post_id' => get_the_ID(),
            'user_image_url' => '',
            'title_plain' => self::prepare_text_value($title),
            'short_url_whatsapp' => '',
            'short_url_twitter' => '',
            'short_url' => '',
            'pinterest_image' => '',
            'pinterest_desc' => '',
            'pinterest_id' => ''
        );
    }
    
    /**
     * @param string $value
     * @return string
     */
    public static function prepare_text_value($value = '') {
        // stripslashes/wp_strip_all_tags
        $value = str_replace('&nbsp;', '', self::remove_shortcodes_keep_content($value));
        return trim(strip_shortcodes(addslashes($value)));
    }
    
    /**
     * Remove shortcodes but keep contents inside
     *
     * @param string $value
     * @return mixed
     */
    public static function remove_shortcodes_keep_content($value = '') {
        return preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $value);
    }
}