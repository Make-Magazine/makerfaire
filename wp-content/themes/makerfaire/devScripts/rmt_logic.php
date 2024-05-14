<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';
$form_id = (isset($_GET['form']) ? $_GET['form'] : '');
$form    = ($form_id != '' ? gfapi::get_form($form_id) : '');
//convert this into a usable array
$field_array = array();
if(isset($form['fields'] )){
    foreach ($form['fields'] as $field) {
        $field_array[$field['id']] = $field;
      }
}

$sql = 'SELECT wp_rmt_rules.id as rule, wp_mf_form_types.form_type, rmt_field, wp_rmt_resources.type , wp_rmt_rules.value, field_number, operator, wp_rmt_rules_logic.value as field_value FROM `wp_rmt_rules` left outer join wp_rmt_resources on wp_rmt_rules.rmt_field=wp_rmt_resources.id left outer join wp_rmt_rules_logic on wp_rmt_rules_logic.rule_id = wp_rmt_rules.id left outer join wp_mf_form_types on wp_mf_form_types.id = wp_rmt_rules.form_type ORDER BY `rule` ASC';

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
?>
<!doctype html>

<html lang="en">

<head>
    <style>
        h1,
        .h1,
        h2,
        .h2,
        h3,
        .h3 {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        ul,
        ol {
            margin-top: 0 !important;
            margin-bottom: 0px !important;
            padding-top: 0px !important;
            padding-bottom: 0px !important;
        }

        table {
            font-size: 14px;
        }

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

        #headerRow td,
        .detailRow td {
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
        .grey {
            background-color: #F5F5F5;
        }
        .blue {
            background-color: #DEEFF5;
        }
    </style>
    <link rel='stylesheet' id='make-bootstrap-css' href='https://makerfaire.com/wp-content/themes/makerfaire/css/bootstrap.min.css' type='text/css' media='all' />
    <link rel='stylesheet' id='font-awesome-css' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css?ver=2.819999999999997' type='text/css' media='all' />
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container" style="width:100%; line-height: 1.3em">
        <div style="clear:both"></div>
        <div style="text-align: center">
            <div style="font-size: 12px;line-height: 12px;">
            <i>add ?form=xxx to the end of the URL to specify a specific form - ie: makerfaire.com/wp-content/themes/makerfaire/devScripts/rmt_logic.php?form=9</i>
            </div>
        </div>
        <table style="margin: 10px 0;">
            <thead>
                <tr id="headerRow">
                    <td style="width:  3%">Rule #</td>                                    
                    <td>If Field</td>
                    <td>Operator</td>
                    <td>Value</td>
                    <td>Resource Set</td>
                    <td>Resource Value</td>
                </tr>
            </thead>
            <?php
            $save_rule=0;
            $row_color='';
            // Loop through the posts
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                if($row['rule']!=$save_rule){
                    $save_rule = $row['rule'];
                    $row_color = ($row_color=='grey' ? 'blue' : 'grey');
                }
            ?>
                <tr class="detailRow <?php echo $row_color;?>">
                    <td class="tcenter"><?php echo $row['rule']; ?></td>                                        
                    <td>
                        <?php 
                        if(isset($field_array[$row['field_number']] )){
                            echo $field_array[$row['field_number']]['label'];
                        }else{
                            echo $row['field_number'];                        
                        } 
                        ?>
                    </td>
                    <td><?php echo $row['operator']; ?></td>
                    <td><?php echo $row['field_value']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['value']; ?></td>
                </tr>
            <?php
            }

            ?>
        </table>
    </div>    
</body>

</html>