<?php

namespace GravityKit\GravityView\Foundation\Notices;

use Closure;
use InvalidArgumentException;

/**
 * Creates notice instances from associative array specifications.
 *
 * @since 1.3.0
 */
final class NoticeFactory implements NoticeFactoryInterface {
	/**
	 * Required fields for a notice.
	 *
	 * @since 1.3.0
	 *
	 * @var string[]
	 */
	private const REQUIRED_FIELDS = [ 'namespace', 'slug', 'message' ];

	/**
	 * Creates a runtime notice instance.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @throws NoticeException When notice creation fails.
	 *
	 * @return RuntimeNoticeInterface Immutable runtime-notice instance.
	 */
	public function make_runtime( array $data ): RuntimeNoticeInterface {
		try {
			$this->assert_required_fields( $data );

			$condition = $data['condition'] ?? '__return_true';

			if ( ! is_callable( $condition ) ) {
				throw $this->invalid_definition( $data );
			}

			return RuntimeNotice::create( $data, $condition );
		} catch ( InvalidArgumentException $e ) {
			throw self::invalid_definition( $data );
		}
	}

	/**
	 * Creates a stored notice instance.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @throws NoticeException When notice creation fails.
	 *
	 * @return StoredNoticeInterface Immutable stored-notice instance.
	 */
	public function make_stored( array $data ): StoredNoticeInterface {
		try {
			$this->assert_required_fields( $data );

			$scope = $data['scope'] ?? 'global';

			if ( ! in_array( $scope, [ 'global', 'user' ], true ) ) {
				throw $this->invalid_definition( $data );
			}

			if ( 'user' === $scope && isset( $data['users'] ) ) {
				foreach ( (array) $data['users'] as $user_id ) {
					if ( ! is_numeric( $user_id ) || (int) $user_id <= 0 ) {
						throw $this->invalid_definition( $data );
					}
				}
			}

			$condition = $data['condition'] ?? '__return_true';

			if ( ! is_string( $condition ) || ! is_callable( $condition ) ) {
				throw $this->invalid_definition( $data );
			}

			$data['condition'] = $condition;

			if ( isset( $data['extra'] ) ) {
				self::assert_serializable( $data['extra'], 'extra' );
			}

			// Validation: live notice configuration (top-level).
			if ( isset( $data['live'] ) && is_array( $data['live'] ) ) {
				$live = &$data['live'];

				// `callback` is mandatory and must be a callable string.
				if ( empty( $live['callback'] ) || ! is_string( $live['callback'] ) || ! is_callable( $live['callback'] ) ) {
					throw $this->invalid_definition( $data );
				}

				// Normalise/validate refresh interval.
				$interval = isset( $live['refresh_interval'] ) ? (int) $live['refresh_interval'] : StoredNotice::LIVE_DEFAULT_REFRESH;

				if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
					$interval = max( StoredNotice::LIVE_DEFAULT_REFRESH, min( $interval, StoredNotice::LIVE_MAX_REFRESH ) );
				} else {
					$interval = min( $interval, StoredNotice::LIVE_MAX_REFRESH );
				}

				$live['refresh_interval'] = $interval;

				// `show_progress` normalized to bool when present.
				if ( isset( $live['show_progress'] ) ) {
					$live['show_progress'] = (bool) $live['show_progress'];
				}

				// `auto_hide_progress` normalized to bool when present.
				if ( isset( $live['auto_hide_progress'] ) ) {
					$live['auto_hide_progress'] = (bool) $live['auto_hide_progress'];
				}
			}

			// Flash notices are always "dismissed" when displayed, so no need to store them as dismissible.
			if ( isset( $data['flash'] ) ) {
				$data['dismissible'] = false;
			}

			return StoredNotice::create( $data );
		} catch ( InvalidArgumentException $e ) {
			throw self::invalid_definition( $data );
		}
	}

	/**
	 * Ensures the mandatory keys are present.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @throws InvalidArgumentException When any required key is missing or empty.
	 *
	 * @return void
	 */
	private function assert_required_fields( array $data ): void {
		foreach ( self::REQUIRED_FIELDS as $key ) {
			if ( empty( $data[ $key ] ) ) {
				throw new InvalidArgumentException( sprintf( 'Notice is missing the required key "%s".', $key ) );
			}
		}
	}

	/**
	 * Recursively ensures a value can be safely serialized for storage by rejecting closures and resources.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed  $value The value to validate.
	 * @param string $path  Dot-notation path used for clearer exception messages.
	 *
	 * @throws InvalidArgumentException When an unserializable value is found.
	 *
	 * @return void
	 */
	private static function assert_serializable( $value, string $path ): void {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				self::assert_serializable( $v, $path . '[' . $k . ']' );
			}

			return;
		}

		if ( is_resource( $value ) || $value instanceof Closure ) {
			throw new InvalidArgumentException( sprintf( 'The "%s" property contains a value that cannot be serialized (closures/resources are not allowed).', $path ) );
		}
	}

	/**
	 * Creates a NoticeException instance for invalid definition.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @return NoticeException
	 */
	private function invalid_definition( array $data ) {
		return NoticeException::validation( sprintf( 'Invalid notice definition "%s".', $data['slug'] ?? 'unknown' ), [ 'definition' => $data ] );
	}
}
