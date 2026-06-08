<?php
/**
 * Headless job query service.
 *
 * Fetches job data without depending on WP_List_Table or $_GET superglobals.
 * Returns data in JobSerializer-compatible format for both the AJAX controller
 * and WP-CLI commands.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Overview;

use ActionScheduler;
use ActionScheduler_Store;
use Exception;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\Scheduler\Models\AbstractAction;
use GravityKit\GravityView\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;

/**
 * Queries and serializes scheduler job data.
 *
 * @since 1.12.0
 */
class JobQueryService {

	/**
	 * Database store instance.
	 *
	 * @since 1.12.0
	 *
	 * @var DbStore
	 */
	private $store;

	/**
	 * Job serializer instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobSerializer
	 */
	private $serializer;

	/**
	 * Constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param DbStore       $store      Database store instance.
	 * @param JobSerializer $serializer Job serializer instance.
	 */
	public function __construct( DbStore $store, JobSerializer $serializer ) {
		$this->store      = $store;
		$this->serializer = $serializer;
	}

	/**
	 * Returns a paginated list of serialized jobs.
	 *
	 * Queries the ActionScheduler store directly without depending on
	 * $_GET superglobals or WP_List_Table.
	 *
	 * @since 1.12.0
	 *
	 * @param array $filters {
	 *     Optional. Query filters.
	 *
	 *     @type string $status   Job status filter. Default empty (all).
	 *     @type string $hook     Hook name filter. Default empty (all).
	 *     @type int    $per_page Results per page. Default 20.
	 *     @type int    $page     Page number. Default 1.
	 *     @type string $orderby  Sort field: 'activity' or 'schedule'. Default 'activity'.
	 *     @type string $order    Sort direction: 'asc' or 'desc'. Default 'desc'.
	 *     @type string $search           Search query. Default empty.
	 *     @type string $product          Comma-separated product text domains. Default empty (all).
	 *     @type bool   $include_products Whether to include products list in response. Default false.
	 * }
	 *
	 * @return array{jobs: array, filters: array, pagination: array, health: array, products?: array}
	 */
	public function list( array $filters = [] ): array {
		$now = time();

		$status           = $filters['status'] ?? '';
		$per_page         = (int) ( $filters['per_page'] ?? 20 );
		$page             = max( 1, (int) ( $filters['page'] ?? 1 ) );
		$orderby          = strtolower( $filters['orderby'] ?? 'activity' );
		$order            = strtolower( $filters['order'] ?? 'desc' );
		$search           = $filters['search'] ?? '';
		$product          = $filters['product'] ?? '';
		$include_products = ! empty( $filters['include_products'] );

		// "scheduled" is a synthetic status — AS only knows "pending".
		$is_scheduled_filter = 'scheduled' === $status;
		$is_pending_filter   = 'pending' === $status;
		$query_status        = $is_scheduled_filter ? 'pending' : $status;

		// Map "in-progress" to AS's internal "in-progress" status.
		if ( 'in-progress' === $query_status ) {
			$query_status = ActionScheduler_Store::STATUS_RUNNING;
		}

		$query = [
			'per_page' => $per_page,
			'offset'   => ( $page - 1 ) * $per_page,
			'group'    => DbStore::GROUP_ID,
			'orderby'  => 'date',
			'order'    => 'DESC',
		];

		if ( $query_status ) {
			$query['status'] = $query_status;
		}

		if ( $search ) {
			$query['search'] = $search;
		}

		if ( ! empty( $filters['hook'] ) ) {
			$query['hook'] = $filters['hook'];
		}

		if ( $product ) {
			$filtered    = $this->query_with_product_filter( $query, $product, $search );
			$total_items = $filtered['total'];
			$action_ids  = $filtered['ids'];
		} else {
			$total_items = (int) $this->store->query_actions( $query, 'count' );
			$action_ids  = (array) $this->store->query_actions( $query );
		}

		// AS search doesn't match by action_id. If the query is numeric, try a direct ID lookup.
		if ( $search && is_numeric( $search ) && ! in_array( (int) $search, array_map( 'intval', $action_ids ), true ) ) {
			try {
				$action = $this->store->fetch_action( (int) $search );
			} catch ( \Exception $e ) {
				$action = null;
			}

			if ( $action && ! is_a( $action, 'ActionScheduler_NullAction' ) && $action->get_group() === DbStore::GROUP_ID ) {
				// Verify the action matches the product filter before injecting it.
				if ( ! $product || self::action_matches_product( $action, $product ) ) {
					array_unshift( $action_ids, (int) $search );
					++$total_items;
				}
			}
		}

		$status_labels = $this->store->get_status_labels();
		$logger        = ActionScheduler::logger(); // @phpstan-ignore method.nonObject

		$jobs = [];

		foreach ( $action_ids as $action_id ) {
			$row = $this->build_row( (int) $action_id, $status_labels, $logger );

			if ( ! $row ) {
				continue;
			}

			$row = $this->serializer->enrich_row_with_ran_at( $row, $this->store );

			$serialized = $this->serializer->serialize_job( $row );

			// Filter by synthetic status when pending/scheduled filter is active.
			$is_past_due = 'scheduled' === $serialized['status']
				&& ! empty( $serialized['schedule']['timestamp'] )
				&& $serialized['schedule']['timestamp'] <= $now;

			$effective_status = $is_past_due ? 'pending' : $serialized['status'];

			if ( $is_scheduled_filter && 'scheduled' !== $effective_status ) {
				continue;
			}

			if ( $is_pending_filter && 'scheduled' === $effective_status ) {
				continue;
			}

			$jobs[] = $serialized;
		}

		usort( $jobs, self::build_sort_comparator( $orderby, $order ) );

		$filter_counts = $product
			? $this->count_actions_by_product( $product )
			: $this->store->action_counts();

		// Adjust pagination when PHP-side filtering removed rows.
		// Use the split counts (SQL-based) for the correct total, not count($jobs)
		// which only reflects the current page.
		if ( $is_scheduled_filter || $is_pending_filter ) {
			$split_counts = self::split_pending_scheduled( $filter_counts, $now, $product );
			$total_items  = $split_counts[ $status ] ?? 0;
		}

		$total_pages = $per_page > 0 ? (int) ceil( $total_items / $per_page ) : 1;

		$result = [
			'jobs'       => $jobs,
			'filters'    => self::split_pending_scheduled( $filter_counts, $now, $product ),
			'pagination' => [
				'total'        => $total_items,
				'per_page'     => $per_page,
				'current_page' => $page,
				'total_pages'  => $total_pages,
			],
			'health'     => $this->health(),
		];

		if ( $include_products ) {
			$result['products'] = $this->collect_products();
		}

		return $result;
	}

