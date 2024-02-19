<?php
$has_controls = ( ($filters_enabled && $map_tags) || $search_enabled );
$controls_classes = array();
if (($filters_enabled && $map_tags) && $search_enabled) {
	$controls_classes[] = 'mpfy-controls-all';
}
if (!$has_controls) {
	$controls_classes[] = 'mpfy-hidden';
}
if (!$search_enabled) {
	$controls_classes[] = 'mpfy-without-search';
}

if (!$filters_enabled || !$map_tags) {
	$controls_classes[] = 'mpfy-without-dropdown';
}

$wrap_classes = array('mpfy-container', 'mpfy-map-id-' . $map_id);
$wrap_classes = apply_filters('mpfy_map_wrap_classes', $wrap_classes, $map_id);
$map_proprietary_data = apply_filters('mpfy_map_proprietary_data', array(), $map_id);

$canvas_style = array('overflow: hidden');
if ($width !== 0) {
	$canvas_style[] = 'width: ' . $width;
}
if ($height !== 0) {
	$canvas_style[] = 'height: ' . $height;
}
$canvas_style = implode( '; ', $canvas_style );
$label_filter_dropdown_default_view = mpfy_meta_label( $map->get_id(), '_map_label_filter_dropdown', 'Default View' );
$label_filter_list_default_view = mpfy_meta_label( $map->get_id(), '_map_label_filter_list', 'Default View' );
$label_search = mpfy_meta_label( $map->get_id(), '_map_label_search', 'Search city or zip code' );
?>
<style type="text/css">
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?>,
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?> p,
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?> p strong,
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?> .mpfy-tooptip-actions a { color: <?php echo $tooltip_text_color; ?>; }
</style>
<div id="mpfy-map-<?php echo $mpfy_instances; ?>" class="<?php echo implode(' ', $wrap_classes); ?>" data-proprietary="<?php echo esc_attr(json_encode($map_proprietary_data)) ?>">
	<?php if ($errors) : ?>
		<p>
			<?php foreach ($errors as $e) : ?>
				<?php echo $e; ?><br />
			<?php endforeach; ?>
		</p>
	<?php else : ?>

		<div class="mpfy-controls-wrap">
			<div class="mpfy-controls <?php echo implode(' ', $controls_classes); ?>">
				<form class="mpfy-search-form" method="post" action="" style="<?php echo (!$search_enabled) ? 'display: none;' : ''; ?>">
					<div class="mpfy-search-wrap">
						<div class="mpfy-search-field">
							<input type="text" name="mpfy_search_query" class="mpfy-search-input" value="" placeholder="<?php echo esc_attr( $label_search ); ?>" />
							<a href="#" class="mpfy-search-clear">&nbsp;</a>
						</div>
						<?php do_action('mpfy_template_after_search_field', $map->get_id()); ?>
						<input type="submit" name="" value="<?php echo esc_attr(__('Search', 'mpfy')); ?>" class="mpfy-search-button" />
					</div>
				</form>

				<div class="mpfy-filter mpfy-selecter-wrap" style="<?php echo (!$filters_enabled || !$map_tags) ? 'display: none;' : ''; ?>">
					<select name="mpfy_tag" class="mpfy-tag-select">
						<option value="0"><?php echo $label_filter_dropdown_default_view; ?></option>
						<?php foreach ($map_tags as $t) : ?>
							<option value="<?php echo $t->term_id; ?>"><?php echo $t->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

		</div>

		<div class="mpfy-map-canvas-shell-outer mpfy-mode-<?php echo $mode ?> <?php echo ($map_tags || $search_enabled) ? 'with-controls' : ''; ?>">
			<div style="display: none;">
				<?php foreach ($pins as $p) : ?>
					<?php
					$settings = array(
						'href' => '#',
						'classes' => array( 'mpfy-pin', 'mpfy-pin-id-' . $p['id'], 'no_link' ),
					);
					if ( $p['popupEnabled'] ) {
						$settings['href'] = add_query_arg( 'mpfy_map', $map->get_id(), get_permalink( $p['id'] ) );
					}
					$settings = apply_filters( 'mpfy_pin_trigger_settings', $settings, $p['id'] );
					$target   = isset( $settings['target'] ) ? $settings['target'] : '_self';
					?>
					<a
						target="<?php echo esc_attr($target); ?>"
						href="<?php echo esc_attr($settings['href']); ?>"
						data-id="<?php echo esc_attr( $p['id'] ); ?>"
						class="<?php echo esc_attr(implode(' ', $settings['classes'])); ?>"
						data-mapify-action="openPopup"
						data-mapify-value="<?php echo esc_attr( $p['id'] ); ?>">
						&nbsp;
					</a>
				<?php endforeach; ?>

			</div>

			<div class="mpfy-map-canvas-shell">
				<div id="mpfy-canvas-<?php echo $mpfy_instances; ?>" class="mpfy-map-canvas" style="<?php echo esc_attr( $canvas_style ); ?>"></div>

				<div class="mpfy-map-controls">
					<?php if ( $map_enable_use_my_location ) : ?>
						<div class="mpfy-map-current-location">
							<div class="mpfy-map-current-location-tooptip">
								<p><?php _e( 'Show My Location', 'mpfy' ); ?></p>
							</div><!-- /.mpfy-map-current-location-tooptip -->

							<a href="#"class="mpfy-map-current-location-icon mpfy-geolocate"></a>
						</div><!-- /.mpfy-map-current-location -->
					<?php endif; ?>

					<?php if ( $manual_zoom_enabled ) : ?>
						<div class="mpfy-zoom">
							<a href="#" class="mpfy-zoom-in"></a>
							<a href="#" class="mpfy-zoom-out"></a>
						</div><!-- /.mpfy-zoom -->
					<?php endif; ?>
				</div><!-- /.mpfy-map-controls -->
			</div>

			<?php do_action('mpfy_template_after_map', $map->get_id()); ?>
		</div>
	<?php endif; ?>
</div>
