<?php
/*  Page layout for the Yearbook Individual Project Pages */

//Pull faire specific information
$faireData       = get_field("faire_information");
$faire_year      = (isset($faireData["faire_year"]) ? $faireData["faire_year"] : '');

//Project Information
$project_title   = get_the_title();
$thumbnail_id    = get_post_thumbnail_id();

//check if there is a featured image set
if ($thumbnail_id) {
    $image_src = wp_get_attachment_image_src($thumbnail_id, 'large');
    $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $image_alt = !empty($image_alt) ? $image_alt : $project_title . " Project Image for Maker Faire " . $faire_name . " " . $faire_year;
}

$exhibit_id                 = get_the_ID();
$exhibit_photo              = (isset($image_src[0]) ? $image_src[0] : '');
$exhibit_video              = get_field("exhibit_video_link");
$exhibit_inspiration        = get_field("exhibit_inspiration");
$exhibit_additional_images  = get_field("additional_exhibit_images");
$exhibit_social             = get_field("exhibit_social");
$exhibit_website            = get_field("exhibit_website");
$exhibit_cats               = wp_get_post_terms($exhibit_id, "mf-project-cat");

//Pull associated faire post
if (isset($faireData["faire_post"])) {
    $faire_id   = $faireData["faire_post"];
    $faire_name = get_the_title($faire_id);
} else {
    $faire_id   = '';
    $faire_name = '';
}

$producerInfo    = get_field("producer_section", $faire_id);
//$faire_badge     = isset($producerSection['circular_faire_logo']['url']) ? $producerSection['circular_faire_logo']["sizes"]["thumbnail"]: "/wp-content/themes/makerfaire/images/default-badge-thumb.png";                    
//$project_info_bg = isset($faire_badge) ? "background-image:url(" . $faire_badge . ");" : "";
$project_info_bg = '';

$topSection      = get_field('top_section', $faire_id);
$faire_logo      = isset($topSection['horizontal_faire_logo']['url']) ? $topSection['horizontal_faire_logo']['sizes']['medium_large'] : '';
$faire_logo_alt     = !empty($topSection['horizontal_faire_logo']['alt']) ? $topSection['horizontal_faire_logo']['alt'] : "Maker Faire " . $faire_name . " Logo";

//Project Location
$project_location = get_field('project_location');
$project_state    = (isset($project_location['state'])   ? $project_location['state'] : '');
$project_country  = (isset($project_location['country']) ? $project_location['country'] : '');

//Maker Data
$maker_data = get_field("maker_data");

