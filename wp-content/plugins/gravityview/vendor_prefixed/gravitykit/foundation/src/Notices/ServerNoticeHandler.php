<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Licenses\ChannelManager;
use GravityKit\GravityView\Foundation\Licenses\Framework;
use GravityKit\GravityView\Foundation\Licenses\ProductManager;
use GravityKit\GravityView\Foundation\Logger\Framework as Logger;
use Throwable;

/**
 * Syncs server-driven product notices with Foundation's stored notice system.
 *
 * The EDD license API sends per-product notices that match the site's installed
 * versions. This handler reconciles those with locally stored notices: adding new
 * ones, updating changed definitions, and removing stale ones.
 *
 * @since 1.13.0
 */
final class ServerNoticeHandler {
	/**
	 * Slug prefix that identifies server-originated notices.
	 *
	 * @since 1.13.0
	 */
	const SLUG_PREFIX = 'server-';

	/**
	 * Transient key for the sync lock.
	 *
	 * @since 1.13.0
	 */
	const LOCK_TRANSIENT = 'gk_server_notice_sync_lock';

	/**
	 * Lock duration in seconds.
	 *
	 * @since 1.13.0
	 */
	const LOCK_TTL = 30;

	/**
	 * Fields from the server notice definition that map directly to Foundation notice fields.
	 *
	 * @since 1.13.0
	 */
	const PASSTHROUGH_FIELDS = [
		'severity',
		'dismissible',
		'sticky',
		'order',
		'screens',
		'capabilities',
		'context',
		'snooze',
		'starts',
		'expires',
	];

	/**
	 * Syncs server-sent product notices with local stored notices.
	 *
	 * Adds new notices, updates changed ones (resetting dismissals on message changes),
	 * and removes notices no longer present in the server response.
	 *
	 * @since 1.13.0
	 *
	 * @param array         $products_data Products from the license response, keyed by text_domain.
	 *                                     Each product must have 'text_domain', 'id', and optionally 'product_notices'.
	 * @param NoticeManager $manager       Notice manager instance.
	 */
	public static function sync( array $products_data, NoticeManager $manager ): void {
		if ( get_transient( self::LOCK_TRANSIENT ) ) {
			return;
		}

		set_transient( self::LOCK_TRANSIENT, 1, self::LOCK_TTL );

		try {
			self::do_sync( $products_data, $manager );
		} catch ( Throwable $e ) {
			Logger::get_instance()->error( 'Server notice sync failed: ' . $e->getMessage() );
		} finally {
			delete_transient( self::LOCK_TRANSIENT );
		}
	}

	/**
	 * Internal sync logic.
	 *
	 * @since 1.13.0
	 *
	 * @param array         $products_data Products from the license response.
	 * @param NoticeManager $manager       Notice manager instance.
	 */
	private static function do_sync( array $products_data, NoticeManager $manager ): void {
		$incoming    = [];
		$product_tds = [];

		// Collect all incoming server notices and track which products are in the response.
		foreach ( $products_data as $product ) {
			$text_domain = $product['text_domain'] ?? '';
			$product_id  = $product['id'] ?? 0;

			if ( ! $text_domain ) {
				continue;
			}

			$product_tds[] = $text_domain;

			foreach ( $product['product_notices'] ?? [] as $notice_key => $notice ) {
				$full_id = $text_domain . '/' . self::SLUG_PREFIX . $notice_key;

				$incoming[ $full_id ] = [
					'text_domain' => $text_domain,
					'product_id'  => $product_id,
					'notice_key'  => $notice_key,
					'notice'      => $notice,
				];
			}
		}

		$existing = $manager->get_stored_by_slug_prefix( self::SLUG_PREFIX );

		// Add new and update changed notices.
		foreach ( $incoming as $full_id => $entry ) {
			$existing_notice = $existing[ $full_id ] ?? null;

			if ( ! $existing_notice ) {
				self::add_notice( $entry, $manager );
				continue;
			}

			self::maybe_update_notice( $full_id, $entry, $existing_notice, $manager );
		}

		// Remove stale notices: product is in the response but its notice key is absent.
		foreach ( $existing as $existing_id => $existing_notice ) {
			$ns = $existing_notice->get_namespace();

			// Only remove notices for products present in this response.
			if ( ! in_array( $ns, $product_tds, true ) ) {
				continue;
			}

			if ( ! isset( $incoming[ $existing_id ] ) ) {
				try {
					$manager->remove( $existing_id );
				} catch ( Throwable $e ) {
					Logger::get_instance()->warning( "Failed to remove stale server notice {$existing_id}: " . $e->getMessage() );
				}
			}
		}
	}

