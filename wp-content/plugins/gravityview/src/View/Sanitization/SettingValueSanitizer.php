<?php
/**
 * Baseline sanitizer for slot setting values.
 *
 * Mirrors InspectorRoute::sanitize_setting_value default-mode behavior so the
 * repository write paths (batch_apply + the dead singleton add()) apply the
 * same XSS / NUL / RTL-spoofing protections the REST path does.
 *
 * Schema-aware modes (textarea→wp_kses_post, raw→json validation) live in
 * InspectorRoute because they need slot-level schema context that repositories
 * do not load. Repositories use default mode as a safe baseline. A follow-up
 * should pull schema lookup into a shared service so batch can do schema-aware
 * sanitization too.
 *
 * @package GravityKit\GravityView\View\Sanitization
 */

namespace GravityKit\GravityView\View\Sanitization;

defined( 'ABSPATH' ) || exit;

final class SettingValueSanitizer {

	/**
	 * Sanitize a value with the default mode.
	 *
	 * Bool → '0'/'1'. Numeric → int/float. Array → recurse. String → strip
	 * control + Unicode directional overrides + sanitize_text_field.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Raw value.
	 * @return mixed
	 */
	public static function sanitize( $value ) {
		if ( null === $value ) {
			// Repositories use null as a delete-this-key sentinel for patches
			// (FieldSlotRepository::patch and siblings `unset()` keys whose
			// patched value is null). Coercing null to '' would turn the
			// delete into a "set to empty string" — silent data corruption.
			return null;
		}

		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}

		if ( is_numeric( $value ) ) {
			return is_float( $value + 0 ) ? (float) $value : (int) $value;
		}

		if ( is_array( $value ) ) {
			return array_map( [ self::class, 'sanitize' ], $value );
		}

		$string = preg_replace(
			[ '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u' ],
			'',
			(string) $value
		);

		return sanitize_text_field( $string );
	}

	/**
	 * Sanitize every value in a settings array.
	 *
	 * Keys are preserved verbatim; values are sanitized via self::sanitize().
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings Raw settings.
	 * @return array
	 */
	public static function sanitize_settings( array $settings ): array {
		$out = [];
		foreach ( $settings as $key => $value ) {
			$out[ $key ] = self::sanitize( $value );
		}

		return $out;
	}
}
