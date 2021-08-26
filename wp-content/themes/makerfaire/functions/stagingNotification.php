<?php
/*
 * Function to change the TO email for all outgoing Gravity Form emails
 */
add_filter('gform_notification', 'change_email_to', 10, 3);

function change_email_to($notification, $form, $entry) {
   $homeurl = get_home_url();
   // Check for our stage and dev sites
   if ($homeurl === "https://stage.makerfaire.com" || $homeurl === "https://dev.makerfaire.com" || $homeurl === "https://makerfaire.staging.wpengine.com") {      
      $notification['to'] = 'webmaster@make.co,siana@make.co';
      $notification['from'] = 'webmaster@make.co';
      $notification['subject'] = 'Redirect Email from '.$homeurl.' sent to ' . $notification['to'] . ' - ' . $notification['subject'];
      if (isset($notification['bcc'])) $notification['bcc'] = '';
   } elseif (strpos($homeurl, ':8888') !== false) {
      // Check for local sites
      if (defined('MF_OVERRITE_EMAIL')) {
         $notification['toType'] = 'email';
         $notification['to'] = MF_OVERRITE_EMAIL;
         if (isset($notification['bcc'])) $notification['bcc'] = '';
      }
   }
   
   return $notification;
   
}

// override for the wp email function
add_filter('wp_mail', 'change_email_for_wp', 10, 2);

function change_email_for_wp($notification) {
	$homeurl = get_home_url();
	// Check for our stage and dev sites	
	if ($homeurl === "https://stage.makerfaire.com" || $homeurl === "https://dev.makerfaire.com" || $homeurl === "https://makerfaire.staging.wpengine.com") {
		$notification = array(
				'to' => 'webmaster@make.co,siana@make.co',
				'bcc' => '',
				'from' => 'webmaster@make.co',
				'subject' => 'Redirect Email from '.$homeurl.' sent to ' . $notification['to'] . ' - ' . $notification['subject']			
		);
		$notification['headers'][] = 'cc: ""';
	}
	
    return ($notification);
   
}
