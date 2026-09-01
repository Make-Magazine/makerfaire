<?php
/**
 * The Request abstract class.
 *
 * Knows more about the request than anyone else.
 *
 * @package GravityKit\GravityView\Request
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Request;

use GravityKit\GravityView\Search\Querying\SearchRequest;

/**
 * The Request abstract class.
 *
 * Knows more about the request than anyone else.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Request namespace.
 */
abstract class Request {
	/**
	 * Class constructor.
	 *
	 * @since 2.0
	 */
	public function __construct() {}

	/**
	 * Whether this request is something that is renderable.
	 *
	 * @since 2.5.2
	 *
	 * @return bool Yes or no.
	 */
	public function is_renderable() {

		// Use legacy class names for comparison since get_class() returns canonical PSR-4 names
		// but existing code references the GV\* aliases.
		$is_renderable = is_a( $this, 'GV\Frontend_Request' )
			|| is_a( $this, 'GV\Mock_Request' )
			|| is_a( $this, 'GV\REST\Request' );

		/**
		 * Is this request renderable?
		 *
		 * @since 2.5.2
		 *
		 * @param bool     $is_renderable Whether the request is renderable.
		 * @param \GV\Request $request       The Request object.
		 */
		return apply_filters( 'gravityview/request/is_renderable', $is_renderable, $this );
	}

	/**
	 * Check if WordPress is_admin(), and make sure not DOING_AJAX.
	 *
	 * @return boolean
	 */
	public static function is_admin() {
		$doing_ajax          = defined( 'DOING_AJAX' ) ? DOING_AJAX : false;
		$load_scripts_styles = preg_match( '#^/wp-admin/load-(scripts|styles).php$#', \GV\Utils::_SERVER( 'SCRIPT_NAME' ) );

		return is_admin() && ! ( $doing_ajax || $load_scripts_styles );
	}

	/**
	 * This is the frontend.
	 *
	 * @return boolean True or false.
	 */
	public static function is_frontend() {
		return ! is_admin();
	}

	/**
	 * Is this the Add Media / From URL preview request?
	 *
	 * Will not work in WordPress 4.8+
	 *
	 * @return boolean
	 */
	public static function is_add_oembed_preview() {
		/* phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reads the request shape only; nothing is written. */
		return ( self::is_ajax() && ! empty( $_POST['action'] ) && 'parse-embed' === $_POST['action'] && ! isset( $_POST['type'] ) );
	}

	/**
	 * Is this an AJAX call in progress?
	 *
	 * @return boolean
	 */
	public static function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Is this a REST request? Call after parse_request.
	 *
	 * @return boolean
	 */
	public static function is_rest() {
		return ! empty( $GLOBALS['wp']->query_vars['rest_route'] );
	}

	/**
	 * The current $post is a View, no?
	 *
	 * @api
	 * @since 2.0
	 * @since 2.16 Added $return_view parameter.
	 *
	 * @param bool $return_view Whether to return a View object or boolean.
	 *
	 * @return \GV\View|bool If the global $post is a View, returns the View or true, depending on $return_view. If not a View, returns false.
	 */
	public function is_view( $return_view = true ) {
		global $post;
		if ( $post && 'gravityview' === get_post_type( $post ) ) {
			return ( $return_view ) ? \GV\View::from_post( $post ) : true;
		}
		return false;
	}

