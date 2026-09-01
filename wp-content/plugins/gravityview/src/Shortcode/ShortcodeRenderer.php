<?php
/**
 * Shortcode Renderer - Universal shortcode building and rendering utilities.
 *
 * This class centralizes shortcode generation and rendering for all page builder
 * integrations (Gutenberg, Divi, Elementor, Beaver Builder) to ensure consistency.
 *
 * @package GravityKit\GravityView\Shortcodes
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Shortcode;

use GravityKit\GravityView\Foundation\Helpers\Arr;
use GV\View;
use GV\View_Settings;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Universal shortcode building and rendering utilities.
 *
 * Provides two approaches to shortcode building:
 * - Simple: `build_from_block_atts()` - For basic page builders with limited settings
 * - Complete: `build_from_view_settings()` - For full-featured integrations reading from View_Settings
 *
 * @since 3.0.0
 */
class ShortcodeRenderer {

	/**
	 * Scripts and styles to ignore when filtering assets.
	 *
	 * These patterns identify assets from other plugins that may cause conflicts
	 * when rendering Views in page builder contexts.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	const IGNORE_SCRIPTS_AND_STYLES = [ 'jetpack', 'elementor', 'yoast' ];

	/**
	 * Default allowlist patterns for GravityView assets.
	 *
	 * These patterns identify GravityView core and extension stylesheets/scripts
	 * by their registered handle names.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	const ALLOWLIST_HANDLE_PATTERNS = [
		'gravityview',
		'gv-',
		'gv_',
		'gk-',
		'gk_',
	];

	/**
	 * Script handles to skip in builder previews.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	const BUILDER_IGNORED_SCRIPT_HANDLES = [
		'jquery',
		'jquery-core',
		'jquery-migrate',
	];

	/**
	 * Map of block attribute names (camelCase) to shortcode attribute names (snake_case).
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	const BLOCK_TO_SHORTCODE_MAP = [
		'viewId'         => 'id',
		'postId'         => 'post_id',
		'secret'         => 'secret',
		'pageSize'       => 'page_size',
		'sortField'      => 'sort_field',
		'sortDirection'  => 'sort_direction',
		'searchField'    => 'search_field',
		'searchValue'    => 'search_value',
		'searchOperator' => 'search_operator',
		'startDate'      => 'start_date',
		'endDate'        => 'end_date',
		'classValue'     => 'class',
		'offset'         => 'offset',
		'singleTitle'    => 'single_title',
		'backLinkLabel'  => 'back_link_label',
	];

	/**
	 * Renders a placeholder message for page builder editor contexts.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message The message to display.
	 *
	 * @return string HTML placeholder div.
	 */
	public static function render_placeholder( $message ) {
		return sprintf(
			'<div style="text-align:center; padding:20px; border:1px dashed #ccc; background:#f9f9f9;">%s</div>',
			esc_html( $message )
		);
	}

	/**
	 * Shared inline style for preview-fallback notices.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	const PREVIEW_NOTICE_STYLE = 'text-align:center; padding:20px; color:#666; font-style:italic;';

	/**
	 * Replace layouts that can't render in builder previews with a notice.
	 *
	 * Each fallback handler receives the full rendered array (content + scripts
	 * + styles) so it can swap the markup AND strip the layout's init scripts.
	 * Leaving those scripts in the payload would let the asset loader run them
	 * against the stubbed markup — e.g. DataTables retrying init against an
	 * empty table, causing duplicate-initialization attempts.
	 *
	 * Mirrors `src/PageBuilder/shared/js/preview-fallbacks.js` for builders that
	 * don't run the shared JS module on rendered output (e.g. Beaver Builder,
	 * which injects module HTML directly into the live DOM rather than through
	 * a React preview layer).
	 *
	 * @since 3.0.0
	 *
	 * @param array $rendered Render result: `content`, `scripts`, `styles`.
	 * @param array $options  Optional overrides:
	 *                        - `datatables_notice` string
	 *                        - `maps_notice`       string
	 *
	 * @return array Modified render result.
	 */
	public static function apply_preview_fallbacks( array $rendered, array $options = [] ) {
		$html = $rendered['content'] ?? '';

		if ( empty( $html ) ) {
			return $rendered;
		}

		if ( false !== strpos( $html, 'gv-datatables' ) ) {
			$rendered = self::apply_datatables_fallback( $rendered, $options['datatables_notice'] ?? '' );
		}

		if ( false !== strpos( $html, 'gv-map-container' ) ) {
			$rendered = self::apply_maps_fallback( $rendered, $options['maps_notice'] ?? '' );
		}

		return $rendered;
	}

