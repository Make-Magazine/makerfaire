<?php
/**
 * Frontend download attachment pure rules.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0-beta.3
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

/**
 * Applies filename, glob, and limit rules for attachment downloads.
 *
 * @since 3.0.0-beta.3
 */
final class DownloadAttachmentRules {
	const STATUS_INCLUDED = 'included';
	const STATUS_SKIPPED  = 'skipped';

	const REASON_INCLUDED               = 'included';
	const REASON_EXCLUDED_PATTERN       = 'excluded-pattern';
	const REASON_PER_FILE_LIMIT         = 'per-file-limit';
	const REASON_PER_ENTRY_LIMIT        = 'per-entry-limit';
	const REASON_TOTAL_LIMIT            = 'total-limit';
	const REASON_FILE_COUNT_LIMIT       = 'file-count-limit';
	const REASON_MISSING_FILE           = 'missing-file';
	const REASON_UNREADABLE_FILE        = 'unreadable-file';
	const REASON_OUTSIDE_UPLOADS        = 'outside-upload-root';
	const REASON_EMPTY_VALUE            = 'empty-value';
	const REASON_FILTERED               = 'filtered';
	const REASON_INVALID_FILENAME       = 'invalid-filename';
	const REASON_ENTRY_FORM_NOT_IN_VIEW = 'entry-form-not-in-view';

	/**
	 * Normalizes newline-delimited glob patterns.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $patterns Raw pattern value.
	 *
	 * @return string[]
	 */
	public static function normalize_patterns( $patterns ) {
		if ( is_string( $patterns ) ) {
			$patterns = preg_split( '/\r\n|\r|\n/', $patterns );
		} elseif ( ! is_array( $patterns ) ) {
			$patterns = [];
		}

		$normalized = [];

		foreach ( $patterns as $pattern ) {
			if ( ! is_scalar( $pattern ) ) {
				continue;
			}

			$pattern = trim( (string) $pattern );

			if ( '' === $pattern ) {
				continue;
			}

			$normalized[ strtolower( $pattern ) ] = $pattern;
		}

		return array_values( $normalized );
	}

