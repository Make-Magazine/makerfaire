<?php
/*
 * Function to change the TO email for all outgoing Gravity Form emails
 */
add_filter('gform_notification', 'change_email_to', 10, 9999);

function change_email_to($notification, $form, $entry) {
   $homeurl = get_home_url();
   // Check for our stage and dev sites	
   if (strpos($homeurl, '.wpengine.com') !== false) {
		//if we are in the testing env, and the to email is one of our own, let it through
		if(strpos($notification['to'], '@make.co') !== false){
			return $notification;
		}
   	  	$notification['to'] = 'webmaster@make.co,siana@make.co';
   	  	$notification['from'] = 'webmaster@make.co';
		$notification['cc'] = '';
   	  	$notification['subject'] = 'Redirect Email from '.$homeurl.' sent to ' . $notification['to'] . ' - ' . $notification['subject'];   	

      	if (isset($notification['bcc'])) $notification['bcc'] = '';
		error_log(print_r($notification,TRUE));
   } elseif (strpos($homeurl, '.local') !== false) {
      // Check for local sites
      if (defined('MF_OVERRITE_EMAIL')) {
         $notification['toType'] = 'email';
         $notification['to'] = MF_OVERRITE_EMAIL;
		 $notification['cc'] = '';
         if (isset($notification['bcc'])) $notification['bcc'] = '';
      }else{
      	//$notification = array();
      }
   }
   
   return $notification;
   
}

// override for the wp email function
add_filter('wp_mail', 'change_email_for_wp', 10, 2);

function change_email_for_wp($notification) {
	$homeurl = get_home_url();

	// Check for our stage and dev sites	
	if ($homeurl === "https://mfairestage.wpengine.com/" || $homeurl === "https://mfairedev.wpengine.com/") {
		$notification = array(
				'to' => 'webmaster@make.co,siana@make.co',
				'bcc' => '',
				'from' => 'webmaster@make.co',
				'subject' => 'Redirect Email from '.$homeurl.' sent to ' . $notification['to'] . ' - ' . $notification['subject']
		);
		$notification['headers'][] = 'cc: ""';
	}elseif (strpos($homeurl, '.local') !== false) {
		// Check for local sites
		if (defined('MF_OVERRITE_EMAIL')) {
			$notification['toType'] = 'email';
			$notification['to'] = MF_OVERRITE_EMAIL;
			if (isset($notification['bcc'])) $notification['bcc'] = '';
		}else{
			$notification = array();
		}
	}
	return ($notification);	
} 