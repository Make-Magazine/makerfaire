<?php
include '../../../../wp-load.php';
global $wpdb;
$delete = (isset($_GET['delete'])?TRUE:FALSE);
$deleteall = (isset($_GET['deleteall'])?TRUE:FALSE);

$output = 'start of update<br/>';
$userSQL = "SELECT user_id, wp_users.user_email, meta_key, meta_value as auth0_obj 
FROM `wp_usermeta` 
left outer join wp_users on wp_usermeta.user_id = wp_users.id 
where meta_key like '%auth0_obj%' 
ORDER BY `wp_usermeta`.`user_id` ASC";

$users = $wpdb->get_results($userSQL,ARRAY_A);
$count = 0;
foreach ($users as $user) {    
    $auth0_user_data = json_decode($user['auth0_obj'], true);



    if (isset($auth0_user_data['identities'])) {
        foreach ($auth0_user_data['identities'] as $identity) {
            if (($identity['connection'] != "DB-Make-Community" && $identity['connection'] != 'google-oauth2') || $deleteall) {
                $wp_prefix = substr($user['meta_key'],0,5);
                $output.= $user['user_id'].' ('.$user['user_email'] . ') found on "' . $identity['connection'] . 
                '" meta_key = '.$user['meta_key'] .'<br/>';
                $count++;                                
            }
        }
    }
    if($deleteall){
        //base site
        wp_auth0_delete_auth0_object($user['user_id']);                                
    }    
}

?>

<!DOCTYPE html>
<html>
    <head>
      <meta charset="UTF-8">
    </head>
    <body>
      
    <?php
    
    echo 'Found '.$count.' users to update<br/>';
    echo '<hr>';
    echo $output;    
    ?>
    </body>
</html>