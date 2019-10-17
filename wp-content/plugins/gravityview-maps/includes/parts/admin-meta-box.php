<?php
/**
 * The template part for GravityView Maps meta box in edit View screen.
 *
 * @package GravityView_Maps
 * @since 0.1.1
 *
 * @global array $ms
 * @global string $address_fields_input
 */

global $ms, $address_fields_input;
?>

<table class="form-table striped">

	<?php

	GravityView_Render_Settings::render_setting_row( 'map_address_field', $ms, $address_fields_input, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );

	// Maps Marker icon ?>
	<tr valign="top">
		<td scope="row">
			<label for="gv_maps_se_map_marker_icon"><?php esc_html_e( 'Pin Icon', 'gravityview-maps' ); ?></label>
		</td>
		<td>
			<img src="<?php echo esc_url( $ms['map_marker_icon'] ); ?>" height="28">
			<input name="gv_maps_settings[map_marker_icon]" id="gv_maps_se_map_marker_icon" type="hidden" value="<?php echo esc_attr( $ms['map_marker_icon'] ); ?>">
			<a id="gv_maps_se_select_icon" class="button-secondary" title="<?php esc_attr_e( 'Select Icon', 'gravityview-maps' ); ?>"><?php esc_html_e( 'Select Icon', 'gravityview-maps' ); ?></a>
			<a id="gv_maps_se_add_icon" class="button-secondary" title="<?php esc_attr_e( 'Upload Custom Icon', 'gravityview-maps' ); ?>"><?php esc_html_e( 'Add Icon', 'gravityview-maps' ); ?></a>
		</td>
	</tr>

	<?php

	$settings = array(
		'map_canvas_position',
		'map_canvas_sticky',
		'map_type',
	);

	foreach( $settings as $setting ) {
		GravityView_Render_Settings::render_setting_row( $setting, $ms, null, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );
	}

	?>

	<tr>
		<td>
			<strong><?php esc_html_e( 'Info Box Settings', 'gravityview-maps' ); ?></strong>
		</td>
		<td>
			<?php printf( '<img src="%s" width="307" height="156" alt="%s" />', plugins_url( '/assets/img/admin/infobox-example.png', $this->loader->_path ), esc_attr__('Example Info Box', 'gravityview-maps') ); ?>
		</td>
	</tr>

	<?php
	// Infowindow settings
	$settings = array(
		'map_info_enable',
		'map_info_title',
		'map_info_content',
		'map_info_image',
		'map_info_image_align',
	);

	foreach( $settings as $setting ) {
		GravityView_Render_Settings::render_setting_row( $setting, $ms, null, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );
	}
	?>

	<tr valign="top">
		<td colspan="2">
			<strong><?php esc_html_e( 'Advanced Settings', 'gravityview-maps' ); ?></strong>
		</td>
	</tr>

	<?php

	// Map Layers (Traffic, Transit, Bicycle)
		$settings = array(
			'map_layers',
			'map_zoom',
			'map_maxzoom',
			'map_minzoom',
			'map_zoom_control',
			'map_draggable',
			'map_doubleclick_zoom',
			'map_scrollwheel_zoom',
			'map_pan_control',
			'map_streetview_control',
			'map_styles',
		);

		foreach( $settings as $setting ) {
			GravityView_Render_Settings::render_setting_row( $setting, $ms, null, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );
		}

	?>
</table>

<div id="gv_maps_se_available_icons" class="hide-if-js gv-tooltip">
	<span class="close"><i class="dashicons dashicons-dismiss"></i></span>
	<?php do_action( 'gravityview/maps/render/available_icons', 'available-icons' ); ?>
</div>