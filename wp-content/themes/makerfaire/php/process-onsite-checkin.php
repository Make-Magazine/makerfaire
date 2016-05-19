<?php

// process-onsite-checkin.php

$errors = array();      // array to hold validation errors
$data = array();      // array to pass back data
// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array


if (empty($_POST['entryID']))
  $errors['entryID'] = 'entryID is required.';

if (empty($_POST['latitude']))
  $errors['latitude'] = 'latitude is required.';

if (empty($_POST['longitude']))
  $errors['longitude'] = 'longitude is required.';



// return a response ===========================================================
// if there are any errors in our errors array, return a success boolean of false
if (!empty($errors)) {

  // if there are items in our errors array, return those errors
  $data['success'] = false;
  $data['errors'] = $errors;
} else {

  // if there are no errors process our form, then return a message
  //form submitted? update database
  //update the database with submitted info
  /* CREATE TABLE `wp_mf_onsitecheckin` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `entry_id` int(11) DEFAULT NULL,
    `latitude` float DEFAULT NULL,
    `longitude` float DEFAULT NULL,
    `comments` varchar(1024) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
   */
  $entry_ID = $_POST['entryID'];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];
  
  $sql = sprintf("REPLACE INTO `wp_mf_onsitecheckin`(`entry_id`, `latitude`, `longitude`) VALUES "
          . " ( %s, %.10f, %.10f ) ", $entry_ID, $latitude, $longitude);

  $wpdb->get_results($sql);

  // show a message of success and provide a true success variable
  $data['success'] = true;
  $data['message'] = 'Success!'.$sql;
}

// return all our data to an AJAX call
echo json_encode($data);
