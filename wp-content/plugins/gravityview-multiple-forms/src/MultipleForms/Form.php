<?php

namespace GravityKit\MultipleForms;

use GFAPI;

/**
 * Class Form
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class Form extends AbstractSingleton {

	/**
	 * Forms that were already fetch from the DB.
	 *
	 * @since 0.3
	 *
	 * @var array
	 */
	public static $forms_cache = [];

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {

	}

	/**
	 * Filter the Active Forms from the Join forms.
	 *
	 * @since 0.3
	 *
	 * @param array<int> $forms
	 *
	 * @return array<int>
	 */
	public static function get_active_form_ids( array $forms ): array {
		$forms = static::get_forms( $forms );

		// Fetches all the active forms from the list.
		$forms = wp_list_filter( $forms, [ 'is_active' => 1 ] );

		return wp_list_pluck( $forms, 'id' );
	}

	/**
	 * From a list of Form IDs return the array defined by GFAPI::get_form
	 * This particular function will store a cache for the form.
	 *
	 * @since 0.3
	 *
	 * @param array<int> $forms
	 *
	 * @return array<array>
	 */
	public static function get_forms( array $forms ): array {
		return array_filter( array_map( static function ( $form_id ) {
			if ( ! is_numeric( $form_id ) ) {
				return null;
			}

			return static::$forms_cache[ $form_id ] ?? ( static::$forms_cache[ $form_id ] = GFAPI::get_form( $form_id ) );
		}, $forms ) );
	}
}
