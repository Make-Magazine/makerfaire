<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 05-December-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

use InvalidArgumentException;
use Throwable;
use GravityKit\GravityView\Foundation\Exceptions\BaseException;
use GravityKit\GravityView\Foundation\Licenses\ProductManager;

/**
 * Stored notice – persisted until removed or expired.
 *
 * @since 1.3.0
 */
class StoredNotice extends Notice implements StoredNoticeInterface {
	/**
	 * Default expiry timestamp (0 = never).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	private const DEFAULT_EXPIRES = 0;

	/**
	 * Default persistence scope (site-wide).
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const DEFAULT_SCOPE = 'global';

	/**
	 * Default capability required for global dismissal.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	private const DEFAULT_GLOBAL_DISMISS_CAPABILITY = 'manage_options';

	/**
	 * Live notice – default polling interval (seconds).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	public const LIVE_DEFAULT_REFRESH = 5;

	/**
	 * Live notice – maximum allowed polling interval (seconds).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	public const LIVE_MAX_REFRESH = 60;

	/**
	 * Live notice – maximum consecutive errors before stopping polling.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	public const LIVE_MAX_CONSECUTIVE_ERRORS = 3;

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Validated definition (or one loaded from DB).
	 *
	 * @throws InvalidArgumentException When condition is not a callable string reference.
	 */
	protected function __construct( array $data ) {
		parent::__construct( $data );

		$condition = $this->get_condition();

		// Stored notices expect the condition to be a callable string reference.
		if ( null !== $condition && ( ! is_string( $condition ) || ! is_callable( $condition ) ) ) {
			throw new InvalidArgumentException( 'Stored notice "condition" must be a string referencing a callable.' );
		}
	}

