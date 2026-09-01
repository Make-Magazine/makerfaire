<?php

namespace GravityKit\GravityView\QueryFilters\MergeTag;

use GFAPI;
use GFCommon;

/**
 * Resolves the `form:` merge-tag modifier against joined-form entries.
 *
 * A field merge tag may target a field on another form through a `form:<id>` modifier, e.g.
 * `{:1:form:4}` reads field 1 from form 4. When a rendered entry carries joined entries under the
 * `_multi` key (`$entry['_multi'][ form_id ]`), the value is resolved from that joined entry. The
 * condition factory uses {@see self::extract_form_id()} to compare columns across forms in the query itself.
 *
 * @since 2.14.0
 */
final class FormMergeModifier {
	/**
	 * Constant name marking the merge-tag filter as registered for this request.
	 *
	 * The library ships under multiple class prefixes; a shared constant name keeps the filter
	 * registered exactly once regardless of how many prefixed copies load.
	 *
	 * @since 2.14.0
	 */
	private const REGISTERED = 'GK_QUERY_FILTERS_FORM_MERGE_MODIFIER';

	/**
	 * Cheap prescreen for text that may contain a `form:` modifier.
	 *
	 * Matches every spelling the segment matchers accept: `form` as the first modifier
	 * (preceded by the field-id colon) or as any later one (preceded by a comma), with
	 * optional whitespace around its colon. A literal `:form:` test misses the
	 * comma-preceded and whitespace-padded forms, which then skip the callback entirely
	 * and render from the primary form.
	 *
	 * @since 2.16.0
	 *
	 * @see   self::without_form_modifier() The `/^form\s*:\s*\d+$/` segment matcher.
	 * @see   self::extract_form_id()       The `/^\s*form\s*:\s*(\d+)\s*$/` segment matcher.
	 */
	private const MODIFIER_PRESCREEN = '/[:,]\s*form\s*:\s*\d+/i';

	/**
	 * Guards against re-entrancy while resolving a joined value.
	 *
	 * @since 2.14.0
	 *
	 * @var bool
	 */
	private static $resolving = false;

	/**
	 * Registers the merge-tag resolver a single time per request.
	 *
	 * @since 2.14.0
	 */
	public static function register(): void {
		if ( defined( self::REGISTERED ) ) {
			return;
		}

		define( self::REGISTERED, true );

		add_filter( 'gform_pre_replace_merge_tags', [ self::class, 'replace' ], 10, 7 );
	}

	/**
	 * Returns the target form ID encoded in a field merge tag's `form:` modifier.
	 *
	 * @since 2.14.0
	 *
	 * @param mixed $value The filter value.
	 *
	 * @return int|null The target form ID, or `null` when no `form:` modifier is present.
	 */
	public static function extract_form_id( $value ): ?int {
		if ( ! is_string( $value ) ) {
			return null;
		}

		if ( ! preg_match( '/^{[^{]*?:\d+(?:\.\w+)?:(.*)}$/', trim( $value ), $matches ) ) {
			return null;
		}

		return self::form_id_from_modifiers( $matches[1] );
	}

	/**
	 * Returns the referenced field ID when the value is a single field merge tag.
	 *
	 * The numeric capture group is the field ID, which distinguishes a field merge tag (e.g.
	 * `{Label:2}` or `{Label:2.3}`) from context tags such as `{date}`, `{user:user_email}`
	 * or `{get:param}`.
	 *
	 * @since 2.14.0
	 *
	 * @param mixed $value The filter value.
	 *
	 * @return string|null The field ID (e.g. `2` or `2.3`), or `null` when the value is not a field merge tag.
	 */
	public static function extract_field_id( $value ): ?string {
		if ( ! is_string( $value ) ) {
			return null;
		}

		$value = trim( $value );
		if ( '' === $value ) {
			return null;
		}

		return preg_match( '/^{[^{]*?:(\d+(?:\.\w+)?)(?::.*?)?}$/', $value, $matches ) ? $matches[1] : null;
	}

	/**
	 * Whether the value is a field merge tag pointing at an existing field.
	 *
	 * The target form comes from the `form:` modifier when present, otherwise from the provided form.
	 * Used to decide whether a value should be preserved for the condition factory rather than
	 * resolved to an (empty) literal at query time.
	 *
	 * @since 2.14.0
	 *
	 * @param mixed $value The filter value.
	 * @param array $form  The fallback form, used when the value carries no `form:` modifier.
	 *
	 * @return bool
	 */
	public static function references_existing_field( $value, array $form = [] ): bool {
		$field_id = self::extract_field_id( $value );
		if ( null === $field_id ) {
			return false;
		}

		$form_id = self::extract_form_id( $value ) ?: ( $form['id'] ?? 0 );
		if ( ! $form_id ) {
			return false;
		}

		return (bool) GFAPI::get_field( $form_id, (int) $field_id );
	}

