<?php
/**
 * Canonical Design Token Registry.
 *
 * Single source of truth for every `--gv-*` CSS variable shipped by the
 * GravityView design token system. Owns the default value, `@property`
 * syntax, contrast metadata, control shape, and customization-API
 * surface flag for every token.
 *
 * Downstream consumers:
 *
 * - `ViewStyles::tokens()` filters `studio_tokens()` to surface tokens
 *   as theme tokens.
 * - `tools/validate-tokens.php` reads `all_css_vars()` to validate that
 *   every `var(--gv-…)` reference in SCSS resolves to a declared token.
 * - `tools/build-tokens-docs.php` generates the token reference tables
 *   in `docs/css-variables.md`.
 * - `_properties.scss` is regenerated from `for_property_registration()`.
 * - `ColorMix.php` uses `canonical_css_var()` to resolve `*-hover` slugs.
 *
 * The registry is intentionally static (no DB lookups): token shape is
 * code-controlled, not site-controlled. Customer overrides live in
 * `template_settings.design_tokens.*` per-View and never mutate the
 * registry shape.
 *
 * @package GravityKit\GravityView
 * @since   TBD
 */

declare( strict_types=1 );

namespace GravityKit\GravityView\Settings;

defined( 'ABSPATH' ) || die();

/**
 * Canonical token registry.
 *
 * @since 3.0.0
 */
final class TokenRegistry {

	/**
	 * Memoised full registry.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private static ?array $cache = null;

	/**
	 * Every shipped design token.
	 *
	 * Keys are dot-namespaced slugs (`color.primary`,
	 * `typography.font_size_base`). Values carry: `css_var`, `default`,
	 * `category`, `group`, `studio`, `private`, `syntax`, `desc`,
	 * `control`, optional `hover_slug`, optional control-specific keys
	 * (`options`, `units`, `min`, `max`, `step`), and the `@property`
	 * extension points `register_property` / `property_initial`. The
	 * full per-entry contract is documented on the
	 * `gk/gravityview/theme/tokens` filter below.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function all(): array {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$tokens = [];

		foreach ( self::color() as $slug => $entry ) {
			$tokens[ "color.{$slug}" ] = self::normalize( $entry, 'color', $slug );
		}
		foreach ( self::typography() as $slug => $entry ) {
			$tokens[ "typography.{$slug}" ] = self::normalize( $entry, 'typography', $slug );
		}
		foreach ( self::dimensions() as $slug => $entry ) {
			$tokens[ "dimensions.{$slug}" ] = self::normalize( $entry, 'dimensions', $slug );
		}
		foreach ( self::border() as $slug => $entry ) {
			$tokens[ "border.{$slug}" ] = self::normalize( $entry, 'border', $slug );
		}
		foreach ( self::shadow() as $slug => $entry ) {
			$tokens[ "shadow.{$slug}" ] = self::normalize( $entry, 'shadow', $slug );
		}
		foreach ( self::layout() as $slug => $entry ) {
			$tokens[ "layout.{$slug}" ] = self::normalize( $entry, 'layout', $slug );
		}
		foreach ( self::motion() as $slug => $entry ) {
			$tokens[ "motion.{$slug}" ] = self::normalize( $entry, 'motion', $slug );
		}

		/**
		 * Filters the full design token registry.
		 *
		 * Array keys are dot-namespaced token slugs (`color.primary`,
		 * `mytheme.accent`). Each entry is an array with this shape:
		 *
		 * - `css_var` (string, required): CSS custom property name. Must
		 *   start with `--gv-`.
		 * - `default` (string, required): cascade default value. An empty
		 *   string means the token ships unset, so `var()` fallback chains
		 *   that reference it stay live.
		 * - `syntax` (string, required): `@property` syntax string, or `*`
		 *   for values that cannot be registered.
		 * - `register_property` (bool, optional): set to `false` to skip
		 *   `@property` emission entirely. Required for tokens whose
		 *   `var()` fallback chain is meaningful when the token is unset.
		 * - `property_initial` (string, optional): overrides the
		 *   `@property` initial-value when the cascade default is not
		 *   computationally independent (for example a rem-based default).
		 * - `studio` (bool, optional): `true` exposes the token to the
		 *   customization API via `studio_tokens()` and
		 *   `ViewStyles::tokens()`.
		 * - Other optional keys: `category`, `group`, `private`, `desc`,
		 *   `control`, `hover_slug`, `options` (plain list of allowed
		 *   value strings), `units`, `min`, `max`, `step`,
		 *   `extra_css_vars`.
		 *
		 * Entries that fail validation (a missing or non-string `css_var`,
		 * `default`, or `syntax`, or a `css_var` that does not start with
		 * `--gv-`) are dropped and a warning naming the slug is logged.
		 *
		 * @since 3.0.0
		 *
		 * @param array<string, array<string, mixed>> $tokens Token entries keyed by dot-namespaced slug.
		 */
		$tokens = (array) apply_filters( 'gk/gravityview/theme/tokens', $tokens );

		self::$cache = self::validate( $tokens );

