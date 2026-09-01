<?php
/**
 * Server-side OKLCh color interpolation for the design-token system.
 *
 * Port of Björn Ottosson's public-domain OKLab reference implementation
 * (the inventor's own): https://bottosson.github.io/posts/oklab/
 *
 * Used by the saved-CSS pipeline to pre-resolve `color-mix(in oklch, …)`
 * expressions into literal hex values so browsers below the
 * `color-mix()` Baseline floor still render derived hovers correctly.
 *
 * Matrix constants come from CSS Color 4 §10.2 (linear sRGB → OKLab M1
 * and M2 matrices) baked in literally so the code matches the spec
 * byte-for-byte. Reverse-direction matrices are computed inverses of
 * M1 and M2 from the same spec section.
 *
 * Test fixtures in `tests/Settings/ColorMixFixtures/` compare against
 * the `culori` JavaScript reference implementation via a one-shot Node
 * harness and assert via OKLab ΔE ≤ 1e-4 (NOT raw relative luminance —
 * same-L different-hue pairs would slip through).
 *
 * @package GravityKit\GravityView
 * @since   TBD
 */

namespace GravityKit\GravityView\Settings;

/**
 * OKLCh color-mix implementation.
 */
final class ColorMix {

	/**
	 * Mix two sRGB hex colors in OKLCh space and return the result as
	 * an sRGB hex string. Mirrors the CSS `color-mix(in oklch, $a $a_pct, $b)`
	 * function for the most common shape used in this design system:
	 * `color-mix(in oklch, var(--brand) {pct}%, #000)`.
	 *
	 * @since 3.0.0
	 *
	 * @param string $a_hex   Base color (e.g. `#204ce5`).
	 * @param string $b_hex   Mix-to color (e.g. `#000000`).
	 * @param int    $a_pct   Percentage of `$a` in the mix (0–100). The
	 *                        remainder is taken from `$b`. Default 90.
	 *
	 * @return string Result hex, e.g. `#1c44ce`. Returns `$a_hex` unchanged
	 *                if either input fails to parse.
	 */
	public static function mix( string $a_hex, string $b_hex, int $a_pct = 90 ): string {
		$a_rgb = self::hex_to_rgb( $a_hex );
		$b_rgb = self::hex_to_rgb( $b_hex );

		if ( ! $a_rgb || ! $b_rgb ) {
			return $a_hex;
		}

		$t = max( 0, min( 100, $a_pct ) ) / 100;

		// Convert both inputs sRGB → linear sRGB → OKLab → OKLCh.
		$a_lch = self::oklab_to_oklch( self::lin_srgb_to_oklab( self::srgb_to_lin_srgb( $a_rgb ) ) );
		$b_lch = self::oklab_to_oklch( self::lin_srgb_to_oklab( self::srgb_to_lin_srgb( $b_rgb ) ) );

		// Interpolate L, C, h in OKLCh.
		//
		// Hue handling: when one endpoint is achromatic (C ≈ 0) its
		// hue is "powerless" per CSS Color 4 §4.4 — use the other
		// endpoint's hue. This is the common case for our recipe
		// (`color-mix(in oklch, $brand 90%, #000)`) where `#000` has
		// no chroma.
		$achroma_threshold = 1e-4;
		if ( $b_lch['C'] < $achroma_threshold ) {
			$b_lch['h'] = $a_lch['h'];
		} elseif ( $a_lch['C'] < $achroma_threshold ) {
			$a_lch['h'] = $b_lch['h'];
		}

		$out_lch = [
			'L' => self::lerp( $a_lch['L'], $b_lch['L'], 1 - $t ),
			'C' => self::lerp( $a_lch['C'], $b_lch['C'], 1 - $t ),
			'h' => self::lerp_hue( $a_lch['h'], $b_lch['h'], 1 - $t ),
		];

		// Reverse: OKLCh → OKLab → linear sRGB → sRGB.
		$out_lin = self::oklab_to_lin_srgb( self::oklch_to_oklab( $out_lch ) );

		// Gamut-map any out-of-range channels via CSS Color 4 §13
		// binary search on chroma — reduce chroma until the color
		// fits inside the sRGB gamut.
		if ( self::is_out_of_gamut_srgb( $out_lin ) ) {
			$out_lch = self::gamut_map_to_srgb( $out_lch );
			$out_lin = self::oklab_to_lin_srgb( self::oklch_to_oklab( $out_lch ) );
		}

		$out_rgb = self::lin_srgb_to_srgb( $out_lin );

		return self::rgb_to_hex( $out_rgb );
	}

	/**
	 * Lerp helper.
	 *
	 * @since 3.0.0
	 *
	 * @param float $a Start value.
	 * @param float $b End value.
	 * @param float $t Mix factor, 0..1 (0 = pure $a, 1 = pure $b).
	 *
	 * @return float
	 */
	private static function lerp( float $a, float $b, float $t ): float {
		return $a + ( $b - $a ) * $t;
	}

