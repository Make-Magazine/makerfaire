<?php
/**
 * Custom Facebook Feed : Error Message Template
 * Display different error message
 *
 * @version 3.18 Custom Facebook Feed by Smash Balloon
 *
 */
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Shortcode_Display;
use CustomFacebookFeed\CFF_Display_Elements_Pro;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $current_user;
$user_id 				= $current_user->ID;
$cap 	 				= CFF_Shortcode_Display::get_error_message_cap();
$cff_ppca_check_error 	= CFF_Shortcode_Display::get_error_check( $page_id, $user_id, $access_token );
$cff_ppca_error 		= CFF_Shortcode_Display::get_error_check_ppca( $FBdata );

if( ($cff_photos_only && empty($cff_album_id)) && $cff_is_group && current_user_can( $cap ) ):
	if ( ! get_user_meta($user_id, 'cff_group_photos_notice_dismiss') ):
		?>
		<section class="cff-error-msg">
	     	<p><b><?php echo esc_html__('This message is only visible to admins:','custom-facebook-feed') ?></b><br /><?php echo esc_html__('Facebook deprecated version 2.0 of their API in August 2016, which unfortunately means that Facebook no longer supports displaying photo grid feeds from Facebook Groups. Please see','custom-facebook-feed') ?> <a href='https://smashballoon.com/can-i-display-photos-from-a-facebook-group/' target='_blank'><?php echo esc_html__('here','custom-facebook-feed') ?></a> <?php echo esc_html__('for more information. We apologize for any inconvenience.','custom-facebook-feed') ?></p>
		 	<a class="cff_notice_dismiss" href="<?php echo esc_url( add_query_arg( 'cff_group_photos_notice_dismiss', '0' ) ); ?>"><?php echo CFF_Display_Elements_Pro::get_icon( "times-circle" ) ?></a>
		</section>
		<?php if ( current_user_can( $cap ) ): ?>
			<style>#cff .cff-error-msg{ display: block !important; }</style>
		<?php 
		endif;
	endif;
endif;

$check_error_no_data = CFF_Shortcode_Display::get_error_check_no_data( $FBdata, $cff_events_only, $cff_events_source, $cff_featured_post_active, $page_id, $cff_ppca_check_error,$atts );

if( $check_error_no_data ):
	//Check whether it's an error in the backup cache
	if( isset($FBdata->cached_error) ) $FBdata->error = $FBdata->cached_error;

	if( ($cff_photos_only && empty($cff_album_id)) && $cff_is_group ):
	?>
		<p class="cff_error_msg_header"><?php echo CFF_Display_Elements_Pro::get_icon( "facebook-square" ) ?><a href="https://www.facebook.com/groups/<?php echo $page_id ?>/photos" target="_blank" <?php echo $cff_nofollow ?>><?php echo esc_html__('View photos on Facebook','custom-facebook-feed') ?></a>
	<?php 
	else:
		if(!empty(get_option('cff_connected_accounts')) && null !== get_option('cff_connected_accounts')):
		?>
		<div class="cff-error-msg">
			<div>
            <?php echo CFF_Display_Elements_Pro::get_icon( "lock" ) ?><b><?php echo esc_html__('This message is only visible to admins.', 'custom-facebook-feed'); ?></b><br/>	
				<?php 
					if ( !$cff_ppca_check_error ) echo esc_html__('Problem displaying Facebook posts.', 'custom-facebook-feed');
					if ( isset($FBdata->cached_error) ) echo esc_html__(' Backup cache in use.', 'custom-facebook-feed');
				?>	
				<?php if( $cff_ppca_check_error || $cff_ppca_error ): ?>
					</div>
					<?php if( $cff_ppca_error ): ?>
						<b>PPCA Error:</b> <?php echo esc_html__('Due to Facebook API changes it is no longer possible to display a feed from a Facebook Page you are not an admin of. The Facebook feed below is not using a valid Access Token for this Facebook page and so has stopped updating.', 'custom-facebook-feed'); ?>
					<?php else: ?>
						<a class="cff_notice_dismiss" href="<?php echo esc_url( add_query_arg( 'cff_ppca_check_notice_dismiss', '0' )  ); ?>"><?php echo CFF_Display_Elements_Pro::get_icon( "times-circle" ) ?></a>
						<b class="cff-warning-notice">PPCA Error:</b> <?php echo esc_html__('Due to Facebook API changes on September 4, 2020, it will no longer be possible to display a feed from a Facebook Page you are not an admin of. The Facebook feed below is not using a valid Access Token for this Facebook page and so will stop updating after this date.', 'custom-facebook-feed'); ?>
					<?php endif; ?>		
					<?php if(  current_user_can( $cap )  ): ?>
						<br /><b style="margin-top: 5px; display: inline-block;"><?php echo esc_html__('Action Required.', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('Please', 'custom-facebook-feed'); ?> <a href="https://smashballoon.com/facebook-ppca-error-notice/" target="_blank"><?php echo esc_html__('see here', 'custom-facebook-feed'); ?></a> <?php echo esc_html__('for information on how to fix this.', 'custom-facebook-feed'); ?>
					<?php endif; ?>						
				<?php else: ?>
					</div>
					<div id="cff-error-reason">
						<?php if( isset($FBdata->error->message) ): ?>
							<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error->message; ?><br/>
						<?php endif; ?>	
						<?php if( isset($FBdata->error->type) ): ?>
							<b><?php echo esc_html__('Type', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error->type; ?><br/>
						<?php endif; ?>	
						<?php if( isset($FBdata->error->error_subcode) ): ?>
							<b><?php echo esc_html__('Subcode', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error->error_subcode; ?><br/>
						<?php endif; ?>	
						<?php if( isset($FBdata->error_msg) ): ?>
							<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error_msg; ?><br/>
						<?php endif; ?>		
						<?php if( isset($FBdata->error_code) ): ?>
							<?php echo esc_html__('Code', 'custom-facebook-feed'); ?>: <?php echo $FBdata->error_code; ?><br/>
						<?php endif; ?>	
						<?php if( $FBdata == null ): ?>
							<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('Server configuration issue', 'custom-facebook-feed'); ?><br/>
						<?php else: ?>	
							<?php if( empty($FBdata->error) && empty($FBdata->error_msg) && !$cff_ppca_check_error ): ?>
								<?php if( $atts['limit'] == '0' ): ?>
									<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('Post limit setting is set to 0. Please increase the \'Facebook API post limit\' setting on the plugin\'s Settings page.', 'custom-facebook-feed'); ?>								
								<?php else: ?>	
									<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('No posts available for this Facebook ID', 'custom-facebook-feed'); ?>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
						<?php if( current_user_can($cap) ): ?>
							<b><?php echo esc_html__('Solution', 'custom-facebook-feed'); ?>:</b> <a href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank"><?php echo esc_html__('See here', 'custom-facebook-feed'); ?></a> <?php echo esc_html__('for how to solve this error', 'custom-facebook-feed'); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>				
		</div>
		<?php
		endif;//Token Empty & ID
	endif;

		if( current_user_can($cap) ): ?>
			<style>#cff .cff-error-msg{ display: block !important; }</style>
	<?php 
		endif;
		if( $cff_is_group ): ?>
			<p class="cff_error_msg_header"><?php echo CFF_Display_Elements_Pro::get_icon( "facebook-square" ) ?><a href="https://www.facebook.com/groups/<?php echo $page_id ?>" target="_blank" <?php echo $cff_nofollow ?>><?php echo esc_html__('Join us on Facebook','custom-facebook-feed') ?></a>
	<?php 
		endif;	

endif;