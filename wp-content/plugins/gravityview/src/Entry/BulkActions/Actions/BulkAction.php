<?php
/**
 * Frontend bulk action contract.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

/**
 * Defines a frontend bulk action.
 *
 * @since 3.0.0
 */
interface BulkAction {
	/**
	 * Returns the action key used in settings and requests.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function key();

	/**
	 * Returns the action configuration consumed by the bulk action registry.
	 *
	 * Supported keys include `label`, `callback`, `capability`, `required_fields`,
	 * `available_callback`, `confirmation`, `background`, `lock`,
	 * `settings_schema`, `request_callback`, `frontend_data_callback`,
	 * `dismiss_callback`, and `supports_multi_form_view`.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function config();
}
