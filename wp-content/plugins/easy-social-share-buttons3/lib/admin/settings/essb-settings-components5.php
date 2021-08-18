<?php

function essb_component_network_selection($position = '', $options_group = 'essb_options', $show_all = false) {
	$active_networks = array();
	$essb_networks = essb_available_social_networks($show_all);
	
	if ($position == '') {
		$active_networks = essb_option_value('networks');
	}
	else {
		$active_networks = essb_option_value($position.'_networks');
	}
	
	$external_bridge = false;
	if (ESSBOptionsFramework::option_keys_to_settings($options_group) != '') {
		$active_networks = ESSBOptionsFramework::external_options_value($options_group, $position.'_networks');
		$external_bridge = true;
	}
	
	$salt = mt_rand();
	
	if (!is_array($active_networks)) {
		$active_networks = array();
	}
	
	echo '<ul class="essb-component-networkselect essb-sortable essb-componentkey-'.esc_attr($salt).' essb-component-networkselect-'.esc_attr($position).'" id="essb-componentkey-'.esc_attr($salt).'" data-position="'.esc_attr($position).'" data-group="'.esc_attr($options_group).'">';
	
	foreach ($active_networks as $network) {
	
		$current_network_name = isset($essb_networks[$network]) ? $essb_networks[$network]["name"] : $network;
		
		if ($position == '') {
			$user_network_name = essb_option_value('user_network_name_'.$network);
		
			if ($user_network_name == '') {
				$user_network_name = $current_network_name;
			}
		}
		else {
			$user_network_name = essb_option_value($position.'_'.$network.'_name');
		}
		
		if ($external_bridge) {
			$user_network_name = ESSBOptionsFramework::external_options_value($options_group, $position.'_'.$network.'_name');
		}
	
		echo '<li class="essb-admin-networkselect-single essb-network-color-'.esc_attr($network).'" data-network="'.esc_attr($network).'" data-key="'.esc_attr($salt).'">';
		if ($position != '') {
			echo '<input type="hidden" name="'.esc_attr($options_group).'['.esc_attr($position).'_networks][]" value="'.esc_attr($network).'"/>';
		}
		else {
			echo '<input type="hidden" name="'.esc_attr($options_group).'[networks][]" value="'.esc_attr($network).'"/>';
		}
		echo '<span class="essb-icon-remove fa fa-times" onclick="essbSettingsHelper.removeNetwork(this); return false;"></span>';
		echo '<span class="essb_icon essb_icon_'.esc_attr($network).'"></span>';
		echo '<span class="essb-sns-name">'.esc_html($current_network_name).'</span>';
		echo '<span class="essb-single-network-name">';

		if ($position != '') {
			echo esc_html__('Personalize text on button:', 'essb').'<br/><input type="text" class="input-element" name="'.esc_attr($options_group).'['.esc_attr($position).'_'.esc_attr($network).'_name]" value="'.esc_attr($user_network_name).'"/>';
		}
		else {
			echo esc_html__('Personalize text on button:', 'essb').'<br/><input type="text" class="input-element" name="essb_options_names['.esc_attr($network).']" value="'.esc_attr($user_network_name).'"/>';
		}
		echo '</span>';	
		echo '</li>';
	}

	$network = 'add';
	echo '<li class="essb-admin-networkselect-single essb-network-color-'.esc_attr($network).'" data-network="'.esc_attr($network).'" data-key="'.esc_attr($salt).'" onclick="essbSettingsHelper.startNetworkSelection(\''.$salt.'\'); return false;">';
	echo '<span class="essb_icon fa fa-plus-square"></span>';
	echo '<span class="essb-sns-name">'.esc_html__('Add more networks', 'essb').'</span>';
	echo '</li>';
	
	
	echo '</ul>';
}

function essb_component_base_dummy_share() {
	return array("url" => "", "title" => "", "image" => "", "description" => "", "twitter_user" => "",
			"twitter_hashtags" => "", "twitter_tweet" => "", "post_id" => 0, "user_image_url" => "", "title_plain" => "",
			'short_url_whatsapp' => '', 'short_url_twitter' => '', 'short_url' => '', 'pinterest_image' => "", "full_url" => "");
}

function essb_component_base_dummy_style($user_counter = false, $counter_pos = '', $total_counter_pos = '') {
	$style = array("button_style" => "button", "align" => "left", "button_width" => "auto", "counters" => false);

	if ($user_counter) {
		$style['show_counter'] = 1;
		$style['counters'] = true;
		if ($counter_pos != '') {
			$style['counter_pos'] = $counter_pos;
		}

		if ($total_counter_pos != '') {
			$style['total_counter_pos'] = $total_counter_pos;
		}
		$style['demo_counter'] = "yes";
	}
	else {
		$style['show_counter'] = 0;
		$style['counter_pos'] = 'hidden';
		$style['total_counter_pos'] = 'hidden';
	}

	$style['button_align'] = 'left';
	$style['counter_pos'] = 'hidden';
	$style['total_counter_hidden_till'] = '';
	$style['nospace'] = false;
	$style['full_url'] = false;
	$style['message_share_buttons'] = '';
	$style['message_share_before_buttons'] = '';
	$style['is_mobile'] = false;
	$style['amp'] = false;
	$style['native'] = false;
	$style['total_counter_afterbefore_text'] = '';

	return $style;
}

function essb_component_template_select($position = '', $options_group = 'essb_options', $show_buttons = '') {
	$value_field_id = 'style';
	// position 
	if ($position != '') {
		$value_field_id = $position.'_template';
	}
	$value_text_id = $value_field_id.'_text';
	
	// selected value
	$selected = essb_option_value('style');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_template');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}
	
	if ($selected == '') {
		$selected = '32';
	}
	$selected_name = '';
	
	$templates = essb_available_tempaltes4();
	foreach ($templates as $key => $name) {
		if ($key == $selected ) {
			$selected_name = $name;
		}
	}
	
	
	if ($show_buttons == 'pinterest') {
		echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-pintemplateselect" data-field-buttons="'.esc_attr($show_buttons).'">';
	}
	else {
		echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-templateselect" data-field-buttons="'.esc_attr($show_buttons).'">';
	}
	echo '<input type="hidden" id="essb_field_'.esc_attr($value_field_id).'" class="essb_field_'.esc_attr($value_field_id).'" value="'.esc_attr($selected).'" name="'.esc_attr($options_group).'['.esc_attr($value_field_id).']">';
	echo '<div class="inner" id="'.esc_attr($value_field_id).'_text">';
	echo $selected_name;
	echo '</div>';
	echo '<div class="picker"><i class="fa fa-ellipsis-h"></i></div>';
	echo '</div>';
}

