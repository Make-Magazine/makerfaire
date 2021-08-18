<?php

/**
 * Get and cache post featured image for sharing
 * 
 * @param unknown $post_id
 * @return string
 */
function essb_core_get_post_featured_image ($post_id) {
    $cache_key = 'featured-image-' . $post_id;
    $post_cached_image = ESSB_Runtime_Cache::get($cache_key);
    
    if ($post_cached_image == '') {
        $post_cached_image = get_post_meta($post_id, 'essb_cached_image', true);
        
        if (empty($post_cached_image)) {
            $post_image = has_post_thumbnail($post_id) ? wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full') : '';
            $post_cached_image = ($post_image != '') ? $post_image[0] : '';
            
            if (! empty($post_cached_image)) {
                update_post_meta($post_id, 'essb_cached_image', $post_cached_image);
            }
        }
        
        ESSB_Runtime_Cache::set($cache_key, $post_cached_image);
    }
    
    return $post_cached_image;
}

/**
 * Replace unicode quotes to prevent sharing problems
 *
 * @param unknown $content            
 * @return mixed
 */
function essb_core_convert_smart_quotes ($content) {
    $content = str_replace('"', '\'', $content);
    $content = str_replace('&#8220;', '\'', $content);
    $content = str_replace('&#8221;', '\'', $content);
    $content = str_replace('&#8216;', '\'', $content);
    $content = str_replace('&#8217;', '\'', $content);
    
    return $content;
}

/**
 * Generate post excerpt
 *
 * @param unknown $post_id            
 * @return mixed
 */
function essb_core_get_post_excerpt ($post_id) {
    // Check if the post has an excerpt
    if (has_excerpt($post_id)) {
        $the_post = get_post($post_id); // Gets post ID
        $the_excerpt = $the_post->post_excerpt;
    }
    else {
        $the_post = get_post($post_id); // Gets post ID
        $the_excerpt = $the_post->post_content; // Gets post_content to be used as a basis for the excerpt
    }
    
    $excerpt_length = 100;
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
    
    $the_excerpt = str_replace(']]>', ']]&gt;', $the_excerpt);
    $the_excerpt = strip_tags($the_excerpt);
    $excerpt_length = apply_filters('excerpt_length', 100);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    $words = preg_split("/[\n\r\t ]+/", $the_excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
    
    if (count($words) > $excerpt_length) {
        array_pop($words);
        $the_excerpt = implode(' ', $words);
    }
    
    $the_excerpt = preg_replace("/\r|\n/", '', $the_excerpt);
    
    return $the_excerpt;
}

/**
 * Generate current post tags as a string with commans for Twitter hashtags
 *
 * @param unknown $post            
 * @return string
 */
function essb_get_post_tags_as_list ($post) {
    $twitter_hashtags = '';
    
    $post_tags = wp_get_post_tags($post->ID);
    if ($post_tags) {
        $generated_tags = array ();
        foreach ($post_tags as $tag) {
            $current_tag = $tag->name;
            $current_tag = str_replace(' ', '', $current_tag);
            $generated_tags[] = $current_tag;
        }
        
        if (count($generated_tags) > 0) {
            $twitter_hashtags = implode(',', $generated_tags);
        }
    }
    
    return $twitter_hashtags;
}

/**
 * Generate sharing information object
 *
 * @param string $position            
 */
function essb_get_post_share_details ($position = '') {
    global $post;
    
    $r = array ();
    $static_positions = array ( 
        'top', 'bottom', 'float', 'followme', 'shortcode', 'widget' 
    );
    
    if (essb_option_bool_value('reset_postdata')) {
        wp_reset_postdata();
    }
    
    if (essb_option_bool_value('force_wp_query_postid')) {
        $current_query_id = get_queried_object_id();
        $post = get_post($current_query_id);
    }
    
    $list_of_articles_mode = false;
    if (essb_option_bool_value('force_archive_pages') && (is_archive() || is_front_page() || is_search() || is_tag() || is_post_type_archive()) && ! in_array($position, $static_positions)) {
        $list_of_articles_mode = true;
    }
    
    // Static display methods on archive pages
    if (essb_option_bool_value('force_archive_pages_content') && (is_archive() || is_front_page() || is_search() || is_tag() || is_post_type_archive()) && in_array($position, $static_positions)) {
        $list_of_articles_mode = true;
    }
    
    // Focing the list of articles mode from the cache (parameters)
    if (ESSB_Runtime_Cache::is('force-archive-'.$position)) {
        $list_of_articles_mode = true;
    }
    
    // Generate single post sharing information
    if (isset($post) && !$list_of_articles_mode) {
        $post_data = ESSB_Runtime_Cache::get_post_sharing_data($post->ID);
        $r = $post_data->compile_share_object();        
    }
    else {
        // Generate global share information or archive share information
        $type = ESSB_Site_Share_Information::type();
        
        $r = ESSB_Site_Share_Information::compile_share_object(ESSB_Site_Share_Information::get_title_by_type($type),
            ESSB_Site_Share_Information::get_description_by_type($type),
            ESSB_Site_Share_Information::get_image_by_type($type),
            ESSB_Site_Share_Information::get_url_by_type($type));
    }
    
    $r['list_of_articles_mode'] = $list_of_articles_mode;
    
    /**
     * @since 7.7.5 Additional filter for reading social share optimization (to get send via external plugins)
     */
    if (has_filter('essb_get_post_share_details')) {
        $r = apply_filters('essb_get_post_share_details', $r);
    }
    
    return $r;
}