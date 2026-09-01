<?php
/**
 * The default GravityView View class.
 *
 * Houses all base View functionality.
 *
 * Can be accessed as an array for old compatibility's sake
 * in line with the elements inside the \GravityView_View_Data::$views array.
 *
 * @package GravityKit\GravityView\View
 * @since 3.0.0
 */

namespace GravityKit\GravityView\View;

use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;
use GravityKit\GravityView\Foundation\Helpers\Arr;
use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;
use GF_Query;
use GravityKit\GravityView\Request\FrontendRequest;
use GravityKit\GravityView\Request\RestRequest;
use GravityKit\GravityView\Search\Querying\SearchRequest;
use GravityKitFoundation;
use GravityView_Cache;
use GravityView_Compatibility;
use GravityView_frontend;
use GV\Frontend_Request;
use GV\Search\Querying\Search_Request;
use GVCommon;

/**
 * The default GravityView View class.
 *
 * Houses all base View functionality.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\View namespace.
 */
#[\AllowDynamicProperties]
class View implements \ArrayAccess {

	/**
	 * @var string GravityView custom post type.
	 * */
	const POST_TYPE = 'gravityview';

	/**
	 * Option name for the per-site key that derives every View validation secret.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	const VALIDATION_SECRET_KEY_OPTION = 'gravityview_view_secret_key';

	/**
	 * Option holding the GMT timestamp when this site first ran the per-site-key code.
	 *
	 * Views created before this boundary may have embeds carrying the previous
	 * secret derivation; Views created after it use only the per-site secret.
	 * Established once, very early, on `gravityview/loaded` so the boundary is the
	 * upgrade/install moment and not whenever a secret first happens to be derived.
	 *
	 * @since 3.1.0
	 *
	 * @var string
	 */
	const VALIDATION_SECRET_KEY_ESTABLISHED_OPTION = 'gravityview_view_secret_key_established';

	/**
	 * @var \WP_Post The backing post instance.
	 */
	private $post;

	/**
	 * @var \GV\View_Settings The settings.
	 *
	 * @api
	 * @since 2.0
	 */
	public $settings;

	/**
	 * @var \GV\Widget_Collection The widgets attached here.
	 *
	 * @api
	 * @since 2.0
	 */
	public $widgets;

	/**
	 * @var \GV\GF_Form|\GV\Form The backing form for this view.
	 *
	 * Contains the form that is sourced for entries in this view.
	 *
	 * @api
	 * @since 2.0
	 */
	public $form;

	/**
	 * @var \GV\Field_Collection The fields for this view.
	 *
	 * Contains all the fields that are attached to this view.
	 *
	 * @api
	 * @since 2.0
	 */
	public $fields;

	/**
	 * @var \GravityKit\GravityView\Settings\ViewStyles The design-token registry adapter for this View.
	 *
	 * Provides token discovery, sanitisation, and CSS emission for the
	 * View's saved palette / typography / spacing / effects overrides.
	 * Backed by static methods; the property is a thin namespace handle
	 * so callers can write `$view->styles->emit_css_declarations( ... )`
	 * (see also `scoped_selector()`, `sticky_header_css()`, and
	 * `prefers_contrast_css()` for the per-View emission helpers).
	 *
	 * @api
	 * @since 3.0.0
	 */
	public $styles;

	/**
	 * @var string A unique anchor ID used to wrap Views.
	 *
	 * @see View_Renderer::render() Dynamically set in hooks here.
	 *
	 * @since 2.15
	 */
	private $anchor_id;

	/**
	 * @var array
	 *
	 * Internal static cache for gets, and whatnot.
	 * This is not persistent, resets across requests.

	 * @internal
	 */
	private static $cache = array();

	/**
	 * @var array Stack to track currently rendering Views (for embedded View detection).
	 *
	 * @since 2.46.2
	 *
	 * @internal
	 */
	private static $rendering_stack = array();

	/**
	 * @var \GV\Join[] The joins for all sources in this view.
	 *
	 * @api
	 * @since 2.0.1
	 */
	public $joins = array();

