<?php

namespace GravityKit\MultipleForms;

use GravityKit\MultipleForms\Models\Join;
use WP_Post;

/**
 * Class Form
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class View extends AbstractSingleton {
	/**
	 * Where the view joins are stored in the Meta table.
	 *
	 * @since 0.3
	 *
	 * @var string
	 */
	public const KEY_FORM_JOINS = '_gravityview_form_joins';

	/**
	 * Joins Forms that were already fetch from the DB.
	 *
	 * @since 0.3
	 *
	 * @var array
	 */
	public static $join_forms_cache = [];

	/**
	 * Joins Data that were already fetch from the DB.
	 *
	 * @since 0.3
	 *
	 * @var array
	 */
	public static $join_data_cache = [];

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
	 * Get the Join Data directly from the View Meta.w
	 *
	 * @since 0.3
	 *
	 * @param WP_Post $post
	 *
	 * @return array<Join|\WP_Error>
	 */
	public static function get_join_data( WP_Post $post ): array {
		// We found cache.
		if ( isset( static::$join_data_cache[ $post->ID ] ) ) {
			return static::$join_data_cache[ $post->ID ];
		}

		$view = \GV\View::from_post( $post );

		if ( ! $view ) {
			return [];
		}

		$joins = get_post_meta( $view->ID, static::KEY_FORM_JOINS, true );

		if ( empty( $joins ) || ! is_array( $joins ) ) {
			static::$join_data_cache[ $post->ID ] = [];
			return static::$join_data_cache[ $post->ID ];
		}

		return static::$join_data_cache[ $post->ID ] = array_map( [ Join::class, 'from_legacy_array' ], $joins );
	}

	/**
	 * Prevent Inactive or Trashed Forms from being included on the Join Data.
	 *
	 * @since 0.3
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public static function get_active_form_joins( WP_Post $post ): array {
		$join_data = static::get_join_data( $post );

		/**
		 * Filters the forms use to only the active ones.
		 * @param Join|\WP_Error $join
		 */
		return array_filter( $join_data, static function( $join ) {

			if ( is_wp_error( $join ) ) {
				do_action( 'gravityview_log_error', 'Fetching Joins failed: ' . $join->get_error_message(), __METHOD__, $join );
				return false;
			}

			return $join->has_active_forms();
		} );
	}

	/**
	 * Gets which are the other forms that needs to be joined on this view.
	 *
	 * @since 0.3
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public static function get_join_form_ids( WP_Post $post ): array {
		$joins = static::get_join_data( $post );
		if ( empty( $joins ) ) {
			return [];
		}

		$form_ids = [];

		// This can be replaced once we are using PHP 7.4 with:
		// $form_ids = array_merge( ...array_map( static function( $join ) { return $join->get_form_ids() }, $joins ) );
		foreach ( $joins as $join ) {

			// Sanity check.
			if( ! $join instanceof Join ) {
				continue;
			}

			foreach ( $join->get_form_ids() as $form_id ) {
				$form_ids[] = $form_id;
			}
		}

		return array_unique( $form_ids );
	}

	/**
	 * Filter the Active Forms from the Join forms.
	 *
	 * @since 0.3
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public static function get_join_active_form_ids( WP_Post $post ): array {
		return Form::get_active_form_ids( static::get_join_form_ids( $post ) );
	}
}
