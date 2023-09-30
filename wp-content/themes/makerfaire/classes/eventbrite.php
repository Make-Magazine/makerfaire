<?php
/**
 * Http client used to perform requests on Eventbrite API.
 */

class eventbrite
{
    protected $token;
    const EVENTBRITE_APIv3_BASE = "https://www.eventbriteapi.com/v3";

    /**
     * Constructor.
     *
     * @param string $token the user's auth token.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
    public function get($path, array $expand = array(), array $query_params = array())
    {
        return $this->request($path, $query_params, $expand, $httpMethod = 'GET');
    }
    public function post($path, array $data = array())
    {
        return $this->request($path, $data, array(), $httpMethod = 'POST');
    }
    public function delete($path, array $data = array())
    {
        return $this->request($path, $data, array(), $httpMethod = 'DELETE');
    }
    public function request($path, $body, $expand, $httpMethod = 'GET')
    {
        // I think this is the only header we need.  If there is a need
        // to pass more headers to the request, we could add a parameter
        // called headers to this function and combine whatever headers are passed
        // in with this header.
        $headers = $this->get_default_headers();
        $content = NULL;

        $url = self::EVENTBRITE_APIv3_BASE . $path . '?token=' . $this->token;

        // On POST calls json encode the body
        if (!empty($body) && $httpMethod == 'POST') {
            $data = json_encode($body);

            $headers["Content-Type"] =  "application/json";
            $content = $data;
        }

        // On GET calls convert body to query_parmas
        if (!empty($body) && $httpMethod == 'GET') {
            $query_params = http_build_query($body, null, '&');
            $url = $url . '&' . $query_params;
        }

        if (!empty($expand)) {
            $expand_str = join(',', $expand);
            $url = $url . '&expand=' . $expand_str;
        }

        $options = array(
            'http'=>array(
                'method'=>$httpMethod,
                'header'=>$this->compile_header($headers),
                'content'=>$content,
                'ignore_errors'=>true
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        /* this is where we will handle connection errors. Eventbrite errors are a part of the response payload. We return errors as an associative array. */
        $response = json_decode($result, true);

        if ($response == NULL) {
            $response = array();
        }
        $response['response_headers'] = $http_response_header;
        return $response;
    }

    public function get_default_headers()
    {
        return [
            'User-Agent' => 'eventbrite-sdk-php'
        ];
    }

    public function compile_header(array $headers)
    {
        $header = [];
        foreach ($headers as $k => $v) {
            $header[] = $k . ': ' . $v;
        }
        return implode("\r\n", $header);
    }
}
