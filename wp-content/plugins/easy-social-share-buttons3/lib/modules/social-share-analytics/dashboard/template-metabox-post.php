<?php
//ESSBSocialShareAnalyticsBackEnd::init_addional_settings();

global $post_id, $post;

if (isset($post)) {
	$post_id = $post->ID;
}

$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ('', $post_id);
$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position('', $post_id);
$device_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_device ('', $post_id);

$calculated_total = $device_stats->cnt;
$desktop = $device_stats->desktop;
$mobile = $device_stats->mobile;

$print_percentd = 0;
$print_percentm = 0;

if ($calculated_total != 0) {
	$print_percentd = round($desktop * 100 / $calculated_total, 2);
	$print_percentm = round($mobile * 100 / $calculated_total, 2);
}

$best_position_value = 0;
$best_position_key = "";
	
$best_network_value = 0;
$best_network_key = "";
	
if (isset ( $overall_stats )) {
	foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {

		$key = "position_" . $k;

		$single = intval ( $position_stats->{$key} );
			
		if ($single > $best_position_value) {
			$best_position_value = $single;
			$best_position_key = $k;
		}
	}

	foreach ( $essb_networks as $k => $v ) {

		$single = intval ( $overall_stats->{$k} );
			
		if ($single > $best_network_value) {
			$best_network_value = $single;
			$best_network_key = $v["name"];
		}
	}
}

$best_position_percent = 0;
$best_network_percent = 0;
	
if ($calculated_total != 0) {
	$best_position_percent = $best_position_value * 100 / $calculated_total;
	$best_position_percent = round ( $best_position_percent, 1 );
}

if ($calculated_total != 0) {
	$best_network_percent = $best_network_value * 100 / $calculated_total;
	$best_network_percent = round ( $best_network_percent, 1 );
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

<div class="stats-head">
  <div class="panel">
    <h4>Total Share Button Clicks</h4>

    <div class="bold-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($calculated_total); ?></div>

    <div class="footer">
      <label class="desc">Desktop:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($desktop); ?> (<?php echo $print_percentd; ?> %)</label>

      <label class="desc">Mobile:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($mobile); ?> (<?php echo $print_percentm; ?> %)</label>
    </div>
  </div>
  <div class="panel">
    <h4>Best Position: <strong><?php echo ESSBSocialShareAnalyticsBackEnd::position_name ($best_position_key); ?></strong></h4>

    <div class="bold-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($best_position_value); ?>
      
    </div>

    <div class="footer">
      <label class="value" style="margin-right: 0px;"><?php echo $best_position_percent; ?> %</label>
      <label class="desc" style="margin-right: 10px;">Of all clicks</label>

    </div>
  </div>
  <div class="panel">
    <h4>Best Social Network: <strong><?php echo $best_network_key; ?></strong></h4>

    <div class="bold-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($best_network_value); ?>

    </div>

    <div class="footer">
      <label class="value" style="margin-right: 0px;"><?php echo $best_network_percent; ?> %</label>
      <label class="desc" style="margin-right: 10px;">Of all clicks</label>

    </div>

  </div>
</div>

<div class="stats-full-report" style="margin-top: 30px; padding-left: 20px;">
	<a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=single&post_id=<?php echo $post_id; ?>" target="_blank" class="essb-btn essb-btn-red" style="font-size:15px; text-transform: capitalize; padding: 15px 20px;">Open Detailed Post Report &rarr;</a>
</div>

<!-- Positions -->
<div class="stat-welcome-graph" style="magin-top: 30px;">
	<div class="dashboard-head">
		<h4>Positions</h4>
		<p>View positions where the social network is used.</p>
	</div>
	
	<div class="positions-report">
	
	<?php 
	if ($overall_stats) {
		
		foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {
				
			$key = "position_" . $k;
				
			$single = intval ( $position_stats->{$key} );
			
			$key = "position_" . $k;
			$keyd = "position_d_" . $k;
			$keym = "position_m_" . $k;
			
			$single = intval ( $position_stats->{$key} );
			$single_d = isset($position_stats->{$keyd})  ? $position_stats->{$keyd} : 0;
			$single_m = isset($position_stats->{$keym})  ? $position_stats->{$keym} : 0;
			
				
			if ($single > 0) {
				if ($calculated_total != 0) {
					$percent = $single * 100 / $calculated_total;
				} else {
					$percent = 0;
				}
				$print_percent = round ( $percent, 2 );
				$percent = round ( $percent );
				
				echo '<div class="position-row">';
				echo '<div class="name" style="display: inline-block; width: 20%;"><a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=position&position='.$k.'" target="_blank">'.ESSBSocialShareAnalyticsBackEnd::position_name($k).'</a></div>';
				echo '<div class="value" style="display: inline-block; width: 10%;">'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single).'</div>';
				echo '<div class="value" style="display: inline-block; width: 10%;"><span class="devices"><i class="ti-desktop"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_d).'<i class="ti-mobile"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_m).'</span></div>';
				echo '<div class="percent" style="display: inline-block; width: 8%;">'.$print_percent.'%'.'</div>';
				echo '<div class="graph" style="display: inline-block; width: 49%;"><span style="width: '.$percent.'%; display: inline-block; ">&nbsp;</span></div>';
				echo '</div>';
			}
		}
	}
	?>
	
	</div>
	
</div>

<!-- Networks -->
<div class="stat-welcome-graph" style="magin-top: 30px;">
	<div class="dashboard-head">
		<h4>Networks</h4>
		<p>View how the positions on site perform. Usage of many social networks may lead to lower shares due to paradox of choice.</p>
	</div>
	
	<div class="positions-report">
	
	<?php 
	if ($overall_stats) {
		
		$essb_networks = essb_available_social_networks();
		
		foreach ( $essb_networks as $k => $v ) {
				
			$key =  $k;
				
			$single = intval ( $overall_stats->{$key} );
			
			$keyd = "desktop_" . $k;
			$keym = "mobile_" . $k;
			
			$single = intval ( $overall_stats->{$key} );
			$single_d = isset($overall_stats->{$keyd})  ? $overall_stats->{$keyd} : 0;
			$single_m = isset($overall_stats->{$keym})  ? $overall_stats->{$keym} : 0;
			
				
			if ($single > 0) {
				if ($calculated_total != 0) {
					$percent = $single * 100 / $calculated_total;
				} else {
					$percent = 0;
				}
				$print_percent = round ( $percent, 2 );
				$percent = round ( $percent );
				
				echo '<div class="position-row">';
				echo '<div class="name" style="display: inline-block; width: 20%;"><i class="network-icon essb_icon_'.$k.'"></i>'.$v['name'].'</div>';
				echo '<div class="value" style="display: inline-block; width: 10%;">'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single).'</div>';
				echo '<div class="value" style="display: inline-block; width: 10%;"><span class="devices"><i class="ti-desktop"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_d).'<i class="ti-mobile"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_m).'</span></div>';
				echo '<div class="percent" style="display: inline-block; width: 8%;">'.$print_percent.'%'.'</div>';
				echo '<div class="graph" style="display: inline-block; width: 49%;"><span style="width: '.$percent.'%; display: inline-block; ">&nbsp;</span></div>';
				echo '</div>';
			}
		}
	}
	?>
	
	</div>
	
</div>

