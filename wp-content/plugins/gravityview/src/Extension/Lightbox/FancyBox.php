<?php
/**
 * FancyBox lightbox provider.
 *
 * PSR-4 migration of the legacy GravityView_Lightbox_Provider_FancyBox class.
 *
 * @package GravityKit\GravityView\Extension\Lightbox
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Extension\Lightbox;

use GravityKit\GravityView\Utils\Assets;

/**
 * Register the FancyBox lightbox
 *
 * @see https://fancyapps.com/fancybox/3/docs/#options
 * @since 2.10
 *
 * @internal
 */
class FancyBox extends \GravityView_Lightbox_Provider {

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public static $slug = 'fancybox';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public static $script_slug = 'gravityview-fancybox';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public static $style_slug = 'gravityview-fancybox';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public static $css_class_name = 'gravityview-fancybox';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public static $data_type_attribute = 'data-type';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	public static $data_type_value = 'ajax';

	/**
	 * @inheritDoc
	 */
	public function print_scripts() {

		parent::print_scripts();

		$settings = self::get_settings();

		$settings = wp_json_encode( $settings );
		?>
		<style>
			.fancybox__container {
				z-index: 100000; /** Divi is 99999 */
			}

			.admin-bar .fancybox__container {
				margin-top: 32px;
			}
		</style>
		<script>
			if ( window.Fancybox ){
				var gvFancyboxSettings = <?php echo $settings; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON produced by wp_json_encode(), safe for a script context. ?>;

				// Stamp the per-View theme marker onto the body-mounted
				// Fancybox container. The `gv-lightbox` hook is applied
				// unconditionally via the `mainClass` setting; the theme
				// classes are copied per-instance from the triggering element
				// so only lightboxes opened from a themed View inherit the
				// theme token scope.
				gvFancyboxSettings.on = Object.assign( {}, gvFancyboxSettings.on, {
					ready: function ( fancybox ) {
						var trigger = fancybox.options && fancybox.options.$trigger;
						var themed  = trigger && trigger.closest ? trigger.closest( '.gv-themed' ) : null;

						if ( themed && fancybox.$container ) {
							fancybox.$container.classList.add( 'gv-themed' );
							themed.classList.forEach( function ( cls ) {
								if ( 0 === cls.indexOf( 'gv-theme-' ) ) {
									fancybox.$container.classList.add( cls );
								}
							} );
						}
					}
				} );

				Fancybox.bind(".<?php echo sanitize_html_class( self::$css_class_name ); ?>", gvFancyboxSettings);
			}
		</script>
		<?php
	}

	/**
	 * Options to pass to Fancybox
	 *
	 * @see https://fancyapps.com/fancybox/3/docs/#options
	 *
	 * @return array
	 */
	protected function default_settings() {

		$defaults = [
			'animationEffect' => 'fade',
			'toolbar'         => true,
			'closeExisting'   => true,
			'arrows'          => true,
			// Stamped onto the body-mounted `.fancybox__container` element;
			// gives GravityView CSS a stable, GV-owned hook for lightboxes
			// the plugin opens.
			'mainClass'       => 'gv-lightbox',
			'buttons'         => [
				'thumbs',
				'close',
			],
			'i18n'            => [
				'en' => [
					'CLOSE'       => __( 'Close', 'gk-gravityview' ),
					'NEXT'        => __( 'Next', 'gk-gravityview' ),
					'PREV'        => __( 'Previous', 'gk-gravityview' ),
					'ERROR'       => __( 'The requested content cannot be loaded. Please try again later.', 'gk-gravityview' ),
					'PLAY_START'  => __( 'Start slideshow', 'gk-gravityview' ),
					'PLAY_STOP'   => __( 'Pause slideshow', 'gk-gravityview' ),
					'FULL_SCREEN' => __( 'Full screen', 'gk-gravityview' ),
					'THUMBS'      => __( 'Thumbnails', 'gk-gravityview' ),
					'DOWNLOAD'    => __( 'Download', 'gk-gravityview' ),
					'SHARE'       => __( 'Share', 'gk-gravityview' ),
					'ZOOM'        => __( 'Zoom', 'gk-gravityview' ),
				],
			],
		];

		return $defaults;
	}

	/**
	 * @inheritDoc
	 */
	public function enqueue_scripts() {
		wp_register_script( self::$script_slug, Assets::url( 'lib/fancybox/dist/fancybox.umd.js' ), [], \GV_PLUGIN_VERSION, false );
	}

	/**
	 * @inheritDoc
	 */
	public function enqueue_styles() {
		wp_register_style( self::$style_slug, Assets::url( 'lib/fancybox/dist/fancybox.css' ), [], \GV_PLUGIN_VERSION );
	}

	/**
	 * {@inheritDoc}
	 */
	public function allowed_atts( $atts = [] ) {

		$atts = parent::allowed_atts( $atts );

		$atts['data-fancybox']         = null;
		$atts['data-fancybox-trigger'] = null;
		$atts['data-fancybox-index']   = null;
		$atts['data-src']              = null;
		$atts['data-type']             = null;
		$atts['data-width']            = null;
		$atts['data-height']           = null;
		$atts['data-srcset']           = null;
		$atts['data-caption']          = null;
		$atts['data-options']          = null;
		$atts['data-filter']           = null;
		$atts['data-preload']          = null;

		return $atts;
	}

	/**
	 * {@inheritDoc}
	 */
	public function fileupload_link_atts( $link_atts, $field_compat = [], $context = null, $additional_details = null ) {

		if ( $context && ! $context->view->settings->get( 'lightbox', false ) ) {
			return $link_atts;
		}

		// Prevent empty content from getting added to the lightbox gallery.
		if ( is_array( $additional_details ) && empty( $additional_details['file_path'] ) ) {
			return $link_atts;
		}

		// Prevent empty content from getting added to the lightbox gallery.
		if ( is_array( $additional_details ) && ! empty( $additional_details['disable_lightbox'] ) ) {
			return $link_atts;
		}

		$link_atts['class'] = \GV\Utils::get( $link_atts, 'class' ) . ' ' . self::$css_class_name;

		$link_atts['class'] = gravityview_sanitize_html_class( $link_atts['class'] );

		if ( $context && ! empty( $context->field->field ) ) {
			if ( $context->field->field->multipleFiles ) {
				$entry                      = $context->entry->as_entry();
				$link_atts['data-fancybox'] = 'gallery-' . sprintf( '%s-%s-%s', $entry['form_id'], $context->field->ID, $context->entry->get_slug() );
			}
		}

		$file_path = \GV\Utils::get( $additional_details, 'file_path', '' );

		/**
		 * For file types that require iframe (e.g., PDFs, text files), declare `pdf` media type.
		 * SVGs are not supported by Fancybox by default but render fine inside an iframe.
		 *
		 * @see https://web.archive.org/web/20230221135246/https://fancyapps.com/docs/ui/fancybox/#media-types
		 */
		if ( false !== strpos( $file_path, 'gv-iframe' ) || preg_match( '/\.svg$/i', $file_path ) ) {
			$link_atts['data-type'] = 'pdf';
		}

		return $link_atts;
	}
}

\GravityView_Lightbox::register( 'GravityKit\GravityView\Extension\Lightbox\FancyBox' );
