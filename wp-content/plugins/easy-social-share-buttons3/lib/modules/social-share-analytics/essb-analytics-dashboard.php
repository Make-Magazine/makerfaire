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