function essb_component_base_template_selection($position = '', $field_id = '', $field_text_id = '', $buttons = array(), $button_texts = array()) {
	$list_of_templates = essb_available_tempaltes4();
	
	$selected = essb_option_value('style');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_template');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}
	
	if ($selected == '') {
		$selected = '32';
	}
	
	$order_buttons = $buttons;
	
	if (count($buttons) == 0) {
		$buttons = array('facebook', 'twitter');
		$order_buttons = $buttons;
	}
	
	if (count($button_texts) == 0) {
		$button_texts = array("facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google");
	}
	
	$button_style = essb_component_base_dummy_style();
	
	echo '<div class="essb-component-clickholder" data-field="'.esc_attr($field_id).'" data-field-text="'.esc_attr($field_text_id).'">';
	
	foreach ($list_of_templates as $key => $name) {
		
		$button_style['template'] = $key;
		
		echo '<div class="essb-component-clickselect'.($selected == $key ? ' active': '').' essb-template-select-'.esc_attr($key).'" data-value="'.esc_attr($key).'" data-text="'.esc_attr($name).'">';
		echo '<div class="inner-title">'.$name.'</div>';
		echo '<div class="inner-content">'.ESSBButtonHelper::draw_share_buttons(essb_component_base_dummy_share(), $button_style, $buttons, $order_buttons, $button_texts, "shortcode", "1112233").'</div>';
		echo '</div>';
	}
	
	echo '</div>';
}

function essb_component_base_button_style_selection($position = '', $pinterest_mode = false) {
	$essb_available_buttons_style = array();
	$essb_available_buttons_style ['button'] = esc_html__('Regular share buttons with icon & name/text', 'essb'); 
	$essb_available_buttons_style ['button_name'] = esc_html__('Share button with name/text only (no icon)', 'essb');
	$essb_available_buttons_style ['icon'] = esc_html__('Share button with icon only', 'essb');
	$essb_available_buttons_style ['icon_hover'] = esc_html__('Share button with icon and name/text appearing on hover', 'essb');
	$essb_available_buttons_style ['vertical'] = esc_html__('Vertical button', 'essb');
	
	$selected = essb_option_value('button_style');
	$template = essb_option_value('style');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_button_style');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}
	
	if ($selected == '') {
		$selected = 'button';
	}
	
	$buttons = array('facebook', 'twitter');
	$buttons_text = array('facebook' => 'Facebook', 'twitter' => 'Twitter');
	
	if ($pinterest_mode) {
		$buttons = array('pinterest');
		$buttons_text = array('pinterest' => 'Pin');
	}
	
	$button_style = essb_component_base_dummy_style();
	
	echo '<div class="essb-component-clickholder" data-field="" data-field-text="">';
	
	foreach ($essb_available_buttons_style as $key => $name) {
	
		$button_style['template'] = $template;
		$button_style['button_style'] = $key;
	
		echo '<div class="essb-component-clickselect'.($selected == $key ? ' active': '').' essb-template-select-'.esc_attr($key).'" data-value="'.esc_attr($key).'" data-text="'.esc_attr($name).'">';
		echo '<div class="inner-title">'.$name.'</div>';
		echo '<div class="inner-content">'.ESSBButtonHelper::draw_share_buttons(essb_component_base_dummy_share(), $button_style, $buttons, $buttons, $buttons_text, "shortcode", "1112233").'</div>';
		echo '</div>';
	}
	
	echo '</div>';
}

function essb_component_buttonstyle_select($position = '', $options_group = 'essb_options', $pinterest_mode = false) {
	$value_field_id = 'button_style';
	// position
	if ($position != '') {
		$value_field_id = $position.'_button_style';
	}
	$value_text_id = $value_field_id.'_text';

	// selected value
	$selected = essb_option_value('button_style');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_button_style');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}

	if ($selected == '') {
		$selected = 'button';
	}
	$selected_name = '';

	$essb_available_buttons_style = array();
	$essb_available_buttons_style ['button'] = esc_html__('Regular share buttons with icon & name/text', 'essb'); 
	$essb_available_buttons_style ['button_name'] = esc_html__('Share button with name/text only (no icon)', 'essb');
	$essb_available_buttons_style ['icon'] = esc_html__('Share button with icon only', 'essb');
	$essb_available_buttons_style ['icon_hover'] = esc_html__('Share button with icon and name/text appearing on hover', 'essb');
	$essb_available_buttons_style ['vertical'] = esc_html__('Vertical button', 'essb');
	foreach ($essb_available_buttons_style as $key => $name) {
		if ($key == $selected ) {
			$selected_name = $name;
		}
	}

	if ($pinterest_mode) {
		echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-pinbuttonstyleselect">';
	}
	else {
		echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-buttonstyleselect">';
	}
	echo '<input type="hidden" id="essb_field_'.esc_attr($value_field_id).'" class="essb_field_'.esc_attr($value_field_id).'" value="'.esc_attr($selected).'" name="'.esc_attr($options_group).'['.esc_attr($value_field_id).']">';
	echo '<div class="inner" id="'.esc_attr($value_field_id).'_text">';
	echo $selected_name;
	echo '</div>';
	echo '<div class="picker"><i class="fa fa-ellipsis-h"></i></div>';
	echo '</div>';
}


