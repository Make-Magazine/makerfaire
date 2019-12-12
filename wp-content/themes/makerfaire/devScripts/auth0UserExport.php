<?php //  ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
   </head>
   <body>
      <h2 style='text-align: center'>Auth0 Users</h2>
   </body>
</html>

<?php
include '../../../../wp-load.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

//First do the authentication
$url = "https://makermedia.auth0.com/oauth/token";
$post_data = "{\"grant_type\":\"client_credentials\",\"client_id\": \"Ya3K0wmP182DRTexd1NdoeLolgXOlqO1\",\"client_secret\": \"eu9e8LC7fvrKb9ou5JglKdIv67QDvhkiMg32vm0q433SMXD5PW3elCV7OuiSFs6n\",\"audience\": \"https://makermedia.auth0.com/api/v2/\"}";
$authRes = curlCall($url, $post_data);

if (isset($authRes->access_token)) {
   echo 'Authenticated<br/>';
   $token = $authRes->access_token;   
   //echo 'returned token is ' . $token;
   $jobID = (isset($_GET['jobID'])?$_GET['jobID']:'');
   if($jobID==''){
      //get connections   
      echo '<hr>';
      echo 'Get Connections result<br/>';
      $post_data = '';
      $url = "https://makermedia.auth0.com/api/v2/connections";
      $authRes = curlCall($url, $post_data, $token);        
      foreach($authRes as $result){
         if($result->name==="Username-Password-Authentication"){
            $connection = $result->id;
         }
      }
      echo '$connection is '.$connection.'<br/>';

      // submit a job to get all auth0 users
      $url = "https://makermedia.auth0.com/api/v2/jobs/users-exports";
      $post_data = "{\"connection_id\": \"$connection\", \"format\": \"csv\", "
              . "\"fields\": ["
                        . "{\"name\": \"user_id\"}, "
                        . "{\"name\": \"name\"}, "
                        . "{\"name\": \"email\"}, "
                        . "{\"name\": \"created_at\"}, "
                        . "{\"name\": \"last_login\"}, "
                        . "{\"name\": \"user_metadata.first_name\"}, "
                        . "{\"name\": \"user_metadata.last_name\"}, "
                        . "{\"name\": \"user_metadata.firstname\"}, "
                        . "{\"name\": \"user_metadata.lastname\"}, "
                        . "{\"name\": \"email_verified\"}, "
                        . "{\"name\": \"loginsCount\"}, "
                        . "{ \"name\": \"identities[0].connection\", \"export_as\": \"provider\" }"
              . "]}";

      $authRes = curlCall($url, $post_data, $token);

      $jobID = $authRes->id;
      echo '<hr>';
      echo 'Submitted Job ID '.$jobID.'<br/>';
      echo 'To Pull the results click <a href="https://makerfaire.com/wp-content/themes/makerfaire/devScripts/auth0UserExport.php?jobID='.$jobID.'">here'.'</a>';
   }else{
       //to get resultsjobID
       //Check the status of the job         
        $url = "https://makermedia.auth0.com/api/v2/jobs/".$jobID;
        $post_data = "";
        $authRes = curlCall($url, $post_data, $token);        
        if($authRes->status==='completed'){
           echo 'Get your results <a href="'.$authRes->location.'">Here</a>';
        }else{
           echo 'Job Status = '.$authRes->status.'. Please refresh';
           
        }
   }
   
   //var_dump($authRes);
   die();
   echo '<hr>';
   echo 'Auth0 returned users<br/>';
   if(isset($authRes->error)){      
      echo'Error on CURL call: ';
      if(isset($authRes->statusCode))  echo 'Status Code = '.$authRes->statusCode.'<br/>';
      if(isset($authRes->error))       echo ' '.$authRes->error. ' ';
      if(isset($authRes->message))     echo ' - '.$authRes->message.' ';
      if(isset($authRes->errorCode))   echo  '('.$authRes->errorCode.')';
   }else {
      var_dump($authRes);
      die();
      $outCSV = array();
      $keyArr = array('date');

      $my_file = 'auth0Users-' . date('m-d-Y_hia') . '.csv';   
      echo 'Creating '.$my_file.'<br/>';
      $output = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
      
      foreach ($authRes as $k => $authLog) {
         $result = [];
         $authLog = json_decode(json_encode($authLog), true);  //translate multi dimensional onject to array
         //flatten multi dimensional array
         array_walk_recursive($authLog, function($item, $key) use (&$result) {
            $result[$key] = $item;
         });
         $arr = array_keys($result);
         foreach ($arr as $keys) {
            if (!in_array($keys, $keyArr)) {
               $keyArr[] = $keys;
            }
         }
         $outCSV[] = $result;
      }

      //var_dump($keyArr);
      fputcsv($output, $keyArr);  //write keydata to CSV

      foreach ($outCSV as $csv) {
         foreach ($keyArr as $key) {
            if (isset($csv[$key])) {
               $outData[$key] = $csv[$key];
            } else {
               $outData[$key] = '';
            }
         }

         fputcsv($output, $outData);  //write to CSV
      }
      fclose($output);
   }
   /*
    *
     ["date"]=>
     string(24) "2018-07-06T20:18:05.635Z"
     ["type"]=>
     string(3) "fsa"
     ["description"]=>
     string(14) "Login required"
     ["client_id"]=>
     string(32) "0sR3MQz8ihaSnLstc1dABgENHS5PQR8d"
     ["client_name"]=>
     string(8) "Makezine"
     ["ip"]=>
     string(13) "73.41.175.108"
     ["user_agent"]=>
     string(35) "Chrome 67.0.3396 / Mac OS X 10.13.5"
     details->qs->client_id=>"0sR3MQz8ihaSnLstc1dABgENHS5PQR8d"
     details->qs->response_type=>"token id_token"
     details->qs->redirect_uri"]=>
     string(35) "https://makezine.com/authenticated/"
     details->qs->scope"]=>
     string(14) "openid profile"
     details->qs->audience"]=>
     string(37) "https://makermedia.auth0.com/userinfo"
     details->qs->state"]=>
     string(32) "d~wjzbFqiqCA8Qn2eLIqaTV18dDfCJ-e"
     details->qs->nonce"]=>
     string(32) "DtnFaHJ14mxs95ohGcAfBnVAAzN1YAFT"
     details->qs->response_mode"]=>
     string(11) "web_message"
     details->qs->prompt"]=>
     string(4) "none"
     details->qs->auth0Client"]=>
     string(52) "eyJuYW1lIjoiYXV0aDAuanMiLCJ2ZXJzaW9uIjoiOS4zLjEifQ=="
     }
     ["connection"]=>
     NULL
     ["error"]=>
     object(stdClass)#2546 (3) {
     ["message"]=>
     string(14) "Login required"
     ["oauthError"]=>
     string(14) "login_required"
     ["type"]=>
     string(19) "oauth-authorization"
     }
     }
     ["hostname"]=>
     string(20) "makermedia.auth0.com"
     ["audience"]=>
     string(37) "https://makermedia.auth0.com/userinfo"
     ["scope"]=>
     array(2) {
     [0]=>
     string(6) "openid"
     [1]=>
     string(7) "profile"
     }
     ["_id"]=>
     string(56) "90020180706201805635973136342917534877417428372661731442"
     ["log_id"]=>
     string(56) "90020180706201805635973136342917534877417428372661731442"
     ["isMobile"]=>
     bool(false) */
}

function curlCall($service_url, $curl_post_data, $token = '') {
   $curl = curl_init();

   curl_setopt_array($curl, array(
       CURLOPT_URL => $service_url,
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => "",
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 30,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   ));

   if ($curl_post_data !== '') {
      curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      if($token!==''){         
         curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer " . $token, "content-type: application/json"));
      }else{
         curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
      }
   } else {
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer " . $token, "content-type: application/json"));
   }


   $curl_response = curl_exec($curl);
   $err           = curl_error($curl);

   curl_close($curl);

   if ($err) {
      echo "cURL Error #:" . $err;
   }
   
   $authRes = json_decode($curl_response);
   if(isset($authRes->error)){      
      echo'Error on CURL call: ';
      if(isset($authRes->statusCode))  echo 'Status Code = '.$authRes->statusCode.'<br/>';
      if(isset($authRes->error))       echo ' '.$authRes->error. ' ';
      if(isset($authRes->message))     echo ' - '.$authRes->message.' ';
      if(isset($authRes->errorCode))   echo  '('.$authRes->errorCode.')';
   }
   return $authRes;
}
