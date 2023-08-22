<?php
/*
 * This is the public facing entry page view
 *
 */
?>
<div class="container-fluid">
    <div class="row flex-row">
        <div class="col-md-8 col-sm-12 col-xs-12" id="viewEntry">
            <!-- Project Title and ribbons -->
            <?php echo $ribbons; ?>
            <div class="entry-header">

                <div class="entry-type">
                    <?php
                    if ($displayFormType == true) {
                        echo $formType;
                    }
                    ?>
                </div>

                <h1>
                    <span id="project_title"><?php echo $project_title; ?></span>
                </h1>

            </div>

            <!-- Project Image -->
            <p id="proj_img">
                <img class="img-responsive dispPhoto" src="<?php echo $project_photo; ?>" />
            </p>

            <!-- Project Short Description -->
            <div id="project_short" class="lead">
                <p><?php echo nl2br($project_short); ?></p>
                <?php if (isset($field_287) && $field_287 != '') { ?>
                    <p><b><?php echo $label_287; ?>:</b><br/><?php echo nl2br($field_287); ?></p>
                <?php } ?>
                <?php if (isset($field_877) && $field_877 != '') { ?>
                    <p><b><?php echo $label_877; ?>:</b><br/><?php echo nl2br($field_877); ?></p>
                <?php } ?>
            </div>
            <?php
            if (!empty($project_website)) {
                ?> <a href="<?php echo $project_website; ?>" class="btn universal-btn entry-website"target="_blank" >Project Website</a><?php
            }

            if (display_group($entryId)) {
                ?><div class="group-entry"><?php echo display_group($entryId); ?></div><?php
            }
            if (display_groupEntries($entryId)) {
                ?><div class="group-entries"><?php echo display_groupEntries($entryId); ?></div>
            <?php }
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            echo do_shortcode('[easy-social-share buttons="facebook,pinterest,reddit,twitter,linkedin,love,more" morebutton_icon="dots" morebutton="2" counters="yes" counter_pos="after" total_counter_pos="hidden" animation="essb_icon_animation6" style="icon" fullwidth="yes" template="6" postid="' . $entryId . '" url="' . $url . '" text="' . $project_title . '"]');
            ?>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12" id="entrySidebar">
            <div class="entryInfo">
                <?php
                //display schedule/location information if there is any
                echo display_entry_schedule($entryId);
                if (!empty($handsOn)) {
                    echo $handsOn;
                }
                if (!empty($registerLink)) {
                    ?>
                    <a href="<?php echo $registerLink; ?>" class="btn universal-btn-red" style="margin-top:10px;">Register Here</a>
                    <?php
                }
                if (!empty($viewNow)) {
                    ?>
                    <a href="<?php echo $viewNow; ?>" class="btn universal-btn-red" style="margin-top:10px;">Watch Live</a>
                <?php } ?>
            </div>


            <div class="sidebar-type"> <!-- Maker/Group/Worskhop etc -->
                <?php if ($dispMakerInfo) { ?>
                    <?php
                    if ($isGroup) {
                        ?>
                        <div class="entry-header"><h2>GROUP</h2></div>
                        <div class="entry-page-maker-info">
                            <div class="row padbottom">
                                <div class="col-xs-12">
                                    <h3 class="text-capitalize" id="groupname"><?php echo $groupname; ?></h3>
                                </div>
                                <div class="col-xs-12">
                                    <div class="entry-page-maker-img">
                                        <img class="img-responsive" src="<?php echo (!empty($groupphoto) ? legacy_get_fit_remote_image_url($groupphoto, 400, 400) : get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg' ); ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12"><p id="groupbio"><?php echo $groupbio; ?></p></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12"><?php echo $groupsocial; ?></div>
                            </div>
                        </div>
                        <?php
                    } else {
                        if (count($makers) == 1) {
                            echo '<div class="entry-header"><h2>MAKER</h2></div>';
                        } else {
                            echo '<div class="entry-header"><h2>MAKERS</h2></div>';
                        }
                        foreach ($makers as $key => $maker) {
                            if ($maker['firstname'] != '') {
                                ?>
                                <div class="entry-page-maker-info">
                                    <div class="row padbottom">
                                        <div class="col-xs-12">
                                            <h3>
                                                <span class="text-capitalize"><?php echo $maker['firstname']; ?></span>
                                                <span class="text-capitalize"><?php echo $maker['lastname']; ?></span>
                                            </h3>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="entry-page-maker-img">
                                                <img class="img-responsive" src="<?php echo (!empty($maker['photo']) ? legacy_get_resized_remote_image_url($maker['photo'], 400, 400) : get_stylesheet_directory_uri() . '/images/makey-profile-default.png' ); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12"><p><?php echo $maker['bio']; ?></p></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12"><?php echo $maker['social']; ?></div>
                                    </div>
                                    <?php
                                    if (!empty($maker['website'])) {
                                        ?>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <a href="<?php echo $maker['website']; ?>" class="btn universal-btn entry-website"target="_blank" >Maker Website</a>
                                            </div>
                                        </div>
                                        <?php }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                    }
                } 
                ?>
            </div>
        </div>  <!-- END SIDEBAR -->
    </div>
    <div class="row">
        <div id="entryFullWidth">
            <?php if(isset($project_gallery)) { ?>
                <div id="projectGallery">
                <?php foreach($project_gallery as $image) { ?>
                    <div class="gallery-item"><img src='<?php echo legacy_get_fit_remote_image_url($image, 750, 500); ?>' /></div>
                <?php } ?>
                </div>
            <?php } 
                echo $video;  //project Video
                echo $video2; //field386
                if ($categoryDisplay) {
                    ?><div class="entry-categories"><?php echo $categoryDisplay; ?></div><?php
                }
            ?>
        </div>
    </div>
</div>

<div class="entry-footer">
    <?php echo displayEntryFooter(); ?>
</div>
