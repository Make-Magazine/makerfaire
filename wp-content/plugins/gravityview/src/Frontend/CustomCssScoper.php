<?php
/**
 * Flat per-selector scoper for customer Custom CSS.
 *
 * @package GravityKit\GravityView\Frontend
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Frontend;

defined( 'ABSPATH' ) || die();

/**
 * Scopes customer Custom CSS to a per-View selector without native CSS
 * nesting.
 *
 * Every selector in every rule gets the scope selector prefixed, so the
 * output parses on every browser the legacy emission path supported and
 * there is no wrapper block a stray closing brace could escape from.
 * Rules that already carry a resolved per-View selector pass through
 * untouched; conditional group at-rules (`@media`, `@supports`,
 * `@container`, `@layer`) recurse; other at-rules (`@keyframes`,
 * `@font-face`, `@import`) pass through unmodified.
 *
 * @since 3.0.0
 */
final class CustomCssScoper {

	/**
	 * Comment emitted when the input CSS has unbalanced braces and the
	 * scoper falls back to lenient best-effort parsing.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	const PARSE_WARNING = '/* gravityview: custom css could not be fully parsed; scoped as a whole */';

	/**
	 * Conditional group at-rules whose inner rules are scoped recursively.
	 *
	 * `@scope` is intentionally NOT supported here yet: it also nests style
	 * rules, but its `(root) to (limit)` / `:scope` semantics need dedicated
	 * handling. Until that exists, `@scope` blocks fall through to the
	 * non-recursive path and their inner selectors are emitted unscoped.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	private const RECURSIVE_AT_RULES = [ 'media', 'supports', 'container', 'layer' ];

	/**
	 * Scopes a CSS blob to the given selector.
	 *
	 * @since 3.0.0
	 *
	 * @param string $css                    The CSS to scope. Placeholders must already be resolved.
	 * @param string $scope_selector         Selector prefixed to every bare selector (e.g. `[id^="gv-view-7-"]`).
	 * @param array  $passthrough_selectors  Selector substrings that mark a rule as already scoped;
	 *                                       rules containing any of them are emitted as-is.
	 *
	 * @return string The scoped CSS. Empty string when the input contains nothing to emit.
	 */
	public static function scope( $css, $scope_selector, array $passthrough_selectors = [] ) {
		$css = (string) $css;

		if ( '' === trim( $css ) ) {
			return '';
		}

		$malformed = false;
		$output    = self::scope_block( $css, (string) $scope_selector, $passthrough_selectors, $malformed );

		if ( $malformed ) {
			$output = self::PARSE_WARNING . "\n" . $output;
		}

		return $output;
	}

	/**
	 * Parses a block of CSS into top-level statements and emits each one scoped.
	 *
	 * @since 3.0.0
	 *
	 * @param string $css       CSS source for one nesting level.
	 * @param string $scope     The scope selector.
	 * @param array  $needles   Pass-through selector substrings.
	 * @param bool   $malformed Set to true when the parser hits unbalanced braces or stray content.
	 *
	 * @return string Scoped CSS for this level.
	 */
	private static function scope_block( $css, $scope, array $needles, &$malformed ) {
		$out = [];

		foreach ( self::parse_statements( $css, $malformed ) as $statement ) {
			if ( 'statement' === $statement['type'] ) {
				// Block-less at-statements (`@import`, `@charset`) and any
				// other stray top-level text ending in a semicolon pass
				// through; the sanitizer is responsible for stripping
				// disallowed at-rules before scoping.
				$text = trim( $statement['text'] );
				if ( '' !== $text ) {
					$out[] = $text;
				}
				continue;
			}

			$body = $statement['body'];

			// Lift comments out of the selector prelude before any
			// classification. The pass-through check below must only see
			// real selector text: a needle that merely appears inside a
			// comment must not exempt the rule from scoping.
			$comments = [];
			$selector = (string) preg_replace_callback(
				'~/\*.*?\*/~s',
				static function ( $matches ) use ( &$comments ) {
					$comments[] = $matches[0];
					return ' ';
				},
				$statement['selector']
			);
			$selector = trim( $selector );

			if ( '' === $selector ) {
				// A body with no selector is not valid CSS; drop the rule
				// rather than guess at intent, but keep any comments.
				if ( ! empty( $comments ) ) {
					$out[] = implode( "\n", $comments );
				}
				$malformed = true;
				continue;
			}

			if ( '@' === $selector[0] ) {
				$at_name = strtolower( (string) preg_replace( '/^@(-[a-z]+-)?([a-zA-Z-]+).*$/s', '$2', $selector ) );

				if ( in_array( $at_name, self::RECURSIVE_AT_RULES, true ) ) {
					$inner = self::scope_block( $body, $scope, $needles, $malformed );
					$out[] = self::with_comments( $comments, $selector . " {\n" . $inner . "\n}" );
					continue;
				}

				// `@keyframes`, `@font-face`, `@page`, `@property`, ...:
				// their inner braces are not selector scopes.
				$out[] = self::with_comments( $comments, $selector . ' {' . $body . '}' );
				continue;
			}

			// Classify each selector in the list independently. An arm that
			// already carries a resolved per-View selector passes through
			// (re-prefixing would build a self-descendant chain that never
			// matches); every other arm gets the scope prefixed. Deciding
			// per arm is what stops a bare arm (`.x, .gv-container-7 .y`)
			// from leaking page-wide just because a sibling arm is scoped.
			$arms = [];
			foreach ( self::split_selector_list( $selector ) as $single ) {
				$single = trim( $single );
				if ( '' === $single ) {
					continue;
				}
				$arms[] = self::selector_contains( $single, $needles )
					? $single
					: $scope . ' ' . $single;
			}

			if ( empty( $arms ) ) {
				$malformed = true;
				continue;
			}

			$out[] = self::with_comments( $comments, implode( ', ', $arms ) . ' {' . $body . '}' );
		}

		return implode( "\n", $out );
	}

