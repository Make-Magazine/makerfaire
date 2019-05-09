<?php
/*
 * This is the public facing entry page view
 *
 */

?>
<div class="container">
   <div class="row">
      <div class="col-md-8 col-sm-12 col-xs-12" id="viewEntry">
         <!-- Project Title and ribbons -->
         <?php echo $ribbons; ?>
         <div class="entry-header">

            <div class="entry-type">
               <?php if($displayFormType == true) {  echo $formType; } ?>
            </div>

            <h1>
               <span id="project_title"><?php echo $project_title; ?></span>
            </h1>

         </div>
         <?php
         $url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
         echo do_shortcode('[easy-social-share buttons="facebook,pinterest,reddit,twitter,linkedin,love,more"  morebutton_icon="dots" morebutton="2" counters=1 counter_pos="bottom" total_counter_pos="hidden" style="icon" fullwidth="yes" template="metro-retina" postid="' . $entryId . '" url="' . $url . '" text="' . $project_title . '"]');
         ?>

         <!-- Project Image -->
         <p id="proj_img">
            <img class="img-responsive dispPhoto lazyload" src="<?php echo $project_photo; ?>" />
         </p>

         <!-- Project Short Description -->
         <p id="project_short" class="lead"><?php echo nl2br($project_short); ?></p>

         <?php
         echo $video;    //project Video
         if (!empty($project_website)) {
            ?> <a href="<?php echo $project_website; ?>" class="btn universal-btn entry-website"target="_blank" >Project Website</a><?php          
         }
         if ($categoryDisplay) {
            ?><div class="entry-categories"><?php echo $categoryDisplay; ?></div><?php          
         }

         if (display_group($entryId)) {
            ?><div class="group-entry"><?php echo display_group($entryId); ?></div><?php
         }
         if (display_groupEntries($entryId)) {
            ?><div class="group-entries"><?php echo display_groupEntries($entryId); ?></div><?php 
         } ?>
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
            <?php } ?>
         </div>


         <div class="sidebar-type"> <!-- Maker/Group/Worskhop etc -->
                  <?php if ($dispMakerInfo) { ?>
               <div class="entry-header">
                  <h2><?php
                     if ($isGroup) {
                        echo 'Group';
                     } elseif ($isList) {
                        echo 'Makers';
                     } else {
                        echo 'Maker';
                     }
                     ?>
                  </h2>
               </div>
   <?php
   if ($isGroup) {
      ?>
                  <div class="entry-page-maker-info">
                     <div class="row padbottom">
                        <div class="col-xs-12">
                           <h3 class="text-capitalize" id="groupname"><?php echo $groupname; ?></h3>
                        </div>
                        <div class="col-xs-6">
                           <div class="entry-page-maker-img">
                              <img class="img-responsive lazyload" src="<?php echo (!empty($groupphoto) ? legacy_get_resized_remote_image_url($groupphoto, 400, 400) : get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg' ); ?>" />
                           </div>
                        </div>
                        <div class="col-xs-6">
      <?php echo $groupsocial; ?>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-xs-12"><p id="groupbio"><?php echo $groupbio; ?></p></div>
                     </div>
                  </div>
                  <?php
               } else {
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
                              <div class="col-xs-6">
                                 <div class="entry-page-maker-img">
                                    <img class="img-responsive lazyload" src="<?php echo (!empty($maker['photo']) ? legacy_get_resized_remote_image_url($maker['photo'], 400, 400) : get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg' ); ?>" />
                                 </div>
                              </div>
                              <div class="col-xs-6">
            <?php echo $maker['social']; ?>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-xs-12"><p><?php echo $maker['bio']; ?></p></div>
                           </div>
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
</div>

<div class="entry-footer">
<?php echo displayEntryFooter(); ?>
</div>