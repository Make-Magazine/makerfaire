<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Business Listings preset template.
 *
 * @since 3.0.0
 */
class BusinessListings extends \GravityView_Default_Template_List {
	const ID = 'preset_business_listings';

	function __construct() {
		$settings = [
			'slug'          => 'list',
			'type'          => 'preset',
			'label'         => __( 'Business Listing', 'gk-gravityview' ),
			'description'   => __( 'Display business profiles.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/business-listings/logo-business-listings.png' ),
			'preset_form'   => Assets::path( 'presets/business-listings/form-business-listings.json' ),
			'preset_fields' => Assets::path( 'presets/business-listings/fields-business-listings.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
