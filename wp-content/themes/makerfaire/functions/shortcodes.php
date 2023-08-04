<?php

// get just the text
function get_summary($html) {
    $summary = preg_replace('/<a[^>]*>([\s\S]*?)<\/a[^>]*>/', '', $html);
    $summary = strip_tags(str_replace('The post  appeared first on .', '', $summary));
    $summary = str_replace('[&hellip;]', '', $summary);
    return $summary;
}

//shortens description
function shorten($string, $length) {
    $suffix = '&hellip;';
    $short_desc = trim(str_replace(array("\r", "\n", "\t"), ' ', strip_tags($string)));
    $desc = trim(substr($short_desc, 0, $length));
    $lastchar = substr($desc, -1, 1);
    if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?')
        $suffix = '';
    $desc .= $suffix;
    return $desc;
}

function get_first_image_url($html) {
  if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
  return $matches[1];
  }
}


// [make_rss title="Makerspace", feed="https://makezine.com/tag/makerspaces/feed/", moreLink="http://makezine.com/tag/makerspaces/", number=4]
function make_rss_func($atts) {
	
    $a = shortcode_atts(array(
        'title' => '',
        'feed' => 'https://makezine.com/feed/',
        'morelink' => "",
        'number' => 6
            ), $atts);

	// each attribute above for some reason has a comma after it	 
	array_walk($a,function(&$val){$val = trim($val);});
	
	if($a['feed'] && $a['feed'] != "") {
		$a['feed'] = 'https://makezine.com/tag/' . $a['feed'] . "/feed";
	} else {
		$a['feed'] = 'https://makezine.com/feed/';
	}
	
    $return = '    
    <div class="container rss-feed">
        <h2>' . $a['title'] . ' News from <img class="logo" src="https://make.co/wp-content/universal-assets/v2/images/make_logo.svg" /> Magazine</h2>
        <div class="row posts-feeds-wrapper">';


    $rss = fetch_feed($a['feed']);
    if (!is_wp_error($rss)) {
        $maxitems = $rss->get_item_quantity($a['number']); //gets latest x items, this can be changed to suit your requirements
        $rss_items = $rss->get_items(0, $maxitems);
    }

    if ($maxitems == 0) {
        $return .= '<li>No items.</li>';
    } else {
        foreach ($rss_items as $item) {
            $return .= '
                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="post-feed">
                            <a class="full-link" href="' . esc_url($item->get_permalink()) . '" target="_blank">
                                <div class="title">
                                    <p class="p-title">' . smartTruncate(esc_html($item->get_title()), 60, " ") . '</p>
								</div>
								<div class="content">
                                    <img src="' . legacy_get_resized_remote_image_url(get_first_image_url($item->get_content()), 250, 250) . '" alt="' . esc_html($item->get_title()) . ' featured image">                                    
                                    <p>' . get_summary($item->get_content()) . '</p>
                                </div>
                            </a>
                        </div>
                    </div>';
        }
    }
    if ($a['morelink'] != '') {
        $return .= '
                <div class="col-xs-12">
                    <a class="btn universal-btn btn-more-articles" href="' . $a['morelink'] . '" target="_blank">See more articles</a>
                </div>';
    }
    $return .= '    
        </div>

    </div>';
    return $return;
}

add_shortcode('make_rss', 'make_rss_func');

// function to return the attributes for a given shortcode present on a page
function get_shortcode_attributes( $shortcode_tag ) {
    global $post;
    if( has_shortcode( $post->post_content, $shortcode_tag ) ) {
        $output = array();
        //get shortcode regex pattern wordpress function
        $pattern = get_shortcode_regex( [ $shortcode_tag ] );
        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) )
        {
            $keys = array();
            $output = array();
            foreach( $matches[0] as $key => $value) {
                // $matches[3] return the shortcode attribute as string
                // replace space with '&' for parse_str() function
                $get = str_replace(" ", "&" , trim( $matches[3][$key] ) );
                $get = str_replace('"', '' , $get );
                parse_str( $get, $sub_output );

                //get all shortcode attribute keys
                $keys = array_unique( array_merge(  $keys, array_keys( $sub_output )) );
                $output[] = $sub_output;
            }
            if( $keys && $output ) {
                // Loop the output array and add the missing shortcode attribute key
                foreach ($output as $key => $value) {
                    // Loop the shortcode attribute key
                    foreach ($keys as $attr_key) {
                        $output[$key][$attr_key] = isset( $output[$key] )  && isset( $output[$key] ) ? $output[$key][$attr_key] : NULL;
                    }
                    //sort the array key
                    ksort( $output[$key]);
                }
            }
        }
        return $output;
    }else{
        return false;
    }
}