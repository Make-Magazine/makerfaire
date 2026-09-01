<?php
/**
 * Page Links widget type.
 *
 * PSR-4 migration of the legacy GravityView_Widget_Page_Links class.
 *
 * @package GravityKit\GravityView\Widget\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Widget\Types;

use GravityKit\GravityView\Pagination\PaginationKeys;
use GravityKit\GravityView\Template\TemplateContext;
use GravityView_View;

use function gravityview;
use function gravityview_get_view_id;

/**
 * Widget to display page links.
 *
 * @since 3.0.0
 */
class PageLinks extends \GV\Widget {

	const DISPLAY_NUMBERS           = 'numbers';
	const DISPLAY_NUMBERS_PREV_NEXT = 'numbers_prev_next';
	const DISPLAY_PREV_NEXT_ONLY    = 'prev_next_only';

	/**
	 * The shortcode this widget registers.
	 *
	 * @var string
	 */
	protected $shortcode_name = 'gravityview_widget_page_links';

	/**
	 * Dashicon shown in the widget picker.
	 *
	 * @var string
	 */
	public $icon = 'dashicons-controls-forward';

	/**
	 * Whether the widget renders on the single entry screen.
	 *
	 * @var bool
	 */
	protected $show_on_single = false;

	/**
	 * Registers widget settings and labels.
	 */
	public function __construct() {

		$this->widget_description = __( 'Links to multiple pages of results.', 'gk-gravityview' );

		$default_values = [
			'header' => 1,
			'footer' => 1,
		];
		$settings       = [
			'display_mode'    => [
				'type'    => 'select',
				'label'   => __( 'Display mode', 'gk-gravityview' ),
				'desc'    => __( 'How page navigation appears on the front end.', 'gk-gravityview' ),
				'value'   => self::DISPLAY_NUMBERS,
				'options' => [
					self::DISPLAY_NUMBERS           => __( 'Numbers with arrows (default)', 'gk-gravityview' ),
					self::DISPLAY_NUMBERS_PREV_NEXT => __( 'Numbers with Previous / Next labels', 'gk-gravityview' ),
					self::DISPLAY_PREV_NEXT_ONLY    => __( 'Previous / Next only', 'gk-gravityview' ),
				],
			],
			'prev_label'      => [
				'type'         => 'text',
				'label'        => __( 'Previous label', 'gk-gravityview' ),
				'desc'         => __( 'Text shown on the "previous page" link.', 'gk-gravityview' ),
				'value'        => __( '« Previous', 'gk-gravityview' ),
				'class'        => 'widefat',
				'requires_not' => 'display_mode=' . self::DISPLAY_NUMBERS,
			],
			'next_label'      => [
				'type'         => 'text',
				'label'        => __( 'Next label', 'gk-gravityview' ),
				'desc'         => __( 'Text shown on the "next page" link.', 'gk-gravityview' ),
				'value'        => __( 'Next »', 'gk-gravityview' ),
				'class'        => 'widefat',
				'requires_not' => 'display_mode=' . self::DISPLAY_NUMBERS,
			],
			'show_first_last' => [
				'type'         => 'checkbox',
				'label'        => __( 'Show First / Last links', 'gk-gravityview' ),
				'desc'         => __( 'Add "First" and "Last" links to the navigation.', 'gk-gravityview' ),
				'value'        => false,
				'requires_not' => 'display_mode=' . self::DISPLAY_PREV_NEXT_ONLY,
			],
			'show_all'        => [
				'type'         => 'checkbox',
				'label'        => __( 'Show each page number', 'gk-gravityview' ),
				'desc'         => __( 'Show every page number instead of summary (eg: 1 2 3 ... 8 »).', 'gk-gravityview' ),
				'value'        => false,
				'requires_not' => 'display_mode=' . self::DISPLAY_PREV_NEXT_ONLY,
			],
		];
		parent::__construct( __( 'Page Links', 'gk-gravityview' ), 'page_links', $default_values, $settings );
	}

