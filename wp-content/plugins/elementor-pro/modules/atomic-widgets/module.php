<?php
namespace ElementorPro\Modules\AtomicWidgets;

use ElementorPro\Base\Module_Base;
use ElementorPro\Plugin;
use Elementor\Modules\AtomicWidgets\Module as AtomicWidgetsModule;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformers_Registry;
use ElementorPro\Modules\AtomicWidgets\PropTypes\Display_Conditions\Display_Conditions_Prop_Type;
use ElementorPro\Modules\AtomicWidgets\PropTypes\Display_Conditions\Condition_Group_Prop_Type;
use ElementorPro\Modules\AtomicWidgets\Transformers\Display_Conditions as Display_Conditions_Transformer;
use ElementorPro\Modules\AtomicWidgets\Transformers\Condition_Group as Condition_Group_Transformer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Module_Base {

	public function get_name() {
		return 'atomic';
	}

	public function __construct() {
		parent::__construct();

		if ( ! Plugin::elementor()->experiments->is_feature_active( AtomicWidgetsModule::EXPERIMENT_NAME ) ) {
			return;
		}

		add_filter(
			'elementor/atomic-widgets/props-schema',
			fn( $schema ) => $this->inject_props_schema( $schema ),
			10,
			1
		);

		add_action(
			'elementor/atomic-widgets/settings/transformers/register',
			fn ( $transformers ) => $this->register_settings_transformers( $transformers ),
		);
	}

	private function inject_props_schema( $schema ) {
		$schema[ Display_Conditions_Prop_Type::get_key() ] = Display_Conditions_Prop_Type::make();

		return $schema;
	}

	private function register_settings_transformers( Transformers_Registry $transformers ): Transformers_Registry {
		$transformers->register( Display_Conditions_Prop_Type::get_key(), new Display_Conditions_Transformer() );
		$transformers->register( Condition_Group_Prop_Type::get_key(), new Condition_Group_Transformer() );

		return $transformers;
	}
}
