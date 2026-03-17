<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Logger;

use GravityKit\GravityEdit\Foundation\ThirdParty\Monolog\Handler\RotatingFileHandler;
use DateTime;
use InvalidArgumentException;
use Exception;

/**
 * Weekly rotating file handler that extends GravityKit\GravityEdit\Foundation\ThirdParty\Monolog's RotatingFileHandler.
 *
 * @since 1.3.0
 */
// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
class WeeklyRotatingFileHandler extends RotatingFileHandler {
	/**
	 * Weekly rotation format using ISO 8601 week notation.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	const FILE_PER_WEEK = 'Y-\WW';

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 *
	 * @param string $filename_format Filename format.
	 * @param string $date_format Date format.
	 *
	 * @throws InvalidArgumentException
	 *
	 * @return void
	 */
	public function setFilenameFormat( $filename_format, $date_format ) {
		// Only override validation for weekly format.
		if ( self::FILE_PER_WEEK === $date_format ) {
			if ( 0 === substr_count( $filename_format, '{date}' ) ) {
				throw new InvalidArgumentException(
					'Invalid filename format - format must contain {date}'
				);
			}

			$this->filenameFormat = $filename_format;
			$this->dateFormat     = $date_format;
			$this->url            = $this->getTimedFilename();

			$this->close();

			return;
		}

		parent::setFilenameFormat( $filename_format, $date_format );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 *
	 * @throws Exception
	 *
	 * @return    void
	 */
	protected function rotate() {
		// Update filename.
		$this->url = $this->getTimedFilename();

		// Calculate next week start based on WP settings.
		$start_of_week = (int) get_option( 'start_of_week', 1 );
		$days          = [ 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' ];

		$current_date    = current_time( 'Y-m-d H:i:s' );
		$next_timestamp  = strtotime( 'next ' . $days[ $start_of_week ] . ' midnight', (int) strtotime( $current_date ) );
		$next_week_start = date( 'Y-m-d H:i:s', $next_timestamp ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date

		$this->nextRotation = new DateTime( $next_week_start );

		// Handle cleanup with proper sorting for weekly files.
		if ( 0 === $this->maxFiles ) {
			return;
		}

		$log_files = glob( $this->getGlobPattern() );

		if ( false === $log_files || $this->maxFiles >= count( $log_files ) ) {
			return;
		}

		// Sort files by actual date, not alphabetically.
		usort(
            $log_files,
            function ( $a, $b ) {
				if ( preg_match( '/(\d{4})-W(\d{2})/', $a, $matches_1 ) &&
			     preg_match( '/(\d{4})-W(\d{2})/', $b, $matches_2 ) ) {
					$year_1 = (int) $matches_1[1];
					$year_2 = (int) $matches_2[1];
					$week_1 = (int) $matches_1[2];
					$week_2 = (int) $matches_2[2];

					// Compare years first, then weeks.
					if ( $year_1 !== $year_2 ) {
						return $year_2 - $year_1;
					}

					return $week_2 - $week_1;
				}

				// Fallback to string comparison if pattern doesn't match.
				return strcmp( $b, $a );
			}
        );

		// Remove oldest files.
		foreach ( array_slice( $log_files, $this->maxFiles ) as $file ) {
			if ( is_writable( $file ) ) {
				set_error_handler( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
                    function ( $errno, $errstr, $errfile, $errline ) {
						return true;
                    }
                );

				unlink( $file );

				restore_error_handler(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
			}
		}

		$this->mustRotate = false;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	protected function getTimedFilename() {
		$file_info      = pathinfo( $this->filename );
		$dirname        = isset( $file_info['dirname'] ) ? $file_info['dirname'] : '';
		$timed_filename = str_replace(
			[ '{filename}', '{date}' ],
			[ $file_info['filename'], current_time( $this->dateFormat ) ], // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$dirname . '/' . $this->filenameFormat
		);

		if ( ! empty( $file_info['extension'] ) ) {
			$timed_filename .= '.' . $file_info['extension'];
		}

		return $timed_filename;
	}

	/**
	 * Get glob pattern for finding rotated files.
	 * Overrides parent to handle weekly format.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	protected function getGlobPattern() {
		// For weekly format, use a specific pattern.
		if ( self::FILE_PER_WEEK === $this->dateFormat ) {
			$file_info = pathinfo( $this->filename );
			$dirname   = isset( $file_info['dirname'] ) ? $file_info['dirname'] : '';
			$glob      = str_replace(
				[ '{filename}', '{date}' ],
				[ $file_info['filename'], '[0-9][0-9][0-9][0-9]-W[0-9][0-9]' ],
				$dirname . '/' . $this->filenameFormat
			);

			if ( ! empty( $file_info['extension'] ) ) {
				$glob .= '.' . $file_info['extension'];
			}

			return $glob;
		}

		// Use parent's pattern for other formats.
		return parent::getGlobPattern();
	}
}
// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

