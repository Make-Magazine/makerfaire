<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 10-October-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

use GravityKit\GravityView\Foundation\Logger\Framework as Logger;
use GravityKit\GravityView\Foundation\State\StateManagerFactory;
use GravityKit\GravityView\Foundation\Helpers\Users;
use GravityKit\GravityView\Foundation\Exceptions\UserException;
use GravityKit\GravityView\Foundation\Exceptions\BaseException;

/**
 * Repository responsible for interacting with low-level persistence layer.
 *
 * @since 1.3.0
 */
final class NoticeRepository {
	/**
	 * Name of the site-level option holding persisted notices.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const OPTION_PERSISTED = 'gk_notices';

	/**
	 * Meta key that stores user state (dismissed, snoozed, …) for notices.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const USER_META_STATE_KEY = 'actions';

	/**
	 * Meta key that stores user-scoped notice definitions.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const USER_META_DEFS_KEY = 'defs';

	/**
	 * State manager factory instance.
	 *
	 * @since 1.3.0
	 *
	 * @var StateManagerFactory
	 */
	private $state_factory;

	/**
	 * Global state manager for notices.
	 *
	 * @since 1.3.0
	 *
	 * @var \GravityKit\GravityView\Foundation\State\GlobalStateManager
	 */
	private $global_state_manager;

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param StateManagerFactory|null $state_factory (optional) State manager factory instance.
	 */
	public function __construct( ?StateManagerFactory $state_factory = null ) {
		$this->state_factory        = $state_factory ?: new StateManagerFactory();
		$this->global_state_manager = $this->state_factory->make_global( self::OPTION_PERSISTED );
	}

	/**
	 * Persists/overwrites a stored notice definition in the appropriate storage (global option or user-scoped meta).
	 *
	 * @since 1.3.0
	 *
	 * @param StoredNoticeInterface $notice Notice instance to persist.
	 *
	 * @throws NoticeException When persistence fails.
	 *
	 * @return void
	 */
	public function persist( StoredNoticeInterface $notice ): void {
		if ( 'user' === $notice->get_scope() ) {
			$this->persist_user( $notice );
		} else {
			$this->persist_global( $notice );
		}
	}