	/**
	 * Replace `<table class="gv-datatables">` bodies with a preview notice row
	 * and strip the layout's init scripts from the rendered assets.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $rendered Render result.
	 * @param string $notice   Optional override notice text.
	 *
	 * @return array Modified render result.
	 */
	private static function apply_datatables_fallback( array $rendered, $notice = '' ) {
		if ( '' === $notice ) {
			$notice = __( 'Entries from the DataTables layout are not available in this preview.', 'gk-gravityview' );
		}

		$rendered['content'] = preg_replace_callback(
			'#(<table[^>]*\bclass="[^"]*\bgv-datatables\b[^"]*"[^>]*>)(.*?)(</table>)#is',
			function ( $matches ) use ( $notice ) {
				preg_match_all( '#<th[\s>]#i', $matches[2], $th_matches );
				$colspan = max( count( $th_matches[0] ), 1 );

				$thead = '';
				if ( preg_match( '#<thead\b.*?</thead>#is', $matches[2], $thead_match ) ) {
					$thead = $thead_match[0];
				}

				$tbody = sprintf(
					'<tbody><tr><td colspan="%d" style="%s">%s</td></tr></tbody>',
					$colspan,
					esc_attr( self::PREVIEW_NOTICE_STYLE ),
					esc_html( $notice )
				);

				return $matches[1] . $thead . $tbody . $matches[3];
			},
			$rendered['content']
		);

		$rendered['scripts'] = self::filter_script_assets(
			$rendered['scripts'] ?? [],
			[ 'gv-datatables', 'gv-dt-', 'datatables-views', 'dataTables' ],
			[ 'DataTable(', 'dataTables_' ]
		);

		// Builders like Beaver Builder render into the live DOM, so anything
		// enqueued during `do_shortcode()` still gets printed by `wp_footer`
		// — running DataTables init against our stubbed <table> regardless of
		// what we put in the preview asset payload. Dequeue the actual handles
		// so WP doesn't print the `<script>` tags in the first place.
		self::dequeue_assets_by_src( [ 'gv-datatables', 'gv-dt-', 'datatables-views', 'datatables.css' ] );

		return $rendered;
	}

	/**
	 * Replace `.gv-map-canvas` contents with a preview notice and strip the
	 * layout's init scripts from the rendered assets.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $rendered Render result.
	 * @param string $notice   Optional override notice text.
	 *
	 * @return array Modified render result.
	 */
	private static function apply_maps_fallback( array $rendered, $notice = '' ) {
		if ( '' === $notice ) {
			$notice = __( 'Map is not available in this preview.', 'gk-gravityview' );
		}

		$rendered['content'] = preg_replace_callback(
			'#(<div[^>]*\bclass="[^"]*\bgv-map-canvas\b[^"]*"[^>]*>)(.*?)(</div>)#is',
			function ( $matches ) use ( $notice ) {
				return $matches[1] . sprintf(
					'<p style="%s">%s</p>',
					esc_attr( self::PREVIEW_NOTICE_STYLE ),
					esc_html( $notice )
				) . $matches[3];
			},
			$rendered['content']
		);

		$rendered['scripts'] = self::filter_script_assets(
			$rendered['scripts'] ?? [],
			[ 'gv-maps', 'gravityview-maps', 'google-maps' ],
			[ 'new google.maps.', 'gvMapsInit' ]
		);

		self::dequeue_assets_by_src( [ 'gv-maps', 'gravityview-maps', 'google-maps', 'maps.googleapis.com' ] );

		return $rendered;
	}