	/**
	 * Checks whether this is a single entry request
	 *
	 * @api
	 * @since 2.0
	 * @todo tests
	 *
	 * @param int $form_id The form ID, since slugs can be non-unique. Default: 0.
	 *
	 * @return \GV\GF_Entry|false The entry requested or false.
	 */
	public function is_entry( $form_id = 0 ) {
		global $wp_query;

		if ( ! $wp_query ) {
			return false;
		}

		$id = \GV\Entry::get_endpoint_value();

		if ( ! $id ) {
			return false;
		}

		static $entries = [];

		if ( isset( $entries[ "$form_id:$id" ] ) ) {
			return $entries[ "$form_id:$id" ];
		}

		$view = $this->is_view();

		/**
		 * Not CPT, so probably a shortcode
		 */
		if ( ! $view ) {
			$view = gravityview()->views->get();
		}

		// If there are multiple Views on a page, the permalink _should_ include `gvid` to specify which View to use.
		if ( $view instanceof \GV\View_Collection ) {
			$gvid = \GV\Utils::get_view_id_from_request();
			$view = $view->get( $gvid );
		}

		/**
		 * A joined request.
		 */
		if ( $view instanceof \GV\View && $view->joins ) {
			$joins       = $view->joins;
			$forms       = array_merge( wp_list_pluck( $joins, 'join' ), wp_list_pluck( $joins, 'join_on' ) );
			$valid_forms = array_values( array_map( 'intval', array_unique( wp_list_pluck( $forms, 'ID' ) ) ) );

			$multientry = [];
			$seen_forms = [];

			foreach ( explode( ',', $id ) as $i => $entry_id ) {
				$positional_form = \GV\Utils::get( $valid_forms, $i, 0 );
				$e               = $positional_form ? \GV\GF_Entry::by_id( $entry_id, $positional_form ) : null;

				foreach ( $valid_forms as $valid_form ) {
					if ( $e ) {
						break;
					}

					$e = \GV\GF_Entry::by_id( $entry_id, $valid_form );
				}

				if ( ! $e ) {
					return false;
				}

				$entry_form_id = (int) $e['form_id'];

				if ( ! in_array( $entry_form_id, $valid_forms, true ) ) {
					return false;
				}

				// Multi_Entry keys entries by form, so a second entry from a form already in the
				// request would silently replace the first. A numeric id resolves regardless of
				// the form looked up, so only a hand-crafted URL gets here. Refuse it.
				if ( in_array( $entry_form_id, $seen_forms, true ) ) {
					return false;
				}

				$seen_forms[] = $entry_form_id;

				array_push( $multientry, $e );
			}

			$is_edit_entry = apply_filters( 'gravityview_is_edit_entry', false );

			if ( $is_edit_entry && 1 !== count( $multientry ) ) {
				return false;
			}

			$entry = \GV\Multi_Entry::from_entries( array_filter( $multientry ) );
		} elseif ( $view instanceof \GV\View && $view->unions ) {
			/**
			 * A unioned request: the entry may belong to the primary form or any union source,
			 * which are tried in that order so a slug shared by several forms always resolves
			 * to the same entry.
			 */
			$primary_form_id = (int) $form_id;

			if ( ! $primary_form_id && $view->form ) {
				$primary_form_id = (int) $view->form->ID;
			}

			$valid_forms = array_merge( [ $primary_form_id ], array_map( 'intval', array_keys( (array) $view->unions ) ) );
			$valid_forms = array_values( array_unique( array_filter( $valid_forms ) ) );

			$entry = null;

			foreach ( $valid_forms as $valid_form ) {
				$entry = \GV\GF_Entry::by_id( $id, $valid_form );

				if ( $entry ) {
					break;
				}
			}

			if ( ! $entry ) {
				return false;
			}

			if ( ! in_array( (int) $entry['form_id'], $valid_forms, true ) ) {
				return false;
			}
		} else {
			/**
			 * A regular one.
			 */
			$entry = \GV\GF_Entry::by_id( $id, $form_id );
		}

		$entries[ "$form_id:$id" ] = $entry;

		return $entry;
	}

	/**
	 * Checks whether this an edit entry request.
	 *
	 * @api
	 * @since 2.0
	 * @todo tests
	 *
	 * @param int $form_id The form ID, since slugs can be non-unique. Default: 0.
	 *
	 * @return \GV\Entry|false The entry requested or false.
	 */
	public function is_edit_entry( $form_id = 0 ) {
		$entry = $this->is_entry( $form_id );

		/**
		 * Checks whether we're currently on the Edit Entry screen.
		 *
		 * The Edit Entry functionality overrides this value.
		 *
		 * @since 2.0-beta.2
		 *
		 * @param bool $is_edit_entry Whether the current request is an Edit Entry request. Default: false.
		 */
		if ( $entry && apply_filters( 'gravityview_is_edit_entry', false ) ) {
			if ( $entry->is_multi() ) {
				return reset( $entry->entries );
			}

			return $entry;
		}

		return false;
	}

	/**
	 * Checks whether this an entry search request.
	 *
	 * @since 2.0
	 *
	 * @param \GravityKit\GravityView\View\View|int|null $view The View (or its ID) to scope the answer to, if known.
	 *
	 * @return boolean True if this is a search request.
	 *
	 * @api
	 */
	public function is_search( $view = null ) {
		// A View ID scopes the same as a View object, so a caller holding an ID
		// gets the per-View answer instead of silently falling back to the global
		// one. Anything else scopes to nothing (the global answer).
		$is_scopeable = $view instanceof \GravityKit\GravityView\View\View || is_numeric( $view );
		$scoped_view  = $is_scopeable ? $view : null;

		return SearchRequest::is_search_request( $this, $scoped_view );
	}
}
