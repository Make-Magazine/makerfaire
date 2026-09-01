<?php
/**
 * Consolidated View design-token registry.
 *
 * Owns the *what*: every user-customisable design token across color,
 * typography, dimensions, border, shadow, layout, and motion, exposed
 * under a single dot-namespaced address space (`color.primary`,
 * `typography.font_size_base`, `dimensions.entry_padding_top`, etc.).
 * Each entry conforms to a single normalised contract so consumers can
 * iterate without per-category branching.
 *
 * Owns the CSS emission too: `emit_css_declarations()` returns the
 * declaration list that applies the saved per-View token overrides, and
 * `prefers_contrast_css()` returns the companion block emitted at the
 * dual-selector scope (`scoped_selector()`).
 *
 * Does NOT know that PageBuilder exists. PageBuilder is a Consumer:
 * each builder subclass reads tokens via this class's public API and
 * maps `control` to its own native control system.
 *
 * @package GravityKit\GravityView
 * @since   TBD
 */

namespace GravityKit\GravityView\Settings;

use GravityKit\GravityView\Settings\ColorMix;
use GravityKit\GravityView\Settings\TokenRegistry;
use GravityKit\GravityView\Settings\ViewSettings;

defined( 'ABSPATH' ) || die();

/**
 * Single source of truth for View styling tokens and their CSS emission.
 *
 * @since 3.0.0
 */
final class ViewStyles {

	/**
	 * Token categories the persistence pipeline reads and writes.
	 *
	 * Every dot-namespaced token id is `{category}.{slug}`; these are the
	 * recognised category buckets inside a View's `template_settings`
	 * array. `sanitize_payload()` cleans these buckets in place and drops
	 * unrecognised bucket-shaped keys; `saved_values()` reads only these.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	private const CATEGORIES = [ 'color', 'typography', 'dimensions', 'border', 'shadow', 'layout', 'motion' ];

	/**
	 * Memoisation cache for the flattened token registry.
	 *
	 * @since 3.0.0
	 *
	 * @var array|null
	 */
	private static $tokens_cache = null;


	/**
	 * Returns every token, keyed by its canonical dot-namespaced id.
	 *
	 * Each entry is normalised:
	 *   - id, category, group, label, desc, control, default, css_var
	 *   - options (when control = select)
	 *   - unit, min, max, step (when control = number)
	 *
	 * The filtered result is memoised for the rest of the request, so
	 * `gk/gravityview/theme/tokens` filters must be registered
	 * before the first render that touches tokens (hook them by `init`).
	 * Call `flush()` to invalidate the cache after a runtime registry
	 * change.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array>
	 */
	public static function tokens(): array {
		if ( null !== self::$tokens_cache ) {
			return self::$tokens_cache;
		}

		// Delegate to the canonical TokenRegistry. `studio_tokens()` returns
		// the user-overridable subset (entries flagged `'studio' => true`)
		// with full Studio metadata (label, desc, control, default, css_var,
		// hover_slug, contrast pairing, etc.), keyed by dot-namespaced slug.
		//
		// Entries arrive in the canonical shape already; we still stamp
		// `id` for back-compat and ensure the per-category default `control`
		// is set when the registry omits it (palette → `color`).
		$out = [];

		foreach ( TokenRegistry::studio_tokens() as $id => $token ) {
			$token['id'] = $id;
			if ( 'color' === ( $token['category'] ?? '' ) && empty( $token['control'] ) ) {
				$token['control'] = 'color';
			}
			$out[ $id ] = $token;
		}

		// Third-party extension happens upstream: TokenRegistry::all()
		// applies (and validates) the `gk/gravityview/theme/tokens`
		// filter, and `studio_tokens()` derives from that filtered set.
		// Applying the same hook again here would run callbacks twice per
		// request against two different array shapes.
		self::$tokens_cache = $out;

		return $out;
	}