	/**
	 * Renders the page links on the frontend.
	 *
	 * @param array                       $widget_args Widget arguments.
	 * @param string                      $content     Render content (unused).
	 * @param string|\GV\Template_Context $context     Render context.
	 */
	public function render_frontend( $widget_args, $content = '', $context = '' ) {
		$gravityview_view = GravityView_View::getInstance();

		if ( ! $this->pre_render_frontend( $context ) ) {
			return;
		}

		$has_context_view = $context instanceof TemplateContext && $context->view;
		$widget_view      = $has_context_view ? $context->view : gravityview_get_view_id();

		$atts = shortcode_atts(
			[
				'page_size'       => \GV\Utils::get( $gravityview_view->paging, 'page_size' ),
				'total'           => $gravityview_view->total_entries,
				'show_all'        => false,
				'display_mode'    => self::DISPLAY_NUMBERS,
				'prev_label'      => __( '« Previous', 'gk-gravityview' ),
				'next_label'      => __( 'Next »', 'gk-gravityview' ),
				'show_first_last' => false,
				'current'         => PaginationKeys::current_page( $widget_view ),
			],
			$widget_args,
			'gravityview_widget_page_links'
		);

		$display_mode = self::normalize_display_mode( $atts['display_mode'] );
		$is_text_mode = self::DISPLAY_NUMBERS !== $display_mode;

		// Empty labels fall back to defaults so a cleared field doesn't render an empty anchor.
		$prev_label = '' !== trim( (string) $atts['prev_label'] )
			? wp_kses_post( (string) $atts['prev_label'] )
			: __( '« Previous', 'gk-gravityview' );
		$next_label = '' !== trim( (string) $atts['next_label'] )
			? wp_kses_post( (string) $atts['next_label'] )
			: __( 'Next »', 'gk-gravityview' );

		$prev_text = $is_text_mode ? $prev_label : '&laquo;';
		$next_text = $is_text_mode ? $next_label : '&raquo;';

		$total_pages = empty( $atts['page_size'] ) ? 0 : (int) ceil( (int) $atts['total'] / (int) $atts['page_size'] );

		$pagination_key = PaginationKeys::key( $widget_view );

		// The directory link carries the request's pagination (scoped or base key).
		// add_query_arg() below only overwrites $pagination_key itself, so the
		// OTHER key would survive in every built link and, because the scoped key
		// wins on read, pin the View to the current page. Drop the stale one.
		$stale_pagination_keys = array_diff( PaginationKeys::keys_to_strip( $widget_view ), [ $pagination_key ] );

		$directory_link = remove_query_arg( $stale_pagination_keys, (string) gv_directory_link() );

		$page_link_args = [
			'base'      => add_query_arg( $pagination_key, '%#%', $directory_link ),
			'format'    => '&' . $pagination_key . '=%#%',
			'add_args'  => [],
			'prev_text' => $prev_text,
			'next_text' => $next_text,
			'type'      => 'array',
			'end_size'  => 1,
			'mid_size'  => 2,
			'total'     => $total_pages,
			'current'   => $atts['current'],
			'show_all'  => ! empty( $atts['show_all'] ),
		];

		/**
		 * Filters the pagination options before they are passed to `paginate_links()`.
		 *
		 * Returning a non-array skips the widget render (the rest of the View
		 * still renders). The `type` argument is overridden to `array` for
		 * "Previous / Next only" mode and when "Show First / Last links" is
		 * enabled, because those modes post-process the link list; other modes
		 * honor any `type` set here.
		 *
		 * @since 1.1.4
		 *
		 * @param array $page_link_args Array of arguments for the `paginate_links()` function.
		 *                              {@link https://developer.wordpress.org/reference/functions/paginate_links/ Read more about `paginate_links()`}.
		 */
		$page_link_args = apply_filters( 'gravityview_page_links_args', $page_link_args );

		if ( ! is_array( $page_link_args ) ) {
			gravityview()->log->error( 'gravityview_page_links_args filter returned non-array; skipping pagination render.' );
			return;
		}

		$filtered_type    = isset( $page_link_args['type'] ) ? $page_link_args['type'] : 'array';
		$filtered_total   = isset( $page_link_args['total'] ) ? (int) $page_link_args['total'] : $total_pages;
		$filtered_current = isset( $page_link_args['current'] ) ? (int) $page_link_args['current'] : (int) $atts['current'];

		// Mirror the resolved type back so paginate_links() doesn't fall through to its 'plain' default
		// when a filter unset the key entirely.
		$page_link_args['type'] = $filtered_type;

		// Prev/Next-only and Show First/Last need to post-process the link
		// list; UI choice wins over a filter that set `type` to something else.
		$requires_array_path = self::DISPLAY_PREV_NEXT_ONLY === $display_mode
			|| ! empty( $atts['show_first_last'] );

		if ( $requires_array_path && 'array' !== $filtered_type ) {
			gravityview()->log->debug(
				sprintf(
					'gravityview_page_links_args set type=%s, but display_mode=%s (or show_first_last) requires type=array. Forcing array.',
					(string) $filtered_type,
					$display_mode
				)
			);
			$page_link_args['type'] = 'array';
			$filtered_type          = 'array';
		}

		if ( 'array' !== $filtered_type ) {
			$legacy = paginate_links( $page_link_args );
			if ( ! empty( $legacy ) ) {
				echo '<nav class="' . esc_attr( $this->build_wrapper_class( $widget_args, $display_mode ) ) . '" aria-label="' . esc_attr__( 'Pagination', 'gk-gravityview' ) . '">' . $legacy . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns trusted markup.
			}
			return;
		}

		$links = paginate_links( $page_link_args );

		if ( empty( $links ) || ! is_array( $links ) ) {
			gravityview()->log->debug( 'No page links; paginate_links() returned empty response.' );
			return;
		}

		if ( self::DISPLAY_PREV_NEXT_ONLY === $display_mode ) {
			$links = array_values( array_filter( $links, [ __CLASS__, 'is_prev_or_next_link' ] ) );

			if ( empty( $links ) ) {
				gravityview()->log->debug( 'No prev/next links available for Prev/Next-only display mode.' );
				return;
			}
		}

		if ( ! empty( $atts['show_first_last'] ) && self::DISPLAY_PREV_NEXT_ONLY !== $display_mode && $filtered_total > 1 ) {
			$base = isset( $page_link_args['base'] ) ? (string) $page_link_args['base'] : '';

			if ( $filtered_current > 1 ) {
				array_unshift(
					$links,
					sprintf(
						'<a class="first page-numbers" href="%s">%s</a>',
						esc_url( self::build_page_url( $base, 1, $pagination_key ) ),
						esc_html__( '« First', 'gk-gravityview' )
					)
				);
			}
			if ( $filtered_current < $filtered_total ) {
				$links[] = sprintf(
					'<a class="last page-numbers" href="%s">%s</a>',
					esc_url( self::build_page_url( $base, $filtered_total, $pagination_key ) ),
					esc_html__( 'Last »', 'gk-gravityview' )
				);
			}
		}

		$class = $this->build_wrapper_class( $widget_args, $display_mode );

		echo '<nav class="' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Pagination', 'gk-gravityview' ) . '"><ul class="page-numbers">';
		foreach ( $links as $link ) {
			echo '<li>' . $link . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns trusted markup.
		}
		echo '</ul></nav>';
	}

