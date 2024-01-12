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
		<span>Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?></span>
	</div>

	<div class="pagination">

		<div class="nav-previous"><?php next_posts_link( 'Older posts', $query->max_num_pages ); ?></div>
		<div class="nav-next"><?php previous_posts_link( 'Newer posts' ); ?></div>
		<?php
			/* example code for using the wp_pagenavi plugin */
			if (function_exists('wp_pagenavi'))
			{
				echo "<br />";
				wp_pagenavi( array( 'query' => $query ) );
			}
		?>
	</div>

	<div class="result-items">
	<?php

		while ($query->have_posts()) {
			$post = $query->the_post();
			$postType = get_post_type($query->post_type);
			$title = get_the_title();
			$result_text_style = "";
			if($postType == "event" || $postType == "faire") { 
				$title = str_replace("Maker Faire", "", $title);
				$producerSection = get_field('producer_section');
				$faire_badge = ($producerSection['circular_faire_logo']) ? $producerSection['circular_faire_logo']['url'] : '//' . $_SERVER['HTTP_HOST'] . "/wp-content/themes/makerfaire/images/default-badge.png";
				$result_text_style = 'style="background-image:url(' . $faire_badge . ');"';
				$event_id = get_post_meta(get_the_ID(), '_event_id');
				$EM_Event = new EM_Event( $event_id[0] );
				$EM_Location = $EM_Event->get_location();
				$faire_location = $EM_Location->location_town;
				$faire_state = $EM_Location->location_state;
				if(!empty($faire_state)) {
					$faire_location .= ", " . $faire_state;
				}
				$faire_countries = em_get_countries();
				$faire_country = $EM_Location->location_country;
			}
			if($postType == "projects") {
				$makerData = get_field('maker_data');
				$maker_name = $makerData[0]['maker_or_group_name'];
				$faireInfo = get_field('faire_information');
				$faireID = $faireInfo['faire_post']->ID;
				$event_id = get_post_meta($faireID, '_event_id');
				$EM_Event = new EM_Event( $event_id[0] );
				$EM_Location = $EM_Event->get_location();
				$faire_location = $EM_Location->location_town;
				$faire_state = $EM_Location->location_state;
				$faire_countries = em_get_countries();
				$faire_country = $EM_Location->location_country;
				if(!empty($faire_state) && $faire_country == "US") {
					$faire_location .= ", " . $faire_state;
				}
				$faire_location .= ", " . $faire_countries[$faire_country];
				$categories = strip_tags(get_the_term_list( get_the_ID(), "mf-project-cat", '', ', ' ));
				$excerpt = get_field('exhibit_description');
			}
			
			?>
			<div class="result-item">
				<?php if ( has_post_thumbnail() ) { ?>
						<div class="result-image"><a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail("small"); ?>
						</a></div>
				<?php } ?>
				<div class="results-text" <?php echo $result_text_style; ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h2>
					<?php if($postType == "event" || $postType == "faire") { ?>
						<div class="result-detail">
							<?php if(!empty($faire_location)){ ?><i class="fa fa-map-marker-alt"></i><?php echo $faire_location; } ?>
						</div>
						<div class="result-detail">
							<?php if(!empty($faire_countries[$faire_country])){ ?><i class="fa fa-globe-africa"></i><?php echo $faire_countries[$faire_country]; } ?>
						</div>
					<?php } ?>
					<?php if($postType == "projects") { ?>
						<div class="result-detail">
							<?php if(!empty($maker_name)){ ?><i class="fa fa-user"></i><?php echo $maker_name; } ?>
						</div>
						<div class="result-detail">
							<?php if(!empty($faire_location)){ ?><i class="fa fa-map-marker-alt"></i><?php echo $faire_location; } ?>
						</div>
						<div class="result-detail">
							<?php if(!empty($categories)){ ?><i class="fa fa-lightbulb"></i><?php echo $categories; } ?>
						</div>
						<div class="result-detail">
							<?php if(!empty($excerpt)){ ?><i class="fa fa-info-circle"></i><span class="truncated"><?php echo $excerpt; ?></span> <?php } ?>
						</div>
						<div class="result-detail"><a href="<?php the_permalink(); ?>" style="margin-left:30px;">Learn More</a></div>
					<?php } ?>
				</div>
			</div>

			<hr />
	<?php } ?>
	</div>
	Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br />

	<div class="pagination">

		<div class="nav-previous"><?php next_posts_link( 'Older posts', $query->max_num_pages ); ?></div>
		<div class="nav-next"><?php previous_posts_link( 'Newer posts' ); ?></div>
		<?php
			/* example code for using the wp_pagenavi plugin */
			if (function_exists('wp_pagenavi'))
			{
				echo "<br />";
				wp_pagenavi( array( 'query' => $query ) );
			}
		?>
	</div>
	<?php
} else {
	echo "No Results Found";
}
?>
