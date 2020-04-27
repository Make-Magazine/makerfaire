<?php
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

wp_enqueue_style ( 'essb-admin5-metrics', ESSB3_PLUGIN_URL . '/assets/admin/essb-admin5-stats.css', array (), ESSB3_VERSION);

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

<div class="stats-full-report">
	<a href="<?php echo esc_url( admin_url('admin.php?page=essb_redirect_analytics&tab=analytics&mode=single&post_id='.$post_id) );?>" target="_blank" class="essb-btn essb-btn-red post-stat-btn">Open Detailed Post Report &rarr;</a>
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
				echo '<div class="name"><a href="admin.php?page=essb_redirect_analytics&tab=analytics&mode=position&position='.$k.'" target="_blank">'.ESSBSocialShareAnalyticsBackEnd::position_name($k).'</a></div>';
				echo '<div class="value">'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single).'</div>';
				echo '<div class="value"><span class="devices"><i class="ti-desktop"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_d).'<i class="ti-mobile"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_m).'</span></div>';
				echo '<div class="percent">'.$print_percent.'%'.'</div>';
				echo '<div class="graph"><span style="width: '.$percent.'%; display: inline-block; ">&nbsp;</span></div>';
				echo '</div>';
			}
		}
	}
	?>
	
	</div>
	
</div>

<!-- Networks -->
<div class="stat-welcome-graph">
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
				echo '<div class="name"><i class="network-icon essb_icon_'.$k.'"></i>'.$v['name'].'</div>';
				echo '<div class="value">'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single).'</div>';
				echo '<div class="value"><span class="devices"><i class="ti-desktop"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_d).'<i class="ti-mobile"></i>'.ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single_m).'</span></div>';
				echo '<div class="percent">'.$print_percent.'%'.'</div>';
				echo '<div class="graph"><span style="width: '.$percent.'%; display: inline-block; ">&nbsp;</span></div>';
				echo '</div>';
			}
		}
	}
	?>
	
	</div>
	
</div>

