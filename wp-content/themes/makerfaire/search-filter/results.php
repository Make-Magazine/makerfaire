<?php
/**
 * Search & Filter Pro
 *
 * Sample Results Template
 *
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      https://searchandfilter.com
 * @copyright 2018 Search & Filter
 *
 * Note: these templates are not full page templates, rather
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode - think
 * of it as a template part
 *
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs
 * and using template tags -
 *
 * http://codex.wordpress.org/Template_Tags
 *
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
            $post = $query->the_post();
            $post_id = get_the_ID();
            $postType = get_post_type($query->post_type);
            $title = get_the_title();
            $result_text_style = "";
            if($postType == "event" || $postType == "faire") { 
                $title = str_replace("Maker Faire", "", $title);
                $producerSection = get_field('producer_section');
                $faire_badge = ($producerSection['circular_faire_logo']) ? $producerSection['circular_faire_logo']['url'] : '//' . $_SERVER['HTTP_HOST'] . "/wp-content/themes/makerfaire/images/default-badge.png";
                $result_text_style = 'style="background-image:url(' . $faire_badge . ');"';
                $event_id = get_post_meta($post_id, '_event_id');
                $EM_Event = new EM_Event( $event_id[0] );
                $faire_year = date('Y', strtotime($EM_Event->event_start_date));
                $EM_Location = $EM_Event->get_location();
                $faire_location = $EM_Location->location_town;
                $faire_state = $EM_Location->location_state;
                $image_id = get_post_meta( $post_id, '_thumbnail_id', true );
                $image_alt  = get_post_meta ( $image_id, '_wp_attachment_image_alt', true );
                $image_alt = !empty($image_alt) ? $image_alt : "Maker Faire " . $faire_year . " " . get_the_title() . " Featured Image";    
                if(!empty($faire_location) && !empty($faire_state)) {
                    $faire_location .= ", ";
                }
                if(!empty($faire_state)) {
                    $faire_location .=  $faire_state;
                }

                $faire_countries = em_get_countries();
                $faire_country = $EM_Location->location_country;
                $faire_date = date("F Y", strtotime($EM_Event->event_start_date));
            }
            if($postType == "projects") {
                $faire_info = get_field("faire_information");
                $faire_id   = (isset($faire_info['faire_post'])?$faire_info['faire_post']:'');
                $faire_name = ($faire_id!='' ? get_the_title($faire_id):'');
                $faire_year = (isset($faire_info["faire_year"])?$faire_info["faire_year"]:'');
                $image_id   = get_post_meta( $post_id, '_thumbnail_id', true );
                $image_alt  = get_post_meta ( $image_id, '_wp_attachment_image_alt', true );
                $image_alt  = !empty($image_alt) ? $image_alt : $title . " Project Image for " . "Maker Faire " . $faire_name . " " . $faire_year;  
                
                $producerSection = get_field('producer_section', $faire_id);
                $faire_badge = (isset($producerSection['circular_faire_logo']['url']) ? $producerSection['circular_faire_logo']['url'] : '//' . $_SERVER['HTTP_HOST'] . "/wp-content/themes/makerfaire/images/default-badge.png");
                $result_text_style = 'style="background-image:url(' . $faire_badge . ');"';
                
                $makerData = get_field('maker_data');
                $maker_name = isset($makerData[0]['maker_or_group_name']) ? $makerData[0]['maker_or_group_name'] : "";
                
                $project_location = get_field('project_location');
                $project_state    = (isset($project_location['state']) ? $project_location['state']:'');
                $project_country  = (isset($project_location['country']) ? $project_location['country']:'');
                //$categories = strip_tags(get_the_term_list( get_the_ID(), "mf-project-cat", '', ', ' ));
                $excerpt = get_field('exhibit_description');
            }
            
            ?>
            <div class="result-item <?php echo $postType; ?>">
                <?php if ( has_post_thumbnail() ) { ?>
                        <div class="result-image">
							<a href="<?php the_permalink(); ?>">
								<img srcset="<?php echo legacy_get_resized_remote_image_url(get_the_post_thumbnail_url(), 400, 300); ?> 400w, <?php echo legacy_get_resized_remote_image_url(get_the_post_thumbnail_url(), 600, 400); ?> 1199w, <?php echo legacy_get_resized_remote_image_url(get_the_post_thumbnail_url(), 400, 300); ?>" sizes="(max-width: 400px) 400px, (max-width: 1199px) 1199px, 1200px" src="<?php echo legacy_get_resized_remote_image_url(get_the_post_thumbnail_url(), 400, 300); ?>" alt="<?php echo $image_alt; ?>" />
							</a>
                        </div>
                <?php } ?>
                <div class="results-text" <?php echo $result_text_style; ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h2>
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
                            <a class="sf-learn-more" href="<?php the_permalink(); ?>">More</a>
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
                                <span class="one-line"><b>Home:</b> 
                                <?php 
                                    if(!empty($project_state)) {
                                        echo $project_state . ", ";
                                    }
                                    if(!empty($project_country)) {
                                        echo $project_country; 
                                    }
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
                        <div class="result-detail"><a href="<?php the_permalink(); ?>" class="sf-learn-more">More</a></div>
                    <?php } ?>
                </div>
            </div>

            <hr />
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

