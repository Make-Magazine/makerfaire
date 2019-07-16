<?php
include 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include admin email
$sql = 'select display_meta, notifications from wp_gf_form_meta';
if (isset($_GET['formID']))
    $sql .= ' and form_id=' . $_GET['formID'];

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
?>
<!doctype html>

<html lang="en">
    <head>

        <style>

            .detailRow {
                font-size: 1.2em;
                border: 1px solid #98bf21;
            }
            .detailRow div {
                border-right: 1px solid #98bf21;
                padding: 3px 7px;
                background-color: cornsilk;
            }
            .detailRow div:last-child {
                border-right: none;
            }
            .row-eq-height {
                display: -webkit-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
            }
            .header {
                font-weight: bold;
            }
        </style>
        <link rel='stylesheet' id='make-bootstrap-css'  href='http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>

    <body>
        <div style="text-align: center">
            <h2> Gravity Form - Form Notifications </h2>
            <h3>Site Admin Email - <?php echo get_option('admin_email');?></h3>
            <small>
                To display information for a certain form: add ?formID=<i>form id</i> to the end of the url<br/>
            </small>
        </div>
        <div class="clear"></div>
        <div class="container" style="width:95%">

            <table>

                <table  border="1" width="100%">
                    <tr>
                        <td colspan="3">Form information</td>
                        <td colspan="8">Notification information</td>
                    </tr>
                    <tr><td>ID</td>
                        <td>Name</td>
                        <td>Active/Inactive</td>
                        <td>Notification Name</td>
                        <td>Active</td>
                        <td>To</td>
                        <td>BCC</td>
                        <td>Subject</td>
                        <td>From</td>
                        <td>From Name</td>
                        <td>Reply to</td>
                    </tr>  
                    <?php
                    // Loop through the posts
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                        $json = json_decode($row['display_meta']);
                        //var_dump($json);
                        $notifications = json_decode($row['notifications']);

                        foreach ($notifications as $notification) {
                            //var_dump($notification);
                            ?>
                            <tr>
                                <td><?php echo $json->id; ?></td>
                                <td><?php echo $json->title; ?></td>
                                <td><?php echo (isset($json->is_active)?($json->is_active ? 'Active': 'Not-Active'):''); ?></td>
                                <td><?php echo $notification->name; ?></td>
                                <td><?php echo (isset($notification->isActive)?($notification->isActive ? 'Active' : 'Not-Active'):''); ?></td>
                                <td><?php echo (isset($notification->to)?$notification->to:''); ?></td>
                                <td><?php echo (isset($notification->bcc)?$notification->bcc:''); ?></td>
                                <td><?php echo (isset($notification->subject)?$notification->subject:''); ?></td>
                                <td><?php echo (isset($notification->from)?$notification->from:''); ?></td>
                                <td><?php echo (isset($notification->fromName)?$notification->fromName:''); ?></td>
                                <td><?php echo (isset($notification->replyTo)?$notification->replyTo:''); ?></td>
                            </tr>                                
                            <?php
                        } //end foreach notification
                    } //end while form
                    ?>
                </table>
        </div>
    </body>
</html>
<?php

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}