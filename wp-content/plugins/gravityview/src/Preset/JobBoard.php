<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Job Board preset template.
 *
 * @since 3.0.0
 */
class JobBoard extends \GravityView_Default_Template_List {
	const ID = 'preset_job_board';

	function __construct() {
		$settings = [
			'slug'          => 'list',
			'type'          => 'preset',
			'label'         => __( 'Job Board', 'gk-gravityview' ),
			'description'   => __( 'Post available jobs in a simple job board.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/job-board/logo-job-board.png' ),
			'preset_form'   => Assets::path( 'presets/job-board/form-job-board.json' ),
			'preset_fields' => Assets::path( 'presets/job-board/fields-job-board.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
