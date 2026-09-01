<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler;

/**
 * Locates the bundled Action Scheduler copy regardless of where the package
 * lives on disk.
 *
 * @since 1.25.0
 */
class VendorPathResolver {
	/**
	 * Action Scheduler entry file, relative to a Composer `vendor/` directory.
	 */
	const ACTION_SCHEDULER_ENTRY = '/woocommerce/action-scheduler/action-scheduler.php';

	/**
	 * Resolves the Action Scheduler entry file for the given Scheduler directory.
	 *
	 * Probes candidate `vendor/` directories nearest-first and returns the first
	 * whose `action-scheduler.php` is readable. Returns null when none is — a
	 * genuinely missing or half-written tree during a failed update — so the caller
	 * can skip loading it and degrade ("background jobs paused") instead of fataling.
	 *
	 * @since 1.25.0
	 *
	 * @param string        $scheduler_dir Absolute path of the Scheduler package (`__DIR__`).
	 * @param callable|null $exists        Predicate reporting whether a file is loadable; defaults to `is_readable`.
	 *
	 * @return string|null Absolute path to a readable action-scheduler.php, or null when none is found.
	 */
	public static function resolve( $scheduler_dir, ?callable $exists = null ) {
		$exists = $exists ?: 'is_readable';

		foreach ( self::candidates( $scheduler_dir ) as $vendor_path ) {
			$entry = $vendor_path . self::ACTION_SCHEDULER_ENTRY;

			if ( $exists( $entry ) ) {
				return $entry;
			}
		}

		return null;
	}

	/**
	 * Builds candidate `vendor/` directories, ordered nearest-first.
	 *
	 * Action Scheduler always lives in Composer's real `vendor/`. When the package
	 * is Strauss-copied into a product it sits beside that `vendor/` under a sibling
	 * directory (e.g. `vendor_prefixed/`), so every ancestor named `vendor` or
	 * `vendor_…` yields the candidate `<that ancestor's parent>/vendor`, nearest
	 * first. A folder that merely starts with "vendor" (e.g. a `vendors.example.com`
	 * domain folder) is deliberately not treated as an anchor.
	 *
	 * @since 1.25.0
	 *
	 * @param string $scheduler_dir Absolute path of the Scheduler package (`__DIR__`).
	 *
	 * @return string[] Candidate vendor directories (forward slashes), nearest first.
	 */
	public static function candidates( $scheduler_dir ) {
		$path       = str_replace( '\\', '/', (string) $scheduler_dir );
		$candidates = [];

		for ( $dir = $path; ; $dir = $parent ) {
			$parent = dirname( $dir );

			if ( $parent === $dir ) {
				break;
			}

			$segment = basename( $dir );

			// Anchor on a real vendor-dir name (Composer `vendor`, Strauss `vendor_…`),
			// not any folder that merely starts with "vendor" (e.g. a `vendors.example.com`
			// domain folder or a `vendor-apps` hosting dir), which would put a bogus
			// ancestor candidate ahead of the package's own copy.
			if ( 'vendor' === $segment || 0 === strpos( $segment, 'vendor_' ) ) {
				$candidates[] = $parent . '/vendor';
			}
		}

		// Un-bundled (standalone plugin) layout: <root>/src/Scheduler -> <root>/vendor.
		// Probed last: a flattened bundled copy has no `<root>/vendor` of its own, so
		// this only ever matches a true standalone install — including one nested under
		// a "vendor…"-named domain folder, where the ancestor walk above cannot reach it.
		$candidates[] = dirname( $path, 2 ) . '/vendor';

		// First-"vendor" substring: covers a custom Composer vendor-dir whose name embeds
		// "vendor" as an infix (e.g. `myvendor`), which the segment walk does not emit.
		$candidates[] = self::conventional_vendor_path( $path );

		return array_values( array_unique( $candidates ) );
	}

	/**
	 * Resolves the vendor directory by the last `vendor` substring in the path.
	 *
	 * Covers a custom Composer vendor-dir whose name embeds "vendor" as an infix
	 * (e.g. `myvendor`), which the nearest-segment walk in candidates() does not emit.
	 * Uses the *last* occurrence so the package's own dir wins over a "vendor…"-named
	 * ancestor higher up the path.
	 *
	 * @since 1.25.0
	 *
	 * @param string $scheduler_dir Absolute path of the Scheduler package (`__DIR__`).
	 *
	 * @return string Absolute path to the vendor directory.
	 */
	private static function conventional_vendor_path( $scheduler_dir ) {
		$pos = strrpos( $scheduler_dir, 'vendor' );

		if ( false !== $pos ) {
			return substr( $scheduler_dir, 0, $pos + strlen( 'vendor' ) );
		}

		return dirname( $scheduler_dir, 2 ) . '/vendor';
	}
}
