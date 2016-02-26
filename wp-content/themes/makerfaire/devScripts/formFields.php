<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';
$sql = 'select display_meta from wp_rg_form_meta where form_id!=1 and form_id!=24';

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
?>
<style>
    table {border-collapse: collapse;}
    
    th {
    font-size: 1.4em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #fff;
    }
    td, th {
    font-size: 1.2em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;
}
</style>

<?php
// Loop through the posts
while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    $json = json_decode($row['display_meta']);    
    echo '<h2>Form '.$json->id.' - '.$json->title.'</h2>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Label</th><th>Field Type</th><th>Options</th></tr>';
    $jsonArray = (array) $json->fields;
    foreach($jsonArray as &$array){
        $array->id = (float) $array->id;
        $array = (array) $array;
    }
    
    usort($jsonArray, "cmp");
    //   var_dump($jsonArray);
    foreach($jsonArray as $field){             
        if($field['type'] != 'html' && $field['type'] != 'section' && $field['type'] != 'page'){
            //var_dump($field);
            $label = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
            if($label=='' && $field['type']=='checkbox') $label = $field['choices'][0]->text;
            echo '<tr>';
            echo '<td>'.$field['id'].'</td><td>' . $label.'</td>';
            echo '<td>'.$field['type'].'</td>';
            echo '<td>';
            if($field['type']=='checkbox'||$field['type']=='radio'||$field['type']=='select'){
                echo '<ul>';
                if(isset($field['inputs']) && !empty($field['inputs'])){
                    foreach($field['inputs'] as $choice){
                        echo '<li>'.$choice->id.' '.$choice->label.'</li>';
                    }
                }else{
                    foreach($field['choices'] as $choice){
                        echo '<li>'.$choice->value.'</li>';
                    }
                }
                echo '</ul>';
            }
            echo '</td>';
            echo '</tr>';
        }       
    }
    echo '</table>';
    echo '<br/><br/>';
}

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}

