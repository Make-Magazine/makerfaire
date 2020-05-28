<?php
/**
 * Creating the Click-to-Tweet shortcode from the plugin
 * 
 * @param unknown_type $atts
 * @return string
 */
function essb_ctt_shortcode($atts) {
	$default_options = array ( 
			'tweet' => '',
			'via' => 'yes',
			'url' => '',
			'nofollow' => 'no',
			'user' => '',
			'hashtags' => '',
			'usehashtags' => 'yes',
			'template' => '',
			'image' => '' );
	
	$atts = shortcode_atts ( $default_options, $atts );
	
	$handle = $atts['user'];
	$handle_code = '';
	$template = '';
	
	if ($handle == '' && essb_sanitize_option_value('ctt_user') != '') {
		$handle = essb_sanitize_option_value('ctt_user');
		$atts['via'] = 'yes';
	}

	if (! empty ( $handle ) && $atts['via'] != 'no') {
		$handle_code = "&amp;via=" . $handle . "&amp;related=" . $handle;
	} 
	else {
		$handle_code = '';
		$handle = '';
	}
	
	if ($atts['hashtags'] == '' && essb_sanitize_option_value('ctt_hashtags') != '') {
		$atts['hashtags'] = essb_sanitize_option_value('ctt_hashtags');
		$atts['usehashtags'] = 'yes';
	}
	
	if ($atts['usehashtags'] != 'no' && $atts['hashtags'] != '') {
		$handle_code .= "&amp;hashtags=".$atts['hashtags'];
	}
	
	
	if ($atts['template'] != '') {
		$template = ' essb-ctt-'.esc_attr($atts['template']);
	}
	else {
		$setup_template = essb_sanitize_option_value('cct_template');
		if ($setup_template != '') {
			$template = ' essb-ctt-'.esc_attr($setup_template);
		}
	}
	
	if (essb_option_bool_value('cct_hide_mobile')) {
		$template .= ' essb_mobile_hidden';
	}
	
	$text = $atts['tweet'];
	
	$post_url = get_permalink();
	$short_url = '';
	$automated_url = false;
	// @since 3.4 - fix problem with missing url in click-to-tweet
	if ($atts['url'] == '' && essb_option_bool_value('cct_url')) {
		$atts['url'] = $post_url;
		$automated_url = true;
		
		if (essb_option_bool_value('shorturl_activate' )) {
			$provider = essb_option_value('shorturl_type');
			$shorturl_bitlyuser = essb_option_value('shorturl_bitlyuser');
			$shorturl_bitlyapi = essb_option_value('shorturl_bitlyapi');
			essb_helper_maybe_load_feature('short-url');
			$short_url = essb_short_url ( $post_url, $provider, get_the_ID (), $shorturl_bitlyuser, $shorturl_bitlyapi );
		}
	}
	else if ($atts['url'] == '' && !essb_option_bool_value('cct_url')) {
		$atts['url'] = 'no';
		$automated_url = false;
	}
	
	// 7.0.3
	// Fixing the missing short URL in the Tweet
	if ($short_url != '' && $automated_url) {
	    $atts['url'] = $short_url;
	}
	
	if (filter_var ( $atts['url'], FILTER_VALIDATE_URL )) {
		
		$bcttURL = '&amp;url=' . esc_url($atts['url']);
	
	} 
	elseif ($atts['url'] != 'no') {
		
			if ($short_url != '') {
				$bcttURL = '&amp;url=' . esc_url($short_url).'&amp;counturl='.esc_url($post_url);
			}
			else {
				$bcttURL = '&amp;url=' . esc_url($post_url);
			}
	
	} 
	else {
		$bcttURL = '';
	}
	
	$bcttBttn = esc_html__('Click to Tweet', 'essb');
	$user_text = essb_option_value('translate_clicktotweet');
	if ($user_text != '') {
		$bcttBttn = $user_text;
	}
	
	$link_short = $text;
	if ($atts['image'] != '') {
		$link_short .= ' '.$atts['image'];
	}
	
	$rel = $atts['nofollow'] != 'no' ? 'rel="nofollow"' : '';	
	
	if (! is_feed ()) {
		
		return "<div class='essb-ctt".esc_attr($template)."' onclick=\"window.open('https://twitter.com/intent/tweet?text=" . urlencode ( $link_short ) . $handle_code . $bcttURL . "', 'essb_share_window', 'height=300,width=500,resizable=1,scrollbars=yes');\">
			<span class='essb-ctt-quote'>
			" . $text . "
			</span>
			<span class='essb-ctt-button'><span>" . $bcttBttn . "</span><i class='essb_icon_twitter'></i>
		</div>";
	} 
}

add_shortcode ( 'easy-ctt', 'essb_ctt_shortcode' );
add_shortcode ( 'easy-tweet', 'essb_ctt_shortcode' );
add_shortcode ( 'sharable-quote', 'essb_ctt_shortcode' );


