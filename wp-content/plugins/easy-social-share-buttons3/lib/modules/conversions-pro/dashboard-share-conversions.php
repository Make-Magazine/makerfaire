<?php

if (!function_exists('essb_share_conversion_device_type')) {
    function essb_share_conversion_device_type($key) {
        if ($key == 'desktop') {
            return 'Desktop';
        }
        else if ($key == 'mobile') {
            return 'Mobile';
        }
        else {
            return 'Unknown';
        }
    }
}

if (!function_exists('essb_share_conversion_position_name')) {
    function essb_share_conversion_position_name($position) {
        $content_positions = essb5_available_content_positions(true);
        $button_positions = essb5_available_button_positions(true);        
        $mobile_positions = essb5_available_button_positions_mobile();
        
        $r = '';
        if ($position == 'shortcode') {
            $r = esc_html__('Shortcode', 'essb');
        }
        else {
            foreach ($content_positions as $key => $data) {
                if ($key == 'content_'.$position) {
                    $r = $data['label'];
                }
            }
            
            foreach ($button_positions as $key => $data) {
                if ($key == $position) {
                    $r = $data['label'];
                }
            }
            
            foreach ($mobile_positions as $key => $data) {
                if ($key == $position) {
                    $r = $data['label'];
                }
            }
        }
        
        if ($position == 'more_popup') {
            $r = esc_html__('More Button Social Networks Pop-up', 'essb');
        }
        
        if ($r == '') {
            $r = $position;
        }
        
        return $r;
    }
}

if (!function_exists('essb_share_conversion_filter_link')) {
    function essb_share_conversion_filter_link($filter = '') {
        $base_url = admin_url('admin.php?page=essb_redirect_conversions&tab=conversions&section=share');
        
        $base_url = add_query_arg('filter', $filter, $base_url);
        return esc_url($base_url);
    }
}

if (!function_exists('essb_share_filter_is_active')) {
    function essb_share_filter_is_active($lookfor = '') {
        $filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
        
        return $filter == $lookfor;
    }
}

if (isset($_REQUEST['filter'])) {
    ESSB_Share_Conversions_Pro::read_set_period_filter($_REQUEST['filter']);
}

$network_conversions = ESSB_Share_Conversions_Pro::read_network_conversions();
$position_conversions = ESSB_Share_Conversions_Pro::read_position_conversions();

$networks = essb_available_social_networks(true);

$totals = array('views' => 0, 'clicks' => 0, 'percent' => 0);
foreach ($network_conversions as $network => $data) {
    $view = isset($data['view']) ? $data['view'] : 0;
    $clicks = isset($data['share']) ? $data['share'] : 0;
    
    $percent = 0;
    if ($view != 0 && $clicks != 0) {
        $percent = $clicks * 100 / $view;
    }
    
    $network_conversions[$network]['percent'] = $percent;
    
    
    $totals['views'] += $view;
    $totals['clicks'] += $clicks;
}

if ($totals['views'] != 0 && $totals['clicks'] != 0) {
    $totals['percent'] = $totals['clicks'] * 100 / $totals['views'];
}

$network_conversions = ESSB_Share_Conversions_Pro::data_sort_desc($network_conversions, 'percent');

foreach ($position_conversions as $position => $data) {
    $view = isset($data['view']) ? $data['view'] : 0;
    $clicks = isset($data['share']) ? $data['share'] : 0;
    
    $percent = 0;
    if ($view != 0 && $clicks != 0) {
        $percent = $clicks * 100 / $view;
    }
    
    $position_conversions[$position]['percent'] = $percent;
}

$position_conversions = ESSB_Share_Conversions_Pro::data_sort_desc($position_conversions, 'percent');

$device_conversions = ESSB_Share_Conversions_Pro::read_device_conversions();


?>


<div class="essb-flex-grid-c c12 essb-heading sub7"><span class="icon"><i class="ti-dashboard"></i></span><div><em>Share Conversions</em></div></div>

<div class="conversion-totals">
	<div class="block">
		<div class="title">Total Views</div>
		<div class="value"><?php echo $totals['views']; ?></div>
	</div>
	<div class="block">
		<div class="title">Total Conversions</div>
		<div class="value"><?php echo $totals['clicks']; ?></div>
	</div>
	<div class="block">
		<div class="title">Conversion Rate</div>
		<div class="value"><?php echo number_format($totals['percent'], 1).'%'; ?></div>
	</div>
</div>

<div class="conversion-filters">
	<div class="buttons">
		<a href="<?php echo essb_share_conversion_filter_link(''); ?>" class="filter-button <?php echo essb_share_filter_is_active('') ? 'active' : ''; ?>">All</a>
		<a href="<?php echo essb_share_conversion_filter_link('1'); ?>" class="filter-button <?php echo essb_share_filter_is_active('1') ? 'active' : ''; ?>">Today</a>
		<a href="<?php echo essb_share_conversion_filter_link('7'); ?>" class="filter-button <?php echo essb_share_filter_is_active('7') ? 'active' : ''; ?>">Last 7 days</a>
		<a href="<?php echo essb_share_conversion_filter_link('30'); ?>" class="filter-button <?php echo essb_share_filter_is_active('30') ? 'active' : ''; ?>">Last 30 days</a>
	</div>
