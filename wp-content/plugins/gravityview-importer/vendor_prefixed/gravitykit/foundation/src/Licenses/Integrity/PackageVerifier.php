<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Licenses\Integrity;

use GravityKit\GravityImport\Foundation\Core;
use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Licenses\ChannelManager;
use GravityKit\GravityImport\Foundation\Licenses\ProductManager;
use GravityKit\GravityImport\Foundation\Logger\Framework as LoggerFramework;
use Throwable;
use WP_Error;

/**
 * Verifies GravityKit Ed25519 package signatures before WordPress unpacks the ZIP.
 *
 * @since 1.15.0
 */
final class PackageVerifier {
	const GK_HOSTS = [
		'store.gravitykit.com',
		'gravitykit.com',
	];

	const VERIFIED_VERSIONS_OPTION               = 'gk_verified_product_versions';
	const VERIFIED_VERSION_WILDCARD              = '*';
	const INTEGRITY_NOTICES_OPTION               = 'gk_integrity_notices';
	const SIGNATURE_VERIFICATION_DISABLED_OPTION = 'gk_signature_verification_disabled_since';

	const REVOCATION_URL              = 'https://verify.gravitykit.com/v1/packages/revocations';
	const REVOCATION_CACHE_KEY        = 'gk_revocation_manifest';
	const REVOCATION_LAST_GOOD_OPTION = 'gk_revocation_manifest_last_good';
	const REVOCATION_SEEN_KEYS_OPTION = 'gk_revocation_seen_keys';
	const REVOCATION_TTL_SUCCESS      = 3600;   // HOUR_IN_SECONDS — literal so the class loads in unit-test env.
	const REVOCATION_TTL_OUTAGE       = 1800;   // 30 * MINUTE_IN_SECONDS.
	const REVOCATION_TTL_COLD_START   = 300;    // 5 * MINUTE_IN_SECONDS.
	const REVOCATION_MAX_BODY_BYTES   = 262144;
	const REVOCATION_SIGNATURE_HEADER = 'x-gk-sig';
	const REVOCATION_SEEN_KEYS_CAP    = 1000;
	const DOWNLOAD_TOKEN_MAX_BYTES    = 512;

	/**
	 * Keys revoked at source level in this Foundation build.
	 *
	 * Empty by default. If a signing key is compromised, shipping a Foundation release with the
	 * key listed here makes revocation effective even before the remote manifest is reachable.
	 *
	 * @since 1.18.0
	 *
	 * @var string[]
	 */
	const BAKED_IN_REVOKED_KEYS = [];

	const REVOCATION_OUTAGE_OPTION                = 'gk_revocation_outage_state';
	const REVOCATION_OUTAGE_THROTTLE_SECONDS      = 300;
	const REVOCATION_OUTAGE_COLD_START_THRESHOLD  = 21600; // 6 * HOUR_IN_SECONDS — literal for unit-test env.
	const REVOCATION_OUTAGE_ESTABLISHED_THRESHOLD = 86400; // DAY_IN_SECONDS — literal for unit-test env.

	/**
	 * Whether the current download is a user-driven channel switch (beta ↔ stable).
	 *
	 * When true, rollback protection is bypassed because the target channel may legitimately
	 * carry a lower version than the installed one. Set by ProductManager prior to triggering
	 * the upgrader.
	 *
	 * @since 1.15.0
	 *
	 * @var bool
	 */
	public static $is_channel_switch = false;

	/**
	 * The channel that should be used for integrity lookup during a channel switch.
	 *
	 * Set by ProductManager immediately before triggering the upgrader for an intentional channel
	 * switch, cleared immediately after. Takes precedence over the product cache's `channel` field,
	 * which may lag — the user's stored channel preference is deliberately only persisted after a
	 * successful upgrade, so during a switch the cache still reports the OLD channel while the
	 * upgrader is downloading the NEW channel's ZIP.
	 *
	 * @since 1.15.0
	 *
	 * @var string|null
	 */
	public static $active_channel_override = null;

	/**
	 * Product ID expected for installs started through ProductManager.
	 *
	 * Store download URLs normally carry the product ID in their token. This extra context lets
	 * the verifier still fail closed if a caller supplies a GravityKit-managed package URL that
	 * cannot be parsed, without depending on browser/UI payloads.
	 *
	 * @since 1.18.0
	 *
	 * @var int|null
	 */
	public static $expected_product_id = null;

	/**
	 * Registers verification hooks and admin notices.
	 *
	 * @since 1.15.0
	 *
	 * @return void
	 */
	public static function init() {
		if ( self::signature_verification_disable_source() ) {
			self::log( 'warning', '[bypass_via_disable_flag] action=off source=' . self::signature_verification_disable_source() );
			self::record_signature_verification_disabled();

			// Clear any tracked outage state — verification is off, so the notice would be
			// misleading and any prior state is now stale.
			self::clear_outage_state();
		}

		add_filter( 'upgrader_pre_download', [ self::class, 'intercept_download' ], 5, 4 );
		add_action( 'admin_init', [ self::class, 'check_for_unverified_installs' ] );
		add_action( 'admin_init', [ self::class, 'register_outage_notice' ] );
		add_action( 'admin_init', [ self::class, 'register_signature_verification_disabled_notice' ] );
	}

	/**
	 * Returns the constant currently disabling signature verification, or an empty string.
	 *
	 * Only strict boolean-style values are accepted. A string like "false" must not silently
	 * disable verification.
	 *
	 * @since 1.18.0
	 *
	 * @return string
	 */
	private static function signature_verification_disable_source(): string {
		foreach ( [ 'GK_DISABLE_SIGNATURE_VERIFICATION', 'GK_ALLOW_UNSIGNED_PACKAGES' ] as $constant ) {
			if ( ! defined( $constant ) ) {
				continue;
			}

			$value = constant( $constant );

			if ( true === $value || 1 === $value || '1' === $value ) {
				return $constant;
			}
		}

		return '';
	}

