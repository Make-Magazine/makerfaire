<?php
/* This tool users UI grid. documentation can be found here http://ui-grid.info/docs
*/
if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', false );
}

require_once '../wp-load.php';

if (!is_user_logged_in())
auth_redirect();

if ( !current_user_can( 'view_rmt' ) ) {
  echo 'The current user can not access this page.';
  die();
}   

include 'header.php';

?>

  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
      <button href="#menu-toggle" class="slidebar-toggle" id="menu-toggle">
        <span>Collapse sidebar</span>
      </button>
    </div>
    <?php do_action('rmt_head');?>
    <div class="navbar-title">
      <i>
        <span id="pageTitle"></span> :
        <span id="subTitle"></span>
      </i>
    </div>
  </nav>

  <div id="wrapper">
    <!-- Sidebar -->
    <div id="slidebar-white" class="slidebar-nav">
      <nav id="navbar-white" class="navbar navbar-default" role="navigation">
        <?php include 'nav.php';?>
      </nav><!--/.navbar -->
    </div><!--/.sidebar-nav -->
    <!-- Page Content -->
    <main id="page-wrapper6">
      <div ng-app="resourceApp" class="ng-scope col-md-12">
        <div ng-view></div>
      </div>
    </main>
  </div><!-- /#wrapper -->


<?php include 'footer.html';
