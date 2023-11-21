<?php

use ParagonIE\Sodium\Core\Curve25519\Ge\P2;

include '../../../../wp-load.php';
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
      
    <?php
    echo 'start of code<br/>';
    $users = get_users();
    foreach($users as $user){
        if( isset($user->ID) && $user->ID != 0 ) {
            error_log("we are a valid user");
            $user_meta = get_user_meta($user->ID);
            if(isset($user_meta['wp_auth0_obj'])) {
                //user data has been stored in multiple places over the lifespan of auth0. need to check everywhere
                $auth0_user_data = json_decode($user_meta['wp_auth0_obj'][0], true);
                if(isset($auth0_user_data['identities'])){
                    foreach($auth0_user_data['identities'] as $identity){                    
                        if($identity['connection'] == "Username-Password-Authentication"){
                            echo $auth0_user_data['email'].' found on "Username-Password-Authentication"<br/>';
                            wp_auth0_delete_auth0_object( $user->ID );
                        }
                    }
                }                        
            }
        }
    }
    
    echo 'end of code';
    ?>
    </body>
</html>