	/**
	 * Dequeue any enqueued scripts/styles whose `src` matches one of the needles,
	 * so wp_footer/wp_head don't print `<script>`/`<link>` tags that would
	 * re-initialize layouts we've already replaced with a preview stub.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $src_needles Substrings to match against registered script/style URLs.
	 *
	 * @return void
	 */
	private static function dequeue_assets_by_src( array $src_needles ) {
		$matches = static function ( $src, array $needles ) {
			if ( ! is_string( $src ) || '' === $src ) {
				return false;
			}
			foreach ( $needles as $needle ) {
				if ( '' !== $needle && false !== strpos( $src, $needle ) ) {
					return true;
				}
			}
			return false;
		};

		global $wp_scripts, $wp_styles;

		foreach ( [ $wp_scripts, $wp_styles ] as $source ) {
			if ( ! $source || empty( $source->registered ) ) {
				continue;
			}
			foreach ( $source->registered as $handle => $dep ) {
				if ( $matches( $dep->src ?? '', $src_needles ) ) {
					if ( $source === $wp_scripts ) {
						wp_dequeue_script( $handle );
					} else {
						wp_dequeue_style( $handle );
					}
				}
			}
		}
	}

	/**
	 * Drop entries from the collected scripts array whose `src` matches any
	 * of `$src_needles` or whose inline `data`/`before`/`after` content
	 * matches any of `$inline_needles`.
	 *
	 * @since 3.0.0
	 *
	 * @param array    $scripts        Script entries as produced by render().
	 * @param string[] $src_needles    Substrings that identify the library by URL.
	 * @param string[] $inline_needles Substrings that identify the init snippet.
	 *
	 * @return array Filtered script entries.
	 */
	private static function filter_script_assets( array $scripts, array $src_needles, array $inline_needles ) {
		$matches = static function ( $haystack, array $needles ) {
			if ( ! is_string( $haystack ) || '' === $haystack ) {
				return false;
			}
			foreach ( $needles as $needle ) {
				if ( '' !== $needle && false !== strpos( $haystack, $needle ) ) {
					return true;
				}
			}
			return false;
		};

		return array_values( array_filter(
			$scripts,
			static function ( $entry ) use ( $matches, $src_needles, $inline_needles ) {
				if ( is_string( $entry ) ) {
					return ! $matches( $entry, $src_needles );
				}

				if ( is_array( $entry ) ) {
					if ( $matches( $entry['src'] ?? '', $src_needles ) ) {
						return false;
					}
					foreach ( [ 'data', 'before', 'after' ] as $key ) {
						if ( $matches( $entry[ $key ] ?? '', $inline_needles ) ) {
							return false;
						}
					}
				}

				return true;
			}
		) );
	}

	/**
	 * Render a JavaScript asset loader for builder/editor previews.
	 *
	 * @since 3.0.0
	 *
	 * @param array $assets Rendered assets array with scripts/styles.
	 *
	 * @return string Script tag markup or empty string.
	 */
	public static function render_asset_loader( $assets ) {
		$styles  = isset( $assets['styles'] ) ? array_values( $assets['styles'] ) : [];
		$scripts = isset( $assets['scripts'] ) ? array_values( $assets['scripts'] ) : [];

		if ( empty( $styles ) && empty( $scripts ) ) {
			return '';
		}

		$payload = wp_json_encode( [
			'styles'  => $styles,
			'scripts' => $scripts,
		] );

		if ( false === $payload ) {
			return '';
		}

		$asset_path = GRAVITYVIEW_DIR . 'src/PageBuilder/assets/gv-builder-asset-loader.js';

		wp_enqueue_script(
			'gk-gravityview-builder-assets',
			plugins_url( 'src/PageBuilder/assets/gv-builder-asset-loader.js', GRAVITYVIEW_FILE ),
			[],
			is_readable( $asset_path ) ? filemtime( $asset_path ) : GV_PLUGIN_VERSION,
			true
		);

		return sprintf(
			'<div class="gk-gravityview-asset-payload" data-gv-assets="%s"></div>',
			esc_attr( $payload )
		);
	}

