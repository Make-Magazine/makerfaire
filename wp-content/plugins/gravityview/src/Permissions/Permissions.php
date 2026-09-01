<?php
/**
 * Single permission service for the entire abilities + REST surface.
 *
 * @package     GravityKit\GravityView\Permissions
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\Permissions;

use WP_Error;

/**
 * Canonical authorization helper. Every ability `permission_callback`
 * and every REST controller `permission_callback` calls into this class.
 *
 * Routes through `GVCommon::has_cap()` so the `gravityview_full_access`
 * / `gform_full_access` shortcut applies uniformly. The CI guard at
 * `tests/ci/check-permission-guard.sh` forbids `current_user_can` /
 * `user_can` / `author_can` / `current_user_can_for_blog` /
 * `GVCommon::has_cap` calls anywhere under `src/Abilities/**` or
 * `src/REST/**` — they MUST route through here.
 *
 * @since 3.0.0
 */
final class Permissions {

	/**
	 * Statuses that require `publish_gravityviews` cap on top of edit.
	 *
	 * @since 3.0.0
	 */
	private const PUBLISH_STATUSES = [ 'publish', 'private' ];

	/**
	 * Statuses that require delete cap on top of edit.
	 *
	 * @since 3.0.0
	 */
	private const DELETE_STATUSES = [ 'trash' ];

	/**
	 * Caps that gate the discovery (catalogue) surface — any GV-author
	 * cap unlocks layouts/widgets/zones/etc. enumeration.
	 *
	 * @since 3.0.0
	 */
	private const DISCOVERY_CAPS = [
		'edit_gravityviews',
		'edit_others_gravityviews',
		'edit_published_gravityviews',
	];

	/* ---- View-scoped checks ---- */

	/**
	 * Can the user edit a specific View?
	 *
	 * Checks the meta cap `edit_post` against the View id so WP's
	 * cap-map runs the authorship + published-status branches (a user
	 * with `edit_gravityviews` can edit their own drafts, but needs
	 * `edit_others_gravityviews` to edit someone else's View and
	 * `edit_published_gravityviews` for a published one). This mirrors
	 * the wp-admin edit-screen gate so the abilities surface can't be
	 * used to bypass the same enforcement the user would hit clicking
	 * "Edit View" in wp-admin.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id View post id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_edit_view( int $view_id, ?int $user_id = null ): bool {
		if ( $view_id <= 0 ) {
			return false;
		}
		return $this->has_cap( [ 'edit_post' ], $view_id, $user_id );
	}

	/**
	 * Can the user publish a specific View?
	 *
	 * Checks the meta cap `publish_post` against the View id so WP's
	 * cap-map maps to `publish_gravityviews` (plus the appropriate
	 * own/others/published branch).
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id View post id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_publish_view( int $view_id, ?int $user_id = null ): bool {
		if ( $view_id <= 0 ) {
			return false;
		}
		return $this->has_cap( [ 'publish_post' ], $view_id, $user_id );
	}

	/**
	 * Can the user delete a specific View?
	 *
	 * Checks the meta cap `delete_post` against the View id so WP's
	 * cap-map maps to the appropriate `delete_*_gravityviews` primitive
	 * based on authorship and post status.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id View post id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_delete_view( int $view_id, ?int $user_id = null ): bool {
		if ( $view_id <= 0 ) {
			return false;
		}
		return $this->has_cap( [ 'delete_post' ], $view_id, $user_id );
	}

	/**
	 * Can the user read a View's config? Same gate as edit — settings
	 * are not exposed to non-editors.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id View post id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_read_view( int $view_id, ?int $user_id = null ): bool {
		return $this->can_edit_view( $view_id, $user_id );
	}

	/**
	 * Can the user read other authors' View settings (used by the
	 * entries API to redact settings/search_criteria from unprivileged
	 * callers).
	 *
	 * @since 3.0.0
	 *
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_read_others_view_settings( ?int $user_id = null ): bool {
		return $this->has_cap( [ 'edit_others_gravityviews' ], null, $user_id );
	}

	/* ---- Composite / conditional rules ---- */

	/**
	 * Can the user create a View with the requested target status?
	 *
	 * Baseline: must have `edit_gravityviews`. If creating directly as
	 * `publish` or `private`, must also have `publish_gravityviews`.
	 * Status must be one of the known values.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $status  Desired post status.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool|WP_Error True on allow, WP_Error on deny.
	 */
	public function can_create_view_with_status( string $status, ?int $user_id = null ) {
		$known = array_merge( [ 'draft', 'pending' ], self::PUBLISH_STATUSES );
		if ( ! in_array( $status, $known, true ) ) {
			return new WP_Error(
				'gv_rest_invalid_status',
				/* translators: %s: invalid status passed in. */
				sprintf( __( 'Can\'t create a View with status "%s".', 'gk-gravityview' ), $status ),
				[ 'status' => 400 ]
			);
		}

		if ( ! $this->has_cap( [ 'edit_gravityviews' ], null, $user_id ) ) {
			return $this->denied();
		}

		if ( in_array( $status, self::PUBLISH_STATUSES, true )
			&& ! $this->has_cap( [ 'publish_gravityviews' ], null, $user_id )
		) {
			return $this->denied(
				'gv_rest_forbidden_publish',
				__( 'You don\'t have permission to publish this View.', 'gk-gravityview' )
			);
		}

		return true;
	}

