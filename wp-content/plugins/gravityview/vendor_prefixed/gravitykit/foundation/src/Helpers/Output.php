<?php
/**
 * @package GravityKit\GravityView\Foundation\Helpers
 */

namespace GravityKit\GravityView\Foundation\Helpers;

/**
 * Detects output emitted before a response was meant to begin.
 *
 * A PHP file that closes with `?>` followed by a blank line prints those bytes
 * every time it is included. Nothing shows on a rendered page, so the fault can
 * sit in a site for years; it is fatal to anything binary, because a downloaded
 * archive no longer starts at its own first byte and will not open.
 *
 * Detection is deliberately conservative. `headers_sent()` reports where output
 * began, never why, and a PHP warning rendered to the page reports identically
 * to a stray closing tag. Only a cause that can be proven from the file itself
 * is reported, so a warning is never mistaken for one.
 *
 * @since 1.30.0
 */
class Output {
	/**
	 * Largest file this will read back to confirm a cause.
	 *
	 * @since 1.30.0
	 */
	const MAX_SOURCE_BYTES = 1048576;

	/**
	 * Reported when a file's PHP block was closed before the end of the file.
	 *
	 * @since 1.30.0
	 */
	const CAUSE_TRAILING_TAG = 'trailing-tag';

	/**
	 * Examine the response for output that was emitted before the caller.
	 *
	 * @since 1.30.0
	 *
	 * @return array{cause:string,file:string,line:int} `cause` is empty when
	 *                                                  there is nothing to report.
	 */
	public static function inspect(): array {
		$file = '';
		$line = 0;

		$flushed = headers_sent( $file, $line );
		$pending = $flushed ? 0 : self::pending_bytes();

		return self::evaluate( $flushed, (string) $file, (int) $line, $pending, get_included_files() );
	}

	/**
	 * Decide what an observed response state amounts to.
	 *
	 * Separated from inspect() so the reasoning can be exercised directly;
	 * headers_sent() and the buffer functions cannot be simulated.
	 *
	 * @since 1.30.0
	 *
	 * @param bool          $flushed Whether output has already left the process.
	 * @param string        $file    File output began in, when known.
	 * @param int           $line    Line output began on, when known.
	 * @param int           $pending Bytes still held in output buffers.
	 * @param array<string> $files   Loaded files, searched when output is buffered.
	 *
	 * @return array{cause:string,file:string,line:int}
	 */
	public static function evaluate( bool $flushed, string $file, int $line, int $pending, array $files = [] ): array {
		$nothing = [
			'cause' => '',
			'file'  => '',
			'line'  => 0,
		];

		// Buffered output carries no record of its origin, so it is traced back
		// through the files that could have produced it.
		if ( ! $flushed ) {
			return self::attribute_pending( $pending, $files );
		}

		if ( ! self::is_trailing_tag( $file, $line ) ) {
			return $nothing;
		}

		return [
			'cause' => self::CAUSE_TRAILING_TAG,
			'file'  => $file,
			'line'  => $line,
		];
	}