	/**
	 * Sanitises a single token value against its definition.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token_id Dot-namespaced token id.
	 * @param mixed  $value    Raw value (string, numeric, etc.).
	 *
	 * @return string Sanitised value, or empty string if invalid.
	 */
	public static function sanitize( string $token_id, $value ): string {
		$tokens = self::tokens();

		if ( ! isset( $tokens[ $token_id ] ) ) {
			return '';
		}

		$token = $tokens[ $token_id ];
		$value = is_string( $value ) ? trim( $value ) : (string) $value;

		if ( '' === $value ) {
			return '';
		}

		$control = $token['control'] ?? '';

		switch ( $control ) {
			case 'color':
				// Hex (#rgb, #rgba, #rrggbb, #rrggbbaa).
				if ( preg_match( '/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value ) ) {
					return $value;
				}
				// theme.json preset references — opt-in for block themes.
				if ( preg_match( '/^var\(--wp--preset--(color|font-size|font-family|spacing)--[a-z0-9-]+\)$/', $value ) ) {
					return $value;
				}
				return '';

			case 'select':
				// `options` is a plain list of allowed value strings, and the
				// stored value IS one of those strings byte-for-byte.
				if ( in_array( $value, $token['options'] ?? [], true ) ) {
					return $value;
				}

				return '';

			case 'toggle':
				// wp_validate_boolean() reads every non-empty string except
				// "false" as true, so "no" and "off" would turn the flag on.
				$falsey = in_array( strtolower( $value ), [ '0', 'no', 'off', 'false', 'disabled', 'n' ], true );

				return $falsey ? '0' : '1';

			case 'number':
				// Accept either a bare number (legacy: `'24'`) or a
				// combined value+unit string (`'24px'`, `'1.5rem'`).
				// On output the value always carries its unit, so
				// `emit_css_declarations()` can dump the saved value
				// verbatim without per-token bookkeeping.
				if ( ! preg_match( '/^(-?\d+(?:\.\d+)?)\s*([a-zA-Z%]*)$/', $value, $m ) ) {
					return '';
				}

				$num  = (float) $m[1];
				$unit = $m[2] ?? '';

				if ( isset( $token['min'] ) && $num < (float) $token['min'] ) {
					return '';
				}
				if ( isset( $token['max'] ) && $num > (float) $token['max'] ) {
					return '';
				}

				// Allowed units: prefer the new `units` array, fall
				// back to the legacy single `unit` field, treat
				// missing as "unitless allowed" (e.g. line-height).
				$allowed_units = [];
				if ( ! empty( $token['units'] ) && is_array( $token['units'] ) ) {
					$allowed_units = $token['units'];
				} elseif ( ! empty( $token['unit'] ) ) {
					$allowed_units = [ $token['unit'] ];
				}

				if ( '' !== $unit ) {
					if ( ! empty( $allowed_units ) && ! in_array( $unit, $allowed_units, true ) ) {
						return '';
					}
				} elseif ( ! empty( $allowed_units ) ) {
					// Bare number plus an allowed-units list — assume
					// the first as the default and bake it in.
					$unit = $allowed_units[0];
				}

				$num_str = ( floor( $num ) === $num )
					? (string) (int) $num
					: rtrim( rtrim( sprintf( '%.4F', $num ), '0' ), '.' );

				return '' !== $unit ? $num_str . $unit : $num_str;

			case 'text':
				$value = sanitize_text_field( $value );

				// Defence-in-depth for the inline-<style> sink. Text-control
				// values (easing functions, durations, colors, letter-spacing)
				// never legitimately carry CSS rule/declaration delimiters, so
				// reject any value containing one — otherwise a `}` could close
				// the per-View scoped rule early and inject a page-wide rule.
				// This mirrors the grammar rejection the color/number/select/
				// toggle branches already enforce; `text` was the only studio
				// control emitting verbatim without it.
				if ( preg_match( '/[{}<>;@\\\\]/', $value ) ) {
					return '';
				}

				return $value;

			default:
				return '';
		}
	}

	/**
	 * Sanitises an incoming `template_settings` payload before it is
	 * persisted to post meta.
	 *
	 * Recognised token buckets (see `self::CATEGORIES`) are cleaned in
	 * place: each slug runs through `sanitize()` and invalid values are
	 * dropped. Unrecognised bucket-shaped keys (string-keyed arrays under
	 * a key that is neither a registry category nor an array-valued View
	 * setting) are removed entirely so unsanitised data is never
	 * persisted waiting for a future category to make it live. Scalar
	 * settings and list-style arrays (e.g. the multisort `sort_field[]`
	 * inputs) flow through unchanged, so this is safe to compose with the
	 * existing save pipeline.
	 *
	 * @since 3.0.0
	 *
	 * @param array $template_settings Raw `template_settings` array (e.g. from `$_POST`).
	 *
	 * @return array Sanitised array with the design buckets cleaned.
	 */
	public static function sanitize_payload( array $template_settings ): array {
		// Resolved on first need only: building it runs every registered
		// `gravityview/view/settings/defaults` callback, and a payload
		// with no drop candidate should not pay for that.
		$registered_settings = null;

		foreach ( $template_settings as $key => $value ) {
			if ( ! in_array( $key, self::CATEGORIES, true ) ) {
				// A string-keyed array under a key that belongs to no
				// registry category and to no setting that declares an
				// array value is shaped like a token bucket but owned by
				// nothing: drop it. `checkboxes` settings post a
				// string-keyed array, so the declaration check is what
				// keeps them from being lost. Gating on registration
				// alone would let an array through for a setting whose
				// consumers only ever expect a string.
				if ( is_array( $value ) && ! wp_is_numeric_array( $value ) ) {
					$registered_settings = $registered_settings ?? ViewSettings::defaults( true );

					if ( ! ViewSettings::declares_array_value( $registered_settings[ $key ] ?? null ) ) {
						unset( $template_settings[ $key ] );

						// Logged because the drop is otherwise invisible: the
						// View saves, and only the one setting goes missing.
						gravityview()->log->debug(
							sprintf(
								'[sanitize_payload] Dropped View setting "%s": it posted a string-keyed array but is not registered with type "checkboxes" or an array default value.',
								$key
							)
						);
					}
				}
				continue;
			}

			if ( ! is_array( $value ) ) {
				$template_settings[ $key ] = [];
				continue;
			}

			$cleaned = [];

			foreach ( $value as $slug => $raw ) {
				if ( ! is_string( $slug ) || '' === $slug ) {
					continue;
				}

				$token_id  = $key . '.' . $slug;
				$sanitised = self::sanitize( $token_id, $raw );

				if ( '' !== $sanitised ) {
					$cleaned[ $slug ] = $sanitised;
				}
			}

			$template_settings[ $key ] = $cleaned;
		}

		return $template_settings;
	}

	/**
	 * Returns saved (sanitised) values for every category, keyed by
	 * dot-namespaced id.
	 *
	 * Defence-in-depth: even though `sanitize_payload()` runs on write,
	 * read-time sanitisation guards against legacy data and pre-3.0
	 * import paths that bypass `save_postdata()`.
	 *
	 * @since 3.0.0
	 *
	 * @param array       $template_settings Raw `template_settings` array.
	 * @param string|null $category          Optional category to scope the result.
	 *
	 * @return array<string, string>
	 */
	public static function saved_values( array $template_settings, ?string $category = null ): array {
		$out        = [];
		$categories = null === $category
			? self::CATEGORIES
			: [ $category ];

		foreach ( $categories as $cat ) {
			$raw = $template_settings[ $cat ] ?? [];
			if ( ! is_array( $raw ) ) {
				continue;
			}

			foreach ( $raw as $slug => $value ) {
				$token_id  = $cat . '.' . $slug;
				$sanitised = self::sanitize( $token_id, $value );
				if ( '' !== $sanitised ) {
					$out[ $token_id ] = $sanitised;
				}
			}
		}

		return $out;
	}

	/**
	 * Returns the dual-selector wrap (outer-wrapper arm + container arm)
	 * for a View.
	 *
	 * The `[id^="gv-view-{ID}-"]` arm catches outer wrappers (so widget
	 * zones inherit) plus Edit Entry, and matches every embed of the View
	 * on the page (`-2`, `-3`, ...). The `.gv-container.gv-container-{ID}`
	 * arm catches every layout's container plus DIY single's
	 * outer-as-container. Together they reach every layout's DOM at
	 * specificity `(0,2,0)`.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id The View's id.
	 *
	 * @return string The selector string, ready to drop into a CSS rule.
	 */
	public static function scoped_selector( int $view_id ): string {
		return sprintf(
			'[id^="gv-view-%d-"], .gv-container.gv-container-%d',
			$view_id,
			$view_id
		);
	}

	/**
	 * Returns the `prefers-contrast: more` re-statement block for a
	 * View's overridden contrast-relevant tokens, or '' when no such
	 * token is overridden.
	 *
	 * The stylesheet ships a boosted palette under
	 * `@media (prefers-contrast: more)` at `.gv-themed` specificity
	 * `(0,1,0)`. A per-View token override lands at the dual selector's
	 * `(0,2,0)` and would beat that boost, so each overridden
	 * contrast-relevant custom property is re-stated here at the same
	 * `(0,2,0)` specificity inside the media query. Tokens the customer
	 * did not override stay covered by the stylesheet block, keeping the
	 * inline payload proportional to what changed.
	 *
	 * Boosted values follow the spec section "prefers-contrast: more":
	 * 9.4:1 / 8.7:1 / 9.2:1 / 7.6:1 against `--gv-color-surface` `#fff`.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id   The View's id.
	 * @param array $overrides Override map keyed by dot-namespaced token
	 *                         id (`color.text_secondary`) or by CSS custom
	 *                         property (`--gv-color-text-secondary`).
	 *
	 * @return string CSS `@media` rule, or empty string.
	 */
	public static function prefers_contrast_css( int $view_id, array $overrides ): string {
		if ( $view_id <= 0 || empty( $overrides ) ) {
			return '';
		}

		$boosted = [
			'--gv-color-text-secondary' => '#3d4757',
			'--gv-color-text-muted'     => '#3d4757',
			'--gv-color-placeholder'    => '#3d4757',
			'--gv-color-border'         => 'var(--gv-color-border-strong)',
			'--gv-color-success'        => '#155724',
			'--gv-color-error'          => '#8a1e07',
			'--gv-color-info'           => '#0a5e74',
			'--gv-focus-ring-width'     => '3px',
		];

		$tokens          = self::tokens();
		$overridden_vars = [];

		foreach ( $overrides as $key => $override_value ) {
			$key = (string) $key;
			if ( 0 === strpos( $key, '--' ) ) {
				$overridden_vars[ $key ] = $override_value;
				continue;
			}
			$css_var = $tokens[ $key ]['css_var'] ?? '';
			if ( '' !== $css_var ) {
				$overridden_vars[ $css_var ] = $override_value;
			}
		}

		$declarations = [];
		foreach ( $boosted as $css_var => $value ) {
			if ( ! array_key_exists( $css_var, $overridden_vars ) ) {
				continue;
			}

			// The focus-ring boost only ever widens the ring: a customer
			// override wider than the boost is kept via max(). The override
			// value is a sanitized literal at this point, so embedding it
			// in max() is safe.
			if ( '--gv-focus-ring-width' === $css_var ) {
				$override_value = trim( (string) $overridden_vars[ $css_var ] );
				if ( '' !== $override_value && preg_match( '/^[0-9.]+[a-z%]+$/i', $override_value ) ) {
					$value = sprintf( 'max(3px, %s)', $override_value );
				}
			}

			$declarations[] = sprintf( '%s: %s', $css_var, $value );
		}

		if ( empty( $declarations ) ) {
			return '';
		}

		return sprintf(
			'@media (prefers-contrast: more) { %s { %s; } }',
			self::scoped_selector( $view_id ),
			implode( '; ', $declarations )
		);
	}

	/**
	 * Returns the saved-token declarations for a View as a single
	 * semicolon-joined string, *without* a wrapping selector. Lets
	 * callers compose tokens with their own declarations under one
	 * rule (e.g. Frontend's grid metrics) instead of emitting two
	 * separate rules with the same selector.
	 *
	 * Override values equal to the token's registry default are skipped
	 * intentionally (except state-suffixed tokens): the stylesheet layer
	 * already supplies the default, so re-emitting it would only bloat
	 * the inline payload. The payload stays proportional to what the
	 * customer actually changed.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view The View whose overrides to emit.
	 *
	 * @return string Declaration list, or empty string if nothing
	 *                to emit. No trailing semicolon.
	 */
	public static function emit_css_declarations( $view ): string {
		$view_id = $view ? (int) $view->ID : 0;
		if ( $view_id <= 0 ) {
			return '';
		}

		$settings = method_exists( $view->settings, 'all' ) ? (array) $view->settings->all() : [];
		$saved    = self::saved_values( $settings );

		// Run filter pipeline — site-wide first, then per-view. Filter
		// authors return a token-slug → value map. See
		// `self::OVERRIDE_PRECEDENCE` for the full source order.
		$saved = self::apply_override_filters( $view_id, $saved );

		if ( empty( $saved ) ) {
			return '';
		}

		$tokens               = self::tokens();
		$overrides            = [];
		$overrides_by_css_var = [];

		foreach ( $saved as $token_id => $value ) {
			if ( ! isset( $tokens[ $token_id ] ) ) {
				continue;
			}

			$token   = $tokens[ $token_id ];
			$css_var = $token['css_var'] ?? '';
			if ( '' === $css_var ) {
				continue;
			}

			// Non-state tokens at their registry default fall through
			// to the SCSS-layer default, so re-emitting buys nothing
			// and bloats the inline `<style>`. State-suffixed tokens
			// (`*_hover`, `*_active`, `*_focus`, `*_visited`,
			// `*_disabled`) are kept regardless — that includes
			// childless variants like `text_disabled` and
			// `shadow_focus` that have no resting parent in the
			// pairing graph but still carry interaction intent.
			if (
				isset( $token['default'] )
				&& (string) $value === (string) $token['default']
				&& ! self::has_state_suffix( $token_id )
			) {
				continue;
			}

			// Belt-and-braces skip for any state variant whose
			// registry default matches its resting parent's default
			// (so the resting cascade already covers it). No token
			// in the current registry hits this branch, but keep it
			// for variants explicitly designed to fall through.
			if (
				isset( $token['default'] )
				&& (string) $value === (string) $token['default']
				&& self::variant_default_tracks_resting( $token_id, $tokens )
			) {
				continue;
			}

			// Stash by css_var so the ColorMix hover resolver below can
			// see the customer's base-color overrides. Saved values
			// always carry their unit (sanitize bakes the default unit
			// in for legacy bare numbers), so the value is verbatim.
			$overrides_by_css_var[ $css_var ] = $value;
		}

		// Saved CSS must never carry runtime `color-mix()` calls. For
		// every base color the customer overrode, recompute the matching
		// `*-hover` token via `ColorMix::mix( base, towards, pct )` and
		// emit the literal hex. Customer-authored hover overrides take
		// precedence inside `resolve_color_mix_hovers()`. Skipping this
		// on the saved-frontend path leaves the wrapper without a
		// matching `*-hover`, and the browser cascades back to the base
		// scope's pre-override hover.
		$overrides_by_css_var = self::resolve_color_mix_hovers( $overrides_by_css_var );

		// Same rule, applied to the shadow scale. The stylesheet guards its
		// `color-mix()` levels behind `@supports`; per-View inline CSS has no
		// such guard, so an unresolved value is invalid-at-computed-value-time
		// below the `color-mix()` floor and the shadow vanishes outright.
		$overrides_by_css_var = self::resolve_shadow_color_mix( $overrides_by_css_var );

		// Re-state the alias layer on the per-View wrapper so its
		// `var()` deps re-substitute against wrapper-level overrides.
		// Aliases come before user overrides so a customer override of
		// the same CSS var wins on equal specificity. `resolved_aliases()`
		// is always emitted (literal values, no deps to filter on);
		// `substituted_aliases()` is filtered to the deps actually
		// overridden by the customer to keep the inline payload
		// proportional to what changed.
		foreach ( self::resolved_aliases() as $declaration ) {
			$overrides[] = $declaration;
		}
		foreach (
			self::aliases_for_overridden_deps(
				self::substituted_aliases(),
				array_keys( $overrides_by_css_var )
			) as $declaration
		) {
			$overrides[] = $declaration;
		}

		foreach ( $overrides_by_css_var as $css_var => $value ) {
			$overrides[] = sprintf( '%s: %s', $css_var, $value );
		}

		// The stylesheet default gives field labels uppercase tracking
		// (0.05em), which reads wrong on any other casing. Declarations are
		// emitted in order into one list, so this one would also beat a
		// saved letter-spacing above: derive it only when there isn't one.
		$transform = $saved['typography.field_label_transform'] ?? null;
		if (
			null !== $transform
			&& 'uppercase' !== $transform
			&& ! isset( $saved['typography.field_label_letter_spacing'] )
		) {
			$overrides[] = '--gv-field-label-letter-spacing: 0';
		}

		// Last-resort escape hatch: when the `important` filter opts in,
		// stamp every declaration `!important` so the View's tokens win
		// against a host theme / builder that ships `!important` on the
		// same custom properties. Default-false, so no effect unless a
		// developer enables it.
		$overrides = self::maybe_force_important( $view_id, $overrides );

		return implode( '; ', $overrides );
	}

	/**
	 * Override-source precedence — published as a class constant so filter
	 * authors can reason about what data they're seeing. Lower-index
	 * sources merge first; later sources can overwrite earlier ones.
	 *
	 * Sources 1-2 merge in PHP (this method's filter pipeline).
	 * Sources 3-5 land in CSS at later cascade-order positions; they
	 * are not visible to PHP filter authors.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	const OVERRIDE_PRECEDENCE = [
		'site_wide_filter'          => 'gk/gravityview/theme/overrides',
		'per_view_filter'           => 'gk/gravityview/theme/view/{view_id}/overrides',
		'preset_writes'             => '(in CSS: preset bundle)',
		'per_token_customer_writes' => '(in CSS: saved Studio values)',
		'custom_css'                => '(in CSS: last emit position)',
	];

	/**
	 * Runs the override-filter pipeline for one View.
	 *
	 * Fires `gk/gravityview/theme/overrides` (site-wide, applies
	 * to every View) followed by
	 * `gk/gravityview/theme/view/{$view_id}/overrides` (per-view,
	 * applies to one View). The per-view filter sees the site-wide
	 * filter's writes as its first argument and can overwrite them.
	 *
	 * Filter-supplied values equal to a token's registry default are
	 * elided from emission like any other override (see
	 * `emit_css_declarations()`); return a different value when the goal
	 * is to force a declaration into the inline payload.
	 *
	 * @since 3.0.0
	 *
	 * @param int                   $view_id    Numeric View id.
	 * @param array<string, string> $overrides  Token-slug → value map.
	 *
	 * @return array<string, string>
	 */
	private static function apply_override_filters( int $view_id, array $overrides ): array {
		/**
		 * Filters the site-wide override map applied to every View.
		 *
		 * Fires first in the pipeline. Per-view filter sees the
		 * post-merge result.
		 *
		 * @since 3.0.0
		 *
		 * @param array $overrides Token-slug → value map.
		 */
		$overrides = (array) apply_filters( 'gk/gravityview/theme/overrides', $overrides );

		/**
		 * Filters the per-view override map for one View id.
		 *
		 * @since 3.0.0
		 *
		 * @param array $overrides Token-slug → value map (site-wide
		 *                         filter already applied).
		 * @param int   $view_id   The View's numeric id.
		 */
		$overrides = (array) apply_filters( "gk/gravityview/theme/view/{$view_id}/overrides", $overrides, $view_id );

		return $overrides;
	}

	/**
	 * Conditionally stamps every declaration `!important` when the
	 * site-wide or per-view `important` filter returns true.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id      Numeric View id.
	 * @param string[] $declarations Pre-composed declarations.
	 *
	 * @return string[]
	 */
	private static function maybe_force_important( int $view_id, array $declarations ): array {
		/**
		 * Filters whether to mark every customer-written token `!important`.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $important Default false.
		 */
		$site_wide = (bool) apply_filters( 'gk/gravityview/theme/important', false );

		/**
		 * Filters whether to mark every customer-written token `!important`
		 * for a single View id.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $important Site-wide filter result.
		 * @param int  $view_id   The View's numeric id.
		 */
		$per_view = (bool) apply_filters( "gk/gravityview/theme/view/{$view_id}/important", $site_wide, $view_id );

		if ( ! $per_view ) {
			return $declarations;
		}

		return array_map(
			static function ( string $decl ): string {
				// Skip declarations that already carry `!important`.
				if ( false !== stripos( $decl, '!important' ) ) {
					return $decl;
				}
				return $decl . ' !important';
			},
			$declarations
		);
	}

	/**
	 * Pre-resolves the `*-hover` companions for every customer-overridden
	 * base color, via `ColorMix::mix()`. Output is a literal hex string;
	 * saved CSS never carries a runtime `color-mix()` call.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, string> $overrides_by_css_var CSS-var → value map.
	 *
	 * @return array<string, string>
	 */
	private static function resolve_color_mix_hovers( array $overrides_by_css_var ): array {
		// Base → [ hover_var, pct toward target, target hex ].
		$base_to_hover_pairs = [
			'--gv-color-primary' => [ '--gv-color-primary-hover', 90, '#000000' ],
			'--gv-color-error'   => [ '--gv-color-error-hover', 90, '#000000' ],
			'--gv-color-success' => [ '--gv-color-success-hover', 90, '#000000' ],
			'--gv-color-info'    => [ '--gv-color-info-hover', 90, '#000000' ],
		];

		foreach ( $base_to_hover_pairs as $base_var => $pair ) {
			[ $hover_var, $pct, $towards ] = $pair;
			if ( ! isset( $overrides_by_css_var[ $base_var ] ) ) {
				continue;
			}
			$base_hex = $overrides_by_css_var[ $base_var ];

			// Only operate on hex inputs — anything else (var(), named
			// colors, color-mix expressions) is left as-is so callers
			// who deliberately pass an alias still get the live recipe.
			if ( ! preg_match( '/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $base_hex ) ) {
				continue;
			}

			// Don't clobber an explicit customer hover override.
			if ( isset( $overrides_by_css_var[ $hover_var ] ) ) {
				continue;
			}

			$overrides_by_css_var[ $hover_var ] = ColorMix::mix( $base_hex, $towards, (int) $pct );
		}

		return $overrides_by_css_var;
	}

	/**
	 * Sanitise customer-authored Custom CSS before emission.
	 *
	 * Runs on the View's `custom_css` setting on the Themes
	 * emission path, where it is wrapped in the View's per-id scope at
	 * render time (see `Frontend::get_view_custom_css_for_theme()`).
	 *
	 * Only a `</style` sequence can escape the inline `<style>` element,
	 * so that sequence is neutralised case-insensitively, and `<` is
	 * stripped when it begins a tag-like sequence (followed by a letter,
	 * `/`, `!`, or `?`). Bare `<` and all `>` are left intact: `>` is the
	 * child combinator and both appear as comparison operators in
	 * range-syntax media queries (`@media (width > 600px)`). `@import`
	 * and JavaScript-protocol references are rejected because neither
	 * belongs in View-scoped customisation.
	 *
	 * Doesn't try to be a CSS parser; this is a low-effort filter for
	 * the common-case dangerous patterns. The save layer additionally
	 * gates persisting Custom CSS behind the `unfiltered_html`
	 * capability, making this sanitiser defense in depth rather than the
	 * only barrier.
	 *
	 * @since 3.0.0
	 *
	 * @param string $css Raw customer input.
	 *
	 * @return string Cleaned CSS, safe to inline. Empty when the
	 *                input is non-string or strips to nothing.
	 */
	public static function sanitize_custom_css( $css ): string {
		if ( ! is_string( $css ) ) {
			return '';
		}

		$css = trim( $css );
		if ( '' === $css ) {
			return '';
		}

		// One deletion can splice text into a NEW dangerous token (removing
		// `@import` from `java@import …;script:` reassembles `javascript:`;
		// from `<@import …;/style>` it leaves a live `</style>`), so every
		// pattern runs in one fixed-point loop. All only delete/collapse, so
		// the string shrinks to a stable point and the loop terminates.
		do {
			$before = $css;
			// JS protocol (tolerating whitespace before the colon).
			$css = preg_replace( '/javascript\s*:/i', '', $css );
			// `@import` at-rule (not valid in View-scoped customisation).
			$css = preg_replace( '/@import[^;]*;?/i', '', $css );
			// `</style`, tolerating whitespace inside the sequence.
			$css = preg_replace( '#<\s*/\s*style#i', '', $css );
			// A `<` that begins a tag-like sequence.
			$css = preg_replace( '/<(?=[a-zA-Z\/!?])/', '', $css );
			// Collapse runs of whitespace so the inline block stays readable.
			$css = preg_replace( '/\s+/', ' ', $css );
		} while ( $before !== $css );

		return trim( $css );
	}

	/**
	 * Whether a token id ends in one of the standard interaction-state
	 * suffixes: `_hover`, `_active`, `_visited`, `_focus`, `_disabled`.
	 *
	 * Matching the suffix only — does not consult the registry's
	 * `hover_slug` pairing graph. Tokens like `text_disabled`,
	 * `shadow_focus`, and `surface_disabled` return true even though
	 * they have no explicit resting parent.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token_id Dot-namespaced token id (e.g. `'shadow.entry_shadow_hover'`).
	 *
	 * @return bool
	 */
	private static function has_state_suffix( string $token_id ): bool {
		static $suffixes = [
			'_hover',
			'_active',
			'_visited',
			'_focus',
			'_disabled',
		];
		foreach ( $suffixes as $suffix ) {
			$len = strlen( $suffix );
			if ( strlen( $token_id ) > $len
				&& 0 === substr_compare( $token_id, $suffix, -$len )
			) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Whether `$variant_id`'s registry default equals the default of
	 * its resting parent — found by walking same-category tokens for
	 * one whose `hover_slug` points at `$variant_id`.
	 *
	 * Returns false when the variant has no resting parent in the
	 * pairing graph, or when the parent's `default` differs from the
	 * variant's.
	 *
	 * @since 3.0.0
	 *
	 * @param string $variant_id Dot-namespaced variant token id.
	 * @param array  $tokens     Token registry (callers pass in to avoid re-walking the cache).
	 *
	 * @return bool
	 */
	private static function variant_default_tracks_resting( string $variant_id, array $tokens ): bool {
		$variant = $tokens[ $variant_id ] ?? null;
		if ( null === $variant ) {
			return false;
		}
		$variant_default = $variant['default'] ?? null;
		if ( null === $variant_default ) {
			return false;
		}

		$dot = strpos( $variant_id, '.' );
		if ( false === $dot ) {
			return false;
		}
		$category = substr( $variant_id, 0, $dot );

		// Walk the same-category tokens looking for one whose
		// `hover_slug` points at this variant — that's the resting
		// parent. Compare its default; same default = track-resting.
		// `hover_slug` in the registry is stored as the dotted token id
		// (`'color.header_text_hover'`), so compare against the full
		// `$variant_id`, not the bare slug.
		$prefix = $category . '.';
		foreach ( $tokens as $other_id => $other ) {
			if ( 0 !== strpos( $other_id, $prefix ) ) {
				continue;
			}
			if ( ( $other['hover_slug'] ?? '' ) !== $variant_id ) {
				continue;
			}
			$parent_default = $other['default'] ?? null;
			return null !== $parent_default
				&& (string) $parent_default === (string) $variant_default;
		}
		return false;
	}

	/**
	 * Aliases whose value is a literal computed in PHP.
	 *
	 * `--gv-color-text-disabled` is `color-mix(text-muted 50%,
	 * surface-disabled)` computed once via `ColorMix::mix()`.
	 * `--gv-color-on-disabled` carries the same literal so cascade
	 * surfaces reading `on-disabled` stay coherent with text-disabled.
	 *
	 * Returned declarations carry no `var()` calls, so dep tracking
	 * cannot filter them — emitted on every View whose token pipeline
	 * runs.
	 *
	 * @since 3.0.0
	 *
	 * @return string[] Each `--gv-X: <literal>` declaration.
	 */
	private static function resolved_aliases(): array {
		$disabled_text = ColorMix::mix( '#5b6470', '#f3f4f6', 50 );
		return [
			sprintf( '--gv-color-text-disabled: %s', $disabled_text ),
			'--gv-color-on-disabled: var(--gv-color-text-disabled)',
		];
	}

	/**
	 * Aliases whose value is a `var()` expression that should re-resolve
	 * at the per-View wrapper.
	 *
	 * Each entry is `--gv-A: var(--gv-B)` (or a composite). The browser
	 * resolves `var()` eagerly at the declaring element, so re-stating
	 * the alias on the per-View wrapper re-substitutes against any
	 * wrapper-level override of its deps. The declaration is therefore
	 * only meaningful when at least one dep was overridden, which is
	 * what `aliases_for_overridden_deps()` filters for.
	 *
	 * Independently-themable hover variants (e.g. `text_hover`,
	 * `entry_shadow_hover`) deliberately do NOT appear here. Their
	 * resting parent already emits as a hex literal, so leaving them
	 * out keeps the hover variant decoupled from a customer's edit to
	 * the resting value.
	 *
	 * @since 3.0.0
	 *
	 * @return string[] Each `--gv-X: <expression-with-var()>` declaration.
	 */
	private static function substituted_aliases(): array {
		return [
			// Palette aliases.
			'--gv-color-row-divider: var(--gv-color-border-light)',
			'--gv-entry-border-color: var(--gv-color-border-light)',
			'--gv-color-header-bg: var(--gv-color-surface-alt)',
			'--gv-color-zebra-bg: var(--gv-color-surface)',

			// Focus shadow composes from the decomposed parts so
			// customers tuning only `--gv-focus-ring-color` or
			// `--gv-focus-ring-width` still update the composite.
			'--gv-shadow-focus: 0 0 0 var(--gv-focus-ring-width) var(--gv-focus-ring-color)',

			// Pagination buttons follow `border.control_radius` so search
			// inputs, the search button, and pager buttons share a corner
			// language by default.
			'--gv-pagination-button-radius: var(--gv-control-radius)',

			// Component aliases consumed throughout the SCSS.
			'--gv-entry-bg: var(--gv-color-surface)',
			'--gv-table-header-background: var(--gv-color-header-bg)',
			'--gv-field-label-color: var(--gv-color-text-muted)',
			'--gv-widget-border-color: var(--gv-entry-border-color)',
			'--gv-control-bg: var(--gv-color-surface)',
			'--gv-control-border-color: var(--gv-color-border-strong)',
			'--gv-button-bg: var(--gv-color-primary)',
			'--gv-button-color: var(--gv-color-on-primary)',
		];
	}

	/**
	 * Filters an alias list to the subset whose `var()` deps include at
	 * least one overridden CSS var, after transitive closure.
	 *
	 * Transitive closure: if alias `--gv-A: var(--gv-B)` matches because
	 * B is overridden, then `--gv-A` is also treated as overridden for
	 * any downstream alias that reads `var(--gv-A)`. Cascade through
	 * alias chains so e.g. an override of `surface-alt` propagates to
	 * `header-bg` and from there to `table-header-background`.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $aliases         Each `--gv-X: <expr>` declaration.
	 * @param string[] $overridden_vars CSS vars the customer overrode.
	 *
	 * @return string[] Affected aliases, in input order.
	 */
	private static function aliases_for_overridden_deps( array $aliases, array $overridden_vars ): array {
		// Pre-parse each alias once.
		$parsed = [];
		foreach ( $aliases as $declaration ) {
			$declared_var = null;
			if ( preg_match( '/^(--gv-[\w-]+)\s*:/', $declaration, $m ) ) {
				$declared_var = $m[1];
			}
			$deps = [];
			if ( preg_match_all( '/var\(\s*(--gv-[\w-]+)/', $declaration, $m_deps ) ) {
				$deps = array_values( array_unique( $m_deps[1] ) );
			}
			$parsed[] = [
				'declaration'  => $declaration,
				'declared_var' => $declared_var,
				'deps'         => $deps,
			];
		}

		// Fixed-point closure: an alias's declared var joins the affected
		// set once any of its deps is already in the set.
		$affected_vars = array_flip( $overridden_vars );
		do {
			$changed = false;
			foreach ( $parsed as $entry ) {
				if ( null === $entry['declared_var'] || isset( $affected_vars[ $entry['declared_var'] ] ) ) {
					continue;
				}
				foreach ( $entry['deps'] as $dep ) {
					if ( isset( $affected_vars[ $dep ] ) ) {
						$affected_vars[ $entry['declared_var'] ] = true;
						$changed                                 = true;
						break;
					}
				}
			}
		} while ( $changed );

		// An alias needs emission iff at least one of its deps is in the
		// affected set — emitting re-runs `var()` substitution against
		// the per-View wrapper's value.
		$result = [];
		foreach ( $parsed as $entry ) {
			foreach ( $entry['deps'] as $dep ) {
				if ( isset( $affected_vars[ $dep ] ) ) {
					$result[] = $entry['declaration'];
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * Clears static caches. Test/admin-render seam.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function flush(): void {
		self::$tokens_cache = null;
	}



	/**
	 * Resolves `color-mix()` shadow recipes into `rgba()` literals.
	 *
	 * Shadow depth is stored as a whole CSS string built from
	 * `color-mix( in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha)
	 * * N%), transparent )`. That is fine in the stylesheet, where an
	 * `@supports` block guards it, but the per-View inline `<style>` carries no
	 * guard: on an engine without `color-mix()` the whole declaration is
	 * dropped and the entry renders with no shadow at all.
	 *
	 * `color-mix( in srgb, C p%, transparent )` is exactly `rgba( C, p / 100 )`,
	 * so this is arithmetic on the effective color and alpha — the customer's
	 * overrides when present, the registry defaults otherwise. As with
	 * `resolve_color_mix_hovers()`, the emitted literal stops tracking a
	 * `--gv-shadow-color` set in hand-written CSS; the saved value wins.
	 *
	 * @since 3.2.0
	 *
	 * @param array<string, string> $overrides_by_css_var CSS-var → value map.
	 *
	 * @return array<string, string>
	 */
	private static function resolve_shadow_color_mix( array $overrides_by_css_var ): array {
		$has_recipe = false;

		foreach ( $overrides_by_css_var as $value ) {
			if ( false !== strpos( (string) $value, 'color-mix(in srgb, var(--gv-shadow-color)' ) ) {
				$has_recipe = true;
				break;
			}
		}

		if ( ! $has_recipe ) {
			return $overrides_by_css_var;
		}

		$tokens = self::tokens();
		$hex    = $overrides_by_css_var['--gv-shadow-color'] ?? ( $tokens['shadow.shadow_color']['default'] ?? '#121961' );
		$alpha  = $overrides_by_css_var['--gv-shadow-alpha'] ?? ( $tokens['shadow.shadow_alpha']['default'] ?? '0.1' );

		$rgb = self::rgb_from_hex( (string) $hex );

		if ( null === $rgb ) {
			// The one case this cannot pre-resolve: `sanitize()` also accepts a
			// `var(--wp--preset--color--*)` reference for color controls, and a
			// theme.json preset has no server-side value. The recipe is passed
			// through unresolved so the customer's chosen color still applies,
			// which means this View's inline CSS does carry a runtime
			// `color-mix()` — the single exception to the rule above. Below the
			// `color-mix()` floor that declaration is dropped and the depth
			// falls back to the stylesheet's own guarded value.
			return $overrides_by_css_var;
		}

		foreach ( $overrides_by_css_var as $css_var => $value ) {
			$overrides_by_css_var[ $css_var ] = (string) preg_replace_callback(
				'/color-mix\(\s*in srgb,\s*var\(--gv-shadow-color\)\s*calc\(\s*var\(--gv-shadow-alpha\)\s*\*\s*([\d.]+)%\s*\)\s*,\s*transparent\s*\)/',
				static function ( array $matches ) use ( $rgb, $alpha ): string {
					// A color carrying its own alpha mixes that in too:
					// `color-mix( in srgb, C p%, transparent )` yields
					// `rgba( C, alpha(C) * p / 100 )`.
					$resolved = (float) $alpha * ( (float) $matches[1] / 100 ) * $rgb[3];
					// Trim trailing zeros, but keep a bare `0` rather than an
					// empty string when the product rounds away entirely.
					$trimmed = rtrim( rtrim( number_format( $resolved, 4, '.', '' ), '0' ), '.' );

					if ( '' === $trimmed ) {
						$trimmed = '0';
					}

					return sprintf( 'rgba(%d, %d, %d, %s)', $rgb[0], $rgb[1], $rgb[2], $trimmed );
				},
				(string) $value
			);
		}

		return $overrides_by_css_var;
	}

	/**
	 * Expands a hex color into 0-255 channels plus its alpha.
	 *
	 * Accepts every length `sanitize()` stores for a color control: `#rgb`,
	 * `#rgba`, `#rrggbb` and `#rrggbbaa`. The alpha channel is returned as a
	 * 0-1 float, and is 1.0 when the value carries none, so a caller can
	 * multiply by it unconditionally.
	 *
	 * @since 3.2.0
	 * @since 3.3.3 Parses the `#rgba` and `#rrggbbaa` forms and returns alpha.
	 *
	 * @param string $hex Hex color.
	 *
	 * @return array{0: int, 1: int, 2: int, 3: float}|null Channels and alpha,
	 *                                                      or null when the
	 *                                                      value is not a plain
	 *                                                      hex.
	 */
	private static function rgb_from_hex( string $hex ): ?array {
		if ( ! preg_match( '/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $hex, $m ) ) {
			return null;
		}

		$digits = $m[1];
		$length = strlen( $digits );
		$is_shorthand = 3 === $length || 4 === $length;

		if ( $is_shorthand ) {
			$expanded = '';

			// Shorthand doubles every digit, the alpha nibble included.
			for ( $i = 0; $i < $length; $i++ ) {
				$expanded .= $digits[ $i ] . $digits[ $i ];
			}

			$digits = $expanded;
		}

		$has_alpha = 8 === strlen( $digits );
		$alpha     = $has_alpha ? hexdec( substr( $digits, 6, 2 ) ) / 255 : 1.0;

		return [
			(int) hexdec( substr( $digits, 0, 2 ) ),
			(int) hexdec( substr( $digits, 2, 2 ) ),
			(int) hexdec( substr( $digits, 4, 2 ) ),
			(float) $alpha,
		];
	}
}