		return self::$cache;
	}

	/**
	 * Drops registry entries that do not satisfy the token contract.
	 *
	 * Every entry must be an array carrying a string `css_var` starting
	 * with `--gv-`, a string `default`, and a string `syntax`. Invalid
	 * entries are removed and a warning naming the slug is logged.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, mixed> $tokens Token entries keyed by slug.
	 *
	 * @return array<string, array<string, mixed>> Valid entries only.
	 */
	private static function validate( array $tokens ): array {
		foreach ( $tokens as $slug => $token ) {
			$css_var = is_array( $token ) ? ( $token['css_var'] ?? null ) : null;

			if (
				is_array( $token )
				&& is_string( $css_var )
				&& str_starts_with( $css_var, '--gv-' )
				&& is_string( $token['default'] ?? null )
				&& is_string( $token['syntax'] ?? null )
			) {
				continue;
			}

			unset( $tokens[ $slug ] );

			if ( function_exists( 'gravityview' ) ) {
				gravityview()->log->warning(
					sprintf(
						'TokenRegistry: dropped invalid token `%s` from the `gk/gravityview/theme/tokens` filter result.',
						(string) $slug
					)
				);
			}
		}

		return $tokens;
	}

	/**
	 * Tokens surfaced as theme tokens.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function studio_tokens(): array {
		return array_filter(
			self::all(),
			static fn( array $token ): bool => ! empty( $token['studio'] )
		);
	}

	/**
	 * Flat list of CSS variable names, for validate-tokens.php.
	 *
	 * @since 3.0.0
	 *
	 * @return string[]
	 */
	public static function all_css_vars(): array {
		$vars = array_map( static fn( array $t ): string => $t['css_var'], self::all() );
		// Some tokens (e.g. dimensions/border sub-tokens) deliberately
		// expose extra css vars via `extra_css_vars`. Flatten those too.
		foreach ( self::all() as $token ) {
			if ( ! empty( $token['extra_css_vars'] ) && is_array( $token['extra_css_vars'] ) ) {
				$vars = array_merge( $vars, $token['extra_css_vars'] );
			}
		}
		return array_values( array_unique( $vars ) );
	}

	/**
	 * Entries eligible for `@property` registration in `_properties.scss`.
	 *
	 * Excludes tokens with a `*` syntax (not registrable per the CSS
	 * Properties and Values spec) and tokens that opt out via
	 * `'register_property' => false` because their `var()` fallback chain
	 * is meaningful when the token is unset.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function for_property_registration(): array {
		return array_filter(
			self::all(),
			static fn( array $t ): bool => isset( $t['syntax'] )
				&& '*' !== $t['syntax']
				&& false !== ( $t['register_property'] ?? true )
		);
	}

	/**
	 * Canonical slug → CSS var name transform.
	 *
	 * Implements the prefix map documented in the spec:
	 *
	 *   color.primary             → --gv-color-primary
	 *   typography.font_size_base → --gv-font-size-base
	 *   dimensions.space_4        → --gv-space-4
	 *   border.radius_xs          → --gv-radius-xs
	 *   border.width_1            → --gv-border-width-1
	 *   border.entry_width        → --gv-entry-border-width
	 *   shadow.entry_shadow       → --gv-entry-shadow
	 *   layout.z_dropdown         → --gv-z-dropdown
	 *   motion.transition_fast    → --gv-transition-fast
	 *
	 * Registry entries override `css_var` literally when the transform
	 * doesn't apply (e.g. legacy `--gv-control-bg` lives under
	 * `color.control_bg`).
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Dot-namespaced slug.
	 *
	 * @return string CSS custom property name including `--` prefix.
	 */
	public static function canonical_css_var( string $slug ): string {
		[ $category, $name ] = array_pad( explode( '.', $slug, 2 ), 2, '' );
		$name                = str_replace( '_', '-', $name );

		switch ( $category ) {
			case 'color':
				return "--gv-color-{$name}";
			case 'typography':
				// Typography splits across font-*, line-height-*, letter-spacing-*.
				if ( str_starts_with( $name, 'font' ) ) {
					return "--gv-{$name}";
				}
				if ( str_starts_with( $name, 'line-height' ) ) {
					return "--gv-{$name}";
				}
				if ( str_starts_with( $name, 'letter-spacing' ) ) {
					return "--gv-{$name}";
				}
				if ( str_starts_with( $name, 'field-label' ) ) {
					return "--gv-{$name}";
				}
				return "--gv-font-{$name}";
			case 'dimensions':
				// space_N keeps `--gv-space-N`; padding/margin/gap/size tokens carry their literal name.
				return "--gv-{$name}";
			case 'border':
				// Compensating fallbacks so the renamed slugs still resolve to their historical CSS vars
				// (registry entries override these literally; this only matters for canonical_css_var() callers).
				if ( str_starts_with( $name, 'width-' ) ) {
					// Maps border.width_xs to --gv-border-width-xs.
					return "--gv-border-{$name}";
				}
				if ( 'entry-width' === $name || 'entry-style' === $name || 'entry-color' === $name ) {
					return '--gv-entry-border-' . substr( $name, strlen( 'entry-' ) );
				}
				if ( 'entry-width-hover' === $name || 'entry-style-hover' === $name || 'entry-color-hover' === $name ) {
					return '--gv-entry-border-' . substr( $name, strlen( 'entry-' ) );
				}
				if ( 'control-width' === $name ) {
					return '--gv-control-border-width';
				}
				if ( 'widget-radius' === $name ) {
					return '--gv-widget-border-radius';
				}
				return "--gv-{$name}";
			case 'shadow':
				return "--gv-{$name}";
			case 'layout':
				return "--gv-{$name}";
			case 'motion':
				return "--gv-{$name}";
			default:
				return "--gv-{$name}";
		}
	}

	/**
	 * Slug → CSS var lookup for a given category + slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dotted Slug like `color.primary_hover`.
	 *
	 * @return string|null CSS var name or null if not registered.
	 */
	public static function css_var_for( string $dotted ): ?string {
		$tokens = self::all();
		return $tokens[ $dotted ]['css_var'] ?? null;
	}

	/**
	 * Reset memoisation. Tests only.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function flush(): void {
		self::$cache = null;
	}

	/**
	 * Normalize an entry into the canonical token shape.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, mixed> $entry Raw entry.
	 * @param string               $category Category bucket.
	 * @param string               $slug Token slug (without category).
	 *
	 * @return array<string, mixed>
	 */
	private static function normalize( array $entry, string $category, string $slug ): array {
		$defaults = [
			'css_var'           => self::canonical_css_var( "{$category}.{$slug}" ),
			'default'           => '',
			'category'          => $category,
			'group'             => '',
			// `studio` marks the token as exposed to the customization API
			// (surfaced by `studio_tokens()` and `ViewStyles::tokens()`).
			'studio'            => false,
			'private'           => false,
			'syntax'            => '*',
			'desc'              => '',
			'control'           => 'text',
			'hover_slug'        => null,
			'options'           => null,
			'units'             => null,
			'min'               => null,
			'max'               => null,
			'step'              => null,
			'extra_css_vars'    => null,
			// `@property` extension points. `register_property => false`
			// skips registration entirely; `property_initial` substitutes
			// the emitted initial-value when the cascade default is not
			// computationally independent.
			'register_property' => true,
			'property_initial'  => null,
		];

		return array_merge( $defaults, $entry );
	}

	// =============================================================
	// @property REGISTRATION GUARDRAILS
	//
	// Two classes of token must NEVER receive an `@property`
	// registration with an initial value:
	//
	// (a) Per-field consumption tokens of the form `--gv-field-*`
	// that SCSS consumes with `var(--gv-field-..., inherit)`
	// fallbacks. A registration gives every element a typed
	// initial value, which replaces the `inherit` fallback on
	// every site.
	//
	// (b) Any token whose `var()` fallback chain is meaningful when
	// the token is unset, e.g.
	// `var(--gv-grid-columns-lg, var(--gv-grid-columns))`. A
	// registered initial value means the token is never unset,
	// so the fallback can never apply. Mark these with
	// `'register_property' => false`.
	// =============================================================

	// -------------------------------------------------------------
	// Color tokens (WP block-supports `color`)
	// -------------------------------------------------------------

	/**
	 * Raw color tokens.
	 *
	 * Returned shape mirrors `all()` entries minus the `category` key
	 * (added in `normalize()`).
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function color(): array {
		return [
			// ---------------------------------------------------------
			// Brand & action.
			// ---------------------------------------------------------
			'primary'                             => [
				'css_var'    => '--gv-color-primary',
				'default'    => '#204ce5',
				'group'      => 'brand',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Submit buttons, active pagination, links, accent borders.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.primary_hover',
			],
			'primary_hover'                       => [
				'css_var' => '--gv-color-primary-hover',
				// OKLCh-derived; fallback resolved server-side via ColorMix.php.
				'default' => '#1c44ce',
				'group'   => 'brand',
				// Surfaced for Dark theme preset writes (lifted blue for dark
				// surfaces; keeps on-primary AA-paired in both modes) and
				// for any third-party preset that needs to break the
				// auto-derived hover when their primary is already light.
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Hover variant of Primary, derived via OKLCh L−10%.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'primary_subtle'                      => [
				'css_var' => '--gv-color-primary-subtle',
				'default' => '#c3d9ff',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Tint for soft hovers on light surfaces. Not a focus-ring color (1.43:1 vs white fails SC 1.4.11).', 'gk-gravityview' ),
				'control' => 'color',
			],
			'on_primary'                          => [
				'css_var' => '--gv-color-on-primary',
				'default' => '#ffffff',
				'group'   => 'brand',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Text and icons that sit on top of the Primary color.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Text.
			// ---------------------------------------------------------
			'text'                                => [
				'css_var'    => '--gv-color-text',
				'default'    => '#112337',
				'group'      => 'text',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Default text color across the View.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.text_hover',
			],
			'text_hover'                          => [
				'css_var' => '--gv-color-text-hover',
				// Matches the resting `text` literal so an uncustomised
				// View paints hover text identical to resting and the
				// customization control's default reads true. Decoupling from
				// `--gv-color-text` is intentional: a customer brand
				// recolor of resting text should not silently move
				// hover text with it.
				'default' => '#112337',
				'group'   => 'text',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Text color when hovering an entry card.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'text_secondary'                      => [
				'css_var' => '--gv-color-text-secondary',
				'default' => '#54667a',
				'group'   => 'text',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Captions, sub-titles, search-widget labels, and supporting copy.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'text_muted'                          => [
				'css_var' => '--gv-color-text-muted',
				'default' => '#5b6470',
				'group'   => 'text',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Small helper text: captions, descriptions.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'text_disabled'                       => [
				'css_var' => '--gv-color-text-disabled',
				'default' => '#a1a8b1',
				'group'   => 'text',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Text on disabled controls. Derived from text-muted blended toward surface-disabled.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'on_disabled'                         => [
				'css_var' => '--gv-color-on-disabled',
				'default' => '#a1a8b1',
				'group'   => 'text',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Foreground color used on surface-disabled. Mirrors text-disabled.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'placeholder'                         => [
				'css_var' => '--gv-color-placeholder',
				'default' => '#5f6772',
				'group'   => 'more',
				// Surfaced via Dark theme preset (lifted hint for dark surfaces),
				// so it must round-trip through `ViewStyles::tokens()`.
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Empty-state hint text inside search fields.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Links: four states.
			// ---------------------------------------------------------
			'link'                                => [
				'css_var'    => '--gv-color-link',
				'default'    => '#204ce5',
				'group'      => 'links',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Color of clickable links in entries and content. Decoupled from Primary so brand-recolor cannot silently drop link contrast below AA.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.link_hover',
			],
			'link_hover'                          => [
				'css_var' => '--gv-color-link-hover',
				'default' => '#1c44ce',
				'group'   => 'links',
				// Surfaced for Dark theme preset writes (lifted blue for
				// dark surfaces).
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of links when the cursor hovers them.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'link_visited'                        => [
				'css_var' => '--gv-color-link-visited',
				'default' => '#7c3aed',
				'group'   => 'links',
				// Written by the Dark theme preset (lifted purple for dark
				// surfaces); must round-trip through `ViewStyles::tokens()`.
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of links the visitor has already clicked.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'link_active'                         => [
				'css_var' => '--gv-color-link-active',
				'default' => '#0c1f7a',
				'group'   => 'links',
				// Written by the Dark theme preset (active-press tone for
				// dark surfaces).
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of links during the click: the brief frame between mousedown and mouseup.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Field label (legacy).
			// ---------------------------------------------------------
			'field_label'                         => [
				'css_var' => '--gv-field-label-color',
				'default' => '#5b6470',
				'group'   => 'text',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'The small header above each field value when "Show Label" is on.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Inputs.
			// ---------------------------------------------------------
			'control_bg'                          => [
				'css_var' => '--gv-control-bg',
				'default' => '#ffffff',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Inside of search fields and dropdown selects.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'control_border_color'                => [
				'css_var' => '--gv-control-border-color',
				'default' => '#6b7280',
				'group'   => 'border',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Border color of inputs, selects, and other form controls.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'control_bg_disabled'                 => [
				'css_var' => '--gv-control-bg-disabled',
				'default' => '#edeef0',
				'group'   => 'forms_disabled',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of disabled inputs and selects.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'control_border_color_disabled'       => [
				'css_var' => '--gv-control-border-color-disabled',
				'default' => '#888888',
				'group'   => 'forms_disabled',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Border color of disabled inputs and selects.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'control_text_disabled'               => [
				'css_var' => '--gv-control-text-disabled',
				'default' => '#a1a8b1',
				'group'   => 'forms_disabled',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Text color inside disabled inputs and selects.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'button_bg'                           => [
				'css_var' => '--gv-button-bg',
				'default' => '#204ce5',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of primary buttons. Aliases Primary by default.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'button_color'                        => [
				'css_var' => '--gv-button-color',
				'default' => '#ffffff',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Text color of primary buttons.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'button_bg_hover'                     => [
				'css_var' => '--gv-button-bg-hover',
				'default' => '#1c44ce',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of primary buttons on hover.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'button_bg_disabled'                  => [
				'css_var' => '--gv-button-bg-disabled',
				'default' => '#edeef0',
				'group'   => 'forms_disabled',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of primary buttons when disabled.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'button_color_disabled'               => [
				'css_var' => '--gv-button-color-disabled',
				'default' => '#a1a8b1',
				'group'   => 'forms_disabled',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Text color of primary buttons when disabled.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Surfaces.
			// ---------------------------------------------------------
			'surface'                             => [
				'css_var'    => '--gv-color-surface',
				'default'    => '#ffffff',
				'group'      => 'surface',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Default background for entry cards, list items, pagination, and panels.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.surface_hover',
			],
			'surface_hover'                       => [
				'css_var' => '--gv-color-surface-hover',
				'default' => '#fafafa',
				'group'   => 'surface',
				// Surface-hover is written by the Dark theme preset, so it
				// must be reachable through `ViewStyles::tokens()` (studio
				// surface). Customers typically still derive it from
				// `color.surface` via ColorMix, but presets need to set
				// it explicitly for the dark-mode pair.
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background when hovering an entry card or list item.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'surface_alt'                         => [
				'css_var' => '--gv-color-surface-alt',
				'default' => '#f2f3f5',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Search-bar surface and edit-entry section panels.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'surface_error'                       => [
				'css_var' => '--gv-color-surface-error',
				'default' => '#fbe2dc',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of error notices and invalid Edit Entry fields.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'surface_success'                     => [
				'css_var' => '--gv-color-surface-success',
				'default' => '#dcf2df',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of success notices.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'surface_warning'                     => [
				'css_var' => '--gv-color-surface-warning',
				'default' => '#fef0d6',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of warning notices.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'surface_info'                        => [
				'css_var' => '--gv-color-surface-info',
				'default' => '#cffafe',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of informational notices.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'surface_disabled'                    => [
				'css_var' => '--gv-color-surface-disabled',
				'default' => '#edeef0',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of disabled controls.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'header_bg'                           => [
				'css_var'    => '--gv-color-header-bg',
				'default'    => '#f2f3f5',
				'group'      => 'surface',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Background of the table header row.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.header_bg_hover',
			],
			'header_bg_hover'                     => [
				'css_var' => '--gv-color-header-bg-hover',
				'default' => '#f2f3f5',
				'group'   => 'surface',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Background when hovering the table header row.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'header_text'                         => [
				'css_var'    => '--gv-color-header-text',
				'default'    => '#5b6470',
				'group'      => 'text',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Color of the column header text in tables.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.header_text_hover',
			],
			'header_text_hover'                   => [
				'css_var' => '--gv-color-header-text-hover',
				'default' => '#5b6470',
				'group'   => 'text',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Text color when hovering the table header.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'zebra_bg'                            => [
				'css_var' => '--gv-color-zebra-bg',
				'default' => '#ffffff',
				'group'   => 'surface',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of every other row in a table for zebra striping.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Borders.
			// ---------------------------------------------------------
			'border'                              => [
				'css_var' => '--gv-color-border',
				'default' => '#888888',
				'group'   => 'border',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Form input borders and table footer rule.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'border_strong'                       => [
				'css_var' => '--gv-color-border-strong',
				'default' => '#6b7280',
				'group'   => 'border',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Default outline color for form controls (4.74:1 vs surface; clears SC 1.4.11).', 'gk-gravityview' ),
				'control' => 'color',
			],
			'border_light'                        => [
				'css_var' => '--gv-color-border-light',
				'default' => '#e5e7eb',
				'group'   => 'border',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Decorative dividers and hairline separators only. Never the sole outline of an interactive control.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'row_divider'                         => [
				'css_var' => '--gv-color-row-divider',
				'default' => '#e5e7eb',
				'group'   => 'border',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Border between repeating rows in a table or list.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'entry_border_color_alias'            => [
				// Layer-1 alias used by `--gv-entry-border-color`. Separate
				// slug so the alias can be re-stated at the scoped wrapper.
				'css_var' => '--gv-color-entry-border',
				'default' => '#e5e7eb',
				'group'   => 'border',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Alias that wires `--gv-entry-border-color` to Border Light.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Status.
			// ---------------------------------------------------------
			'error'                               => [
				'css_var'    => '--gv-color-error',
				'default'    => '#c02b0a',
				'group'      => 'more',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Invalid Edit Entry input borders, validation messages, and required-field markers.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.error_hover',
			],
			'error_hover'                         => [
				'css_var' => '--gv-color-error-hover',
				'default' => '#922008',
				'group'   => 'more',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'OKLCh-derived hover variant of Error.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'on_error'                            => [
				'css_var' => '--gv-color-on-error',
				'default' => '#ffffff',
				'group'   => 'more',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Foreground (text/icon) used on solid error surfaces.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'success'                             => [
				'css_var'    => '--gv-color-success',
				'default'    => '#23692f',
				'group'      => 'more',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Success notices and confirmation indicators.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.success_hover',
			],
			'success_hover'                       => [
				'css_var' => '--gv-color-success-hover',
				'default' => '#1a4f23',
				'group'   => 'more',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'OKLCh-derived hover variant of Success.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'on_success'                          => [
				'css_var' => '--gv-color-on-success',
				'default' => '#ffffff',
				'group'   => 'more',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Foreground used on solid success surfaces.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'warning'                             => [
				'css_var' => '--gv-color-warning',
				'default' => '#febc48',
				'group'   => 'more',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Decorative-only warning hue. Never emitted as bare text; only as backgrounds or inside .gv-status mixin.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'warning_icon'                        => [
				'css_var' => '--gv-color-warning-icon',
				'default' => '#b07000',
				'group'   => 'more',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Icon-stroke color for .gv-status--warning. Decorative; paired with text per SC 1.4.1. Must keep ≥ 3:1 vs surface-warning.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'info'                                => [
				'css_var'    => '--gv-color-info',
				'default'    => '#0e7490',
				'group'      => 'more',
				'studio'     => true,
				'syntax'     => '<color>',
				'desc'       => __( 'Informational notices. Teal/cyan, distinct from Primary blue.', 'gk-gravityview' ),
				'control'    => 'color',
				'hover_slug' => 'color.info_hover',
			],
			'info_hover'                          => [
				'css_var' => '--gv-color-info-hover',
				'default' => '#0a5e74',
				'group'   => 'more',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'OKLCh-derived hover variant of Info.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'on_info'                             => [
				'css_var' => '--gv-color-on-info',
				'default' => '#ffffff',
				'group'   => 'more',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Foreground used on solid info surfaces.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// ---------------------------------------------------------
			// Focus ring (decomposed).
			// ---------------------------------------------------------
			'focus_ring_color'                    => [
				'css_var' => '--gv-focus-ring-color',
				'default' => '#2563eb',
				'group'   => 'focus',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of the focus-visible indicator. 5.17:1 vs surface; clears SC 1.4.11 non-text.', 'gk-gravityview' ),
				'control' => 'color',
			],

			'entry_bg'                            => [
				'css_var' => '--gv-entry-bg',
				'default' => '#ffffff',
				'group'   => 'entry',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Background color of entry cards.', 'gk-gravityview' ),
				'control' => 'color',
			],

			// =========================================================
			// Layer-2 component color tokens. Slots that resolve to a
			// Layer-0/1 primitive via `var(--gv-...)`: cards, rows,
			// buttons (default / secondary / danger), pagination
			// buttons, table cells. SCSS reads the component slot so a
			// single primitive recolor (e.g. `--gv-color-primary`)
			// propagates to everything derived from it.
			//
			// `studio: false` keeps them out of the customization surface
			// (cascade plumbing, not customer-facing). `syntax: '*'`
			// because `var()` defaults can't be `@property`-registered
			// (not computationally independent).
			// =========================================================
			'row_hover_bg'                        => [
				'css_var' => '--gv-color-row-hover-bg',
				'default' => 'var(--gv-color-surface-hover)',
				'group'   => 'state',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'row_hover_text'                      => [
				'css_var' => '--gv-color-row-hover-text',
				'default' => 'var(--gv-color-text-hover)',
				'group'   => 'state',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'entry_bg_hover'                      => [
				'css_var' => '--gv-entry-bg-hover',
				'default' => 'var(--gv-entry-bg)',
				'group'   => 'state',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'table_cell_border_color'             => [
				'css_var' => '--gv-table-cell-border-color',
				'default' => 'var(--gv-color-border-light)',
				'group'   => 'table',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_border_color'                 => [
				'css_var' => '--gv-button-border-color',
				'default' => 'var(--gv-button-bg)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_border_color_hover'           => [
				'css_var' => '--gv-button-border-color-hover',
				'default' => 'var(--gv-button-bg-hover)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_bg'                 => [
				'css_var' => '--gv-button-secondary-bg',
				'default' => 'transparent',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_color'              => [
				'css_var' => '--gv-button-secondary-color',
				'default' => 'var(--gv-color-text)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_border_color'       => [
				'css_var' => '--gv-button-secondary-border-color',
				'default' => 'var(--gv-color-border)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_bg_hover'           => [
				'css_var' => '--gv-button-secondary-bg-hover',
				'default' => 'var(--gv-color-surface-alt)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_color_hover'        => [
				'css_var' => '--gv-button-secondary-color-hover',
				'default' => 'var(--gv-color-text)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_border_color_hover' => [
				'css_var' => '--gv-button-secondary-border-color-hover',
				'default' => 'var(--gv-color-text)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_bg'                    => [
				'css_var' => '--gv-button-danger-bg',
				'default' => 'transparent',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_color'                 => [
				'css_var' => '--gv-button-danger-color',
				'default' => 'var(--gv-color-error)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_border_color'          => [
				'css_var' => '--gv-button-danger-border-color',
				'default' => 'var(--gv-color-error)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_bg_hover'              => [
				'css_var' => '--gv-button-danger-bg-hover',
				'default' => 'var(--gv-color-surface-error)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_color_hover'           => [
				'css_var' => '--gv-button-danger-color-hover',
				'default' => 'var(--gv-color-error-hover)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_border_color_hover'    => [
				'css_var' => '--gv-button-danger-border-color-hover',
				'default' => 'var(--gv-color-error-hover)',
				'group'   => 'brand',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'pagination_button_bg'                => [
				'css_var' => '--gv-pagination-button-bg',
				'default' => 'var(--gv-color-surface)',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'pagination_button_color'             => [
				'css_var' => '--gv-pagination-button-color',
				'default' => 'var(--gv-color-text)',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'pagination_button_bg_hover'          => [
				'css_var' => '--gv-pagination-button-bg-hover',
				'default' => 'color-mix(in oklch, var(--gv-color-surface) 90%, #000)',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'pagination_button_color_hover'       => [
				'css_var' => '--gv-pagination-button-color-hover',
				'default' => 'var(--gv-color-text)',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'pagination_button_bg_active'         => [
				'css_var' => '--gv-pagination-button-bg-active',
				'default' => 'var(--gv-color-primary)',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'pagination_button_color_active'      => [
				'css_var' => '--gv-pagination-button-color-active',
				'default' => 'var(--gv-color-on-primary)',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
		];
	}

	// -------------------------------------------------------------
	// Typography tokens
	// -------------------------------------------------------------

	/**
	 * Raw typography tokens.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function typography(): array {
		return [
			'font_family'                => [
				'css_var' => '--gv-font-family',
				'default' => 'inherit',
				'group'   => 'font',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Typeface used across the View. "Inherit" follows the page theme.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => [
					'inherit',
					'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
					'Georgia, "Times New Roman", serif',
					'"SF Mono", Monaco, Consolas, "Liberation Mono", monospace',
				],
			],
			'font_size_base'             => [
				'css_var'          => '--gv-font-size-base',
				// Registry default is `1rem` so the `number` control can
				// sanitize and round-trip the value. The host-theme
				// defense against `html { font-size: 62.5% }` themes
				// (where 1rem = 10px) ships from `_tokens.scss` as
				// `max(1rem, 16px)`; that cascades whenever the saved
				// value matches this default, so the per-View pipeline
				// drops the token and the SCSS-layer floor wins.
				'default'          => '1rem',
				// The cascade default stays 1rem; the @property fallback
				// uses the 16px equivalent because registered initial
				// values cannot reference font-relative units.
				'property_initial' => '16px',
				'group'            => 'font',
				'studio'           => true,
				'syntax'           => '<length-percentage>',
				'desc'             => __( 'Body-text size. Other sizes (small, large) scale proportionally.', 'gk-gravityview' ),
				'control'          => 'number',
				'units'            => [ 'px', 'rem', 'em' ],
				'min'              => 1,
				'step'             => 1,
			],
			'font_size_xs'               => [
				'css_var' => '--gv-font-size-xs',
				'default' => 'calc(var(--gv-font-size-base) * 0.75)',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Scale step 0.75×: small captions and chips.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'font_size_sm'               => [
				'css_var' => '--gv-font-size-sm',
				'default' => 'calc(var(--gv-font-size-base) * 0.875)',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Scale step 0.875×: secondary text.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'font_size_md'               => [
				'css_var' => '--gv-font-size-md',
				'default' => 'calc(var(--gv-font-size-base) * 1.125)',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Scale step 1.125×: small headings.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'font_size_lg'               => [
				'css_var' => '--gv-font-size-lg',
				'default' => 'calc(var(--gv-font-size-base) * 1.25)',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Scale step 1.25×: medium headings.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'font_size_xl'               => [
				'css_var' => '--gv-font-size-xl',
				'default' => 'calc(var(--gv-font-size-base) * 1.5)',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Scale step 1.5×: large headings.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'font_size_2xl'              => [
				'css_var' => '--gv-font-size-2xl',
				'default' => 'calc(var(--gv-font-size-base) * 1.875)',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Scale step 1.875×: hero headings.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'font_weight_light'          => [
				'css_var' => '--gv-font-weight-light',
				'default' => '300',
				'group'   => 'font',
				'studio'  => true,
				'syntax'  => '<integer>',
				'desc'    => __( 'Optional emphasis-down weight (300).', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 100,
				'max'     => 900,
				'step'    => 100,
			],
			'font_weight_normal'         => [
				'css_var' => '--gv-font-weight-normal',
				'default' => '400',
				'group'   => 'font',
				// Surfaced for theme-preset writes; Midnight pairs a
				// slightly heavier body weight with its dark surface to
				// keep small text legible.
				'studio'  => true,
				'syntax'  => '<integer>',
				'desc'    => __( 'Body text weight (400).', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 100,
				'max'     => 900,
				'step'    => 100,
			],
			'font_weight_medium'         => [
				'css_var' => '--gv-font-weight-medium',
				'default' => '500',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '<integer>',
				'desc'    => __( 'Medium-emphasis weight (500).', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 100,
				'max'     => 900,
				'step'    => 100,
			],
			'font_weight_semibold'       => [
				'css_var' => '--gv-font-weight-semibold',
				'default' => '600',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '<integer>',
				'desc'    => __( 'Field labels and other strong-emphasis weight (600).', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 100,
				'max'     => 900,
				'step'    => 100,
			],
			'font_weight_bold'           => [
				'css_var' => '--gv-font-weight-bold',
				'default' => '700',
				'group'   => 'font',
				'studio'  => true,
				'syntax'  => '<integer>',
				'desc'    => __( 'Headings and strong emphasis (700).', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 100,
				'max'     => 900,
				'step'    => 100,
			],
			'line_height_tight'          => [
				'css_var' => '--gv-line-height-tight',
				'default' => '1.25',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '<number>',
				'desc'    => __( 'Tight line-height: 1.25.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'line_height_normal'         => [
				'css_var' => '--gv-line-height-normal',
				'default' => '1.5',
				'group'   => 'font',
				'studio'  => true,
				'syntax'  => '<number>',
				'desc'    => __( 'Vertical rhythm of body text. Higher values are airier.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => [
					'1.25',
					'1.5',
					'1.75',
				],
			],
			'line_height_relaxed'        => [
				'css_var' => '--gv-line-height-relaxed',
				'default' => '1.75',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '<number>',
				'desc'    => __( 'Relaxed line-height: 1.75.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'line_height_cjk'            => [
				'css_var' => '--gv-line-height-cjk',
				'default' => '1.85',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '<number>',
				'desc'    => __( 'Line height for stacked-diacritic scripts (Thai, Burmese, Devanagari, etc.).', 'gk-gravityview' ),
				'control' => 'number',
			],

			// Field label.
			'field_label_font_size'      => [
				'css_var' => '--gv-field-label-font-size',
				'default' => 'calc(var(--gv-font-size-base) * 0.75)',
				'group'   => 'font',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Size of the small header above each field value when "Show Label" is on.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em' ],
				'min'     => 1,
				'step'    => 1,
			],
			'field_label_font_weight'    => [
				'css_var' => '--gv-field-label-font-weight',
				'default' => '600',
				'group'   => 'font',
				'studio'  => false,
				'syntax'  => '<integer>',
				'desc'    => __( 'Font weight used for field labels.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'field_label_transform'      => [
				'css_var' => '--gv-field-label-text-transform',
				'default' => 'none',
				'group'   => 'font',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Casing applied to field labels. Default `none` is SC 1.4.12 text-spacing safe.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => [
					'none',
					'uppercase',
					'capitalize',
					'lowercase',
				],
			],
			'field_label_letter_spacing' => [
				'css_var' => '--gv-field-label-letter-spacing',
				'default' => '0.05em',
				'group'   => 'font',
				// Surfaced for theme-preset writes (Boutique pairs an
				// uppercase field-label transform with a wider tracking
				// to keep the legibility of an all-caps label).
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Letter-spacing applied to field labels (only relevant when transform is uppercase).', 'gk-gravityview' ),
				'control' => 'text',
			],

			// Private scale primitives; not user-tuneable.
			'_font_scale_xs'             => [
				'css_var' => '--gv-_font-scale-xs',
				'default' => '0.75',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '<number>',
				'desc'    => '',
				'control' => 'number',
			],
			'_font_scale_sm'             => [
				'css_var' => '--gv-_font-scale-sm',
				'default' => '0.875',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '<number>',
				'desc'    => '',
				'control' => 'number',
			],
			'_font_scale_md'             => [
				'css_var' => '--gv-_font-scale-md',
				'default' => '1.125',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '<number>',
				'desc'    => '',
				'control' => 'number',
			],
			'_font_scale_lg'             => [
				'css_var' => '--gv-_font-scale-lg',
				'default' => '1.25',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '<number>',
				'desc'    => '',
				'control' => 'number',
			],
			'_font_scale_xl'             => [
				'css_var' => '--gv-_font-scale-xl',
				'default' => '1.5',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '<number>',
				'desc'    => '',
				'control' => 'number',
			],
			'_font_scale_2xl'            => [
				'css_var' => '--gv-_font-scale-2xl',
				'default' => '1.875',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '<number>',
				'desc'    => '',
				'control' => 'number',
			],
			'_letter_spacing_uppercase'  => [
				'css_var' => '--gv-_letter-spacing-uppercase',
				'default' => '0.05em',
				'group'   => '_private',
				'studio'  => false,
				'private' => true,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],

			// Layer-2 typography slots (field-label / button) that
			// resolve to typography primitives via `var(--gv-...)`.
			'field_label_direction'      => [
				'css_var' => '--gv-field-label-direction',
				'default' => 'column',
				'group'   => 'field-label',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_font_size'           => [
				'css_var' => '--gv-button-font-size',
				'default' => 'var(--gv-font-size-sm)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_font_weight'         => [
				'css_var' => '--gv-button-font-weight',
				'default' => 'var(--gv-font-weight-medium)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
		];
	}

	// -------------------------------------------------------------
	// Dimensions tokens (WP block-supports `dimensions`)
	// -------------------------------------------------------------

	/**
	 * Raw dimensions tokens: space scale, padding, margin, gap, sizes.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function dimensions(): array {
		$space_scale = [
			'1'  => '4px',
			'2'  => '8px',
			'3'  => '12px',
			'4'  => '16px',
			'5'  => '20px',
			'6'  => '24px',
			'8'  => '32px',
			'10' => '40px',
			'12' => '48px',
			'16' => '64px',
		];
		$space       = [];
		foreach ( $space_scale as $step => $value ) {
			$step                     = (string) $step;
			$space[ "space_{$step}" ] = [
				'css_var' => "--gv-space-{$step}",
				'default' => $value,
				'group'   => 'space',
				'studio'  => false,
				'syntax'  => '<length>',
				/* translators: %s: the spacing step's length value, e.g. 4px. */
				'desc'    => sprintf( __( '%s base spacing increment.', 'gk-gravityview' ), $value ),
				'control' => 'number',
			];
		}

		$entry_padding = [];
		foreach ( [ 'top', 'right', 'bottom', 'left', 'block_start', 'block_end', 'inline_start', 'inline_end' ] as $side ) {
			$slug                   = "entry_padding_{$side}";
			$css                    = '--gv-entry-padding-' . str_replace( '_', '-', $side );
			$entry_padding[ $slug ] = [
				'css_var' => $css,
				'default' => '24px',
				'group'   => 'entry_padding',
				'studio'  => in_array( $side, [ 'top', 'right', 'bottom', 'left' ], true ),
				'syntax'  => '<length-percentage>',
				'desc'    => __( 'Inner padding of entry cards on one side.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em', '%' ],
				'min'     => 0,
				'step'    => 2,
			];
		}

		$widget_padding = [];
		foreach ( [ 'block', 'inline' ] as $axis ) {
			$widget_padding[ "widget_padding_{$axis}" ] = [
				'css_var' => "--gv-widget-padding-{$axis}",
				'default' => 'block' === $axis ? '20px' : '16px',
				'group'   => 'widget_padding',
				'studio'  => true,
				'syntax'  => '<length-percentage>',
				'desc'    => __( 'Padding inside the search bar and widget areas.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em', '%' ],
				'min'     => 0,
				'step'    => 2,
			];
		}

		$misc = [
			'widget_margin_block_end'  => [
				'css_var' => '--gv-widget-margin-block-end',
				'default' => '32px',
				'group'   => 'widget',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Bottom spacing between widget areas.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'search_field_min_width'   => [
				'css_var' => '--gv-search-field-min-width',
				'default' => 'clamp(140px, 50vw, 200px)',
				'group'   => 'search',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Minimum inline-size of each search field. Clamps on narrow viewports.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'search_row_gap'           => [
				'css_var' => '--gv-search-row-gap',
				'default' => '16px',
				'group'   => 'search',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Vertical gap between rows of search fields.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'search_col_gap'           => [
				'css_var' => '--gv-search-col-gap',
				'default' => '16px',
				'group'   => 'search',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Horizontal gap between side-by-side search fields.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'toolbar_gap'              => [
				'css_var' => '--gv-toolbar-gap',
				'default' => '8px',
				'group'   => 'toolbar',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Gap between toolbar children.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'toolbar_padding'          => [
				'css_var' => '--gv-toolbar-padding',
				'default' => '16px',
				'group'   => 'toolbar',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Inner padding of toolbar containers.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'toolbar_margin_block_end' => [
				'css_var' => '--gv-toolbar-margin-block-end',
				'default' => '16px',
				'group'   => 'toolbar',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Bottom spacing of toolbar containers.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'pagination_gap'           => [
				'css_var' => '--gv-pagination-gap',
				'default' => '4px',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Gap between pagination buttons.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'pagination_button_size'   => [
				'css_var' => '--gv-pagination-button-size',
				'default' => '36px',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Pagination button width / height. 36px meets SC 2.5.8 AA (24px floor) but not SC 2.5.5 AAA (44px).', 'gk-gravityview' ),
				'control' => 'number',
			],
			'checkbox_size'            => [
				'css_var' => '--gv-checkbox-size',
				'default' => '16px',
				'group'   => 'control',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Width and height of checkboxes / radios.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'map_marker_size'          => [
				'css_var' => '--gv-map-marker-size',
				'default' => '18px',
				'group'   => 'map',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Width and height of map cluster markers and SVG indicators.', 'gk-gravityview' ),
				'control' => 'number',
			],
		];

		// Layer-2 spacing slots (entry / field-label / button) that
		// resolve to the space scale via `var(--gv-...)`.
		$components = [
			'entry_field_gap'              => [
				'css_var' => '--gv-entry-field-gap',
				'default' => 'var(--gv-space-3)',
				'group'   => 'entry',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'field_label_margin_block_end' => [
				'css_var' => '--gv-field-label-margin-block-end',
				'default' => 'var(--gv-space-1)',
				'group'   => 'field-label',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_padding_block'         => [
				'css_var' => '--gv-button-padding-block',
				'default' => 'var(--gv-space-2)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_padding_inline'        => [
				'css_var' => '--gv-button-padding-inline',
				'default' => 'var(--gv-space-5)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_min_height'            => [
				'css_var' => '--gv-button-min-height',
				'default' => '0',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
		];

		return array_merge( $space, $entry_padding, $widget_padding, $misc, $components );
	}

	// -------------------------------------------------------------
	// Border tokens (WP block-supports `border`)
	// -------------------------------------------------------------

	/**
	 * Raw border tokens: radius, width, style, color, focus-ring.
	 *
	 * Slugs follow the WP block-supports vocabulary: redundant `border_`
	 * prefixes are dropped where the category name already conveys it
	 * (`width_xs` instead of `border_width_xs`, `entry_color` instead of
	 * `entry_border_color`). CSS-var names keep their original spelling
	 * (`--gv-border-width-*`, `--gv-entry-border-*`) so customer Custom
	 * CSS targeting those vars continues to resolve.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function border(): array {
		$radius_scale  = [
			'xs'   => '2px',
			'sm'   => '4px',
			'md'   => '6px',
			'lg'   => '8px',
			'xl'   => '12px',
			'full' => '9999px',
		];
		$radius_studio = [ 'xs', 'xl' ];
		$radius        = [];
		foreach ( $radius_scale as $name => $value ) {
			$radius[ "radius_{$name}" ] = [
				'css_var' => "--gv-radius-{$name}",
				'default' => $value,
				'group'   => 'radius',
				'studio'  => in_array( $name, $radius_studio, true ),
				'syntax'  => '<length>',
				/* translators: %s: the radius step's length value, e.g. 6px. */
				'desc'    => sprintf( __( '%s corner-radius step.', 'gk-gravityview' ), $value ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em' ],
				'min'     => 0,
				'step'    => 1,
			];
		}

		$widths = [
			'0' => '0',
			'1' => '1px',
			'2' => '2px',
			'3' => '3px',
			'4' => '4px',
			'8' => '8px',
		];
		$width  = [];
		foreach ( $widths as $step => $value ) {
			$step                     = (string) $step;
			$width[ "width_{$step}" ] = [
				'css_var' => "--gv-border-width-{$step}",
				'default' => $value,
				'group'   => 'width',
				'studio'  => false,
				'syntax'  => '<length>',
				/* translators: %s: the border-width step's length value, e.g. 1px. */
				'desc'    => sprintf( __( '%s border-width step.', 'gk-gravityview' ), $value ),
				'control' => 'number',
			];
		}

		$component = [
			'widget_radius'            => [
				'css_var' => '--gv-widget-border-radius',
				'default' => '8px',
				'group'   => 'widget',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Corner radius of widget containers.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'pagination_button_radius' => [
				'css_var' => '--gv-pagination-button-radius',
				'default' => '6px',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Corner radius of pagination buttons.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'control_width'            => [
				'css_var' => '--gv-control-border-width',
				'default' => '1px',
				'group'   => 'control',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'Border width of form controls.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px' ],
				'min'     => 0,
				'step'    => 1,
			],
			'control_radius'           => [
				'css_var' => '--gv-control-radius',
				'default' => '6px',
				'group'   => 'control',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'Corner roundness of search inputs, the search button, pagination buttons, and edit-entry inputs.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em', '%' ],
				'min'     => 0,
				'step'    => 1,
			],
		];

		$focus_ring = [
			'focus_ring_width'  => [
				'css_var' => '--gv-focus-ring-width',
				'default' => '2px',
				'group'   => 'focus',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'Width of the focus-visible ring.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px' ],
				'min'     => 1,
				'step'    => 1,
			],
			'focus_ring_offset' => [
				'css_var' => '--gv-focus-ring-offset',
				'default' => '2px',
				'group'   => 'focus',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'Distance from element edge to ring inner edge.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px' ],
				'min'     => 0,
				'step'    => 1,
			],
			'focus_ring_style'  => [
				'css_var' => '--gv-focus-ring-style',
				'default' => 'solid',
				'group'   => 'focus',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Outline style of the focus-visible ring.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => [
					'solid',
					'dashed',
					'dotted',
				],
			],
		];

		$entry = [
			'entry_radius' => [
				'css_var' => '--gv-entry-radius',
				'default' => '8px',
				'group'   => 'entry',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'How rounded the corners of entry cards are.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em', '%' ],
				'min'     => 0,
				'step'    => 1,
			],
		];
		foreach ( [ 'width', 'style', 'color' ] as $part ) {
			$entry[ "entry_{$part}" ]       = [
				'css_var'    => "--gv-entry-border-{$part}",
				'default'    => 'width' === $part ? '1px' : ( 'style' === $part ? 'solid' : '#e5e7eb' ),
				'group'      => 'entry',
				'studio'     => true,
				'syntax'     => 'width' === $part ? '<length>' : ( 'color' === $part ? '<color>' : '*' ),
				'desc'       => __( 'Card border component.', 'gk-gravityview' ),
				'control'    => 'color' === $part ? 'color' : ( 'style' === $part ? 'select' : 'number' ),
				'options'    => 'style' === $part ? [ 'solid', 'dashed', 'dotted', 'double', 'none' ] : null,
				'hover_slug' => "border.entry_{$part}_hover",
			];
			$entry[ "entry_{$part}_hover" ] = [
				'css_var' => "--gv-entry-border-{$part}-hover",
				'default' => 'width' === $part ? '1px' : ( 'style' === $part ? 'solid' : '#e5e7eb' ),
				'group'   => 'entry',
				// Written by Dark / Minimal theme presets; must round-trip
				// through `ViewStyles::tokens()`.
				'studio'  => true,
				'syntax'  => 'width' === $part ? '<length>' : ( 'color' === $part ? '<color>' : '*' ),
				'desc'    => __( 'Hover variant of card border component.', 'gk-gravityview' ),
				'control' => 'color' === $part ? 'color' : ( 'style' === $part ? 'select' : 'number' ),
				'options' => 'style' === $part ? [ 'solid', 'dashed', 'dotted', 'double', 'none' ] : null,
			];
		}

		// Layer-2 border slots (table cell / button) that resolve to
		// the border-width and radius scales via `var(--gv-...)`.
		$components = [
			'table_cell_border_width'       => [
				'css_var' => '--gv-table-cell-border-width',
				'default' => 'var(--gv-border-width-0)',
				'group'   => 'table',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'table_cell_border_style'       => [
				'css_var' => '--gv-table-cell-border-style',
				'default' => 'solid',
				'group'   => 'table',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_border_width'           => [
				'css_var' => '--gv-button-border-width',
				'default' => 'var(--gv-border-width-1)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_secondary_border_width' => [
				'css_var' => '--gv-button-secondary-border-width',
				'default' => 'var(--gv-border-width-1)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_danger_border_width'    => [
				'css_var' => '--gv-button-danger-border-width',
				'default' => 'var(--gv-border-width-1)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
			'button_radius'                 => [
				'css_var' => '--gv-button-radius',
				'default' => 'var(--gv-radius-md)',
				'group'   => 'button',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => '',
				'control' => 'text',
			],
		];

		return array_merge( $radius, $width, $component, $focus_ring, $entry, $components );
	}

	// -------------------------------------------------------------
	// Shadow tokens (WP block-supports `shadow`)
	// -------------------------------------------------------------

	/**
	 * Raw shadow tokens: scale, primitives, entry shadow.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function shadow(): array {
		$shadow_options = [
			'none',
			'0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
			'0 1px 3px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
			'0 4px 6px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 2px 4px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
			'0 10px 15px -3px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 4px 6px -2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
		];

		$shadow_scale = [
			'shadow_xs' => [
				'css_var' => '--gv-shadow-xs',
				'default' => '0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
			],
			'shadow_sm' => [
				'css_var' => '--gv-shadow-sm',
				'default' => '0 1px 4px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 78%), transparent)',
			],
			'shadow_md' => [
				'css_var' => '--gv-shadow-md',
				'default' => '0 1px 3px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
			],
			'shadow_lg' => [
				'css_var' => '--gv-shadow-lg',
				'default' => '0 4px 6px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 2px 4px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
			],
			'shadow_xl' => [
				'css_var' => '--gv-shadow-xl',
				'default' => '0 10px 15px -3px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 4px 6px -2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
			],
		];
		$shadows      = [];
		foreach ( $shadow_scale as $slug => $entry ) {
			$shadows[ $slug ] = [
				'css_var' => $entry['css_var'],
				'default' => $entry['default'],
				'group'   => 'shadow',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Composed shadow value derived from shadow color + alpha.', 'gk-gravityview' ),
				'control' => 'text',
			];
		}

		$primitives = [
			'shadow_color' => [
				'css_var' => '--gv-shadow-color',
				'default' => '#121961',
				'group'   => 'advanced',
				'studio'  => true,
				'syntax'  => '<color>',
				'desc'    => __( 'Color every shadow level is tinted with. Set it to a brand color for a non-blue palette.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'shadow_alpha' => [
				'css_var' => '--gv-shadow-alpha',
				'default' => '0.1',
				'group'   => 'advanced',
				'studio'  => true,
				'syntax'  => '<number>',
				'desc'    => __( 'Base alpha multiplier shared by every shadow level.', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 0,
				'max'     => 1,
				'step'    => 0.05,
			],
			'shadow_focus' => [
				'css_var' => '--gv-shadow-focus',
				'default' => '0 0 0 var(--gv-focus-ring-width) var(--gv-focus-ring-color)',
				'group'   => 'focus',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Composed box-shadow value derived from focus-ring width + color.', 'gk-gravityview' ),
				'control' => 'text',
			],
		];

		$entry_shadows = [
			'entry_shadow'       => [
				'css_var'    => '--gv-entry-shadow',
				'default'    => '0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
				'group'      => 'entry',
				'studio'     => true,
				'syntax'     => '*',
				'desc'       => __( 'Drop-shadow depth applied to entry cards at rest.', 'gk-gravityview' ),
				'control'    => 'select',
				'options'    => $shadow_options,
				'hover_slug' => 'shadow.entry_shadow_hover',
			],
			'entry_shadow_hover' => [
				'css_var' => '--gv-entry-shadow-hover',
				'default' => '0 4px 6px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 2px 4px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
				'group'   => 'entry',
				// Written by Minimal / Editorial theme presets; must
				// round-trip through `ViewStyles::tokens()`.
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Drop-shadow depth applied to entry cards on hover.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => $shadow_options,
			],
		];

		return array_merge( $shadows, $primitives, $entry_shadows );
	}


	// -------------------------------------------------------------
	// Layout tokens (grid, z-index, opacity, touch-target)
	// -------------------------------------------------------------

	/**
	 * Raw layout / structural tokens.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function layout(): array {
		$opacity_scale = [];
		for ( $i = 0; $i <= 100; $i += 10 ) {
			$value                           = 100 === $i ? '1' : ( 0 === $i ? '0' : '0.' . ( $i / 10 ) );
			$opacity_scale[ "opacity_{$i}" ] = [
				'css_var' => "--gv-opacity-{$i}",
				'default' => $value,
				'group'   => 'opacity',
				'studio'  => false,
				'syntax'  => '<number>',
				/* translators: %s: the opacity step value, e.g. 0.5. */
				'desc'    => sprintf( __( 'Opacity step %s.', 'gk-gravityview' ), $value ),
				'control' => 'number',
			];
		}

		$z_scale = [
			'z_base'     => [
				'css_var' => '--gv-z-base',
				'default' => '0',
			],
			'z_raised'   => [
				'css_var' => '--gv-z-raised',
				'default' => '10',
			],
			'z_dropdown' => [
				'css_var' => '--gv-z-dropdown',
				'default' => '100',
			],
			'z_popover'  => [
				'css_var' => '--gv-z-popover',
				'default' => '200',
			],
			'z_sticky'   => [
				'css_var' => '--gv-z-sticky',
				'default' => '300',
			],
			'z_overlay'  => [
				'css_var' => '--gv-z-overlay',
				'default' => '400',
			],
			// Modal sits far above the in-View scale: lightboxes mount at
			// body level and must clear theme sticky headers, cookie
			// banners, and chat widgets (Fancybox itself defaults to 99992).
			'z_modal'    => [
				'css_var' => '--gv-z-modal',
				'default' => '100000',
			],
			'z_toast'    => [
				'css_var' => '--gv-z-toast',
				'default' => '600',
			],
		];
		$z_studio = [ 'z_dropdown', 'z_popover', 'z_modal', 'z_toast' ];
		$z        = [];
		foreach ( $z_scale as $slug => $entry ) {
			$z[ $slug ] = [
				'css_var' => $entry['css_var'],
				'default' => $entry['default'],
				'group'   => 'z_index',
				'studio'  => in_array( $slug, $z_studio, true ),
				'syntax'  => '<integer>',
				'desc'    => __( 'Z-index scale step.', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 0,
				'step'    => 1,
			];
		}

		$grid = [
			'grid_columns'    => [
				'css_var' => '--gv-grid-columns',
				'default' => '1',
				'group'   => 'grid',
				'studio'  => false,
				'syntax'  => '<integer>',
				'desc'    => __( 'Number of columns in card-grid layouts (1–6).', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 1,
				'max'     => 6,
				'step'    => 1,
			],
			'grid_columns_lg' => [
				'css_var'           => '--gv-grid-columns-lg',
				// Empty default: unset means "inherit the base column count
				// via the var() fallback". SCSS consumes this token as
				// `var(--gv-grid-columns-lg, var(--gv-grid-columns))`, so a
				// typed @property registration with an initial value would
				// make that fallback permanently dead. Registration is
				// skipped via `register_property`.
				'default'           => '',
				'register_property' => false,
				'group'             => 'grid',
				'studio'            => false,
				'syntax'            => '<integer>',
				'desc'              => __( 'Responsive cap for grid columns at the large breakpoint.', 'gk-gravityview' ),
				'control'           => 'number',
			],
			'grid_columns_md' => [
				'css_var'           => '--gv-grid-columns-md',
				// Empty default: unset means "inherit the base column count
				// via the var() fallback". SCSS consumes this token as
				// `var(--gv-grid-columns-md, var(--gv-grid-columns))`, so a
				// typed @property registration with an initial value would
				// make that fallback permanently dead. Registration is
				// skipped via `register_property`.
				'default'           => '',
				'register_property' => false,
				'group'             => 'grid',
				'studio'            => false,
				'syntax'            => '<integer>',
				'desc'              => __( 'Responsive cap for grid columns at the medium breakpoint.', 'gk-gravityview' ),
				'control'           => 'number',
			],
			'grid_gap'        => [
				'css_var' => '--gv-grid-gap',
				'default' => '24px',
				'group'   => 'grid',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Gap between cards in the grid.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'grid_align'      => [
				'css_var' => '--gv-grid-align',
				'default' => 'stretch',
				'group'   => 'grid',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Card alignment within the grid track.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => [
					'stretch',
					'start',
				],
			],
			'card_min_width'  => [
				'css_var' => '--gv-card-min-width',
				'default' => '0px',
				'group'   => 'grid',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Minimum card width before reflow.', 'gk-gravityview' ),
				'control' => 'number',
			],
		];

		$structural = [
			'single_entry_label_inline_size' => [
				'css_var' => '--gv-single-entry-label-inline-size',
				'default' => '30%',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length-percentage>',
				'desc'    => __( 'Logical inline-size of the label column in single-entry view. RTL-correct.', 'gk-gravityview' ),
				'control' => 'text',
			],
			// Legacy alias retained for current consumers.
			'single_entry_label_width'       => [
				'css_var' => '--gv-single-entry-label-width',
				'default' => '30%',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length-percentage>',
				'desc'    => __( 'Legacy physical-side alias for single-entry label width.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'list_media_inline_size'         => [
				'css_var' => '--gv-list-media-inline-size',
				'default' => '33.33%',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length-percentage>',
				'desc'    => __( 'Logical inline-size of the media column in list layouts.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'list_media_width'               => [
				'css_var' => '--gv-list-media-width',
				'default' => '33.33%',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length-percentage>',
				'desc'    => __( 'Legacy physical-side alias for list media width.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'map_infowindow_max_width'       => [
				'css_var' => '--gv-map-infowindow-max-width',
				'default' => 'min(320px, calc(100vw - 2 * var(--gv-space-4)))',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Maximum width of map infowindows.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'map_infowindow_image_max_width' => [
				'css_var' => '--gv-map-infowindow-image-max-width',
				'default' => '100px',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Maximum width of images inside map infowindows.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'size_touch_target'              => [
				'css_var' => '--gv-size-touch-target',
				'default' => '44px',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Minimum inline/block size for interactive elements (SC 2.5.5 AAA).', 'gk-gravityview' ),
				'control' => 'number',
			],
			'drag_handle_size'               => [
				'css_var' => '--gv-drag-handle-size',
				'default' => '44px',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Minimum size of drag handles. Pairs with click-to-pick alternative per SC 2.5.7.', 'gk-gravityview' ),
				'control' => 'number',
			],
			'drag_handle_color'              => [
				'css_var' => '--gv-drag-handle-color',
				'default' => '#5b6470',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of drag-handle iconography.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'scrollbar_thumb_color'          => [
				'css_var' => '--gv-scrollbar-thumb-color',
				'default' => '#888888',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of scrollbar thumbs (browsers that support `scrollbar-color`).', 'gk-gravityview' ),
				'control' => 'color',
			],
			'scrollbar_track_color'          => [
				'css_var' => '--gv-scrollbar-track-color',
				'default' => '#f2f3f5',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Color of scrollbar tracks.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'auto_advance_paused'            => [
				'css_var' => '--gv-auto-advance-paused',
				'default' => '0',
				'group'   => 'structural',
				'studio'  => false,
				'syntax'  => '<integer>',
				'desc'    => __( 'SC 2.2.2 pause flag: set to 1 to halt auto-rotating widgets.', 'gk-gravityview' ),
				'control' => 'number',
			],
		];

		// Table layer-2 tokens are structurally grouped under layout
		// because they are not user-tunable customization-surface color items.
		$tables = [
			'table_header_background'   => [
				'css_var' => '--gv-table-header-background',
				'default' => '#f2f3f5',
				'group'   => 'table',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Background of table header rows.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'table_header_font_size'    => [
				'css_var' => '--gv-table-header-font-size',
				'default' => 'calc(var(--gv-font-size-base) * 0.75)',
				'group'   => 'table',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Font size of table header text.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'table_cell_padding_block'  => [
				'css_var' => '--gv-table-cell-padding-block',
				'default' => '12px',
				'group'   => 'table',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'Vertical padding inside table cells.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em' ],
				'min'     => 0,
				'step'    => 0.1,
			],
			'table_cell_padding_inline' => [
				'css_var' => '--gv-table-cell-padding-inline',
				'default' => '16px',
				'group'   => 'table',
				'studio'  => true,
				'syntax'  => '<length>',
				'desc'    => __( 'Horizontal padding inside table cells.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem', 'em' ],
				'min'     => 0,
				'step'    => 0.1,
			],
		];

		// Widget container Layer-2 tokens.
		$widgets = [
			'widget_border_color'            => [
				'css_var' => '--gv-widget-border-color',
				'default' => '#e5e7eb',
				'group'   => 'widget',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Border color of widget containers.', 'gk-gravityview' ),
				'control' => 'color',
			],
			'pagination_button_border_color' => [
				'css_var' => '--gv-pagination-button-border-color',
				'default' => '#6b7280',
				'group'   => 'pagination',
				'studio'  => false,
				'syntax'  => '<color>',
				'desc'    => __( 'Border color of pagination buttons. Defaults to border-strong (4.74:1 vs surface).', 'gk-gravityview' ),
				'control' => 'color',
			],
		];

		// Host-contrast multi-layer defense, Layer 2 (self-contained mode).
		// See docs/superpowers/specs/2026-05-20-host-contrast-multi-layer.md.
		$self_contained = [
			'self_contained'      => [
				'css_var' => '--gv-self-contained',
				'default' => '0',
				'group'   => 'layout',
				'studio'  => true,
				'syntax'  => '<number>',
				'desc'    => __( 'Paint a backdrop around the entire View so contrast is guaranteed regardless of host theme. Trade-off: View looks like a discrete container rather than integrating with the host page.', 'gk-gravityview' ),
				'control' => 'toggle',
			],
			'view_wrapper_radius' => [
				'css_var' => '--gv-view-wrapper-radius',
				'default' => '0',
				'group'   => 'layout',
				'studio'  => false,
				'syntax'  => '<length>',
				'desc'    => __( 'Border radius applied to the outer View wrapper when Self-Contained Mode is enabled. Presets may override.', 'gk-gravityview' ),
				'control' => 'number',
				'units'   => [ 'px', 'rem' ],
				'min'     => 0,
				'step'    => 1,
			],
			'view_wrapper_shadow' => [
				'css_var' => '--gv-view-wrapper-shadow',
				'default' => 'none',
				'group'   => 'layout',
				'studio'  => false,
				'syntax'  => '*',
				'desc'    => __( 'Drop-shadow applied to the outer View wrapper when Self-Contained Mode is enabled. Presets may override.', 'gk-gravityview' ),
				'control' => 'select',
				'options' => [
					'none',
					'0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
					'0 1px 3px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 1px 2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
					'0 4px 6px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 2px 4px -1px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 60%), transparent)',
					'0 10px 15px -3px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 100%), transparent), 0 4px 6px -2px color-mix(in srgb, var(--gv-shadow-color) calc(var(--gv-shadow-alpha) * 50%), transparent)',
				],
			],
		];

		return array_merge( $opacity_scale, $z, $grid, $structural, $tables, $widgets, $self_contained );
	}

	// -------------------------------------------------------------
	// Motion tokens (transitions + easings)
	// -------------------------------------------------------------

	/**
	 * Raw motion tokens.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function motion(): array {
		return [
			'motion_multiplier' => [
				'css_var' => '--gv-motion-multiplier',
				'default' => '1',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '<number>',
				'desc'    => __( 'Global scalar applied to every transition duration. Set to 0.01 under prefers-reduced-motion.', 'gk-gravityview' ),
				'control' => 'number',
				'min'     => 0,
				'max'     => 2,
				'step'    => 0.1,
			],
			'transition_fast'   => [
				'css_var' => '--gv-transition-fast',
				'default' => 'calc(0.15s * var(--gv-motion-multiplier))',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Hover / focus transitions.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'transition_medium' => [
				'css_var' => '--gv-transition-medium',
				'default' => 'calc(0.25s * var(--gv-motion-multiplier))',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Panel open / close transitions.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'transition_slow'   => [
				'css_var' => '--gv-transition-slow',
				'default' => 'calc(0.4s * var(--gv-motion-multiplier))',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Hero / page-scale transitions.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'easing_standard'   => [
				'css_var' => '--gv-easing-standard',
				'default' => 'cubic-bezier(0.2, 0.0, 0.0, 1.0)',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Default easing curve.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'easing_decelerate' => [
				'css_var' => '--gv-easing-decelerate',
				'default' => 'cubic-bezier(0.0, 0.0, 0.2, 1.0)',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Enter / appear easing.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'easing_accelerate' => [
				'css_var' => '--gv-easing-accelerate',
				'default' => 'cubic-bezier(0.3, 0.0, 1.0, 1.0)',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Exit / dismiss easing.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'easing_emphasized' => [
				'css_var' => '--gv-easing-emphasized',
				'default' => 'cubic-bezier(0.3, 0.0, 0.8, 0.15)',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Hero transition easing.', 'gk-gravityview' ),
				'control' => 'text',
			],
			'easing_linear'     => [
				'css_var' => '--gv-easing-linear',
				'default' => 'linear',
				'group'   => 'motion',
				'studio'  => true,
				'syntax'  => '*',
				'desc'    => __( 'Linear easing.', 'gk-gravityview' ),
				'control' => 'text',
			],
		];
	}
}
