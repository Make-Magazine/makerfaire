<?php
/**
 * Main live customizer template containing cotrols used to display on screen
 */
?>

<div class="essb-live-customizer essb-live-customizer-main">
		<div class="essb-live-customizer-options-title">
			<span class="title"></span><i
				class="fa fa-arrow-right essb-live-customizer-back"
				title="Back to main menu"></i>
		</div>
	<div class="essb-live-customizer-close">
		<a href="#" class="essb-live-customizer-close-icon" title="<?php esc_html_e('Close', 'essb'); ?>"><i
			class="fa fa-close"></i></a>
	</div>

	<!-- Customizer Navigation -->
	<div class="essb-live-customizer-icons essb-scroll-effect">
		<div class="customizer-box customizer-close active"
			data-title="<?php esc_html_e('Close', 'essb'); ?>" data-options="close">
			<div class="icon">
				<i class="ti-close"></i>
			</div>
			<div class="title"><?php esc_html_e('Close', 'essb'); ?></div>
		</div>
	
		<div class="customizer-box customizer-share"
			data-title="<?php esc_html_e('Post Share Information', 'essb'); ?>" data-options="share">
			<div class="icon">
				<i class="ti-new-window"></i>
			</div>
			<div class="title"><?php esc_html_e('Post Share Information', 'essb'); ?></div>
		</div>
		
		<div class="customizer-box customizer-mobile"
			data-title="<?php esc_html_e('Share Counter Information', 'essb'); ?>"
			data-options="counters">
			<div class="icon">
				<i class="ti-infinite"></i>
			</div>
			<div class="title"><?php esc_html_e('Share Counter Information', 'essb'); ?></div>
		</div>

		<div class="customizer-box customizer-layout"
			data-title="Button Positions" data-options="positions" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_where&tab=where&section=positions')); ?>">
			<div class="icon">
				<i class="ti-layout"></i>
			</div>
			<div class="title"><?php esc_html_e('Control Share Button Positions', 'essb'); ?></div>
		</div>

		<?php 
		if (essb_option_value('functions_mode_mobile') != 'auto' && essb_option_value('functions_mode_mobile') != 'deactivate' && !essb_option_bool_value('activate_automatic_mobile')) {		
		?>
		
		<div class="customizer-box customizer-mobile"
			data-title="<?php esc_html_e('Mobile Settings', 'essb'); ?>" data-options="mobile" data-url="<?php echo esc_url(admin_url('admin.php?page=essb_redirect_where&tab=where&section=mobile'));?>">
			<div class="icon">
				<i class="ti-mobile"></i>
			</div>
			<div class="title"><?php esc_html_e('Mobile Settings', 'essb'); ?></div>
		</div>
		
		<?php 
		}
		?>
		

	</div>

	<!-- Customizer Settings Panel -->
	<div class="essb-live-customizer-options essb-scroll-effect">
		<div class="essb-live-customizer-options-content"></div>
	</div>
</div>