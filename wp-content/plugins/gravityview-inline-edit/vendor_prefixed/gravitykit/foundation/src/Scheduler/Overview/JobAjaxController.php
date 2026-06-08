<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Scheduler\Overview;

use Exception;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityEdit\Foundation\Helpers\WP;
use GravityKit\GravityEdit\Foundation\Scheduler\JobScheduler;
use GravityKit\GravityEdit\Foundation\Scheduler\Store\DbStore;

/**
 * Ajax controller that wires AjaxRouter routes to the Background Jobs data layer.
 *
 * @since 1.12.0
 */
final class JobAjaxController {

	/**
	 * Router slug used by frontend JS when sending Ajax requests.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	public const AJAX_ROUTER = 'background_jobs';

	/**
	 * Allowed single-job actions.
	 *
	 * @since 1.12.0
	 *
	 * @var string[]
	 */
	private const ALLOWED_ACTIONS = [ 'run_now', 'run_reschedule', 'retry', 'pause', 'unpause', 'cancel', 'delete' ];

	/**
	 * Allowed bulk actions.
	 *
	 * @since 1.12.0
	 *
	 * @var string[]
	 */
	private const ALLOWED_BULK_ACTIONS = [ 'delete', 'cancel' ];

	/**
	 * JobSerializer instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobSerializer
	 */
	private $serializer;

	/**
	 * DbStore instance.
	 *
	 * @since 1.12.0
	 *
	 * @var DbStore|null
	 */
	private $store;

	/**
	 * JobActionService instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobActionService|null
	 */
	private $action_service;

	/**
	 * Class constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param JobSerializer         $serializer     JobSerializer instance.
	 * @param DbStore|null          $store          DbStore instance. Default: null (uses singleton).
	 * @param JobActionService|null $action_service JobActionService instance. Default: null (lazy-initialized).
	 */
	public function __construct( JobSerializer $serializer, ?DbStore $store = null, ?JobActionService $action_service = null ) {
		$this->serializer     = $serializer;
		$this->store          = $store;
		$this->action_service = $action_service;

		add_filter(
			'gk/foundation/ajax/' . self::AJAX_ROUTER . '/routes',
			[ $this, 'routes' ]
		);

		// Bust the product list cache when a new job is scheduled.
		add_action( 'gk/foundation/scheduler/job/schedule/after', [ $this, 'invalidate_product_cache' ] );
	}