	/**
	 * Returns serialized data for a single job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return array|null Serialized job data, or null if not found.
	 */
	public function get( int $id ): ?array {
		$status_labels = $this->store->get_status_labels();
		$logger        = ActionScheduler::logger();

		$row = $this->build_row( $id, $status_labels, $logger );

		if ( ! $row ) {
			return null;
		}

		$row = $this->serializer->enrich_row_with_ran_at( $row, $this->store );

		return $this->serializer->serialize_job( $row );
	}

	/**
	 * Returns health and diagnostics data.
	 *
	 * Bypasses the transient cache so results are always fresh.
	 *
	 * @since 1.12.0
	 *
	 * @return array{has_failure: bool, failure_code: string|null, message: string|null, docs_url: string|null}
	 */
	public function health(): array {
		return HealthCheck::run()->to_array();
	}

	/**
	 * Returns the distinct products that have scheduled GK jobs.
	 *
	 * Results are cached in a 60-second transient to avoid repeated queries.
	 *
	 * @since 1.12.0
	 *
	 * @return array[] Array of {text_domain, name} arrays, sorted by name.
	 */
	public function collect_products(): array {
		$cache_key = 'gk_scheduler_products';
		$cached    = WP::get_transient( $cache_key );

		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		global $wpdb;

		// Extract product values from extended_args (preferred) or args.
		$sql = $wpdb->prepare(
			"SELECT DISTINCT
				CASE
					WHEN a.extended_args IS NOT NULL AND a.extended_args != ''
						THEN TRIM(BOTH '\"' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(a.extended_args, '\"product\":\"', -1), '\"', 1))
					ELSE TRIM(BOTH '\"' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(a.args, '\"product\":\"', -1), '\"', 1))
				END AS product
			FROM {$wpdb->actionscheduler_actions} a
			INNER JOIN {$wpdb->actionscheduler_groups} g
				ON g.group_id = a.group_id AND g.slug = %s
			WHERE (
				(a.extended_args IS NOT NULL AND a.extended_args != '' AND a.extended_args LIKE %s)
				OR (a.args LIKE %s)
			)",
			DbStore::GROUP_ID,
			'%"product":"%',
			'%"product":"%'
		);

		$raw      = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared above.
		$products = [];

		foreach ( array_unique( array_filter( $raw ) ) as $text_domain ) {
			$info = AbstractAction::resolve_product( $text_domain );

			$products[] = [
				'text_domain' => $text_domain,
				'name'        => $info['name'] ?? $text_domain,
			];
		}

		usort(
            $products,
            function ( $a, $b ) {
				return strcasecmp( $a['name'], $b['name'] );
			}
        );

		WP::set_transient( $cache_key, $products, 60 );

		return $products;
	}

	/**
	 * Returns full diagnostics data including health.
	 *
	 * @since 1.12.0
	 *
	 * @return array{diagnostics: array, health: array}
	 */
	public function diagnostics(): array {
		HealthCheck::flush();

		return [
			'diagnostics' => $this->serializer->serialize_diagnostics( Diagnostics::collect() ),
			'health'      => $this->health(),
		];
	}

	/**
	 * Builds a SQL WHERE clause fragment for product filtering.
	 *
	 * Handles the dual-column (args/extended_args) LIKE pattern used throughout
	 * the query service to match product text domains in serialized action args.
	 *
	 * @since 1.12.0
	 *
	 * @param string $product Comma-separated product text domains.
	 *
	 * @return string SQL WHERE fragment (parenthesized), or empty string if no products.
	 */
	public static function build_product_where_clause( string $product ): string {
		if ( ! $product ) {
			return '';
		}

		global $wpdb;

		$product_domains = array_filter( array_map( 'trim', explode( ',', $product ) ) );

		if ( empty( $product_domains ) ) {
			return '';
		}

		$product_clauses = [];

		foreach ( $product_domains as $domain ) {
			$like = '%' . $wpdb->esc_like( '"product":"' . $domain . '"' ) . '%';

			$product_clauses[] = $wpdb->prepare(
				"((a.extended_args IS NULL OR a.extended_args = '') AND a.args LIKE %s) OR a.extended_args LIKE %s",
				$like,
				$like
			);
		}

		return '(' . implode( ' OR ', $product_clauses ) . ')';
	}

	/**
	 * Queries action IDs with product filter using direct SQL.
	 *
	 * Bypasses AS's query_actions() to add dual-column LIKE clauses
	 * for product filtering. Follows the same pattern as
	 * query_ids_by_synthetic_status().
	 *
	 * @since 1.12.0
	 *
	 * @param array  $query   Base query params (status, group, search, per_page, offset).
	 * @param string $product Comma-separated product text domains.
	 * @param string $search  Search string.
	 *
	 * @return array{ids: int[], total: int}
	 */
	private function query_with_product_filter( array $query, string $product, string $search ): array {
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

		$product_clause = self::build_product_where_clause( $product );

		if ( $product_clause ) {
			$conditions[] = $product_clause;
		}

		$where  = implode( ' AND ', $conditions );
		$offset = (int) ( $query['offset'] ?? 0 );
		$limit  = (int) ( $query['per_page'] ?? 20 );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$total = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->actionscheduler_actions} a INNER JOIN {$wpdb->actionscheduler_groups} g ON g.group_id = a.group_id WHERE {$where}",
				$values
			)
		);

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT a.action_id FROM {$wpdb->actionscheduler_actions} a INNER JOIN {$wpdb->actionscheduler_groups} g ON g.group_id = a.group_id WHERE {$where} ORDER BY a.scheduled_date_gmt DESC, a.action_id DESC LIMIT {$limit} OFFSET {$offset}",
				$values
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare

		return [
			'ids'   => array_map( 'intval', $ids ?: [] ),
			'total' => $total,
		];
	}

	/**
	 * Counts actions grouped by status, filtered to a specific product.
	 *
	 * Returns the same format as DbStore::action_counts() but scoped to actions
	 * whose args/extended_args contain the given product text domain(s).
	 *
	 * @since 1.12.0
	 *
	 * @param string $product Comma-separated product text domains.
	 *
	 * @return array<string, int> Status => count map.
	 */
	private function count_actions_by_product( string $product ): array {
		$product_clause = self::build_product_where_clause( $product );

		if ( ! $product_clause ) {
			return $this->store->action_counts();
		}

		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $product_clause built from $wpdb->prepare() calls.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.status, COUNT(*) AS cnt
				FROM {$wpdb->actionscheduler_actions} a
				INNER JOIN {$wpdb->actionscheduler_groups} g
					ON g.group_id = a.group_id AND g.slug = %s
				WHERE {$product_clause}
				GROUP BY a.status",
				DbStore::GROUP_ID
			),
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$labels = $this->store->get_status_labels();
		$counts = [];

		foreach ( $rows as $row ) {
			if ( array_key_exists( $row['status'], $labels ) ) {
				$counts[ $row['status'] ] = (int) $row['cnt'];
			}
		}

		return $counts;
	}

	/**
	 * Checks whether an action's args contain one of the given product text domains.
	 *
	 * @since 1.12.0
	 *
	 * @param \ActionScheduler_Action $action  The action to check.
	 * @param string                  $product Comma-separated product text domains.
	 *
	 * @return bool
	 */
	private static function action_matches_product( $action, string $product ): bool {
		$args           = $action->get_args();
		$action_product = $args['product'] ?? '';

		if ( ! $action_product ) {
			return false;
		}

		$product_domains = array_filter( array_map( 'trim', explode( ',', $product ) ) );

		return in_array( $action_product, $product_domains, true );
	}

	/**
	 * Builds a row array from an action ID for consumption by JobSerializer.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $action_id    The action ID.
	 * @param array  $status_labels Status key => label map.
	 * @param object $logger       ActionScheduler logger instance.
	 *
	 * @return array|null Row data, or null if the action is invalid.
	 */
	private function build_row( int $action_id, array $status_labels, $logger ): ?array {
		try {
			$action = $this->store->fetch_action( $action_id );
		} catch ( Exception $e ) {
			return null;
		}

		if ( ! $action || is_a( $action, 'ActionScheduler_NullAction' ) ) { // @phpstan-ignore booleanNot.alwaysFalse
			return null;
		}

		$status_name = $this->store->get_status( $action_id );

		return [
			'ID'          => $action_id,
			'hook'        => $action->get_hook(),
			'status_name' => $status_name,
			'status'      => $status_labels[ $status_name ] ?? $status_name,
			'args'        => $action->get_args(),
			'group'       => $action->get_group(),
			'log_entries' => $logger->get_logs( $action_id ), // @phpstan-ignore method.notFound
			'claim_id'    => $this->store->get_claim_id( $action_id ),
			'recurrence'  => $this->get_recurrence_label( $action ),
			'schedule'    => $action->get_schedule(),
		];
	}

	/**
	 * Returns the recurrence label for an action.
	 *
	 * @since 1.12.0
	 *
	 * @param \ActionScheduler_Action $action The ActionScheduler action object.
	 *
	 * @return string
	 */
	private function get_recurrence_label( $action ): string {
		$schedule = $action->get_schedule();

		if ( method_exists( $schedule, 'get_recurrence' ) ) {
			return (string) $schedule->get_recurrence();
		}

		return __( 'Non-repeating', 'gk-gravityview' );
	}

	/**
	 * Builds a usort comparator for job lists.
	 *
	 * "activity" groups by status (running task -> in-progress -> scheduled -> pending -> terminal),
	 * then by timestamp within each group. "schedule" sorts purely by timestamp.
	 *
	 * @since 1.12.0
	 *
	 * @param string $orderby "activity" or "schedule".
	 * @param string $order   "asc" or "desc".
	 *
	 * @return callable
	 */
	public static function build_sort_comparator( string $orderby, string $order ): callable {
		$timestamp_cmp = static function ( $a, $b ) use ( $order ) {
			$ts_cmp = ( $a['schedule']['timestamp'] ?? 0 ) <=> ( $b['schedule']['timestamp'] ?? 0 );

			if ( 0 !== $ts_cmp ) {
				return 'asc' === $order ? $ts_cmp : -$ts_cmp;
			}

			// Same timestamp: higher ID = newer.
			// Composite IDs ("blog_id:action_id") need numeric extraction to avoid lexicographic comparison.
			$a_id   = self::extract_sort_id( $a['id'] ?? 0 );
			$b_id   = self::extract_sort_id( $b['id'] ?? 0 );
			$id_cmp = $a_id <=> $b_id;

			return 'asc' === $order ? $id_cmp : -$id_cmp;
		};

		if ( 'schedule' === $orderby ) {
			return $timestamp_cmp;
		}

		// "activity" sort: group by status, then timestamp within each group.
		$status_priority = [
			'in-progress' => 0,
			'scheduled'   => 1,
			'pending'     => 2,
			'complete'    => 3,
			'failed'      => 3,
			'canceled'    => 3,
		];

		return static function ( $a, $b ) use ( $status_priority, $timestamp_cmp ) {
			$a_priority = $status_priority[ $a['status'] ] ?? 4;
			$b_priority = $status_priority[ $b['status'] ] ?? 4;

			if ( $a_priority !== $b_priority ) {
				return $a_priority - $b_priority;
			}

			// Within in-progress, jobs with a running task come first.
			if ( 0 === $a_priority ) {
				$a_running = self::has_running_task( $a );
				$b_running = self::has_running_task( $b );

				if ( $a_running !== $b_running ) {
					return $a_running ? -1 : 1;
				}
			}

			return $timestamp_cmp( $a, $b );
		};
	}

	/**
	 * Checks whether a serialized job has a task with status "running".
	 *
	 * @since 1.12.0
	 *
	 * @param array $job Serialized job data.
	 *
	 * @return bool
	 */
	private static function has_running_task( array $job ): bool {
		foreach ( $job['tasks'] ?? [] as $task ) {
			if ( 'running' === ( $task['status'] ?? '' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Extracts a numeric sort key from a job ID.
	 *
	 * For composite IDs ("blog_id:action_id"), returns the action_id portion.
	 * For plain integer IDs, returns the value directly.
	 *
	 * @since 1.12.0
	 *
	 * @param int|string $id Job ID (plain or composite).
	 *
	 * @return int Numeric sort key.
	 */
	private static function extract_sort_id( $id ): int {
		if ( is_int( $id ) ) {
			return $id;
		}

		$pos = strpos( (string) $id, ':' );

		if ( false !== $pos ) {
			return (int) substr( (string) $id, $pos + 1 );
		}

		return (int) $id;
	}

	/**
	 * Splits the AS "pending" count into "pending" (async/immediate) and "scheduled" (future/recurring).
	 *
	 * @since 1.12.0
	 *
	 * @param array<string, int> $counts  Raw status counts from DbStore::action_counts().
	 * @param int|null           $now     Unix timestamp to use as "now". Default: current time.
	 * @param string             $product Comma-separated product text domains. Default empty (all).
	 *
	 * @return array<string, int>
	 */
	public static function split_pending_scheduled( array $counts, ?int $now = null, string $product = '' ): array {
		if ( ! empty( $counts['pending'] ) ) {
			$scheduled_count     = self::count_scheduled_pending( $now, $product );
			$counts['scheduled'] = $scheduled_count;
			$counts['pending']   = max( 0, (int) $counts['pending'] - $scheduled_count );

			if ( 0 === $counts['pending'] ) {
				unset( $counts['pending'] );
			}
		}

		return $counts;
	}

	/**
	 * Queries action IDs matching a synthetic status ("scheduled" or "pending").
	 *
	 * "Scheduled" = pending + future date + real schedule.
	 * "Pending" = pending + past-due or async (null schedule).
	 *
	 * @since 1.12.0
	 *
	 * @param string $status  "scheduled" or "pending".
	 * @param string $search  Optional search string.
	 * @param string $product Comma-separated product text domains. Default empty (all).
	 *
	 * @return int[] Matching action IDs.
	 */
	public static function query_ids_by_synthetic_status( string $status, string $search = '', string $product = '' ): array {
		global $wpdb;

		$like_pattern = '%' . $wpdb->esc_like( 'ActionScheduler_NullSchedule' ) . '%';
		$now_str      = gmdate( 'Y-m-d H:i:s' );

		$conditions = [
			'a.status = %s',
			'g.slug = %s',
		];
		$values     = [ 'pending', DbStore::GROUP_ID ];

		if ( 'scheduled' === $status ) {
			$conditions[] = 'a.scheduled_date_gmt > %s';
			$conditions[] = 'a.schedule NOT LIKE %s';
			$conditions[] = "a.schedule != ''";
			$values[]     = $now_str;
			$values[]     = $like_pattern;
		} elseif ( 'pending' === $status ) {
			$conditions[] = "(a.scheduled_date_gmt <= %s OR a.schedule LIKE %s OR a.schedule = '')";
			$values[]     = $now_str;
			$values[]     = $like_pattern;
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

		$product_clause = self::build_product_where_clause( $product );

		if ( $product_clause ) {
			$conditions[] = $product_clause;
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
	 * Counts pending actions that have a real schedule (not async/null).
	 *
	 * @since 1.12.0
	 *
	 * @param int|null $now     Unix timestamp to use as "now". Default: current time.
	 * @param string   $product Comma-separated product text domains. Default empty (all).
	 *
	 * @return int Number of scheduled pending actions.
	 */
	private static function count_scheduled_pending( ?int $now = null, string $product = '' ): int {
		global $wpdb;

		$like_pattern = '%' . $wpdb->esc_like( 'ActionScheduler_NullSchedule' ) . '%';

		$extra_where    = '';
		$product_clause = self::build_product_where_clause( $product );

		if ( $product_clause ) {
			$extra_where = " AND {$product_clause}";
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $extra_where built from $wpdb->prepare() calls via build_product_where_clause().
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->actionscheduler_actions} a
				INNER JOIN {$wpdb->actionscheduler_groups} g
					ON g.group_id = a.group_id AND g.slug = %s
				WHERE a.status = 'pending'
					AND a.scheduled_date_gmt > %s
					AND a.schedule NOT LIKE %s
					AND a.schedule != ''{$extra_where}",
				DbStore::GROUP_ID,
				gmdate( 'Y-m-d H:i:s', $now ?? time() ),
				$like_pattern
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
