<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Licenses;

class ChannelManager {
	const OPTION_KEY = 'gk_product_channels';

	/**
	 * Class instance.
	 *
	 * @since 1.13.0
	 *
	 * @var ChannelManager|null
	 */
	private static $_instance = null;

	/**
	 * Returns class instance.
	 *
	 * @since 1.13.0
	 *
	 * @return ChannelManager
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Returns the channel for a product, or false if not on a non-default channel.
	 *
	 * @since 1.13.0
	 *
	 * @param string $text_domain Product text domain.
	 *
	 * @return string|false Channel name or false if not set.
	 */
	public function get_channel( string $text_domain ) {
		$channels = $this->get_channels();

		return $channels[ $text_domain ] ?? false;
	}

	/**
	 * Sets a product to a specific channel.
	 *
	 * @since 1.13.0
	 *
	 * @param string $text_domain Product text domain.
	 * @param string $channel     Channel name (e.g., 'beta', 'nightly').
	 *
	 * @return void
	 */
	public function set_channel( string $text_domain, string $channel ): void {
		$channels                 = $this->get_channels();
		$channels[ $text_domain ] = $channel;

		update_option( self::OPTION_KEY, $channels );
	}

	/**
	 * Clears a product's channel by removing it from the channels list.
	 *
	 * @since 1.13.0
	 *
	 * @param string $text_domain Product text domain.
	 *
	 * @return void
	 */
	public function clear_channel( string $text_domain ): void {
		$channels = $this->get_channels();

		unset( $channels[ $text_domain ] );

		update_option( self::OPTION_KEY, $channels );
	}

	/**
	 * Returns all products currently on a non-default channel.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	public function get_channels(): array {
		$channels = get_option( self::OPTION_KEY, [] );

		if ( ! is_array( $channels ) ) {
			return [];
		}

		return $channels;
	}

	/**
	 * Resets a product to the stable channel when the server no longer provides the active channel.
	 *
	 * @since 1.13.0
	 *
	 * @param string $text_domain Product text domain.
	 * @param array  $product     Product data array containing 'channels' map.
	 *
	 * @return void
	 */
	public function maybe_reset_channel( string $text_domain, array $product ): void {
		$channel = $this->get_channel( $text_domain );

		if ( ! $channel ) {
			return;
		}

		if ( empty( $product['channels'][ $channel ]['version'] ) ) {
			$this->clear_channel( $text_domain );
		}
	}

	/**
	 * Default pre-release identifiers used when a caller does not supply a product-specific
	 * channel list. Covers industry-standard semver pre-release labels; custom channels
	 * (e.g., `gov`, `enterprise`) should be passed in via `$channel_names` so they're
	 * recognised alongside these.
	 *
	 * @since 1.15.0
	 *
	 * @var string[]
	 */
	const DEFAULT_PRERELEASE_IDENTIFIERS = [ 'alpha', 'beta', 'rc', 'pre', 'nightly', 'dev' ];

	/**
	 * Checks whether a version string is a pre-release of a known channel.
	 *
	 * A pre-release is a version carrying a channel identifier suffix (optionally followed by
	 * `.N`). The channel identifiers are discovered from the product's channel list when
	 * provided; otherwise the defaults (`alpha`, `beta`, `rc`, `pre`, `nightly`, `dev`) apply.
	 * Pass the product's channel keys so custom channels (e.g. `gov`) are recognised here —
	 * hardcoded identifiers cannot scale to customer-specific distribution tracks.
	 *
	 * Versions with arbitrary suffixes (commit hashes, labels that aren't channel names like
	 * `2.56.1-foo`) are custom/dev builds, not pre-releases — they do not qualify.
	 *
	 * @since 1.13.0
	 *
	 * @param string                  $version       Version string to check.
	 * @param array<array-key, mixed> $channel_names (optional) Product channel names (e.g., from
	 *                                `array_keys( $product['channels'] )`). `stable` is
	 *                                automatically excluded.
	 *
	 * @return bool
	 */
	public static function is_prerelease_version( string $version, array $channel_names = [] ): bool {
		$version = trim( $version );

		if ( '' === $version ) {
			return false;
		}

		return (bool) preg_match( self::prerelease_identifiers_pattern( $channel_names ), $version );
	}

	/**
	 * Checks whether a version string is a custom/dev build of a base release.
	 *
	 * A custom build carries a suffix that isn't a recognised channel identifier — typically a
	 * short Git commit hash (`2.56.1-aaf4f6d`) or a custom label (`2.56.1-foo`). These are
	 * considered equivalent to the base version for update-comparison purposes (see
	 * `strip_build_suffix()`) but warrant a distinct UI badge so installers can tell the build
	 * apart from the officially-published release.
	 *
	 * @since 1.15.0
	 *
	 * @param string                  $version       Version string to check.
	 * @param array<array-key, mixed> $channel_names (optional) Product channel names; passed through to
	 *                                `is_prerelease_version()`.
	 *
	 * @return bool
	 */
	public static function is_custom_build_version( string $version, array $channel_names = [] ): bool {
		$version = trim( $version );

		if ( '' === $version || self::is_prerelease_version( $version, $channel_names ) ) {
			return false;
		}

		return (bool) preg_match( '/^v?\d+(\.\d+)*-.+$/', $version );
	}

	/**
	 * Strips a custom/dev-build suffix from a version, leaving the comparable base version.
	 *
	 * If the suffix matches a recognised channel identifier (from `$channel_names` or the
	 * defaults), it is PRESERVED so PHP's semver ordering still places `2.0.0-beta.1` before
	 * `2.0.0`. Otherwise the suffix is a custom label or commit hash and gets stripped so
	 * `2.56.1-foo` and `2.56.1-aaf4f6d` both normalise to `2.56.1`.
	 *
	 * @since 1.15.0
	 *
	 * @param string                  $version       Version string to strip.
	 * @param array<array-key, mixed> $channel_names Product channel names to treat as valid pre-release
	 *                                suffixes.
	 *
	 * @return string The version with any custom-build suffix removed.
	 */
	public static function strip_build_suffix( string $version, array $channel_names = [] ): string {
		$version = trim( $version );

		if ( '' === $version ) {
			return $version;
		}

		if ( preg_match( self::prerelease_identifiers_pattern( $channel_names ), $version ) ) {
			return $version;
		}

		return preg_replace( '/-.+$/', '', $version );
	}

	/**
	 * Builds a case-insensitive regex that matches a recognised pre-release suffix at the end
	 * of a version string.
	 *
	 * @since 1.15.0
	 *
	 * @param array<array-key, mixed> $channel_names Product-specific channel names to include.
	 *
	 * @return string Regex, or an empty string if no identifiers are known.
	 */
	private static function prerelease_identifiers_pattern( array $channel_names ): string {
		$channels = array_filter(
			$channel_names,
			static function ( $c ) {
				return is_string( $c ) && '' !== $c && 'stable' !== $c;
			}
		);

		$identifiers = array_unique( array_merge( self::DEFAULT_PRERELEASE_IDENTIFIERS, $channels ) );
		$escaped     = array_map( 'preg_quote', $identifiers );

		return '/-(' . implode( '|', $escaped ) . ')(\.\d+)?$/i';
	}
}
