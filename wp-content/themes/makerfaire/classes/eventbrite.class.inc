<?php
  class eventbrite {
    var $endpoint = 'https://www.eventbriteapi.com/v3/';
    var $token    = 'DGP75OB7ME3EQO3OIL'; //oauth token



    function __construct() {
      //$this->token = $oauth_token;
    }

    function __call($method, $vars) {
      $args = $vars[0];
      $curl_post_data  = '';
      if(isset($args['create'])){
        $curl_post_data = $args['create'];
        unset ($args['create']);
      }

      // Get the URI we need.
      $uri = (is_array($args)?implode("/",$args):'');
      // Construct the full URL.
      $request_url = $this->endpoint . $method.'/'.$uri;

      //send post request
      $resp = $this->curl_call($request_url,$curl_post_data);

      // Return it as arrays/objects.
      return json_decode($resp);
    }

    function curl_call($service_url,$curl_post_data){
      $service_url .= '/';
      $curl = curl_init($service_url);
      curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $this->token ) );
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
      if($curl_post_data!=''){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
      }

      $curl_response = curl_exec($curl);
      if ($curl_response === false) {
          $info = curl_getinfo($curl);
          curl_close($curl);
          die('error occured during curl exec. Additioanl info: ' . var_export($info));
      }

      curl_close($curl);

      return $curl_response;
    }
  }