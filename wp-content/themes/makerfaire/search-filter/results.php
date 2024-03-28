<?php
/**
 * Search & Filter Pro Results template
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $query->have_posts() ) {
    
    ?>

    <div class="results-info">
        <span>Found <?php echo $query->found_posts; ?> Results</span>
    </div>

    <div class="pagination">

        <div class="nav-previous"><?php next_posts_link( 'Older posts', $query->max_num_pages ); ?></div>
        <div class="nav-count">Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?></div>
        <div class="nav-next"><?php previous_posts_link( 'Newer posts' ); ?></div>

    </div>

    <div class="result-items">
    <?php

        while ($query->have_posts()) {         
            $query->the_post();   
            $faire_id = $post_id = get_the_ID();
            $postType = get_post_type($query->post_type);
            
            $result_text_style = "";
            if($postType == "projects") {
                $faire_info = get_field("faire_information");
                $faire_id   = (isset($faire_info['faire_post'])?$faire_info['faire_post']:'');
                $faire_name = get_the_title($faire_id);             
                $faire_year = (isset($faire_info["faire_year"])?$faire_info["faire_year"]:'');   
            }

            //get the faire name
            $title = get_the_title();


            //find producer information
            $producerSection = get_field('producer_section', $faire_id);

            //Faire Badge                
            $faire_badge       = (isset($producerSection['circular_faire_logo']['url']) ? $producerSection['circular_faire_logo']["sizes"]["thumbnail"]: "/wp-content/themes/makerfaire/images/default-badge-thumb.png");                    
            $result_text_style = 'style="background-image:url(' . $faire_badge . ');"';

            //featured image            
            $image_alt      = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );   
            
            $thumbnail_url  = get_the_post_thumbnail_url();         
            $featured_image_400_300 = legacy_get_resized_remote_image_url($thumbnail_url, 400, 300);
            $featured_image_600_400 = legacy_get_resized_remote_image_url($thumbnail_url, 600, 400);

            //permalink
            $permalink = get_permalink($post_id);
            
            if($postType == "event" || $postType == "faire") {                                                 
                //set faire year
                $event_id = get_post_meta($faire_id, '_event_id');                
                $EM_Event = new EM_Event( $event_id[0] );
                $faire_year = date('Y', strtotime($EM_Event->event_start_date));
                $faire_date = date("F Y", strtotime($EM_Event->event_start_date));

                //set faire location information
                $EM_Location = $EM_Event->get_location();
                $faire_city  = $EM_Location->location_town;
                $faire_state = $EM_Location->location_state;                                
                               
                //populate faire location with city and state
                $faire_location  = ''; 
                $faire_location .= (!empty($faire_city) ? $faire_city : "");                
                $faire_location .= (!empty($faire_city) && !empty($faire_state) ? ', ':'');
                $faire_location .= (!empty($faire_state) ? $faire_state : "");                   

                $faire_countries = em_get_countries();
                $faire_country = $EM_Location->location_country;                

                //set default alt tag text if none is set
                $image_alt = !empty($image_alt) ? $image_alt : "Maker Faire " . $faire_year . " " . get_the_title() . " Featured Image";        
            }elseif($postType == "projects") {                                                                
                $makerData = get_field('maker_data');
                $maker_name = isset($makerData[0]['maker_or_group_name']) ? $makerData[0]['maker_or_group_name'] : "";
                
                $project_location = get_field('project_location');
                $project_state    = (isset($project_location['state']) ? $project_location['state']:'');
                $project_country  = (isset($project_location['country']) ? $project_location['country']:'');
                //$categories = strip_tags(get_the_term_list( get_the_ID(), "mf-project-cat", '', ', ' ));
                $excerpt = get_field('exhibit_description');

                //set default alt tag text if none is set
                $image_alt  = !empty($image_alt) ? $image_alt : $title . " Project Image for " . "Maker Faire " . $faire_name . " " . $faire_year;
            }
                        
            ?>
            <div class="result-item <?php echo $postType; ?>">
                <?php if ( has_post_thumbnail() ) { ?>
                        <div class="result-image">
							<a href="<?php echo $permalink; ?>">
								<img srcset="<?php echo $featured_image_400_300; ?> 400w, <?php echo $featured_image_600_400; ?> 1199w, <?php echo $featured_image_400_300; ?>" sizes="(max-width: 400px) 400px, (max-width: 1199px) 1199px, 1200px" src="<?php echo $featured_image_400_300; ?>" alt="<?php echo $image_alt; ?>" />
							</a>
                        </div>
                <?php } ?>
                <div class="results-text" <?php echo $result_text_style; ?>>
                    <h2><a href="<?php echo $permalink; ?>"><?php echo $title; ?></a></h2>
                    <?php if($postType == "event" || $postType == "faire") { ?>
                        <?php if(!empty($faire_location)){ ?>
                            <div class="result-detail">
                                <?php echo $faire_location; ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($faire_countries[$faire_country])){ ?>
                            <div class="result-detail">
                                <?php echo $faire_countries[$faire_country]; ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($faire_date)){ ?>
                            <div class="result-detail">
                                <?php echo $faire_date; ?>
                            </div>
                        <?php } ?>
                        <div class="result-detail">
                            <a class="sf-learn-more" href="<?php echo $permalink; ?>">More</a>
                        </div>
                    <?php } ?>
                    <?php if($postType == "projects") { ?>
                        <?php if(!empty($faire_name)){ ?>
                            <div class="result-detail">
                                <b>Faire:</b>&nbsp;<?php echo $faire_name; ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($maker_name)){ ?>
                            <div class="result-detail">
                                <b>Maker<span></span>:</b>&nbsp;<?php echo $maker_name; ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($project_location)){ ?>
                            <div class="result-detail">
                                <span class="one-line">
                                    <?php
                                    echo (empty($project_state) && empty($project_country) ? '&nbsp;':'<b>Home: </b>');
                                    echo (!empty($project_state) ? $project_state : "");                
                                    echo (!empty($project_state) && !empty($project_country) ? ', ':'');
                                    echo (!empty($project_country) ? $project_country : "");                
                                    ?>
                                </span>
                            </div>
                        <?php } ?>
                        <?php /* if(!empty($categories)){ ?>
                            <div class="result-detail">
                                <?php echo $categories; ?>
                            </div>
                        <?php } */ ?>
                        <?php if(!empty($excerpt)){ ?>
                            <div class="result-detail desc">
                                <span class="truncated"><?php echo strip_tags(html_entity_decode($excerpt)); ?></span>
                            </div>
                        <?php } ?>
                        <div class="result-detail"><a href="<?php echo $permalink; ?>" class="sf-learn-more">More</a></div>
                    <?php } ?>
                </div>
            </div>
    <?php } ?>
    </div>
   

    <div class="pagination">

        <div class="nav-previous"><?php next_posts_link( 'Older posts', $query->max_num_pages ); ?></div>
        <div class="nav-count">Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?></div>
        <div class="nav-next"><?php previous_posts_link( 'Newer posts' ); ?></div>

    </div>
    <?php
} else {
    echo "<span class='no-results'>No Results Found</span>";
}
?>

