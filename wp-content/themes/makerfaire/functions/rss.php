<?php
//randomly return projects and only return those that have a good description
function projects_rss_random_sort( $query ) {
	// We do not want unintended consequences.
	if ( ! $query->is_feed() ) {
		return;    
	}

    //randomly return projects and only return 1
    if( isset( $query->query['post_type'] ) ) {
        if('projects' === $query->query['post_type']){
            $query->set('posts_per_page', 3); //only return 5 projects
            $query->set('orderby', 'rand'); //return projects randomly        
            
            //only return projects where project description is set                
            $exhibit_query = array(             
                    'relation'      => 'AND',
                    array(
                        'key'       => 'exhibit_description',                    
                        'compare'   => 'EXISTS',
                    ),
                    array(
                        'key'       => 'exhibit_description',
                        'value'     => '',
                        'compare'   => '!=',
                    ),
            );            
        $query->set('meta_query', $exhibit_query);
        }
    }
    
    return $query;
}
add_filter( 'pre_get_posts', 'projects_rss_random_sort',10 );

//add the featured image to the RSS feed
function featuredtoRSS($content) {
    global $post;
    $post_type = get_post_type($post->ID);
    
    if ($post_type == "projects") {
        if (has_post_thumbnail($post->ID)) {
            $content  = '<div>' . get_the_post_thumbnail($post->ID, 'thumbnail', array('style' => 'margin-bottom: 15px;')) . '</div>';                     
            
            //return the first 500 characeters of the exhibit description
            $content .= substr(html_entity_decode(get_field("exhibit_description", $post->ID), ENT_QUOTES, get_bloginfo("charset")),0,500);                        
            
        }        
    }    
    
    return $content;
}
add_filter('the_excerpt_rss', 'featuredtoRSS', 20, 1);
add_filter('the_content_feed', 'featuredtoRSS', 20, 1);

function modify_rss_feed() { 
    add_action('rss2_item', 'add_rss_tags'); 
} 
add_action('init', 'modify_rss_feed'); 
 
function add_rss_tags() { 
    global $post; 
    //add faire name initalics
    $faireData  = get_field("faire_information", $post->ID);                        
    if(isset($faireData["faire_post"]) && $faireData["faire_post"]!='') {
        echo '<faire_name>' . get_the_title($faireData["faire_post"]).'</faire_name>';
    }    
} 
/* Custom RSS feed */
/*
add_action('init', 'mfRSS');
function mfRSS(){
    add_feed('faire_projects', 'mfRSSProjects');
    add_feed('faire_ribbons', 'mfRSSRibbons');
}

//custom RSS feed to return MF entries for a specific faire
function mfRSSProjects(){
    get_template_part('feed_faire_projects', 'faire_projects');    
}

//custom RSS feed to return data from the faire ribbons page
function mfRSSRibbons(){
    get_template_part('feed_faire_ribbons', 'faire_ribbons');    
}*/

add_action( 'init', function () {
    add_rewrite_rule(
        '^feed/(faire_projects|faire_ribbons|yb_projects)/?$',
        'index.php?feed=$matches[1]',
        'top'
    );
} );

add_action( 'do_feed_faire_ribbons', function () {    
    get_template_part('feed_faire_ribbons', 'faire_ribbons');    
} );

add_action( 'do_feed_faire_projects', function () {    
    get_template_part('feed_faire_projects', 'faire_projects');    
} );

add_action( 'do_feed_yb_projects', function () {    
    get_template_part('feed_yb_projects', 'yb_projects');    
} );