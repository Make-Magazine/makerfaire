<?php

// Create new categories for our blocks
function make_panels($categories, $post) {
    return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'make-panels',
                    'title' => __('Make Panels', 'make-panels'),
                ),
            )
    );
}
add_filter('block_categories', 'make_panels', 10, 2);

add_action('acf/init', 'make_add_acf_blocks');

function make_add_acf_blocks() {    
    // check function exists
    if (function_exists('acf_register_block')) {        
        //Hero Panel
        acf_register_block(array(
            'name' => '1_column',
            'title' => __('Hero Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'formatting',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('column', 'hero', 'panel'),
        ));
        
        //2 column video and text panel
        acf_register_block(array(
            'name' => '2_column_video',
            'title' => __('2 Column Video Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('video', 'panel'),
        ));
        
        // Image Panels in the same style as the Video Panels
        acf_register_block(array(
            'name' => '2_column_images',
            'title' => __('2 Column Image and Text Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'panel'),
        ));
        
        //3 column
        acf_register_block(array(
            'name' => '3_column',
            'title' => __('3 Column Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('columns', 'dynamic', 'panel'),
        ));
        
        //6 column navigation panel
        acf_register_block(array(
            'name' => '6_column',
            'title' => __('6 Column Navigation Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('columns', 'navigation', 'panel'),
        ));
        
        //Get Tickets Floating Banner
        acf_register_block(array(
            'name' => 'buy_tickets_float',
            'title' => __('Get Tickets Floating Banner'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('buy', 'tickets', 'panel'),
        ));

        //CTA Panel
        acf_register_block(array(
            'name' => 'call_to_action',
            'title' => __('Call to Action Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('call', 'action', 'panel'),
        ));
        
        //featured faires panel
        acf_register_block(array(
            'name' => 'featured_faires_panel',
            'title' => __('Featured Faires - Dynamic'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'faires', 'panel'),
        ));
        
        //featured makers panel
        acf_register_block(array(
            'name' => 'featured_makers_panel',
            'title' => __('Featured Items'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'panel'),
        ));
        
        //featured makers - dynamic
        acf_register_block(array(
            'name' => 'featured_makers_panel_dynamic',
            'title' => __('Featured Makers - Dynamic'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'dynamic', 'panel'),
        ));
        
        //Flag Banner Separator Panel
        acf_register_block(array(
            'name' => 'flag_banner_panel',
            'title' => __('Flag Banner Separator Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('flag', 'banner', 'separator','panel'),
        ));

        // Image Slider
        acf_register_block(array(
            'name' => 'image_slider',
            'title' => __('Image Slider'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'panel'),
        ));        
        
        // Makey Banner
        acf_register_block(array(
            'name' => 'makey_banner',
            'title' => __('Makey Banner'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('makey', 'banner','panel'),
        ));        
        
        // News Block Panel
        acf_register_block(array(
            'name' => 'news_block_panel',
            'title' => __('News Block Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('makey', 'banner','panel'),
        ));        
        
        //Newsletter Panel
        acf_register_block(array(
            'name' => 'newsletter_panel',
            'title' => __('Newsletter Sign Up'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('newsletter', 'panel'),
        ));

        //Ribbon Separator Panel
        acf_register_block(array(
            'name' => 'ribbon_separator_panel',
            'title' => __('Ribbon Separator Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('newsletter', 'panel'),
        ));
        
        //Sponsors Panel
        acf_register_block(array(
            'name' => 'sponsors_panel',
            'title' => __('Sponsors'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('sponsors', 'panel'),
        ));

        //Tint Social Block Panel
        acf_register_block(array(
            'name' => 'tint_social_block_panel',
            'title' => __('Tint Social Block Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('sponsors', 'panel'),
        ));        
    }
}

function call_ACF_block_panels($block) {    
    GLOBAL $acf_blocks;
    $acf_blocks = TRUE;
    $name = str_replace("acf/", "", $block['name']);
    $name = str_replace("-", "_", $name);
    //echo ($name != '' ? dispLayout($name) : '');
    echo 'helllllooo layout is '.$name;
}
