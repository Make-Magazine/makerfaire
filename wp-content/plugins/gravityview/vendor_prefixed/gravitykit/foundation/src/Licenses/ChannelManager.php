<?php

namespace GravityKit\GravityView\Foundation\Licenses;

class ChannelManager {
	const OPTION_KEY = 'gk_product_channels';

	/**
	 * Version installed at the moment a product's channel was cleared, keyed by text domain.
	 *
	 * Suffix shape cannot prove a build came from a channel once the channel is gone: a commit hash
	 * is indistinguishable from a hand-installed one-off. Recording the version on the way out keeps
	 * the stable-recovery path working without treating every custom build as channel provenance.
	 *
	 * @since 1.31.0
	 */
	const EXIT_VERSION_OPTION_KEY = 'gk_product_channel_exit_versions';

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

		// Re-entering a channel makes any recorded exit meaningless. Left behind, it would push a
		// later sideload of that exact build back to stable.
		$this->clear_exit_version( $text_domain );
	}

	/**
	 * Removes a product's recorded channel-exit version.
	 *
	 * @since 1.31.0
	 *
	 * @param string $text_domain Product text domain.
	 *
	 * @return void
	 */
	public function clear_exit_version( string $text_domain ): void {
		$versions = get_option( self::EXIT_VERSION_OPTION_KEY, [] );

		if ( ! is_array( $versions ) || ! array_key_exists( $text_domain, $versions ) ) {
			return;
		}

		unset( $versions[ $text_domain ] );

		if ( $versions ) {
			update_option( self::EXIT_VERSION_OPTION_KEY, $versions );

			return;
		}

		delete_option( self::EXIT_VERSION_OPTION_KEY );
	}

	/**
	 * Clears a product's channel by removing it from the channels list.
	 *
	 * @since 1.13.0
	 *
	 * @param string $text_domain       Product text domain.
	 * @param string $installed_version (optional) Version installed at the time of the exit, recorded
	 *                                  so stable recovery still works once the channel is gone.
	 *
	 * @return void
	 */
	public function clear_channel( string $text_domain, string $installed_version = '' ): void {
		$channels = $this->get_channels();

		unset( $channels[ $text_domain ] );

		update_option( self::OPTION_KEY, $channels );

		// Only a channel-shaped build is provenance worth recording. A stale channel preference on a
		// site running plain stable would otherwise stamp that stable version as a channel exit, and
		// the recovery path would then authorize a downgrade to whatever stable the server names.
		if ( self::is_prerelease_version( $installed_version ) || self::is_custom_build_version( $installed_version ) ) {
			$this->record_exit_version( $text_domain, $installed_version );

			return;
		}

		$this->clear_exit_version( $text_domain );
	}

	/**
	 * Records the version installed when a product left a channel.
	 *
	 * @since 1.31.0
	 *
	 * @param string $text_domain Product text domain.
	 * @param string $version     Installed version at the time of the exit.
	 *
	 * @return void
	 */
	public function record_exit_version( string $text_domain, string $version ): void {
		$versions = get_option( self::EXIT_VERSION_OPTION_KEY, [] );

		if ( ! is_array( $versions ) ) {
			$versions = [];
		}

		$versions[ $text_domain ] = trim( $version );

		update_option( self::EXIT_VERSION_OPTION_KEY, $versions );
	}

	/**
	 * Checks whether a version is the one recorded when the product left a channel.
	 *
	 * @since 1.31.0
	 *
	 * @param string $text_domain Product text domain.
	 * @param string $version     Version to check.
	 *
	 * @return bool
	 */
	public function is_exit_version( string $text_domain, string $version ): bool {
		$version = trim( $version );

		if ( '' === $version ) {
			return false;
		}

		$versions = get_option( self::EXIT_VERSION_OPTION_KEY, [] );

		return is_array( $versions ) && ( $versions[ $text_domain ] ?? null ) === $version;
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
			$this->clear_channel( $text_domain, (string) ( $product['installed_version'] ?? '' ) );
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
	 * Checks whether a version is a build the server is currently serving on some channel.
	 *
	 * Suffix shape cannot answer this. A channel may serve builds identified by a commit hash
	 * (`3.3.2-b89efabaa`), which `is_prerelease_version()` deliberately rejects because an
	 * arbitrary suffix is indistinguishable from a hand-installed one-off. The server already
	 * states which build each channel serves, so an exact match against that is authoritative
	 * and needs no parsing or ordering.
	 *
	 * @since 1.31.0
	 *
	 * @param string $version  Version string to check.
	 * @param array  $channels Product channel map (channel name => channel data).
	 *
	 * @return bool
	 */
	public static function is_channel_build( string $version, array $channels ): bool {
		$version = trim( $version );

		if ( '' === $version ) {
			return false;
		}

		foreach ( $channels as $channel_name => $channel_data ) {
			if ( 'stable' === $channel_name || ! is_array( $channel_data ) ) {
				continue;
			}

			if ( ! empty( $channel_data['version'] ) && $version === $channel_data['version'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks whether an installed version is the same build a channel advertises.
	 *
	 * A channel's advertised version is often the human-facing one (`3.0.0-beta.2`) while the zip it
	 * serves carries the build hash too (`3.0.0-beta.2-148f3b382`). A plain `!==` then reports a
	 * pending update that installing can never satisfy, because the installed header never equals
	 * what was advertised.
	 *
	 * @since 1.31.0
	 *
	 * @param string $installed_version  Version currently installed.
	 * @param string $advertised_version Version the channel advertises.
	 *
	 * @return bool
	 */
	public static function is_same_channel_build( string $installed_version, string $advertised_version ): bool {
		$installed  = trim( $installed_version );
		$advertised = trim( $advertised_version );

		if ( '' === $installed || '' === $advertised ) {
			return false;
		}

		if ( $installed === $advertised ) {
			return true;
		}

		// Same build, with the commit hash appended to the advertised version.
		return (bool) preg_match( '/^' . preg_quote( $advertised, '/' ) . '-[0-9a-f]{7,40}$/i', $installed );
	}

	/**
	 * Checks whether a version looks like a build obtained from a channel rather than a stable release.
	 *
	 * Covers the three shapes a channel build can take: a semver pre-release naming the channel
	 * (`3.4.0-beta.1`), a build identified by a commit hash (`3.3.2-b89efabaa`), and an exact match
	 * against what a channel is serving right now. The hash case is why suffix shape alone is not
	 * enough -- the installed build is the PREVIOUS one, so it never equals the current version.
	 *
	 * @since 1.31.0
	 *
	 * @param string $version  Version string to check.
	 * @param array  $channels Product channel map (channel name => channel data).
	 *
	 * @return bool
	 */
	public static function is_channel_tracked_version( string $version, array $channels ): bool {
		$channel_names = array_keys( $channels );

		return self::is_prerelease_version( $version, $channel_names )
			|| self::is_custom_build_version( $version, $channel_names )
			|| self::is_channel_build( $version, $channels );
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