	/**
	 * Can the user duplicate a View? AND semantics: edit on the source
	 * + create capability.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $source_view_id Source View id.
	 * @param int|null $user_id        Optional explicit user id.
	 *
	 * @return bool|WP_Error True on allow, WP_Error on deny.
	 */
	public function can_duplicate_view( int $source_view_id, ?int $user_id = null ) {
		if ( ! $this->can_edit_view( $source_view_id, $user_id ) ) {
			return $this->denied(
				'gv_rest_forbidden',
				__( 'You don\'t have permission to edit the View you\'re duplicating.', 'gk-gravityview' )
			);
		}

		if ( ! $this->has_cap( [ 'edit_gravityviews' ], null, $user_id ) ) {
			return $this->denied(
				'gv_rest_forbidden_create',
				__( 'You don\'t have permission to create a new View.', 'gk-gravityview' )
			);
		}

		return true;
	}

	/**
	 * Can the user transition the View to the requested status?
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id    Target View id.
	 * @param string   $new_status Desired post status.
	 * @param int|null $user_id    Optional explicit user id.
	 *
	 * @return bool|WP_Error True on allow, WP_Error on deny.
	 */
	public function can_set_view_status( int $view_id, string $new_status, ?int $user_id = null ) {
		$known = array_merge( [ 'draft', 'pending' ], self::PUBLISH_STATUSES, self::DELETE_STATUSES );
		if ( ! in_array( $new_status, $known, true ) ) {
			return new WP_Error(
				'gv_rest_invalid_status',
				/* translators: %s: invalid status passed in. */
				sprintf( __( 'Status "%s" isn\'t supported.', 'gk-gravityview' ), $new_status ),
				[
					'status'  => 400,
					'view_id' => $view_id,
				]
			);
		}

		if ( $view_id <= 0 ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'A valid View id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		if ( ! $this->can_edit_view( $view_id, $user_id ) ) {
			return $this->denied();
		}

		if ( in_array( $new_status, self::PUBLISH_STATUSES, true )
			&& ! $this->can_publish_view( $view_id, $user_id )
		) {
			return $this->denied(
				'gv_rest_forbidden_publish',
				__( 'You don\'t have permission to publish this View.', 'gk-gravityview' )
			);
		}

		if ( in_array( $new_status, self::DELETE_STATUSES, true )
			&& ! $this->can_delete_view( $view_id, $user_id )
		) {
			return $this->denied(
				'gv_rest_forbidden_delete',
				__( 'You don\'t have permission to delete this View.', 'gk-gravityview' )
			);
		}

		return true;
	}

	/* ---- Resource-specific gates (all delegate to edit) ---- */

	/**
	 * Can the user modify a slot of any per-View child resource
	 * (field / widget / search-field / grid-row)?
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id Target View id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool|WP_Error True on allow, WP_Error on deny.
	 */
	public function can_modify_view_child_slot( int $view_id, ?int $user_id = null ) {
		if ( $view_id <= 0 ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'A valid View id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}
		if ( ! $this->can_edit_view( $view_id, $user_id ) ) {
			return $this->denied();
		}
		return true;
	}

	/**
	 * Can the user read widget settings + schema (read-only path)?
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id Target View id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_read_widget_settings( int $view_id, ?int $user_id = null ): bool {
		return $this->can_edit_view( $view_id, $user_id );
	}

	/**
	 * Can the user render a field (read-only preview path)?
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id Target View id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_render_field( int $view_id, ?int $user_id = null ): bool {
		return $this->can_edit_view( $view_id, $user_id );
	}

	/* ---- Discovery (catalogue) gates ---- */

	/**
	 * Can the user access the global discovery surface (layouts,
	 * widgets, search-zones, etc.)?
	 *
	 * @since 3.0.0
	 *
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_access_discovery( ?int $user_id = null ): bool {
		return $this->has_cap( self::DISCOVERY_CAPS, null, $user_id );
	}

	/**
	 * Can the user access View-scoped discovery (e.g., available-fields)?
	 * Same gate as edit on the target View.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id Target View id.
	 * @param int|null $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	public function can_access_view_discovery( int $view_id, ?int $user_id = null ): bool {
		return $this->can_edit_view( $view_id, $user_id );
	}

	/* ---- Internal helpers ---- */

	/**
	 * Primitive cap-check helper. The CI guard allows GVCommon::has_cap
	 * ONLY from inside this file — every other call site must route
	 * through one of the public methods above.
	 *
	 * @since 3.0.0
	 *
	 * @param array<int,string> $caps    Caps to check (OR semantics).
	 * @param int|null          $view_id Optional object id.
	 * @param int|null          $user_id Optional explicit user id.
	 *
	 * @return bool
	 */
	private function has_cap( array $caps, ?int $view_id = null, ?int $user_id = null ): bool {
		return (bool) \GVCommon::has_cap( $caps, $view_id, $user_id );
	}

	/**
	 * Build a standardized WP_Error for permission denial.
	 *
	 * @since 3.0.0
	 *
	 * @param string $code    Error code (default `gv_rest_forbidden`).
	 * @param string $message Optional override message.
	 *
	 * @return WP_Error
	 */
	private function denied( string $code = 'gv_rest_forbidden', string $message = '' ): WP_Error {
		return new WP_Error(
			$code,
			'' !== $message ? $message : __( 'You don\'t have permission to do that.', 'gk-gravityview' ),
			[ 'status' => 403 ]
		);
	}
}
