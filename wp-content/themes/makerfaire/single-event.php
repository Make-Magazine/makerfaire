<?php get_header(); 

?>

<main id="content">
	
	<?php
	while ( have_posts() ) : the_post(); 
		// ACF Data
		//hero section
		$topSection = get_field('top_section');
		$hero_bg = $topSection['hero_image'];
		$faire_logo = $topSection['horizontal_faire_logo'];
		
		//faire info
		$faireInfo = get_field('faire_info');
		$faire_video = $faireInfo['faire_video'];
		$faire_custom_image = $faireInfo['faire_custom_image'];
		$faire_num_makers = $faireInfo['number_of_makers']; //this needs to be deleted
		$faire_num_attendees = $faireInfo['number_of_attendees'];
		$faire_num_projects = $faireInfo['number_of_projects'];

		$faire_year = date('Y', strtotime($EM_Event->event_start_date));
		$faire_date = date("F, Y", strtotime($EM_Event->event_start_date));
		$faire_country = $EM_Event->location->location_country;

		//social links 
		$socialLinks = $faireInfo['social_links'];
		$fb_link = $socialLinks['facebook'];
		$twit_link = $socialLinks['twitter'];
		$insta_link = $socialLinks['instagram'];
		$ytube_link = $socialLinks['youtube'];
		
		//producer section	
		$producerSection = get_field('producer_section');
		$faire_graphic = $producerSection['faire_graphic'];
		$faire_badge = ($producerSection['circular_faire_logo']) ? $producerSection['circular_faire_logo']['url'] : '//' . $_SERVER['HTTP_HOST'] . "/wp-content/themes/makerfaire/images/default-badge.png";
		$producer_org = $producerSection['producer_or_org'];
		$contact = $producerSection['contact_email'];
		if(str_contains($contact, "@")) {
		$contact = "mailto:" . $contact;
		}
		$faire_link = $producerSection['link_to_faire'];

		//highlights section
		$highlightsSection = get_field("faire_highlights");
		$highlightImages = $highlightsSection['faire_images'];
		$highlightLink = $highlightsSection['faire_highlight_link'];				

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
			} else { ?>
				<img src="<?php echo $faire_graphic['url']; ?>" alt="<?php the_title(); ?> Featured Image" />
			<?php }
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
				<a class="link fa fa-link" href="<?php echo $faire_link; ?>" target="_blank"></a>
			</div>
		</div>
	</section>

	<section id="faireProjects"></section>

	<section id="producerInfo">
		<div class="faire_custom_image">
			<div class="striped_background"></div>
			<img src="<?php echo $faire_graphic['url']; ?>" alt="<?php the_title(); ?> Custom Image"  />
		</div>
		<div class="producer-details" style="background-image:url('<?php echo $faire_badge; ?>');">
		  <div class="producer-details-overlay">
			<div class="producer-details-inner-wrap">
				<h3>Producer Information</h3>
				<?php if($producer_org) { ?>
					<div class="producer-detail"><b>Producer:</b> <?php echo $producer_org; ?></div>
				<?php } ?>
				<?php if(!empty($contact)) { ?>
					<div class="producer-detail"><b>Contact:</b> <a href="<?php echo $contact; ?>"><?php echo $contact; ?></a></div>
				<?php } ?>
				<?php if(!empty($faire_link)) { ?>
		  			<div class="producer-detail"><b>Website:</b> <a href="<?php echo $faire_link; ?>" target="_blank"><?php echo $faire_link; ?></a></div>
				<?php } ?>
			</div>
		  </div>
		</div>
	</section>

	<section id="faireHighlights">
		<?php
			if( $highlightImages ): ?>
			    <h2>Highlights</h2>
				<div class="spacer"></div>
				<div id="highlightGallery">
                <?php foreach($highlightImages as $image) { ?>
                    <div class="gallery-item"><img alt="<?php echo $image['caption'];?>"  src='<?php echo $image['url']; ?>' /></div>
                <?php } ?>
                </div>
			<?php endif; ?>
	</section>
	
	<?php // End of the loop.
		endwhile; ?>

</main><!-- content -->

<script>
jQuery(document).ready(function(){
	var numImages = jQuery('#highlightGallery .gallery-item').length;
	jQuery('#highlightGallery .gallery-item').on("click", function () {
		//every time you click on an owl item, open a dialog modal to show the images
		var galleryItem = jQuery(this);
		jQuery('body').append('<div id="dialog"><img src="' + jQuery("img", this).attr('src') + '" width="100%" /></div>');
		jQuery('#dialog').dialog({
			dialogClass: "hide-heading",
			modal: true,
			// these buttons will go to the next image from the #projectGallery and replace the src of the image in the modal with the next or previous image in the gallery
			buttons: numImages <= 1 ? [] : [
				{
					"class": "dialog-nav-btn dialog-prev-btn",
					click: function() {
						if(galleryItem.prev(".gallery-item").children("img").attr("src")) {
							jQuery("#dialog img").attr("src", galleryItem.prev(".gallery-item").children("img").attr("src"));
							galleryItem = galleryItem.prev();
						} else {
							jQuery("#dialog").dialog('close');
						}
					}
				},
				{
					"class": "dialog-nav-btn dialog-next-btn",
					click: function() {  
						if(galleryItem.next(".gallery-item").children("img").attr("src")) {
							jQuery("#dialog img").attr("src", galleryItem.next(".gallery-item").children("img").attr("src"));
							galleryItem = galleryItem.next();
						} else {
							jQuery("#dialog").dialog('close');
						}
					}
				}
			],
			close: function(event, ui) {
				jQuery(this).remove();
			},
			open: function(event, ui) { 
			jQuery('.ui-widget-overlay').bind('click', function(){ 
				jQuery("#dialog").dialog('close');
			}); 
		}
		});
	});
});
</script>
<?php get_footer(); ?>