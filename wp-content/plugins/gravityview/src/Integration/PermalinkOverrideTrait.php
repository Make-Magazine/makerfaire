<?php
/**
 * Trait for handling permalink structure overrides in plugin and theme integrations.
 *
 * This trait provides functionality to disable WordPress permalinks and override
 * directory links when rendering GravityView templates. It's useful for plugins
 * with custom endpoints (like LearnDash, BuddyPress, etc.) that need special
 * URL handling to render GravityView properly.
 *
 * @package GravityKit\GravityView\Integration
 * @license GPL2+
 * @author  GravityKit <hello@gravitykit.com>
 * @link    https://www.gravitykit.com
 *
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Integration;

/**
 * Trait for permalink structure override functionality.
 *
 * Classes using this trait must implement the should_disable_permalink_structure() method
 * to determine when permalink overrides should be applied. The abstract class will automatically
 * detect classes using this trait and set up the necessary hooks.
 *
 * @since 3.0.0
 */
trait PermalinkOverrideTrait {

	/**
	 * Handle template redirect to set up permalink overrides if needed.
	 *
	 * This is running on the `template_redirect` action so that $post is set up.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function on_template_redirect() {
		if ( ! $this->should_disable_permalink_structure() ) {
			return;
		}

		// Remove the entry endpoint from the back link.
		add_filter( 'gravityview/template/links/back/url', [ $this, 'remove_entry_endpoint_from_back_link' ] );

		// Add hooks before template renders
		add_action( 'gravityview/template/before', [ $this, 'disable_permalinks_before_template' ], 1 );

		// Remove hooks after template renders
		add_action( 'gravityview/template/after', [ $this, 'restore_permalinks_after_template' ], 1000 );
	}

	/**
	 * Remove the entry endpoint from the back link URL.
	 *
	 * @since 3.0.0
	 *
	 * @return string The URL with entry endpoint removed.
	 */
	public function remove_entry_endpoint_from_back_link() {
		return remove_query_arg( \GV\Entry::get_endpoint_name() );
	}

	/**
	 * Get the base URL for directory links without query args.
	 *
	 * @since 3.0.0
	 *
	 * @return string The base site URL without GravityView query args.
	 */
	public function get_base_url_without_query_args() {
		return site_url( remove_query_arg( \gv_get_query_args(), add_query_arg( [] ) ) );
	}

	/**
	 * Disable permalinks and override directory links before template renders.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function disable_permalinks_before_template() {
		add_filter( 'option_permalink_structure', '__return_false' );
		add_filter( 'gravityview/view/links/directory', [ $this, 'get_base_url_without_query_args' ] );
		add_filter( 'gravityview/widget/search/form/action', [ $this, 'get_base_url_without_query_args' ] );
	}

	/**
	 * Restore permalink structure and remove directory link overrides after template renders.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function restore_permalinks_after_template() {
		remove_filter( 'option_permalink_structure', '__return_false' );
		remove_filter( 'gravityview/view/links/directory', [ $this, 'get_base_url_without_query_args' ] );
		remove_filter( 'gravityview/widget/search/form/action', [ $this, 'get_base_url_without_query_args' ] );
	}

	/**
	 * Determine whether permalinks should be disabled for View rendering.
	 *
	 * This method must be implemented by classes using this trait.
	 * Return true to disable permalinks and use the override functionality.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Whether to remove the permalink structure from View rendered links.
	 */
	abstract protected function should_disable_permalink_structure();
}