function essb_component_base_counter_position_selection($position = '', $field_id = '', $field_text_id = '') {
	$list_of_templates = essb_avaliable_counter_positions();

	$selected = essb_option_value('counter_pos');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_counter_pos');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}

	$template = essb_option_value('style');
	if ($selected == '') {
		$selected = 'hidden';
	}


	echo '<div class="essb-component-clickholder" data-field="'.esc_attr($field_id).'" data-field-text="'.esc_attr($field_text_id).'">';

	foreach ($list_of_templates as $key => $name) {

		$button_style = essb_component_base_dummy_style(true, $key, 'hidden');
		$button_style['template'] = $template;
		$button_style['counter_pos'] = $key;

		echo '<div class="essb-component-clickselect'.($selected == $key ? ' active': '').' essb-counterpos-select-'.esc_attr($key).'" data-value="'.esc_attr($key).'" data-text="'.esc_attr($name).'">';
		echo '<div class="inner-title">'.$name.'</div>';
		echo '<div class="inner-content">'.ESSBButtonHelper::draw_share_buttons(essb_component_base_dummy_share(), $button_style, array("facebook","twitter"), array("facebook","twitter","google"), array("facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google"), "shortcode", "1112233").'</div>';
		echo '</div>';
	}

	echo '</div>';
}

function essb_component_counterpos_select($position = '', $options_group = 'essb_options') {
	$value_field_id = 'counter_pos';
	// position
	if ($position != '') {
		$value_field_id = $position.'_counter_pos';
	}
	$value_text_id = $value_field_id.'_text';

	// selected value
	$selected = essb_option_value('counter_pos');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_counter_pos');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}

	if ($selected == '') {
		$selected = 'hidden';
	}
	$selected_name = '';

	$list = essb_avaliable_counter_positions();
	foreach ($list as $key => $name) {
		if ($key == $selected ) {
			$selected_name = $name;
		}
	}

	echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-counterposselect">';
	echo '<input type="hidden" id="essb_field_'.esc_attr($value_field_id).'" class="essb_field_'.esc_attr($value_field_id).'" value="'.esc_attr($selected).'" name="'.esc_attr($options_group).'['.esc_attr($value_field_id).']">';
	echo '<div class="inner" id="'.esc_attr($value_field_id).'_text">';
	echo $selected_name;
	echo '</div>';
	echo '<div class="picker"><i class="fa fa-ellipsis-h"></i></div>';
	echo '</div>';
}

// Total Counter Position
function essb_component_base_total_counter_position_selection($position = '', $field_id = '', $field_text_id = '') {
	$list_of_templates = essb_avaiable_total_counter_position();

	$selected = essb_option_value('total_counter_pos');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_total_counter_pos');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}

	$template = essb_option_value('style');
	if ($selected == '') {
		$selected = 'hidden';
	}


	echo '<div class="essb-component-clickholder" data-field="'.esc_attr($field_id).'" data-field-text="'.esc_attr($field_text_id).'">';

	foreach ($list_of_templates as $key => $name) {

		$button_style = essb_component_base_dummy_style(true, $key, 'hidden');
		$button_style['template'] = $template;
		$button_style['total_counter_pos'] = $key;

		echo '<div class="essb-component-clickselect'.($selected == $key ? ' active': '').' essb-counterpos-select-'.esc_attr($key).'" data-value="'.esc_attr($key).'" data-text="'.esc_attr($name).'">';
		echo '<div class="inner-title">'.$name.'</div>';
		echo '<div class="inner-content">'.ESSBButtonHelper::draw_share_buttons(essb_component_base_dummy_share(), $button_style, array("facebook","twitter"), array("facebook","twitter","google"), array("facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google"), "shortcode", "1112233").'</div>';
		echo '</div>';
	}

	echo '</div>';
}

function essb_component_totalcounterpos_select($position = '', $options_group = 'essb_options') {
	$value_field_id = 'total_counter_pos';
	// position
	if ($position != '') {
		$value_field_id = $position.'_total_counter_pos';
	}
	$value_text_id = $value_field_id.'_text';

	// selected value
	$selected = essb_option_value('total_counter_pos');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_total_counter_pos');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}

	if ($selected == '') {
		$selected = 'hidden';
	}
	$selected_name = '';

	$list = essb_avaiable_total_counter_position();
	foreach ($list as $key => $name) {
		if ($key == $selected ) {
			$selected_name = $name;
		}
	}

	echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-totalcounterposselect">';
	echo '<input type="hidden" id="essb_field_'.esc_attr($value_field_id).'" class="essb_field_'.esc_attr($value_field_id).'" value="'.esc_attr($selected).'" name="'.esc_attr($options_group).'['.esc_attr($value_field_id).']">';
	echo '<div class="inner" id="'.esc_attr($value_field_id).'_text">';
	echo $selected_name;
	echo '</div>';
	echo '<div class="picker"><i class="fa fa-ellipsis-h"></i></div>';
	echo '</div>';
}

function essb_component_options_group_select($field = '', $values = array(), $size = '', $default_value = '', $options_group = 'essb_options', $custom_class = '') {
	$value = essb_option_value($field);
	
	if ($default_value != '' && $value == '') {
		$value = $default_value;
	}
	
	if ($size != '') {
		$size = ' '.$size;
	}
	
	if ($custom_class != '') {
		$custom_class = ' '.$custom_class;
	}
	
	echo '<div class="essb-component-toggleselect essb-component-'.esc_attr($field).esc_attr($size).esc_attr($custom_class).'">';
	echo '<input type="hidden" name="'.esc_attr($options_group).'['.esc_attr($field).']" id="essb_options_'.esc_attr($field).'" value="'.esc_attr($value).'" class="toggleselect-holder"/>';
	
	foreach ($values as $key => $data) {
		$title = isset($data['title']) ? $data['title'] : '';
		$content = isset($data['content']) ? $data['content'] : '';
		$isText = isset($data['isText']) ? true: false;
		$customPadding = isset($data['padding']) ? $data['padding'] : '';
		
		if ($customPadding != '') {
			$customPadding = ' style="padding:'.$customPadding.'"';
		}
		
		if ($isText) {
			$content = '<span class="text">'.$content.'</span>';
		}
		
		echo '<span class="toggleselect-item toggleselect-item-'.esc_attr($field).esc_attr($key).($key == $value ? ' active': '').'" data-value="'.esc_attr($key).'" title="'.esc_attr($title).'"'.$customPadding.'>';
		echo $content;
		echo '</span>';
	}
	
	echo '</div>';
}

