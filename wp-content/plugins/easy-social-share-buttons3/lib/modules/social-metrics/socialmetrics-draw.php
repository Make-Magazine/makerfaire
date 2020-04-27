<?php
/**
 * Draw information for popular posts inside Metrics based on last updated details
 */

$esml_data = new ESSBSocialMetricsDataHolder ();
$esml_data->get_posts();

?>

<div class="wrap essb-page-welcome about-wrap essb-wrap-metricslite">
	<h1>Social Metrics by Easy Social Share Buttons for WordPress</h1>

	<div class="about-text">
		Social Metrics data provide details for shares over social networks you are using on your site. To collect and update data you need to have share counters working on site.
	</div>
	<div class="wp-badge essb-page-logo essb-logo">
		<span class="essb-version"><?php echo sprintf( esc_html__( 'Version %s', 'essb' ), ESSB3_VERSION )?></span>
	</div>
</div>

<div class="wrap">

	<div class="essb-clear"></div>

	<div class="essb-title-panel">
	<form id="easy-social-metrics-lite" method="get" action="admin.php?page=easy-social-metrics-lite">
	<input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ?>" />
	<?php
	$range = (isset ( $_GET ['range'] )) ? $_GET ['range'] : 0;
	?>
	    			<label for="range">Show only:</label> <select name="range">
			<option value="1"
				<?php if ($range == 1) echo 'selected="selected"'; ?>>Items
				published within 1 Month</option>
			<option value="3"
				<?php if ($range == 3) echo 'selected="selected"'; ?>>Items
				published within 3 Months</option>
			<option value="6"
				<?php if ($range == 6) echo 'selected="selected"'; ?>>Items
				published within 6 Months</option>
			<option value="12"
				<?php if ($range == 12) echo 'selected="selected"'; ?>>Items
				published within 12 Months</option>
			<option value="0"
				<?php if ($range == 0) echo 'selected="selected"'; ?>>Items
				published anytime</option>
		</select>
	    					
	    					<?php do_action( 'esml_dashboard_query_options' ); // Allows developers to add additional sort options ?>
	    
	    					<input type="submit" name="filter" id="submit_filter"
			class="button" value="Filter"> 
	    			<?php
								?>
								</form>
	</div>

	<!-- dashboard start -->
	<div class="essb-dashboard">

		<div class="row">

			<div class="onecol">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4><?php esc_html_e('Networks', 'essb'); ?></h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					
					$esml_data->output_total_result_modern();

					?>
					</div>
				</div>
			</div>
			
			<div class="onecol">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4>Trending Content by Social Network</h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_trending_content();
					?>
					</div>
				</div>
			</div>

			<div class="onecol">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4>Top Shared Content by Social Network</h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_total_content();
					?>
					</div>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="essb-dashboard-panel">
				<div class="essb-dashboard-panel-title">
					<h4>Post Details</h4>
				</div>
				<div class="essb-dashboard-panel-content">
					<?php
					
					$esml_data->output_main_result();
					
					?>
					</div>
			</div>
		</div>

	</div>
</div>

