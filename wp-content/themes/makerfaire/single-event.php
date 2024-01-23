<?php get_header(); 

?>

<main id="content">
	
	<?php
	while ( have_posts() ) : the_post(); 
		// ACF Data
		//hero section
		$topSection 			= get_field('top_section');
		$hero_bg 				= isset($topSection['hero_image']['url']) 	         ? $topSection['hero_image']['url'] : get_stylesheet_directory_uri()."/images/faire-page-hero-img-default.png"; 				
		$faire_logo 			= isset($topSection['horizontal_faire_logo']['url']) ? $topSection['horizontal_faire_logo']['url'] : ''; 
		
		// Faire Info Section
		$faireInfo 				= get_field('faire_info');
		$faire_video 			= $faireInfo['faire_video'];
		
		$faire_num_attendees 	= $faireInfo['number_of_attendees'];
		$faire_num_projects 	= $faireInfo['number_of_projects'];

		// Social Links
		$socialLinks 			= $faireInfo['social_links'];
		$fb_link 				= $socialLinks['facebook'];
		$twit_link 				= $socialLinks['twitter'];
		$insta_link 			= $socialLinks['instagram'];
		$ytube_link 			= $socialLinks['youtube'];

		// Producer Section
		$producerSection 		= get_field('producer_section');
		$faire_graphic 			= isset($producerSection['faire_graphic']['url']) 		? $producerSection['faire_graphic']['url'] 	 	 : get_stylesheet_directory_uri()."/images/faire-page-faire-graphic.png";
		$faire_badge 			= isset($producerSection['circular_faire_logo']['url']) ? $producerSection['circular_faire_logo']['url'] : get_stylesheet_directory_uri()."/images/default-badge.png";
		
		$producer_org 			= $producerSection['producer_or_org'];
		$contact 				= $producerSection['contact_email'];
		$contactLink			= (str_contains($contact, "@") ? "mailto:" . $contact : $contact);
		
		$faire_link 			= $producerSection['link_to_faire'];

		// Highlights Section
		$highlightsSection 		= get_field("faire_highlights");
		$highlightImages 		= $highlightsSection['faire_images'];	
		$photo_credit			= $highlightsSection['photo_credit'];		
		
		// Dates
		$faire_year 			= date('Y', strtotime($EM_Event->event_start_date));
		$faire_date 			= date("F Y", strtotime($EM_Event->event_start_date));
		$faire_countries 		= em_get_countries();
		$faire_country 			= $EM_Event->location->location_country;

		//Projects Section
		//find any projects associated with this faire
		$args = array(
			'posts_per_page' 	=> 3,
			'post_type'    	 	=> 'projects',
			'meta_key'			=> 'faire_information_faire_post',
			'meta_value'    	=>  get_the_id(),
			'orderby'        	=> 'rand'
		);
	
		
		$project_query = new WP_Query( $args );				

		$linkToProjects = (isset($project_query->posts) && !empty($project_query->posts) ? '/yearbook/'.$faire_year.'-projects?_sfm_faire_information_faire_post='.get_the_ID() : '');
	?>
    <section id="eventHeader" class="hero-header" style="background-image:url('<?php echo $hero_bg; ?>')">
	    <div class="logo-wrapper">
			<h1 class="single-post-title"><?php the_title(); ?></h1>
			<?php if(!empty($faire_logo)) { ?>
				<img id="faireLogo" src="<?php echo $faire_logo; ?>" alt="<?php echo get_the_title() . " Logo";?>" />
			<?php } ?>
		</div>
	</section>

	<nav class="eoy-breadcrumbs">			
		<a href="/yearbook/<?php echo $faire_year; ?>-faires">All Faires</a> <?php if($linkToProjects !='') echo '/ <a href="'.$linkToProjects.'" target="_blank">Faire Projects</a>';?>
	</nav>

	<section id="faireInfo">
		<div class="faire-video">
			<?php
			if($faire_video && is_valid_video($faire_video)) {
				//global $wp_embed;
				//echo $wp_embed->run_shortcode('[embed]' . $faire_video . '[/embed]');
				if(str_contains("vimeo.com", $faire_video)) {
					echo do_shortcode("[vimeo " . $faire_video . "]");
				} else { ?>
					<iframe width="560" height="315" src="<?php echo getYoutubeEmbedUrl($faire_video) . "?autoplay=1&mute=1"; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
				<?php } 
			} else { ?>			
				<?php echo get_the_post_thumbnail(); ?>
			<?php }
			?>
		</div>
		<div class="faire-info-box">
			<div class="striped-background"></div>
			<h5 class="faire-date"><?php echo strtoupper($faire_date); ?></h5>
			<h4 class="faire-country"><?php echo (isset($faire_countries[$faire_country])?$faire_countries[$faire_country]:''); ?></h4>
			<div class="blue-spacer"></div>
			<?php if($faire_num_projects != ''){?>
				<h3 class="faire-stat">Projects: <?php echo number_format($faire_num_projects); ?></h3>
			<?php } ?>			
			<?php if($faire_num_attendees != ''){?>
				<h3 class="faire-stat">Attendees: <?php echo number_format($faire_num_attendees); ?></h3>
			<?php } ?>						
			<div class="social-links reversed">
				<?php foreach ($socialLinks as $link) {
					if($link) {
						echo('<a class="link" href="' . $link . '"></a>');
					}
				} ?>
				<?php if($faire_link != ''){ ?>
					<a class="link fa fa-link" href="<?php echo $faire_link; ?>" target="_blank"></a>
				<?php } ?>				
			</div>
		</div>
	</section>

	<section id="faireProjects">
		<?php if(isset($project_query->posts) && !empty($project_query->posts)) { ?>
			<h2>Projects</h2>
			<div class="blue-spacer"></div>
			<div class="projects-wrapper">
				<?php foreach($project_query->posts as $project){ ?>
					<div class="faire-project">
					  <a href="<?php echo get_permalink($project->ID); ?>">
					    <div class="project-image">
							<?php echo get_the_post_thumbnail($project->ID); ?>
						</div>
						<h4><?php echo $project->post_title; ?></h4>
						<p><?php echo get_field('exhibit_description', $project->ID); ?>
						<p class="universal-btn">More</p>
					  </a>
					</div>
				<?php } ?>	
			</div>
			<a href="<?php echo $linkToProjects;?>" class="btn universal-btn-red">All Projects</a>
		<?php } ?>
		
	</section>
	
	<section id="producerInfo">
		<div class="faire-custom-image">
			<div class="striped-background"></div>
			<img src="<?php echo $faire_graphic; ?>" alt="<?php the_title(); ?> Custom Image"  /> <?php // // pull the default image from 1920 image ?>
		</div>
		<div class="producer-details" style="background-image:url('<?php echo $faire_badge; ?>');">
			<?php if($producer_org || !empty($contact) || !empty($faire_link) ){ ?>
				<div class="producer-details-overlay">
					<div class="producer-details-inner-wrap">				
						<h3>Producer Information</h3>
						<?php if($producer_org) { ?>
							<div class="producer-detail"><b>Producer:</b> <?php echo $producer_org; ?></div>
						<?php } ?>
						<?php if(!empty($contact)) { ?>
							<div class="producer-detail"><b>Contact:</b> <a href="<?php echo $contactLink; ?>" target="_blank"><?php echo $contact; ?></a></div>
						<?php } ?>
						<?php if(!empty($faire_link)) { ?>
							<div class="producer-detail"><b>Website:</b> <a href="<?php echo $faire_link; ?>" target="_blank"><?php echo $faire_link; ?></a></div>
						<?php } ?>				
					</div>
				</div>
			<?php } ?>				
		</div>
	</section>

	<?php
	if( $highlightImages ) { ?>
		<section id="faireHighlights">
			<h2>Highlights</h2>
			<div class="blue-spacer"></div>
			<div id="highlightGallery">
				<?php foreach($highlightImages as $image) { ?>
					<div class="gallery-item"><img alt="<?php echo $image['alt'];?>"  src='<?php echo $image['url']; ?>' /></div>
				<?php } ?>
				<?php if($photo_credit!=''){?>
					<span>Photo Credit: <?php echo $photo_credit;?></span>
				<?php } ?>
			</div>
		</section>
	<?php } ?>
	
	<?php // End of the loop.
		endwhile; ?>

</main><!-- content -->

<script>
jQuery(document).ready(function(){
	var numImages = jQuery('#highlightGallery .gallery-item').length;
	jQuery('#highlightGallery .gallery-item').on("click", function () {
		//every time you click on a gallery item, open a dialog modal to show the images
		var galleryItem = jQuery(this);
		jQuery('body').append('<div id="dialog"><img src="' + jQuery("img", this).attr('src') + '" width="100%" /></div>');
		jQuery('#dialog').dialog({
			dialogClass: "hide-heading",
			modal: true,
			// these buttons will go to the next image from the faireGallery and replace the src of the image in the modal with the next or previous image in the gallery
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