	/**
	 * Static factory method to create new StoredNotice instances.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Validated definition (or one loaded from DB).
	 *
	 * @return static New stored notice instance.
	 *
	 * @throws InvalidArgumentException When condition is not a callable string reference.
	 */
	public static function create( array $data ): self {
		/** @phpstan-ignore-next-line new static() is safe here for factory method */
		return new static( $data );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_expiration(): int {
		return isset( $this->data['expires'] )
			? (int) $this->data['expires']
			: self::DEFAULT_EXPIRES;
	}

	/**
	 * Helper - checks if the notice is expired.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_expired(): bool {
		$expires = $this->get_expiration();

		return $expires && time() >= $expires;
	}


	/**
	 * {@inheritDoc}
	 */
	public function get_scope(): string {
		$scope = $this->data['scope'] ?? self::DEFAULT_SCOPE;

		return in_array( $scope, [ 'global', 'user' ], true )
			? $scope
			: self::DEFAULT_SCOPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_users(): array {
		$user_ids = $this->data['users'] ?? [];

		// Return raw array to support 'not:' prefix. The NoticeRepository will handle parsing.
		return (array) $user_ids;
	}

	/**
	 * Clamps a progress value between 0 and 100.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $progress The progress value to clamp.
	 *
	 * @return int The clamped progress value.
	 */
	private function clamp_progress( $progress ): int {
		return max( 0, min( 100, (int) $progress ) );
	}

	/**
	 * Applies a live update by invoking the notice's live callback, if configured.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeRepository $repository Repository instance for persistence operations.
	 *
	 * @return void
	 */
	public function apply_live_updates( NoticeRepository $repository ): void {
		$live = $this->get_live_config();

		if ( ! is_array( $live ) || empty( $live['callback'] ) || ! is_callable( $live['callback'] ) ) {
			return;
		}

		$context = [
			'id'       => $this->get_id(),
			'message'  => $this->get_message(),
			'progress' => $live['progress'] ?? null,
			'extra'    => $this->get_extra(),
		];

		if ( null !== $context['progress'] && 100 === $this->clamp_progress( $context['progress'] ) ) {
			return;
		}

		try {
			/** @var callable $cb */
			$cb       = $live['callback'];
			$response = $cb( $context );

			/**
			 * Filters the response from a live update callback.
			 *
			 * @filter `gk/foundation/notices/ajax/live-update`
			 *
			 * @since 1.3.0
			 *
			 * @param mixed                 $response Response from the callback.
			 * @param array                 $context  Context passed to the callback.
			 * @param StoredNoticeInterface $notice   The notice being updated.
			 */
			$response = apply_filters( 'gk/foundation/notices/ajax/live-update', $response, $context, $this );

			if ( $response instanceof BaseException ) {
				$this->data['live']['_error'] = $response->to_array();

				return;
			}

			if ( $response instanceof Throwable ) {
				$this->data['live']['_error'] = [
					'code'    => 'exception_in_callback',
					'message' => $response->getMessage(),
					'data'    => [
						'class' => get_class( $response ),
						'file'  => $response->getFile(),
						'line'  => $response->getLine(),
					],
				];

				return;
			}

			if ( is_wp_error( $response ) ) {
				$this->data['live']['_error'] = [
					'code'    => $response->get_error_code(),
					'message' => $response->get_error_message(),
					'data'    => $response->get_error_data(),
				];

				return;
			}

			if ( ! is_array( $response ) ) {
				$this->data['live']['_error'] = [
					'code'    => 'malformed_response',
					'message' => 'Live callback returned non-array payload',
				];

				return;
			}

			// Normalize the response to ensure all expected fields exist.
			$response = array_merge( $context, $response );

			$this->data['live']['progress'] = null !== $response['progress']
				? $this->clamp_progress( $response['progress'] )
				: null;

			if ( is_string( $response['message'] ) ) {
				$this->data['message'] = self::sanitize_message( $response['message'] );
			}

			if ( is_array( $response['extra'] ) ) {
				$this->data['extra'] = $response['extra'];
			}

			// Handle auto-dismissal.
			if ( ( $this->data['live']['auto_dismiss'] ?? false ) && 100 === $this->data['live']['progress'] ) {
				if ( 'user' === $this->get_scope() ) {
					// For user-scoped notices, dismiss for the current user.
					$repository->dismiss_for_user( get_current_user_id(), $this->get_id() );
				} else {
					// For global notices, remove from storage.
					$repository->remove( $this->get_id() );
				}

				// Signal to frontend that notice was auto-dismissed.
				$this->data['live']['_dismissed'] = true;
			} else {
				// Otherwise, persist the updated notice state.
				$repository->persist( $this );
			}
		} catch ( Throwable $e ) {
			$this->data['live']['_error'] = [
				'code'    => 'exception',
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * Returns the live configuration for dynamic updates.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>|null
	 */
	public function get_live_config(): ?array {
		return isset( $this->data['live'] ) && is_array( $this->data['live'] )
			? $this->data['live']
			: null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 *
	 * @param ProductManager|null $product_manager Optional ProductManager instance for dependency injection.
	 */
	public function as_payload( ?ProductManager $product_manager = null ): array {
		$payload = parent::as_payload( $product_manager );

		// Add live configuration if this is a stored notice with live config.
		$live_cfg = $this->get_live_config();

		if ( is_array( $live_cfg ) && isset( $live_cfg['callback'] ) ) {
			$interval = isset( $live_cfg['refresh_interval'] ) ? (int) $live_cfg['refresh_interval'] : self::LIVE_DEFAULT_REFRESH;

			// Enforce minimum of LIVE_DEFAULT_REFRESH only in production.
			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				$interval = max( self::LIVE_DEFAULT_REFRESH, min( $interval, self::LIVE_MAX_REFRESH ) );
			} else {
				$interval = min( $interval, self::LIVE_MAX_REFRESH );
			}

			$payload['live'] = [
				'refresh_interval' => $interval,
				'show_progress'    => ! empty( $live_cfg['show_progress'] ),
				'progress'         => isset( $live_cfg['progress'] ) ? (int) $live_cfg['progress'] : null,
				'auto_dismiss'     => (bool) ( $live_cfg['auto_dismiss'] ?? false ),
			];

			if ( isset( $live_cfg['_error'] ) ) {
				$payload['live']['_error'] = $live_cfg['_error'];
			}
		}

		// Add globally dismissible flag if applicable and user has capability.
		if ( $this->is_globally_dismissible() ) {
			$required_caps  = $this->get_global_dismiss_capability();
			$has_capability = false;

			// Check if user has any of the required capabilities.
			if ( is_array( $required_caps ) ) {
				foreach ( $required_caps as $cap ) {
					if ( current_user_can( $cap ) ) {
						$has_capability = true;
						break;
					}
				}
			} else {
				$has_capability = current_user_can( $required_caps );
			}

			if ( $has_capability ) {
				$payload['globally_dismissible']      = true;
				$payload['global_dismiss_capability'] = $required_caps;
			}
		}

		return $payload;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.4.0
	 */
	public function is_globally_dismissible(): bool {
		return ! empty( $this->data['globally_dismissible'] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.4.0
	 */
	public function get_global_dismiss_capability() {
		$caps = $this->data['global_dismiss_capability'] ?? self::DEFAULT_GLOBAL_DISMISS_CAPABILITY;

		// Cast single value to array and filter out empty strings.
		if ( is_array( $caps ) ) {
			/** @phpstan-ignore-next-line */
			return array_values( array_filter( $caps, 'strlen' ) );
		}

		return $caps;
	}
}
