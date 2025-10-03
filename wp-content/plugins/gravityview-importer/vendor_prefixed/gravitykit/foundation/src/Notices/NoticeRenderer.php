<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Notices;

use GravityKit\GravityImport\Foundation\Core as FoundationCore;
use GravityKit\GravityImport\Foundation\Translations\Framework as TranslationsFramework;
use GravityKit\GravityImport\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityImport\Foundation\Logger\Framework as Logger;
use GravityKit\GravityImport\Foundation\WP\AjaxRouter;

/**
 * Handles rendering of admin notices including assets, HTML generation, etc.
 *
 * @since 1.3.0
 */
final class NoticeRenderer {
	/**
	 * Handle for the compiled CSS and JS files.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const ASSETS_HANDLE = 'gk-notices';

	/**
	 * CSS file name.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const CSS_FILE = 'notices.css';

	/**
	 * JS file name.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const JS_FILE = 'notices.js';

	/**
	 * Renders notices by enqueuing assets and generating HTML.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $active_notices Array of active notices for the current request.
	 *
	 * @throws NoticeException When rendering fails.
	 *
	 * @return bool
	 */
	public function render( array $active_notices ): bool {
		if ( empty( $active_notices ) ) {
			return false;
		}

		$this->enqueue_assets( $active_notices );

		$this->output_notices( $active_notices );

		return true;
	}

	/**
	 * Enqueues CSS/JS assets and localizes script data.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $active_notices Array of active notices.
	 *
	 * @throws NoticeException When asset enqueuing fails.
	 *
	 * @return void
	 */
	private function enqueue_assets( array $active_notices ): void {
		$css_path = CoreHelpers::get_assets_path( self::CSS_FILE );
		$js_path  = CoreHelpers::get_assets_path( self::JS_FILE );

		if ( ! file_exists( $css_path ) || ! file_exists( $js_path ) ) {
			$missing = [];

			if ( ! file_exists( $css_path ) ) {
				$missing[] = self::CSS_FILE;
			}

			if ( ! file_exists( $js_path ) ) {
				$missing[] = self::JS_FILE;
			}

			Logger::get_instance()->warning( 'NoticeRenderer: required asset(s) missing – ' . implode( ', ', $missing ) );

			throw NoticeException::evaluation( 'Assets missing', [ 'files' => $missing ] );
		}

		wp_enqueue_script(
			self::ASSETS_HANDLE,
			CoreHelpers::get_assets_url( self::JS_FILE ),
			[ 'wp-i18n' ],
			(string) filemtime( $js_path ),
			[ 'strategy' => 'async' ]
		);

		wp_enqueue_style(
			self::ASSETS_HANDLE,
			CoreHelpers::get_assets_url( self::CSS_FILE ),
			[],
			(string) filemtime( $css_path )
		);

		$params = array_merge(
			AjaxRouter::get_ajax_params( NoticeAjaxController::AJAX_ROUTER ),
			[
				'noticeGroupProduct' => Notice::default_product(),
				'languageDirection'  => is_rtl() ? 'rtl' : 'ltr',
				'liveDefaults'       => [
					'defaultRefresh'       => StoredNotice::LIVE_DEFAULT_REFRESH,
					'maxRefresh'           => StoredNotice::LIVE_MAX_REFRESH,
					'maxConsecutiveErrors' => StoredNotice::LIVE_MAX_CONSECUTIVE_ERRORS,
				],
			]
		);

		$payload_notices = array_map(
			static function ( NoticeInterface $notice ) {
				return $notice->as_payload();
			},
			$active_notices
		);

		$data = [
			'params'  => $params,
			'notices' => array_values( $payload_notices ),
		];

		/**
		 * Filters the payload data used to render notices in the UI.
		 *
		 * @filter `gk/foundation/notices/render/payload`
		 *
		 * @since 1.3.0
		 *
		 * @param array{
		 *     params: array<string, mixed>,
		 *     notices: array<array<string, mixed>>
		 * } $data Payload data containing notices and parameters.
		 */
		$data = apply_filters( 'gk/foundation/notices/render/payload', $data );

		wp_localize_script(
			self::ASSETS_HANDLE,
			'gkNotices',
			[
				'data' => $data,
			]
		);

		$foundation_information = FoundationCore::get_instance()->get_foundation_information();
		TranslationsFramework::get_instance()->load_frontend_translations( $foundation_information['source_plugin']['TextDomain'], '', 'gk-foundation' );
	}

