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
	
	$preview_mode = isset($atts['preview_mode']) ? $atts['preview_mode'] : '';
	
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
		
		/**
		 * @since 8.0 Migrating code to the new short URL class
		 */
		if (class_exists('ESSB_Short_URL')) {
		    if (ESSB_Short_URL::active()) {
		        $short_url = ESSB_Short_URL::generate_short_url($post_url, get_the_ID(), 'click2tweet');
		    }
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
	
	$link_short = urlencode ( $link_short );
	
	/**
	 * @since 8.5 Adding support for new lines
	 */
	$link_short = str_replace('{nl}', '%0a', $link_short);
	$link_short = str_replace('%7Bnl%7D', '%0a', $link_short);
	
	/**
	 * SVG Icons Loader
	 */
	if (!class_exists('ESSB_SVG_Icons')) {
	    include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
	}
	
	if (! is_feed ()) {
		
	    if ($preview_mode == 'true') {
	        return "<div class='essb-ctt".esc_attr($template)."' >
    			<span class='essb-ctt-quote'>
    			" . str_replace('{nl}', '<br/>', $text) . "
    			</span>
    			<span class='essb-ctt-button'><span>" . $bcttBttn . "</span><i class='essb_svg_icon_twitter'>".ESSB_SVG_Icons::get_icon('twitter')."</i>
    		</div>";
	        
	    }
	    else {
    		return "<div class='essb-ctt".esc_attr($template)."' onclick=\"window.open('https://twitter.com/intent/tweet?text=" . $link_short . $handle_code . $bcttURL . "', 'essb_share_window', 'height=300,width=500,resizable=1,scrollbars=yes');\">
    			<span class='essb-ctt-quote'>
    			" . str_replace('{nl}', '<br/>', $text) . "
    			</span>
    			<span class='essb-ctt-button'><span>" . $bcttBttn . "</span><i class='essb_svg_icon_twitter'>".ESSB_SVG_Icons::get_icon('twitter')."</i>
    		</div>";
	    }
	} 
}

function essb_ctt_shortcode_inline($atts, $content = null) {
    if (!isset($content) || empty($content)) {
        return essb_ctt_shortcode($atts);
    }
    
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
    
    $preview_mode = isset($atts['preview_mode']) ? $atts['preview_mode'] : '';
    
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
        $template = ' essb-ctt-inline-'.esc_attr($atts['template']);
    }
    else {
        $setup_template = essb_sanitize_option_value('cct_template_inline');
        
        if ($setup_template == 'same') {
            $setup_template = essb_sanitize_option_value('cct_template');
        }
        
        if ($setup_template != '') {
            $template = ' essb-ctt-inline-'.esc_attr($setup_template);
        }
    }    
    
    
    $post_url = get_permalink();
    $short_url = '';
    $automated_url = false;
    
    $text = $atts['tweet'];    
    if (empty($text)) {
        $text = strip_tags($content);       
        
        if (empty($atts['url'])) {
            $atts['url'] = $post_url;
            $automated_url = true;
            
            /**
             * @since 8.0 Migrating code to the new short URL class
             */
            if (class_exists('ESSB_Short_URL')) {
                if (ESSB_Short_URL::active()) {
                    $short_url = ESSB_Short_URL::generate_short_url($post_url, get_the_ID(), 'click2tweet');
                }
            }
        }
    }
    
    // @since 3.4 - fix problem with missing url in click-to-tweet
    if ($atts['url'] == '' && essb_option_bool_value('cct_url')) {
        $atts['url'] = $post_url;
        $automated_url = true;
        
        /**
         * @since 8.0 Migrating code to the new short URL class
         */
        if (class_exists('ESSB_Short_URL')) {
            if (ESSB_Short_URL::active()) {
                $short_url = ESSB_Short_URL::generate_short_url($post_url, get_the_ID(), 'click2tweet');
            }
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
    
    /**
     * SVG Icons Loader
     */
    if (!class_exists('ESSB_SVG_Icons')) {
        include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
    }
    
    $tweet_final_link = "https://twitter.com/intent/tweet?text=" . urlencode ( $link_short ) . $handle_code . $bcttURL;
    
    if (! is_feed ()) {
        
        if ($preview_mode == 'true') {
            return "<a class='essb-ctt-inline".esc_attr($template)."' title='".$bcttBttn."'>
    			" . $text . "
    			<span class='essb-ctt-button'><i class='essb_svg_icon_twitter'>".ESSB_SVG_Icons::get_icon('twitter')."</i>
    		</div>";
            
        }
        else {
            return "<a href='".$tweet_final_link."' class='essb-ctt-inline".esc_attr($template)."' onclick=\"window.open('". $tweet_final_link . "', 'essb_share_window', 'height=300,width=500,resizable=1,scrollbars=yes'); return false;\" title='".$bcttBttn."'>
    			" . $content . "
    			<span class='essb-ctt-button'><i class='essb_svg_icon_twitter'>".ESSB_SVG_Icons::get_icon('twitter')."</i>
    		</a>";
        }
    } 
}

add_shortcode ( 'easy-ctt', 'essb_ctt_shortcode' );
add_shortcode ( 'easy-tweet', 'essb_ctt_shortcode' );
add_shortcode ( 'sharable-quote', 'essb_ctt_shortcode' );
add_shortcode ( 'inline-sharable-quote', 'essb_ctt_shortcode_inline');
add_shortcode ( 'inline-tweet', 'essb_ctt_shortcode_inline');


