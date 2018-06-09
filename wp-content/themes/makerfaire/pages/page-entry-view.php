<?php

/*
 * This is the public facing entry page view
 *
 */

//display schedule/location information if there is any
  if (!empty(display_entry_schedule($entryId))) {
    display_entry_schedule($entryId);
  }
  ?>
<div id="viewEntry">
  <!-- Project Title and ribbons -->
  <div class="page-header">
    <h1>
      <span id="project_title"><?php echo $project_title; ?></span>
      <?php echo $ribbons;?>
    </h1>
  </div>

  <!-- Project Image -->
  <p id="proj_img">
    <img class="img-responsive dispPhoto" src="<?php echo $project_photo; ?>" />
  </p>

  <!-- Project Short Description -->
  <p id="project_short" class="lead"><?php echo nl2br($project_short); ?></p>

  <?php
  echo $website;  //project Website
  echo $video;    //project Video
  ?>

  <!-- Maker Info -->
  <div class="entry-page-maker-info">
  <?php
    if($dispMakerInfo) { ?>
      <div class="page-header">
        <h2><?php
        if($isGroup) {
          echo 'Group';
        }elseif($isList){
          echo 'Makers';
        }else{
          echo 'Maker';
        }?>
        </h2>
      </div>

      <?php
      if ($isGroup) {
        ?>
        <div class="row padbottom">
          <div class="col-sm-3" id="groupphoto">
            <div class="entry-page-maker-img" style="background: url(<?php echo (!empty($groupphoto) ? legacy_get_resized_remote_image_url($groupphoto,400,400) : get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg' ); ?>) no-repeat center center;"></div>
          </div>
          <div class="col-sm-9 col-lg-7">
            <h3 class="text-capitalize" id="groupname"><?php echo $groupname;?></h3>
            <p id="groupbio"><?php echo $groupbio;?></p>
          </div>
        </div>
    <?php
    } else {
      foreach($makers as $key=>$maker) {
        if($maker['firstname'] !=''){
          ?>
          <div class="row padbottom">
            <div class="col-sm-3">
              <div class="entry-page-maker-img" style="background: url(<?php echo (!empty($maker['photo']) ? legacy_get_resized_remote_image_url($maker['photo'],400,400) : get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg' ); ?>) no-repeat center center;"></div>
            </div>
            <div class="col-sm-9 col-lg-7">
              <h3>
                <span class="text-capitalize"><?php echo $maker['firstname'];?></span>
                <span class="text-capitalize"><?php echo $maker['lastname'];?></span>
              </h3>
              <p><?php echo $maker['bio'];?></p>
            </div>
          </div>
          <?php
        }
      }
    }
  }
  ?>
  </div>
  <?php
  echo display_groupEntries($entryId);
?>
</div>
