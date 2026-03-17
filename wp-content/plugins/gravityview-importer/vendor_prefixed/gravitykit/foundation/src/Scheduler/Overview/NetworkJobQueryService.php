<?php
/**
 * Network-wide job query service.
 *
 * Builds UNION ALL queries across per-site Action Scheduler tables
 * to provide an aggregated view of jobs for the network admin.
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Overview;

use ActionScheduler;
use ActionScheduler_Store;
use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Scheduler\Models\AbstractAction;
use GravityKit\GravityImport\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;

/**
 * Queries and serializes scheduler job data across all network sites.
 *
 * @since 1.12.0
 */
class NetworkJobQueryService {

	/**
	 * Job serializer instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobSerializer
	 */
	private $serializer;

	/**
	 * Per-request cache for network status counts.
	 *
	 * @since 1.12.0
	 *
	 * @var array<string, int>|null
	 */
	private $cached_status_counts;

	/**
	 * Constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param JobSerializer $serializer Job serializer instance.
	 */
	public function __construct( JobSerializer $serializer ) {
		$this->serializer = $serializer;

		self::register_cache_hooks();
	}

	/**
	 * Registers cache invalidation hooks for site creation/deletion.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	private static function register_cache_hooks(): void {
		static $registered = false;

		if ( $registered ) {
			return;
		}

		$registered = true;
		$flush      = static function () {
			wp_cache_delete( 'gk_network_as_sites', 'gk_scheduler' );
		};

		add_action( 'wp_insert_site', $flush );
		add_action( 'wp_delete_site', $flush );
	}

	/**
	 * Returns paginated jobs across all network sites.
	 *
	 * Uses UNION ALL queries for efficient cross-site pagination, then
	 * fetches full row data per site using switch_to_blog.
	 *
	 * @since 1.12.0
	 *
	 * @param array $filters {
	 *     Optional. Query filters.
	 *
	 *     @type string $status   Job status filter. Default empty (all).
	 *     @type int    $per_page Results per page. Default 20.
	 *     @type int    $paged    Page number. Default 1.
	 *     @type string $orderby  Sort field. Default 'activity'.
	 *     @type string $order    Sort direction. Default 'desc'.
	 *     @type string $s                Search query. Default empty.
	 *     @type int    $site_id          Filter to a specific site. Default 0 (all).
	 *     @type string $product          Comma-separated product text domains. Default empty (all).
	 *     @type bool   $include_products Whether to include products list in response. Default false.
	 * }
	 *
	 * @return array{jobs: array, filters: array, pagination: array, health: array, products?: array}
	 */
	public function list( array $filters = [] ): array { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- "list" is the most descriptive name.
		$now              = time();
		$status           = $filters['status'] ?? '';
		$per_page         = (int) ( $filters['per_page'] ?? 20 );
		$page             = max( 1, (int) ( $filters['paged'] ?? 1 ) );
		$orderby          = strtolower( $filters['orderby'] ?? 'activity' );
		$order            = strtolower( $filters['order'] ?? 'desc' );
		$search           = $filters['s'] ?? '';
		$site_id          = (int) ( $filters['site_id'] ?? 0 );
		$product          = $filters['product'] ?? '';
		$include_products = ! empty( $filters['include_products'] );

		// Map synthetic statuses.
		$is_scheduled_filter = 'scheduled' === $status;
		$is_pending_filter   = 'pending' === $status;
		$query_status        = $is_scheduled_filter ? 'pending' : $status;

		if ( 'in-progress' === $query_status ) {
			$query_status = ActionScheduler_Store::STATUS_RUNNING;
		}

		$all_sites = $this->get_sites_with_as_tables();

		// Ignore invalid site_id — fall back to all sites.
		if ( $site_id && ! in_array( $site_id, $all_sites, true ) ) {
			$site_id = 0;
		}

		$sites = $site_id ? [ $site_id ] : $all_sites;

		if ( empty( $sites ) ) {
			return $this->empty_response( $all_sites, $now );
		}

		// Single site selected: switch to it and use the standard query service.
		if ( $site_id ) {
			return $this->query_single_site( $site_id, $filters, $all_sites, $now );
		}

		// Multi-site: UNION ALL for pagination, then fetch full rows per site.
		$offset = ( $page - 1 ) * $per_page;
		$items  = $this->union_query_ids( $sites, $query_status, $search, $order, $per_page, $offset, $product );
		$total  = $this->union_query_count( $sites, $query_status, $search, $product );

		$jobs = $this->fetch_and_serialize( $items, $now, $is_scheduled_filter, $is_pending_filter );

		usort( $jobs, JobQueryService::build_sort_comparator( $orderby, $order ) );

		$filter_counts = $product
			? $this->network_product_status_counts( $all_sites, $product, $now )
			: $this->network_status_counts( $all_sites, $now );

		// Adjust pagination when PHP-side filtering removed rows.
		if ( $is_scheduled_filter || $is_pending_filter ) {
			$total = $filter_counts[ $status ] ?? 0;
		}

		$total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 1;

		$result = [
			'jobs'       => $jobs,
			'filters'    => $filter_counts,
			'pagination' => [
				'total'        => $total,
				'per_page'     => $per_page,
				'current_page' => $page,
				'total_pages'  => $total_pages,
			],
			'health'     => $this->aggregate_health(),
		];

		if ( $include_products ) {
			$result['products'] = $this->collect_products();
		}

		return $result;
	}

