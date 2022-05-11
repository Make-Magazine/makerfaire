<?php

if (!function_exists('essb_subscribe_conversion_position_name')) {
    function essb_subscribe_conversion_position_name($position) {
        
        $r = '';
        
        if ($position == 'shortcode') {
            $r = esc_html__('Shortcode', 'essb');
        }
        
        if ($position == 'widget') {
            $r = esc_html__('Widget', 'essb');
        }
        
        if ($position == 'belowcontent') {
            $r = esc_html__('Subscribe Form Below Content', 'essb');
        }
        
        
        if (strpos($position, 'flyout') !== false) {
            $position_data = explode('-', $position);
            
            $r = esc_html__('Flyout', 'essb');
            if ($position_data[1] == 'time') {
                $r .= ': Timed delay';
            }
            if ($position_data[1] == 'scroll') {
                $r .= ': On scroll';
            }
            if ($position_data[1] == 'exit') {
                $r .= ': Exit intent';
            }
            if ($position_data[1] == 'manual') {
                $r .= ': Manual';
            }
        }
        
        if (strpos($position, 'booster') !== false) {
            $position_data = explode('-', $position);
            
            $r = esc_html__('Booster Pop-up', 'essb');
            if ($position_data[1] == 'time') {
                $r .= ': Timed delay';
            }
            if ($position_data[1] == 'scroll') {
                $r .= ': On scroll';
            }
            if ($position_data[1] == 'exit') {
                $r .= ': Exit intent';
            }
            if ($position_data[1] == 'manual') {
                $r .= ': Manual';
            }
        }
        
        if (strpos($position, 'locker') !== false) {
            $position_data = explode('-', $position);
            
            $r = esc_html__('Locker', 'essb');
            if ($position_data[1] == 'time') {
                $r .= ': Timed delay';
            }
            if ($position_data[1] == 'scroll') {
                $r .= ': On scroll';
            }
            if ($position_data[1] == 'exit') {
                $r .= ': Exit intent';
            }
            if ($position_data[1] == 'manual') {
                $r .= ': Manual';
            }
        }
        
        
        if ($r == '') {
            $r = $position;
        }
        
        return $r;
    }
}

if (!function_exists('essb_subscribe_conversion_filter_link')) {
    function essb_subscribe_conversion_filter_link($filter = '') {
        $base_url = admin_url('admin.php?page=essb_redirect_conversions&tab=conversions&section=subscribe');
        
        $base_url = add_query_arg('filter', $filter, $base_url);
        return esc_url($base_url);
    }
}

if (!function_exists('essb_subscribe_filter_is_active')) {
    function essb_subscribe_filter_is_active($lookfor = '') {
        $filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
        
        return $filter == $lookfor;
    }
}

if (isset($_REQUEST['filter'])) {
    ESSB_Subscribe_Conversions_Pro::read_set_period_filter($_REQUEST['filter']);
}

$design_conversions = ESSB_Subscribe_Conversions_Pro::read_design_conversions();
$position_conversions = ESSB_Subscribe_Conversions_Pro::read_position_conversions();

$designs = essb_optin_designs();

$totals = array('views' => 0, 'clicks' => 0, 'percent' => 0);
foreach ($design_conversions as $design => $data) {
    $view = isset($data['view']) ? $data['view'] : 0;
    $clicks = isset($data['subscribe_ok']) ? $data['subscribe_ok'] : 0;
    
    $totals['views'] += $view;
    $totals['clicks'] += $clicks;
}

if ($totals['views'] != 0 && $totals['clicks'] != 0) {
    $totals['percent'] = $totals['clicks'] * 100 / $totals['views'];
}

?>

<div class="essb-flex-grid-c c12 essb-heading sub7"><span class="icon"><i class="ti-dashboard"></i></span><div><em>Subscribe Conversions</em></div></div>

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
		<a href="<?php echo essb_subscribe_conversion_filter_link(''); ?>" class="filter-button <?php echo essb_subscribe_filter_is_active('') ? 'active' : ''; ?>">All</a>
		<a href="<?php echo essb_subscribe_conversion_filter_link('1'); ?>" class="filter-button <?php echo essb_subscribe_filter_is_active('1') ? 'active' : ''; ?>">Today</a>
		<a href="<?php echo essb_subscribe_conversion_filter_link('7'); ?>" class="filter-button <?php echo essb_subscribe_filter_is_active('7') ? 'active' : ''; ?>">Last 7 days</a>
		<a href="<?php echo essb_subscribe_conversion_filter_link('30'); ?>" class="filter-button <?php echo essb_subscribe_filter_is_active('30') ? 'active' : ''; ?>">Last 30 days</a>
	</div>
</div>

<!-- Detailed -->
<div class="conversion-report-table">
	<div class="table-content">
    	<div class="table-header">
    		<div class="header-title">Element</div>
    		<div class="header-item">Views</div>
    		<div class="header-item">Conversions</div>
    		<div class="header-item">Successful</div>
    		<div class="header-item">Failed</div>
    		<div class="header-item">Conversion Rate</div>
    	</div>
    	
    	<div class="table-row">
    		<div class="title">Designs</div>
    	</div>
	
    	<?php 
    	foreach ($design_conversions as $design => $data) {
    	    $view = isset($data['view']) ? $data['view'] : 0;
    	    $clicks = isset($data['subscribe_ok']) ? $data['subscribe_ok'] : 0;
    	    $clicks_fail = isset($data['subscribe_fail']) ? $data['subscribe_fail'] : 0;
    	    
    	    $percent = 0;
    	    if ($view != 0 && $clicks != 0) {
    	        $percent = $clicks * 100 / $view;
    	    }
    	    
    	    
    	    ?>
    	    <div class="table-row">
    	    	<div class="row-name"><?php echo isset($designs[$design]) ? $designs[$design] : $design; ?></div>
    	    	<div class="row-value"><?php echo $view; ?></div>
    	    	<div class="row-value"><?php echo $clicks + $clicks_fail; ?></div>
    	    	<div class="row-value"><?php echo $clicks; ?></div>
    	    	<div class="row-value"><?php echo $clicks_fail; ?></div>
    	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
    	    </div>
    	    <?php 
        }
        ?>
        
    	<div class="table-row">
    		<div class="title">Positions</div>
    	</div>
	
    	<?php 
    	foreach ($position_conversions as $design => $data) {
    	    $view = isset($data['view']) ? $data['view'] : 0;
    	    $clicks = isset($data['subscribe_ok']) ? $data['subscribe_ok'] : 0;
    	    $clicks_fail = isset($data['subscribe_fail']) ? $data['subscribe_fail'] : 0;
    	    
    	    $percent = 0;
    	    if ($view != 0 && $clicks != 0) {
    	        $percent = $clicks * 100 / $view;
    	    }
    	    
    	    
    	    ?>
    	    <div class="table-row">
    	    	<div class="row-name"><?php echo essb_subscribe_conversion_position_name($design); ?></div>
    	    	<div class="row-value"><?php echo $view; ?></div>
    	    	<div class="row-value"><?php echo $clicks + $clicks_fail; ?></div>
    	    	<div class="row-value"><?php echo $clicks; ?></div>
    	    	<div class="row-value"><?php echo $clicks_fail; ?></div>
    	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
    	    </div>
    	    <?php 
        }
        ?>        
	</div>	
</div>

<div class="conversion-filters">
	<div class="buttons">
		<a href="#" class="ao-clear-conversion-data-subscribe"><i class="fa fa-times"></i> Reset conversion data</a>
	</div>
</div>