	/**
	 * Persists a global notice to the options table.
	 *
	 * @since 1.3.0
	 *
	 * @param StoredNoticeInterface $notice Notice instance to persist.
	 *
	 * @throws NoticeException When persistence fails.
	 *
	 * @return void
	 */
	private function persist_global( StoredNoticeInterface $notice ): void {
		$notice_id = $notice->get_id();

		try {
			$this->global_state_manager->add( $notice_id, $notice->as_definition() );
		} catch ( BaseException $e ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'notice_id' => $notice_id,
					'error'     => $e->getMessage(),
				]
			);
		}

		/**
		 * Fires after a notice has been persisted to storage.
		 *
		 * @action `gk/foundation/notices/saved`
		 *
		 * @since  1.3.0
		 *
		 * @param StoredNoticeInterface $notice The notice that was persisted.
		 */
		do_action( 'gk/foundation/notices/saved', $notice );
	}

	/**
	 * Persists a user-scoped notice to user meta for specific users.
	 *
	 * @since 1.3.0
	 *
	 * @param StoredNoticeInterface $notice Notice instance to persist.
	 *
	 * @throws NoticeException When persistence fails.
	 *
	 * @return void
	 */
	private function persist_user( StoredNoticeInterface $notice ): void {
		$notice_id = $notice->get_id();
		$users     = $notice->get_users();

		// Parse includes and excludes.
		$includes = [];
		$excludes = [];

		/** @var array<int|string> $users */
		foreach ( $users as $user ) {
			if ( is_string( $user ) && strpos( $user, 'not:' ) === 0 ) {
				$excludes[] = (int) substr( $user, 4 );
			} else {
				$includes[] = (int) $user;
			}
		}

		// If only excludes, we need to store for all users except excluded.
		// This requires a different approach - store as global with exclusion list.
		if ( ! empty( $excludes ) && empty( $includes ) ) {
			$this->persist_global_with_exclusions( $notice, $excludes );

			return;
		}

		// If no users specified after parsing, use current user.
		if ( empty( $includes ) ) {
			$user_id = Users::current_id();

			if ( $user_id ) {
				$includes = [ $user_id ];
			}
		}

		// Store for included users, excluding any in the exclude list.
		foreach ( $includes as $user_id ) {
			if ( in_array( $user_id, $excludes, true ) ) {
				continue; // Skip excluded users.
			}

			$user = Users::get( $user_id );

			if ( $user instanceof UserException ) {
				continue;
			}

			$usm                = $this->state_factory->make_user( $user, self::OPTION_PERSISTED );
			$defs               = (array) $usm->get( self::USER_META_DEFS_KEY );
			$defs[ $notice_id ] = $notice->as_definition();

			try {
				$usm->add( self::USER_META_DEFS_KEY, $defs );
			} catch ( BaseException $e ) {
				throw NoticeException::persistence(
					__METHOD__,
					[
						'notice_id' => $notice_id,
						'user_id'   => $user_id,
						'error'     => $e->getMessage(),
					]
				);
			}
		}

		/**
		 * Fires after a user-scoped notice has been persisted to storage.
		 *
		 * @action `gk/foundation/notices/saved`
		 *
		 * @since  1.3.0
		 *
		 * @param StoredNoticeInterface $notice The notice that was persisted.
		 */
		do_action( 'gk/foundation/notices/saved', $notice );
	}

	/**
	 * Persists a user notice with only exclusions as a global notice with excluded_users.
	 *
	 * @since 1.3.0
	 *
	 * @param StoredNoticeInterface $notice   Notice instance to persist.
	 * @param array<int>            $excludes Array of user IDs to exclude.
	 *
	 * @throws NoticeException When persistence fails.
	 *
	 * @return void
	 */
	private function persist_global_with_exclusions( StoredNoticeInterface $notice, array $excludes ): void {
		$notice_id = $notice->get_id();

		// Convert to global notice with user exclusion condition.
		$definition                   = $notice->as_definition();
		$definition['scope']          = 'global';
		$definition['excluded_users'] = $excludes;

		unset( $definition['users'] );

		try {
			$this->global_state_manager->add( $notice_id, $definition );
		} catch ( BaseException $e ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'notice_id' => $notice_id,
					'error'     => $e->getMessage(),
				]
			);
		}

		/**
		 * Fires after a notice has been persisted to storage.
		 *
		 * @action `gk/foundation/notices/saved`
		 *
		 * @since  1.3.0
		 *
		 * @param StoredNoticeInterface $notice The notice that was persisted.
		 */
		do_action( 'gk/foundation/notices/saved', $notice );
	}

	/**
	 * Removes a stored notice from storage (global or user-scoped).
	 *
	 * @since 1.3.0
	 * @since 1.4.0 Enhanced to handle user-scoped notices.
	 *
	 * @param string $notice_id Notice ID.
	 *
	 * @throws NoticeException When removal fails.
	 *
	 * @return void
	 */
	public function remove( string $notice_id ): void {
		$removed_from_global = false;
		$removed_from_users  = false;

		// Try to remove from global storage.
		$all = $this->global_state_manager->all();

		if ( isset( $all[ $notice_id ] ) ) {
			try {
				$this->global_state_manager->remove( $notice_id );

				$removed_from_global = true;
			} catch ( BaseException $e ) {
				throw NoticeException::persistence(
					__METHOD__,
					[
						'notice_id' => $notice_id,
						'error'     => $e->getMessage(),
						'context'   => 'global',
					]
				);
			}
		}

		// Also remove from all users' storage if it's a user-scoped notice.
		// This ensures complete removal even if the notice was stored per-user.
		$users = get_users( [ 'fields' => 'ID' ] );

		foreach ( $users as $user_id ) {
			$user = Users::get( $user_id );

			if ( $user instanceof UserException ) {
				continue;
			}

			$user_meta = $this->state_factory->make_user( $user, self::OPTION_PERSISTED );
			$defs      = (array) $user_meta->get( self::USER_META_DEFS_KEY );

			if ( isset( $defs[ $notice_id ] ) ) {
				unset( $defs[ $notice_id ] );

				try {
					$user_meta->add( self::USER_META_DEFS_KEY, $defs );

					$removed_from_users = true;
				} catch ( BaseException $e ) {
					Logger::get_instance()->error( "Failed to remove notice '{$notice_id}' from user ID #{$user_id}: {$e->getMessage()}" );
				}
			}
		}

		// Only fire the action if something was actually removed.
		if ( $removed_from_global || $removed_from_users ) {
			/**
			 * Fires after a notice has been removed from storage.
			 *
			 * @action `gk/foundation/notices/removed`
			 *
			 * @since  1.3.0
			 *
			 * @param string $notice_id The ID of the notice that was removed.
			 */
			do_action( 'gk/foundation/notices/removed', $notice_id );
		}
	}

	/**
	 * Returns all currently stored notices.
	 *
	 * @since 1.3.0
	 *
	 * @return StoredNoticeInterface[]
	 */
	public function get_all_stored(): array {
		$global_defs = $this->global_state_manager->all();

		// Retrieve per-user definitions.
		$user_defs    = [];
		$current_user = Users::get();

		if ( ! $current_user instanceof UserException ) {
			$user_state = $this->state_factory->make_user( $current_user, self::OPTION_PERSISTED );
			$user_defs  = (array) $user_state->get( self::USER_META_DEFS_KEY );
		}

		// Merge – user-scoped definitions override global ones when same key exists.
		$merged = array_replace( $global_defs, $user_defs );

		$list = [];

		foreach ( $merged as $payload ) {
			if ( is_array( $payload ) ) {
				$list[] = StoredNotice::create( $payload );
			}
		}

		return $list;
	}

	/**
	 * Returns the user state array for the given user.
	 *
	 * @since 1.3.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array<string,mixed>
	 */
	public function get_user_state( int $user_id ): array {
		$user = Users::get( $user_id );

		if ( $user instanceof UserException ) {
			return [];
		}

		$user_meta  = $this->state_factory->make_user( $user, self::OPTION_PERSISTED );
		$user_state = (array) $user_meta->get( self::USER_META_STATE_KEY );

		return $user_state;
	}

	/**
	 * Applies changes to the user-specific state.
	 *
	 * @since 1.3.0
	 *
	 * @param int   $user_id User ID.
	 * @param array $changes Associative array noticeKey => newState.
	 *
	 * @throws NoticeException When user state update fails.
	 *
	 * @return void
	 */
	public function update_user_state( int $user_id, array $changes ): void {
		$user = Users::get( $user_id );

		if ( $user instanceof UserException ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'uid'       => $user_id,
					'exception' => $user->to_array(),
				]
			);
		}

		$user_meta = $this->state_factory->make_user( $user, self::OPTION_PERSISTED );

		/**
		 * Filters user state changes before they are saved.
		 *
		 * @filter `gk/foundation/notices/user-state`
		 *
		 * @since  1.3.0
		 *
		 * @param array $changes State changes to apply.
		 * @param int   $user_id User ID.
		 */
		$changes = apply_filters( 'gk/foundation/notices/user-state', $changes, $user_id );

		if ( ! is_array( $changes ) ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'uid'    => $user_id,
					'reason' => 'Invalid changes format - expected array',
				]
			);
		}

		$state_before = $user_meta->get( self::USER_META_STATE_KEY );

		if ( ! is_array( $state_before ) ) {
			$state_before = [];
		}

		$state_after = array_merge( $state_before, $changes );

		// No-op – state already contained desired values.
		if ( $state_after === $state_before ) {
			return;
		}

		try {
			$user_meta->add( self::USER_META_STATE_KEY, $state_after );
		} catch ( BaseException $e ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'user_id' => $user_id,
					'error'   => $e->getMessage(),
				]
			);
		}
	}

	/**
	 * Deletes a user-scoped notice definition for the given user, if present.
	 *
	 * @since 1.3.0
	 *
	 * @param int    $user_id   User ID.
	 * @param string $notice_id Notice ID.
	 *
	 * @throws NoticeException When user notice definition deletion fails.
	 *
	 * @return void
	 */
	public function delete_user_notice_def( int $user_id, string $notice_id ): void {
		$user = Users::get( $user_id );

		if ( $user instanceof UserException ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'uid' => $user_id,
					'id'  => $notice_id,
				]
			);
		}

		$user_meta = $this->state_factory->make_user( $user, self::OPTION_PERSISTED );

		$defs = (array) $user_meta->get( self::USER_META_DEFS_KEY );

		if ( ! isset( $defs[ $notice_id ] ) ) {
			return;
		}

		unset( $defs[ $notice_id ] );

		try {
			$user_meta->add( self::USER_META_DEFS_KEY, $defs );
		} catch ( BaseException $e ) {
			throw NoticeException::persistence(
				__METHOD__,
				[
					'user_id'   => $user_id,
					'notice_id' => $notice_id,
					'error'     => $e->getMessage(),
				]
			);
		}
	}

	/**
	 * Dismisses a notice for the given user.
	 *
	 * @since 1.3.0
	 *
	 * @param int    $user_id   User ID.
	 * @param string $notice_id Notice ID.
	 *
	 * @throws NoticeException When dismissal fails.
	 *
	 * @return void
	 */
	public function dismiss_for_user( int $user_id, string $notice_id ): void {
		$this->delete_user_notice_def( $user_id, $notice_id );
		$user_state  = $this->get_user_state( $user_id );
		$already_set = isset( $user_state[ $notice_id ]['dismissed'] ) && true === $user_state[ $notice_id ]['dismissed'];

		// For runtime notices, we allow dismissal even if the notice definition
		// is not found in storage. We only check if it's already dismissed.
		if ( $already_set ) {
			return;
		}

		// Update state to mark as dismissed (works for both stored and runtime notices).
		$this->update_user_state( $user_id, [ $notice_id => [ 'dismissed' => true ] ] );
	}

	/**
	 * Snoozes a notice for the user until the provided timestamp.
	 *
	 * @since 1.3.0
	 *
	 * @param int    $user_id   User ID.
	 * @param string $notice_id Notice ID.
	 * @param int    $until     Unix timestamp until which the notice is snoozed.
	 *
	 * @throws NoticeException When snoozing fails.
	 *
	 * @return void
	 */
	public function snooze_for_user( int $user_id, string $notice_id, int $until ): void {
		$user = Users::get( $user_id );

		if ( $user instanceof UserException ) {
			throw NoticeException::persistence( __METHOD__, [ 'uid' => $user_id ] );
		}

		// For runtime notices, we still need to track snooze state even though
		// the notice definition itself is not persisted. This allows runtime
		// notices to be snoozed just like stored notices.
		$this->update_user_state( $user_id, [ $notice_id => [ 'snoozed_until' => $until ] ] );
	}
}
