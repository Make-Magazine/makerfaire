<?php
/**
 * Add Beaver Builder compatibility to GravityView.
 *
 * @package GravityKit\GravityView\Integration
 *
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Integration;

use GravityKit\GravityView\PageBuilder\BeaverBuilder\BasicModule;
use GravityKit\GravityView\PageBuilder\BeaverBuilder\EntryFieldModule;
use GravityKit\GravityView\PageBuilder\BeaverBuilder\EntryLinkModule;
use GravityKit\GravityView\PageBuilder\BeaverBuilder\EntryModule;
use GravityKit\GravityView\PageBuilder\BeaverBuilder\ViewDetailsModule;

/**
 * Beaver Builder plugin hooks.
 *
 * @inheritDoc
 *
 * @since 3.0.0
 */
class BeaverBuilder extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 */
	protected $class_name = 'FLBuilder';

	/**
	 * @inheritDoc
	 *
	 * Each BB module file calls `FLBuilder::register_module()` at file-scope,
	 * so forcing the autoloader to load each class is what registers the
	 * module with Beaver Builder.
	 */
	public function add_hooks() {
		parent::add_hooks();

		class_exists( BasicModule::class );
		class_exists( EntryModule::class );
		class_exists( EntryFieldModule::class );
		class_exists( EntryLinkModule::class );
		class_exists( ViewDetailsModule::class );
	}
}
