<?php
/*
 * This is the public facing entry page view
 *
 */
$showcaseResults = showcase($entryId); // this will also tell us if this is a parent or child of a showcase
$showEditMakey = false;
?>
<main class="wrapper-fluid">
    <section id="topSection">
        <div class="big-column">
            <?php // if proj_photo_size is false, the project photo does not exist
            if(!$proj_photo_size) { ?>
                <picture class="exhibit-picture">
                    <img src="/wp-content/themes/makerfaire/images/default-featured-image.jpg"
                         alt="<?php echo $project_title; ?> project image" />
                </picture>
            <?php } elseif( isset($proj_photo_size[0]) && $proj_photo_size[0] > 900 ) { ?>
                <picture class="exhibit-picture">
                    <source media="(max-width: 420px)" srcset="<?php echo $project_photo_small; ?>">
                    <source media="(max-width: 1200px)" srcset="<?php echo $project_photo_medium; ?>">
                    <source media="(max-width: 1500px)" srcset="<?php echo legacy_get_resized_remote_image_url($project_photo_large, 840, 560); ?>">
                    <img src="<?php echo $project_photo_large; ?>" 
                         alt="<?php echo $project_title; ?> project image"
                         onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-featured-image.jpg';this.srcset=''"
                         data-photo="<?php echo $project_photo; ?>">
                </picture>
            <?php } elseif(isset($proj_photo_size[0]) && $proj_photo_size[0] > 420 ) { ?>
                <picture class="exhibit-picture small-picture">
                    <source media="(max-width: 420px)" srcset="<?php echo $project_photo_small; ?>">
                    <img src="<?php echo $project_photo_medium; ?>" 
                         alt="<?php echo $project_title; ?> project image"
                         onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-featured-image.jpg';this.srcset=''"
                         data-photo="<?php echo $project_photo; ?>">
                </picture>
            <?php } else { ?>
                <picture class="exhibit-picture small-picture">
                    <img src="<?php echo $project_photo_small; ?>" 
                         alt="<?php echo $project_title; ?> project image"
                         onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-featured-image.jpg';this.srcset=''"
                         data-photo="<?php echo $project_photo; ?>">
                </picture>
            <?php } ?>
        </div>
        <div class="small-column">
            <div class="small-column-wrapper">
                <div class="entry-box">
                    <h1 class="project-title"><?php echo $project_title; ?></h1>
                    <h3 class="faireName"><a href="/<?php echo $url_sub_path; ?>"><?php echo $faire_name;?></a></h3>
                    <h4 class="faireDate"><?php echo $faire_dates; ?></h4>
                    <div class="entry-box-items">
                        <?php if(isset($location) && trim($location) != '' && count(array_intersect($exhibit_type, array("Exhibit", "Sponsor", "Startup Sponsor"))) > 0) { ?><span class="entry-box-item" aria-label="Location"><i class="fa fa-map-signs" aria-hidden="true"></i><?php echo $location; ?></span><?php } ?>
                        <?php if(isset($friday) && $friday == 1 && count(array_intersect($exhibit_type, array("Exhibit", "Sponsor", "Startup Sponsor"))) > 0) { ?><span class="entry-box-item" aria-label="Calendar Detail"><i class="fa fa-calendar-days" aria-hidden="true"></i>Friday Only</span><?php } ?>
                        <?php if(!empty($exhibit_type)) { ?>
                            <span class="entry-box-item" aria-label="Exhibit Type"  >
                                <?php /* <a href="/<?php echo $url_sub_path; ?>/meet-the-makers/?type=<?php echo reset($exhibit_type); ?>"> */ ?>
                                    <i class="fa <?php echo strtolower(reset($exhibit_type)); ?>"></i>
                                    <?php echo implode(" & ",$exhibit_type); ?>
                                <?php /* </a> */ ?>
                            </span>
                        <?php } ?>
                        <?php if(isset($mainCategoryName) && $mainCategoryName != '') { ?><span class="entry-box-item" aria-label="Main Category"><a href="/<?php echo $url_sub_path; ?>/meet-the-makers/?category=<?php echo $mainCategoryName; ?>" class="icon-link"><?php echo $mainCategoryIcon; ?><span><?php echo $mainCategoryName; ?></span></a></span><?php } ?>
                        <?php if(!empty($ribbons)) { ?><span class="entry-box-item" aria-label="Ribbon"><a href="/ribbons/"><i class="fa fa-award" aria-hidden="true"></i>Ribbon Recipient</a></span><?php } ?>
                        <?php if($faire_end > date("Y-m-d j:i:s")) { ?>
                            <span class="entry-box-item" aria-label="Tickets"><a href="/<?php echo $url_sub_path; ?>/buy-tickets/"><i class="fa fa-ticket" aria-hidden="true"></i>Buy Tickets</a></span>
                        <?php } ?>
                    </div>
                    <?php if(isset($project_short) && $project_short != '') { ?>
                        <p class="project-description"><?php echo nl2br($project_short); 
                            if (isset($form['gv_id_update_public_info']) && $form['gv_id_update_public_info'] != '') {
                                if(strlen($project_short) < 200 && $makerEdit) { 
                                    $showEditMakey = true;
                                    ?>
                                    <span class="edit-message">Consider <a href="#" onclick="document.getElementById('edit-photos').click();return false;">editing</a> your Project Description to be at least 350 characters to help fillout your page better.</span>
                            <?php 
                                } 
                            }
                            ?>
                        </p>
                    <?php } ?>
                    
                </div>
                <?php
                if( $scheduleOutput != '' && $show_sched ) { ?>
                    <div class="entry-box">
                        <?php echo $scheduleOutput; ?>
                    </div>
                <?php
                }   
                if(!empty($video) && !empty($video2)) {
                    echo $video;  //project Video
                    echo $video2; //field386
                } 
                if(isset($project_gallery) && !empty($project_gallery)) { ?>
                    <div id="projectGallery" class="owl-carousel">
                    <?php foreach($project_gallery as $key=>$image) { 
                            if(isset($image) && $image!=''){
                                // if image doesn't exist @getimagesize will return false
                                $image_size = @getimagesize($image);
                                $image_default = $image;
                                $image_sources = array();
                                ?>
                                <picture class="gallery-item">
                                    <?php 
                                    // we never want the image to get upsized beyond it's initial size, and no reason to put sources above it size either.
                                    // sources have to be listed by size though, so we will put them into an array based on size to output later!
                                    if($image_size) {
                                        if($image_size[0] >= 200 && $image_size[1] >= 200) { 
                                            $image_default = legacy_get_resized_remote_image_url($image, 200, 200);
                                            $image_sources[0] = '<source media="(max-width: 450px)" srcset="'.legacy_get_resized_remote_image_url($image, 200, 200).'">';
                                        }
                                        if($image_size[0] >= 225 && $image_size[1] >= 225) { 
                                            $image_default = legacy_get_resized_remote_image_url($image, 225, 225);
                                            $image_sources[3] = '<source media="(max-width: 1200px)" srcset="'.legacy_get_resized_remote_image_url($image, 225, 225).'">';
                                        }
                                        if($image_size[0] >= 280 && $image_size[1] >=280) { 
                                            $image_default = legacy_get_resized_remote_image_url($image, 280, 280); 
                                            $image_sources[1] = '<source media="(max-width: 600px)" srcset="'.legacy_get_resized_remote_image_url($image, 280, 280).'">';
                                        } 
                                        if($image_size[0] >= 360 && $image_size[1] >= 360) { 
                                            $image_default = legacy_get_resized_remote_image_url($image, 360, 360);
                                            $image_sources[4] = '<source media="(max-width: 2000px)" srcset="'.legacy_get_resized_remote_image_url($image, 360, 360).'">';
                                        } 
                                        if($image_size[0] >= 380 && $image_size[1] >= 380) { 
                                            $image_default = legacy_get_resized_remote_image_url($image, 380, 380); 
                                            $image_sources[2] = '<source media="(max-width: 800px)" srcset="'.legacy_get_resized_remote_image_url($image, 380, 380).'">';
                                        } 
                                        // sort our image sources so they are in the right order to display (lowest to highest)
                                        ksort($image_sources);
                                        foreach($image_sources as $source) {
                                            echo ($source);
                                        }
                                    } 
  
                                    ?>

                                    <img src="<?php echo $image_default; ?>" 
                                        alt="<?php echo $project_title;?> - exhibit detail <?php echo $key;?>"
                                        onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-gallery-image.jpg';this.srcset=''"
                                        data-photo="<?php echo $image; ?>">
                                </picture>
                            <?php } ?>
                    <?php } ?>
                    </div>
                <?php } else if($makerEdit && empty($video) && empty($video2)) { 
                    $showEditMakey = true;
                    ?>
                    <span class="edit-message">Please <a href="#" onclick="document.getElementById('edit-photos').click();return false;">edit your project</a> to add additional photos or a video.</span>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php if ($dispMakerInfo && $showcase != 'parent') { ?>
        <section id="makerInfo" class="makers-<?php echo count($makers); ?>">
            <?php if(count($makers) > 1) {  
                foreach($makers as $maker) { ?>
                    <div class='entry-box'>
                        <img src='<?php echo(legacy_get_resized_remote_image_url($maker['photo'], 400, 400)); ?>' 
                            alt='<?php echo $maker['firstname'].' '.$maker['lastname']; ?> Maker Picture'
                            onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-makey-medium.jpg?v=1';this.srcset=''" />
                        <h3><?php echo($maker['firstname'] . " " . $maker['lastname']); ?></h3>
                        <p class="maker-description"><?php echo($maker['bio']); ?></p>
                        <?php if(!empty($maker['website'])) { ?>
                            <a class="maker-website" href="<?php echo($maker['website']); ?>" target="_blank"><?php echo($maker['website']); ?></a>                     
                        <?php } ?> 
                        <?php if($maker['social'] != '<span class="social-links reversed"></span>') {                    
                                echo $maker['social'];
                              } 
                        ?> 
                    </div>
                <?php } 
            } else if( $makers  ) { 
                $maker = current($makers);
                $small_photo = isset($maker['photo']) ? legacy_get_resized_remote_image_url($maker['photo'], 400, 400) : "";
                $large_photo = isset($maker['photo']) ? legacy_get_resized_remote_image_url($maker['photo'], 760, 760) : "";
                if($maker['firstname'] != '' || $maker['photo'] != '') {
            ?>
                <div class="small-column">
                    <picture>
                        <source media="(max-width: 420px)" srcset="<?php echo  $small_photo; ?>">
                        <img src="<?php echo $large_photo ?>" 
                             alt="<?php echo $maker['firstname'].' '.$maker['lastname']; ?> Maker Picture"
                             onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-makey-large.jpg?v=1';this.srcset=''" />
                    </picture>
                </div>
                <div class="big-column">
                    <h2><?php echo($maker['firstname'] . " " . $maker['lastname']); ?></h2>
                    <p class="maker-description"><?php echo($maker['bio']);
                    if (isset($form['gv_id_update_public_info']) && $form['gv_id_update_public_info'] != '') {
                        if(strlen($maker['bio']) < 200 && $makerEdit) { 
                            $showEditMakey = true;
                            ?>
                            <span class="edit-message">Consider <a href="#" onclick="document.getElementById('edit-photos').click();return false;">editing</a> your Bio or Group/Company description to be at least 200 characters to help fillout your page better.</span>                        
                            <?php 
                        } 
                    }   ?>
                </div>
            <?php 
                }
            } ?>              
        </section>
    <?php } ?>  

    <?php 
    // displays the showcase parent if it's one of the makers in the showcase, and the makers in the showcase with the showcase maker info under if it's the showcase parent themselves
    if($showcase != '') {
        echo $showcaseResults;
    }
    ?>

    <?php if($showcase != 'parent') { // we're not showing this section for showcase makers?>
        <section id="bottomSection">
            <?php 
            if(count($makers) == 1) { 
                $maker = current($makers); 
                if(!empty($maker['website']) || $maker['social'] != '<span class="social-links reversed"></span>') { ?>
                <div class="entry-box">
                    <h4>More Maker Info</h4>
                    <?php if(!empty($maker['website'])) { ?>
                        <a class="maker-website" href="<?php echo($maker['website']); ?>" target="_blank"><?php echo($maker['website']); ?></a>
                    <?php } ?>  
                    <?php if($maker['social'] != '<span class="social-links reversed"></span>') { 
                        echo $maker['social'];                   
                        } ?> 
                </div>
            <?php }
            } ?>  
            <div class="entry-box">
                <h4>More Event Info</h4>
                <div class="entry-box-items">
                    <?php if(isset($mainCategoryName ) && $mainCategoryName  != '') { ?>
                        <span class="entry-box-item"><a href="/<?php echo $url_sub_path; ?>/meet-the-makers/?category=<?php echo $mainCategoryName; ?>" class="icon-link"><?php echo $mainCategoryIcon; ?><span>See All <?php echo $mainCategoryName; ?></span></a></span>
                    <?php } ?>
                    <?php if($show_sched ){ ?>
                        <span class="entry-box-item"><i class="fa fa-calendar-days"></i><a href="/<?php echo $url_sub_path; ?>/schedule/">Event Schedule</a></span>
                    <?php } ?>
                    <span class="entry-box-item"><i class="fa fa-tools"></i><a href="/<?php echo $url_sub_path; ?>/meet-the-makers/">See All Makers</a></span>
                </div>
            </div>
            <?php if((!empty($project_website) && !empty($project_social))) { ?>
                <div class="entry-box">
                    <h4>More Project Info</h4>
                    <?php if(!empty($project_website)) { ?>
                        <a class="maker-website" href="<?php echo($project_website); ?>" target="_blank"><?php echo($project_website); ?></a>                    
                    <?php } ?> 
                    <?php if($project_social != '<span class="social-links reversed"></span>') { 
                        echo $project_social;                    
                        } ?> 
                </div>
            <?php } ?>  
        </section>
    <?php } ?>  

    <?php if($showEditMakey == true) { ?>
        <a id="editMakey" href="#" onclick="document.getElementById('edit-photos').click();return false;">
            <img src="/wp-content/themes/makerfaire/images/more-info-makey.png" width="203px" height="254px" alt="Edit your entry!" title="Click here to edit your entry details to make this page the best it can be!" />
        </a>
    <?php } ?>
    
    <?php
    /* <section id="sponsorSection">
        <?php 
            $slideshowShortcode = "[sponsor_slideshow faire_id=" . $faireShort . " url=https://" . $_SERVER['SERVER_NAME'] . "/" . $url_sub_path . "/sponsors/]";
            echo(do_shortcode($slideshowShortcode)); 
        ?>
    </section> */ ?>

</main>