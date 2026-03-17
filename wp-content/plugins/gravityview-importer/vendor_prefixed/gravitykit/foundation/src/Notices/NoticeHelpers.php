<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Notices;

/**
 * Helper functions (e.g., creating notices with specific audience targeting, etc.)
 *
 * @since 1.3.0
 */
class NoticeHelpers {
	/**
	 * Creates configuration for notices that should only appear in the network admin area.
	 *
	 * This sets the context to 'ms_network' and requires network management capabilities.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string[], capabilities: string[]} Notice configuration array.
	 */
	public static function network_only(): array {
		return [
			'context'      => [ 'ms_network' ],
			'capabilities' => [ 'manage_network' ],
		];
	}

	/**
	 * Creates configuration for notices that should appear in site admin areas.
	 *
	 * This includes single-site installations, multisite main sites, and multisite subsites,
	 * but NOT the network admin area.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string[], capabilities: string[]} Notice configuration array.
	 */
	public static function sites_only(): array {
		return [
			'context'      => [ 'site', 'ms_main', 'ms_subsite' ],
			'capabilities' => [ 'manage_options' ],
		];
	}

	/**
	 * Creates configuration for notices that should appear to site admins,
	 * but explicitly exclude network super admins.
	 *
	 * This is useful when you want to show notices only to site admins
	 * without overwhelming network super admins who manage many sites.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string[], capabilities: string[], condition: callable} Notice configuration array.
	 */
	public static function sites_exclude_super(): array {
		return [
			'context'      => [ 'site', 'ms_main', 'ms_subsite' ],
			'capabilities' => [ 'manage_options' ],
			'condition'    => static function() {
				return ! is_super_admin();
			},
		];
	}

	/**
	 * Creates configuration for notices that should only appear to non-admin users.
	 *
	 * This targets users who can read but don't have administrative capabilities.
	 * Uses default context since non-admin users don't access network/user admin.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string[], capabilities: string[], condition: callable} Notice configuration array.
	 */
	public static function non_admins_only(): array {
		return [
			'context'      => [ 'site', 'ms_subsite' ],
			'capabilities' => [ 'read' ],
			'condition'    => static function() {
				return ! current_user_can( 'manage_options' );
			},
		];
	}

	/**
	 * Creates configuration for notices that should appear only on specific sites.
	 *
	 * @since 1.3.0
	 *
	 * @param int|int[]|string $site_ids Single site ID, array of site IDs, or 'main' for main site only.
	 *
	 * @return array{context: string[], capabilities: string[], condition?: callable} Notice configuration array.
	 */
	public static function specific_sites( $site_ids ): array {
		// Handle special 'main' keyword.
		if ( 'main' === $site_ids ) {
			return self::main_site_only();
		}

		$site_ids = (array) $site_ids;

		return [
			'context'      => [ 'ms_main', 'ms_subsite' ],
			'capabilities' => [ 'manage_options' ],
			'condition'    => static function() use ( $site_ids ) {
				return is_multisite() && in_array( get_current_blog_id(), $site_ids, true );
			},
		];
	}

	/**
	 * Creates configuration for notices that should appear only on the main site.
	 *
	 * In single-site installations, this will always show the notice.
	 * In multisite, it will only show on the main site.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string[], capabilities: string[]} Notice configuration array.
	 */
	public static function main_site_only(): array {
		return [
			'context'      => [ 'site', 'ms_main' ],
			'capabilities' => [ 'manage_options' ],
		];
	}

	/**
	 * Creates configuration for notices that should appear only on subsites
	 * (excluding the main site) in a multisite network.
	 *
	 * This will never show in single-site installations.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string[], capabilities: string[]} Notice configuration array.
	 */
	public static function subsites_only(): array {
		return [
			'context'      => [ 'ms_subsite' ],
			'capabilities' => [ 'manage_options' ],
		];
	}

	/**
	 * Creates configuration for notices visible in all admin contexts.
	 *
	 * This shows notices in network, user, and all site admin areas to users
	 * with read capabilities.
	 *
	 * @since 1.3.0
	 *
	 * @return array{context: string, capabilities: string[]} Notice configuration array.
	 */
	public static function all_contexts(): array {
		return [
			'context'      => 'all',
			'capabilities' => [ 'read' ],
		];
	}

	/**
	 * Creates configuration for notices that should appear to users with specific
	 * capabilities, with optional network/site context restrictions.
	 *
	 * @since 1.3.0
	 *
	 * @param string|string[] $capabilities Single capability or array of capabilities.
	 * @param string|null     $context      Optional context: 'ms_network', 'ms_main', 'ms_subsite', 'site', 'user', or null for all.
	 *
	 * @return array{capabilities: array, context?: string|array} Notice configuration array.
	 */
	public static function with_capabilities( $capabilities, ?string $context = null ): array {
		$config = [
			'capabilities' => (array) $capabilities,
		];

		if ( null !== $context ) {
			$config['context'] = 'all' === $context ? 'all' : [ $context ];
		}

		return $config;
	}

	/**
	 * Creates configuration for notices that should only appear on specific
	 * admin screens to users with certain capabilities.
	 *
	 * @since 1.3.0
	 *
	 * @param string|string[] $screens      Screen ID(s) where the notice should appear.
	 * @param string|string[] $capabilities Capability/capabilities required to see the notice.
	 * @param string|null     $context      Optional context: 'ms_network', 'ms_main', 'ms_subsite', 'site', 'user', or null for default.
	 *
	 * @return array{capabilities: array, screens: array, context?: string|array} Notice configuration array.
	 */
	public static function on_screens( $screens, $capabilities = [ 'manage_options' ], ?string $context = null ): array {
		$config = [
			'capabilities' => (array) $capabilities,
			'screens'      => (array) $screens,
		];

		if ( null !== $context ) {
			$config['context'] = 'all' === $context ? 'all' : [ $context ];
		}

		return $config;
	}

	/**
	 * Creates configuration to explicitly exclude super admins from seeing a notice,
	 * even if they have the required capabilities.
	 *
	 * This is useful for notices that should be hidden from super admins to avoid
	 * notification overload in multisite environments.
	 *
	 * @since 1.3.0
	 *
	 * @param array $base_config Base notice configuration to extend.
	 *
	 * @return array Notice configuration with super admin exclusion.
	 */
	public static function exclude_super_admin( array $base_config ): array {
		$existing_condition = $base_config['condition'] ?? null;

		$base_config['condition'] = static function( $notice ) use ( $existing_condition ) {
			// First check if super admin.
			if ( is_super_admin() ) {
				return false;
			}

			// Then check existing condition if present.
			if ( is_callable( $existing_condition ) ) {
				return $existing_condition( $notice );
			}

			return true;
		};

		return $base_config;
	}
}
