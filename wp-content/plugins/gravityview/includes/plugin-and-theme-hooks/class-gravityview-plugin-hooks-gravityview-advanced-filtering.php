<?php

use GravityKit\GravityView\Foundation\Helpers\Core;
use GV\Template_Context;

/**
 * @inheritDoc
 *
 * @since 2.26
 */
class GravityView_Plugin_Hooks_GravityView_Advanced_Filtering extends GravityView_Plugin_and_Theme_Hooks {
	/**
	 * Admin-post action for activating the extension.
	 *
	 * @since 2.56.0
	 *
	 * @var string
	 */
	private const ACTION_ACTIVATE = 'gv_activate_af';

	/**
	 * Admin-post action for removing conditional logic.
	 *
	 * @since 2.56.0
	 *
	 * @var string
	 */
	private const ACTION_REMOVE = 'gv_remove_af_config';

	/**
	 * The post meta key used to store Advanced Filtering conditional logic.
	 *
	 * @since 2.56.0
	 *
	 * @var string
	 */
	private const ADVANCED_FILTER_OPTION_KEY = '_gravityview_filters';

	/**
	 * Foundation notice namespace.
	 *
	 * @since 2.56.0
	 *
	 * @var string
	 */
	private const NOTICE_NAMESPACE = 'gk-gravityview';

	/**
	 * Foundation notice slug prefix.
	 *
	 * @since 2.56.0
	 *
	 * @var string
	 */
	private const NOTICE_SLUG_PREFIX = 'advanced-filter-inactive-';

	/**
	 * Cached result of the plugin installation check.
	 *
	 * @since 2.56.0
	 *
	 * @var bool|null
	 */
	private static ?bool $is_plugin_installed_inactive = null;

	/**
	 * @inheritDoc
	 *
	 * @since 2.26
	 *
	 * @var string
	 */
	public $constant_name = 'GRAVITYKIT_ADVANCED_FILTERING_VERSION';

	/**
	 * @inheritDoc
	 * @since 2.26
	 */
	protected function add_inactive_hooks(): void {
		add_action( 'gk/gravityview/metabox/content/after', [ $this, 'render_metabox_placeholder' ] );
		add_action( 'add_meta_boxes_gravityview', [ __CLASS__, 'maybe_add_editor_notice' ] );
		add_action( 'admin_post_' . self::ACTION_ACTIVATE, [ __CLASS__, 'handle_activate_extension' ] );
		add_action( 'admin_post_' . self::ACTION_REMOVE, [ __CLASS__, 'handle_remove_configuration' ] );

		add_filter( 'gk/foundation/notices/content/allowed-tags', static function ( array $tags ): array {
			if ( isset( $tags['a'] ) ) {
				$tags['a']['data-gv-confirm'] = [];
			}

			return $tags;
		} );

		add_filter( 'gk/foundation/inline-scripts', static function ( array $scripts ): array {
			$scripts[] = [
				'dependencies' => [ 'jquery' ],
				'script'       => "jQuery(document).on('click','a[data-gv-confirm]',function(e){if(!confirm(jQuery(this).data('gv-confirm'))){e.preventDefault();}});",
			];

			return $scripts;
		} );
	}

	/**
	 * Returns whether the View has Advanced Filtering configuration while the extension is not active.
	 *
	 * @since 2.56.0
	 *
	 * @param int $view_id The View ID.
	 *
	 * @return bool Whether the View has inactive Advanced Filtering configuration.
	 */
	public static function has_inactive_configuration( int $view_id ): bool {
		if ( ! $view_id ) {
			return false;
		}

		if ( defined( 'GRAVITYKIT_ADVANCED_FILTERING_VERSION' ) ) {
			return false;
		}

		$filters = get_post_meta( $view_id, self::ADVANCED_FILTER_OPTION_KEY, true );

		if ( empty( $filters ) || 'null' === $filters ) {
			return false;
		}

		// Cache the plugin installation check for the duration of the request.
		if ( self::$is_plugin_installed_inactive === null ) {
			$plugin = Core::get_installed_plugin_by_text_domain( 'gravityview-advanced-filter' );

			self::$is_plugin_installed_inactive = $plugin && empty( $plugin['active'] ?? false );
		}

		return self::$is_plugin_installed_inactive;
	}


	/**
	 * Activates the Advanced Filtering extension and redirects back.
	 *
	 * @since 2.56.0
	 */
	public static function handle_activate_extension(): void {
		$view_id = (int) ( $_GET['view_id'] ?? 0 );

		check_admin_referer( self::ACTION_ACTIVATE . '_' . $view_id );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to activate plugins.', 'gk-gravityview' ) );
		}