	/**
	 * Invalidates the cached product list transient.
	 *
	 * Hooked into job schedule and used after bulk/single actions to keep
	 * the product filter dropdown in sync.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function invalidate_product_cache(): void {
		WP::delete_transient( 'gk_scheduler_products' );

		if ( is_multisite() ) {
			WP::delete_site_transient( 'gk_scheduler_products_network' );
		}
	}

	/**
	 * Throws if the current user lacks permission to manage background jobs.
	 *
	 * @since 1.12.0
	 *
	 * @throws Exception When the user does not have the manage_options capability.
	 *
	 * @return void
	 */
	private function verify_capability(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			throw new Exception( esc_html__( 'You do not have permission to perform this action.', 'gk-foundation' ) );
		}
	}

	/**
	 * Checks whether the current request is a network admin context.
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @return bool
	 */
	private function is_network_request( array $payload ): bool {
		return ! empty( $payload['is_network'] ) && is_multisite() && CoreHelpers::is_network_admin();
	}

	/**
	 * Verifies the user has network-level capabilities.
	 *
	 * @since 1.12.0
	 *
	 * @throws Exception When the user does not have the manage_network capability.
	 *
	 * @return void
	 */
	private function verify_network_capability(): void {
		if ( ! current_user_can( 'manage_network' ) ) {
			throw new Exception( esc_html__( 'You do not have permission to perform this action.', 'gk-foundation' ) );
		}
	}

	/**
	 * Returns Ajax route map consumed by the Foundation's AjaxRouter.
	 *
	 * @since 1.12.0
	 *
	 * @param array<string, callable> $routes Existing routes.
	 *
	 * @return array<string, callable>
	 */
	public function routes( array $routes ): array {
		return $routes + [
			'get_jobs'    => [ $this, 'get_jobs' ],
			'job_action'  => [ $this, 'job_action' ],
			'bulk_action' => [ $this, 'bulk_action' ],
			'diagnostics' => [ $this, 'diagnostics' ],
		];
	}

	/**
	 * Returns paginated jobs with status counts and health.
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload with optional status, per_page, paged, s, orderby, order keys.
	 *
	 * @return array{jobs: array, filters: array, health: array}
	 */
	public function get_jobs( array $payload ): array {
		if ( $this->is_network_request( $payload ) ) {
			return $this->get_network_jobs( $payload );
		}

		$this->verify_capability();

		$service = new JobQueryService( $this->get_store(), $this->serializer );

		return $service->list(
			[
				'status'           => $payload['status'] ?? '',
				'per_page'         => (int) ( $payload['per_page'] ?? 20 ),
				'page'             => max( 1, (int) ( $payload['paged'] ?? 1 ) ),
				'orderby'          => $payload['orderby'] ?? 'activity',
				'order'            => $payload['order'] ?? 'desc',
				'search'           => $payload['s'] ?? '',
				'product'          => ! empty( $payload['product'] ) ? sanitize_text_field( $payload['product'] ) : '',
				'include_products' => ! empty( $payload['include_products'] ),
			]
		);
	}

	/**
	 * Executes a single job action (run_now, run_reschedule, pause, unpause, cancel, delete).
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload with id and action keys.
	 *
	 * @throws Exception When required parameters are missing or action is invalid.
	 *
	 * @return array{success: bool, filters: array, job?: array|null}
	 */
	public function job_action( array $payload ): array {
		if ( $this->is_network_request( $payload ) ) {
			return $this->network_job_action( $payload );
		}

		$this->verify_capability();

		$id     = (int) ( $payload['id'] ?? 0 );
		$action = $payload['action'] ?? '';

		if ( ! $id ) {
			throw new Exception( esc_html__( 'Missing required parameter: id', 'gk-foundation' ) );
		}

		if ( ! $action ) {
			throw new Exception( esc_html__( 'Missing required parameter: action', 'gk-foundation' ) );
		}

		if ( ! in_array( $action, self::ALLOWED_ACTIONS, true ) ) {
			throw new Exception(
				/* translators: [action]: the invalid action name. */
				strtr( esc_html__( 'Invalid action: [action]', 'gk-foundation' ), [ '[action]' => $action ] )
			);
		}

		$success = $this->execute_action( $id, $action );

		if ( 'delete' === $action ) {
			$this->invalidate_product_cache();
		}

		$result = [
			'success' => $success,
			'filters' => $this->get_status_counts(),
		];

		// For non-delete actions, return updated job data.
		if ( 'delete' !== $action ) {
			$service       = new JobQueryService( $this->get_store(), $this->serializer );
			$result['job'] = $service->get( $id );
		}

		return $result;
	}

	/**
	 * Executes an action on multiple jobs.
	 *
	 * Supports two modes:
	 * - Explicit IDs: `{ ids: [1, 2, 3], action: "delete" }`
	 * - All matching: `{ all_matching: true, action: "delete", status: "failed" }`
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload with ids (array) and action keys.
	 *
	 * @throws Exception When required parameters are missing or action is invalid.
	 *
	 * @return array{success: bool, processed: int, failed: int, filters: array}
	 */
	public function bulk_action( array $payload ): array {
		if ( $this->is_network_request( $payload ) ) {
			return $this->network_bulk_action( $payload );
		}

		$this->verify_capability();

		$action       = $payload['action'] ?? '';
		$all_matching = ! empty( $payload['all_matching'] );

		if ( ! $action ) {
			throw new Exception( esc_html__( 'Missing required parameter: action', 'gk-foundation' ) );
		}

		if ( ! in_array( $action, self::ALLOWED_BULK_ACTIONS, true ) ) {
			throw new Exception(
				/* translators: [action]: the invalid action name. */
				strtr( esc_html__( 'Invalid action: [action]', 'gk-foundation' ), [ '[action]' => $action ] )
			);
		}

		if ( $all_matching ) {
			$ids = $this->query_all_matching_ids( $payload );
		} else {
			$ids = $payload['ids'] ?? null;

			if ( ! is_array( $ids ) || empty( $ids ) ) {
				throw new Exception( esc_html__( 'Missing required parameter: ids', 'gk-foundation' ) );
			}
		}

		$processed = 0;
		$failed    = 0;

		foreach ( $ids as $id ) {
			$success = $this->execute_action( (int) $id, $action );

			if ( $success ) {
				++$processed;
			} else {
				++$failed;
			}
		}
		$this->invalidate_product_cache();

		return [
			'success'   => 0 === $failed,
			'processed' => $processed,
			'failed'    => $failed,
			'filters'   => $this->get_status_counts(),
		];
	}

	/**
	 * Queries all job IDs matching the given filter criteria.
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Payload with optional status and s (search) keys.
	 *
	 * @return int[] Matching action IDs.
	 */
	private function query_all_matching_ids( array $payload ): array {
		$status  = $payload['status'] ?? '';
		$search  = ! empty( $payload['s'] ) ? sanitize_text_field( $payload['s'] ) : '';
		$product = ! empty( $payload['product'] ) ? sanitize_text_field( $payload['product'] ) : '';

		// Synthetic statuses require a date-aware SQL query to distinguish
		// "scheduled" (future date + real schedule) from "pending" (past-due/async).
		if ( 'scheduled' === $status || 'pending' === $status ) {
			return JobQueryService::query_ids_by_synthetic_status( $status, $search, $product );
		}

		$query = [
			'per_page' => -1,
			'group'    => DbStore::GROUP_ID,
		];

		if ( 'in-progress' === $status ) {
			$query['status'] = \ActionScheduler_Store::STATUS_RUNNING;
		} elseif ( $status ) {
			$query['status'] = $status;
		}

		if ( $search ) {
			$query['search'] = $search;
		}

		if ( $product ) {
			return $this->query_ids_with_product( $query, $product, $search );
		}

		$ids = $this->get_store()->query_actions( $query );

		return is_array( $ids ) ? array_map( 'intval', $ids ) : [];
	}

	/**
	 * Queries all matching action IDs with product filter.
	 *
	 * @since 1.12.0
	 *
	 * @param array  $query   Base query params.
	 * @param string $product Comma-separated product text domains.
	 * @param string $search  Search string.
	 *
	 * @return int[]
	 */
	private function query_ids_with_product( array $query, string $product, string $search ): array {
		global $wpdb;

		$conditions = [ 'g.slug = %s' ];
		$values     = [ DbStore::GROUP_ID ];

		if ( ! empty( $query['status'] ) ) {
			$conditions[] = 'a.status = %s';
			$values[]     = $query['status'];
		}

		if ( $search ) {
			if ( is_numeric( $search ) ) {
				$conditions[] = 'a.action_id = %d';
				$values[]     = (int) $search;
			} else {
				$conditions[] = 'a.hook LIKE %s';
				$values[]     = '%' . $wpdb->esc_like( $search ) . '%';
			}
		}

		$product_domains = array_filter( array_map( 'trim', explode( ',', $product ) ) );
		$product_clauses = [];

		foreach ( $product_domains as $domain ) {
			$like = '%' . $wpdb->esc_like( '"product":"' . $domain . '"' ) . '%';

			$product_clauses[] = $wpdb->prepare(
				"((a.extended_args IS NULL OR a.extended_args = '') AND a.args LIKE %s) OR a.extended_args LIKE %s",
				$like,
				$like
			);
		}

		if ( ! empty( $product_clauses ) ) {
			$conditions[] = '(' . implode( ' OR ', $product_clauses ) . ')';
		}

		$where = implode( ' AND ', $conditions );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $where built from prepared placeholders; values array has matching count.
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT a.action_id FROM {$wpdb->actionscheduler_actions} a INNER JOIN {$wpdb->actionscheduler_groups} g ON g.group_id = a.group_id WHERE {$where}",
				$values
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare

		return array_map( 'intval', $ids ?: [] );
	}

	/**
	 * Returns system diagnostics and health status.
	 *
	 * @since 1.12.0
	 *
	 * @return array{diagnostics: array, health: array}
	 */
	public function diagnostics(): array {
		$this->verify_capability();

		$service = new JobQueryService( $this->get_store(), $this->serializer );

		return $service->diagnostics();
	}

	/**
	 * Executes a single action on a job by delegating to the action service.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $id     The job ID.
	 * @param string $action The action to execute.
	 *
	 * @return bool Whether the action succeeded.
	 */
	private function execute_action( int $id, string $action ): bool {
		try {
			$service = $this->get_action_service();

			switch ( $action ) {
				case 'run_now':
					$service->run_now( $id );
					break;
				case 'run_reschedule':
					$service->run_reschedule( $id );
					break;
				case 'retry':
					$service->retry( $id );
					break;
				case 'pause':
					$service->pause( $id );
					break;
				case 'unpause':
					$service->unpause( $id );
					break;
				case 'cancel':
					$service->cancel( $id );
					break;
				case 'delete':
					$service->delete( $id );
					break;
				default:
					return false;
			}

			return true;
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * Returns status filter counts from the store.
	 *
	 * @since 1.12.0
	 *
	 * @param int|null $now Unix timestamp to use as "now". Default: current time.
	 *
	 * @return array<string, int>
	 */
	private function get_status_counts( ?int $now = null ): array {
		return JobQueryService::split_pending_scheduled( $this->get_store()->action_counts(), $now );
	}

	/**
	 * Returns the JobActionService instance.
	 *
	 * @since 1.12.0
	 *
	 * @return JobActionService
	 */
	private function get_action_service(): JobActionService {
		if ( null === $this->action_service ) {
			$this->action_service = new JobActionService(
				JobScheduler::get_instance()->manager(),
				$this->get_store()
			);
		}

		return $this->action_service;
	}

	/**
	 * Returns the DbStore instance.
	 *
	 * @since 1.12.0
	 *
	 * @return DbStore
	 */
	private function get_store(): DbStore {
		if ( null === $this->store ) {
			$this->store = DbStore::get_instance();
		}

		return $this->store;
	}

	/**
	 * Returns paginated jobs across all network sites.
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @return array{jobs: array, filters: array, pagination: array, health: array}
	 */
	private function get_network_jobs( array $payload ): array {
		$this->verify_network_capability();

		$network_service = new NetworkJobQueryService( $this->serializer );

		return $network_service->list( $payload );
	}

	/**
	 * Executes a single job action in network admin context.
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload with id (composite) and action keys.
	 *
	 * @throws Exception When required parameters are missing or action is invalid.
	 *
	 * @return array{success: bool, filters: array, job?: array|null}
	 */
	private function network_job_action( array $payload ): array {
		$this->verify_network_capability();

		$composite_id = $payload['id'] ?? '';
		$action       = $payload['action'] ?? '';

		if ( ! $composite_id ) {
			throw new Exception( esc_html__( 'Missing required parameter: id', 'gk-foundation' ) );
		}

		if ( ! $action ) {
			throw new Exception( esc_html__( 'Missing required parameter: action', 'gk-foundation' ) );
		}

		if ( ! in_array( $action, self::ALLOWED_ACTIONS, true ) ) {
			throw new Exception(
				strtr( esc_html__( 'Invalid action: [action]', 'gk-foundation' ), [ '[action]' => $action ] )
			);
		}

		$network_action_service = new NetworkJobActionService();
		$network_query_service  = new NetworkJobQueryService( $this->serializer );

		try {
			$success = $network_action_service->execute( (string) $composite_id, $action );
		} catch ( \Throwable $e ) {
			$success = false;
		}

		$result = [
			'success' => $success,
			'filters' => $network_query_service->network_status_counts(),
		];

		if ( 'delete' !== $action ) {
			$result['job'] = $network_action_service->reload_job( (string) $composite_id );
		}

		return $result;
	}

	/**
	 * Executes a bulk action in network admin context.
	 *
	 * @since 1.12.0
	 *
	 * @param array $payload Ajax payload.
	 *
	 * @throws Exception When required parameters are missing or action is invalid.
	 *
	 * @return array{success: bool, processed: int, failed: int, filters: array}
	 */
	private function network_bulk_action( array $payload ): array {
		$this->verify_network_capability();

		$action       = $payload['action'] ?? '';
		$all_matching = ! empty( $payload['all_matching'] );

		if ( ! $action ) {
			throw new Exception( esc_html__( 'Missing required parameter: action', 'gk-foundation' ) );
		}

		if ( ! in_array( $action, self::ALLOWED_BULK_ACTIONS, true ) ) {
			throw new Exception(
				strtr( esc_html__( 'Invalid action: [action]', 'gk-foundation' ), [ '[action]' => $action ] )
			);
		}

		$network_action_service = new NetworkJobActionService();
		$network_query_service  = new NetworkJobQueryService( $this->serializer );

		if ( $all_matching ) {
			$ids = $network_query_service->query_all_matching_ids(
				$payload['status'] ?? '',
				! empty( $payload['s'] ) ? sanitize_text_field( $payload['s'] ) : '',
				(int) ( $payload['site_id'] ?? 0 ),
				! empty( $payload['product'] ) ? sanitize_text_field( $payload['product'] ) : ''
			);
		} else {
			$ids = $payload['ids'] ?? null;

			if ( ! is_array( $ids ) || empty( $ids ) ) {
				throw new Exception( esc_html__( 'Missing required parameter: ids', 'gk-foundation' ) );
			}
		}

		$processed = 0;
		$failed    = 0;

		foreach ( $ids as $composite_id ) {
			try {
				$success = $network_action_service->execute( (string) $composite_id, $action );

				if ( $success ) {
					++$processed;
				} else {
					++$failed;
				}
			} catch ( \Throwable $e ) {
				++$failed;
			}
		}

		$this->invalidate_product_cache();

		return [
			'success'   => 0 === $failed,
			'processed' => $processed,
			'failed'    => $failed,
			'filters'   => $network_query_service->network_status_counts(),
		];
	}
}