	/**
	 * Outputs HTML for notices.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $active_notices Array of active notices.
	 *
	 * @return void
	 */
	private function output_notices( array $active_notices ): void {
		$skeleton_html = $this->get_skeleton_html( $active_notices );

		if ( ! empty( $skeleton_html ) ) {
			echo '<style>' . $this->get_skeleton_css() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div id="gk-notices-skeleton" class="notice">' . $skeleton_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Filters the HTML container where notices are rendered.
		 *
		 * @filter `gk/foundation/notices/render/container`
		 *
		 * @since 1.3.0
		 *
		 * @param string $html HTML container for notices.
		 */
		echo apply_filters( 'gk/foundation/notices/render/container', '<div id="gk-notices" class="notice"></div>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Generates skeleton loader CSS.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	private function get_skeleton_css(): string {
		$is_rtl = is_rtl();

		$styles = '
			/* Initial state - before WP moves notices */
			#gk-notices-skeleton {
				padding: 0 !important;
				padding-' . ( $is_rtl ? 'left' : 'right' ) . ': 20px !important;
				margin: 15px 0 15px !important;
				border: none !important;
				background: transparent !important;
				box-shadow: none !important;
			}
			/* After WP moves notices under .wrap */
			.wrap #gk-notices-skeleton {
				padding: 0 !important;
			}
			@keyframes gk-skeleton-shimmer-ltr {
				0% {
					transform: translateX(-100%);
				}
				100% {
					transform: translateX(100%);
				}
			}
			@keyframes gk-skeleton-shimmer-rtl {
				0% {
					transform: translateX(100%);
				}
				100% {
					transform: translateX(-100%);
				}
			}
			@media (prefers-reduced-motion: reduce) {
				.gk-skeleton {
					animation: none !important;
				}
			}
			.gk-skeleton {
				background-color: #e0e0e0;
				border-radius: 0.25rem;
				overflow: hidden;
				position: relative;
			}
			.gk-skeleton::after {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: linear-gradient(
					90deg,
					transparent 0%,
					rgba(255, 255, 255, 0.4) 50%,
					transparent 100%
				);
				animation: ' . ( $is_rtl ? 'gk-skeleton-shimmer-rtl' : 'gk-skeleton-shimmer-ltr' ) . ' 1.5s linear infinite;
			}
			.gk-skeleton-wrapper {
				width: 100%;
			}
			.gk-skeleton-container {
				background: white;
				box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
				border: 1px solid rgba(0, 0, 0, 0.05);
			}
			.gk-skeleton-header {
				height: 63px;
				box-sizing: border-box;
				padding: 16px;
				background-color: rgba(249, 250, 251, 0.5);
				border-bottom: 1px solid #e5e7eb;
				display: flex;
				justify-content: space-between;
				align-items: center;
			}
			.gk-skeleton-group {
				display: grid;
				position: relative;
			}
			.gk-skeleton-group > div {
				grid-column: 1;
				grid-row: 1;
			}
			.gk-skeleton-card {
				height: 66px;
				box-sizing: border-box;
				padding: 16px;
				position: relative;
				border-bottom: 1px solid #e5e7eb;
			}
			.gk-skeleton-card:last-child {
				border-bottom: none;
			}
			.gk-skeleton-content {
				display: flex;
				gap: 16px;
				align-items: center;
			}
			.gk-skeleton-text {
				flex: 1;
			}
			.gk-skeleton-line {
				height: 12px;
				margin-bottom: 8px;
			}
			.gk-skeleton-line:last-child {
				margin-bottom: 0;
			}
			.gk-skeleton-icon {
				width: 28px;
				height: 28px;
				border-radius: 0.375rem;
				flex-shrink: 0;
			}
			.gk-skeleton-button {
				width: 80px;
				height: 30px;
				border-radius: 3px;
			}
			.gk-skeleton-actions {
				display: flex;
				gap: 12px;
			}
			.gk-skeleton-notice-actions {
				display: flex;
				align-items: center;
				gap: 16px;
			}
			.gk-skeleton-notice-button {
				width: 100px;
				height: 30px;
				border-radius: 3px;
			}
			.gk-skeleton-notice-actions-with-dismiss {
				margin-' . ( $is_rtl ? 'left' : 'right' ) . ': 25px;
			}
			.gk-skeleton-dismiss {
				width: 15px;
				height: 15px;
				border-radius: 50%;
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				' . ( $is_rtl ? 'left' : 'right' ) . ': 16px;
			}
			.gk-skeleton-severity {
				position: absolute;
				width: 0;
				height: 0;
				top: 0;
				' . ( $is_rtl ? 'right' : 'left' ) . ': 0;
				border-style: solid;
				border-width: ' . ( $is_rtl ? '0 12px 12px 0' : '12px 12px 0 0' ) . ';
				border-color: transparent;
				' . ( $is_rtl ? 'border-right-color' : 'border-top-color' ) . ': #e0e0e0;
			}
			.gk-sr-only {
				position: absolute;
				width: 1px;
				height: 1px;
				padding: 0;
				margin: -1px;
				overflow: hidden;
				clip: rect(0, 0, 0, 0);
				white-space: nowrap;
				border: 0;
			}';

		// Minify the CSS.
		$styles = preg_replace( '/\/\*[^*]*\*+(?:[^\/][^*]*\*+)*\//', '', $styles ); // Remove comments.
		$styles = preg_replace( '/\s+/', ' ', $styles ); // Replace multiple spaces with single space.
		$styles = preg_replace( '/\s*([{}:;,])\s*/', '$1', $styles ); // Remove spaces around CSS delimiters.
		$styles = trim( $styles );

		return $styles;
	}

