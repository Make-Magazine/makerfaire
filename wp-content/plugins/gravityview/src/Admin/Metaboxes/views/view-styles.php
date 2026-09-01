<?php
/**
 * Styles tab for the View admin metabox.
 *
 * Renders the per-View "Styles" home: a plain-language intro to Themes
 * and the theme selector.
 *
 * @package    GravityKit\GravityView
 * @subpackage Gravityview/admin/metaboxes/views
 * @global $post
 *
 * @since 3.0.0
 */

global $post;

$current_settings = gravityview_get_template_settings( $post->ID );

// Resolve the theme selection from the raw saved meta, because
// gravityview_get_template_settings() merges runtime defaults into it.
// An existing View only changes look when someone opts it in: when the View
// has saved settings without the `theme` key, it stays on Legacy. A
// brand-new View (no saved settings meta yet) inherits the global
// Appearance default.
$saved_settings = get_post_meta( $post->ID, '_gravityview_template_settings', true );

if ( ! is_array( $saved_settings ) || ! isset( $saved_settings['theme'] ) ) {
	$current_settings['theme'] = is_array( $saved_settings )
		? 'legacy'
		: (string) gravityview()->plugin->settings->get( 'theme', 'legacy' );
}

?>

<div class="gv-styles-tab">

	<div class="gv-styles-tab__header">
		<h3 class="gv-styles-tab__title"><?php esc_html_e( 'Styles', 'gk-gravityview' ); ?></h3>
		<p class="gv-styles-tab__description">
        <?php
			esc_html_e( 'Themes give this View a refreshed visual design that works with every View layout. Choose a theme below.', 'gk-gravityview' );
		?>
        </p>
	</div>

	<div class="gv-styles-tab__panel">

		<?php // The React theme + column pickers mount here. With JS off, the native controls below remain. ?>
		<div id="gv-theme-picker-mount" class="gv-theme-picker-mount"></div>

		<table role="presentation" class="form-table gv-styles-tab__enable gv-theme-picker-hide">
			<?php GravityView_Render_Settings::render_setting_row( 'theme', $current_settings ); ?>
		</table>
		<table role="presentation" class="form-table gv-styles-tab__settings">
			<tbody class="gv-theme-picker-hide">
				<?php GravityView_Render_Settings::render_setting_row( 'grid_columns', $current_settings ); ?>
			</tbody>
			<tbody>
				<?php GravityView_Render_Settings::render_setting_row( 'card_equal_height', $current_settings ); ?>
			</tbody>
		</table>
	</div>

</div>
