<?php
include '../wp-load.php';
if (!is_user_logged_in())
    auth_redirect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MakerFaire Resource Management Tool</title>
	<!-- Bootstrap -->
	<link href="http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/animate.min.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
  <link href="node_modules/angular-ui-grid/ui-grid.css" rel="stylesheet">
  <!-- Script -->
  <script src="../wp-includes/js/jquery/jquery.js"></script>

  <script src="node_modules/angular/angular.min.js"></script>
  <script src="node_modules/angular-route/angular-route.min.js"></script>
  <script src="node_modules/angular-sanitize/angular-sanitize.min.js"></script>
  <script src="http://ui-grid.info/docs/grunt-scripts/csv.js"></script>
  <script src="node_modules/angular-ui-grid/ui-grid.min.js"></script>
  <script src="js/bootstrap/bootstrap.min.js"></script>
  <script src="node_modules/angular-ui-bootstrap/dist/ui-bootstrap-tpls.js"></script>

  <style>
  .nav-list>li>a, .nav-list .nav-header {
    margin-left: -10px;
    margin-right: -10px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
}
.nav-header {
    display: block;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: bold;
    line-height: 20px;
    color: #999999;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
}
.nav-list {
    padding-left: 10px;
    padding-right: 10px;
    margin-bottom: 0;
}
.nav {
    margin-left: 0;
    margin-bottom: 20px;
    list-style: none;
}
.nav-list>li>a {
    padding: 3px 0;
}
.nav-list>li>a, .nav-list .nav-header {
    margin-left: -10px;
    margin-right: -10px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
}
.nav>li>a {
    display: block;
    font-size:14px;
}
a {
    color: #0088cc;
    text-decoration: none;
}
/*
   This is the background of our overlay. We need it to be
   absolutely positioned within the grid, and fill from
   top to bottom, and the full width. It will also have
   a black background with 40% opacity.
*/
.grid-msg-overlay {
  position: absolute;
  top: 0;
  bottom: 0;
  width: 100%;
  background: rgba(0, 0, 0, 0.4);
}

/*
  This guy will contain our message. We want it centered
  so it's positioned absolutely with percentage-based
  offsets and dimensions. It also has some basic border
  stuff and most important is using "display: table" so
  we can vertically center its contents.
*/
.grid-msg-overlay .msg {
  opacity: 1;
  position: absolute;
  top: 20%;
  left: 20%;
  width: 60%;
  height: 50%;
  background-color: #eee;
  border-radius: 4px;
  border: 1px solid #555;
  text-align: center;
  font-size: 24px;
  display: table;
}

/*
  Lastly this is the actual message text. It uses
  display: table-cell so the vertical alignment
  works properly.
*/
.grid-msg-overlay .msg span {
  display: table-cell;
  vertical-align: middle;
}
.well{padding:19px 5px;}
  </style>
</head>
<body>
  <div class="container">
    <div class="row"><h1>Resource Management Tool</h1></div>
  </div>

  <div class="row">
    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
      <div class="well">
        <div>
          <ul class="nav nav-list">
            <li><label class="tree-toggle nav-header">Reports</label>
              <ul class="nav nav-list tree">
                <li><a href="#reports">Entry Resources</a></li>
                <li><a href="#reports/ent2resource">Entry to Resources</a></li>
                <li><a href="#reports/change">Entry Change Report</a></li>
                <li><a href="#reports/location">Faire Location Report</a></li>
                <li><a href="#reports/build">Build your own Report</a></li>
              </ul>
            </li>
            <li><label class="tree-toggle nav-header">Resources</label>
              <ul class="nav nav-list tree">
                <li><a href="#resources/list">Type</a></li>
                <li><a href="#resources/items">Items</a></li>
              </ul>
            </li>
            <li><label class="tree-toggle nav-header">Vendors</label>
              <ul class="nav nav-list tree">
                <li><a href="#vendors/list">List</a></li>
                <li><a href="#vendors/resources">Resources</a></li>
              </ul>
            </li>
            <li><label class="tree-toggle nav-header">Faires</label>
              <ul class="nav nav-list tree">
                <li><a href="#faire/global-faire">Global Faire Data</a></li>
                <li><a href="#faire/data">Faire Data</a></li>
                <li><a href="#faire/areas">Areas</a></li>
                <li><a href="#faire/subareas">Sub-Areas</a></li>
              </ul>
            </li>
            <li><label class="tree-toggle nav-header">Entry</label>
              <ul class="nav nav-list tree">
                <li><a href="#entry/atttibuteCategories">Attributes</a></li>
                <li><a href="#entry/workflow">Workflow</a></li>
                <!--<li><a href="#entry">Entry Resources</a></li>-->
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div ng-app="resourceApp" class="col-xs-10 col-sm-10 col-md-10 col-lg-10 ng-scope" >
      <div ng-view></div>
    </div>
  </div>
  <script src="js/main.js"></script>
  <script src="js/controllers.js"></script>
  <script src="js/report.controllers.js"></script>
  <script src='/wp-content/plugins/gravityforms/js/gf_field_filter.js'></script>
</body>
</html>