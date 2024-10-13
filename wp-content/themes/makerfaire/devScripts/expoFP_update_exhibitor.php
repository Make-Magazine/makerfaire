<?php
include '../../../../wp-load.php';
//include '../classes/gf-rmt-helper.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$form_id = (isset($_GET['form']) ? $_GET['form'] : '278');
$form = GFAPI::get_form($form_id);
$page = (isset($_GET['page']) ? $_GET['page'] : 1);
$limit = (isset($_GET['limit']) ? $_GET['limit'] : 30);
$offset = ($page != 1 ? (($page - 1) * $limit) : 0);

global $wpdb;        
                        
$count=0;
$entries = GFAPI::get_entries($form_id,array('status' => 'active'), null, array( 'offset' => $offset, 'page_size' => $limit ), $count);                                

if($count>999){
    echo 'WARNING!! More than 999 entries found. Total found = '.$count.'<br/>';
    die('mission aborted');
}
                
$expoFP_count = 0;

//loop through entries
foreach ($entries as $entry) {   
    //GFRMTHELPER::buildRmtData($entry, $form);    
    update_expofp_exhibitor($form, $entry['id']);
    $expoFP_count++;    
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>

        <?php
        echo '&emsp;found '.count($entries).' entries<br/>';        
        echo '&emsp;wrote '.$expoFP_count.' entries to expoFP<br/>';                                                      
        ?>
    </body>
</html>