<?php

namespace ElementorPro\License\EditorOneMenuItems;

use Elementor\Core\Admin\EditorOneMenu\Interfaces\Menu_Item_Interface;
use Elementor\Modules\EditorOne\Classes\Menu_Config;
use ElementorPro\License\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Editor_One_Renew_Menu_Item implements Menu_Item_Interface {

	const RENEW_URL = 'https://go.elementor.com/wp-menu-renew/';

	public function get_capability() : string {
		return 'manage_options';
	}

	public function get_label() : string {
		return esc_html__( 'Renew Now', 'elementor-pro' );
	}

	public function get_parent_slug() : string {
		return Menu_Config::ELEMENTOR_MENU_SLUG;
	}

	public function is_visible() : bool {
		return API::is_license_expired();
	}

	public function get_position() : int {
		return 61;
	}

	public function get_slug() : string {
		return self::RENEW_URL;
	}

	public function get_group_id(): string {
		return Menu_Config::SYSTEM_GROUP_ID;
	}
}