function essb_component_options_group_select_multiple($field = '', $values = array(), $size = '', $default_value = array(), $options_group = 'essb_options') {
	$value = essb_option_value($field);

	if (!is_array($default_value)) {
		$default_value = array();
	}
	
	if ($size != '') {
		$size = ' '.$size;
	}

	echo '<div class="essb-component-groupselect essb-component-'.esc_attr($field).esc_attr($size).'">';	

	foreach ($values as $key => $data) {
		$title = isset($data['title']) ? $data['title'] : '';
		$content = isset($data['content']) ? $data['content'] : '';
		$isText = isset($data['isText']) ? true: false;
		$customPadding = isset($data['padding']) ? $data['padding'] : '';

		if ($customPadding != '') {
			$customPadding = ' style="padding:'.$customPadding.'"';
		}

		if ($isText) {
			$content = '<span class="text">'.$content.'</span>';
		}

		$isChecked = in_array($key, $default_value);
		
		echo '<span class="toggleselect-item'.($isChecked ? ' active': '').'" data-value="'.esc_attr($key).'" title="'.esc_attr($title).'"'.$customPadding.'>';
		echo $content;
		echo '<input type="checkbox" name="'.esc_attr($options_group).'['.esc_attr($field).'][]" id="essb_options_'.esc_attr($field).'_'.esc_attr($key).'" value="'.esc_attr($key).'" class="toggleselect-holder" '.($isChecked ? 'checked="checked"': '').'/>';
		echo '</span>';
	}

	echo '</div>';
}

// Animations
function essb_component_base_animation_selection($position = '', $field_id = '', $field_text_id = '') {
	$list_of_templates = essb_available_animations(true);

	$selected = essb_option_value('css_animations');

	$template = essb_option_value('style');
	if ($selected == '') {
		$selected = '';
	}


	echo '<div class="essb-component-clickholder" data-field="'.esc_attr($field_id).'" data-field-text="'.esc_attr($field_text_id).'">';

	foreach ($list_of_templates as $key => $name) {

		$button_style = essb_component_base_dummy_style(false, $key, 'hidden');
		$button_style['template'] = $template;
		$button_style['button_animation'] = $key;

		echo '<div class="essb-component-clickselect'.($selected == $key ? ' active': '').' essb-counterpos-select-'.esc_attr($key).'" data-value="'.esc_attr($key).'" data-text="'.esc_attr($name).'">';
		echo '<div class="inner-title">'.$name.'</div>';
		echo '<div class="inner-content">'.ESSBButtonHelper::draw_share_buttons(essb_component_base_dummy_share(), $button_style, array("facebook","twitter"), array("facebook","twitter","google"), array("facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google"), "shortcode", "1112233").'</div>';
		echo '</div>';
	}

	echo '</div>';
}

function essb_component_animation_select($position = '', $options_group = 'essb_options') {
	$value_field_id = 'css_animations';
	// position
	if ($position != '') {
		$value_field_id = $position.'_css_animations';
	}
	$value_text_id = $value_field_id.'_text';

	// selected value
	$selected = essb_option_value('css_animations');
	if ($position != '') {
		$position_selected = essb_option_value($position.'_css_animations');
		if ($position_selected != '') {
			$selected = $position_selected;
		}
	}

	if ($selected == '') {
		$selected = '';
	}
	$selected_name = '';

	$list = essb_available_animations(true);
	foreach ($list as $key => $name) {
		if ($key == $selected ) {
			$selected_name = $name;
		}
	}

	echo '<div class="essb-popup-select essb-popup-select-'.esc_attr($value_field_id).'" data-field="'.esc_attr($value_field_id).'" data-field-text="'.esc_attr($value_text_id).'" data-field-window="#essb-animationsselect">';
	echo '<input type="hidden" id="essb_field_'.esc_attr($value_field_id).'" class="essb_field_'.esc_attr($value_field_id).'" value="'.esc_attr($selected).'" name="'.$options_group.'['.$value_field_id.']">';
	echo '<div class="inner" id="'.esc_attr($value_field_id).'_text">';
	echo $selected_name;
	echo '</div>';
	echo '<div class="picker"><i class="fa fa-ellipsis-h"></i></div>';
	echo '</div>';
}

function essb_component_single_position_select($positions, $field_id = '', $options_group = 'essb_options') {
	$value = essb_option_value($field_id);
	
	echo '<div class="essb-position-select essb-single-position-select">';
	
	foreach ($positions as $key => $data) {
		
		$image = isset($data['image']) ? $data['image'] : '';
		$label = isset($data['label']) ? $data['label'] : '';
		$desc = isset($data['desc']) ? $data['desc'] : '';
		$link = isset($data['link']) ? $data['link'] : '';
		
		$pathToImages = ESSB3_PLUGIN_URL.'/';
		if (strpos($image, 'http://') !== false || strpos($image, 'https://') !== false) {
			$pathToImages = '';
		}
		
		echo '<div class="essb-single essb-single-'.esc_attr($key).($key == $value ? ' active' : '').'" data-value="'.esc_attr($key).'">';
		echo '<div class="icon"><img src="'.esc_url($pathToImages.$image).'" title="'.esc_attr($label).'"/>';
		echo '<div class="active-mark" title="Active Position"><i class="ti-check-box"></i></div>';

		if ($link != '') {
			$link_parts = explode('|', $link);
			echo '<div class="customize" title="Personalize Display Options" data-menu="'.esc_attr($link_parts[0]).'" data-sub-menu="'.esc_attr($link_parts[1]).'" data-tab="'.esc_attr($link_parts[2]).'"><i class="ti-settings"></i></div>';
		}
		
		
		
		echo '</div>';
		echo '<div class="title">'.$label;
		
		if ($desc != '') {
			echo '<div class="description">'.$desc.'</div>';
		}
		echo '</div>';
		
		echo '</div>';
	}
	
	echo '<input type="hidden" name="'.$options_group.'['.$field_id.']" id="essb_component_'.$field_id.'" value="'.esc_attr($value).'" class="value-holder"/>';
	
	echo '</div>';
}