	/**
	 * Formats an attributes array into a shortcode attributes string.
	 *
	 * Handles both scalar and array values. Array values are formatted with
	 * numbered suffixes (e.g., sort_direction, sort_direction_2, sort_direction_3).
	 *
	 * @since 3.0.0
	 *
	 * @param array $atts Attributes to format (key => value pairs).
	 *
	 * @return string Formatted attributes string (e.g., 'key1="value1" key2="value2"').
	 */
	public static function format_atts_string( $atts ) {
		$parts = [];

		foreach ( $atts as $key => $value ) {
			if ( is_array( $value ) ) {
				// Handle array values (e.g., multi-sort: sort_direction, sort_direction_2).
				foreach ( $value as $index => $item ) {
					$suffix  = 0 === $index ? '' : '_' . ( $index + 1 );
					$escaped = esc_attr( self::strip_shortcode_delimiters( $item ) );
					$parts[] = sprintf( '%s%s="%s"', $key, $suffix, $escaped );
				}
			} else {
				$escaped = esc_attr( self::strip_shortcode_delimiters( $value ) );
				$parts[] = sprintf( '%s="%s"', $key, $escaped );
			}
		}

		return implode( ' ', $parts );
	}

	/**
	 * Strips square-bracket shortcode delimiters from a value bound for a
	 * shortcode-attribute string.
	 *
	 * esc_attr(), wp_strip_all_tags(), and wp_kses_post() all leave `[` and `]`
	 * intact, so a builder attribute carrying them can prematurely close the
	 * shortcode and smuggle a second one into do_shortcode(). Brackets are not
	 * valid inside a shortcode attribute value, so removing them is lossless for
	 * legitimate input while closing the injection. Applied on top of (not in
	 * place of) the existing esc_attr()/sanitisation.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Attribute value (cast to string).
	 *
	 * @return string The value with `[` and `]` removed.
	 */
	private static function strip_shortcode_delimiters( $value ) {
		return str_replace( [ '[', ']' ], '', (string) $value );
	}

	/**
	 * Converts block attributes array to shortcode attributes array.
	 *
	 * This is useful for page builders that use camelCase attribute names
	 * (like Gutenberg blocks) and need to convert to snake_case shortcode attributes.
	 *
	 * @since 3.0.0
	 *
	 * @param array $block_attributes Block attributes array (camelCase keys).
	 *
	 * @return array Shortcode attributes array (snake_case keys).
	 */
	public static function map_block_atts_to_shortcode_atts( $block_attributes = [] ) {
		$shortcode_attributes = [];

		// Remove searchOperator if searchValue is empty.
		if ( isset( $block_attributes['searchOperator'] ) && ( ! isset( $block_attributes['searchValue'] ) || '' === trim( (string) $block_attributes['searchValue'] ) ) ) {
			unset( $block_attributes['searchOperator'] );
		}

		foreach ( $block_attributes as $attribute => $value ) {
			if ( ! isset( self::BLOCK_TO_SHORTCODE_MAP[ $attribute ] ) ) {
				continue;
			}

			if ( '' === $value ) {
				continue;
			}

			$shortcode_attributes[ self::BLOCK_TO_SHORTCODE_MAP[ $attribute ] ] = $value;
		}

		return $shortcode_attributes;
	}

	/**
	 * Build a [gravityview] shortcode string from block-style attributes.
	 *
	 * This is the SIMPLE approach for page builders with limited UI controls.
	 * For full-featured integrations, use `build_from_view_settings()` instead.
	 *
	 * @since 3.0.0
	 *
	 * @param array       $block_atts Block-style attributes (camelCase: viewId, pageSize, etc.).
	 * @param string|null $secret     Optional. View validation secret. If provided, added to shortcode.
	 *
	 * @return string The formatted [gravityview ...] shortcode string.
	 */
	public static function build_from_block_atts(
		$block_atts,
		#[\SensitiveParameter]
		$secret = null
	) {
		$shortcode_atts = self::map_block_atts_to_shortcode_atts( $block_atts );

		if ( $secret ) {
			$shortcode_atts['secret'] = $secret;
		}

		return sprintf( '[gravityview %s]', self::format_atts_string( $shortcode_atts ) );
	}

