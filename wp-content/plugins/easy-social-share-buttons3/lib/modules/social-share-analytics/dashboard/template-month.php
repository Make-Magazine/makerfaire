<?php
global $essb_networks;
$mode = isset ( $_GET ["mode"] ) ? $_GET ["mode"] : "1";
$month = isset ( $_GET ['essb_month'] ) ? $_GET ['essb_month'] : '';
$date = isset ( $_GET ['date'] ) ? $_GET ['date'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';

//ESSBSocialShareAnalyticsBackEnd::init_addional_settings ();

// overall stats by social network
if ($date != '' || $position != '') {
	$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ( '', '', $date, $position );
	$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position ( '', '', $date, $position );
} else {
	$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ( $month );
	$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position ( $month );

}

// print_r($overall_stats);

$calculated_total = 0;
$networks_with_data = array ();

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( $essb_networks as $k => $v ) {
		
		$calculated_total += intval ( $overall_stats->{$k} );
		if (intval ( $overall_stats->{$k} ) != 0) {
			$networks_with_data [$k] = $calculated_total;
		}
	}
}

$device_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_device ( $month, '', $date, $position );

$today = date ( 'Y-m-d' );
$today_month = date ( 'Y-m' );

$essb_date_to = "";
$essb_date_from = "";

if ($essb_date_to == '') {
	$essb_date_to = date ( "Y-m-d" );
}

if ($essb_date_from == '') {
	$essb_date_from = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( date ( "Y-m-d" ) ) ) . "-1 month" ) );
}

if ($mode == "1") {
	$sqlObject = ESSBSocialShareAnalyticsBackEnd::getDateRangeRecords ( $essb_date_from, $essb_date_to );
	// print_r($sqlObject);
	$dataPeriodObject = ESSBSocialShareAnalyticsBackEnd::sqlDateRangeRecordConvert ( $essb_date_from, $essb_date_to, $sqlObject );
	
	$sqlMonthsData = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks_by_months ();
}

?>


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

<div class="stat-welcome-graph" style="magin-top: 30px;">
	<div class="dashboard-head">
		<h4>Positions</h4>
		<p>View how the positions on site perform. Usage of many positions may lead to lower shares due to paradox of choice.</p>
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

<?php if ($mode == 'month'): ?>
<div class="stat-welcome-graph" style="magin-top: 30px;">
	<div class="dashboard-head">
		<h4>Day by Day Report for <?php echo $month; ?></h4>
		<p></p>
		
		<?php ESSBSocialShareAnalyticsBackEnd::generate_bar_graph_month($month, $networks_with_data);?>
	</div>
</div>

<script type="text/javascript">

	var essb_analytics_date_report = window.essb_analytics_date_report = function(date) {
	
		window.location='admin.php?page=essb_redirect_analytics&tab=analytics&mode=date&date='+date;
	
	}
</script>

<?php endif; ?>

<?php if ($mode == 'date'): ?>
<div class="stat-welcome-graph" style="magin-top: 30px;">
	<div class="dashboard-head">
		<h4>Content Report for Date <?php echo $date; ?></h4>
		<p></p>
	</div>	
		<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_post( '', $networks_with_data, '', $date );?>
			</div>
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
<?php endif; ?>