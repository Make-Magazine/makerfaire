<?php
$mode = isset ( $_GET ["mode"] ) ? $_GET ["mode"] : "1";
$month = isset ( $_GET ['essb_month'] ) ? $_GET ['essb_month'] : '';
$date = isset ( $_GET ['date'] ) ? $_GET ['date'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';
$network = isset($_GET['network']) ? $_GET['network'] : '';

$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ( '', '', $date, $position, $network );
$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position ( '', '', $date, $position, $network );


$best_position_value = 0;
$best_position_key = "";
$best_position_percent = 0;

$best_network_value = 0;
$best_network_key = "";
$best_network_percent = 0;

$networks_with_data = array();

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
    
    if ($single > 0) {
    	$networks_with_data[$k] = $single;
    }

    if ($k == $network) {
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
    <h4>Report for Network: <strong><?php echo $best_network_key; ?></strong></h4>

    <div class="bold-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($best_network_value); ?>
      <a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=positions" class="essb-btn essb-btn-green1" style="float: right; padding: 6px 12px;">View All</a>
    </div>

    <div class="footer">
     
      <label class="desc">Last 7 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($nvalue7_days->cnt); ?></label>

      <label class="desc">Last 30 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($nvalue30_days->cnt); ?></label>
    </div>
  </div>
  <div class="panel">
    <h4>Best Position: <strong><?php echo ESSBSocialShareAnalyticsBackEnd::position_name($best_position_key); ?></strong></h4>

    <div class="bold-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($best_position_value); ?>
	<a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=networks" class="essb-btn essb-btn-green1" style="float: right; padding: 6px 12px;">View All</a>
    </div>

    <div class="footer">
      <label class="value" style="margin-right: 0px;"><?php echo $best_network_percent; ?> %</label>
      <label class="desc" style="margin-right: 10px;">Of all clicks</label>

      <label class="desc">Last 7 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($value7_days->cnt); ?></label>

      <label class="desc">Last 30 days:</label>
      <label class="value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($value30_days->cnt); ?></label>
    </div>

  </div>
</div>

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
				echo '<div class="name" style="display: inline-block; width: 20%;"><a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=position&position='.$k.'">'.ESSBSocialShareAnalyticsBackEnd::position_name($k).'</a></div>';
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

<div class="stat-content-report">
	<div class="dashboard-head">
		<h4>Content Report</h4>
		<p>View all posts or pages that are used with share buttons for the selected location</p>
	</div>
	
<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_post( '', $networks_with_data, '', $date, $position, $network );?>
</div>


<script type="text/javascript">
jQuery(document).ready(function($){
    
	if (jQuery("#table-posts").length)
		jQuery('#table-posts').DataTable({ pageLength: 50, scrollX: true, order: [[1, 'desc']], fixedColumns: true});

	var essb_analytics_date_report = window.essb_analytics_date_report = function(date) {

		window.location='admin.php?page=essb_redirect_analytics&tab=analytics&mode=4&date='+date;

	}

	var essb_analytics_position_report = function(position) {

		window.location='admin.php?page=essb_redirect_analytics&tab=analytics&mode=5&position='+position;

	}
});
	
</script>