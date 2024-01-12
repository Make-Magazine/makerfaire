<?php 
//Project Information
$exhibit_photo = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' )[0];
$exhibit_video = get_field("exhibit_video_link");
$exhibit_inspiration = get_field("exhibit_inspiration");
$exhibit_additional_images = get_field("additional_exhibit_images");
$exhibit_social = get_field("exhibit_social");
$exhibit_website = get_field("exhibit_website");

//faire information
$faireData  = get_field("faire_information");
$faire_name = (isset($faireData["faire_post"]->post_title)?$faireData["faire_post"]->post_title:'');
$faire_id   = $faireData["faire_post"]->ID;
$faire_year = (isset($faireData["faire_year"])?$faireData["faire_year"]:'');

//hero image
$faireTopSection = get_field("top_section", $faire_id);
$hero_bg         = isset($faireTopSection['hero_image']['url']) ? $faireTopSection['hero_image']['url'] :  get_stylesheet_directory_uri()."/images/faire-page-hero-img-default.png";

//Project Location
$project_location = get_field('project_location');
$project_state    = (isset($project_location['state'])   ? $project_location['state']:'');
$project_country  = (isset($project_location['country']) ? $project_location['country']:'');

//Maker Data
$maker_data = get_field("maker_data");

?>
<?php get_header(); ?>
<article>
   
    <header id="project-hero" style="background-image:url('<?php echo $hero_bg; ?>');">
        <div class="hero-overlay"></div>
        <div class="breadcrumbs">
            <a href="/<?php echo $faire_year; ?>/faires">Home</a> / <a href="/<?php echo $faire_year; ?>/projects">Projects</a>
        </div>
    </header>
    
    <section id="project-info-section" class="container">
        <div class="project-info">
            <h3 class="faire-details"><?php echo $faire_name ." ".$faire_year;?></h3>
            <h1 class="project-title"><?php echo get_field("title");?></h1>
            <h4><?php
                if(!empty($project_state)) {
                    echo $project_state . ", ";
                }
                if(!empty($project_country)) {
                    echo $project_country; 
                }
                 ?>
            </h4>
            <div class="blue-spacer"></div>
            <p><?php echo html_entity_decode(get_field("exhibit_description"));?></p>
            <div class="social-links reversed">
				<?php if(!empty($exhibit_social)) {
                        foreach ($exhibit_social as $social) {                    
                            if($social['social_url']) {
                                echo('<a class="link" href="' . $social['social_url'] . '"></a>');
                            }
				        }
                    }
                if(!empty($exhibit_website)) { ?>
				    <a class="link fa fa-link" href="<?php echo $exhibit_website; ?>" target="_blank"></a>
                <?php } ?>
			</div>          
        </div>
        <div class="project-picture">
            <img class="featured-image" src="<?php echo $exhibit_photo;?>" />
        </div>
    </section>
    
    <?php if(!empty($exhibit_video) || !empty($exhibit_inspiration)){ ?>
        <section id="project-highlights-section">
            <span class="striped-background"></span>
            <?php if($exhibit_video && is_valid_video($exhibit_video)) { ?>
                <div class="project-video">            
                    <?php                        
                    echo $wp_embed->run_shortcode('[embed]' . $exhibit_video . '[/embed]');
                    ?>
                </div>
            <?php } ?>
            <?php if($exhibit_inspiration!=''){ ?>
                <div class="project-inspiration">
                    <?php
                        echo '<h3>What Inspired You to Make This?</h3>';
                        echo '<p>'.$exhibit_inspiration.'</p>';
                    ?>
                    <div class="blue-spacer"></div>
                </div>
            <?php } ?>
        </section>
    <?php } ?>

    <section id="project-photos">
		<?php
			if( $exhibit_additional_images ) { ?>
			    <h2>Additional Project Photos</h2>
				<div id="highlightGallery">
					<?php foreach($exhibit_additional_images as $image) { ?>
						<div class="gallery-item"><img alt="<?php echo $image['alt'];?>"  src='<?php echo $image['url']; ?>' /></div>
					<?php } ?>
					<?php /* for later if($photo_credit!=''){?>
						<span>Photo Credit: <?php echo $photo_credit;?></span>
					<?php } */ ?>
                </div>
			<?php } ?>
	</section>
    
    <section id="project-makers" class="container">
    
    <!-- Maker Data -->
    <?php
    if(!empty($maker_data)) {
        
        if(count($maker_data) == 1) { ?>
            <div class="single-maker-info">
                <?php if(isset($maker_data[0]["maker_photo"]["url"])){ ?>
                    <img src="<?php echo $maker_data[0]["maker_photo"]["url"]; ?>" alt="<?php echo $maker_data[0]["maker_or_group_name"]; ?> Maker Photo">
                <?php } ?>
            </div>
            <div class="single-maker-bio">
                <p class="maker-name"><?php echo $maker_data[0]["maker_or_group_name"]; ?></p>
                <p><?php echo $maker_data[0]["maker_bio"]?></p>
                <div class="social-links reversed">
                    <?php
                    
                    if(!empty($maker_data[0]['maker_social'])) {
                        
                        foreach($maker_data[0]['maker_social'] as $link) {
                            if($link['maker_social_link']) {
                                echo('<a class="link" href="' . $link['maker_social_link'] . '"></a>');
                            }
                        } 
                    }
                    if(!empty($maker_data[0]["maker_website"])) { ?>
                        <a class="link fa fa-link" href="<?php echo $maker_data[0]["maker_website"]; ?>" target="_blank"></a>
                    <?php } ?>
                </div>
            </div>
        <?php } else { // we got a different layout for multiple makers ?>
            <h2>Makers</h2>
            <div class="multiple-maker-wrapper">
            <?php foreach($maker_data as $maker){ ?>
                <div class="maker-wrapper">
                    <div class="img-wrap">                   
                        <?php if(isset($maker["maker_photo"]["url"])){ ?>
                            <img src="<?php echo $maker["maker_photo"]["url"]; ?>" alt="<?php echo $maker["maker_or_group_name"]; ?> Maker Photo">
                        <?php } ?>
                    </div>
                    
                    <h4><?php echo $maker["maker_or_group_name"];?></h4>
                    <p class="maker-bio"><?php echo $maker["maker_bio"]?></p>
                    <div class="social-links reversed">
                        <?php 
                        if(!empty($maker['maker_social'])) {
                            foreach($maker['maker_social'] as $link) {
                                if($link) {
                                    echo('<a class="link" href="' . $link['maker_social_link'] . '"></a>');
                                }
                            } 
                        }
                        if(!empty($maker["maker_website"])) { ?>
                            <a class="link fa fa-link" href="<?php echo $maker["maker_website"]; ?>" target="_blank"></a>
                        <?php } ?>
                    </div>
                </div>
                <?php
            } ?>
            </div>
            <?php
        }
    }
    
    
    
    //echo '<div>'.$exhibit_social
    
        $faire = ( isset( $_GET['faire'] ) && ! empty( $_GET['faire'] ) ) ? sanitize_title( $_GET['faire'] ) : '';				
    ?>
    </section>


</article><!--Container-->
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