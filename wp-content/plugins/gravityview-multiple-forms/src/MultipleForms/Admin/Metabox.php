<?php

namespace GravityKit\MultipleForms\Admin;

use GravityKit\MultipleForms\AbstractSingleton;
use GravityKit\MultipleForms\View;

/**
 * Class Metabox
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms\Admin
 */
class Metabox extends AbstractSingleton {

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {
		add_filter( 'gravityview/view/settings/defaults', [ $this, 'add_default_view_setting' ] );
		add_action( 'gravityview/metaboxes/data-source/after', [ $this, 'add_metabox_html' ] );
		add_action( 'gravityview_admin_directory_settings', [ $this, 'add_setting' ] );
		add_action( 'gravityview_view_saved', [ $this, 'save_view_joins' ] );
	}

	/**
	 * Render the setting row in the View settings metabox
	 *
	 * @param $current_settings
	 *
	 * @return void
	 */
	public function add_setting( $current_settings ): void {

		if ( ! gravityview()->plugin->supports( \GV\Plugin::FEATURE_GFQUERY ) ) {
			gravityview()->log->notice( 'The Multiple Forms setting was not shown: GF_Query is not supported.' );

			return;
		}

		\GravityView_Render_Settings::render_setting_row( 'multiple_forms_disable_null_joins', $current_settings );
	}

	/**
	 * Add a View setting to toggle strict entry matching
	 *
	 * @param array $defaults
	 *
	 * @return array
	 */
	public function add_default_view_setting( $defaults = [] ): array {

		$defaults['multiple_forms_disable_null_joins'] = [
			'label'             => __( 'Strict Entry Match', 'gravityview-multiple-forms' ),
			'group'             => 'default',
			'requires'          => false,
			'desc'              => __( 'When checked, this setting will only return entries from the main form that have matching entries in the connected form(s). This setting is strict and will reduce the number of results.', 'gravityview-multiple-forms' ),
			'tooltip'           => '<p>' . __( 'By default, all entries in the main form are shown regardless of whether those records have a match in the connected form(s).', 'gravityview-multiple-forms' ) . '</p>'
			                       . sprintf( '<p><strong>%s</strong>:</p><img src="%s" alt="%s">', esc_html_x( 'Default Results', 'Caption of graphic showing differences in result sets', 'gravityview-multiple-forms' ), plugins_url( 'assets/images/LEFT_JOIN_2.svg', GV_MF_FILE ), esc_html_x( 'A table of Form A and Form B. All Form A rows are displaying. Some cells in Form B have content, others do not.', 'Text describing an image', 'gravityview-multiple-forms' ) )
			                       . '<p>' . __( 'When checked, this setting will only return entries from the main form that have matching entries in the connected form(s). This setting is strict and will reduce the number of results.', 'gravityview-multiple-forms' ) . '</p>'
			                       . sprintf( '<p><strong>%s</strong>:</p><img src="%s" alt="%s">', esc_html_x( 'When Enabled', 'Caption of graphic showing differences in result sets', 'gravityview-multiple-forms' ), plugins_url( 'assets/images/INNER_JOIN_2.svg', GV_MF_FILE ), esc_html_x( 'A table of Form A and Form B. Form A rows are displaying only where Form B cells have content.', 'Text describing an image', 'gravityview-multiple-forms' ) ),
			'type'              => 'checkbox',
			'value'             => false,
			'show_in_shortcode' => false,
			'full_width'        => false,
		];

		return $defaults;
	}

	/**
	 * Adds the HTML container to the Data Source plugin, as needed by the plugin
	 *
	 * @since 0.1-beta
	 */
	public function add_metabox_html(): void {

		?>
        <div id="gravityview-multiple-forms"></div>
		<?php
	}

	/**
	 * Update view's meta with available joins
	 *
	 * @since 0.1-beta
	 *
	 * @param int $view_id ID of the View being saved
	 */
	public function save_view_joins( $view_id ): void {

		$parent_form_id = (int) rgpost( 'gravityview_form_id' );
		$joins          = json_decode( rgpost( 'gv_joins' ), true );

		if ( ! $parent_form_id && ! is_array( $joins ) ) {
			return;
		}

		if ( empty( $joins ) ) {
			delete_post_meta( $view_id, View::KEY_FORM_JOINS );
		} else {
			update_post_meta( $view_id, View::KEY_FORM_JOINS, $joins );
		}
	}
}
