<?php
/**
 * GravityView Admin Add Shortcode class.
 *
 * PSR-4 migration of the legacy GravityView_Admin_Add_Shortcode class.
 *
 * @package GravityKit\GravityView\Admin
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

use GravityKit\GravityView\Utils\Assets;

class AddShortcode {

	/**
	 * Whether hooks have already been added.
	 *
	 * Prevents double-registration when both PSR-4 autoload and legacy
	 * side-effect instantiation paths are triggered.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $hooks_added = false;

	function __construct() {

		if ( self::$hooks_added ) {
			return;
		}
		self::$hooks_added = true;

		add_action( 'media_buttons', [ $this, 'add_shortcode_button' ], 30 );

		add_action( 'admin_footer', [ $this, 'add_shortcode_popup' ] );

		// adding styles and scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'add_scripts_and_styles' ] );

		// ajax - populate sort fields based on the selected view
		add_action( 'wp_ajax_gv_sortable_fields', [ $this, 'get_sortable_fields' ] );
	}


	/**
	 * check if screen post editor and is not related with post type 'gravityview'
	 *
	 * @return bool
	 */
	function is_post_editor_screen() {
		global $current_screen, $pagenow;
		return ! empty( $current_screen->post_type ) && 'gravityview' != $current_screen->post_type && in_array( $pagenow, [ 'post.php', 'post-new.php' ] );
	}


	/**
	 * Add shortcode button to the Add Media right
	 *
	 * @return void
	 */
	function add_shortcode_button() {

		/**
		 * @since 1.15.3
		 */
		if ( ! \GVCommon::has_cap( [ 'publish_gravityviews' ] ) ) {
			return;
		}

		if ( ! $this->is_post_editor_screen() ) {
			return;
		}
		?>
		<a href="#TB_inline?width=600&amp;height=800&amp;inlineId=select_gravityview_view" class="thickbox hide-if-no-js button gform_media_link" id="add_gravityview" title="<?php esc_attr_e( 'Insert View', 'gk-gravityview' ); ?>"><span class="icon gv-icon-astronaut-head"></span><?php esc_html_e( 'Add View', 'gk-gravityview' ); ?></a>
		<?php
	}



	/**
	 * Add shortcode popup div
	 *
	 * @return void
	 */
	function add_shortcode_popup() {
		global $post;

		if ( ! $this->is_post_editor_screen() ) {
			return;
		}

		$post_type = get_post_type_object( $post->post_type );

		$views = get_posts(
			[
				'post_type'      => 'gravityview',
				'posts_per_page' => -1,
			]
		);

		// If there are no views set up yet, we get outta here.
		if ( empty( $views ) ) {
			echo '<div id="select_gravityview_view"><div class="wrap">' . \GravityView_Admin::no_views_text() . '</div></div>';
			return;
		}

		?>
		<div id="select_gravityview_view">
			<form action="#" method="get" id="select_gravityview_view_form">
				<div class="wrap">

					<h2 class=""><?php esc_html_e( 'Embed a View', 'gk-gravityview' ); ?></h2>
					<?php /* translators: 1: post type singular name, 2: opening anchor tag, 3: closing anchor tag. */ ?>
					<p class="subtitle"><?php printf( esc_attr( __( 'Use this form to embed a View into this %1$s. %2$sLearn more about using shortcodes.%3$s', 'gk-gravityview' ) ), $post_type->labels->singular_name, '<a href="https://docs.gravitykit.com/article/73-using-the-shortcode" target="_blank" rel="noopener noreferrer">', '</a>' ); ?></p>

					<div>
						<h3><label for="gravityview_id"><?php esc_html_e( 'Select a View', 'gk-gravityview' ); ?></label></h3>

						<select name="gravityview_id" id="gravityview_id">
							<option value=""><?php esc_html_e( '&mdash; Select a View to Insert &mdash;', 'gk-gravityview' ); ?></option>
							<?php
							foreach ( $views as $view ) {
								$title = empty( $view->post_title ) ? __( '(no title)', 'gk-gravityview' ) : $view->post_title;
								echo '<option value="' . $view->ID . '">' . esc_html( sprintf( '%s #%d', $title, $view->ID ) ) . '</option>';
							}
							?>
						</select>
					</div>

					<table class="form-table hide-if-js">

						<caption><?php esc_html_e( 'View Settings', 'gk-gravityview' ); ?></caption>

						<?php

						$settings = \GV\View_Settings::defaults( true );

						foreach ( $settings as $key => $setting ) {

							if ( empty( $setting['show_in_shortcode'] ) ) {
								continue; }

							\GravityView_Render_Settings::render_setting_row( $key, [], null, 'gravityview_%s', 'gravityview_%s' );
						}
						?>

					</table>

					<div class="submit">
						<input type="submit" class="button button-primary button-large alignleft hide-if-js" value="<?php esc_attr_e( 'Insert View', 'gk-gravityview' ); ?>" id="insert_gravityview_view" />
						<input class="button button-secondary alignright" type="submit" onclick="tb_remove(); return false;" value="<?php esc_attr_e( 'Cancel', 'gk-gravityview' ); ?>" />
					</div>

				</div>
			</form>
		</div>
		<?php
	}




	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function add_scripts_and_styles() {

		if ( ! $this->is_post_editor_screen() ) {
			return;
		}

		wp_enqueue_style( 'dashicons' );

		// Register the QF pickers bundle (provides the calendar popover for date fields).
		\GravityKit\GravityView\QueryFilters\QueryFilters::enqueue_date_picker( [ 'variable_name' => 'gvAdminDatePicker' ] );

		// enqueue styles
		wp_register_style( 'gravityview_postedit_styles', Assets::url( 'css/admin-post-edit.css' ), [], GV_PLUGIN_VERSION );
		wp_enqueue_style( 'gravityview_postedit_styles' );

		// custom js
		wp_register_script( 'gravityview_postedit_scripts', Assets::url( 'js/admin-post-edit' . Assets::min() . '.js' ), [ 'jquery', 'gk-query-filters-pickers' ], GV_PLUGIN_VERSION );
		wp_enqueue_script( 'gravityview_postedit_scripts' );
		wp_localize_script(
			'gravityview_postedit_scripts',
			'gvGlobals',
			[
				'nonce'        => wp_create_nonce( 'gravityview_ajaxaddshortcode' ),
				'loading_text' => esc_html__( 'Loading&hellip;', 'gk-gravityview' ),
				'alert_1'      => esc_html__( 'Please select a View', 'gk-gravityview' ),
			]
		);
	}



	/**
	 * Ajax
	 * Given a View id, calculates the assigned form, and returns the form fields (only the sortable ones )
	 *
	 * @return mixed
	 */
	function get_sortable_fields() {

		// Not valid request
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_ajaxaddshortcode' ) ) {
			return $this->maybe_exit( false );
		}

		// A nonce is CSRF protection, not authorization. The legitimate callers (the
		// classic Add View button and the block editor) already hold one of these
		// caps, so requiring them blocks arbitrary field reads without a regression.
		if ( ! \GVCommon::has_cap( [ 'edit_gravityviews', 'publish_gravityviews' ] ) ) {
			return $this->maybe_exit( false );
		}

		// Not properly formatted request
		if ( empty( $_POST['viewid'] ) || ! is_numeric( $_POST['viewid'] ) ) {
			return $this->maybe_exit( false );
		}

		$viewid = (int) $_POST['viewid'];

		// fetch form id assigned to the view
		$formid = gravityview_get_form_id( $viewid );

		// Get the default sort field for the view
		$sort_field = gravityview_get_template_setting( $viewid, 'sort_field' );

		// Generate the output `<option>`s
		$response = gravityview_get_sortable_fields( $formid, $sort_field );

		return $this->maybe_exit( $response );
	}

	/**
	 * Output a response and stop. Returns instead of exiting under the test suite.
	 *
	 * @since 3.0.2
	 *
	 * @param mixed $response Response to output.
	 *
	 * @return mixed
	 */
	private function maybe_exit( $response = null ) {
		if ( defined( 'DOING_GRAVITYVIEW_TESTS' ) && \DOING_GRAVITYVIEW_TESTS ) {
			return $response;
		}

		exit( $response );
	}
}
