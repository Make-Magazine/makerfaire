<div class="clearfix">
  <h4 class="welcome-head pull-left">Hi
    <?php echo $current_user->user_firstname.' ';
      echo $current_user->user_lastname;
    ?>
  </h4>
  <div class="settings-pop-btn pull-right">
    <button type="button" class="btn btn-default btn-no-border notifications-button"
      data-toggle="popover" data-html="true"
      data-placement="bottom" data-trigger="focus"
      data-content='<div class="manage-entry-popover">
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
    <a href="http://makerfaire.com/bay-area-2016/call-for-makers/" target="_blank"
      class="btn btn-primary btn-no-border">
      Submit another entry
    </a>
  </span>
</div>
<hr class="header-break"/>
