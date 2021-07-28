<?php
if (!function_exists('essb_rs_mailform_build')) {
	add_action('essb_rs_footer', 'essb_rs_mailform_build');
	
	function essb_rs_mailform_build() {
		global $post;
		
		$mail_salt_check = get_option(ESSB3_MAIL_SALT);
		
		// prevent after removing all options to generate blank salt setup
		if (!$mail_salt_check || empty($mail_salt_check)) {
		    $mail_salt_check = mt_rand();
		    update_option(ESSB3_MAIL_SALT, $mail_salt_check);
		}
		
		$translate_mail_title = essb_option_value('translate_mail_title');
		$translate_mail_email = essb_option_value('translate_mail_email');
		$translate_mail_name = essb_option_value('translate_mail_name');
		$translate_mail_recipient = essb_option_value('translate_mail_recipient');
		$translate_mail_cancel = essb_option_value('translate_mail_cancel');
		$translate_mail_send = essb_option_value('translate_mail_send');
		$translate_mail_custom = essb_option_value('translate_mail_custom');
		
		$translate_mail_captcha = essb_option_value('translate_mail_captcha');
		
		$mail_popup_preview = essb_option_bool_value('mail_popup_preview');
		
		$translate_mail_message_error_fill = essb_option_value('translate_mail_message_error_fill');
		if ($translate_mail_message_error_fill == '') {
			$translate_mail_message_error_fill = esc_html__('Please fill all form fields', 'essb');
		}
		
		if ($translate_mail_title == '') $translate_mail_title = esc_html__('Send this to a friend', 'essb');
		if ($translate_mail_email == '') $translate_mail_email = esc_html__('Your email', 'essb');
		if ($translate_mail_recipient == '') $translate_mail_recipient = esc_html__('Recipient email', 'essb');
		if ($translate_mail_cancel == '') $translate_mail_cancel = esc_html__('Cancel', 'essb');
		if ($translate_mail_send == '') $translate_mail_send = esc_html__('Send', 'essb');
		if ($translate_mail_custom == '') $translate_mail_custom = esc_html__('Your message', 'essb');
		if ($translate_mail_captcha == '') $translate_mail_captcha = esc_html__('Fill captcha code', 'essb');
		if ($translate_mail_name == '') $translate_mail_name = esc_html__ ('Your name', 'essb');
		
		$mail_captcha = essb_option_value('mail_captcha');
		$mail_popup_edit = false; // deprecated @since 7.0
		
		$code = '';
		
		$code .= '<div class="essb_mailform" data-error="'.esc_attr($translate_mail_message_error_fill).'">';
		$code .= '<div class="essb_mailform_header">';
		$code .= '<div class="heading">'.$translate_mail_title.'</div>';
		$code .= '</div>';
		$code .= '<div class="essb_mailform_content">';
		$code .= '<input type="text" id="essb_mailform_from" class="essb_mailform_content_input" placeholder="'.esc_attr($translate_mail_email).'"/>';
		$code .= '<input type="text" id="essb_mailform_from_name" class="essb_mailform_content_input" placeholder="'.esc_attr($translate_mail_name).'"/>';
		$code .= '<input type="text" id="essb_mailform_to" class="essb_mailform_content_input" placeholder="'.esc_attr($translate_mail_recipient).'"/>';
		
		if ($mail_popup_edit) {
			$code .= '<label class="essb_mailform_content_label">'.$translate_mail_custom.'</label>';
			$code .= '<textarea id="essb_mailform_custom" class="essb_mailform_content_input" placeholder="'.esc_attr($translate_mail_custom).'"></textarea>';
				
		}
 		
		if ($mail_captcha != '') {
			$code .= '<label class="essb_mailform_content_label">'.$mail_captcha.'</label>';
			$code .= '<input type="text" id="essb_mailform_c" class="essb_mailform_content_input" placeholder="'.esc_attr($translate_mail_captcha).'"/>';
		}
				
		if ($mail_popup_preview && isset($post)) {
			$message_body = essb_option_value('mail_body');
			$message_body = stripslashes($message_body);
							
			$url = get_permalink($post->ID);
			
			if (has_filter('essb_mailshare_url')) {
				$url = apply_filters('essb_mailshare_url', $url);
			}
			
			$base_post_url = $url;
				
			$site_url = get_site_url();
			
			if (has_filter('essb_mailshare_siteurl')) {
				$site_url = apply_filters('essb_mailshare_siteurl', $site_url);
			}
				
			$base_site_url = $site_url;
				
			$site_url = '<a href="'.esc_url($site_url).'">'.esc_url($site_url).'</a>';
			$url = '<a href="'.esc_url($url).'">'.esc_url($url).'</a>';
				
			$title = $post->post_title;
			$image = essb_core_get_post_featured_image($post->ID);
			$description = $post->post_excerpt;
				
			if ($image != '') {
				$image = '<img src="'.esc_url($image).'" />';
			}
			
			
			$parsed_address = parse_url($base_site_url);
				
			$message_body = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%image%%#'), array($title, $site_url, $url, $image), $message_body);

			/**
			 * @since 7.4.2
			 */
			$message_body = preg_replace(array('#%%from_email%%#', '#%%from_name%%#', '#%%to_email%%#'), array($translate_mail_email, $translate_mail_name, $translate_mail_recipient), $message_body);
			
			$message_body = str_replace("\r\n", "<br />", $message_body);
			
			$code .= '<div class="essb_mailform_preview">'.$message_body.'</div>';
		}
		
		$recaptcha = essb_option_bool_value('mail_recaptcha') && ! empty( essb_sanitize_option_value('mail_recaptcha_site') ) && ! empty( essb_sanitize_option_value('mail_recaptcha_secret') );
		
		if ($recaptcha) {
			$code .= '<div id="essb-modal-recaptcha"></div>';
		}
		
		$code .= '<div class="essb_mailform_content_buttons">';
		$code .= '<button id="essb_mailform_btn_submit" class="essb_mailform_content_button" onclick="essb_mailform_send();">'.$translate_mail_send.'</button>';
		$code .= '<button id="essb_mailform_btn_cancel" class="essb_mailform_content_button" onclick="essb_close_mailform(); return false;">'.$translate_mail_cancel.'</button>';
		$code .= '</div>';
		
		$code .= '<input type="hidden" id="essb_mail_salt" value="'.esc_attr($mail_salt_check).'"/>';
		$code .= '<input type="hidden" id="essb_mail_instance" value=""/>';
		$code .= '<input type="hidden" id="essb_mail_post" value=""/>';
		
		$code .= '</div>';
		$code .= '</div>';
		$code .= '<div class="essb_mailform_shadow"></div>';

		echo $code;
	}
}

if (!function_exists('essb_register_mail_recaptcha')) {
	function essb_register_mail_recaptcha() {
		$recaptcha = essb_option_bool_value('mail_recaptcha') && ! empty( essb_sanitize_option_value('mail_recaptcha_site') ) && ! empty( essb_sanitize_option_value('mail_recaptcha_secret') );
		if ( $recaptcha ) {
			wp_enqueue_script(
					'recaptcha',
					'https://www.google.com/recaptcha/api.js',
					array(),
					'2.0',
					true
			);
			
			$args = array();
			$args['recaptchaSitekey'] = sanitize_text_field( essb_sanitize_option_value('mail_recaptcha_site') );
			wp_localize_script( 'recaptcha', 'essb_recaptcha', $args );
		}
	}
}