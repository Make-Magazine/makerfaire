<?php
/**
 * Generate the options for showing notifications on the front of site
 *
 * @return string
 */
function essbspnlite_notification_holder_options() {
	$options = array();

	$delay_start = essb_sanitize_option_value('proofnotifications_start');
	if (intval($delay_start) == 0) {
		$delay_start = 10;
	}
	
	$options[] = 'data-start="'.esc_attr($delay_start).'"';

	$delay_stay = essb_sanitize_option_value('proofnotifications_stay');
	if (intval($delay_stay) == 0) {
	    $delay_stay = 5;
	}
	$options[] = 'data-stay="'.esc_attr($delay_stay).'"';

	$delay_wait = essb_sanitize_option_value('proofnotifications_wait');
	if (intval($delay_wait) == 0) {
	    $delay_wait = 5;
	}
	$options[] = 'data-delay="'.esc_attr($delay_wait).'"';

	$notification_loop = essb_option_bool_value('proofnotifications_loop');
	if ($notification_loop) {
		$options[] = 'data-loop="yes"';
	}

	return implode(' ', $options);
}

/**
 * Generate a single social proof notification message
 *
 * @param unknown_type $message
 * @param unknown_type $index
 * @return string
 */
function essbspnlite_notification_draw_code($message, $index = '') {
	$output = '';

	$default_image = '';
	$close_button = false;
	$shape = 'full-rounded';
	$showat = essb_sanitize_option_value('proofnotifications_appear');
	$mobile_display = '';
	$image = isset($message['image']) ? $message['image'] : '';
	$network_icon = isset($message['network_icon']) ? $message['network_icon'] : 'false';

	if (empty($image)) {
		$image = $default_image;
	}
	
	$message['output'] = nl2br($message['output']);
	$message['output'] = str_replace(array("\r\n", "\r", "\n"), "<br />", $message['output']);
	$message['output'] = str_replace('[nl]', "<br />", $message['output']);

	$message_parts = preg_split('/<br[^>]*>/i', $message['output']);

	$output .= '<div class="essbspn-box essbspn-'.esc_attr($shape).esc_attr($mobile_display).' essbspn-location-'.esc_attr($showat).' essbspn-index-'.esc_attr($index).'" data-index="'.esc_attr($index).'">';

	if ($close_button) {
		$output .= '<span class="essbspn-close" title="Close">×</span>';
	}

	$output .= '<div class="essbspn-content">';

	// image
	$output .= '<div class="essbspn-content-image">';
	if ($network_icon == 'true') {
		$output .= $message['network_icon_code'];
	}
	else {
		if ($image != '') {
			$output .= '<img src="'.esc_url($image).'" alt="" />';
		}
	}
	$output .= '</div>';

	// message content
	$output .= '<div class="essbspn-content-text">';

	$cnt = 1;
	foreach ($message_parts as $line) {
		$output .= '<div class="message-line message-line'.esc_attr($cnt).'">'.$line.'</div>';
		$cnt++;
	}

	$output .= '</div>'; // closing content-text

	$output .= '</div>'; // closing content


	$output .= '<a href="http://go.appscreo.com/essb" class="essbspn-credit" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M216.056 509.316l197.488-340.044c4.471-7.699-1.87-17.173-10.692-15.973l-131.364 17.855L302.875 6.372c1.058-5.555-6.104-8.738-9.518-4.231L99.183 258.451c-5.656 7.465.333 18.08 9.647 17.1l144.828-15.245-47.479 245.308c-1.145 5.917 6.85 8.914 9.877 3.702z" fill="#ffdc64"/><path d="M159.905 240.287c-3.627.29-6.036-3.675-4.108-6.76L300.976 1.241c-2.16-1.709-5.56-1.819-7.619.899L99.183 258.45c-5.656 7.466.333 18.08 9.647 17.1l144.828-15.245-47.479 245.308c-.64 3.309 1.592 5.637 4.201 6.194l81.359-257.447c3.814-12.067-5.808-24.156-18.423-23.146l-113.411 9.073z" fill="#ffc850"/></svg>Powered by ESSB</a>';

	if (isset($message['action_url']) || isset($message['action_command'])) {
		$output .= '<a class="essbspn-action-link" ';

		if (isset($message['action_url']) && !empty($message['action_url'])) {
			$output .= 'href="'.esc_url($message['action_url']).'" target="_blank"';
		}

		if (isset($message['action_command']) && !empty($message['action_command'])) {
			$output .= 'onclick="'.$message['action_command'].'"';
		}

		$output .= '></a>';
	}

	$output .= '</div>'; // colsing box

	return $output;
}

