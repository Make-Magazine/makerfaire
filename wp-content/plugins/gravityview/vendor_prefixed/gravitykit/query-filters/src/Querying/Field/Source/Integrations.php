<?php

namespace GravityKit\GravityView\QueryFilters\Querying\Field\Source;

/**
 * Registers the built-in ChoiceSources on the manager.
 *
 * @since 2.14.0
 */
final class Integrations {
	/**
	 * Built-in sources, each exposing a static `register( ChoiceSourceManager $manager )` callback.
	 *
	 * @since 2.14.0
	 *
	 * @var string[]
	 */
	private const INTEGRATIONS = [
		DynamicLookupSource::class,
		PopulateAnythingSource::class,
	];

	/**
	 * Registers the built-in sources directly on the manager.
	 *
	 * @since 2.14.0
	 *
	 * @param ChoiceSourceManager $manager The manager to register the sources on.
	 */
	public static function register( ChoiceSourceManager $manager ): void {
		foreach ( self::INTEGRATIONS as $integration ) {
			if ( ! is_subclass_of( $integration, ChoiceSource::class ) ) {
				continue;
			}

			$integration::register( $manager );
		}
	}
}
