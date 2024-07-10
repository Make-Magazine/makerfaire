<?php
namespace WPGMZA\UI;


class ProAdmin extends Admin
{
	public function __construct()
	{
		Admin::__construct();
	}
	
	public function onAdminMenu()
	{
		global $wpgmza;
		
		Admin::onAdminMenu();
		
		$access_level = $wpgmza->getAccessCapability();
		
		if(empty($wpgmza->settings->categoryTreeSource) || $wpgmza->settings->categoryTreeSource === \WPGMZA\CategoryTree::SOURCE_NATIVE){
			add_submenu_page(
				'wp-google-maps-menu', 
				'WP Go Maps - Categories', 
				__('Categories', 'wp-google-maps'), 
				$access_level,
				'wp-google-maps-menu-categories',
				'wpgmaps_menu_category_layout',
				3
			);
		}
		
		add_submenu_page(
			'wp-google-maps-menu', 
			'WP Go Maps - Custom Fields', 
			__('Marker Fields', 'wp-google-maps'), 
			$access_level,
			'wp-google-maps-menu-custom-fields',
			'WPGMZA\\UI\\legacy_on_sub_menu',
			4
		);

		add_submenu_page(
			'wp-google-maps-menu', 
			'WP Go Maps - Advanced', 
			__('Tools', 'wp-google-maps'), 
			$access_level,
			'wp-google-maps-menu-advanced',
			'wpgmaps_menu_advanced_layout',
			5
		);
	}
	
	public function onMainMenu()
	{
		global $wpgmza;
		
		$action = (isset($_GET['action']) ? $_GET['action'] : null);
		
		switch($action)
		{
			case "wizard":
				if(!$wpgmza->internalEngine->isLegacy()){
					/* 
			    	 * Bail early and hand over to the action which will now be responsible for handling wizard page
			    	 *
			    	 * From Atlas Novus onward, we want to keep things super modular
			    	*/

					/* Developer Hook (Action) - Wizard page instance creator, atlas novus only */
			    	do_action("wpgmza_wizard_page_create_instance");
					return;
				}
				wpgmaps_wizard_layout();
				return;
				break;
			
			default:
				break;
		}
		
		return Admin::onMainMenu();
	}
	
	public function onSubMenu()
	{
		global $wpgmza;

		switch($_GET['page'])
		{
			case "wp-google-maps-menu-advanced":
				break;
			
			case "wp-google-maps-menu-categories":
				break;
			
			case "wp-google-maps-menu-custom-fields":
				if(!$wpgmza->internalEngine->isLegacy()){
					$page = \WPGMZA\CustomFieldsPage::createInstance();
					echo $page->html;
				} else {
					$page = new \WPGMZA\CustomFieldsPageLegacy();
					$page->html();
				}
				break;
			
			default:
				return Admin::onSubMenu();
				break;
		}
	}
}

add_filter('wpgmza_create_WPGMZA\\UI\\Admin', function() {
	
	return new ProAdmin();
	
});
