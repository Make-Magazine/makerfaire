<?php
if (!function_exists('essb_actions_sendmail')) {
	function essb_actions_sendmail() {
		$exist_captcha = essb_option_value('mail_captcha_answer');
		$mail_function_security = essb_option_value('mail_function_security');

		$post_id = essb_object_value($_REQUEST, 'post_id');
		$from = essb_object_value($_REQUEST, 'from');
		$to = essb_object_value($_REQUEST, 'to');
		$c = essb_object_value($_REQUEST, 'c');
		$mail_salt = essb_object_value($_REQUEST, 'salt');
		$cu = essb_object_value($_REQUEST, 'cu');

		$post_id = sanitize_text_field($post_id);
		$from = sanitize_email($from);
		$to = sanitize_email($to);
		$c = sanitize_text_field($c);
		$mail_salt = sanitize_text_field($mail_salt);

		$translate_mail_message_sent = essb_option_value('translate_mail_message_sent');
		$translate_mail_message_invalid_captcha = essb_option_value('translate_mail_message_invalid_captcha');
		$translate_mail_message_error_send = essb_option_value('translate_mail_message_error_send');
		$translate_mail_message_error_mail = essb_option_value('translate_mail_message_error_mail');
		
		$validate_recaptcha = essb_option_bool_value('mail_recaptcha') && ! empty( essb_sanitize_option_value('mail_recaptcha_site') ) && ! empty( essb_sanitize_option_value('mail_recaptcha_secret') );
		$recaptcha = essb_object_value($_REQUEST, 'recaptcha');
		
		$output = array("code" => "", "message" => "");
		$valid = true;
		
		if ( $validate_recaptcha ) {
		
			if ( empty( $recaptcha ) ) {
				$valid = false;
				$output["code"] = "101";
				$output['message'] = esc_html__( 'reCAPTCHA is required.', 'essb' );
			}
		
			$api_results = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . essb_sanitize_option_value('mail_recaptcha_secret') . '&response=' . $recaptcha );
			$results     = json_decode( wp_remote_retrieve_body( $api_results ) );
			if ( empty( $results->success ) ) {
				$valid = false;
				$output["code"] = "101";
				$output['message'] = esc_html__( 'Incorrect reCAPTCHA, please try again.', 'essb' );
				
			}
		}

		if ($exist_captcha != '' && $exist_captcha != $c) {
			$valid = false;
			$output["code"] = "101";
			$output["message"] = $translate_mail_message_invalid_captcha != '' ? $translate_mail_message_invalid_captcha : esc_html__("Invalid captcha code", "essb");
		}

		if (strlen($to) > 80) {
			$valid = false;
			$output["code"] = "102";
			$output["message"] = translate_mail_message_error_mail != '' ? translate_mail_message_error_mail : esc_html__('Invalid recepient email', 'essb');
		}

		$mail_salt_check = get_option(ESSB3_MAIL_SALT);
		if ($mail_function_security == 'level2') {
			$mail_salt = "salt";
			$mail_salt_check = "salt";
		}

		if ($mail_salt != $mail_salt_check) {
			$valid = false;
			$output["code"] = "103";
			$output["message"] = esc_html__('Invalid security key provided', 'essb');
		}

		if (filter_var($from, FILTER_VALIDATE_EMAIL) === false) {
			$valid = false;
			$output["code"] = "104";
			$output["message"] = esc_html__('Invalid sender email', 'essb');
		}

		if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
			$valid = false;
			$output["code"] = "102";
			$output["message"] = translate_mail_message_error_mail != '' ? translate_mail_message_error_mail : esc_html__('Invalid recepient email', 'essb');
		}

		if ($valid) {
			$message_subject = essb_option_value('mail_subject');
			$message_body = essb_option_value('mail_body');
				
			$post = get_post($post_id);
			$url = get_permalink($post_id);
			$short_url = '';
				
			if (essb_option_bool_value('shorturl_activate')) {
				$short_url = essb_short_url ( $url, essb_option_value('shorturl_type'), $post_id, essb_option_value('shorturl_bitlyuser'), essb_option_value('shorturl_bitlyapi') );
				if ($short_url == '') {
					$short_url = $url;
				}
			}
				
			if (has_filter('essb_mailshare_url')) {
				$url = apply_filters('essb_mailshare_url', $url);
			}
				
				
			$base_post_url = $url;
				
			$site_url = get_site_url();
				
			if (has_filter('essb_mailshare_siteurl')) {
				$site_url = apply_filters('essb_mailshare_siteurl', $site_url);
			}
				
			$base_site_url = $site_url;
				
			$site_url = '<a href="'.esc_url($site_url).'">'.$site_url.'</a>';
			$url = '<a href="'.esc_url($url).'">'.$url.'</a>';
			$short_url = '<a href="'.esc_url($short_url).'">'.$short_url.'</a>';
				
			$title = $post->post_title;
			$image = essb_core_get_post_featured_image($post->ID);
			$description = $post->post_excerpt;
				
			if ($image != '') {
				$image = '<img src="'.$image.'" />';
			}


			$parsed_address = parse_url($base_site_url);
			
			/**
			 * Replacing variables that are passed by the Yoast SEO
			 */
			$title = str_replace('&#039;', "'", $title);
			$title = str_replace('&#034;', '"', $title);
			$title = str_replace('&#038;', '&', $title);
			$title = str_replace('%27', "'", $title);
				
			$description = str_replace('&#039;', "'", $description);
			$description = str_replace('&#034;', '"', $description);
			$description = str_replace('&#038;', '&', $description);
			$description = str_replace('%27', "'", $description);
				
			$message_subject = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%image%%#'), array($title, $base_site_url, $base_post_url, $image), $message_subject, $short_url);
			$message_body = preg_replace(array('#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%image%%#', '#%%shorturl%%#'), array($title, $site_url, $url, $image), $message_body, $short_url);
				
			if ($cu != '') {
				$message_body = $cu . $message_body;
			}
				
			$copy_address = essb_option_value('mail_copyaddress');
			$message_body = str_replace("\r\n", "<br />", $message_body);
				
			$headers = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=utf-8";
			$headers[] = "Reply-to: ".$from;
			$headers[] = "From: ".get_bloginfo('name').' <'.get_bloginfo('admin_email').'>';//admin_email
			if ($copy_address != '') {
				$headers[] = 'Bcc: '. $copy_address;
			}
				
			wp_mail($to, $message_subject, $message_body, $headers);
			$output["code"] = "1";
			$output["message"] = $translate_mail_message_sent != '' ? $translate_mail_message_sent : esc_html__("Message sent!", "essb");
		}

		echo str_replace('\\/','/',json_encode($output));
		die();
	}
}