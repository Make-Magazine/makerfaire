<?php
/**
 * Handle management of the Admin Bar links
 *
 * @since 1.13
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @package GravityKit\GravityView\Admin
 */

namespace GravityKit\GravityView\Admin;

use GravityKit\GravityView\Permissions\Permissions;
/**
 * Handle management of the Admin Bar links
 *
 * @since 1.13
 * @since 3.0.0 Migrated to PSR-4 namespace.
 */
class AdminBar {

	/**
	 * Whether hooks have already been added.
	 *
	 * Prevents duplicate hook registration when both PSR-4 and legacy classes are loaded.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $hooks_added = false;

	/**
	 * @var \GravityView_frontend|null
	 */
	public $gravityview_view = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.13
	 */
	public function __construct() {

		if ( self::$hooks_added ) {
			return;
		}

		self::$hooks_added = true;

		$this->gravityview_view = \GravityView_frontend::getInstance();

		$this->add_hooks();
	}

	/**
	 * @since 1.13
	 */
	private function add_hooks() {
		add_action( 'add_admin_bar_menus', [ $this, 'remove_links' ], 80 );
		add_action( 'admin_bar_menu', [ $this, 'add_links' ], 85 );
		add_action( 'wp_after_admin_bar_render', [ $this, 'add_floaty_icon' ] );
	}

	/**
	 * Add helpful GV links to the menu bar, like Edit Entry on single entry page.
	 *
	 * @since 1.13
	 * @return void
	 */
	public function add_links() {
		/** @var \WP_Admin_Bar $wp_admin_bar */
		global $wp_admin_bar;

		if ( ! \GVCommon::has_cap( [ 'edit_gravityviews', 'gravityview_edit_entry', 'gravityforms_edit_forms' ] ) ) {
			return;
		}

		$view_data = \GravityView_View_Data::getInstance()->get_views();

		// Dashboard Views.
		$view = false;
		if ( is_admin() ) {
			$view = gravityview()->request->is_view();
		}

		if ( empty( $view_data ) && empty( $view ) ) {
			return;
		}

		$wp_admin_bar->add_menu(
			[
				'id'    => 'gravityview',
				'title' => __( 'GravityKit', 'gk-gravityview' ),
				'href'  => admin_url( 'edit.php?post_type=gravityview&page=gravityview_settings' ),
			]
		);

		$this->add_edit_view_and_form_link();

		$this->add_edit_entry_link();
	}

	/**
	 * Add the Floaty icon to the toolbar without loading the whole icon font
	 *
	 * @since 1.17
	 *
	 * @return void
	 */
	public function add_floaty_icon() {
		?>
		<style>
			#wp-admin-bar-gravityview > .ab-item:before {
				content: '';
				background: url("data:image/svg+xml;base64,<?php echo base64_encode( '<svg id="Artwork" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><path fill="#a7aaad" class="st0" d="M128 0C57.3 0 0 57.3 0 128s57.3 128 128 128 128-57.3 128-128S198.7 0 128 0zm0 243.2c-63.6 0-115.2-51.6-115.2-115.2S64.4 12.8 128 12.8 243.2 64.4 243.2 128 191.6 243.2 128 243.2zm7.9-172.5c-.8.1-1.4-.5-1.5-1.3V57.7c-.1-.9.4-1.8 1.3-2.1 7.8-4.2 10.6-13.9 6.4-21.7-4.2-7.8-13.9-10.6-21.7-6.4-7.8 4.2-10.6 13.9-6.4 21.7 1.5 2.7 3.7 4.9 6.4 6.4.8.3 1.4 1.2 1.3 2.1v11.4c.1.8-.4 1.5-1.2 1.6h-.3c-41 3-68.9 29.6-68.9 66.9 0 39.6 31.5 67.2 76.8 67.2s76.8-27.6 76.8-67.2c-.1-37.3-28-63.9-69-66.9zM128 182.4c-35.9 0-60.8-18.4-60.8-44.8S92.1 92.8 128 92.8s60.8 18.4 60.8 44.8-24.9 44.8-60.8 44.8zm53.8-44.8c0 22.3-22.1 37.8-53.8 37.8-5.1 0-10.2-.4-15.2-1.3-6.8-1.2-9.4-3.2-12-9.6-3.1-7.5-4.8-16.6-4.8-26.9s1.7-19.4 4.8-26.9c2.7-6.4 5.2-8.4 12-9.6 5-.9 10.1-1.3 15.2-1.3 31.7 0 53.8 15.5 53.8 37.8z"/></svg>' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode, WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup base64-encoded into a CSS data URI; escaping would corrupt the image. ?>") 50% 50% no-repeat !important;
				/* The SVG has no intrinsic dimensions, so pin its size for consistent rendering across browsers. */
				background-size: 20px 20px !important;

				/*
				 * Center the 20px icon in the 32px toolbar.
				 *
				 * The centering relies on the pseudo-element's box being exactly 20px tall, which means
				 * the inherited core `padding: 4px 0` must sit inside the height (border-box), not add to it.
				 * We cannot rely on inheriting that: WordPress declares content-box on `#wpadminbar *`, but
				 * the universal selector never matches `::before`, so the pseudo takes the document default.
				 * That default is border-box on the frontend (theme reset) but content-box in wp-admin, where
				 * the box then grows to 28px and the icon drops ~4px. Pinning border-box centers it in both.
				 */
				box-sizing: border-box;
				top: 6px;
				width: 20px;
				height: 20px;
				display: inline-block;
			}
		</style>
		<?php
	}

