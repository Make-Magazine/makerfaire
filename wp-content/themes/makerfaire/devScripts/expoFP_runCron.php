<?php
include '../../../../wp-load.php';
//include '../classes/gf-rmt-helper.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('EXPOFP_TOKEN', '9184lcd016f2a3c23afda141b19e6137afa2f9019113ee0d810d8f85b88446299d702');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php
        //call function
        echo 'start of job<br/>';
        $return = cron_expofp_sync(17504);
        echo $return.'<br/>';
        echo 'end of job<br/>';
        ?>
    </body>
</html>