	/**
	 * Records the first time signature verification was disabled on this site/network.
	 *
	 * @since 1.18.0
	 *
	 * @return void
	 */
	private static function record_signature_verification_disabled() {
		if ( ! function_exists( 'get_site_option' ) || ! function_exists( 'update_site_option' ) ) {
			return;
		}

		if ( get_site_option( self::SIGNATURE_VERIFICATION_DISABLED_OPTION, 0 ) ) {
			return;
		}

		update_site_option( self::SIGNATURE_VERIFICATION_DISABLED_OPTION, time() );
	}

	/**
	 * Registers an admin-visible warning when package signature verification is disabled.
	 *
	 * The `GK_DISMISS_SIGNATURE_DISABLED_WARNING` constant suppresses the admin
	 * notice for operators who have intentionally disabled verification (air-gapped sites,
	 * vendored builds, signing infra unreachable) and don't need the recurring nag. The
	 * underlying disable state is still recorded in `gk_signature_verification_disabled_since`
	 * so audit/incident-response tooling can still detect the degraded state.
	 *
	 * @since 1.18.0
	 *
	 * @return void
	 */
	public static function register_signature_verification_disabled_notice() {
		$source = self::signature_verification_disable_source();

		if ( '' === $source ) {
			return;
		}

		self::record_signature_verification_disabled();

		if ( defined( 'GK_DISMISS_SIGNATURE_DISABLED_WARNING' ) && GK_DISMISS_SIGNATURE_DISABLED_WARNING ) {
			return;
		}

		try {
			$notice_manager = Core::notices();
		} catch ( Throwable $e ) {
			return;
		}

		if ( ! is_object( $notice_manager ) || ! method_exists( $notice_manager, 'add_runtime' ) ) {
			return;
		}

		$notice_manager->add_runtime(
			[
				'namespace'    => 'gk-foundation',
				'slug'         => 'signature-verification-disabled',
				'message'      => esc_html(
					strtr(
						// translators: [constant] is the wp-config.php constant that disables signature verification.
						__( 'GravityKit package signature verification is disabled by [constant]. Product installs and updates are not being checked before WordPress unpacks them.', 'gk-foundation' ),
						[
							'[constant]' => $source,
						]
					)
				),
				'severity'     => 'error',
				'dismissible'  => false,
				'sticky'       => true,
				'capabilities' => [ 'update_plugins', 'manage_network_options' ],
				'screens'      => [
					'dashboard',
					'dashboard-network',
				],
				'context'      => [ 'site', 'ms_main', 'ms_subsite', 'ms_network' ],
			]
		);
	}

