<div class="mpf-p-popup-holder">
	<div class="mpfy-p-popup-background"></div>
	<section class="mpfy-p-popup mpfy-p-popup-style-two crowd-popup">
		<div class="mpfy-p-holder mpfy-p-color-popup-background">
			<div class="mpfy-p-top">
				<h1><?php _e('Submit a New Map Location', 'crowd'); ?></h1>
				<a href="#" class="mpfy-p-close"></a>
			</div>

			<div class="mpfy-p-bottom">
				<div class="mpfy-p-scroll">
					<div class="crowd-popup-body">
						<div class="crowd-popup-notification">
							<?php 
							$current_post_id   = @$_REQUEST['current_post_id'];
							$current_permalink = get_permalink( $current_post_id );
							$login_url         = wp_logout_url(  wp_login_url( $current_permalink ) ); // make sure to log-out first

							if ( current_user_can( 'edit_map_locations' ) ) {
								$user = wp_get_current_user();													
								printf( 
									__( 'You are logged in as %s. Not you?, please %slogin to your account%s.', 'crowd' ), 
									'<b>' . esc_html( $user->display_name ) . '</b>', 
									'<a href="' . esc_url( $login_url ) . '" onclick="window.Mapify.closePopup()"><b>', 
									'</b></a>'
								);
							} else {
								printf( 
									__( 'Have an account?, please %slog in to your account%s before adding your map location.', 'crowd' ), 
									'<a href="' . esc_url( $login_url ) . '"><b>', 
									'</b></a>'
								);
							}
							?>
						</div>
						<div class="crowd-popup-error-notification"></div>

						<form action="<?php echo add_query_arg('action', 'crowd_add_location', admin_url('admin-ajax.php')); ?>" method="post" class="crowd-add-location-form">							
							<?php wp_nonce_field( 'ZFVr6pPwmsdhMUVvYc', '_crowd_ajax_nonce' ); ?>
							
							<input type="hidden" name="map_id" value="<?php echo @$_REQUEST['map_id'] ?>" />
							<input type="hidden" name="location_coords" value="<?php echo @$_REQUEST['location'] ?>" />

							<div class="crowd-fields">
								<div class="crowd-fields-column">
									<div class="crowd-field-wrap">
										<label><?php _e('Location Title', 'crowd'); ?>*</label>
										<input type="text" class="field" value="" name="location_title" />
									</div><!-- /.crowd-field-wrap -->

									<div class="crowd-field-wrap">
										<label><?php _e('Location Address (optional)', 'crowd'); ?></label>
										<input type="text" class="field" value="" name="location_address" />
									</div><!-- /.crowd-field-wrap -->

									<div class="crowd-field-wrap">
										<label><?php _e('Location Address Line 2 (optional)', 'crowd'); ?></label>
										<input type="text" class="field" value="" name="location_address_2" />
									</div><!-- /.crowd-field-wrap -->

									<div class="crowd-field-wrap">
										<label><?php _e('Tooltip (optional)', 'crowd'); ?></label>
										<input type="text" class="field" value="" name="location_tooltip" />
									</div><!-- /.crowd-field-wrap -->

									<div class="crowd-field-wrap">
										<label><?php _e('Description', 'crowd'); ?>: <em><?php _e('Tell others what’s so special about this location!', 'crowd'); ?></em></label>
										<textarea cols="0" class="field" rows="0" name="location_description"></textarea>
									</div><!-- /.crowd-field-wrap -->
								</div>

								<div class="crowd-fields-column">
									<?php if ( ! current_user_can( 'edit_map_locations' ) ) : ?>
										<div class="crowd-field-wrap">											
											<label><?php _e('Your Email', 'crowd'); ?>*</label>
											<input type="text" class="field" value="" name="location_email" />
										</div><!-- /.crowd-field-wrap -->
									<?php endif ?>

									<div class="crowd-field-wrap">
										<label><?php _e('Video URL', 'crowd'); ?> <em><?php _e('(optional Vimeo or Youtube video relating to location)', 'crowd'); ?></em></label>
										<input type="text" class="field" value="" name="location_video" placeholder="http://youtu.be/IegAD0C8jsI" />
									</div><!-- /.crowd-field-wrap -->

									<div class="crowd-field-wrap">
										<label><?php _e('Photos', 'crowd'); ?> <em><?php _e('(optional: include up to 5 photos from the location)', 'crowd'); ?></em></label>

										<?php for ($i = 0; $i < CROWD_PLUGIN_MAX_IMAGES; $i++) : ?>
											<div class="crowd-file-upload">
												<div class="crowd-file-upload-box">
													<input type="hidden" name="location_image[]" value="" />
													<span><?php _e('Click Here To Add Photo', 'crowd'); ?></span>
													<div class="crowd-file-upload-image"></div>
													<a class="crowd-file-upload-trigger"></a>
												</div><!-- /.crowd-file-upload-box -->
											</div><!-- /.crowd-file-upload -->
										<?php endfor; ?>

									</div><!-- /.crowd-field-wrap -->
								</div>
							</div>

							<div class="crowd-form-actions">
								<input type="submit" class="crowd-btn-submit" value="<?php echo esc_attr(__('Submit Location for Review', 'crowd')); ?>" name="submit" />
							</div><!-- /.crowd-form-actions -->
						</form>
					</div><!-- /.crowd-popup-body -->

				</div>
			</div>
		</div>
	</section>
</div>