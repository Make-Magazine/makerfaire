<?php
include '../../../../wp-load.php';
//include '../classes/gf-rmt-helper.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        set_time_limit(60000);
        $return = cron_expofp_sync(17504);
        echo $return.'<br/>';
        echo 'end of job<br/>';
        ?>
    </body>
</html>