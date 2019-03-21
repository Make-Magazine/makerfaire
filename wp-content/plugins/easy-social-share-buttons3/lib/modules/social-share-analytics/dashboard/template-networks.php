<?php
$mode = isset ( $_GET ["mode"] ) ? $_GET ["mode"] : "1";
$month = isset ( $_GET ['essb_month'] ) ? $_GET ['essb_month'] : '';
$date = isset ( $_GET ['date'] ) ? $_GET ['date'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';

$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ( '', '', $date, $position );
$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position ( '', '', $date, $position );


$best_position_value = 0;
$best_position_key = "";
$best_position_percent = 0;

$best_network_value = 0;
$best_network_key = "";
$best_network_percent = 0;

if (isset ( $overall_stats )) {
  foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {

    $key = "position_" . $k;
    $keyd = "position_d_" . $k;
    $keym = "position_m_" . $k;

    $single = intval ( $position_stats->{$key} );
	$single_d = isset($position_stats->{$keyd})  ? $position_stats->{$keyd} : 0;
	$single_m = isset($position_stats->{$keym})  ? $position_stats->{$keym} : 0;
    
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


$device_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_device ( $month, '', $date, $position );
$desktop = 0;
$mobile = 0;
$calculated_total = 0;
if (isset ( $device_stats )) {
  $desktop = $device_stats->desktop;
  $mobile = $device_stats->mobile;

  $calculated_total = $device_stats->cnt;

  if ($calculated_total != 0) {
    $percentd = $desktop * 100 / $calculated_total;
  } else {
    $percentd = 0;
  }
  $print_percentd = round ( $percentd, 2 );

  if ($calculated_total != 0) {
    $percentm = $mobile * 100 / $calculated_total;
  } else {
    $percentm = 0;
  }
  $print_percentm = round ( $percentm, 2 );
}

$period_today = date('Y-m-d');
$period_7days = date('Y-m-d', strtotime('-7 day', strtotime(date("Y-m-d"))));
$period_30days = date('Y-m-d', strtotime('-30 day', strtotime(date("Y-m-d"))));

$value7_days = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position_for_period($best_position_key, $period_7days, $period_today);
$value30_days = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position_for_period($best_position_key, $period_30days, $period_today);

if ($calculated_total != 0) {
  $best_position_percent = $best_position_value * 100 / $calculated_total;
  $best_position_percent = round ( $best_position_percent, 1 );
}

$nvalue7_days = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_network_for_period($best_network_key, $period_7days, $period_today);
$nvalue30_days = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_network_for_period($best_network_key, $period_30days, $period_today);
if ($calculated_total != 0) {
  $best_network_percent = $best_network_value * 100 / $calculated_total;
  $best_network_percent = round ( $best_network_percent, 1 );
}

?>

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
      <a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=positions" class="essb-btn essb-btn-green1" style="float: right; padding: 6px 12px;">View All</a>
    </div>

    <div class="footer">
      <label class="value" style="margin-right: 0px;"><?php echo $best_position_percent; ?> %</label>
      <label class="desc" style="margin-right: 10px;">Of all clicks</label>

      <label class="desc">Last 7 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($value7_days->cnt); ?></label>

      <label class="desc">Last 30 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($value30_days->cnt); ?></label>
    </div>
  </div>
  <div class="panel">
    <h4>Best Social Network: <strong><?php echo $best_network_key; ?></strong></h4>

    <div class="bold-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($best_network_value); ?>
	<a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=networks" class="essb-btn essb-btn-green1" style="float: right; padding: 6px 12px;">View All</a>
    </div>

    <div class="footer">
      <label class="value" style="margin-right: 0px;"><?php echo $best_network_percent; ?> %</label>
      <label class="desc" style="margin-right: 10px;">Of all clicks</label>

      <label class="desc">Last 7 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($nvalue7_days->cnt); ?></label>

      <label class="desc">Last 30 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($nvalue30_days->cnt); ?></label>
    </div>

  </div>
</div>

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
				echo '<div class="name" style="display: inline-block; width: 20%;"><a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=network&network='.$k.'"><i class="network-icon essb_icon_'.$k.'"></i>'.$v['name'].'</a></div>';
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
