<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Source;

use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Filter\FilterFactory;
use InvalidArgumentException;
use JsonException;

/**
 * Returns a {@see Filter} based on a JSON string (optionally Base64-encoded).
 *
 * @since 2.9.0
 */
final class JsonFilterSource implements FilterSource {
	/**
	 * Map of full key names to single-letter keys.
	 *
	 * @since 2.9.0
	 *
	 * @var array
	 */
	private const KEY_MAP = [
		'_id'        => 'i',
		'form_id'    => 'f',
		'key'        => 'k',
		'value'      => 'v',
		'operator'   => 'o',
		'mode'       => 'm',
		'conditions' => 'c',
	];

	/**
	 * The filter factory.
	 *
	 * @since 2.9.0
	 *
	 * @var FilterFactory
	 */
	private FilterFactory $filter_factory;

	/**
	 * Creates the instance.
	 *
	 * @since 2.9.0
	 *
	 * @param FilterFactory $filter_factory The filter factory.
	 */
	public function __construct( FilterFactory $filter_factory ) {
		$this->filter_factory = $filter_factory;
	}

	/**
	 * Returns a {@see Filter} based on a JSON string (plain or Base64-encoded).
	 *
	 * @since 2.9.0
	 *
	 * @param string $value The JSON string or Base64-encoded JSON string.
	 *
	 * @return Filter|null The Filter.
	 */
	public function __invoke( string $value ): ?Filter {
		$json_string = self::decode_input( $value );

		if ( null === $json_string ) {
			return null;
		}

		try {
			$filter_array              = json_decode( $json_string, true, 512, JSON_THROW_ON_ERROR );
			$filter_array              = $this->expand_keys( $filter_array );
			$filter_array['version'] ??= 2;

			return $this->filter_factory->from_array( $filter_array );
		} catch ( JsonException | InvalidArgumentException $e ) {
			return null;
		}
	}

	/**
	 * Decodes the input string, trying Base64 decoding first, then plain JSON.
	 *
	 * @since 2.9.0
	 *
	 * @param string $value The input string.
	 *
	 * @return string|null The decoded JSON string or null if both attempts fail.
	 */
	public static function decode_input( string $value ): ?string {
		// Try Base64 decoding first.
		//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$decoded = base64_decode( $value, true );

		if ( false !== $decoded && json_validate( $decoded ) ) {
			return $decoded;
		}

		// If Base64 decoding failed or result is not valid JSON, try as plain JSON.
		if ( json_validate( $value ) ) {
			return $value;
		}

		$value = stripslashes( $value );
		if ( json_validate( $value ) ) {
			return $value;
		}

		return null;
	}

	/**
	 * Transforms a filter array into a JSON string.
	 *
	 * @since 2.9.0
	 *
	 * @param array $filter_array The filter array.
	 * @param bool  $minify       Whether to minify the keys before encoding.
	 *
	 * @return string The JSON string, or empty string on error.
	 */
	public static function to_json( array $filter_array, bool $minify = true ): string {
		if ( $minify ) {
			$filter_array = self::minify_filter( $filter_array );
		}

		return wp_json_encode( $filter_array, JSON_THROW_ON_ERROR );
	}

	/**
	 * Transforms a filter array into a Base64-encoded JSON string.
	 *
	 * @since 2.9.0
	 *
	 * @param array $filter_array The filter array.
	 * @param bool  $minify       Whether to minify the keys before encoding.
	 *
	 * @return string The Base64-encoded string, or empty string on error.
	 */
	public static function to_base64( array $filter_array, bool $minify = true ): string {
		$json_string = self::to_json( $filter_array, $minify );

		if ( '' === $json_string ) {
			return '';
		}

		//phpcs:ignore
		return base64_encode( $json_string );
	}

	/**
	 * Minifies filter array keys to single letters.
	 *
	 * @since 2.9.0
	 *
	 * @param array $filter_array The filter array with full key names.
	 *
	 * @return array The filter array with minified keys.
	 */
	private static function minify_filter( array $filter_array ): array {
		$minified = [];

		foreach ( $filter_array as $key => $value ) {
			if ( '_id' === $key ) {
				// Minified, we don't need the ID.
				continue;
			}
			$short_key = self::KEY_MAP[ $key ] ?? $key;

			// Recursively minify conditions.
			if ( 'conditions' === $key && is_array( $value ) ) {
				$value = array_map( __METHOD__, $value );
			}

			$minified[ $short_key ] = $value;
		}

		return $minified;
	}

	/**
	 * Expands single-letter keys to their full names.
	 *
	 * @since 2.9.0
	 *
	 * @param array $filter_array The filter array with potentially compressed keys.
	 *
	 * @return array The filter array with expanded keys.
	 */
	private function expand_keys( array $filter_array ): array {
		$key_map  = array_flip( self::KEY_MAP );
		$expanded = [];

		foreach ( $filter_array as $key => $value ) {
			$full_key = $key_map[ $key ] ?? $key;

			if ( 'conditions' === $full_key && is_array( $value ) ) {
				$value = array_map( [ $this, 'expand_keys' ], $value );
			}

			$expanded[ $full_key ] = $value;
		}

		return $expanded;
	}
}
