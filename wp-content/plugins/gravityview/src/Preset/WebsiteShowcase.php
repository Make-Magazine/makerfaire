<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Website Showcase preset template.
 *
 * @since 3.0.0
 */
class WebsiteShowcase extends \GravityView_Default_Template_List {
	const ID = 'preset_website_showcase';

	function __construct() {
		$settings = [
			'slug'          => 'list',
			'type'          => 'preset',
			'label'         => __( 'Website Showcase', 'gk-gravityview' ),
			'description'   => __( 'Feature submitted websites with screenshots.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/website-showcase/logo-website-showcase.png' ),
			'preset_form'   => Assets::path( 'presets/website-showcase/form-website-showcase.json' ),
			'preset_fields' => Assets::path( 'presets/website-showcase/fields-website-showcase.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