	/**
	 * Returns status filter counts aggregated across all network sites.
	 *
	 * Includes the scheduled/pending split applied to the combined counts.
	 *
	 * @since 1.12.0
	 *
	 * @param int[] $site_ids Blog IDs to query. Empty = auto-discover.
	 * @param int   $now      Unix timestamp for "now". Default: current time.
	 *
	 * @return array<string, int>
	 */
	public function network_status_counts( array $site_ids = [], ?int $now = null ): array {
		if ( null !== $this->cached_status_counts ) {
			return $this->cached_status_counts;
		}

		if ( empty( $site_ids ) ) {
			$site_ids = $this->get_sites_with_as_tables();
		}

		if ( empty( $site_ids ) ) {
			return [];
		}

		global $wpdb;

		$sub_selects = [];

		foreach ( $site_ids as $blog_id ) {
			$blog_id = (int) $blog_id;
			$prefix  = $wpdb->get_blog_prefix( $blog_id );
			$a_table = esc_sql( $prefix . 'actionscheduler_actions' );
			$g_table = esc_sql( $prefix . 'actionscheduler_groups' );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names from $wpdb->get_blog_prefix().
			$sub_selects[] = $wpdb->prepare(
				"SELECT a.status, COUNT(*) AS cnt FROM `{$a_table}` a INNER JOIN `{$g_table}` g ON g.group_id = a.group_id AND g.slug = %s GROUP BY a.status",
				DbStore::GROUP_ID
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		$union = implode( ' UNION ALL ', $sub_selects );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- UNION built from individually prepared sub-selects.
		$rows = $wpdb->get_results(
			"SELECT status, SUM(cnt) AS cnt FROM ({$union}) AS combined GROUP BY status",
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

		$counts = [];

		foreach ( $rows ?: [] as $row ) {
			$counts[ (string) $row['status'] ] = (int) $row['cnt'];
		}

		// Apply the scheduled/pending split.
		if ( ! empty( $counts['pending'] ) ) {
			$scheduled_count     = $this->count_network_scheduled_pending( $site_ids, $now );
			$counts['scheduled'] = $scheduled_count;
			$counts['pending']   = max( 0, (int) $counts['pending'] - $scheduled_count );

			if ( 0 === $counts['pending'] ) {
				unset( $counts['pending'] );
			}
		}

		$this->cached_status_counts = $counts;

		return $counts;
	}

	/**
	 * Returns status counts filtered by product, aggregated across network sites.
	 *
	 * @since 1.12.0
	 *
	 * @param int[]  $site_ids Blog IDs to query.
	 * @param string $product  Comma-separated product text domains.
	 * @param int    $now      Unix timestamp for "now". Default: current time.
	 *
	 * @return array<string, int> Status => count map with scheduled/pending split applied.
	 */
	private function network_product_status_counts( array $site_ids, string $product, ?int $now = null ): array {
		$product_domains = array_filter( array_map( 'trim', explode( ',', $product ) ) );

		if ( empty( $product_domains ) || empty( $site_ids ) ) {
			return $this->network_status_counts( $site_ids, $now );
		}

		global $wpdb;

		$sub_selects = [];

		foreach ( $site_ids as $blog_id ) {
			$blog_id = (int) $blog_id;
			$prefix  = $wpdb->get_blog_prefix( $blog_id );
			$a_table = esc_sql( $prefix . 'actionscheduler_actions' );
			$g_table = esc_sql( $prefix . 'actionscheduler_groups' );

			$product_clauses = [];

			foreach ( $product_domains as $domain ) {
				$like = '%' . $wpdb->esc_like( '"product":"' . $domain . '"' ) . '%';

				$product_clauses[] = $wpdb->prepare(
					"((a.extended_args IS NULL OR a.extended_args = '') AND a.args LIKE %s) OR a.extended_args LIKE %s",
					$like,
					$like
				);
			}

			$product_where = '(' . implode( ' OR ', $product_clauses ) . ')';

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names from $wpdb->get_blog_prefix(); product clauses from $wpdb->prepare().
			$sub_selects[] = $wpdb->prepare(
				"SELECT a.status, COUNT(*) AS cnt FROM `{$a_table}` a INNER JOIN `{$g_table}` g ON g.group_id = a.group_id AND g.slug = %s WHERE {$product_where} GROUP BY a.status",
				DbStore::GROUP_ID
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		$union = implode( ' UNION ALL ', $sub_selects );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- UNION built from individually prepared sub-selects.
		$rows = $wpdb->get_results(
			"SELECT status, SUM(cnt) AS cnt FROM ({$union}) AS combined GROUP BY status",
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

		$counts = [];

		foreach ( $rows ?: [] as $row ) {
			$counts[ (string) $row['status'] ] = (int) $row['cnt'];
		}

		// Apply the scheduled/pending split.
		if ( ! empty( $counts['pending'] ) ) {
			$scheduled_count     = $this->count_network_scheduled_pending( $site_ids, $now, $product );
			$counts['scheduled'] = $scheduled_count;
			$counts['pending']   = max( 0, (int) $counts['pending'] - $scheduled_count );

			if ( 0 === $counts['pending'] ) {
				unset( $counts['pending'] );
			}
		}

		return $counts;
	}

	/**
	 * Returns blog IDs that have Action Scheduler tables.
	 *
	 * Cached in the object cache for 5 minutes.
	 *
	 * @since 1.12.0
	 *
	 * @return int[]
	 */
	public function get_sites_with_as_tables(): array {
		$cache_key = 'gk_network_as_sites';
		$cached    = wp_cache_get( $cache_key, 'gk_scheduler' );

		if ( false !== $cached ) {
			return $cached;
		}

		global $wpdb;

		$sites  = get_sites(
			[
				'fields' => 'ids',
				'number' => 0,
			]
		);
		$result = [];

		foreach ( $sites as $site_id ) {
			$prefix        = $wpdb->get_blog_prefix( (int) $site_id );
			$actions_table = $prefix . 'actionscheduler_actions';
			$groups_table  = $prefix . 'actionscheduler_groups';

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names from $wpdb->get_blog_prefix().
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $actions_table ) ) && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $groups_table ) ) ) {
				$result[] = (int) $site_id;
			}
		}

		wp_cache_set( $cache_key, $result, 'gk_scheduler', 300 );

		return $result;
	}

	/**
	 * Returns info for network sites that have Action Scheduler tables.
	 *
	 * @since 1.12.0
	 *
	 * @return array[] Array of {id, name, url} arrays.
	 */
	public function get_all_sites(): array {
		$result = [];

		foreach ( $this->get_sites_with_as_tables() as $site_id ) {
			$result[] = self::get_site_info( (int) $site_id );
		}

		return $result;
	}

	/**
	 * Queries all matching IDs across network sites for bulk "select all" operations.
	 *
	 * @since 1.12.0
	 *
	 * @param string $status  Status filter. Default empty.
	 * @param string $search  Search string. Default empty.
	 * @param int    $site_id Site filter. Default 0 (all).
	 * @param string $product Comma-separated product text domains. Default empty (all).
	 *
	 * @return string[] Composite IDs in "blog_id:action_id" format.
	 */
	public function query_all_matching_ids( string $status = '', string $search = '', int $site_id = 0, string $product = '' ): array {
		$query_status     = $status;
		$synthetic_status = '';

		if ( 'scheduled' === $status || 'pending' === $status ) {
			$synthetic_status = $status;
			$query_status     = 'pending';
		} elseif ( 'in-progress' === $status ) {
			$query_status = ActionScheduler_Store::STATUS_RUNNING;
		}

		$valid_sites = $this->get_sites_with_as_tables();
		$sites       = $site_id ? ( in_array( $site_id, $valid_sites, true ) ? [ $site_id ] : [] ) : $valid_sites;

		if ( empty( $sites ) ) {
			return [];
		}

		$sub_selects = $this->build_sub_selects( $sites, $query_status, $search, $synthetic_status, $product );

		if ( empty( $sub_selects ) ) {
			return [];
		}

		global $wpdb;

		$union = implode( ' UNION ALL ', $sub_selects );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- UNION built from individually prepared sub-selects.
		$rows = $wpdb->get_results(
			"SELECT action_id, blog_id FROM ({$union}) AS combined",
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

		$ids = [];

		foreach ( $rows ?: [] as $row ) {
			$ids[] = $row['blog_id'] . ':' . $row['action_id'];
		}

		return $ids;
	}

	/**
	 * Returns an empty response structure.
	 *
	 * @since 1.12.0
	 *
	 * @param int[] $all_sites All sites with AS tables.
	 * @param int   $now       Unix timestamp.
	 *
	 * @return array{jobs: array, filters: array, pagination: array, health: array}
	 */
	private function empty_response( array $all_sites, int $now ): array {
		return [
			'jobs'       => [],
			'filters'    => $this->network_status_counts( $all_sites, $now ),
			'pagination' => [
				'total'        => 0,
				'per_page'     => 20,
				'current_page' => 1,
				'total_pages'  => 1,
			],
			'health'     => $this->aggregate_health(),
		];
	}

	/**
	 * Queries a single site using the standard JobQueryService.
	 *
	 * @since 1.12.0
	 *
	 * @param int   $site_id   The blog ID.
	 * @param array $filters   Query filters.
	 * @param int[] $all_sites All sites with AS tables (for status counts).
	 * @param int   $now       Unix timestamp.
	 *
	 * @return array{jobs: array, filters: array, pagination: array, health: array}
	 */
	private function query_single_site( int $site_id, array $filters, array $all_sites, int $now ): array {
		// Map AJAX param names to JobQueryService param names.
		if ( isset( $filters['s'] ) ) {
			$filters['search'] = $filters['s'];
		}

		if ( isset( $filters['paged'] ) ) {
			$filters['page'] = $filters['paged'];
		}

		switch_to_blog( $site_id );

		try {
			$store   = DbStore::get_instance();
			$service = new JobQueryService( $store, $this->serializer );
			$result  = $service->list( $filters );

			// Add site info to each job and use composite IDs.
			$site_info = self::get_site_info( $site_id );

			foreach ( $result['jobs'] as &$job ) {
				$job['site'] = $site_info;
				$job['id']   = $site_id . ':' . $job['id'];

				// Remove cross-site unsafe actions.
				$job['actions'] = array_values(
					array_diff( $job['actions'], NetworkJobActionService::UNSAFE_CROSS_SITE_ACTIONS )
				);
			}
		} catch ( \Exception $e ) {
			return $this->empty_response( $all_sites, $now );
		} finally {
			restore_current_blog();
		}

		// Keep site-specific counts so filter tabs match the visible jobs.

		return $result;
	}

	/**
	 * Builds UNION ALL sub-SELECTs for the given sites and conditions.
	 *
	 * Each sub-SELECT joins the per-site actions and groups tables,
	 * filtering by the GK scheduler group slug.
	 *
	 * @since 1.12.0
	 *
	 * @param int[]  $site_ids         Blog IDs to query.
	 * @param string $query_status     AS status filter. Empty = all.
	 * @param string $search           Search string for hook names.
	 * @param string $synthetic_status Optional. "scheduled" or "pending" to add date conditions. Default empty.
	 * @param string $product          Optional. Comma-separated product text domains. Default empty (all).
	 *
	 * @return string[] Array of prepared SQL sub-SELECTs.
	 */
	private function build_sub_selects( array $site_ids, string $query_status, string $search, string $synthetic_status = '', string $product = '' ): array {
		global $wpdb;

		$sub_selects = [];

		foreach ( $site_ids as $blog_id ) {
			$blog_id = (int) $blog_id;
			$prefix  = $wpdb->get_blog_prefix( $blog_id );
			$a_table = esc_sql( $prefix . 'actionscheduler_actions' );
			$g_table = esc_sql( $prefix . 'actionscheduler_groups' );

			$conditions = [ 'g.slug = %s' ];
			$values     = [ DbStore::GROUP_ID ];

			if ( $query_status ) {
				$conditions[] = 'a.status = %s';
				$values[]     = $query_status;
			}

			// Apply date conditions for synthetic scheduled/pending split.
			if ( 'scheduled' === $synthetic_status ) {
				$like_pattern = '%' . $wpdb->esc_like( 'ActionScheduler_NullSchedule' ) . '%';
				$conditions[] = 'a.scheduled_date_gmt > %s';
				$conditions[] = 'a.schedule NOT LIKE %s';
				$conditions[] = "a.schedule != ''";
				$values[]     = gmdate( 'Y-m-d H:i:s' );
				$values[]     = $like_pattern;
			} elseif ( 'pending' === $synthetic_status ) {
				$like_pattern = '%' . $wpdb->esc_like( 'ActionScheduler_NullSchedule' ) . '%';
				$conditions[] = "(a.scheduled_date_gmt <= %s OR a.schedule LIKE %s OR a.schedule = '')";
				$values[]     = gmdate( 'Y-m-d H:i:s' );
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

			if ( $product ) {
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
			}

			$where = implode( ' AND ', $conditions );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- table names from $wpdb->get_blog_prefix(), blog_id is cast to int, placeholders are in $where.
			$sub_selects[] = $wpdb->prepare(
				"SELECT a.action_id, {$blog_id} AS blog_id, a.scheduled_date_gmt FROM `{$a_table}` a INNER JOIN `{$g_table}` g ON g.group_id = a.group_id WHERE {$where}",
				$values
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		}

		return $sub_selects;
	}

	/**
	 * Returns (blog_id, action_id) pairs for the current page via UNION ALL.
	 *
	 * @since 1.12.0
	 *
	 * @param int[]  $site_ids     Blog IDs to query.
	 * @param string $query_status AS status filter.
	 * @param string $search       Search string.
	 * @param string $order        Sort direction: 'asc' or 'desc'.
	 * @param int    $limit        Max rows.
	 * @param int    $offset       Row offset.
	 * @param string $product      Comma-separated product text domains. Default empty (all).
	 *
	 * @return array[] Array of {action_id, blog_id} arrays.
	 */
	private function union_query_ids( array $site_ids, string $query_status, string $search, string $order, int $limit, int $offset, string $product = '' ): array {
		global $wpdb;

		$sub_selects = $this->build_sub_selects( $site_ids, $query_status, $search, '', $product );

		if ( empty( $sub_selects ) ) {
			return [];
		}

		$union     = implode( ' UNION ALL ', $sub_selects );
		$order_dir = 'asc' === $order ? 'ASC' : 'DESC';
		$limit     = absint( $limit );
		$offset    = absint( $offset );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- UNION built from individually prepared sub-selects; limit/offset are absint().
		$results = $wpdb->get_results(
			"SELECT action_id, blog_id FROM ({$union}) AS combined ORDER BY scheduled_date_gmt {$order_dir}, action_id {$order_dir} LIMIT {$limit} OFFSET {$offset}",
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

		return $results ?: [];
	}

	/**
	 * Returns total count of matching actions across all sites via UNION ALL.
	 *
	 * @since 1.12.0
	 *
	 * @param int[]  $site_ids     Blog IDs to query.
	 * @param string $query_status AS status filter.
	 * @param string $search       Search string.
	 * @param string $product      Comma-separated product text domains. Default empty (all).
	 *
	 * @return int
	 */
	private function union_query_count( array $site_ids, string $query_status, string $search, string $product = '' ): int {
		global $wpdb;

		$sub_selects = $this->build_sub_selects( $site_ids, $query_status, $search, '', $product );

		if ( empty( $sub_selects ) ) {
			return 0;
		}

		$union = implode( ' UNION ALL ', $sub_selects );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- UNION built from individually prepared sub-selects.
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM ({$union}) AS combined" );
	}

	/**
	 * Fetches full row data and serializes jobs, grouped by site.
	 *
	 * Groups (blog_id, action_id) pairs by blog_id, then for each site:
	 * switches to it, fetches full action data, serializes, and restores.
	 *
	 * @since 1.12.0
	 *
	 * @param array[] $items               Array of {action_id, blog_id} arrays.
	 * @param int     $now                 Unix timestamp.
	 * @param bool    $is_scheduled_filter Whether the "scheduled" synthetic filter is active.
	 * @param bool    $is_pending_filter   Whether the "pending" filter is active.
	 *
	 * @return array[] Serialized job arrays with site info.
	 */
	private function fetch_and_serialize( array $items, int $now, bool $is_scheduled_filter, bool $is_pending_filter ): array {
		// Group by blog_id.
		$grouped = [];

		foreach ( $items as $item ) {
			$grouped[ (int) $item['blog_id'] ][] = (int) $item['action_id'];
		}

		$jobs = [];

		foreach ( $grouped as $blog_id => $action_ids ) {
			switch_to_blog( $blog_id );

			try {
				$store   = DbStore::get_instance();
				$service = new JobQueryService( $store, $this->serializer );

				$site_info = self::get_site_info( $blog_id );

				foreach ( $action_ids as $action_id ) {
					$serialized = $service->get( $action_id );

					if ( ! $serialized ) {
						continue;
					}

					// Filter by synthetic status.
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

					// Add site info and use composite ID.
					$serialized['site'] = $site_info;
					$serialized['id']   = $blog_id . ':' . $serialized['id'];

					// Remove cross-site unsafe actions.
					$serialized['actions'] = array_values(
						array_diff( $serialized['actions'], NetworkJobActionService::UNSAFE_CROSS_SITE_ACTIONS )
					);

					$jobs[] = $serialized;
				}
			} catch ( \Exception $e ) {
				// Per-site failure: skip this site's jobs rather than aborting the entire response.
				continue;
			} finally {
				restore_current_blog();
			}
		}

		return $jobs;
	}

	/**
	 * Counts pending actions with a real schedule across all network sites.
	 *
	 * Used to split the "pending" count into "pending" and "scheduled" for the UI.
	 *
	 * @since 1.12.0
	 *
	 * @param int[]    $site_ids Blog IDs to query.
	 * @param int|null $now      Unix timestamp. Default: current time.
	 * @param string   $product  Comma-separated product text domains. Default: empty (all products).
	 *
	 * @return int
	 */
	private function count_network_scheduled_pending( array $site_ids, ?int $now = null, string $product = '' ): int {
		global $wpdb;

		$like_pattern = '%' . $wpdb->esc_like( 'ActionScheduler_NullSchedule' ) . '%';
		$date_str     = gmdate( 'Y-m-d H:i:s', $now ?? time() );
		$sub_selects  = [];

		$product_domains = $product ? array_filter( array_map( 'trim', explode( ',', $product ) ) ) : [];

		foreach ( $site_ids as $blog_id ) {
			$blog_id = (int) $blog_id;
			$prefix  = $wpdb->get_blog_prefix( $blog_id );
			$a_table = esc_sql( $prefix . 'actionscheduler_actions' );
			$g_table = esc_sql( $prefix . 'actionscheduler_groups' );

			$product_extra = '';

			if ( ! empty( $product_domains ) ) {
				$product_clauses = [];

				foreach ( $product_domains as $domain ) {
					$like = '%' . $wpdb->esc_like( '"product":"' . $domain . '"' ) . '%';

					$product_clauses[] = $wpdb->prepare(
						"((a.extended_args IS NULL OR a.extended_args = '') AND a.args LIKE %s) OR a.extended_args LIKE %s",
						$like,
						$like
					);
				}

				$product_extra = ' AND (' . implode( ' OR ', $product_clauses ) . ')';
			}

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names from $wpdb->get_blog_prefix(); product clauses from $wpdb->prepare().
			$sub_selects[] = $wpdb->prepare(
				"SELECT COUNT(*) AS cnt FROM `{$a_table}` a INNER JOIN `{$g_table}` g ON g.group_id = a.group_id AND g.slug = %s WHERE a.status = 'pending' AND a.scheduled_date_gmt > %s AND a.schedule NOT LIKE %s AND a.schedule != ''{$product_extra}",
				DbStore::GROUP_ID,
				$date_str,
				$like_pattern
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		if ( empty( $sub_selects ) ) {
			return 0;
		}

		$union = implode( ' UNION ALL ', $sub_selects );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- UNION built from individually prepared sub-selects.
		return (int) $wpdb->get_var( "SELECT SUM(cnt) FROM ({$union}) AS combined" );
	}

	/**
	 * Returns site info for display.
	 *
	 * @since 1.12.0
	 *
	 * @param int $blog_id The blog ID.
	 *
	 * @return array{id: int, name: string, url: string}
	 */
	public static function get_site_info( int $blog_id ): array {
		$details = get_blog_details( $blog_id );

		return [
			'id'   => $blog_id,
			'name' => $details ? $details->blogname : strtr( 'Site #[id]', [ '[id]' => $blog_id ] ),
			'url'  => $details ? untrailingslashit( $details->siteurl ) : '',
		];
	}

	/**
	 * Collects distinct products across all network sites.
	 *
	 * @since 1.12.0
	 *
	 * @return array[] Array of {text_domain, name} arrays.
	 */
	public function collect_products(): array {
		$cache_key = 'gk_scheduler_products_network';
		$cached    = WP::get_site_transient( $cache_key );

		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		global $wpdb;

		$sites = $this->get_sites_with_as_tables();

		if ( empty( $sites ) ) {
			return [];
		}

		$sub_selects = [];

		foreach ( $sites as $blog_id ) {
			$blog_id = (int) $blog_id;
			$prefix  = $wpdb->get_blog_prefix( $blog_id );
			$a_table = esc_sql( $prefix . 'actionscheduler_actions' );
			$g_table = esc_sql( $prefix . 'actionscheduler_groups' );

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names from $wpdb->get_blog_prefix().
			$sub_selects[] = $wpdb->prepare(
				"SELECT DISTINCT
					CASE
						WHEN a.extended_args IS NOT NULL AND a.extended_args != ''
							THEN TRIM(BOTH '\"' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(a.extended_args, '\"product\":\"', -1), '\"', 1))
						ELSE TRIM(BOTH '\"' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(a.args, '\"product\":\"', -1), '\"', 1))
					END AS product
				FROM `{$a_table}` a
				INNER JOIN `{$g_table}` g ON g.group_id = a.group_id AND g.slug = %s
				WHERE (a.extended_args LIKE %s OR a.args LIKE %s)",
				DbStore::GROUP_ID,
				'%"product":"%',
				'%"product":"%'
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		$union = implode( ' UNION ', $sub_selects );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- each sub-select is prepared above.
		$raw = $wpdb->get_col( "SELECT DISTINCT product FROM ({$union}) AS combined" );

		$products = [];

		foreach ( array_unique( array_filter( $raw ?: [] ) ) as $text_domain ) {
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

		WP::set_site_transient( $cache_key, $products, 60 );

		return $products;
	}

	/**
	 * Returns health status from the main site's perspective.
	 *
	 * Loopback and WP-Cron configuration are server-wide, so the main
	 * site's health check is representative of all network sites.
	 *
	 * @since 1.12.0
	 *
	 * @return array{has_failure: bool, failure_code: string|null, message: string|null, docs_url: string|null}
	 */
	private function aggregate_health(): array {
		return HealthCheck::run()->to_array();
	}
}
