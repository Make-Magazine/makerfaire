<?php 
/*  Page layout for the Yearbook Individual Project Pages */

//Pull faire specific information
$faireData       = get_field("faire_information");
$faire_year      = (isset($faireData["faire_year"])?$faireData["faire_year"]:'');

//Project Information
$project_title   = get_the_title();
$thumbnail_id    = get_post_thumbnail_id();

//check if there is a featured image set
if($thumbnail_id) {
    $image_src = wp_get_attachment_image_src( $thumbnail_id, 'large' );
    $image_alt = get_post_meta ( $thumbnail_id, '_wp_attachment_image_alt', true );
    $image_alt = !empty($image_alt) ? $image_alt : $project_title . " Project Image for Maker Faire " . $faire_name . " " . $faire_year;
}

$exhibit_id                 = get_the_ID();
$exhibit_photo              = (isset($image_src[0])?$image_src[0]:'');
$exhibit_video              = get_field("exhibit_video_link");
$exhibit_inspiration        = get_field("exhibit_inspiration");
$exhibit_additional_images  = get_field("additional_exhibit_images");
$exhibit_social             = get_field("exhibit_social");
$exhibit_website            = get_field("exhibit_website");
$exhibit_cats               = wp_get_post_terms($exhibit_id, "mf-project-cat");

//Pull associated faire post
if(isset($faireData["faire_post"])){
    $faire_id   = $faireData["faire_post"];
    $faire_name = get_the_title($faire_id);
}else{
    $faire_id   = '';
    $faire_name = '';
}

$producerInfo    = get_field("producer_section", $faire_id);
//$faire_badge     = isset($producerSection['circular_faire_logo']['url']) ? $producerSection['circular_faire_logo']["sizes"]["thumbnail"]: "/wp-content/themes/makerfaire/images/default-badge-thumb.png";                    
//$project_info_bg = isset($faire_badge) ? "background-image:url(" . $faire_badge . ");" : "";
$project_info_bg = '';

