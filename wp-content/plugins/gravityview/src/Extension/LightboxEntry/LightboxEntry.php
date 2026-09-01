<?php
/**
 * Lightbox Entry handler class.
 *
 * PSR-4 migration of the legacy GravityView_Lightbox_Entry class.
 *
 * @package GravityKit\GravityView\Extension\LightboxEntry
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Extension\LightboxEntry;

use GravityView_API;
use GravityView_Delete_Entry;
use GravityView_Duplicate_Entry;
use GravityView_Edit_Entry;
use GravityView_frontend;
use GravityView_Lightbox_Entry_Request;
use GravityView_View;
use GravityView_View_Data;
use GV\Edit_Entry_Renderer;
use GV\Entry_Renderer;
use GV\GF_Entry;
use GV\Multi_Entry;
use GV\Template_Context;
use GV\View;
use GV\View_Collection;
use GVCommon;
use WP_Post;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

use function gravityview;
use function gravityview_get_link;
use function gv_get_query_args;

class LightboxEntry {
	/**
	 * The REST namespace used for the single entry lightbox view.
	 *
	 * @since 2.29.0
	 */
	const REST_NAMESPACE = 'gravityview';

	/**
	 * The REST version used for the single entry lightbox view.
	 *
	 * @since 2.29.0
	 */
	const REST_VERSION = 1;

	/**
	 * Regex used to match the REST endpoint.
	 *
	 * @since 2.29.0
	 */
	const REST_ENDPOINT_REGEX = 'view/(?P<view_id>[0-9]+)/entry/(?P<entry_ids>[0-9,]+)';

	/**
	 * The block that embeds a View directory.
	 *
	 * @since 3.2.0
	 */
	const VIEW_BLOCK_NAME = 'gk-gravityview-blocks/view';

	/**
	 * Whether hooks have been added.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $hooks_added = false;

	/**
	 * Class constructor.
	 *
	 * @since 2.29.0
	 */
	public function __construct() {
		if ( self::$hooks_added ) {
			return;
		}

		// LightboxEntryRequest is autoloaded via PSR-4; no require_once needed.

		add_filter( 'gravityview/template/before', [ $this, 'maybe_enable_lightbox' ] );
		add_filter( 'gk/foundation/rest/routes', [ $this, 'register_rest_routes' ] );
		// After WordPress's own rest_cookie_check_errors() (priority 100), so this can downgrade its 403.
		add_filter( 'rest_authentication_errors', [ $this, 'allow_stale_nonce_for_lightbox_route' ], 110 );
		add_filter( 'gravityview/template/field/entry_link', [ $this, 'rewrite_entry_link' ], 10, 3 );
		add_filter( 'gk/foundation/inline-scripts', [ $this, 'enqueue_view_editor_script' ] );
		add_filter( 'gravityview/view/links/directory', [ $this, 'rewrite_directory_link' ] );
		add_filter( 'gform_get_form_confirmation_filter', [ $this, 'process_gravity_forms_form_submission' ] );
		add_filter( 'gform_get_form_filter', [ $this, 'process_gravity_forms_form_submission' ] );
		add_filter( 'gk/gravityview/lightbox/entry/output/head-after', [ $this, 'run_during_head_output' ], 10, 2 );

		self::$hooks_added = true;
	}

	/**
	 * Enables lightbox when it's not explicitly enabled in the View settings but a field is configured to use it.
	 *
	 * @used-by `gravityview/template/before` filter.
	 *
	 * @since   2.29.0
	 *
	 * @param Template_Context $context The template context.
	 *
	 * @return void
	 */
	public function maybe_enable_lightbox( $context ) {
		if ( $context->view->settings->get( 'lightbox' ) ) {
			return;
		}

		foreach ( $context->view->fields->all() as $field ) {
			if ( (int) ( $field->as_configuration()['lightbox'] ?? 0 ) ) {
				$context->view->settings->set( 'lightbox', 1 );

				/**
				 *  Set a flag to indicate the lightbox was auto-enabled, so file upload fields can distinguish
				 *  between explicit "Enable lightbox for images" (which should affect file uploads) and auto-enabled
				 *  for entry links (which should not affect file uploads).
				 */
				$context->view->settings->set( 'lightbox_auto_enabled', 1 );

				break;
			}
		}
	}

	/**
	 * Registers the REST route for the single entry lightbox view.
	 *
	 * @used-by `gk/foundation/rest/routes` filter.
	 *
	 * @since   2.29.0
	 *
	 * @param array[] $routes The registered REST routes.
	 *
	 * @return array
	 */
	public function register_rest_routes( $routes ) {
		$routes = $routes ?? [];

		$routes[] = [
			'namespace'           => self::REST_NAMESPACE,
			'version'             => self::REST_VERSION,
			'endpoint'            => self::REST_ENDPOINT_REGEX,
			'methods'             => [ 'GET', 'POST' ],
			'callback'            => [ $this, 'process_rest_request' ],
			'permission_callback' => '__return_true', // WP will handle the nonce and Entry_Renderer::render() will take care of permissions.
		];

		return $routes;
	}

	/**
	 * Lets a lightbox entry request proceed when its REST cookie nonce is stale.
	 *
	 * The lightbox link carries a `wp_rest` nonce so per-user and Advanced-Filtered
	 * Views resolve in the logged-in user's context. Once that nonce expires,
	 * WordPress rejects the request before the route runs; downgrading only this
	 * error for only this route lets the route render its own page instead.
	 *
	 * A stale nonce is an unverifiable identity, so the request is dropped to the
	 * anonymous user (mirroring core's `rest_cookie_check_errors()`) before it
	 * proceeds. Rendering as anonymous keeps a restricted entry hidden and stops an
	 * unverified request from being treated as the logged-in user; a valid nonce
	 * never reaches this filter.
	 *
	 * @used-by `rest_authentication_errors` filter.
	 *
	 * @since   3.1.0
	 *
	 * @param \WP_Error|true|null $errors The current authentication result.
	 *
	 * @return \WP_Error|true|null
	 */
	public function allow_stale_nonce_for_lightbox_route( $errors ) {
		$is_stale_nonce = is_wp_error( $errors ) && 'rest_cookie_invalid_nonce' === $errors->get_error_code();

		if ( ! $is_stale_nonce ) {
			return $errors;
		}

		if ( ! $this->get_rest_endpoint_from_request() ) {
			return $errors;
		}

		wp_set_current_user( 0 );

		return true;
	}

	/**
	 * Processes the REST request by rendering the single or edit entry lightbox view, and handling delete and other actions.
	 *
	 * @since 2.29.0
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_REST_Response
	 */
	public function process_rest_request( $request ) {
		$entry_ids = $request->get_param( 'entry_ids' ) ?? '';
		$entries   = [];

		foreach ( explode( ',', $entry_ids ) as $entry_id ) {
			$_entry = GF_Entry::by_id( $entry_id );

			if ( ! $_entry ) {
				continue;
			}

			$entries[] = $_entry;
		}

		$entry            = ! empty( $entries ) ? reset( $entries ) : null;
		$multiple_entries = count( $entries ) > 1 ? Multi_Entry::from_entries( $entries ) : null;
		$view             = View::by_id( $request->get_param( 'view_id' ) ?? 0 );
		$form             = GVCommon::get_form( $view->form->ID ?? 0 );
		$edit_nonce       = $request->get_param( 'edit' ) ?? null;
		$delete_nonce     = $request->get_param( 'delete' ) ?? null;
		$duplicate_nonce  = $request->get_param( 'duplicate' ) ?? null;

		if ( ! $view || ! $entry || ! $form ) {
			gravityview()->log->error( 'Unable to find View, entry or form.' );

			return $this->access_denied_response();
		}

		$this->apply_directory_post_context( $view, absint( $request->get_param( 'post_id' ) ?? 0 ) );

		// permission_callback is '__return_true', so visibility is gated here. The
		// `[ 'lightbox' ]` context skips embed_only/direct-access so the embedded
		// lightbox works (`null` would 404 it; `[ 'rest' ]` ties it to the REST API
		// setting); it is distinct from `[ 'shortcode' ]` so a filter can target it.
		if ( true !== $view->can_render( [ 'lightbox' ], gravityview()->request ) ) {
			return $this->access_denied_response();
		}

		// Each entry must belong to one of the View's forms (primary or joined).
		// check_access() validates status/approval/slug but not form membership, so a
		// cross-form entry could otherwise be disclosed through a crafted URL.
		$view_form_ids = $view->get_form_ids();

		// Only a pure edit request skips the display gate below; a delete/duplicate
		// token (even alongside `edit`) keeps it.
		$is_edit = $edit_nonce && ! $delete_nonce && ! $duplicate_nonce;

		foreach ( $entries as $gv_entry ) {
			if ( ! in_array( (int) $gv_entry['form_id'], $view_form_ids, true ) ) {
				return $this->access_denied_response();
			}

			// Status / approval / slug: enforced for display AND edit, exactly as the
			// front-end single-entry path: \GV\View::content() runs check_access() in
			// both its edit branch and its view branch. Editing an unapproved entry on
			// a "Show only approved" View is therefore denied here too; the edit
			// capability itself is enforced downstream by Edit_Entry_Renderer.
			$access = $gv_entry->check_access( $view );

			if ( is_wp_error( $access ) ) {
				return $this->access_denied_response();
			}

			// The View's filtered subset (Advanced Filtering), skipped only for an edit
			// request to mirror the front-end edit branch (\GV\View::content()), which
			// runs check_access() but not check_entry_display(); display, delete, and
			// duplicate keep it so a filtered-out entry cannot be probed with a forged
			// action token. Its own embed_only denial is ignored (the `[ 'lightbox' ]`
			// context already allowed it above).
			$display_denied = false;

			if ( ! $is_edit ) {
				$display_check  = GVCommon::check_entry_display( $gv_entry->as_entry(), $view );
				$display_denied = is_wp_error( $display_check ) && 'gravityview/embed_only' !== $display_check->get_error_code();
			}

			if ( $display_denied ) {
				return $this->access_denied_response();
			}
		}

		gravityview()->request = new GravityView_Lightbox_Entry_Request( $view, $entry );

		if ( $delete_nonce ) {
			return $this->process_delete_entry( $view, $entry, $form );
		}

		if ( $duplicate_nonce ) {
			return $this->process_duplicate_entry();
		}

		if ( $edit_nonce ) {
			$this->process_edit_entry( $edit_nonce, $view, $entry, $form );
		}

		return $this->render_entry(
			$edit_nonce ? 'edit' : 'single',
			$view,
			$multiple_entries ?? $entry,
			$form
		);
	}

	/**
	 * Restores the directory page's post context on the lightbox REST request.
	 *
	 * Filter values like {custom_field:...} and {embed_post:...} resolve from the
	 * embedding post and its singular query state, so without this context
	 * check_entry_display() re-runs the View's filters against empty values and
	 * rejects legitimately visible entries.
	 *
	 * The invariant the gates below hold: the lightbox resolves filters only under
	 * a post that the visitor can open AND that renders this View. That keeps the
	 * REST path's visibility within what the directory on that post already lists.
	 * It does not stop someone who can publish a post from choosing the context
	 * they see, because they can already do that by loading their own page.
	 *
	 * @since 3.1.1
	 *
	 * @param View $view    The View being rendered.
	 * @param int  $post_id The embedding post ID carried on the lightbox URL.
	 */
	private function apply_directory_post_context( $view, $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		$context_post = get_post( $post_id );

		if ( ! $context_post || post_password_required( $context_post ) ) {
			return;
		}

		if ( ! is_post_publicly_viewable( $context_post ) && ! current_user_can( 'read_post', $context_post->ID ) ) {
			return;
		}

		$detected = View_Collection::from_post_detailed( $context_post );

		if ( ! $detected['views']->contains( $view->ID ) ) {
			return;
		}

		if ( ! $this->embed_clears_validation_secret( $view, $detected ) ) {
			return;
		}

		$GLOBALS['post'] = $context_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restores the embedding page as the global post so filter merge tags resolve as they did on the page.

		global $wp_query;

		if ( $wp_query instanceof WP_Query ) {
			$wp_query->post              = $context_post;
			$wp_query->posts             = [ $context_post ];
			$wp_query->queried_object    = $context_post;
			$wp_query->queried_object_id = (int) $context_post->ID;
			$wp_query->is_singular       = true;
		}
	}

	/**
	 * Whether an embed written into the post's content clears the View's secret.
	 *
	 * A secured View renders only where the embed carries its validation secret
	 * ({@see \GV\Shortcode::get_view_by_atts()}), so an embed the renderer would
	 * refuse must not qualify as context either.
	 *
	 * Works off the strings the detection pass itself parsed, never a second parse
	 * of the post: an embed that only one of the two sees is an embed neither
	 * validates. A View absent from those strings was declared server side through
	 * the from-post/views filter, which the post's author cannot write to.
	 *
	 * @since 3.2.0
	 *
	 * @param View  $view     The View being rendered.
	 * @param array $detected The result of {@see \GV\View_Collection::from_post_detailed()}.
	 *
	 * @return bool
	 */
	private function embed_clears_validation_secret( $view, array $detected ): bool {
		if ( ! $view->get_validation_secret() ) {
			return true;
		}

		if ( ! $detected['authored']->contains( $view->ID ) ) {
			return true;
		}

		$secrets = [];

		foreach ( $detected['sources'] as $content ) {
			$secrets = array_merge( $secrets, $this->collect_embed_secrets( (int) $view->ID, (string) $content ) );
		}

		// Page builders such as SiteOrigin parse their own stored markup and merge
		// the Views in without naming a meta key, so no source string holds the
		// embed. The attributes ride along on the View instead.
		if ( ! $secrets ) {
			$secrets[] = (string) $detected['authored']->get( $view->ID )->settings->get( 'secret', '' );
		}

		foreach ( $secrets as $secret ) {
			if ( $view->validate_secret( $secret ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the secret carried by each embed of this View found in a string.
	 *
	 * @since 3.2.0
	 *
	 * @param int    $view_id The View being matched.
	 * @param string $content The string to scan.
	 *
	 * @return string[] One entry per embed, empty string when it carries no secret.
	 */
	private function collect_embed_secrets( int $view_id, string $content ): array {
		$secrets = [];

		preg_match_all( '/' . get_shortcode_regex( [ 'gravityview' ] ) . '/', $content, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			$atts = shortcode_parse_atts( $match[3] );

			if ( ! is_array( $atts ) ) {
				continue;
			}

			// The renderer resolves the View from either spelling.
			$embedded_id = (int) ( $atts['id'] ?? $atts['view_id'] ?? 0 );

			if ( $embedded_id !== $view_id ) {
				continue;
			}

			// Doubled brackets mean WordPress prints the shortcode instead of
			// rendering it. It still counts as a reference so the post cannot pass
			// as undeclared, but its secret can never validate.
			$is_escaped = '[' === $match[1] && ']' === $match[6];

			$secrets[] = ! $is_escaped && is_string( $atts['secret'] ?? null ) ? $atts['secret'] : '';
		}

		// Containment recurses into an enclosing [gravityview]...[/gravityview], so
		// an embed nested there must be counted or the post reads as carrying none.
		// WordPress never re-processes that content, so no secret written inside it
		// can render the View: each one counts only as an inert reference.
		foreach ( $matches as $match ) {
			if ( '' === ( $match[5] ?? '' ) ) {
				continue;
			}

			$enclosed = $this->collect_embed_secrets( $view_id, $match[5] );

			$secrets = array_merge( $secrets, array_fill( 0, count( $enclosed ), '' ) );
		}

		if ( ! function_exists( 'has_block' ) || ! has_block( self::VIEW_BLOCK_NAME, $content ) ) {
			return $secrets;
		}

		foreach ( $this->flatten_blocks( parse_blocks( $content ) ) as $block ) {
			// Only the View block embeds a directory; entry and field blocks carry
			// a viewId too, and they render something else entirely.
			if ( self::VIEW_BLOCK_NAME !== ( $block['blockName'] ?? '' ) ) {
				continue;
			}

			if ( (int) ( $block['attrs']['viewId'] ?? 0 ) !== $view_id ) {
				continue;
			}

			$secrets[] = is_string( $block['attrs']['secret'] ?? null ) ? $block['attrs']['secret'] : '';
		}

		return $secrets;
	}

	/**
	 * Returns the parsed blocks and every descendant, depth first.
	 *
	 * @since 3.2.0
	 *
	 * @param array $blocks Parsed blocks.
	 *
	 * @return array
	 */
	private function flatten_blocks( array $blocks ): array {
		$flattened = [];
		$queue     = $blocks;

		while ( $queue ) {
			$block       = array_shift( $queue );
			$flattened[] = $block;

			if ( empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
				continue;
			}

			array_unshift( $queue, ...$block['innerBlocks'] );
		}

		return $flattened;
	}

	/**
	 * The response returned when a lightbox entry request is denied.
	 *
	 * Identical to the "not found" response so a hidden or unapproved entry is
	 * indistinguishable from a missing one (prevents enumeration).
	 *
	 * @since 3.0.1
	 *
	 * @return WP_REST_Response
	 */
	private function access_denied_response(): WP_REST_Response {
		ob_start();

		printf( '<html>%s</html>', esc_html__( 'The requested entry could not be found.', 'gk-gravityview' ) );

		return new WP_REST_Response( null, 404, [ 'Content-Type' => 'text/html' ] );
	}

	/**
	 * Rewrites the directory link when inside the REST context.
	 *
	 * @used-by `gravityview/view/links/directory` filter.
	 *
	 * @since   2.29.0
	 *
	 * @param string $link The directory link.
	 *
	 * @return string
	 */
	public function rewrite_directory_link( $link ) {
		if ( ! is_a( gravityview()->request, 'GravityView_Lightbox_Entry_Request' ) ) {
			return $link;
		}

		$view  = gravityview()->request->is_view();
		$entry = gravityview()->request->is_entry();

		if ( ! $view || ! $entry ) {
			return $link;
		}

		return $this->get_rest_directory_link( $view->ID, $entry->ID );
	}

	/**
	 * Returns REST directory link for specific View and entry.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $view_id   The View ID.
	 * @param string $entry_ids The entry IDs (comma-separated).
	 *
	 * @return string
	 */
	public function get_rest_directory_link( $view_id, $entry_ids ) {
		$args = [];

		// Carry the directory's query context (search inputs, Advanced Filter
		// {get:...} values) so a request-dependent filter recomputes the same entry
		// subset on the REST request; without it the entry falls outside that subset
		// and 404s.
		if ( apply_filters( 'gravityview/entry_link/add_query_args', true ) ) {
			$args = gv_get_query_args();

			// Never carry keys that address the REST route or its actions; the
			// generated link resolves only to this View and entry, as a read.
			unset( $args['rest_route'], $args['view_id'], $args['entry_ids'], $args['edit'], $args['delete'], $args['duplicate'], $args['post_id'] );
		}

		global $post;

		// The embedding post, so the REST request can rebuild the page context
		// that {custom_field:...} and {embed_post:...} filter values resolve from.
		if ( $post instanceof WP_Post ) {
			$args['post_id'] = $post->ID;
		}

		$args['_wpnonce'] = wp_create_nonce( 'wp_rest' );

		return add_query_arg(
			$args,
			rest_url( $this->get_rest_endpoint( $view_id, $entry_ids ) ),
		);
	}

	/**
	 * Returns REST endpoint for specific View and entry.
	 *
	 * @since 2.29.0
	 *
	 * @param int    $view_id   The View ID.
	 * @param string $entry_ids The entry IDs (comma-separated).
	 *
	 * @return string
	 */
	public function get_rest_endpoint( $view_id, $entry_ids ) {
		return sprintf(
			'%s/v%s/view/%s/entry/%s',
			self::REST_NAMESPACE,
			self::REST_VERSION,
			$view_id,
			$entry_ids
		);
	}

	/**
	 * Returns REST endpoint from the current request.
	 *
	 * @since 2.29.0
	 *
	 * @return string|null
	 */
	public function get_rest_endpoint_from_request() {
		global $wp;

		$rest_route = isset( $wp->query_vars['rest_route'] ) ? $wp->query_vars['rest_route'] : null;

		if ( ! is_string( $rest_route ) ) {
			return null;
		}

		// Anchored to the full route so a request path that merely contains
		// this endpoint is not matched as the lightbox route.
		preg_match(
			sprintf(
				'#^/?%s/v%s/(?P<endpoint>%s)/?$#',
				self::REST_NAMESPACE,
				self::REST_VERSION,
				self::REST_ENDPOINT_REGEX
			),
			$rest_route,
			$matches
		);

		return $matches['endpoint'] ?? null;
	}

	/**
	 * Returns the View and entry IDs from the REST endpoint.
	 *
	 * @since 2.29.0
	 *
	 * @param string $endpoint The REST endpoint.
	 *
	 * @return array{view_id:string, entry_ids:string}|null
	 */
	public function get_view_and_entry_from_rest_endpoint( $endpoint ) {
		preg_match( self::REST_ENDPOINT_REGEX, $endpoint, $matches );

		return ! empty( $matches ) ? [
			'view_id'   => $matches['view_id'],
			'entry_ids' => $matches['entry_ids'],
		] : null;
	}

	/**
	 * Rewrites Single Entry or Edit Entry links to open inside lightbox.
	 *
	 * @used-by `gravityview/template/field/entry_link` filter.
	 *
	 * @since   2.29.0
	 * @since   2.36.0 Switched to using `gravityview/template/field/entry_link` filter, and updated method parameters.
	 *
	 * @param string           $link    The entry link (HTML markup).
	 * @param string           $href    The entry link URL.
	 * @param Template_Context $context The context.
	 *
	 * @return string
	 */
	public function rewrite_entry_link( $link, $href, $context ) {
		$view           = GravityView_View::getInstance();
		$entry          = $context->entry->as_entry();
		$field_settings = $context->field->as_configuration();
		$is_rest        = ! empty( $this->get_rest_endpoint_from_request() );
		$is_edit        = 'edit_link' === ( $field_settings['id'] ?? '' );

		if ( ! (int) ( $field_settings['lightbox'] ?? 0 ) && ! $is_rest ) {
			return $link;
		}

		$entry_ids = $context->entry->is_multi() ? array_map( fn( $entry ) => $entry->ID, $context->entry->entries ) : [ $entry['id'] ];

		$entry_link_url = $this->get_rest_directory_link( $view->view_id, implode( ',', $entry_ids ) );

		if ( $is_edit ) {
			$entry_link_url = add_query_arg(
				[
					'edit' => wp_create_nonce(
						GravityView_Edit_Entry::get_nonce_key(
							$view->view_id,
							$view->form_id,
							$entry['id']
						)
					),
				],
				$entry_link_url,
			);
		}

		$atts = [
			'class'         => 'gravityview-fancybox',
			'rel'           => 'nofollow',
			'data-type'     => 'iframe',
			'data-fancybox' => $view->getCurrentField()['UID'],
		];

		if ( in_array( $field_settings['id'], [ 'edit_link', 'entry_link' ], true ) ) {
			$link_text = $is_edit ? $field_settings['edit_link'] : $field_settings['entry_link_text'];
		} else {
			// This sets the text for entry values that link to the Single Entry.
			$link_text = preg_match( '/<a[^>]*>(.*?)<\/a>/', $link, $matches ) ? $matches[1] : '';
		}

		$entry_link_markup = gravityview_get_link(
			$entry_link_url,
			$link_text,
			$is_rest ? [] : $atts // Do not add the attributes if the link is being rendered in the REST context.
		);

		/**
		 * Filters the markup of Single Entry or Edit Entry links that open inside a lightbox.
		 *
		 * @since 2.39.0
		 *
		 * @param string           $entry_link_markup The full HTML markup for the entry link.
		 * @param string           $entry_link_url    The entry link URL.
		 * @param string           $link_text         The anchor text of the link.
		 * @param array            $atts              The HTML attributes for the link.
		 * @param \GravityView_View $view              The View object.
		 * @param Template_Context $context           The template context.
		 * @param bool             $is_rest           Whether the link is rendered in a REST context.
		 * @param bool             $is_edit           Whether the link is for editing an entry.
		 *
		 * @return string Filtered entry link markup.
		 */
		return apply_filters( 'gk/gravityview/lightbox/entry/link', $entry_link_markup, $entry_link_url, $link_text, $atts, $view, $context, $is_rest, $is_edit );
	}

	/**
	 * Configures the necessary logic to process the edit entry request.
	 *
	 * @since 2.29.0
	 *
	 * @param string   $nonce The edit entry nonce.
	 * @param View     $view  The View object.
	 * @param GF_Entry $entry The entry object.
	 * @param array    $form  The form data.
	 *
	 * @return void
	 */
	private function process_edit_entry( $nonce, $view, $entry, $form ) {
		if ( ! wp_verify_nonce( $nonce, GravityView_Edit_Entry::get_nonce_key( $view->ID, $form['id'], $entry->ID ) ) ) {
			return;
		}

		add_filter( 'gk/gravityview/edit-entry/renderer/enqueue-entry-lock-assets', '__return_true' );

		add_filter( 'gravityview/edit_entry/verify_nonce', '__return_true' );

		add_filter(
            'gravityview/edit_entry/cancel_onclick',
            function () use ( $view ) {
				if ( 'close_lightbox' === $view->settings->get( 'edit_cancel_lightbox_action' ) ) {
					return 'window.parent.postMessage( { closeFancybox: true, } );';
				} else {
					return '';
				}
			}
        );

		// Updates the GF entry lock UI markup to properly handle requests for accepting the release or taking over the edit lock.
		add_filter(
            'gk/gravityview/edit-entry/renderer/entry-lock-dialog-markup',
            function ( $markup ) {
				// To accept the release, we do an Ajax GET request by passing "release-edit-lock=1" and then close the lightbox.
				$markup = str_replace(
                    'id="gform-release-lock-button"',
                    'id="gform-release-lock-button" onclick="event.preventDefault(); jQuery.ajax({ url: window.location.href, data: { \'release-edit-lock\': 1 }, method: \'GET\', dataType: \'html\' }).done(function() { window.parent.postMessage({ closeFancybox: true }); });"',
                    $markup
				);

				// To take over once the release has been accepted, we do an Ajax GET request by passing "get-edit-lock=1" and then close the GF lock dialog window.
				$markup = str_replace(
                    'id="gform-take-over-button"',
                    'id="gform-take-over-button" onclick="event.preventDefault(); jQuery.ajax({ url: window.location.href, data: { \'get-edit-lock\': 1 }, method: \'GET\', dataType: \'html\' }).done(function() { jQuery( \'#gform-lock-dialog\' ).hide(); });"',
                    $markup
				);

				return $markup;
			}
        );

		// Prevent redirection inside the lightbox by sending event to the parent window and hiding the success message.
		if ( ! in_array( $view->settings->get( 'edit_redirect' ), [ '1', '2' ] ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- The setting can hold a string or an integer; loose matching is relied upon.
			return;
		}

		$reload_page     = 1 === (int) $view->settings->get( 'edit_redirect' ) ? 'true' : 'false';
		$redirect_to_url = 2 === (int) $view->settings->get( 'edit_redirect' ) ? $view->settings->get( 'edit_redirect_url', '' ) : '';

		if ( $redirect_to_url ) {
			$redirect_to_url = esc_url( GravityView_API::replace_variables( $redirect_to_url, $form, $entry->as_entry() ) );
		}

		add_filter(
			'gravityview/edit_entry/success',
			function ( $message ) use ( $view, $reload_page, $redirect_to_url ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- The filter signature provides the message; this callback replaces it entirely.
				return <<<JS
				<style>.gv-notice { display: none; }</style>
				<script>
					window.parent.postMessage( {
					    removeHash: {$reload_page},
						reloadPage: {$reload_page},
						redirectToUrl: '{$redirect_to_url}',
					} );
				</script>
			JS;
			}
		);
	}

	/**
	 * Processes the delete entry action.
	 *
	 * @since 2.29.0
	 * @since 2.33 Added $entry and $form parameters.
	 *
	 * @param View     $view  The View object.
	 * @param GF_Entry $entry The entry object.
	 * @param array    $form  The form data.
	 *
	 * @return \WP_REST_Response
	 */
	private function process_delete_entry( $view, $entry, $form ) {
		global $wp;

		add_filter( 'wp_redirect', '__return_false' ); // Prevent redirection after the entry is deleted.

		do_action_ref_array( 'wp', [ $wp ] ); // Entry deletion hooks to the `wp` action.

		$reload_page     = GravityView_Delete_Entry::REDIRECT_TO_MULTIPLE_ENTRIES_VALUE === (int) $view->settings->get( 'delete_redirect' ) ? 'true' : 'false';
		$redirect_to_url = GravityView_Delete_Entry::REDIRECT_TO_URL_VALUE === (int) $view->settings->get( 'delete_redirect' ) ? esc_url( $view->settings->get( 'delete_redirect_url', '' ) ) : '';

		if ( $redirect_to_url ) {
			$redirect_to_url = esc_url( GravityView_API::replace_variables( $redirect_to_url, $form, $entry->as_entry() ) );
		}

		ob_start();

		// phpcs:disable WordPress.Security.EscapeOutput.HeredocOutputNotEscaped -- $reload_page is a literal true/false string and $redirect_to_url is escaped with esc_url().
		echo <<<JS
			<style>.gv-notice { display: none; }</style>
			<script>
				window.parent.postMessage( {
					closeFancybox: true,
					reloadPage: {$reload_page},
					redirectToUrl: '{$redirect_to_url}',
				} );
			</script>
		JS;
		// phpcs:enable WordPress.Security.EscapeOutput.HeredocOutputNotEscaped

		return new WP_REST_Response(
			null,
			200,
			[ 'Content-Type' => 'text/html' ]
		);
	}

	/**
	 * Processes the duplicate entry action.
	 *
	 * @since 2.29.0
	 *
	 * @return \WP_REST_Response
	 */
	private function process_duplicate_entry() {
		add_filter( 'wp_redirect', '__return_false' ); // Prevent redirection after the entry is duplicated.

		( GravityView_Duplicate_Entry::getInstance() )->process_duplicate();

		ob_start();

		echo <<<'JS'
			<script>
				window.parent.postMessage( {
					closeFancybox: true,
					reloadPage: true,
				} );
			</script>
		JS;

		return new WP_REST_Response(
			null,
			200,
			[ 'Content-Type' => 'text/html' ]
		);
	}

	/**
	 * Sets headers for Gravity Forms form submission.
	 *
	 * @used-by `gform_get_form_confirmation_filter` filter.
	 *
	 * @since   2.29.0
	 *
	 * @param string $response The form submission response.
	 *
	 * @return string
	 */
	public function process_gravity_forms_form_submission( $response ) {
		$rest_endpoint = $this->get_rest_endpoint_from_request();

		if ( array_key_exists( 'gform_submit', $_REQUEST ) && $rest_endpoint ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checks for the presence of the Gravity Forms submission key to set a header; Gravity Forms performs its own verification.
			header( 'Content-Type: text/html' );
		}

		return $response;
	}

	/**
	 * Suppresses the "go back" link URL while the lightbox renders an entry.
	 *
	 * The lightbox renders an entry in isolation, so there is no directory destination
	 * to return to. Registering `__return_false` on the replacement hook hides the link.
	 *
	 * @since 2.60.2
	 */
	private function suppress_back_link_url() {
		add_filter( 'gravityview/template/links/back/url', '__return_false' );
	}

	/**
	 * Renders the single or edit entry lightbox view.
	 *
	 * @since   2.29.0
	 *
	 * @param string               $type  The type of the entry view (single or edit).
	 * @param View                 $view  The View object.
	 * @param Multi_Entry|GF_Entry $entry The entry data.
	 * @param array                $form  The form data.
	 *
	 * @return \WP_REST_Response
	 */
	private function render_entry( $type, $view, $entry, $form ) {
		global $wp;
		global $post;

		$post = $post ?? get_post( $view->ID ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Sets up the global post context required to render the View inside the standalone lightbox document.

		$this->suppress_back_link_url();

		$view_data = GravityView_View_Data::getInstance();
		$view_data->add_view( $view->ID );

		GravityView_frontend::getInstance()->setGvOutputData( $view_data );
		GravityView_frontend::getInstance()->add_scripts_and_styles();

		$entry_renderer = 'edit' === $type ? new Edit_Entry_Renderer() : new Entry_Renderer();

		do_action_ref_array( 'wp', [ $wp ] );

		/**
		 * Fires before rendering the lightbox entry view.
		 *
		 * @since 2.31.0
		 *
		 * @param View         $view           The View object being rendered.
		 * @param GF_Entry     $entry          The Gravity Forms entry data.
		 * @param array        $form           The Gravity Forms form array.
		 * @param Entry_Render $entry_renderer The renderer object responsible for rendering the entry.
		 */
		do_action_ref_array( 'gk/gravityview/lightbox/entry/before-output', [ &$view, &$entry, &$form, &$entry_renderer ] );

		ob_start();

		$title = do_shortcode(
			GravityView_API::replace_variables(
				$view->settings->get( 'single_title', '' ),
				$form,
				$entry->as_entry()
			)
		);

		$content = $entry_renderer->render(
			$entry,
			$view,
			gravityview()->request,
		);

		?>
		<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>"<?php echo is_rtl() ? ' dir="rtl"' : ''; ?>>
			<head>
				<?php
				/**
				 * Fires after the opening head tag.
				 *
				 * @since 2.31.0
				 *
				 * @param string   $type  The type of the entry view (single or edit).
				 * @param View     $view  The View object being rendered.
				 * @param GF_Entry $entry The Gravity Forms entry data.
				 * @param array    $form  The Gravity Forms form array.
				 */
				do_action( 'gk/gravityview/lightbox/entry/output/head-before', $type, $view, $entry, $form );
				?>

				<title><?php echo esc_html( $title ); ?></title>

				<?php wp_head(); ?>

				<?php $custom_css = $this->get_custom_css( $view ); ?>
				<?php if ( '' !== $custom_css ) : ?>
				<style>
					<?php echo $custom_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Placeholder-resolved and sanitized by get_custom_css(); a </style sequence cannot survive. ?>
				</style>
				<?php endif; ?>

				<?php $custom_javascript = $this->get_custom_javascript( $view ); ?>
				<?php if ( '' !== $custom_javascript ) : ?>
				<script type="text/javascript">
					<?php echo $custom_javascript; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Placeholder-resolved; persisting custom code is gated behind the `unfiltered_html` capability at the save layer. ?>
				</script>
				<?php endif; ?>

				<?php
				/**
				 * Fires before the closing head tag.
				 *
				 * @since 2.31.0
				 *
				 * @param string   $type  The type of the entry view (single or edit).
				 * @param View     $view  The View object being rendered.
				 * @param GF_Entry $entry The Gravity Forms entry data.
				 * @param array    $form  The Gravity Forms form array.
				 */
				do_action( 'gk/gravityview/lightbox/entry/output/head-after', $type, $view, $entry, $form );
				?>
			</head>

			<body>
				<?php
				/**
				 * Fires after the body tag before the content is rendered.
				 *
				 * @since 2.31.0
				 *
				 * @param string   $type  The type of the entry view (single or edit).
				 * @param View     $view  The View object being rendered.
				 * @param GF_Entry $entry The Gravity Forms entry data.
				 * @param array    $form  The Gravity Forms form array.
				 */
				do_action( 'gk/gravityview/lightbox/entry/output/content-before', $type, $view, $entry, $form );
				?>

				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is rendered by GravityView's trusted Entry_Renderer, which escapes field output. ?>

				<?php
				/**
				 * Fires inside the body tag after the content is rendered and before the footer.
				 *
				 * @since 2.31.0
				 *
				 * @param string   $type  The type of the entry view (single or edit).
				 * @param View     $view  The View object being rendered.
				 * @param GF_Entry $entry The Gravity Forms entry data.
				 * @param array    $form  The Gravity Forms form array.
				 */
				do_action( 'gk/gravityview/lightbox/entry/output/content-after', $type, $view, $entry, $form );
				?>

				<?php wp_footer(); ?>

				<?php
				/**
				 * Fires after the footer and before the closing body tag.
				 *
				 * @since 2.31.0
				 *
				 * @param string   $type  The type of the entry view (single or edit).
				 * @param View     $view  The View object being rendered.
				 * @param GF_Entry $entry The Gravity Forms entry data.
				 * @param array    $form  The Gravity Forms form array.
				 */
				do_action( 'gk/gravityview/lightbox/entry/output/footer-after', $type, $view, $entry, $form );
				?>
			</body>
		</html>
		<?php

		return new WP_REST_Response(
			null,
			200,
			[ 'Content-Type' => 'text/html' ]
		);
	}

	/**
	 * Returns the View's Custom CSS prepared for the lightbox document head.
	 *
	 * Runs the same pipeline as the main frontend render: placeholders
	 * (`VIEW_SELECTOR`, `VIEW_WRAPPER`, `VIEW_ID`, `GF_FORM_ID`) resolve
	 * first, then the value runs through
	 * {@see \GravityKit\GravityView\Settings\ViewStyles::sanitize_custom_css()}
	 * so a `</style` sequence cannot escape the inline `<style>` element.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view The View being rendered.
	 *
	 * @return string Sanitized CSS, or an empty string when the View has none.
	 */
	private function get_custom_css( $view ) {
		$custom_css = (string) $view->settings->get( 'custom_css', '' );

		$custom_css = \GravityKit\GravityView\Frontend\Frontend::replace_code_placeholders( $custom_css, $view );

		return \GravityKit\GravityView\Settings\ViewStyles::sanitize_custom_css( $custom_css );
	}

	/**
	 * Returns the View's custom JavaScript prepared for the lightbox document head.
	 *
	 * Matches the main frontend emission: placeholders resolve, and the
	 * value is otherwise trusted because persisting custom code is gated
	 * behind the `unfiltered_html` capability at the save layer.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view The View being rendered.
	 *
	 * @return string JavaScript with placeholders resolved.
	 */
	private function get_custom_javascript( $view ) {
		$custom_javascript = (string) $view->settings->get( 'custom_javascript', '' );

		return \GravityKit\GravityView\Frontend\Frontend::replace_code_placeholders( $custom_javascript, $view );
	}

	/**
	 * Enqueues View editor script that handles the lightbox entry settings.
	 *
	 * @used-by `gk/foundation/inline-scripts` filter.
	 *
	 * @since   2.29.0
	 *
	 * @param array $scripts The registered scripts.
	 *
	 * @return array
	 */
	public function enqueue_view_editor_script( $scripts ) {
		global $post;

		if ( ! $post || ( ! $post instanceof \WP_Post && 'gravityview' !== $post->post_type && 'edit' !== $post->filter ) ) {
			return $scripts;
		}

		$scripts[] = [
			'script' => <<<'JS'
				jQuery( document ).ready( function ( jQuery ) {
					// Show/hide the View's "Cancel Link Action" setting under Edit Entry.
					function toggleCancelLinkActionViewSetting() {
						const isLightBoxEnabled = jQuery( 'input[type="checkbox"]' )
							.filter( function () {
								return /^fields\[directory_table-columns\]\[.*?\]\[lightbox\]$/.test( jQuery( this ).attr( 'name' ) || '' );
							} )
							.toArray()
							.some( checkbox => checkbox.checked );

						jQuery( 'tr:has(#gravityview_se_edit_cancel_lightbox_action)' ).toggle( isLightBoxEnabled );
					}

					// Disable "open in new tab/window" input when "open in lightbox" is checked, and vice versa.
					function handleInputState( dialog ) {
						const newWindow = dialog.find( '.gv-setting-container-new_window input' );
						const lightbox = dialog.find( '.gv-setting-container-lightbox input' );

						function updateState( active, inactive ) {
							if ( active.is( ':checked' ) ) {
								inactive.prop( 'checked', false ).prop( 'disabled', true );
							} else {
								inactive.prop( 'disabled', false );
							}
						}

						updateState( newWindow, lightbox );
						updateState( lightbox, newWindow );
					}

					function handleChange( changedInput, otherInput ) {
						otherInput.prop( 'checked', false );

						// This is a workaround for the element not being disabled probably due to interference from other JS.
						setTimeout( function () {
							otherInput.prop( 'disabled', changedInput.is( ':checked' ) );
						}, 10 );
					}

					jQuery( document ).on( 'gravityview/dialog-opened', function ( event, thisDialog ) {
						const dialog = jQuery( thisDialog );

						handleInputState( dialog );

						const newWindow = dialog.find( '.gv-setting-container-new_window input' );
						const lightbox = dialog.find( '.gv-setting-container-lightbox input' );

						newWindow.on( 'change.lightboxEntry', function () {
							handleChange( jQuery( this ), lightbox );
						} );

						lightbox.on( 'change.lightboxEntry', function () {
							handleChange( jQuery( this ), newWindow );
						} );
					} );

					jQuery( document ).on( 'gravityview/dialog-closed', function ( event, thisDialog ) {
						jQuery( thisDialog )
							.find( '.gv-setting-container-new_window input, .gv-setting-container-lightbox input' )
							.off( 'change.lightboxEntry' );

						toggleCancelLinkActionViewSetting();
					} );

					toggleCancelLinkActionViewSetting();
				} );
			JS,
			'deps'   => [ 'jquery' ],
		];

		return $scripts;
	}

	/**
	 * Performs actions during <head> output.
	 *
	 * @since 2.31.0
	 *
	 * @param string $type The type of the entry view (single or edit).
	 * @param View   $view The View object being rendered.
	 *
	 * @return void
	 */
	public function run_during_head_output( $type, $view ) {
		// Enqueue scripts for the Entry Notes field.
		if ( 'single' !== $type ) {
			return;
		}

		foreach ( $view->fields->all() as $field ) {
			if ( 'notes' === $field->type ) {
				do_action( 'gravityview/field/notes/scripts' );

				break;
			}
		}
	}
}