	/**
	 * Generates skeleton loader HTML.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $notices Array of notices.
	 *
	 * @return string
	 */
	private function get_skeleton_html( array $notices ): string {
		if ( empty( $notices ) ) {
			return '';
		}

		// Count sticky, non-sticky, and dismissible notices.
		$sticky_count     = 0;
		$non_sticky_count = 0;
		$has_dismissible  = false;

		foreach ( $notices as $notice ) {
			if ( $notice->is_sticky() ) {
				$sticky_count++;
			} else {
				$non_sticky_count++;
			}

			if ( $notice->is_dismissible() ) {
				$has_dismissible = true;
			}
		}

		// Calculate visible notices: all sticky + up to 3 non-sticky.
		$visible_non_sticky = min( $non_sticky_count, 3 );
		$total_visible      = $sticky_count + $visible_non_sticky;
		$collapsed_count    = max( 0, $non_sticky_count - 3 );

		if ( 0 === $total_visible ) {
			return '';
		}

		if ( 1 === $total_visible ) {
			// Single notice skeleton.
			$single_notice       = $notices[0] ?? null;
			$single_actions_html = '';
			$single_card_style   = '';

			if ( $single_notice ) {
				$has_single_actions = $single_notice->is_dismissible() || ! empty( $single_notice->get_snooze_options() );
				$single_card_style  = $single_notice->is_dismissible() ? 'style="padding-' . ( is_rtl() ? 'left' : 'right' ) . ': 56px;"' : '';

				if ( $has_single_actions ) {
					$single_actions_html = '<div class="gk-skeleton-notice-actions">';

					if ( ! empty( $single_notice->get_snooze_options() ) ) {
						$single_actions_html .= '<div class="gk-skeleton-notice-button gk-skeleton" aria-hidden="true"></div>';
					}

					if ( $single_notice->is_dismissible() ) {
						$single_actions_html .= '<div class="gk-skeleton-dismiss gk-skeleton" aria-hidden="true"></div>';
					}

					$single_actions_html .= '</div>';
				}
			}

			return '
			<div class="gk-skeleton-wrapper" role="status" aria-live="polite" aria-label="Loading notice">
				<span class="gk-sr-only">Loading notice content</span>
				<div class="gk-skeleton-container">
					<div class="gk-skeleton-card" ' . $single_card_style . '>
						<div class="gk-skeleton-severity"></div>
						<div class="gk-skeleton-content">
							<div class="gk-skeleton-icon gk-skeleton" aria-hidden="true"></div>
							<div class="gk-skeleton-text">
								<div class="gk-skeleton-line gk-skeleton" style="width: 70%"></div>
							</div>
							' . $single_actions_html . '
						</div>
					</div>
				</div>
			</div>';
		}

		// Generate skeleton cards for visible notices.
		$skeleton_cards = '';

		// Get actual visible notices to check their properties.
		$visible_notices = [];
		foreach ( $notices as $notice ) {
			if ( $notice->is_sticky() || count( $visible_notices ) - $sticky_count < 3 ) {
				$visible_notices[] = $notice;
			}
		}

		// Determine if we should show icons (same logic as in the UI).
		$product_names = [];

		foreach ( $notices as $notice ) {
			if ( method_exists( $notice, 'get_product' ) ) {
				$product = $notice->get_product();

				if ( ! empty( $product['name'] ) ) {
					$product_names[] = $product['name'];
				}
			}
		}

		$show_skeleton_icons = count( array_unique( $product_names ) ) > 1;

		for ( $i = 0; $i < $total_visible; $i++ ) {
			$widths    = [
				[ '70%', '90%' ],
				[ '80%', '60%' ],
				[ '65%', '85%' ],
				[ '75%', '70%' ],
				[ '85%', '65%' ],
			];
			$width_set = $widths[ $i % count( $widths ) ];

			// Check if this notice has actions.
			$has_actions      = false;
			$has_both_actions = false;

			if ( isset( $visible_notices[ $i ] ) ) {
				$notice           = $visible_notices[ $i ];
				$has_actions      = $notice->is_dismissible() || ! empty( $notice->get_snooze_options() );
				$has_both_actions = $notice->is_dismissible() && ! empty( $notice->get_snooze_options() );
			}

			$actions_class = $has_both_actions ? 'gk-skeleton-notice-actions gk-skeleton-notice-actions-with-dismiss' : 'gk-skeleton-notice-actions';

			$skeleton_cards .= '
					<div class="gk-skeleton-card">
						<div class="gk-skeleton-severity"></div>
						<div class="gk-skeleton-content">'
						. ( $show_skeleton_icons ? '<div class="gk-skeleton-icon gk-skeleton" aria-hidden="true"></div>' : '' ) . '
							<div class="gk-skeleton-text">
								<div class="gk-skeleton-line gk-skeleton" style="width: ' . $width_set[0] . '"></div>
							</div>
							' . ( $has_actions ? '<div class="' . $actions_class . '">' : '' );

			// Add snooze button if notice has snooze options.
			if ( isset( $visible_notices[ $i ] ) && ! empty( $visible_notices[ $i ]->get_snooze_options() ) ) {
				$skeleton_cards .= '<div class="gk-skeleton-notice-button gk-skeleton" aria-hidden="true"></div>';
			}

			// Add dismiss button if notice is dismissible.
			if ( isset( $visible_notices[ $i ] ) && $visible_notices[ $i ]->is_dismissible() ) {
				$skeleton_cards .= '<div class="gk-skeleton-dismiss gk-skeleton" aria-hidden="true"></div>';
			}

			$skeleton_cards .= ( $has_actions ? '</div>' : '' ) . '
						</div>
					</div>';
		}

		// Build stacked containers only if there are collapsed notices.
		$stacked_html = '';
		if ( $collapsed_count > 0 ) {
			$stacked_html = '
				<div class="gk-skeleton-container" style="transform: translateY(8px); opacity: 0.5;" aria-hidden="true"></div>
				<div class="gk-skeleton-container" style="transform: translateY(4px); opacity: 0.8;" aria-hidden="true"></div>';
		}

		// Build action buttons based on what's available.
		$actions_html = '';

		if ( $collapsed_count > 0 ) {
			$actions_html .= '<div class="gk-skeleton-button gk-skeleton" aria-hidden="true"></div>';
		}

		if ( $has_dismissible ) {
			$actions_html .= '<div class="gk-skeleton-button gk-skeleton" aria-hidden="true"></div>';
		}

		// Grouped notices skeleton.
		return '
		<div class="gk-skeleton-wrapper" role="status" aria-live="polite" aria-label="Loading ' . $total_visible . ' notices">
			<span class="gk-sr-only">Loading ' . $total_visible . ' notices</span>
			<div class="gk-skeleton-group">' . $stacked_html . '
				<div class="gk-skeleton-container">
					<div class="gk-skeleton-header">
						<div style="display: flex; align-items: center; gap: 16px;">
							<div class="gk-skeleton-icon gk-skeleton" aria-hidden="true"></div>
							<div class="gk-skeleton-line gk-skeleton" style="width: 120px; height: 14px;" aria-hidden="true"></div>
						</div>
						' . ( ! empty( $actions_html ) ? '<div class="gk-skeleton-actions">' . $actions_html . '</div>' : '' ) . '
					</div>
					<div>' . $skeleton_cards . '
					</div>
				</div>
			</div>
		</div>';
	}
}
