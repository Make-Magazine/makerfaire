<?php
function makewidget_rss_output($rss, $settings) {
    if (is_string($rss)) {
        $rss = fetch_feed($rss);
    } elseif (is_array($rss) && isset($rss['url'])) {
        $args = $rss;
        $rss = fetch_feed($rss['url']);
    } elseif (!is_object($rss)) {
        return;
    }

    if (is_wp_error($rss)) {
        if (is_admin() || current_user_can('manage_options')) {
            echo '<p><strong>' . __('RSS Error:') . '</strong> ' . $rss->get_error_message() . '</p>';
        }
        return;
    }

    $default_args = array(
        'show_author' => 0,
        'show_date' => 0,
        'show_summary' => 0,
        'items' => 0,
    );
    $args = $default_args;
    $items = (int) $settings['num_display']; // this is the number of items we show

    $classes = $settings['rss_class'];
    $show_summary = $settings['show_summary'];
    $show_author = $settings['show_author'];
    $show_date = $settings['show_date'];
	$feed_link = $settings['link'];
	$title_position = $settings['title_position'];
	$horizontal = $settings['horizontal_display'];
	$stacked = $settings['stacked'];
	$carousel = $settings['carousel'];
	$read_more = $settings['read_more'];

    if (!$rss->get_item_quantity()) {
        echo '<ul><li>' . __('An error has occurred, which probably means the feed is down. Try again later.') . '</li></ul>';
        $rss->__destruct();
        unset($rss);
        return;
    }

    $dateNow = new DateTime('now');

    $sortedFeed = array();
    $feedItems = $rss->get_items();

    //sort based on disp_order
    if($settings['disp_order']=='random'){
        shuffle($feedItems);
    }

    $i = 0;
    foreach ($feedItems as $item) {
        //exclude events that have already occurred
        $date = '';
        if(is_array($item->get_item_tags('', 'event_date'))){
            if($item->get_item_tags('', 'event_date')[0]['data']) {
                $dateString = new DateTime($item->get_item_tags('', 'event_date')[0]['data']);
                if(date_timestamp_get($dateNow) > 	date_timestamp_get($dateString)){
                    continue; //(skip this record);
                }
                // if it isn't a youtube feed, exclude feed items with no date
            }
        } else if(strpos($settings['rss_url'], 'youtube.com/feeds') == false ) {
			if(isset($item->get_item_tags('', 'pubDate')[0]) && $item->get_item_tags('', 'pubDate')[0]['data']){
				$dateString = new DateTime($item->get_item_tags('', 'pubDate')[0]['data']);
			}
		}
		if ($show_date == 'yes' && $dateString) {
			$date = $dateString->format('M j, Y');
		}

        //get the link
        $link = $item->get_link();
        while (stristr($link, 'http') != $link) {
            $link = substr($link, 1);
        }
        $link = esc_url(strip_tags($link));

        //set the title
        $title = esc_html(trim(strip_tags($item->get_title())));
        if (empty($title)) {
            $title = __('Untitled');
        }

        //set image
        if (strpos($settings['rss_url'], 'youtube.com/feeds') !== false && $enclosure = $item->get_enclosure()) {
            $image = '<img src="' . legacy_get_resized_remote_image_urll($enclosure->get_thumbnail(), 600, 400) . '" alt="'.$title.'"  />';
        } else {
            $image = '<img src="' . legacy_get_resized_remote_image_url(get_first_image_url($item->get_content()), 600, 400) . '" alt="'.$title.'" />';
        }
        
        $title = '<div class="rssTitle">' . $title . "</div>";

        //set description
		if (strpos($settings['rss_url'], 'www.makershed.com')) {
			//$desc = "<p><b>" . $item->get_item_tags("http://jadedpixel.com/-/spec/shopify", 'variant')[0]['child']["http://jadedpixel.com/-/spec/shopify"]['price'][0]['data'] . "</b></p>";
			$desc = "<a href='" . $link . "' class='universal-btn btn'>Buy Now</a>";
		} else {
	        $desc = html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'));
	        $desc = esc_html(esc_attr(wp_trim_words($desc, 55, ' [&hellip;]')));
		}

        //summary
        $summary = '';
        if ($show_summary == 'yes') {
            $summary = $desc;
            // Change existing [...] to [&hellip;].
            if ('[...]' == substr($summary, -5)) {
                $summary = substr($summary, 0, -5) . '[&hellip;]';
            }
            $summary = '<div class="rssSummary">' . $summary . '</div>';
        }

        //author
        $author = '';
        if ($show_author == 'yes') {
            $author = $item->get_author();
            if (is_object($author)) {
                $author = $author->get_name();
                $author = ' <cite>' . esc_html(strip_tags($author)) . '</cite>';
            }
        }

        $sortedFeed[] = array('date' => $date, 'show_date' => $show_date, 'link' => $link, 'title' => $title, 'image' => $image, 'desc' => $desc, 'summary' => $summary, 'author' => $author);
        //sort this by date, newest first, if it's an event
        if(is_array($item->get_item_tags('', 'event_date'))){
            if($item->get_item_tags('', 'event_date')[0]['data']) {
                usort($sortedFeed, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
            }
        }
        // limit by items
        if (++$i == $items) break;
    }

	$wrapper_classes = $classes;
	if ($horizontal == 'yes') {
		$wrapper_classes .= " horizontal";
	}
	if ($carousel == 'yes') {
		$wrapper_classes .= " carousel";
	}
    if ($show_summary == 'yes') {
		$wrapper_classes .= " description";
	}
	if ($summary != '') {
		$wrapper_classes .= " summary";
	}
	if (isset($settings['rss_url']) && strpos($settings['rss_url'], 'www.makershed.com')) {
		$wrapper_classes .= " makershed";
	}
    if ($stacked == 'yes') {
		$wrapper_classes .= " stacked";
	}
    echo '<ul class="custom-rss-element' . $wrapper_classes . '">';
    foreach ($sortedFeed as $item) {
        $link       = $item['link'];
        $title      = $item['title'];
        $image      = $item['image'];
        $desc       = $item['desc'];
        $summary    = $item['summary'];
        $author     = $item['author'];

		echo "<li style='list-style:none;'>";
        if ($link != '') {
            echo "<a class='rss-link' href='$link' target='_blank'>";
		}
		if ($title_position == "top") {
            echo "{$title}";
        }
		if ($horizontal == 'yes') {
			echo "<div class='rss-content-wrapper'>";
		}
		echo 	"<div class='rss-image-wrapper'>{$image}</div>";
		if ($title_position == "bottom") {
            echo "{$title}";
        }
		if ($horizontal == 'yes') {
			echo "<div class='rss-text-wrapper'>";
			echo "{$title}";
		}
		if ($show_summary == "yes") {
            echo "{$summary}";
        }
		if ($item['show_date'] == 'yes') {
            echo '<date>' . $item['date'] . '</date>';
        }
		if ($show_author == "yes") {
            echo "{$author}";
        }
        if ($horizontal == 'yes') {
			echo "</div>";
			echo "</div>";
		}
		if ($link != '') {
            echo "</a>";
		}
		echo "</li>";
    }
	if ($carousel == 'yes') {
		echo "<li class='rss-carousel-read-more'><a href='". $feed_link ."' target='_blank'>" . $read_more . "</a></li>";
	}
    echo '</ul>';
    $rss->__destruct();
    unset($rss);
}

//curl functionality
function MakeBasicCurl($url, $headers = null) {
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
    //curl_setopt($ch, CURLOPT_STDERR, $verbose = fopen('php://temp', 'rw+'));
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($headers != null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $network_url = network_site_url();
	if (strpos($network_url, '.local') > -1 || strpos($network_url, '.test') > -1 ) { // wpengine local environments
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);

    //echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
    curl_close($ch);
    return $data;
}