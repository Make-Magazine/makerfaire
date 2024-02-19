<style type="text/css">
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?>,
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?> p,
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?> p strong,
	.mpfy-tooltip.mpfy-tooltip-map-<?php echo $map_id; ?> .mpfy-tooptip-actions a { color: <?php echo $tooltip_text_color; ?>; }
	.mpfy-map-id-<?php echo $map_id; ?> .mpfy-mll-location .mpfy-mll-l-buttons a:first-child {
		background-color: <?php echo $open_popup_bgcolor; ?>;
		color: <?php echo $open_popup_color; ?>;
	}
	.mpfy-map-id-<?php echo $map_id; ?> .mpfy-mll-location .mpfy-mll-l-buttons a + a {
		background-color: <?php echo $get_directions_bgcolor; ?>;
		color: <?php echo $get_directions_color; ?>;
	}
	.mpfy-map-id-<?php echo $map_id; ?> .mpfy-mll-location .mpfy-mll-l-buttons a:first-child:hover,
	.mpfy-map-id-<?php echo $map_id; ?> .mpfy-mll-location .mpfy-mll-l-buttons a + a:hover {
		background-color: <?php echo $button_hover_bgcolor; ?>;
		color: <?php echo $button_hover_color; ?>;
	}
</style>

<div id="mpfy-map-<?php echo $mpfy_instances; ?>" class="<?php echo implode( ' ', $wrap_classes ); ?>" data-attrs="<?php echo esc_attr( json_encode( $map_attrs_data ) ) ?>" data-proprietary="<?php echo esc_attr( json_encode( $map_proprietary_data ) ) ?>">
	
	<?php if ( $errors ) : ?>

	<p>
		<?php 
		foreach ( $errors as $e ) : 
			printf( "%s<br />", $e );
		endforeach; 
		?>
	</p>

	<?php else : ?>

	<div class="mpfy-controls-wrap">
		<div class="mpfy-controls <?php echo implode( ' ', $controls_classes ); ?>">
			<form class="mpfy-search-form" method="post" action="" autocomplete="off" style="<?php echo ( ! $search_enabled ) ? 'display: none;' : ''; ?>">
				<div class="mpfy-search-wrap">

					<?php do_action('mpfy_template_before_search_field', $map->get_id()); ?>	

					<div class="mpfy-search-field">

						<?php if ( $enable_search_radius ) : ?>
							<div class="mpfy-after-search-radius-label"><?php _e( 'of', 'mpfy' ); ?></div>
						<?php endif; ?>

						<div class="mpfy-search-field-queries">
							<a href="javascript:;" class="mpfy-search-field-dropdown-toggle"></a>
							<div class="mpfy-search-field-multiple-label">(<?php _e( 'Multiple', 'mpfy' ) ?>)</div>
							<div class="mpfy-search-field-item">
								<input type="search" class="mpfy-search-input" autocomplete="off" value="" placeholder="<?php echo esc_attr( $label_search_first ); ?>" />
								<a href="javascript:;" class="mpfy-search-field-add-item">+</a>
							</div>
						</div>

						<div class="mpfy-reset-search-button" aria-label="<?php esc_attr_e( 'Reset the search', 'mpfy' ) ?>" data-microtip-position="bottom-left" role="tooltip"></div>
						<div class="mpfy-search-button" aria-label="<?php esc_attr_e( 'Please insert your desired search keywords (minimum 3 characters) to begin the search', 'mpfy' ) ?>" data-microtip-position="bottom-left" data-microtip-size="large" role="tooltip"></div>
					</div>					
					
				</div>
			</form>

			<div class="mpfy-filter mpfy-selecter-wrap" style="<?php echo ( ! $filters_enabled || ! $map_tags ) ? 'display: none;' : ''; ?>">
				<select name="mpfy_tag" class="mpfy-tag-select">
					<option value="0"><?php echo $label_filter_dropdown_default_view; ?></option>

					<?php foreach ( $map_tags as $t ) : ?>
						<option value="<?php echo $t->term_id; ?>"><?php echo $t->name; ?></option>					
					<?php endforeach; ?>
				
				</select>
			</div>
		</div>
	</div>

	<div class="mpfy-map-canvas-shell-outer mpfy-mode-<?php echo $mode ?> <?php echo ( $map_tags || $search_enabled ) ? 'with-controls' : ''; ?>">
		<div style="display: none;">
		
			<?php 			
			foreach ( $pins as $p ) :
				$settings = array(
					'href'    => '#',
					'classes' => array( 'mpfy-pin', 'mpfy-pin-id-' . $p['id'], 'no_link' ),
					'target'  => '_self',
				);

				if ( $p['popupEnabled'] ) {
					$settings['href'] = add_query_arg( 'mpfy_map', $map->get_id(), get_permalink( $p['id'] ) );
				}

				$settings = apply_filters( 'mpfy_pin_trigger_settings', $settings, $p['id'] );
				
				?>

				<a
					target             = "<?php echo esc_attr( $settings['target'] ); ?>"
					href               = "<?php echo esc_attr( $settings['href'] ); ?>"
					class              = "<?php echo esc_attr( implode( ' ', $settings['classes'] ) ); ?>"
					data-id            = "<?php echo esc_attr( $p['id'] ); ?>"
					data-mapify-action = "openPopup"
					data-mapify-value  = "<?php echo esc_attr( $p['id'] ); ?>">
					&nbsp;
				</a>

				<?php 
			endforeach;
			?>

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

		<?php if ( $filters_list_enabled ) : ?>

			<div class="mpfy-tags-list">
				<div class="cl">&nbsp;</div>
				<a href="#" class="mpfy-tl-item tag-all" data-mapify-map-id="<?php echo esc_attr( $map_id ); ?>" data-mapify-action="toggleMapTag" data-mapify-value="0">
					<span class="mpfy-tl-i-icon">
						<span class="mpfy-tl-i-icon-default"></span>
					</span>

					<em>
						<?php echo $label_filter_list_default_view; ?>
					</em>
				</a>

				<?php 
				foreach ( $map_tags as $t ) : 				
					$image = wp_get_attachment_image_src( mpfy_carbon_get_term_meta( $t->term_id, 'mpfy_location_tag_image' ), 'mpfy_location_tag' );
					?>

					<a href="#" class="mpfy-tl-item" data-mapify-map-id="<?php echo esc_attr( $map_id ); ?>" data-mapify-action="toggleMapTag" data-mapify-value="<?php echo esc_attr( $t->term_id ); ?>">
						
						<?php if ( $image ) : ?>

							<span class="mpfy-tl-i-icon" style="background: none;">
								<?php echo wp_get_attachment_image( mpfy_carbon_get_term_meta( $t->term_id, 'mpfy_location_tag_image' ), 'mpfy_location_tag' ); ?>
							</span>

						<?php endif; ?>

						<em>
							<?php echo $t->name; ?>
						</em>
					</a>
			
					<?php 
				endforeach; 
				?>

				<div class="cl">&nbsp;</div>
			</div>

		<?php endif; ?>

		<?php do_action( 'mpfy_template_after_map', $map->get_id() ); ?>
	</div>

	<?php endif; ?>

</div>
