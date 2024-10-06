<?php
/* This tool users UI grid. documentation can be found here http://ui-grid.info/docs
*/
if (! defined('WP_ADMIN')) {
  define('WP_ADMIN', false);
}

require_once '../wp-load.php';

if (!is_user_logged_in()) {
  $password = false;
  $error = '';
  if(isset($_POST['post_password'])){
    if($_POST['post_password']=='BA24_reports'){      
      $password = true;
    }else{
      $error = 'Incorrect password. Please try again';
    }
  }
  //give option to enter a password instead
  $reports_only = true;
} else {
  $password = true;
  $reports_only = current_user_can('reports_only');
  if (!current_user_can('view_rmt')) {
    echo 'The current user can not access this page.';
    die();
  }
}

include 'header.php';
if($reports_only){
  ?>
  <input type="hidden" id="reports_only" value="true" />
  <?php
}
if (!$reports_only && $password) { ?>
  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
      <button href="#menu-toggle" class="slidebar-toggle" id="menu-toggle">
        <span>Collapse sidebar</span>
      </button>
    </div>

    <div class="navbar-title">
      <i>
        <span id="pageTitle"></span> :
        <span id="subTitle"></span>
      </i>
    </div>
  </nav>
<?php
} ?>

<div id="wrapper" style="<?php if ($reports_only) echo 'padding-left:25px !important;' ?>">
  <?php
  if (!$reports_only && $password) { ?>
    <!-- Sidebar -->
    <div id="slidebar-white" class="slidebar-nav">
      <nav id="navbar-white" class="navbar navbar-default" role="navigation">
        <?php include 'nav.php'; ?>
      </nav><!--/.navbar -->
    </div><!--/.sidebar-nav -->
  <?php } ?>
  
  <!-- Page Content -->
  <main id="page-wrapper6" style="<?php if ($reports_only) echo 'top:0px !important;' ?>">
    <?php
    if (!$password) {
      if($error!='')  echo $error;
      $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]#/canned/undefined/17";
      
    
      ?>
      
      <form action="<?php echo $url;  ?>" class="post-password-form" method="post">
        <p>This content is password protected.<br/>To view it please enter your password below:</p>
        <p><label for="pwbox-688531">Password: <input name="post_password" id="pwbox-688531" type="password" spellcheck="false" size="20"><div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
          </label> 
          <input type="submit" name="Submit" value="Enter"></p>
      </form>
      <?php
    }else{
      ?>
      <div ng-app="resourceApp" class="ng-scope col-md-12">
        <div ng-view></div>
      </div>
      <?php
    }
    ?>    
  </main>
</div><!-- /#wrapper -->


<?php include 'footer.html';
