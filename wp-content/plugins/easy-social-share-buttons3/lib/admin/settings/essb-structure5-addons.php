<?php


$current_list = array ();

if (!class_exists ( 'ESSBAddonsHelper' )) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons-helper4.php');
}

if (class_exists ( 'ESSBAddonsHelper' )) {
	$essb_addons = ESSBAddonsHelper::get_instance ();
	$current_list = $essb_addons->get_addons ();
}

if ( ! function_exists( 'get_plugins' ) ) {
    require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$plugins = ESSB_TGM_Plugin_Activation::$instance->plugins; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
$current_plugins = essb_get_site_plugins();

if (! isset ( $current_list )) {
	$current_list = array ();
}

?>
<?php include_once(ESSB3_PLUGIN_ROOT.'lib/admin/helpers/about-page-header.php'); ?>
<div class="essb-extension-list">
	<div class="essb-inner-breadcrumb"><i class="title-icon ti-package"></i><?php esc_html_e('Extensions', 'essb'); ?></div>
	<?php 
	ESSBOptionsFramework::draw_help('', '', '', array('buttons' => array(esc_html__('Need Help?') => 'https://docs.socialsharingplugin.com/knowledgebase/working-with-plugin-extensions-add-ons-installation-and-update/')));
	?>
	
	<div class="list">
	<?php 
	
	foreach ($current_list as $key => $data) {
		$price = $data['price'];
		$check_exist = $data ['check'];
		$require = isset($data['requires']) ? $data['requires'] : '';
		$version7 = isset($data['version7']) ? $data['version7'] : '';
		$actual_version = isset($data['actual_version']) ? $data['actual_version'] : '';
		$url = $data['page'];
		
		$price_tag = ($price == 'free' || $price == 'Free' || $price == 'FREE') ? '<span class="free-tag">'.esc_html__('Free', 'essb').'</span>' : '<span class="paid-tag">'.esc_html__('Paid', 'essb').'</span>';
		$is_free = ($price == 'free' || $price == 'Free' || $price == 'FREE');
		
		$is_installed = false;

		if (!is_array($check_exist)) {
			$check_exist = array();
		}
		
		$check_type = isset($check_exist['type']) ? $check_exist['type'] : 'param';
		$check_for = isset($check_exist['param']) ? $check_exist['param'] : '';
		
		$price_tag = '';
		$data['price'] = '';
		
		if ($check_for != '' && $check_type != '') {
			$is_installed = $check_type == 'param' ? defined($check_for) : function_exists($check_for);
		}
		
        $url_install = '';
        $url_activate = '';
        $url_deactivate = '';
        
        $url_command = '';
        $command_text = '';
		
        if ($is_free) {
		    
		    $url_install = wp_nonce_url(
		        add_query_arg(
		            array(
		                'plugin'           => urlencode( $key ),
		                'essb-tgmpa-install' => 'install-plugin',
		            ),
		            ESSB_TGM_Plugin_Activation::$instance->get_tgmpa_url()
		            ),
		        'essb-tgmpa-install',
		        'essb-tgmpa-nonce'
		        );
		    
		    
		    $url_command = $url_install;
		    $command_text = 'Install';
		    $command_class = 'button-primary';
		    
		    if (isset($current_plugins[$key])) {
		        $addon_slug = $current_plugins[$key]['path'];
                $url_activate = wp_nonce_url( "plugins.php?action=activate&plugin={$addon_slug}", "activate-plugin_{$addon_slug}" );
                $url_deactivate = wp_nonce_url( "plugins.php?action=deactivate&plugin={$addon_slug}", "deactivate-plugin_{$addon_slug}" );
                
                $url_command = $current_plugins[$key]['active'] ? $url_deactivate : $url_activate;
                $command_text = $current_plugins[$key]['active'] ? 'Deactivate' : 'Activate';
                $command_class = $current_plugins[$key]['active'] ? 'button-deactivate' : 'button-activate';
		    }		    
		}
		
		echo '<div class="addon-card '.($is_installed ? 'addon-card-installed' : '').'">';
		echo '<div class="header"><img src="'.esc_url(ESSB3_PLUGIN_URL .'/assets/images/'.$data['icon'].'.svg' ).'"/>'.$price_tag.'</div>';
		
		echo '<div class="main">';
		echo '<div class="title">'.$data['name'].'</div>';
		echo '<div class="desc">'.$data['description'].'</div>';		
		
		echo '</div>';
		
		echo '<div class="footer">';
		echo '<div class="action">';
		
		if (!$is_free) {
			echo '<a class="button button-orange" target="_blank" href="'.esc_url($url).'">'.esc_html__('Learn More', 'essb').' &rarr;</a>';
		}
		else {
			if ($is_installed) {
			    echo '<a class="button '.esc_attr($command_class).'" href="'.esc_url($url_command).'">' . $command_text . '</a>';
			}
			else {
				if (ESSBActivationManager::isActivated()) {
				    echo '<a class="button '.esc_attr($command_class).'" href="'.esc_url($url_command).'">' . $command_text . '</a>';
				}
				else {
					echo '<span class="not-activated">'.ESSBAdminActivate::activateToUnlock(esc_html__('Activate plugin to download', 'essb')).'</span>';
				}
			}
		}
		
		echo '</div>';
		echo '<div class="price">'.$data['price'].'</div>';
		echo '</div>';
		
		echo '</div>';
	}
	
	?>
	</div>
	
</div>