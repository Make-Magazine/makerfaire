<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SW_Display_Elements
{
	/**
	 * Creates a style attribute that contains all of the styles for
	 * the main feed div.
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function get_feed_style( $settings ) {

		$styles = '';

		if ( ! empty( $settings['itemspacing'] )
		     || ! empty( $settings['width'] )
		     || ! empty( $settings['height'] ) ) {
			$styles = ' style="';
			if ( ! empty( $settings['itemspacing'] ) ) {
				$styles .= 'padding-bottom: ' . ((int)$settings['itemspacing'] * 2) . esc_attr( $settings['itemspacingunit'] ) . ';';
			}
			if ( ! empty( $settings['width'] ) ) {
				$styles .= 'width: ' . (int)$settings['width'] . esc_attr( $settings['widthunit'] ) . ';';
			}
			if ( ! empty( $settings['height'] ) ) {
				$styles .= 'height: ' . (int)$settings['height'] . esc_attr( $settings['heightunit'] ) . ';';
			}
			if ( ! empty( $settings['contenttextsize'] ) && $settings['contenttextsize'] !== 'inherit' ) {
				$styles .= 'font-size: ' . esc_attr( $settings['contenttextsize'] ) . ';';
			}
			//contenttextsize
			$styles .= '"';
		}
		return $styles;
	}

	/**
	 * @param $settings
	 *
	 * @return string
	 */
	public static function get_sb_items_style( $settings ) {
		$styles = '';

		if ( ! empty ( $settings['itemspacing'] ) && $settings['itemspacing'] . $settings['itemspacingunit'] !== '9px' ) {
			$styles = ' style="';
			if ( ! empty ( $settings['itemspacing'] ) && $settings['itemspacing'] . $settings['itemspacingunit'] !== '9px' ) {
				if ( $settings['layout'] !== 'list' ) {
					$styles .= 'padding-right: '.(int)$settings['itemspacing'] . esc_attr( $settings['itemspacingunit'] ) . ';padding-left: '.(int)$settings['itemspacing'] . esc_attr( $settings['itemspacingunit'] ) . ';margin-bottom: '.(int)$settings['itemspacing'] * 2 . esc_attr( $settings['itemspacingunit'] ) . ';';
				} else {
					$styles .= 'margin-bottom: '.(int)$settings['itemspacing'] * 2 . esc_attr( $settings['itemspacingunit'] ) . ';';
				}
			}
			$styles .= '"';
		}
		return $styles;
	}

	public static function get_sb_inner_item_style( $settings ) {
		$styles = '';
		$bg_color = str_replace( '#', '', $settings['background'] );

		if ( ! empty( $bg_color ) ) {
			$styles = ' style="';

			if ( ! empty ( $bg_color ) ) {
				$styles .= 'background-color: rgb(' . esc_attr( sbsw_hextorgb( $bg_color ) ). ');';
			}
			$styles .= '"';
		}
		return $styles;
	}

	public static function get_item_header_style( $settings ) {
		$styles = '';
		$bg_color = str_replace( '#', '', $settings['background'] );

		if ( ! empty( $bg_color ) ) {
			$styles = ' style="';

			if ( ! empty ( $bg_color ) ) {
				$styles .= 'background-color: rgb(' . esc_attr( sbsw_hextorgb( $bg_color ) ). ');';
			}
			$styles .= '"';
		}
		return $styles;
	}

	public static function date_format_setting( $settings ) {

		if ( empty( $settings['customdate'] ) ) {
			$date_format = get_option( 'date_format' );
			if ( ! $date_format ) {
				$date_format = 'F j, Y';
			}
			return $date_format;
		}
		return $settings['customdate'];
	}

	public static function get_avatar_class( $avatar ) {
		if ( empty( $avatar ) ) {
			return ' sbsw-no-avatar';
		}
		return '';
	}

	public static function display_date( $post, $plugin, $settings ) {
		if ( $plugin === 'facebook' && CustomFacebookFeed\CFF_Parse_Pro::get_status_type( $post ) === 'event' ) {
			return '';
		}

		return SW_Display_Elements::format_date( SW_Parse::get_timestamp( $post, $plugin ), $settings );
	}

	public static function full_date( $timestamp, $settings ) {
		return date_i18n( SW_Display_Elements::date_format_setting( $settings ), $timestamp + sbsw_get_utc_offset() );
	}

	public static function format_date( $timestamp, $settings ) {
		$use_custom = isset( $settings['dateformat'] ) ? $settings['dateformat'] === 'custom' : false;

		if ( $use_custom ) {
			return SW_Display_Elements::full_date( $timestamp, $settings );
		} else {
			$now                 = time();
			$difference          = $now - $timestamp;
			// future date, is a live stream
			if ( $difference < HOUR_IN_SECONDS ) {
				$num_text  = floor( $difference / 60 );

				return max( 1, $num_text ) . __( $settings['minutetext'], 'social-wall' );
			} elseif ( $difference < 1 * DAY_IN_SECONDS ) {
				$num_text  = floor( $difference / HOUR_IN_SECONDS );

				return max( 1, $num_text ) . __( $settings['hourtext'], 'social-wall' );
			} elseif ( $difference < WEEK_IN_SECONDS ) {
				$num_text  = floor( $difference / DAY_IN_SECONDS );

				return max( 1, $num_text ) . __( $settings['daytext'], 'social-wall' );
			} elseif ( $difference < MONTH_IN_SECONDS ) {
				$num_text  = floor( $difference / WEEK_IN_SECONDS );

				return max( 1, $num_text ) . __( $settings['weektext'], 'social-wall' );
			} elseif ( $difference < YEAR_IN_SECONDS ) {
				$num_text  = floor( $difference / MONTH_IN_SECONDS );

				return max( 1, $num_text ) . __( $settings['monthtext'], 'social-wall' );
			}
			$num_text  = floor( $difference / YEAR_IN_SECONDS );

			return max( 1, $num_text ) . __( $settings['yeartext'], 'social-wall' );
		}

	}

	public static function get_icon( $icon ) {
		if ( $icon === 'instagram' ) {
			return '<svg class="svg-inline--fa fa-instagram fa-w-14" aria-hidden="true" data-fa-processed="" aria-label="Instagram" data-prefix="fab" data-icon="instagram" role="img" viewBox="0 0 448 512">
	                <path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path>
	            </svg>';
		} elseif ( $icon === 'facebook' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="facebook" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-facebook fa-w-16"><path fill="currentColor" d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z" class=""></path></svg>';
		} elseif ( $icon === 'youtube' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="youtube" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="sby_new_logo svg-inline--fa fa-youtube fa-w-18"><path fill="currentColor" d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" class=""></path></svg>';
		} elseif ( $icon === 'twitter' ) {
			return '<svg class="svg-inline--fa fa-twitter fa-w-16" aria-hidden="true" aria-label="twitter logo" data-fa-processed="" data-prefix="fab" data-icon="twitter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>';
		} elseif ( $icon === 'share' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="share" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-share fa-w-18"><path fill="currentColor" d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z" class=""></path></svg>';
		} elseif ( $icon === 'link' ) {
			return '<svg viewBox="0 0 24 24"><g><path d="M11.96 14.945c-.067 0-.136-.01-.203-.027-1.13-.318-2.097-.986-2.795-1.932-.832-1.125-1.176-2.508-.968-3.893s.942-2.605 2.068-3.438l3.53-2.608c2.322-1.716 5.61-1.224 7.33 1.1.83 1.127 1.175 2.51.967 3.895s-.943 2.605-2.07 3.438l-1.48 1.094c-.333.246-.804.175-1.05-.158-.246-.334-.176-.804.158-1.05l1.48-1.095c.803-.592 1.327-1.463 1.476-2.45.148-.988-.098-1.975-.69-2.778-1.225-1.656-3.572-2.01-5.23-.784l-3.53 2.608c-.802.593-1.326 1.464-1.475 2.45-.15.99.097 1.975.69 2.778.498.675 1.187 1.15 1.992 1.377.4.114.633.528.52.928-.092.33-.394.547-.722.547z"></path><path d="M7.27 22.054c-1.61 0-3.197-.735-4.225-2.125-.832-1.127-1.176-2.51-.968-3.894s.943-2.605 2.07-3.438l1.478-1.094c.334-.245.805-.175 1.05.158s.177.804-.157 1.05l-1.48 1.095c-.803.593-1.326 1.464-1.475 2.45-.148.99.097 1.975.69 2.778 1.225 1.657 3.57 2.01 5.23.785l3.528-2.608c1.658-1.225 2.01-3.57.785-5.23-.498-.674-1.187-1.15-1.992-1.376-.4-.113-.633-.527-.52-.927.112-.4.528-.63.926-.522 1.13.318 2.096.986 2.794 1.932 1.717 2.324 1.224 5.612-1.1 7.33l-3.53 2.608c-.933.693-2.023 1.026-3.105 1.026z"></path></g></svg>';
		} elseif ( $icon === 'comments' ) {
			return '<svg viewBox="0 0 24 24" aria-label="reply" role="img" xmlns="http://www.w3.org/2000/svg"><g><path fill="currentColor" d="M14.046 2.242l-4.148-.01h-.002c-4.374 0-7.8 3.427-7.8 7.802 0 4.098 3.186 7.206 7.465 7.37v3.828c0 .108.044.286.12.403.142.225.384.347.632.347.138 0 .277-.038.402-.118.264-.168 6.473-4.14 8.088-5.506 1.902-1.61 3.04-3.97 3.043-6.312v-.017c-.006-4.367-3.43-7.787-7.8-7.788zm3.787 12.972c-1.134.96-4.862 3.405-6.772 4.643V16.67c0-.414-.335-.75-.75-.75h-.396c-3.66 0-6.318-2.476-6.318-5.886 0-3.534 2.768-6.302 6.3-6.302l4.147.01h.002c3.532 0 6.3 2.766 6.302 6.296-.003 1.91-.942 3.844-2.514 5.176z"></path></g></svg>';
		} elseif ( $icon === 'retweet' ) {
			return '<svg viewBox="0 0 24 24" aria-hidden="true" aria-label="retweet" role="img"><path fill="currentColor" d="M23.77 15.67c-.292-.293-.767-.293-1.06 0l-2.22 2.22V7.65c0-2.068-1.683-3.75-3.75-3.75h-5.85c-.414 0-.75.336-.75.75s.336.75.75.75h5.85c1.24 0 2.25 1.01 2.25 2.25v10.24l-2.22-2.22c-.293-.293-.768-.293-1.06 0s-.294.768 0 1.06l3.5 3.5c.145.147.337.22.53.22s.383-.072.53-.22l3.5-3.5c.294-.292.294-.767 0-1.06zm-10.66 3.28H7.26c-1.24 0-2.25-1.01-2.25-2.25V6.46l2.22 2.22c.148.147.34.22.532.22s.384-.073.53-.22c.293-.293.293-.768 0-1.06l-3.5-3.5c-.293-.294-.768-.294-1.06 0l-3.5 3.5c-.294.292-.294.767 0 1.06s.767.293 1.06 0l2.22-2.22V16.7c0 2.068 1.683 3.75 3.75 3.75h5.85c.414 0 .75-.336.75-.75s-.337-.75-.75-.75z"></path></svg>';
		} elseif ( $icon === 'heart' ) {
			return '<svg viewBox="0 0 24 24" aria-hidden="true" aria-label="like" role="img" xmlns="http://www.w3.org/2000/svg"><g><path fill="currentColor" d="M12 21.638h-.014C9.403 21.59 1.95 14.856 1.95 8.478c0-3.064 2.525-5.754 5.403-5.754 2.29 0 3.83 1.58 4.646 2.73.814-1.148 2.354-2.73 4.645-2.73 2.88 0 5.404 2.69 5.404 5.755 0 6.376-7.454 13.11-10.037 13.157H12zM7.354 4.225c-2.08 0-3.903 1.988-3.903 4.255 0 5.74 7.034 11.596 8.55 11.658 1.518-.062 8.55-5.917 8.55-11.658 0-2.267-1.823-4.255-3.903-4.255-2.528 0-3.94 2.936-3.952 2.965-.23.562-1.156.562-1.387 0-.014-.03-1.425-2.965-3.954-2.965z"></path></g></svg>';
		} elseif ( $icon === 'views' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-eye fa-w-18"><path fill="currentColor" d="M288 288a64 64 0 0 0 0-128c-1 0-1.88.24-2.85.29a47.5 47.5 0 0 1-60.86 60.86c0 1-.29 1.88-.29 2.85a64 64 0 0 0 64 64zm284.52-46.6C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 96a128 128 0 1 1-128 128A128.14 128.14 0 0 1 288 96zm0 320c-107.36 0-205.46-61.31-256-160a294.78 294.78 0 0 1 129.78-129.33C140.91 153.69 128 187.17 128 224a160 160 0 0 0 320 0c0-36.83-12.91-70.31-33.78-97.33A294.78 294.78 0 0 1 544 256c-50.53 98.69-148.64 160-256 160z" class=""></path></svg>';
		} elseif ( $icon === 'play' ) {
			return '<svg style="color: rgba(255,255,255,1)" class="svg-inline--fa fa-play fa-w-14 sbsw-play-button" aria-label="Play" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="play" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg>';
		} elseif ( $icon === 'user' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-user fa-w-14"><path fill="currentColor" d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z" class=""></path></svg>';
		} elseif ( $icon === 'image' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-image fa-w-16"><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 336H54a6 6 0 0 1-6-6V118a6 6 0 0 1 6-6h404a6 6 0 0 1 6 6v276a6 6 0 0 1-6 6zM128 152c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40zM96 352h320v-80l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L192 304l-39.515-39.515c-4.686-4.686-12.284-4.686-16.971 0L96 304v48z" class=""></path></svg>';
		} elseif ( $icon === 'video' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-video fa-w-18"><path fill="currentColor" d="M543.9 96c-6.2 0-12.5 1.8-18.2 5.7L416 171.6v-59.8c0-26.4-23.2-47.8-51.8-47.8H51.8C23.2 64 0 85.4 0 111.8v288.4C0 426.6 23.2 448 51.8 448h312.4c28.6 0 51.8-21.4 51.8-47.8v-59.8l109.6 69.9c5.7 4 12.1 5.7 18.2 5.7 16.6 0 32.1-13 32.1-31.5v-257c.1-18.5-15.4-31.5-32-31.5zM384 400.2c0 8.6-9.1 15.8-19.8 15.8H51.8c-10.7 0-19.8-7.2-19.8-15.8V111.8c0-8.6 9.1-15.8 19.8-15.8h312.4c10.7 0 19.8 7.2 19.8 15.8v288.4zm160-15.7l-1.2-1.3L416 302.4v-92.9L544 128v256.5z" class=""></path></svg>';
		} elseif ( $icon === 'like' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="thumbs-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-thumbs-up fa-w-16"><path fill="currentColor" d="M496.656 285.683C506.583 272.809 512 256 512 235.468c-.001-37.674-32.073-72.571-72.727-72.571h-70.15c8.72-17.368 20.695-38.911 20.695-69.817C389.819 34.672 366.518 0 306.91 0c-29.995 0-41.126 37.918-46.829 67.228-3.407 17.511-6.626 34.052-16.525 43.951C219.986 134.75 184 192 162.382 203.625c-2.189.922-4.986 1.648-8.032 2.223C148.577 197.484 138.931 192 128 192H32c-17.673 0-32 14.327-32 32v256c0 17.673 14.327 32 32 32h96c17.673 0 32-14.327 32-32v-8.74c32.495 0 100.687 40.747 177.455 40.726 5.505.003 37.65.03 41.013 0 59.282.014 92.255-35.887 90.335-89.793 15.127-17.727 22.539-43.337 18.225-67.105 12.456-19.526 15.126-47.07 9.628-69.405zM32 480V224h96v256H32zm424.017-203.648C472 288 472 336 450.41 347.017c13.522 22.76 1.352 53.216-15.015 61.996 8.293 52.54-18.961 70.606-57.212 70.974-3.312.03-37.247 0-40.727 0-72.929 0-134.742-40.727-177.455-40.727V235.625c37.708 0 72.305-67.939 106.183-101.818 30.545-30.545 20.363-81.454 40.727-101.817 50.909 0 50.909 35.517 50.909 61.091 0 42.189-30.545 61.09-30.545 101.817h111.999c22.73 0 40.627 20.364 40.727 40.727.099 20.363-8.001 36.375-23.984 40.727zM104 432c0 13.255-10.745 24-24 24s-24-10.745-24-24 10.745-24 24-24 24 10.745 24 24z" class=""></path></svg>';
		} elseif ( $icon === 'close' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-times fa-w-10"><path fill="currentColor" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z" class=""></path></svg>';
		} elseif ( $icon === 'minus' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="minus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-minus fa-w-14 fa-2x"><path fill="currentColor" d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z" class=""></path></svg>';
		} elseif ( $icon === 'add' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-plus fa-w-14 fa-2x"><path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z" class=""></path></svg>';
		} elseif ( $icon === 'yes' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-check-square fa-w-14 fa-2x"><path fill="currentColor" d="M400 480H48c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h352c26.51 0 48 21.49 48 48v352c0 26.51-21.49 48-48 48zm-204.686-98.059l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.248-16.379-6.249-22.628 0L184 302.745l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.25 16.379 6.25 22.628.001z" class=""></path></svg>';
		} elseif ( $icon === 'no' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-times-square fa-w-14 fa-2x"><path fill="currentColor" d="M400 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm-54.4 289.1c4.7 4.7 4.7 12.3 0 17L306 377.6c-4.7 4.7-12.3 4.7-17 0L224 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L102.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L280 256l65.6 65.1z" class=""></path></svg>';
		} elseif ( $icon === 'linkthick' ) {
			return '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="link" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-link fa-w-16 fa-2x"><path fill="currentColor" d="M326.612 185.391c59.747 59.809 58.927 155.698.36 214.59-.11.12-.24.25-.36.37l-67.2 67.2c-59.27 59.27-155.699 59.262-214.96 0-59.27-59.26-59.27-155.7 0-214.96l37.106-37.106c9.84-9.84 26.786-3.3 27.294 10.606.648 17.722 3.826 35.527 9.69 52.721 1.986 5.822.567 12.262-3.783 16.612l-13.087 13.087c-28.026 28.026-28.905 73.66-1.155 101.96 28.024 28.579 74.086 28.749 102.325.51l67.2-67.19c28.191-28.191 28.073-73.757 0-101.83-3.701-3.694-7.429-6.564-10.341-8.569a16.037 16.037 0 0 1-6.947-12.606c-.396-10.567 3.348-21.456 11.698-29.806l21.054-21.055c5.521-5.521 14.182-6.199 20.584-1.731a152.482 152.482 0 0 1 20.522 17.197zM467.547 44.449c-59.261-59.262-155.69-59.27-214.96 0l-67.2 67.2c-.12.12-.25.25-.36.37-58.566 58.892-59.387 154.781.36 214.59a152.454 152.454 0 0 0 20.521 17.196c6.402 4.468 15.064 3.789 20.584-1.731l21.054-21.055c8.35-8.35 12.094-19.239 11.698-29.806a16.037 16.037 0 0 0-6.947-12.606c-2.912-2.005-6.64-4.875-10.341-8.569-28.073-28.073-28.191-73.639 0-101.83l67.2-67.19c28.239-28.239 74.3-28.069 102.325.51 27.75 28.3 26.872 73.934-1.155 101.96l-13.087 13.087c-4.35 4.35-5.769 10.79-3.783 16.612 5.864 17.194 9.042 34.999 9.69 52.721.509 13.906 17.454 20.446 27.294 10.606l37.106-37.106c59.271-59.259 59.271-155.699.001-214.959z" class=""></path></svg>';
		}
	}

	public static function get_date( $post, $plugin = '' ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}
		if ( $plugin === 'youtube' ) {
			$timestamp = SBY_Parse_Pro::get_timestamp( $post );
		} elseif ( $plugin === 'instagram' ) {
			$timestamp = SB_Instagram_Parse_Pro::get_timestamp( $post );
		} elseif ( $plugin === 'facebook' ) {
			$timestamp = CustomFacebookFeed\CFF_Parse_Pro::get_timestamp( $post );
		} elseif ( $plugin === 'twitter' ) {
			$timestamp = CTF_Parse_Pro::get_original_post_timestamp( $post );
		} else {
			$timestamp = time();

		}


		return date_i18n( sbsw_get_date_format(), $timestamp );
	}


	public static function get_media_placeholder( $post, $settings = array( 'disable_js_image_loading' => false ), $plugin = '' ) {
		$thumbnail = SW_Parse::get_media_thumbnail( $post, $settings, $plugin );
		if ( empty( $thumbnail ) ) {
			return '';
		}
		if ( ! $settings['disable_js_image_loading'] ) {
			return trailingslashit( SBSW_PLUGIN_URL ) . 'img/placeholder.png';
		}
		return $thumbnail;
	}

	public static function get_media_html( $post, $settings, $plugin = '' ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}
		if ( $plugin === 'facebook' ) {
			if ( CustomFacebookFeed\CFF_Parse_Pro::get_status_type( $post ) === 'shared_story' ) {
				return '';
			}
			$media_thumbnail = SW_Display_Elements::get_media_placeholder( $post, $settings, $plugin );
			if ( empty( $media_thumbnail ) ) {
				return '';
			}
			$description = SW_Parse::get_description( $post, $plugin );
			$media_alt = $description;

			return '<img src="' . $media_thumbnail . '" alt="' . esc_attr( $media_alt ) . '">';
		} else {
			$media_thumbnail = SW_Display_Elements::get_media_placeholder( $post, $settings, $plugin );
			if ( empty( $media_thumbnail ) ) {
				return '';
			}
			$description = SW_Parse::get_description( $post, $plugin );
			$media_alt = $description;

			return '<img src="' . $media_thumbnail . '" alt="' . esc_attr( $media_alt ) . '">';
		}
	}

	public static function get_facebook_event_content( $post, $plugin = '', $settings = array() ) {
		$content = '';
		$title = sbsw_esc_html_with_br( CustomFacebookFeed\CFF_Parse_Pro::get_item_title( $post ) );
		$message = CustomFacebookFeed\CFF_Parse_Pro::get_message( $post );

		if ( ! empty( $title ) ) {
			$content = $title;

			$content .= '<br><br>';
		}

		$start_time = CustomFacebookFeed\CFF_Parse_Pro::get_event_start_time( $post );
		$end_time = CustomFacebookFeed\CFF_Parse_Pro::get_event_end_time( $post );
		$location_name = CustomFacebookFeed\CFF_Parse_Pro::get_event_location_name( $post );
		$street = CustomFacebookFeed\CFF_Parse_Pro::get_event_street( $post );
		$city = CustomFacebookFeed\CFF_Parse_Pro::get_event_city( $post );
		$state = CustomFacebookFeed\CFF_Parse_Pro::get_event_state( $post );
		$zip = CustomFacebookFeed\CFF_Parse_Pro::get_event_zip( $post );
		$style_options = get_option( 'cff_style_settings', array() );
		$cff_event_date_formatting = isset( $style_options[ 'cff_event_date_formatting' ] ) ? $style_options[ 'cff_event_date_formatting' ] : '';
		$cff_event_date_custom = isset( $style_options[ 'cff_event_date_custom' ] ) ? $style_options[ 'cff_event_date_custom' ] : '';
		$cff_event_timezone_offset = '';

		if ( ! empty( $start_time ) || ! empty ( $end_time ) ) {
			$formatted_start_time = str_replace( array( '<k>','</k>' ), '', CustomFacebookFeed\CFF_Utils::cff_eventdate($start_time, $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset) );
			$content .= sbsw_esc_html_with_br( $formatted_start_time );

			if ( ! empty ( $end_time ) ) {
				$formatted_end_time = str_replace( array( '<k>','</k>' ), '', CustomFacebookFeed\CFF_Utils::cff_eventdate($end_time, $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset) );
				$content .= ' - ' . sbsw_esc_html_with_br( $formatted_end_time );
			}
		}

		if ( ! empty( $location_name )
		     || ! empty( $street )
		     || ! empty( $city )
		     || ! empty( $state )
		     || ! empty( $zip ) ) {
			$content .= '<br><br>';
		}

		if ( ! empty( $location_name ) ) {
			$content .= $location_name;
			if ( ! empty( $street )
			     || ! empty( $city )
			     || ! empty( $state )
			     || ! empty( $zip ) ) {
				$content .= '<br>';
			}
		}

		if ( ! empty( $street )
		     || ! empty( $city )
		     || ! empty( $state )
		     || ! empty( $zip ) ) {
			if ( ! empty ( $street ) ) {
				$content .= sbsw_esc_html_with_br( $street ) . '<br>';
			}
			if ( ! empty ( $city ) ) {
				$content .= sbsw_esc_html_with_br( $city );
				if ( ! empty ( $state ) ) {
					$content .= ', ' . sbsw_esc_html_with_br( $state );
				}
				if ( ! empty ( $zip ) ) {
					$content .= ' ' . sbsw_esc_html_with_br( $zip );
				}
			} else {
				if ( ! empty ( $state ) ) {
					$content .= sbsw_esc_html_with_br( $state );
					if ( ! empty ( $zip ) ) {
						$content .= ' ' . sbsw_esc_html_with_br( $zip );
					}
				} elseif ( ! empty ( $zip ) ) {
					$content .= sbsw_esc_html_with_br( $zip );
				}
			}
		}

		if ( ! empty( $message ) ) {
			$content .= '<br><br>';
		}

		return $content;
	}

	public static function get_escaped_bottom_content( $post, $plugin = '', $settings = array() ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}

		$content = '';
		$textlength = (int)$settings['textlength'];

		if ( $plugin === 'youtube' ) {
			$sbsw_caption = SBY_Parse_Pro::get_video_title( $post, '' );
			if( !empty($sbsw_caption) ){
				$content = '<p class="sbsw-content-text">';
				$content .= sbsw_maybe_shorten_text( sbsw_esc_html_with_br( $sbsw_caption ), $textlength, true );
				$content .= '</p>';
			}
		} elseif ( $plugin === 'instagram' ) {
			$sbsw_caption = SB_Instagram_Parse_Pro::get_caption( $post, '' );
			if( !empty($sbsw_caption) ){
				$content = '<p class="sbsw-content-text">';
				$content .= sbsw_maybe_shorten_text( sbsw_esc_html_with_br( $sbsw_caption ), $textlength, true );
				$content .= '</p>';
			}
		} elseif ( $plugin === 'facebook' ) {
			$status_type = CustomFacebookFeed\CFF_Parse_Pro::get_status_type( $post );
			$message = sbsw_esc_html_with_br( CustomFacebookFeed\CFF_Parse_Pro::get_message( $post ) );

			if ( $status_type === 'album' ) {
				$title = sbsw_esc_html_with_br( CustomFacebookFeed\CFF_Parse_Pro::get_item_title( $post ) );
				if ( ! empty( $title ) ) {
					$content .= $title;

					if ( ! empty ( $message ) ) {
						$content .= '<br><br>';
					}
				}
			}

			if ( $status_type === 'event' ) {
				$content .= SW_Display_Elements::get_facebook_event_content( $post, $plugin = '', $settings = array() );
			}

			$content .= $message;

			if ( ! empty( trim( $content ) ) ) {
				$content = '<p class="sbsw-content-text">' . sbsw_maybe_shorten_text( $content, $textlength, true ) . '</p>';
			}

			if ( $status_type === 'shared_story' ) {
				$attachment_data = CustomFacebookFeed\CFF_Parse_Pro::get_attachments( $post );

				$content .= SW_Display_Elements::get_shared_story_html( $attachment_data[0] );
			}

		} elseif ( $plugin === 'twitter' ) {
			$has_card = CTF_Parse_Pro::has_twitter_card( $post );
			$card_not_empty = CTF_Parse_Pro::twitter_card_not_empty( $post );
			$first_url = CTF_Parse_Pro::get_twitter_card_url( $post );
			$content = sbsw_esc_html_with_br( CTF_Parse_Pro::get_tweet_content( $post, true, $has_card ) );

			if ( ! empty( trim( $content ) ) )  {
				$content = '<p class="sbsw-content-text">' . sbsw_maybe_shorten_text( $content, $textlength, true ) . '</p>';
			}

			if ( ! empty( CTF_Parse_Pro::get_quoted_user_name( $post ) ) ) {
				$media_url = CTF_Parse_Pro::get_quoted_media_url( $post );

				$content .= '<div class="sbsw-quote-or-card sbsw-quoted-tweet">';
				$content .= '<div class="sbsw-quoted-identity">';
				$content .= '<span class="sbsw-quoted-avatar"><img src="'.CTF_Parse_Pro::get_quoted_avatar( $post ) . '" alt="@'.esc_attr( CTF_Parse_Pro::get_quoted_user_name( $post ) ).'"></span>';
				$content .= '<div class="sbsw-quoted-name-date">';
				$content .= '<a href="https://twitter.com/' . CTF_Parse_Pro::get_quoted_user_name( $post ) .'" target="_blank" rel="noopener noreferrer nofollow" class="sbsw-quoted-screenname">@'.esc_html( CTF_Parse_Pro::get_quoted_user_name( $post ) ).'</a>';
				$content .= '<span class="sbsw-quoted-date">'.esc_html( SW_Display_Elements::format_date( CTF_Parse_Pro::get_quoted_timestamp( $post ), $settings ) ) . '</span>';
				$content .= '</div>';
				$content .= '</div>';
				$content .= '<p class="sbsw-quoted-tweet-text">'.sbsw_esc_html_with_br( CTF_Parse_Pro::get_quoted_text( $post ) ).'</p>';
				if ( ! empty( $media_url ) ) {
					$quoted_post = CTF_Parse_Pro::get_quoted_post( $post );
					$media_type = SW_Parse::get_media_type( $quoted_post, $plugin );
					$maybe_play_button_html = SW_Display_Elements::maybe_play_button_html( $media_type );

					$content .= '<div class="sbsw-quoted-media">';
					$content .= $maybe_play_button_html;
					$content .= '<img src="'.esc_url( $media_url ).'" alt="' . esc_attr( CTF_Parse_Pro::get_quoted_text( $post ) ) .'">';
					$lightbox_attribute = SW_Display_Elements::get_lightbox_attributes( array(), $quoted_post, $plugin );
					$content .= '<a href="' . esc_attr( $media_url ) . '" class="sbsw-lightbox-hover"' . $lightbox_attribute . '><span class="sbsw-screenreader">' . esc_html( sprintf( __( 'Lightbox link for post with description %s', 'social-wall' ), sbsw_maybe_shorten_text( CTF_Parse_Pro::get_quoted_text( $post ), 50 ) ) ) . '</span></a>';

                    $content .= '</div>';
				}
				$content .= '</div>';
			} elseif ( $has_card ) {
				if ( $card_not_empty ) {
					$content .= SW_Display_Elements::get_twitter_card_html( $post );
				}
			} elseif ( $first_url && CTF_Parse_Pro::should_retrieve_twitter_card( $post ) ) {
				$content .= CTF_Display_Elements_Pro::get_twitter_card_placeholder( $post, $first_url );
			}
		}

		return $content;
	}

	public static function get_twitter_card_html( $data ) {
		$url = CTF_Parse_Pro::get_twitter_card_url( $data );

		$twitter_card = isset( $data['twitter_card'] ) ? $data['twitter_card'] : $data;
		$image = CTF_Display_Elements_Pro::get_twitter_card_media_html( $twitter_card );
		$title = CTF_Parse_Pro::get_twitter_card_title( $data );
		$description = CTF_Parse_Pro::get_twitter_card_description( $data );
		$link_html = CTF_Display_Elements_Pro::get_icon( 'link' ) . CTF_Display_Elements_Pro::get_twitter_card_link_text( $data );

		$parts = array(
			'url' => $url,
			'image' => $image,
			'title' => $title,
			'description' => sbsw_maybe_shorten_text( $description, 150 ),
			'link_html' => $link_html
		);
		$content = SW_Display_Elements::get_boxed_content_html( $parts );

		return $content;
	}

	public static function get_shared_story_html( $data ) {
		$url = CustomFacebookFeed\CFF_Parse_Pro::get_attachment_unshimmed_url( $data );

		$alt   = __( 'Image for shared link', 'social-wall' );
		if ( empty( CustomFacebookFeed\CFF_Parse_Pro::get_sub_attachments( $data ) ) ) {
			$image_url = CustomFacebookFeed\CFF_Parse_Pro::get_attachment_image( $data );
			$image = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt ) . '">';
		} else {
			$sub_attachments = CustomFacebookFeed\CFF_Parse_Pro::get_sub_attachments( $data );
			$image_url = CustomFacebookFeed\CFF_Parse_Pro::get_attachment_image( $sub_attachments[0] );
			$image = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt ) . '">';
		}

		$title = CustomFacebookFeed\CFF_Parse_Pro::get_attachment_title( $data );
		$description = CustomFacebookFeed\CFF_Parse_Pro::get_attachment_description( $data );
		$link_html = sbsw_maybe_shorten_link_text( $url );

		$parts = array(
			'url' => $url,
			'image' => $image,
			'title' => $title,
			'description' => sbsw_maybe_shorten_text( $description, 150 ),
			'link_html' => $link_html
		);
		$content = SW_Display_Elements::get_boxed_content_html( $parts );

		return $content;
	}

	public static function get_boxed_content_html( $parts ) {
		$content = '<a href="' . esc_url( $parts['url'] ) .'" target="_blank" rel="noopener noreferrer nofollow" class="sbsw-twitter-card">';
		$content .= '<div class="sbsw-quote-or-card">';

			$content .= '<div class="sbsw-tc-media">';
				$content .= '<span class="sbsw-tc-link-hover">'.SW_Display_Elements::get_icon( 'linkthick' ).'</span>';
				$content .= $parts['image'];
			$content .= '</div>';

			$content .= '<div class="sbsw-tc-content">';
				$content .= '<span class="sbsw-tc-title">';
				$content .= $parts['title'];
				$content .= '</span>';

				$content .= '<p class="sbsw-tc-description">';
				$content .= $parts['description'];
				$content .= '</p>';

				$content .= '<span class="sbsw-tc-link">';
				$content .= $parts['link_html'];
				$content .= '</span>';
			$content .= '</div>';

		$content .= '</div>';
		$content .= '</a>';

		return $content;
	}

	public static function get_identity_text( $data, $post, $plugin = '' ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}

		$identifier = SW_Parse::get_account_identifier( $post, $plugin );
		$name = '';
		if ( $plugin === 'youtube' ) {
			if ( isset( $data[ $plugin ][ $identifier ] ) ) {
				$name = SBY_Parse_Pro::get_channel_title( $data[ $plugin ][ $identifier ] );
			}
		} elseif ( $plugin === 'instagram' ) {

			if ( isset( $data[ $plugin ][ $identifier ] ) ) {
				$name = SB_Instagram_Parse_Pro::get_name( $data[ $plugin ][ $identifier ] );
			} elseif ( isset( $post['term'] ) ) {
				$name = $post['term'];
			} elseif ( isset( $data[ $plugin ]['hashtags_top'] ) ) {
				$name = $data[ $plugin ]['hashtags_top']['header_text'];
			} elseif ( isset( $data[ $plugin ]['hashtags_recent'] ) ) {
				$name = $data[ $plugin ]['hashtags_recent']['header_text'];
			} else {
				$part = '';
				foreach ( $data[ $plugin ] as $key => $data ) {
					if ( empty( $part ) ) {
						$part = $key;
					}
				}
				return $part;
			}
		} elseif ( $plugin === 'facebook' ) {
			$name = CustomFacebookFeed\CFF_Parse_Pro::get_name( $post );
		} elseif ( $plugin === 'twitter' ) {
			$name = CTF_Parse_Pro::get_name( $post );
		}

		return $name;
	}

	public static function maybe_play_button_html( $media_type ) {
		if ( $media_type === 'video'
			|| $media_type === 'iframe'
			|| $media_type === 'youtube') {
			return SW_Display_Elements::get_icon( 'play' );
		}
		return '';
	}

	public static function youtube_stats_html( $views_count, $likes_count, $comments_count, $settings ) {

		$need_counts_flag_class = ($likes_count === '' && $comments_count === '') && $settings['youtube_stats'] ? ' sbsw-need-counts' : '';

		$html = '<span class="sbsw-sby-views sbsw-views'.$need_counts_flag_class.'">' . SW_Display_Elements::get_icon( 'views' ) . sbsw_format_count( $views_count ) . '</span>';
		if ( $settings['youtube_stats'] ) {
			$html .= '<span class="sbsw-sby-likes sbsw-likes">' . SW_Display_Elements::get_icon( 'like' ) . sbsw_format_count( $likes_count ) . '</span>';
			$html .= '<span class="sbsw-sby-comments sbsw-comments">' . SW_Display_Elements::get_icon( 'comments' ) . sbsw_format_count( $comments_count ) . '</span>';
		}

		return $html;
	}

	public static function get_escaped_stats_html( $data, $post, $misc_data, $plugin = '', $settings = array() ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}

		$html = '';
		if ( $plugin === 'youtube' ) {
			$views_count = SBY_Parse_Pro::get_view_count( $post, $misc_data['youtube'] );
			$likes_count = SBY_Parse_Pro::get_like_count( $post, $misc_data['youtube'] );
			$comments_count = SBY_Parse_Pro::get_comment_count( $post, $misc_data['youtube'] );

			$html = SW_Display_Elements::youtube_stats_html( $views_count, $likes_count, $comments_count, $settings );
			// get stats
		} elseif ( $plugin === 'instagram' ) {
			$likes_count         = SB_Instagram_Parse_Pro::get_likes_count( $post );
			$comments_count      = SB_Instagram_Parse_Pro::get_comments_count( $post );

			if ( SB_Instagram_Parse_Pro::comment_or_like_counts_data_exists( $post ) ) {
				$html = '<span class="sbsw-sbi-likes sbsw-likes">' . SW_Display_Elements::get_icon( 'heart' ) . sbsw_format_count( $likes_count ) . '</span>';
				$html .= '<span class="sbsw-sbi-comments sbsw-comments">' . SW_Display_Elements::get_icon( 'comments' ) . sbsw_format_count( $comments_count ) . '</span>';
			}
		} elseif ( $plugin === 'facebook' ) {
			$status_type = CustomFacebookFeed\CFF_Parse_Pro::get_status_type( $post );
			if ( $status_type === 'album' ) {
				$count_count         = CustomFacebookFeed\CFF_Parse_Pro::get_count_count( $post );
				$html = '<span class="sbsw-cff-count sbsw-count">' . SW_Display_Elements::get_icon( 'image' ) . sbsw_format_count( $count_count ) . '</span>';

			} elseif ( $status_type === 'event' ) {
				$interested_count         = CustomFacebookFeed\CFF_Parse_Pro::get_interested_count( $post );
				$attending_count      = CustomFacebookFeed\CFF_Parse_Pro::get_attending_count( $post );

				$html = '<span class="sbsw-cff-interested sbsw-interested">' . SW_Display_Elements::get_icon( 'heart' ) . sbsw_format_count( $interested_count ) . '</span>';
				$html .= '<span class="sbsw-cff-attending sbsw-attending">' . SW_Display_Elements::get_icon( 'user' ) . sbsw_format_count( $attending_count ) . '</span>';

			} else {
				$likes_count         = CustomFacebookFeed\CFF_Parse_Pro::get_likes_count( $post );
				$comments_count      = CustomFacebookFeed\CFF_Parse_Pro::get_comments_count( $post );

				$html = '<span class="sbsw-cff-likes sbsw-likes">' . SW_Display_Elements::get_icon( 'like' ) . sbsw_format_count( $likes_count ) . '</span>';
				$html .= '<span class="sbsw-cff-comments sbsw-comments">' . SW_Display_Elements::get_icon( 'comments' ) . sbsw_format_count( $comments_count ) . '</span>';

			}
		} elseif ( $plugin === 'twitter' ) {
			$screen_name = CTF_Parse_Pro::get_handle( $post );
			$id = CTF_Parse_Pro::get_tweet_id( $post );
			$retweet_count = (int)CTF_Parse_Pro::get_retweet_count( $post );
			$favorite_count = (int)CTF_Parse_Pro::get_favorite_count( $post );
			$reply_icon = SW_Display_Elements::get_icon( 'comments' );
			$retweet_icon = SW_Display_Elements::get_icon( 'retweet' );
			$favorite_icon = SW_Display_Elements::get_icon( 'heart' );

			$html .= '<a href="https://twitter.com/intent/retweet?tweet_id=' . $id . '&related=' . $screen_name . '" class="sbsw-ctf-retweet" target="_blank">' . $retweet_icon;
			$html .= '<span class="sbsw-ctf-action-count sbsw-ctf-retweet-count">';
			if ( $retweet_count > 0 ) {
				$html .= sbsw_format_count( $retweet_count );
			}
			$html .= '</span><span class="sbsw-screenreader">Retweet on Twitter ' . $id . '</span></a>';
			$html .= '<a href="https://twitter.com/intent/like?tweet_id=' . $id . '&related=' . $screen_name . '" class="sbsw-ctf-like" target="_blank">' . $favorite_icon . '';
			$html .= '<span class="sbsw-ctf-action-count sbsw-ctf-favorite-count">';
			if ( $favorite_count > 0 ) {
				$html .= sbsw_format_count( $favorite_count );
			}
			$html .= '</span><span class="sbsw-screenreader">Like on Twitter ' . $id . '</span></a>';
		}

		return $html;
	}

	public static function get_escaped_before_identity_html( $account_data, $post, $plugin = '' ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}

		$content = '';
		if ( $plugin === 'youtube' ) {
			$content = '';
		} elseif ( $plugin === 'instagram' ) {
			$content = '';
		} elseif ( $plugin === 'facebook' ) {
			$content = '';
		} elseif ( $plugin === 'twitter' ) {
			$content = '';
			if ( isset( $post['retweeted_status'] ) ) {
				$retweet_icon = '<svg viewBox="0 0 24 24" aria-hidden="true" aria-label="retweet" role="img"><path d="M23.77 15.67c-.292-.293-.767-.293-1.06 0l-2.22 2.22V7.65c0-2.068-1.683-3.75-3.75-3.75h-5.85c-.414 0-.75.336-.75.75s.336.75.75.75h5.85c1.24 0 2.25 1.01 2.25 2.25v10.24l-2.22-2.22c-.293-.293-.768-.293-1.06 0s-.294.768 0 1.06l3.5 3.5c.145.147.337.22.53.22s.383-.072.53-.22l3.5-3.5c.294-.292.294-.767 0-1.06zm-10.66 3.28H7.26c-1.24 0-2.25-1.01-2.25-2.25V6.46l2.22 2.22c.148.147.34.22.532.22s.384-.073.53-.22c.293-.293.293-.768 0-1.06l-3.5-3.5c-.293-.294-.768-.294-1.06 0l-3.5 3.5c-.294.292-.294.767 0 1.06s.767.293 1.06 0l2.22-2.22V16.7c0 2.068 1.683 3.75 3.75 3.75h5.85c.414 0 .75-.336.75-.75s-.337-.75-.75-.75z"></path></svg>';
				$content      = '<div class="sbsw-retweeted-text"><span>' . $retweet_icon . '</span><a href="https://twitter.com/' . CTF_Parse_Pro::get_retweeter( $post ) .'" target="_blank" rel="noopener nofollow noreferrer"><span class="sbsw-retweeter-name">'. CTF_Parse_Pro::get_retweeter( $post ) . ' </span>' . __( 'Retweeted', 'social-wall' ) . '</a></div>';
			}
		}

		return $content;
	}

	public static function get_escaped_share_content( $account_data, $post, $plugin = '' ) {
		$share_data = array(
			'link' => esc_url( SW_Parse::get_post_permalink( $post ) )
		);
		$content = '<a class="sbsw-share-button" href="JavaScript:void(0);" data-share-data="' . esc_attr( sbsw_json_encode( $share_data ) ) . '">'.SW_Display_Elements::get_icon('share').'<span class="sbsw-screenreader">' . __('Share', 'social-wall' ) . ' ' . SW_Parse::get_post_id( $post, $plugin ) . '</span></a>';

		return $content;
	}

	public static function get_item_classes( $settings, $post ) {
		$classes = '';
		if ( !$settings['disable_js_image_loading'] ) {
			$classes = ' sbsw-new sbsw-transition';
		} else {
			$classes = ' sbsw-new sbsw-no-resraise sbsw-js-load-disabled';
		}

		if ( ! is_object( $post ) && isset( $post['retweeted_status'] ) ) {
			$classes .= ' sbsw-retweet';
		}

		return $classes;
	}

	public static function get_lightbox_attributes( $account_data, $post, $plugin = '' ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}

		$avatar = SW_Parse::get_avatar( $account_data, $post, $plugin );
		$full_name = SW_Parse::get_full_name( $account_data, $post, $plugin );
		$media_type = SW_Parse::get_media_type( $post, '', $plugin );
		if ( $media_type === 'youtube' ) {
			if ( $plugin === 'youtube' ) {
				$media_url = SBY_Parse::get_video_id( $post );
			} else {
				$media_url = '';
				// parse the video id
			}
		} elseif ( $media_type === 'video' ) {
			$media_url = SW_Parse::get_video_url( $post, $plugin );
		} elseif ( $media_type === 'iframe' ) {
			$media_url = SW_Parse::get_iframe_url( $post, $plugin );
		} else {
			$media_url = SW_Parse::get_media_thumbnail( $post, array(), $plugin );
		}
		$description = SW_Parse::get_description( $post, $plugin );
		$account_link = SW_Parse::get_account_link( $account_data, $post, $plugin );

		$data_array = array(
			'avatar' => $avatar,
			'full_name' => $full_name,
			'media_type' => $media_type,
			'media_url' => $media_url,
			'title' => sbsw_esc_html_with_br( sbsw_replace_double_quotes( $description ) ),
			'account_url' => $account_link
		);

		if ( $plugin === 'facebook' && CustomFacebookFeed\CFF_Parse_Pro::get_status_type( $post ) === 'event' ) {
			$data_array['title'] = SW_Display_Elements::get_facebook_event_content( $post, 'facebook', 'settings' );
			if ( ! empty ( $description ) ) {
				$data_array['title'] .=  sbsw_esc_html_with_br( sbsw_replace_double_quotes( $description ) );
			}
		}

		$attr = ' data-lightbox-info="' . esc_attr( json_encode( $data_array ) ) . '"';

		return $attr;
	}

	public static function get_available_images_attribute( $account_data, $post, $plugin = '', $misc_data = array() ) {
		if ( empty( $plugin ) ) {
			$plugin = SW_Parse::get_plugin( $post );
		}

		$data_array = array();
		if ( $plugin === 'youtube' ) {
			$data_array = SBY_Parse_Pro::get_media_src_set( $post );
		} elseif ( $plugin === 'instagram' ) {
			$resized = isset( $misc_data['instagram']['resized_images'] ) ? $misc_data['instagram']['resized_images'] : array();
			$data_array = SB_Instagram_Parse_Pro::get_media_src_set( $post, $resized );
		} elseif ( $plugin === 'facebook' ) {
			$data_array = CustomFacebookFeed\CFF_Parse_Pro::get_media_src_set( $post );
		} elseif ( $plugin === 'twitter' ) {
			$media = CTF_Parse_Pro::get_media_items_for_tweet( $post );
			if ( ! empty( $media ) ) {
				$data_array = CTF_Parse_Pro::get_media_src_set( $media[0] );
			}
		}

		$attr = ' data-available-images="' . esc_attr( json_encode( $data_array ) ) . '"';

		return $attr;
	}

	public static function get_follow_button_text( $plugin, $settings ) {
		if ( $plugin === 'youtube' ) {
			return __( 'Subscribe', 'social-wall' );
		}

		return __( 'Follow', 'social-wall' );
	}
}