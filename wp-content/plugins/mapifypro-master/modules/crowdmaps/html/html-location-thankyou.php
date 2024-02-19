<div class="mpf-p-popup-holder">
	<div class="mpfy-p-popup-background"></div>
	<section class="mpfy-p-popup mpfy-p-popup-style-two crowd-popup crowd-popup-thank-you">
		<div class="mpfy-p-holder mpfy-p-color-popup-background">
			<div class="mpfy-p-top">
				<h1><?php echo $title; ?></h1>
				<a href="#" class="mpfy-p-close"></a>
			</div>

			<div class="mpfy-p-bottom">
				<div class="mpfy-p-scroll">
					<div class="crowd-popup-body">
						<?php if ( $notification ) : ?>
							<div class="crowd-popup-notification">
								<?php echo wp_kses_post( $notification ); ?>
							</div>
						<?php endif; ?>

						<?php echo wpautop( $content ); ?>
						<div class="crowd-form-actions">
							<input type="button" class="crowd-btn-submit crowd-btn-close" value="<?php echo esc_attr( __( 'Close This Pop-up', 'crowd' ) ); ?>" name="submit" />
						</div><!-- /.crowd-form-actions -->
					</div><!-- /.crowd-popup-body -->
				</div>
			</div>
		</div>
	</section>
</div>