	/**
	 * Build a [gravityview] shortcode string from View settings.
	 *
	 * This is the COMPLETE approach that reads from View_Settings::defaults()
	 * and includes ALL settings with `show_in_shortcode=true`. This approach
	 * is used by Advanced Elementor Widget and other full-featured integrations.
	 *
	 * Supports array values for multi-sort (e.g., sort_direction[0], sort_direction_2).
	 *
	 * @since 3.0.0
	 *
	 * @param array  $settings Widget/module settings array (snake_case keys matching View_Settings).
	 * @param View   $view     View object.
	 * @param string $secret   Optional. View validation secret. Auto-retrieved from View if not provided.
	 *
	 * @return string The formatted [gravityview ...] shortcode string.
	 */
	public static function build_from_view_settings(
		$settings,
		$view,
		#[\SensitiveParameter]
		$secret = null
	) {
		$atts = self::convert_settings_to_shortcode_atts( $settings );

		// Add secret if provided or get from view.
		$secret = $secret ?? $view->get_validation_secret();
		if ( $secret ) {
			$atts['secret'] = $secret;
		}

		// Add view ID.
		$atts['id'] = $view->ID;

		// Reverse to put `id` first, `secret` second for readability.
		$atts = array_reverse( $atts, true );

		return sprintf( '[gravityview %s]', self::format_atts_string( $atts ) );
	}

	/**
	 * Convert widget/module settings to shortcode attributes.
	 *
	 * Reads from View_Settings::defaults() and only includes settings
	 * where `show_in_shortcode=true` and value differs from default.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings Widget/module settings.
	 *
	 * @return array Shortcode attributes.
	 */
	public static function convert_settings_to_shortcode_atts( $settings ) {
		$defaults = View_Settings::defaults( true );
		$atts     = [];

		foreach ( $defaults as $key => $view_setting ) {
			// Only render settings that are shown in the shortcode.
			if ( empty( $view_setting['show_in_shortcode'] ) ) {
				continue;
			}

			// Get the passed value, falling back to default if empty.
			$passed_value = ( ! isset( $settings[ $key ] ) || null === $settings[ $key ] || '' === $settings[ $key ] )
				? $view_setting['value']
				: $settings[ $key ];

			// Convert based on type.
			switch ( $view_setting['type'] ) {
				case 'number':
					$converted_value = (int) $passed_value;
					break;

				case 'checkbox':
					// Handle various truthy representations (Elementor uses 'yes').
					$converted_value = in_array( $passed_value, [ 1, '1', true, 'yes' ], true ) ? 1 : 0;
					break;

				default:
					$converted_value = $passed_value;
					break;
			}

			// Only add if different from default value (type-flexible comparison).
			// For checkbox fields, normalize both sides to int before comparing.
			$default_value = $view_setting['value'];
			if ( 'checkbox' === $view_setting['type'] ) {
				$default_value = in_array( $default_value, [ 1, '1', true, 'yes' ], true ) ? 1 : 0;
			}

			if ( is_array( $converted_value ) || is_array( $default_value ) ) {
				if ( wp_json_encode( $converted_value ) === wp_json_encode( $default_value ) ) {
					continue;
				}
			} elseif ( (string) $converted_value === (string) $default_value ) {
				continue;
			}

			$atts[ $key ] = $converted_value;
		}

		return $atts;
	}

	/**
	 * Filters asset handles based on patterns.
	 *
	 * This method filters WordPress script/style handles using pattern matching.
	 * It can operate in two modes:
	 * - 'blocklist': Remove handles matching any pattern (default for backward compatibility)
	 * - 'allowlist': Keep only handles matching at least one pattern
	 *
	 * Filtering happens on handles BEFORE dependency resolution, which prevents
	 * unrelated dependencies from being included in the final output.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $handles  Array of asset handles (slugs) to filter.
	 * @param array  $patterns Array of patterns to match against handles.
	 * @param string $mode     Filter mode: 'allowlist' or 'blocklist'. Default 'blocklist'.
	 *
	 * @return array Filtered array of handles.
	 */
	public static function filter_asset_handles( $handles, $patterns = [], $mode = 'blocklist' ) {
		if ( empty( $handles ) ) {
			return $handles;
		}

		if ( empty( $patterns ) ) {
			return 'allowlist' === $mode ? [] : $handles;
		}

		// Pass the delimiter '/' to preg_quote to properly escape it within patterns.
		$pattern_regex = '/(' . implode( '|', array_map( function( $p ) { return preg_quote( $p, '/' ); }, $patterns ) ) . ')/i';

		if ( 'allowlist' === $mode ) {
			// Keep only handles that match at least one pattern.
			return array_values( preg_grep( $pattern_regex, $handles ) );
		}

		// Blocklist mode: remove handles that match any pattern.
		return array_values( array_diff( $handles, preg_grep( $pattern_regex, $handles ) ) );
	}