</div>

<!-- Detailed -->
<div class="conversion-report-table">
	<div class="table-content">
    	<div class="table-header">
    		<div class="header-title">Element</div>
    		<div class="header-item">Views</div>
    		<div class="header-item">Shares</div>
    		<div class="header-item">Conversion Rate</div>
    	</div>
    	
    	<div class="table-row">
    		<div class="title">Device</div>
    	</div>
    	
		<?php 
    	foreach ($device_conversions as $key => $data) {
    	    $view = isset($data['view']) ? $data['view'] : 0;
    	    $clicks = isset($data['share']) ? $data['share'] : 0;
    	    
    	    $percent = 0;
    	    if ($view != 0 && $clicks != 0) {
    	        $percent = $clicks * 100 / $view;
    	    }
    	    
    	    
    	    ?>
    	    <div class="table-row">
    	    	<div class="row-name"><?php echo essb_share_conversion_device_type($key); ?></div>
    	    	<div class="row-value"><?php echo $view; ?></div>
    	    	<div class="row-value"><?php echo $clicks; ?></div>
    	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
    	    </div>
    	    <?php 
        }
        ?>
    	
    	<div class="table-row">
    		<div class="title">Networks</div>
    	</div>
	
    	<?php 
    	foreach ($network_conversions as $network => $data) {
    	    $view = isset($data['view']) ? $data['view'] : 0;
    	    $clicks = isset($data['share']) ? $data['share'] : 0;
    	    
    	    $percent = 0;
    	    if ($view != 0 && $clicks != 0) {
    	        $percent = $clicks * 100 / $view;
    	    }
    	    
    	    
    	    ?>
    	    <div class="table-row">
    	    	<div class="row-name"><?php echo (isset($networks[$network]) ? $networks[$network]['name'] : $network); ?></div>
    	    	<div class="row-value"><?php echo $view; ?></div>
    	    	<div class="row-value"><?php echo $clicks; ?></div>
    	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
    	    </div>
    	    <?php 
        }
        ?>
        
    	<div class="table-row">
    		<div class="title">Positions</div>
    	</div>
	
    	<?php 
    	foreach ($position_conversions as $position => $data) {
    	    $view = isset($data['view']) ? $data['view'] : 0;
    	    $clicks = isset($data['share']) ? $data['share'] : 0;
    	    
    	    $percent = 0;
    	    if ($view != 0 && $clicks != 0) {
    	        $percent = $clicks * 100 / $view;
    	    }
    	    
    	    $networks_to_position = ESSB_Share_Conversions_Pro::read_network_conversions($position);
    	    
    	    foreach ($networks_to_position as $network => $data) {
    	        $view = isset($data['view']) ? $data['view'] : 0;
    	        $clicks = isset($data['share']) ? $data['share'] : 0;
    	        
    	        $percent = 0;
    	        if ($view != 0 && $clicks != 0) {
    	            $percent = $clicks * 100 / $view;
    	        }
    	        
    	        $networks_to_position[$network]['percent'] = $percent;
    	    }
    	    
    	    $networks_to_position = ESSB_Share_Conversions_Pro::data_sort_desc($networks_to_position, 'percent');
    	    
    	    ?>
    	    <div class="table-row conversion-parent">
    	    	<div class="row-name"><?php echo essb_share_conversion_position_name($position); ?></div>
    	    	<div class="row-value"><?php echo $view; ?></div>
    	    	<div class="row-value"><?php echo $clicks; ?></div>
    	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
    	    </div>
    	    <?php 
    	    
    	    foreach ($networks_to_position as $network => $data) {
    	        $view = isset($data['view']) ? $data['view'] : 0;
    	        $clicks = isset($data['share']) ? $data['share'] : 0;
    	        
    	        $percent = 0;
    	        if ($view != 0 && $clicks != 0) {
    	            $percent = $clicks * 100 / $view;
    	        }
    	        
    	        
    	        ?>
        	    <div class="table-row conversion-related">
        	    	<div class="row-name"><?php echo (isset($networks[$network]) ? $networks[$network]['name'] : $network); ?></div>
        	    	<div class="row-value"><?php echo $view; ?></div>
        	    	<div class="row-value"><?php echo $clicks; ?></div>
        	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
        	    </div>
        	    <?php 
            } // network_to_position    	    
        } // position
        ?>        
	</div>	
</div>

<div class="conversion-filters">
	<div class="buttons">
		<a href="#" class="ao-clear-conversion-data-share"><i class="fa fa-times"></i> Reset conversion data</a>
	</div>
</div>
