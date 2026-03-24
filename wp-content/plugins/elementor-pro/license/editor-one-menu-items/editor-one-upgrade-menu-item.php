<?php

namespace ElementorPro\License\EditorOneMenuItems;

use Elementor\Core\Admin\EditorOneMenu\Interfaces\Menu_Item_Interface;
use Elementor\Modules\EditorOne\Classes\Menu_Config;
use ElementorPro\Core\Utils as Pro_Utils;
use ElementorPro\License\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Editor_One_Upgrade_Menu_Item implements Menu_Item_Interface {

	const UPGRADE_URL = 'https://go.elementor.com/go-pro-advanced-elementor-menu/';

	public function get_capability() : string {
		return 'manage_options';
	}

	public function get_label() : string {
		if ( Pro_Utils::is_sale_time() ) {
			return esc_html__( 'Discounted Upgrades', 'elementor-pro' );
		}

		return esc_html__( 'Unlock More Features', 'elementor-pro' );
	}

	public function get_parent_slug() : string {
		return Menu_Config::ELEMENTOR_MENU_SLUG;
	}

	public function is_visible() : bool {
		return ! API::is_license_expired() && API::is_need_to_show_upgrade_promotion();
	}

	public function get_position() : int {
		return 62;
	}

	public function get_slug() : string {
		return self::UPGRADE_URL;
	}

	public function get_group_id(): string {
		return Menu_Config::SYSTEM_GROUP_ID;
	}
}
