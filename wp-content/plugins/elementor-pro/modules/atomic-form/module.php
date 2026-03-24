<?php
namespace ElementorPro\Modules\AtomicForm;

use ElementorPro\Base\Module_Base;
use ElementorPro\Plugin;
use Elementor\Modules\AtomicWidgets\Module as AtomicWidgetsModule;
use Elementor\Core\Experiments\Manager as ExperimentsManager;
use ElementorPro\Modules\AtomicForm\Widgets\Input;
use ElementorPro\Modules\AtomicForm\Widgets\Label;
use ElementorPro\Modules\AtomicForm\Widgets\Textarea;
use Elementor\Widgets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Module_Base {
	const MODULE_NAME = 'e-atomic-form';
	const EXPERIMENT_NAME = 'e_pro_atomic_form';

	public function get_name() {
		return self::MODULE_NAME;
	}

	public static function get_experimental_data(): array {
		return [
			'name' => self::EXPERIMENT_NAME,
			'title' => esc_html__( 'Atomic Form', 'elementor-pro' ),
			'description' => esc_html__( 'Atomic form widgets. Note: This feature requires the "Atomic Widgets" experiment to be enabled.', 'elementor-pro' ),
			'hidden' => true,
			'default' => ExperimentsManager::STATE_INACTIVE,
			'release_status' => ExperimentsManager::RELEASE_STATUS_DEV,
		];
	}

	public function __construct() {
		parent::__construct();

		if ( ! $this->is_experiment_active() ) {
			return;
		}

		add_filter(
			'elementor/widgets/register',
			fn( $widgets_manager ) => $this->register_widgets( $widgets_manager )
		);

		add_action( 'elementor/frontend/after_enqueue_styles', fn () => $this->add_inline_styles() );
	}

	private function is_experiment_active(): bool {
		return Plugin::elementor()->experiments->is_feature_active( self::EXPERIMENT_NAME )
			&& Plugin::elementor()->experiments->is_feature_active( AtomicWidgetsModule::EXPERIMENT_NAME );
	}

	private function register_widgets( Widgets_Manager $widgets_manager ) {
		$widgets_manager->register( new Input() );
		$widgets_manager->register( new Label() );
		$widgets_manager->register( new Textarea() );
	}

	private function add_inline_styles() {
		// Default html textarea is resizable, but Elementor Atomic textarea is not resizable by default
		$inline_css = '.e-form-textarea-base:not([data-resizable]) { resize: none; }';
		wp_add_inline_style( 'elementor-frontend', $inline_css );
	}

}
