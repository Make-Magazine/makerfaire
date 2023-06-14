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

global $ms, $address_fields_input, $choice_marker_icon_field_input;
?>

<table class="form-table striped">

	<?php

	GravityView_Render_Settings::render_setting_row( 'map_address_field', $ms, $address_fields_input, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );

	// Maps Marker icon ?>
	<tr>
		<th scope="row">
			<label for="gv_maps_se_map_marker_icon"><?php esc_html_e( 'Default Pin Icon', 'gk-gravitymaps' ); ?></label>
		</th>
		<td>
			<img src="<?php echo esc_url( $ms['map_marker_icon'] ); ?>" height="28" alt="<?php esc_attr_e( 'Default Pin Icon', 'gk-gravitymaps' ); ?>" style="margin: 0 10px;">
			<input name="gv_maps_settings[map_marker_icon]" id="gv_maps_se_map_marker_icon" type="hidden" value="<?php echo esc_attr( $ms['map_marker_icon'] ); ?>">
			<a id="gv_maps_se_select_icon" class="button button-secondary" title="<?php esc_attr_e( 'Select Icon', 'gk-gravitymaps' ); ?>"><?php esc_html_e( 'Select Icon', 'gk-gravitymaps' ); ?></a>
			<a id="gv_maps_se_add_icon" class="button button-secondary" title="<?php esc_attr_e( 'Upload Custom Icon', 'gk-gravitymaps' ); ?>"><?php esc_html_e( 'Add Icon', 'gk-gravitymaps' ); ?></a>
			<p><span class="howto"><?php echo strtr( esc_html__( 'This is the default icon. Entries that have a Map Icon field or a choice-based custom marker will override this value. {link}Learn more about custom icons{/link}.', 'gk-gravitymaps' ), array(
				'{link}'   => '<a href="https://docs.gravitykit.com/article/828-a" target="_blank" rel="noopener noreferrer" class="gk-link" data-beacon-article-modal="61e9ea8c39e5d05141b617f5">',
				'{/link}'  => '<span class="screen-reader-text"> ' . esc_html__( '(This link opens in a new window.)', 'gk-gravitymaps' ) . '</span></a>',
			) ); ?></span></p>
		</td>
	</tr>

	<?php

	GravityView_Render_Settings::render_setting_row( 'choice_marker_icon_field', $ms, $choice_marker_icon_field_input, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );

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
		<th>
			<h3><?php esc_html_e( 'Info Box Settings', 'gk-gravitymaps' ); ?></h3>
		</th>
		<td>
			<?php printf( '<img src="%s" width="307" height="156" alt="%s" />', plugins_url( '/assets/img/admin/infobox-example.png', $this->loader->path ), esc_attr__('Example Info Box', 'gk-gravitymaps') ); ?>
		</td>
	</tr>

	<?php
	// Info window settings
	$settings = array(
		'map_info_enable',
		'map_info_title',
		'map_info_title_link',
		'map_info_content',
		'map_info_image',
		'map_info_image_align',
	);

	foreach( $settings as $setting ) {
		GravityView_Render_Settings::render_setting_row( $setting, $ms, null, 'gv_maps_settings[%s]', 'gv_maps_se_%s' );
	}
	?>

	<tr>
		<th colspan="2">
			<h3><?php esc_html_e( 'Advanced Settings', 'gk-gravitymaps' ); ?></h3>
		</th>
	</tr>

	<?php

	// Map Layers (Traffic, Transit, Bicycle)
		$settings = array(
			'map_layers',
			'map_default_radius_search',
			'map_default_radius_search_unit',
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
			'map_marker_clustering',
			'map_marker_clustering_maxzoom',
			'map_international_autocomplete_filter',
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