	/**
	 * Intercepts a plugin download, verifies the signed envelope, and returns the verified
	 * temp-file path on success or a `WP_Error` on any failure.
	 *
	 * @since 1.15.0
	 *
	 * @param mixed  $reply      Short-circuit value from earlier filters.
	 * @param string $package    The download URL.
	 * @param object $upgrader   WP_Upgrader instance.
	 * @param mixed  $hook_extra Extra upgrader arguments.
	 *
	 * @return bool|string|WP_Error Temp file path on success, WP_Error on failure, $reply to pass through.
	 */
	public static function intercept_download( $reply, $package, $upgrader, $hook_extra ) {
		if ( ! is_string( $package ) ) {
			return $reply;
		}

		$is_gravitykit_url      = self::is_gravitykit_url( $package );
		$hook_extra             = is_array( $hook_extra ) ? $hook_extra : [];
		$has_gravitykit_context = $is_gravitykit_url || self::has_gravitykit_package_context( $package, $hook_extra );

		if ( ! $has_gravitykit_context ) {
			return $reply;
		}

		if ( false !== $reply && ! is_string( $reply ) && ! is_wp_error( $reply ) ) {
			self::log(
				'error',
				'[pre_download_short_circuit] action=blocked type=' . gettype( $reply ),
				[
					'package' => $package,
				]
			);

			return new WP_Error(
				'gk_pre_download_short_circuit',
				esc_html__( 'Another component tried to bypass GravityKit download verification. The install was stopped before unpacking.', 'gk-foundation' )
			);
		}

		if ( self::signature_verification_disable_source() ) {
			self::log( 'warning', '[bypass_via_disable_flag] action=off source=' . self::signature_verification_disable_source() );
			self::record_signature_verification_disabled();

			return $reply;
		}

		if ( ! $is_gravitykit_url ) {
			self::log(
				'error',
				'[untrusted_package_host] action=blocked',
				[
					'package' => $package,
				]
			);

			return new WP_Error(
				'gk_untrusted_package_host',
				esc_html__( 'This GravityKit download URL is not from an approved GravityKit host. Retrying will not help — please contact GravityKit support.', 'gk-foundation' )
			);
		}

		if ( false !== $reply && is_wp_error( $reply ) ) {
			return $reply;
		}

		if ( ! SignatureVerifier::is_available() ) {
			self::log( 'error', '[sodium_unavailable] action=blocked' );

			return new WP_Error(
				'gk_sodium_unavailable',
				esc_html__( 'Cannot verify signature: libsodium is unavailable on this server. Update PHP or restore wp-includes/sodium_compat.', 'gk-foundation' )
			);
		}

		$temp_file = false !== $reply ? $reply : download_url( $package );

		if ( is_wp_error( $temp_file ) ) {
			return $temp_file;
		}

		if ( ! is_string( $temp_file ) || ! is_file( $temp_file ) ) {
			self::log(
				'error',
				'[pre_download_short_circuit] action=blocked reason=missing_file',
				[
					'package' => $package,
				]
			);

			return new WP_Error(
				'gk_pre_download_short_circuit',
				esc_html__( 'Another component provided an invalid download file before GravityKit could verify it. The install was stopped before unpacking.', 'gk-foundation' )
			);
		}

		$signature_data = self::resolve_signature_data( $package, $hook_extra );

		if ( ! $signature_data ) {
			self::log(
				'error',
				'[signature_missing] action=blocked',
				[
					'package' => $package,
				]
			);

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best-effort cleanup; may already be gone.
			@unlink( $temp_file );

			return new WP_Error(
				'gk_signature_missing',
				esc_html__( 'This download is missing its signature. Retrying will not help — please contact GravityKit support so we can investigate.', 'gk-foundation' )
			);
		}

		$sha256_hex = hash_file( 'sha256', $temp_file );

		if ( $sha256_hex !== $signature_data['sha256'] ) {
			self::log(
				'error',
				'[hash_mismatch] action=blocked',
				[
					'expected' => $signature_data['sha256'],
					'actual'   => $sha256_hex,
					'package'  => $package,
				]
			);

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best-effort cleanup; may already be gone.
			@unlink( $temp_file );

			return new WP_Error(
				'gk_signature_hash_mismatch',
				esc_html__( 'This download does not match what we published. Often a network glitch — retry once. If it fails again, stop and contact GravityKit support.', 'gk-foundation' )
			);
		}

		if ( self::is_key_revoked( $signature_data['signing_key_id'] ) ) {
			self::log(
				'error',
				'[key_revoked] action=blocked key_id=' . $signature_data['signing_key_id'],
				[
					'key_id'   => $signature_data['signing_key_id'],
					'filename' => $signature_data['filename'],
				]
			);

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best-effort cleanup; may already be gone.
			@unlink( $temp_file );

			return new WP_Error(
				'gk_key_revoked',
				esc_html__( 'This download was signed with a revoked key. Wait a few hours and try again — we are reissuing now. Contact GravityKit support only if it persists past a day.', 'gk-foundation' )
			);
		}

		$verified = SignatureVerifier::verify(
			$signature_data['signature'],
			SignatureVerifier::ENVELOPE_PURPOSE_PACKAGE,
			$signature_data['signing_key_id'],
			[
				'filename' => $signature_data['filename'],
				'sha256'   => $sha256_hex,
			]
		);

		if ( ! $verified ) {
			self::log(
				'error',
				'[signature_invalid] action=blocked key_id=' . $signature_data['signing_key_id'],
				[
					'filename' => $signature_data['filename'],
					'key_id'   => $signature_data['signing_key_id'],
				]
			);

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best-effort cleanup; may already be gone.
			@unlink( $temp_file );

			return new WP_Error(
				'gk_signature_invalid',
				esc_html__( "This download's signature is invalid, which suggests the file was tampered with after signing. Do not retry — contact GravityKit support.", 'gk-foundation' )
			);
		}

		// Best-effort hardening for shared hosts: make the verified temp file private and
		// confirm it still hashes to the signed bytes immediately before WordPress unpacks it.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- chmod may be unsupported on some filesystems; the hash check is the enforced control.
		@chmod( $temp_file, 0600 );

		$post_verify_sha256_hex = hash_file( 'sha256', $temp_file );

		if ( $post_verify_sha256_hex !== $sha256_hex ) {
			self::log(
				'error',
				'[toctou_mismatch] action=blocked',
				[
					'expected' => $sha256_hex,
					'actual'   => $post_verify_sha256_hex,
					'package'  => $package,
				]
			);

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best-effort cleanup; may already be gone.
			@unlink( $temp_file );

			return new WP_Error(
				'gk_toctou_mismatch',
				esc_html__( 'This download changed after verification and was stopped before WordPress unpacked it. Retry once; if it fails again, contact GravityKit support.', 'gk-foundation' )
			);
		}

		self::log(
			'info',
			'[package_verified] key_id=' . $signature_data['signing_key_id'],
			[
				'filename' => $signature_data['filename'],
				'key_id'   => $signature_data['signing_key_id'],
			]
		);

		$plugin_path      = $hook_extra['plugin'] ?? '';
		$incoming_version = '';

		if ( $plugin_path && function_exists( 'get_plugins' ) ) {
			$installed_plugins = get_plugins();

			if ( isset( $installed_plugins[ $plugin_path ] ) ) {
				$installed_version = $installed_plugins[ $plugin_path ]['Version'] ?? '';

				$update_transient = get_site_transient( 'update_plugins' );
				$update           = $update_transient->response[ $plugin_path ] ?? null;

				if ( is_object( $update ) ) {
					$incoming_version = $update->new_version ?? $update->version ?? '';
				}

				if ( $installed_version && $incoming_version && self::is_rollback( $incoming_version, $installed_version, self::$is_channel_switch ) ) {
					self::log(
						'warning',
						'[rollback_blocked] action=blocked',
						[
							'installed' => $installed_version,
							'incoming'  => $incoming_version,
						]
					);

					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best-effort cleanup; may already be gone.
					@unlink( $temp_file );

					return new WP_Error(
						'gk_rollback_blocked',
						esc_html__( 'Downgrading is not allowed. To install an older version, upload the plugin ZIP manually from the Plugins page.', 'gk-foundation' )
					);
				}
			}
		}

		// Keyed by product slug and storing the version string so check_for_unverified_installs()
		// can match its installed-version lookup. The reader iterates ProductManager products and
		// compares by slug → version; mis-keying by filename or storing sha256 would silently break
		// that path.
		//
		// When `$incoming_version` is unknown (direct ZIP uploads have no `update_plugins` entry),
		// store a `*` sentinel meaning "verified, no version assertion." Without this, the reader
		// would surface a false-positive "unverified install" notice on every direct upload of a
		// signed package because $verified_versions[$slug] would stay null.
		if ( ! empty( $signature_data['slug'] ) ) {
			$verified_versions                            = get_option( self::VERIFIED_VERSIONS_OPTION, [] );
			$verified_versions[ $signature_data['slug'] ] = '' !== $incoming_version ? $incoming_version : self::VERIFIED_VERSION_WILDCARD;

			update_option( self::VERIFIED_VERSIONS_OPTION, $verified_versions, false );
		}

		return $temp_file;
	}

