<?php // ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>
    <h2 style='text-align: center'>Auth0 logs</h2>
  </body>
</html>

<?php
include '../../../../wp-load.php';

//First do the authentication
$url = "https://makermedia.auth0.com/oauth/token";
$post_data = "{\"grant_type\":\"client_credentials\",\"client_id\": \"Ya3K0wmP182DRTexd1NdoeLolgXOlqO1\",\"client_secret\": \"eu9e8LC7fvrKb9ou5JglKdIv67QDvhkiMg32vm0q433SMXD5PW3elCV7OuiSFs6n\",\"audience\": \"https://makermedia.auth0.com/api/v2/\"}";
$authRes = curlCall($url, $post_data);

if(isset($authRes->access_token)){
  echo 'Authenticated<br/>';
  $token = $authRes->access_token;
  echo 'returned token is '.$token;
  //get auth0 logs
  $url = "https://makermedia.auth0.com/api/v2/logs";
  $post_data = '';
  $authRes = curlCall($url, $post_data, $token);
  echo 'Log request result<br/>';
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
    ["details"]=>
    object(stdClass)#2544 (4) {
      ["body"]=>
      object(stdClass)#2543 (0) {
      }
      ["qs"]=>
      object(stdClass)#2545 (10) {
        ["client_id"]=>
        string(32) "0sR3MQz8ihaSnLstc1dABgENHS5PQR8d"
        ["response_type"]=>
        string(14) "token id_token"
        ["redirect_uri"]=>
        string(35) "https://makezine.com/authenticated/"
        ["scope"]=>
        string(14) "openid profile"
        ["audience"]=>
        string(37) "https://makermedia.auth0.com/userinfo"
        ["state"]=>
        string(32) "d~wjzbFqiqCA8Qn2eLIqaTV18dDfCJ-e"
        ["nonce"]=>
        string(32) "DtnFaHJ14mxs95ohGcAfBnVAAzN1YAFT"
        ["response_mode"]=>
        string(11) "web_message"
        ["prompt"]=>
        string(4) "none"
        ["auth0Client"]=>
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
    bool(false)*/

}

function curlCall($service_url,$curl_post_data,$token=''){
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $service_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  ));

  if($curl_post_data !== ''){
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/json"));
  }else{
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer ".$token ,"content-type: application/json"));
  }


  $curl_response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  }

  return json_decode($curl_response);
}