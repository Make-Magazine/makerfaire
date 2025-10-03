<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Encryption;

use Exception;

/**
 * This class provides basic data encryption functionality.
 */
class Encryption {
	const DEFAULT_NONCE = 'bc5d92ffc6c54ff8d865a1e6f3361f48d0a84a2b145be34e'; // 24-bit value stored as a hex string

	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, Encryption>
	 */
	private static $instances = [];

	/**
	 * Secret key used to encrypt license key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $secret_key;

	/**
	 * Options for encryption.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Returns default options for encryption.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	protected function get_default_options() {
		return array(
			'base64_variant'              => SODIUM_BASE64_VARIANT_ORIGINAL,
			'crypto_secretbox_keybytes'   => SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
			'crypto_secretbox_noncebytes' => SODIUM_CRYPTO_SECRETBOX_NONCEBYTES,
			'hash_algo'                   => 'sha256',
		);
	}

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $options parameter.
	 *
	 * @param string $secret_key (optional) Secret key to be used for encryption. Default: wp_salt() value.
	 * @param array  $options    (optional) Options for encryption. Default: empty array.
	 *
	 * @return void
	 */
	private function __construct( $secret_key = '', $options = array() ) {
		$this->require_sodium();

		if ( ! $secret_key ) {
			$secret_key = defined( 'GRAVITYKIT_SECRET_KEY' ) ? GRAVITYKIT_SECRET_KEY : wp_salt();
		}

		// Set default options first so we can use them for key length validation.
		$this->options = wp_parse_args( $options, $this->get_default_options() );

		if ( strlen( $secret_key ) < $this->options['crypto_secretbox_keybytes'] ) {
			$secret_key = hash_hmac( $this->options['hash_algo'], $secret_key, self::DEFAULT_NONCE );
		}

		if ( strlen( $secret_key ) > $this->options['crypto_secretbox_keybytes'] ) {
			$secret_key = mb_substr( $secret_key, 0, $this->options['crypto_secretbox_keybytes'], '8bit' );
		}

		$this->secret_key = $secret_key;
	}

	/**
	 * Returns class instance based on the secret key.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $options parameter.
	 *
	 * @param string $secret_key (optional) Secret key to be used for encryption. Default: wp_salt() value.
	 * @param array  $options    (optional) Options for encryption. Default: empty array.
	 *
	 * @return Encryption
	 */
	public static function get_instance( $secret_key = '', $options = array() ) {
		$cache_key = $secret_key . '_' . md5( wp_json_encode( $options ) ?: '' );

		if ( ! isset( self::$instances[ $cache_key ] ) ) {
			self::$instances[ $cache_key ] = new self( $secret_key, $options );
		}

		return self::$instances[ $cache_key ];
	}

	/**
	 * Encrypts data.
	 *
	 * Note: This is for basic internal use and is not intended for highly-sensitive applications.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $data             Data to encrypt.
	 * @param bool        $use_random_nonce (optional) Whether to use random nonce. Default: true.
	 * @param string|null $custom_nonce     (optional) Custom IV value to use. Default: null.
	 *
	 * @return false|string
	 */
	public function encrypt( $data, $use_random_nonce = true, $custom_nonce = null ) {
		try {
			if ( ! $use_random_nonce ) {
				$nonce = $custom_nonce ?: sodium_hex2bin( self::DEFAULT_NONCE );
			} else {
				$nonce = $this->get_random_nonce();
			}
		} catch ( Exception $e ) {
			return false;
		}

		if ( strlen( $nonce ) < $this->options['crypto_secretbox_noncebytes'] ) {
			$nonce = hash_hmac( $this->options['hash_algo'], $nonce, self::DEFAULT_NONCE ) ?: $nonce;
		}

		if ( strlen( $nonce ) > $this->options['crypto_secretbox_noncebytes'] ) {
			$nonce = mb_substr( $nonce, 0, $this->options['crypto_secretbox_noncebytes'], '8bit' );
		}

		try {
			$encrypted = sodium_crypto_secretbox( $data, $nonce, $this->secret_key );
			$encrypted = sodium_bin2base64( $nonce . $encrypted, $this->options['base64_variant'] );
			if ( extension_loaded( 'sodium' ) || extension_loaded( 'libsodium' ) ) {
				sodium_memzero( $nonce );
			}
		} catch ( Exception $e ) {
			return false;
		}

		return $encrypted;
	}

	/**
	 * Decrypts data.
	 *
	 * Note: This is for internal use and is not intended for highly-sensitive applications.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Data to encrypt.
	 *
	 * @return string|null
	 */
	public function decrypt( $data ) {
		try {
			$encrypted = sodium_base642bin( $data, $this->options['base64_variant'] );
		} catch ( Exception $e ) {
			return null;
		}

		$nonce     = mb_substr( $encrypted, 0, $this->options['crypto_secretbox_noncebytes'], '8bit' );
		$encrypted = mb_substr( $encrypted, $this->options['crypto_secretbox_noncebytes'], null, '8bit' );

		try {
			$decrypted = sodium_crypto_secretbox_open( $encrypted, $nonce, $this->secret_key );
		} catch ( Exception $e ) {
			return null;
		}

		if ( false === $decrypted ) {
			$decrypted = null;
		}

		return $decrypted;
	}

	/**
	 * Generates a quick one-way hash of data.
	 *
	 * Note: This is for internal use and is not intended for highly-sensitive applications.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data The data to create a hash of.
	 *
	 * @return string The hash.
	 */
	public function hash( $data ) {
		return hash_hmac( $this->options['hash_algo'], $data, self::DEFAULT_NONCE );
	}

	/**
	 * Returns a random nonce.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return string
	 */
	public function get_random_nonce() {
		$length = (int) $this->options['crypto_secretbox_noncebytes'];
		return random_bytes( $length > 0 ? $length : SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	}

	/**
	 * Includes PHP polyfill for ext/sodium if some core functions are not available.
	 *
	 * @since 1.1.1
	 *
	 * @return void
	 */
	private function require_sodium() {
		$required_functions = [
			'sodium_hex2bin',
			'sodium_bin2base64',
			'sodium_base642bin',
			'sodium_crypto_secretbox',
			'sodium_crypto_secretbox_open',
		];

		foreach ( $required_functions as $function ) {
			if ( ! function_exists( $function ) ) {
				require_once ABSPATH . WPINC . '/sodium_compat/autoload.php';

				break;
			}
		}
	}
}
