<?php gravityview_before(); ?>

<div class="gv-container gv-map-single-container gv-map-container">

	<p class="gv-back-link"><?php echo gravityview_back_link(); ?></p>

	<?php foreach( $this->getEntries() as $entry ) :

		$this->setCurrentEntry( $entry );
		?>

		<div id="gv_map_<?php echo $entry['id']; ?>" class="gv-map-view">

			<?php if( !empty(  $this->fields['single_map-title'] ) || !empty(  $this->fields['single_map-subtitle'] ) ): ?>
				<div class="gv-map-view-title">

					<?php if( !empty(  $this->fields['single_map-title'] ) ):
						$i = 0;
						$title_args = array(
							'entry' => $entry,
							'form' => $this->form,
							'hide_empty' => $this->atts['hide_empty'],
						);
						foreach( $this->fields['single_map-title'] as $field ) :
							$title_args['field'] = $field;
							if( $i == 0 ) {
								$title_args['markup'] = '<h3 class="{{class}}">{{label}}{{value}}</h3>';
								echo gravityview_field_output( $title_args );
							} else {
								$title_args['wpautop'] = true;
								echo gravityview_field_output( $title_args );
							}
							$i++;
						endforeach;
					endif;

					$this->renderZone( 'subtitle', array(
						'wrapper_class' => 'gv-map-view-subtitle',
						'markup'     => '<h4 class="{{class}}">{{label}}{{value}}</h4>'
					));

					?>
				</div>
			<?php endif; ?>

			<div class="gv-grid gv-map-view-content">
				<?php

				$this->renderZone( 'image', array(
					'wrapper_class' => 'gv-grid-col-1-3 gv-map-view-image',
					'markup'     => '<h4 class="{{class}}">{{label}}{{value}}</h4>'
				));

				$this->renderZone( 'description', array(
					'wrapper_class' => 'gv-grid-col-2-3 gv-map-view-description',
					'label_markup' => '<h4>{{label}}</h4>',
					'wpautop' => true
				));


				?>
			</div>

			<?php if( !empty(  $this->fields['single_map-footer-left'] ) || !empty(  $this->fields['single_map-footer-right'] ) ): ?>

				<div class="gv-grid gv-map-view-footer">
					<div class="gv-grid-col-1-2 gv-left">
						<?php $this->renderZone( 'footer-left' ); ?>
					</div>

					<div class="gv-grid-col-1-2 gv-right">
						<?php $this->renderZone( 'footer-right' ); ?>
					</div>
				</div>

			<?php endif; ?>

		</div>

	<?php endforeach; ?>

</div>

<?php gravityview_after(); ?>
