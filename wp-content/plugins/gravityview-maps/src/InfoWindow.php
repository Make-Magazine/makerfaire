<?php

namespace GravityKit\GravityMaps;

use GravityView_View;
use GravityView_Merge_Tags;

class InfoWindow {
	/**
	 * Holds the info window template content variables
	 * @var array
	 */
	var $content = array();

	/**
	 * Holds the template html string ready to be filled in with the entry details
	 * @var string
	 */
	var $template_html = '';

	/**
	 * InfoWindow constructor.
	 *
	 * @param array $map_settings
	 */
	function __construct( $map_settings = array() ) {
		$this->set_content_vars( $map_settings );

		$this->prepare_template();
	}

	/**
	 * Set the info window content variables according to the Map settings
	 *
	 * @param $settings array Maps Settings
	 */
	function set_content_vars( $settings ) {
		$content = array(
			'img'             => '',
			'title'           => $settings['map_info_title'],
			'img_src'         => $settings['map_info_image'],
			'container_class' => '',
			'link_atts'       => 'class="gv-infowindow-entry-link"',
			'content'         => $settings['map_info_content'],
			'link_open'       => '',
			'link_close'      => '',
		);

		// set image alignment
		if ( ! empty( $content['img_src'] ) ) {
			$content['img']             = '<div class="gv-infowindow-image"><img src="[[img_src]]" alt="[[title]]"></div>';
			$content['container_class'] = ! empty( $settings['map_info_image_align'] ) ? 'gv-infowindow-' . esc_attr( $settings['map_info_image_align'] ) : 'gv-infowindow-full';
		} else {
			$content['container_class'] = 'gv-infowindow-no-image';
		}

		// set link attributes
		/**
		 * @filter `gravityview/maps/infowindow/link_target` Customise the entry link target
		 * @since  1.4
		 *
		 * @param string $target Define the entry link target '_top', '_blank', ..
		 */
		$target = apply_filters( 'gravityview/maps/infowindow/link_target', '' );
		if ( ! empty( $target ) ) {
			$content['link_atts'] .= ' target="' . $target . '"';
		}

		// Set the content variables so that they can be used by 'link_open' and 'link_close' below.
		$this->content = $content;

		// By default, use the link (for backward-compatibility, before we added the setting).
		if( ! isset( $settings['map_info_title_link'] ) || ! empty( $settings['map_info_title_link'] ) ) {
			$content['link_open']  = $this->replace_tags( '<a href="[[entry_url]]" [[link_atts]]>' );
			$content['link_close'] = '</a>';
		}

		/**
		 * @filter `gravityview/maps/infowindow/content/vars` Filter the content variables values before building the template markup
		 * Add `allow-empty` attribute to `link_atts` if you want to prevent "View Details" default text from being added
		 * @since  1.4
		 *
		 * @param array $content The array containing the template tags as keys and the template values as array values
		 */
		$this->content = apply_filters( 'gravityview/maps/infowindow/content/vars', $content );

		do_action( 'gravityview_log_debug', __METHOD__ . ': Marker Info Window content vars: ', $this->content );
	}

	/**
	 * Load the Marker Infowindow template and replace the placeholders by the configured content
	 */
	function prepare_template() {
		$gravityview_view = GravityView_View::getInstance();

		$template_path = $gravityview_view->locate_template( 'map-marker-infowindow.php' );

		ob_start();

		load_template( $template_path, false );

		$markup = ob_get_clean();

		/**
		 * @filter `gravityview/maps/infowindow/pre_html` Allow Pre filtering of the Info Window template
		 * @since  1.4
		 *
		 * @param string $markup The HTML for the markup
		 */
		$markup = apply_filters( 'gravityview/maps/infowindow/pre_html', $markup );

		// Final HTML template before replacing the entry values
		$this->template_html = gravityview_strip_whitespace( $this->replace_tags( $markup ) );

		do_action( 'gravityview_log_debug', __METHOD__ . ': Marker Info Window loaded: ', $this->template_html );
	}

	/**
	 * Replace the template placeholders by the configured content
	 *
	 * @param string $markup Template markup
	 *
	 * @return mixed|void
	 */
	function replace_tags( $markup ) {
		foreach ( $this->content as $tag => $value ) {
			$search = '[[' . $tag . ']]';

			/**
			 * `gravityview/maps/infowindow/pre_html/value/{$tag}` Allow users to filter value before replacing it on the pre template html
			 * @since 1.4
			 *
			 * @param string $value The content to be shown instead of the [[tag]] placeholder
			 */
			$value = apply_filters( 'gravityview/maps/infowindow/pre_html/value/' . $tag, $value );

			// Finally do the replace
			$markup = str_replace( $search, $value, $markup );
		}

		/**
		 * @filter `gravityview/maps/infowindow/html` Allow post filtering of the Info Window template
		 * @since  1.4
		 *
		 * @param string $markup The HTML for the info window markup
		 */
		$markup = apply_filters( 'gravityview/maps/infowindow/html', $markup );

		return $markup;
	}

	/**
	 * Fills the Infowindow pre-rendered template with the entry details
	 *
	 * @since 1.4
	 *
	 * @param GravityView_View $view      Currently-displayed View
	 * @param array            $entry     Gravity Forms entry array
	 * @param string           $entry_url URL to the current entry
	 *
	 * @return string
	 */
	function get_marker_content( $view, $entry, $entry_url = '' ) {
		$content = GravityView_Merge_Tags::replace_variables( $this->template_html, $view->getForm(), $entry );

		$backup_entry = $view->getCurrentEntry( $entry );

		/**
		 * The Javascript will be displayed after the entry loop is output; the last entry shown will be the "current" one.
		 * In order to process some GravityView shortcodes, we need to set the current data. This makes [gv_entry_link] work, for example.
		 */
		$view->setCurrentEntry( $entry );

		$content = do_shortcode( $content );

		// Replace the entry url merge tag
		$content = str_replace( '[[entry_url]]', $entry_url, $content );

		$view->setCurrentEntry( $backup_entry );

		return wpautop( $content );
	}
}
