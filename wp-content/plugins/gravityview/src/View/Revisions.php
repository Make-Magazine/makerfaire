<?php
/**
 * Revision support for the GravityView post type. Snapshots the
 * meta-driven shape of a View onto every revision row, surfaces a
 * pretty-JSON diff in the WP UI, and restores meta on rollback.
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

namespace GravityKit\GravityView\View;

defined( 'ABSPATH' ) || exit;

class Revisions {

	/**
	 * Distinct from any post meta key so `WP_Post::__get( $field )`
	 * returns `''` instead of resolving to an array meta value, which
	 * would crash WP's `normalize_whitespace( $post->$field )`
	 * change-detection loop in `wp_save_post_revision()`.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	const PSEUDO_FIELD_PREFIX = 'gv_rev_';

	/**
	 * Default tracked meta keys. Add-on labels for these (and any keys
	 * registered via the filter) live in `pseudo_field_labels()`; they
	 * stay separate so translation extraction can find each `__()` call
	 * literal — const expressions can't carry `__()`.
	 *
	 * @since 3.0.0
	 * @var string[]
	 */
	const DEFAULT_TRACKED_META_KEYS = array(
		'_gravityview_form_id',
		'_gravityview_directory_template',
		'_gravityview_single_template',
		'_gravityview_template_settings',
		'_gravityview_directory_fields',
		'_gravityview_directory_widgets',
		'_gravityview_datatables_settings',
		'_gravityview_maps_settings',
	);

	/**
	 * Wire every revision hook. Idempotent.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function register() {
		static $registered = false;
		if ( $registered ) {
			return;
		}
		$registered = true;

		add_action( '_wp_put_post_revision', array( __CLASS__, 'snapshot_meta' ) );
		add_filter( 'wp_save_post_revision_post_has_changed', array( __CLASS__, 'force_revision_when_meta_changed' ), 10, 3 );
		add_filter( '_wp_post_revision_fields', array( __CLASS__, 'register_revision_fields' ), 10, 2 );
		add_action( 'wp_restore_post_revision', array( __CLASS__, 'restore_meta' ), 10, 2 );

		foreach ( self::DEFAULT_TRACKED_META_KEYS as $meta_key ) {
			self::ensure_render_filter( self::pseudo_field_for( $meta_key ) );
		}
	}

	/**
	 * @since 3.0.0
	 * @return string[]
	 */
	public static function tracked_meta_keys() {
		/**
		 * @since 3.0.0
		 * @param string[] $keys Default GravityView-owned meta keys.
		 */
		$keys = apply_filters( 'gk/gravityview/view/revisions/tracked-meta-keys', self::DEFAULT_TRACKED_META_KEYS );

		if ( ! is_array( $keys ) ) {
			return self::DEFAULT_TRACKED_META_KEYS;
		}

		return array_values( array_unique( array_filter( array_map( 'strval', $keys ) ) ) );
	}

	/**
	 * Map a tracked meta key to its diff-UI pseudo-field name. The full
	 * meta key (including any leading underscore) is preserved so two
	 * distinct keys never collapse to the same pseudo-field.
	 *
	 * @since 3.0.0
	 * @param string $meta_key
	 * @return string
	 */
	public static function pseudo_field_for( $meta_key ) {
		return self::PSEUDO_FIELD_PREFIX . (string) $meta_key;
	}

	/**
	 * @since 3.0.0
	 * @param int $revision_id
	 * @return void
	 */
	public static function snapshot_meta( $revision_id ) {
		$parent_id = wp_is_post_revision( $revision_id );
		if ( ! $parent_id ) {
			return;
		}

		if ( View::POST_TYPE !== get_post_type( $parent_id ) ) {
			return;
		}

		foreach ( self::tracked_meta_keys() as $key ) {
			$value = get_post_meta( $parent_id, $key, true );
			if ( '' === $value || null === $value || array() === $value ) {
				delete_metadata( 'post', $revision_id, $key );
				continue;
			}
			update_metadata( 'post', $revision_id, $key, wp_slash( $value ) );
		}
	}

	/**
	 * Force a revision when post columns are unchanged but tracked
	 * meta has changed. Without this, GravityView saves that don't
	 * touch the post title would be silently skipped by WP's default
	 * post_content-only check.
	 *
	 * @since 3.0.0
	 * @param bool     $has_changed
	 * @param \WP_Post $last_revision
	 * @param \WP_Post $post
	 * @return bool
	 */
	public static function force_revision_when_meta_changed( $has_changed, $last_revision, $post ) {
		if ( $has_changed ) {
			return true;
		}

		if ( ! ( $post instanceof \WP_Post ) || View::POST_TYPE !== $post->post_type ) {
			return $has_changed;
		}

		// Warm the meta cache for both posts so the per-key reads below
		// hit cache instead of N+1 DB queries.
		get_post_meta( $post->ID );
		get_post_meta( $last_revision->ID );

		foreach ( self::tracked_meta_keys() as $key ) {
			$current  = self::normalize_meta_value_for_revision_compare( get_post_meta( $post->ID, $key, true ) );
			$previous = self::normalize_meta_value_for_revision_compare(
				get_metadata( 'post', $last_revision->ID, $key, true )
			);
			if ( $current !== $previous ) {
				return true;
			}
		}

		return $has_changed;
	}

	/**
	 * Normalize values the same way snapshot_meta() stores them so empty
	 * live values don't force a redundant revision forever.
	 *
	 * @since 3.0.0
	 * @param mixed $value
	 * @return mixed
	 */
	private static function normalize_meta_value_for_revision_compare( $value ) {
		if ( '' === $value || null === $value || array() === $value ) {
			return '';
		}

		return $value;
	}

	/**
	 * Inject GravityView pseudo-fields into the WP revision diff field
	 * list when inspecting a View; strip them otherwise.
	 *
	 * The strip step is required because WP caches the field list in a
	 * `static`, so our entries would leak into other post types' diff
	 * UIs after the first View render.
	 *
	 * @since 3.0.0
	 * @param array       $fields
	 * @param array|mixed $post
	 * @return array
	 */
	public static function register_revision_fields( $fields, $post = null ) {
		$post_type = null;
		if ( is_array( $post ) && isset( $post['post_type'] ) ) {
			$post_type = $post['post_type'];
		} elseif ( is_object( $post ) && isset( $post->post_type ) ) {
			$post_type = $post->post_type;
		}

		$is_view = ( View::POST_TYPE === $post_type );

		foreach ( self::tracked_meta_keys() as $meta_key ) {
			$pseudo_field = self::pseudo_field_for( $meta_key );

			if ( $is_view ) {
				$fields[ $pseudo_field ] = self::label_for( $meta_key );
				self::ensure_render_filter( $pseudo_field );
				continue;
			}

			unset( $fields[ $pseudo_field ] );
		}

		return $fields;
	}

	/**
	 * @since 3.0.0
	 * @param string $pseudo_field
	 * @return void
	 */
	private static function ensure_render_filter( $pseudo_field ) {
		static $registered = array();
		if ( isset( $registered[ $pseudo_field ] ) ) {
			return;
		}
		$registered[ $pseudo_field ] = true;

		add_filter( '_wp_post_revision_field_' . $pseudo_field, array( __CLASS__, 'render_pseudo_field' ), 10, 3 );
	}

	/**
	 * Render the snapshotted value of one tracked meta key for the WP
	 * revision diff column. Derives the meta key from the pseudo-field
	 * name; arrays + objects render as pretty-printed JSON.
	 *
	 * @since 3.0.0
	 * @param mixed         $value
	 * @param string        $pseudo_field
	 * @param \WP_Post|null $compare_post
	 * @return string
	 */
	public static function render_pseudo_field( $value, $pseudo_field, $compare_post = null ) {
		if ( ! ( $compare_post instanceof \WP_Post ) ) {
			return is_string( $value ) ? $value : '';
		}

		$meta_key = substr( (string) $pseudo_field, strlen( self::PSEUDO_FIELD_PREFIX ) );
		$raw      = get_metadata( 'post', $compare_post->ID, $meta_key, true );
		if ( '' === $raw || null === $raw ) {
			return '';
		}

		if ( is_array( $raw ) || is_object( $raw ) ) {
			$encoded = wp_json_encode( $raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			return false === $encoded ? '' : $encoded;
		}

		return (string) $raw;
	}

	/**
	 * @since 3.0.0
	 * @param string $meta_key
	 * @return string
	 */
	private static function label_for( $meta_key ) {
		$labels = self::pseudo_field_labels();
		return isset( $labels[ $meta_key ] ) ? $labels[ $meta_key ] : ltrim( $meta_key, '_' );
	}

	/**
	 * Translated label map, memoized so each revision UI render rebuilds
	 * the `__()` array at most once per request.
	 *
	 * @since 3.0.0
	 * @return array<string,string>
	 */
	private static function pseudo_field_labels() {
		static $labels = null;
		if ( null !== $labels ) {
			return $labels;
		}
		$labels = array(
			'_gravityview_form_id'             => __( 'Connected Form', 'gk-gravityview' ),
			'_gravityview_directory_template'  => __( 'Multiple Entries Layout', 'gk-gravityview' ),
			'_gravityview_single_template'     => __( 'Single Entry Layout', 'gk-gravityview' ),
			'_gravityview_template_settings'   => __( 'View Settings', 'gk-gravityview' ),
			'_gravityview_directory_fields'    => __( 'Field Configuration', 'gk-gravityview' ),
			'_gravityview_directory_widgets'   => __( 'Widget Configuration', 'gk-gravityview' ),
			'_gravityview_datatables_settings' => __( 'DataTables Settings', 'gk-gravityview' ),
			'_gravityview_maps_settings'       => __( 'Maps Settings', 'gk-gravityview' ),
		);
		return $labels;
	}

	/**
	 * @since 3.0.0
	 * @param int $post_id
	 * @param int $revision_id
	 * @return void
	 */
	public static function restore_meta( $post_id, $revision_id ) {
		if ( View::POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}

		foreach ( self::tracked_meta_keys() as $key ) {
			$value = get_metadata( 'post', $revision_id, $key, true );

			if ( '' === $value || null === $value ) {
				delete_post_meta( $post_id, $key );
				continue;
			}

			update_post_meta( $post_id, $key, wp_slash( $value ) );
		}

		/**
		 * @since 3.0.0
		 * @param int $post_id
		 * @param int $revision_id
		 */
		do_action( 'gk/gravityview/view/revisions/restored', $post_id, $revision_id );
	}
}