	/**
	 * Tokenizes CSS into top-level statements by brace counting.
	 *
	 * Quoted strings and comments are skipped so braces inside them do not
	 * affect nesting depth. A stray closing brace at the top level and an
	 * unclosed block both flag the input as malformed; parsing continues
	 * leniently so the remaining rules still get scoped.
	 *
	 * @since 3.0.0
	 *
	 * @param string $css       CSS source for one nesting level.
	 * @param bool   $malformed Set to true on unbalanced braces or trailing junk.
	 *
	 * @return array[] List of statements: `[ 'type' => 'rule', 'selector' => string, 'body' => string ]`
	 *                 or `[ 'type' => 'statement', 'text' => string ]`.
	 */
	private static function parse_statements( $css, &$malformed ) {
		$statements   = [];
		$length       = strlen( $css );
		$i            = 0;
		$buffer_start = 0;
		$depth        = 0;
		$selector     = '';
		$body_start   = 0;

		while ( $i < $length ) {
			$char = $css[ $i ];

			// A backslash escapes the next byte everywhere in CSS, so an
			// escaped quote or brace is identifier content, never syntax.
			if ( '\\' === $char ) {
				$i += 2;
				continue;
			}

			if ( '/' === $char && $i + 1 < $length && '*' === $css[ $i + 1 ] ) {
				$end = strpos( $css, '*/', $i + 2 );
				$i   = ( false === $end ) ? $length : $end + 2;
				continue;
			}

			if ( '"' === $char || "'" === $char ) {
				$i = self::skip_string( $css, $i );
				continue;
			}

			if ( '{' === $char ) {
				if ( 0 === $depth ) {
					$selector   = substr( $css, $buffer_start, $i - $buffer_start );
					$body_start = $i + 1;
				}
				++$depth;
				++$i;
				continue;
			}

			if ( '}' === $char ) {
				if ( 0 === $depth ) {
					// Stray closing brace: flag it and keep going so the
					// rules after it still get scoped.
					$malformed    = true;
					$buffer_start = $i + 1;
					++$i;
					continue;
				}

				--$depth;

				if ( 0 === $depth ) {
					$statements[] = [
						'type'     => 'rule',
						'selector' => $selector,
						'body'     => substr( $css, $body_start, $i - $body_start ),
					];
					$buffer_start = $i + 1;
				}
				++$i;
				continue;
			}

			if ( ';' === $char && 0 === $depth ) {
				$statements[] = [
					'type' => 'statement',
					'text' => substr( $css, $buffer_start, $i - $buffer_start + 1 ),
				];
				$buffer_start = $i + 1;
				++$i;
				continue;
			}

			++$i;
		}

		if ( $depth > 0 ) {
			// Unclosed block: close it implicitly and flag the input.
			$malformed    = true;
			$statements[] = [
				'type'     => 'rule',
				'selector' => $selector,
				'body'     => substr( $css, $body_start ),
			];

			return $statements;
		}

		$trailing = substr( $css, $buffer_start );
		if ( '' !== trim( $trailing ) ) {
			$without_comments = (string) preg_replace( '~/\*.*?\*/~s', '', $trailing );

			if ( '' === trim( $without_comments ) ) {
				// Trailing comment only; keep it.
				$statements[] = [
					'type' => 'statement',
					'text' => trim( $trailing ),
				];
			} else {
				// Trailing prelude that never opened a block.
				$malformed = true;
			}
		}

		return $statements;
	}