	/**
	 * Returns whether a basename matches any configured exclusion pattern.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string   $basename File basename.
	 * @param string[] $patterns Glob patterns.
	 *
	 * @return bool
	 */
	public static function basename_matches_any_glob( $basename, array $patterns ) {
		$basename = basename( str_replace( "\0", '', (string) $basename ) );

		foreach ( self::normalize_patterns( $patterns ) as $pattern ) {
			if ( self::glob_match( $pattern, $basename ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns a ZIP-safe basename.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $filename Original filename.
	 *
	 * @return string
	 */
	public static function sanitize_basename( $filename ) {
		$filename = str_replace( "\0", '', (string) $filename );
		$filename = str_replace( [ '\\', '/' ], '/', $filename );
		$filename = basename( $filename );
		$filename = trim( $filename );

		if ( '' === $filename || '.' === $filename || '..' === $filename ) {
			return '';
		}

		if ( function_exists( 'sanitize_file_name' ) ) {
			$filename = sanitize_file_name( $filename );
		} else {
			$filename = preg_replace( '/[^A-Za-z0-9._ -]/', '-', $filename );
			$filename = preg_replace( '/-+/', '-', (string) $filename );
			$filename = trim( (string) $filename, '.- ' );
		}

		$filename = str_replace( [ '\\', '/', "\0" ], '', (string) $filename );
		$filename = trim( $filename );

		if ( '' === $filename || '.' === $filename || '..' === $filename ) {
			return '';
		}

		if ( strlen( $filename ) > 180 ) {
			$extension = pathinfo( $filename, PATHINFO_EXTENSION );
			$stem      = '' === $extension ? $filename : substr( $filename, 0, -1 * ( strlen( $extension ) + 1 ) );
			$limit     = '' === $extension ? 180 : max( 1, 179 - strlen( $extension ) );
			$filename  = substr( $stem, 0, $limit ) . ( '' === $extension ? '' : '.' . $extension );
		}

		return $filename;
	}

	/**
	 * Returns a CSV-safe cell value.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $value Raw cell value.
	 *
	 * @return string
	 */
	public static function sanitize_csv_cell( $value ) {
		$value = (string) $value;

		if ( '' === $value ) {
			return '';
		}

		if ( in_array( $value[0], [ '=', '+', '-', '@', "\t" ], true ) ) {
			return "'" . $value;
		}

		return $value;
	}

	/**
	 * Returns a unique basename for a folder.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $basename Existing safe basename.
	 * @param array  $used     Already-used names keyed by lowercase basename.
	 *
	 * @return string
	 */
	public static function unique_basename( $basename, array &$used ) {
		$basename = self::sanitize_basename( $basename );

		if ( '' === $basename ) {
			return '';
		}

		$key = strtolower( $basename );

		if ( ! isset( $used[ $key ] ) ) {
			$used[ $key ] = true;
			return $basename;
		}

		$extension = pathinfo( $basename, PATHINFO_EXTENSION );
		$stem      = '' === $extension ? $basename : substr( $basename, 0, -1 * ( strlen( $extension ) + 1 ) );
		$suffix    = 1;

		do {
			$candidate = $stem . '-' . $suffix . ( '' === $extension ? '' : '.' . $extension );
			$key       = strtolower( $candidate );
			++$suffix;
		} while ( isset( $used[ $key ] ) );

		$used[ $key ] = true;

		return $candidate;
	}

	/**
	 * Returns the first limit reason that prevents adding a file.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $file_size       File size in bytes.
	 * @param int $entry_bytes     Bytes already included for this entry.
	 * @param int $total_bytes     Bytes already included for this operation.
	 * @param int $total_count     Files already included for this operation.
	 * @param int $per_file_limit  Per-file byte limit.
	 * @param int $per_entry_limit Per-entry byte limit.
	 * @param int $total_limit     Operation byte limit.
	 * @param int $file_count_cap  Operation file-count cap.
	 *
	 * @return string
	 */
	public static function first_limit_reason( $file_size, $entry_bytes, $total_bytes, $total_count, $per_file_limit, $per_entry_limit, $total_limit, $file_count_cap ) {
		$file_size       = max( 0, (int) $file_size );
		$entry_bytes     = max( 0, (int) $entry_bytes );
		$total_bytes     = max( 0, (int) $total_bytes );
		$total_count     = max( 0, (int) $total_count );
		$per_file_limit  = max( 0, (int) $per_file_limit );
		$per_entry_limit = max( 0, (int) $per_entry_limit );
		$total_limit     = max( 0, (int) $total_limit );
		$file_count_cap  = max( 0, (int) $file_count_cap );

		if ( $per_file_limit > 0 && $file_size > $per_file_limit ) {
			return self::REASON_PER_FILE_LIMIT;
		}

		if ( $per_entry_limit > 0 && $entry_bytes + $file_size > $per_entry_limit ) {
			return self::REASON_PER_ENTRY_LIMIT;
		}

		if ( $total_limit > 0 && $total_bytes + $file_size > $total_limit ) {
			return self::REASON_TOTAL_LIMIT;
		}

		if ( $file_count_cap > 0 && $total_count + 1 > $file_count_cap ) {
			return self::REASON_FILE_COUNT_LIMIT;
		}

		return '';
	}

	/**
	 * Performs a case-insensitive glob match against one basename.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $pattern  Glob pattern.
	 * @param string $basename File basename.
	 *
	 * @return bool
	 */
	private static function glob_match( $pattern, $basename ) {
		$pattern  = strtolower( (string) $pattern );
		$basename = strtolower( (string) $basename );

		if ( function_exists( 'fnmatch' ) ) {
			return fnmatch( $pattern, $basename );
		}

		$regex  = '';
		$length = strlen( $pattern );

		for ( $i = 0; $i < $length; ++$i ) {
			$char = $pattern[ $i ];

			if ( '*' === $char ) {
				$regex .= '.*';
				continue;
			}

			if ( '?' === $char ) {
				$regex .= '.';
				continue;
			}

			if ( '[' === $char ) {
				$end = strpos( $pattern, ']', $i + 1 );

				if ( false !== $end ) {
					$class  = substr( $pattern, $i, $end - $i + 1 );
					$regex .= $class;
					$i      = $end;
					continue;
				}
			}

			$regex .= preg_quote( $char, '#' );
		}

		$regex = '#^' . $regex . '$#u';

		return 1 === preg_match( $regex, $basename );
	}
}
