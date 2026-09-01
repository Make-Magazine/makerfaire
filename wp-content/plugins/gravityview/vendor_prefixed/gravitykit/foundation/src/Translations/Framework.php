<?php

namespace GravityKit\GravityView\Foundation\Translations;

use GravityKit\GravityView\Foundation\Core as FoundationCore;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\Logger\Framework as LoggerFramework;
use Exception;

class Framework {
	const ID = 'gk-translations';

	const WP_LANG_DIR = WP_LANG_DIR . '/plugins';

	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Framework|null;
	 */
	private static $_instance = null;

	/**
	 * Logger class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var LoggerFramework
	 */
	private $_logger;

	/**
	 * Text domain for which translations are fetched.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $_text_domains = [];

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Framework
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Returns translation updater instance.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Renamed from get_T15s_updater(); return type updated to TranslationUpdater.
	 *
	 * @param string $text_domain Text domain.
	 *
	 * @return TranslationUpdater
	 */
	public function get_updater( $text_domain ) {
		return TranslationUpdater::get_instance( $text_domain );
	}

	/**
	 * Initializes Translations framework.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		if ( did_action( 'gk/foundation/translations/initialized' ) ) {
			return;
		}

		$this->_logger = LoggerFramework::get_instance();

		// Clear the translations cache when the site language changes so new locale translations are fetched immediately.
		add_action(
			'update_option_WPLANG',
			function () {
				WP::delete_site_transient( TranslationUpdater::TRANSLATIONS_TRANSIENT );
			}
		);

		$is_en = $this->is_en_locale();

		/**
		 * Disables downloading translations.
		 *
		 * @filter `gk/foundation/translations/disable-download`
		 *
		 * @since  1.2.6
		 *
		 * @param bool $disable_translations Whether to download translations. Default: false.
		 */
		$disable_download = apply_filters( 'gk/foundation/translations/disable-download', false );

		foreach ( FoundationCore::get_instance()->get_registered_plugins() as $plugin ) {
			$plugin_data = CoreHelpers::get_plugin_data( $plugin['plugin_file'] );

			if ( isset( $plugin_data['TextDomain'] ) ) {
				$this->_text_domains[] = $plugin_data['TextDomain'];

				if ( $is_en ) {
					continue;
				}

				if ( $this->can_install_languages() && ! $disable_download ) {
					// This will automatically try to install translations for all plugins when:
					// 1) The language is available on the translation platform and is not installed locally.
					// 2) The platform has updated translations.
					// Minimal to no performance impact if the 2 conditions are not met.
					$this->install_and_load_translations( $plugin_data['TextDomain'], get_user_locale() );
				} else {
					// If translations can't be installed due to permissions but were previously installed,
					// remap the location of the .mo file as it is stored in a folder suffixed with the blog ID.
					add_filter(
                        'load_textdomain_mofile',
                        function ( $mo_file, $text_domain ) use ( $plugin_data ) {
							/**
							 * Specifies the location of the .mo file.
							 *
							 * @filter `gk/foundation/translations/<text-domain>/mo-file`
							 *
							 * @since  1.2.6
							 *
							 * @param string $remapped_mo_file
							 */
							$remapped_mo_file = apply_filters(
                                "gk/foundation/translations/{$plugin_data['TextDomain']}/mo-file",
                                self::get_translation_file_name( $plugin_data['TextDomain'], get_user_locale() )
							);

							if ( $plugin_data['TextDomain'] === $text_domain && $remapped_mo_file ) {
								return $remapped_mo_file;
							}

							return $mo_file;
						},
                        10,
                        2
                    );

					$this->load_backend_translations( $plugin_data['TextDomain'], get_user_locale() );
				}
			}
		}

		if ( empty( $this->_text_domains ) ) {
			return;
		}

		add_action( 'gk/foundation/plugin-deactivated', [ $this, 'on_plugin_deactivation' ] );

