<?php

if (!function_exists('essb_get_shortcode_options_share_codes_loader')) {
	function essb_get_shortcode_options_share_codes_loader() {
		return true;		
	}
}

if (!function_exists('essb_get_shortcode_options_easy_popular_posts')) {
	function essb_get_shortcode_options_easy_popular_posts() {
		$r = array();
		
		$r['title'] = array('type' => 'text', 'title' => esc_html__('Title', 'essb'));
		$r['number'] = array('type' => 'text', 'title' => esc_html__('Number of posts', 'essb'), 'options' => array('size' => 'small'));
		$r['show_num'] = array('type' => 'checkbox', 'title' => esc_html__('Show source value', 'essb'));
		$r['show_num_text'] = array('type' => 'text', 'title' => esc_html__('Text describing value', 'essb'), 'description' => esc_html__('Example: shares, loves, likes', 'essb'));
		
		$listOfTypes = array(
				"shares" => "Number of shares (you should use share counter on site)", 
				"loves" => "Number of loves (require Love this button to be active)", 
				"views" => "Post views (you should already use the plugin post views extension");		
		$r['source'] = array('type' => 'select', 'title' => esc_html__('Value source', 'essb'),
				'options' => $listOfTypes);
		$r['same_cat'] = array('type' => 'checkbox', 'title' => esc_html__('From same category only', 'essb'), 'description' => esc_html__('Use this option if you plan to put the shortcode on the home page or archive pages. With this option set to Yes the shortcode will show only posts from the same category as the listing.', 'essb'));
		
		return $r;
	}
}

if (!function_exists('essb_get_shortcode_options_social_share_display')) {
	function essb_get_shortcode_options_social_share_display() {
		$r = array();
		
		$r['design'] = array('type' => 'select', 'title' => esc_html__('Custom display/position', 'essb'),
				'options' => essb5_get_custom_positions());
		$r['force'] = array('type' => 'checkbox', 'title' => esc_html__('Always show', 'essb'), 'description' => esc_html__('Display the custom display/position even when the position is not marked as active in the Where to Display menu.', 'essb'));
		return $r;
	}
}

if (!function_exists('essb_get_shortcode_options_share_action_button')) {
	function essb_get_shortcode_options_share_action_button() {
		$r = array();

		$r['text'] = array('type' => 'text', 'title' => esc_html__('Button text', 'essb'));
		$r['background'] = array('type' => 'text', 'title' => esc_html__('Background color', 'essb'), 'options' => array('size' => 'small'));
		$r['color'] = array('type' => 'text', 'title' => esc_html__('Text color', 'essb'), 'options' => array('size' => 'small'));
		
		$style = array('' => 'Button with background color', 'outline' => 'Outline button', 'modern' => 'Modern button');
		$r['style'] = array('type' => 'select', 'title' => esc_html__('Button style', 'essb'),
				'options' => $style);
		
		$listOfTypes = array(
				'' => 'Icon #1',
				'share-alt-square' => 'Icon #2',
				'share-alt' => 'Icon #3',
				'share-tiny' => 'Icon #4',
				'share-outline' => 'Icon #5'
		);
		
		$r['icon'] = array('type' => 'select', 'title' => esc_html__('Icon', 'essb'),
				'options' => $listOfTypes);
		
		$r['stretched'] = array('type' => 'checkbox', 'title' => esc_html__('Stretch button on entire content width', 'essb'));
		$r['total'] = array('type' => 'checkbox', 'title' => esc_html__('Show total number of shares', 'essb'));
		
		return $r;
	}
}