/**
 * Parsing generated messages and compile the final content based on all variables that are set
 */
function essbspnlite_compile_message_pool($messages = array()) {
	$r = array();
	$variables = array('title', 'network', 'value', 'link');

	foreach ($messages as $message) {
		$template = $message['template'];

		foreach ($variables as $param) {
			$value = isset($message[$param]) ? $message[$param] : '';
				
			$template = str_replace('{'.$param.'}', $value, $template);
		}

		$message['output'] = $template;
		$r[] = $message;
	}

	return $r;
}

function essbspnlite_get_share_notifications_pool($post_id = '') {
	$r = array();
	
	$share_notification_count = essb_sanitize_option_value('proofnotifications_counter');
	$share_minimal_value = essb_sanitize_option_value('proofnotifications_min');
	
	$share_message_template = essb_sanitize_option_value('proofnotifications_message');
	if (empty($share_message_template)) {
	   $share_message_template = '{title} is highly popular post having {value} {network} shares[nl]Share with your friends';
	}
	
	$share_action_link = 'share';
	$share_action_custom = '';
	
	if (intval($share_notification_count) == 0) {
		$share_notification_count = 1;
	}
	
	if (intval($share_minimal_value) == 0) {
		$share_minimal_value = '1';
	}
	
	// start generation of social networks
	$all_networks = essb_available_social_networks();
	$active_networks = array();
	foreach ($all_networks as $key => $data) {
		$active_networks[$key] = $data['name'];
	}
	
	$qualified_networks = array();
	$total_shares = 0;
	foreach ($active_networks as $key => $name) {
		$shares = intval(get_post_meta($post_id, 'essb_c_'.$key, true));
			
		$total_shares += intval($shares);
			
		// escaping networks that are below the limit
		if (intval($share_minimal_value) != 0 && intval($shares) < intval($share_minimal_value)) {
			continue;
		}
			
		$qualified_networks[$key] = array('name' => $name, 'value' => $shares);
	}
		
	// start forming the message that wil appear on the list
	$messages = array();
	$post_data = essb_get_post_share_details('');
	
	if (isset($post_data['title_plain']) && !empty($post_data['title_plain'])) {
		$post_data['title'] = $post_data['title_plain'];
		$post_data['title'] = stripcslashes($post_data['title']);
	}
	
	$share['essb_encode_url'] = false;
	
	if (!isset($post_data['full_url'])) {
		$post_data['full_url'] = $post_data['url'];
	}
	
	foreach ($qualified_networks as $key => $data) {
		$name = $data['name'];
		$value = $data['value'];
		$url = '';
		$image = '';
		$url_click_action = '';
			
		if ($share_action_link == 'custom') {
			$url = $share_action_custom;
		}
		else {
			$share_command = essb_get_share_address($key, $post_data, '');
			$url = $share_command['url'];
			$url_click_action = $share_command['api_command'];
	
			if ($key == 'total') {
				$url = '';
				$url_click_action = '';
			}
		}
			
		$messages[] = array(
				'title' => $post_data['title'],
				'value' => $value,
				'network' => $name,
				'image' => $post_data['image'],
				'link' => $post_data['url'],
				'action_url' => $url,
				'action_command' => $url_click_action,
				'template' => $share_message_template,
				'output' => ''
		);	

	}
	
	// Randomizing the generated messages (but only if have such);
	if (count($messages) > 0) {
		shuffle($messages);
			
		if (intval($share_notification_count) == 1) {
			$r[] = $messages[0];
		}
		else {
			$max = intval($share_notification_count) > count($messages) ? count($messages) : intval($share_notification_count);
	
			for ($i = 0; $i < $max; $i++) {
				$r[] = $messages[$i];
			}
		}
	}
	
	return $r;
}


