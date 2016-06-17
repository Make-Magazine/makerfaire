<?php /** Template Name: Maker Admin Manage Entries */
if (!is_user_logged_in())
    auth_redirect();
?>
<?php get_header(); ?>
<div class="content col-md-12 maker-admin-manage-faire-entries">
  <script>
  jQuery(document).ready(function() {
    jQuery('[data-toggle="popover"]').popover();

    //popover manage entries
    jQuery("#manage-entries").popover({
        html : true,
        placement : "bottom",
        content: function() {
          return jQuery("#manage-entries-content").html();
        },
        title: ''
    });
  });
  </script>
  <div class="clearfix">
    <?php
    $current_user = wp_get_current_user();

    //require_once our model
    require_once( 'models/maker.php' );
    //instantiate the model
    $maker = new maker($current_user->user_email);
?>
    <h4 class="welcome-head pull-left">Hi, <?= $maker->first_name .' '. $maker->last_name?></h4>
    <div class="settings-pop-btn pull-right">
      <button type="button" class="btn btn-default btn-no-border notifications-button"
        data-toggle="popover" data-html="true" data-placement="bottom"
        data-trigger="focus" data-content='<div class="manage-entry-popover">
          <a href="/login/?mode=reset">Change Password</a>
          <a href="/login/?action=logout">Log Out</a>
          <h6 class="popover-head">Questions?</h6>
          <a href="mailto:support@makerfaire.com">Email us</a>
        </div>'>
        Settings &amp; Help
        <i class="fa fa-cog"></i>
      </button>
    </div>
  </div>
  <div class="clearfix">
    <h2 class="title-head pull-left">Your Maker Faire entries</h2>
    <span class="submit-entry pull-right">
      <a href="/bay-area-2016/call-for-makers/" target="_blank" class="btn btn-primary btn-no-border">
        Submit another entry
      </a>
    </span>
  </div>
  <hr class="header-break">
  <?php
  $entries = $maker->get_entries();

  foreach($entries as $entry) {
    if($entry['status']=='Accepted'){
      $statusBlock = 'greenStatus';
    }else{
      $statusBlock = 'greyStatus';
    }
    ?>
    <div class="maker-admin-list-wrp">
      <div class="gv-list-view-title-maker-entry">
        <div class="entryImg">
          <div class="faire-entry-image-wrp">
            <?php $image = legacy_get_fit_remote_image_url($entry['project_photo'],250,570);?>
            <a class="thickbox" href="<?php echo $image;?>">
              <img src="<?php echo $image;?>" alt="Project Photo">
            </a>
          </div>
        </div>
        <div class="entryData">
          <div class="statusBox <?php echo $statusBlock;?>">
            <div class="pull-left"><span class="gv-field-label">Maker Faire</span> <?php echo $entry['faire'];?></div>
            <div class="pull-right statusText"><span class="gv-field-label">Status: </span> <?php echo $entry['status'];?></div>
          </div>
          <h3 class="entry-title"><?php echo $entry['presentation_title'];?></h3>
          <div class="clear pull-left entryID latReg">
            Exhibit: <span class="entryStandout"><?php echo $entry['lead_id'];?></span></div>
          <div class="clear links latReg">
            <div class="submit-date"><span class="gv-field-label">Submitted: </span> <?php echo date('M j, Y g:i  A',strtotime($entry['date_created']));?></div>
            <div class="entry-action-buttons">
              <button type="button" class="btn btn-default btn-no-border notifications-button"
                data-toggle="popover" data-html="true" data-placement="bottom" data-trigger="focus"
                data-content='<div class="manage-entry-popover"><a>Pay Commercial Maker Fee etc</a></div>'>
                NOTIFICATIONS
                <span class="fa-stack fa-lg">
                <i class="fa fa-circle"></i>
                <span class="notification-counter">3</span>
                </span>
              </button>

              <button type="button" class="btn btn-default btn-no-border manage-button" id="manage-entries">MANAGE<i class="fa fa-cog"></i></button>
              <div id="manage-entries-content" class="hidden">
                <div class="manage-entry-popover">
                  <a href="/manage-entries/entry/<?php echo $entry['lead_id'];?>/?page=gf_entries&amp;view=entry&amp;edit=7a1199eb19">Edit</a>
                  <a href="#cancelEntry" data-toggle="modal" data-entry-id="<?php echo $entry['lead_id'];?>" data-projName="<?php echo $entry['presentation_title'];?>">Cancel</a>
                  <a href="/manage-entries/entry/<?php echo $entry['lead_id'];?>/">View</a>
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
