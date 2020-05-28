<?php

if (!class_exists('ESSBLiveCustomizerControls')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/controls/controls.php');
}

global $post_id;
$custom = get_post_custom ( $post_id );
$shareoptimization_state = essb_options_bool_value ( 'opengraph_tags' );
$pinterest_sniff_disable = essb_options_bool_value('pinterest_sniff_disable');

$essb_post_og_desc = isset ( $custom ["essb_post_og_desc"] ) ? $custom ["essb_post_og_desc"] [0] : "";
$essb_post_og_title = isset ( $custom ["essb_post_og_title"] ) ? $custom ["essb_post_og_title"] [0] : "";
$essb_post_og_image = isset ( $custom ["essb_post_og_image"] ) ? $custom ["essb_post_og_image"] [0] : "";

$essb_post_twitter_hashtags = isset ( $custom ['essb_post_twitter_hashtags'] ) ? $custom ['essb_post_twitter_hashtags'] [0] : "";
$essb_post_twitter_username = isset ( $custom ['essb_post_twitter_username'] ) ? $custom ['essb_post_twitter_username'] [0] : "";
$essb_post_twitter_tweet = isset ( $custom ['essb_post_twitter_tweet'] ) ? $custom ['essb_post_twitter_tweet'] [0] : "";
$essb_post_pin_image = isset ( $custom ["essb_post_pin_image"] ) ? $custom ["essb_post_pin_image"] [0] : "";
$essb_post_share_message = isset ( $custom ["essb_post_share_message"] ) ? $custom ["essb_post_share_message"] [0] : "";

$sso_post_title = '';
$sso_post_desc = '';
$sso_post_image = '';

$settings_twitteruser = essb_option_value('twitteruser');
$settings_twitterhash = essb_option_value('twitterhashtags');

if ($shareoptimization_state) {
	$sso_post_title = get_the_title($post_id);
	$sso_post_desc = essb_core_get_post_excerpt($post_id);
	$sso_post_image = essb_core_get_post_featured_image($post_id);
	
	if (defined('WPSEO_VERSION')) {
			
		$yoast_title = get_post_meta( $post_id, '_yoast_wpseo_title', true);
		$yoast_description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true);
			
		if ($yoast_title != '') {
			$sso_post_title = $yoast_title;
		}
		if ($yoast_description != '') {
			$sso_post_desc = $yoast_description;
		}
	}
}

?>