?>
<?php get_header(); ?>
<article>
    <nav class="eoy-breadcrumbs">
        <a href="/yearbook/<?php echo $faire_year; ?>-faires">All Faires</a> / <a href="<?php echo get_permalink($faire_id); ?>">Faire Home</a> / <a href="/yearbook/<?php echo $faire_year; ?>-projects?_sfm_faire_information_faire_post=<?php echo $faire_id; ?>">Faire Projects</a>
    </nav>
    <div class="logo-wrapper">
        <?php if (!empty($faire_logo)) { ?>
            <img id="faireLogo" src="<?php echo $faire_logo; ?>" alt="<?php echo $faire_logo_alt; ?>" />
        <?php } ?>
    </div>

    <section id="project-info-section" class="container">
        <div class="project-info" style="<?php echo $project_info_bg; ?>">
            <h3 class="faire-details"><a href="<?php echo get_permalink($faire_id); ?>">Maker Faire <?php echo $faire_name . " " . $faire_year; ?></a></h3>
            <h1 class="project-title"><?php echo get_field("title"); ?></h1>
            <?php if (!empty($project_state) || !empty($project_country)) { ?>
                <h4>Home: <?php
                            echo (!empty($project_state) ? $project_state : "");
                            echo (!empty($project_state) && !empty($project_country) ? ', ' : '');
                            echo (!empty($project_country) ? $project_country : "");
                            ?>
                </h4>
            <?php } ?>
            <div class="blue-spacer"></div>
            <p><?php echo html_entity_decode(get_field("exhibit_description")); ?></p>
            <div class="social-links reversed">
                <?php if (!empty($exhibit_social)) {
                    foreach ($exhibit_social as $social) {
                        if ($social['social_url'] && filter_var($social['social_url'], FILTER_VALIDATE_URL)) {
                            echo ('<a class="link" href="' . $social['social_url'] . '"></a>');
                        }
                    }
                }
                ?>
            </div>
            <?php if (!empty($exhibit_website) && filter_var($exhibit_website, FILTER_VALIDATE_URL)) { ?>
                <a class="project-link" href="<?php echo $exhibit_website; ?>" target="_blank"><?php echo $exhibit_website; ?></a>
            <?php } ?>
            <?php if (!empty($exhibit_cats)) { ?>
                <div class="project-categories"><b>Categories: </b>
                    <?php
                    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
                    if ($referer == "" || (str_contains($referer, "/yearbook/".$faire_year."-projects") && !str_contains($referer, "_sfm_faire_information_faire_post"))) {
                        foreach ($exhibit_cats as $category) { ?>
                            <a href="/yearbook/<?php echo $faire_year;?>-projects/?_sft_mf-project-cat=<?php echo $category->slug; ?>"><?php echo $category->name; ?></a><span>, </span>
                        <?php }
                    } else {
                        foreach ($exhibit_cats as $category) { ?>
                            <a href="/yearbook/<?php echo $faire_year;?>-projects/?_sfm_faire_information_faire_post=<?php echo $faire_id; ?>&_sft_mf-project-cat=<?php echo $category->slug; ?>"><?php echo $category->name; ?></a><span>, </span>
                    <?php }
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
        <div class="project-picture">
            <?php if ($thumbnail_id) { ?>
                <img class="featured-image" src="<?php echo $exhibit_photo; ?>" alt="<?php echo $image_alt; ?>" />
            <?php } ?>
        </div>
    </section>

    <section id="project-highlights-section">
        <span class="striped-background"></span>
        <?php if($exhibit_video && filter_var($exhibit_video, FILTER_VALIDATE_URL) && is_valid_video($exhibit_video)) { ?>
            <div class="project-video">            
                <?php                 		
                    if(stripos($exhibit_video,'vimeo.com') !== false) {                    
                        echo do_shortcode("[vimeo " . $exhibit_video . "]");
                    } elseif(stripos($exhibit_video,'instagram.com') !== false) { ?>				
                        <blockquote class="instagram-media" data-instgrm-permalink="<?php echo $exhibit_video; ?>?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style="background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="<?php echo $exhibit_video; ?>'?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="<?php echo $exhibit_video; ?>?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank"></a></p></div></blockquote>
                        <script async src="//www.instagram.com/embed.js"></script>
                    <?php } elseif(stripos($exhibit_video,'facebook.com') !== false) {
                        $facebook_code = getFacebookVideoId($exhibit_video); ?>
                        <iframe src="https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2F61558192734819%2Fvideos%2F<?php echo $facebook_code; ?>%2F&show_text=false&width=267&t=0" width="267" height="476" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>
                    <?php } elseif(stripos($exhibit_video,'tiktok.com') !== false) { 
                        $tiktok_code = getTikTokVideoId($exhibit_video); ?>
                        <blockquote class="tiktok-embed" data-video-id="<?php echo $tiktok_code; ?>" style="max-width: 605px;min-width: 325px;" > 
                        <section data-warning="Don't Remove This"></section> 
                        </blockquote> <script async src="https://www.tiktok.com/embed.js"></script>
                    <?php } else { ?>
                        <iframe width="560" height="315" src="<?php echo getYoutubeEmbedUrl($exhibit_video) . "?autoplay=1&mute=1"; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <?php }               
                ?>
            </div>
        <?php } ?>
    </section>

    <section id="project-photos">
        <?php
        if ($exhibit_additional_images) { ?>
            <h2>Additional Project Photos</h2>
            <div id="highlightGallery">
                <?php foreach ($exhibit_additional_images as $image) {
                    if (isset($image['url'])) {
                        $alt = ($image['alt'] != "") ? $image['alt'] : $project_title . " - " . $image['title']; ?>
                        <div class="gallery-item"><img alt="<?php echo $alt; ?>" src='<?php echo $image['sizes']['medium_large']; ?>' /></div>
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
        if (!empty($maker_data)) { ?>
            <h2>Maker<?php echo (count($maker_data) > 1 ? 's' : '') ?></h2>
            <div class="makers-wrapper <?php if (count($maker_data) == 1) {
                                            echo "single-maker";
                                        } ?>">
                <?php foreach ($maker_data as $maker) { ?>
                    <div class="maker-wrapper">
                        <div class="img-wrap">
                            <?php if (isset($maker["maker_photo"]["url"])) { ?>
                                <img src="<?php echo $maker["maker_photo"]["sizes"]["medium"]; ?>" alt="<?php if (!empty($maker["maker_photo"]["alt"])) {
                                                                                                            echo $maker["maker_photo"]["alt"];
                                                                                                        } else {
                                                                                                            echo $maker["maker_or_group_name"] . " Maker Photo";
                                                                                                        } ?>" width="250px" height="250px" />
                            <?php } else { ?>
                                <img src="/wp-content/themes/makerfaire/images/default-makey.png" alt="Default Maker Photo" width="250px" height="250px">
                            <?php } ?>
                        </div>
                        <div class="maker-detail">
                            <h4><?php echo $maker["maker_or_group_name"]; ?></h4>
                            <p class="maker-bio show-more-snippet"><?php echo $maker["maker_bio"] ?></p>
                            <div class="social-links reversed">
                                <?php
                                if (!empty($maker['maker_social'])) {
                                    foreach ($maker['maker_social'] as $link) {
                                        if ($link && filter_var($link['maker_social_link'], FILTER_VALIDATE_URL)) {
                                            echo ('<a class="link" href="' . $link['maker_social_link'] . '"></a>');
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php if (!empty($maker["maker_website"]) && filter_var($maker["maker_website"], FILTER_VALIDATE_URL)) { ?>
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
    if (!empty($exhibit_inspiration)) { ?>
        <section id="project-inspiration-section">
            <span class="striped-background"></span>
            <div class="project-inspiration">
                <?php
                echo '<h3>What Inspired You to Make This?</h3>';
                echo '<p>' . $exhibit_inspiration . '</p>';
                ?>
                <div class="blue-spacer"></div>
            </div>
        </section>
    <?php } ?>

</article><!--Container-->
<script>
    jQuery(document).ready(function() {
        var numImages = jQuery('#highlightGallery .gallery-item').length;
        jQuery('#highlightGallery .gallery-item').on("click", function() {
            //every time you click on a gallery item, open a dialog modal to show the images
            var galleryItem = jQuery(this);
            jQuery('body').append('<div id="dialog"><img src="' + jQuery("img", this).attr('src') + '" width="100%" /></div>');
            jQuery('#dialog').dialog({
                dialogClass: "hide-heading",
                modal: true,
                // these buttons will go to the next image from the faireGallery and replace the src of the image in the modal with the next or previous image in the gallery
                buttons: numImages <= 1 ? [] : [{
                        "class": "dialog-nav-btn dialog-prev-btn",
                        click: function() {
                            if (galleryItem.prev(".gallery-item").children("img").attr("src")) {
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
                            if (galleryItem.next(".gallery-item").children("img").attr("src")) {
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
                    jQuery('.ui-widget-overlay').bind('click', function() {
                        jQuery("#dialog").dialog('close');
                    });
                }
            });
        });
    });
</script>
<?php get_footer(); ?>