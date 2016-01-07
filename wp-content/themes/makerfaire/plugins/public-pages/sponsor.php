<?php
/**
 * Sponsor Functions
 */
function mf_sponsor_carousel( $category_name, $slug='' ) {
    //get the list of links based on faire name
    $sponsors = get_bookmarks( array( 'orderby' => 'name', 'category_name' => $category_name, 'limit' => 40 ) );    
    $slugData = get_term_by( 'slug', $slug, 'link_category', OBJECT );
    if(is_object($slugData)){        
        $slugCat = get_objects_in_term($slugData->term_id,'link_category');
    }else{ 
        $slugCat = array();
    }    
   
    //get the list of links based on sponsor category name
    $slugData    = get_term_by( 'slug', $category_name, 'link_category', OBJECT );
    $sponsorName = get_objects_in_term($slugData->term_id,'link_category');        
    $category = array(); 
    //find the links that are in both the sponsor category and specified faire
    if(!empty($slugCat)){
        $category = array_intersect($slugCat,$sponsorName);
    }else{
        return false; //no sponsors found
    }
    
    $include = implode(',',$category); 
    $sponsors = array();
    if(!empty($include)){
        $sponsors = get_bookmarks( array( 'orderby' => 'name',  'limit' => 40, 'include'=> $include) );
    }

	// Split them into chucks of two
	$sponsors = array_chunk( $sponsors, 2, true );

	// Get the output started.
	$output = '';

	// Loop through each block of sponsors
	foreach ($sponsors as $idx => $sponsor) {
		if ( $idx == 0 ) {
			$output .= '<div class="item active">';
		} else {
			$output .= '<div class="item">';
		}
		$output .= '<div class="row">';

		// Loop through the individual sponsors
		foreach ($sponsor as $spon) {
			$output .= '<div class="col-md-6"><div class="thumb"><a href="' . esc_url( $spon->link_url ) . '"><img src="' . legacy_get_resized_remote_image_url( $spon->link_image, 125, 105 ) . '" alt="' . esc_attr( $spon->link_name ) . '"></a></div></div>';
		}
		$output .= '</div></div>';
	}

	return $output;
}

function mf_sponsor_list( $category_name, $slug='' ) {       
    //get the list of links based on faire name
    $sponsors = get_bookmarks( array( 'orderby' => 'name', 'category_name' => $category_name, 'limit' => 40 ) );    
    $slugData = get_term_by( 'slug', $slug, 'link_category', OBJECT );
    if(is_object($slugData)){        
        $slugCat = get_objects_in_term($slugData->term_id,'link_category');
    }else{ 
        $slugCat = array();
    }    
    
    //get the list of links based on sponsor category name
    $slugData    = get_term_by( 'slug', $category_name, 'link_category', OBJECT );
    $sponsorName = get_objects_in_term($slugData->term_id,'link_category');        
    
    //find the links that are in both the sponsor category and specified faire
    if(!empty($slugCat)){
        $category = array_intersect($slugCat,$sponsorName);
    }else{
        $category = $sponsorName;
    }
    
    $include = implode(',',$category); 
    $sponsors = array();
    if(!empty($include)){
        $sponsors = get_bookmarks( array( 'orderby' => 'name',  'limit' => 40, 'include'=> $include) );
    }
    	
    // Get the output started.
    $output = '<ul>';

    // Loop through each block of sponsors
    foreach ($sponsors as $idx => $spon) {
        //foreach ($sponsor as $spon) {
        $output .= '<li><a href="' . esc_url( $spon->link_url ) . '"><img src="' . legacy_get_resized_remote_image_url( $spon->link_image, 125, 105 ) . '" alt=""></a></li>';
        //}
    }

    $output .= '</ul>';
    return $output;
}
