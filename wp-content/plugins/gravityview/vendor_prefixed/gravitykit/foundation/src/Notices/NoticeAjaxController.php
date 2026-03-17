<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

use GravityKit\GravityView\Foundation\Helpers\Users;

/**
 * Ajax controller that wires AjaxRouter routes to NoticeRepository methods.
 *
 * @since 1.3.0
 */
final class NoticeAjaxController {
	/**
	 * Router slug used by frontend JS when sending Ajax requests.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const AJAX_ROUTER = 'notices';

	/**
	 * NoticeRepository instance for persistence operations.
	 *
	 * @since 1.3.0
	 *
	 * @var NoticeRepository
	 */
	private $repository;

	/**
	 * NoticeManager instance for notice operations.
	 *
	 * @since 1.3.0
	 *
	 * @var NoticeManager
	 */
	private $manager;


	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeRepository $repository NoticeRepository instance.
	 * @param NoticeManager    $manager    NoticeManager instance.
	 */
	public function __construct( NoticeRepository $repository, NoticeManager $manager ) {
		$this->repository = $repository;
		$this->manager    = $manager;

		add_filter(
			'gk/foundation/ajax/' . self::AJAX_ROUTER . '/routes',
			[ $this, 'routes' ]
		);
	}

	/**
	 * Returns Ajax route map consumed by the Foundation's AjaxRouter.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string, callable> $routes Existing routes.
	 *
	 * @return array<string, callable>
	 */
	public function routes( array $routes ): array {
		return $routes + [
			'dismiss'        => [ $this, 'dismiss' ],
			'dismiss_global' => [ $this, 'dismiss_global' ],
			'snooze'         => [ $this, 'snooze' ],
			'live'           => [ $this, 'live' ],
		];
	}

	/**
	 * Handles the notice "dismiss" Ajax action.
	 *
	 * @since 1.3.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @throws NoticeException When requirements are not met or dismissal fails.
	 *
	 * @return bool
	 */
	public function dismiss( array $payload ): bool {
		$user_id = Users::current_id();

		if ( ! $user_id ) {
			throw NoticeException::forbidden( 'Not logged in' );
		}

		// Normalize input to an array of string IDs.
		if ( isset( $payload['ids'] ) && is_array( $payload['ids'] ) ) {
			$notice_ids = array_filter( $payload['ids'], 'is_string' );
		} else {
			$notice_id = $payload['id'] ?? null;

			if ( ! $notice_id ) {
				throw NoticeException::validation( __( 'Missing "id" parameter', 'gk-gravityview' ) );
			}

			$notice_ids = [ (string) $notice_id ];
		}

		$errors = [];

		foreach ( $notice_ids as $notice_id ) {
			try {
				$this->repository->dismiss_for_user( $user_id, $notice_id );

				/**
				 * Fires after a notice has been dismissed via Ajax.
				 *
				 * @action `gk/foundation/notices/ajax/dismissed`
				 *
				 * @since  1.3.0
				 *
				 * @param string $notice_id ID of the dismissed notice.
				 * @param int    $user_id   ID of the user who dismissed the notice.
				 */
				do_action( 'gk/foundation/notices/ajax/dismissed', $notice_id, $user_id );
			} catch ( NoticeException $e ) {
				$errors[] = [
					'id'    => $notice_id,
					'error' => $e->get_error_message(),
				];
			}
		}

		if ( ! empty( $errors ) ) {
			throw NoticeException::persistence( 'dismiss_failed', [ 'errors' => $errors ] );
		}

		return true;
	}

