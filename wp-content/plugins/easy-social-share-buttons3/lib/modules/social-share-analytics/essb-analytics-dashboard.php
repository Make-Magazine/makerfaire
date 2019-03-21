<?php

$mode = isset ( $_GET ["mode"] ) ? $_GET ["mode"] : "";
$month = isset ( $_GET ['essb_month'] ) ? $_GET ['essb_month'] : '';
$date = isset ( $_GET ['date'] ) ? $_GET ['date'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';
$network = isset($_GET['network']) ? $_GET['network'] : '';
$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '';

ESSBSocialShareAnalyticsBackEnd::init_addional_settings ();

$extra_title = '';

if ($month != '') {
	$extra_title = ' for Month: ' . $month;
}

if ($date != '') {
	$extra_title = ' for Date: ' . $date;
}

if ($mode == 'position' && $position != '') {
	$extra_title = ' for Position: '.ESSBSocialShareAnalyticsBackEnd::position_name($position);
}

if ($mode == 'network' && $network != '') {
	$all_networks = essb_available_social_networks();
	if (isset($all_networks[$network])) {
		$extra_title = ' for Social Network: '.$all_networks[$network]['name'];
	}
}

if ($mode == 'single' && $post_id != '') {
	$extra_title .= ' for <b>' . get_the_title($post_id).'</b>';
}

$is_home = true;

if ($mode != '') {
	$is_home = false;
}

?>

<style type="text/css">

.essb-page-stats { padding: 25px; }
.essb-page-stats h3 { font-size: 24px; font-weight: 400; letter-spacing: -1px; margin: 0;}

.essb-page-stats .one-half {
	width: calc(50% - 60px);
	margin: 20px;
	display: inline-block;
	vertical-align: top;
	position: relative;
}

.essb-page-stats .dataTables_length, .essb-page-stats .dataTables_filter,
.dataTables_info, .dataTables_paginate {
	font-size: 12px;
}

.dataTables_wrapper .sub2 { font-size: 12px; text-align: left; text-transform: uppercase; border-bottom: 0; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.1); }
table.dataTable.stripe tbody tr.odd, table.dataTable.display tbody tr.odd { background: #fff; }
table.dataTable.display tbody tr.odd > .sorting_1, table.dataTable.order-column.stripe tbody tr.odd > .sorting_1 { background: #fafafa; }
table.dataTable.display tbody tr > .sorting_1 { font-weight: 600; }
.dataTables_scrollBody { border-bottom: 0 !important; box-shadow: 0 0 30px 0 rgba(0,0,0,0.1); margin-bottom: 20px; }

.stats-head { margin-top: 30px; }

.stats-head .panel {
  width: calc(33% - 60px);
  margin: 0 10px;
  padding: 20px;
  box-shadow: 0 0 10px 0 rgba(0,0,0,0.1);
  display: inline-block;
  vertical-align: top;
  position: relative;
}

.essb-page-stats h4 { font-size: 16px; font-weight: 400; }

.dashboard-head {
	padding: 20px;
	margin-top: 30px;
}

.dashboard-head h4 {
	font-weight: 600;
	font-size: 20px;
	margin: 0;
	letter-spacing: -0.5px;
}

.dashboard-head p {
	margin-top: 5px;
	color: #999;
}

.stats-head .panel strong { font-weight: 700; }
.stats-head .panel h4 { margin: 0; font-size: 16px; font-weight: 400; }
.stats-head .panel .bold-value { font-size: 32px; font-weight: bold; letter-spacing: -1px; margin: 30px 0; }
.stats-head .panel .footer { border-top: 1px solid #f1f3f3; padding-top: 10px; }
.stats-head .panel .footer .value { font-weight: 700; margin-right: 10px; }
.stats-head .panel .footer .desc { color: #787878; }

.position-row { padding: 15px 20px; box-shadow: inset 0 -1px 0 rgba(0,0,0,0.1); }
.position-row .name { font-size: 15px; }
.position-row .value, .position-row .percent { font-size: 16px; text-align: right; font-weight: bold; padding-right: 10px; }
.position-row .graph span {
	background: #f39558; line-height: 24px; }
.position-row .value span {
	    margin-left: 10px;
    background: #f1f1f1;
    padding: 8px;
    font-size: 13px;
    font-weight: 400;
    min-width: 100px;
}

.position-row .value span i {
	font-size: 16px;
	margin: 0 5px;
}

.position-row .name .network-icon {
	font-size: 21px;
	margin-right: 10px;
}

.position-row:hover {
	background: #d5e6f7;
}

.date-reports-dates .day-value {
	display: inline-block;
	padding: 6px 10px;
	margin: 10px;
	background: #f1f3f5;
}

@media screen and (max-width: 1200px) {
	.essb-page-stats .one-half { width: 100%; display: block; }
	.stats-head .panel { margin: 20px 10px; width: 90%; }
}

</style>
<div class="wrap essb-page-stats">

    <h3>Social Share Button Usage / Analytics <?php echo $extra_title; ?></h3>

    <?php if ($is_home) {
      include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-welcome-total.php';
    }
    
    if ($mode == 'positions') {
    	include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-positions.php';
    }
    
    if ($mode == 'position') {
    	include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-single-position.php';
    }
    
    if ($mode == 'networks') {
    	include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-networks.php';
    }
    
    if ($mode == 'month' || $mode == 'date') {
    	include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-month.php';
    }
    
    if ($mode == 'network') {
    	include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-single-network.php';
    }
    
    if ($mode == 'single') {
    	include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/dashboard/template-single-post.php';
    }
    ?>

</div>
