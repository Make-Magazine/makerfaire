<?php
/**
 * A collection of View objects.
 *
 * @package GravityKit\GravityView\View
 * @since 3.0.0
 */

namespace GravityKit\GravityView\View;

/**
 * A collection of View objects.
 *
 * @implements \GV\Collection<View>
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\View namespace.
 */
class ViewCollection extends \GV\Collection {

	/**
	 * @inheritDoc
	 * @return View[]
	 */
	public function all() {
		return parent::all();
	}

	/**
	 * Add a View to this collection.
	 *
	 * @param View $view The view to add to the internal array.
	 *
	 * @api
	 * @since 2.0
	 * @return void
	 */
	public function add( $view ) {

		if ( ! $view instanceof \GV\View ) {
			\gravityview()->log->error( 'View_Collections can only contain objects of type \GV\View.' );
			return;
		}

		parent::add( $view );
	}

	/**
	 * Get a View from this list.
	 *
	 * @param int $view_id The ID of the view to get.
	 *
	 * @api
	 * @since 2.0
	 *
	 * @return \GV\View|null The \GV\View with the $view_id as the ID, or null if not found.
	 */
	public function get( $view_id ) {
		foreach ( $this->all() as $view ) {
			if ( $view->ID == $view_id ) {
				return $view;
			}
		}
		return null;
	}

	/**
	 * Check whether \GV\View with an ID is already here.
	 *
	 * @param int $view_id The ID of the view to check.
	 *
	 * @api
	 * @since 2.0
	 *
	 * @return boolean Whether it exists or not.
	 */
	public function contains( $view_id ) {
		return ! is_null( $this->get( $view_id ) );
	}

	/**
	 * Get a list of View objects inside the supplied \WP_Post.
	 *
	 * The post can be a gravityview post, which is the simplest case.
	 * The post can contain gravityview shortcodes as well.
	 * The post meta can contain gravityview shortcodes.
	 *
	 * @param \WP_Post $post The \WP_Post object to look into.
	 *
	 * @api
	 * @since 2.0
	 * @return ViewCollection A ViewCollection instance containing the views inside the supplied \WP_Post.
	 */
	public static function from_post( \WP_Post $post ) {
		return self::from_post_detailed( $post )['views'];
	}

	/**
	 * Returns the Views found in a post alongside the strings they were found in.
	 *
	 * Callers that must decide whether an embed would actually render need the exact
	 * text this parse looked at. Re-deriving it against a second parser lets the two
	 * drift, and an embed only one of them sees is an embed nobody validates.
	 *
	 * @since 3.2.0
	 *
	 * @param \WP_Post $post The post to look into.
	 *
	 * @return array{views:ViewCollection, authored:ViewCollection, sources:string[]}
	 *               `authored` holds the Views found in the post's own content and
	 *               meta, before server-side declarations are filtered in.
	 */
	public static function from_post_detailed( \WP_Post $post ): array {
		$views    = new self();
		$authored = new self();
		$sources  = array();

		$post_type = get_post_type( $post );

		if ( 'gravityview' === $post_type ) {
			/** A straight up gravityview post. */
			$views->add( \GV\View::from_post( $post ) );
		} else {
			$sources[] = (string) $post->post_content;

			$views->merge( self::from_content( $post->post_content ) );

			/**
			 * Define meta keys to parse to check for GravityView shortcode content.
			 *
			 * This is useful when using themes that store content that may contain shortcodes in custom post meta.
			 *
			 * @since 2.0
			 * @since 2.0.7 Added $views parameter, passed by reference.
			 *
			 * @param array              $meta_keys Array of key values to check. If empty, do not check. Default: empty array.
			 * @param \WP_Post           $post      The post that is being checked.
			 * @param \GV\View_Collection $views    The current View Collection object, passed as reference.
			 */
			$meta_keys = apply_filters_ref_array( 'gravityview/view_collection/from_post/meta_keys', array( array(), $post, &$views ) );

			/**
			 * @filter `gravityview/data/parse/meta_keys`
			 * @deprecated 3.0.0
			 * @see The `gravityview/view_collection/from_post/meta_keys` filter.
			 */
			$meta_keys = (array) \GravityView_Deprecated_Hook_Notices::apply_filters( 'gravityview/data/parse/meta_keys', array( $meta_keys, $post->ID ), '2.0.7', 'gravityview/view_collection/from_post/meta_keys' );

			/** What about inside post meta values? */
			foreach ( $meta_keys as $meta_key ) {
				$views = self::merge_deep( $views, $post->{$meta_key}, $sources );
			}

			// Everything found so far was written into this post by whoever can edit
			// it. A View's own permalink renders with no embed at all, so it never
			// counts as authored.
			$authored->merge( $views );
		}

		/**
		 * Filters the View Collection processed from a WP_Post object.
		 *
		 * This allows code to add Views to the Collection that are not found by the default logic, or modify the View Collection before it is returned.
		 * The GravityView Elementor Widget uses this filter to add Views to the Collection that are embedded in Elementor widgets.
		 * This allows the widget support `?gvid` being properly handled when multiple Views are embedded in the same post.
		 *
		 * @since 2.48.2
		 *
		 * @param \GV\View_Collection $view_collection The View Collection.
		 * @param \WP_Post $post The post.
		 */
		$views = apply_filters( 'gk/gravityview/view-collection/from-post/views', $views, $post );

		return array(
			'views'    => $views,
			'authored' => $authored,
			'sources'  => $sources,
		);
	}

