<?php
/*
 * This is the public facing entry page view
 *
 */
?>

<main class="wrapper-fluid">
    <section id="topSection">
        <div class="big-column">
            <?php if($proj_photo_size[0] > 900 ) { ?>
                <picture class="exhibit-picture">
                    <img srcset="<?php echo $project_photo_small; ?> 420w, <?php echo $project_photo_medium; ?> 760w, <?php echo $project_photo_large; ?> 1199w" 
                        sizes="(max-width: 420px) 420px, (max-width: 760px) 760px, (max-width: 1199px) 1199px, 1200px" 
                        src="<?php echo $project_photo_small; ?>" alt="<?php echo $project_title; ?> project image" 
                        onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-featured-image.jpg';this.srcset=''" />
                </picture>
            <?php } else { ?>
                <picture class="exhibit-picture small-picture">
                    <img class="small-image" width="<?php echo $proj_photo_size[0]; ?>" height="<?php echo $proj_photo_size[1]; ?>"
                        srcset="<?php echo $project_photo_small; ?> 420w, <?php echo $project_photo_medium; ?> 760w" 
                        sizes="(max-width: 420px) 420px, (max-width: 760px) 760px, 761px" 
                        src="<?php echo $project_photo_small; ?>" alt="<?php echo $project_title; ?> project image" 
                        onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-featured-image.jpg';this.srcset=''" />
                </picture>
            <?php } ?>
        </div>
        <div class="small-column">
            <div class="small-column-wrapper">
                <div class="entry-box">
                    <h1 class="project-title"><?php echo $project_title; ?></h1>
                    <h3 class="faireName"><a href="/<?php echo $url_sub_path; ?>"><?php echo ucwords(str_replace('-', ' ', $faire));?></a></h3>
                    <div class="entry-box-items">
                        <?php if(isset($location) && trim($location) != '' && count(array_intersect($exhibit_type, array("Exhibit", "Sponsor", "Startup Sponsor"))) > 0) { ?><span class="entry-box-item"><i class="fa fa-map-signs" aria-label="Location"></i><?php echo $location; ?></span><?php } ?>
                        <?php if(isset($friday) && $friday == 1 && count(array_intersect($exhibit_type, array("Exhibit", "Sponsor", "Startup Sponsor"))) > 0) { ?><span class="entry-box-item"><i class="fa fa-calendar" aria-label="Calendar Detail"></i>Friday Only</span><?php } ?>
                        <?php if(!empty($exhibit_type)) { ?><span class="entry-box-item"><i class="fa fa-check" aria-label="Exhibit Type"></i><?php echo implode(" & ",$exhibit_type); ?></span><?php } ?>
                        <?php if(isset($mainCategory ) && $mainCategory  != '') { ?><span class="entry-box-item"><i class="fa fa-rocket" aria-label="Main Category"></i><?php echo $mainCategory ; ?></span><?php } ?>
                        <?php if(!empty($ribbons)) { ?><span class="entry-box-item"><i class="fa fa-award" aria-label="Ribbon"></i>Ribbon Recipient</span><?php } ?>
                    </div>
                    <p class="project-description"><?php echo nl2br($project_short); ?></p>
                </div>
                <?php
                if( $scheduleOutput != '' && $show_sched ) { ?>
                    <div class="entry-box">
                        <?php echo $scheduleOutput; ?>
                    </div>
                <?php
                    }   
                    echo $video;  //project Video
                    echo $video2; //field386
                ?>
                <?php if(isset($project_gallery) && !empty($project_gallery)) { ?>
                    <div id="projectGallery" class="owl-carousel">
                    <?php foreach($project_gallery as $key=>$image) { 
                            if($image!=''){?>
                                <div class="gallery-item"><img alt="<?php echo $project_title;?> - exhibit detail <?php echo $key;?>"  src='<?php echo legacy_get_fit_remote_image_url($image, 750, 500); ?>' /></div>
                            <?php } ?>
                    <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php if ($dispMakerInfo) { ?>
        <section id="makerInfo" class="makers-<?php echo count($makers); ?>">
            <?php if(count($makers) > 1) {  
                foreach($makers as $maker) { ?>
                    <div class='entry-box'>
                        <img src='<?php echo(legacy_get_resized_remote_image_url($maker['photo'], 300, 300)); ?>' 
                            alt='<?php echo $maker['firstname'].' '.$maker['lastname']; ?> Maker Picture'
                            onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-makey-medium.png';this.srcset=''" />
                        <h3><?php echo($maker['firstname'] . " " . $maker['lastname']); ?></h3>
                        <p class="maker-description"><?php echo($maker['bio']); ?></p>
                        <a class="maker-website" href="<?php echo($maker['website']); ?>" target="_blank"><?php echo($maker['website']); ?></a>
                        <?php echo $maker['social']; ?>
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
                        <img srcset="<?php echo $small_photo; ?> 420w, <?php echo $large_photo; ?> 760w, <?php echo $large_photo; ?>" 
                            sizes="(max-width: 420px) 420px, (max-width: 760px) 760px, 1200px" 
                            src="<?php echo $large_photo ?>" 
                            alt="<?php echo $maker['firstname'].' '.$maker['lastname']; ?> Maker Picture"
                            onerror="this.onerror=null;this.src='/wp-content/themes/makerfaire/images/default-makey-large.png';this.srcset=''" />
                    </picture>
                </div>
                <div class="big-column">
                    <h2><?php echo($maker['firstname'] . " " . $maker['lastname']); ?></h2>
                    <p class="maker-description"><?php echo($maker['bio']); ?>
                </div>
            <?php }
            } ?>              
        </section>
    <?php } ?>  

    <?php 
    $displayGroup = display_group($entryId);
    if($displayGroup) { 
        echo $displayGroup;
    } 
    ?>

    <section id="bottomSection">
        <?php if(count($makers) == 1 && !empty($maker['website']) && !empty($maker['social'])) { 
            $maker = current($makers); ?>
            <div class="entry-box">
                <h4>More Maker Info</h4>
                <?php if(!empty($maker['website'])) { ?>
                    <a class="maker-website" href="<?php echo($maker['website']); ?>" target="_blank"><?php echo($maker['website']); ?></a>
                <?php } ?>  
                <?php echo $maker['social']; ?>
            </div>
        <?php } ?>  
        <div class="entry-box">
            <h4>More Event Info</h4>
            <div class="entry-box-items">
                <?php if(isset($mainCategory ) && $mainCategory  != '') { ?>
                    <span class="entry-box-item"><i class="fa fa-rocket"></i><a href="/<?php echo $url_sub_path; ?>/meet-the-makers/?category=<?php echo $mainCategory; ?>">Category</a></span>
                <?php } ?>
                <span class="entry-box-item"><i class="fa fa-calendar"></i><a href="/<?php echo $url_sub_path; ?>/schedule/">Event Schedule</a></span>
                <span class="entry-box-item"><i class="fa fa-tools"></i><a href="/<?php echo $url_sub_path; ?>/meet-the-makers/">See All Makers</a></span>
            </div>
        </div>
        <?php if(!empty($project_website) && !empty($project_social)) { ?>
            <div class="entry-box">
                <h4>More Project Info</h4>
                <?php if(!empty($project_website)) { ?>
                    <a class="maker-website" href="<?php echo($project_website); ?>" target="_blank"><?php echo($project_website); ?></a>
                <?php } ?>  
                <?php echo $project_social; ?>
            </div>
        <?php } ?>  
    </section>

    <section id="sponsorSection">
        <?php 
            $slideshowShortcode = "[sponsor_slideshow faire_id=" . $faireShort . " url=https://" . $_SERVER['SERVER_NAME'] . "/" . $url_sub_path . "/sponsors/]";
            echo(do_shortcode($slideshowShortcode)); 
        ?>
    </section>

</main>