	/**
	 * Normalizes a display-mode string to one of the DISPLAY_* constants.
	 *
	 * @since 3.0.0
	 *
	 * @param string $mode Raw mode value.
	 * @return string
	 */
	public static function normalize_display_mode( $mode ) {
		$mode = is_string( $mode ) ? strtolower( $mode ) : '';

		$valid = [
			self::DISPLAY_NUMBERS,
			self::DISPLAY_NUMBERS_PREV_NEXT,
			self::DISPLAY_PREV_NEXT_ONLY,
		];

		return in_array( $mode, $valid, true ) ? $mode : self::DISPLAY_NUMBERS;
	}

	/**
	 * Whether the given paginate_links() array element is a prev or next anchor.
	 *
	 * Uses WP_HTML_Tag_Processor (WP 6.2+) when available; falls back to a
	 * whitespace-anchored class-attribute regex on older WordPress.
	 *
	 * @since 3.0.0
	 *
	 * @param string $link HTML element produced by paginate_links().
	 * @return bool
	 */
	public static function is_prev_or_next_link( $link ) {
		if ( ! is_string( $link ) || '' === $link ) {
			return false;
		}

		if ( class_exists( '\WP_HTML_Tag_Processor' ) ) {
			$processor = new \WP_HTML_Tag_Processor( $link );
			if ( ! $processor->next_tag() ) {
				return false;
			}
			$class = $processor->get_attribute( 'class' );
			if ( ! is_string( $class ) ) {
				return false;
			}
			return self::class_attribute_marks_prev_or_next( $class );
		}

		// An (?:^|\s) anchor before the class attribute keeps data-class attributes from matching.
		if ( ! preg_match( '/(?:^|\s)class=("|\')([^"\']*)\1/', $link, $matches ) ) {
			return false;
		}

		return self::class_attribute_marks_prev_or_next( $matches[2] );
	}

	/**
	 * Whole-token check on a class-attribute value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class_value Raw class-attribute value.
	 * @return bool
	 */
	private static function class_attribute_marks_prev_or_next( $class_value ) {
		$class_value = trim( (string) $class_value );
		if ( '' === $class_value ) {
			return false;
		}

		$tokens = preg_split( '/\s+/', $class_value );
		if ( ! is_array( $tokens ) ) {
			return false;
		}

		// Whole-token comparison rejects `prevented`, `nextlink`, `page-numbers-fancy`.
		if ( ! in_array( 'page-numbers', $tokens, true ) ) {
			return false;
		}

		return in_array( 'prev', $tokens, true ) || in_array( 'next', $tokens, true );
	}

	/**
	 * Substitutes a page number into a paginate_links() base URL.
	 *
	 * Falls back to `add_query_arg()` when `%#%` is missing so a filter that
	 * rewrote `base` can't produce broken hrefs.
	 *
	 * @since 3.0.0
	 *
	 * @param string $base           Base URL — typically contains `%#%`.
	 * @param int    $page           Page number to embed (clamped to >= 1).
	 * @param string $pagination_key Query key appended by the fallback. Default: `pagenum`.
	 * @return string Final URL.
	 */
	public static function build_page_url( $base, $page, $pagination_key = PaginationKeys::BASE_KEY ) {
		$page = (string) max( 1, (int) $page );
		$base = (string) $base;

		if ( false !== strpos( $base, '%#%' ) ) {
			return str_replace( '%#%', $page, $base );
		}

		return add_query_arg( $pagination_key, $page, $base );
	}

	/**
	 * Builds the wrapper class string applied to the widget output.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $widget_args  Widget arguments.
	 * @param string $display_mode Normalized display mode.
	 * @return string
	 */
	protected function build_wrapper_class( $widget_args, $display_mode ) {
		$custom   = ! empty( $widget_args['custom_class'] ) ? $widget_args['custom_class'] : '';
		$modifier = 'gv-widget-page-links--' . str_replace( '_', '-', $display_mode );

		return gravityview_sanitize_html_class( 'gv-widget-page-links ' . $modifier . ' ' . $custom );
	}
}
