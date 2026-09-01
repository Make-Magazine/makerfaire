<?php

namespace GravityKit\GravityView\Foundation\Notices;

use GravityKit\GravityView\Foundation\Logger\Framework as Logger;
use GravityKit\GravityView\Foundation\Helpers\Users;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Exceptions\UserException;
use GravityKit\GravityView\Foundation\State\UserStateManager;
use Throwable;

/**
 * Public facade for interacting with the Notices framework.
 *
 * @since 1.3.0
 */
final class NoticeManager {
	/**
	 * Holds the singleton instance.
	 *
	 * @since 1.3.0
	 *
	 * @var NoticeManager|null
	 */
	private static $instance;

	/**
	 * Runtime notices added during the current request.
	 *
	 * @since 1.3.0
	 *
	 * @var RuntimeNoticeInterface[]
	 */
	private $runtime_notices = [];

	/**
	 * Cached stored notices.
	 *
	 * @since 1.3.0
	 *
	 * @var StoredNoticeInterface[]|null
	 */
	private $stored_notices = null;

	/**
	 * Internal repository.
	 *
	 * @since 1.3.0
	 *
	 * @var NoticeRepository
	 */
	private $repository;

	/**
	 * Internal evaluator instance.
	 *
	 * @var NoticeEvaluator|null
	 */
	private $evaluator;

