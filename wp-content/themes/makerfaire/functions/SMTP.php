<?php
////////////////////////////////////////////////////////////////////
// Adds SMTP Settings
////////////////////////////////////////////////////////////////////
add_action('phpmailer_init', 'send_smtp_email');

function send_smtp_email($phpmailer) {
  $phpmailer->isSMTP();  // Define that we are sending with SMTP
  $phpmailer->Host = "smtp.postmarkapp.com"; // The hostname of the mail server
  $phpmailer->SMTPAuth = true; // Use SMTP authentication (true|false)
  $phpmailer->Port = "587"; // SMTP port number - likely to be 25, 465 or 587
  $phpmailer->Username = "PM-T-outbound-xNM_QFQ5KPQTU4WgwLlzHY"; // Username to use for SMTP authentication
  $phpmailer->Password = "i0sZjtQKZ2DGRS407RFum3xiskSv3snWIOxO"; // Password to use for SMTP authentication
  $phpmailer->SMTPSecure = "tls"; // Encryption system to use - ssl or tls
}
