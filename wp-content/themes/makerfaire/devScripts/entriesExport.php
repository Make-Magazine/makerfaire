<?php
include 'db_connect.php';
//check that the request is valid
/*
$auth = (isset($_GET['auth'])?$_GET['auth']:'');
if($auth==''){
  exit();
}
//create a crypt key to pass to entriesExport.php to avoid outside from accessing
$date  = date('mdY');
$crypt = crypt($date, AUTH_SALT);
if($auth != $crypt){
  exit();
}*/
$form=(isset($_GET['formID'])?$_GET['formID']:46);

// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="exportForm'.$form.'.csv"');

// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');

$mysqli->query("SET NAMES 'utf8'");
//get form data
$sql = 'select display_meta from wp_rg_form_meta where form_id='.$form;
$result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
  $json = json_decode($row['display_meta']);
  $jsonArray = (array) $json->fields;
  foreach($jsonArray as &$array){
      $array->id = (float) $array->id;
      $array = (array) $array;
  }
  usort($jsonArray, "cmp");
  $fieldData = array();
  $fieldData[]='entry id';

  foreach($jsonArray as $field){
    $fieldID = (string) $field['id'];

    if($field['type']!='section' && $field['type']!='page'){
      $label = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
      if($label=='' && $field['type']=='checkbox') $label = $field['choices'][0]->text;

      if($field['type']=='checkbox'||$field['type']=='address' || $field['type']=='name'){
        if(isset($field['inputs']) && !empty($field['inputs'])){
          foreach($field['inputs'] as $choice){
            $choiceID = (string) $choice->id;
            $fieldData[$choiceID] = $label.' '.$choice->label.'('.$choiceID.')';
          }
        }else{
          $fieldData[$fieldID] = $label.'('.$fieldID.')';
        }
      }else{
        $fieldData[$fieldID] = $label.'('.$fieldID.')';
      }
    }
  }
}

//entry data
$sql = "SELECT wp_rg_lead_detail.*,wp_rg_lead_detail_long.value as 'long value'
        FROM wp_gf_entry
          left outer join wp_rg_lead_detail
            on wp_rg_lead_detail.lead_id = wp_gf_entry.id
          left OUTER join wp_rg_lead_detail_long
            ON wp_rg_lead_detail.id = wp_rg_lead_detail_long.lead_detail_id
        where wp_gf_entry.form_id = $form
        and wp_gf_entry.status='active'
        ORDER BY lead_id asc, field_number asc";
//loop thru entry data
$entries = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
$entryData = array();

foreach($entries as $entry){
  $fieldNum = (string) $entry['field_number'];
  //field 302 and 320 is stored as category number, use cross reference to find text value
  if($fieldNum=='320' || strpos($fieldNum, '321.')!== false || strpos($fieldNum, '302.')!== false){
    $value = get_CPT_name($entry['value']);
  }else{
    $value = (isset($entry['long_value']) && $entry['long_value']!=''?$entry['long_value']:$entry['value']);
  }
  $value = htmlspecialchars_decode ($value);
  $entryData[$entry['lead_id']][$fieldNum]=$value;
}

// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');

// send the column headers
fputcsv($file, $fieldData);

//send the data
foreach($entryData as $entrykey=>$entryvalue){
  $output=array();

  foreach($fieldData as $fieldkey=>$fieldvalue){
    if($fieldkey==0){
      $output[]=$entrykey;
    }else{
      if(isset($entryvalue[$fieldkey])){
        $output[]=$entryvalue[$fieldkey];
      }else{
        $output[]='';
      }
    }
  }
  fputcsv($file, $output);
}

exit();

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}