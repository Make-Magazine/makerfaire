<?php
/**
 * Display information for post share counters
 */

if (!class_exists('ESSBLiveCustomizerControls')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/controls/controls.php');
}


global $post_id;
$custom = get_post_custom ( $post_id );
$listOfNetworks = essb_available_social_networks();

?>

<div class="section-counters">
	<div class="row description">
		<?php esc_html_e('Below you can see information for the current post share counters based on the last update. You can also see when the next update will happen or call a manual share counter update.', 'essb'); ?>
	</div>
	
	<div class="row counter-update-button">
		<a href="<?php echo esc_url(admin_url('admin.php?page=essb_options&tab=social&section=sharecnt')); ?>" class="essb-composer-button essb-composer-blue"><i class="fa fa-cog"></i> <?php esc_html_e('Configure Share Counters', 'essb'); ?></a>
	</div>
	
	
	<?php if (essb_option_value('counter_mode') != ''): ?>
	<div class="customizer-inner-title"><span><?php esc_html_e('Share Counters for', 'essb')?><br/><?php echo esc_attr(get_the_title($post_id));?></span></div>
	
	<?php 
	echo '<div class="row">';
	foreach ($listOfNetworks as $key => $data) {
		$value = isset ( $custom ["essb_c_".$key] ) ? $custom ["essb_c_".$key] [0] : "";
	
		if (intval($value) != 0) {
			echo '<div class="counter-panel">';
			echo '  <div class="counter-icon">';
			echo '     <i class="essb_icon_'.esc_attr($key).'"></i>';
			echo '  </div>';
			echo '  <div class="counter-details">';
			echo '     <div class="network-name">';
			echo esc_attr($data["name"]);
			echo '     </div>';
			echo '     <div class="counter-value">';
			echo '     '.esc_attr($value);
			echo '     </div>';
			echo '  </div>';
			echo '</div>';
		}
	}
	echo '</div>';
	$essb_cache_expire = isset ( $custom ['essb_cache_expire'] ) ? $custom ['essb_cache_expire'] [0] : "";
	
	echo '<div class="counter-update-message">';
	
	if ($essb_cache_expire != '') {
		echo esc_html__('Next counter update will be at ', 'essb').date(DATE_RFC822, $essb_cache_expire);
	}
	else {
		echo esc_html__('Counter update information is not available', 'essb');
	}
	
	echo '</div>';
	?>
	
	<div class="row counter-update-button">
		<a href="<?php esc_url(get_permalink($post_id).'?essb_counter_update=true'); ?>" class="essb-composer-button essb-composer-blue"><i class="fa fa-refresh"></i> <?php esc_html_e('Update Counters', 'essb'); ?></a>
	</div>
	<div class="row description">
		<?php esc_html_e('Do an immediate share counter update for the current post. The button will just remove the last update time to start the update. It will not touch any previous share values.', 'essb'); ?>
	</div>
	
	<div class="row counter-update-button">
		<a href="<?php esc_url(get_permalink($post_id).'?essb_clear_cached_counters=true'); ?>" class="essb-composer-button essb-composer-blue"><i class="fa fa-refresh"></i> <?php esc_html_e('Update Counters for Entire Site', 'essb'); ?></a>
	</div>
	<div class="row description">
		<?php esc_html_e('Do an immediate share counter update for the entire site. The button will just remove the last update time to start refreshing the values on next post load. It will not touch any previous share values.', 'essb'); ?>
	</div>
	
	<div class="row counter-update-button">
		<a href="<?php esc_url(get_permalink($post_id).'?essb_clear_counters_history=true'); ?>" class="essb-composer-button essb-composer-red"><i class="fa fa-refresh"></i> <?php esc_html_e('Clear Counter History', 'essb'); ?></a>
	</div>
	<div class="row description">
		<?php esc_html_e('The button will clear any stored counters on site and the last update period. This will make the entire counter information to update with the next load of a post.', 'essb'); ?>
	</div>
		
	<?php endif; ?>

</div>