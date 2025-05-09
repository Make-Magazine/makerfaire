<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
    </ul>
  </body>
</html>
<?php
include 'db_connect.php';


function calculate_signature($string, $private_key) {
    $hash = hash_hmac("sha1", $string, $private_key, true);
    $sig = rawurlencode(base64_encode($hash));
    return $sig;
}
function call_api($data,$form){
    $api_key      = '84ed801ad4';
    $private_key  = 'cacff8d71d9cc6e';
    $method  = 'POST';
    $domain = $_SERVER['HTTP_HOST'];
    if($domain=='localhost')    $domain .= '/makerfaire';

    //endpoint = 'http://makerfaire.staging.wpengine.com/gravityformsapi/';
    //$endpoint = 'http://makerfaire.com/gravityformsapi/';
    $endpoint = $domain.'/gravityformsapi/';
    echo 'sending to '.$endpoint.'<br/>';
    //$route = 'entries';
    $route = 'forms/'.$form.'/entries';
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
      foreach($returnedData->response as $newID){
        echo 'added '.$newID.'<br/>';
      }
      return $returnedData->response;
    }else{
        echo 'There was an error in the call to '.$api_call.'<br/><br/>';
        var_dump($result);
        echo '<Br/><br/>';
        var_dump($returnedData);
        die();
    }
}

ini_set("auto_detect_line_endings", "1");
//$success = GFAPI::update_entry_field( $entry_id, $input_id, $value );
if (isset($_POST["submit"]) ) {
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
      $nameArr = explode('.', $name);

      $ext = strtolower(end($nameArr));

      $type = $_FILES['fileToUpload']['type'];
      $tmpName = $_FILES['fileToUpload']['tmp_name'];

      //Print File Details
      echo "Upload: "    . $name . "<br />";
      echo "Type: "      . $type . "<br />";
      echo "Size: "      . ($_FILES["fileToUpload"]["size"] / 1024) . " Kb<br />";
      echo "Temp file: " . $tmpName . "<br />";

      //Save file to server
       //if file already exists
      $savedFile = "/dataUpload/upload/" . $name;
      $savedFile = $target_file;
       if (file_exists($savedFile)) {
        echo $name . " already exists. ";
       }else {
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
    unset($rowData[0]);
    unset($rowData[1]);
    if(trim($faire)!='' && trim($form)!=''){
      //echo 'For ' .$faire .' setting form '.$form.'<br/>';
      $data  = array('form_id'=>$form,'status'=>'active',"id" => "","date_created" => "");
      foreach($rowData as $key => $value){
        if($fieldIDs[$key] != ''  && $value !=''){
          $data[$fieldIDs[$key]] = htmlentities($value);
          //echo 'Setting field '.$fieldIDs[$key]. ' to '.htmlspecialchars($value).'<br/>';
        }
      }
      $APIdata[] = $data;
    }
  }
  $childID = call_api ($APIdata, $form);
}