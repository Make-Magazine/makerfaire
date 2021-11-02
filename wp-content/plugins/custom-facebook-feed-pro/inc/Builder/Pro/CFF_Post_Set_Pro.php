<?php
/**
 * Custom Facebook Feed Feed Post Set Pro
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Builder\Pro;

class CFF_Post_Set_Pro extends \CustomFacebookFeed\Builder\CFF_Post_Set {
	public static function add_general_pro_settings( $builder_settings ) {
		return $builder_settings;
	}

	public static function add_builder_pro_settings( $processed_settings ) {
		/* Feed Layout */
		$default_grid = [
			'albums',
			'videos',
			'photos',
			'singlealbum'
		];

		$single_type = count( $processed_settings['type'] ) === 1 ? $processed_settings['type'][0] : false;

		if ( $single_type ) {
			if ( in_array( $single_type, $default_grid ) ) {
				$processed_settings['feedlayout'] = 'grid';
			}
			$processed_settings['feedtype'] = $single_type;
		} else {
			if ( $processed_settings['cols'] > 1
			     || $processed_settings['colsmobile'] > 1 ) {
				$processed_settings['feedlayout'] = 'masonry';
			} else {
				$processed_settings['feedlayout'] = 'list';
			}
			$processed_settings['feedtype'] = 'timeline';
		}

		/* Lightbox */
		if ( empty( $processed_settings['disablelightbox'] ) ) {
			$processed_settings['disablelightbox'] = 'off';
		}

		$processed_settings['lightboxbgcolor'] = !empty( $options['cff_lightbox_bg_color'] ) ? $options['cff_lightbox_bg_color'] : '';
		$processed_settings['lightboxtextcolor'] = !empty( $options['cff_lightbox_text_color'] ) ? $options['cff_lightbox_text_color'] : '';
		$processed_settings['lightboxlinkcolor'] = !empty( $options['cff_lightbox_link_color'] ) ? $options['cff_lightbox_link_color'] : '';

		/* Date Range */
		$processed_settings['from'] = get_option( 'cff_date_from', '' );
		$processed_settings['until'] = get_option( 'cff_date_until', '' );

		if ( ! empty( $processed_settings['from'] ) || ! empty( $processed_settings['until'] ) ) {
			$processed_settings['daterange'] = 'on';

			$check_value = $processed_settings['from'];
			$relative = strpos( $check_value, '-' ) !== false || strpos( $check_value, '+' ) !== false || strpos( $check_value, 'now' ) !== false;
			if ( $relative ) {
				$processed_settings['daterangefromtype'] = 'relative';
				$processed_settings['daterangefromrelative'] = $processed_settings['from'];
			} else {
				$processed_settings['from'] = date( 'Y-m-d', strtotime( $processed_settings['from'] ) );
				$processed_settings['daterangefromtype'] = 'specific';
				$processed_settings['daterangefromspecific'] = $processed_settings['from'];
			}

			$check_value = $processed_settings['until'];
			$relative = strpos( $check_value, '-' ) !== false || strpos( $check_value, '+' ) !== false || strpos( $check_value, 'now' ) !== false;
			if ( $relative ) {
				$processed_settings['daterangeuntiltype'] = 'relative';
				$processed_settings['daterangeuntilrelative'] = $processed_settings['until'];
			} else {
				$processed_settings['until'] = date( 'Y-m-d', strtotime( $processed_settings['until'] ) );
				$processed_settings['daterangeuntiltype'] = 'specific';
				$processed_settings['daterangeuntilspecific'] = $processed_settings['until'];
			}
		}

		/* Comments */
		$processed_settings['hidecommentimages'] = $processed_settings['hidecommentimages'] === 'false' || $processed_settings['hidecommentimages'] === false ? '' : 'on';

		return $processed_settings;
	}

	public static function add_pro_settings_with_multiple( $settings_with_multiple ) {
		$settings_with_multiple[] = 'filter';
		$settings_with_multiple[] = 'exfilter';

		return $settings_with_multiple;
	}
}