	/**
	 * Handles the notice "dismiss_global" Ajax action.
	 * This permanently removes the notice from the database for all users.
	 *
	 * @since 1.4.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @throws NoticeException When requirements are not met or global dismissal fails.
	 *
	 * @return array Response with success status and affected notices.
	 */
	public function dismiss_global( array $payload ): array {
		$user_id = Users::current_id();

		if ( ! $user_id ) {
			throw NoticeException::forbidden( 'Not logged in' );
		}

		// Normalize input to an array of string IDs.
		if ( isset( $payload['ids'] ) && is_array( $payload['ids'] ) ) {
			$notice_ids = array_filter( $payload['ids'], 'is_string' );
		} else {
			$notice_id = $payload['id'] ?? null;

			if ( ! $notice_id ) {
				throw NoticeException::validation( __( 'Missing "id" parameter', 'gk-gravityview' ) );
			}

			$notice_ids = [ (string) $notice_id ];
		}

		$results = [
			'global'   => [],
			'personal' => [],
			'errors'   => [],
		];

		foreach ( $notice_ids as $notice_id ) {
			try {
				$notice = $this->manager->get_notice( $notice_id );

				if ( ! $notice instanceof StoredNoticeInterface ) {
					// Runtime notice or not found - fall back to personal dismiss.
					$this->repository->dismiss_for_user( $user_id, $notice_id );

					$results['personal'][] = $notice_id;

					continue;
				}

				// Check if notice is globally dismissible and user has capability.
				if ( $notice->is_globally_dismissible() ) {
					$required_caps  = $notice->get_global_dismiss_capability();
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
						// Remove from database entirely.
						$this->repository->remove( $notice_id );

						$results['global'][] = $notice_id;

						/**
						 * Fires after a notice has been globally dismissed via Ajax.
						 *
						 * @action `gk/foundation/notices/ajax/dismissed-global`
						 *
						 * @since  1.4.0
						 *
						 * @param string $notice_id ID of the globally dismissed notice.
						 * @param int    $user_id   ID of the user who globally dismissed the notice.
						 */
						do_action( 'gk/foundation/notices/ajax/dismissed-global', $notice_id, $user_id );
					} else {
						// Fall back to personal dismiss if user lacks capability.
						$results['personal'][] = $notice_id;

						$this->repository->dismiss_for_user( $user_id, $notice_id );

						/**
						 * Fires after a notice has been dismissed via Ajax.
						 *
						 * @action `gk/foundation/notices/ajax/dismissed`
						 *
						 * @since  1.3.0
						 *
						 * @param string $notice_id ID of the dismissed notice.
						 * @param int    $user_id   ID of the user who dismissed the notice.
						 */
						do_action( 'gk/foundation/notices/ajax/dismissed', $notice_id, $user_id );
					}
				} else {
					// Fall back to personal dismiss if not globally dismissible.
					$this->repository->dismiss_for_user( $user_id, $notice_id );

					$results['personal'][] = $notice_id;

					/**
					 * Fires after a notice has been dismissed via Ajax.
					 *
					 * @action `gk/foundation/notices/ajax/dismissed`
					 *
					 * @since  1.3.0
					 *
					 * @param string $notice_id ID of the dismissed notice.
					 * @param int    $user_id   ID of the user who dismissed the notice.
					 */
					do_action( 'gk/foundation/notices/ajax/dismissed', $notice_id, $user_id );
				}
			} catch ( NoticeException $e ) {
				$results['errors'][] = [
					'id'    => $notice_id,
					'error' => $e->get_error_message(),
				];
			}
		}

		if ( ! empty( $results['errors'] ) && empty( $results['global'] ) && empty( $results['personal'] ) ) {
			throw NoticeException::persistence( 'dismiss_global_failed', [ 'errors' => $results['errors'] ] );
		}

