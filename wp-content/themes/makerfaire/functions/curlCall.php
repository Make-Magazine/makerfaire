<?php
define('NETWORK_HOME_URL',network_home_url());
////////////////////////////////////////////////////////////////////
// our good old postCurl call
////////////////////////////////////////////////////////////////////
function postCurl($url, $headers = null, $datastring = null,$type="POST") {
	$ch = curl_init($url);

	if (strpos(NETWORK_HOME_URL, '.local') > -1  || strpos(NETWORK_HOME_URL, '.test') > -1) { // wpengine local environments
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT    5.0'); 
	}

	//curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	//curl_setopt($ch, CURLOPT_STDERR, $verbose = fopen('php://temp', 'rw+'));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

	if($datastring != null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
	}

	if ($headers != null) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	$response = curl_exec($ch);

    //echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
	if(curl_errno($ch)){
	  throw new Exception(curl_error($ch));
	}

	curl_close($ch);
    return $response;
}