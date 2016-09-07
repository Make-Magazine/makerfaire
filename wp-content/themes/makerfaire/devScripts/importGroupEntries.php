<?php

/*
 * Script to do faire imports
 * - this import is used for grouped entries
 */
?>

<?php
include 'db_connect.php';

ini_set("auto_detect_line_endings", "1");
if ( isset($_POST["submit"]) ) {
  $csv = [];
  if ( isset($_FILES["fileToUpload"])) {
    //if there was an error uploading the file
    if ($_FILES["fileToUpload"]["error"] > 0) {
      echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br />";

    } else {
      //save the file
      $target_dir = "uploads/";
      if(!file_exists($target_dir)){
          mkdir("uploads/", 0777);
      }
      $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]).date('dmyhi');

      $name = $_FILES['fileToUpload']['name'];
      $name = explode('.', $name);
      $name = end($name);
      $ext = strtolower($name);

      $type    = $_FILES['fileToUpload']['type'];
      $tmpName = $_FILES['fileToUpload']['tmp_name'];

      //Print File Details
      echo "Upload: "    . $name . "<br />";
      echo "Type: "      . $type . "<br />";
      echo "Size: "      . ($_FILES["fileToUpload"]["size"] / 1024) . " Kb<br />";
      echo "Temp file: " . $tmpName . "<br />";

      //Save file to server
      $savedFile = "/dataUpload/upload/" . $name;
      $savedFile = $target_file;
      if (file_exists($savedFile)) {  //if file already exists
        echo $name . " already exists. ";
      } else {
        if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
          //Store file in directory
          if( move_uploaded_file($tmpName, $savedFile) ) {
            echo "Stored in: " . $savedFile . "<br />";
          } else {
            echo "Not uploaded<br/>";
          }
        }
      }

      if(($handle = fopen($savedFile, 'r')) !== FALSE) {
        // necessary if a large csv file
        set_time_limit(0);
        $row = 0;
        while(($data = fgetcsv($handle, 0, ',')) !== FALSE) {
          // number of fields in the csv
          foreach($data as $value){
            $csv[$row][] = trim($value);
          }
          // inc the row
          $row++;
        }
        fclose($handle);
      }
    }
  } else {
    echo "No file selected <br />";
  }

  //row 0 contains field id's
  //row 1 contains field names
  $fieldIDs = $csv[0];
  //$catKey = array_search('147.44', $fieldIDs);

  unset($csv[0]);
  unset($csv[1]);
  $tableData = [];
  $APIdata   = [];
  $catArray  = [];

  foreach ($csv as $rowData){
    $faire = $rowData[0];
    $form  = $rowData[1];
    $parentID = (int) $rowData[2];
    if(trim($faire)!='' && trim($form)!=''){
      //echo 'For ' .$faire .' setting form '.$form.'<br/>';
      $randIP = "".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
      $data  = array('form_id'=>$form,'status'=>'active',"id" => "","date_created" => "",'ip'=>$randIP);
      foreach($rowData as $key => $value){
        if($fieldIDs[$key] != ''  && $value !=''){
          $data[$fieldIDs[$key]] = htmlentities($value, ENT_COMPAT,'ISO-8859-1', true);
          echo 'Setting field '.$fieldIDs[$key]. ' to '.$data[$fieldIDs[$key]].'<br/>';
        }
      }
      $childID = GFAPI::add_entry($data);
      $tableData[] = array( 'parentID'=> $parentID,
                            'childID' => $childID,
                            'faire'   => $faire,
                            'form_id' => $form);
    }
    echo '<br/><br/>';
  }

  //now we need to update the database
  //find the end of the $tableData
  $endkey = key( array_slice( $tableData, -1, 1, TRUE ) );
  $contchar = ',';
  $insertRel = '';$insertLead= '';
  //loop thru array to build SQL inserts
  foreach($tableData as $key => $value){
    if($endkey == $key) $contchar = '';
    echo 'parent: '. $value['parentID'].' child: '.$value['childID'].'<br/>';
    $insertRel .= " (".$value['parentID'].", ".$value['childID'].", '".$value['faire']."', '".$value['form_id']."')".$contchar;
    gform_update_meta( $value['childID'], 'res_status','ready' );
    //process new entry
    prcNewEntry($value['childID']);
  }
  // add to the wp_rg_lead_rel table
  $sql = "INSERT INTO wp_rg_lead_rel (`parentID`, `childID`, `faire`, `form`) values " .$insertRel.";";
  $result=mysqli_query($mysqli,$sql) or die("error in SQL ".mysqli_error($mysqli).' '.$sql);
}else{
  ?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
</head>
<body>

  <h2>Update Form entries</h2>
  <form method="post" enctype="multipart/form-data">
    Select File to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload" name="submit">
  </form>
  <br/>Do not upload more than 15 records at a time.<br/>
  It will time out!<br /><br/>
  <ul>
      <li>Note: File format should be CSV</li>
      <li>Row 1: Field ID's</li>
      <li>Row 2: Field Names</li>
      <li>Row 3: Start of Data</li>

      <li>Column A: Faire ID</li>
      <li>Column B: Form ID</li>
      <li>Column C: Parent ID</li>
  </ul>
</body>
</html>
<?php
}


