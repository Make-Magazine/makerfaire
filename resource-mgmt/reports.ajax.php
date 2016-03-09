<?php
/*
ajax to populate resource management table
*/
require_once 'config.php';
require_once 'table.fields.defs.php';

if(isset($_POST['type']) && !empty( isset($_POST['type']) ) ){
	$type = $_POST['type'];
  if($type =="tableData"){
    if($_POST['table']=='formData'){
      $data = array();
      //return form data
      $formReturn = array();
      $forms = RGFormsModel::get_forms( null, 'title' );
      foreach ( $forms as $form ) {
        //exclude master form
        if($form->id!=9){
          $formReturn[] = array('id'=>absint( $form->id ), 'name' => htmlspecialchars_decode( $form->title));
        }
      }
      $form       = RGFormsModel::get_form_meta( 9 );

      $field_filters = GFCommon::get_field_filter_settings( $form );
      $data['field_filters'] = $field_filters;
      $init_field_id       = empty( $search_field_id ) ? 0 : $search_field_id;
      $init_field_operator = empty( $search_operator ) ? 'contains' : $search_operator;
      $data['init_field_filters'] = array(
        'mode'    => 'off',
        'filters' => array(
          array(
            'field'    => $init_field_id,
            'operator' => $init_field_operator,
            'value'    => $search,
          ),
        )
      );
      //field list (from form 9)
      $fieldReturn = array();
      $sql = 'select display_meta from wp_rg_form_meta where form_id=9';
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
          switch ($field['type']) {
            case 'html':
            case 'section':
            case 'page':
              break;
            default:
              $fieldLabel = (isset($field['label']) ? $field['label']:'');
              $label = (isset($field['adminLabel']) && $field['adminLabel'] != '' ? $field['adminLabel'] : $fieldLabel);

              if($field['type']=='checkbox'||$field['type']=='radio'||$field['type']=='select'||$field['type']=='address'){
                if(isset($field['inputs']) && !empty($field['inputs'])){
                  foreach($field['inputs'] as $choice){
                    $label = ($label!=''?$label:$choice->label);
                    $fieldReturn[] = array('id' => $choice->id, 'label' => $label, 'choices'=>$choice->label);
                  }
                }else{
                  foreach($field['choices'] as $choice){
                    $label = ($label!=''?$label:$choice->value);
                    $fieldReturn[] = array('id' => $field['id'], 'label' => $label, 'choices'=>$choice->value);
                  }
                }
              }else{
                $fieldReturn[] = array('id'     => $field['id'], 'label' => $label, 'choices'=>'');
              }

          }
        }
      }

      $data['forms']  = $formReturn;
      $data['fields'] = $fieldReturn;
      echo json_encode($data);
      exit;
    }else{
      //build report data
      retrieveRptData($_POST['table']);
    }
  }elseif($type =="customRpt"){
    if(isset($_POST['formSelect']) && isset($_POST['selectedFields'])){
      //check if data is empty
      if(empty($_POST['formSelect']) || empty($_POST['selectedFields'])){
        invalidRequest('Error: Form or Fields not selected');
      }else{
        //build report
        buildRpt($_POST['formSelect'],$_POST['selectedFields']);
      }
    }else{
      invalidRequest('Error: Form and Field Data not sent');
    }
  }else{
    invalidRequest('Invalid Request type');
  }
}else{
	invalidRequest('Request Type Not Sent');
}
function buildRpt($formSelect=array(),$selectedFields=array()){
  global $mysqli;
  $forms = implode(",",$formSelect);

  $data['columnDefs'][] = array('field'=>'entry_id');
  $data['columnDefs'][] = array('field'=>'form_id');

  //TBD - remove duplicate field ID's
  $fieldArr=array();
  $fieldIDArr = array();
  foreach($selectedFields as $selFields){
    //build field array
    if(isset($selFields['choices']) && $selFields['choices']!=''){
      $fieldArr[$selFields['id']][] = $selFields['choices'];
    }
    //create array of selected field id's
    $fieldIDArr[] = $selFields['id'];
    //build grid columns. using the id as an index to avoid dups
    $data['columnDefs'][$selFields['id']] =   array('field'=> 'field_'.str_replace('.','_',$selFields['id']),'displayName'=>$selFields['label']);
  }
  $fieldIDArr = array_unique($fieldIDArr);
  $fields = implode(",",$fieldIDArr);
//BUILD field number query
  $fieldNumQry = '';
foreach($fieldIDArr as $fieldID){
  if($fieldNumQry==''){
    $fieldNumQry = " wp_rg_lead_detail.field_number like '$fieldID' ";
  }else{
   $fieldNumQry  .= " or wp_rg_lead_detail.field_number like '$fieldID' ";
  }
}
  //pull entry data
  $sql = "SELECT wp_rg_lead_detail.*,wp_rg_lead_detail_long.value as 'long value'
          FROM wp_rg_lead
            left outer join wp_rg_lead_detail
              on wp_rg_lead_detail.lead_id = wp_rg_lead.id
            left OUTER join wp_rg_lead_detail_long
              ON wp_rg_lead_detail.id = wp_rg_lead_detail_long.lead_detail_id
          where wp_rg_lead.form_id in($forms)
          and ($fieldNumQry)
          and wp_rg_lead.status='active'
          ORDER BY lead_id asc, field_number asc";

  //loop thru entry data
  $entries = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
  $entryData = array();

  foreach($entries as $entry){
    $value = (isset($entry['long_value']) && $entry['long_value']!=''?$entry['long_value']:$entry['value']);
    $pass = true;
    //if this field has choices, only return those entries where the field value was selected when building the report
    if(isset($fieldArr[$entry['field_number']])){

      if(!in_array($value,$fieldArr[$entry['field_number']])){
        //reject
        $pass=false;
      }
    }
    if($pass){
      $entryData[$entry['lead_id']]['entry_id'] = $entry['lead_id'];
      $entryData[$entry['lead_id']]['form_id']  = $entry['form_id'];
      $entryData[$entry['lead_id']]['field_'.str_replace('.','_',$entry['field_number'])]  = $value;
    }
  }
  foreach($entryData as $row){
    $data['data'][]= $row;
  }
  //reindex columnDefs as the grid will blow up if the indexes aren't in order
  $data['columnDefs'] = array_values($data['columnDefs']);

  echo json_encode($data);
  exit;
}
function retrieveRptData($table){
  global $mysqli;global $tableFields;
  $sql   = '';
  $where = '';

  //build columnDefs
  foreach($tableFields[$table] as $fields){
    if(isset($fields['dataSql'])) $sql   .= ','.$fields['dataSql'];
    if(isset($fields['limit'])){
      if($where==''){
        $where .= ' where ';
      }else{
        $where .= ' and ';
      }
      $where .= $fields['fieldName'] .' '. $fields['limit']['opt'].' '.$fields['limit']['value'];
    }
    $vars = array();
    switch($fields['filterType']){
      case 'dropdown':
        $options = array();  $selectOptions=array();
        //retrieve dropdown info
        if(isset($fields['fkey'])){
          $fkeyData      = getFkeyData($fields['fkey'],$fields['fieldName']);
          $options       = $fkeyData[0];
          $selectOptions = $fkeyData[1];
          //additional select options outside of fkey
          if(isset($fields['options'])){
            foreach($fields['options'] as $optKey=>$option){
              $options[]       = array('id'    => $optKey, 'fkey'   => $option);
              $selectOptions[] = array('value' => $optKey, 'label' => $option);
            }
          }
        }else{
          //use defined options
          foreach($fields['options'] as $optKey=>$option){
            $options[]       = array('id'    => $optKey, 'fkey'   => $option);
            $selectOptions[] = array('value' => $optKey, 'label' => $option);
          }
        }
        $vars = array('displayName'=> (isset($fields['fieldLabel'])?$fields['fieldLabel']:$fields['fieldName']),
                      'filter'=> array('selectOptions'=>$selectOptions),
                      'cellFilter'               => 'griddropdown:this',
                      'headerCellClass'          => '$scope.highlightFilteredHeader',
                      'editDropdownValueLabel'   => 'fkey',
                      'editDropdownIdLabel'      => 'id',
                      'editDropdownOptionsArray' => $options);
        break;
      case 'entrylink':
        $vars = array('cellTemplate'=>'<div class="ui-grid-cell-contents"><a href="http://makerfaire.com/wp-admin/admin.php?page=mf_entries&view=mfentry&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>');
        break;
      case 'hidden':
        $vars = array('visible'=>false);
        break;
      case 'custom':
      case 'number':
      case 'text':
      default:
        break;
    }
    $vars['field']    = $fields['fieldName'];
    $vars['name']     = $fields['fieldName'];
    $vars['minWidth'] = 100;
    $columnDefs[] = $vars;
  }

  //build data
  $data['columnDefs'] = $columnDefs;
  //get table data
  $query = "select * ".$sql." from ".$table.$where;

  $result = $mysqli->query( $query );
  //create array of table data
  while ($row = $result->fetch_assoc()) {
    $data['data'][]= $row;
  }
  echo json_encode($data);
  exit;
}

function invalidRequest($message='')
{
	$data = array();
	$data['success'] = false;
	$data['message'] = ($message!=''?$message:"Invalid request.");
	echo json_encode($data);
	exit;
}

function getFkeyData($tabFkeyData,$fkey){
  global $mysqli;
  $referenceTable   = $tabFkeyData['referenceTable'];
  $referenceField   = $tabFkeyData['referenceField'];
  $referenceDisplay = $tabFkeyData['referenceDisplay'];
  //build options drop down
  $options = array();
  $selectOptions = array();
  $optionquery = "select " . $referenceField . ", " . $referenceDisplay . " from "   . $referenceTable;
  $result = $mysqli->query( $optionquery );
  while ($row = $result->fetch_assoc()) {
    $options[]       = array('id'    => $row[$referenceField], 'fkey'   => $row[$referenceDisplay]);
    $selectOptions[] = array('value' => $row[$referenceField], 'label' => $row[$referenceDisplay]);
  }
  return(array($options,$selectOptions));
}

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}