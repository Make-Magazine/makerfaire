<?php
include 'db_connect.php';
//check that the request is valid
$auth = (isset($_GET['auth'])?$_GET['auth']:'');
if($auth==''){
  exit();
}
//create a crypt key to pass to entriesExport.php to avoid outside from accessing
$date  = date('mdY');
$crypt = crypt($date, AUTH_SALT);
if($auth != $crypt){
  exit();
}
$form=(isset($_GET['formID'])?$_GET['formID']:46);
// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="exportForm'.$form.'-'.$date.'.csv"');

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
    if($field['type']!='section' && $field['type']!='page'){
      $label = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
      if($label=='' && $field['type']=='checkbox') $label = $field['choices'][0]->text;
      //build category crossreference
      if($field['id']==320){
        $catCross = array();
        foreach($field['choices'] as $choice){
          $catCross[$choice->value]=$choice->text;
        }
      }
      if($field['type']=='checkbox'){
        if(isset($field['inputs']) && !empty($field['inputs'])){
          foreach($field['inputs'] as $choice){
            $fieldData[$choice->id] = $label.' '.$choice->label.'('.$choice->id.')';
          }
        }else{
          $fieldData[$field['id']] = $label.'('.$field['id'].')';
        }

      }else{
        $fieldData[$field['id']] = $label.'('.$field['id'].')';
      }
    }
  }
}

//entry data
$sql = "SELECT wp_rg_lead_detail.*,wp_rg_lead_detail_long.value as 'long value'
        FROM wp_rg_lead
          left outer join wp_rg_lead_detail
            on wp_rg_lead_detail.lead_id = wp_rg_lead.id
          left OUTER join wp_rg_lead_detail_long
            ON wp_rg_lead_detail.id = wp_rg_lead_detail_long.lead_detail_id
        where wp_rg_lead.form_id = $form
        and wp_rg_lead.status='active'
        ORDER BY lead_id asc, field_number asc";
//loop thru entry data
$entries = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
$entryData = array();
foreach($entries as $entry){
  //field 320 is stored as category number, use cross reference to find text value
  if($entry['field_number']==320){
    $value = (isset($catCross[$entry['value']])?$catCross[$entry['value']]:$entry['value']);
  }else{
    $value = (isset($entry['long_value']) && $entry['long_value']!=''?$entry['long_value']:$entry['value']);
  }
  $value = htmlspecialchars_decode ($value);
  $entryData[$entry['lead_id']][$entry['field_number']]=$value;
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