	/**
	 * @var \GV\Field[][] The unions for all sources in this view.
	 *                    An array of fields grouped by form_id keyed by
	 *                    main field_id:
	 *
	 *                    array(
	 *                        $form_id => array(
	 *                            $field_id => $field,
	 *                            $field_id => $field,
	 *                        )
	 *                    )
	 *
	 * @api
	 * @since 2.2.2
	 */
	public $unions = array();

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->settings = new \GV\View_Settings();
		$this->fields   = new \GV\Field_Collection();
		$this->widgets  = new \GV\Widget_Collection();
		$this->styles   = new \GravityKit\GravityView\Settings\ViewStyles();
	}

	/**
	 * Register the gravityview WordPress Custom Post Type.
	 *
	 * @internal
	 * @return void
	 */
	public static function register_post_type() {
		/** Register only once */
		if ( post_type_exists( self::POST_TYPE ) ) {
			return;
		}

		if ( ! gravityview()->plugin->is_compatible() ) {
			GravityView_Compatibility::override_post_pages_when_compatibility_fails();
		}

		/**
		 * Make GravityView Views hierarchical by returning TRUE.
		 * This will allow for Views to be nested with Parents and also allows for menu order to be set in the Page Attributes metabox
		 *
		 * @since 1.13
		 * @param boolean $is_hierarchical Default: false
		 */
		$is_hierarchical = (bool) apply_filters( 'gravityview_is_hierarchical', false );

		$supports = array( 'title', 'revisions' );

		if ( $is_hierarchical ) {
			$supports[] = 'page-attributes';
		}

		/**
		 * @filter  `gravityview_post_type_supports` Modify post type support values for `gravityview` post type
		 * @see add_post_type_support()
		 * @since 1.15.2
		 * @param array $supports Array of features associated with a functional area of the edit screen. Default: 'title', 'revisions'. If $is_hierarchical, also 'page-attributes'
		 * @param boolean $is_hierarchical Do Views support parent/child relationships? See `gravityview_is_hierarchical` filter.
		 */
		$supports = apply_filters( 'gravityview_post_type_support', $supports, $is_hierarchical );

		/** Register Custom Post Type - gravityview */
		$labels = array(
			'name'                   => _x( 'Views', 'Post Type General Name', 'gk-gravityview' ),
			'singular_name'          => _x( 'View', 'Post Type Singular Name', 'gk-gravityview' ),
			'menu_name'              => _x( 'Views', 'Menu name', 'gk-gravityview' ),
			'parent_item_colon'      => __( 'Parent View:', 'gk-gravityview' ),
			'all_items'              => __( 'All Views', 'gk-gravityview' ),
			'view_item'              => _x( 'View', 'View Item', 'gk-gravityview' ),
			'add_new_item'           => __( 'Add New View', 'gk-gravityview' ),
			'add_new'                => __( 'New View', 'gk-gravityview' ),
			'edit_item'              => __( 'Edit View', 'gk-gravityview' ),
			'update_item'            => __( 'Update View', 'gk-gravityview' ),
			'search_items'           => __( 'Search Views', 'gk-gravityview' ),
			'not_found'              => \GravityView_Admin::no_views_text(),
			'not_found_in_trash'     => __( 'No Views found in Trash', 'gk-gravityview' ),
			'filter_items_list'      => __( 'Filter Views list', 'gk-gravityview' ),
			'items_list_navigation'  => __( 'Views list navigation', 'gk-gravityview' ),
			'items_list'             => __( 'Views list', 'gk-gravityview' ),
			'view_items'             => __( 'See Views', 'gk-gravityview' ),
			'attributes'             => __( 'View Attributes', 'gk-gravityview' ),
			'item_updated'           => __( 'View updated.', 'gk-gravityview' ),
			'item_published'         => __( 'View published.', 'gk-gravityview' ),
			'item_reverted_to_draft' => __( 'View reverted to draft.', 'gk-gravityview' ),
			'item_scheduled'         => __( 'View scheduled.', 'gk-gravityview' ),
		);

		$args = array(
			'label'               => __( 'view', 'gk-gravityview' ),
			'description'         => __( 'Create views based on a Gravity Forms form', 'gk-gravityview' ),
			'labels'              => $labels,
			'supports'            => $supports,
			'hierarchical'        => $is_hierarchical,
			/**
			 * Should Views be directly accessible, or only visible using the shortcode?
			 *
			 * @see https://codex.wordpress.org/Function_Reference/register_post_type#public
			 * @since 1.15.2
			 * @param boolean `true`: allow Views to be accessible directly. `false`: Only allow Views to be embedded via shortcode. Default: `true`
			 * @param int $view_id The ID of the View currently being requested. `0` for general setting
			 */
			'public'              => apply_filters( 'gravityview_direct_access', gravityview()->plugin->is_compatible(), 0 ),
			'show_ui'             => true,
			'show_in_menu'        => false, // Menu items are added in \GV\Plugin::add_to_gravitykit_admin_menu().
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 17,
			'menu_icon'           => '',
			'can_export'          => true,
			/**
			 * Enable Custom Post Type archive?
			 *
			 * @since 1.7.3
			 * @param boolean False: don't have frontend archive; True: yes, have archive. Default: false
			 */
			'has_archive'         => apply_filters( 'gravityview_has_archive', false ),
			'exclude_from_search' => true,
			'rewrite'             => array(
				/**
				 * Modify the url part for a View.
				 *
				 * @since 1.0
				 *
				 * @see https://docs.gravitykit.com/article/62-changing-the-view-slug
				 *
				 * @param string $slug The slug shown in the URL.
				 */
				'slug'       => apply_filters( 'gravityview_slug', 'view' ),

				/**
				 * Should the permalink structure be prepended with the front base.
				 *
				 * Example: If your permalink structure is `/blog/`, then your links will be:
				 * - `false` → `/view/`
				 * - `true` → `/blog/view/`
				 *
				 * @since 2.0
				 *
				 * @see https://codex.wordpress.org/Function_Reference/register_post_type
				 *
				 * @param bool $with_front Whether to prepend the front base. Default: true.
				 */
				'with_front' => apply_filters( 'gravityview/post_type/with_front', true ),
			),
			'capability_type'     => 'gravityview',
			'map_meta_cap'        => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Add extra rewrite endpoints.
	 *
	 * @return void
	 */
	public static function add_rewrite_endpoint() {
		/**
		 * CSV.
		 */
		global $wp_rewrite;

		$slug     = apply_filters( 'gravityview_slug', 'view' );
		$slug     = ( '/' !== $wp_rewrite->front ) ? sprintf( '%s/%s', trim( $wp_rewrite->front, '/' ), $slug ) : $slug;
		$csv_rule = array( sprintf( '%s/([^/]+)/csv/?', $slug ), 'index.php?gravityview=$matches[1]&csv=1', 'top' );
		$tsv_rule = array( sprintf( '%s/([^/]+)/tsv/?', $slug ), 'index.php?gravityview=$matches[1]&tsv=1', 'top' );

		add_filter(
			'query_vars',
			function ( $query_vars ) {
				$query_vars[] = 'csv';
				$query_vars[] = 'tsv';
				return $query_vars;
			}
		);

		if ( ! isset( $wp_rewrite->extra_rules_top[ $csv_rule[0] ] ) ) {
			call_user_func_array( 'add_rewrite_rule', $csv_rule );
			call_user_func_array( 'add_rewrite_rule', $tsv_rule );
		}
	}

	/**
	 * A renderer filter for the View post type content.
	 *
	 * @param string $content Should be empty, as we don't store anything there.
	 *
	 * @return string $content The view content as output by the renderers.
	 */
	public static function content( $content ) {
		$request = gravityview()->request;

		// Plugins may run through the content in the header. WP SEO does this for its OpenGraph functionality.
		if ( ! defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			if ( ! did_action( 'loop_start' ) ) {
				\gravityview()->log->debug( 'Not processing yet: loop_start hasn\'t run yet. Current action: {action}', array( 'action' => current_filter() ) );
				return $content;
			}

			// We don't want this filter to run infinite loop on any post content fields.
			remove_filter( 'the_content', [ 'GV\View', 'content' ] );
		}

		/**
		 * This is not a View. Bail.
		 *
		 * Shortcodes and oEmbeds and whatnot will be handled
		 *  elsewhere.
		 */
		$view = $request->is_view();

		if ( ! $view ) {
			return $content;
		}

		/**
		 * Check permissions.
		 */
		$error = $view->can_render( null, $request );

		while ( $error ) {
			if ( ! is_wp_error( $error ) ) {
				break;
			}

			switch ( str_replace( 'gravityview/', '', $error->get_error_code() ) ) {
				case 'post_password_required':
					return get_the_password_form( $view->ID );
				case 'in_trash':
					return '';  // Views in trash are unreachable when accessed as a CPT, but adding this just in case. We do not give a hint that this content exists, for security purposes.
				default:
					return \GravityView_Error_Messages::get( $error->get_error_code(), $view, 'shortcode' );
			}

			return $content;
		}

		$is_admin_and_can_view = $view->settings->get( 'admin_show_all_statuses' ) && GVCommon::has_cap( 'gravityview_moderate_entries', $view->ID );

		/**
		 * Editing a single entry.
		 */
		$entry = $request->is_edit_entry( $view->form ? $view->form->ID : 0 );

		if ( $entry ) {
			$check = $entry->check_access( $view );
			if ( is_wp_error( $check ) ) {
				return \GravityView_Error_Messages::get( $check, $view, 'shortcode', $entry );
			}

			$renderer = new \GV\Edit_Entry_Renderer();
			return $renderer->render( $entry, $view, $request );
		}

		/**
		 * Viewing a single entry.
		 */
		$entry = $request->is_entry( $view->form ? $view->form->ID : 0 );

		if ( $entry ) {

			$entryset = $entry->is_multi() ? $entry->entries : array( $entry );

			$custom_slug = apply_filters( 'gravityview_custom_entry_slug', false );
			$ids         = explode( ',', get_query_var( \GV\Entry::get_endpoint_name() ) );

			$show_only_approved = $view->settings->get( 'show_only_approved' );

			foreach ( $entryset as $e ) {

				$check = $e->check_access( $view );
				if ( is_wp_error( $check ) ) {
					return \GravityView_Error_Messages::get( $check, $view, 'shortcode', $e );
				}

				$error = GVCommon::check_entry_display( $e->as_entry(), $view );

				if ( is_wp_error( $error ) ) {
					\gravityview()->log->error(
						'Entry ID #{entry_id} is not approved for viewing: {message}',
						array(
							'entry_id' => $e->ID,
							'message'  => $error->get_error_message(),
						)
					);
					return \GravityView_Error_Messages::get( $error->get_error_code(), $view, 'shortcode', $e );
				}
			}

			$renderer = new \GV\Entry_Renderer();
			return $renderer->render( $entry, $view, $request );
		}

		/**
		 * Plain old View.
		 */
		$renderer = new \GV\View_Renderer();
		return $renderer->render( $view, $request );
	}

	/**
	 * Checks whether this view can be accessed or not.
	 *
	 * @param string[]    $context The context we're asking for access from.
	 *                             Can any and as many of one of:
	 *                                 edit      An edit context.
	 *                                 single    A single context.
	 *                                 cpt       The custom post type single page accessed.
	 *                                 shortcode Embedded as a shortcode.
	 *                                 lightbox  Rendered inside a GravityView entry lightbox.
	 *                                 oembed    Embedded as an oEmbed.
	 *                                 rest      A REST call.
	 *                                 csv       A CSV export.
	 * @param \GV\Request $request The request.
	 *
	 * @return bool|\WP_Error An error if this View shouldn't be rendered here.
	 */
	public function can_render( $context = null, $request = null ) {
		if ( ! $request ) {
			$request = gravityview()->request;
		}

		if ( ! is_array( $context ) ) {
			$context = array();
		}

		/**
		 * Whether the view can be rendered or not.
		 *
		 * @param null|bool|\WP_Error $allow_render  The result. Default: null.
		 * @param \GV\View       $view  The view.
		 * @param string[]       $context Render contexts, e.g. `shortcode`, `lightbox`, `rest`, `csv`; see \GV\View::can_render for the full list. Return a `WP_Error` to deny a specific context (e.g. the entry lightbox).
		 * @param \GV\Request    $request The request.
		 */
		$allow_render = apply_filters( 'gravityview/view/can_render', null, $this, $context, $request );
		if ( ! is_null( $allow_render ) ) {
			return $allow_render;
		}

		if ( in_array( 'rest', $context, true ) ) {
			// REST.
			if ( gravityview()->plugin->settings->get( 'rest_api' ) && '1' === $this->settings->get( 'rest_disable' ) ) {
				return new \WP_Error( 'gravityview/rest_disabled' );
			} elseif ( ! gravityview()->plugin->settings->get( 'rest_api' ) && '1' !== $this->settings->get( 'rest_enable' ) ) {
				return new \WP_Error( 'gravityview/rest_disabled' );
			}
		}

		if ( in_array( 'csv', $context, true ) ) {
			if ( '1' !== $this->settings->get( 'csv_enable' ) ) {
				return new \WP_Error( 'gravityview/csv_disabled', 'The CSV endpoint is not enabled for this View' );
			}
		}

		/**
		 * This View is password protected. Nothing to do here.
		 */
		if ( post_password_required( $this->ID ) ) {
			\gravityview()->log->notice( 'Post password is required for View #{view_id}', array( 'view_id' => $this->ID ) );
			return new \WP_Error( 'gravityview/post_password_required' );
		}

		if ( ! $this->form ) {
			\gravityview()->log->notice( 'View #{id} has no form attached to it.', array( 'id' => $this->ID ) );
			return new \WP_Error( 'gravityview/no_form_attached' );
		}

		// The Embed Only and Prevent Direct Access settings below gate direct access
		// to the View. A context containing `shortcode` (an embedded render) or
		// `lightbox` is exempt (so they don't 404 the lightbox route); every other
		// context (a direct CPT request, `rest`, `csv`, `oembed`) is subject to them.
		$skips_direct_access_checks = in_array( 'shortcode', $context, true ) || in_array( 'lightbox', $context, true );

		if ( ! $skips_direct_access_checks ) {
			/**
			 * Is this View directly accessible via a post URL?
			 *
			 * @see https://codex.wordpress.org/Function_Reference/register_post_type#public
			 */

			/**
			 * Should Views be directly accessible, or only visible using the shortcode?
			 *
			 * @deprecated 3.0.0
			 * @param boolean `true`: allow Views to be accessible directly. `false`: Only allow Views to be embedded. Default: `true`
			 * @param int $view_id The ID of the View currently being requested. `0` for general setting
			 */
			$direct_access = \GravityView_Deprecated_Hook_Notices::apply_filters( 'gravityview_direct_access', [ true, $this->ID ], '2.55', 'gravityview/view/output/direct' );

			/**
			 * Should this View be directly accessible?
			 *
			 * @since 2.0
			 *
			 * @param bool $direct_access Whether the View is directly accessible. Default: true.
			 * @param \GV\View $view The View we're trying to directly render here.
			 * @param \GV\Request $request The current request.
			 */
			if ( ! apply_filters( 'gravityview/view/output/direct', $direct_access, $this, $request ) ) {
				return new \WP_Error( 'gravityview/no_direct_access' );
			}

			/**
			 * Is this View an embed-only View? If so, don't allow rendering here,
			 *  as this is a direct request.
			 */
			if ( $this->settings->get( 'embed_only' ) && ! GVCommon::has_cap( 'read_private_gravityviews' ) ) {
				return new \WP_Error( 'gravityview/embed_only' );
			}
		}

		if ( 'trash' === get_post_status( $this->ID ) ) {
			return new \WP_Error( 'gravityview/in_trash' );
		}

		/** Private, pending, draft, etc. */
		$public_states = get_post_stati( array( 'public' => true ) );
		if ( ! in_array( $this->post_status, $public_states, true ) && ! GVCommon::has_cap( 'read_gravityview', $this->ID ) ) {
			\gravityview()->log->notice( 'The current user cannot access this View #{view_id}', array( 'view_id' => $this->ID ) );
			return new \WP_Error( 'gravityview/not_public' );
		}

		return true;
	}

	/**
	 * The IDs of every form this View spans: its own form plus any joined or unioned forms.
	 *
	 * @since 3.0.1
	 * @since 3.2.0 Includes unioned forms.
	 *
	 * @return int[]
	 */
	public function get_form_ids(): array {
		$form_ids = [ (int) ( $this->form->ID ?? 0 ) ];

		foreach ( $this->joins as $join ) {
			foreach ( [ $join->join, $join->join_on ] as $join_form ) {
				if ( $join_form instanceof \GV\GF_Form ) {
					$form_ids[] = (int) $join_form->ID;
				}
			}
		}

		foreach ( array_keys( $this->unions ) as $union_form_id ) {
			$form_ids[] = (int) $union_form_id;
		}

		return array_values( array_filter( array_unique( $form_ids ) ) );
	}

	/**
	 * A scalar, order-independent representation of the View's union field map.
	 *
	 * The union is assembled through `gform_gf_query_sql`, so the field map never reaches the primary
	 * query the entry cache hashes; the map has to be part of the cache key on its own.
	 *
	 * @since 3.2.0
	 *
	 * @return array<int, array<string, string>> Unioned form ID, then primary field ID to unioned field ID.
	 */
	private function get_union_signature(): array {
		$signature = [];

		foreach ( $this->unions as $union_form_id => $field_map ) {
			$fields = [];

			foreach ( $field_map as $field_id => $union_field ) {
				$fields[ (string) $field_id ] = (string) ( $union_field->ID ?? '' );
			}

			ksort( $fields );

			$signature[ (int) $union_form_id ] = $fields;
		}

		ksort( $signature );

		return $signature;
	}

	/**
	 * Get joins associated with a view
	 *
	 * @param \WP_Post $post GravityView CPT to get joins for.
	 *
	 * @api
	 * @since 2.0.11
	 *
	 * @return \GV\Join[] Array of \GV\Join instances
	 */
	public static function get_joins( $post ) {
		$joins = array();

		if ( ! gravityview()->plugin->supports( \GV\Plugin::FEATURE_JOINS ) ) {
			return $joins;
		}

		if ( ! $post || self::POST_TYPE !== get_post_type( $post ) ) {
			\gravityview()->log->error( 'Only "gravityview" post types can be \GV\View instances.' );
			return $joins;
		}

		$joins_meta = get_post_meta( $post->ID, '_gravityview_form_joins', true );

		if ( empty( $joins_meta ) ) {
			return $joins;
		}

		// Ensure the joins meta is an array to prevent foreach errors.
		$joins_meta = (array) $joins_meta;

		foreach ( $joins_meta as $meta ) {
			if ( ! is_array( $meta ) || 4 !== count( $meta ) ) {
				continue;
			}

			[ $join, $join_column, $join_on, $join_on_column ] = $meta;

			$join    = \GV\GF_Form::by_id( $join );
			$join_on = \GV\GF_Form::by_id( $join_on );

			$join_column    = is_numeric( $join_column ) ? \GV\GF_Field::by_id( $join, $join_column ) : \GV\Internal_Field::by_id( $join_column );
			$join_on_column = is_numeric( $join_on_column ) ? \GV\GF_Field::by_id( $join_on, $join_on_column ) : \GV\Internal_Field::by_id( $join_on_column );

			$joins [] = new \GV\Join( $join, $join_column, $join_on, $join_on_column );
		}

		return $joins;
	}

	/**
	 * Get joined forms associated with a view
	 * In no particular order.
	 *
	 * @since 2.0.11
	 *
	 * @api
	 * @since 2.0
	 * @param int $post_id ID of the View.
	 *
	 * @return \GV\GF_Form[] Array of \GV\GF_Form instances
	 */
	public static function get_joined_forms( $post_id ) {
		$forms = array();

		if ( ! gravityview()->plugin->supports( \GV\Plugin::FEATURE_JOINS ) ) {
			return $forms;
		}

		if ( ! $post_id || ! gravityview()->plugin->supports( \GV\Plugin::FEATURE_JOINS ) ) {
			return $forms;
		}

		if ( empty( $post_id ) ) {
			\gravityview()->log->error( 'Cannot get joined forms; $post_id was empty' );
			return $forms;
		}

		$joins_meta = get_post_meta( $post_id, '_gravityview_form_joins', true );

		if ( empty( $joins_meta ) ) {
			return $forms;
		}

		// Ensure the joins meta is an array. It sometimes is a string.
		$joins_meta = (array) $joins_meta;

		foreach ( $joins_meta  as $meta ) {
			if ( ! is_array( $meta ) || 4 !== count( $meta ) ) {
				continue;
			}

			[ $join, $join_column, $join_on, $join_on_column ] = $meta;

			$form = \GV\GF_Form::by_id( $join_on );

			if ( $form ) {
				$forms[ $join_on ] = $form;
			}

			$form = \GV\GF_Form::by_id( $join );

			if ( $form ) {
				$forms[ $join ] = $form;
			}
		}

		return $forms;
	}

	/**
	 * Get unions associated with a view
	 *
	 * @param \WP_Post $post GravityView CPT to get unions for.
	 *
	 * @api
	 * @since 2.2.2
	 *
	 * @return \GV\Field[][] Array of unions (see self::$unions)
	 */
	public static function get_unions( $post ) {
		$unions = array();

		if ( ! $post || self::POST_TYPE !== get_post_type( $post ) ) {
			\gravityview()->log->error( 'Only "gravityview" post types can be \GV\View instances.' );
			return $unions;
		}

		$fields = get_post_meta( $post->ID, '_gravityview_directory_fields', true );

		if ( empty( $fields ) ) {
			return $unions;
		}

		// Ensure the fields meta is an array to prevent foreach errors.
		$fields = (array) $fields;

		foreach ( $fields as $location => $_fields ) {
			if ( 0 !== strpos( $location, 'directory_' ) ) {
				continue;
			}

			foreach ( $_fields as $field ) {
				if ( ! empty( $field['unions'] ) ) {
					foreach ( $field['unions'] as $form_id => $field_id ) {
						if ( ! isset( $unions[ $form_id ] ) ) {
							$unions[ $form_id ] = array();
						}

						$unions[ $form_id ][ $field['id'] ] = is_numeric( $field_id )
							? \GV\GF_Field::by_id( \GV\GF_Form::by_id( $form_id ), $field_id )
							: \GV\Internal_Field::by_id( $field_id );
					}
				}
			}

			break;
		}

		if ( $unions ) {
			if ( ! gravityview()->plugin->supports( \GV\Plugin::FEATURE_UNIONS ) ) {
				\gravityview()->log->error( 'Cannot get unions; unions feature not supported.' );
			}
		}

		// @todo We'll probably need to backfill null unions

		return $unions;
	}

	/**
	 * Resolves a configured field to its unioned counterpart, if the form has a union mapping.
	 *
	 * The slot's display configuration (position, label, visibility, class, and link settings) is
	 * carried over so the unioned field is presented exactly as the slot it replaces; only the value
	 * comes from the unioned form.
	 *
	 * @since 3.2.0
	 *
	 * @param \GV\Field  $field   The configured field from the primary form.
	 * @param int|string $form_id The form ID of the entry being rendered.
	 *
	 * @return \GV\Field The unioned field for the form, or the original field when there is no mapping.
	 */
	public function maybe_union_field( $field, $form_id ) {
		if ( ! isset( $this->unions[ $form_id ] ) || (int) $field->form_id === (int) $form_id ) {
			return $field;
		}

		$resolved = $this->unions[ $form_id ][ $field->ID ] ?? $this->union_field_from_inputs( $field, $form_id );

		if ( ! $resolved ) {
			if ( $field instanceof \GV\Internal_Field ) {
				return $field;
			}

			$resolved = \GV\Internal_Field::from_configuration( [ 'id' => 'custom' ] );
		}

		$resolved           = clone $resolved;
		$resolved->position = $field->position;
		/* phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Public property of \GV\Field. */
		$resolved->UID           = $field->UID;
		$resolved->cap           = $field->cap;
		$resolved->label         = $field->label;
		$resolved->custom_label  = $field->custom_label;
		$resolved->show_label    = $field->show_label;
		$resolved->custom_class  = $field->custom_class;
		$resolved->search_filter = $field->search_filter;
		$resolved->show_as_link  = $field->show_as_link;
		$resolved->new_window    = $field->new_window;

		return $resolved;
	}

	/**
	 * Resolves a composite field through the union mapping of any of its inputs.
	 *
	 * A Name or Address field is mapped per input, so a slot holding the whole field has no
	 * mapping of its own. Each input's own mapping is taken as it stands, whatever field type
	 * it points at: a Name is commonly a pair of plain text fields on the unioned form.
	 *
	 * @since 3.2.0
	 *
	 * @param \GV\Field  $field   The configured field from the primary form.
	 * @param int|string $form_id The form ID of the entry being rendered.
	 *
	 * @return \GV\Field|null The unioned field, or null when its inputs carry no usable mapping.
	 */
	private function union_field_from_inputs( $field, $form_id ) {
		$source = $field->field ?? null;

		if ( ! $field instanceof \GravityKit\GravityView\Field\FieldGravityForms
			|| ! $source instanceof \GF_Field
			|| ! $source->get_entry_inputs()
		) {
			return null;
		}

		$inputs = [];

		foreach ( $source->get_entry_inputs() as $input ) {
			$mapped = $this->unions[ $form_id ][ $input['id'] ] ?? null;

			if ( $mapped ) {
				$inputs[ $input['id'] ] = $mapped;
			}
		}

		$composite = \GravityKit\GravityView\Field\UnionCompositeField::from_inputs( $field, $form_id, $inputs );

		return $composite->has_inputs() ? $composite : null;
	}

	/**
	 * Construct a \GV\View instance from a \WP_Post.
	 *
	 * @param \WP_Post $post The \WP_Post instance to wrap.
	 *
	 * @api
	 * @since 2.0
	 * @return \GV\View|null An instance around this \WP_Post if valid, null otherwise.
	 */
	public static function from_post( $post ) {

		if ( ! $post || self::POST_TYPE !== get_post_type( $post ) ) {
			return null;
		}

		$view = \GV\Utils::get( self::$cache, "View::from_post:{$post->ID}" );

		if ( $view ) {
			/**
			 * Override View.
			 *
			 * @param \GV\View $view The View instance pointer.
			 * @since 2.1
			 */
			do_action_ref_array( 'gravityview/view/get', array( &$view ) );

			return $view;
		}

		$view       = new self();
		$view->post = $post;

		self::$cache[ "View::from_post:{$post->ID}" ] = &$view;

		/** Get connected form. */
		$view->form = \GV\GF_Form::by_id( $view->_gravityview_form_id );
		global $pagenow;
		if ( ! $view->form && 'post-new.php' !== $pagenow ) {
			\gravityview()->log->error(
				'View #{view_id} tried attaching non-existent Form #{form_id} to it.',
				array(
					'view_id' => $view->ID,
					'form_id' => $view->_gravityview_form_id ? $view->_gravityview_form_id : 0,
				)
			);
		}

		$view->joins = $view::get_joins( $post );

		$view->unions = $view::get_unions( $post );

		/**
		 * Filter the View fields' configuration array.
		 *
		 * @since 1.6.5
		 *
		 * @deprecated 3.0.0 `gravityview/view/configuration/fields` or `gravityview/view/fields` filters.
		 *
		 * @param $fields array Multi-array of fields with first level being the field zones.
		 * @param $view_id int The View the fields are being pulled for.
		 */
		$configuration = \GravityView_Deprecated_Hook_Notices::apply_filters( 'gravityview/configuration/fields', [ (array) $view->_gravityview_directory_fields, $view->ID ], '2.55', 'gravityview/view/configuration/fields' );

		/**
		 * Filter the View fields' configuration array.
		 *
		 * @since 2.0
		 *
		 * @param array $fields Multi-array of fields with first level being the field zones.
		 * @param \GV\View $view The View the fields are being pulled for.
		 */
		$configuration = apply_filters( 'gravityview/view/configuration/fields', $configuration, $view );

		/**
		 * Filter the Field Collection for this View.
		 *
		 * @since 2.0
		 *
		 * @param \GV\Field_Collection $fields A collection of fields.
		 * @param \GV\View $view The View the fields are being pulled for.
		 */
		$view->fields = apply_filters( 'gravityview/view/fields', \GV\Field_Collection::from_configuration( $configuration ), $view );

		/**
		 * Filter the View widgets' configuration array.
		 *
		 * @since 2.0
		 *
		 * @param array $fields Multi-array of widgets with first level being the field zones.
		 * @param \GV\View $view The View the widgets are being pulled for.
		 */
		$configuration = apply_filters( 'gravityview/view/configuration/widgets', (array) $view->_gravityview_directory_widgets, $view );

		/**
		 * Filter the Widget Collection for this View.
		 *
		 * @since 2.0
		 *
		 * @param \GV\Widget_Collection $widgets A collection of widgets.
		 * @param \GV\View $view The View the widgets are being pulled for.
		 */
		$view->widgets = apply_filters( 'gravityview/view/widgets', \GV\Widget_Collection::from_configuration( $configuration ), $view );

		/** View configuration. */
		$view->settings->update( gravityview_get_template_settings( $view->ID ) );

		/** Add the template names into the settings. */
		$view->settings->update( array( 'template' => gravityview_get_directory_entries_template_id( $view->ID ) ) );
		$view->settings->update( array( 'template_single_entry' => gravityview_get_single_entry_template_id( $view->ID ) ) );

		/** View basics. */
		$view->settings->update(
			array(
				'id' => $view->ID,
			)
		);

		/**
		 * Override View.
		 *
		 * @param \GV\View $view The View instance pointer.
		 * @since 2.1
		 */
		do_action_ref_array( 'gravityview/view/get', array( &$view ) );

		return $view;
	}

	/**
	 * Flush the view cache.
	 *
	 * @param int $view_id The View to reset cache for. Optional. Default: resets everything.
	 *
	 * @internal
	 */
	public static function _flush_cache( $view_id = null ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Established public API method name; renaming would break back-compat.
		if ( $view_id ) {
			unset( self::$cache[ "View::from_post:$view_id" ] );
			return;
		}
		self::$cache = array();
	}

	/**
	 * Construct a \GV\View instance from a post ID.
	 *
	 * @param int|string $post_id The post ID.
	 *
	 * @api
	 * @since 2.0
	 * @return \GV\View|null An instance around this \WP_Post or null if not found.
	 */
	public static function by_id( $post_id ) {
		if ( ! $post_id ) {
			return null;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return null;
		}

		return self::from_post( $post );
	}

	/**
	 * Gets the source of the field.
	 *
	 * @since 2.33
	 *
	 * @param \GV\Field $field The field.
	 * @param \GV\View  $view The view.
	 *
	 * @return \GV\GF_Form|\GV\Internal_Source
	 */
	public static function get_source( $field, $view ) {
		if ( ! is_numeric( $field->ID ) ) {
			return new \GV\Internal_Source();
		}

		$form_id = $field->field->formId ?? null;

		// If the field's form differs from the main view form, get the form from the joined entries.
		if ( $form_id && $view->form->ID != $form_id && ! empty( $view->joins ) ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseNotEqual -- Form IDs may be int or numeric string depending on source; loose comparison is intentional.
			foreach ( $view->joins as $join ) {
				if ( isset( $join->join_on->ID ) && $join->join_on->ID == $form_id ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual -- Form IDs may be int or numeric string depending on source; loose comparison is intentional.
					return $join->join_on;
				}
			}

			// Edge case where the form cannot be retrieved from the joins.
			return \GV\GF_Form::by_id( $form_id );
		}

		// Return the main view form.
		return $view->form;
	}

	/**
	 * Determines if a view exists to begin with.
	 *
	 * @param int|\WP_Post|null $view The WordPress post ID, a \WP_Post object or null for global $post.
	 *
	 * @api
	 * @since 2.0
	 * @return bool Whether the post exists or not.
	 */
	public static function exists( $view ) {
		return self::POST_TYPE === get_post_type( $view );
	}

	/**
	 * Starts tracking that a View is being rendered.
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @param int $view_id The View ID being rendered.
	 */
	public static function push_rendering( $view_id ) {
		self::$rendering_stack[] = $view_id;
	}

	/**
	 * Stops tracking that a View is being rendered
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @return int|null The View ID that was being rendered, or null if stack was empty.
	 */
	public static function pop_rendering() {
		return array_pop( self::$rendering_stack );
	}

	/**
	 * Checks if a View is currently being rendered (embedded View detection).
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @param int|null $view_id If provided, check if this specific View is being rendered. If null, check if any view is being rendered.
	 *
	 * @return bool True if the View (or any View) is being rendered.
	 */
	public static function is_rendering( $view_id = null ) {
		if ( null === $view_id ) {
			return ! empty( self::$rendering_stack );
		}

		return in_array( $view_id, self::$rendering_stack, true );
	}

	/**
	 * Returns the currently rendering View ID (the most recent one).
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @return int|null The View ID currently being rendered, or null if none.
	 */
	public static function get_current_rendering() {
		if ( empty( self::$rendering_stack ) ) {
			return null;
		}

		return end( self::$rendering_stack );
	}

	/**
	 * Returns all currently rendering View IDs.
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @return array Array of View IDs in rendering order (oldest to newest).
	 */
	public static function get_rendering_stack() {
		return self::$rendering_stack;
	}

	/**
	 * Checks if View is the primary (first) rendering View.
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @param int $view_id The View ID to check.
	 *
	 * @return bool True if the View is the primary rendering View.
	 */
	public static function is_primary_view( $view_id ) {
		return ! empty( self::$rendering_stack ) && self::$rendering_stack[0] === $view_id;
	}

	/**
	 * Checks if View is embedded (not the first in stack).
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @param int $view_id The View ID to check.
	 *
	 * @return bool True if the View is embedded within another View.
	 */
	public static function is_embedded_view( $view_id ) {
		return in_array( $view_id, self::$rendering_stack, true ) && $view_id !== self::$rendering_stack[0];
	}

	/**
	 * Returns the parent View of an embedded View.
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @param int $view_id The View ID to get parent for.
	 *
	 * @return int|null The parent View ID, or null if not embedded or no parent.
	 */
	public static function get_parent_view( $view_id ) {
		$position = array_search( $view_id, self::$rendering_stack, true );

		if ( $position > 0 ) {
			return self::$rendering_stack[ $position - 1 ];
		}

		return null;
	}

	/**
	 * Returns the rendering depth of a View (how many levels deep it's nested).
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @param int $view_id The View ID to check.
	 *
	 * @return int|false The depth (0 for primary, 1+ for nested), or false if not rendering.
	 */
	public static function get_rendering_depth( $view_id ) {
		return array_search( $view_id, self::$rendering_stack, true );
	}

	/**
	 * Resets the rendering stack.
	 *
	 * @internal
	 *
	 * @since 2.46.2
	 *
	 * @internal
	 */
	public static function reset_rendering_stack() {
		self::$rendering_stack = [];
	}

	/**
	 * ArrayAccess compatibility layer with GravityView_View_Data::$views
	 *
	 * @internal
	 * @deprecated 3.0.0
	 * @since 2.0
	 *
	 * @param mixed $offset The offset to check.
	 *
	 * @return bool Whether the offset exists or not, limited to GravityView_View_Data::$views element keys.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		$data_keys = array( 'id', 'view_id', 'form_id', 'template_id', 'atts', 'fields', 'widgets', 'form' );
		return in_array( $offset, $data_keys, true );
	}

	/**
	 * ArrayAccess compatibility layer with GravityView_View_Data::$views
	 *
	 * Maps the old keys to the new data;
	 *
	 * @internal
	 * @deprecated 3.0.0
	 * @since 2.0
	 *
	 * @param mixed $offset The offset to get.
	 *
	 * @return mixed The value of the requested view data key limited to GravityView_View_Data::$views element keys. If offset not found, return null.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {

		\gravityview()->log->notice( 'This is a \GV\View object should not be accessed as an array.' );

		if ( ! isset( $this[ $offset ] ) ) {
			return null;
		}

		switch ( $offset ) {
			case 'id':
			case 'view_id':
				return $this->ID;
			case 'form':
				return $this->form;
			case 'form_id':
				return $this->form ? $this->form->ID : null;
			case 'atts':
				return $this->settings->as_atts();
			case 'template_id':
				return $this->settings->get( 'template' );
			case 'widgets':
				return $this->widgets->as_configuration();
		}

		return null;
	}

	/**
	 * ArrayAccess compatibility layer with GravityView_View_Data::$views
	 *
	 * @internal
	 * @deprecated 3.0.0
	 * @since 2.0
	 *
	 * @param mixed $offset The offset to set.
	 * @param mixed $value  The value to set.
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		\gravityview()->log->error( 'The old view data is no longer mutable. This is a \GV\View object should not be accessed as an array.' );
	}

	/**
	 * ArrayAccess compatibility layer with GravityView_View_Data::$views
	 *
	 * @internal
	 * @deprecated 3.0.0
	 * @since 2.0
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		\gravityview()->log->error( 'The old view data is no longer mutable. This is a \GV\View object should not be accessed as an array.' );
	}

	/**
	 * Be compatible with the old data object.
	 *
	 * Some external code expects an array (doing things like foreach on this, or array_keys)
	 *  so let's return an array in the old format for such cases. Do not use unless using
	 *  for back-compatibility.
	 *
	 * @internal
	 * @deprecated 3.0.0
	 * @since 2.0
	 * @return array
	 */
	public function as_data() {
		return array(
			'id'          => $this->ID,
			'view_id'     => $this->ID,
			'form_id'     => $this->form ? $this->form->ID : null,
			'form'        => $this->form ? gravityview_get_form( $this->form->ID ) : null,
			'atts'        => $this->settings->as_atts(),
			'fields'      => $this->fields->by_visible( $this )->as_configuration(),
			'template_id' => $this->settings->get( 'template' ),
			'widgets'     => $this->widgets->as_configuration(),
		);
	}

	/**
	 * Retrieve the entries for the current view and request.
	 *
	 * @param \GV\Request $request The request.
	 *
	 * @return \GV\Entry_Collection The entries.
	 */
	public function get_entries( $request = null ) {
		$entries = new \GV\Entry_Collection();

		if ( ! $this->form ) {
			// Documented below.
			return apply_filters( 'gravityview/view/entries', $entries, $this, $request );
		}

		// Withhold entries when Advanced Filter is configured but the plugin is deactivated.
		if ( \GravityView_Plugin_Hooks_GravityView_Advanced_Filtering::has_inactive_configuration( $this->ID ?? 0 ) ) {
			return $entries;
		}

		$parameters = $this->settings->as_atts();

		/**
		 * Remove multiple sorting before calling legacy filters.
		 * This allows us to fake it till we make it.
		 */
		if ( ! empty( $parameters['sort_field'] ) && is_array( $parameters['sort_field'] ) ) {
			$has_multisort            = true;
			$parameters['sort_field'] = reset( $parameters['sort_field'] );
			if ( ! empty( $parameters['sort_direction'] ) && is_array( $parameters['sort_direction'] ) ) {
				$parameters['sort_direction'] = reset( $parameters['sort_direction'] );
			}
		}

		/**
		 * @todo: Stop using _frontend and use something like $request->get_search_criteria() instead
		 */
		$parameters = GravityView_frontend::get_view_entries_parameters( $parameters, $this->form->ID );

		$parameters['context_view_id'] = $this->ID;
		$parameters                    = GVCommon::calculate_get_entries_criteria( $parameters, $this->form->ID );

		if ( ! is_array( $parameters ) ) {
			$parameters = array();
		}

		if ( ! is_array( $parameters['search_criteria'] ) ) {
			$parameters['search_criteria'] = array();
		}

		// When overwriting is allowed, remove View date constraints for dates the user is actively searching on.
		if ( $this->settings->get( 'allow_date_range_overwrite' ) ) {
			$active_request = $request ?? new FrontendRequest();
			$search_request = SearchRequest::from_request( $active_request, $this );
			$entry_date     = $search_request ? $search_request->get_filter( 'entry_date' ) : null;

			// Only the View the search was performed on drops its configured
			// window; a date search on another View on the page leaves this
			// View's window intact.
			$search_is_ours = $active_request->is_search( $this );

			if ( $entry_date && $search_is_ours ) {
				$is_day = 'day' === ( $entry_date['type'] ?? null );

				if ( ! empty( $entry_date['start_date'] ) || $is_day ) {
					unset( $parameters['search_criteria']['start_date'] );
				}

				if ( ! empty( $entry_date['end_date'] ) || $is_day ) {
					unset( $parameters['search_criteria']['end_date'] );
				}
			}
		}

		if ( ( ! isset( $parameters['search_criteria']['field_filters'] ) ) || ( ! is_array( $parameters['search_criteria']['field_filters'] ) ) ) {
			$parameters['search_criteria']['field_filters'] = array();
		}

		if ( $request instanceof RestRequest ) {
			$atts                 = $this->settings->as_atts();
			$paging_parameters    = wp_parse_args(
				$request->get_paging(),
				array(
					'paging' => array( 'page_size' => $atts['page_size'] ),
				)
			);
			$parameters['paging'] = $paging_parameters['paging'];
		}

		$page = \GV\Utils::get( $parameters['paging'], 'current_page' );

		if ( ! $page ) {
			$page      = 1;
			$page_size = \GV\Utils::get( $parameters, 'paging/page_size', 25 );
			if ( $page_size > 0 ) {
				$offset_diff = $parameters['paging']['offset'] - $this->settings->get( 'offset' );
				$page        = ( $offset_diff / $page_size ) + 1;
			}
		}

		/**
		 * Cleanup duplicate field_filter parameters to simplify the query.
		 */
		$unique_field_filters = array();
		foreach ( \GV\Utils::get( $parameters, 'search_criteria/field_filters', array() ) as $key => $filter ) {
			if ( 'mode' === $key ) {
				$unique_field_filters['mode'] = $filter;
			} elseif ( ! in_array( $filter, $unique_field_filters ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- Filters are arrays; loose comparison intentionally dedupes equivalent filters regardless of key order or scalar types.
				$unique_field_filters[] = $filter;
			}
		}
		$parameters['search_criteria']['field_filters'] = $unique_field_filters;

		if ( ! empty( $parameters['search_criteria']['field_filters'] ) ) {
			\gravityview()->log->notice( 'search_criteria/field_filters is not empty, third-party code may be using legacy search_criteria filters.' );
		}

		if ( gravityview()->plugin->supports( \GV\Plugin::FEATURE_GFQUERY ) ) {

			$query_class = $this->get_query_class();

			/** @type \GF_Query $query */
			$query = new $query_class( $this->form->ID, $parameters['search_criteria'], \GV\Utils::get( $parameters, 'sorting' ) );

			// Determine if we need to apply multisort: if the query is random, we don't need to.
			$has_random = false;
			foreach ( $query->_introspect()['order'] as $order ) {
				if ( isset( $order[0] ) && $order[0] instanceof \GF_Query_Call ) {
					if ( 'RAND' === $order[0]->function_name ) {
						$has_random = true;
						break;
					}
				}
			}

			/**
			 * Apply multisort.
			 */
			if ( ! empty( $has_multisort ) && ! $has_random ) {
				// Clear ordering that was set when initializing the query since we're going to set it from scratch.
				( function () {
					$this->order = [];
				} )->bindTo( $query, $query )();

				$atts = $this->settings->as_atts();

				$view_setting_sort_field_ids = \GV\Utils::get( $atts, 'sort_field', array() );

				$view_setting_sort_directions = \GV\Utils::get( $atts, 'sort_direction', array() );

				$has_sort_query_param = ! empty( $_GET['sort'] ) && is_array( $_GET['sort'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend sort parameter; no state changes or data persistence.

				if ( $has_sort_query_param ) {
					$has_sort_query_param = array_filter( array_values( $_GET['sort'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend sort parameter; no state changes or data persistence.
				}

				if ( $this->settings->get( 'sort_columns' ) && $has_sort_query_param ) {
					$sort_field_ids  = array_keys( $_GET['sort'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend sort parameter; no state changes or data persistence.
					$sort_directions = array_values( $_GET['sort'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend sort parameter; no state changes or data persistence.

					// A crafted `?sort[2][]=asc` nests an array here, which would fatal in
					// strtoupper() below; anything but a scalar known direction becomes the default.
					$sort_directions = array_map(
						static function ( $direction ) {
							return is_scalar( $direction ) && in_array( strtoupper( (string) $direction ), [ 'ASC', 'DESC', 'RAND' ], true ) ? $direction : 'ASC';
						},
						$sort_directions
					);
				} else {
					$sort_field_ids  = (array) $view_setting_sort_field_ids;
					$sort_directions = (array) $view_setting_sort_directions;
				}

				$sorting_parameters = [];

				foreach ( $sort_field_ids as $key => $id ) {
					// The original field ID can be overridden for certain fields, including the Name field
					// where a single ID becomes multiple field input IDs joined by a pipe (e.g., "3.3|3.6").
					$id_overrides = explode(
						'|',
						GravityView_frontend::_override_sorting_id_by_field_type( $id, $this->form->ID )
					);

					foreach ( $id_overrides as $id_override ) {
						$sorting_parameters[] = [
							'_original_id' => $id,
							'id'           => $id_override,
							'is_numeric'   => GVCommon::is_field_numeric( $this->form->ID, $id ),
							'direction'    => strtoupper( \GV\Utils::get( $sort_directions, $key, 'ASC' ) ),
						];
					}
				}

				/**
				 * Modifies the sorting parameters applied during the retrieval of View entries.
				 *
				 * @filter `gk/gravityview/view/entries/query/sorting-parameters`
				 *
				 * @since  2.28.0
				 *
				 * @param array $sorting_parameters The array of sorting parameters, including field ID, field type (direction, and casting types.
				 * @param View  $this               The View instance.
				 */
				$sorting_parameters = apply_filters( 'gk/gravityview/view/entries/query/sorting-parameters', $sorting_parameters, $this );

				foreach ( $sorting_parameters as $field ) {
					if ( empty( $field['id'] ) ) {
						continue;
					}

					if ( 'RAND' === strtoupper( $field['direction'] ) ) {
						$query->order( \GF_Query_Call::RAND() );

						continue;
					}

					$order = new GF_Query_Column( $field['id'], $this->form->ID );

					if ( 'id' !== $field['id'] && (int) $field['is_numeric'] ) {
						$order = \GF_Query_Call::CAST( $order, defined( 'GF_Query::TYPE_DECIMAL' ) ? \GF_Query::TYPE_DECIMAL : \GF_Query::TYPE_SIGNED );
					}

					$query->order( $order, $field['direction'] );
				}
			}

			/**
			 * Merge time subfield sorts.
			 */
			add_filter(
				'gform_gf_query_sql',
				$gf_query_timesort_sql_callback = function ( $sql ) use ( &$query ) {
					$q      = $query->_introspect();
					$orders = array();

					$merged_time = false;

					foreach ( $q['order'] as $oid => $order ) {

						$column = null;

						if ( $order[0] instanceof GF_Query_Column ) {
							$column = $order[0];
						} elseif ( $order[0] instanceof \GF_Query_Call ) {
							if ( 1 !== count( $order[0]->columns ) || ! $order[0]->columns[0] instanceof GF_Query_Column ) {
								$orders[ $oid ] = $order;
								continue; // Need something that resembles a single sort.
							}
							$column = $order[0]->columns[0];
						}

						$field = $column ? \GFAPI::get_field( $column->source, $column->field_id ) : null;

						if ( ! $column || ! $field || 'time' !== $field->type ) {
							$orders[ $oid ] = $order;
							continue; // Not a time field.
						}

						$orders[ $oid ] = array(
							new \GravityKit\GravityView\GravityForms\QueryExtensions\GFQueryCallTimesort( 'timesort', array( $column, $sql ) ),
							$order[1], // Mock it!
						);

						$merged_time = true;
					}

					if ( $merged_time ) {
						/**
						 * ORDER again.
						 */
						if ( ! empty( $orders ) ) {
							$_orders = $query->_order_generate( $orders );

							if ( $_orders ) {
								$sql['order'] = 'ORDER BY ' . implode( ', ', $_orders );
							}
						}
					}

					return $sql;
				}
			);

			$query->limit( $parameters['paging']['page_size'] )
				->offset( ( ( $page - 1 ) * $parameters['paging']['page_size'] ) + $this->settings->get( 'offset' ) );

			/**
			 * Any joins?
			 */
			if ( gravityview()->plugin->supports( \GV\Plugin::FEATURE_JOINS ) && count( $this->joins ) ) {
				foreach ( $this->joins as $join ) {
					$query = $join->as_query_join( $query );

					if ( $this->settings->get( 'multiple_forms_disable_null_joins' ) ) {

						// Disable NULL outputs.
						$condition = new GF_Query_Condition(
							new GF_Query_Column( $join->join_on_column->ID, $join->join_on->ID ),
							GF_Query_Condition::NEQ,
							new GF_Query_Literal( '' )
						);

						$query_parameters = $query->_introspect();

						$query->where( GF_Query_Condition::_and( $query_parameters['where'], $condition ) );
					}

					// Filter to active entries only.
					$status_conditions = GF_Query_Condition::_or(
						new GF_Query_Condition(
							new GF_Query_Column( 'status', $join->join_on->ID ),
							GF_Query_Condition::EQ,
							new GF_Query_Literal( 'active' )
						),
						new GF_Query_Condition(
							new GF_Query_Column( 'status', $join->join_on->ID ),
							GF_Query_Condition::IS,
							GF_Query_Condition::NULL
						)
					);

					/**
					 * Modifies the join conditions applied during the retrieval of View entries.
					 *
					 * @filter `gk/gravityview/view/entries/join-conditions`
					 *
					 * @since 2.32.0
					 *
					 * @param GF_Query_Condition $status_conditions The GF_Query_Condition instance.
					 * @param Join $join The Join instance.
					 * @param View $this The View instance.
					 */
					$status_conditions = apply_filters( 'gk/gravityview/view/entries/join-conditions', $status_conditions, $join, $this );

					$q = $query->_introspect();
					$query->where( GF_Query_Condition::_and( $q['where'], $status_conditions ) );

					/**
					 * Applies legacy modifications to Query for is_approved settings.
					 */
					$this->apply_legacy_join_is_approved_query_conditions( $query, $join );
				}

				/**
				 * Unions?
				 */
			} elseif ( gravityview()->plugin->supports( \GV\Plugin::FEATURE_UNIONS ) && count( $this->unions ) ) {
				$union_query = new Union\QueryTransformer( $this );
				$union_query->apply( $query, $request );
			}

			/**
			 * Override the \GF_Query before the get() call.
			 *
			 * @param \GF_Query $query The current query object reference
			 * @param \GV\View $this The current view object
			 * @param \GV\Request $request The request object
			 */
			do_action_ref_array( 'gravityview/view/query', array( &$query, $this, $request ) );

			\gravityview()->log->debug( 'GF_Query parameters: ', array( 'data' => \GV\Utils::gf_query_debug( $query ) ) );

			$result = $this->run_db_query( $query );

			[ $db_entries, $query ] = $result;

			/**
			 * Map from Gravity Forms entries arrays to an Entry_Collection.
			 */
			if ( count( $this->joins ) ) {
				foreach ( $db_entries as $entry ) {
					$entries->add(
						\GV\Multi_Entry::from_entries( array_map( '\GV\GF_Entry::from_entry', $entry ) )
					);
				}
			} else {
				array_map( array( $entries, 'add' ), array_map( '\GV\GF_Entry::from_entry', $db_entries ) );
			}

			if ( isset( $union_query ) ) {
				$union_query->detach();
			}

			if ( isset( $gf_query_timesort_sql_callback ) ) {
				remove_action( 'gform_gf_query_sql', $gf_query_timesort_sql_callback );
			}

			/**
			 * Add total count callback.
			 */
			$entries->add_count_callback(
				function () use ( $query ) {
					return $query->total_found;
				}
			);
		} else {
			$entries = $this->form->entries
				->filter( \GV\GF_Entry_Filter::from_search_criteria( $parameters['search_criteria'] ) )
				->offset( $this->settings->get( 'offset' ) )
				->limit( $parameters['paging']['page_size'] )
				->page( $page );

			if ( ! empty( $parameters['sorting'] ) && is_array( $parameters['sorting'] ) && ! isset( $parameters['sorting']['key'] ) ) {
				// Pluck off multisort arrays.
				$parameters['sorting'] = $parameters['sorting'][0];
			}

			if ( ! empty( $parameters['sorting'] ) && ! empty( $parameters['sorting']['key'] ) ) {
				$field     = new \GV\Field();
				$field->ID = $parameters['sorting']['key'];
				$direction = 'asc' === strtolower( $parameters['sorting']['direction'] ) ? \GV\Entry_Sort::ASC : \GV\Entry_Sort::DESC;
				$entries   = $entries->sort( new \GV\Entry_Sort( $field, $direction ) );
			}
		}

		/**
		 * Modify the entry fetching filters, sorts, offsets, limits.
		 *
		 * @param \GV\Entry_Collection $entries The entries for this view.
		 * @param \GV\View $view The view.
		 * @param \GV\Request $request The request.
		 */
		return apply_filters( 'gravityview/view/entries', $entries, $this, $request );
	}

	/**
	 * Queries database and conditionally caches results.
	 * First, checks if the long-lived cache is enabled and if the query is cached. If not, it checks if the short-lived cache is enabled and if the query is cached.
	 *
	 * @since 2.18.2
	 *
	 * @param \GF_Query $query The query to run.
	 *
	 * @return array{0: array, 1: GF_Query} Array of entries and the query object. The latter may be needed as it is modified during the query.
	 */
	private function run_db_query( \GF_Query $query ) {
		$db_entries = null;

		$query_introspect = $query->_introspect();

		$random_order = false;

		// Order keys are randomly generated, so we need to make them deterministic or else the query hash will change every time.
		if ( isset( $query_introspect['order'] ) ) {
			$order_hashes = [];

			foreach ( $query_introspect['order'] as $order ) {
				$serialized_order = serialize( $order ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Used only to build a deterministic cache hash; the data is never unserialized.

				if ( strpos( $serialized_order, '"RAND"' ) !== false ) {
					$random_order = true;
				}

				$order_hashes[] = md5( serialize( $serialized_order ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Used only to build a deterministic cache hash; the data is never unserialized.
			}

			$query_introspect['order'] = $order_hashes;
		}

		$query_hash = md5( serialize( $query_introspect ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Used only to build a deterministic cache hash; the data is never unserialized.

		$caching_atts = [
			'view_id'         => $this->ID ? $this->ID : null,
			'caching'         => $this->settings->get( 'caching' ),
			'caching_entries' => $this->settings->get( 'caching_entries' ),
			'query_hash'      => $query_hash,
		];

		$caching_atts = array_merge( $caching_atts, $this->settings->get( 'caching_atts', [] ) );

		if ( $this->unions ) {
			$caching_atts['unions'] = $this->get_union_signature();
		}

		$long_lived_cache = new \GravityView_Cache( $this->get_form_ids(), $caching_atts );

		if ( ! $random_order && $long_lived_cache->use_cache() ) {
			$cached_entries = $long_lived_cache->get();

			if ( is_array( $cached_entries ) && array_key_exists( 'entries', $cached_entries ) && array_key_exists( 'total', $cached_entries ) ) {
				$query->total_found = $cached_entries['total'];

				return [
					$cached_entries['entries'],
					$query,
				];
			}

			$cached_entries = [
				'entries' => $query->get(),
				'total'   => $query->total_found,
			];

			if ( $long_lived_cache->set( $cached_entries, 'entries' ) ) {
				return [
					$cached_entries['entries'],
					$query,
				];
			}
		}

		/**
		 * Controls whether the query is cached per request. This is a short-lived cache.
		 *
		 * @filter gk/gravityview/view/entries/cache
		 *
		 * @since  2.18.2
		 *
		 * @param bool $enable_caching Default: true.
		 */
		if ( ! apply_filters( 'gk/gravityview/view/entries/cache', true ) ) {
			$db_entries = $query->get();

			return [
				$db_entries,
				$query,
			];
		}

		if ( ! Arr::get( self::$cache, $query_hash ) ) {
			$db_entries = $db_entries ?? $query->get();

			self::$cache[ $query_hash ] = array(
				$db_entries,
				$query,
			);
		}

		return self::$cache[ $query_hash ];
	}

	/**
	 * Last chance to configure the output.
	 *
	 * Used for CSV output, for example.
	 *
	 * @return void
	 */
	public static function template_redirect() {
		$is_csv = get_query_var( 'csv' );
		$is_tsv = get_query_var( 'tsv' );

		/**
		 * CSV output.
		 */
		if ( ! $is_csv && ! $is_tsv ) {
			return;
		}

		$view = gravityview()->request->is_view();

		if ( ! $view ) {
			return;
		}

		$error_csv = $view->can_render( array( 'csv' ) );

		if ( is_wp_error( $error_csv ) ) {
			\gravityview()->log->error( 'Not rendering CSV or TSV: ' . $error_csv->get_error_message() );
			return;
		}

		$file_type = $is_csv ? 'csv' : 'tsv';

		/**
		 * Modify the name of the generated CSV or TSV file. Name will be sanitized using sanitize_file_name() before output.
		 *
		 * @see sanitize_file_name()
		 * @since 2.1
		 * @param string   $filename File name used when downloading a CSV or TSV. Default is "{View title}.csv" or "{View title}.tsv"
		 * @param \GV\View $view Current View being rendered
		 */
		$filename = apply_filters( 'gravityview/output/' . $file_type . '/filename', get_the_title( $view->post ), $view );

		if ( ! defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			header( sprintf( 'Content-Disposition: attachment;filename="%s.' . $file_type . '"', sanitize_file_name( $filename ) ) );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Content-Type: text/' . $file_type );
		}

		ob_start();
		$csv_or_tsv = fopen( 'php://output', 'w' );

		/**
		 * Add da' BOM if GF uses it
		 *
		 * @see GFExport::start_export()
		 */
		if ( apply_filters( 'gform_include_bom_export_entries', true, $view->form ? $view->form->form : null ) ) {
			fputs( $csv_or_tsv, "\xef\xbb\xbf" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fputs -- Writing the BOM to the php://output stream, not the filesystem; WP_Filesystem does not apply.
		}

		if ( $view->settings->get( 'csv_nolimit' ) ) {
			$view->settings->update( array( 'page_size' => -1 ) );
		}

		$entries = $view->get_entries();

		$headers_done = false;
		$allowed      = array();
		$headers      = array();

		foreach ( $view->fields->by_position( 'directory_*' )->by_visible( $view )->all() as $id => $field ) {
			$allowed[] = $field;
		}

		$renderer = new \GV\Field_Renderer();

		foreach ( $entries->all() as $entry ) {

			$return = array();

			/**
			 * Allowlist more entry fields by ID that are output in CSV requests.
			 *
			 * @param array $allowed The allowed ones, default by_visible, by_position( "context_*" ), i.e. as set in the View.
			 * @param \GV\View $view The view.
			 * @param \GV\Entry $entry WordPress representation of the item.
			 */
			$allowed_field_ids = apply_filters( 'gravityview/csv/entry/fields', wp_list_pluck( $allowed, 'ID' ), $view, $entry );

			$allowed = array_filter(
				$allowed,
				function ( $field ) use ( $allowed_field_ids ) {
					return in_array( $field->ID, $allowed_field_ids, true );
				}
			);

			foreach ( array_diff( $allowed_field_ids, wp_list_pluck( $allowed, 'ID' ) ) as $field_id ) {
				$allowed[] = is_numeric( $field_id ) ? \GV\GF_Field::by_id( $view->form, $field_id ) : \GV\Internal_Field::by_id( $field_id );
			}

			foreach ( $allowed as $field ) {
				// Remove all links from output.
				$field->update_configuration( [ 'show_as_link' => '0' ] );

				$source = self::get_source( $field, $view );

				$return[] = $renderer->render( $field, $view, $source, $entry, gravityview()->request, '\GV\Field_CSV_Template' );

				if ( ! $headers_done ) {
					$label     = $field->get_label( $view, $source, $entry );
					$headers[] = $label ? $label : $field->ID;
				}
			}

			// If not "tsv" then use comma.
			$delimiter = ( 'tsv' === $file_type ) ? "\t" : ',';

			if ( ! $headers_done ) {
				$headers_done = fputcsv( $csv_or_tsv, array_map( array( '\GV\Utils', 'strip_excel_formulas' ), array_values( $headers ) ), $delimiter );
			}

			fputcsv( $csv_or_tsv, array_map( array( '\GV\Utils', 'strip_excel_formulas' ), $return ), $delimiter );
		}

		fflush( $csv_or_tsv );

		echo rtrim( ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Buffered CSV/TSV file content generated by fputcsv() and served with a text/csv Content-Type; HTML escaping would corrupt the download.

		if ( ! defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			exit;
		}
	}

	/**
	 * Return the query class for this View.
	 *
	 * @return string The class name.
	 */
	public function get_query_class() {
		/**
		 * @filter `gravityview/query/class`
		 * @param string The query class. Default: GF_Query.
		 * @param \GV\View $this The View.
		 */
		$query_class = apply_filters( 'gravityview/query/class', '\GF_Query', $this );
		return $query_class;
	}

	/**
	 * Restrict View access to specific capabilities.
	 *
	 * Hooked into `map_meta_cap` WordPress filter.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $caps    The output capabilities.
	 * @param string $cap     The cap that is being checked.
	 * @param int    $user_id The User ID.
	 * @param array  $args    Additional arguments to the capability.
	 *
	 * @return array   The resulting capabilities.
	 */
	public static function restrict( $caps, $cap, $user_id, $args ) {
		/**
		 * Bypass restrictions on Views that require `unfiltered_html`.
		 *
		 * @since 2.16
		 *
		 * @param bool $require_unfiltered_html Whether to require unfiltered_html capability. Default: true.
		 * @param string $cap The capability requested.
		 * @param int $user_id The user ID.
		 */
		if ( ! apply_filters( 'gravityview/security/require_unfiltered_html', true, $cap, $user_id ) ) {
			return $caps;
		}

		switch ( $cap ) :
			case 'edit_gravityview':
			case 'edit_gravityviews':
			case 'edit_others_gravityviews':
			case 'edit_private_gravityviews':
			case 'edit_published_gravityviews':
				if ( ! user_can( $user_id, 'unfiltered_html' ) ) {
					if ( ! user_can( $user_id, 'gravityview_full_access' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability registered by GravityView_Roles_Capabilities.
						return array( 'do_not_allow' );
					}
				}

				return $caps;
			case 'edit_post':
				if ( self::POST_TYPE === get_post_type( array_pop( $args ) ) ) {
					return self::restrict( $caps, 'edit_gravityview', $user_id, $args );
				}
		endswitch;

		return $caps;
	}

	/**
	 * Sets the anchor ID of a View, without the prefix.
	 *
	 * @since 2.15
	 *
	 * @param int $counter An incremental counter reflecting how many times this View has been rendered.
	 *
	 * @return void
	 */
	public function set_anchor_id( $counter = 1 ) {
		$this->anchor_id = sprintf( 'gv-view-%d-%d', $this->ID, (int) $counter );
	}

	/**
	 * Returns the anchor ID to be used in the View container HTML `id` attribute.
	 *
	 * @since 2.15
	 *
	 * @return string Unsanitized anchor ID.
	 */
	public function get_anchor_id() {
		/**
		 * Modify the anchor ID.
		 *
		 * @since 2.15
		 * @param string $anchor_id The anchor ID.
		 * @param \GV\View $this The View.
		 */
		return apply_filters( 'gravityview/view/anchor_id', $this->anchor_id, $this );
	}

	/**
	 * Magic getter forwarded to the backing \WP_Post.
	 *
	 * @param string $key The property name.
	 *
	 * @return mixed The property value, or null if not set.
	 */
	public function __get( $key ) {
		if ( $this->post ) {
			$raw_post = $this->post->filter( 'raw' );
			return $raw_post->{$key};
		}
		return isset( $this->{$key} ) ? $this->{$key} : null;
	}

	/**
	 * Return associated WP post
	 *
	 * @since 2.13.2
	 *
	 * @return \WP_Post|null
	 */
	public function get_post() {
		return $this->post ? $this->post : null;
	}

	/**
	 * On version 0.3.0 of Multiple Forms is_approved for joins is handled elsewhere, for backwards compatibility purposes
	 * the goal here is to only apply this while Multiple Forms is still compatible with older versions of GravityView.
	 *
	 * @since 2.17.2
	 *
	 * @param \GF_Query $query The query to modify.
	 * @param \GV\Join  $join  The join to apply the conditions for.
	 */
	protected function apply_legacy_join_is_approved_query_conditions( \GF_Query $query, \GV\Join $join ): void {
		/**
		 * Allows Multiple Forms and other plugins to deactivate this piece of functionality when loaded.
		 *
		 * @since 2.17.2
		 *
		 * @param bool      $should_apply Determines if legacy join condition should be applied.
		 * @param \GF_Query $query        Which is being dealt with.
		 * @param \GV\Join  $join         Which join we are dealing with.
		 * @param self      $view         Instance of the view we are dealing with.
		 */
		$should_apply = (bool) apply_filters( 'gravityview/view/get_entries/should_apply_legacy_join_is_approved_query_conditions', true, $query, $join, $this );
		if ( ! $should_apply ) {
			return;
		}

		if ( ! $this->settings->get( 'show_only_approved' ) ) {
			return;
		}

		$is_admin_and_can_view = $this->settings->get( 'admin_show_all_statuses' ) && GVCommon::has_cap( 'gravityview_moderate_entries', $this->ID );

		if ( $is_admin_and_can_view ) {
			return;
		}

		// Show only approved joined entries.
		$condition = new GF_Query_Condition(
			new GF_Query_Column( \GravityView_Entry_Approval::meta_key, $join->join_on->ID ),
			GF_Query_Condition::EQ,
			new GF_Query_Literal( \GravityView_Entry_Approval_Status::APPROVED )
		);

		$condition = GF_Query_Condition::_or(
			$condition,
			new GF_Query_Condition(
				new GF_Query_Column( \GravityView_Entry_Approval::meta_key, $join->join_on->ID ),
				GF_Query_Condition::IS,
				GF_Query_Condition::NULL
			)
		);

		$query_parameters = $query->_introspect();

		$query->where( GF_Query_Condition::_and( $query_parameters['where'], $condition ) );
	}

	/**
	 * Calculates and returns the View's validation secret.
	 *
	 * @since 2.21
	 *
	 * @param bool $is_forced Whether to compute the secret for non-secure Views too.
	 *
	 * @return string|null The View's secret.
	 */
	final public function get_validation_secret( bool $is_forced = false ): ?string {
		return self::get_validation_secret_by_id( (int) $this->ID, $is_forced );
	}

	/**
	 * Calculates and returns a View's validation secret without hydrating the View.
	 *
	 * Bulk callers (e.g. the block editor View picker) use this so listing
	 * thousands of Views does not pay full View hydration per row.
	 *
	 * @since 3.0.0
	 * @since 3.1.0 The secret is derived from a per-site random key stored in the
	 *              `gravityview_view_secret_key` option. Embeds of Views that
	 *              predate the per-site key keep working because validate_secret()
	 *              still accepts the previous secret for them.
	 *
	 * @param int  $view_id   The View ID.
	 * @param bool $is_forced Whether to compute the secret for non-secure Views too.
	 *
	 * @return string|null The View's secret.
	 */
	public static function get_validation_secret_by_id( int $view_id, bool $is_forced = false ): ?string {
		// Reject non-positive IDs before get_post(): get_post( 0 ) falls back to
		// the global $post, which could mint a secret for an unrelated View.
		if ( $view_id <= 0 ) {
			return null;
		}

		// The instance method can only run on a hydrated View; enforce the
		// same invariant here instead of minting secrets for arbitrary IDs.
		// No post_status check on purpose: drafts mint secrets through the
		// instance method too (the editor shortcode hint relies on that).
		$post = get_post( $view_id );

		if ( ! $post instanceof \WP_Post || self::POST_TYPE !== $post->post_type ) {
			return null;
		}

		// Cannot use the setting variable because it can be overwritten from the short code.
		$settings  = get_post_meta( $view_id, '_gravityview_template_settings', true );
		$is_secure = (bool) rgar( $settings, 'is_secure', false );

		// No Foundation dependency in the derivation below on purpose: a secure
		// View must still require a valid secret when Foundation is absent.
		// Returning null here would make validate_secret() accept any secret.
		if ( ! $is_secure && ! $is_forced ) {
			return null;
		}

		$key = self::get_secret_key();

		if ( null === $key ) {
			// The key could not be persisted (e.g. a read-only database). Return
			// a random value so the gate denies access rather than a predictable
			// one.
			gravityview()->log->error( 'Could not persist the View validation secret key; Enhanced Security Views will not render until the database is writable.' );

			return bin2hex( random_bytes( 6 ) );
		}

		return substr( hash_hmac( 'sha256', (string) $view_id, $key ), 0, 12 );
	}

	/**
	 * Returns the per-site secret key, minting it once if absent or corrupt.
	 *
	 * A 256-bit random value stored in an autoloaded option and reused thereafter.
	 *
	 * @since 3.1.0
	 *
	 * @return string|null The key, or null if it cannot be persisted.
	 */
	private static function get_secret_key(): ?string {
		// The null default distinguishes an absent row from a corrupt one.
		$key          = get_option( self::VALIDATION_SECRET_KEY_OPTION, null );
		$is_valid_key = self::is_valid_secret_key( $key );

		if ( $is_valid_key ) {
			return $key;
		}

		// Not wp_generate_password(): the random_password filter is
		// third-party-overridable and its output is not a crypto primitive.
		$new_key = bin2hex( random_bytes( 32 ) );

		if ( null === $key ) {
			// Insert-only on purpose: add_option() clobbers a row a
			// concurrent request inserted after our read, handing the two
			// requests different keys. A losing mint converges on the
			// winner's key via the uncached re-read below.
			WPHelper::insert_option_if_absent( self::VALIDATION_SECRET_KEY_OPTION, $new_key, true );
		} else {
			// The row exists but is corrupt (a truncated restore, an options
			// importer); no valid secret derives from it, so overwrite.
			update_option( self::VALIDATION_SECRET_KEY_OPTION, $new_key, true );
		}

		wp_cache_delete( self::VALIDATION_SECRET_KEY_OPTION, 'options' );

		$key          = get_option( self::VALIDATION_SECRET_KEY_OPTION );
		$is_valid_key = self::is_valid_secret_key( $key );

		return $is_valid_key ? $key : null;
	}

	/**
	 * Whether a value is a usable per-site secret key (64+ hex characters).
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $key The stored option value.
	 *
	 * @return bool
	 */
	private static function is_valid_secret_key(
		#[\SensitiveParameter]
		$key
	): bool {
		return is_string( $key ) && (bool) preg_match( '/^[0-9a-f]{64,}\z/', $key );
	}

	/**
	 * Computes the previous (pre per-site-key) validation secret for a View.
	 *
	 * Retained only for the opt-in grace period in validate_secret(). This value
	 * is not site-specific, so it must not be surfaced anywhere a secret is
	 * emitted (the shortcode, the editor hint, the block picker).
	 *
	 * @since 3.1.0
	 *
	 * @param int $view_id The View ID.
	 *
	 * @return string|null The previous secret, or null if it cannot be computed.
	 */
	private static function get_legacy_validation_secret_by_id( int $view_id ): ?string {
		if ( ! class_exists( GravityKitFoundation::class ) ) {
			return null;
		}

		$hash = GravityKitFoundation::get_instance()->encryption()->hash( $view_id );

		return substr( $hash, 0, 12 );
	}

	/**
	 * Establishes the per-site secret-key boundary on this site's first run.
	 *
	 * Hooked to `gravityview/loaded` (an early, every-request action), so the
	 * stored GMT timestamp is the moment this site first ran the per-site-key
	 * code: activation time on a fresh install, first request after the update
	 * on an upgrade. Writes once and returns the same boundary thereafter.
	 *
	 * @since 3.1.0
	 *
	 * @return int The boundary as a GMT Unix timestamp, or 0 if it cannot be persisted.
	 */
	public static function maybe_establish_secret_key_boundary(): int {
		$established = (int) get_option( self::VALIDATION_SECRET_KEY_ESTABLISHED_OPTION );

		if ( $established ) {
			return $established;
		}

		add_option( self::VALIDATION_SECRET_KEY_ESTABLISHED_OPTION, (string) time(), '', true );

		// add_option() returns a bool, and a concurrent request may have won the
		// insert with a different timestamp, so read back what actually persisted.
		return (int) get_option( self::VALIDATION_SECRET_KEY_ESTABLISHED_OPTION );
	}

	/**
	 * Whether this View may validate against the previous secret derivation.
	 *
	 * True only for Views that existed before this site established its per-site
	 * key: those are the ones whose embeds may still carry the previous secret.
	 * A View created after the boundary uses only the per-site secret. This is
	 * the default the `gk/gravityview/view/secret/allow-legacy` filter can
	 * still override.
	 *
	 * @since 3.1.0
	 *
	 * @return bool
	 */
	private function is_legacy_secret_view(): bool {
		// Stamps the boundary on first use (e.g. a switched blog on multisite whose
		// own bootstrap has not run) and returns it; a no-op once established.
		$established = self::maybe_establish_secret_key_boundary();

		// Boundary unknown (unwritable database): stay permissive; the missing
		// per-site key makes validate_secret() fail closed regardless.
		if ( ! $established ) {
			return true;
		}

		$created_gmt = get_post_timestamp( $this->post, 'date' );

		// Unknown creation time (auto-draft, unpublished, malformed import): stay permissive.
		if ( ! $created_gmt ) {
			return true;
		}

		return $created_gmt < $established;
	}

	/**
	 * Returns whether the provided secret validates for this View.
	 *
	 * @since 2.21
	 * @since 3.1.0 The current secret is derived from a per-site key. The previous
	 *              secret is accepted only for Views that predate the per-site key,
	 *              so existing embeds keep working while newly created Views use
	 *              only the per-site secret. Override with the
	 *              `gk/gravityview/view/secret/allow-legacy` filter.
	 *
	 * @param string $secret The provided secret.
	 *
	 * @return bool
	 */
	final public function validate_secret(
		#[\SensitiveParameter]
		string $secret
	): bool {
		$view_secret = $this->get_validation_secret();

		if ( ! $view_secret ) {
			return true;
		}

		// Constant-time comparison to avoid leaking the secret via timing.
		$is_current_match = hash_equals( $view_secret, $secret );

		if ( $is_current_match ) {
			return true;
		}

		// No persisted key means the comparison above ran against a random
		// placeholder (see get_validation_secret_by_id()). Deny instead of
		// consulting the previous-secret path in that state.
		$has_valid_secret_key = self::is_valid_secret_key( get_option( self::VALIDATION_SECRET_KEY_OPTION ) );

		if ( ! $has_valid_secret_key ) {
			return false;
		}

		/**
		 * Whether to accept a View's previous (pre per-site-key) secret.
		 *
		 * It is accepted by default only for Views created before this site
		 * established its per-site key, so their existing embeds keep working;
		 * Views created after that boundary use only the per-site secret. Return
		 * true to accept the previous secret for every View during a wider
		 * transition window, or false to require the per-site secret everywhere.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $allow_legacy Whether to accept the previous secret. Default: true only for pre-key Views.
		 * @param View $view         The View being validated.
		 */
		$allow_legacy = (bool) apply_filters( 'gk/gravityview/view/secret/allow-legacy', $this->is_legacy_secret_view(), $this );

		if ( ! $allow_legacy ) {
			return false;
		}

		$legacy_secret = self::get_legacy_validation_secret_by_id( (int) $this->ID );

		return null !== $legacy_secret && hash_equals( $legacy_secret, $secret );
	}

	/**
	 * Returns the shortcode for this View.
	 *
	 * @since 2.21
	 * @since 2.22 Added `$atts` parameter.
	 *
	 * @param array $atts Additional attributes for the shortcode.
	 *
	 * @return string
	 */
	final public function get_shortcode( array $atts = [] ): string {
		$secret = $this->get_validation_secret();

		// ID & secret can't be overwritten from the View.
		$atts['id'] = $this->post->ID;

		if ( $secret ) {
			$atts['secret'] = $secret;
		}

		$options = [];
		foreach ( $atts as $key => $value ) {
			$options[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return sprintf( '[gravityview %s]', implode( ' ', $options ) );
	}
}