	/**
	 * Adds a new server notice.
	 *
	 * @since 1.13.0
	 *
	 * @param array         $entry   Notice entry from the incoming data.
	 * @param NoticeManager $manager Notice manager instance.
	 */
	private static function add_notice( array $entry, NoticeManager $manager ): void {
		$data = self::build_notice_data( $entry['text_domain'], $entry['product_id'], $entry['notice_key'], $entry['notice'] );

		if ( empty( $data ) ) {
			return;
		}

		try {
			$manager->add_stored( $data );
		} catch ( Throwable $e ) {
			Logger::get_instance()->warning( "Failed to add server notice {$entry['text_domain']}/{$entry['notice_key']}: " . $e->getMessage() );
		}
	}

	/**
	 * Updates an existing notice if the definition has changed.
	 *
	 * Message content changes trigger a full remove + re-add (resets dismissals).
	 * Other field changes use update_notice() to preserve dismissal state.
	 *
	 * @since 1.13.0
	 *
	 * @param string          $full_id         Full notice ID.
	 * @param array           $entry           Incoming notice entry.
	 * @param NoticeInterface $existing_notice  Existing stored notice.
	 * @param NoticeManager   $manager         Notice manager instance.
	 */
	private static function maybe_update_notice( string $full_id, array $entry, NoticeInterface $existing_notice, NoticeManager $manager ): void {
		$new_data = self::build_notice_data( $entry['text_domain'], $entry['product_id'], $entry['notice_key'], $entry['notice'] );

		if ( empty( $new_data ) ) {
			// User emails no longer resolve — remove the existing notice.
			try {
				$manager->remove( $full_id );
			} catch ( Throwable $e ) {
				Logger::get_instance()->warning( "Failed to remove server notice {$full_id}: " . $e->getMessage() );
			}

			return;
		}

		$existing_def  = $existing_notice->as_definition();
		$existing_hash = $existing_def['extra']['content_hash'] ?? '';
		$new_hash      = $new_data['extra']['content_hash'] ?? '';

		// Message changed — remove and re-add to reset dismissals.
		if ( $existing_hash !== $new_hash ) {
			try {
				$manager->remove( $full_id );
				$manager->add_stored( $new_data );
			} catch ( Throwable $e ) {
				Logger::get_instance()->warning( "Failed to replace server notice {$full_id}: " . $e->getMessage() );
			}

			return;
		}

		// Check for non-message field changes.
		$changes = [];

		foreach ( self::PASSTHROUGH_FIELDS as $field ) {
			$new_val      = $new_data[ $field ] ?? null;
			$existing_val = $existing_def[ $field ] ?? null;

			if ( $new_val !== $existing_val ) {
				$changes[ $field ] = $new_val;
			}
		}

		// Check extra fields (version_match may change).
		if ( ( $new_data['extra'] ?? [] ) !== ( $existing_def['extra'] ?? [] ) ) {
			$changes['extra'] = $new_data['extra'];
		}

		if ( empty( $changes ) ) {
			return;
		}

		try {
			$manager->update_notice( $full_id, $changes );
		} catch ( Throwable $e ) {
			Logger::get_instance()->warning( "Failed to update server notice {$full_id}: " . $e->getMessage() );
		}
	}

	/**
	 * Builds a Foundation notice definition array from server notice data.
	 *
	 * @since 1.13.0
	 *
	 * @param string $text_domain Product text domain (used as notice namespace).
	 * @param int    $product_id  Product ID (used for placeholder resolution).
	 * @param string $notice_key  Notice key from the server definition.
	 * @param array  $notice      Notice data from the server.
	 *
	 * @return array Foundation notice definition.
	 */
	private static function build_notice_data( string $text_domain, int $product_id, string $notice_key, array $notice ): array {
		$message = self::sanitize_server_message( $notice['message'] ?? '' );
		$message = self::replace_placeholders( $message, $product_id, $text_domain );

		$data = [
			'namespace' => $text_domain,
			'slug'      => self::SLUG_PREFIX . $notice_key,
			'message'   => $message,
			'scope'     => 'global',
			'context'   => [ 'site', 'ms_main', 'ms_subsite', 'ms_network' ],
			'condition' => self::class . '::check_version_match',
			'extra'     => [
				'text_domain'   => $text_domain,
				'version_match' => $notice['version_match'] ?? null,
				'min_version'   => $notice['min_version'] ?? null,
				'max_version'   => $notice['max_version'] ?? null,
				'content_hash'  => md5( $notice['message'] ?? '' ),
			],
		];

		foreach ( self::PASSTHROUGH_FIELDS as $field ) {
			if ( isset( $notice[ $field ] ) ) {
				$data[ $field ] = $notice[ $field ];
			}
		}

		// Resolve email-based user targeting.
		if ( ! empty( $notice['user_emails'] ) && is_array( $notice['user_emails'] ) ) {
			$user_ids = self::resolve_user_emails( $notice['user_emails'] );

			if ( empty( $user_ids ) ) {
				// None of the targeted emails exist on this site — skip the notice.
				return [];
			}

			$data['scope'] = 'user';
			$data['users'] = $user_ids;
		}

		return $data;
	}

