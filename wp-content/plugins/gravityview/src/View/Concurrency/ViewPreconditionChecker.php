<?php
/**
 * Optimistic-concurrency precondition check (If-Match → 412).
 *
 * @package     GravityKit\GravityView\View\Concurrency
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Concurrency;

use WP_Error;

/**
 * Verifies a client-supplied `If-Match` token against the View's current
 * version. Mirrors the legacy `InspectorRoute::check_precondition`.
 *
 * Accepts:
 * - `null` / missing header — no precondition; pass through.
 * - `auto` literal — server resolves to the version the client last read
 *   (this is the abilities-side affordance; clients pass `ifMatch: "auto"`
 *   when they want optimistic concurrency without round-tripping the version
 *   string themselves).
 * - Literal version string — compared against the current computed version;
 *   412 on mismatch.
 *
 * @since 3.0.0
 */
final class ViewPreconditionChecker {

	/**
	 * Version computer instance.
	 *
	 * @since 3.0.0
	 *
	 * @var ViewVersionComputer
	 */
	private ViewVersionComputer $computer;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param ViewVersionComputer|null $computer Version computer.
	 */
	public function __construct( ?ViewVersionComputer $computer = null ) {
		$this->computer = $computer ?? new ViewVersionComputer();
	}

	/**
	 * Verify the If-Match token. Returns true on pass; WP_Error (412) on fail.
	 *
	 * `auto` resolves to the version computed BEFORE any write attempt — the
	 * caller wraps the precondition check + the write inside a `ViewLock` to
	 * make the read-modify-write atomic.
	 *
	 * @since 3.0.0
	 *
	 * @param int         $view_id  Target View id.
	 * @param string|null $if_match Optional `If-Match` token from the request.
	 *                              header or body. `null` / empty bypasses the
	 *                              check; `auto` short-circuits to true.
	 *
	 * @return true|WP_Error True on pass.
	 */
	public function check( int $view_id, ?string $if_match ) {
		if ( $view_id <= 0 ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'A valid View id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		// Trim whitespace + strip surrounding quotes (clients may send the
		// version as an HTTP-spec ETag string `"..."` or as a bare value).
		if ( null !== $if_match ) {
			$if_match = trim( $if_match );
			if ( '' !== $if_match && '"' === $if_match[0] && '"' === substr( $if_match, -1 ) ) {
				$if_match = substr( $if_match, 1, -1 );
			}
		}

		if ( null === $if_match || '' === $if_match ) {
			// No precondition supplied; pass through.
			return true;
		}

		if ( 'auto' === $if_match ) {
			// Auto-resolution short-circuits to true. The caller is opting into.
			// "use whatever I just read"; the surrounding ViewLock guarantees
			// no concurrent writer can land between read and write.
			return true;
		}

		$current = $this->computer->compute( $view_id );
		if ( '' === $current ) {
			// View doesn't exist or has no version — treat as 404 rather than.
			// 412 (no version to compare against).
			return new WP_Error(
				'gv_rest_view_not_found',
				__( 'The View couldn\'t be found.', 'gk-gravityview' ),
				[
					'status'  => 404,
					'view_id' => $view_id,
				]
			);
		}
		if ( $current === $if_match ) {
			return true;
		}

		return new WP_Error(
			'gv_rest_precondition_failed',
			__( 'Someone else changed this View. Reload and try again.', 'gk-gravityview' ),
			[
				'status'          => 412,
				'current_version' => $current,
				'if_match'        => $if_match,
			]
		);
	}
}
