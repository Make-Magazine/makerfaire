<?php

function determine_staging() {
  // Display a message if on staging or development
  if (Jetpack::is_development_mode()) {
    echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white;">Development Site</div>';
  } else if (Jetpack::is_staging_site()) {
    echo '<div class="stagingMsg" style="display: block; text-align: center; background: red; font-size: large;color: white;">Staging Site</div>';
  }
}

add_action('admin_head', 'determine_staging');
add_action('wp_head', 'determine_staging');
add_action('rmt_head', 'determine_staging');

//if on staging send all emails to kate@makermedia.com and set the from email to staging@makermedia.com
add_filter('gform_notification', 'change_email_to', 10, 3);

function change_email_to($notification, $form, $entry) {
  if (Jetpack::is_staging_site()) {
    $notification['toType'] = 'email';
    $notification['to'] = 'kate@makermedia.com,alicia@makermedia.com';
    $notification['from'] = 'staging@makermedia.com';
    if (isset($notification['bcc']))
      $notification['bcc'] = '';

    return $notification;
  }
  else {
    return $notification;
  }
}
