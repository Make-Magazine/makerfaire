<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Staff Profiles preset template.
 *
 * @since 3.0.0
 */
class StaffProfiles extends \GravityView_Default_Template_List {
	const ID = 'preset_staff_profiles';

	function __construct() {
		$settings = [
			'slug'          => 'list',
			'type'          => 'preset',
			'label'         => __( 'Staff Profiles', 'gk-gravityview' ),
			'description'   => __( 'List members of your team.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/staff-profiles/logo-staff-profiles.png' ),
			'preset_form'   => Assets::path( 'presets/staff-profiles/form-staff-profiles.json' ),
			'preset_fields' => Assets::path( 'presets/staff-profiles/fields-staff-profiles.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
