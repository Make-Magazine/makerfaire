<?php
/*
ajax to populate resource management table
*/
require_once 'config.php';
require_once 'table.fields.defs.php';

if(isset($_POST['type']) && !empty( isset($_POST['type']) ) ){
	$type = $_POST['type'];
  if($type =="tableData"){
    retrieveRptData($_POST['table']);
  }else{
    invalidRequest();
	}
}else{
	invalidRequest();
}

function retrieveRptData($table){
  global $mysqli;global $tableFields;
  $sql = '';

  //build columnDefs
  foreach($tableFields[$table] as $fields){
    if(isset($fields['dataSql'])) $sql.= ','.$fields['dataSql'];
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

        $columnDefs[] =   array('field'=> $fields['fieldName'],
                                'displayName'=> (isset($fields['fieldLabel'])?$fields['fieldLabel']:$fields['fieldName']),
                                'filter'=> array('selectOptions'=>$selectOptions),
            'cellFilter'               => 'griddropdown:this',
            'headerCellClass'          => '$scope.highlightFilteredHeader',
            'editDropdownValueLabel'   => 'fkey',
            'editDropdownIdLabel'      => 'id',
            'editDropdownOptionsArray' => $options);
        break;
      case 'entrylink':
        $columnDefs[] = array('field'=> $fields['fieldName'],'cellTemplate'=>'<div class="ui-grid-cell-contents"><a href="http://makerfaire.com/wp-admin/admin.php?page=mf_entries&view=mfentry&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>');
        break;
      case 'number':
      case 'text':
      default:
        $columnDefs[] = array('field'=> $fields['fieldName']);
			break;
    }
  }

  //build data
  $data['columnDefs'] = $columnDefs;
  //get table data
  $query = "select * ".$sql." from ".$table;

  $result = $mysqli->query( $query );
  //create array of table data
  while ($row = $result->fetch_assoc()) {
    $data['data'][]= $row;
  }
  echo json_encode($data);
  exit;
}

function invalidRequest()
{
	$data = array();
	$data['success'] = false;
	$data['message'] = "Invalid request.";
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