	/**
	 * Bytes a file emits because its PHP block was closed before the end of it.
	 *
	 * PHP swallows one newline directly after a closing tag, so a file ending
	 * `?>` and a single line break prints nothing at all and is not a fault.
	 * Returns null when the file has no such tag, or nothing follows it that
	 * would reach the browser.
	 *
	 * @since 1.30.0
	 *
	 * @param string $file Absolute path.
	 *
	 * @return array{bytes:int,line:int}|null Emitted byte count and the line the
	 *                                        first of them sits on.
	 */
	public static function trailing_emission( string $file ) {
		if ( '' === $file || ! is_readable( $file ) ) {
			return null;
		}

		$size = filesize( $file );

		if ( false === $size || $size > self::MAX_SOURCE_BYTES ) {
			return null;
		}

		$source = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( false === $source ) {
			return null;
		}

		$close = strrpos( $source, '?' . '>' );

		if ( false === $close ) {
			return null;
		}

		// Nothing after __halt_compiler() is compiled, so a tag beyond it is inert.
		$halt = stripos( $source, '__halt_compiler' );

		if ( false !== $halt && $halt < $close ) {
			return null;
		}

		$trailing = substr( $source, $close + 2 );

		if ( '' !== trim( $trailing ) ) {
			return null;
		}

		// The one line break immediately after the tag belongs to the tag and is
		// never printed; whatever survives that is what reaches the browser.
		$emitted   = preg_replace( '/\A(?:\r\n|\n|\r)/', '', $trailing, 1 );
		$swallowed = $emitted !== $trailing;

		if ( '' === $emitted ) {
			return null;
		}

		$before     = substr( $source, 0, $close );
		$newlines   = substr_count( $before, "\n" );
		$close_line = ( 0 === $newlines ? substr_count( $before, "\r" ) : $newlines ) + 1;

		return [
			'bytes' => strlen( $emitted ),
			'line'  => $close_line + ( $swallowed ? 1 : 0 ),
		];
	}

	/**
	 * Whether a file printing at a given line is explained by a trailing tag.
	 *
	 * The line must be exactly where the tag's own output would land. Output
	 * reported anywhere else in the file came from something else — a warning,
	 * a deliberate echo — and the tag is not the cause.
	 *
	 * @since 1.30.0
	 *
	 * @param string $file Absolute path to the file output began in.
	 * @param int    $line Line output began on.
	 *
	 * @return bool
	 */
	public static function is_trailing_tag( string $file, int $line ): bool {
		if ( $line < 1 ) {
			return false;
		}

		$emission = self::trailing_emission( $file );

		return null !== $emission && $emission['line'] === $line;
	}

	/**
	 * Total bytes waiting in every output buffer.
	 *
	 * Only the innermost buffer is described by ob_get_length(), while PHP's own
	 * `output_buffering` sits at the bottom of the stack — which is exactly
	 * where a theme's stray bytes land on a stock configuration.
	 *
	 * @since 1.30.0
	 *
	 * @param int $floor Buffers at or below this level are someone else's and
	 *                   are not counted. Zero counts the whole stack.
	 *
	 * @return int
	 */
	public static function pending_bytes( int $floor = 0 ): int {
		$total = 0;

		// ob_get_status( true ) is indexed from the outermost buffer, so index N
		// describes level N + 1.
		foreach ( ob_get_status( true ) as $index => $status ) {
			if ( $index < $floor ) {
				continue;
			}

			$total += (int) ( $status['buffer_used'] ?? 0 );
		}

		return $total;
	}

	/**
	 * Hold back output so a binary response can still start at its first byte.
	 *
	 * A file served from the admin has to be the whole response body. Themes and
	 * plugins loading after the caller can still print, and on a server with no
	 * buffering of its own those bytes reach the browser before the download
	 * runs, where nothing can take them back. Called early — while the product's
	 * own plugin file loads — this keeps them recoverable.
	 *
	 * Only worth calling on a request already known to serve a file; buffering
	 * anything else changes output timing for no reason.
	 *
	 * @since 1.30.0
	 *
	 * @return bool Whether a buffer was opened.
	 */
	public static function protect(): bool {
		return ob_start();
	}

	/**
	 * Drop pending output down to $floor.
	 *
	 * Each buffer is handled by what its own flags permit: removable ones are
	 * popped, one that is merely cleanable is emptied in place, and one that is
	 * neither ends the sweep — nothing underneath it can be reached, and calling
	 * ob_end_clean() on it would fail and raise a notice that is itself output.
	 * is_clean() is the authority on the result.
	 *
	 * @since 1.30.0
	 *
	 * @param int $floor Buffer level to stop at.
	 *
	 * @return void
	 */
	public static function discard( int $floor = 0 ): void {
		while ( ob_get_level() > $floor ) {
			$status = ob_get_status();
			$flags  = isset( $status['flags'] ) ? (int) $status['flags'] : 0;

			if ( $flags & PHP_OUTPUT_HANDLER_REMOVABLE ) {
				ob_end_clean();

				continue;
			}

			if ( $flags & PHP_OUTPUT_HANDLER_CLEANABLE ) {
				ob_clean();
			}

			return;
		}
	}