	/**
	 * Notice Factory.
	 *
	 * @since 1.3.0
	 *
	 * @var NoticeFactoryInterface
	 */
	private $factory;


	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 */
	/**
	 * Returns a shared instance.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeRepository|null       $repository Optional repository instance.
	 * @param NoticeFactoryInterface|null $factory    Optional factory instance.
	 *
	 * @return NoticeManager
	 */
	public static function get_instance( ?NoticeRepository $repository = null, ?NoticeFactoryInterface $factory = null ): self {
		if ( ! self::$instance ) {
			$repo           = $repository ?? new NoticeRepository();
			self::$instance = new self( $repo, $factory );
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeRepository            $repository Repository instance.
	 * @param NoticeFactoryInterface|null $factory    Optional factory instance.
	 *
	 * @return void
	 */
	private function __construct( NoticeRepository $repository, ?NoticeFactoryInterface $factory = null ) {
		$this->repository = $repository;
		$this->factory    = $factory ?? new NoticeFactory();

		add_action( 'all_admin_notices', [ $this, 'render_notices' ], 1 );

		new NoticeAjaxController( $this->repository, $this );
	}

	/**
	 * Registers a runtime notice.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @return RuntimeNoticeInterface|null The runtime notice when successfully created; null otherwise.
	 */
	public function add_runtime( array $data ): ?RuntimeNoticeInterface {
		try {
			/**
			 * Filters the notice definition data before creating a runtime notice.
			 *
			 * @filter `gk/foundation/notices/add`
			 *
			 * @since 1.3.0
			 *
			 * @param array  $data Notice definition data.
			 * @param string $type Notice type: 'runtime' or 'stored'.
			 */
			$data = apply_filters( 'gk/foundation/notices/add', $data, 'runtime' );

			$notice = $this->factory->make_runtime( $data );

			$this->runtime_notices[ $notice->get_id() ] = $notice;

			if ( $this->evaluator ) {
				$this->flush_cache();
			}

			return $notice;
		} catch ( Throwable $e ) {
			return null;
		}
	}

	/**
	 * Creates & persists a stored notice.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @return StoredNoticeInterface|null The stored notice when successfully created and persisted; null otherwise.
	 */
	public function add_stored( array $data ): ?StoredNoticeInterface {
		try {
			/**
			 * Filters the notice definition data before creating a stored notice.
			 *
			 * @filter `gk/foundation/notices/add`
			 *
			 * @since 1.3.0
			 *
			 * @param array  $data Notice definition data.
			 * @param string $type Notice type: 'runtime' or 'stored'.
			 */
			$data = apply_filters( 'gk/foundation/notices/add', $data, 'stored' );

			$notice = $this->factory->make_stored( $data );

			$this->repository->persist( $notice );

			if ( null === $this->stored_notices ) {
				$this->stored_notices = [];

				foreach ( $this->repository->get_all_stored() as $notice_item ) {
					$this->stored_notices[ $notice_item->get_id() ] = $notice_item;
				}
			}

			$this->stored_notices[ $notice->get_id() ] = $notice;

			/**
			 * Fires after a stored notice has been successfully added.
			 *
			 * @action `gk/foundation/notices/added`
			 *
			 * @since 1.3.0
			 *
			 * @param NoticeInterface $notice The notice that was added.
			 * @param string          $type   Notice type: 'runtime' or 'stored'.
			 */
			do_action( 'gk/foundation/notices/added', $notice, 'stored' );

			return $notice;
		} catch ( Throwable $e ) {
			return null;
		}
	}

	/**
	 * Returns the active notices for the current request/user.
	 *
	 * @since 1.3.0
	 *
	 * @param string|null $context Optional admin context to filter by: 'ms_network', 'ms_main', 'ms_subsite', 'site', or 'user'.
	 *
	 * @return NoticeInterface[]
	 */
	public function get_active( ?string $context = null ): array {
		// Clean up expired notices before evaluation.
		$this->cleanup_expired_notices();

		$current_user = Users::get();
		$user_id      = $current_user instanceof UserException ? 0 : $current_user->ID;
		$user_state   = $user_id ? $this->repository->get_user_state( $user_id ) : [];

		try {
			$notices = $this->get_evaluator()->evaluate( $context, $user_state );
		} catch ( NoticeException $e ) {
			Logger::get_instance()->error( 'Notice evaluation failed: ' . $e->get_error_message(), [ 'error' => $e ] );

			return [];
		}

		$notices = $this->sort_notices( $notices );

		/**
		 * Filters the active notices after evaluation and sorting.
		 *
		 * @filter `gk/foundation/notices/active`
		 *
		 * @since 1.3.0
		 *
		 * @param NoticeInterface[] $notices Array of active notices.
		 */
		return apply_filters( 'gk/foundation/notices/active', $notices );
	}

	/**
	 * Retrieves a single notice by its ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $notice_id Notice ID.
	 *
	 * @return NoticeInterface|null Notice instance when found; null otherwise.
	 */
	public function get_notice( string $notice_id ): ?NoticeInterface {
		if ( isset( $this->runtime_notices[ $notice_id ] ) ) {
			return $this->runtime_notices[ $notice_id ];
		}

		if ( null === $this->stored_notices ) {
			$this->stored_notices = [];

			foreach ( $this->repository->get_all_stored() as $notice ) {
				$this->stored_notices[ $notice->get_id() ] = $notice;
			}
		}

		return $this->stored_notices[ $notice_id ] ?? null;
	}

	/**
	 * Returns all stored notices whose slug starts with the given prefix.
	 *
	 * @since 1.13.0
	 *
	 * @param string $slug_prefix Slug prefix to match (e.g., 'server-').
	 *
	 * @return StoredNoticeInterface[] Keyed by notice ID.
	 */
	public function get_stored_by_slug_prefix( string $slug_prefix ): array {
		if ( null === $this->stored_notices ) {
			$this->stored_notices = [];

			foreach ( $this->repository->get_all_stored() as $notice ) {
				$this->stored_notices[ $notice->get_id() ] = $notice;
			}
		}

		$matches = [];

		foreach ( $this->stored_notices as $id => $notice ) {
			if ( str_starts_with( $notice->get_slug(), $slug_prefix ) ) {
				$matches[ $id ] = $notice;
			}
		}

		return $matches;
	}

	/**
	 * Returns stored notices across all scopes whose slug starts with a prefix.
	 *
	 * @since TBD
	 *
	 * @param string $slug_prefix Slug prefix to match.
	 *
	 * @return StoredNoticeInterface[] Keyed by notice ID.
	 */
	public function get_stored_by_slug_prefix_all_scopes( string $slug_prefix ): array {
		$matches = [];

		foreach ( $this->repository->get_all_stored_across_scopes() as $notice ) {
			if ( str_starts_with( $notice->get_slug(), $slug_prefix ) ) {
				$matches[ $notice->get_id() ] = $notice;
			}
		}

		return $matches;
	}

	/**
	 * Removes a stored notice from persistent storage.
	 *
	 * Deletes the notice from both global options and all user meta where it may
	 * be stored. Also clears the internal cache so the notice is no longer returned
	 * by get_active() or get_notice().
	 *
	 * @since 1.12.0
	 *
	 * @param string $notice_id Notice ID (namespace/slug).
	 *
	 * @throws NoticeException When removal fails.
	 *
	 * @return void
	 */
	public function remove( string $notice_id ): void {
		$this->repository->remove( $notice_id );

		$this->flush_cache();
	}

	/**
	 * Updates a persisted stored notice definition with partial changes.
	 * Runtime notices cannot be updated as they live only in memory.
	 *
	 * @since 1.3.0
	 * @since TBD Find user-scoped notices outside the current user context.
	 * @since TBD Accept an already-resolved stored notice.
	 *
	 * @param string                     $notice_id Notice ID.
	 * @param array<string,mixed>        $changes   Partial definition data to merge.
	 * @param StoredNoticeInterface|null $notice    Already-resolved stored notice.
	 *
	 * @throws NoticeException When notice update fails.
	 *
	 * @return void
	 */
	public function update_notice( string $notice_id, array $changes, ?StoredNoticeInterface $notice = null ): void {
		if ( ! $notice || $notice_id !== $notice->get_id() ) {
			$notice = $this->get_notice( $notice_id );
		}

		if ( ! $notice instanceof StoredNoticeInterface ) {
			foreach ( $this->repository->get_all_stored_across_scopes() as $stored_notice ) {
				if ( $notice_id === $stored_notice->get_id() ) {
					$notice = $stored_notice;
					break;
				}
			}
		}

		if ( ! $notice instanceof StoredNoticeInterface ) {
			throw NoticeException::persistence( __METHOD__, [ 'notice_id' => $notice_id ] );
		}

		$updated_def = array_merge( $notice->as_definition(), $changes );

		/**
		 * Filters the updated notice definition before saving.
		 *
		 * @filter `gk/foundation/notices/update`
		 *
		 * @since 1.3.0
		 *
		 * @param array  $updated_def Updated notice definition.
		 * @param array  $changes     Changes that were applied.
		 * @param string $notice_id   Notice ID being updated.
		 */
		$updated_def = apply_filters( 'gk/foundation/notices/update', $updated_def, $changes, $notice_id );

		$this->repository->persist( StoredNotice::create( $updated_def ) );

		// Clear evaluator cache so updates reflect immediately.
		$this->flush_cache();
	}

	/**
	 * Returns the evaluator instance, creating it if necessary.
	 *
	 * @since 1.3.0
	 *
	 * @return NoticeEvaluator
	 */
	public function get_evaluator(): NoticeEvaluator {
		if ( $this->evaluator ) {
			return $this->evaluator;
		}

		if ( null === $this->stored_notices ) {
			$this->stored_notices = [];

			foreach ( $this->repository->get_all_stored() as $notice ) {
				$this->stored_notices[ $notice->get_id() ] = $notice;
			}
		}

		$all_notices = array_merge(
			array_values( $this->stored_notices ),
			array_values( $this->runtime_notices )
		);

		$this->evaluator = new NoticeEvaluator( $all_notices );

		return $this->evaluator;
	}

	/**
	 * Clears cached notices and evaluator so get_active() reflects recently added or modified notices.
	 *
	 * @since 1.3.0
	 */
	public function flush_cache(): void {
		$this->evaluator      = null;
		$this->stored_notices = null;
	}

	/**
	 * Cleans up expired notices from both global and user-scoped storage.
	 * This method handles the removal logic that was previously in NoticeEvaluator.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function cleanup_expired_notices(): void {
		try {
			$this->cleanup_global_expired_notices();
			$this->cleanup_user_expired_notices();
		} catch ( Throwable $e ) {
			Logger::get_instance()->error( 'Expired notice cleanup failed: ' . $e->getMessage(), [ 'trace' => $e->getTraceAsString() ] );
		}
	}

	/**
	 * Cleans up expired notices from global storage.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	private function cleanup_global_expired_notices(): void {
		$global_notices = get_option( NoticeRepository::OPTION_PERSISTED, [] );
		$cleanup_needed = false;

		foreach ( $global_notices as $notice_id => $definition ) {
			if ( ! is_array( $definition ) ) {
				continue;
			}

			// Create a temporary StoredNotice instance to check expiration.
			$notice = StoredNotice::create( $definition );

			if ( $notice->is_expired() ) {
				unset( $global_notices[ $notice_id ] );

				$cleanup_needed = true;

				Logger::get_instance()->debug( 'Removed expired global notice', [ 'notice_id' => $notice_id ] );
			}
		}

		if ( $cleanup_needed ) {
			update_option( NoticeRepository::OPTION_PERSISTED, $global_notices, false );

			$this->flush_cache(); // Clear cache so changes reflect immediately.
		}
	}

	/**
	 * Cleans up expired notices from user-scoped storage for the current user.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	private function cleanup_user_expired_notices(): void {
		$current_user = Users::get();

		if ( $current_user instanceof UserException ) {
			return; // No user logged in, nothing to clean up.
		}

		$user_meta      = new UserStateManager( $current_user, 'gk_notices' );
		$user_notices   = (array) $user_meta->get( 'defs' );
		$cleanup_needed = false;

		foreach ( $user_notices as $notice_id => $definition ) {
			if ( ! is_array( $definition ) ) {
				continue;
			}

			// Create a temporary StoredNotice instance to check expiration.
			$notice = StoredNotice::create( $definition );

			if ( $notice->is_expired() ) {
				unset( $user_notices[ $notice_id ] );

				$cleanup_needed = true;

				Logger::get_instance()->debug(
					'Removed expired user notice',
					[
						'notice_id' => $notice_id,
						'user_id'   => $current_user->ID,
					]
				);
			}
		}

		if ( ! $cleanup_needed ) {
			return;
		}

		try {
			$user_meta->add( 'defs', $user_notices );
		} catch ( Throwable $e ) {
			Logger::get_instance()->error( 'Could not update user state', [ 'notices' => $user_notices ] );
		}

		$this->flush_cache(); // Clear cache so changes reflect immediately.
	}

	/**
	 * Determines the current admin context.
	 *
	 * @since 1.3.0
	 *
	 * @return string One of: 'ms_network', 'ms_main', 'ms_subsite', 'site', or 'user'.
	 */
	private function get_current_context(): string {
		// For network admin pages.
		if ( is_network_admin() ) {
			return 'ms_network';
		}

		// For user admin pages.
		if ( is_user_admin() ) {
			return 'user';
		}

		if ( ! is_multisite() ) {
			return 'site';
		}

		if ( CoreHelpers::is_main_network_site() ) {
			return 'ms_main';
		}

		if ( CoreHelpers::is_not_main_network_site() ) {
			return 'ms_subsite';
		}

		// Fallback.
		return 'site';
	}

	/**
	 * Renders active notices for the current admin context.
	 *
	 * This method is hooked to 'in_admin_header' to bypass plugin filtering
	 * that occurs with standard admin notice hooks.
	 *
	 * @since 1.3.0
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function render_notices(): void {
		static $rendered = false;

		if ( $rendered ) {
			return;
		}

		$context = $this->get_current_context();
		$notices = $this->get_active( $context );

		if ( empty( $notices ) ) {
			return;
		}

		/**
		 * Fires before notices are rendered.
		 *
		 * @action `gk/foundation/notices/render/before`
		 *
		 * @since 1.3.0
		 *
		 * @param NoticeInterface[] &$notices Active notices to be rendered (passed by reference).
		 * @param string            $context  Current admin context.
		 */
		do_action_ref_array( 'gk/foundation/notices/render/before', [ &$notices, $context ] );

		foreach ( $notices as $notice ) {
			if ( $notice instanceof StoredNotice ) {
				// Trigger live updates only for notices that weren't added during this request.
				$notice->apply_live_updates( $this->repository );
			}
		}

		try {
			( new NoticeRenderer() )->render( $notices );
		} catch ( NoticeException $e ) {
			Logger::get_instance()->error( 'Failed to render notices', [ 'error' => $e ] );

			return;
		}

		$this->purge_flash_notices( $notices );

		$rendered = true;
	}

	/**
	 * Purges flash notices so they don't appear again.
	 * Applies per-user dismissal or permanently removes stored definitions depending on scope and type.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $notices Array of notices that have just been rendered.
	 *
	 * @return void
	 */
	private function purge_flash_notices( array $notices ): void {
		if ( empty( $notices ) ) {
			return;
		}

		$current_user = Users::get();
		$user_id      = 0;

		if ( ! $current_user instanceof UserException ) {
			$user_id = $current_user->ID;
		}

		foreach ( $notices as $notice ) {
			if ( ! $notice->is_flash() ) {
				continue;
			}

			$notice_id = $notice->get_id();

			if ( $notice instanceof StoredNotice ) {
				try {
					switch ( $notice->get_scope() ) {
						case 'global':
							// Dismiss global flash notices for current user only.
							if ( $user_id ) {
								$this->repository->update_user_state( $user_id, [ $notice_id => [ 'dismissed' => true ] ] );
							}

							break;
						case 'user':
							// Remove user-scoped flash notices from user's storage.
							if ( $user_id ) {
								$this->repository->delete_user_notice_def( $user_id, $notice_id );
							}

							break;
					}
				} catch ( NoticeException $e ) {
					Logger::get_instance()->error( 'Flash notice purge failed: ' . $e->get_error_message(), [ 'notice_id' => $notice_id ] );
				}
			} elseif ( $user_id ) {
				// Dismiss runtime flash notices for current user.
				try {
					$this->repository->update_user_state( $user_id, [ $notice->get_id() => [ 'dismissed' => true ] ] );
				} catch ( NoticeException $e ) {
					Logger::get_instance()->error( 'Runtime flash notice purge failed: ' . $e->get_error_message(), [ 'notice_id' => $notice->get_id() ] );
				}
			}
		}
	}

	/**
	 * Sorts notices by sticky status first, then by severity, text-domain, and ID.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $notices Array of notices to sort.
	 *
	 * @return NoticeInterface[] Sorted array of notices.
	 */
	private function sort_notices( array $notices ): array {
		$sticky     = [];
		$non_sticky = [];

		foreach ( $notices as $notice ) {
			if ( $notice->is_sticky() ) {
				$sticky[] = $notice;
			} else {
				$non_sticky[] = $notice;
			}
		}

		$sort = function ( NoticeInterface $a, NoticeInterface $b ) {
			// Priorities (lower = higher priority).
			$severity_priority = [
				'error'   => 1,
				'warning' => 2,
				'success' => 3,
				'info'    => 4,
			];

			$a_severity_priority = $severity_priority[ $a->get_severity() ] ?? 999;
			$b_severity_priority = $severity_priority[ $b->get_severity() ] ?? 999;

			// 1. Sort by severity.
			if ( $a_severity_priority !== $b_severity_priority ) {
				return $a_severity_priority <=> $b_severity_priority;
			}

			// 2. Sort by text namespace.
			$namespace_comparison = strcmp( $a->get_namespace(), $b->get_namespace() );
			if ( 0 !== $namespace_comparison ) {
				return $namespace_comparison;
			}

			// 3. Sort by ID for stability.
			return strcmp( $a->get_id(), $b->get_id() );
		};

		usort( $sticky, $sort );
		usort( $non_sticky, $sort );

		return array_merge( $sticky, $non_sticky );
	}
}