	/**
	 * Splits a selector list on top-level commas.
	 *
	 * Commas inside parentheses (`:is(.a, .b)`), square brackets, and
	 * quoted strings do not split.
	 *
	 * @since 3.0.0
	 *
	 * @param string $selector The full selector prelude.
	 *
	 * @return string[] Individual selectors.
	 */
	private static function split_selector_list( $selector ) {
		$parts  = [];
		$length = strlen( $selector );
		$i      = 0;
		$start  = 0;
		$depth  = 0;

		while ( $i < $length ) {
			$char = $selector[ $i ];

			// An escaped byte is identifier content; an escaped comma must
			// not split the list, an escaped quote must not open a string.
			if ( '\\' === $char ) {
				$i += 2;
				continue;
			}

			if ( '"' === $char || "'" === $char ) {
				$i = self::skip_string( $selector, $i );
				continue;
			}

			if ( '(' === $char || '[' === $char ) {
				++$depth;
			} elseif ( ')' === $char || ']' === $char ) {
				$depth = max( 0, $depth - 1 );
			} elseif ( ',' === $char && 0 === $depth ) {
				$parts[] = substr( $selector, $start, $i - $start );
				$start   = $i + 1;
			}

			++$i;
		}

		$parts[] = substr( $selector, $start );

		return $parts;
	}

	/**
	 * Re-attaches lifted comments above an emitted rule.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $comments Comments lifted from the rule's prelude.
	 * @param string $rule     The emitted rule text.
	 *
	 * @return string
	 */
	private static function with_comments( array $comments, $rule ) {
		if ( empty( $comments ) ) {
			return $rule;
		}

		return implode( "\n", $comments ) . "\n" . $rule;
	}

	/**
	 * Whether a selector is already scoped by one of the pass-through needles.
	 *
	 * The selector must already have comments lifted. A needle counts only when
	 * it begins at the top level of the selector — outside any quoted string,
	 * functional pseudo-class (`:not()`, `:is()`, `:where()`, `:has()`), or
	 * attribute `[...]`. A needle nested inside those does not constrain the
	 * matched subject to the View scope (`:not([id^="gv-view-7-"]) body` matches
	 * the whole page), so it must not exempt the arm from scoping.
	 *
	 * @since 3.0.0
	 *
	 * @param string $selector The comment-free selector prelude.
	 * @param array  $needles  Selector substrings that mark a rule as already scoped.
	 *
	 * @return bool
	 */
	private static function selector_contains( $selector, array $needles ) {
		$needles = array_filter(
			array_map( 'strval', $needles ),
			static function ( $needle ) {
				return '' !== $needle;
			}
		);

		if ( empty( $needles ) ) {
			return false;
		}

		$length = strlen( $selector );
		$depth  = 0;
		$i      = 0;

		while ( $i < $length ) {
			// Only a needle that begins at depth 0 marks the arm as already
			// scoped. Checked before the bracket below is counted, so a needle
			// that itself opens with `[` (the wrapper arm) still matches.
			if ( 0 === $depth ) {
				foreach ( $needles as $needle ) {
					if ( $needle === substr( $selector, $i, strlen( $needle ) ) ) {
						return true;
					}
				}
			}

			$char = $selector[ $i ];

			// A backslash escapes the next byte; a quote opens a string whose
			// contents are not selector syntax. Both mirror the tokenizer so a
			// needle hidden in a string or behind an escape cannot count.
			if ( '\\' === $char ) {
				$i += 2;
				continue;
			}

			if ( '"' === $char || "'" === $char ) {
				$i = self::skip_string( $selector, $i );
				continue;
			}

			if ( '(' === $char || '[' === $char ) {
				++$depth;
			} elseif ( ')' === $char || ']' === $char ) {
				$depth = max( 0, $depth - 1 );
			}

			++$i;
		}

		return false;
	}

	/**
	 * Returns the index just past a quoted string starting at `$i`.
	 *
	 * Honors backslash escapes. An unterminated string consumes the rest
	 * of the input, which mirrors how browsers error-recover.
	 *
	 * @since 3.0.0
	 *
	 * @param string $css The CSS source.
	 * @param int    $i   Index of the opening quote.
	 *
	 * @return int Index just past the closing quote (or end of input).
	 */
	private static function skip_string( $css, $i ) {
		$quote  = $css[ $i ];
		$length = strlen( $css );
		++$i;

		while ( $i < $length ) {
			if ( '\\' === $css[ $i ] ) {
				$i += 2;
				continue;
			}
			if ( $css[ $i ] === $quote ) {
				return $i + 1;
			}
			++$i;
		}

		return $length;
	}
}
