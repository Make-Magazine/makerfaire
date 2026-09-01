<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Notices;

use GravityKit\GravityImport\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityImport\Foundation\Licenses\ChannelManager;
use GravityKit\GravityImport\Foundation\Licenses\Framework;
use GravityKit\GravityImport\Foundation\Licenses\ProductManager;
use GravityKit\GravityImport\Foundation\Logger\Framework as Logger;
use Throwable;

/**
 * Syncs server-driven product notices with Foundation's stored notice system.
 *
 * The EDD license API sends per-product notices that match the site's installed
 * versions. This handler reconciles those with locally stored notices: adding new
 * ones, updating changed definitions, and removing stale ones.
 *
 * @since 1.13.0
 * @since TBD Added source-aware catalog and license reconciliation.
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
	 * Option containing source contributions for server notices.
	 *
	 * @since TBD
	 */
	const SOURCES_OPTION = 'gk_server_notice_sources';

	/**
	 * Option used as an atomic cross-source write lock.
	 *
	 * @since TBD
	 */
	const WRITE_LOCK_OPTION = 'gk_server_notice_sources_lock';

	/**
	 * License notice source.
	 *
	 * @since TBD
	 */
	const SOURCE_LICENSE = 'license';

	/**
	 * Product catalog notice source.
	 *
	 * @since TBD
	 */
	const SOURCE_CATALOG = 'catalog';

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
	 * @since TBD Added the $source parameter.
	 *
	 * @param array         $products_data Products from the license response, keyed by text_domain.
	 *                                     Each product must have 'text_domain', 'id', and optionally 'product_notices'.
	 * @param NoticeManager $manager       Notice manager instance.
	 * @param string        $source        Notice source: 'license' or 'catalog'.
	 *
	 * @return bool Whether the source data was reconciled.
	 */
	public static function sync( array $products_data, NoticeManager $manager, string $source = self::SOURCE_LICENSE ): bool {
		if ( ! in_array( $source, [ self::SOURCE_LICENSE, self::SOURCE_CATALOG ], true ) ) {
			Logger::get_instance()->warning( "Unsupported server notice source: {$source}." );

			return false;
		}

		$lock_transient = self::LOCK_TRANSIENT . '_' . $source;

		if ( get_transient( $lock_transient ) ) {
			return false;
		}

		set_transient( $lock_transient, 1, self::LOCK_TTL );

		if ( ! self::acquire_write_lock() ) {
			delete_transient( $lock_transient );

			return false;
		}

		try {
			// The shared lock serializes source-map updates while the per-source lock avoids duplicate work.
			return self::do_sync( $products_data, $manager, $source );
		} catch ( Throwable $e ) {
			Logger::get_instance()->error( 'Server notice sync failed: ' . $e->getMessage() );

			return false;
		} finally {
			self::release_write_lock();
			delete_transient( $lock_transient );
		}
	}

	/**
	 * The exact write-lock value this request wrote, so release only removes a lock it still owns.
	 *
	 * @since TBD
	 *
	 * @var string|null
	 */
	private static $write_lock_value = null;

	/**
	 * Acquires the cross-source write lock.
	 *
	 * @since TBD
	 *
	 * @return bool Whether the lock was acquired.
	 */
	private static function acquire_write_lock(): bool {
		if ( self::insert_write_lock() ) {
			return true;
		}

		$current = (string) get_option( self::WRITE_LOCK_OPTION, '' );
		$expires = self::lock_expiry( $current );

		if ( ! $expires || $expires > time() ) {
			return false;
		}

		delete_option( self::WRITE_LOCK_OPTION );

		return self::insert_write_lock();
	}

	/**
	 * Parses the expiry timestamp from a stored lock value ("<token>:<expiry>", or a bare timestamp
	 * written by an older version).
	 *
	 * @since TBD
	 *
	 * @param string $value Stored lock value.
	 *
	 * @return int Expiry timestamp, or 0 when absent.
	 */
	private static function lock_expiry( string $value ): int {
		if ( '' === $value ) {
			return 0;
		}

		$separator = strrpos( $value, ':' );

		return (int) ( false === $separator ? $value : substr( $value, $separator + 1 ) );
	}

	/**
	 * Atomically inserts the cross-source write lock.
	 *
	 * @since TBD
	 *
	 * @return bool Whether the lock row was inserted.
	 */
	private static function insert_write_lock(): bool {
		global $wpdb;

		if ( ! is_object( $wpdb ) || ! isset( $wpdb->options ) || ! method_exists( $wpdb, 'prepare' ) || ! method_exists( $wpdb, 'query' ) ) {
			return false;
		}

		// A random token makes each lock value unique, so release can tell its own lock from one another
		// request took over after this one's TTL lapsed.
		$value = bin2hex( random_bytes( 8 ) ) . ':' . ( time() + self::LOCK_TTL );

		$result = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'off')", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name comes from wpdb.
				self::WRITE_LOCK_OPTION,
				$value
			)
		);

		if ( 1 !== $result ) {
			return false;
		}

		self::$write_lock_value = $value;

		wp_cache_delete( self::WRITE_LOCK_OPTION, 'options' );

		return true;
	}

	/**
	 * Releases the cross-source write lock.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	private static function release_write_lock(): void {
		if ( null === self::$write_lock_value ) {
			return;
		}

		// Only remove the row if it is still the one we wrote: once our TTL lapses another request may
		// have taken the lock over, and deleting its row would let a third writer race it.
		if ( (string) get_option( self::WRITE_LOCK_OPTION, '' ) === self::$write_lock_value ) {
			delete_option( self::WRITE_LOCK_OPTION );
		}

		self::$write_lock_value = null;
	}

	/**
	 * Internal sync logic.
	 *
	 * @since 1.13.0
	 * @since TBD Added source-aware reconciliation.
	 *
	 * @param array         $products_data Products from the license response.
	 * @param NoticeManager $manager       Notice manager instance.
	 * @param string        $source        Notice source.
	 *
	 * @return bool Whether every registry and notice write succeeded.
	 */
	private static function do_sync( array $products_data, NoticeManager $manager, string $source ): bool {
		$incoming    = [];
		$product_tds = [];

		foreach ( $products_data as $product ) {
			if ( ! is_array( $product ) ) {
				continue;
			}

			$text_domain = $product['text_domain'] ?? '';
			$product_id  = $product['id'] ?? 0;

			if ( ! is_scalar( $text_domain ) || '' === (string) $text_domain ) {
				continue;
			}

			$text_domain   = (string) $text_domain;
			$product_id    = is_numeric( $product_id ) ? (int) $product_id : 0;
			$product_tds[] = $text_domain;
			$text_domains  = self::get_product_text_domains( $product, $text_domain );

			$product_notices = $product['product_notices'] ?? [];

			if ( ! is_array( $product_notices ) ) {
				continue;
			}

			foreach ( $product_notices as $notice_key => $notice ) {
				if ( ! is_scalar( $notice_key ) || ! is_array( $notice ) ) {
					continue;
				}

				$notice_key = (string) $notice_key;
				$full_id    = $text_domain . '/' . self::SLUG_PREFIX . $notice_key;
				$data       = self::build_notice_data( $text_domain, $product_id, $notice_key, $notice, $text_domains );

				if ( empty( $data ) ) {
					continue;
				}

				$incoming[ $full_id ] = self::compact_source_definition( $data );
			}
		}

		$existing = $manager->get_stored_by_slug_prefix_all_scopes( self::SLUG_PREFIX );
		$registry = self::get_sources_registry( $existing );
		$touched  = array_fill_keys( array_keys( $incoming ), true );

		foreach ( $incoming as $full_id => $definition ) {
			$registry[ $full_id ][ $source ] = $definition;
		}

		foreach ( $registry as $existing_id => $sources ) {
			if ( ! isset( $sources[ $source ] ) ) {
				continue;
			}

			$namespace = $sources[ $source ]['namespace'] ?? '';

			if ( self::SOURCE_LICENSE === $source && ! in_array( $namespace, $product_tds, true ) ) {
				continue;
			}

			if ( ! isset( $incoming[ $existing_id ] ) ) {
				unset( $registry[ $existing_id ][ $source ] );

				$touched[ $existing_id ] = true;

				if ( empty( $registry[ $existing_id ] ) ) {
					unset( $registry[ $existing_id ] );
				}
			}
		}

		if ( ! self::persist_sources_registry( $registry ) ) {
			return false;
		}

		$success = true;

		foreach ( array_keys( $touched ) as $full_id ) {
			$existing_notice = $existing[ $full_id ] ?? null;
			$new_data        = self::materialize_sources( $registry[ $full_id ] ?? [] );

			if ( empty( $new_data ) ) {
				if ( $existing_notice ) {
					$success = self::remove_notice( $full_id, $manager ) && $success;
				}

				continue;
			}

			if ( ! $existing_notice ) {
				$success = self::add_notice( $full_id, $new_data, $manager ) && $success;
				continue;
			}

			$success = self::persist_notice_update( $full_id, $new_data, $existing_notice, $manager ) && $success;
		}

		return $success;
	}

	/**
	 * Returns a product's current and legacy text domains.
	 *
	 * @since TBD
	 *
	 * @param array  $product     Product data.
	 * @param string $text_domain Current text domain.
	 *
	 * @return string[]
	 */
	private static function get_product_text_domains( array $product, string $text_domain ): array {
		$text_domains = is_array( $product['text_domains'] ?? null ) ? $product['text_domains'] : [];
		$legacy       = $product['text_domain_legacy'] ?? '';

		if ( is_scalar( $legacy ) ) {
			$text_domains = array_merge( $text_domains, explode( '|', (string) $legacy ) );
		}

		$text_domains[] = $text_domain;

		return self::normalize_text_domains( $text_domains );
	}

	/**
	 * Normalizes a list of product text domains.
	 *
	 * @since TBD
	 *
	 * @param array $text_domains Text domains.
	 *
	 * @return string[]
	 */
	private static function normalize_text_domains( array $text_domains ): array {
		$normalized = [];

		foreach ( $text_domains as $text_domain ) {
			if ( ! is_scalar( $text_domain ) || '' === (string) $text_domain ) {
				continue;
			}

			$normalized[] = (string) $text_domain;
		}

		return array_values( array_unique( $normalized ) );
	}

	/**
	 * Returns the normalized source registry, migrating legacy notices when needed.
	 *
	 * @since TBD
	 *
	 * @param StoredNoticeInterface[] $existing Stored server notices keyed by notice ID.
	 *
	 * @return array
	 */
	private static function get_sources_registry( array $existing ): array {
		$stored   = get_option( self::SOURCES_OPTION, null );
		$registry = is_array( $stored ) ? self::normalize_sources_registry( $stored ) : [];

		foreach ( $existing as $full_id => $notice ) {
			// Union each stored source independently so a previously failed registry write
			// self-heals without allowing stored data to overwrite a newer registry source.
			foreach ( self::get_sources( $notice ) as $source => $definition ) {
				if ( ! isset( $registry[ $full_id ][ $source ] ) ) {
					$registry[ $full_id ][ $source ] = $definition;
				}
			}
		}

		return self::normalize_sources_registry( $registry );
	}

	/**
	 * Persists the source registry.
	 *
	 * WordPress returns false when update_option() receives an unchanged value, so
	 * verify the stored value before treating false as a write failure.
	 *
	 * @since TBD
	 *
	 * @param array $registry Normalized source registry.
	 *
	 * @return bool Whether the registry is persisted.
	 */
	private static function persist_sources_registry( array $registry ): bool {
		if ( update_option( self::SOURCES_OPTION, $registry, false ) ) {
			return true;
		}

		return get_option( self::SOURCES_OPTION, null ) === $registry;
	}

	/**
	 * Normalizes the source registry.
	 *
	 * @since TBD
	 *
	 * @param array $registry Source registry.
	 *
	 * @return array
	 */
	private static function normalize_sources_registry( array $registry ): array {
		$normalized = [];

		foreach ( $registry as $full_id => $sources ) {
			if ( ! is_string( $full_id ) || ! is_array( $sources ) ) {
				continue;
			}

			foreach ( [ self::SOURCE_CATALOG, self::SOURCE_LICENSE ] as $source ) {
				if ( isset( $sources[ $source ] ) && is_array( $sources[ $source ] ) ) {
					$normalized[ $full_id ][ $source ] = self::compact_source_definition( $sources[ $source ] );
				}
			}
		}

		return $normalized;
	}

	/**
	 * Adds a materialized server notice.
	 *
	 * @since TBD
	 *
	 * @param string        $full_id Full notice ID.
	 * @param array         $data    Notice definition.
	 * @param NoticeManager $manager Notice manager instance.
	 *
	 * @return bool Whether the notice was added.
	 */
	private static function add_notice( string $full_id, array $data, NoticeManager $manager ): bool {
		try {
			if ( null === $manager->add_stored( $data ) ) {
				return false;
			}
		} catch ( Throwable $e ) {
			Logger::get_instance()->warning( "Failed to add server notice {$full_id}: " . $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Persists a materialized server notice update.
	 *
	 * @since TBD
	 *
	 * @param string                $full_id         Full notice ID.
	 * @param array                 $new_data        New materialized notice definition.
	 * @param StoredNoticeInterface $existing_notice Existing stored notice.
	 * @param NoticeManager         $manager         Notice manager instance.
	 *
	 * @return bool Whether the notice was updated.
	 */
	private static function persist_notice_update( string $full_id, array $new_data, StoredNoticeInterface $existing_notice, NoticeManager $manager ): bool {
		$existing_def  = $existing_notice->as_definition();
		$existing_hash = $existing_def['extra']['content_hash'] ?? '';
		$new_hash      = $new_data['extra']['content_hash'] ?? '';
		$scope_changed = ( $new_data['scope'] ?? 'global' ) !== ( $existing_def['scope'] ?? 'global' );
		$users_changed = ( $new_data['users'] ?? [] ) !== ( $existing_def['users'] ?? [] );

		// Message changes reset dismissals. Storage-target changes require replacement so stale
		// global/user definitions are removed from their previous persistence location.
		if ( $existing_hash !== $new_hash || $scope_changed || $users_changed ) {
			try {
				$manager->remove( $full_id );

				if ( null === $manager->add_stored( $new_data ) ) {
					return false;
				}
			} catch ( Throwable $e ) {
				Logger::get_instance()->warning( "Failed to replace server notice {$full_id}: " . $e->getMessage() );

				return false;
			}

			return true;
		}

		// Check for non-message field changes.
		$changes = [];

		foreach ( array_merge( [ 'message', 'scope', 'context', 'condition', 'users' ], self::PASSTHROUGH_FIELDS ) as $field ) {
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
			return true;
		}

		try {
			$manager->update_notice( $full_id, $changes, $existing_notice );
		} catch ( Throwable $e ) {
			Logger::get_instance()->warning( "Failed to update server notice {$full_id}: " . $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Removes a stored server notice.
	 *
	 * @since TBD
	 *
	 * @param string        $full_id Full notice ID.
	 * @param NoticeManager $manager Notice manager instance.
	 *
	 * @return bool Whether the notice was removed.
	 */
	private static function remove_notice( string $full_id, NoticeManager $manager ): bool {
		try {
			$manager->remove( $full_id );
		} catch ( Throwable $e ) {
			Logger::get_instance()->warning( "Failed to remove server notice {$full_id}: " . $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Returns the normalized source contributions for a stored notice.
	 *
	 * Notices created before source tracking are license-sourced.
	 *
	 * @since TBD
	 *
	 * @param NoticeInterface $notice Stored notice.
	 *
	 * @return array
	 */
	private static function get_sources( NoticeInterface $notice ): array {
		$definition = $notice->as_definition();
		$stored     = $definition['extra']['sources'] ?? null;
		$sources    = [];

		if ( is_array( $stored ) ) {
			foreach ( [ self::SOURCE_CATALOG, self::SOURCE_LICENSE ] as $source ) {
				if ( isset( $stored[ $source ] ) && is_array( $stored[ $source ] ) ) {
					$sources[ $source ] = self::compact_source_definition( $stored[ $source ] );
				}
			}
		}

		if ( ! empty( $sources ) ) {
			return $sources;
		}

		unset( $definition['extra']['sources'] );

		return [ self::SOURCE_LICENSE => self::compact_source_definition( $definition ) ];
	}

	/**
	 * Builds the displayed notice from its available source contributions.
	 *
	 * @since TBD
	 *
	 * @param array $sources Normalized source contributions.
	 *
	 * @return array
	 */
	private static function materialize_sources( array $sources ): array {
		$normalized = [];

		foreach ( [ self::SOURCE_CATALOG, self::SOURCE_LICENSE ] as $source ) {
			if ( isset( $sources[ $source ] ) && is_array( $sources[ $source ] ) ) {
				$normalized[ $source ] = self::compact_source_definition( $sources[ $source ] );
			}
		}

		$winner = $normalized[ self::SOURCE_LICENSE ] ?? ( $normalized[ self::SOURCE_CATALOG ] ?? [] );

		if ( empty( $winner ) ) {
			return [];
		}

		$winner['extra']['sources'] = $normalized;

		return $winner;
	}

	/**
	 * Reduces a source contribution to fields used by server notices.
	 *
	 * @since TBD
	 *
	 * @param array $definition Notice definition.
	 *
	 * @return array
	 */
	private static function compact_source_definition( array $definition ): array {
		$compact = [];
		$fields  = array_merge(
			[
				'namespace',
				'slug',
				'message',
				'scope',
				'context',
				'condition',
				'users',
			],
			self::PASSTHROUGH_FIELDS
		);

		foreach ( $fields as $field ) {
			if ( array_key_exists( $field, $definition ) ) {
				$compact[ $field ] = $definition[ $field ];
			}
		}

		$extra = is_array( $definition['extra'] ?? null ) ? $definition['extra'] : [];

		$compact['extra'] = [
			'text_domain'   => $extra['text_domain'] ?? '',
			'text_domains'  => self::normalize_text_domains( is_array( $extra['text_domains'] ?? null ) ? $extra['text_domains'] : [] ),
			'version_match' => $extra['version_match'] ?? null,
			'min_version'   => $extra['min_version'] ?? null,
			'max_version'   => $extra['max_version'] ?? null,
			'content_hash'  => $extra['content_hash'] ?? '',
		];

		return $compact;
	}

	/**
	 * Builds a Foundation notice definition array from server notice data.
	 *
	 * @since 1.13.0
	 * @since TBD Reject malformed messages and preserve legacy text domains.
	 *
	 * @param string $text_domain Product text domain (used as notice namespace).
	 * @param int    $product_id  Product ID (used for placeholder resolution).
	 * @param string $notice_key  Notice key from the server definition.
	 * @param array  $notice      Notice data from the server.
	 * @param array  $text_domains Current and legacy product text domains.
	 *
	 * @return array Foundation notice definition.
	 */
	private static function build_notice_data( string $text_domain, int $product_id, string $notice_key, array $notice, array $text_domains = [] ): array {
		$raw_message = $notice['message'] ?? '';

		if ( ! is_scalar( $raw_message ) || '' === (string) $raw_message ) {
			return [];
		}

		$raw_message       = (string) $raw_message;
		$sanitized_message = self::sanitize_server_message( $raw_message );
		$message           = self::replace_placeholders( $sanitized_message, $product_id, $text_domain );
		$text_domains      = self::normalize_text_domains( array_merge( [ $text_domain ], $text_domains ) );

		$data = [
			'namespace' => $text_domain,
			'slug'      => self::SLUG_PREFIX . $notice_key,
			'message'   => $message,
			'scope'     => 'global',
			'context'   => [ 'site', 'ms_main', 'ms_subsite', 'ms_network' ],
			'condition' => self::class . '::check_version_match',
			'extra'     => [
				'text_domain'   => $text_domain,
				'text_domains'  => $text_domains,
				'version_match' => $notice['version_match'] ?? null,
				'min_version'   => $notice['min_version'] ?? null,
				'max_version'   => $notice['max_version'] ?? null,
				// Hash sanitized source text before placeholder resolution so product updates do not
				// reset dismissals. The first sync after this change may reset an old-basis hash once.
				'content_hash'  => md5( $sanitized_message ),
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
	 * @since TBD Require a product-bound notice's product to be installed.
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
		$text_domain   = $extra['text_domain'] ?? '';
		$text_domains  = is_array( $extra['text_domains'] ?? null ) ? $extra['text_domains'] : [];
		$installed     = null;

		if ( $text_domain ) {
			$text_domains[] = $text_domain;
			$text_domains   = self::normalize_text_domains( $text_domains );
			$installed      = CoreHelpers::get_installed_plugin_by_text_domain( $text_domains );

			if ( ! $installed ) {
				$text_domains = self::get_catalog_text_domains( (string) $text_domain, $text_domains );
				$installed    = CoreHelpers::get_installed_plugin_by_text_domain( $text_domains );
			}

			if ( ! $installed ) {
				return false;
			}
		}

		if ( null === $version_match && null === $min_version && null === $max_version ) {
			return true;
		}

		if ( ! $text_domain ) {
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
	 * Finds current and legacy text domains in the product catalog.
	 *
	 * @since TBD
	 *
	 * @param string $text_domain  Notice text domain.
	 * @param array  $text_domains Known text domains.
	 *
	 * @return string[]
	 */
	private static function get_catalog_text_domains( string $text_domain, array $text_domains ): array {
		try {
			$products = ProductManager::get_instance()->get_products_data();
		} catch ( Throwable $e ) {
			return $text_domains;
		}

		foreach ( $products as $product ) {
			if ( ! is_array( $product ) ) {
				continue;
			}

			$product_domains = self::get_product_text_domains( $product, (string) ( $product['text_domain'] ?? '' ) );

			if ( ! in_array( $text_domain, $product_domains, true ) ) {
				continue;
			}

			return array_values( array_unique( array_merge( $text_domains, $product_domains ) ) );
		}

		return $text_domains;
	}

	/**
	 * Resolves an array of email addresses to WordPress user IDs.
	 *
	 * Emails that don't match a user on this site are silently skipped.
	 *
	 * @since 1.13.0
	 * @since TBD Return resolved IDs in deterministic order.
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

		$ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
		sort( $ids, SORT_NUMERIC );

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
