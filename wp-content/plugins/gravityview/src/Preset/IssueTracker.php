<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Issue Tracker preset template.
 *
 * @since 3.0.0
 */
class IssueTracker extends \GravityView_Default_Template_Table {
	const ID = 'preset_issue_tracker';

	function __construct() {
		$settings = [
			'slug'          => 'table',
			'type'          => 'preset',
			'label'         => __( 'Issue Tracker', 'gk-gravityview' ),
			'description'   => __( 'Manage issues and their statuses.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/issue-tracker/logo-issue-tracker.png' ),
			'preset_form'   => Assets::path( 'presets/issue-tracker/form-issue-tracker.json' ),
			'preset_fields' => Assets::path( 'presets/issue-tracker/fields-issue-tracker.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
