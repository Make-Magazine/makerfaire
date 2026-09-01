<?php
/**
 * Manages transient preview stages for a View (Studio preview pane).
 *
 * @package     GravityKit\GravityView\View\Preview
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Preview;

use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;
use WP_Error;

/**
 * Owns the lifecycle of preview stages — transient (TTL'd) snapshots of a
 * View's staged config used by the Studio preview pane to render
 * unsaved changes without persisting them.
 *
 * Backs `preview-stage-create` and `preview-stage-delete` abilities +
 * `POST /views/{id}/preview/_stage` + `DELETE /views/{id}/preview/_stage/{token}`
 * routes.
 *
 * Each stage is keyed by a server-issued token (UUIDv4 segment) stored as a
 * site transient with TTL. The token is the only identity returned to the
 * client — it doesn't expose internal storage layout.
 *
 * @since 3.0.0
 */
final class PreviewStageManager {

	/**
	 * Preview stage TTL (seconds). Stages auto-expire after this window.
	 *
	 * @since 3.0.0
	 */
	public const TTL_SECONDS = 3600;

	/**
	 * Transient key prefix for preview stages.
	 *
	 * @since 3.0.0
	 */
	private const TRANSIENT_PREFIX = 'gv_preview_stage_';

	/**
	 * Create a preview stage from a config payload. Returns the token.
	 *
	 * @since 3.0.0
	 *
	 * @param int                 $view_id View post id.
	 * @param array<string,mixed> $config_overrides Config to stage on top of.
	 *                                              the current View state.
	 *
	 * @return array{token: string, expires_at: int}|WP_Error
	 */
	public function create( int $view_id, array $config_overrides ) {
		if ( $view_id <= 0 ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'A valid View id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		// Verify the View actually exists. Preview stages for non-existent.
		// Views accumulate as orphan transients.
		$post = get_post( $view_id );
		if ( ! $post || 'gravityview' !== get_post_type( $post ) ) {
			return new WP_Error(
				'gv_rest_view_not_found',
				__( 'The View couldn\'t be found.', 'gk-gravityview' ),
				[
					'status'  => 404,
					'view_id' => $view_id,
				]
			);
		}

		/**
		 * Filters the preview-stage TTL in seconds. Default 3600 (1 hour).
		 * Add-ons (e.g., CI/runner tooling) can shorten this for ephemeral
		 * previews, or design-review setups can lengthen for stable links.
		 * Floored at 60 seconds after filtering.
		 *
		 * @since 3.0.0
		 *
		 * @param int $ttl     Default TTL (PreviewStageManager::TTL_SECONDS).
		 * @param int $view_id View this stage will preview.
		 */
		$ttl = (int) apply_filters( 'gk/gravityview/rest/view/preview-stage/ttl', self::TTL_SECONDS, $view_id );
		$ttl = max( 60, $ttl ); // Floor at 1 minute.

		$token      = wp_generate_uuid4();
		$expires_at = time() + $ttl;

		$stage = [
			'view_id'    => $view_id,
			'overrides'  => $config_overrides,
			'created_at' => time(),
			'expires_at' => $expires_at,
		];

		// Use Foundation's WPHelper::set_transient — wraps set_transient with.
		// $wpdb safety checks (the helper short-circuits to false when wpdb
		// is unavailable, e.g., during very early WP boot) and matches the
		// convention used elsewhere in the codebase (Cache.php).
		$saved = WPHelper::set_transient( $this->transient_key( $token ), $stage, $ttl );
		if ( false === $saved ) {
			// set_transient returns false on storage failure (e.g., disk
			// full, options write blocked, persistent-cache disconnect).
			// Surface as 500 rather than returning a token the client
			// can't actually read back.
			return new WP_Error(
				'gv_rest_storage_failed',
				__( 'Couldn\'t save the preview stage.', 'gk-gravityview' ),
				[ 'status' => 500 ]
			);
		}

		return [
			'token'      => $token,
			'expires_at' => $expires_at,
			'ttl'        => $ttl,
		];
	}

	/**
	 * Retrieve a stage by token. Returns null if absent/expired.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Preview-stage token.
	 *
	 * @return array<string,mixed>|null
	 */
	public function find( string $token ): ?array {
		if ( '' === $token ) {
			return null;
		}
		$stage = WPHelper::get_transient( $this->transient_key( $token ) );
		return is_array( $stage ) ? $stage : null;
	}

	/**
     * Delete a preview stage by token.
     *
     * @since 3.0.0
     *
     * @param string $token Preview-stage token.
     *
     * @return array{token: string, deleted: bool}|WP_Error
     */
	public function delete( string $token ) {
		if ( '' === $token ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'The preview token is missing.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		// Distinguish "removed an existing stage" from "no stage to remove"
		// delete_transient returns false in BOTH cases — checking find()
		// first separates the two.
		$existed = null !== $this->find( $token );
		WPHelper::delete_transient( $this->transient_key( $token ) );

		return [
			'token'   => $token,
			'deleted' => $existed,
		];
	}

	/**
	 * Build the transient key for a token. Namespaced so manual debugging /
	 * cache inspection finds them.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Preview-stage token.
	 */
	private function transient_key( string $token ): string {
		return self::TRANSIENT_PREFIX . $token;
	}
}
