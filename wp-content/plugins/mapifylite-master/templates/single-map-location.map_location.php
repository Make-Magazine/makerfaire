<?php
$post = get_post(get_the_ID());
$popup_directions = mpfy_meta_to_bool(get_the_ID(), '_map_location_popup_directions', true);
$popup_location_information = mpfy_meta_to_bool(get_the_ID(), '_map_location_popup_location_information', true);
$has_content = ($post->post_content != '' || $popup_directions || $popup_location_information);
?>
<div class="mpf-p-popup-holder">
	<?php do_action('mpfy_popup_before_section', get_the_ID(), $map_id); ?>

	<div class="mpfy-p-popup-background"></div>
	<section class="<?php echo implode(' ', $classes); ?>">
		<div class="mpfy-p-holder mpfy-p-color-popup-background">
			<div class="mpfy-p-bottom">
				<?php do_action('mpfy_popup_before_content_layout', get_the_ID()); ?>
				
				<a href="#" class="mpfy-p-close <?php echo $has_content ? '' : 'has-no-content'; ?>"></a>

				<?php if ( $media_count > 0 ) : ?>
					<div class="mpfy-p-slider mpfy-sliders-container <?php echo $has_content ? '' : 'mpfy-p-full-width-slider'; ?>">
						<div class="mpfy-main-slider">
							<?php if ( $video_embed_code ) : ?>
								<div class="holder video-holder">
									<?php echo Mpfy_Carbon_Video::create( $video_embed_code )->get_embed_code( '100%', 624 ); ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $images ) ) : foreach ( $images as $image ) : ?>
								<div class="holder" >
									<div style="background-image: url('<?php echo esc_url( $image['image'] ); ?>')"></div>
								</div>
							<?php endforeach; endif; ?>
						</div>

						<div class="mpfy-navigation-slider">
							<?php if ( $video_embed_code ) : ?>
								<div class="holder video-holder slick-current-slide">
									<img src="<?php echo mpfy_get_thumb( $video_thumb, 200, 200 ); ?>" alt="" />
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $images ) ) : foreach ( $images as $image ) : ?>
								<div class="holder <?php echo $video_embed_code ? 'slick-current-slide' : '' ?>">
									<img src="<?php echo mpfy_get_thumb( $image['image'], 200, 200 ); ?>" alt="" />
								</div>
							<?php endforeach; endif; ?>
						</div>
					</div>					
				<?php endif; ?>
		
				<?php if ( $has_content ) : ?>
					<div class="mpfy-p-content">		
						<div class="mpfy-p-local-info">
							<?php do_action('mpfy_popup_location_information', get_the_ID(), $map_id); ?>
						</div><!-- /.mpfy-p-local-info -->

						<div class="mpfy-p-scroll">
							<div class="mpfy-p-holder">
								<div class="cl">&nbsp;</div>

								<div class="mpfy-title">
									<h1><?php the_title(); ?></h1>
								</div>

								<div class="mpfy-p-entry">
									<?php do_action('mpfy_popup_content_before', get_the_ID()); ?>
									<?php the_content(); ?>
									<?php do_action('mpfy_popup_content_after', get_the_ID()); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
</div>