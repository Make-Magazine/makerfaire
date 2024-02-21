<?php
//add the featured image to the RSS feed
function featuredtoRSS($content) {
    global $post;
    $post_type = get_post_type($post->ID);
    
    if ($post_type == "projects") {
        if (has_post_thumbnail($post->ID)) {
            $content  = '<div>' . get_the_post_thumbnail($post->ID, 'medium', array('style' => 'margin-bottom: 15px;')) . '</div>' . $content;
            //return the first 50 characeters of the exhibit description
            $content .= substr(html_entity_decode(get_field("exhibit_description", $post->ID), ENT_QUOTES, get_bloginfo("")),0,50);
        }
    }    
    
    return $content;
}
add_filter('the_excerpt_rss', 'featuredtoRSS', 20, 1);
add_filter('the_content_feed', 'featuredtoRSS', 20, 1);