	/**
	 * Replaces `{:field:form:<id>}` tags with values from joined entries before Gravity Forms parses them.
	 *
	 * @since 2.14.0
	 *
	 * @param string $text       The text being processed.
	 * @param array  $form       The primary form.
	 * @param array  $entry      The primary entry, optionally carrying joined entries under `_multi`.
	 * @param bool   $url_encode Whether to URL-encode values.
	 * @param bool   $esc_html   Whether to escape HTML.
	 * @param bool   $nl2br      Whether to convert newlines to line breaks.
	 * @param string $format     The output format.
	 *
	 * @return string
	 */
	public static function replace(
		$text,
		$form = [],
		$entry = [],
		$url_encode = false,
		$esc_html = true,
		$nl2br = true,
		$format = 'html'
	) {
		if ( self::$resolving || ! is_string( $text ) || ! preg_match( self::MODIFIER_PRESCREEN, $text ) ) {
			return $text;
		}

		return (string) preg_replace_callback(
			'/{[^{]*?:(\d+(?:\.\w+)?):([^{}]*)}/',
			static function ( array $match ) use ( $form, $entry, $url_encode, $esc_html, $nl2br, $format ): string {
				$target_form_id = self::form_id_from_modifiers( $match[2] );
				if ( null === $target_form_id ) {
					return $match[0];
				}

				$target = self::target_entry( $entry, $form, $target_form_id );
				if ( null === $target ) {
					return '';
				}

				return self::resolve(
					self::without_form_modifier( $match[1], $match[2] ),
					$target['form'],
					$target['entry'],
					$url_encode,
					$esc_html,
					$nl2br,
					$format
				);
			},
			$text
		);
	}

	/**
	 * Returns the form and entry to resolve a target form ID against.
	 *
	 * @since 2.14.0
	 *
	 * @param mixed $entry          The primary entry.
	 * @param array $form           The primary form.
	 * @param int   $target_form_id The target form ID.
	 *
	 * @return array{form: array, entry: array}|null The form/entry pair, or `null` when unavailable.
	 */
	private static function target_entry( $entry, array $form, int $target_form_id ): ?array {
		if ( ! is_array( $entry ) ) {
			return null;
		}

		$joined = $entry['_multi'][ $target_form_id ] ?? null;
		if ( is_array( $joined ) ) {
			return [ 'form' => GFAPI::get_form( $target_form_id ) ?: $form, 'entry' => $joined ];
		}

		if ( (int) ( $entry['form_id'] ?? 0 ) === $target_form_id ) {
			return [ 'form' => $form, 'entry' => $entry ];
		}

		return null;
	}

	/**
	 * Resolves a single field merge tag against the given form and entry.
	 *
	 * @since 2.14.0
	 *
	 * @param string $tag        The field merge tag.
	 * @param array  $form       The form.
	 * @param array  $entry      The entry.
	 * @param bool   $url_encode Whether to URL-encode values.
	 * @param bool   $esc_html   Whether to escape HTML.
	 * @param bool   $nl2br      Whether to convert newlines to line breaks.
	 * @param string $format     The output format.
	 *
	 * @return string
	 */
	private static function resolve(
		string $tag,
		array $form,
		array $entry,
		bool $url_encode,
		bool $esc_html,
		bool $nl2br,
		string $format
	): string {
		self::$resolving = true;

		try {
			$value = class_exists( 'GravityView_API' )
				? \GravityView_API::replace_variables( $tag, $form, $entry, $url_encode, $esc_html, $nl2br, $format )
				: GFCommon::replace_variables( $tag, $form, $entry, $url_encode, $esc_html, $nl2br, $format );
		} finally {
			self::$resolving = false;
		}

		return (string) $value;
	}

	/**
	 * Rebuilds a field merge tag without its `form:` modifier, preserving any other modifiers.
	 *
	 * @since 2.14.0
	 *
	 * @param string $field_id  The field ID.
	 * @param string $modifiers The raw modifier string.
	 *
	 * @return string
	 */
	private static function without_form_modifier( string $field_id, string $modifiers ): string {
		$kept = array_filter(
			array_map( 'trim', preg_split( '/(?<!\\\\),/', $modifiers ) ),
			static function ( string $modifier ): bool {
				return '' !== $modifier && ! preg_match( '/^form\s*:\s*\d+$/', $modifier );
			}
		);

		return $kept
			? sprintf( '{:%s:%s}', $field_id, implode( ',', $kept ) )
			: sprintf( '{:%s}', $field_id );
	}

	/**
	 * Extracts the form ID from a comma-separated modifier string.
	 *
	 * @since 2.14.0
	 *
	 * @param string $modifiers The raw modifier string.
	 *
	 * @return int|null
	 */
	private static function form_id_from_modifiers( string $modifiers ): ?int {
		foreach ( preg_split( '/(?<!\\\\),/', $modifiers ) as $modifier ) {
			if ( preg_match( '/^\s*form\s*:\s*(\d+)\s*$/', $modifier, $matches ) ) {
				$form_id = (int) $matches[1];

				return $form_id > 0 ? $form_id : null;
			}
		}

		return null;
	}
}
