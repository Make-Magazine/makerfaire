<?php

namespace GravityKit\GravityImport;

use GFFormsModel;
use WP_Error;

/**
 * Import Profile model for saving and loading reusable field mapping configurations.
 *
 * @since 2.11.0
 */
class ImportProfile {
	const VERSION = 1;
	const TYPE = 'import_profile';
	const MAX_PROFILE_SIZE = 1048576;

	/**
	 * Loads and validates a profile from a JSON file path.
	 *
	 * Only invoke with a path under filesystem control of the calling code. Never wire to an HTTP-supplied path.
	 *
	 * @since 2.11.0
	 *
	 * @param string $file_path Absolute path to the profile JSON file.
	 *
	 * @return array|WP_Error The profile data or an error.
	 */
	public static function load( $file_path ) {
		$real_path = realpath( $file_path );

		if ( ! $real_path || ! is_file( $real_path ) || ! is_readable( $real_path ) ) {
			return new WP_Error( 'gravityimport/profile/not_found', __( 'Profile file not found or not readable.', 'gk-gravityimport' ) );
		}

		$size = filesize( $real_path );

		if ( false !== $size && $size > self::MAX_PROFILE_SIZE ) {
			return new WP_Error( 'gravityimport/profile/too_large', __( 'Profile file exceeds the maximum allowed size.', 'gk-gravityimport' ) );
		}

		$contents = file_get_contents( $real_path );

		if ( false === $contents ) {
			return new WP_Error( 'gravityimport/profile/not_found', __( 'Profile file not found or not readable.', 'gk-gravityimport' ) );
		}

		$data = json_decode( $contents, true );

		if ( null === $data || JSON_ERROR_NONE !== json_last_error() ) {
			return new WP_Error( 'gravityimport/profile/invalid_json', __( 'Profile file contains invalid JSON.', 'gk-gravityimport' ) );
		}

		return self::validate( $data );
	}