function calculate_signature($string, $private_key) {
    $hash = hash_hmac("sha1", $string, $private_key, true);
    $sig = rawurlencode(base64_encode($hash));
    return $sig;
}
function call_api($data){
    $api_key = '84ed801ad4';
    $private_key = 'cacff8d71d9cc6e';
    $method  = 'POST';
    $domain = $_SERVER['HTTP_HOST'];
    if($domain=='localhost')    $domain .= '/makerfaire';

    $endpoint = $domain.'/gravityformsapi/';
    echo 'sending to '.$endpoint.'<br/>';
    //$route = 'entries';
    $route = 'forms/46/entries';
    $expires = strtotime('+60 mins');
    $string_to_sign = sprintf('%s:%s:%s:%s', $api_key, $method, $route, $expires);
    $sig = calculate_signature($string_to_sign, $private_key);

    $api_call = $endpoint.$route.'?api_key='.$api_key.'&signature='.$sig.'&expires='.$expires;

    $ch = curl_init($api_call);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);

    $result = curl_exec($ch);
    $returnedData = json_decode($result);//201 status indicates it inserted the entry. Should return id of the entry.

    if($returnedData->status==201 || $returnedData->status==200){
      return $returnedData->response;
    }else{
      echo 'There was an error in the call to '.$api_call.'<br/><br/>';
      var_dump($result);
      echo '<Br/><br/>';
      var_dump($returnedData);
      die();
    }
}

//process new entry
function prcNewEntry($entryID){
  global $wpdb;
  if (!class_exists('eventbrite')) {
    require_once('../classes/eventbrite.class.inc');
  }
  $eventbrite = new eventbrite();

  $entry    = GFAPI::get_entry($entryID);
  $form_id  = $entry['form_id'];
  $form     = GFAPI::get_form($form_id);

  //create RMT data
  GFRMTHELPER::gravityforms_makerInfo($entry,$form);

  //generate eventbrite tickets
  /*
    NY16:
      ME - 2 Maker Entry Passes - eid 26455360696, ticket id 52207452(event id 3)
      SC - 2 Comp tickets       - eid 25957796468, ticket id 52164508(event id 4)
      SD - 2 discount tickets   - eid 25957796468, ticket id 52164509(event id 4)
   */

  $tickets = array();
  $tickets[] =  array('ticket_type' => 'ME',
                      'ticket_id'   => '52207452',
                      'hidden'      => 0,
                      'qty'         => 2,
                      'eid'         => 26455360696
      );
  $tickets[] =  array('ticket_type' => 'SC',
                      'ticket_id'   => '52164508',
                      'hidden'      => 0,
                      'qty'         => 2,
                      'eid'         => 25957796468
      );
  $tickets[] =  array('ticket_type' => 'SD',
                      'ticket_id'   => '52164509',
                      'hidden'      => 0,
                      'qty'         => 2,
                      'eid'         => 25957796468
      );

  //generate access code for each ticket type
  $digits = 3;
  $charIP = (string) $entry['ip'];
  $rand   =  substr(base_convert($charIP, 10, 36),0,$digits);
$tickets=array();
  foreach($tickets as $ticket){
    $hidden     = $ticket['hidden'];
    $accessCode = $ticket['ticket_type'] . $entryID . $rand;
    $args = array(
      'id'   => $ticket['eid'],
      'data' => 'access_codes',
      'create' => array(
        'access_code.code'               => $accessCode,
        'access_code.ticket_ids'         => $ticket['ticket_id'],
        'access_code.quantity_available' => $ticket['qty']
      )
    );

    //call eventbrite to create access code
    $access_codes = $eventbrite->events($args);
    if(isset($access_codes->status_code) && $access_codes->status_code==400){
      $response['msg'] =  $access_codes->error_description;
      exit;
    }else{
      $response[$accessCode] = $access_codes->resource_uri;
    }

    //save access codes to db
    $dbSQL = 'INSERT INTO `eb_entry_access_code`(`entry_id`, `access_code`, `hidden`,EBticket_id) '
            . ' VALUES ('.$entryID.',"'.$accessCode.'",'.$hidden.','.$ticket['ticket_id'].')'
            . ' on duplicate key update access_code = "'.$accessCode.'"';

    $wpdb->get_results($dbSQL);
  }
}