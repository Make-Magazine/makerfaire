<?php

namespace ElementorPro\Base;

use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Editor_One_Trait {

	protected function is_editor_one_active(): bool {
		return (bool) Plugin::elementor()->modules_manager->get_modules( 'editor-one' );
	}
}
