<?php

//ajax functionality to update the entry rating
function myajax_update_entry_rating() {
  global $wpdb;
  $entry_id = $_POST['rating_entry_id'];
  $rating   = $_POST['rating'];
  $user     = $_POST['rating_user'];

  //update user rating

  //if there is already a record for this user, update it.
  //else add it.
  $sql = "Insert into wp_mf_lead_rating (entry_id, user_id, rating) "
       . " values (".$entry_id.','.$user.','.$rating.")"
       . " on duplicate key update rating=".$rating.", ratingDate=now()";

  $wpdb->get_results($sql);

  //update the meta with the average rating
  $sql = "SELECT avg(rating) as rating FROM `wp_mf_lead_rating` where entry_id = ".$entry_id;
  $results = $wpdb->get_results($sql);
  $rating = round($results[0]->rating);

  gform_update_meta( $entry_id, 'entryRating', $rating );
  echo 'Your Rating Has Been Saved';
  // IMPORTANT: don't forget to "exit"
  exit;
}
add_action( 'wp_ajax_update-entry-rating', 'myajax_update_entry_rating' );