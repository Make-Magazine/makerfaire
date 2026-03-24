<?php

namespace ElementorPro\License\EditorOneMenuItems;

use Elementor\Core\Admin\EditorOneMenu\Interfaces\Menu_Item_Interface;
use Elementor\Modules\EditorOne\Classes\Menu_Config;
use ElementorPro\License\Admin;
use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Editor_One_License_Menu_Item implements Menu_Item_Interface, Admin_Menu_Item_With_Page {

	public function get_page_title() : string {
		return esc_html__( 'License', 'elementor-pro' );
	}

	public function get_capability() : string {
		return 'manage_options';
	}

	public function get_label() : string {
		return esc_html__( 'License', 'elementor-pro' );
	}

	public function get_parent_slug() : string {
		return Menu_Config::ELEMENTOR_MENU_SLUG;
	}

	public function is_visible() : bool {
		return true;
	}

	public function get_position() : int {
		return 60;
	}

	public function get_slug() : string {
		return Admin::PAGE_ID;
	}

	public function get_group_id() : string {
		return Menu_Config::SYSTEM_GROUP_ID;
	}

	public function render() : void {
		Plugin::instance()->license_admin->display_page();
	}
}