	/**
	 * Process meta values when stored singular (string) or multiple (array). Supports nested arrays and JSON strings.
	 *
	 * @since 2.1
	 *
	 * @param ViewCollection $views      Existing View Collection to merge with.
	 * @param string|array   $meta_value Value to parse. Normally the value of $post->{$meta_key}.
	 * @param string[]       $sources    Optional. Collects every string parsed here.
	 *
	 * @return ViewCollection $views View Collection containing any additional Views found
	 */
	private static function merge_deep( $views, $meta_value, array &$sources = array() ) {
		$meta_value = gv_maybe_json_decode( $meta_value, true );

		if ( is_array( $meta_value ) ) {
			foreach ( $meta_value as $index => $item ) {
				$meta_value[ $index ] = self::merge_deep( $views, $item, $sources );
			}
		}

		if ( is_string( $meta_value ) ) {
			$sources[] = $meta_value;

			$view_ids = array_map( fn( $view ) => $view->ID, $views->all() );

			// Merge only unique Views.
			foreach ( self::from_content( $meta_value )->all() as $view ) {
				if ( ! in_array( $view->ID, $view_ids, true ) ) {
					$views->add( $view );
				}
			}
		}

		return $views;
	}

	/**
	 * Get a list of detected View objects inside the supplied content.
	 *
	 * The content can have a shortcode, this is the simplest case.
	 *
	 * @param string $content The content to look into.
	 *
	 * @api
	 * @since 2.0
	 * @return ViewCollection A ViewCollection instance containing the views inside the supplied \WP_Post.
	 */
	public static function from_content( $content ) {
		$views = new self();

		/** Let's find us some [gravityview] shortcodes perhaps. */
		foreach ( \GV\Shortcode::parse( $content ) as $shortcode ) {
			if ( 'gravityview' != $shortcode->name || empty( $shortcode->atts['id'] ) ) {
				continue;
			}

			if ( is_numeric( $shortcode->atts['id'] ) ) {
				$view = \GV\View::by_id( $shortcode->atts['id'] );
				if ( ! $view ) {
					continue;
				}

				$view->settings->update( $shortcode->atts );
				$views->add( $view );
			}
		}

		if ( function_exists( 'has_block' ) && has_block( 'gk-gravityview-blocks/view', $content ) ) {
			// Flattened because has_block() matches nested blocks: a View placed
			// inside a Group or Columns block is only reachable through innerBlocks.
			$blocks = self::flatten_blocks( parse_blocks( $content ) );

			foreach ( $blocks as $block ) {
				// Entry, entry-field, entry-link and view-details blocks carry a
				// viewId as well; only the View block embeds a directory.
				if ( 'gk-gravityview-blocks/view' !== ( $block['blockName'] ?? '' ) ) {
					continue;
				}

				if ( empty( $block['attrs']['viewId'] ) ) {
					continue;
				}

				if ( ! is_numeric( $block['attrs']['viewId'] ) ) {
					continue;
				}

				$view = \GV\View::by_id( $block['attrs']['viewId'] );

				if ( ! $view ) {
					\gravityview()->log->error( 'Could not find View #{view_id} associated with the block.', array( 'view_id' => $block['attrs']['viewId'] ) );
					continue;
				}

				$atts = \GV\Shortcodes\gravityview::map_block_atts_to_shortcode_atts( $block['attrs'] );

				$view->settings->update( $atts );
				$views->add( $view );
			}
		}

		return $views;
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
	private static function flatten_blocks( array $blocks ): array {
		$flattened = array();
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
}
