<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Translations;

use Exception;
use GravityKit\GravityEdit\Foundation\Helpers\WP;
use GravityKit\GravityEdit\Foundation\Logger\Framework as LoggerFramework;

/**
 * Allows downloading and installing translations from the GravityKit translation platform.
 *
 * @since 1.0.0
 * @since 1.15.0   Rewritten: removed WordPress upgrader hooks and PO-header parsing; uses metadata JSON for freshness checks.
 *
 * @see   https://translate.gravitykit.com
 */
class TranslationUpdater {
	const TRANSLATIONS_TRANSIENT = 'gk-translations-registry';

	const TRANSLATIONS_TRANSIENT_EXPIRY = 24 * HOUR_IN_SECONDS;

	const API_LANGUAGE_PACKAGES_URL = 'https://translate.gravitykit.com/packages.json';

	const METADATA_FILE = 'gravitykit-translations-meta.json';

	/**
	 * The plugin text domain.
	 *
	 * @since 1.2.6
	 *
	 * @var string
	 */
	private $_text_domain;

	/**
	 * Cached translation data for all GravityKit plugins.
	 *
	 * @since 1.0.0
	 *
	 * @var null|object
	 */
	private $_all_translations;

	/**
	 * Instances of this class keyed by plugin text domain.
	 *
	 * @since 1.15.0
	 *
	 * @var TranslationUpdater[]
	 */
	private static $_instances = [];

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Simplified to only set text domain.
	 *
	 * @param string $text_domain The plugin text domain.
	 */
	private function __construct( $text_domain ) {
		$this->_text_domain = $text_domain;
	}

	/**
	 * Returns an instance of this class for the given text domain.
	 *
	 * @since 1.0.0
	 * @since 1.2.6 Removed $translations_path parameter.
	 *
	 * @param string $text_domain The plugin text domain.
	 *
	 * @return TranslationUpdater
	 */
	public static function get_instance( $text_domain ) {
		if ( empty( self::$_instances[ $text_domain ] ) ) {
			self::$_instances[ $text_domain ] = new self( $text_domain );
		}

		return self::$_instances[ $text_domain ];
	}

	/**
	 * Installs translations for a given locale if an update is available.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Simplified to single-locale install with metadata-based freshness check.
	 *
	 * @param string $locale Locale for which to install the translation.
	 *
	 * @throws Exception When no translations are found or installation fails.
	 *
	 * @return void
	 */
	public function install( $locale = '' ) {
		$translations = $this->get_plugin_translations()['translations'];

		if ( empty( $translations ) ) {
			throw new Exception(
				sprintf( '%s(): No translations found for %s.', __METHOD__, $this->_text_domain )
			);
		}

		foreach ( $translations as $translation ) {
			if ( $locale && $locale !== $translation['language'] ) {
				continue;
			}

			if ( ! $this->needs_update( $translation ) ) {
				continue;
			}

			$this->install_translation( $translation );

			if ( $locale ) {
				return;
			}
		}
	}

	/**
	 * Gets the translation data for the current plugin.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Simplified to return translations array directly.
	 *
	 * @throws Exception When remote data cannot be fetched.
	 *
	 * @return array
	 */
	public function get_plugin_translations() {
		$this->set_all_translations();

		return $this->_all_translations->projects[ $this->_text_domain ] ?? [ 'translations' => [] ];
	}

	/**
	 * Retrieves and caches translation data from the remote API.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Simplified error handling; renamed transient key.
	 *
	 * @return void
	 */
	public function set_all_translations() {
		if ( is_object( $this->_all_translations ) ) {
			return;
		}

		$this->_all_translations = WP::get_site_transient( self::TRANSLATIONS_TRANSIENT );

		if ( is_object( $this->_all_translations ) ) {
			return;
		}

		$this->_all_translations = (object) [ 'projects' => [] ];

		try {
			$this->_all_translations->projects = $this->get_remote_translations_data();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( $e->getMessage() );
		}

		WP::set_site_transient( self::TRANSLATIONS_TRANSIENT, $this->_all_translations, self::TRANSLATIONS_TRANSIENT_EXPIRY );
	}

	/**
	 * Gets the translation data from the GravityKit translations API.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Simplified error handling.
	 *
	 * @throws Exception When the API is unreachable or returns invalid data.
	 *
	 * @return array
	 */
	public function get_remote_translations_data() {
		/**
		 * Filters the URL used to fetch translation packages.
		 *
		 * @since 1.15.0
		 *
		 * @param string $url The translation packages API URL.
		 */
		$api_url = apply_filters( 'gk/foundation/translations/api-url', self::API_LANGUAGE_PACKAGES_URL );

		$request = wp_remote_get(
			$api_url,
			[
				'timeout' => 3,
			]
		);

		if ( is_wp_error( $request ) ) {
			throw new Exception(
				sprintf( '%s(): Unable to reach translations API: %s.', __METHOD__, $request->get_error_message() )
			);
		}

		if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
			throw new Exception(
				sprintf( '%s(): Translations API returned an invalid response: %s.', __METHOD__, $request['response']['message'] )
			);
		}

		$result = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( ! is_array( $result ) ) {
			throw new Exception(
				sprintf( '%s(): Could not decode the response received from translations API.', __METHOD__ )
			);
		}

