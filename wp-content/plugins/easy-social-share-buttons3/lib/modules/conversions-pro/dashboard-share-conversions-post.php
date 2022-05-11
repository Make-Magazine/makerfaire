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

if (!function_exists('essb_share_conversions_filter_action')) {
    function essb_share_conversions_filter_action($values = array()) {
        $filter = isset($_REQUEST['conversions']) ? $_REQUEST['conversions'] : '';
        
        $r = array();
        
        foreach ($values as $key => $data) {
            if ($filter != '') {
                $share_value = isset($data['share']) ? $data['share'] : '';
                
                if ($filter == '1' && $share_value > 0) {
                    $r[$key] = $data;
                }
                
                if ($filter == '0' && $share_value <= 0) {
                    $r[$key] = $data;
                }
            }
            else {
                $r[$key] = $data;
            }
        }
        
        return $r;
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

if (!function_exists('essb_share_conversion_filter_link_post')) {
    function essb_share_conversion_filter_link_post($filter = '') {
        $base_url = admin_url('admin.php?page=essb_redirect_conversions&tab=conversions&section=shareposts');
        
        $base_url = add_query_arg('filter', $filter, $base_url);
        return esc_url($base_url);
    }
}

if (!function_exists('essb_share_conversion_filter_link_post_has')) {
    function essb_share_conversion_filter_link_post_has($filter = '') {
        $base_url = admin_url('admin.php?page=essb_redirect_conversions&tab=conversions&section=shareposts');
        
        $has_period = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
        
        if (!empty($has_period)) {
            $base_url = add_query_arg('filter', $has_period, $base_url);
        }
        
        $base_url = add_query_arg('conversions', $filter, $base_url);
        return esc_url($base_url);
    }
}

if (!function_exists('essb_share_filter_is_active')) {
    function essb_share_filter_is_active($lookfor = '') {
        $filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
        
        return $filter == $lookfor;
    }
}

if (!function_exists('essb_share_filter_is_active_has')) {
    function essb_share_filter_is_active_has($lookfor = '') {
        $filter = isset($_REQUEST['conversions']) ? $_REQUEST['conversions'] : '';
        
        return $filter == $lookfor;
    }
}

if (isset($_REQUEST['filter'])) {
    ESSB_Share_Conversions_Pro::read_set_period_filter($_REQUEST['filter']);
}

$post_conversions = ESSB_Share_Conversions_Pro::read_post_conversions();

if (isset($_REQUEST['conversions'])) {
    $post_conversions = essb_share_conversions_filter_action($post_conversions);
}

$totals = array('views' => 0, 'clicks' => 0, 'percent' => 0);
foreach ($post_conversions as $network => $data) {
    $view = isset($data['view']) ? $data['view'] : 0;
    $clicks = isset($data['share']) ? $data['share'] : 0;
    
    $percent = 0;
    if ($view != 0 && $clicks != 0) {
        $percent = $clicks * 100 / $view;
    }
    
    $post_conversions[$network]['percent'] = $percent;
    
    
    $totals['views'] += $view;
    $totals['clicks'] += $clicks;
}

if ($totals['views'] != 0 && $totals['clicks'] != 0) {
    $totals['percent'] = $totals['clicks'] * 100 / $totals['views'];
}

$post_conversions = ESSB_Share_Conversions_Pro::data_sort_desc($post_conversions, 'percent');

?>


<div class="essb-flex-grid-c c12 essb-heading sub7"><span class="icon"><i class="ti-dashboard"></i></span><div><em>Post Share Conversions</em></div></div>

<div class="conversion-filters">
	<div class="buttons">
		<a href="<?php echo essb_share_conversion_filter_link_post(''); ?>" class="filter-button <?php echo essb_share_filter_is_active('') ? 'active' : ''; ?>">All</a>
		<a href="<?php echo essb_share_conversion_filter_link_post('1'); ?>" class="filter-button <?php echo essb_share_filter_is_active('1') ? 'active' : ''; ?>">Today</a>
		<a href="<?php echo essb_share_conversion_filter_link_post('7'); ?>" class="filter-button <?php echo essb_share_filter_is_active('7') ? 'active' : ''; ?>">Last 7 days</a>
		<a href="<?php echo essb_share_conversion_filter_link_post('30'); ?>" class="filter-button <?php echo essb_share_filter_is_active('30') ? 'active' : ''; ?>">Last 30 days</a>
	</div>
	
	<div class="buttons">
		<a href="<?php echo essb_share_conversion_filter_link_post_has(''); ?>" class="filter-button <?php echo essb_share_filter_is_active_has('') ? 'active' : ''; ?>">All</a>
		<a href="<?php echo essb_share_conversion_filter_link_post_has('1'); ?>" class="filter-button <?php echo essb_share_filter_is_active_has('1') ? 'active' : ''; ?>" title="Has conversions">With</a>
		<a href="<?php echo essb_share_conversion_filter_link_post_has('0'); ?>" class="filter-button <?php echo essb_share_filter_is_active_has('0') ? 'active' : ''; ?>" title="Without conversions">Without</a>
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
    	    	
		<?php 
		foreach ($post_conversions as $key => $data) {
    	    $view = isset($data['view']) ? $data['view'] : 0;
    	    $clicks = isset($data['share']) ? $data['share'] : 0;
    	    
    	    $percent = 0;
    	    if ($view != 0 && $clicks != 0) {
    	        $percent = $clicks * 100 / $view;
    	    }
    	    
    	    
    	    ?>
    	    <div class="table-row">
    	    	<div class="row-name"><a href="<?php echo get_permalink($key); ?>" target="_blank"><?php echo get_the_title($key); ?></a></div>
    	    	<div class="row-value"><?php echo $view; ?></div>
    	    	<div class="row-value"><?php echo $clicks; ?></div>
    	    	<div class="row-value"><?php echo number_format($percent, 1).'%'; ?></div>
    	    </div>
    	    <?php 
        }
        ?>
    	
	</div>	
</div>

