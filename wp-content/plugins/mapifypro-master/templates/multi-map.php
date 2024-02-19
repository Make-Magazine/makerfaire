<div class="mpfy-multi-map" data-height="<?php echo esc_attr( intval( $height ) ) ?>">
	<div class="mpfy-multi-map-dropdown">
		<div class="mpfy-multi-map-dropdown-label" style="background-color: <?php echo esc_attr( $label_bg_color ) ?>">
			<p><?php echo esc_html( $label ) ?></p>
		</div>
	
		<div class="mpfy-multi-map-dropdown-list-wrapper">
			<span class="mpfy-multi-map-dropdown-list-current"></span>	
			
			<ul class="mpfy-multi-map-dropdown-list">

				<?php
				foreach ( $map_ids as $key => $map_id ) : 
					$map = new Mpfy_Map( $map_id );

					// must be a valid map
					if ( ! $map->get_id() ) continue;

					?>

					<li class="<?php echo ( 0 === $key ) ? esc_attr( 'current' ) : ''; ?>">
						<a href="#" data-target="<?php echo esc_attr( $map->get_id() ) ?>"><?php echo esc_html( $map->get_title() ); ?></a>
					</li>
					
					<?php 
				endforeach; 
				?>

			</ul>
		</div>
	</div>

	<?php
	foreach ( $map_ids as $key => $map_id ) : 
		$map = new Mpfy_Map( $map_id );

		// must be a valid map
		if ( ! $map->get_id() ) continue;

		?>

		<div class="mpfy-multi-map-item mpfy-<?php echo esc_attr( $map->get_id() ) ?> <?php echo ( 0 < $key ) ? esc_attr( 'd-none' ) : ''; ?>" >
			<?php 
			echo do_shortcode( sprintf( 
				'[custom-mapping height="%d" map_id="%d" ]',
				intval( $height ),
				intval( $map->get_id() )
			) ); ?>
		</div>
		
		<?php 
	endforeach; 
	?>

</div>