		return $results;
	}

	/**
	 * Handles the notice "snooze" Ajax action.
	 *
	 * @since 1.3.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @throws NoticeException When requirements are not met or snoozing fails.
	 *
	 * @return bool
	 */
	public function snooze( array $payload ): bool {
		$notice_id = $payload['id'] ?? null;
		$seconds   = (int) ( $payload['in'] ?? 0 );

		if ( ! $notice_id || $seconds <= 0 ) {
			throw NoticeException::validation( __( 'Missing "id" or invalid "in" parameter.', 'gk-gravityview' ) );
		}

		$user_id = Users::current_id();

		if ( ! $user_id ) {
			throw NoticeException::forbidden( 'Not logged in' );
		}

		$snooze_until = time() + $seconds;

		$this->repository->snooze_for_user( $user_id, $notice_id, $snooze_until );

		/**
		 * Fires after a notice has been snoozed via Ajax.
		 *
		 * @action `gk/foundation/notices/ajax/snoozed`
		 *
		 * @since  1.3.0
		 *
		 * @param string $notice_id    ID of the snoozed notice.
		 * @param int    $user_id      ID of the user who snoozed the notice.
		 * @param int    $snooze_until Timestamp when the snooze expires.
		 */
		do_action( 'gk/foundation/notices/ajax/snoozed', $notice_id, $user_id, $snooze_until );

		return true;
	}

	/**
	 * Handles the "live" Ajax action for polling live notice updates.
	 *
	 * The request payload expects:
	 *   - id (string): notice ID.
	 *   - force (bool, optional): bypass server rate-limit; honoured only when WP_DEBUG is true.
	 *
	 * @since 1.3.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @throws NoticeException When requirements are not met or processing fails.
	 *
	 * @return array<string,mixed> Response payload provided by the plugin callback.
	 */
	public function live( array $payload ): array {
		// Batch mode: when an array of IDs is supplied, return responses keyed by each ID.
		if ( isset( $payload['ids'] ) && is_array( $payload['ids'] ) ) {
			$notice_ids = array_filter( $payload['ids'], 'is_string' );
			$responses  = [];

			foreach ( $notice_ids as $notice_id ) {
				// Re-use the single-item logic by calling this method recursively with a single ID.
				try {
					$single_response         = $this->live(
						[
							'id' => $notice_id,
						]
					);
					$responses[ $notice_id ] = $single_response;
				} catch ( NoticeException $e ) {
					$responses[ $notice_id ] = [
						'error'   => $e->get_error_code(),
						'message' => $e->get_error_message(),
						'data'    => $e->get_error_data(),
					];
				}
			}

			return $responses;
		}

		$notice_id = $payload['id'] ?? '';

		if ( ! is_string( $notice_id ) || '' === $notice_id ) {
			throw NoticeException::validation( __( 'Missing "id" parameter', 'gk-gravityview' ) );
		}

		$notice = $this->manager->get_notice( $notice_id );

		if ( ! $notice instanceof StoredNoticeInterface ) {
			throw NoticeException::not_found( 'Notice not found' );
		}

		// Capability guard: ensure current user is allowed to see the notice.
		if ( ! $this->manager->get_evaluator()->check_capabilities( $notice ) ) {
			throw NoticeException::forbidden( __( 'Insufficient permissions.', 'gk-gravityview' ) );
		}

		$live = method_exists( $notice, 'get_live_config' ) ? $notice->get_live_config() : null;

		if ( ! is_array( $live ) || empty( $live['callback'] ) || ! is_callable( $live['callback'] ) ) {
			throw NoticeException::validation( __( 'Invalid configuration.', 'gk-gravityview' ) );
		}

		$notice->apply_live_updates( $this->repository );

		$live = $notice->get_live_config();

		$response = [
			'message'  => $notice->get_message(),
			'progress' => $live['progress'] ?? 0,
		];

		if ( isset( $live['_error'] ) ) {
			$response['error'] = $live['_error'];
		}

		if ( ! empty( $live['_dismissed'] ) ) {
			$response['dismissed'] = true;
		}

		/**
		 * Filters the live update response data.
		 *
		 * @filter `gk/foundation/notices/ajax/live-response`
		 *
		 * @since  1.3.0
		 *
		 * @param array                 $response Response data.
		 * @param StoredNoticeInterface $notice   The notice being updated.
		 */
		return apply_filters( 'gk/foundation/notices/ajax/live-response', $response, $notice );
	}
}