	/**
	 * Whether a binary body would still start at the first byte of the response.
	 *
	 * False once any output has left the process, and false while output is
	 * pending in a buffer the sweep could not empty — in that case headers_sent()
	 * is still false, so it cannot answer this on its own.
	 *
	 * @since 1.30.0
	 *
	 * @param int       $floor   Buffer level below which buffers belong to the caller.
	 * @param bool|null $started Whether the response has already begun. Defaults
	 *                           to headers_sent(); pass it when the caller knows
	 *                           better, as under the CLI SAPI where that is true
	 *                           from the first line a test runner prints.
	 *
	 * @return bool
	 */
	public static function is_clean( int $floor = 0, ?bool $started = null ): bool {
		$started = null === $started ? headers_sent() : $started;

		if ( $started ) {
			return false;
		}

		return 0 === self::pending_bytes( $floor );
	}

	/**
	 * Find the loaded file accounting for bytes still held in a buffer.
	 *
	 * Buffered output carries no record of where it came from, so the source is
	 * identified by elimination: exactly one loaded file may emit through a
	 * trailing tag, and what it emits must account for every pending byte. Any
	 * ambiguity — two candidates, or a byte count that does not add up — means
	 * something else contributed, and nothing is reported.
	 *
	 * @since 1.30.0
	 *
	 * @param int           $pending Bytes waiting in buffers.
	 * @param array<string> $files   Loaded files to consider.
	 *
	 * @return array{cause:string,file:string,line:int}
	 */
	public static function attribute_pending( int $pending, array $files ): array {
		$nothing = [
			'cause' => '',
			'file'  => '',
			'line'  => 0,
		];

		if ( $pending < 1 ) {
			return $nothing;
		}

		$match = null;

		foreach ( $files as $file ) {
			$emission = self::trailing_emission( (string) $file );

			// A file only accounts for the pending output if it explains all of
			// it. A site of any size has several files ending in a closing tag;
			// most emit an amount that does not match and are not candidates.
			if ( null === $emission || $emission['bytes'] !== $pending ) {
				continue;
			}

			if ( null !== $match ) {
				// Two files could equally have printed it. Which one did cannot
				// be known from here, and a guess would name the wrong file.
				return $nothing;
			}

			$match = [ $file, $emission ];
		}

		if ( null === $match ) {
			return $nothing;
		}

		return [
			'cause' => self::CAUSE_TRAILING_TAG,
			'file'  => (string) $match[0],
			'line'  => (int) $match[1]['line'],
		];
	}

	/**
	 * Path of a file relative to the WordPress content directory.
	 *
	 * Keeps absolute server paths out of a message rendered in a browser.
	 *
	 * @since 1.30.0
	 *
	 * @param string      $file        Absolute path.
	 * @param string|null $content_dir Content directory to measure against.
	 *                                 Defaults to the WordPress constant.
	 *
	 * @return string Relative path, or the file's own name when it sits outside
	 *                the content directory.
	 */
	public static function relative_path( string $file, ?string $content_dir = null ): string {
		if ( '' === $file ) {
			return '';
		}

		if ( null === $content_dir ) {
			$content_dir = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : '';
		}

		$normalized  = str_replace( '\\', '/', $file );
		$content_dir = str_replace( '\\', '/', $content_dir );

		if ( '' !== $content_dir && 0 === strpos( $normalized, $content_dir ) ) {
			return ltrim( substr( $normalized, strlen( $content_dir ) ), '/' );
		}

		return basename( $normalized );
	}
}