function essb_component_multi_position_select($positions, $field_id = '', $options_group = 'essb_options') {
	$value = essb_option_value($field_id);
	if (!is_array($value)) {
		$value = array();
	}

	echo '<div class="essb-position-select essb-multi-position-select">';

	foreach ($positions as $key => $data) {

		$image = isset($data['image']) ? $data['image'] : '';
		$label = isset($data['label']) ? $data['label'] : '';
		$desc = isset($data['desc']) ? $data['desc'] : '';
		$link = isset($data['link']) ? $data['link'] : '';
		$active = in_array($key, $value);

		$pathToImages = ESSB3_PLUGIN_URL.'/';
		if (strpos($image, 'http://') !== false || strpos($image, 'https://') !== false) {
			$pathToImages = '';
		}

		echo '<div class="essb-single essb-single-'.esc_attr($key).($active ? ' active' : '').'" data-value="'.esc_attr($key).'">';
		echo '<div class="icon"><img src="'.esc_url($pathToImages.$image).'" title="'.esc_attr($label).'"/>';
		echo '<div class="active-mark" title="Active Position"><i class="ti-check-box"></i></div>';

		if ($link != '') {
			$link_parts = explode('|', $link);
			echo '<div class="customize" title="Personalize Display Options" data-menu="'.esc_attr($link_parts[0]).'" data-sub-menu="'.esc_attr($link_parts[1]).'" data-tab="'.esc_attr($link_parts[2]).'"><i class="ti-settings"></i></div>';
		}


		echo '</div>';
		echo '<div class="title">'.$label;

		if ($desc != '') {
			echo '<div class="description">'.$desc.'</div>';
		}
		echo '</div>';

		echo '<input type="checkbox" name="'.$options_group.'['.$field_id.'][]" id="essb_component_'.$field_id.'_'.$key.'" value="'.$key.'" class="value-holder" '.($active ? 'checked="checked"' : '').'/>';
		
		echo '</div>';
	}

	echo '</div>';
}

/**
 * Generate the actual HTML code for working with the style manager on site
 * 
 * @param array $options
 */
function essb5_stylemanager_include_buttons($options = array()) {

	$element_options = isset($options['element_options']) ? $options['element_options'] : array();
	$position = isset($element_options['position']) ? $element_options['position'] : '';
	$show_save = isset($element_options['show_save']) ? $element_options['show_save'] : '';
	
	?>
<div class="essb-options-hint essb-options-hint-glowstyles">
	<div class="essb-options-hint-icon"><i class="fa32 ti-paint-roller"></i></div>
	<div class="essb-options-hint-withicon">
		<div class="content-part">
			<div class="essb-options-hint-title"><?php esc_html_e('Style Library', 'essb'); ?></div>
			<div class="essb-options-hint-desc"><?php esc_html_e('Save and reuse again already configured styles and network list. Saved in the library you can also move the style to a new site. Try also one of 40+ already configured styles if you wonder how to start.', 'essb'); ?></div>
		</div>
		<div class="essb-options-hint-buttons button-part">
			<a href="#" class="essb-style-apply" data-position="<?php echo esc_attr($position);?>"><i class="ti-ruler-pencil"></i> <span>Apply</span></a>
			<?php if ($show_save == 'true'): ?>
				<a href="#" class="essb-style-save" data-position="<?php echo esc_attr($position);?>"><i class="ti-save"></i> <span>Save</span></a>
			<?php endif; ?>
		</div>
		
		<span class="deactivate-link deactivate-styles-panel"><?php esc_html_e('Remove styles library', 'essb'); ?></span>
	</div>
</div>	
	<?php 
}

/**
 * Include the components for selecting/saving styles to the pre-set location inside menu. The
 * buttons will appear dynamically based on other settings
 * 
 * @param string $tab_id
 * @param string $menu_id
 * @param string $location
 * 
 * @since 5.9
 * @author appscreo
 * @package EasySocialShareButtons
 */
function essb5_stylemanager_include_menu($tab_id, $menu_id, $location = '', $show_save = '') {
	if (!essb_option_bool_value('deactivate_stylelibrary')) {
		ESSBOptionsStructureHelper::field_func($tab_id, $menu_id, 'essb5_stylemanager_include_buttons', '', '', '', array('position' => $location, 'show_save' => $show_save));
	}
}

/**
 * Register an advanced options small tile inside the menu
 * 
 * @param unknown_type $tab_id
 * @param unknown_type $menu_id
 * @param unknown_type $title
 * @param unknown_type $subtitle
 * @param unknown_type $description
 * @param unknown_type $center
 * @param unknown_type $button_center
 * @param unknown_type $icon
 * @param unknown_type $ao_option
 * @param unknown_type $ao_option_text
 * @param unknown_type $tag
 */
function essb5_menu_advanced_options_small_tile($tab_id = '', $menu_id = '', $title = '',
		$subtitle = '', $description = '', $center = 'false', $button_center = 'false',
		$icon = '', $ao_option = '', $ao_option_text = '', $ao_window_text = '', $tag = '', $custom_buttons = '') {

	$callback_opts = array(
			'title' => $title,
			'subtitle' => $subtitle,
			'description' => $description,
			'center' => $center,
			'button_center' => $button_center,
			'icon' => $icon,
			'ao_option' => $ao_option,
			'ao_option_text' => $ao_option_text,
			'ao_window_text' => $ao_window_text,
			'tag' => $tag,
			'custom_buttons' => $custom_buttons
	);
	
	ESSBOptionsStructureHelper::field_component($tab_id, $menu_id, 'essb5_advanced_options_small_settings_tile', 'false', $callback_opts);
}

/**
 * Generate a small advanced options tile. The tile will have a title, description, icon and callback button
 * to open the advanced options screen.
 * 
 * @param {array} $options Component drawing options
 * @since 5.9
 * @author appscreo
 * @package EasySocialShareButtons
 */