		/**
		 * Fires when the class has finished initializing.
		 *
		 * @action `gk/foundation/translations/initialized`
		 *
		 * @since  1.0.0
		 *
		 * @param Framework $instance
		 */
		do_action( 'gk/foundation/translations/initialized', $this );
	}

	/**
	 * Checks of user has permissions to install languages.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function can_install_languages() {
		if ( CoreHelpers::is_cli() ) {
			return true;
		}

		/**
		 * Sets permission to install languages.
		 *
		 * @filter `gk/foundation/translations/permissions/can-install-languages`
		 *
		 * @since  1.0.0
		 *
		 * @param bool $can_install_languages Default: `install_languages` capability.
		 */
		return apply_filters( 'gk/foundation/translations/permissions/can-install-languages', current_user_can( 'install_languages' ) );
	}

	/**
	 * Downloads and installs translations from the GravityKit translation platform.
	 *
	 * @since 1.0.0
	 * @since 1.2.6 Method renamed from `install` to `install_and_load_translations`.
	 *
	 * @param string $text_domain Text domain.
	 * @param string $language    Language to install.
	 *
	 * @return void
	 */
	public function install_and_load_translations( $text_domain, $language ) {
		$current_user = wp_get_current_user();

		if ( ! $this->can_install_languages() ) {
			$this->_logger->error(
				sprintf(
					'User "%s" does not have permissions to install languages.',
					$current_user->user_login
				)
			);

			return;
		}

		try {
			$updater = $this->get_updater( $text_domain );

			$updater->install( $language );

			$translations = $updater->get_installed_translations( true );

			if ( isset( $translations[ $language ] ) ) {
				$this->load_backend_translations( $text_domain, $language );
			}
		} catch ( Exception $e ) {
			$this->_logger->error( $e->getMessage() );
		}
	}

	/**
	 * Loads and sets frontend and backend translations.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text_domain Text domain.
	 * @param string $language    (optional) Language to load. Default is site locale.
	 *
	 * @return void
	 */
	public function load_all_translations( $text_domain, $language = '' ) {
		$this->load_backend_translations( $text_domain, $language );
		$this->load_frontend_translations( $text_domain, $language );
	}

	/**
	 * Loads and sets backend translations.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text_domain Text domain.
	 * @param string $language    (optional) Language to load. Default is site locale.
	 *
	 * @return void
	 */
	public function load_backend_translations( $text_domain, $language = '' ) {
		if ( ! $language ) {
			$language = get_user_locale();
		}

		$mo_file = $this->get_translation_file_name( $text_domain, $language );

		if ( ! $mo_file ) {
			$this->_logger->notice(
				sprintf(
					'"%s" .mo translation file not found for "%s".',
					$text_domain,
					$language
				)
			);

			return;
		}

		// WP 6.5+ auto-discovers .l10n.php alongside .mo files via load_textdomain().
		load_textdomain( $text_domain, $mo_file );
	}

	/**
	 * Loads and sets frontend translations.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text_domain          Text domain.
	 * @param string $language             (optional) Language to load. Default is site locale.
	 * @param string $frontend_text_domain (optional) Frontend text domain if different from the backend text domain (e.g., plugin uses 'foo', but JS uses 'bar' for the same translations).
	 *
	 * @return void
	 */
	public function load_frontend_translations( $text_domain, $language = '', $frontend_text_domain = '' ) {
		if ( ! $language ) {
			$language = get_user_locale();
		}

		if ( $this->is_en_locale( $language ) ) {
			return;
		}

		$json_translations = $this->get_translation_file_name( $text_domain, $language, 'json' );

		if ( ! $json_translations ) {
			$this->_logger->notice(
				sprintf(
					'No %s.json translations file found for "%s" text domain.',
					$text_domain ?: $frontend_text_domain,
					$language
				)
			);

			return;
		}

		$json_translations = file_get_contents( $json_translations );

		// Validate JSON before interpolating into inline JS.
		if ( ! is_string( $json_translations ) || null === json_decode( $json_translations ) ) {
			$this->_logger->error(
				sprintf( 'Invalid JSON in translation file for "%s" text domain.', $text_domain )
			);

			return;
		}

		// Optionally override text domain if UI expects a different one.
		$text_domain = $frontend_text_domain ?: $text_domain;

		$js = $this->build_set_locale_data_js( $text_domain, $json_translations );

		// Attach to wp-i18n handle directly. This avoids race conditions with
		// defer/async script strategies and works from any hook.
		if ( wp_script_is( 'wp-i18n', 'registered' ) ) {
			wp_add_inline_script( 'wp-i18n', $js, 'after' );
		} else {
			// wp-i18n not registered yet (e.g., called during plugins_loaded).
			// Defer until scripts are being enqueued.
			$attach_js = function () use ( $js, &$attach_js ) {
				wp_add_inline_script( 'wp-i18n', $js, 'after' );

				remove_action( 'admin_enqueue_scripts', $attach_js, 1 );
				remove_action( 'wp_enqueue_scripts', $attach_js, 1 );
			};

			add_action( 'admin_enqueue_scripts', $attach_js, 1 );
			add_action( 'wp_enqueue_scripts', $attach_js, 1 );
		}
	}

	/**
	 * Builds the JS snippet that calls wp.i18n.setLocaleData().
	 *
	 * Includes a dedup guard to prevent multiple active products from
	 * overwriting each other's locale data for the same domain.
	 *
	 * @since 1.15.0
	 *
	 * @param string $text_domain       The text domain to register.
	 * @param string $json_translations Raw JSON string of translation data.
	 *
	 * @return string
	 */
	private function build_set_locale_data_js( $text_domain, $json_translations ) {
		$encoded_domain = wp_json_encode( $text_domain );

		return <<<JS
( function( domain, translations ) {
	if ( window.__gkTranslationsLoaded && window.__gkTranslationsLoaded[ domain ] ) {
		return;
	}
	window.__gkTranslationsLoaded = window.__gkTranslationsLoaded || {};
	window.__gkTranslationsLoaded[ domain ] = true;
	var localeData = translations.locale_data[ domain ] || translations.locale_data.messages;
	localeData[""].domain = domain;
	wp.i18n.setLocaleData( localeData, domain );
} )( {$encoded_domain}, {$json_translations});
JS;
	}

	/**
	 * Returns the translation filename for a given language.
	 *
	 * @param string $text_domain Text domain.
	 * @param string $language    Translation language (e.g. 'en_EN').
	 * @param string $extension   (optional) File extension. Default is 'mo'.
	 *
	 * @return string|null
	 */
	public static function get_translation_file_name( $text_domain, $language, $extension = 'mo' ) {
		$path = sprintf(
			'%s/%s-%s.%s',
			self::get_path_to_translations_folder(),
			$text_domain,
			$language,
			$extension
		);

		if ( ! file_exists( $path ) ) {
			return null;
		}

		return $path;
	}

	/**
	 * Returns path to folder where translations are stored.
	 *
	 * On single-site: wp-content/languages/plugins/gravitykit/
	 * On multisite:    wp-content/languages/plugins/gravitykit/{blog_id}/
	 *
	 * @since 1.2.6
	 * @since 1.15.0   Changed from numeric blog ID folder to gravitykit/ namespace.
	 *
	 * @return string
	 */
	public static function get_path_to_translations_folder() {
		$base = untrailingslashit( self::WP_LANG_DIR ) . '/gravitykit';

		if ( is_multisite() ) {
			return $base . '/' . get_current_blog_id();
		}

		return $base;
	}

	/**
	 * Deletes translations when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file Plugin file.
	 *
	 * @return void
	 */
	public function on_plugin_deactivation( $plugin_file ) {
		$plugin_data = CoreHelpers::get_plugin_data( $plugin_file );

		if ( $this->is_en_locale() || ! $this->can_install_languages() ) {
			return;
		}

		$files = glob(
            sprintf(
                '%s/%s-*',
                self::get_path_to_translations_folder(),
                $plugin_data['TextDomain']
            )
        );

		if ( empty( $files ) ) {
			return;
		}

		array_walk( $files, 'wp_delete_file' );

		// Only remove the directory if it's empty (other GravityKit plugins may still have files here).
		$remaining = glob( self::get_path_to_translations_folder() . '/*' );

		if ( ! empty( $remaining ) ) {
			return;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . '/wp-admin/includes/admin.php';
		}

		if ( ! WP_Filesystem() ) {
			return;
		}

		$wp_filesystem->rmdir( self::get_path_to_translations_folder() );
	}

	/**
	 * Checks whether the current locale is set to English language.
	 *
	 * @since 1.0.0
	 *
	 * @param string $locale (optional) Locale to check. Default is site locale.
	 *
	 * @return bool
	 */
	public function is_en_locale( $locale = '' ) {
		if ( ! $locale ) {
			$locale = get_user_locale();
		}

		// en_EN = en_US; en_GB and en_CA can have their own "translations" due to differences in spelling.
		return in_array( $locale, [ 'en_EN', 'en_US' ], true );
	}
}