		$plugin = Core::get_installed_plugin_by_text_domain( 'gravityview-advanced-filter' );
		$path   = $plugin['path'] ?? '';

		if ( $path ) {
			$result = activate_plugin( $path );

			if ( is_wp_error( $result ) ) {
				gravityview()->log->error(
					'Could not activate Advanced Filtering: {error}',
					[ 'error' => $result->get_error_message() ]
				);
			} else {
				self::remove_stored_notice( $view_id );
			}
		}

		self::redirect_back();
	}

	/**
	 * Removes Advanced Filtering conditional logic from a View and redirects back.
	 *
	 * @since 2.56.0
	 */
	public static function handle_remove_configuration(): void {
		$view_id = (int) ( $_GET['view_id'] ?? 0 );

		check_admin_referer( self::ACTION_REMOVE . '_' . $view_id );

		if ( ! GVCommon::has_cap( 'edit_gravityview', $view_id ) ) {
			wp_die( esc_html__( 'You do not have permission to edit this View.', 'gk-gravityview' ) );
		}

		$deleted = delete_post_meta( $view_id, self::ADVANCED_FILTER_OPTION_KEY );

		if ( $deleted ) {
			self::remove_stored_notice( $view_id );
		} else {
			gravityview()->log->error(
				'Could not remove Advanced Filtering configuration for View #{view_id}.',
				[ 'view_id' => $view_id ]
			);
		}

		self::redirect_back();
	}

	/**
	 * Redirects to the URL specified in the `redirect` query parameter.
	 *
	 * @since 2.56.0
	 */
	private static function redirect_back(): void {
		$redirect = wp_validate_redirect(
			$_GET['redirect'] ?? '',
			admin_url()
		);

		wp_safe_redirect( $redirect );
		exit();
	}


	/**
	 * Builds a nonce-protected admin-post.php URL for a given action.
	 *
	 * @since 2.56.0
	 *
	 * @param string $action  The admin_post action name.
	 * @param int    $view_id The View ID.
	 *
	 * @return string The action URL.
	 */
	private static function build_action_url( string $action, int $view_id ): string {
		// add_query_arg( [] ) returns REQUEST_URI (e.g., /wp-admin/post.php?post=6&action=edit).
		// On the frontend, prefix with home_url to make it absolute.
		// On admin, prefix with site_url (REQUEST_URI already includes /wp-admin/).
		$redirect = is_admin()
			? site_url( add_query_arg( [] ) )
			: home_url( add_query_arg( [] ) );

		$url = add_query_arg(
			[
				'action'   => $action,
				'view_id'  => $view_id,
				'redirect' => rawurlencode( $redirect ),
			],
			admin_url( 'admin-post.php' )
		);

		return wp_nonce_url( $url, $action . '_' . $view_id );
	}


	/**
	 * Builds the Foundation notice message for a View with inactive Advanced Filtering.
	 *
	 * Used for both stored (dashboard) and runtime (View editor) Foundation notices.
	 *
	 * @since 2.56.0
	 *
	 * @param int $view_id The View ID.
	 *
	 * @return string HTML message safe for Foundation's sanitizer.
	 */
	private static function build_foundation_message( int $view_id ): string {
		$view_title = get_the_title( $view_id );
		$edit_link  = admin_url( 'post.php?post=' . $view_id . '&action=edit' );

		return strtr(
			esc_html__(
				'The [view_link][b]{view_title}[/b][/view_link] View has Advanced Filtering conditional logic configured, but the extension is not active. Entries are hidden as a precaution to prevent unfiltered data from displaying.',
				'gk-gravityview'
			),
			[
				'{view_title}'  => esc_html( $view_title ),
				'[b]'           => '<strong>',
				'[/b]'          => '</strong>',
				'[view_link]'   => '<a href="' . esc_url( $edit_link ) . '">',
				'[/view_link]'  => '</a>',
			]
		);
	}

	/**
	 * Builds the full inline notice HTML for the frontend admin notice.
	 *
	 * @since 2.56.0
	 *
	 * @param int $view_id The View ID.
	 *
	 * @return string Complete HTML output for GVCommon::generate_notice().
	 */
	private static function build_inline_notice_html( int $view_id ): string {
		$title = esc_html__( 'Entries are hidden for this View.', 'gk-gravityview' );

		$message = esc_html__(
			'This View has Advanced Filtering conditional logic configured, but the extension is not active. Entries are hidden as a precaution to prevent unfiltered data from displaying.',
			'gk-gravityview'
		);

		$activate_url = self::build_action_url( self::ACTION_ACTIVATE, $view_id );
		$remove_url   = self::build_action_url( self::ACTION_REMOVE, $view_id );

		$actions = sprintf(
			'<hr><div><a href="%s">%s</a> <span class="gv-notice-description">%s</span></div><div><a href="%s" onclick="return confirm(\'%s\');">%s</a> <span class="gv-notice-description">%s</span></div>',
			esc_url( $activate_url ),
			esc_html__( 'Activate Advanced Filtering', 'gk-gravityview' ),
			esc_html__( 'Activate the extension to restore filtered entries.', 'gk-gravityview' ),
			esc_url( $remove_url ),
			esc_js( __( 'This will permanently remove the Advanced Filtering conditional logic from this View. Continue?', 'gk-gravityview' ) ),
			esc_html__( 'Remove conditional logic from this View', 'gk-gravityview' ),
			esc_html__( 'All entries will be displayed without filtering.', 'gk-gravityview' )
		);

		$admin_message = esc_html__( 'Note: You can only see this message because you are able to edit this View.', 'gk-gravityview' );

		$dom_id = 'gv-notice-af-inactive-' . $view_id;

		$style = sprintf(
			'<style>
#%1$s h3 { text-align: center; margin-top: 0; }
#%1$s div { margin: 1em 0; }
#%1$s hr { border: none; border-bottom: 1px solid #ddd; margin: 1em 0; }
#%1$s span.gv-notice-description { display: block; font-style: italic; }
#%1$s .gv-notice-admin-message { margin-top: 1em; }
</style>',
			$dom_id
		);

		return sprintf(
			'%s<div id="%s"><h3>%s</h3><p class="gv-notice-message">%s</p>%s<hr><p class="gv-notice-admin-message"><em>%s</em></p></div>',
			$style,
			esc_attr( $dom_id ),
			$title,
			$message,
			$actions,
			$admin_message
		);
	}


	/**
	 * Registers a runtime Foundation notice when editing a View with inactive Advanced Filtering.
	 *
	 * @since 2.56.0
	 *
	 * @param \WP_Post $post The View post object.
	 */
	public static function maybe_add_editor_notice( $post ): void {
		if ( ! $post instanceof \WP_Post || ! self::has_inactive_configuration( $post->ID ) ) {
			return;
		}

		if ( ! class_exists( 'GravityKitFoundation' ) ) {
			return;
		}

		$notice_manager = GravityKitFoundation::notices();

		if ( ! $notice_manager ) {
			return;
		}

		$activate_url = self::build_action_url( self::ACTION_ACTIVATE, $post->ID );
		$remove_url   = self::build_action_url( self::ACTION_REMOVE, $post->ID );

		$message = strtr(
			esc_html__(
				'This View has Advanced Filtering conditional logic configured, but the extension is not active. Entries are hidden as a precaution to prevent unfiltered data from displaying. [activate_link]Activate Advanced Filtering[/activate_link] or [remove_link]remove conditional logic[/remove_link] from this View.',
				'gk-gravityview'
			),
			[
				'[activate_link]'  => '<a href="' . esc_url( $activate_url ) . '">',
				'[/activate_link]' => '</a>',
				'[remove_link]'    => '<a href="' . esc_url( $remove_url ) . '" data-gv-confirm="' . esc_attr( __( 'This will permanently remove the Advanced Filtering conditional logic from this View. Continue?', 'gk-gravityview' ) ) . '">',
				'[/remove_link]'   => '</a>',
			]
		);

		try {
			$notice_manager->add_runtime( [
				'namespace'    => self::NOTICE_NAMESPACE,
				'slug'         => self::NOTICE_SLUG_PREFIX . 'editor-' . $post->ID,
				'message'      => $message,
				'severity'     => 'error',
				'capabilities' => [ 'edit_gravityviews' ],
				'dismissible'  => false,
				'screens'      => [
					static function ( $notice, $screen ) {
						return $screen && 'gravityview' === ( $screen->post_type ?? '' );
					},
				],
				'context'      => 'all',
			] );
		} catch ( Exception $e ) {
			// Silently fail.
		}
	}

	/**
	 * Prints the inline security notice and registers the stored admin notice.
	 *
	 * @since 2.56.0
	 *
	 * @param int $view_id The View ID.
	 */
	public static function maybe_print_inactive_notice( int $view_id ): void {
		if ( ! self::has_inactive_configuration( $view_id ) ) {
			return;
		}

		// Register a stored notice for admins who haven't visited this View.
		if ( ! GVCommon::has_cap( 'edit_gravityview', $view_id ) ) {
			self::maybe_register_stored_notice( $view_id );

			return;
		}

		echo GVCommon::generate_notice(
			self::build_inline_notice_html( $view_id ),
			'gv-error error',
			'edit_gravityview',
			$view_id
		);
	}

	/**
	 * Registers a stored Foundation notice for a View with inactive Advanced Filtering.
	 *
	 * @since 2.56.0
	 *
	 * @param int $view_id The View ID.
	 */
	private static function maybe_register_stored_notice( int $view_id ): void {
		if ( ! class_exists( 'GravityKitFoundation' ) ) {
			return;
		}

		$notice_manager = GravityKitFoundation::notices();

		if ( ! $notice_manager ) {
			return;
		}

		$notice_id = self::NOTICE_NAMESPACE . '/' . self::NOTICE_SLUG_PREFIX . $view_id;

		// Skip if the notice already exists to avoid repeated update_option calls on every page view.
		if ( $notice_manager->get_notice( $notice_id ) ) {
			return;
		}

		try {
			$notice_manager->add_stored( [
				'namespace'    => self::NOTICE_NAMESPACE,
				'slug'         => self::NOTICE_SLUG_PREFIX . $view_id,
				'message'      => self::build_foundation_message( $view_id ),
				'severity'     => 'error',
				'capabilities' => [ 'edit_gravityviews' ],
				'dismissible'  => false,
				'context'      => 'all',
				'screens'      => [ 'dashboard', 'edit-gravityview' ],
			] );
		} catch ( Exception $e ) {
			gravityview()->log->debug(
				'Failed to register Advanced Filtering inactive notice for View #{view_id}: {error}',
				[ 'view_id' => $view_id, 'error' => $e->getMessage() ]
			);
		}
	}

	/**
	 * Removes the stored Foundation notice for a View.
	 *
	 * @since 2.56.0
	 *
	 * @param int $view_id The View ID.
	 */
	private static function remove_stored_notice( int $view_id ): void {
		if ( ! class_exists( 'GravityKitFoundation' ) ) {
			return;
		}

		$notice_manager = GravityKitFoundation::notices();

		if ( ! $notice_manager ) {
			return;
		}

		try {
			$notice_manager->remove( self::NOTICE_NAMESPACE . '/' . self::NOTICE_SLUG_PREFIX . $view_id );
		} catch ( Exception $e ) {
			// Notice may not exist; safe to ignore.
		}
	}


	/**
	 * Renders the placeholder in the sort_filter metabox.
	 *
	 * @since 2.26
	 *
	 * @param GravityView_Metabox_Tab $metabox The metabox.
	 */
	public function render_metabox_placeholder( GravityView_Metabox_Tab $metabox ): void {
		$disabled = apply_filters( 'gk/gravityview/feature/upgrade/disabled', false );

		if ( $disabled || 'gravityview_sort_filter' !== $metabox->id ) {
			return;
		}

		$this->get_placeholder()->render();
	}

	/**
	 * Returns the placeholder value object.
	 *
	 * @since 2.26
	 *
	 * @return GravityView_Object_Placeholder The placeholder.
	 */
	private function get_placeholder(): GravityView_Object_Placeholder {
		return
			GravityView_Object_Placeholder::inline(
				__( 'Advanced Filtering', 'gk-gravityview' ),
				__( 'Control what entries are displayed in a View using advanced conditional logic.', 'gk-gravityview' ),
				$this->get_placeholder_icon(),
				'gravityview-advanced-filter',
				'https://www.gravitykit.com/products/advanced-filter'
			);
	}

	/**
	 * Returns the icon for the Advanced Filtering extension.
	 *
	 * @since 2.26
	 *
	 * @return string The SVG icon.
	 */
	private function get_placeholder_icon(): string {
		return <<<ICON
<svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="1.5" y="1.5" width="77" height="77" rx="6.5" fill="white"/>
<rect x="1.5" y="1.5" width="77" height="77" rx="6.5" stroke="#FF1B67" stroke-width="3"/>
<path d="M61.877 26.923L44.5 47.5V62.5H36.5V47.5L19.123 26.923" stroke="#FF1B67" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M40.5 31.5C52.6503 31.5 62.5 28.8137 62.5 25.5C62.5 22.1863 52.6503 19.5 40.5 19.5C28.3497 19.5 18.5 22.1863 18.5 25.5C18.5 28.8137 28.3497 31.5 40.5 31.5Z" stroke="#FF1B67" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
ICON;
	}
}

new GravityView_Plugin_Hooks_GravityView_Advanced_Filtering();