	/**
	 * @since 1.15.0
	 *
	 * @param string $url The URL to check.
	 *
	 * @return bool True if the URL host is a GravityKit host.
	 */
	public static function is_gravitykit_url( string $url ): bool {
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
		$host   = wp_parse_url( $url, PHP_URL_HOST );

		if ( ! $host || 'https' !== strtolower( (string) $scheme ) ) {
			return false;
		}

		$host = strtolower( $host );

		foreach ( self::GK_HOSTS as $allowed_host ) {
			$suffix = '.' . $allowed_host;

			if ( $host === $allowed_host || substr( $host, -strlen( $suffix ) ) === $suffix ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns whether this download belongs to a GravityKit-managed product even when its URL
	 * host is not accepted. Used to fail closed if a Store/API/update path supplies a non-GK host.
	 *
	 * @since 1.18.0
	 *
	 * @param string $package    Download URL.
	 * @param array  $hook_extra Upgrader extra arguments.
	 *
	 * @return bool
	 */
	private static function has_gravitykit_package_context( string $package, array $hook_extra ): bool {
		if ( null !== self::extract_product_id_from_url( $package ) ) {
			return true;
		}

		if ( null !== self::$expected_product_id ) {
			return true;
		}

		$plugin_path = isset( $hook_extra['plugin'] ) && is_string( $hook_extra['plugin'] ) ? $hook_extra['plugin'] : '';

		if ( '' === $plugin_path ) {
			return false;
		}

		try {
			$products = ProductManager::get_instance()->get_products_data();
		} catch ( Throwable $e ) {
			return false;
		}

		foreach ( $products as $product ) {
			// Hidden third-party entries are catalog records for dependency resolution only; their
			// updates flow through the publisher's host, so a non-GK URL is the expected case.
			if ( ! empty( $product['hidden'] ) && ! empty( $product['third_party'] ) ) {
				continue;
			}

			if ( (string) ( $product['path'] ?? '' ) === $plugin_path ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines whether the incoming version is an in-channel downgrade.
	 *
	 * Channel switches (beta ↔ stable) are always allowed because the target channel may
	 * legitimately carry a lower version number. This also applies implicitly when the currently
	 * installed version is a pre-release (`3.0.0-beta.2`, `1.0.0-rc.1`, etc.) and the incoming
	 * version is stable: leaving the pre-release track is a channel transition, not a downgrade,
	 * even if PHP's `version_compare` reports the stable as numerically smaller. This is how
	 * `superseded_by` drives users from an abandoned beta back to the supported stable release.
	 *
	 * @since 1.15.0
	 *
	 * @param string $incoming_version  The version being installed.
	 * @param string $installed_version The currently installed version.
	 * @param bool   $is_channel_switch Whether this is a user-driven channel switch.
	 *
	 * @return bool True if this is a rollback that must be blocked.
	 */
	public static function is_rollback( string $incoming_version, string $installed_version, bool $is_channel_switch = false ): bool {
		if ( $is_channel_switch ) {
			return false;
		}

		// Leaving a pre-release for a stable version is a channel transition, not a rollback.
		if ( ChannelManager::is_prerelease_version( $installed_version ) && ! ChannelManager::is_prerelease_version( $incoming_version ) ) {
			return false;
		}

		return version_compare( $incoming_version, $installed_version, '<' );
	}

	/**
	 * Resolves the signing metadata for a package URL from the product cache or update transient.
	 *
	 * @since 1.15.0
	 *
	 * @param string $package    The package URL.
	 * @param array  $hook_extra Upgrader extra arguments.
	 *
	 * @return array|null Keys: signature, signing_key_id, sha256, filename, slug. Null if not resolvable.
	 */
	private static function resolve_signature_data( string $package, array $hook_extra ): ?array {
		$product_id = self::extract_product_id_from_url( $package );

		if ( ! $product_id ) {
			$plugin = $hook_extra['plugin'] ?? '';

			if ( $plugin ) {
				$transient = get_site_transient( 'update_plugins' );
				$update    = $transient->response[ $plugin ] ?? null;

				if ( is_object( $update ) && ! empty( $update->id ) ) {
					$product_id = (int) $update->id;
				}
			}
		}

		if ( ! $product_id && null !== self::$expected_product_id ) {
			$product_id = (int) self::$expected_product_id;
		}

		if ( ! $product_id ) {
			return null;
		}

		try {
			$products = ProductManager::get_instance()->get_products_data();
		} catch ( Throwable $e ) {
			return null;
		}

		foreach ( $products as $product ) {
			if ( (int) ( $product['id'] ?? 0 ) !== $product_id ) {
				continue;
			}

			// Resolve the integrity block atomically from the active channel, falling back to the
			// product level only if the channel has no signature at all. Mixing fields across
			// blocks (e.g. a channel's signature paired with the product-level filename) would
			// build an envelope whose filename does not match what was signed, and verification
			// would always fail.
			//
			// During an intentional channel switch, trust the override set by ProductManager —
			// the product cache's `channel` field may still report the old channel because the
			// user's preference is deliberately persisted only after a successful upgrade.
			$active_key = self::$active_channel_override ?: ( $product['channel'] ?: 'stable' );
			$channel    = $product['channels'][ $active_key ] ?? [];

			$source = ! empty( $channel['signature'] ) ? $channel : $product;

			$signature = $source['signature'] ?? '';
			$filename  = $source['filename'] ?? '';

			if ( empty( $signature ) || empty( $filename ) ) {
				return null;
			}

			return [
				'signature'      => $signature,
				'signing_key_id' => $source['signing_key_id'] ?? '',
				'sha256'         => $source['sha256'] ?? '',
				'filename'       => $filename,
				'slug'           => $product['slug'] ?? '',
			];
		}

		return null;
	}

	/**
	 * Extracts the product ID from a Store API signed download URL.
	 *
	 * Store API download tokens are `{base64_payload}.{hmac}` where the payload decodes to a
	 * JSON object with a `p` key carrying the product ID.
	 *
	 * @since 1.15.0
	 *
	 * @param string $url Download URL.
	 *
	 * @return int|null Product ID, or null if not extractable.
	 */
	private static function extract_product_id_from_url( string $url ): ?int {
		$path = wp_parse_url( $url, PHP_URL_PATH );

		if ( ! $path ) {
			return null;
		}

		$token = basename( $path );

		if ( strlen( $token ) > self::DOWNLOAD_TOKEN_MAX_BYTES ) {
			return null;
		}

		$parts = explode( '.', $token, 2 );

		if ( count( $parts ) < 2 ) {
			return null;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Decoding the Store API download token payload to extract the product id.
		$payload = json_decode( base64_decode( $parts[0] ), true );

		if ( ! is_array( $payload ) || empty( $payload['p'] ) ) {
			return null;
		}

		return (int) $payload['p'];
	}

	/**
	 * Records post-install notices for products whose installed version does not match the
	 * version last verified during download.
	 *
	 * @since 1.15.0
	 *
	 * @return void
	 */
	public static function check_for_unverified_installs() {
		try {
			$products = ProductManager::get_instance()->get_products_data();
		} catch ( Throwable $e ) {
			return;
		}

		$verified_versions = get_option( self::VERIFIED_VERSIONS_OPTION, [] );
		$notices           = [];

		foreach ( $products as $product ) {
			if ( ! $product['installed'] || $product['third_party'] ) {
				continue;
			}

			$slug              = $product['slug'];
			$installed_version = $product['installed_version'];

			if ( empty( $installed_version ) ) {
				continue;
			}

			$verified_version = $verified_versions[ $slug ] ?? null;

			// No record means we have nothing to compare against — treat as unknown, not tampered.
			if ( null === $verified_version ) {
				continue;
			}

			// `*` is the wildcard sentinel for direct ZIP uploads where the version isn't known.
			if ( $verified_version === $installed_version || self::VERIFIED_VERSION_WILDCARD === $verified_version ) {
				continue;
			}

			$notices[] = [
				'slug'    => $slug,
				'name'    => $product['name'],
				'version' => $installed_version,
			];
		}

		update_option( self::INTEGRITY_NOTICES_OPTION, $notices, false );
	}

	/**
	 * Returns true iff `$key_id` is in the currently-cached revocation manifest. On fetch or
	 * verify failure, falls back to the last-known-good durable copy (asymmetric fail-closed).
	 *
	 * @since 1.15.0
	 *
	 * @param string $key_id Key identifier to check.
	 *
	 * @return bool
	 */
	public static function is_key_revoked( string $key_id ): bool {
		if ( in_array( $key_id, self::baked_in_revoked_keys(), true ) ) {
			return true;
		}

		if ( in_array( $key_id, self::get_seen_revoked_keys(), true ) ) {
			return true;
		}

		if ( ! function_exists( 'wp_remote_get' ) ) {
			return false;
		}

		/**
		 * Filters the revocation feed URL.
		 *
		 * Used by local/staging environments to point at a test worker. Production sites should
		 * never override this — a rogue URL defeats revocation enforcement on cold start because
		 * the durable copy is read without re-verification.
		 *
		 * @since 1.17.0
		 *
		 * @private
		 *
		 * @param string $url Default revocation feed URL.
		 */
		$revocation_url = (string) apply_filters( 'gk/foundation/integrity/revocation-url', self::REVOCATION_URL );

		// Use Foundation's WP::*_site_transient wrappers (raw SQL, request-local memo) instead of
		// core's get/set_site_transient. Core routes through the persistent object cache when one
		// is installed, where a Redis-under-memory-pressure eviction would silently drop the manifest
		// mid-hour and force a re-fetch — defeating the asymmetric fail-closed property when the
		// re-fetch lands on a forged response. The DB-backed wrapper is the persistence layer the
		// fail-closed design was always assuming.
		$cached = WP::get_site_transient( self::REVOCATION_CACHE_KEY );

		if ( false !== $cached ) {
			return self::manifest_lists_key( $cached, $key_id );
		}

		$response = wp_remote_get(
			$revocation_url,
			[
				'timeout'             => 5,
				'sslverify'           => true,
				'limit_response_size' => self::REVOCATION_MAX_BODY_BYTES,
			]
		);

		$fetch_failed  = is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response );
		$verify_failed = false;
		$body          = '';

		if ( ! $fetch_failed ) {
			$body = wp_remote_retrieve_body( $response );

			if ( strlen( $body ) > self::REVOCATION_MAX_BODY_BYTES ) {
				self::log(
					'error',
					'[revocation_body_oversized] action=reject',
					[
						'received_bytes' => strlen( $body ),
						'limit_bytes'    => self::REVOCATION_MAX_BODY_BYTES,
					]
				);

				$verify_failed = true;
			} else {
				$verify_failed = ! self::verify_revocation_signature( $body, $response );
			}
		}

		if ( $fetch_failed || $verify_failed ) {
			$prior = (string) get_site_option( self::REVOCATION_LAST_GOOD_OPTION, '' );

			if ( '' !== $prior ) {
				self::log(
					'error',
					$verify_failed
						? '[revocation_manifest_invalid] action=preserve_previous'
						: '[revocation_manifest_fetch_failed] action=preserve_previous'
				);

				WP::set_site_transient( self::REVOCATION_CACHE_KEY, $prior, self::REVOCATION_TTL_OUTAGE );

				self::record_revocation_outcome( 'failure', 'preserve_previous' );

				self::merge_seen_revoked_keys_from_manifest( $prior );

				return self::manifest_lists_key( $prior, $key_id );
			}

			self::log(
				'error',
				$verify_failed
					? '[revocation_manifest_invalid] action=fallback_empty'
					: '[revocation_manifest_fetch_failed] action=fallback_empty'
			);

			WP::set_site_transient( self::REVOCATION_CACHE_KEY, '{"revoked_keys":[]}', self::REVOCATION_TTL_COLD_START );

			self::record_revocation_outcome( 'failure', 'fallback_empty' );

			return false;
		}

		// autoload=false on single-site keeps the manifest body out of wp_load_alloptions.
		$durable_ok = is_multisite()
			? (bool) update_site_option( self::REVOCATION_LAST_GOOD_OPTION, $body )
			: (bool) update_option( self::REVOCATION_LAST_GOOD_OPTION, $body, false );

		// update_option/update_site_option returns false on no-op writes too — distinguish from
		// real failure by reading back, otherwise we'd log a false alarm on every steady-state refresh.
		if ( ! $durable_ok ) {
			$existing = is_multisite()
				? (string) get_site_option( self::REVOCATION_LAST_GOOD_OPTION, '' )
				: (string) get_option( self::REVOCATION_LAST_GOOD_OPTION, '' );

			if ( $existing !== $body ) {
				self::log( 'warning', '[durable_write_failed] action=continue body_len=' . strlen( $body ) );
			}
		}

		self::merge_seen_revoked_keys_from_manifest( $body );

		WP::set_site_transient( self::REVOCATION_CACHE_KEY, $body, self::REVOCATION_TTL_SUCCESS );

		self::record_revocation_outcome( 'success' );

		return self::manifest_lists_key( $body, $key_id );
	}

	/**
	 * Returns source-baked revoked keys for this Foundation build.
	 *
	 * @since 1.18.0
	 *
	 * @return string[]
	 */
	private static function baked_in_revoked_keys(): array {
		return self::BAKED_IN_REVOKED_KEYS;
	}

	/**
	 * Records the outcome of a revocation feed fetch attempt.
	 *
	 * On success, clears any tracked outage state.
	 * On failure, opens or extends an outage record. The outage record drives the admin notice
	 * registered by `register_outage_notice()` once the relevant time threshold is crossed.
	 *
	 * The `GK_DISMISS_REVOCATION_OUTAGE_WARNING` constant suppresses the registration side of
	 * outage tracking but never blocks the recovery path — flipping the constant off must
	 * cleanly recover.
	 *
	 * @since 1.17.0
	 *
	 * @param string      $outcome Either `'success'` or `'failure'`.
	 * @param string|null $mode    `'preserve_previous'` or `'fallback_empty'`. Required on failure.
	 *
	 * @return void
	 */
	private static function record_revocation_outcome( string $outcome, ?string $mode = null ) {
		if ( 'success' === $outcome ) {
			self::clear_outage_state();

			return;
		}

		if ( defined( 'GK_DISMISS_REVOCATION_OUTAGE_WARNING' ) && GK_DISMISS_REVOCATION_OUTAGE_WARNING ) {
			return;
		}

		if ( 'preserve_previous' !== $mode && 'fallback_empty' !== $mode ) {
			return;
		}

		self::record_outage_failure( $mode );
	}

	/**
	 * Persists or extends an outage record. Throttled to one write per
	 * `REVOCATION_OUTAGE_THROTTLE_SECONDS` to avoid hammering the option store on rapid retries.
	 *
	 * `started_at` is set once per outage and preserved across mode changes within the same
	 * outage; `last_failure_at` and `last_check_at` advance on each call.
	 *
	 * @since 1.17.0
	 *
	 * @param string $mode `'preserve_previous'` or `'fallback_empty'`.
	 *
	 * @return void
	 */
	private static function record_outage_failure( string $mode ) {
		$existing = self::get_outage_state();
		$now      = time();

		if ( $existing && ( $now - (int) ( $existing['last_check_at'] ?? 0 ) ) < self::REVOCATION_OUTAGE_THROTTLE_SECONDS && $existing['mode'] === $mode ) {
			return;
		}

		$state = [
			'mode'            => $mode,
			'started_at'      => $existing ? (int) $existing['started_at'] : $now,
			'last_failure_at' => $now,
			'last_check_at'   => $now,
		];

		self::write_outage_state( $state );
	}

	/**
	 * Returns the current outage state, or null if no outage is tracked.
	 *
	 * @since 1.17.0
	 *
	 * @return array|null
	 */
	private static function get_outage_state(): ?array {
		$state = is_multisite()
			? get_site_option( self::REVOCATION_OUTAGE_OPTION, null )
			: get_option( self::REVOCATION_OUTAGE_OPTION, null );

		if ( ! is_array( $state ) || empty( $state['started_at'] ) || empty( $state['mode'] ) ) {
			return null;
		}

		return $state;
	}

	/**
	 * Persists the outage state. Network-scoped on multisite, non-autoloaded on single-site.
	 *
	 * @since 1.17.0
	 *
	 * @param array $state Outage state record.
	 *
	 * @return void
	 */
	private static function write_outage_state( array $state ) {
		if ( is_multisite() ) {
			update_site_option( self::REVOCATION_OUTAGE_OPTION, $state );

			return;
		}

		// Anchor autoload=false on first insert.
		if ( false === get_option( self::REVOCATION_OUTAGE_OPTION, false ) ) {
			add_option( self::REVOCATION_OUTAGE_OPTION, '', '', false );
		}

		update_option( self::REVOCATION_OUTAGE_OPTION, $state, false );
	}

	/**
	 * Clears any tracked outage state. Idempotent.
	 *
	 * @since 1.17.0
	 *
	 * @return void
	 */
	private static function clear_outage_state() {
		if ( is_multisite() ) {
			delete_site_option( self::REVOCATION_OUTAGE_OPTION );

			return;
		}

		delete_option( self::REVOCATION_OUTAGE_OPTION );
	}

	/**
	 * Registers an admin notice if the revocation feed has been failing long enough to warrant
	 * customer attention.
	 *
	 * Two tiers, each with a different threshold and severity:
	 *
	 *   - `fallback_empty` (cold start, no permanent backup ever fetched) → 6 hours →
	 *     warning, non-dismissible (suppressible only via GK_DISMISS_REVOCATION_OUTAGE_WARNING).
	 *     This is the only state where revocation is effectively disabled, so the customer
	 *     needs to know.
	 *   - `preserve_previous` (durable backup serving fine, just stale data) → 24 hours →
	 *     info, dismissible. No active risk; just informational.
	 *
	 * Notice slug carries the outage's `started_at` so dismissals are scoped to the current
	 * outage incident — a customer who dismisses the 24-hour info notice in 2026 will still
	 * see a fresh notice for a separate outage in 2027.
	 *
	 * Notice is removed automatically when the outage state clears (RuntimeNotice's condition
	 * callback returns false on next admin pageload after recovery).
	 *
	 * @since 1.17.0
	 *
	 * @return void
	 */
	public static function register_outage_notice() {
		if ( defined( 'GK_DISMISS_REVOCATION_OUTAGE_WARNING' ) && GK_DISMISS_REVOCATION_OUTAGE_WARNING ) {
			return;
		}

		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		$state = self::get_outage_state();

		if ( ! $state ) {
			return;
		}

		$elapsed = time() - (int) $state['started_at'];
		$mode    = (string) $state['mode'];

		if ( 'fallback_empty' === $mode && $elapsed < self::REVOCATION_OUTAGE_COLD_START_THRESHOLD ) {
			return;
		}

		if ( 'preserve_previous' === $mode && $elapsed < self::REVOCATION_OUTAGE_ESTABLISHED_THRESHOLD ) {
			return;
		}

		if ( ! function_exists( 'apply_filters' ) ) {
			return;
		}

		if ( ! is_admin() ) {
			return;
		}

		$is_cold_start = 'fallback_empty' === $mode;
		$started_at    = (int) $state['started_at'];

		try {
			$notice_manager = Core::notices();
		} catch ( Throwable $e ) {
			return;
		}

		if ( ! is_object( $notice_manager ) || ! method_exists( $notice_manager, 'add_runtime' ) ) {
			return;
		}

		$notice_manager->add_runtime(
			[
				'namespace'    => 'gk-foundation',
				// Slug includes started_at so each outage incident has its own dismissal scope.
				'slug'         => 'revocation-outage-' . $started_at,
				'message'      => self::build_outage_notice_message( $is_cold_start ),
				'severity'     => $is_cold_start ? 'warning' : 'info',
				'dismissible'  => ! $is_cold_start,
				'sticky'       => $is_cold_start,
				'capabilities' => [ 'update_plugins', 'manage_network_options' ],
				'screens'      => [
					'dashboard',          // WP Dashboard.
					'dashboard-network',  // Network admin Dashboard.
				],
				'context'      => [ 'site', 'ms_main', 'ms_subsite', 'ms_network' ],
				'condition'    => static function () use ( $is_cold_start, $started_at ) {
					$current = self::get_outage_state();

					if ( ! $current || (int) $current['started_at'] !== $started_at ) {
						return false;
					}

					$elapsed = time() - (int) $current['started_at'];
					$mode    = (string) $current['mode'];

					if ( $is_cold_start ) {
						return 'fallback_empty' === $mode && $elapsed >= self::REVOCATION_OUTAGE_COLD_START_THRESHOLD;
					}

					return 'preserve_previous' === $mode && $elapsed >= self::REVOCATION_OUTAGE_ESTABLISHED_THRESHOLD;
				},
			]
		);
	}

	/**
	 * Builds the human-readable notice body. Customer-facing language; written by the
	 * tech-writer agent for the audience of WordPress site administrators.
	 *
	 * @since 1.17.0
	 *
	 * @param bool $is_cold_start Whether this is the cold-start (no-backup) variant.
	 *
	 * @return string
	 */
	private static function build_outage_notice_message( bool $is_cold_start ): string {
		$replacements = [
			'[link]'  => '<a href="' . esc_url( 'https://www.gravitykit.com/?p=886711' ) . '" target="_blank" rel="noopener noreferrer">',
			'[/link]' => '</a>',
		];

		$template = $is_cold_start
			? __( 'GravityKit is temporarily unable to fully verify the integrity of product installations and updates. [link]Learn more[/link].', 'gk-foundation' )
			: __( 'GravityKit has not fully verified the integrity of product installations and updates in more than 24 hours. [link]Learn more[/link].', 'gk-foundation' );

		return wp_kses_post( strtr( $template, $replacements ) );
	}

	/**
	 * Returns true iff the given JSON manifest lists the key ID as revoked.
	 *
	 * @since 1.17.0
	 *
	 * @param string $manifest_json The cached or freshly-fetched manifest body.
	 * @param string $key_id        Key identifier to check.
	 *
	 * @return bool
	 */
	private static function manifest_lists_key( string $manifest_json, string $key_id ): bool {
		return in_array( $key_id, self::manifest_revoked_keys( $manifest_json ), true );
	}

	/**
	 * Extracts valid revoked key IDs from a manifest body.
	 *
	 * @since 1.18.0
	 *
	 * @param string $manifest_json Manifest JSON body.
	 *
	 * @return string[]
	 */
	private static function manifest_revoked_keys( string $manifest_json ): array {
		$manifest = json_decode( $manifest_json, true );

		if ( ! is_array( $manifest['revoked_keys'] ?? null ) ) {
			return [];
		}

		$keys = [];

		foreach ( $manifest['revoked_keys'] as $key_id ) {
			if ( is_string( $key_id ) && SignatureVerifier::is_valid_key_id( $key_id ) ) {
				$keys[] = $key_id;
			}
		}

		return array_slice( array_values( array_unique( $keys ) ), 0, self::REVOCATION_SEEN_KEYS_CAP );
	}

	/**
	 * Returns every revoked signing key this site has ever seen in a verified manifest.
	 *
	 * @since 1.18.0
	 *
	 * @return string[]
	 */
	private static function get_seen_revoked_keys(): array {
		if ( ! function_exists( 'get_site_option' ) ) {
			return [];
		}

		$stored = get_site_option( self::REVOCATION_SEEN_KEYS_OPTION, [] );

		if ( ! is_array( $stored ) ) {
			return [];
		}

		$keys = [];

		foreach ( $stored as $key_id ) {
			if ( is_string( $key_id ) && SignatureVerifier::is_valid_key_id( $key_id ) ) {
				$keys[] = $key_id;
			}
		}

		return array_slice( array_values( array_unique( $keys ) ), 0, self::REVOCATION_SEEN_KEYS_CAP );
	}

	/**
	 * Persists revoked key IDs from a verified or already-trusted manifest.
	 *
	 * This intentionally stores only key IDs, not manifest versions. The goal is simple stickiness:
	 * once a customer site has seen a key revoked, an older signed manifest cannot un-revoke it.
	 *
	 * @since 1.18.0
	 *
	 * @param string $manifest_json Verified or trusted manifest JSON body.
	 *
	 * @return void
	 */
	private static function merge_seen_revoked_keys_from_manifest( string $manifest_json ) {
		$incoming = self::manifest_revoked_keys( $manifest_json );

		if ( [] === $incoming ) {
			return;
		}

		$merged = array_slice( array_values( array_unique( array_merge( self::get_seen_revoked_keys(), $incoming ) ) ), 0, self::REVOCATION_SEEN_KEYS_CAP );

		if ( is_multisite() ) {
			update_site_option( self::REVOCATION_SEEN_KEYS_OPTION, $merged );

			return;
		}

		update_option( self::REVOCATION_SEEN_KEYS_OPTION, $merged, false );
	}

	/**
	 * Verifies the revocation manifest's envelope signature.
	 *
	 * The response carries `X-GK-Sig: {key_id}:{hex_sig}`. The verifier rebuilds the envelope
	 * `gk.sig.v1.rev:ed25519:{key_id}:{manifest_bytes}` and checks the signature against
	 * the KeyRing entry for `{key_id}` scoped to purpose `root`.
	 *
	 * @since 1.15.0
	 *
	 * @param string $body     Response body (the JSON manifest, as served).
	 * @param array  $response WP HTTP response array.
	 *
	 * @return bool True iff the envelope validates.
	 */
	private static function verify_revocation_signature( string $body, $response ): bool {
		$header = wp_remote_retrieve_header( $response, self::REVOCATION_SIGNATURE_HEADER );

		if ( is_array( $header ) ) {
			if ( 1 !== count( $header ) ) {
				return false;
			}

			$header = reset( $header );
		}

		if ( ! is_string( $header ) || '' === $header ) {
			return false;
		}

		$parts = explode( ':', $header, 2 );

		if ( count( $parts ) !== 2 ) {
			return false;
		}

		[ $key_id, $hex_sig ] = $parts;

		if ( ! SignatureVerifier::is_valid_key_id( $key_id ) ) {
			return false;
		}

		if ( SignatureVerifier::verify_with_raw_body( $hex_sig, SignatureVerifier::ENVELOPE_PURPOSE_ROOT, $key_id, $body ) ) {
			return true;
		}

		// Compatibility path for pre-1.18 publishers that signed the canonical body but served
		// harmlessly non-canonical bytes. Keep it narrow: `revoked_keys` is the only field the
		// verifier acts on, and duplicate instances of that key are exactly the smuggling case
		// raw-body verification was added to close.
		if ( 1 !== preg_match_all( '/"revoked_keys"\s*:/', $body ) ) {
			return false;
		}

		$manifest = json_decode( $body, true );

		if ( ! is_array( $manifest ) ) {
			return false;
		}

		return SignatureVerifier::verify( $hex_sig, SignatureVerifier::ENVELOPE_PURPOSE_ROOT, $key_id, $manifest );
	}

	/**
	 * Routes a log message through Foundation's logger if it is already loaded, prefixed
	 * with a stable `[gk-sig]` tag that support engineers can grep for.
	 *
	 * @since 1.15.0
	 *
	 * @param string $level   Log level: 'info', 'warning', or 'error'.
	 * @param string $message Log message (written after the `[gk-sig]` prefix).
	 * @param array  $context Structured context.
	 *
	 * @return void
	 */
	private static function log( string $level, string $message, array $context = [] ) {
		if ( ! class_exists( LoggerFramework::class, false ) ) {
			return;
		}

		try {
			$logger = LoggerFramework::get_instance();

			if ( method_exists( $logger, $level ) ) {
				$logger->$level( '[gk-sig] ' . $message, $context );
			}
		} catch ( Throwable $e ) {
			// Logger unavailable; signature verification must not fail because logging did.
			unset( $e );
		}
	}
}
