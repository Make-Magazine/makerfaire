<?php /** Template Name: Maker Admin Manage Entries */
if (!is_user_logged_in())
    auth_redirect();

get_header();
?>
<div class="content col-md-12 maker-admin-manage-faire-entries">
  <script>
  jQuery(document).ready(function() {
    //jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('body').tooltip( {selector: '[data-toggle=tooltip]'} );
    //returns content based on separate element - For Notificationd, get your tickets and
    jQuery(".toggle-popover").popover({
        html : true,
        placement : "auto bottom",
        content: function() {
          return jQuery(this).next('.popover-content').html();
        },
        title: ''
    });
  });
  </script>
  <div class="clearfix">
    <?php
    $current_user = wp_get_current_user();

    //require_once our model
    require_once( get_template_directory().'/models/maker.php' );

    //instantiate the model
    $maker   = new maker($current_user->user_email);

    $tableData = $maker->get_table_data();
    $entries = $tableData['data'];

?>
    <h4 class="welcome-head pull-left">Hi <?php echo $maker->first_name .' '. $maker->last_name; ?></h4>
    <div class="settings-pop-btn pull-right">
      <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
        Settings &amp; Help<i class="fa fa-cog"></i>
      </button>
      <div class="popover-content hidden">
        <div class="manage-entry-popover">
          <a href="/login/?mode=reset">Change Password</a>
          <a href="/login/?action=logout">Log Out</a>
          <h6 class="popover-head">Questions?</h6>
          <ul>
            <?php if($maker->isSponsor) { ?>
              <li>Sponsors
                <ul><li><a href="mailto:sponsorrelations@makerfaire.com">Email Us</a></li></ul>
              </li>
            <?php } ?>

            <?php if($maker->isMaker) { ?>
              <li>Maker Toolkits
                <ul>
                  <li><a href="/national/maker-toolkit" target="_blank">National Maker Faire</a></li>
                  <li><a href="/bay-area-2016/maker-toolkit/" target="_blank">Bay Area</a></li>
                  <li><a href="/new-york/maker-toolkit/" target="_blank">World Maker Faire</a></li>
                </ul>
              </li>
            <?php } ?>
          </ul>
        </div>
      </div>

    </div>
  </div>
  <div class="clearfix">
    <h2 class="title-head pull-left">Manage your Maker Faire Applications</h2>
    <span class="submit-entry pull-right">
      <a href="/new-york-2016/call-for-makers/" target="_blank" class="btn btn-primary btn-no-border">
        Submit another entry
      </a>
    </span>
  </div>
  <hr class="header-break">

  <?php
  echo $maker->createPageLinks( 'pagination pagination-sm' );
  foreach($entries as $entryData) {
    //prepare the data
    if($entryData['status']=='Accepted'){
      $statusBlock = 'greenStatus';
    }else{
      $statusBlock = 'greyStatus';
    }
    //$image = legacy_get_fit_remote_image_url($entryData['project_photo'],275,275);
    $image =  (isset($entryData['project_photo'])&&$entryData['project_photo']!=''?$entryData['project_photo']:get_template_directory_uri() .'/images/no-image.png');
    ?>
  <style>
  .image-container {
    background-size:cover;
    background-repeat:no-repeat;
    width:275px;
    height:275px;
  }
  </style>
    <div class="maker-admin-list-wrp">
      <div class="gv-list-view-title-maker-entry">
        <div class="entryImg">
          <div class="faire-entry-image-wrp">
            <a class="thickbox" href="<?php echo $image;?>">
              <div class="image-container" style="background-image: url('<?php echo $image;?>');"></div>
              <!--<img class="img-responsive" src="<?php echo $image;?>" alt="Project Photo" />-->
            </a>
          </div>
        </div>
        <div class="entryData">
          <div class="statusBox <?php echo $statusBlock;?>">
            <div class="pull-left"><span class="gv-field-label"><?php echo $entryData['faire_name'];?></span> </div>
            <div class="pull-right statusText"><span class="gv-field-label">Status: </span> <?php echo $entryData['status'];?></div>
          </div>
          <h3 class="entry-title">
            <?php //if status is accepted, the title links to the public facing entry page
            if($entryData['status']=='Accepted') {?>
              <a target="_blank" href="/maker/entry/<?php echo $entryData['lead_id'];?>"><?php echo $entryData['presentation_title'];?></a>
            <?php
            }else{
              echo $entryData['presentation_title'];
            } ?>
          </h3>
          <div class="clear pull-left entryID latReg">
            <?php echo $entryData['form_type'];?>: <span class="entryStandout"><?php echo $entryData['lead_id'];?></span></div>
          <div class="clear links latReg">
            <div class="submit-date"><span class="gv-field-label">Submitted: </span> <?php echo date('M j, Y g:i  A',strtotime($entryData['date_created']));?></div>
            <!-- Action Bar for Entries -->
            <div class="entry-action-buttons">
              <!-- Get Your Tickets Section -->
              <?php
              //only display if there are tickets and if the entry has been accepted
              if(!empty($entryData['ticketing']) && $entryData['status']=='Accepted'){
                ?>
                <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
                  GET YOUR TICKETS<i class="fa fa-ticket fa-lg" aria-hidden="true"></i>
                </button>
                <div class="popover-content hidden">
                  <?php
                  foreach($entryData['ticketing'] as $ticket){
                    ?>
                    <div class="row mat-ticketing">
                      <div class="col-md-10">
                        <div class="title"><?php echo $ticket['title'];?></div>
                        <div class="subtitle"><?php echo $ticket['subtitle'];?></div>
                      </div>
                      <div class="col-md-2">
                        <a target="_blank" href="<?php echo $ticket['link'];?>">
                          <i class="fa fa-chevron-circle-right fa-2x" aria-hidden="true"></i>
                        </a>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                  <div class="footer">
                    To share a ticket with a friend, just send them the Eventbrite URL
                  </div>
                </div>
                <?php
              }
              ?>

              <!-- Notifications -->
              <button type="button" class="btn btn-default btn-no-border notifications-button toggle-popover"
                data-toggle="popover">
                NOTIFICATIONS
                <span class="fa-stack fa-lg">
                <i class="fa fa-circle"></i>
                <span class="notification-counter">3</span>
                </span>
              </button>
              <div class="popover-content hidden">
                <div class="manage-entry-popover"><a>Pay Commercial Maker Fee etc</a></div>
              </div>

              <!-- Manage Entry links -->
              <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
                MANAGE<i class="fa fa-cog fa-lg"></i>
              </button>
              <div class="popover-content hidden">
                <div class="manage-entry-popover row">
                  <div class="manage-links">
                    <?php
                    //View Link
                    $url = do_shortcode('[gv_entry_link action="read" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
                    $url = str_replace('/view/', '/', $url);  //remove view slug from URL
                    ?>
                    <a href="<?php echo $url;?>">View</a>
                    <?php if($entryData['status']=='Accepted') {?>
                    <a target="_blank" href="/maker/entry/<?php echo $entryData['lead_id'];?>">View Public Information</a>
                    <?php } ?>
                  </div>
                  <div class="manage-links">
                    <?php
                    $class = '';
                    $tooltip = '';
                    //edit link
                    if($entryData['status']!='Cancelled' and $entryData['maker_type']=='contact'){
                      $url = do_shortcode('[gv_entry_link action="edit" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
                      $url = str_replace('/view/', '/', $url);  //remove view slug from URL
                      echo '<a href="'. $url .'">Edit Entry</a>';
                    }else{
                      echo  '<div class="disabled" data-placement="left"  data-toggle="tooltip" title="Only the main contact can edit">Edit Entry</div>';
                    }
                    ?>
                    <a href="#">Copy Entry</a>
                  </div>
                  <div>
                    <?php
                    //cancel link - only shown if Status is not currently Cancel
                    if($entryData['status']!='Cancelled'){
                      ?>
                      <a href="#cancelEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['lead_id'];?>" data-projName="<?php echo $entryData['presentation_title'];?>">Cancel</a>
                      <?php
                    }

                    ?>
                    <?php
                    //Delete Link
                    if($entryData['status']=='Proposed' || $entryData['status']=='In Progress'){
                      ?>
                      <a href="#deleteEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['lead_id'];?>" data-projName="<?php echo $entryData['presentation_title'];?>">Delete</a>
                      <?php
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  <?php } ?>
  <hr>
  <!-- Modal to cancel entry -->
<div class="modal" id="cancelEntry">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Cancel <span id="projName"></span>, Exhibit ID: <span id="cancelEntryID" name="entryID"></span></h4>
        </div>
        <div class="modal-body">
            <div id="cancelText">
                <p>Sorry you can't make it. Why are you canceling?</p><br/>
                <textarea rows="4" cols="50" name="cancelReason"></textarea>
            </div>
        <span id="cancelResponse"></span><br/>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="submitCancel">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal to copy entry to a new form -->
  <div class="modal" id="copy_entry">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title">Copy Exhibit ID: <span id="copyEntryID" name="entryID"></span></h4>
        </div>
        <div class="modal-body">
          No Open faires at the moment
          <br><span id="copyResponse"></span>
          <br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="submitCopy">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal to delete entry-->
  <div class="modal" id="deleteEntry">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title">Delete <span id="delProjName"></span>, Exhibit ID: <span id="deleteEntryID" name="entryID"></span></h4>
        </div>
        <div class="modal-body">
          <div id="deleteText">
            <p>Are you sure you want to trash this entry? You can not reverse this action.</p>
          </div>
          <span id="deleteResponse"></span>
          <br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="submitDelete">Yes, delete it</button>
          <button type="button" class="btn btn-default" id="cancelDelete" data-dismiss="modal">No, I'll keep it</button>
          <button type="button" class="btn btn-default" id="closeDelete" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
