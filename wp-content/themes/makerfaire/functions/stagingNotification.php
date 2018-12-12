<?php

function determine_staging() {
   // Display a message if on staging or development
   if (class_exists('Jetpack')) {
      if (Jetpack::is_development_mode()) {
         echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white; position: fixed; right 0px; z-index: 9999;">Development Site</div>';
      } else if (Jetpack::is_staging_site()) {
         echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white; position: fixed; right 0px; z-index: 9999;">Staging Site</div>';
      }
   }
   
}

add_action('admin_head', 'determine_staging');
add_action('wp_head', 'determine_staging');
add_action('rmt_head', 'determine_staging');

/*
 * Function to change the TO email for all outgoing Gravity Form emails
 */
add_filter('gform_notification', 'change_email_to', 10, 3);

function change_email_to($notification, $form, $entry) {
   $homeurl = get_home_url();
   // Check for our stage and dev sites
   if ($homeurl === "https://stage.makerfaire.com" || $homeurl === "https://dev.makerfaire.com" || $homeurl === "https://makerfaire.staging.wpengine.com") {
      $notification['toType'] = 'email';
      $notification['to'] = 'alicia@makermedia.com,sianabrook@gmail.com';
      $notification['from'] = 'staging@makermedia.com';
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
   if (class_exists('Jetpack')) {
      if (Jetpack::is_staging_site()) {
         $notification = array(
            'to' => 'alicia@makermedia.com,sianabrook@gmail.com',
            'bcc' => '',
            'from' => 'staging@makermedia.com',
            'subject' => $notification['subject'],
            'message' => $notification['message'],
            'headers' => $notification['headers'],
            'toType' => 'email',
            'attachments' => (isset($args) && isset($args['attachments']) ? $args['attachments'] : '')
         );
      } elseif (Jetpack::is_development_mode()) {
         if (defined('MF_OVERRITE_EMAIL')) {
            $notification = array(
               'to' => MF_OVERRITE_EMAIL,
               'bcc' => '',
               'from' => 'staging@makermedia.com',
               'subject' => $notification['subject'],
               'message' => $notification['message'],
               'headers' => $notification['headers'],
               'toType' => 'email',
               'attachments' => $notification['attachments']
            );
         }
      }
   }
   return ($notification);
   
}
