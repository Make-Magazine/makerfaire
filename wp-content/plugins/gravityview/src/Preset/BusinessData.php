<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Business Data preset template.
 *
 * @since 3.0.0
 */
class BusinessData extends \GravityView_Default_Template_Table {
	const ID = 'preset_business_data';

	function __construct() {
		$settings = [
			'slug'          => 'table',
			'type'          => 'preset',
			'label'         => __( 'Business Data', 'gk-gravityview' ),
			'description'   => __( 'Display business information in a table.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/business-data/logo-business-data.png' ),
			'preset_form'   => Assets::path( 'presets/business-data/form-business-data.json' ),
			'preset_fields' => Assets::path( 'presets/business-data/fields-business-data.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