	/**
	 * Condition callback for server notices.
	 *
	 * Re-evaluates the version_match regex against the currently installed plugin version
	 * on every page load. Returns false if the plugin is no longer installed or the version
	 * no longer matches, causing the notice to be hidden immediately without waiting for
	 * the next license revalidation sync.
	 *
	 * @since 1.13.0
	 *
	 * @param NoticeInterface $notice The notice being evaluated.
	 *
	 * @return bool Whether the notice should be displayed.
	 */
	public static function check_version_match( NoticeInterface $notice ): bool {
		$extra         = $notice->get_extra();
		$version_match = $extra['version_match'] ?? null;
		$min_version   = $extra['min_version'] ?? null;
		$max_version   = $extra['max_version'] ?? null;

		if ( null === $version_match && null === $min_version && null === $max_version ) {
			return true;
		}

		$text_domain = $extra['text_domain'] ?? '';

		if ( ! $text_domain ) {
			return false;
		}

		$installed = CoreHelpers::get_installed_plugin_by_text_domain( $text_domain );

		if ( ! $installed ) {
			return false;
		}

		$installed_version = $installed['version'] ?? '';

		if ( ! $installed_version ) {
			return false;
		}

		if ( null !== $version_match && ! @preg_match( '/' . $version_match . '/', $installed_version ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Malformed regex should fail gracefully.
			return false;
		}

		if ( null !== $min_version && version_compare( $installed_version, (string) $min_version, '<' ) ) {
			return false;
		}

		if ( null !== $max_version && version_compare( $installed_version, (string) $max_version, '>' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Resolves an array of email addresses to WordPress user IDs.
	 *
	 * Emails that don't match a user on this site are silently skipped.
	 *
	 * @since 1.13.0
	 *
	 * @param string[] $emails Email addresses to resolve.
	 *
	 * @return int[] WordPress user IDs.
	 */
	private static function resolve_user_emails( array $emails ): array {
		$ids = [];

		foreach ( $emails as $email ) {
			$user = get_user_by( 'email', $email );

			if ( $user ) {
				$ids[] = $user->ID;
			}
		}

		return $ids;
	}

	/**
	 * Strips img tags from server-originated messages to prevent tracking pixels.
	 *
	 * @since 1.13.0
	 *
	 * @param string $message Raw message content.
	 *
	 * @return string Message with img tags removed.
	 */
	private static function sanitize_server_message( string $message ): string {
		return preg_replace( '/<img[^>]*>/i', '', $message );
	}

	/**
	 * Replaces placeholders in notice messages.
	 *
	 * Supported placeholders:
	 * - [product_link] — URL to the product in Manage Your Kit.
	 * - [product_name] — Product display name (e.g., "GravityView").
	 * - [product_version] — Currently installed version (e.g., "2.54.2").
	 *
	 * @since 1.13.0
	 *
	 * @param string     $message     Message content with placeholders.
	 * @param int|string $product_id  Product ID for link generation.
	 * @param string     $text_domain Product text domain for name/version lookup.
	 *
	 * @return string Message with placeholders replaced.
	 */
	private static function replace_placeholders( string $message, $product_id, string $text_domain = '' ): string {
		if ( str_contains( $message, '[product_link]' ) ) {
			$link    = Framework::get_instance()->get_link_to_product_search( (string) $product_id );
			$message = str_replace( '[product_link]', esc_url( $link ), $message );
		}

		if ( str_contains( $message, '[product_name]' ) || str_contains( $message, '[product_version]' ) ) {
			$product = $text_domain ? CoreHelpers::get_installed_plugin_by_text_domain( $text_domain ) : null;

			$message = str_replace( '[product_name]', esc_html( $product['name'] ?? '' ), $message );
			$message = str_replace( '[product_version]', esc_html( $product['version'] ?? '' ), $message );
		}

		return $message;
	}
}
