<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SW_Settings {
	/**
	 * @var array
	 */
	protected $atts;

	/**
	 * @var array
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var array
	 */
	protected $plugin_type_and_terms;

	protected $plugin_settings;


	/**
	 * @var string
	 */
	protected $transient_name;

	/**
	 * SBY_Settings constructor.
	 *
	 * Overwritten in the Pro version.
	 *
	 * @param array $atts shortcode settings
	 * @param array $db settings from the wp_options table
	 */
	public function __construct( $atts, $db, $plugin_types_and_terms, $plugin_settings = array() ) {
		$atts = is_array( $atts ) ? $atts : array();

		// convert string 'false' and 'true' to booleans
		foreach ( $atts as $key => $value ) {
			if ( $value === 'false' ) {
				$atts[ $key ] = false;
			} elseif ( $value === 'true' ) {
				$atts[ $key ] = true;
			}
		}

		$this->atts = $atts;
		$this->db   = $db;
		$this->plugin_type_and_terms = $plugin_types_and_terms;

		$this->settings = wp_parse_args( $atts, $db );

		if ( empty( $atts['cols'] ) && $this->settings['layout'] === 'masonry' ) {
			$this->settings['cols'] = $this->settings['masonrycols'];
		}

		if ( empty( $atts['colsmobile'] ) && $this->settings['layout'] === 'masonry' ) {
			$this->settings['colsmobile'] = $this->settings['masonrycolsmobile'];
		}

		if ( empty( $atts['cols'] ) && $this->settings['layout'] === 'carousel' ) {
			$this->settings['cols'] = $this->settings['carouselcols'];
		}

		if ( empty( $atts['colsmobile'] ) && $this->settings['layout'] === 'carousel' ) {
			$this->settings['colsmobile'] = $this->settings['carouselcolsmobile'];
		}
		$this->settings['num'] = max( (int)$this->settings['num'], 0);
		$this->settings['minnum'] = max( (int)$this->settings['num'], (int)$this->settings['nummobile'] );

		if ( empty( $plugin_settings['youtube']['api_key'] ) ) {
			$this->settings['youtube_stats'] = false;
		} else {
			$this->settings['youtube_stats'] = true;
		}
		$this->plugin_settings = $plugin_settings;
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * The plugin will output settings on the frontend for debugging purposes.
	 * Safe settings to display are added here.
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public static function get_public_db_settings_keys() {
		$public = array(
		);

		return $public;
	}

	/**
	 * @return bool|string
	 *
	 * @since 1.0
	 */
	public function get_transient_name() {
		if ( isset( $this->transient_name ) ) {
			return $this->transient_name;
		} else {
			return false;
		}
	}

	/**
	 * Uses the feed types and terms as well as as some
	 * settings to create a semi-unique feed id used for
	 * caching and other features.
	 *
	 * @param string $transient_name
	 *
	 * @since 1.0
	 */
	public function set_transient_name( $transient_name = '' ) {

		if ( ! empty( $transient_name ) ) {
			$this->transient_name = $transient_name;
		} elseif ( ! empty( $this->settings['feedid'] ) ) {
			$this->transient_name = 'sbsw_' . $this->settings['feedid'];
		} else {
			$plugin_type_and_terms = $this->plugin_type_and_terms;

			$transient_name = 'sbsw_';

			$plugins_included_string = '';
			$term_included_string = '';

			foreach ( $plugin_type_and_terms as $plugin => $feed_type_and_terms ) {
				if ( $plugin === 'instagram' ) {
					$plugins_included_string .= 'i';
					$this_term_included_string = '';
					if ( isset( $feed_type_and_terms['users'] ) ) {
						foreach ( $feed_type_and_terms['users'] as $term_and_params ) {
							$user = $term_and_params['term'];
							$this_term_included_string .= $user;
						}
					}

					if ( isset( $feed_type_and_terms['hashtags_top'] ) || isset( $feed_type_and_terms['hashtags_recent'] ) ) {
						if ( isset( $feed_type_and_terms['hashtags_recent'] ) ) {
							$terms_params = $feed_type_and_terms['hashtags_recent'];
						} else {
							$terms_params = $feed_type_and_terms['hashtags_top'];
							$this_term_included_string .= '+';
						}

						foreach ( $terms_params as $term_and_params ) {
							$hashtag = $term_and_params['hashtag_name'];
							$full_tag = str_replace('%','',urlencode( $hashtag ));
							$max_length = strlen( $full_tag ) < 20 ? strlen( $full_tag ) : 20;
							$this_term_included_string .= strtoupper( substr( $full_tag, 0, $max_length ) );
						}
					}

					if ( isset( $feed_type_and_terms['tagged'] ) ) {
						$this_term_included_string .= SBI_TAGGED_PREFIX;

						foreach ( $feed_type_and_terms['tagged'] as $term_and_params ) {
							$user = $term_and_params['term'];

							$this_term_included_string .= $user;
						}
					}
					$term_included_string .= strlen( $this_term_included_string ) > 8 ? substr( $this_term_included_string, 0, 8 ) : $this_term_included_string;
				} elseif ( $plugin === 'facebook' ) {
					$plugins_included_string .= 'f';
					$this_term_included_string = $feed_type_and_terms['generic'][0]['transient'];
					$term_included_string .= strlen( $this_term_included_string ) > 10 ? substr( $this_term_included_string, 0, 5 ) . substr( $this_term_included_string, -5, 5 ) : $this_term_included_string;
				} elseif ( $plugin === 'youtube' ) {
					$plugins_included_string .= 'y';
					$this_term_included_string = '';
					if ( isset( $feed_type_and_terms['channels'] ) ) {
						foreach ( $feed_type_and_terms['channels'] as $term_and_params ) {
							$channel = $term_and_params['term'];
							$this_term_included_string .= str_replace( 'UC-', '', $channel );
						}
					} elseif ( isset( $feed_type_and_terms['playlist'] ) ) {
						foreach ( $feed_type_and_terms['playlist'] as $term_and_params ) {
							$playlist = substr( $term_and_params['term'], 0, 13 );
							$this_term_included_string .= str_replace( 'PL', '', $playlist );
						}
					} elseif ( isset( $feed_type_and_terms['search'] ) ) {
						foreach ( $feed_type_and_terms['search'] as $term_and_params ) {
							$this_term_included_string .= 'Q?';
							$search = $term_and_params['term'];
							$this_term_included_string .= substr( $search, 0, 8 );
						}
					} elseif ( isset( $feed_type_and_terms['live'] ) ) {
						foreach ( $feed_type_and_terms['live'] as $term_and_params ) {
							$this_term_included_string .= $term_and_params['term'];
						}
					} elseif ( isset( $feed_type_and_terms['favorites'] ) ) {
						foreach ( $feed_type_and_terms['favorites'] as $term_and_params ) {
							$this_term_included_string .= 'F!';
							$channel = $term_and_params['term'];
							$this_term_included_string .= $channel;
						}
					} elseif ( isset( $feed_type_and_terms['single'] ) ) {
						foreach ( $feed_type_and_terms['single'] as $term_and_params ) {
							$this_term_included_string .= 'S!';
							$video = $term_and_params['term'];
							$this_term_included_string .= $video;
						}
					}
					$term_included_string .= strlen( $this_term_included_string ) > 8 ? substr( $this_term_included_string, 0, 8 ) : $this_term_included_string;
				} elseif ( $plugin === 'facebook' ) {
					$plugins_included_string .= 'f';
				} elseif ( $plugin === 'twitter' ) {
					$plugins_included_string .= 't';
					$this_term_included_string = '';
					if ( isset( $feed_type_and_terms['usertimeline'] ) ) {
						foreach ( $feed_type_and_terms['usertimeline'] as $term_and_params ) {
							$user = $term_and_params['term'];
							$this_term_included_string .= substr( $user, 0, 4 );
						}
					}

					if ( isset( $feed_type_and_terms['search'] ) ) {
						foreach ( $feed_type_and_terms['search'] as $term_and_params ) {
							$search = $term_and_params['term'];
							$this_term_included_string .= '?' . substr( $search, 0, 7 );
						}
					}

					if ( isset( $feed_type_and_terms['hometimeline'] ) ) {
						foreach ( $feed_type_and_terms['hometimeline'] as $term_and_params ) {
							$this_term_included_string .= '^home';
						}
					}

					if ( isset( $feed_type_and_terms['mentionstimeline'] ) ) {
						foreach ( $feed_type_and_terms['mentionstimeline'] as $term_and_params ) {
							$this_term_included_string .= '@me';
						}
					}

					if ( isset( $feed_type_and_terms['lists'] ) ) {
						foreach ( $feed_type_and_terms['lists'] as $term_and_params ) {
							$list = $term_and_params['list'];
							$this_term_included_string .= substr( $list, 0, 7 );
						}
					}
					$term_included_string .= strlen( $this_term_included_string ) > 8 ? substr( $this_term_included_string, 0, 8 ) : $this_term_included_string;


				}
			}

			$settings_string = '';
			$filter_string = '';
			$filters_included = array();

			foreach ( $this->plugin_settings as $plugin => $setting ) {
				if ( isset( $setting['includewords'] ) ) {
					$includewords = explode( ',', str_replace( ' ', '', $setting['includewords'] ) );
					foreach ( $includewords as $word ) {
						if ( ! in_array( $word, $filters_included, true ) ) {
							$filters_included[] = $word;
							$filter_string .= substr( $word, 1, 1 );
							$filter_string .= substr( $word, -1 );
						}

					}
				} elseif ( isset( $setting['excludewords'] ) ) {
					$excludewords = explode( ',', str_replace( ' ', '', $setting['excludewords'] ) );
					foreach ( $excludewords as $word ) {
						if ( ! in_array( $word, $filters_included, true ) ) {
							$filters_included[] = $word;
							$filter_string      .= substr( $word, 1, 1 );
							$filter_string      .= substr( $word, - 1 );
						}
					}
				} elseif ( isset( $setting['filter'] ) ) {
					$filter = explode( ',', str_replace( ' ', '', $setting['filter'] ) );
					foreach ( $filter as $word ) {
						if ( ! in_array( $word, $filters_included, true ) ) {
							$filters_included[] = $word;
							$filter_string      .= substr( $word, 1, 1 );
							$filter_string      .= substr( $word, - 1 );
						}
					}
				} elseif ( isset( $setting['exfilter'] ) ) {
					$filter = explode( ',', str_replace( ' ', '', $setting['exfilter'] ) );
					foreach ( $filter as $word ) {
						if ( ! in_array( $word, $filters_included, true ) ) {
							$filters_included[] = $word;
							$filter_string      .= substr( $word, 1, 1 );
							$filter_string      .= substr( $word, - 1 );
						}
					}
				}
				if ( isset( $setting['media'] ) && $setting['media'] !== 'all' ) {
					$filter = str_replace( ' ', '', $setting['media'] );
					$filter_string .= substr( $filter, 0, 1 );
				}
				if ( isset( $setting['offset'] ) ) {
					$settings_string .= substr( $setting['offset'], 0, 1 );
				}
				if ( isset( $setting['offset'] ) ) {
					$settings_string .= substr( $setting['offset'], 0, 1 );
				}
				if ( isset( $setting['includereplies'] ) && $setting['includereplies'] ) {
					$settings_string .= 'r';
				}
				if ( isset( $setting['includeretweets'] ) && ! $setting['includeretweets'] ) {
					$settings_string .= 'n';
				}
			}
			$filter_string = substr( $filter_string, 0, 10 );
			$settings_string = substr( $settings_string, 0, 10 );
			$transient_name .= $plugins_included_string . $term_included_string . $filter_string . $settings_string;

			$num = $this->settings['num'];

			$num_length = strlen( $num ) + 1;

			//Add both parts of the caching string together and make sure it doesn't exceed 45
			$transient_name = substr( $transient_name, 0, 45 - $num_length );

			$transient_name .= '#' . $num;

			$this->transient_name = $transient_name;
		}

	}

	/**
	 * @return array|bool
	 *
	 * @since 1.0
	 */
	public function get_plugin_type_and_terms() {
		if ( isset( $this->plugin_type_and_terms ) ) {
			return $this->plugin_type_and_terms;
		} else {
			return false;
		}
	}
}