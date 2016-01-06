<?php
/*
Plugin Name: Latest Post Shortcode
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JJA37EHZXWUTJ
Description: This plugin allows you to create a dynamic content selection from your posts, pages and custom post types that can be embedded with a shortcode.
Author: Iulia Cazan
Version: 6.1
Author URI: https://profiles.wordpress.org/iulia-cazan
License: GPL2

Copyright (C) 2015 Iulia Cazan

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'LPS_PLUGIN_VERSION', '6.1' );

class Latest_Post_Shortcode
{
	private static $instance;
	var $tile_pattern;
	var $tile_pattern_links;
	var $tile_pattern_nolinks;

	/**
	 * Get active object instance
	 *
	 * @access public
	 * @static
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new Latest_Post_Shortcode();
		}
		return self::$instance;
	}

	/**
	 * Class constructor.  Includes constants, includes and init method.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Run action and filter hooks.
	 *
	 * @access private
	 * @return void
	 */
	private function init() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		/** Apply the tiles shortcodes */
		add_shortcode( 'latest-selected-content', array( $this, 'latest_selected_content' ) );

		$this->tile_pattern = array(
			0  => '[image][title][text][read_more_text]',
			3  => '[a][image][title][text][read_more_text][/a]',
			5  => '[image][title][text][a][read_more_text][/a]',
			1  => '[title][image][text][read_more_text]',
			11 => '[a][title][image][text][read_more_text][/a]',
			13 => '[title][image][text][a][read_more_text][/a]',
			2  => '[title][text][image][read_more_text]',
			14 => '[a][title][text][image][read_more_text][/a]',
			17 => '[title][text][image][a][read_more_text][/a]',
			18 => '[title][text][read_more_text][image]',
			19 => '[a][title][text][read_more_text][image][/a]',
			22 => '[title][text][a][read_more_text][/a][image]',
		);

		if ( is_admin() ) {
			add_action( 'media_buttons_context', array( $this, 'add_shortcode_button' ) );
			add_action( 'admin_footer', array( $this, 'add_shortcode_popup_container' ) );
			add_action( 'admin_head', array( $this, 'load_admin_assets' ) );
			$this->tile_pattern_links = array();
			$this->tile_pattern_nolinks = array();
			foreach ( $this->tile_pattern as $k => $v ) {
				if ( substr_count( $v, '[a]' ) != 0 ) {
					array_push( $this->tile_pattern_links, $k );
				} else {
					array_push( $this->tile_pattern_nolinks, $k );
				}
			}
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		} else {
			add_action( 'wp_head', array( $this, 'load_assets' ) );
		}

		add_action( 'wp_ajax_nopriv_lps_navigate_to_page', array( $this, 'lps_navigate_callback' ) );
		add_action( 'wp_ajax_lps_navigate_to_page', array( $this, 'lps_navigate_callback' ) );
	}

	/**
	 * Latest_Post_Shortcode::load_textdomain() Load text domain for internalization
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'lps', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * Latest_Post_Shortcode::load_assets() Load the front assets
	 */
	function load_assets() {
		wp_enqueue_style( 'lps-style', plugins_url( '/assets/css/style.css', __FILE__ ), array(), LPS_PLUGIN_VERSION, false );

		wp_register_script(
			'lps-ajax-pagination-js',
			plugins_url( '/assets/js/custom-pagination.js', __FILE__ ),
			array( 'jquery' ),
			LPS_PLUGIN_VERSION,
			true
		);
		wp_localize_script(
			'lps-ajax-pagination-js',
			'LPS',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
		wp_enqueue_script( 'lps-ajax-pagination-js' );
	}

	/**
	 * Latest_Post_Shortcode::load_admin_assets() Load the admin assets
	 */
	function load_admin_assets() {
		wp_enqueue_style( 'lps-admin-style', plugins_url( '/assets/css/admin-style.css', __FILE__ ), array(), LPS_PLUGIN_VERSION, false );
		wp_enqueue_script( 'lps-admin-shortcode-button', plugins_url( '/assets/js/custom.js', __FILE__ ), array( 'jquery' ), LPS_PLUGIN_VERSION, true );
	}

	/**
	 * Latest_Post_Shortcode::add_shortcode_button() Add a button to the content editor, next to the media button, this button will show a popup that contains inline content
	 */
	function add_shortcode_button( $context ) {
		$container_id = 'lps_shortcode_popup_container';
		$title = __( 'Content Selection', 'lps' );
		$context .= '<a class="thickbox button" title="' . $title . '"
		href="#TB_inline?width=100%25&inlineId=' . $container_id . '" id="lps_shortcode_button_open"><span class="dashicons dashicons-format-aside"></span> ' . __( 'Content Selection', 'lps' ) . '</a>';
		echo $context;
	}

	/**
	 * Latest_Post_Shortcode::add_shortcode_popup_container() Add some content to the bottom of the page. This will be shown in the inline modal
	 */
	function add_shortcode_popup_container() {
		$body = '
		<div id="lps_shortcode_popup_container" style="display:none; width:100%; height:100%">
			<h2>' . __( 'Create Your Custom Content Selection Shortcode By Combining What You Need', 'lps' ) . '</h2>
			<table width="100%" cellpadding="0" cellspacing="0" class="lps_shortcode_popup_container_table">
				<tr>
					<td>' . __( 'Number of Posts', 'lps' ) . '</td>
					<td width="38%">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td><input type="text" name="lps_limit" id="lps_limit" value="1" onchange="lps_preview_configures_shortcode()" class="small" /></td>
								<td>
									<select name="lps_use_pagination" id="lps_use_pagination" onchange="lps_preview_configures_shortcode()">
										<option value="">' . __( 'No Pagination', 'lps' ) . '</option>
										<option value="yes">' . __( 'Paginate Results', 'lps' ) . '</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td>' . __( 'Post Type', 'lps' ) . '</td>
					<td width="38%">
						<select name="lps_post_type" id="lps_post_type" onchange="lps_preview_configures_shortcode()">
							<option value="">' . __( 'Any', 'lps' ) . '</option>
							';
		$post_types = get_post_types( array(), 'objects' );
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $k => $v ) {
				if ( $k != 'revision' && $k != 'nav_menu_item' ) {
					$body .= '<option value="' . esc_attr( $k ) . '">' . esc_html( $k ) . '</option>';
				}
			}
		}
		$body .= '
						</select>
					</td>
				</tr>
				<tr>					
					<td colspan="4">
						<div id="lps_pagination_options">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td>' . __( 'Records Per Page', 'lps' ) . '</td>
									<td><input type="text" name="lps_per_page" id="lps_per_page" value="0" onchange="lps_preview_configures_shortcode()" class="small" /></td>
									<td>' . __( 'Offset', 'lps' ) . '</td>
									<td><input type="text" name="lps_offset" id="lps_offset" value="0" onchange="lps_preview_configures_shortcode()" class="small" /></td>
									<td>
										<select name="lps_showpages" id="lps_showpages" onchange="lps_preview_configures_shortcode()">
											<option value="">' . __( 'Hide Pages Navigation', 'lps' ) . '</option>
											<option value="4">' . __( 'Show Pages Navigation (range of 4 visible pages)', 'lps' ) . '</option>
											<option value="5">' . __( 'Show Pages Navigation (range of 5 visible pages)', 'lps' ) . '</option>
											<option value="10">' . __( 'Show Pages Navigation (range of 10 visible pages)', 'lps' ) . '</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>' . __( 'Pagination Position', 'lps' ) . '</td>
									<td colspan="4">
										<select name="lps_showpages_pos" id="lps_showpages_pos" onchange="lps_preview_configures_shortcode()">
											<option value="">' . __( 'Above the results', 'lps' ) . '</option>
											<option value="1">' . __( 'Below the results', 'lps' ) . '</option>
											<option value="2">' . __( 'Above & below the result', 'lps' ) . '</option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<hr />
					</td>
				</tr>
				<tr>
					<td>' . __( 'Display Post', 'lps' ) . '</td>
					<td>
						<select name="lps_display" id="lps_display" onchange="lps_preview_configures_shortcode()">
							<option value="title">Title</option>
							<option value="title,excerpt">Title + Post Excerpt</option>
							<option value="title,content">Title + Post Content</option>
							<option value="title,excerpt-small">Title + Few Chars From The Excerpt</option>
							<option value="title,content-small">Title + Few Chars From The Content</option>
							<option value="date">Date</option>
							<option value="title,date">Title + Date</option>
							<option value="title,date,excerpt">Title + Date + Post Excerpt</option>
							<option value="title,date,content">Title + Date + Post Content</option>
							<option value="title,date,excerpt-small">Title + Date + Few Chars From The Excerpt</option>
							<option value="title,date,content-small">Title + Date + Few Chars From The Content</option>
							<option value="date,title">Date + Title</option>
							<option value="date,title,excerpt">Date + Title + Post Excerpt</option>
							<option value="date,title,content">Date + Title + Post Content</option>
							<option value="date,title,excerpt-small">Date + Title + Few Chars From The Excerpt</option>
							<option value="date,title,content-small">Date + Title + Few Chars From The Content</option>
						</select>
						<div id="lps_display_limit">
							<input type="text" name="lps_chrlimit" id="lps_chrlimit" onchange="lps_preview_configures_shortcode()" placeholder="Ex: 120" value="120" class="small" /> ' . __( 'chars from excerpt / content', 'lps' ) . '
						</div>
					</td>
					<td>' . __( 'Use Post URL', 'lps' ) . '</td>
					<td>
						<select name="lps_url" id="lps_url" onchange="lps_preview_configures_shortcode()">
							<option value="">No link to the post</option>
							<option value="yes">Link to the post</option>
							<option value="yes_blank">Link to the post (_blank)</option>
						</select>
						<div id="lps_url_options">
							<input type="text" name="lps_linktext" id="lps_linktext" onchange="lps_preview_configures_shortcode()" placeholder="Custom \'Read more\' message" />
						</div>
					</td>
				</tr>
				<tr>
					<td>' . __( 'Extra Options', 'lps' ) . '</td>
					<td>
						<label><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_tags" value="tags" onclick="lps_preview_configures_shortcode()" class="lps_show_extra" /> ' . __( 'Show post tags', 'lps' ) . '</label>
						<label><input type="checkbox" name="lps_show_extra[]" id="lps_show_extra_tags" value="ajax_pagination" onclick="lps_preview_configures_shortcode()" class="lps_show_extra" /> ' . __( 'Ajax Pagination', 'lps' ) . '</label>
					</td>
					<td>' . __( 'Order by', 'lps' ) . '</td>
					<td>
						<select name="lps_orderby" id="lps_orderby" onchange="lps_preview_configures_shortcode()">
							<option value="dateD">Date DESC</option>
							<option value="dateA">Date ASC</option>
							<option value="menuA">Menu Order ASC</option>
							<option value="menuD">Menu Order DESC</option>
							<option value="titleA">Title ASC</option>
							<option value="titleD">Title DESC</option>
						</select>
					</td>
				</tr>
				<tr>					
					<td colspan="4"><hr /></td>
				</tr>
				<tr>
					<td>' . __( 'Use Image', 'lps' ) . '</td>
					<td>
						<select name="lps_image" id="lps_image" onchange="lps_preview_configures_shortcode()">
							<option value="">No Image</option>';

		$app_sizes = get_intermediate_image_sizes();
		if ( ! empty( $app_sizes ) ) {
			foreach ( $app_sizes as $s ) {
				$body .= '<option value="' . $s . '">' . $s . '</option>';
			}
		}
		$body .= '
							<option value="full">full (original size)</option>
						</select>
					</td>
					<td>' . __( 'CSS Class Selector', 'lps' ) . '</td>
					<td><input type="text" name="lps_css" id="lps_css" onchange="lps_preview_configures_shortcode()" placeholder="Ex: two-columns, three-columns" />
					</td>
				</tr>
				<tr>					
					<td colspan="4"><hr /></td>
				</tr>
				<tr>					
					<td>' . __( 'Tile Pattern', 'lps' ) . '<br />' . __( '(order of the html tags and the link - marked with red)', 'lps' ) . '</td>
					<td colspan="3"><input type="hidden" name="lps_elements" id="lps_elements" value="0" onchange="lps_preview_configures_shortcode()" />';
		foreach ( $this->tile_pattern as $k => $p ) {
			$cl = ( in_array( $k, $this->tile_pattern_links ) ) ? 'with-link' : 'without-link';
			$body .= '<label class="' . $cl . '"><img src="' . plugins_url( '/assets/images/post_tiles' . $k . '.png', __FILE__ ) . '" title="' . $p . '" /><input type="radio" name="lps_elements_img" id="lps_elements_img_' . $k . '" value="' . $k . '" onclick="jQuery(\'#lps_elements\').val(\'' . $k . '\'); lps_preview_configures_shortcode();"></label>';
		}
		$body .= '
					</td>
				</tr>
				<tr>
					<td colspan="4"><hr /><h3>' . __( 'Select The Latest From Taxonomy', 'lps' ) . '</h3></td>
				</tr>
				<tr>
					<td>' . __( 'Taxonomy', 'lps' ) . '</td>
					<td>
						<select name="lps_taxonomy" id="lps_taxonomy" onchange="lps_preview_configures_shortcode()">
							<option value="">' . __( 'Any', 'lps' ) . '</option>
							';
		$exclude_tax = array( 'post_tag', 'nav_menu', 'link_category', 'post_format' );
		$tax = get_taxonomies( array(), 'objects' );
		if ( ! empty( $tax ) ) {
			foreach ( $tax as $k => $v ) {
				if ( ! in_array( $k, $exclude_tax ) ) {
					$body .= '<option value="' . esc_attr( $k ) . '">' . esc_html( $v->labels->name ) . '</option>';
				}
			}
		}
		$body .= '
						</select>
					<td>' . __( 'Term', 'lps' ) . '</td>
					<td><input type="text" name="lps_term" id="lps_term"  placeholder="Term slug (ex: news)" onchange="lps_preview_configures_shortcode()" /></td>
				</tr>
				<tr>
					<td colspan="4"><hr /><h3>' . __( 'OR Select The Latest Posts With The Tag', 'lps' ) . '</h3></td>
				</tr>
				<tr>
					<td>' . __( 'Tag', 'lps' ) . '</td>
					<td><input type="text" name="lps_tag" id="lps_tag" onchange="lps_preview_configures_shortcode()" /></td>
					<td>' . __( 'Dynamic', 'lps' ) . '</td>
					<td>
						<select name="lps_dtag" id="lps_dtag" onchange="lps_preview_configures_shortcode()">
							<option value="">No, use the selected ones</option>
							<option value="yes">Yes, use the current post tags</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="4"><hr /><h3>' . __( 'OR Select', 'lps' ) . '</h3></td>
				</tr>
				<tr>
					<td colspan="2"><h3>' . __( 'The Posts By IDs', 'lps' ) . '</h3></td>
					<td colspan="2"><h3>' . __( 'The Latest Posts By Parent IDs', 'lps' ) . '</h3></td>
				</tr>
				<tr>
					<td>' . __( 'Post ID', 'lps' ) . '</td>
					<td><input type="text" name="lps_post_id" id="lps_post_id" onchange="lps_preview_configures_shortcode()" placeholder="Separate IDs with comma" /></td>
					<td>' . __( 'Parent ID', 'lps' ) . '</td>
					<td colspan="3"><input type="text" name="lps_parent_id" id="lps_parent_id" onchange="lps_preview_configures_shortcode()" placeholder="Separate IDs with comma" /></td>
				</tr>
				<tr>
					<td colspan="4">
						<hr />
					</td>
				</tr>
				';
				echo $body;
				
				/** Introduce the slider extension options */
				do_action( 'latest_selected_content_slider_configuration' );

				$body = '
				<tr>
					<td colspan="3">
						<h3>' . __( 'Shortcode Preview', 'lps' ) . '</h3>
					</td>
					<td>
						<a class="button button-primary" id="lps_button_embed_shortcode">' . __( 'Embed The Shortcode', 'lps' ) . '</a>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<hr /><div id="lps_preview_embed_shortcode">[latest-selected-content type="post" limit="1" tag="news"]</div>
					</td>
				</tr>
			</table>
		</div>
		';
		echo $body;
	}

	/**
	 * Latest_Post_Shortcode::get_short_text() Get short text of maximum x chars
	 */
	function get_short_text( $text, $limit, $is_excerpt = false ) {
		$filter = ( $is_excerpt ) ? 'the_excerpt' : 'the_content';
		
		$text = strip_tags( $text );
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );
		$text = strip_shortcodes( $text );
		$text = apply_filters( $filter, strip_shortcodes( $text ) );
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );
		$text = strip_tags( $text );
		$text = preg_replace( '~\[[^\]]+\]~', '', $text );
		/** This is a trick to replace the unicode whitespace :) */
		$text = preg_replace( '/\xA0/u', ' ', $text );
		$text = str_replace( '&nbsp;', ' ', $text );
		$text = preg_replace( '/\s\s+/', ' ', $text );
		$text = preg_replace( '/\s+/', ' ', $text );
		$text = trim( $text );
		if ( ! empty( $text ) ) {
			$content = explode( ' ', $text );
			$len = $i = 0;
			$max = count( $content );
			$text = '';
			while ( $len < $limit ) {
				$text .= $content[$i] . ' ';
				$i ++;
				$len = strlen( $text );
				if ( $i >= $max ) {
					break;
				}
			}
			$text = trim( $text );
			$text = preg_replace( '/\[.+\]/', '', $text );
			$text = apply_filters( $filter, $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
		}
		return $text;
	}

	/**
	 * Latest_Post_Shortcode::lps_navigate() Return the content generated after an ajax call for the pagination
	 */
	function lps_navigate_callback() {
		if ( ! empty( $_POST['args'] ) ) {
			header ('Content-type: text/html; charset=utf-8');
			$_args = $_POST['args'];
			$_args = stripslashes( stripslashes( $_args ) );
			$args = ( ! empty( $_POST['args'] ) ) ? json_decode( $_args ) : false;
			if ( ! empty( $_POST['page'] ) && $args ) {
				$args = (array) $args;
				if ( ! empty( $args['linktext'] ) ) {
					$args['linktext'] = preg_replace( '/u([0-9a-z]{4})+/', '&#x$1;', $args['linktext'] );
				}
				set_query_var( 'page', (int) $_POST['page'] );
				echo $this->latest_selected_content( $args );
			}
		}
		die();
	}

	/**
	 * Latest_Post_Shortcode::lps_pagination() Return the content generated for plugin pagination with the specific arguments
	 */
	function lps_pagination( $total = 1, $per_page = 10, $range = 4, $shortcode_id = '' ) {
		wp_reset_query();
		$body = '';
		$total = intval( $total );
		$per_page = ( ! empty( $per_page ) ) ? intval( $per_page ) : 1;
		$range = abs( intval( $range ) - 1 );
		$range = ( empty( $range ) ) ? 1 : $range;
		$total_pages = ceil( $total / $per_page );
		if ( $total_pages > 1 ) {
			$current_page = get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : 1;
			$body .= '
			<ul class="latest-post-selection pages ' . esc_attr( $shortcode_id ) . '">
				<li>' . __( 'Page', 'lps' ) . ' ' . $current_page . ' ' . __( 'of', 'lps' ) . ' ' . $total_pages . '</li>';

			if ( $total_pages > $range && $current_page > 1 ) {
				$body .= '<li><a href="' . get_permalink() . '" data-page="1">&lsaquo;&nbsp;</a></li>';
			}
			if ( $current_page > $range && $current_page > 1 ) {
				$body .= '<li><a href="' . get_pagenum_link( $current_page - 1 ) . '" data-page="' . ( $current_page - 1 ) . '">&laquo;</a></li>';
			}

			$lrang = ceil( ( $current_page % $range ) );
			$start = $current_page - $lrang;
			$start = ( $start <= 1 ) ? 1 : $start;
			$end = $start + $range;

			if ( $end >= $total_pages ) {
				$end = $total_pages;
				$start = $end - $range;
				$start = ( $start <= 1 ) ? 1 : $start;
			}

			for ( $i = $start; $i <= $end; $i ++ ) {
				if ( 1 == $i ) {
					$body .= '<li><a href="' . get_permalink() . '" data-page="1">' . $i . '</a></li>';
				} else {
					if ( $current_page == $i ) {
						$body .= '<li class="current"><a data-page="' . $i . '">' . $i . '</a></li>';
					} else {
						$body .= '<li><a href="' . get_pagenum_link( $i ) . '" data-page="' . $i . '">' . $i . '</a></li>';
					}
				}
			}

			if ( $current_page < $total_pages ) {
				$body .= '<li><a href="' . get_pagenum_link( $current_page + 1 ) . '" data-page="' . ( $current_page + 1 ) . '">&raquo;</a></li>';
			}
			if ( $current_page < $total_pages - 1 && $current_page + $range - 1 < $total_pages && $current_page < $total_pages ) {
				$body .= '<li><a href="' . get_pagenum_link( $total_pages ) . '" data-page="' . $total_pages . '">&nbsp;&rsaquo;</a></li>';
			}
			$body .= '</ul>';

			if ( get_site_url() . '/' == get_permalink() ) {
				/** We must use /page/x */
				if ( ! empty( $current_page ) && substr_count( $body, '/page/' . $current_page . '/' ) != 0 ) {
					$body = str_replace( '/page/' . $current_page . '/', '/', $body );
				}
			} else {
				/** We must use /x */
				$body = str_replace( '/' . $current_page . '/page/', '/', $body );
				$body = str_replace( '/page/', '/', $body );
			}
		}

		return $body;
	}

	/**
	 * Latest_Post_Shortcode::latest_selected_content() Return the content generated by a shortcode with the specific arguments
	 */
	function latest_selected_content( $args ) {
		global $post;

		/** Get the post arguments from shortcode arguments */
		$ids = ( ! empty( $args['id'] ) ) ? explode( ',', $args['id'] ) : array();
		$parent = ( ! empty( $args['parent'] ) ) ? intval( $args['parent'] ) : 0;
		$type = ( ! empty( $args['type'] ) ) ? $args['type'] : 'post';
		$chrlimit = ( ! empty( $args['chrlimit'] ) ) ? intval( $args['chrlimit'] ) : 120;

		$extra_display = ( ! empty( $args['display'] ) ) ? explode( ',', $args['display'] ) : array( 'title' );
		$linkurl = ( ! empty( $args['url'] ) && ( 'yes' == $args['url'] || 'yes_blank' == $args['url'] ) ) ? true : false;
		$tile_type = 0;
		if ( $linkurl ) {
			$linktext = ( ! empty( $args['linktext'] ) ) ? $args['linktext'] : '';
			$tile_type = ( ! empty( $args['elements'] ) && ! empty( $this->tile_pattern[$args['elements']] ) ) ? $args['elements'] : 0;
		}
		$tile_pattern = $this->tile_pattern[$tile_type];
		$read_more_class = ( ! in_array( $tile_type, array( 3, 11, 14, 19 ) ) ) ? ' class="read-more"' : ' class="read-more-wrap"';
		$show_extra = ( ! empty( $args['show_extra'] ) ) ? explode( ',', $args['show_extra'] ) : array();

		$qargs = array(
			'post_status' => 'publish',
			'numberposts' => 1,
		);

		$orderby = ( ! empty( $args['orderby'] ) ) ? $args['orderby'] : 'dateD';
		$qargs['order'] = 'DESC';
		$qargs['orderby'] = 'date_publish';

		switch ( $orderby ) {
			case 'dateA' :
				$qargs['order'] = 'ASC';
				$qargs['orderby'] = 'date_publish';
				break;
			case 'menuA' :
				$qargs['order'] = 'ASC';
				$qargs['orderby'] = 'menu_order';
				break;
			case 'menuD' :
				$qargs['order'] = 'DESC';
				$qargs['orderby'] = 'menu_order';
				break;
			case 'titleA' :
				$qargs['order'] = 'ASC';
				$qargs['orderby'] = 'post_title';
				break;
			case 'titleD' :
				$qargs['order'] = 'DESC';
				$qargs['orderby'] = 'post_title';
				break;
			default :
				break;
		}

		/** Make sure we do not loop in the current page */
		if ( ! empty( $post->ID ) ) {
			$qargs['post__not_in'] = array( $post->ID );
		}

		if ( ! empty( $args['limit'] ) ) {
			$qargs['numberposts'] = ( ! empty( $args['limit'] ) ) ? intval( $args['limit'] ) : 1;
		}
		if ( ! empty( $args['perpage'] ) ) {
			$qargs['posts_per_page'] = ( ! empty( $args['perpage'] ) ) ? intval( $args['perpage'] ) : 0;
			$paged = get_query_var( 'page' ) ? abs( intval( get_query_var( 'page' ) ) ) : 1;
			$current_page = $paged;
			$qargs['paged'] = $paged;
			$qargs['page'] = $current_page;
		}
		if ( ! empty( $args['offset'] ) ) {
			$qargs['offset'] = ( ! empty( $args['offset'] ) ) ? intval( $args['offset'] ) : 0;
			if ( ! empty( $qargs['paged'] ) ) {
				$qargs['offset'] = abs( $current_page - 1 ) * $args['offset'];
			}
		}

		$force_type = true;
		if ( ! empty( $ids ) && is_array( $ids ) ) {
			foreach ( $ids as $k => $v ) {
				$ids[$k] = intval( $v );
			}
			$qargs['post__in'] = $ids;
			$force_type = false;
		}
		if ( $force_type ) {
			$qargs['post_type'] = $type;
		} else {
			if ( ! empty( $args['type'] ) ) {
				$qargs['post_type'] = $args['type'];
			}
		}
		if ( ! empty( $parent ) ) {
			$qargs['post_parent'] = $parent;
		}
		$qargs['tax_query'] = array();
		if ( ! empty( $args['tag'] ) ) {
			array_push(
				$qargs['tax_query'],
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'slug',
					'terms'    => ( ! empty( $args['tag'] ) ) ? $args['tag'] : 'homenews',
				)
			);
		}
		if ( ! empty( $args['dtag'] ) ) {
			$tag_ids = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
			if ( ! empty( $tag_ids ) && is_array( $tag_ids ) ) {
				if ( ! empty( $qargs['tax_query'] ) ) {
					array_push(
						$qargs['tax_query'],
						array(
							'relation' => 'AND',
						)
					);
				}
				array_push(
					$qargs['tax_query'],
					array(
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $tag_ids,
						'operator' => 'IN',
					)
				);
			}
		}
		if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			if ( ! empty( $qargs['tax_query'] ) ) {
				array_push(
					$qargs['tax_query'],
					array(
						'relation' => 'AND',
					)
				);
			}
			array_push(
				$qargs['tax_query'],
				array(
					'taxonomy' => $args['taxonomy'],
					'field'    => 'slug',
					'terms'    => $args['term'],
				)
			);
		}
		$qargs['suppress_filters'] = false;
		$posts = get_posts( $qargs );
		
		/** If the slider extension is enabled and the shortcode is configured to output the slider, let's do that and return */
		if ( 
			! empty( $posts ) 
			&& class_exists( 'Latest_Post_Shortcode_Slider' ) 
			&& ! empty( $args['output'] ) 
			&& 'slider' == $args['output'] 
		) {
			ob_start();
			do_action( 'latest_selected_content_slider', $posts, $args );
			wp_reset_postdata();
			return ob_get_clean();
		}


		$is_lps_ajax = get_query_var( 'lps_ajax' ) ? intval( get_query_var( 'lps_ajax' ) ) : 0;
		$shortcode_id = 'lps-' . md5( serialize( $args ) . microtime() );

		ob_start();
		if ( ! empty( $qargs['posts_per_page'] ) && ! empty( $args['showpages'] ) ) {
			$counter = new WP_Query( $qargs );
			$found_posts = ( ! empty( $counter->found_posts ) ) ? $counter->found_posts : 0;
			$pagination_html = $this->lps_pagination( intval( $found_posts ), ( ! empty( $qargs['posts_per_page'] ) ) ? $qargs['posts_per_page'] : 1, intval( $args['showpages'] ), $shortcode_id );

			if ( in_array( 'ajax_pagination', $show_extra ) && ! $is_lps_ajax && ! empty( $args ) && is_array( $args ) ) {
				if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
					echo '<div id="' . esc_attr( $shortcode_id ) . '-wrap" data-args="' . esc_js( json_encode( $args ) ) . '">';
				} else {
					echo '<div id="' . esc_attr( $shortcode_id ) . '-wrap" data-args="' . esc_js( json_encode( $args, JSON_UNESCAPED_UNICODE ) ) . '">';
				}
			}

			if ( empty( $args['pagespos'] ) || ( ! empty( $args['pagespos'] ) && 2 == $args['pagespos'] ) ) {
				echo $pagination_html;
			}
		}
		if ( ! empty( $posts ) ) {

			if ( in_array( 'date', $extra_display ) ) {
				$date_format = get_option( 'date_format' ) . ' \<\i\>' . get_option( 'time_format' ) . '\<\/\i\>';
			}

			$class = ( ! empty( $args['css'] ) ) ? ' ' . $args['css'] : '';
			if ( in_array( 'ajax_pagination', $show_extra ) ) {
				$class .= ' ajax_pagination';
			}
			echo '<section class="latest-post-selection' . esc_attr( $class ) . '" id="' . esc_attr( $shortcode_id ) . '">';
			
			$iw = get_option( esc_attr( $args['image'] ) . '_size_w', true );
			$iw = ( 1 == $iw ) ? '100%' : $iw . 'px';
			$ih = get_option( esc_attr( $args['image'] ) . '_size_h', true );
			$ih = ( 1 == $ih ) ? '100%' : $ih . 'px';
			echo '<style>#' . esc_attr( $shortcode_id ) . ' .custom-' . esc_attr( $args['image'] ) .' { width:' . $iw . '; min-height:' . $ih . '; height:auto;}</style>';
			
			foreach ( $posts as $post ) {
				$tile = $tile_pattern;
				$a_start = $a_end = '';
				if ( $linkurl ) {
					$link_target = ( 'yes_blank' == $args['url'] ) ? ' target="_blank"' : '';
					$a_start = '<a href="' . get_permalink( $post->ID ) . '"' . $read_more_class . $link_target . '>';
					$a_end = '</a>';
				}
				$tile = str_replace( '[a]', $a_start, $tile );
				$tile = str_replace( '[/a]', $a_end, $tile );
				if ( ! empty( $args['image'] ) ) {
					$img_html = apply_filters( 'post_thumbnail_html', '', $post->ID, 0, $args['image'], array( 'class' => 'custom-' . $args['image'] ) );
					$th_id = get_post_thumbnail_id( intval( $post->ID ) );
					$image = wp_get_attachment_image_src( $th_id, $args['image'] );
					if ( ! empty( $image[0] ) ) {
						$img_html = '<img src="' . esc_url( $image[0] ) . '" />';
						$img_html = apply_filters( 'post_thumbnail_html', $img_html, $post->ID, $th_id, $args['image'], array() );
					}
					if ( ! empty( $img_html ) ) {
						$tile = str_replace( '[image]', $img_html, $tile );
					} else {
						$tile = str_replace( '[image]', '', $tile );
					}
				} else {
					$tile = str_replace( '[image]', '', $tile );
				}

				if ( in_array( 'date', $extra_display ) ) {
					if ( in_array( 'title', $extra_display ) ) {
						if ( ! empty( $args['display'] ) && substr_count( $args['display'], 'date,title' ) ) {
							$tile = str_replace( '[title]', '[date][title]', $tile );
						} else {
							$tile = str_replace( '[title]', '[title][date]', $tile );
						}
					} else {
						$tile = str_replace( '[title]', '[date]', $tile );
					}
				}
				if ( in_array( 'date', $extra_display ) ) {
					$tile = str_replace( '[date]', '<em>' . date_i18n( $date_format, strtotime( $post->post_date ), true ) . '</em>', $tile );
				} else {
					$tile = str_replace( '[date]', '', $tile );
				}

				if ( in_array( 'tags', $show_extra ) ) {
					$tags = apply_filters( 'the_tags', get_the_term_list( $post->ID, 'post_tag', '<div class="tags">', ', ', '</div>' ), '<div class="tags">', ', ', '</div>', $post->ID );
					$tile = str_replace( '[text]', '[text]' . $tags, $tile );
				}

				if ( in_array( 'title', $extra_display ) ) {
					$tile = str_replace( '[title]', '<h1>' . esc_html( $post->post_title ) . '</h1>', $tile );
				} else {
					$tile = str_replace( '[title]', '', $tile );
				}
				$text = '';
				if ( in_array( 'excerpt', $extra_display ) || in_array( 'content', $extra_display ) || in_array( 'content-small', $extra_display ) || in_array( 'excerpt-small', $extra_display ) ) {
					if ( in_array( 'excerpt', $extra_display ) ) {
						$text = apply_filters( 'the_excerpt', strip_shortcodes( $post->post_excerpt ) );
					} elseif ( in_array( 'excerpt-small', $extra_display ) ) {
						$text = $this->get_short_text( $post->post_excerpt, $chrlimit, true );
					} else if ( in_array( 'content', $extra_display ) ) {
						$text = apply_filters( 'the_content', $post->post_content );
					} elseif ( in_array( 'content-small', $extra_display ) ) {
						$text = $this->get_short_text( $post->post_content, $chrlimit, false );
					}
				}
				$tile = str_replace( '[text]', $text, $tile );
				if ( ! empty( $linktext ) ) {
					$tile = str_replace( '[read_more_text]', $linktext, $tile );
				} else {
					$tile = str_replace( '[read_more_text]', '', $tile );
				}

				echo '<article>' . $tile . '<div class="clear"></div></article>';
			}
			echo '</section>';
		}
		if ( ! empty( $qargs['posts_per_page'] ) && ! empty( $args['showpages'] ) ) {
			if ( ! empty( $args['pagespos'] ) && ( 1 == $args['pagespos'] || 2 == $args['pagespos'] ) ) {
				echo $pagination_html;
			}
			if ( in_array( 'ajax_pagination', $show_extra ) && ! $is_lps_ajax && ! empty( $args ) && is_array( $args ) ) {
				echo '</div>';
			}
		}
		wp_reset_postdata();
		return ob_get_clean();
	}

	function plugin_action_links( $links ) {
		$all = array();
		$all[] = '<a href="http://iuliacazan.ro/latest-post-shortcode">Plugin URL</a>';
		$all = array_merge( $all, $links );
		return $all;
	}

}

Latest_Post_Shortcode::get_instance();

/** Allow the text widget to render the Latest Post Shortcode */
add_filter( 'widget_text', 'do_shortcode', 11 );
