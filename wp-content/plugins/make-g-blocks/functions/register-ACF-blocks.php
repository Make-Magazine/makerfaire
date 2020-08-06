<?php

// Create new categories for our blocks

function make_panels($categories, $post) {
    return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'make-panels',
                    'title' => __('Make: Panels', 'make-panels'),
                ),
            )
    );
}

add_filter('block_categories', 'make_panels', 10, 2);

add_action('acf/init', 'make_add_acf_blocks');

function make_add_acf_blocks() {
    
    // check function exists
    if (function_exists('acf_register_block_type')) {
        //These have to manually be placed in alphabetical order for them to show up in that order in admin
        
        // Image Panels in the same style as the Video Panels
        acf_register_block_type(array(
            'name' => 'two_column_image',
            'title' => __('2 column Image and text Panel'),
            'render_callback' => 'call_ACF_block_panels',            
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'panel'),
        ));     
        
        //2 column video and text panel
        acf_register_block_type(array(
            'name' => 'two_column_video',
            'title' => __('2 Column Video Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('video', 'panel'),
        ));   
        
        acf_register_block_type(array(
            'name' => 'three_column',
            'title' => __('3 column'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('columns', 'dynamic', 'panel'),
        ));
	acf_register_block_type(array(
            'name' => 'six_column',
            'title' => __('6 column navigation panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('columns', 'dynamic', 'panel'),
        ));
       acf_register_block_type(array(
            'name' => 'call_to_action_panel',
            'title' => __('Call to Action'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('call', 'action', 'panel'),
        ));
       
        //Featured section 
        acf_register_block_type(array(
            'name' => 'featured_makers_panel',
            'title' => __('Featured Makers (Square images)'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'square', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'featured_makers_panel_dynamic',
            'title' => __('Featured Makers (Square images) - Dynamic panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'square', 'dynamic', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'featured_makers_panel_circle',
            'title' => __('Featured Makers (Circle images)'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'circle', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'featured_makers_panel_circle_dynamic',
            'title' => __('Featured Makers (Circle images) - Dynamic Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'circle', 'dynamic', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'featured_events',
            'title' => __('Featured Events'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'events', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'featured_events_dynamic',
            'title' => __('Featured Events - Dynamic Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'events', 'dynamic', 'panel'),
        ));
        //featured faires panel
        acf_register_block_type(array(
            'name' => 'featured_faires_panel',
            'title' => __('Featured Faires - Dynamic'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'faires', 'panel'),
        ));
        //Flag Banner Separator Panel
        acf_register_block_type(array(
            'name' => 'flag_banner_panel',
            'title' => __('Flag Banner Separator Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('flag', 'banner', 'separator','panel'),
        ));
        //Get tickets
        acf_register_block_type(array(
            'name' => 'buy_tickets_float',
            'title' => __('Get Tickets Floating Banner'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('buy', 'tickets', 'panel'),
            'example' => [
		'attributes' => [
			'mode' => 'preview',
			'data' => ['is_example' => true],
		]
            ]
        ));
        //Hero Panel
        acf_register_block_type(array(
            'name' => 'one_column',
            'title' => __('Hero Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'formatting',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('column', 'hero', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'home_page_image_carousel',
            'title' => __('Home Page Image Carousel'),
            'render_callback' => 'home_page_image_carousel',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'carousel'),
        ));        
        //Image Carousels
        acf_register_block_type(array(
            'name' => 'static_or_carousel',
            'title' => __('Image Carousel (Rectangle)'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'carousel', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'square_image_carousel',
            'title' => __('Image Carousel (Square)'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'carousel', 'panel', 'square'),
        ));
	// Dynamic carousel with multiple columns
        acf_register_block_type(array(
            'name' => 'image_slider',
            'title' => __('Image Slider'),
            'render_callback' => 'call_ACF_block_panels',            
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'panel'),
        ));    
        // Makey Banner
        acf_register_block_type(array(
            'name' => 'makey_banner',
            'title' => __('Makey Banner'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('makey', 'banner','panel'),
        ));    
        // News Block Panel
        acf_register_block_type(array(
            'name' => 'news_block_panel',
            'title' => __('News Block Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('makey', 'banner','panel'),
        ));            
        acf_register_block_type(array(
            'name' => 'post_feed',
            'title' => __('News / Post Feed'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('post', 'panel'),
        ));        
        //Ribbon Separator Panel
        acf_register_block_type(array(
            'name' => 'ribbon_separator_panel',
            'title' => __('Ribbon Separator Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('newsletter', 'panel'),
        ));        
        acf_register_block_type(array(
            'name' => 'social_media',
            'title' => __('Social Media'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('social', 'media', 'panel'),
        ));
        acf_register_block_type(array(
            'name' => 'sponsors_panel',
            'title' => __('Sponsors'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('sponsors', 'panel'),
        ));        
        acf_register_block_type(array(
            'name' => 'what_is_maker_faire',
            'title' => __('What is Maker Faire'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('maker', 'faire', 'panel'),
        ));


        //end               
    }
}

function call_ACF_block_panels($block) {
    GLOBAL $acf_blocks;
    $acf_blocks = TRUE;
    $name = str_replace("acf/", "", $block['name']);
    $name = str_replace("-", "_", $name);
    if (get_field('is_example')){
	/* Render screenshot for example */
        echo 'this is it'.plugin_dir_path().'/examples/buy_tickets.png';
        //echo '<img src="'.plugin_dir_path().'/examples/buy_tickets.png" />';
    }
    echo ($name != '' ? dispLayout($name) : '');
}
