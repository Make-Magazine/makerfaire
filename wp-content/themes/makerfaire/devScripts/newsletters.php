<?php
include 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ac_headers = array('Api-Token: 6a90725830fc5e03e6cddebdbc550ee624b30aea80abcaf9d1c239ea6ffeb30ea2f86075');
$acURL = 'https://make.api-us1.com/api/3';

$message = '';
if (isset($_POST['userEmail']) && $_POST['userEmail'] != '') {
    $email      = $_POST['userEmail'];
    $first_name = trim($_POST['firstName']);
    $last_name  = trim($_POST['lastName']);

    $subscribeToBeehiv = FALSE;
    $AC_contactID = '';
    $signUp = array();
    foreach ($_POST['newsletterSel'] as $selectedNewsletter) {
        $message .= $selectedNewsletter . '<br/>';
        $newsArray = explode('-', $selectedNewsletter);

        $selCRM   = $newsArray[0];
        $selListID =  $newsArray[1];
        
        //AC or Beehiive sign up?
        if ($selCRM == 'ActiveCampaign') {
            if ($AC_contactID == '') {
                //call AC and see if the user exists to get id
                $url = $acURL . '/contacts/?email=' . $email;

                //call API
                $response = postCurl($url, $ac_headers, NULL, 'GET');
                $response = json_decode($response);

                //does the user exist?
                if (isset($response->id)) {
                    $AC_contactID = $response->id; //set contact ID
                } else {
                    //need to add the user
                    $url = $acURL . '/contacts';
                    $body =  array(
                        "contact" => array(
                            "email"     => $email,
                            "firstName" => $first_name,
                            "lastName"  => $last_name
                        )
                    );

                    $response = postCurl($url, $ac_headers, $post);
                    $response = json_decode($response);
                    
                    //NOTE: add error checks
                    //if response code = 200 && $response->id is set
                    $AC_contactID = $response->id; //set contact ID
                }
            }    
            
            if($AC_contactID!=''){
                //call AC to subscribe contact to the selected newsletters
                $url = $acURL . '/contactLists';
                $body = array(
                    "contactList" => array(
                        "list" => $selListID,
                        "contact" => $AC_contactID,
                        "status" => 1
                    )
                );

                //NOTE: add error checks
                $response = postCurl($url, $ac_headers, $post);
                $response = json_decode($response);
                //if response code = 200
                if(!isset($response->errors)){
                    //user enrolled
                    $message .= 'Email '.$email. ' successfully enrolled in newsletter '. $selectedNewsletter .'<br/>';                       
                }else{
                    $message .= 'Email '.$email. ' not successfully enrolled in newsletter '. $selectedNewsletter .'<br/>';
                    foreach($response->errors as $error){
                        $message .= $error->title.' ('.$error->code.')<br/>';
                    }
                }
            }                    
        }elseif ($selCRM == 'Beehiiv') {
            $subscribeToBeehiv = TRUE;
            //code here to build the beehiiv post body
            //$acBody['p['.$newsArray[1].']'] = $newsArray[1];            
        }
    }
} else {
}

$newsletterList = array();

//get list of AC available lists 
$url = $acURL . "/lists?limit=99";

//call API
$response = postCurl($url, $ac_headers, NULL, 'GET');
$response = json_decode($response);

foreach ($response->lists as $list) {
    $newsletterList[] = array(
        'CRM'   => 'ActiveCampaign',
        'id'    => $list->id,
        'name'  => $list->name
    );
}

//get list of Beehiiv lists
$beehiiv_headers = array(
    'Accept: application/json',
    'Authorization: Bearer Ebv1c4cIol9jjEuN5P6sR6fSPObDp8g0MbmalWPHQ4HjPmg8IM3SJ6RtA8EYWGOb',
    'Content-Type: application/json'
);
$beehiiv_url = 'https://api.beehiiv.com/v2/publications';

//call API
$response = postCurl($beehiiv_url, $beehiiv_headers, NULL, 'GET');
$response = json_decode($response);

foreach ($response->data as $list) {
    $newsletterList[] = array(
        'CRM'   => 'Beehiiv',
        'id'    => $list->id,
        'name'  => $list->name
    );
}

?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <div style="text-align: center; margin: 0 auto; width:90% ">
        <h2>Newsletter Signup test</h2>

        <form method="post" enctype="multipart/form-data" name="newsletterForm">
            <div class="d-flex justify-content-around">
                <div class="form-group">
                    <label for="FirstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" placeholder="First name" name="firstName">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" placeholder="Last name" name="lastName">
                </div>
                <div class="form-group">
                    <label for="userEmail">Email address</label>
                    <input type="email" class="form-control" id="userEmail" placeholder="Enter email" name="userEmail">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
            </div>
            <div style="text-align: left; display: flex; flex-wrap:wrap;">
                <?php
                foreach ($newsletterList as $list) {
                    echo '<div class="form-check form-check-flex" style="width:30%">';
                    echo '  <input name="newsletterSel[]" type="checkbox" class="form-check-input"  value="' . $list['CRM'] . '-' . $list['id'] . '" id="' . $list['CRM'] . '-' . $list['id'] . '">';
                    echo '  <label class="form-check-label" for="' . $list['CRM'] . '-' . $list['id'] . '">' . $list['name'] . '</label>';
                    echo '</div>';
                }
                ?>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div style="text-align: left;">
            <h4><?php echo $message; ?></h4>
        </div>
    </div>
</body>

</html>