function essbspnlite_get_activity_notifications_pool() {
    global $wpdb;
    
    if (!essb_option_bool_value('proofnotifications_activity')) {
        return array();
    }
    
    $activity_message_template = essb_sanitize_option_value('proofnotifications_activity_message');
       
    $activity_count = essb_sanitize_option_value('proofnotifications_activity_counter');
    $activity_action_link = 'default';
    $activity_action_custom = '';
    
    $activity_unique = false;
    $activity_period = '24h';
    $activity_fake = false;
    
    /**
     * Selected networks only
     */
    $use_user_share_networks = false;
    $notification_networks = array();
    
    if (intval($activity_count) == 0) {
        $activity_count = 1;
    }
    
    if (empty($activity_message_template)) {
        $activity_message_template = 'Someone share {title} on {network}[nl]{value} ago';
    }
    
    /**
     * @since 3.3 stripslashes
     */
    $activity_message_template = stripslashes($activity_message_template);
    
    $message = array();
    $r = array();
    
    $table_name = $wpdb->prefix . ESSB3_TRACKER_TABLE;
    
    /**
     * Generate information only if the analytics is enabled
     */
    if (class_exists('ESSBSocialShareAnalytics')) {
        
        $all_networks = essb_available_social_networks();
        $active_networks = array();
        foreach ($all_networks as $key => $data) {
            $active_networks[$key] = $data['name'];
        }
        
        
        if ($activity_unique) {
            $sql = 'SELECT DISTINCT essb_post_id, essb_service, essb_date FROM '.$table_name.' WHERE essb_date > ';
        }
        else {
            $sql = 'SELECT * FROM '.$table_name.' WHERE essb_date > ';
        }
        
        if ($activity_period == '6h') { $sql .= 'DATE_ADD(NOW(), INTERVAL -6 HOUR)'; }
        if ($activity_period == '12h') { $sql .= 'DATE_ADD(NOW(), INTERVAL -12 HOUR)'; }
        if ($activity_period == '24h') { $sql .= 'DATE_ADD(NOW(), INTERVAL -24 HOUR)'; }
        if ($activity_period == 'week') { $sql .= 'DATE_ADD(NOW(), INTERVAL -7 day)'; }
        if ($activity_period == 'month') { $sql .= 'DATE_ADD(NOW(), INTERVAL -30 day)'; }
        if ($activity_period == '3month') { $sql .= 'DATE_ADD(NOW(), INTERVAL -90 day)'; }
        
        if ($use_user_share_networks && count($notification_networks) > 0) {
            $sql .= ' AND essb_service IN (';
            
            foreach ($notification_networks as $network) {
                $sql .= "'".$network. "',";
            }
            $sql .= '"")';
        }
        
        $sql .= ' ORDER BY essb_date DESC';
        
        $results = $wpdb->get_results ( $sql );
        
        $added = 0;
        
        foreach ($results as $object) {
            $post = get_post($object->essb_post_id);
            $network = $object->essb_service;
            $date = $object->essb_date;
            
            $time = human_time_diff(strtotime($date), current_time( 'timestamp' ));
            
            if ($activity_fake) {
                $time = rand(1, 12);
                
                if ($time == 1) {
                    $time = $time . ' hour';
                }
                else {
                    $time = $time . ' hours';
                }
            }
            
            $added++;
            
            if ($added > intval($activity_count)) {
                continue;
            }
            
            $r[] = array(
                'title' => get_the_title($post->ID),
                'value' => $time,
                'network' => isset($active_networks[$network]) ? $active_networks[$network] : $network,
                'image' => get_the_post_thumbnail_url($post->ID),
                'link' => get_permalink($post->ID),
                'action_url' => get_permalink($post->ID),
                'action_command' => '',
                'template' => $activity_message_template,
                'output' => ''
            );
        }
    }
    
    return $r;
}