		return $result;
	}

	/**
	 * Determines if a translation needs to be installed or updated.
	 *
	 * Compares the remote `updated` timestamp against locally stored metadata
	 * rather than parsing PO file headers on every request.
	 *
	 * @since 1.15.0
	 *
	 * @param array $translation The translation data from the remote API.
	 *
	 * @return bool
	 */
	private function needs_update( $translation ) {
		if ( empty( $translation['updated'] ) ) {
			return true;
		}

		$metadata       = $this->read_metadata();
		$installed_date = $metadata[ $this->_text_domain ][ $translation['language'] ] ?? null;

		if ( ! $installed_date ) {
			return true;
		}

		$local  = date_create( $installed_date );
		$remote = date_create( $translation['updated'] );

		return $remote > $local;
	}

	/**
	 * Downloads and installs the given translation, then updates the metadata file.
	 *
	 * @since 1.0.0
	 * @since 1.15.0   Simplified; writes metadata JSON after successful install.
	 *
	 * @param array $translation The translation data.
	 *
	 * @throws Exception When filesystem operations fail.
	 *
	 * @return void
	 */
	public function install_translation( $translation ) {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . '/wp-admin/includes/admin.php';

			if ( ! WP_Filesystem() ) {
				throw new Exception(
					sprintf( '%s(): Aborting language package installation; unable to init WP_Filesystem.', __METHOD__ )
				);
			}
		}

		$translations_folder = Framework::get_path_to_translations_folder();

		if ( ! $wp_filesystem->is_dir( $translations_folder ) && ! wp_mkdir_p( $translations_folder ) ) {
			throw new Exception(
				sprintf( '%s(): Unable to create translations folder at %s.', __METHOD__, $translations_folder )
			);
		}

		$temp_package_file = download_url( $translation['package'] );

		if ( is_wp_error( $temp_package_file ) ) {
			throw new Exception(
				sprintf( '%s(): Error downloading language package. Code: %s; Message: %s.', __METHOD__, $temp_package_file->get_error_code(), $temp_package_file->get_error_message() )
			);
		}

		$zip_file = sprintf(
			'%s/%s-%s.zip',
			Framework::get_path_to_translations_folder(),
			$this->_text_domain,
			$translation['language']
		);

		$copy_result = $wp_filesystem->copy( $temp_package_file, $zip_file, true, FS_CHMOD_FILE );

		$wp_filesystem->delete( $temp_package_file );

		if ( ! $copy_result ) {
			throw new Exception(
				sprintf( '%s(): Unable to move language package to %s.', __METHOD__, Framework::get_path_to_translations_folder() )
			);
		}

		$result = unzip_file(
			$zip_file,
			Framework::get_path_to_translations_folder()
		);

		$wp_filesystem->delete( $zip_file );

		if ( is_wp_error( $result ) ) {
			throw new Exception(
				sprintf( '%s(): Error extracting language package. Code: %s; Message: %s.', __METHOD__, $result->get_error_code(), $result->get_error_message() )
			);
		}

		$this->write_metadata( $translation['language'], $translation['updated'] );
	}

	/**
	 * Returns an array of locales or .mo translation files found in the translations folder.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $return_files Indicates if the result should be keyed by locale with file paths as values.
	 *
	 * @return array
	 */
	public function get_installed_translations( $return_files = false ) {
		$files = glob(
			sprintf(
				'%s/%s-*.mo',
				Framework::get_path_to_translations_folder(),
				$this->_text_domain
			)
		);

		if ( empty( $files ) ) {
			return [];
		}

		$translations = [];

		foreach ( $files as $file ) {
			$translations[ str_replace( $this->_text_domain . '-', '', basename( $file, '.mo' ) ) ] = $file;
		}

		return $return_files ? $translations : array_keys( $translations );
	}

	/**
	 * Reads the translations metadata JSON file.
	 *
	 * @since 1.15.0
	 *
	 * @return array
	 */
	private function read_metadata() {
		$path = Framework::get_path_to_translations_folder() . '/' . self::METADATA_FILE;

		if ( ! file_exists( $path ) ) {
			return [];
		}

		$contents = file_get_contents( $path );

		if ( ! is_string( $contents ) ) {
			return [];
		}

		$data = json_decode( $contents, true );

		return is_array( $data ) ? $data : [];
	}

	/**
	 * Writes a timestamp entry to the translations metadata JSON file after a successful install.
	 *
	 * @since 1.15.0
	 *
	 * @param string $locale    The locale that was installed (e.g., 'de_DE').
	 * @param string $timestamp The remote `updated` timestamp for the installed translation.
	 *
	 * @return void
	 */
	private function write_metadata( $locale, $timestamp ) {
		$metadata = $this->read_metadata();

		if ( ! isset( $metadata[ $this->_text_domain ] ) ) {
			$metadata[ $this->_text_domain ] = [];
		}

		$metadata[ $this->_text_domain ][ $locale ] = $timestamp;

		$path = Framework::get_path_to_translations_folder() . '/' . self::METADATA_FILE;

		file_put_contents( $path, wp_json_encode( $metadata, JSON_PRETTY_PRINT ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}
}
