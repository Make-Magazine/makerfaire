<?php
/**
 * Network-wide job action service.
 *
 * Wraps mutations with switch_to_blog context switching for multisite.
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Overview;

use Exception;
use GravityKit\GravityImport\Foundation\Scheduler\JobScheduler;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;

/**
 * Executes job actions across network sites using blog context switching.
 *
 * @since 1.12.0
 */
class NetworkJobActionService {

	/**
	 * Actions that are unsafe to execute cross-site.
	 *
	 * These require hook callbacks to be registered in the target blog context,
	 * which only happens during that site's own request lifecycle.
	 *
	 * @since 1.12.0
	 *
	 * @var string[]
	 */
	public const UNSAFE_CROSS_SITE_ACTIONS = [ 'run_now', 'run_reschedule' ];

	/**
	 * Parses a composite ID into blog_id and action_id.
	 *
	 * @since 1.12.0
	 *
	 * @param string $composite_id Composite ID in "blog_id:action_id" format.
	 *
	 * @return array{blog_id: int, action_id: int}
	 * @throws Exception When the composite ID format is invalid.
	 */
	public static function parse_composite_id( string $composite_id ): array {
		$parts = explode( ':', $composite_id, 2 );

		if ( count( $parts ) !== 2 || ! is_numeric( $parts[0] ) || ! is_numeric( $parts[1] ) ) {
			throw new Exception(
				strtr( 'Invalid composite ID: [id]', [ '[id]' => $composite_id ] )
			);
		}

		return [
			'blog_id'   => (int) $parts[0],
			'action_id' => (int) $parts[1],
		];
	}

	/**
	 * Executes a single action on a job in the correct blog context.
	 *
	 * @since 1.12.0
	 *
	 * @param string $composite_id Composite ID in "blog_id:action_id" format.
	 * @param string $action       The action to execute.
	 *
	 * @return bool Whether the action succeeded.
	 * @throws Exception When the action is unsafe cross-site or the ID is invalid.
	 */
	public function execute( string $composite_id, string $action ): bool {
		if ( in_array( $action, self::UNSAFE_CROSS_SITE_ACTIONS, true ) ) {
			throw new Exception(
				strtr(
					'Action [action] is not supported in network admin context.',
					[ '[action]' => $action ]
				)
			);
		}

		$parsed    = self::parse_composite_id( $composite_id );
		$blog_id   = $parsed['blog_id'];
		$action_id = $parsed['action_id'];

		if ( ! get_blog_details( $blog_id ) ) {
			throw new Exception(
				strtr( 'Site [id] does not exist.', [ '[id]' => $blog_id ] )
			);
		}

		switch_to_blog( $blog_id );

		try {
			$service = new JobActionService(
				JobScheduler::get_instance()->manager(),
				DbStore::get_instance()
			);

			switch ( $action ) {
				case 'retry':
					$service->retry( $action_id );
					break;
				case 'pause':
					$service->pause( $action_id );
					break;
				case 'unpause':
					$service->unpause( $action_id );
					break;
				case 'cancel':
					$service->cancel( $action_id );
					break;
				case 'delete':
					$service->delete( $action_id );
					break;
				default:
					return false;
			}

			return true;
		} finally {
			restore_current_blog();
		}
	}

	/**
	 * Reloads and serializes a single job after an action.
	 *
	 * @since 1.12.0
	 *
	 * @param string $composite_id Composite ID in "blog_id:action_id" format.
	 *
	 * @return array|null Serialized job data with site info, or null if not found.
	 */
	public function reload_job( string $composite_id ): ?array {
		$parsed    = self::parse_composite_id( $composite_id );
		$blog_id   = $parsed['blog_id'];
		$action_id = $parsed['action_id'];

		if ( ! get_blog_details( $blog_id ) ) {
			return null;
		}

		switch_to_blog( $blog_id );

		try {
			$serializer = new JobSerializer();
			$store      = DbStore::get_instance();
			$service    = new JobQueryService( $store, $serializer );
			$job        = $service->get( $action_id );

			if ( ! $job ) {
				return null;
			}

			$job['site'] = NetworkJobQueryService::get_site_info( $blog_id );
			$job['id']   = $composite_id;

			// Remove cross-site unsafe actions.
			$job['actions'] = array_values(
				array_diff( $job['actions'], static::UNSAFE_CROSS_SITE_ACTIONS )
			);

			return $job;
		} finally {
			restore_current_blog();
		}
	}
}
