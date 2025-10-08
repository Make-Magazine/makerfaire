<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Notices;

use GravityKit\GravityEdit\Foundation\Helpers\Users;

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
			'dismiss' => [ $this, 'dismiss' ],
			'snooze'  => [ $this, 'snooze' ],
			'live'    => [ $this, 'live' ],
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
				throw NoticeException::validation( __( 'Missing "id" parameter', 'gk-gravityedit' ) );
			}

			$notice_ids = [ (string) $notice_id ];
		}

		$errors = [];

		foreach ( $notice_ids as $notice_id ) {
			try {
				$this->repository->dismiss_for_user( $user_id, $notice_id );

				/**
				 * Fires after a notice has been dismissed via AJAX.
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
			throw NoticeException::validation( __( 'Missing "id" or invalid "in" parameter.', 'gk-gravityedit' ) );
		}

		$user_id = Users::current_id();

		if ( ! $user_id ) {
			throw NoticeException::forbidden( 'Not logged in' );
		}

		$snooze_until = time() + $seconds;

		$this->repository->snooze_for_user( $user_id, $notice_id, $snooze_until );

		/**
		 * Fires after a notice has been snoozed via AJAX.
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
			throw NoticeException::validation( __( 'Missing "id" parameter', 'gk-gravityedit' ) );
		}

		$notice = $this->manager->get_notice( $notice_id );

		if ( ! $notice instanceof StoredNoticeInterface ) {
			throw NoticeException::not_found( 'Notice not found' );
		}

		// Capability guard: ensure current user is allowed to see the notice.
		if ( ! $this->manager->get_evaluator()->check_capabilities( $notice ) ) {
			throw NoticeException::forbidden( __( 'Insufficient permissions.', 'gk-gravityedit' ) );
		}

		$live = method_exists( $notice, 'get_live_config' ) ? $notice->get_live_config() : null;

		if ( ! is_array( $live ) || empty( $live['callback'] ) || ! is_callable( $live['callback'] ) ) {
			throw NoticeException::validation( __( 'Invalid configuration.', 'gk-gravityedit' ) );
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