<div class="section-share">
	<div class="customizer-inner-title"><span>
		<?php esc_html_e('Optimize information for sharing on social networks', 'essb'); ?></span></div>
	
	<?php if (!$shareoptimization_state): ?>
	<div class="row">
		<?php esc_html_e('Social Share optimization tags are not active on your site. The social share optimization tags allow you to control shared information for the networks. The tags may be inactive because you already have them generated on your site. If you are not sure should you act or not, you can refer to our support team for assistance.', 'essb'); ?>
	</div>
	<div class="row">
		<a href="<?php echo esc_url(admin_url('admin.php?page=essb_options&tab=social&section=optimize')); ?>" class="essb-composer-button essb-composer-blue" target="_blank"><i class="fa fa-cog"></i> Open Required Settings</a>
	</div>
	
	<?php else: ?>
	
	<div class="row">
		<div class="col1-2">
		<!-- first column -->
			<div class="row field">
			<?php esc_html_e('Social Image', 'essb'); ?>
			</div>
	<div class="row param">
		<div class="facebook-image-preview">
			<img src="<?php echo esc_url(($essb_post_og_image != '') ? $essb_post_og_image: $sso_post_image); ?>" class="facebook-image-preview-placeholder"/>
					
				<a href="#" class="essb-composer-button essb-composer-blue" id="essb_fileselect_og_image_button"><i class="fa fa-upload"></i></a>
		</div>

		<script type="text/javascript">

		jQuery(document).ready(function($){
			 
			 
		    var custom_uploader;
		 
			function essb_og_image_upload() {
				 //If the uploader object has already been created, reopen the dialog
		        if (custom_uploader) {
		            custom_uploader.open();
		            return;
		        }
		 
		        //Extend the wp.media object
		        custom_uploader = wp.media.frames.file_frame = wp.media({
		            title: 'Select File',
		            button: {
		                text: 'Select File'
		            },
		            multiple: false
		        });
		 
		        //When a file is selected, grab the URL and set it as the text field's value
		        custom_uploader.on('select', function() {
		            attachment = custom_uploader.state().get('selection').first().toJSON();
		            $('#essb_fileselect_og_image').val(attachment.url);

		            if ($('.facebook-image-preview-placeholder').length) {
			            $('.facebook-image-preview-placeholder').attr('src', attachment.url);
		            }
		        });
		 
		        //Open the uploader dialog
		        custom_uploader.open();
		    }

			 
		    $('#essb_fileselect_og_image').click(function(e) {
		 
		        e.preventDefault();
		 
		        essb_og_image_upload();
		 
		    });

		    $('#essb_fileselect_og_image_button').click(function(e) {
				 
		        e.preventDefault();
		 
		        essb_og_image_upload();
		 
		    });
		});
		
		</script>
		<input type="text" name="essb_fileselect_og_image" id="essb_fileselect_og_image" class="section-save" data-update="meta" data-field="essb_post_og_image" value="<?php echo $essb_post_og_image; ?>" placeholder="<?php echo $sso_post_image; ?>" style="display: none; " />
	</div>
	<div class="row description">
		<?php esc_html_e('Add an image that is optimized for maximum exposure on social networks. We recommend 1,200px by 628px', 'essb'); ?>
	</div>
		<!-- end first column -->
	
		</div>
		<div class="col1-2">
		<!-- second column -->

	<div class="row field">
		<?php esc_html_e('Share Title', 'essb'); ?>
	</div>
	<div class="row param">
		<input type="text" name="sso_title" class="section-save" data-update="meta" data-field="essb_post_og_title" value="<?php echo esc_attr($essb_post_og_title); ?>" placeholder="<?php echo esc_attr($sso_post_title); ?>" />
	</div>
	<div class="row description">
		<?php esc_html_e('Fill the title for sharing. It will be used by almost all social networks as a title (some of them may show only the title).', 'essb'); ?> 
	</div>

	
	<div class="row field">
		<?php esc_html_e('Share Description', 'essb'); ?>
	</div>
	<div class="row param">
		<textarea name="sso_title" class="section-save" data-update="meta" data-field="essb_post_og_desc" rows="4" placeholder="<?php echo esc_attr($sso_post_desc); ?>"><?php echo esc_textarea($essb_post_og_desc); ?></textarea>
	</div>
	<div class="row description">
		<?php esc_html_e('The custom description will appear as an addition to the shared information. The share description may not be read by all networks.', 'essb'); ?>
	</div>
		
		<!-- end second column -->
		</div>
	</div>
	

	
	
	<?php endif; ?>
	
	<div class="row">
		<div class="col1-2">
		<!-- first column -->
			<div class="customizer-inner-title"><span><?php esc_html_e('Custom Tweet', 'essb'); ?></span></div>
			<div class="row description"><?php esc_html_e('The custom Tweet settings can be used only if the Twitter button is active on your site.', 'essb'); ?></div>

	<div class="row field">
		<?php esc_html_e('Hashtags', 'essb'); ?>
	</div>
	<div class="row param">
		<input type="text" class="section-save" name="sso_title" data-update="meta" data-field="essb_post_twitter_hashtags" value="<?php echo esc_attr($essb_post_twitter_hashtags); ?>" placeholder="<?php echo esc_attr($settings_twitterhash); ?>" />
	</div>
	<div class="row description">
		<?php esc_html_e('Enter custom hashtags that will appear inside the Tweet for this post. If the field is blank the global tags will appear (if configured). You can also fill custom hashtags inside the Tweet itself.', 'essb'); ?>
	</div>
	
	<div class="row field">
		<?php esc_html_e('Mention Username', 'essb'); ?>
	</div>
	<div class="row param">
		<input type="text" class="section-save" name="sso_title" data-update="meta" data-field="essb_post_twitter_username" value="<?php echo esc_attr($essb_post_twitter_username); ?>" placeholder="<?php echo esc_attr($settings_twitteruser); ?>" />
	</div>
	<div class="row description">
		<?php esc_html_e('Enter a custom Twitter username to be mentioned for this post only. If blank the site username will appear. ', 'essb'); ?>
	</div>
	
	<div class="row field">
		<?php esc_html_e('Tweet', 'essb'); ?>
	</div>
	<div class="row param">
		<textarea name="sso_title" class="section-save" data-update="meta" data-field="essb_post_twitter_tweet" rows="4" placeholder="<?php echo esc_attr($sso_post_title); ?>"><?php echo esc_textarea($essb_post_twitter_tweet); ?></textarea>
	</div>
	<div class="row description">
		<?php esc_html_e('The Tweet is automatically building from the post title. You can personalize and set a custom Tweet using the field here. ', 'essb'); ?>
	</div>
		
		<!-- end: first column -->
		</div>

		<div class="col1-2">
		<!-- second column -->
			<div class="customizer-inner-title"><span><?php esc_html_e('Custom Share Message', 'essb'); ?></span></div>
	<div class="row param">
		<textarea name="sso_title" class="section-save" data-update="meta" data-field="essb_post_share_message" rows="4" placeholder="<?php echo esc_attr($sso_post_title); ?>"><?php echo esc_textarea($essb_post_share_message); ?></textarea>
	</div>
	<div class="row description">
		<?php esc_html_e('The custom share message is an additional component of share personalization. This message will not appear in the social share optimization tags or the custom Tweet but it will read by networks that support such - for example, Mobile Messenger.', 'essb'); ?>
	</div>

	<div class="customizer-inner-title"><span><?php esc_html_e('Pinterest', 'essb'); ?></span></div>
	<div class="row field">
		<?php esc_html_e('Pin a selected image only', 'essb'); ?>
	</div>
	<div class="row">
		<?php 
		ESSBLiveCustomizerControls::draw_switch_field2('pinterest_sniff_disable', $pinterest_sniff_disable, 'options', 'pinterest_sniff_disable');
		?>
	</div>
	<div class="row description">
		<?php esc_html_e('The default mode of the Pinterest button is to share any possible image from your site. If you need to have full control over the generated shared message and image, you can stop this function. The activation of the custom Pin image will give you the chance to select a fully optimized Pinterest image and set a custom Pin.', 'essb'); ?>
	</div>
	<div class="pinterest-custom-image" <?php if ($pinterest_sniff_disable == '') { echo 'style="display: none;"'; } ?>>
		<div class="row field">
			<?php esc_html_e('Pinable Image', 'essb'); ?>
		</div>
		<div class="row param">
			<?php ESSBLiveCustomizerControls::draw_image_select('pinterest-preview-image', 'essb_post_pin_image', 'meta', $essb_post_pin_image); ?>
		</div>
		<div class="row description">
			<?php esc_html_e('Optimized Pinterest image is formatted at 2:3 aspect ratio like 735 x 1102.', 'essb'); ?>
		</div>
	
	</div>
	
		<!-- end: second column -->
		</div>
		
	</div>

	
	
	<div class="row">
		<a href="#" class="essb-composer-button essb-composer-blue essb-section-save" data-section="section-share"><i class="fa fa-save"></i> <?php esc_html_e('Save Settings', 'essb'); ?></a>
	</div>
</div>

<script type="text/javascript">

function essbLiveCustomizerPostLoad() {
	jQuery('.essb-live-customizer .switch').change(function(){
	    jQuery(this).toggleClass('checked');

	    if (jQuery(this).hasClass('pinterest_sniff_disable')) {
		    if (jQuery(this).hasClass('checked'))
		    	jQuery('.pinterest-custom-image').fadeIn();
		    else
		    	jQuery('.pinterest-custom-image').fadeOut();
	    }
	  });
	
	jQuery(".essb-switch.pinterest_sniff_disable .cb-enable").click(function(){
		jQuery('.pinterest-custom-image').fadeIn();
	});

	jQuery(".essb-switch.pinterest_sniff_disable .cb-disable").click(function(){
		jQuery('.pinterest-custom-image').fadeOut();
	});
}
		
</script>