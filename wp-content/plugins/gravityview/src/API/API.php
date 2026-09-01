<?php
/**
 * GravityView template tags API.
 *
 * PSR-4 migration of the legacy GravityView_API class.
 *
 * @package GravityKit\GravityView\API
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\API;

use GFFormsModel;
use GravityKit\GravityView\Pagination\PaginationKeys;
use GravityKit\GravityView\Template\TemplateContext;
use GravityKit\GravityView\View\View;
use GravityView_frontend;
use GravityView_Merge_Tags;
use GravityView_View;
use GravityView_View_Data;
use GVCommon;
use RGFormsModel;

use function gform_update_meta;
use function gravityview;
use function gravityview_get_link;
use function gravityview_get_view_id;
use function gravityview_sanitize_html_class;
use function gv_get_query_args;

class API {

	/**
	 * Fetch Field Label
	 *
	 * @deprecated 3.0.0 \GV\Field::get_label()
	 *
	 * @static
	 * @param array   $field GravityView field array
	 * @param array   $entry Gravity Forms entry array
	 * @param boolean $force_show_label Whether to always show the label, regardless of field settings
	 * @return string
	 */
	public static function field_label( $field, $entry = [], $force_show_label = false ) {

		$gravityview_view = GravityView_View::getInstance();

		$form = $gravityview_view->getForm();

		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) && ! empty( $GLOBALS['GravityView_API_field_label_override'] ) ) {
			/** Allow to fall through for back compatibility testing purposes. */
		} else {
			return \GravityKit\GravityView\GravityForms\QueryExtensions\GravityView_API_field_label( $form, $field, $entry, $force_show_label );
		}

		$label = '';

		if ( ! empty( $field['show_label'] ) || $force_show_label ) {

			$label = $field['label'];

			// Support Gravity Forms 1.9+
			if ( class_exists( 'GF_Field' ) ) {

				$field_object = RGFormsModel::get_field( $form, $field['id'] );

				if ( $field_object ) {

					$input = GFFormsModel::get_input( $field_object, $field['id'] );

					// This is a complex field, with labels on a per-input basis
					if ( $input ) {

						// Does the input have a custom label on a per-input basis? Otherwise, default label.
						$label = ! empty( $input['customLabel'] ) ? $input['customLabel'] : $input['label'];

					} else {

						// This is a field with one label
						$label = $field_object->get_field_label( true, $field['label'] );

					}
				}
			}

			// Use Gravity Forms label by default, but if a custom label is defined in GV, use it.
			if ( ! empty( $field['custom_label'] ) ) {

				$label = self::replace_variables( $field['custom_label'], $form, $entry );

			}

			/**
			 * Append content to a field label.
			 *
			 * @param string $appended_content Content you can add after a label. Empty by default.
			 * @param array $field GravityView field array
			 */
			$label .= apply_filters( 'gravityview_render_after_label', '', $field );

		} // End $field['show_label']

		/**
		 * Modify field label output.
		 *
		 * @since 1.7
		 * @param string $label Field label HTML
		 * @param array $field GravityView field array
		 * @param array $form Gravity Forms form array
		 * @param array $entry Gravity Forms entry array
		 *
		 * @deprecated 3.0.0 the context-aware version `gravityview/template/field/label`
		 */
		$label = \GravityView_Deprecated_Hook_Notices::apply_filters( 'gravityview/template/field_label', [ $label, $field, $form, $entry ], '2.55', 'gravityview/template/field/label' );

		return $label;
	}

	/**
	 * Alias for GravityView_Merge_Tags::replace_variables()
	 *
	 * @see GravityView_Merge_Tags::replace_variables() Moved in 1.8.4
	 * @since 1.22.4 - Added $nl2br, $format, $aux_data args
	 *
	 * @param  string $text         Text to replace variables in
	 * @param  array  $form         GF Form array
	 * @param  array  $entry        GF Entry array
	 * @param  bool   $url_encode   Pass return value through `url_encode()`
	 * @param  bool   $esc_html     Pass return value through `esc_html()`
	 * @param  bool   $nl2br        Convert newlines to <br> HTML tags
	 * @param  string $format       The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param  array  $aux_data     Additional data to be used to replace merge tags {@see https://www.gravityhelp.com/documentation/article/gform_merge_tag_data/}
	 * @return string                   Text with variables maybe replaced
	 */
	public static function replace_variables( $text, $form = [], $entry = [], $url_encode = false, $esc_html = true, $nl2br = true, $format = 'html', $aux_data = [] ) {
		return GravityView_Merge_Tags::replace_variables( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format, $aux_data );
	}

	/**
	 * Get column width from the field setting
	 *
	 * @since 1.9
	 *
	 * @param array  $field Array of settings for the field
	 * @param string $format Format for width. "%" (default) will return
	 *
	 * @return string|null If not empty, string in $format format. Otherwise, null.
	 */
	public static function field_width( $field, $format = '%d%%' ) {

		$width = null;

		if ( ! empty( $field['width'] ) ) {
			$width = absint( $field['width'] );

			// If using percentages, limit to 100%
			if ( '%d%%' === $format && $width > 100 ) {
				$width = 100;
			}

			$width = sprintf( $format, $width );
		}

		return $width;
	}

	/**
	 * Fetch Field class
	 *
	 * @static
	 * @param mixed $field
	 * @return string
	 */
	public static function field_class( $field, $form = null, $entry = null ) {
		$classes = [];

		if ( ! empty( $field['custom_class'] ) ) {

			$custom_class = $field['custom_class'];

			if ( ! empty( $entry ) ) {

				// We want the merge tag to be formatted as a class. The merge tag may be
				// replaced by a multiple-word value that should be output as a single class.
				// "Office Manager" will be formatted as `.OfficeManager`, not `.Office` and `.Manager`
				add_filter( 'gform_merge_tag_filter', 'sanitize_html_class' );

				$custom_class = self::replace_variables( $custom_class, $form, $entry );

				// And then we want life to return to normal
				remove_filter( 'gform_merge_tag_filter', 'sanitize_html_class' );
			}

			// And now we want the spaces to be handled nicely.
			$classes[] = gravityview_sanitize_html_class( $custom_class );

		}

		if ( ! empty( $field['id'] ) ) {
			if ( ! empty( $form ) && ! empty( $form['id'] ) ) {
				$form_id = $form['id'];
			} else {
				// @deprecated 3.0.0 Form should always be given.
				gravityview()->log->warning( 'GravityView_View::getInstance() legacy API called' );
				$gravityview_view = GravityView_View::getInstance();
				$form_id          = $gravityview_view->getFormId() ? $gravityview_view->getFormId() : '';
			}

			$classes[] = 'gv-field' . ( $form_id ? '-' . $form_id : '' ) . '-' . $field['id'];

			// Field is from different form, so we add an extra class.
			if ( (int) ( $field['form_id'] ?? $form_id ) !== (int) $form_id ) {
				$classes[] = 'gv-field-' . $field['form_id'] . '-' . $field['id'];
			}
		}

		return esc_attr( implode( ' ', $classes ) );
	}

	/**
	 * Fetch Field HTML ID
	 *
	 * @since 1.11
	 *
	 * @static
	 * @param array $field GravityView field array passed to gravityview_field_output()
	 * @param array $form Gravity Forms form array, if set.
	 * @param array $entry Gravity Forms entry array
	 * @return string Sanitized unique HTML `id` attribute for the field
	 */
	public static function field_html_attr_id( $field, $form = [], $entry = [] ) {
		$id = $field['id'];

		if ( ! empty( $id ) ) {
			if ( ! empty( $form ) && ! empty( $form['id'] ) ) {
				$form_id = $field['form_id'] ?? $form['id'];
				$form_id = '-' . $form_id;
			} else {
				// @deprecated 3.0.0 Form should always be given.
				gravityview()->log->warning( 'GravityView_View::getInstance() legacy API called' );
				$gravityview_view = GravityView_View::getInstance();
				$form_id          = $gravityview_view->getFormId() ? '-' . $gravityview_view->getFormId() : '';
			}

			$id = 'gv-field' . $form_id . '-' . $field['id'];
		}

		return esc_attr( $id );
	}


	/**
	 * Given an entry and a form field id, calculate the entry value for that field.
	 *
	 * @deprecated 3.0.0 \GV\Field_Template::render() or the more low-level \GV\Field::get_value()
	 *
	 * @param array $entry
	 * @param array $field_settings
	 * @param string $format
	 * @return null|string
	 */
	public static function field_value( $entry, $field_settings, $format = 'html' ) {
		gravityview()->log->notice( '\GravityView_API::field_value is deprecated. Use \GV\Field_Template::render() or \GV\Field::get_value()' );
		return \GravityKit\GravityView\GravityForms\QueryExtensions\GravityView_API_field_value( $entry, $field_settings, $format );
	}

	/**
	 * Generate an anchor tag that links to an entry.
	 *
	 * @since 1.6
	 * @see GVCommon::get_link_html()
	 *
	 * @param string       $anchor_text The text or HTML inside the link
	 * @param array        $entry Gravity Forms entry array
	 * @param array|string $passed_tag_atts Attributes to be added to the anchor tag, such as `title` or `rel`.
	 * @param array        $field_settings Array of field settings. Optional, but passed to the `gravityview_field_entry_link` filter
	 *
	 * @since 2.0
	 * @param int          $base_id The post or the view that this entry is linked from.
	 *
	 * @return string|null Returns HTML for an anchor link. Null if $entry isn't defined or is missing an ID.
	 */
	public static function entry_link_html( $entry = [], $anchor_text = '', $passed_tag_atts = [], $field_settings = [], $base_id = null ) {

		if ( empty( $entry ) || ! is_array( $entry ) || ! isset( $entry['id'] ) ) {
			gravityview()->log->debug( 'Entry not defined; returning null', [ 'data' => $entry ] );
			return null;
		}

		$href = self::entry_link( $entry, $base_id );

		if ( '' === $href ) {
			return null;
		}

		$link = gravityview_get_link( $href, $anchor_text, $passed_tag_atts );

		/**
		 * Modify the link HTML.
		 *
		 * @param string $link HTML output of the link
		 * @param string $href URL of the link
		 * @param array  $entry The GF entry array
		 * @param  array $field_settings Settings for the particular GV field
		 */
		$output = apply_filters( 'gravityview_field_entry_link', $link, $href, $entry, $field_settings );

		return $output;
	}

	/**
	 * Get the "No Results" text depending on whether there were results.
	 *
	 * @since 2.0
	 *
	 * @param  boolean                   $wpautop Apply wpautop() to the output?
	 * @param \GV\Template_Context|null $context The context
	 *
	 * @return string               HTML of "no results" text
	 */
	public static function no_results( $wpautop = true, $context = null ) {
		$is_search = false;

		if ( $context instanceof \GV\Template_Context ) {
			if ( $context->request->is_search( $context->view ) ) {
				$is_search = true;
			}
		} else {
			$gravityview_view = GravityView_View::getInstance();

			if ( $gravityview_view && ( $gravityview_view->curr_start || $gravityview_view->curr_end || $gravityview_view->curr_search ) ) {
				$is_search = true;
			}
		}

		$setting = '';

		if ( $is_search ) {

			$output = esc_html__( 'This search returned no results.', 'gk-gravityview' );

			if ( $context ) {
				$setting = $context->view->settings->get( 'no_search_results_text', $output );
			}
		} else {

			$output = esc_html__( 'No entries match your request.', 'gk-gravityview' );

			if ( $context ) {
				$setting = $context->view->settings->get( 'no_results_text', $output );
			}
		}

		if ( '' !== $setting ) {
			$output = $setting;
		}

		/**
		 * Added now that users are able to modify via View settings
		 *
		 * @since 2.8.2
		 */
		$output = wp_kses(
			$output,
			[
				'p'      => [
					'class' => [],
					'id'    => [],
				],
				'h1'     => [
					'class' => [],
					'id'    => [],
				],
				'h2'     => [
					'class' => [],
					'id'    => [],
				],
				'h3'     => [
					'class' => [],
					'id'    => [],
				],
				'h4'     => [
					'class' => [],
					'id'    => [],
				],
				'h5'     => [
					'class' => [],
					'id'    => [],
				],
				'strong' => [
					'class' => [],
					'id'    => [],
				],
				'span'   => [
					'class' => [],
					'id'    => [],
				],
				'b'      => [
					'class' => [],
					'id'    => [],
				],
				'em'     => [
					'class' => [],
					'id'    => [],
				],
				'a'      => [
					'class'  => [],
					'id'     => [],
					'href'   => [],
					'title'  => [],
					'rel'    => [],
					'target' => [],
				],
				'div'    => [
					'class' => [],
					'id'    => [],
				],
				'br'     => [],
			]
		);

		$unformatted_output = $output;

		$output = $wpautop ? wpautop( $output ) : $output;

		/**
		 * Added now that users are able to modify via View settings
		 *
		 * Note: this filter is, and always has been, misspelled. This will not be fixed, since the filter is deprecated.
		 *
		 * @param string $output The existing "No Entries" text
		 * @param boolean $is_search Is the current page a search result, or just a multiple entries screen?
		 * @return string The modified text.
		 * @deprecated 3.0.0 `gravityview/template/text/no_entries`
		 */
		$output = \GravityView_Deprecated_Hook_Notices::apply_filters( 'gravitview_no_entries_text', [ $output, $is_search ], '2.55', 'gravityview/template/text/no_entries' );

		/**
		 * Modify the text displayed when there are no entries.
		 *
		 * @since 2.0
		 * @since 2.17 Added $wpautop parameter.
		 * @param string $output The existing "No Entries" text.
		 * @param boolean $is_search Is the current page a search result, or just a multiple entries screen?
		 * @param \GV\Template_Context $context The context.
		 * @param string $unformatted_output Output without `wpautop()`.
		 * @return string The modified text.
		 */
		$output = apply_filters( 'gravityview/template/text/no_entries', $output, $is_search, $context, $unformatted_output );

		return $output;
	}

	/**
	 * Generate a URL to the Directory context
	 *
	 * Uses local static variable to speed up repeated requests to get permalink, which improves load time. Since we may be doing this hundreds of times per request, it adds up!
	 *
	 * @used-by self::entry_link()
	 * @used-by GravityView_Widget_Page_Links::render_frontend()
	 *
	 * @param int                  $post_id Post ID
	 * @param boolean              $add_query_args Add pagination and sorting arguments
	 *
	 * @since 2.0
	 * @param \GV\Template_Context $context The context this is being used in.
	 *
	 * @return string      Permalink to multiple entries view
	 */
	public static function directory_link( $post_id = null, $add_query_args = true, $context = null ) {
		global $post;

		// Legacy templates hand-build a context whose `view` is the template object, not a View.
		$context_view = $context instanceof TemplateContext && $context->view instanceof View ? $context->view : null;

		if ( empty( $post_id ) ) {
			// DataTables passes the Post ID
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$post_id = \GV\Utils::_POST( 'post_id', false );
			} elseif ( $context instanceof \GV\Template_Context ) {
					// Shortcodes, embeds
				if ( is_a( $post, 'WP_Post' ) ) {
					$post_id = $post->ID;

					// Actual views
				} else {
					$post_id = $context_view ? $context_view->ID : gravityview_get_view_id();
				}
			} else {

				if ( ! class_exists( \GravityKit\GravityView\View\ViewLegacy::class ) ) {
					gravityview()->plugin->include_legacy_frontend( true );
				}

				/** @deprecated 3.0.0 of execution */
				$gravityview_view = GravityView_View::getInstance();

				// The Post ID has been passed via the shortcode
				if ( ! empty( $gravityview_view ) && $gravityview_view->getPostId() ) {
					$post_id = $gravityview_view->getPostId();
				} else {
					// This is a GravityView post type
					if ( GravityView_frontend::getInstance()->isGravityviewPostType() ) {
						$post_id = isset( $gravityview_view ) ? $gravityview_view->getViewId() : $post->ID;
					} else {
						// This is an embedded GravityView; use the embedded post's ID as the base.
						if ( GravityView_frontend::getInstance()->isPostHasShortcode() && is_a( $post, 'WP_Post' ) ) {
							$post_id = $post->ID;
						} elseif ( $gravityview_view->getViewId() ) {
							// The GravityView has been embedded in a widget or in a template, and
							// is not in the current content. Thus, we defer to the View's own ID.
							$post_id = $gravityview_view->getViewId();
						}
					}
				}
			}
		}

		// No post ID, get outta here.
		if ( empty( $post_id ) ) {
			return null;
		}

		static $directory_links = [];

		/**
		 * If we've saved the permalink, use it. Reduces time spent on `get_permalink()`, which is heavy.
		 *
		 * @since 1.3
		 * @since 2.17 Changed from using wp_cache_set() to using a static variable.
		 */
		if ( isset( $directory_links[ 'gv_directory_link_' . $post_id ] ) ) {
			$link = $directory_links[ 'gv_directory_link_' . $post_id ];
		}

		if ( (int) $post_id === (int) get_option( 'page_on_front' ) ) {
			$link = home_url();
		}

		if ( empty( $link ) ) {
			$link = get_permalink( $post_id );

			$directory_links[ 'gv_directory_link_' . $post_id ] = $link;
		}

		// Deal with returning to proper pagination for embedded views
		if ( $link && $add_query_args ) {

			// Prefer the originating View; the rendering stack and legacy singleton are fallbacks.
			$link_view = $context_view ?? gravityview_get_view_id();

			$args = PaginationKeys::request_pagination_args( $link_view );

			if ( $sort = \GV\Utils::_GET( 'sort' ) ) {
				$args['sort'] = $sort;
				$args['dir']  = \GV\Utils::_GET( 'dir' );
			}

			$link = add_query_arg( $args, $link );
		}

		/**
		 * Modify the URL to the View "directory" context.
		 *
		 * @since 1.19.4
		 * @param string $link URL to the View's "directory" context (Multiple Entries screen)
		 * @param int $post_id ID of the post to link to. If the View is embedded, it is the post or page ID
		 */
		$link = \GravityView_Deprecated_Hook_Notices::apply_filters( 'gravityview_directory_link', [ $link, $post_id ], '2.55', 'gravityview/view/links/directory' );

		/**
		 * Modify the URL to the View "directory" context.
		 *
		 * @since 2.0
		 * @param string $link URL to the View's "directory" context (Multiple Entries screen)
		 * @param \GV\Template_Context $context
		 */
		return apply_filters( 'gravityview/view/links/directory', $link, $context );
	}

	/**
	 * Calculate an *unique* hash for an entry based on the entry ID
	 *
	 * This allows you to be more discrete as to the number of the entry - if you don't want users to know that you have made a certain number of sales, for example, or that their entry in the giveaway is entry #3.
	 *
	 * The hashed value MUST be unique, otherwise multiple entries will share the same URL, which leads to obvious problems.
	 *
	 * @param  int|string $id Entry ID to generate the hash for.
	 * @param  array      $entry        Entry data passed to provide additional information when generating the hash. Optional - don't rely on it being available.
	 * @return string               Hashed unique value for entry
	 */
	private static function get_custom_entry_slug( $id, $entry = [] ) {

		// Generate an unique hash to use as the default value
		$slug = substr( wp_hash( $id, 'gravityview' . $id ), 0, 8 );

		/**
		 * Modify the unique entry slug, which is used in the entry URL.
		 *
		 * You can customize the slug based on entry data, for example using `{first-name}-{last-name}` (if unique).
		 *
		 * @since 1.4
		 *
		 * @param string $slug Existing slug generated by GravityView (8-character hash by default).
		 * @param string $id The entry ID.
		 * @param array $entry Entry data array. May be empty.
		 */
		$slug = apply_filters( 'gravityview_entry_slug', $slug, $id, $entry );

		// Make sure we have something - use the original ID as backup.
		if ( empty( $slug ) ) {
			$slug = $id;
		}

		return sanitize_title( $slug );
	}

	/**
	 * Get the entry slug for the entry. By default, it is the entry ID.
	 *
	 * @see gravityview_get_entry()
	 * @uses self::get_custom_entry_slug() If using custom slug, gets the custom slug value
	 * @since 1.4
	 * @param  int|string $id_or_string ID of the entry, or custom slug string
	 * @param  array      $entry        Gravity Forms Entry array, optional. Used only to provide data to customize the `gravityview_entry_slug` filter
	 * @return string               Unique slug ID, passed through `sanitize_title()`
	 */
	public static function get_entry_slug( $id_or_string, $entry = [] ) {

		/**
		 * Default: use the entry ID as the unique identifier
		 */
		$slug = $id_or_string;

		/**
		 * Whether to enable and use custom entry slugs.
		 *
		 * @param boolean True: Allow for slugs based on entry values. False: always use entry IDs (default)
		 */
		$custom = apply_filters( 'gravityview_custom_entry_slug', false );

		// If we're using custom slug...
		if ( $custom ) {

			// Get the entry hash
			$hash = self::get_custom_entry_slug( $id_or_string, $entry );

			// Cache the slugs
			static $cache = [];

			if ( ! isset( $cache[ $id_or_string ] ) ) {
				global $wpdb;

				if ( version_compare( GFFormsModel::get_database_version(), '2.3-dev-1', '>=' ) ) {
					$table  = GFFormsModel::get_entry_meta_table_name();
					$column = 'entry_id';
				} else {
					$table  = RGFormsModel::get_lead_meta_table_name();
					$column = 'lead_id';
				}

				$results = $wpdb->get_results( $wpdb->prepare( "SELECT $column, meta_value FROM $table WHERE form_id = (SELECT form_id FROM $table WHERE $column = %d LIMIT 1) AND meta_key = 'gravityview_unique_id'", $id_or_string ) );

				if ( $results ) {
					$cache = array_replace( $cache, array_combine( wp_list_pluck( $results, $column ), wp_list_pluck( $results, 'meta_value' ) ) );
				}

				if ( ! isset( $cache[ $id_or_string ] ) ) {
					$cache[ $id_or_string ] = false;
				}
			}

			$value = $cache[ $id_or_string ];

			// If it does have a hash set, and the hash is expected, use it.
			// This check allows users to change the hash structure using the
			// gravityview_entry_hash filter and have the old hashes expire.
			if ( empty( $value ) || $value !== $hash ) {
				gravityview()->log->debug(
					'Setting hash for entry {entry}: {hash}',
					[
						'entry' => $id_or_string,
						'hash'  => $hash,
					]
				);
				gform_update_meta( $id_or_string, 'gravityview_unique_id', $hash, \GV\Utils::get( $entry, 'form_id' ) );
			}

			$slug = $hash;

			unset( $value, $hash );
		}

		return sanitize_title( $slug );
	}

	/**
	 * If using the entry custom slug feature, make sure the new entries have the custom slug created and saved as meta
	 *
	 * Triggered by add_action( 'gform_entry_created', array( 'GravityView_API', 'entry_create_custom_slug' ), 10, 2 );
	 *
	 * @param $entry array Gravity Forms entry object
	 * @param $form array Gravity Forms form object
	 */
	public static function entry_create_custom_slug( $entry, $form ) {
		/**
		 * On entry creation, check if we are using the custom entry slug feature and update the meta.
		 *
		 * @param boolean $custom Should we process the custom entry slug?
		 */
		$custom = apply_filters( 'gravityview_custom_entry_slug', false );
		if ( $custom ) {
			// create the gravityview_unique_id and save it

			// Get the entry hash
			$hash = self::get_custom_entry_slug( $entry['id'], $entry );

			gravityview()->log->debug(
				'Setting hash for entry {entry_id}: {hash}',
				[
					'entry_id' => $entry['id'],
					'hash'     => $hash,
				]
			);

			gform_update_meta( $entry['id'], 'gravityview_unique_id', $hash, \GV\Utils::get( $entry, 'form_id' ) );

		}
	}


	/**
	 * return href for single entry
	 *
	 * @since 1.7.3 Added $add_directory_args parameter
	 * @since 2.7.2 Added $view_id parameter
	 * @since 2.10  Added $_GET args to links by default. Use `gravityview/entry_link/add_query_args` filter to override.
	 *
	 * @param  array|int $entry   Entry array or entry ID.
	 * @param  int|null  $post_id If wanting to define the parent post, pass a post ID.
	 * @param boolean   $add_directory_args True: Add args to help return to directory; False: only include args required to get to entry.
	 * @param int       $view_id
	 *
	 * @return string Link to the entry with the directory parent slug, or empty string if embedded post or View doesn't exist
	 */
	public static function entry_link( $entry, $post_id = null, $add_directory_args = true, $view_id = 0 ) {

		if ( ! empty( $entry ) && ! is_array( $entry ) ) {
			$entry = GVCommon::get_entry( $entry );
		} elseif ( empty( $entry ) ) {
			// @deprecated 3.0.0
			$entry = GravityView_frontend::getInstance()->getEntry();
		}

		// Second parameter used to be passed as $field; this makes sure it's not an array
		if ( ! is_numeric( $post_id ) ) {
			$post_id = null;
		}

		// Get the permalink to the View
		$directory_link = self::directory_link( $post_id, false );

		// No post ID? Get outta here.
		if ( empty( $directory_link ) ) {
			return '';
		}

		$query_arg_name = \GV\Entry::get_endpoint_name();

		if ( ! empty( $entry['_multi'] ) ) {
			$entry_slugs = [];
			$forms       = [];

			foreach ( $entry['_multi'] as $_multi ) {

				if ( $gv_multi = \GV\GF_Entry::from_entry( $_multi ) ) {
					$entry_slugs[] = $gv_multi->get_slug();
				} else {
					$entry_slugs[] = self::get_entry_slug( $_multi['id'], $_multi );
				}

				unset( $gv_multi );

				$forms[] = $_multi['form_id'];
			}

			$entry_slug = implode( ',', $entry_slugs );
		} else {

			// Fallback when
			if ( $gv_entry = \GV\GF_Entry::from_entry( $entry ) ) {
				$entry_slug = $gv_entry->get_slug();
			} else {
				$entry_slug = self::get_entry_slug( \GV\Utils::get( $entry, 'id' ), $entry );
			}

			unset( $gv_entry );
		}

		$args = [];

		/**
		 * Modify whether to include passed $_GET parameters to the end of the url.
		 *
		 * @since 2.10
		 * @param bool $add_query_params Whether to include passed $_GET parameters to the end of the Entry Link URL. Default: true.
		 */
		$add_query_args = apply_filters( 'gravityview/entry_link/add_query_args', true );

		if ( $add_query_args ) {
			$args = gv_get_query_args();
		}

		if ( get_option( 'permalink_structure' ) && ! is_preview() ) {

			/**
			 * Make sure the $directory_link doesn't contain any query otherwise it will break when adding the entry slug.
			 *
			 * @since 1.16.5
			 */
			$link_parts = explode( '?', $directory_link );

			$query = ! empty( $link_parts[1] ) ? '?' . $link_parts[1] : '';

			$directory_link = trailingslashit( $link_parts[0] ) . $query_arg_name . '/' . $entry_slug . '/' . $query;

		} else {

			$args[] = [ $query_arg_name => $entry_slug ];
		}

		/**
		 * @since 1.7.3
		 */
		if ( $add_directory_args && $add_query_args ) {

			// Prefer the explicit View; the rendering stack and legacy singleton are fallbacks.
			$link_view_id = $view_id ? (int) $view_id : gravityview_get_view_id();

			$args = array_merge( $args, PaginationKeys::request_pagination_args( $link_view_id ) );

			/**
			 * @since 1.7
			 */
			if ( $sort = \GV\Utils::_GET( 'sort' ) ) {
				$args['sort'] = $sort;
				$args['dir']  = \GV\Utils::_GET( 'dir' );
			}
		}

		if ( $post_id ) {
			$passed_post        = get_post( $post_id );
			$views              = \GV\View_Collection::from_post( $passed_post );
			$has_multiple_views = $views->count() > 1;
		} else {
			$has_multiple_views = class_exists( \GravityKit\GravityView\Data\ViewData::class ) && \GravityKit\GravityView\Data\ViewData::getInstance()->has_multiple_views();
		}

		if ( $has_multiple_views ) {
			$args['gvid'] = $view_id ? $view_id : gravityview_get_view_id();
		}

		return add_query_arg( $args, $directory_link );
	}
}
