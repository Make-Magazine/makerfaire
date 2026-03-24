<?php

namespace ElementorPro\Modules\Interactions;

use ElementorPro\Plugin;
use ElementorPro\Base\Module_Base;
use Elementor\Modules\AtomicWidgets\Module as AtomicWidgetsModule;
use Elementor\Modules\Interactions\Module as InteractionsModule;
use Elementor\Core\Experiments\Manager as ExperimentsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Module_Base {
	const MODULE_NAME = 'e-interactions';
	const EXPERIMENT_NAME = 'e_pro_interactions';

	public function get_name() {
		return self::MODULE_NAME;
	}

	public static function get_experimental_data(): array {
		return [
			'name' => self::EXPERIMENT_NAME,
			'title' => esc_html__( 'Pro Interactions', 'elementor-pro' ),
			'description' => esc_html__( 'Enhanced interactions with replay support. Note: This feature requires both the "Atomic Widgets" and "Interactions" experiments to be enabled.', 'elementor-pro' ),
			'hidden' => true,
			'default' => ExperimentsManager::STATE_INACTIVE,
			'release_status' => ExperimentsManager::RELEASE_STATUS_DEV,
		];
	}

	private function hooks() {
		return new Hooks();
	}

	public function __construct() {
		parent::__construct();

		if ( ! $this->is_experiment_active() ) {
			return;
		}

		$this->hooks()->register();
	}

	private function is_experiment_active(): bool {
		return class_exists( 'Elementor\\Modules\\Interactions\\Module' )
			&& Plugin::elementor()->experiments->is_feature_active( self::EXPERIMENT_NAME )
			&& Plugin::elementor()->experiments->is_feature_active( AtomicWidgetsModule::EXPERIMENT_NAME )
			&& Plugin::elementor()->experiments->is_feature_active( InteractionsModule::EXPERIMENT_NAME );
	}
}