	/**
	 * Renders shortcode and returns rendered content along with newly enqueued scripts and styles.
	 *
	 * This is the universal rendering method used by all page builder integrations.
	 *
	 * @since 3.0.0
	 *
	 * @param string $shortcode The shortcode to render.
	 * @param array  $options   {
	 *     Optional. Configuration options.
	 *
	 *     @type array $allowed_style_patterns  Patterns to allowlist for styles. If provided, only
	 *                                          styles with handles matching these patterns are returned.
	 *     @type array $allowed_script_patterns Patterns to allowlist for scripts. If provided, only
	 *                                          scripts with handles matching these patterns are returned.
	 * }
	 *
	 * @return array{content: string, scripts: array, styles: array}
	 */
	public static function render( $shortcode, $options = [] ) {
		global $wp_scripts, $wp_styles;

		// Ensure WordPress script/style systems are initialized.
		if ( ! $wp_scripts instanceof \WP_Scripts ) {
			$wp_scripts = wp_scripts();
		}
		if ( ! $wp_styles instanceof \WP_Styles ) {
			$wp_styles = wp_styles();
		}

		$scripts_before_shortcode = $wp_scripts->queue;
		$styles_before_shortcode  = $wp_styles->queue;

		// Use output buffering to capture any stray output from the shortcode or enqueue hooks.
		// In REST block-renderer requests, plugins that echo resource hints during wp_enqueue_scripts
		// would otherwise leak HTML into the response body before the JSON payload.
		ob_start();
		$rendered_shortcode = do_shortcode( $shortcode );

		if ( 0 === did_action( 'wp_enqueue_scripts' ) ) {
			do_action( 'wp_enqueue_scripts' );
		}
		ob_end_clean();

		$gravityview_frontend = \GravityView_frontend::getInstance();
		$view_data            = \GravityView_View_Data::getInstance();
		$view_ids             = $view_data->maybe_get_view_id( $shortcode );

		// Save singleton state so we can restore it after rendering.
		$previous_output_data   = $gravityview_frontend->getGvOutputData();
		$previous_post_id       = $gravityview_frontend->getPostId();
		$previous_has_shortcode = $gravityview_frontend->isPostHasShortcode();

		try {
			$gravityview_frontend->setGvOutputData( $view_data );

			if ( empty( $gravityview_frontend->getPostId() ) ) {
				// First, check for a post_id in the shortcode attributes.
				$shortcode_atts = shortcode_parse_atts( $shortcode );
				$post_id        = 0;

				if ( ! empty( $shortcode_atts['post_id'] ) ) {
					$post_id = (int) $shortcode_atts['post_id'];
				} elseif ( ! empty( $shortcode_atts['postId'] ) ) {
					$post_id = (int) $shortcode_atts['postId'];
				}

				// Fall back to POST data, global $post, or View IDs.
				if ( ! $post_id ) {
					$post_id = \GV\Utils::_POST( 'post_id', 0 );
				}

				if ( ! $post_id ) {
					$post_id = \GV\Utils::_POST( 'postId', 0 );
				}

				if ( ! $post_id && isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof \WP_Post ) {
					$post_id = $GLOBALS['post']->ID;
				}

				if ( ! $post_id && $view_ids ) {
					$post_id = is_array( $view_ids ) ? reset( $view_ids ) : $view_ids;
				}

				if ( $post_id ) {
					$gravityview_frontend->setPostId( (int) $post_id );
					$gravityview_frontend->setPostHasShortcode( true );
				}
			}

			$gravityview_frontend->add_scripts_and_styles();

			if ( wp_script_is( 'gravityview-fe-view', 'registered' ) ) {
				$views_data = $gravityview_frontend->getGvOutputData();
				$views      = $views_data ? $views_data->get_views() : [];

				$js_localization = [
					'cookiepath' => COOKIEPATH,
					'clear'      => _x( 'Clear', 'Clear all data from the form', 'gk-gravityview' ),
					'reset'      => _x( 'Reset', 'Reset the search form to the state that existed on page load', 'gk-gravityview' ),
				];

				/**
				 * Modify the gvGlobals data used by front-end scripts.
				 *
				 * @param array $js_localization The data passed to the Javascript file.
				 * @param array $views Array of View data arrays with View settings.
				 */
				$js_localization = apply_filters( 'gravityview_js_localization', $js_localization, $views );

				wp_localize_script( 'gravityview-fe-view', 'gvGlobals', $js_localization );
			}

			$scripts_after_shortcode = $wp_scripts->queue;
			$styles_after_shortcode  = $wp_styles->queue;

			$newly_enqueued_scripts = array_diff( $scripts_after_shortcode, $scripts_before_shortcode );
			$newly_enqueued_styles  = array_diff( $styles_after_shortcode, $styles_before_shortcode );

			// First, apply blocklist to remove scripts/styles that may cause conflicts.
			$newly_enqueued_scripts = self::filter_asset_handles(
				$newly_enqueued_scripts,
				self::IGNORE_SCRIPTS_AND_STYLES,
				'blocklist'
			);
			$newly_enqueued_styles = self::filter_asset_handles(
				$newly_enqueued_styles,
				self::IGNORE_SCRIPTS_AND_STYLES,
				'blocklist'
			);

			// Then, apply allowlist if patterns are provided to further filter assets.
			if ( ! empty( $options['allowed_script_patterns'] ) ) {
				$newly_enqueued_scripts = self::filter_asset_handles(
					$newly_enqueued_scripts,
					$options['allowed_script_patterns'],
					'allowlist'
				);
			}

			if ( ! empty( $options['allowed_style_patterns'] ) ) {
				$newly_enqueued_styles = self::filter_asset_handles(
					$newly_enqueued_styles,
					$options['allowed_style_patterns'],
					'allowlist'
				);
			}

			$ignored_script_handles = isset( $options['ignored_script_handles'] ) && is_array( $options['ignored_script_handles'] )
				? $options['ignored_script_handles']
				: [];

			// This will return an array of all dependencies sorted in the order they should be loaded.
			$visited          = [];
			$get_dependencies = function ( $handle, $source, $dependencies = [] ) use ( &$get_dependencies, $ignored_script_handles, &$visited ) {
				if ( in_array( $handle, $ignored_script_handles, true ) ) {
					return $dependencies;
				}

				if ( in_array( $handle, $visited, true ) ) {
					return $dependencies;
				}
				$visited[] = $handle;

				if ( empty( $source->registered[ $handle ] ) ) {
					return $dependencies;
				}

				// Check for inline content (scripts use 'data', styles use 'before'/'after').
				$has_inline_data = $source->registered[ $handle ]->extra && (
					! empty( $source->registered[ $handle ]->extra['data'] ) ||
					! empty( $source->registered[ $handle ]->extra['before'] ) ||
					! empty( $source->registered[ $handle ]->extra['after'] )
				);

				if ( $has_inline_data ) {
					$inline_content = [];

					// For scripts: use 'data' (localized scripts, inline scripts).
					if ( ! empty( $source->registered[ $handle ]->extra['data'] ) ) {
						$inline_content['data'] = $source->registered[ $handle ]->extra['data'];
					}

					// For styles: use 'before' and 'after' (wp_add_inline_style).
					if ( ! empty( $source->registered[ $handle ]->extra['before'] ) ) {
						$inline_content['before'] = is_array( $source->registered[ $handle ]->extra['before'] )
							? implode( "\n", $source->registered[ $handle ]->extra['before'] )
							: $source->registered[ $handle ]->extra['before'];
					}

					if ( ! empty( $source->registered[ $handle ]->extra['after'] ) ) {
						$inline_content['after'] = is_array( $source->registered[ $handle ]->extra['after'] )
							? implode( "\n", $source->registered[ $handle ]->extra['after'] )
							: $source->registered[ $handle ]->extra['after'];
					}

					array_unshift(
						$dependencies,
						array_filter(
							array_merge(
								[ 'src' => $source->registered[ $handle ]->src ],
								$inline_content
							)
						)
					);
				} elseif ( $source->registered[ $handle ]->src ) {
					array_unshift( $dependencies, $source->registered[ $handle ]->src );
				}

				if ( ! $source->registered[ $handle ]->deps ) {
					return $dependencies;
				}

				foreach ( $source->registered[ $handle ]->deps as $dependency ) {
					array_unshift( $dependencies, $get_dependencies( $dependency, $source ) );
				}

				return Arr::flatten( $dependencies );
			};

			$script_dependencies = [];
			foreach ( $newly_enqueued_scripts as $script ) {
				$script_dependencies = array_merge( $script_dependencies, $get_dependencies( $script, $wp_scripts ) );
			}

			$visited            = [];
			$style_dependencies = [];
			foreach ( $newly_enqueued_styles as $style ) {
				$style_dependencies = array_merge( $style_dependencies, $get_dependencies( $style, $wp_styles ) );
			}

			return [
				'scripts' => array_unique( $script_dependencies, SORT_REGULAR ),
				'styles'  => array_unique( $style_dependencies, SORT_REGULAR ),
				'content' => $rendered_shortcode,
			];
		} finally {
			// Restore singleton state so subsequent renders are not affected.
			$gravityview_frontend->setGvOutputData( $previous_output_data );
			$gravityview_frontend->setPostId( $previous_post_id );
			$gravityview_frontend->setPostHasShortcode( $previous_has_shortcode );
		}
	}