if (!function_exists('essb_get_shortcode_options_easy_total_shares')) {
	function essb_get_shortcode_options_easy_total_shares() {
		$r = array();

		$r['message'] = array('type' => 'text', 'title' => esc_html__('Text before total counter', 'essb'));
		$r['share_text'] = array('type' => 'text', 'title' => esc_html__('Text after the number of shares', 'essb'));
		$listOfTypes = array(
				'left' => 'Left',
				'center' => 'Center',
				'right' => 'Right'
		);
		
		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => $listOfTypes);
		$r['fullnumber'] = array('type' => 'checkbox', 'title' => esc_html__('Display the full number', 'essb'), 'description' => esc_html__('Show 1,000 instead of 1k', 'essb'));
		$r['inline'] = array('type' => 'checkbox', 'title' => esc_html__('Inline display', 'essb'), 'description' => esc_html__('Display the value inside the content without breaking to a new line', 'essb'));
		$r['url'] = array('type' => 'text', 'title' => esc_html__('Custom URL for getting shares', 'essb'), 'description' => esc_html__('The custom URL option will work only if on the page there are no other calls for share counters with different URLs. If you use the shortcode on a page where you put different codes with different URLs probably you will see one counter on all and it will be from the last updated instance.', 'essb'));
		$r['postid'] = array('type' => 'text', 'title' => esc_html__('Post ID', 'essb'), 'description' => esc_html__('Enter custom post ID where shortcode will read/store the share counter. If nothing is filled it will use the current post.', 'essb'));
		
		$all_networks = essb_available_social_networks();
		$source = array();
		foreach ($all_networks as $key => $data) {
			$source[$key] = isset($data['name']) ? $data['name'] : $key;
		}
		
		$r['networks'] = array('type' => 'networks', 'title' => esc_html__('Selected networks only', 'essb'), 'description' => esc_html__('Make a selection if you want to show the total value of selected networks only. Leave unchecked to show the total number of all networks.', 'essb'),
				'options' => $source);
		return $r;
	}
}

