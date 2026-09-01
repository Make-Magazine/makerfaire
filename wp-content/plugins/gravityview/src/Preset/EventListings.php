<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * Event Listings preset template.
 *
 * @since 3.0.0
 */
class EventListings extends \GravityView_Default_Template_List {
	const ID = 'preset_event_listings';

	function __construct() {
		$settings = [
			'slug'          => 'list',
			'type'          => 'preset',
			'label'         => __( 'Event Listings', 'gk-gravityview' ),
			'description'   => __( 'Present a list of your events.', 'gk-gravityview' ),
			'logo'          => Assets::url( 'presets/event-listings/logo-event-listings.png' ),
			'preset_form'   => Assets::path( 'presets/event-listings/form-event-listings.json' ),
			'preset_fields' => Assets::path( 'presets/event-listings/fields-event-listings.xml' ),
		];

		parent::__construct( self::ID, $settings );
	}
}
