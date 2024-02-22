<?php 
/*  Page layout for the Yearbook Individual Faire Pages */
get_header(); 
global $_wp_additional_image_sizes; 
print '<pre>'; 
print_r( $_wp_additional_image_sizes ); 
print '</pre>';
?>

<main id="content">
	
	<?php	
	while ( have_posts() ) : the_post(); 
		$faire_id 	= get_the_ID();
		$faire_name = get_the_title();
		$EM_Event 	= em_get_event($faire_id, 'post_id');
						
		// Dates
		$faire_year 			= date('Y', strtotime($EM_Event->event_start_date));
		$faire_date 			= date("F Y", strtotime($EM_Event->event_start_date));

		//faire location
		$event_location 		= $EM_Event->get_location();
		$faire_countries 		= em_get_countries();
		$faire_country 			= (isset($event_location->location_country)?$event_location->location_country:'');
	
		// ACF Data
		//hero section
		$topSection 			= get_field('top_section');
		$hero_bg 				= isset($topSection['hero_image']['url']) 	          ? $topSection['hero_image']['url'] : get_stylesheet_directory_uri()."/images/faire-page-hero-img-default.png"; 				
		
		$faire_logo 	 		= isset($topSection['horizontal_faire_logo']['url'])  ? $topSection['horizontal_faire_logo']['sizes']['medium_large'] : ''; 
		$faire_logo_alt			= !empty($topSection['horizontal_faire_logo']['alt']) ? $topSection['horizontal_faire_logo']['alt'] : "Maker Faire " . $faire_name . " Logo"; 
		
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
		
		$faire_graphic 			= isset($producerSection['faire_graphic']['url']) 			? $producerSection['faire_graphic']['sizes']['medium_large'] 	: get_stylesheet_directory_uri()."/images/faire-page-faire-graphic.png";
		$faire_graphic_alt		= !empty($producerSection['faire_graphic']['alt']) 			? $producerSection['faire_graphic']['alt'] 	 	 		     	: "Maker Faire " . $faire_year . " " . $faire_name . " Custom Image";
		$faire_badge 			= isset($producerSection['circular_faire_logo']['url']) 	? $producerSection['circular_faire_logo']['sizes']['thumbnail']	: get_stylesheet_directory_uri()."/images/default-badge.png";
		
		$producer_org 			= $producerSection['producer_or_org'];
		$contact 				= $producerSection['contact_email'];
		$contactLink			= (str_contains($contact, "@") ? "mailto:" . $contact : $contact);
		
		$faire_link 			= $producerSection['link_to_faire'];

		// Highlights Section
		$highlightsSection 		= get_field("faire_highlights");
		$highlightImages 		= $highlightsSection['faire_images'];	
		$photo_credit			= $highlightsSection['photo_credit'];		
		
		//Projects Section
		//find any projects associated with this faire
		$args = array(
			'posts_per_page' 	=> 20,
			'post_type'    	 	=> 'projects',
			'meta_key'			=> 'faire_information_faire_post',
			'meta_value'    	=>  $faire_id,
			'orderby'        	=> 'rand'
		);
	
		$project_query = new WP_Query( $args );		
		//var_dump($project_query->posts);
		$projects = $project_query->posts;
		shuffle($projects);
		$projects = array_slice($projects, 0, 3);

		$linkToProjects = (isset($projects) && !empty($projects) ? '/yearbook/'.$faire_year.'-projects?_sfm_faire_information_faire_post='.$faire_id : '');
	?>
    <section id="eventHeader" class="hero-header" style="background-image:url('<?php echo $hero_bg; ?>')">
	    <div class="logo-wrapper">			
			<?php if(!empty($faire_logo)) { ?>
				<img id="faireLogo" src="<?php echo $faire_logo; ?>" alt="<?php echo $faire_logo_alt; ?>" />
			<?php } else {	?>
				<h1 class="single-post-title">Maker Faire <?php the_title(); ?></h1>
			<?php } ?>
		</div>
	</section>

	<nav class="eoy-breadcrumbs">			
		<a href="/yearbook/<?php echo $faire_year; ?>-faires">All Faires</a> <?php if($linkToProjects !='') echo '/ <a href="'.$linkToProjects.'">Faire Projects</a>';?>
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
				<img src="<?php echo get_the_post_thumbnail_url('full'); ?>" alt="Maker Faire <?php echo $faire_year . " " . $faire_name?> Featured Image" />
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
		<?php if(isset($projects) && !empty($projects)) { ?>
			<h2>Projects</h2>
			<div class="blue-spacer"></div>
			<div class="projects-wrapper">
				<?php foreach($projects as $project){ 
					?>
					<div class="faire-project">
					  <a href="<?php echo get_permalink($project->ID); ?>">
					    <div class="project-image">
							<?php 
							$thumbnail_id  = get_post_thumbnail_id($project->ID);
							//check if there is a featured image set
							if($thumbnail_id) {
								$image_src = wp_get_attachment_image_src( $thumbnail_id, 'full' );
								$image_alt = get_post_meta ( $thumbnail_id, '_wp_attachment_image_alt', true );
								$image_alt = !empty($image_alt) ? $image_alt : get_the_title($project->ID) . " Project Image for Maker Faire " . $faire_name . " " . $faire_year;;	
								?><img src="<?php echo $image_src[0] ?>" alt="<?php echo $image_alt; ?>" /><?php
							}
							?>	
							
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
			<img src="<?php echo $faire_graphic; ?>" alt="<?php echo $faire_graphic_alt; ?>"  /> <?php // // pull the default image from 1920 image ?>
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
				<?php foreach($highlightImages as $image) { 
					if(isset($image['url'])){
					$alt = ($image['alt'] != "") ? $image['alt'] : "Maker Faire " . $faire_name . " " . $faire_year . " - " . $image['title']; ?>
					<div class="gallery-item"><img alt="<?php echo $alt;?>"  src='<?php echo $image['sizes']['medium_large']; ?>' /></div>
					<?php } ?>
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