<?php
/*
ajax to populate resource management table and apply insert, edit and delete logic
*/
require_once 'config.php';

if( isset($_POST['type']) && !empty( isset($_POST['type']) ) ){
	$type = $_POST['type'];

	switch ($type) {
		case "save_data":
			save_user($mysqli, $_POST['table']);
			break;
		case "deleteData":
			deleteData($mysqli,$_POST['table'],$_POST['pKeyField'], $_POST['id']);
			break;
    case "getTables":
			getTables($mysqli);
			break;
    case "tableData":
      getTableData($mysqli, $_POST['table']);
      break;
    case "insertData":
    case "updateData":
      save_data($mysqli, $_POST['table'], $_POST['data'],$_POST['pKeyField']);
      break;
		default:
			invalidRequest();
	}
}else{
	invalidRequest();
}

/**
 * This function will handle user add, update functionality
 * @throws Exception
 */
function save_data($mysqli,$table='',$data='',$pkeyField=''){
	try{
    $id = (isset($data['ID'])?$data['ID']:'');
    //get field names
    //set field names and values
    $field_names  = array();
    $field_values = array();
		if(is_array($data)){
      //loop thru the passed information
      foreach($data as $key=>$value){
        if($key!= $pkeyField && $key != '$$hashKey' && $key != 'sending'){ //not valid table fields
          //if $id is empty this is an insert not an update
          if(empty($id)){
            $field_names[]  = $key;
            //tbd clean input data
            $field_values[] = (string) $value;
          }else{
            $updateField[] = $key .'="'.$value.'"';
          }
        }
      }
    }

    $query = '';
		if(empty($id)){
      $query   = "INSERT INTO " . $table . " (" .  implode(",", $field_names) . ') VALUES ("' . implode('", "',$field_values) . '")';
    }else{
      if($pkeyField!=''){
        //tbd update query
        $query = "Update " . $table . " SET " . implode(",", $updateField) ." where ".$pkeyField.'='.$id;
      }
		}

		if( $mysqli->query( $query ) ){
			$data['success'] = true;
			if(!empty($id))$data['message'] = 'Data updated successfully.';
			else $data['message'] = 'Data inserted successfully.';
			if(empty($id))$data['id'] = (int) $mysqli->insert_id;
			else $data['id'] = (int) $id;
		}else{
			throw new Exception( $mysqli->sqlstate.' - '. $mysqli->error.' query='.$query );
		}
		$mysqli->close();
		echo json_encode($data);
		exit;
	}catch (Exception $e){
		$data = array();
		$data['success'] = false;
		$data['message'] = $e->getMessage();
		echo json_encode($data);
		exit;
	}
}

/**
 * This function will handle user deletion
 * @param string $id
 * @throws Exception
 */
function deleteData($mysqli,$table, $pKeyField='',$id = ''){
	try{
		if(empty($id)) throw new Exception( "Invalid ID." );
    if(empty($pKeyField)) throw new Exception( "Invalid primary key." );
		$query = "DELETE FROM $table WHERE $pKeyField = $id";

		if($mysqli->query( $query )){
			$data['success'] = true;
			$data['message'] = 'Data deleted successfully.';
			echo json_encode($data);
			exit;
		}else{
			throw new Exception( $mysqli->sqlstate.' - '. $mysqli->error );
		}


	}catch (Exception $e){
		$data = array();
		$data['success'] = false;
		$data['message'] = $e->getMessage();
		echo json_encode($data);
		exit;
	}
}

/**
 * This function gets list of tables from database that start with wp_resource
 */
function getTables($mysqli){
  $tables=array('wp_rmt_entry_resources',
'wp_rmt_resources',
'wp_rmt_resource_att',
'wp_rmt_resource_att_values',
'wp_rmt_resource_categories',
'wp_rmt_vendors',
'wp_rmt_vendor_orders',
'wp_rmt_vendor_resources');
	try{
    $data = array();

    foreach($tables as $table){
      $data['tables'][] = array('name'=>$table);
    }
		echo json_encode($data);exit;

	}catch (Exception $e){
		$data = array();
		$data['success'] = false;
		$data['message'] = $e->getMessage();
		echo json_encode($data);
		exit;
	}
}

//return field names and table data of requested db table
function getTableData($mysqli,$table){
  if($table!=''){
    try{
    $data = array();
    //get table setup
    $columnDefs = defTableInfo($table);
    $data['columnDefs'] = $columnDefs[0];
    $pkey = $columnDefs[1];

    //get table data
    $query = "select * from ".$table;

		$result = $mysqli->query( $query );
    //create array of table data
		while ($row = $result->fetch_assoc()) {
      $data['data'][]= $row;
		}
    $data['pInfo'] = $pkey;
		$data['success'] = true;
		echo json_encode($data);exit;

    }catch (Exception $e){
      $data = array();
      $data['success'] = false;
      $data['message'] = $e->getMessage();
      echo json_encode($data);
      exit;
    }
  }
}
function invalidRequest()
{
	$data = array();
	$data['success'] = false;
	$data['message'] = "Invalid request.";
	echo json_encode($data);
	exit;
}

