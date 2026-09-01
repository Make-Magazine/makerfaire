<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * People Profiles preset template.
 *
 * @since 3.0.0
 */
class Profiles extends \GravityView_Default_Template_List {
	const ID = 'preset_profiles';

	function __construct() {
		$settings = [
			'slug'          => 'list',
			'type'          => 'preset',
			'label'         => __( 'People Profiles', 'gk-gravityview' ),
			'description'   => __( 'List people with individual profiles.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/profiles/logo-profiles.png' ),
			'preset_form'   => Assets::path( 'presets/profiles/form-profiles.json' ),
			'preset_fields' => Assets::path( 'presets/profiles/fields-profiles.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
