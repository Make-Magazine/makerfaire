<?php
require_once '../wp-load.php';

if (!is_user_logged_in())
    auth_redirect();
include 'header.html';
?>

  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
      <button href="#menu-toggle" class="slidebar-toggle" id="menu-toggle">
        <span>Toggle sidebar</span>
      </button>
    </div>

    <h1>Resource Management Tool</h1>
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