$topSection 	 = get_field('top_section', $faire_id);
$faire_logo 	 = isset($topSection['horizontal_faire_logo']['url']) ? $topSection['horizontal_faire_logo']['sizes']['medium_large'] : ''; 
$faire_logo_alt	 = !empty($topSection['horizontal_faire_logo']['alt']) ? $topSection['horizontal_faire_logo']['alt'] : "Maker Faire " . $faire_name . " Logo"; 

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
   
    <header id="project-hero" class="hero-header" style="background-image:url('<?php echo $hero_bg; ?>');">
        <div class="hero-overlay"></div>
        <div class="logo-wrapper">
			<?php if(!empty($faire_logo)) { ?>
				<img id="faireLogo" src="<?php echo $faire_logo; ?>" alt="<?php echo $faire_logo_alt; ?>" />
			<?php } ?>
		</div>
    </header>

    <nav class="eoy-breadcrumbs">
        <a href="/yearbook/<?php echo $faire_year; ?>-faires">All Faires</a> / <a href="<?php echo get_permalink($faire_id); ?>">Faire Home</a> / <a href="/yearbook/<?php echo $faire_year; ?>-projects?_sfm_faire_information_faire_post=<?php echo $faire_id; ?>">Faire Projects</a>
    </nav>
    
    <section id="project-info-section" class="container">
        <div class="project-info" style="<?php echo $project_info_bg; ?>">
            <h3 class="faire-details"><a href="<?php echo get_permalink($faire_id); ?>">Maker Faire <?php echo $faire_name ." ".$faire_year;?></a></h3>
            <h1 class="project-title"><?php echo get_field("title");?></h1>
            <?php if(!empty($project_state) || !empty($project_country)){?> 
            <h4>Home: <?php
                echo (!empty($project_state) ? $project_state : "");                
                echo (!empty($project_state) && !empty($project_country) ? ', ':'');
                echo (!empty($project_country) ? $project_country : "");                
                ?>
            </h4>
            <?php } ?>
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
                ?>
			</div>  
            <?php if(!empty($exhibit_website)) { ?>
				    <a class="project-link" href="<?php echo $exhibit_website; ?>" target="_blank"><?php echo $exhibit_website; ?></a>
            <?php } ?>    
            <?php if(!empty($exhibit_cats)) { ?>
                <div class="project-categories"><b>Categories: </b>
                    <?php 
                    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
                    if($referer == "" || (str_contains($referer, "/yearbook/2023-projects") && !str_contains($referer, "_sfm_faire_information_faire_post"))) {
                        foreach($exhibit_cats as $category) { ?>
                            <a href="/yearbook/2023-projects/?_sft_mf-project-cat=<?php echo $category->slug;?>"><?php echo$category->name; ?></a><span>, </span>
                        <?php } 
                    } else {
                        foreach($exhibit_cats as $category) { ?>
                            <a href="/yearbook/2023-projects/?_sfm_faire_information_faire_post=<?php echo $faire_id; ?>&_sft_mf-project-cat=<?php echo $category->slug;?>"><?php echo$category->name; ?></a><span>, </span>
                        <?php } 
                    }
                    ?>
                </div>
            <?php } ?>     
        </div>
        <div class="project-picture">
            <?php if($thumbnail_id) { ?>
                <img class="featured-image" src="<?php echo $exhibit_photo;?>" alt="<?php echo $image_alt; ?>" />
            <?php } ?>
        </div>
    </section>
    
    <section id="project-highlights-section">
        <span class="striped-background"></span>
        <?php if($exhibit_video && is_valid_video($exhibit_video)) { ?>
            <div class="project-video">            
                <?php                 		
				if(str_contains($exhibit_video, "vimeo.com")) {
					echo $GLOBALS['wp_embed']->run_shortcode("[embed width='900px' height='auto']" . $exhibit_video . "[/embed]");
				} else { ?>
					<iframe src="<?php echo getYoutubeEmbedUrl($exhibit_video) . "?autoplay=1&mute=1"; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
				<?php }                    
                
                ?>
            </div>
        <?php } ?>
    </section>

    <section id="project-photos">
		<?php
			if( $exhibit_additional_images ) { ?>
			    <h2>Additional Project Photos</h2>
				<div id="highlightGallery">
					<?php foreach($exhibit_additional_images as $image) { 
                        if(isset($image['url'])){
                            $alt = ($image['alt'] != "") ? $image['alt'] : $project_title . " - " . $image['title']; ?>
                            <div class="gallery-item"><img alt="<?php echo $alt;?>"  src='<?php echo $image['sizes']['medium_large']; ?>' /></div>
                            <?php
                        }                        
					} 
                    /* for later if($photo_credit!=''){?>
						<span>Photo Credit: <?php echo $photo_credit;?></span>
					<?php } */ ?>
                </div>
			<?php } ?>
	</section>
    
    <section id="project-makers" class="container">
    
    <!-- Maker Data -->
    <?php
    if(!empty($maker_data)) { ?>
        <h2>Maker<?php echo (count($maker_data)>1?'s':'')?></h2>
        <div class="makers-wrapper <?php if(count($maker_data) == 1) { echo "single-maker"; }?>">
        <?php foreach($maker_data as $maker){ ?>
            <div class="maker-wrapper">
                <div class="img-wrap">                           
                    <?php if(isset($maker["maker_photo"]["url"])){ ?>
                        <img src="<?php echo $maker["maker_photo"]["sizes"]["medium"]; ?>" alt="<?php if(!empty($maker["maker_photo"]["alt"])) { echo $maker["maker_photo"]["alt"]; } else { echo $maker["maker_or_group_name"] . " Maker Photo"; } ?>" width="250px" height="250px" />
                    <?php } else { ?>
                        <img src="/wp-content/themes/makerfaire/images/default-makey.png" alt="Default Maker Photo" width="250px" height="250px">
                    <?php } ?>
                </div>
                <div class="maker-detail">
                    <h4><?php echo $maker["maker_or_group_name"];?></h4>
                    <p class="maker-bio show-more-snippet"><?php echo $maker["maker_bio"]?></p>
                    <div class="social-links reversed">
                        <?php 
                        if(!empty($maker['maker_social'])) {
                            foreach($maker['maker_social'] as $link) {
                                if($link) {
                                    echo('<a class="link" href="' . $link['maker_social_link'] . '"></a>');
                                }
                            } 
                        }
                        ?>
                    </div>
                    <?php if(!empty($maker["maker_website"])) { ?>
                        <a class="maker-link" href="<?php echo $maker["maker_website"]; ?>" target="_blank"><?php echo $maker["maker_website"]; ?></a>
                    <?php } ?>
                    </div>
            </div>
            <?php
        } ?>
        </div>
        <?php
    }		
    ?>
    </section>
    
    <?php
    if(!empty($exhibit_inspiration) ){ ?>
        <section id="project-inspiration-section">
            <span class="striped-background"></span>
                <div class="project-inspiration">
                    <?php
                        echo '<h3>What Inspired You to Make This?</h3>';
                        echo '<p>'.$exhibit_inspiration.'</p>';
                    ?>
                    <div class="blue-spacer"></div>
                </div>
        </section>
    <?php } ?>
    
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