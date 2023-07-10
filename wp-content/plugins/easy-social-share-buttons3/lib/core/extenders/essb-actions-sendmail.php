<?php
if (!function_exists('essb_actions_sendmail')) {
    function essb_set_mail_charset($charset) {
        if ( empty( $charset ) ) {
            $charset = get_option( 'blog_charset' );
        }
        
        return $charset;
    }
    
    function essb_sendmail_generate_header() {
        $output = '';
        
        $output .= '<!DOCTYPE html>';
        $output .= '<html '.get_language_attributes().'>';
        $output .= '<head>';
        $output .= '<meta http-equiv="Content-Type" content="text/html; charset='.get_bloginfo( 'charset' ).'" />';
        $output .= '<title>'.get_bloginfo( 'name', 'display' ).'</title>';
    	$output .= '</head>';
	    $output .= '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">';
	    
	    $output .= '<style type="text/css">';
	    $output .= 'body { padding: 0; }';
	    $output .= '#wrapper {margin: 0;padding: 70px 0;-webkit-text-size-adjust: none !important;width: 100%;}';
	    $output .= '#wrapper table td { padding: 20px; }';
	    $output .= '</style>';
	    
	    $output .= '<div id="wrapper">';
	    $output .= '<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"><tr><td>';
	    
        return $output;
    }
    
    function essb_sendmail_generate_footer() {
        $output = '';
        
        $output .= '</td></tr></table>';
        $output .= '</div></body></html>';
        
        return $output;
    }
    
	function essb_actions_sendmail() {
		$exist_captcha = essb_option_value('mail_captcha_answer');
		$mail_function_security = essb_option_value('mail_function_security');

		$post_id = essb_object_value($_REQUEST, 'post_id');
		$from = essb_object_value($_REQUEST, 'from');
		$to = essb_object_value($_REQUEST, 'to');
		$c = essb_object_value($_REQUEST, 'c');
		$mail_salt = essb_object_value($_REQUEST, 'salt');
		$cu = essb_object_value($_REQUEST, 'cu');
		$from_name = essb_object_value($_REQUEST, 'from_name');
		$affid = essb_object_value($_REQUEST, 'affid');

		$post_id = sanitize_text_field($post_id);
		$from = sanitize_email($from);
		$to = sanitize_email($to);
		$c = sanitize_text_field($c);
		$mail_salt = sanitize_text_field($mail_salt);
		$from_name = sanitize_text_field($from_name);

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
			$output["message"] = $translate_mail_message_error_mail != '' ? $translate_mail_message_error_mail : esc_html__('Invalid recepient email', 'essb');
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
			$output["message"] = $translate_mail_message_error_mail != '' ? $translate_mail_message_error_mail : esc_html__('Invalid recepient email', 'essb');
		}

		
		if ($valid) {
			$message_subject = essb_option_value('mail_subject');
			$message_body = essb_option_value('mail_body');
			
			$message_original_body = essb_option_value('mail_body');
				
			$post = get_post($post_id);
			$url = get_permalink($post_id);
			$short_url = '';
			
			/**
			 * Affiliate integration
			 */
			if (essb_option_bool_value('affwp_active')) {
			    essb_helper_maybe_load_feature('integration-affiliatewp');
			    $url = essb_generate_affiliatewp_referral_link($url);
			}
			
			if (essb_option_bool_value('slicewp_active')) {
			    essb_helper_maybe_load_feature('integration-slicewp');
			    $url = essb_generate_slicewp_referral_link($url);
			}
			
			if (essb_option_bool_value('affs_active')) {
			    $url = do_shortcode('[affiliates_url]'.$url.'[/affiliates_url]');
			}
			
			if (essb_option_bool_value('mycred_referral_activate') && function_exists('mycred_render_affiliate_link')) {
			    $url = mycred_render_affiliate_link( array( 'url' => $url ) );
			}
			//--end: affiliate integration
				
			if (essb_option_bool_value('shorturl_activate')) {
			    
			    if (class_exists('ESSB_Short_URL')) {
			        if (ESSB_Short_URL::active()) {
			            $short_url = ESSB_Short_URL::generate_short_url($url, $post_id, 'mail');
			        }
			    }
			    
			    if (empty($short_url)) {
			        $short_url = $url;
			    }
			}
				
			if (has_filter('essb_mailshare_url')) {
				$url = apply_filters('essb_mailshare_url', $url);
			}
			
			if (!empty($affid)) {
			    if (strpos($affid, '/') === false) {
			        if (strpos($url, '?') === false) {
			            $url .= '?' . $affid;
			        }
			        else {
			            $url .= '&' . $affid;
			        }
			    }
			    else {
			        $url = trailingslashit($url) . $affid;
			    }
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
				
			$message_subject = preg_replace(array (
                '#%%title%%#',
                '#%%siteurl%%#',
                '#%%permalink%%#',
                '#%%image%%#',
                '#%%shorturl%%#',
			    '#%%from_email%%#',
			    '#%%from_name%%#',
			    '#%%to_email%%#'
            ), array (
                $title,
                $base_site_url,
                $base_post_url,
                $image,
                $short_url,
                $from,
                $from_name,
                $to
            ), $message_subject);
            
			$message_body = preg_replace(array (
                '#%%title%%#',
                '#%%siteurl%%#',
                '#%%permalink%%#',
                '#%%image%%#',
                '#%%shorturl%%#',
			    '#%%from_email%%#',
			    '#%%from_name%%#',
			    '#%%to_email%%#'
            ), array (
                $title,
                $site_url,
                $url,
                $image,
                $short_url,
                $from,
                $from_name,
                $to
            ), $message_body);
				
			if ($cu != '') {
				$message_body = $cu . $message_body;
			}
				
			$copy_address = essb_option_value('mail_copyaddress');
			$message_body = str_replace("\r\n", "<br />", $message_body);
			
			// @added in 7.0.2
			$message_body = wp_kses_post( wpautop( wptexturize( $message_body ) ) );
			
			if (has_filter('essb_mailshare_subject')) {
			    $essb_mailshare_subject = apply_filters('essb_mailshare_subject', $essb_mailshare_subject);
			}
			if (has_filter('essb_mailshare_body')) {
			    $message_body = apply_filters('essb_mailshare_body', $message_body);
			}
			
			if (has_filter('essb_mailshare_body_advanced')) {			    
			    $sending_options = array(
			         'title' => $title,
			         'siteurl' => $site_url,
			         'permalink' => $url,
			         'image' => $image,
			         'shorturl' => $short_url,
			         'from_email' => $from,
			         'from_name' => $from_name,
			         'to_email' => $to
			    );
			    
			    $message_original_body = apply_filters('essb_mailshare_body_advanced', $message_body, $sending_options);
			    $message_body = $message_original_body;
			}
			
			$message_body = essb_sendmail_generate_header() . $message_body . essb_sendmail_generate_footer();
			
			/**
			 * @since 8.8.2
			 * Additional filter that allows changing the sender's email address
			 */
			$header_from = "From: ".get_bloginfo('name').' <'.get_bloginfo('admin_email').'>';
			if (has_filter('essb_mailshare_from_header')) {
			    $options = array(
			        'from_email' => $from,
			        'from_name' => $from_name,
			        'to_email' => $to,
			        'site_email' => get_bloginfo('admin_email'),
			        'site_name' => get_bloginfo('name')
			    );
			    
			    $header_from = apply_filters('essb_mailshare_from_header', $options);
			}
				
			$headers = array();
			$headers['From'] = $header_from;//admin_email
			$headers['Reply-To'] = "Reply-To: ".$from;
			$headers['Content-type:'] = "Content-type: text/html; charset=".get_option( 'blog_charset' );

			if ($copy_address != '') {
				$headers['Bcc'] = 'Bcc: '. $copy_address;
			}
			
			add_filter( 'wp_mail_charset', 'essb_set_mail_charset' );
			
			wp_mail($to, $message_subject, $message_body, $headers);
			
			remove_filter( 'wp_mail_charset', 'essb_set_mail_charset' );
			$output["code"] = "1";
			$output["message"] = $translate_mail_message_sent != '' ? $translate_mail_message_sent : esc_html__("Message sent!", "essb");
		}

		echo str_replace('\\/','/',json_encode($output));
		die();
	}
}