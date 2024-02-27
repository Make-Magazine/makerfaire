<?php
//randomly return projects and only return those that have a good description
function projects_rss_random_sort( $query ) {
	// We do not want unintended consequences.
	if ( ! $query->is_feed() ) {
		return;    
	}

    //randomly return projects and only return 1
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
            //return the first 100 characeters of the exhibit description
            $content .= substr(html_entity_decode(get_field("exhibit_description", $post->ID), ENT_QUOTES, get_bloginfo("")),0,100);
            
            //add faire name initalics
            $faireData  = get_field("faire_information");            
            $content .= (isset($faireData["faire_post"]) ? '&lt;br/&rt;&lt;i&rt;' . get_the_title($faireData["faire_post"]).'&lt;/i&rt;':'');
        }        
    }    
    
    return $content;
}
add_filter('the_excerpt_rss', 'featuredtoRSS', 20, 1);
add_filter('the_content_feed', 'featuredtoRSS', 20, 1);