<?php


$current_list = array ();

if (!class_exists ( 'ESSBAddonsHelper' )) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons-helper4.php');
}

if (class_exists ( 'ESSBAddonsHelper' )) {
	$essb_addons = ESSBAddonsHelper::get_instance ();
	$current_list = $essb_addons->get_addons ();
}


if (! isset ( $current_list )) {
	$current_list = array ();
}

?>

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
		
		if ($check_for != '' && $check_type != '') {
			$is_installed = $check_type == 'param' ? defined($check_for) : function_exists($check_for);
		}
		
		
		echo '<div class="addon-card '.($is_installed ? 'addon-card-installed' : '').'">';
		echo '<div class="header"><img src="'.esc_url(ESSB3_PLUGIN_URL .'/assets/images/'.$data['icon'].'.svg' ).'"/>'.$price_tag.'</div>';
		
		echo '<div class="main">';
		echo '<div class="title">'.$data['name'].'</div>';
		echo '<div class="desc">'.$data['description'].'</div>';
		
		if ($actual_version != '') {
		    echo '<div class="essb-column-compatibility column-version">';
		    echo '<span class="compatibility-compatible">'.esc_html__('Latest Version: ', 'essb').'<b>'.esc_attr($actual_version).'</b>'.'</span>';
		    echo '</div>';
		}
		
		if ($require != '' || $version7 == '') {
		echo '<div class="essb-column-compatibility column-compatibility">';
			if ($version7 == '') {
				echo '<span class="compatibility-untested2">'.esc_html__('The extension is not tested with the version 7 interface update and may not work properly.', 'essb').'</span>';
			}
			else {
				if (version_compare ( ESSB3_VERSION, $require, '<' )) {
					echo '<span class="compatibility-untested">Requires plugin version <b>' . $require . '</b> or newer</span>';
				} else {
					echo '<span class="compatibility-compatible"><b>Compatible</b> with your version of plugin</span>';
				
				}
			}
			echo '</div>';
		}
		
		echo '</div>';
		
		echo '<div class="footer">';
		echo '<div class="price">'.$data['price'].'</div>';
		echo '<div class="action">';
		
		if (!$is_free) {
			echo '<a class="action-btn action-open-btn" target="_blank" href="'.esc_url($url).'">'.esc_html__('Learn More', 'essb').' &rarr;</a>';
		}
		else {
			if ($is_installed) {
				echo '<span class="installed">'.esc_html__('Installed', 'essb').'</span>';
			}
			else {
				if (ESSBActivationManager::isActivated()) {
					echo '<a class="action-btn action-download-btn" target="_blank"  href="' . esc_url($url) .'&url='.get_bloginfo('url') .'&code='.ESSBActivationManager::getActivationCode() . '" onclick="essbShowFreeAddonInstallation();">Download &rarr;</a>';
				}
				else {
					echo '<span class="not-activated">'.ESSBAdminActivate::activateToUnlock(esc_html__('Activate plugin to download', 'essb')).'</span>';
				}
			}
		}
		
		echo '</div>';
		echo '</div>';
		
		echo '</div>';
	}
	
	?>
	</div>
	
</div>