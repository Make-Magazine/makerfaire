<?php

add_action( 'mpfy_popup_location_information', 'mpfy_pld_display_info_block', 10, 2 );
function mpfy_pld_display_info_block( $location_id, $map_id ) {
	$map = new Mpfy_Map( $map_id );
	$map_location = new Mpfy_Map_Location( $location_id );

	$map_mode = $map->get_mode();

	$popup_location_information = mpfy_meta_to_bool( get_the_ID(), '_map_location_popup_location_information', true );
	if ( !$popup_location_information ) {
		return;
	}
	
	$address_lines_formatted = $map_location->get_formatted_address( $map_id );
	$links = mpfy_carbon_get_post_meta( get_the_ID(), 'map_location_links', 'complex' );

	$tags = $map_location->get_tags();
	$location_details_label = mpfy_meta_label( $map_id, '_map_label_location_details', 'Location Details' );

	// Ignore if there is no content to show.
	if ( !$address_lines_formatted && !$links && !$tags ) {
		return;
	}

	?>
	<aside class="mpfy-p-widget mpfy-p-widget-location">
		<div class="mpfy-p-holder">
			<h5 class="mpfy-p-widget-title"><?php echo $location_details_label; ?></h5>

			<div class="mpfy-location-details">
				<?php if ( $address_lines_formatted ) : ?>
					<div class="mpfy-p-entry">
						<p><?php echo $address_lines_formatted; ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $links ) : ?>
					<div class="mpfy-p-links">
						<ul>
							<?php foreach ( $links as $o ) : ?>
								<li>
									<a href="<?php echo esc_attr( $o['url'] ); ?>" target="<?php echo $o['target']; ?>" class=" mpfy-p-color-accent-color"><?php echo esc_attr( $o['text'] ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $tags ) : ?>
					<div class="mpfy-p-tags">
						<?php foreach ( $tags as $t ) : ?>
							<a href="#" data-mapify-map-id="<?php echo esc_attr( $map->get_id() ); ?>" data-mapify-action="setMapTag" data-mapify-value="<?php echo $t->term_id; ?>"><?php echo esc_attr( $t->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div><!-- /.mpfy-location-details -->
		</div>
		<div class="cl">&nbsp;</div>
	</aside>
	<?php
}
