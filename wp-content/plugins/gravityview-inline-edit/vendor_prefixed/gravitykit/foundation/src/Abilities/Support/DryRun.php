<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare( strict_types=1 );

namespace GravityKit\GravityEdit\Foundation\Abilities\Support;

/**
 * Dry-run helpers for abilities that would otherwise write post columns.
 *
 * Product-specific metadata dry-runs may still use their local metadata
 * short-circuit helpers. This helper covers the complementary case: abilities
 * whose live path calls wp_insert_post(), wp_update_post(), wp_trash_post(), or
 * wp_delete_post(). In dry-run mode, guarded work is not invoked.
 *
 * @since 1.23.0
 */
final class DryRun {

	/**
	 * Active dry-run frame count.
	 *
	 * @since 1.23.0
	 *
	 * @var int
	 */
	private static int $depth = 0;

	/**
	 * Whether a Foundation dry-run frame is active.
	 *
	 * @since 1.23.0
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		return self::$depth > 0;
	}

	/**
	 * Runs work with dry-run mode enabled.
	 *
	 * @since 1.23.0
	 *
	 * @param callable $work Work to run.
	 * @return mixed
	 */
	public static function with_active( callable $work ) {
		++self::$depth;

		try {
			return $work();
		} finally {
			--self::$depth;
		}
	}

	/**
	 * Conditionally run work with dry-run mode enabled.
	 *
	 * @since 1.23.0
	 *
	 * @param bool     $dry_run Whether to enable dry-run mode.
	 * @param callable $work    Work to run.
	 * @return mixed
	 */
	public static function with_dry_run( bool $dry_run, callable $work ) {
		if ( ! $dry_run ) {
			return $work();
		}

		return self::with_active( $work );
	}

	/**
	 * Guards work that writes post columns.
	 *
	 * If dry-run mode is active, the guarded callable is not invoked and a
	 * planned outcome envelope is returned. Callers may pass planned output in
	 * the optional second argument; the dry-run flags are stamped consistently.
	 *
	 * @since 1.23.0
	 *
	 * @param callable $work            Live write work.
	 * @param array    $planned_outcome Optional dry-run outcome context.
	 * @return mixed
	 */
	public static function with_post_write_guard( callable $work, array $planned_outcome = [] ) {
		if ( self::is_active() ) {
			$planned_outcome['dry_run'] = true;

			return $planned_outcome;
		}

		return $work();
	}
}