	/**
	 * Hue interpolation in degrees, shortest-arc per CSS Color 4 §12.1
	 * ("shorter hue interpolation" — the default for `color-mix`).
	 *
	 * @since 3.0.0
	 *
	 * @param float $a Start hue (degrees, 0..360).
	 * @param float $b End hue (degrees, 0..360).
	 * @param float $t Mix factor.
	 *
	 * @return float Interpolated hue, normalised to [0, 360).
	 */
	private static function lerp_hue( float $a, float $b, float $t ): float {
		$diff = $b - $a;
		if ( $diff > 180 ) {
			$a += 360;
		} elseif ( $diff < -180 ) {
			$b += 360;
		}
		$h = self::lerp( $a, $b, $t );
		$h = fmod( $h, 360 );
		return $h < 0 ? $h + 360 : $h;
	}

	/**
	 * Parse a `#rgb`/`#rrggbb` hex string to integer RGB 0..255.
	 *
	 * @since 3.0.0
	 *
	 * @param string $hex Input hex string.
	 *
	 * @return array{r:int,g:int,b:int}|null Null on parse failure.
	 */
	private static function hex_to_rgb( string $hex ): ?array {
		$hex = ltrim( trim( $hex ), '#' );

		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) {
			return null;
		}

		return [
			'r' => hexdec( substr( $hex, 0, 2 ) ),
			'g' => hexdec( substr( $hex, 2, 2 ) ),
			'b' => hexdec( substr( $hex, 4, 2 ) ),
		];
	}

	/**
	 * Format RGB 0..255 back to `#rrggbb`.
	 *
	 * @since 3.0.0
	 *
	 * @param array{r:int,g:int,b:int} $rgb RGB triple.
	 *
	 * @return string Lowercase hex with leading `#`.
	 */
	private static function rgb_to_hex( array $rgb ): string {
		return sprintf( '#%02x%02x%02x',
			max( 0, min( 255, (int) round( $rgb['r'] ) ) ),
			max( 0, min( 255, (int) round( $rgb['g'] ) ) ),
			max( 0, min( 255, (int) round( $rgb['b'] ) ) )
		);
	}

	/**
	 * sRGB (0..255) → linear sRGB (0..1). Gamma decode per IEC 61966-2-1.
	 *
	 * @since 3.0.0
	 *
	 * @param array{r:int,g:int,b:int} $rgb 0..255 sRGB.
	 *
	 * @return array{r:float,g:float,b:float} 0..1 linear sRGB.
	 */
	private static function srgb_to_lin_srgb( array $rgb ): array {
		$decode = static function ( float $c ): float {
			$c /= 255;
			return $c <= 0.04045
				? $c / 12.92
				: pow( ( $c + 0.055 ) / 1.055, 2.4 );
		};
		return [
			'r' => $decode( $rgb['r'] ),
			'g' => $decode( $rgb['g'] ),
			'b' => $decode( $rgb['b'] ),
		];
	}

	/**
	 * Linear sRGB (0..1) → sRGB (0..255). Gamma encode + clamp.
	 *
	 * @since 3.0.0
	 *
	 * @param array{r:float,g:float,b:float} $lin 0..1 linear sRGB.
	 *
	 * @return array{r:int,g:int,b:int} 0..255 sRGB.
	 */
	private static function lin_srgb_to_srgb( array $lin ): array {
		$encode = static function ( float $c ): float {
			$c = max( 0, min( 1, $c ) );
			return $c <= 0.0031308
				? $c * 12.92
				: 1.055 * pow( $c, 1 / 2.4 ) - 0.055;
		};
		return [
			'r' => (int) round( $encode( $lin['r'] ) * 255 ),
			'g' => (int) round( $encode( $lin['g'] ) * 255 ),
			'b' => (int) round( $encode( $lin['b'] ) * 255 ),
		];
	}

	/**
	 * Linear sRGB → OKLab via Björn Ottosson's M1/M2 matrices.
	 * Constants from CSS Color 4 §10.2 (the spec reproduces Ottosson's
	 * original derivation).
	 *
	 * @since 3.0.0
	 *
	 * @param array{r:float,g:float,b:float} $lin Linear sRGB 0..1.
	 *
	 * @return array{L:float,a:float,b:float} OKLab.
	 */
	private static function lin_srgb_to_oklab( array $lin ): array {
		// M1: linear sRGB → LMS.
		$l = 0.4122214708 * $lin['r'] + 0.5363325363 * $lin['g'] + 0.0514459929 * $lin['b'];
		$m = 0.2119034982 * $lin['r'] + 0.6806995451 * $lin['g'] + 0.1073969566 * $lin['b'];
		$s = 0.0883024619 * $lin['r'] + 0.2817188376 * $lin['g'] + 0.6299787005 * $lin['b'];

		// Cube root of each LMS component (Ottosson's nonlinearity).
		$l_ = self::cbrt( $l );
		$m_ = self::cbrt( $m );
		$s_ = self::cbrt( $s );

		// M2: LMS' → OKLab.
		return [
			'L' => 0.2104542553 * $l_ + 0.7936177850 * $m_ - 0.0040720468 * $s_,
			'a' => 1.9779984951 * $l_ - 2.4285922050 * $m_ + 0.4505937099 * $s_,
			'b' => 0.0259040371 * $l_ + 0.7827717662 * $m_ - 0.8086757660 * $s_,
		];
	}

	/**
	 * OKLab → linear sRGB. Inverse of `lin_srgb_to_oklab()`.
	 *
	 * @since 3.0.0
	 *
	 * @param array{L:float,a:float,b:float} $lab OKLab.
	 *
	 * @return array{r:float,g:float,b:float} Linear sRGB (may exceed 0..1
	 *                                        if out of gamut; gamut-map
	 *                                        before calling
	 *                                        `lin_srgb_to_srgb()`).
	 */
	private static function oklab_to_lin_srgb( array $lab ): array {
		// Inverse of M2.
		$l_ = $lab['L'] + 0.3963377774 * $lab['a'] + 0.2158037573 * $lab['b'];
		$m_ = $lab['L'] - 0.1055613458 * $lab['a'] - 0.0638541728 * $lab['b'];
		$s_ = $lab['L'] - 0.0894841775 * $lab['a'] - 1.2914855480 * $lab['b'];

		$l = $l_ ** 3;
		$m = $m_ ** 3;
		$s = $s_ ** 3;

		// Inverse of M1.
		return [
			'r' =>  4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
			'g' => -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
			'b' => -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
		];
	}

	/**
	 * OKLab → OKLCh (polar form).
	 *
	 * @since 3.0.0
	 *
	 * @param array{L:float,a:float,b:float} $lab OKLab.
	 *
	 * @return array{L:float,C:float,h:float} OKLCh with h in degrees [0, 360).
	 */
	private static function oklab_to_oklch( array $lab ): array {
		$c = hypot( $lab['a'], $lab['b'] );
		$h = atan2( $lab['b'], $lab['a'] ) * 180 / M_PI;
		if ( $h < 0 ) {
			$h += 360;
		}
		return [ 'L' => $lab['L'], 'C' => $c, 'h' => $h ];
	}

	/**
	 * OKLCh → OKLab.
	 *
	 * @since 3.0.0
	 *
	 * @param array{L:float,C:float,h:float} $lch OKLCh.
	 *
	 * @return array{L:float,a:float,b:float} OKLab.
	 */
	private static function oklch_to_oklab( array $lch ): array {
		$rad = $lch['h'] * M_PI / 180;
		return [
			'L' => $lch['L'],
			'a' => $lch['C'] * cos( $rad ),
			'b' => $lch['C'] * sin( $rad ),
		];
	}

	/**
	 * Cube root that handles negative inputs (PHP's `**` doesn't).
	 *
	 * @since 3.0.0
	 *
	 * @param float $x Input.
	 *
	 * @return float Cube root.
	 */
	private static function cbrt( float $x ): float {
		return $x < 0 ? -pow( -$x, 1 / 3 ) : pow( $x, 1 / 3 );
	}

	/**
	 * True when any channel of linear sRGB is outside [0, 1] (out of
	 * sRGB gamut). Allow a tiny epsilon for floating-point drift.
	 *
	 * @since 3.0.0
	 *
	 * @param array{r:float,g:float,b:float} $lin Linear sRGB.
	 *
	 * @return bool
	 */
	private static function is_out_of_gamut_srgb( array $lin ): bool {
		$eps = 1e-6;
		return $lin['r'] < -$eps || $lin['r'] > 1 + $eps
			|| $lin['g'] < -$eps || $lin['g'] > 1 + $eps
			|| $lin['b'] < -$eps || $lin['b'] > 1 + $eps;
	}

	/**
	 * Gamut-map an OKLCh color to sRGB via CSS Color 4 §13 binary
	 * search on chroma — keep L fixed, reduce C until the color sits
	 * inside the sRGB gamut.
	 *
	 * @since 3.0.0
	 *
	 * @param array{L:float,C:float,h:float} $lch OKLCh.
	 *
	 * @return array{L:float,C:float,h:float} Gamut-mapped OKLCh.
	 */
	private static function gamut_map_to_srgb( array $lch ): array {
		$min = 0.0;
		$max = $lch['C'];
		$tol = 1e-4;

		// 50 iterations is overkill for 1e-4 tolerance but safe.
		for ( $i = 0; $i < 50; $i++ ) {
			$mid = ( $min + $max ) / 2;
			$candidate_lab = self::oklch_to_oklab( [ 'L' => $lch['L'], 'C' => $mid, 'h' => $lch['h'] ] );
			$candidate_lin = self::oklab_to_lin_srgb( $candidate_lab );
			if ( self::is_out_of_gamut_srgb( $candidate_lin ) ) {
				$max = $mid;
			} else {
				$min = $mid;
			}
			if ( $max - $min < $tol ) {
				break;
			}
		}

		return [ 'L' => $lch['L'], 'C' => $min, 'h' => $lch['h'] ];
	}
}
