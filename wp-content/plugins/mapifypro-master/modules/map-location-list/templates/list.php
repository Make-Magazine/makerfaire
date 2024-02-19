<?php
$map_id = $map->get_id();
$label_more_details = mpfy_meta_label( $map_id, '_map_label_interactive_list_open_popup_button', 'More Details' );
$label_get_directions = mpfy_meta_label( $map_id, '_map_label_interactive_list_directions_button', 'Get Directions' );
?>
<div class="mpfy-mll" data-hide-in-default-view="<?php echo esc_attr( json_encode( $hide_in_default_view ) ); ?>" style="display: none;">
	<div class="mpfy-mll-list">
		<?php for ($i=0; $i < count($locations); $i++) : ?>
			<?php
			$l = $locations[$i];
			$include_location = mpfy_meta_to_bool($l->get_id(), '_map_location_mll_include', true);
			if (!$include_location) {
				continue;
			}

			$tags = $l->get_tags();
			$pin = $l->get_pin_image( $map_id );
			$pin = ($pin['url'] ? $pin['url'] : plugins_url('assets/images/google-pin.png', MAPIFY_PLUGIN_FILE));
			$formatted_address = $l->get_formatted_address( $map_id );
			?>
			<div class="mpfy-mll-location" data-id="<?php echo $l->get_id(); ?>" data-order="<?php echo $i; ?>">
				<div class="mpfy-mll-l-heading">
					<div class="mpfy-mll-l-pin" style="background-image: url(<?php echo $pin; ?>);"></div>
					<div class="mpfy-mll-l-title">
						<span>
							<?php echo $l->get_title(); ?>
						</span>

						<?php if ($tags) : ?>
							<span class="mpfy-mll-l-categories">
								<?php foreach ($tags as $t) : ?>
									<a href="#" data-mapify-map-id="<?php echo esc_attr( $map_id ); ?>" data-mapify-action="setMapTag" data-mapify-value="<?php echo $t->term_id; ?>"><?php echo esc_attr($t->name); ?></a>
								<?php endforeach; ?>
							</span>
						<?php endif; ?>
					</div>
				</div>
				<div class="mpfy-mll-l-content">
					<?php if ($formatted_address) : ?>
						<p><strong><?php echo $formatted_address; ?></strong></p>
					<?php endif; ?>
					<?php echo wpautop(get_post_meta($l->get_id(), '_map_location_mll_description', true)); ?>

					<div class="mpfy-mll-l-buttons">
						<?php if ($l->get_popup_enabled()) : ?>
							<a href="#" data-mapify-action="openPopup" data-mapify-value="<?php echo $l->get_id(); ?>"><?php echo $label_more_details; ?></a>
						<?php endif; ?>

						<?php if ( $map->get_mode() === 'map' && $l->get_directions_enabled() ) : ?>
							<a href="<?php echo $l->get_directions_url(); ?>" target="_blank"><?php echo $label_get_directions; ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endfor; ?>
	</div>
	<div class="mpfy-mll-pagination" data-number="<?php echo $number_of_locations; ?>">
		<a href="#" class="mpfy-mll-button mpfy-mll-button-prev"><?php echo esc_html(__('Previous', 'mpfy')); ?></a>
		<a href="#" class="mpfy-mll-button mpfy-mll-button-next" style="float: right;"><?php echo esc_html(__('Next', 'mpfy')); ?></a>
		<div class="mpfy-mll-pagination-status"><?php echo sprintf(__('Page %1$s of %2$s', 'mpfy'), '<span class="mpfy-mll-pagination-current-page">1</span>', '<span class="mpfy-mll-pagination-max-page">1</span>'); ?></div>
	</div>
	<div class="cl">&nbsp;</div>
</div>