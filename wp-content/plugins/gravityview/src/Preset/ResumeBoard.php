<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Resume Board preset template.
 *
 * @since 3.0.0
 */
class ResumeBoard extends \GravityView_Default_Template_Table {
	const ID = 'preset_resume_board';

	function __construct() {
		$settings = [
			'slug'          => 'table',
			'type'          => 'preset',
			'label'         => __( 'Resume Board', 'gk-gravityview' ),
			'description'   => __( 'Allow job-seekers to post their resumes.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/resume-board/logo-resume-board.png' ),
			'preset_form'   => Assets::path( 'presets/resume-board/form-resume-board.json' ),
			'preset_fields' => Assets::path( 'presets/resume-board/fields-resume-board.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
