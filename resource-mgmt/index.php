<?php
if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', false );
}

require_once '../wp-load.php';

if (!is_user_logged_in())
    auth_redirect();
include 'header.php';

/* required to display wp-admin bar */
/** WordPress Administration Screen API */
require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
require_once(ABSPATH . 'wp-admin/includes/screen.php');

require_once(ABSPATH . 'wp-admin/includes/template.php');
do_action( 'admin_init' );
wp_footer();
/* end section for wp-admin bar */
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
        <?php include 'nav.html';?>
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
