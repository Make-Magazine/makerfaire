<?php
//include '../../../../wp-load.php';
 include 'db_connect.php';
global $wpdb;

$fieldID  = (isset($_GET['fieldID']) ? $_GET['fieldID'] : '');
$findme   = (isset($_GET['findme']) ? $_GET['findme'] : '');
if ($findme != '')
    echo 'looking for ' . $findme . ' in fieldID ' . $fieldID . '<br/>';
global $wpdb;

$count = 0;

$formType = array('Exhibit', 'Presentaton','Performance','Startup Sponsor','Sponsor','Workshop');
    $formResults = $wpdb->get_results('select display_meta, form_id from wp_gf_form_meta', ARRAY_A);

    $formArray = array();
    foreach ($formResults as $formrow) {
        $form_id = $formrow['form_id'];
        $fieldData = '';
        
        $output = true;
        $json = json_decode($formrow['display_meta']);
        $form_type = (isset($json->form_type) ? $json->form_type : '');
        if (!in_array($form_type,$formType)){
            continue; //move on to next record
        }
                
        //if we are searching for a specific field id, hide the output unless we find it
        $output = false;
        foreach ($json->fields as $field) {
            if ($fieldID != '') {
                if ($field->id != $fieldID) {
                    continue;
                }
            }
            //if($field->id==$fieldID){
        
            $labelpos = true;
            $descpos = true;
            if ($findme != '') {
                $labelpos = strpos($field->label ?? '', $findme);
                $descpos = strpos($field->description ?? '', $findme);
                //if($labelpos !== false) echo 'found in label<br/>';
                //if($descpos !== false) echo 'found in description<br/>';
            }

            if ($descpos !== false || $labelpos !== false) {
                //findme phrase was found in either the label or description or not set, return data
                $fieldData = (isset($field->label) ? $field->label : '') . '<br/>' . (isset($field->description) ? $field->description : '');
                //if we are not looking for a specific field, add the field id to the beginning so we know where we found the phrase
                if($fieldID=='') $fieldData = 'Field ' .$field->id.' = '.$fieldData;
                //if($findme !='') echo 'output found<br/>';
                $output = true; //show the output
                break;
            } else {
                $output = false;
                //if($findme !='') echo 'output not found in blog '.$blogID.' and form '.$form_id. '<br/>';
            }
            //
            //}
        }
        //}
        
        if ($output) {            
            $formArray[] = array(
                'form_id' => $form_id,
                'form_type' => (isset($json->form_type) ? $json->form_type : ''),
                'form_name' => $json->title,
                'date_created' => $json->date_created,
                'is_active' => $json->is_active,
                'is_trash' => $json->is_trash,
                'field' => $fieldData
            );
        }
    }
    if (!empty($formArray)) {
        $blogArray[] = array(
            'blog_id' => $blogID,
            'blog_name' => $blogrow['domain'],
            'forms' => $formArray
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
            foreach ($blogArray as $blogData) {
                echo '<b>' . $blogData['blog_id'] . ' - ' . $blogData['blog_name'] . '</b>';
                ?>

                <div style="clear:both">
                    <table width="100%">
                        <tr>
                            <td width="2.5%">Blog ID</td>
                            <td width="2.5%">Form ID</td>
                            <td width="5%">Form Type</td>
                            <td width="50%">Name</td>
                            <td width="15%">Date Created</td>
                            <td width="5%">Active?</td>
                            <td width="5%">Deleted?</td>            
                            <td width="15"><?php echo ($fieldID != '' ? 'Field ' . $fieldID : 'All Fields'); ?></td>
                        </tr>
                        <?php
                        foreach ($blogData['forms'] as $formData) {
                            echo '<tr>';
                            echo '<td>' . $blogData['blog_id'] . '</td>';
                            echo '<td>' . $formData['form_id'] . '</td>';
                            echo '<td>' . $formData['form_type'] . '</td>';
                            echo '<td>' . $formData['form_name'] . '</td>';
                            echo '<td>' . $formData['date_created'] . '</td>';
                            echo '<td>' . ($formData['is_active'] == 0 ? 'No' : '') . '</td>';
                            echo '<td>' . ($formData['is_trash'] == 1 ? 'Yes' : '') . '</td>';
                            echo '<td>' . $formData['field'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                    <br/>
                </div>
            <?php } ?>
        </div>
    </body>
</html>