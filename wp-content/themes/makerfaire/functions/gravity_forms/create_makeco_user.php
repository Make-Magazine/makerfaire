<?php

add_action( 'gform_after_submission', 'create_makeco_user', 10, 2 );
function create_makeco_user( $entry, $form ) {
    if($form['form_type'] == 'Master') {
        $url = "https://make.co/wp-json/wp/v2/users";
        $headers = array(
            'Accept: application/json', 
            'Content-Type: application/json',
        );
        $data = [
            'username' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $entry['98'])), //usernames must be lowercase and can't have special characters
            'password' => 'Ch@ngeMe1!2@3#',
            'email' => $entry['98']
        ];
        $userpass = MAKECO_API_USER.":".MAKECO_API_PASS;
        $result = postCurl($url, $headers, json_encode($data), "POST", $userpass);
        //error_log(print_r($result, TRUE));
    }
}