	/**
	 * Validates profile data structure.
	 *
	 * @since 2.11.0
	 *
	 * @param array $data The profile data.
	 *
	 * @return array|WP_Error The validated profile or an error.
	 */
	public static function validate( $data ) {
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'gravityimport/profile/invalid_json', __( 'Profile file contains invalid JSON.', 'gk-gravityimport' ) );
		}

		if ( empty( $data['version'] ) || self::VERSION !== $data['version'] ) {
			return new WP_Error( 'gravityimport/profile/unsupported_version', __( 'Unsupported profile version.', 'gk-gravityimport' ) );
		}

		if ( empty( $data['type'] ) || self::TYPE !== $data['type'] ) {
			return new WP_Error( 'gravityimport/profile/invalid_type', __( 'Not an import profile file.', 'gk-gravityimport' ) );
		}

		if ( empty( $data['formId'] ) ) {
			return new WP_Error( 'gravityimport/profile/missing_form_id', __( 'Profile is missing form ID.', 'gk-gravityimport' ) );
		}

		if ( ! isset( $data['schema'] ) || ! is_array( $data['schema'] ) ) {
			return new WP_Error( 'gravityimport/profile/missing_schema', __( 'Profile is missing field mapping schema.', 'gk-gravityimport' ) );
		}

		if ( ! isset( $data['columns'] ) || ! is_array( $data['columns'] ) ) {
			return new WP_Error( 'gravityimport/profile/missing_columns', __( 'Profile is missing column definitions.', 'gk-gravityimport' ) );
		}

		if ( ! isset( $data['selectedColumnFields'] ) || ! is_array( $data['selectedColumnFields'] ) ) {
			return new WP_Error( 'gravityimport/profile/missing_column_fields', __( 'Profile is missing column field data.', 'gk-gravityimport' ) );
		}

		foreach ( $data['columns'] as $column ) {
			if ( ! is_array( $column ) || ! array_key_exists( 'header', $column ) || ! array_key_exists( 'index', $column ) ) {
				return new WP_Error( 'gravityimport/profile/invalid_columns', __( 'Profile has invalid column definitions.', 'gk-gravityimport' ) );
			}
		}

		foreach ( $data['selectedColumnFields'] as $field ) {
			if ( ! is_array( $field ) || ! array_key_exists( 'columnIndex', $field ) ) {
				return new WP_Error( 'gravityimport/profile/invalid_column_fields', __( 'Profile has invalid column field data.', 'gk-gravityimport' ) );
			}
		}

		return $data;
	}

	/**
	 * Validates profile mapping rules against a Gravity Forms form.
	 *
	 * @since 2.11.0
	 *
	 * @param array $data Profile data.
	 * @param array $form Gravity Forms form data.
	 *
	 * @return array|WP_Error Validation result with filtered data and warnings.
	 */
	public static function validate_against_form( $data, $form ) {
		if ( empty( $form ) || empty( $form['fields'] ) ) {
			return new WP_Error( 'gravityimport/profile/form_not_found', __( 'Form not found.', 'gk-gravityimport' ) );
		}

		$warnings = [];
		$schema   = [];

		foreach ( $data['schema'] as $i => $rule ) {
			$batch_rule = self::strip_profile_rule_keys( $rule );
			$result     = Batch::validate_rule( $i, $batch_rule, $form );

			if ( is_wp_error( $result ) ) {
				$warnings[] = [
					'rule'    => $i,
					'code'    => $result->get_error_code(),
					'message' => $result->get_error_message(),
				];

				continue;
			}

			$type_warning = self::get_field_type_warning( $i, $rule, $form );

			if ( $type_warning ) {
				$warnings[] = $type_warning;
			}

			$schema[] = $batch_rule;
		}

		$data['schema'] = $schema;

		return [
			'data'     => $data,
			'warnings' => $warnings,
		];
	}

	/**
	 * Computes a deterministic hash of form field structure.
	 *
	 * @since 2.11.0
	 *
	 * @param array $form The GF form array.
	 *
	 * @return string 8-character hex hash.
	 */
	public static function compute_form_hash( $form ) {
		$fields = $form['fields'] ?? [];
		$entries = [];

		foreach ( $fields as $field ) {
			$id = self::get_field_value( $field, 'id' );

			if ( ! is_numeric( $id ) ) {
				continue;
			}

			$entries[] = [
				'id'          => (float) $id,
				'fingerprint' => self::get_field_fingerprint( $field ),
			];
		}

		usort( $entries, function( $a, $b ) {
			if ( $a['id'] === $b['id'] ) {
				return 0;
			}

			return $a['id'] < $b['id'] ? -1 : 1;
		} );

		$str = implode( '|', wp_list_pluck( $entries, 'fingerprint' ) );
		$hash = 5381;

		for ( $i = 0, $len = strlen( $str ); $i < $len; $i++ ) {
			$hash = ( ( $hash << 5 ) + $hash + ord( $str[ $i ] ) ) & 0xFFFFFFFF;
		}

		return str_pad( dechex( $hash ), 8, '0', STR_PAD_LEFT );
	}

	/**
	 * Matches profile columns to CSV headers.
	 *
	 * @since 2.11.0
	 *
	 * @param array $profile_columns Profile's columns array [{ header, index }].
	 * @param array $csv_headers     Array of CSV header strings.
	 *
	 * @return array { matched: [ profileIdx => currentIdx ], unmatched: [ profileIdx, ... ] }
	 */
	public static function match_columns( $profile_columns, $csv_headers ) {
		$matched = [];
		$used = [];
		$header_to_current_index = [];

		foreach ( $csv_headers as $index => $header ) {
			$normalized = self::normalize_header( $header, $index );

			if ( ! array_key_exists( $normalized, $header_to_current_index ) ) {
				$header_to_current_index[ $normalized ] = $index;
			}
		}

		// Pass 1: match by name.
		foreach ( $profile_columns as $col ) {
			$normalized = self::normalize_header( $col['header'], $col['index'] );
			$found      = $header_to_current_index[ $normalized ] ?? false;

			if ( false !== $found && ! in_array( $found, $used, true ) ) {
				$matched[ $col['index'] ] = $found;
				$used[] = $found;
			}
		}

		// Pass 2: position fallback.
		$unmatched = [];

		foreach ( $profile_columns as $col ) {
			if ( isset( $matched[ $col['index'] ] ) ) {
				continue;
			}

			if ( $col['index'] < count( $csv_headers ) && ! in_array( $col['index'], $used, true ) ) {
				$matched[ $col['index'] ] = $col['index'];
				$used[] = $col['index'];
			} else {
				$unmatched[] = $col['index'];
			}
		}

		return [
			'matched'   => $matched,
			'unmatched' => $unmatched,
		];
	}

	/**
	 * Remaps schema column indices based on column matching results.
	 *
	 * @since 2.11.0
	 *
	 * @param array $schema  The profile's schema array.
	 * @param array $matched Map of profileIdx => currentIdx.
	 *
	 * @return array Remapped schema with updated column indices.
	 */
	public static function remap_schema( $schema, $matched ) {
		$remapped = [];

		foreach ( $schema as $rule ) {
			$old_col = $rule['column'];

			if ( ! isset( $matched[ $old_col ] ) ) {
				continue;
			}

			$rule['column'] = $matched[ $old_col ];
			$remapped[] = self::strip_profile_rule_keys( $rule );
		}

		return $remapped;
	}

	/**
	 * Builds a structural fingerprint for a form field.
	 *
	 * @since 2.11.0
	 *
	 * @param mixed $field Gravity Forms field object or array.
	 *
	 * @return string Field fingerprint.
	 */
	private static function get_field_fingerprint( $field ) {
		$id       = self::get_field_value( $field, 'id' );
		$type     = (string) self::get_field_value( $field, 'type', '' );
		$required = ( self::get_field_value( $field, 'isRequired' ) || self::get_field_value( $field, 'required' ) ) ? '1' : '0';
		$inputs   = self::get_input_ids( self::get_field_value( $field, 'inputs', [] ) );
		$choices  = self::get_choice_values( self::get_field_value( $field, 'choices', [] ) );
		$format   = self::get_field_format( $field, $type );

		return implode( ':', [
			$id,
			$type,
			$required,
			implode( ',', $inputs ),
			implode( ',', $choices ),
			$format,
		] );
	}

	/**
	 * Gets a value from a GF field object or array.
	 *
	 * @since 2.11.0
	 *
	 * @param mixed  $field   Gravity Forms field object or array.
	 * @param string $key     Value key.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed Field value.
	 */
	private static function get_field_value( $field, $key, $default = null ) {
		if ( is_array( $field ) && array_key_exists( $key, $field ) ) {
			return $field[ $key ];
		}

		if ( is_object( $field ) && isset( $field->{$key} ) ) {
			return $field->{$key};
		}

		return $default;
	}

	/**
	 * Gets sorted input IDs from a field inputs collection.
	 *
	 * @since 2.11.0
	 *
	 * @param mixed $inputs Field inputs.
	 *
	 * @return array Input IDs.
	 */
	private static function get_input_ids( $inputs ) {
		if ( empty( $inputs ) || ! is_array( $inputs ) ) {
			return [];
		}

		$ids = [];

		foreach ( $inputs as $input ) {
			$id = self::get_field_value( $input, 'id' );

			if ( null !== $id && '' !== (string) $id ) {
				$ids[] = (string) $id;
			}
		}

		usort( $ids, function( $a, $b ) {
			if ( (float) $a === (float) $b ) {
				return 0;
			}

			return (float) $a < (float) $b ? -1 : 1;
		} );

		return $ids;
	}

	/**
	 * Gets sorted choice values from a field choices collection.
	 *
	 * @since 2.11.0
	 *
	 * @param mixed $choices Field choices.
	 *
	 * @return array Choice values.
	 */
	private static function get_choice_values( $choices ) {
		if ( empty( $choices ) || ! is_array( $choices ) ) {
			return [];
		}

		$values = [];

		foreach ( $choices as $choice ) {
			$value = self::get_field_value( $choice, 'value' );

			if ( null === $value || '' === (string) $value ) {
				$value = self::get_field_value( $choice, 'text', '' );
			}

			$values[] = (string) $value;
		}

		sort( $values, SORT_STRING );

		return $values;
	}

	/**
	 * Gets the format value used by structurally sensitive field types.
	 *
	 * @since 2.11.0
	 *
	 * @param mixed  $field Gravity Forms field object or array.
	 * @param string $type  Field type.
	 *
	 * @return string Field format.
	 */
	private static function get_field_format( $field, $type ) {
		$format_keys = [
			'date'   => 'dateFormat',
			'time'   => 'timeFormat',
			'phone'  => 'phoneFormat',
			'number' => 'numberFormat',
		];

		if ( empty( $format_keys[ $type ] ) ) {
			return '';
		}

		return (string) self::get_field_value( $field, $format_keys[ $type ], '' );
	}

	/**
	 * Normalizes a CSV header for matching.
	 *
	 * @since 2.11.0
	 *
	 * @param mixed $header Header value.
	 * @param int   $index  Header index.
	 *
	 * @return string Normalized header.
	 */
	private static function normalize_header( $header, $index ) {
		$header = (string) $header;

		if ( 0 === (int) $index ) {
			$header = preg_replace( '/^\xEF\xBB\xBF/', '', $header );
		}

		return strtolower( trim( $header ) );
	}

	/**
	 * Removes profile-only schema keys before passing rules to Batch.
	 *
	 * @since 2.11.0
	 *
	 * @param array $rule Profile schema rule.
	 *
	 * @return array Batch schema rule.
	 */
	private static function strip_profile_rule_keys( $rule ) {
		unset( $rule['type'] );

		return $rule;
	}

	/**
	 * Builds a field type warning for profile rules saved against older field structure.
	 *
	 * @since 2.11.0
	 *
	 * @param int   $rule_index Rule index.
	 * @param array $rule       Profile schema rule.
	 * @param array $form       Gravity Forms form data.
	 *
	 * @return array|null Warning data or null.
	 */
	private static function get_field_type_warning( $rule_index, $rule, $form ) {
		if ( empty( $rule['type'] ) || empty( $rule['field'] ) || ! is_numeric( $rule['field'] ) ) {
			return null;
		}

		$field = GFFormsModel::get_field( $form, $rule['field'] );

		if ( ! $field ) {
			return null;
		}

		$current_type = self::get_field_value( $field, 'type', '' );

		if ( (string) $rule['type'] === (string) $current_type ) {
			return null;
		}

		return [
			'rule'    => $rule_index,
			'code'    => 'gravityimport/profile/field_type_changed',
			'message' => sprintf(
				__( 'Rule %1$d maps to field %2$s, whose type changed from %3$s to %4$s.', 'gk-gravityimport' ),
				$rule_index,
				$rule['field'],
				$rule['type'],
				$current_type
			),
		];
	}
}
