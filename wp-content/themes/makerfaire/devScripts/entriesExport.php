<?php
include 'db_connect.php';
$formID=(isset($_GET['formID'])?$_GET['formID']:260 );

// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="exportForm'.$formID.'.csv"');

// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');

//get form data
/* New export for BA23 */
$field_array = array(303, 151, 302, 16, 27, 32, 45, 803, 806, 805, 60, 345, 344, 61, 66, 438, 65, 62, 347, 348,
69, 71, 44, 144, 122, 72, 73, 74, 75, 76, 77, 78, 79, 81, 758, 64, 83, 84, 85, 317, 318, 99, 134, 304, 339, 98, 96);

$fieldData = array();

//get form headers
$form=GFAPI::get_form($formID);
foreach($form['fields'] as $field){
  if($field->type=='html' || $field->type=='section' || $field->type=='page'){
    continue;
  }
  
  if(in_array($field->id, $field_array)){    
    $label = (isset($field['adminLabel']) && trim($field['adminLabel']) != '' ? $field['adminLabel'] : $field['label']);
    $fieldData[$field->id] = array('id'=>$field->id,'label' => $label, 'type' => $field->type, 'inputs' => $field->inputs);
  }
}  
  
$entryData = array();
$search_criteria = array('status'        => 'active');
$sorting         = array();
$paging          = array( 'offset' => 0, 'page_size' => 500 );
$entries = GFAPI::get_entries( $formID, $search_criteria, $sorting, $paging );

foreach($entries as $entry){
  $entryData[$entry['id']] =  array();
  foreach($field_array as $fieldID){
    $field = $fieldData[$fieldID];
    //comma separate input values for checkbox, address, name    
    if($field['type']=='checkbox'||$field['type']=='address' || $field['type']=='name'){
      $inputArray = array();
      foreach($field['inputs'] as $input){        
        if(isset($entry[$input['id']]) && $entry[$input['id']]!='') {
          //field 321 is stored as category number, use cross reference to find text value
          if($fieldID=='321'){
            $value = get_CPT_name($entry[$input['id']]);
          }else{
            $value = $entry[$input['id']];
          }
          $inputArray[] = $value;
        }
      }      

      $entryData[$entry['id']][$fieldID] = ($field['type']=='name'?implode(" ",$inputArray):implode(", ",$inputArray));
    }else{
      $value = '';
      if(isset($entry[$fieldID])){
        //field 320 and 321 is stored as category number, use cross reference to find text value
        if($fieldID=='320'){
          $value = get_CPT_name($entry[$fieldID]);
        }else{
          $value = $entry[$fieldID];
        }
      }
      $entryData[$entry['id']][$fieldID] = $value;
    }
  }
}

// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');

// send the column headers
$headers=array('Entry ID');
foreach($field_array as $fieldID){   
  $headers[] = $fieldData[$fieldID]['label'];
}

/*echo '<br/>header is<br/>';
var_dump($headers);*/
fputcsv($file, $headers);

//send the data
foreach($entryData as $entry_id=>$entryvalue){
  $output=array();
  $output[]=$entry_id;
  $output = array_merge($output, $entryvalue);    
  //echo '<br/>data is<br/>';
  //var_dump($output);
  fputcsv($file, $output);
}
//echo '<br/>';
die();

exit();