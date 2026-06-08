<?php
/**
 * Handles Gravity Forms version-specific compatibility.
 *
 * Centralizes GF version checks so call sites don't scatter version_compare() logic.
 *
 * @since 2.55.0
 * @license GPL2+
 * @package GravityView
 */

/**
 * Provides version-aware wrappers for Gravity Forms methods whose signatures change across versions.
 *
 * @since 2.55.0
 */
class GravityView_GF_Compat {

	/**
	 * Whether the installed GF version uses entry arrays instead of currency strings.
	 *
	 * GF 2.9.29 changed `GF_Field::get_value_entry_detail()` and `GFCommon::get_lead_field_display()`
	 * to accept a full entry array where they previously accepted a currency code string.
	 *
	 * @since 2.55.0
	 *
	 * @return bool
	 */
	public static function uses_entry_array() {
		return version_compare( GFForms::$version, '2.9.29', '>=' );
	}

	/**
	 * Whether `GFCommon::get_lead_field_display()` is deprecated.
	 *
	 * GF 2.9.31 deprecates this method in favor of `$field->get_value_entry_detail()`
	 * and `$field->get_value_all_fields_merge_tag()`.
	 *
	 * @since 2.55.0
	 *
	 * @return bool
	 */
	public static function is_get_lead_field_display_deprecated() {
		return method_exists( 'GF_Field', 'get_value_all_fields_merge_tag' );
	}

	/**
	 * Returns the formatted display value for a field, compatible across GF versions.
	 *
	 * On GF < 2.9.29: calls `GFCommon::get_lead_field_display()` with a currency string.
	 * On GF 2.9.29+: calls `GFCommon::get_lead_field_display()` with the entry array.
	 * On GF 2.9.31+ (when deprecated): calls `$field->get_value_entry_detail()` directly,
	 * replicating the post_category preparation that `get_lead_field_display()` performs.
	 *
	 * @since 2.55.0
	 *
	 * @param GF_Field     $field    The Gravity Forms field object.
	 * @param string|array $value    The raw field value.
	 * @param array        $entry    The entry array.
	 * @param bool         $use_text Whether to use choice text instead of value. Default: false.
	 * @param string       $format   Output format. Default: 'html'. Accepts 'html' or 'text'.
	 * @param string       $media    Media context. Default: 'screen'. Accepts 'screen' or 'email'.
	 *
	 * @return string|false
	 */
	public static function get_field_display( $field, $value, $entry, $use_text = false, $format = 'html', $media = 'screen' ) {
		if ( ! $field instanceof GF_Field ) {
			$field = GF_Fields::create( $field );
		}

		// GF 2.9.31+ deprecates get_lead_field_display() — call get_value_entry_detail() directly.
		// Post_category preparation is handled by field subclasses in this GF version.
		if ( self::is_get_lead_field_display_deprecated() ) {
			return $field->get_value_entry_detail( $value, $entry, $use_text, $format, $media );
		}

		// GF 2.9.29+: pass the full entry array.
		if ( self::uses_entry_array() ) {
			return GFCommon::get_lead_field_display( $field, $value, $entry, $use_text, $format, $media );
		}

		// GF < 2.9.29: pass the currency string.
		$currency = is_array( $entry ) ? rgar( $entry, 'currency', '' ) : '';

		return GFCommon::get_lead_field_display( $field, $value, $currency, $use_text, $format, $media );
	}

	/**
	 * Whether GF provides a native Edit action link on the entries list.
	 *
	 * GF 2.9.31.3 added an "Edit" action to the entries list table via
	 * `handle_row_actions()`, making GravityView's custom edit link redundant.
	 *
	 * @since 2.57.0
	 *
	 * @return bool
	 */
	public static function has_native_entry_edit_link() {
		return version_compare( GFForms::$version, '2.9.31.3', '>=' );
	}

	/**
	 * Returns the entry-detail value for a field, compatible across GF versions.
	 *
	 * Wraps `$field->get_value_entry_detail()` to pass the correct second parameter
	 * based on GF version: entry array on 2.9.29+, currency string on older versions.
	 *
	 * Use this instead of calling `$field->get_value_entry_detail()` directly when
	 * GravityView templates need version-aware field rendering without the full
	 * `get_lead_field_display()` pipeline.
	 *
	 * @since 2.55.0
	 *
	 * @param GF_Field     $field    The Gravity Forms field object.
	 * @param string|array $value    The raw field value.
	 * @param array        $entry    The entry array.
	 * @param bool         $use_text Whether to use choice text instead of value. Default: false.
	 * @param string       $format   Output format. Default: 'html'. Accepts 'html' or 'text'.
	 * @param string       $media    Media context. Default: 'screen'. Accepts 'screen' or 'email'.
	 *
	 * @return string|false
	 */
	public static function get_entry_detail( $field, $value, $entry, $use_text = false, $format = 'html', $media = 'screen' ) {
		$entry_or_currency = self::uses_entry_array() ? $entry : rgar( $entry, 'currency', '' );

		return $field->get_value_entry_detail( $value, $entry_or_currency, $use_text, $format, $media );
	}

	/**
	 * Returns a File Upload field value as an array of URLs, regardless of GF storage format.
	 *
	 * GF 2.10.0 unified File Upload storage so the form editor sets `storageType='json'` on
	 * every new field — including single-file ones — and values are stored as a JSON-encoded
	 * array (`["url"]`) so that toggling `multipleFiles` is safe on fields with existing
	 * entries. Older GF versions stored single-file values as a plain URL string.
	 *
	 * Delegates to `GF_Field_FileUpload::to_array()` (added in GF 2.10) when available, with
	 * a fallback for older GF versions that detects the JSON shape via `GFCommon::is_json()`.
	 *
	 * @since 2.60.0
	 *
	 * @param GF_Field     $field The File Upload field.
	 * @param string|array $value The raw stored value.
	 *
	 * @return array Array of file URLs. Empty array when the value is empty or unparseable.
	 */
	public static function file_upload_value_to_array( $field, $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		if ( is_array( $value ) ) {
			return $value;
		}

		if ( $field instanceof GF_Field_FileUpload && method_exists( $field, 'to_array' ) ) {
			return $field->to_array( $value );
		}

		if ( is_string( $value ) && GFCommon::is_json( $value ) ) {
			$decoded = json_decode( $value, true );

			return is_array( $decoded ) ? $decoded : array();
		}

		return is_string( $value ) ? array( $value ) : array();
	}
}