	/**
	 * Add Edit Entry links when on a single entry
	 *
	 * @since 1.13
	 * @return void
	 */
	public function add_edit_entry_link() {
		/** @var \WP_Admin_Bar $wp_admin_bar */
		global $wp_admin_bar;

		$entry = gravityview()->request->is_entry();

		if ( $entry && \GVCommon::has_cap( [ 'gravityforms_edit_entries', 'gravityview_edit_entries' ], $entry->ID ) ) {
			$entry_data = $entry->as_entry();

			$wp_admin_bar->add_menu(
				[
					'id'     => 'edit-entry',
					'parent' => 'gravityview',
					'title'  => __( 'Edit Entry', 'gk-gravityview' ),
					'meta'   => [
						/* translators: %s: The entry slug (entry ID or custom entry slug). */
						'title' => sprintf( __( 'Edit Entry %s', 'gk-gravityview' ), $entry->get_slug() ),
					],
					'href'   => esc_url_raw( admin_url( sprintf( 'admin.php?page=gf_entries&amp;screen_mode=edit&amp;view=entry&amp;id=%d&lid=%d', $entry_data['form_id'], $entry_data['id'] ) ) ),
				]
			);

		}
	}

	/**
	 * Add Edit View link when in embedded View
	 *
	 * @since 1.13
	 * @return void
	 */
	public function add_edit_view_and_form_link() {
		/** @var \WP_Admin_Bar $wp_admin_bar */
		global $wp_admin_bar, $post;

		if ( ! \GVCommon::has_cap(
			[ 'edit_gravityviews', 'edit_gravityview', 'gravityforms_edit_forms' ],
			isset( $post ) ? $post->ID : null
		) ) {
			return;
		}

		$view_data = \GravityView_View_Data::getInstance();
		$views     = $view_data->get_views();

		// Dashboard Views.
		if ( is_admin() ) {
			$view    = gravityview()->request->is_view();
			$views[] = $view;
		}

		// If there is a View embed, show Edit View link.
		if ( ! empty( $views ) ) {

			$added_forms = [];
			$added_views = [];

			foreach ( $views as $view ) {
				if ( ! $view ) {
					continue;
				}

				$view    = \GV\View::by_id( $view['id'] );
				$view_id = $view->ID;
				$form_id = $view->form ? $view->form->ID : null;

				$edit_view_title = esc_html__( 'Edit View', 'gk-gravityview' );
				$edit_form_title = esc_html__( 'Edit Form', 'gk-gravityview' );

				if ( count( $views ) > 1 ) {
					/* translators: %d: The ID of the View. */
					$edit_view_title = sprintf( esc_html_x( 'Edit View #%d', 'Edit View with the ID of %d', 'gk-gravityview' ), $view_id );
					/* translators: %d: The ID of the form. */
					$edit_form_title = sprintf( esc_html_x( 'Edit Form #%d', 'Edit Form with the ID of %d', 'gk-gravityview' ), $form_id );
				}

				if ( ( new Permissions() )->can_edit_view( (int) $view_id ) && ! in_array( $view_id, $added_views, true ) ) {

					$added_views[] = $view_id;

					$wp_admin_bar->add_menu(
						[
							'id'     => 'edit-view-' . $view_id,
							'parent' => 'gravityview',
							'title'  => $edit_view_title,
							'href'   => esc_url_raw( admin_url( sprintf( 'post.php?post=%d&action=edit', $view_id ) ) ),
						]
					);
				}

				if ( ! empty( $form_id ) && \GVCommon::has_cap( [ 'gravityforms_edit_forms' ], $form_id ) && ! in_array( $form_id, $added_forms, true ) ) {

					$added_forms[] = $form_id;

					$wp_admin_bar->add_menu(
						[
							'id'     => 'edit-form-' . $form_id,
							'parent' => 'gravityview',
							'title'  => $edit_form_title,
							'href'   => esc_url_raw( admin_url( sprintf( 'admin.php?page=gf_edit_forms&id=%d', $form_id ) ) ),
						]
					);
				}
			}
		}
	}

	/**
	 * Remove "Edit Page" or "Edit View" links when on single entry.
	 *
	 * @since 1.17 Also remove when on GravityView post type; the new GravityView menu will be the one-stop shop.
	 * @since 1.13
	 *
	 * @return void
	 */
	public function remove_links() {

		// If we're on the single entry page, we don't want to cause confusion.
		if ( $this->gravityview_view->getSingleEntry() || $this->gravityview_view->isGravityviewPostType() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );
		}
	}
}