function defTableInfo($table){
  global $mysqli;
  $tableOptions = array();
  //single foreign key
  $tableOptions['wp_rmt_resources'] = array(
          array('fkey'             => 'resource_category_id',
                'referenceTable'   => 'wp_rmt_resource_categories',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'category')
          );
  //multiple foreign keys
  $tableOptions['wp_rmt_vendor_resources'] = array(
          array('fkey'             => 'vendor_id',
                'referenceTable'   => 'wp_rmt_vendors',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'company_name'),
          array('fkey'             => 'resource_id',
                'referenceTable'   => 'wp_rmt_resources',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'item')
          );
  $tableOptions['wp_rmt_vendor_orders'] = array(
          array('fkey'             => 'vendor_resource_id',
                'referenceTable'   => 'wp_rmt_vendor_resources',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'ID'),
          array('fkey'             => 'faire_id',
                'referenceTable'   => 'wp_mf_faire',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'faire')
          );
  $tableOptions['wp_mf_faire_area']   = array(
          array('fkey'             => 'faire_id',
                'referenceTable'   => 'wp_mf_faire',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'faire')
          );
  $tableOptions['wp_mf_faire_subarea']   = array(
          array('fkey'             => 'area_id',
                'referenceTable'   => 'wp_mf_faire_area',
                'referenceField'   => 'ID',
                'referenceDisplay' => 'area')
          );
  $fkey='';
  //does this table have foreign key drop downs that need to be set?
  if(isset($tableOptions[$table])){
    if(is_array($tableOptions[$table])){
      foreach($tableOptions[$table] as $forKeyData){
        $fkeyData[$forKeyData['fkey']] = getFkeyData($forKeyData);
      }
    }
  }


  //retrieve field names and primary key
  $pquery = "show full columns in " . $table;
  $presult  = $mysqli->query( $pquery );

  $columnDefs = array(array('name' => 'delete', 'displayName' =>'','sortable'=>false,'enableColumnMenu' => false,'enableFiltering'=> false,
                            'enableCellEdit' => false,
                            'cellTemplate' => '<span class="ui-grid-cell-contents ng-binding ng-scope" ng-click="grid.appScope.deleteRow(row)"><i class="fa fa-trash"></i></span>',
                            'width' => '25'));
  //create array of field names
  while ($row = $presult->fetch_assoc()) {
    //var_dump($row);
    $enableFiltering = true;
    $enableCellEdit  = true;
    $width           = '*';
    if($row['Key']=='PRI'){
      $pkey = $row['Field'];
      $enableFiltering = false;
      $enableCellEdit  = false;
      $width           = '5%';
    }
    //foreign key's have a different setup
    if(isset($fkeyData[$row['Field']])){ //is this the foreign key?
    //if($fkey == $row['Field']){ //is this the foreign key?
      $selectOptions = $fkeyData[$row['Field']][1];
      $options       = $fkeyData[$row['Field']][0];
      $columnDefs[] = array('name'            => $row['Field'],
                            'displayName'     => (isset($row['Comment']) && $row['Comment']!='' ? $row['Comment']:$row['Field']),
                            'editableCellTemplate'=>'ui-grid/dropdownEditor',
                            'headerCellClass' => '$scope.highlightFilteredHeader',
                            'editDropdownValueLabel'=> 'fkey',
                            'editDropdownIdLabel'=>'id',
                            'filter' => array('type' => 'uiGridConstants.filter.SELECT',
                                              'selectOptions' => $selectOptions),
                            'cellFilter'=> 'griddropdown:this',
                            'editDropdownOptionsArray'=>$options);
    }else{
      if($row['Type']=='datetime'){
        $cellFilter = 'date:"yyyy-MM-dd\"';
      }
      $columnDefs[] = array('name'            => $row['Field'],
                             'displayName'     => (isset($row['Comment']) && $row['Comment']!='' ? $row['Comment']:$row['Field']),
                            'enableCellEdit'  => $enableCellEdit,
                            'enableFiltering' => $enableFiltering,
                            'width'           => $width,
                            'headerCellClass' => '$scope.highlightFilteredHeader');
    }
  }

  $pkey = 'ID';
  return(array($columnDefs,$pkey));
}

function getFkeyData($tabFkeyData){
  global $mysqli;
  $fkey             = $tabFkeyData['fkey'];
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