<?php
/**
 * Customization for the Gravity Forms Polls Addon
 *
 * @file      class-gravityview-plugin-hooks-gravity-forms-polls.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2022, Katz Web Services, Inc.
 *
 * @since 2.16
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 2.16
 */
class GravityFormsPolls extends AbstractPluginHooks {

	/**
	 * @type string Optional. Constant that should be defined by plugin or theme. Used to check whether plugin is active.
	 * @since 2.16
	 */
	protected $constant_name = 'GF_POLLS_VERSION';

	protected function add_hooks() {

		/**
		 * These hooks to clear the cache were only being called on admin_init, so the cache was not being cleared
		 * when using Edit Entry on the frontend.
		 */
		add_action( 'gform_after_update_entry', [\GFPolls::get_instance(), 'entry_updated'], 10, 2 );
		add_action( 'gform_update_status', [\GFPolls::get_instance(), 'update_entry_status'], 10, 2 );
	}
}
