<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';
$form_id    = (isset($_GET['form']) ? $_GET['form'] : '');
$form       = ($form_id != '' ? gfapi::get_form($form_id) : '');
$form_type  = (isset($form['form_type']) ? $form['form_type'] : '');

//convert this into a usable array
$field_array = array();
if (isset($form['fields'])) {
    foreach ($form['fields'] as $field) {
        $field_array[$field['id']] = $field;
    }
}

$sql =  'SELECT wp_rmt_rules.id as rule, wp_mf_form_types.form_type, rmt_field, rmt_type, ' .
    'if(rmt_type="resource", wp_rmt_resources.type,wp_rmt_entry_att_categories.category) as rmt_field_text, ' .
    'if(rmt_type="resource", wp_rmt_rules.value, if(wp_rmt_rules.comment<>"", wp_rmt_rules.comment, wp_rmt_rules.value)) as rmt_field_value,  '.
    'field_number, operator, wp_rmt_rules_logic.value as field_value ' .
    'FROM `wp_rmt_rules` ' .
    'LEFT OUTER JOIN wp_rmt_resources on wp_rmt_rules.rmt_field=wp_rmt_resources.id ' .
    'LEFT OUTER JOIN wp_rmt_entry_att_categories on wp_rmt_rules.rmt_field=wp_rmt_entry_att_categories.ID ' .
    'LEFT OUTER JOIN wp_rmt_rules_logic on wp_rmt_rules_logic.rule_id = wp_rmt_rules.id ' .
    'LEFT OUTER JOIN wp_mf_form_types on wp_mf_form_types.id = wp_rmt_rules.form_type ' .
    'ORDER BY rule ASC';

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
$save_rule = '';
$ruleArray = array();
$writeRule = array();
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    //set first instance of rule
    if ($save_rule == '')  $save_rule = $row['rule'];

    //if form_type is set is set in the rule, we need to show who to apply to
    if ($row['rule'] != $save_rule && !empty($ruleArray)) {
        //write array        
        $writeRule[$save_rule] = $ruleArray;

        $save_rule = $row['rule'];
        //clear out array
        $ruleArray = array();
    }

    //build $ruleArray
    $ruleArray[] = array(
        'ruleID'            => $row['rule'],
        'field_number'      => $row['field_number'], //logic 
        'operator'          => $row['operator'],     //logic
        'field_value'       => $row['field_value'],  //logic
        'rmt_type'          => $row['rmt_type'],
        'rmt_field_text'    => $row['rmt_field_text'],
        'rmt_field_value'   => $row['rmt_field_value']
    );
}

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

        tbody tr.detailRow:nth-child(odd) {
            background-color: #DEEFF5;            
        }
        tbody tr.detailRow:nth-child(even) {
            background-color: #F5F5F5;
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
                    <td>Rule</td>
                    <td>Type</td>
                    <td>Resource/Attribute</td>
                    <td>Value</td>
                    <td>Logic</td>
                </tr>
            </thead>
            <?php
            $save_rule = 0;
            $row_color = '';
            // Loop through the posts
            foreach ($writeRule as $rule) {
                $output = '';

                //first if the form ID is set, let's ensure that the rule even applies to this form
                foreach ($rule as $logic) {
                    //echo 'processing logic for '.$logic['ruleID'].'<br/>';
                    $pass = TRUE;
                    if ($form_type != '' && $logic['field_number'] == 'form_type') {
                        switch ($logic['operator']) {
                            case 'is':
                                if (strtolower($form_type) == strtolower($logic['field_value'])) {
                                    $pass = TRUE;
                                } else {
                                    $pass = FALSE;
                                }
                                break;
                            case 'not':
                                if (strtolower($form_type) != strtolower($logic['field_value'])) {
                                    $pass = TRUE;
                                } else {
                                    $pass = FALSE;
                                }
                                break;
                            case 'contains';
                                $pos = stripos($entryfield, $logic['field_value']);
                                if ($pos !== false) {
                                    $pass = true;
                                } else {
                                    $pass = false;
                                }
                                break;
                        }
                    }
                    if (!$pass) {
                        //blank out the output
                        $output = '';

                        //skip rule;
                        break;
                    } else {
                        //if the value of this res/att is set by a field, return the field name
                        if(isset($field_array[$logic['field_number']] )){
                            $fieldNumber = $field_array[$logic['field_number']]['label'].' {'.$logic['field_number'].'}';
                        }else{
                            $fieldNumber =  $logic['field_number'];                        
                        }

                        //build logic output
                        $output .=
                            '<tr>' .
                            '   <td style="width: 45%">' . $fieldNumber              . '</td>' .
                            '   <td style="width: 10%">' . $logic['operator']        . '</td>' .
                            '   <td style="width: 45%">' . $logic['field_value']     . '</td>' .
                            '</tr>';
                    }
                }
                //if the, rule hasn't been skipped write it out
                if ($output != '') {
                    echo    
                        '<tr class="detailRow">' .
                        '   <td class="tcenter">' . $logic['ruleID'] . '</td>' .
                        '   <td>' . ucfirst($logic['rmt_type'])   . '</td>' .
                        '   <td>' . $logic['rmt_field_text']         . '</td>' .
                        '   <td>';

                    //if this is being set from a value of a field, display that here
                    //the rmt field value can be set with multiple fields,
                    $rmt_field_value = $logic['rmt_field_value'];
                    $pos = strpos($rmt_field_value, '{');
                    //loop through all fields and replace id with field name
                    while ($pos !== false) {
                        $endPos = strpos($rmt_field_value, '}');
                        $field_id  = substr($rmt_field_value, $pos+1,$endPos-$pos-1);
                        $req_field = substr($rmt_field_value, $pos,$endPos-$pos+1);

                        //if this is a valid field in the form, return the label
                        if(isset($field_array[$field_id] )){                        
                            //if the field is an array, create a comma separated list
                            $fieldData = $field_array[$field_id]['label'].' ['.$field_id.']';
                        }else{
                            $fieldData = ' ['.$field_id.']';
                        }
                        $rmt_field_value = str_replace($req_field, $fieldData, $rmt_field_value);
                        $pos = strpos($rmt_field_value, '{');
                    }
                    echo $rmt_field_value;
                                            
                    echo '  </td>'.                            
                        '   <td><table style="width:100%">' . $output                   . '</table></td>' .
                        '</tr>';
                }
            }


            ?>
        </table>
    </div>
</body>

</html>