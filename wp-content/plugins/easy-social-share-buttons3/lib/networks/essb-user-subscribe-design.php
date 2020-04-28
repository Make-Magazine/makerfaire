<?php
if (!function_exists('essb_user_subscribe_form_design')) {

	/**
	 * Generating an user build subscribe form. The form creator lets visitors build own subscribe forms with ease.
	 *
	 * @param unknown_type $salt
	 * @param unknown_type $design
	 * @param unknown_type $is_widget
	 * @param unknown_type $position
	 */
	function essb_user_subscribe_form_design($salt = '', $design = '', $is_widget = false, $position = '') {
		global $wp;

		$output = '';

		$default_texts = array(
				"title" => __('Join our list', 'essb'),
				"text" => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'essb'),
				"email" => __('Enter your email here', 'essb'),
				"name" => __('Enter your name here', 'essb'),
				"button" => __('Join Now', 'essb'),
				"footer" => __('We respect your privacy and take protecting it seriously', 'essb'),
				"success" => __('Thank you for subscribing.', 'essb'),
				"error" => __('Something went wrong.', 'essb')
		);

		/**
		 * Loading the form designer functios that are required to work and deal
		 * with load save and update. But load only if we have not done than in the past.
		 */
		if (! function_exists ( 'essb5_get_form_designs' )) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/formdesigner-helper.php');
		}

		$key = str_replace('userdesign-', '', $design);
		$user_forms = essb5_get_form_designs();
		$subscribe_design = isset($user_forms[$key]) ? $user_forms[$key] : array();

		$form_title = stripslashes(essb_array_value('title', $subscribe_design));
		$form_text = stripslashes(essb_array_value('text', $subscribe_design));
		$form_footer = stripslashes(essb_array_value('footer', $subscribe_design));
		$form_name_placeholder = stripslashes(essb_array_value('name_placeholder', $subscribe_design));
		$form_email_placeholder = stripslashes(essb_array_value('email_placeholder', $subscribe_design));
		$form_button_placeholder = stripslashes(essb_array_value('button_placeholder', $subscribe_design));
		$form_error_message = stripslashes(essb_array_value('error_message', $subscribe_design));
		$form_ok_message = stripslashes(essb_array_value('ok_message', $subscribe_design));
		$form_add_name = stripslashes(essb_array_value('add_name', $subscribe_design));

		$form_image = stripslashes(essb_array_value('image', $subscribe_design));
		$form_image_location = stripslashes(essb_array_value('image_location', $subscribe_design));
		$has_image = ($form_image != '' && $form_image_location != '' && $form_image_location != 'background') ? true : false; // form contains or not proper image
		$image_area_width = stripslashes(essb_array_value('image_area_width', $subscribe_design));

		if ($has_image && $image_area_width == '') { $image_area_width = '30'; }
		if (!$has_image && $image_area_width != '') { $image_area_width = ''; }

		// setting defaults for compoments that cannot be blank
		if ($form_name_placeholder == '') {
			$form_name_placeholder = $default_texts['name'];
		}

		if ($form_email_placeholder == '') {
			$form_email_placeholder = $default_texts['email'];
		}

		if ($form_button_placeholder == '') {
			$form_button_placeholder = $default_texts['button'];
		}

		if ($form_ok_message == '') {
			$form_ok_message = $default_texts['success'];
		}

		if ($form_error_message == '') {
			$form_error_message = $default_texts['error'];
		}

		/**
		 * Building the subscribe action URL. The URL will be the same page but using a nonce check
		 * to ensure the subscribe action is secured
		 *
		 */
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$secure_nonce = wp_create_nonce('essb3_subscribe_nonce');
		$current_url = add_query_arg('essb3_subscribe_nonce', $secure_nonce, $current_url);

		$form_classes = 'essb-custom-'.$design;
		if ($is_widget) { $form_classes .= ' essb-subscribe-form-inwidget'; }
		if ($has_image) {
			$form_classes .= ' essb-subscribe-image-'.$form_image_location;
			if ($image_area_width != '') {
				$form_classes .= ' essb-imagearea-'.$image_area_width;
			}
		}

		$output = '';

		// appeding user CSS styles for the form
		$output .= essb_user_subscribe_form_custom_css($salt, $design, 'essb-custom-'.$design, $subscribe_design);

		$output .= '<div class="essb-subscribe-form-content essb-userform '.esc_attr($form_classes).'" data-position="'.esc_attr($position).'" data-design="'.esc_attr($design).'">';

		if ($has_image && ($form_image_location == 'top' || $form_image_location == 'left')) {
			$output .= '<div class="essb-userform-imagearea"><img src="'.esc_url($form_image).'"/></div>';
		}


		if ($has_image && ($form_image_location == 'left' || $form_image_location == 'right')) {
			$output .= '<div class="essb-userform-contentarea">';
		}

		$output .= '<div class="essb-subscribe-form-content-top">';

		if ($form_title != '') {
			$output .= '<div class="essb-subscribe-form-content-title">'.$form_title.'</div>';
		}

		if ($has_image && $form_image_location == 'below_heading') {
			$output .= '<div class="essb-userform-imagearea"><img src="'.esc_url($form_image).'"/></div>';
		}

		if ($form_text != '') {
			$output .= '<p class="essb-subscribe-form-content-text">'.$form_text.'</p>';
		}

		$output .= '</div>';

		$output .= '<div class="essb-subscribe-form-content-bottom">';
		// generating form output
		$output .= '<form action="'.esc_url(add_query_arg('essb-malchimp-signup', '1', $current_url)).'" method="post" class="essb-subscribe-from-content-form" id="essb-subscribe-from-content-form-mailchimp">';

		if ($form_add_name == 'true') {
			$output .= '<input class="essb-subscribe-form-content-name-field essb-userform-field" type="text" value="" placeholder="'.esc_attr($form_name_placeholder).'" name="mailchimp_name">';
		}

		$output .= '<input class="essb-subscribe-form-content-email-field essb-userform-field" type="text" value="" placeholder="'.esc_attr($form_email_placeholder).'" name="mailchimp_email">';

		$output .= ESSBNetworks_Subscribe::generate_if_needed_agree_check();

		$output .= '<input class="submit essb-userform-button" name="submit" type="submit" value="'.esc_attr($form_button_placeholder).'" onclick="essb.ajax_subscribe(\''.$salt.'\', event);">';
		$output .= '</form>';

		$output .= '<div class="essb-subscribe-loader"  style="display: none;">
		<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
		<path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
		<animateTransform attributeType="xml"
		attributeName="transform"
		type="rotate"
		from="0 25 25"
		to="360 25 25"
		dur="0.6s"
		repeatCount="indefinite"/>
		</path>
		</svg>
		</div>';

		$output .= '<p class="essb-subscribe-form-content-success essb-subscribe-form-result-message" style="display: none;">'.$form_ok_message.'</p>';
		$output .= '<p class="essb-subscribe-form-content-error essb-subscribe-form-result-message"  style="display: none;">'.$form_error_message.'</p>';
		$output .= '<p class="essb-subscribe-form-content-footer">'.$form_footer.'</p>';

		$output .= '<div class="clear"></div>';

		$output .= '</div>';

		if ($has_image && ($form_image_location == 'left' || $form_image_location == 'right')) {
			$output .= '</div>';
		}

		if ($has_image && $form_image_location == 'right') {
			$output .= '<div class="essb-userform-imagearea"><img src="'.esc_url($form_image).'"/></div>';
		}

		$output .= '</div>';

		return $output;
	}

	function essb_user_subscribe_form_custom_css($salt = '', $design = '', $design_class = '', $options = '') {
		$output = '';

		$bgcolor = stripslashes(essb_array_value('bgcolor', $options));
		$bgcolor2 = stripslashes(essb_array_value('bgcolor2', $options));
		$textcolor = stripslashes(essb_array_value('textcolor', $options));
		$border_color = stripslashes(essb_array_value('border_color', $options));
		$border_width = stripslashes(essb_array_value('border_width', $options));
		$padding = stripslashes(essb_array_value('padding', $options));
		$image_location = stripslashes(essb_array_value('image_location', $options));
		$image = stripslashes(essb_array_value('image', $options));
		$border_radius = stripslashes(essb_array_value('border_radius', $options));

		if ($padding == '') {
			$padding = '40px';
		}

		$core_form_styles = '';


		if ($bgcolor != '' || $bgcolor2 != '') {
			if ($bgcolor != '' && $bgcolor2 == '') {
				$core_form_styles .= 'background-color: '.esc_attr($bgcolor).' !important;';
			}

			else if ($bgcolor == '' && $bgcolor2 != '') {
				$core_form_styles .= 'background-color: '.esc_attr($bgcolor2).'!important;';
			}

			else {
				$core_form_styles .= 'background: '.esc_attr($bgcolor).';background: -moz-linear-gradient(top, '.esc_attr($bgcolor).' 0%, '.esc_attr($bgcolor2).' 100%);background: -webkit-linear-gradient(top, '.$bgcolor.' 0%,'.$bgcolor2.' 100%);background: linear-gradient(to bottom, '.$bgcolor.' 0%,'.$bgcolor2.' 100%)!important;';
			}
		}

		if ($padding != '') {
			$core_form_styles .= 'padding:'.$padding.'!important;';
		}


		if ($textcolor != '') {
			$core_form_styles .= 'color:'.$textcolor.'!important;';
		}

		if ($border_color != '' && $border_width != '') {
			$core_form_styles .= 'border: '.$border_width.' solid '.$border_color.'!important;';
		}

		if ($image != '' && $image_location == 'background') {
			$core_form_styles = 'background: url('.$image.')!important;background-size: cover; background-position: center; background-repeat: no-repeat;';
		}

		$glow_color = stripslashes(essb_array_value('glow_color', $options));
		$glow_size = stripslashes(essb_array_value('glow_size', $options));

		if ($glow_color != '' && $glow_size != '') {
			$core_form_styles .= 'box-shadow: 0 0 '.$glow_size.'px 0 '.$glow_color;
		}

		if ($border_radius != '') {
			$core_form_styles .= 'border-radius:'.$border_radius.';-webkit-border-radius:'.$border_radius.';';
		}

		// adding core form styles
		if ($core_form_styles != '') {
			$output .= '.'.$design_class.'{'.$core_form_styles.'}';
		}

		// heading styles
		$heading_fontsize = stripslashes(essb_array_value('heading_fontsize', $options));
		$heading_fontweight = stripslashes(essb_array_value('heading_fontweight', $options));
		$headingcolor = stripslashes(essb_array_value('headingcolor', $options));
		if ($heading_fontsize != '' || $heading_fontweight != '' || $headingcolor != '') {
			$heading_styles = '.'.$design_class.' .essb-subscribe-form-content-title {';

			if ($heading_fontsize != '') { $heading_styles .= 'font-size:'.$heading_fontsize.';'; }
			if ($heading_fontweight != '') {
				$heading_styles .= 'font-weight:'.$heading_fontweight.'!important;';
			}
			if ($headingcolor != '') {
				$heading_styles .= 'color:'.$headingcolor.'!important;';
			}

			$heading_styles .= '}';

			$output .= $heading_styles;
		}

		// text styles
		$text_fontsize = stripslashes(essb_array_value('text_fontsize', $options));
		$text_fontweight = stripslashes(essb_array_value('text_fontweight', $options));
		$textcolor = stripslashes(essb_array_value('textcolor', $options));
		if ($text_fontsize != '' || $text_fontweight != '' || $textcolor != '') {
			$text_styles = '.'.$design_class.' .essb-subscribe-form-content-text {';

			if ($text_fontsize != '') {
				$text_styles .= 'font-size:'.$text_fontsize.'!important;';
			}
			if ($text_fontweight != '') {
				$text_styles .= 'font-weight:'.$text_fontweight.'!important;';
			}
			if ($textcolor != '') {
				$text_styles .= 'color:'.$textcolor.'!important;';
			}

			$text_styles .= '}';

			if ($textcolor != '') {
				$text_styles .= '.'.$design_class.' .essb-subscribe-form-result-message {color:'.$textcolor.'!important;}';
				$text_styles .= '.'.$design_class.' .essb-subscribe-loader svg path, .'.$design_class.' .essb-subscribe-loader svg rect {fill:'.$textcolor.'!important;}';
			}

			$output .= $text_styles;
		}

		// footer styles
		$footer_fontsize = stripslashes(essb_array_value('footer_fontsize', $options));
		$footer_fontweight = stripslashes(essb_array_value('footer_fontweight', $options));
		$footercolor = stripslashes(essb_array_value('footercolor', $options));
		if ($footer_fontsize != '' || $footer_fontweight != '' || $footercolor != '') {
			$footer_styles = '.'.$design_class.' .essb-subscribe-form-content-footer {';

			if ($footer_fontsize != '') {
				$footer_styles .= 'font-size:'.$footer_fontsize.'!important;';
			}
			if ($footer_fontweight != '') {
				$footer_styles .= 'font-weight:'.$footer_fontweight.'!important;';
			}
			if ($footercolor != '') {
				$footer_styles .= 'color:'.$footercolor.'!important;';
			}

			$footer_styles .= '}';

			$output .= $footer_styles;
		}

		// alignment code
		$align = stripslashes(essb_array_value('align', $options));
		if ($align != '') {
			$output .= '.'.$design_class.' .essb-subscribe-form-content-footer, .'.$design_class.' .essb-subscribe-form-content-text, .'.$design_class.' .essb-subscribe-form-content-title { text-align: '.$align.'!important; }';
		}

		// input styles
		$fields_bg = stripslashes(essb_array_value('fields_bg', $options));
		$fields_text = stripslashes(essb_array_value('fields_text', $options));
		if ($fields_bg != '' || $fields_text != '') {
			$output .= '.'.$design_class.' .essb-userform-field { ';
			if ($fields_bg != '') { $output .= 'background:'.$fields_bg.'!important;'; }
			if ($fields_text != '') {
				$output .= 'color:'.$fields_text.'!important;';
			}
			$output .= '}';
		}

		$input_fontsize = stripslashes(essb_array_value('input_fontsize', $options));
		$input_fontweight = stripslashes(essb_array_value('input_fontweight', $options));
		if ($input_fontsize != '' || $input_fontweight != '') {
			$output .= '.'.$design_class.' .essb-userform-field { ';
			if ($input_fontsize != '') {
				$output .= 'font-size:'.$input_fontsize.'!important;';
			}
			if ($input_fontweight != '') {
				$output .= 'font-weight:'.$input_fontweight.'!important;';
			}
			$output .= '}';
		}


		$button_fontsize = stripslashes(essb_array_value('button_fontsize', $options));
		$button_fontweight = stripslashes(essb_array_value('button_fontweight', $options));
		if ($button_fontsize != '' || $button_fontweight != '') {
			$output .= '.'.$design_class.' .essb-userform-button { ';
			if ($button_fontsize != '') {
				$output .= 'font-size:'.$button_fontsize.'!important;';
			}
			if ($button_fontweight != '') {
				$output .= 'font-weight:'.$button_fontweight.'!important;';
			}
			$output .= '}';
		}


		// button styles
		$button_bg = stripslashes(essb_array_value('button_bg', $options));
		$button_text = stripslashes(essb_array_value('button_text', $options));
		if ($button_bg != '' || $button_text != '') {
			$output .= '.'.$design_class.' .essb-userform-button { ';
			if ($button_bg != '') {
				$output .= 'background:'.$button_bg.'!important;';
			}
			if ($button_text != '') {
				$output .= 'color:'.$button_text.'!important;';
			}
			$output .= '}';
		}

		// image section tune
		$image_padding = stripslashes(essb_array_value('image_padding', $options));
		$image_bgcolor = stripslashes(essb_array_value('image_bgcolor', $options));
		if ($image_padding != '' || $image_bgcolor != '') {
			$output .= '.'.$design_class.' .essb-userform-imagearea { ';
			if ($image_padding != '') {
				$output .= 'padding:'.$image_padding.'!important;';
			}
			if ($image_bgcolor != '') {
				$output .= 'background:'.$image_bgcolor.'!important;';
			}
			$output .= '}';
		}

		$form_image_width = stripslashes(essb_array_value('image_width', $options));
		$form_image_height = stripslashes(essb_array_value('image_height', $options));
		if ($form_image_width != '' || $form_image_height != '') {
			$output .= '.'.$design_class.' .essb-userform-imagearea img { ';
			if ($form_image_width != '') {
				$output .= 'width:'.$form_image_width.'!important;';
			}
			if ($form_image_height != '') {
				$output .= 'height:'.$form_image_height.'!important;';
			}

			$output .= 'display: inline-block; padding: 0; margin: 0 auto;';

			$output .= '}';
		}


		if ($output != '') {
			$output = '<style type="text/css">'.$output.'</style>';
		}

		return $output;
	}
}
