<?php

namespace ElementorPro\Modules\Interactions;

use ElementorPro\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Hooks {
	const PACKAGES = [
		'editor-interactions-extended',
	];

	public function register() {
		$this->register_packages()
			->register_pro_scripts()
			->replace_core_handlers();

		return $this;
	}

	private function register_packages() {
		add_filter( 'elementor-pro/editor/v2/packages', function ( $packages ) {
			return array_merge( $packages, self::PACKAGES );
		} );

		return $this;
	}

	private function register_pro_scripts() {
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_frontend_scripts' ], 20 );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'register_editor_scripts' ], 1 );

		return $this;
	}

	private function replace_core_handlers() {
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'replace_frontend_handlers' ], 20 );

		add_action( 'elementor/preview/enqueue_scripts', function() {
			$core_module = Plugin::elementor()->modules_manager->get_modules( 'e-interactions' );
			if ( $core_module && method_exists( $core_module, 'enqueue_preview_scripts' ) ) {
				remove_action( 'elementor/preview/enqueue_scripts', [ $core_module, 'enqueue_preview_scripts' ] );
			}
		}, 1 );

		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'replace_preview_handlers' ], 20 );

		return $this;
	}

	public function register_frontend_scripts() {
		$suffix = ( Utils::is_script_debug() || Utils::is_elementor_tests() ) ? '' : '.min';

		wp_register_script(
			'elementor-interactions-pro',
			$this->get_js_assets_url( 'interactions-pro' ),
			[ 'motion-js' ],
			ELEMENTOR_PRO_VERSION,
			true
		);
	}

	public function register_editor_scripts() {
		$suffix = ( Utils::is_script_debug() || Utils::is_elementor_tests() ) ? '' : '.min';

		wp_register_script(
			'elementor-editor-interactions-pro',
			$this->get_js_assets_url( 'editor-interactions-pro' ),
			[ 'motion-js' ],
			ELEMENTOR_PRO_VERSION,
			true
		);
	}

	public function replace_frontend_handlers() {
		wp_dequeue_script( 'elementor-interactions' );
		wp_deregister_script( 'elementor-interactions' );

		wp_enqueue_script( 'elementor-interactions-pro' );

		$config = $this->get_config();
		wp_localize_script(
			'elementor-interactions-pro',
			'ElementorInteractionsConfig',
			$config
		);
	}

	public function replace_preview_handlers() {
		wp_dequeue_script( 'elementor-editor-interactions' );
		wp_deregister_script( 'elementor-editor-interactions' );

		wp_enqueue_script( 'elementor-editor-interactions-pro' );

		$config = $this->get_config();
		wp_localize_script(
			'elementor-editor-interactions-pro',
			'ElementorInteractionsConfig',
			$config
		);
	}

	private function get_config() {
		$interactions_module = Plugin::elementor()->modules_manager->get_modules( 'e-interactions' );

		if ( $interactions_module && method_exists( $interactions_module, 'get_config' ) ) {
			return $interactions_module->get_config();
		}

		return [
			'constants' => [
				'defaultDuration' => 300,
				'defaultDelay' => 0,
				'slideDistance' => 100,
				'scaleStart' => 0,
				'easing' => 'linear',
			],
			'animationOptions' => [],
		];
	}

	private function get_js_assets_url( $filename ) {
		$suffix = ( Utils::is_script_debug() || Utils::is_elementor_tests() ) ? '' : '.min';
		return ELEMENTOR_PRO_URL . 'assets/js/' . $filename . $suffix . '.js';
	}
}
