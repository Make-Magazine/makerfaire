<?php /** Template Name: Maker Admin Manage Entries */?>
<?php get_header(); ?>
<div class="content col-md-12 maker-admin-manage-faire-entries">
  <script>
  jQuery(document).ready(function() {
    jQuery('[data-toggle="popover"]').popover();
  });
  </script>
  <div class="clearfix">
    <h4 class="welcome-head pull-left">Hi</h4>
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
  <div class="maker-admin-list-wrp">
    <div class="gv-list-view-title-maker-entry">
      <div class="entryImg">
        <div class="faire-entry-image-wrp">
          <a class="thickbox" href="http://i1.wp.com/img1.etsystatic.com/058/0/5900797/il_570xN.706338171_t595.jpg">
            <img src="http://i1.wp.com/img1.etsystatic.com/058/0/5900797/il_570xN.706338171_t595.jpg?zoom=2&amp;w=250" alt="Project Photo">
          </a>
        </div>
      </div>
      <div class="entryData">
        <div class="statusBox greenStatus">
          <div class="pull-left"><span class="gv-field-label">Maker Faire</span> New York 2015</div>
          <div class="pull-right statusText"><span class="gv-field-label">Status: </span> Accepted</div>
        </div>
        <h3 class="entry-title"> The BQE</h3>
        <div class="clear pull-left entryID latReg">
          Exhibit: <span class="entryStandout">53541</span></div>
        <div class="clear links latReg">
          <div class="submit-date"><span class="gv-field-label">Submitted: </span> Sep 18, 2015</div>
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
            <button type="button" class="btn btn-default btn-no-border manage-button"
              data-toggle="popover" data-html="true" data-placement="bottom" data-trigger="focus"
              data-content='<div class="manage-entry-popover">
                <a href="http://local.wordpress.dev/manage-entries/entry/53541/?page=gf_entries&amp;view=entry&amp;edit=7a1199eb19">Edit</a>
                <a href="#cancelEntry" data-toggle="modal" data-projName="The BQE">Cancel</a>
                <a href="http://local.wordpress.dev/manage-entries/entry/53541/">View</a>
              </div>'>
              MANAGE
              <i class="fa fa-cog"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <hr>
  <!-- Modal to cancel entry -->
  <div class="modal" id="cancelEntry">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title">Cancel <span id="projName"></span>, Exhibit ID: <span id="cancelEntryID" name="entryID"></span></h4>
        </div>
        <div class="modal-body">
          <div id="cancelText">
            <p>Sorry you can't make it. Why are you canceling?</p>
            <br>
            <span class="input-placeholder-text" style="color: rgb(51, 51, 51); position: absolute;"></span>
            <textarea rows="4" cols="50" name="cancelReason"></textarea>
          </div>
          <span id="cancelResponse"></span>
          <br>
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
