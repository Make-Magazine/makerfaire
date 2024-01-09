<?php get_header(); 

?>

<main id="content">
	
	<?php
	while ( have_posts() ) : the_post(); 
		// ACF Data
		$topSection = get_field('top_section');
		  $hero_bg = $topSection['hero_image'];
		  $faire_logo = $topSection['horizontal_faire_logo'];
		$faireInfo = get_field('faire_info');
		  $faire_video = $faireInfo['faire_video'];
		  $faire_custom_image = $faireInfo['faire_custom_image'];
		  $faire_num_makers = $faireInfo['number_of_makers'];
		  $faire_num_attendees = $faireInfo['number_of_attendees'];
		  $faire_num_projects = $faireInfo['number_of_projects'];
		  $socialLinks = $faireInfo['social_links'];
		  	$fb_link = $socialLinks['facebook'];
		  	$twit_link = $socialLinks['twitter'];
		  	$insta_link = $socialLinks['instagram'];
		  	$ytube_link = $socialLinks['youtube'];
		$producerSection = get_field('producer_section');
		  $faire_graphic = $producerSection['faire_graphic'];
		  $faire_badge = ($producerSection['circular_faire_logo']) ? $producerSection['circular_faire_logo']['url'] : '//' . $_SERVER['HTTP_HOST'] . "/wp-content/themes/makerfaire/images/default-badge.png";
		  $producer_org = $producerSection['producer_or_org'];
		  $contact_email = $producerSection['contact_email'];
		  $faire_link = $producerSection['link_to_faire'];
		$faire_year = date('Y', strtotime($EM_Event->event_start_date));
		$faire_date = date("M jS, Y", strtotime($EM_Event->event_start_date));
		if($EM_Event->event_start_date != $EM_Event->event_end_date) {
			$faire_date .= " - " . date("M jS, Y", strtotime($EM_Event->event_end_date));
		}
		$faire_country = $EM_Event->location->location_country;

	?>
    <section id="eventHeader" style="background-image:url(<?php echo $hero_bg['url']; ?>">
	    <div class="logo-wrapper">
			<h1 class="single-post-title"><?php the_title(); ?></h1>
			<img id="faireLogo" src="<?php echo $faire_logo['url']; ?>" />
		</div>
		<div class="breadcrumbs">
			<a href="/yearbook/<?php echo $faire_year; ?>/faires">Home</a> / <a href="<?php echo '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>projects">Projects</a>
		</div>
	</section>

	<section id="faireInfo">
		<div class="faire_video">
			<?php
			if($faire_video && is_valid_video($faire_video)) {
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . $faire_video . '[/embed]');
			}
			?>
		</div>
		<div class="faire_info_box">
			<div class="striped_background"></div>
			<h5 class="faire_date"><?php echo $faire_date; ?></h5>
			<h4 class="faire_country"><?php echo getCountryName($faire_country) ?></h4>
			<div class="spacer"></div>
			<h3 class="faire_stat">Makers: <?php echo $faire_num_makers; ?></h3>
			<h3 class="faire_stat">Projects: <?php echo $faire_num_projects; ?></h3>
			<div class="social-links reversed">
				<?php foreach ($socialLinks as $link) {
					if($link) {
						echo('<a class="link" href="' . $link . '"></a>');
					}
				} ?>
			</div>
		</div>
	</section>

	<section id="faireProjects"></section>

	<section id="producerInfo">
		<div class="faire_custom_image">
			<div class="striped_background"></div>
			<img src="<?php echo $faire_graphic['url']; ?>" />
		</div>
		<div class="producer-details" style="background-image:url('<?php echo $faire_badge; ?>');">
		  <div class="producer-details-overlay">
			<div class="producer-details-inner-wrap">
				<div class="producer-detail"><?php echo $producer_org; ?></div>
				<div class="producer-detail"><a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></div>
		  		<div class="producer-detail"><a href="<?php echo $faire_link; ?>" target="_blank"><?php echo $faire_link; ?></a></div>
			</div>
			</div>
		</div>
	</section>

	<section id="faireHighlights"></section>
	
	<?php // End of the loop.
		endwhile; ?>

</main><!-- content -->

<?php get_footer(); ?>