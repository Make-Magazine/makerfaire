<?php
/**
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder;

use GravityKit\GravityView\Shortcode\ShortcodeRenderer;

if ( ! defined( 'GRAVITYVIEW_DIR' ) ) { die(); }

trait PageBuilderModuleCore {

	/**
	 * Get the block type identifier for this module.
	 *
	 * Must return one of: 'view', 'entry', 'entry-field', 'entry-link', 'view-details'.
	 *
	 * @since 3.0.0
	 *
	 * @return string Block type identifier.
	 */
	abstract protected static function get_block_type();

	/**
	 * Get the builder-specific integration instance.
	 *
	 * @since 3.0.0
	 *
	 * @return PageBuilder Integration instance.
	 */
	abstract public static function get_builder_integration();

	/**
	 * Extract block attributes from module props.
	 *
	 * Reads the block type's attribute_map and extracts matching values from props,
	 * trying snake_case, lowercase, camelCase, and shortcode key variants for each.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props Module properties.
	 *
	 * @return array camelCase block attributes with non-empty values.
	 */
	protected static function extract_block_atts( $props ) {
		$metadata      = static::get_builder_integration()->get_block_types_metadata()[ static::get_block_type() ] ?? [];
		$attribute_map = $metadata['attribute_map'] ?? [];
		$fields_meta   = static::get_builder_integration()->get_block_type_field_metadata( static::get_block_type() );
		$block_atts    = [];

		foreach ( $attribute_map as $camel_key => $shortcode_key ) {
			if ( 'secret' === $camel_key ) {
				continue;
			}

			$snake_key = self::camel_to_snake( $camel_key );
			$lower_key = strtolower( $camel_key );

			// Also check the shortcode key as a prop name (handles edge cases like 'class' → 'classValue').
			$value = self::get_prop_value( $props, [ $snake_key, $lower_key, $camel_key, $shortcode_key ] );

			if ( '' !== $value ) {
				$block_atts[ $camel_key ] = is_string( $value )
					? self::sanitize_attribute( $value, $camel_key, $fields_meta )
					: $value;
			}
		}

		return $block_atts;
	}

	/**
	 * Sanitize a single attribute value based on its field type.
	 *
	 * Uses the field metadata to choose the appropriate sanitizer:
	 * - number: cast to int
	 * - select: sanitize_text_field (constrained input, no encoding)
	 * - Raw attributes (linkAtts, fieldValues, fieldSettingOverrides):
	 *   wp_strip_all_tags only — preserves percent-encoded bytes and
	 *   HTML-attribute syntax that sanitize_text_field would destroy
	 * - Everything else: sanitize_text_field (standard WP text sanitization)
	 *
	 * @since 3.0.0
	 *
	 * @param string $value       Raw attribute value.
	 * @param string $camel_key   Attribute key in camelCase.
	 * @param array  $fields_meta Field metadata from the integration.
	 *
	 * @return string|int Sanitized value.
	 */
	private static function sanitize_attribute( $value, $camel_key, $fields_meta ) {
		// Attributes that carry query strings, HTML attributes, or raw
		// shortcode fragments must not be run through sanitize_text_field
		// which strips percent-encoded bytes and angle-bracket-like content.
		$raw_attributes = [ 'linkAtts', 'fieldValues', 'fieldSettingOverrides' ];
		if ( in_array( $camel_key, $raw_attributes, true ) ) {
			return trim( wp_strip_all_tags( $value ) );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Validate that all required attributes are present.
	 *
	 * @since 3.0.0
	 *
	 * @param array $block_atts camelCase block attributes.
	 *
	 * @return string|null Error message if validation fails, null if valid.
	 */
	protected static function validate_required_atts( $block_atts ) {
		$metadata    = static::get_builder_integration()->get_block_types_metadata()[ static::get_block_type() ] ?? [];
		$required    = $metadata['required_fields'] ?? [];
		$fields_meta = static::get_builder_integration()->get_block_type_field_metadata( static::get_block_type() );

		foreach ( $required as $camel_key ) {
			$value = $block_atts[ $camel_key ] ?? '';

			if ( 'viewId' === $camel_key ) {
				if ( 0 === (int) $value ) {
					return __( 'Please select a View.', 'gk-gravityview' );
				}
				continue;
			}

			if ( empty( $value ) ) {
				$label = $fields_meta[ $camel_key ]['label'] ?? $camel_key;

				// translators: %s is the field label (e.g., "Entry ID", "Field ID").
				return sprintf( __( '%s is required.', 'gk-gravityview' ), $label );
			}
		}

		return null;
	}

	/**
	 * Validate that the referenced entry exists and belongs to the View's form.
	 *
	 * Only runs when the block type has `entryId` in its required fields.
	 * Prevents the generic "Please select a View" fallback from showing when
	 * the real problem is an invalid Entry ID or an entry from a different
	 * form than the View is connected to. Delegates lookup to
	 * \GV\GF_Entry::by_id so custom entry slugs are respected; enforces
	 * form matching here because by_id only scopes lookups by form for the
	 * slug fallback path, not for numeric IDs.
	 *
	 * Return values are raw strings — `ShortcodeRenderer::render_placeholder()`
	 * handles escaping for output.
	 *
	 * @since 3.0.0
	 *
	 * @param array    $block_atts camelCase block attributes.
	 * @param \GV\View $view       Resolved View.
	 *
	 * @return string|null Error message if entry is invalid, null otherwise.
	 */
	protected static function validate_entry_exists( $block_atts, $view ) {
		$metadata = static::get_builder_integration()->get_block_types_metadata()[ static::get_block_type() ] ?? [];
		$required = $metadata['required_fields'] ?? [];

		if ( ! in_array( 'entryId', $required, true ) ) {
			return null;
		}

		$entry_id = $block_atts['entryId'] ?? '';
		if ( '' === $entry_id ) {
			return null;
		}

		if ( ! class_exists( '\GV\GF_Entry' ) ) {
			return null;
		}

		$view_form_id = $view && $view->form ? (int) $view->form->ID : 0;
		$entry        = \GV\GF_Entry::by_id( $entry_id, $view_form_id );

		if ( ! $entry ) {
			return sprintf(
				/* translators: %s is the entry ID the user provided. */
				__( 'Entry #%s was not found.', 'gk-gravityview' ),
				(string) $entry_id
			);
		}

		if ( $view_form_id && isset( $entry['form_id'] ) && (int) $entry['form_id'] !== $view_form_id ) {
			return sprintf(
				/* translators: %s is the entry ID the user provided. */
				__( 'Entry #%s does not belong to the selected View.', 'gk-gravityview' ),
				(string) $entry_id
			);
		}

		return null;
	}

	/**
	 * Validate enum-like attributes against allowed values from metadata.
	 *
	 * @since 3.0.0
	 *
	 * @param array $block_atts camelCase block attributes (modified in place).
	 *
	 * @return void
	 */
	protected static function validate_enum_atts( &$block_atts ) {
		$fields_meta = static::get_builder_integration()->get_block_type_field_metadata( static::get_block_type() );

		foreach ( $block_atts as $camel_key => $value ) {
			if ( empty( $fields_meta[ $camel_key ]['options'] ) || ! is_array( $fields_meta[ $camel_key ]['options'] ) ) {
				continue;
			}

			// A `detail` key registered via the shortcode filter is valid even
			// though it isn't in the built-in options list — match the Gutenberg
			// path, which passes custom details straight through.
			$is_custom_detail = 'detail' === $camel_key && has_filter( "gravityview/shortcode/detail/{$value}" );

			if ( $is_custom_detail ) {
				continue;
			}

			if ( ! in_array( $value, array_keys( $fields_meta[ $camel_key ]['options'] ), true ) ) {
				unset( $block_atts[ $camel_key ] );
			}
		}
	}

	/**
	 * Build a shortcode string for a block type.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $block_type Block type identifier.
	 * @param array    $block_atts camelCase block attributes.
	 * @param \GV\View $view       View object.
	 *
	 * @return string Formatted shortcode string.
	 */
	protected static function build_shortcode( $block_type, $block_atts, $view ) {
		$mapping = array_combine( array_keys( $block_atts ), array_keys( $block_atts ) );

		return static::get_builder_integration()->build_block_type_shortcode( $block_type, $block_atts, $view, $mapping );
	}

	/**
	 * Convert a camelCase string to snake_case.
	 *
	 * @since 3.0.0
	 *
	 * @param string $string camelCase string.
	 *
	 * @return string snake_case string.
	 */
	private static function camel_to_snake( $string ) {
		return strtolower( preg_replace( '/[A-Z]/', '_$0', lcfirst( $string ) ) );
	}

	/**
	 * Get a prop value from multiple possible keys.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props   Properties array.
	 * @param array $keys    Keys to check in order.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	protected static function get_prop_value( $props, $keys, $default = '' ) {
		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $props ) && '' !== $props[ $key ] ) {
				return $props[ $key ];
			}
		}

		return $default;
	}
}