if (!function_exists('essb_get_shortcode_options_social_share')) {
	function essb_get_shortcode_options_social_share() {
		$r = array();
		
		$r['split1'] = array('type' => 'separator', 'title' => esc_html__('Share Buttons Style', 'essb'));
		$listOfTypes = array(
				'' => 'Default',
				'left' => 'Left',
				'center' => 'Center',
				'right' => 'Right',
				'stretched' => 'Stretched'
		);
		
		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => $listOfTypes);
		
		$listOfButtonStyles = array('' => 'Default');
		foreach (essb_avaiable_button_style() as $key => $name) {
			$listOfButtonStyles[$key] = $name;
		}
		
		$r['style'] = array('type' => 'select', 'title' => esc_html__('Style', 'essb'),
				'options' => $listOfButtonStyles);
		
		$listOfSizes = array(
				'' => 'Default',
				'xs' => 'Extra Small',
				's' => 'Small',
				'm' => 'Medium',
				'l' => 'Large',
				'xl' => 'Extra Large',
				'xxl' => 'Extra Extra Large'
		);
		$r['size'] = array('type' => 'select', 'title' => esc_html__('Size', 'essb'),
				'options' => $listOfSizes);
		
		$r['template'] = array('type' => 'select', 'title' => esc_html__('Template', 'essb'),
				'options' => essb_available_tempaltes4(true));
		
		$r['nospace'] = array('type' => 'select', 'title' => esc_html__('Without space between buttons', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		
		$listOfAnimations = array('' => 'Default');
		foreach (essb_available_animations() as $key => $text) {
			if ($key != '') {
				$listOfAnimations[$key] = $text;
			}
			else {
				$listOfAnimations['no'] = 'No amination';
			}
		}
		
		$r['animation'] = array('type' => 'select', 'title' => esc_html__('Animation', 'essb'),
				'options' => $listOfAnimations);
		
		$r['mobile_app'] = array('type' => 'select', 'title' => esc_html__('Device appearance', 'essb'),
				'options' => array('' => 'On any device', 'mobile' => esc_html__('On mobile only', 'essb'), 'desktop' => esc_html__('On desktop only', 'essb')));
		
		$r['split2'] = array('type' => 'separator', 'title' => esc_html__('Share Counter', 'essb'));
		$r['counters'] = array('type' => 'select', 'title' => esc_html__('Display share counters', 'essb'),
				'options' => array('' => 'Default', '0' => esc_html__('No', 'essb'), '1' => esc_html__('Yes', 'essb')));
		
		$listOfCounterPos = array('' => 'Default');
		foreach (essb_avaliable_counter_positions() as $key => $text) {
			$listOfCounterPos[$key] = $text;
		}
		
		$r['counter_pos'] = array('type' => 'select', 'title' => esc_html__('Button counter position', 'essb'),
				'options' => $listOfCounterPos);
		
		$listOfTotalCounterPos = array('' => 'Default');
		foreach (essb_avaiable_total_counter_position() as $key => $text) {
			$listOfTotalCounterPos[$key] = $text;
		}
		
		$r['total_counter_pos'] = array('type' => 'select', 'title' => esc_html__('Total counter position', 'essb'),
				'options' => $listOfTotalCounterPos);
		
		$r['split3'] = array('type' => 'separator', 'title' => esc_html__('Social Networks', 'essb'));
		$all_networks = essb_available_social_networks();
		$source = array();
		foreach ($all_networks as $key => $data) {
			$source[$key] = isset($data['name']) ? $data['name'] : $key;
		}
		
		$r['buttons'] = array('type' => 'networks', 'title' => esc_html__('Selected networks only', 'essb'), 'description' => esc_html__('Make a selection only if you need to show a custom list of social networks with this shortcode.', 'essb'),
				'options' => $source);
		return $r;
	}	
}

if (!function_exists('essb_get_shortcode_options_easy_social_share')) {
	function essb_get_shortcode_options_easy_social_share() {
		$r = array();
		
		/**
		 * Button style
		 */
		$r['split1'] = array('type' => 'separator', 'title' => esc_html__('Share Buttons Style', 'essb'));
		$listOfTypes = array(
				'' => 'Default',
				'left' => 'Left',
				'center' => 'Center',
				'right' => 'Right',
				'stretched' => 'Stretched'
		);
		
		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => $listOfTypes);
		
		$listOfButtonStyles = array('' => 'Default');
		foreach (essb_avaiable_button_style() as $key => $name) {
			$listOfButtonStyles[$key] = $name;
		}
		
		$r['style'] = array('type' => 'select', 'title' => esc_html__('Style', 'essb'),
				'options' => $listOfButtonStyles);
		
		$listOfSizes = array(
				'' => 'Default',
				'xs' => 'Extra Small',
				's' => 'Small',
				'm' => 'Medium',
				'l' => 'Large',
				'xl' => 'Extra Large',
				'xxl' => 'Extra Extra Large'
		);
		$r['size'] = array('type' => 'select', 'title' => esc_html__('Size', 'essb'),
				'options' => $listOfSizes);
		
		$r['template'] = array('type' => 'select', 'title' => esc_html__('Template', 'essb'),
				'options' => essb_available_tempaltes4(true));
		
		$r['nospace'] = array('type' => 'select', 'title' => esc_html__('Without space between buttons', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		
		$listOfAnimations = array('' => 'Default');
		foreach (essb_available_animations() as $key => $text) {
			if ($key != '') {
				$listOfAnimations[$key] = $text;
			}
			else {
				$listOfAnimations['no'] = 'No amination';
			}
		}
		
		$r['animation'] = array('type' => 'select', 'title' => esc_html__('Animation', 'essb'),
				'options' => $listOfAnimations);
		
		$r['mobile_app'] = array('type' => 'select', 'title' => esc_html__('Device appearance', 'essb'),
				'options' => array('' => 'On any device', 'mobile' => esc_html__('On mobile only', 'essb'), 'desktop' => esc_html__('On desktop only', 'essb')));

		$r['message'] = array('type' => 'select', 'title' => esc_html__('Show message above share buttons', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		
		$r['native'] = array('type' => 'select', 'title' => esc_html__('Show native buttons (when configured)', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		
		/**
		 * Share Counter
		 */
		
		$r['split2'] = array('type' => 'separator', 'title' => esc_html__('Share Counter', 'essb'));
		$r['counters'] = array('type' => 'select', 'title' => esc_html__('Display share counters', 'essb'),
				'options' => array('' => 'Default', '0' => esc_html__('No', 'essb'), '1' => esc_html__('Yes', 'essb')));
		
		$listOfCounterPos = array('' => 'Default');
		foreach (essb_avaliable_counter_positions() as $key => $text) {
			$listOfCounterPos[$key] = $text;
		}
		
		$r['counter_pos'] = array('type' => 'select', 'title' => esc_html__('Button counter position', 'essb'),
				'options' => $listOfCounterPos);
		
		$listOfTotalCounterPos = array('' => 'Default');
		foreach (essb_avaiable_total_counter_position() as $key => $text) {
			$listOfTotalCounterPos[$key] = $text;
		}
		
		$r['total_counter_pos'] = array('type' => 'select', 'title' => esc_html__('Total counter position', 'essb'),
				'options' => $listOfTotalCounterPos);
		
		/**
		 * Full width share buttons
		 */
		$r['split4'] = array('type' => 'separator', 'title' => esc_html__('Full width button style', 'essb'));
		$r['fullwidth'] = array('type' => 'select', 'title' => esc_html__('Enable full width', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		$r['fullwidth_fix'] = array('type' => 'text', 'title' => esc_html__('Single button width correction', 'essb'), 'options' => array('size' => 'small'));
		$r['fullwidth_first'] = array('type' => 'text', 'title' => esc_html__('Custom width of the first button', 'essb'), 'options' => array('size' => 'small'));
		$r['fullwidth_second'] = array('type' => 'text', 'title' => esc_html__('Custom width of the second button', 'essb'), 'options' => array('size' => 'small'));
		
		/**
		 * Fixed width
		 */
		$r['split5'] = array('type' => 'separator', 'title' => esc_html__('Fixed width button style', 'essb'));
		$r['fixedwidth'] = array('type' => 'select', 'title' => esc_html__('Enable fixed width', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		$r['fixedwidth_px'] = array('type' => 'text', 'title' => esc_html__('Single button width correction', 'essb'), 'options' => array('size' => 'small'));
		$r['fixedwidth_align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => array('' => esc_html__('Center', 'essb'), 'left' => esc_html__('Left', 'essb'), 'right' => esc_html__('Right', 'essb')));

		/**
		 * Columns
		 */
		$r['split6'] = array('type' => 'separator', 'title' => esc_html__('Columns button style', 'essb'));
		$r['column'] = array('type' => 'select', 'title' => esc_html__('Enable display in columns', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		$r['columns'] = array('type' => 'text', 'title' => esc_html__('Number of columns', 'essb'), 'options' => array('size' => 'small'));

		/**
		 * Flex width
		 */
		$r['split7'] = array('type' => 'separator', 'title' => esc_html__('Flex width button style', 'essb'));
		$r['flex'] = array('type' => 'select', 'title' => esc_html__('Enable flex width display', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		
		/**
		 * Share message customization
		 */
		$r['split8'] = array('type' => 'separator', 'title' => esc_html__('Customize Shared Information', 'essb'));
		$r['url'] = array('type' => 'text', 'title' => esc_html__('Custom share URL', 'essb'), 'description' => esc_html__('Optional setup custom URL that shortcode will share. Keep in mind that most of the social networks read the shared information from the URL only. The message and image options are optional and only for networks like Pinterest or mobile messengers.', 'essb'));
		$r['text'] = array('type' => 'text', 'title' => esc_html__('Custom share message', 'essb'), 'description' => esc_html__('Supported by very networks only. Almost all of the social networks read the message from the social share optimization tags on the URL you are sharing.', 'essb'));		
		$r['twitter_user'] = array('type' => 'text', 'title' => esc_html__('Twitter username', 'essb'), 'description' => esc_html__('Optional when Twitter is used only. Enter without the @ - example: appscreo.', 'essb'));
		$r['twitter_hashtags'] = array('type' => 'text', 'title' => esc_html__('Twitter hashtags', 'essb'), 'description' => esc_html__('Optional when Twitter is used only. Enter without the # - example: WordPress,blogging.', 'essb'));
		$r['twitter_tweet'] = array('type' => 'textarea', 'title' => esc_html__('Custom tweet', 'essb'), 'description' => esc_html__('Optional when Twitter is used only.', 'essb'));
		$r['postid'] = array('type' => 'text', 'title' => esc_html__('Post ID', 'essb'), 'description' => esc_html__('Enter custom post ID where shortcode will read/store the share counter. If nothing is filled it will use the current post.', 'essb'));
		$r['noaffiliate'] = array('type' => 'checkbox', 'title' => esc_html__('Don\'t generate an affiliate link for the URL (when the option is enabled in the settings)', 'essb'));
		
		$r['split9'] = array('type' => 'separator', 'title' => esc_html__('Show as Position', 'essb'));
		$r['sidebar'] = array('type' => 'checkbox', 'title' => esc_html__('Display as sidebar', 'essb'));
		$r['sidebar_pos'] = array('type' => 'select', 'title' => esc_html__('Sidebar position on screen', 'essb'),
				'options' => array('' => 'Default', 'left' => esc_html__('Left', 'essb'), 'right' => esc_html__('Right', 'essb')));
		$r['popup'] = array('type' => 'checkbox', 'title' => esc_html__('Display as pop-up', 'essb'));
		$r['popafter'] = array('type' => 'text', 'title' => esc_html__('Delay pop-up with (seconds)', 'essb'), 'options' => array('size' => 'small'));
		$r['popup'] = array('type' => 'checkbox', 'title' => esc_html__('Display as pop-up', 'essb'));
		$r['float'] = array('type' => 'checkbox', 'title' => esc_html__('Display as float from the top of content', 'essb'));
		$r['postfloat'] = array('type' => 'checkbox', 'title' => esc_html__('Display as content vertical float', 'essb'));
		$r['topbar'] = array('type' => 'checkbox', 'title' => esc_html__('Display as top bar', 'essb'));
		$r['bottombar'] = array('type' => 'checkbox', 'title' => esc_html__('Display as bottom bar', 'essb'));

		$r['point'] = array('type' => 'checkbox', 'title' => esc_html__('Display as point', 'essb'));
		$r['point_type'] = array('type' => 'select', 'title' => esc_html__('Sidebar position on screen', 'essb'),
				'options' => array('' => 'Default', 'simple' => esc_html__('Simple', 'essb'), 'advanced' => esc_html__('Advanced', 'essb')));
		
		
		$r['mobilebar'] = array('type' => 'checkbox', 'title' => esc_html__('Display as mobile bottom bar', 'essb'));
		$r['mobilebuttons'] = array('type' => 'checkbox', 'title' => esc_html__('Display as mobile buttons bar', 'essb'));
		$r['mobilepoint'] = array('type' => 'checkbox', 'title' => esc_html__('Display as mobile point', 'essb'));
		
		
		$r['hide_mobile'] = array('type' => 'select', 'title' => esc_html__('Hide on mobile devices', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		$r['only_mobile'] = array('type' => 'select', 'title' => esc_html__('Show only on mobile devices', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		/**
		 * Social networks
		 */
		$r['split3'] = array('type' => 'separator', 'title' => esc_html__('Social Networks', 'essb'));
		$all_networks = essb_available_social_networks();
		$source = array();
		foreach ($all_networks as $key => $data) {
			$source[$key] = isset($data['name']) ? $data['name'] : $key;
		}
		
		$r['buttons'] = array('type' => 'networks', 'title' => esc_html__('Selected networks only', 'essb'), 'description' => esc_html__('Make a selection only if you need to show a custom list of social networks with this shortcode.', 'essb'),
				'options' => $source);
		
		$r['morebutton'] = array('type' => 'select', 'title' => esc_html__('More button function', 'essb'),
				'options' => array (""=>"", "1" => "Display all active networks after more button", "2" => "Display all social networks as popup", "3" => "Display only active social networks as popup" ));
		$r['morebutton_icon'] = array('type' => 'select', 'title' => esc_html__('More button icon', 'essb'),
				'options' => array (""=>"", "plus" => "Plus icon", "dots" => "Dots icon"));
		

		$r['sharebtn_func'] = array('type' => 'select', 'title' => esc_html__('Share button function', 'essb'),
				'options' => array (""=>"", "1" => "Display all active networks after share button", "2" => "Display all social networks as popup", "3" => "Display only active social networks as popup" ));
		$r['sharebtn_style'] = array('type' => 'select', 'title' => esc_html__('Share button style', 'essb'),
				'options' => array ("" => "", "icon"=> "Icon", "button" => "Button", "text" => "Text"));
		$r['sharebtn_icon'] = array('type' => 'select', 'title' => esc_html__('Share button icon', 'essb'),
				'options' => array (""=> "", "plus" => "Plus", "dots" => "Dots", "share" => "Share icon #1", "share-alt-square" => "Share icon #2", "share-alt" => "Share icon #3", "share-tiny" => "Share icon #4", "share-outline" => "Share icon #5" ));
		$r['sharebtn_counter'] = array('type' => 'select', 'title' => esc_html__('Share button counter position', 'essb'),
				'options' => array (""=>"", "hidden" => "No counter", "inside" => "Inside button without text", "insidename" => "Inside button after text", "insidebeforename" => "Inside button before text", "topn" => "Top", "bottom" => "Bottom"));
				
		$r['split9'] = array('type' => 'separator', 'title' => esc_html__('Network Names', 'essb'));
		
		foreach ($source as $key => $name) {
			$r[$key . '_text'] = array('type' => 'text', 'title' => $name);
		}
		
		return $r;
	}
}


if (!function_exists('essb_get_shortcode_options_easy_social_share_popup')) {
	function essb_get_shortcode_options_easy_social_share_popup() {
		$r = array();

		/**
		 * Button style
		 */
		$r['split1'] = array('type' => 'separator', 'title' => esc_html__('Share Buttons Style', 'essb'));
		$listOfTypes = array(
				'' => 'Default',
				'left' => 'Left',
				'center' => 'Center',
				'right' => 'Right',
				'stretched' => 'Stretched'
		);

		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => $listOfTypes);

		$listOfButtonStyles = array('' => 'Default');
		foreach (essb_avaiable_button_style() as $key => $name) {
			$listOfButtonStyles[$key] = $name;
		}

		$r['style'] = array('type' => 'select', 'title' => esc_html__('Style', 'essb'),
				'options' => $listOfButtonStyles);

		$listOfSizes = array(
				'' => 'Default',
				'xs' => 'Extra Small',
				's' => 'Small',
				'm' => 'Medium',
				'l' => 'Large',
				'xl' => 'Extra Large',
				'xxl' => 'Extra Extra Large'
		);
		$r['size'] = array('type' => 'select', 'title' => esc_html__('Size', 'essb'),
				'options' => $listOfSizes);

		$r['template'] = array('type' => 'select', 'title' => esc_html__('Template', 'essb'),
				'options' => essb_available_tempaltes4(true));

		$r['nospace'] = array('type' => 'select', 'title' => esc_html__('Without space between buttons', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));

		$listOfAnimations = array('' => 'Default');
		foreach (essb_available_animations() as $key => $text) {
			if ($key != '') {
				$listOfAnimations[$key] = $text;
			}
			else {
				$listOfAnimations['no'] = 'No amination';
			}
		}

		$r['animation'] = array('type' => 'select', 'title' => esc_html__('Animation', 'essb'),
				'options' => $listOfAnimations);

		$r['mobile_app'] = array('type' => 'select', 'title' => esc_html__('Device appearance', 'essb'),
				'options' => array('' => 'On any device', 'mobile' => esc_html__('On mobile only', 'essb'), 'desktop' => esc_html__('On desktop only', 'essb')));

		$r['message'] = array('type' => 'select', 'title' => esc_html__('Show message above share buttons', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));

		$r['native'] = array('type' => 'select', 'title' => esc_html__('Show native buttons (when configured)', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));

		/**
		 * Share Counter
		 */

		$r['split2'] = array('type' => 'separator', 'title' => esc_html__('Share Counter', 'essb'));
		$r['counters'] = array('type' => 'select', 'title' => esc_html__('Display share counters', 'essb'),
				'options' => array('' => 'Default', '0' => esc_html__('No', 'essb'), '1' => esc_html__('Yes', 'essb')));

		$listOfCounterPos = array('' => 'Default');
		foreach (essb_avaliable_counter_positions() as $key => $text) {
			$listOfCounterPos[$key] = $text;
		}

		$r['counter_pos'] = array('type' => 'select', 'title' => esc_html__('Button counter position', 'essb'),
				'options' => $listOfCounterPos);

		$listOfTotalCounterPos = array('' => 'Default');
		foreach (essb_avaiable_total_counter_position() as $key => $text) {
			$listOfTotalCounterPos[$key] = $text;
		}

		$r['total_counter_pos'] = array('type' => 'select', 'title' => esc_html__('Total counter position', 'essb'),
				'options' => $listOfTotalCounterPos);

		/**
		 * Full width share buttons
		 */
		$r['split4'] = array('type' => 'separator', 'title' => esc_html__('Pop-up settings', 'essb'));
		$r['popup_title'] = array('type' => 'text', 'title' => esc_html__('Window title', 'essb'));
		$r['popup_message'] = array('type' => 'text', 'title' => esc_html__('Window message', 'essb'));
		$r['popup_percent'] = array('type' => 'text', 'title' => esc_html__('Show after percent of content', 'essb'), 'options' => array('size' => 'small'));
		$r['popup_end'] = array('type' => 'checkbox', 'title' => esc_html__('Show at the end of screen', 'essb'));
		
		/**
		 * Share message customization
		 */
		$r['split8'] = array('type' => 'separator', 'title' => esc_html__('Customize Shared Information', 'essb'));
		$r['url'] = array('type' => 'text', 'title' => esc_html__('Custom share URL', 'essb'), 'description' => esc_html__('Optional setup custom URL that shortcode will share. Keep in mind that most of the social networks read the shared information from the URL only. The message and image options are optional and only for networks like Pinterest or mobile messengers.', 'essb'));
		$r['text'] = array('type' => 'text', 'title' => esc_html__('Custom share message', 'essb'), 'description' => esc_html__('Supported by very networks only. Almost all of the social networks read the message from the social share optimization tags on the URL you are sharing.', 'essb'));
		$r['twitter_user'] = array('type' => 'text', 'title' => esc_html__('Twitter username', 'essb'), 'description' => esc_html__('Optional when Twitter is used only. Enter without the @ - example: appscreo.', 'essb'));
		$r['twitter_hashtags'] = array('type' => 'text', 'title' => esc_html__('Twitter hashtags', 'essb'), 'description' => esc_html__('Optional when Twitter is used only. Enter without the # - example: WordPress,blogging.', 'essb'));
		$r['twitter_tweet'] = array('type' => 'textarea', 'title' => esc_html__('Custom tweet', 'essb'), 'description' => esc_html__('Optional when Twitter is used only.', 'essb'));

		/**
		 * Social networks
		 */
		$r['split3'] = array('type' => 'separator', 'title' => esc_html__('Social Networks', 'essb'));
		$all_networks = essb_available_social_networks();
		$source = array();
		foreach ($all_networks as $key => $data) {
			$source[$key] = isset($data['name']) ? $data['name'] : $key;
		}

		$r['buttons'] = array('type' => 'networks', 'title' => esc_html__('Selected networks only', 'essb'), 'description' => esc_html__('Make a selection only if you need to show a custom list of social networks with this shortcode.', 'essb'),
				'options' => $source);

		$r['morebutton'] = array('type' => 'select', 'title' => esc_html__('More button function', 'essb'),
				'options' => array (""=>"", "1" => "Display all active networks after more button", "2" => "Display all social networks as popup", "3" => "Display only active social networks as popup" ));
		$r['morebutton_icon'] = array('type' => 'select', 'title' => esc_html__('More button icon', 'essb'),
				'options' => array (""=>"", "plus" => "Plus icon", "dots" => "Dots icon"));


		$r['sharebtn_func'] = array('type' => 'select', 'title' => esc_html__('Share button function', 'essb'),
				'options' => array (""=>"", "1" => "Display all active networks after share button", "2" => "Display all social networks as popup", "3" => "Display only active social networks as popup" ));
		$r['sharebtn_style'] = array('type' => 'select', 'title' => esc_html__('Share button style', 'essb'),
				'options' => array ("" => "", "icon"=> "Icon", "button" => "Button", "text" => "Text"));
		$r['sharebtn_icon'] = array('type' => 'select', 'title' => esc_html__('Share button icon', 'essb'),
				'options' => array (""=> "", "plus" => "Plus", "dots" => "Dots", "share" => "Share icon #1", "share-alt-square" => "Share icon #2", "share-alt" => "Share icon #3", "share-tiny" => "Share icon #4", "share-outline" => "Share icon #5" ));
		$r['sharebtn_counter'] = array('type' => 'select', 'title' => esc_html__('Share button counter position', 'essb'),
				'options' => array (""=>"", "hidden" => "No counter", "inside" => "Inside button without text", "insidename" => "Inside button after text", "insidebeforename" => "Inside button before text", "topn" => "Top", "bottom" => "Bottom"));

		$r['split9'] = array('type' => 'separator', 'title' => esc_html__('Network Names', 'essb'));

		foreach ($source as $key => $name) {
			$r[$key . '_text'] = array('type' => 'text', 'title' => $name);
		}

		return $r;
	}
}