	/**
	 * Build any shortcode from attributes using a custom attribute mapping.
	 *
	 * This is a generic method that can build any GravityView shortcode
	 * ([gravityview], [gventry], [gvfield], [gv_entry_link], etc.).
	 *
	 * @since 3.0.0
	 *
	 * @param string $shortcode_name     Shortcode name (e.g., 'gravityview', 'gventry').
	 * @param array  $block_atts         Block attributes (camelCase).
	 * @param array  $attribute_map      Mapping (camelCase => snake_case).
	 * @param bool   $supports_content   Whether shortcode supports content (wrapping).
	 *
	 * @return string Formatted shortcode string.
	 */
	public static function build_shortcode_from_atts( $shortcode_name, $block_atts, $attribute_map, $supports_content = false ) {
		// Remove searchOperator when searchValue is empty (meaningless without a value).
		if ( isset( $block_atts['searchOperator'] ) && ( ! isset( $block_atts['searchValue'] ) || '' === trim( (string) $block_atts['searchValue'] ) ) ) {
			unset( $block_atts['searchOperator'] );
		}

		$shortcode_atts = [];
		$raw_extras     = [];

		foreach ( $attribute_map as $block_key => $shortcode_key ) {
			if ( isset( $block_atts[ $block_key ] ) && '' !== $block_atts[ $block_key ] && null !== $block_atts[ $block_key ] ) {
				// Special handling for fieldSettingOverrides (doesn't use key="value" format).
				if ( 'field_setting_overrides' === $shortcode_key ) {
					// Raw extras are concatenated into the shortcode string
					// unescaped (they carry their own key="value" syntax), so
					// strip bracket delimiters to stop a smuggled shortcode.
					$raw_extras[] = self::strip_shortcode_delimiters( $block_atts[ $block_key ] );
				} else {
					$shortcode_atts[ $shortcode_key ] = $block_atts[ $block_key ];
				}
			}
		}

		// Handle content for wrapping shortcodes (like [gv_entry_link]content[/gv_entry_link]).
		$content = '';
		if ( $supports_content && isset( $block_atts['content'] ) && '' !== $block_atts['content'] ) {
			$content = wp_kses_post( $block_atts['content'] );
			// Remove content from attributes so it's not duplicated.
			unset( $shortcode_atts['content'] );
		}

		// Build shortcode string.
		$atts_string = self::format_atts_string( $shortcode_atts );

		if ( ! empty( $raw_extras ) ) {
			$atts_string = trim( $atts_string . ' ' . implode( ' ', $raw_extras ) );
		}

		if ( $supports_content && '' !== $content ) {
			return sprintf( '[%s %s]%s[/%s]', $shortcode_name, $atts_string, $content, $shortcode_name );
		}

		return sprintf( '[%s %s]', $shortcode_name, $atts_string );
	}
}
