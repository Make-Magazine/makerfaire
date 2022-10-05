<?php
include '../../../../wp-load.php';
// include 'db_connect.php';
global $wpdb;

$findme  = (isset($_GET['findme']) ? $_GET['findme'] : '');
if ($findme != '') {
    echo 'looking for ' . $findme . '<br/>';
}else{
  echo 'Please enter your search term in the URL using the findme variable';
  die();
}
global $wpdb;


$table = 'wp_posts';
$postArray = array();

$postResults = $wpdb->get_results('select ID, post_title, post_date, post_status, post_type from ' . $table.' where post_content like "%'.$findme.'%" order by post_date', ARRAY_A);

foreach ($postResults as $postRow) {
  $postArray[] = array(
      'post_id'    => $postRow['ID'],
      'post_date'  => $postRow['post_date'],
      'post_status'  => $postRow['post_status'],
      'post_type'  => $postRow['post_type'],
      'post_title' => $postRow['post_title']
  );
}


if ((isset($_GET['debug']) && trim($_GET['debug']) != '')) {
    $debug = 1;
    echo 'Turning on DEBUG mode <br>';
}
?>
<!doctype html>

<html lang="en">
    <head>
        <style>
            h1, .h1, h2, .h2, h3, .h3 {
                margin-top: 10px !important;
                margin-bottom: 10px !important;
            }
            ul, ol {
                margin-top: 0 !important;
                margin-bottom: 0px !important;
                padding-top: 0px !important;
                padding-bottom: 0px !important;
            }
            table {font-size: 14px;}
            #headerRow {
                font-size: 1.2em;
                border: 1px solid #98bf21;
                padding: 5px;
                background-color: #A7C942;
                color: #fff;
                text-align: center;
            }

            .detailRow {
                font-size: 1.2em;
                border: 1px solid #98bf21;
            }
            #headerRow td, .detailRow td {
                border-right: 1px solid #98bf21;
                padding: 3px 7px;
                vertical-align: baseline;
            }
            .detailRow td:last-child {
                border-right: none;
            }
            .row-eq-height {
                display: -webkit-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
            }
            .tcenter {
                text-align: center;
            }
            td, th {
                padding: 5px !important;
                border: thin solid lightgrey;
            }
        </style>
        <link rel='stylesheet' id='make-bootstrap-css'  href='http://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' id='font-awesome-css'  href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=2.819999999999997' type='text/css' media='all' />
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="container" style="width:100%; line-height: 1.3em">
            <?php
            if(!empty($postArray)){
              ?>
              <div style="clear:both">
                <table width="100%">
                    <tr>
                        <td width="5%">Post ID</td>
                        <td width="15%">Post Date</td>
                        <td width="5%">Post Status</td>
                        <td width="15%">Post Type</td>
                        <td width="60%">Post Name</td>
                    </tr>
                    <?php
                    foreach ($postArray as $postData) {
                      echo '<tr>';
                        echo '<td>' . $postData['post_id']    . '</td>';
                        echo '<td>' . $postData['post_date']    . '</td>';
                        echo '<td>' . $postData['post_status']    . '</td>';
                        echo '<td>' . $postData['post_type']    . '</td>';
                        echo '<td><a target="_blank" href="' . get_site_url() . '/?p='.$postData['post_id'].'">' . $postData['post_title'] . '</a></td>';
                      echo '</tr>';
                    }
                    ?>
                  </table>
                </div>
              <?php
            }
            ?>
        </div>
    </body>
</html>