function essb5_advanced_options_small_settings_tile($parameters = array()) {
	$options = isset($parameters['element_options']) ? $parameters['element_options'] : array();
	$title = isset($options['title']) ? $options['title'] : '';
	$subtitle = isset($options['subtitle']) ? $options['subtitle'] : '';
	$description = isset($options['description']) ? $options['description'] : '';
	$center = isset($options['center']) ? $options['center'] : 'false';
	$button_center = isset($options['button_center']) ? $options['button_center'] : '';
	$icon = isset($options['icon']) ? $options['icon'] : '';
	$ao_option = isset($options['ao_option']) ? $options['ao_option'] : '';
	$ao_option_text = isset($options['ao_option_text']) ? $options['ao_option_text'] : esc_html__('Configure', 'essb');
	$ao_window_text = isset($options['ao_window_text']) ? $options['ao_window_text'] : '';
	$tag = isset($options['tag']) ? $options['tag'] : '';
	$custom_buttons = isset($options['custom_buttons']) ? $options['custom_buttons'] : '';
	
	echo '<div class="advancedoptions-tile advancedoptions-smalltile'.($center == 'true' ? ' center-c': '').'">';
	echo '<div class="advancedoptions-tile-head">'; // open head
	
	if ($title != '') {
		echo '<div class="advancedoptions-tile-head-title"><h3>'.$title.'</h3></div>';
	}
	
	if ($tag != '') {
		echo '<div class="advancedoptions-tile-head-tools"><span class="status tag">'.$tag.'</span></div>';
	}
	
	echo '</div>'; // closing head
	
	if ($icon != '') {
		echo '<div class="advnacedoptions-tile-icon"><i class="'.esc_attr($icon).'"></i></div>';
	}
	
	if ($subtitle != '') {
		echo '<div class="advnacedoptions-tile-subtitle"><h3>'.$subtitle.'</h3></div>';
	}
	
	if ($description != '') {
		echo '<div class="advancedoptions-tile-body">'.$description.'</div>';
	}
	
	if ($ao_option != '') {
		echo '<div class="advancedoptions-tile-foot'.($button_center == 'true'? ' center-b': '').'">';
		echo '<a href="#" class="essb-btn tile-config ao-option-callback" data-option="'.esc_attr($ao_option).'" data-window-title="'.esc_attr($ao_window_text).'"><i class="fa fa-cog"></i>'.$ao_option_text.'</a>';
		if ($custom_buttons != '') {
			echo $custom_buttons;
		}
		echo '</div>';
	}
	else if ($custom_buttons != '') {
		echo '<div class="advancedoptions-tile-foot'.($button_center == 'true'? ' center-b': '').'">';
		echo $custom_buttons;
		echo '</div>';
	}
	
	echo '</div>'; // closing tile
}

/**
 * Function will check a list of options to find if a value is set in one of them. If so
 * the function will return a true result
 * 
 * @param unknown_type $value_fields
 * @param unknown_type $switch_fields
 * @return {boolean}
 */
function essb5_has_setting_values($value_fields = array(), $switch_fields = array()) {
	$r = false;
	
	foreach ($value_fields as $field) {
		if (essb_option_value($field) != '') {
			$r = true;
			break;
		}
	}
	
	if (!$r) {
		foreach ($switch_fields as $field) {
			if (essb_option_bool_value($field)) {
				$r = true;
				break;
			}
		}
	}
	
	return $r;
}

/**
 * Generating settings row with editor field
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 * @param unknown_type $mode
 */
function essb5_draw_editor_option($field_id, $title = '', $description = '', $mode = 'htmlmixed', $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	
	$value = stripslashes($value);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	ESSBOptionsFramework::draw_editor_field($field_id, 'essb_options', $value, $mode);
	ESSBOptionsFramework::draw_options_row_end();
}

/**
 * Generating a settings row with input field
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 * @param unknown_type $full_width
 */
function essb5_draw_input_option($field_id, $title = '', $description = '', $full_width = false, $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	$value = stripslashes($value);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	ESSBOptionsFramework::draw_input_field($field_id, $full_width, 'essb_options', $value);
	ESSBOptionsFramework::draw_options_row_end();
}

/**
 * Generate textarea field
 * @param unknown $field_id
 * @param string $title
 * @param string $description
 * @param string $user_value
 * @param string $value
 */
function essb5_draw_textarea_option($field_id, $title = '', $description = '', $user_value = false, $value = '') {
    $value = $user_value ? $value : essb_option_value($field_id);
    $value = stripslashes($value);
    ESSBOptionsFramework::draw_options_row_start($title, $description);
    ESSBOptionsFramework::draw_textarea_field($field_id, 'essb_options', $value);
    ESSBOptionsFramework::draw_options_row_end();
}

/**
 * Generating a settings row with switch field
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 */
function essb5_draw_switch_option($field_id, $title = '', $description = '', $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	$value = stripslashes($value);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	ESSBOptionsFramework::draw_switch_field($field_id, 'essb_options', $value);
	ESSBOptionsFramework::draw_options_row_end();
}

function essb5_draw_help($title = '', $description = '', $buttons = array(), $in_section = 'false') {
	$element_options = array();
	$element_options['buttons'] = $buttons;
	
	ESSBOptionsFramework::draw_help($title, $description, $in_section, $element_options);
}

/**
 * Output a simple heading
 * 
 * @param unknown_type $title
 * @param unknown_type $level
 */
function essb5_draw_heading($title = '', $level = '5', $desc = '') {
	ESSBOptionsFramework::draw_heading($title, $level, '', $desc);
}

function essb5_draw_hint($title = '', $description = '', $icon = '', $style = '', $in_section = 'false') {
	ESSBOptionsFramework::draw_hint($title, $description, $icon, $style, $in_section);
}

function essb5_draw_network_select($id = '', $position = '', $all_networks = false) {
	essb_component_network_selection($position, 'essb_options', $all_networks);
}

/**
 * Generate a color picker field
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 * @param unknown_type $alpha
 */
