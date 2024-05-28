<?php
include '../../../../wp-load.php';
//include '../classes/gf-rmt-helper.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$form_id = (isset($_GET['form']) ? $_GET['form'] : '260');
$page = (isset($_GET['page']) ? $_GET['page'] : 1);
$limit = (isset($_GET['limit']) ? $_GET['limit'] : 30);
$offset = ($page != 1 ? (($page - 1) * $limit) : 0);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>

        <?php
        global $wpdb;        
                 
        
                $count=0;
                $entries = GFAPI::get_entries($form_id,array('status' => 'active'), null, array( 'offset' => 0, 'page_size' => 999 ), $count);                                
                
                if($count>999){
                    echo 'WARNING!! More than 999 entries found. Total found = '.$count.'<br/>';
                    die('mission aborted');
                }
                echo '&emsp;found '.count($entries).' entries<br/>';                        
                $approved = 0;

                //loop through entries
                foreach ($entries as $entry) {   
                    GFRMTHELPER::updateMakerTables($entry['id']);                                                            
                }
                echo '&emsp;wrote '.$count.' entries<br/>';                  
                        
            
        ?>
    </body>
</html>
