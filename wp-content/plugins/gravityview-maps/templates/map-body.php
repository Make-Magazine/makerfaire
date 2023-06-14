<?php
/**
 * Display multiple entries as a map
 *
 * @package GravityView
 *
 * @global GravityView_View $this
 */

// There are no entries.
if ( ! $this->getTotalEntries() ) {

	?>
	<div class="gv-map-view gv-no-results">
		<div class="gv-map-view-title">
			<h3><?php echo gv_no_results(); ?></h3>
		</div>
	</div>
	<?php
} else {

	/**
	 * Before map template list of entries
	 *
	 * @param GravityView_View $this The GravityView_View instance
	 */
	do_action( 'gravityview_map_body_before', $this );

	?>
	<div class="<?php echo gv_container_class( 'gv-map-entries', false ); ?>">

		<?php
		// There are entries. Loop through them.
		foreach ( $this->getEntries() as $entry ) :

			$this->setCurrentEntry( $entry );

			/**
			 * @param CSS              $map_css_class Class for the map container. Default: `gv-map-view`
			 * @param array            $entry         Gravity Forms entry being currently displayed
			 * @param GravityView_View $this          Template class
			 */
			$map_css_class = apply_filters( 'gravityview_entry_class', 'gv-map-view', $entry, $this );
			?>

			<div id="gv_map_<?php echo $entry['id']; ?>" class="<?php echo esc_attr( $map_css_class ); ?>">

				<?php
				/**
				 * Tap in before the entry has been displayed, but after the container is open
				 *
				 * @param array            $entry Gravity Forms Entry array
				 * @param GravityView_View $this  The GravityView_View instance
				 */
				do_action( 'gravityview_entry_before', $entry, $this ); ?>

				<div class="gv-grid gv-map-view-main-attr">

					<?php // image zone
					$this->renderZone( 'image', [
							'wrapper_class' => 'gv-grid-col-1-3 gv-map-view-image',
							'markup'        => '<span class="{{class}}">{{label}}{{value}}</span>',
					] ); ?>


					<div class="gv-grid-col-1-3 gv-map-view-title">

						<?php
						/**
						 * Tap in before the title block
						 *
						 * @param array            $entry Gravity Forms Entry array
						 * @param GravityView_View $this  The GravityView_View instance
						 */
						do_action( 'gravityview_entry_title_before', $entry, $this ); ?>

						<?php if ( $this->getField( 'directory_map-title' ) ):
							$i          = 0;
							$title_args = [
									'entry'      => $entry,
									'form'       => $this->getForm(),
									'hide_empty' => $this->getAtts( 'hide_empty' ),
							];

							foreach ( $this->getField( 'directory_map-title' ) as $field ) :
								$title_args['field'] = $field;

								// The first field in the title zone is the main
								if ( $i == 0 ) {
									$title_args['markup'] = '<h3 class="{{class}}">{{label}}{{value}}</h3>';
									echo gravityview_field_output( $title_args );
									unset( $title_args['markup'] );
								} else {
									$title_args['wpautop'] = true;
									echo gravityview_field_output( $title_args );
								}

								$i ++;
							endforeach;
						endif; ?>

						<?php

						/**
						 * Tap in after the title block
						 *
						 * @param array            $entry Gravity Forms Entry array
						 * @param GravityView_View $this  The GravityView_View instance
						 */
						do_action( 'gravityview_entry_title_after', $entry, $this );

						?>

					</div>

					<?php
					$this->renderZone( 'details', [
							'wrapper_class' => 'gv-grid-col-1-3 gv-map-view-details',
							'markup'        => '<p class="{{class}}">{{label}}{{value}}</p>',
					] );
					?>

				</div>
				<div class="gv-grid gv-map-view-middle-container">
					<?php

					$this->renderZone( 'middle', [
							'wrapper_class' => 'gv-grid-col-1-1 gv-map-view-middle',
							'markup'        => '<div class="{{class}}">{{label}}{{value}}</div>',
					] );

					?>
				</div>
				<div class="gv-grid gv-map-view-footer">
					<?php
					/**
					 * Tap in before the footer wrapper
					 *
					 * @param array            $entry Gravity Forms Entry array
					 * @param GravityView_View $this  The GravityView_View instance
					 */
					do_action( 'gravityview_entry_footer_before', $entry, $this );

					$this->renderZone( 'footer', [
							'wrapper_class' => 'gv-grid-col-1-1 gv-map-view-footer',
							'markup'        => '<div class="{{class}}">{{label}}{{value}}</div>',
					] );

					/**
					 * Tap in after the footer wrapper
					 *
					 * @param array            $entry Gravity Forms Entry array
					 * @param GravityView_View $this  The GravityView_View instance
					 */
					do_action( 'gravityview_entry_footer_after', $entry, $this );

					?>
				</div>


				<?php

				/**
				 * Tap in after the entry has been displayed, but before the container is closed
				 *
				 * @param array            $entry Gravity Forms Entry array
				 * @param GravityView_View $this  The GravityView_View instance
				 */
				do_action( 'gravityview_entry_after', $entry, $this );

				?>

			</div>
		<?php endforeach; ?>
	</div>

	<?php
	/**
	 * After map template list of entries
	 *
	 * @param GravityView_View $this The GravityView_View instance
	 */
	do_action( 'gravityview_map_body_after', $this );
} // End if has entries