function essb5_draw_color_option($field_id, $title = '', $description = '', $alpha = false, $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	$value = stripslashes($value);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	if ($alpha) {
		ESSBOptionsFramework::draw_acolor_field($field_id, 'essb_options', $value);
	}
	else {
		ESSBOptionsFramework::draw_color_field($field_id, 'essb_options', $value);
	}
	ESSBOptionsFramework::draw_options_row_end();
}

/**
 * Generate file selecting field
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 */
function essb5_draw_file_option($field_id = '', $title = '', $description = '', $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	$value = stripslashes($value);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	ESSBOptionsFramework::draw_fileselect_field($field_id, 'essb_options', $value);
	ESSBOptionsFramework::draw_options_row_end();
}

/**
 * 
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 * @param unknown_type $user_value
 * @param unknown_type $value
 */
function essb5_draw_toggle_option($field_id = '', $title = '', $description = '', $values = array(), $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	$value = stripslashes($value);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	ESSBOptionsFramework::draw_toggle_field($field_id, $values, 'essb_options', $value);
	ESSBOptionsFramework::draw_options_row_end();
}

/**
 * Generate select value field
 * 
 * @param unknown_type $field_id
 * @param unknown_type $title
 * @param unknown_type $description
 * @param unknown_type $values
 */
function essb5_draw_select_option($field_id = '', $title = '', $description = '', $values = array(), $user_value = false, $value = '') {
	$value = $user_value ? $value : essb_option_value($field_id);
	ESSBOptionsFramework::draw_options_row_start($title, $description);
	ESSBOptionsFramework::draw_select_field($field_id, $values, false, 'essb_options', $value);
	ESSBOptionsFramework::draw_options_row_end();
}

function essb5_draw_panel_start($title = '', $description = '', $icon = '', $element_options = array()) {
	ESSBOptionsFramework::draw_panel_start($title, $description, $icon, $element_options, 'essb_options');
}

function essb5_draw_panel_end() {
	ESSBOptionsFramework::draw_panel_end();
}

function essb5_generate_code_advanced_button($text = '', $icon = '', $ao_option = '', $class = '', $title = '', $reload = '', 
		$width = '', $hide_save = false) {
	$code = '';	
	$code .= '<a href="#" class="ao-options-btn '.esc_attr($class).'" data-option="'.esc_attr($ao_option).'" data-reload="'.esc_attr($reload).'" data-title="'.esc_attr($title).'" data-width="'.esc_attr($width).'" data-hidesave="'.($hide_save ? 'yes': 'no').'"><span class="essb_icon '.esc_attr($icon).'"></span><span>'.esc_html($text).'</span></a>';
	
	return $code;
}

function essb5_generate_code_advanced_setting_panel_open($title = '', $desc = '', $class = '') {
	$code = '';
	$code .= '<div class="ao-settings-section ao-settings-section-panel '.esc_attr($class).'">';
	
	if ($title != '' || $desc != '') {
	$code .= '<div class="panel-title">';
		if ($title != '') {
			$code .= '<span class="title">'.esc_html($title).$running_code_state.'</span>';
		}
		if ($desc != '') {
			$code .= '<span class="label">'.esc_html($desc).'</span>';
		}
		$code .= '</div>';
	}

	$code .= '<div class="panel-content">';
	
	return $code;
}

function essb5_generate_code_advanced_setting_panel_close() {
	$code = '</div></div>';
	
	return $code;
}

function essb5_generate_code_advanced_deactivate_panel($title = '', $desc = '', $field = '', $class = '', $button_text = '', $button_icon = '', $panel_icon = '') {
	if ($class == '') {
		$class = 'ao-'.$field;
	}
	$panel_class = $class . '-panel';
	
	if ($panel_icon != '') {
		$panel_icon = '<i class="ao-panel-icon '.esc_attr($panel_icon).'"></i>';
	}
	
	$code = '';
	$code .= '<div class="ao-settings-section ao-settings-section-deactivate '.esc_attr($panel_class).'">';
	
	$code .= '<div class="title-col">';
	if ($title != '') {
		$code .= '<span class="title">'.$panel_icon.esc_html($title).'</span>';
	}
	if ($desc != '') {
		$code .= '<span class="label">'.esc_html($desc).'</span>';
	}
	$code .= '</div>';
	$code .= '<div class="action-col">';
	$code .= '<a href="#" class="ao-options-btn-deactivate" data-field="'.esc_attr($field).'"><span class="essb_icon '.esc_attr($button_icon).'"></span><span>'.esc_html($button_text).'</span></a>';
	$code .= '</div>';
	$code .= '</div>';
	
	return $code;
}

function essb5_generate_code_advanced_activate_panel($title = '', $desc = '', $field = '', $class = '', $button_text = '', $button_icon = '', $panel_icon = '', $extra_panel_class = '', $custom_activate_value = '') {
	if ($class == '') {
		$class = 'ao-'.$field;
	}
	$panel_class = $class . '-panel';
	
	if ($extra_panel_class != '') {
		$panel_class .= ' ' . $extra_panel_class;
	}

	if ($panel_icon != '') {
		$panel_icon = '<i class="ao-panel-icon '.esc_attr($panel_icon).'"></i>';
	}

	$code = '';
	$code .= '<div class="ao-settings-section ao-settings-section-activate '.esc_attr($panel_class).'">';

	$code .= '<div class="title-col">';
	if ($title != '') {
		$code .= '<span class="title">'.$panel_icon.esc_html($title).'</span>';
	}
	if ($desc != '') {
		$code .= '<span class="label">'.esc_html($desc).'</span>';
	}
	$code .= '</div>';
	$code .= '<div class="action-col">';
	$code .= '<a href="#" class="ao-options-btn-activate" data-field="'.esc_attr($field).'" '.($custom_activate_value != '' ? 'data-uservalue="'.esc_attr($custom_activate_value).'"' : '').'><span class="essb_icon '.esc_attr($button_icon).'"></span><span>'.esc_html($button_text).'</span></a>';
	$code .= '</div>';
	$code .= '</div>';

	return $code;
}

function essb5_generate_help_link($help_link = '') {
	$code = '';
	
	if ($help_link != '') {
		$code .= '<div class="essb-help-hint">';
		$code .= '<a href="'.esc_url($help_link).'" target="_blank">'.esc_html__('Learn more', 'essb').' <i class="fa fa-external-link"></i></a>';
		$code .= '</div>';
	}
	
	return $code;
}

/**
 * Generate a settings panel with Advanced Options button
 * 
 * @param unknown_type $title
 * @param unknown_type $desc
 * @param unknown_type $ao_option
 * @param unknown_type $class
 * @param unknown_type $button_text
 * @param unknown_type $button_icon
 * @param unknown_type $reload
 * @return string
 */
function essb5_generate_code_advanced_settings_panel($title = '', $desc = '', $ao_option = '', 
		$class = '', $button_text = '', $button_icon = '', $reload = '', $width = '', 
		$running_tag = '', $panel_icon = '', $window_title = '', $hide_save = false, $help_link = '',
        $automation_action = '', $automation_message = '') {
	if ($class == '') {
		$class = 'ao-'.$ao_option;
	}
	$panel_class = $class . '-panel';
	
	if ($panel_icon != '') {
		$panel_icon = '<i class="ao-panel-icon '.esc_attr($panel_icon).'"></i>';
	}
	
	/**
	 * Provide a check for the active state of the panel option
	 */
	
	$has_check_settings = essb5_advanced_settings_running_checks($ao_option);
	$running_code_state = '';
	
	if ($has_check_settings['active']) {
		$advanced_option_running = essb5_has_setting_values($has_check_settings['value'], $has_check_settings['switch']);
		if ($advanced_option_running) {
			$running_code_state = '<span class="running">'.esc_html__('Running', 'essb').'</span>';
			
			$panel_class .= ' ao-active-panel';
			
			if (!empty($running_tag)) {
				$running_code_state = '<span class="running">'.$running_tag.'</span>';
			}
		}
	}
	
	if ($desc == '') {
	    $panel_class .= ' ao-settings-nodesc';
	}
	
	$code = '';
	$code .= '<div class="ao-settings-section '.esc_attr($panel_class).'">';
	$code .= '<div class="title-col">';
	if ($title != '') {
		$code .= '<span class="title">'.$panel_icon.esc_html($title).$running_code_state.'</span>';
	}
	if ($desc != '') {
		$code .= '<span class="label">'.esc_html($desc).'</span>';
	}
	
	if ($help_link != '' || $automation_action != '') {
		$code .= '<div class="help-hint">';
		if ($help_link != '') {
		  $code .= '<a href="'.esc_url($help_link).'" target="_blank" class="ao-internal-help-hint">'.esc_html__('Learn more', 'essb').' <i class="fa fa-external-link"></i></a>';
		}
		if ($automation_action != '') {
		    $code .= '<a href="#'.esc_attr($automation_action).'" class="ao-automation-action" data-automation="'.esc_attr($automation_action).'">'.$automation_message.' <i class="fa fa-bolt"></i></a>';
		}
		
		$code .= '</div>';
	}
	
	if ($window_title == '') { 
		$window_title = $title;
	}
	
	$code .= '</div>';
	$code .= '<div class="action-col">';
	$code .= essb5_generate_code_advanced_button($button_text, $button_icon, $ao_option, $class, $window_title, $reload, $width, $hide_save);
	$code .= '</div>';
	$code .= '</div>';
	
	return $code;
}

function essb5_advanced_settings_running_checks($ao_option = '') {
	$has_settings = false;
	$value_checks = array();
	$switch_checks = array();
	
	if ($ao_option == 'advanced-networks') {
		$has_settings = true;
		$switch_checks[] = 'activate_networks_manage';
	}
	
	if ($ao_option == 'advanced-networks-visibility') {
		$has_settings = true;
		$switch_checks[] = 'activate_networks_responsive';
	}
	
	if ($ao_option == 'avoid-negative-proof') {
		$has_settings = true;
		$switch_checks[] = 'social_proof_enable';
	}
	
	if ($ao_option == 'share-recovery') {
		$has_settings = true;
		$switch_checks[] = 'counter_recover_active';
	}
	
	if ($ao_option == 'adaptive-styles') {
		$has_settings = true;
		$switch_checks[] = 'activate_automatic_position';
		$switch_checks[] = 'activate_automatic_mobile';
		$switch_checks[] = 'activate_automatic_mobile_content';
	}
	
	if ($ao_option == 'share-fake') {
		$has_settings = true;
		$switch_checks[] = 'activate_fake_counters';
	}
	
	if ($ao_option == 'integration-mycred') {
		$has_settings = true;
		$switch_checks[] = 'mycred_activate';
		$switch_checks[] = 'mycred_activate_custom';
		$switch_checks[] = 'mycred_referral_activate';
		$switch_checks[] = 'mycred_referral_activate_shortcode';
	}
	
	if ($ao_option == 'integration-affiliatewp') {
		$has_settings = true;
		$switch_checks[] = 'affwp_active';
	}
	
	if ($ao_option == 'integration-affiliates') {
		$has_settings = true;
		$switch_checks[] = 'affs_active';
	}
	
	if ($ao_option == 'analytics') {
		$has_settings = true;
		$switch_checks[] = 'stats_active';
	}
	
	if ($ao_option == 'share-conversions') {
		$has_settings = true;
		$switch_checks[] = 'conversions_lite_run';
	}
	
	if ($ao_option == 'metrics-lite') {
		$has_settings = true;
		$switch_checks[] = 'esml_active';
	}
	
	if ($ao_option == 'share-google-analytics') {
		$has_settings = true;
		$switch_checks[] = 'activate_ga_tracking';
	}
	
	if ($ao_option == 'excerpts') {
		$has_settings = true;
		$switch_checks[] = 'display_excerpt';
	}
	
	if ($ao_option == 'style-builder') {
		$has_settings = true;
		$switch_checks[] = 'use_stylebuilder';
	}
	
	return array('active' => $has_settings, 'value' => $value_checks, 'switch' => $switch_checks);
}

