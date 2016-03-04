<?php
/*
ajax to populate resource management table and apply insert, edit and delete logic
*/
require_once 'config.php';

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
$tableOptions['wp_rmt_entry_attributes']   = array(
        array('fkey'             => 'attribute_id',
              'referenceTable'   => 'wp_rmt_entry_att_categories',
              'referenceField'   => 'ID',
              'referenceDisplay' => 'category')
        );
$tableOptions['wp_rmt_entry_resources']   = array(
        array('fkey'             => 'resource_id',
              'referenceTable'   => 'wp_rmt_resources',
              'referenceField'   => 'ID',
              'referenceDisplay' => 'type')
        );

if( isset($_POST['type']) && !empty( isset($_POST['type']) ) ){
	$type = $_POST['type'];

	switch ($type) {
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
    case "entryData":
//      getTableData($mysqli, 'wp_rmt_entry_resources');
      entryData();
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

//retrieve entry tables - resources, resource cat, entry attributes, entry workflow
/*
 * Need to return column defs with entry2Resource ID, Item(Category), Type, Comments, add/delete scope buttons, based on entry ID passed
 */
function entryData(){
  global $mysqli; global $tableOptions;
  $entryID = (isset($_POST['entryID'])?$_POST['entryID']:0);
  $data = array();

  //gather resource data
  $sql = "SELECT er.*, res.resource_category_id from `wp_rmt_entry_resources` er, wp_rmt_resources res where er.resource_id = res.ID ";
  if($entryID!=0) $sql .=" and er.entry_id = ".$entryID;
  $result = $mysqli->query( $sql );

  //create array of table data
  while ($row = $result->fetch_assoc()) {
    $data['resource']['gridData'][] = $row;
  }

  //now let's build the column defs item, type, amount(value), comments, add/delete buttons
  $tables = array('wp_rmt_resources',  //build type drop down
                  'wp_rmt_entry_resources'); //build item/category drop down
  $fkeyData = array();
  foreach($tables as $table){
    if(isset($tableOptions[$table])){
      if(is_array($tableOptions[$table])){
        foreach($tableOptions[$table] as $forKeyData){
          $fkeyData[$table] = getFkeyData($forKeyData);
        }
      }
    }
  }
  $addTemplate    = '<span ng-click="grid.appScope.entry.addNew()"><i class="fa fa-plus-circle"></i></span>';
  $removeTemplate = '<span ng-click="grid.appScope.entry.remove(row)"><i class="fa fa-minus-circle"></i></span>';

  $data['resource']['columnDefs'] = array(
      array('field'=> 'entry_id','displayName'=>'Entry ID','enableCellEdit'=>false),
      array('field'=> 'resource_category_id','displayName'=>'Item','enableCellEdit'=>true,
            'editableCellTemplate'=>'ui-grid/dropdownEditor',
            'editDropdownValueLabel'=> 'fkey', 'editDropdownIdLabel'=>'id','cellFilter'=> 'griddropdown:this',
            'editDropdownOptionsArray'=>$fkeyData['wp_rmt_resources'][0]),
      array('field'=> 'resource_id', 'displayName' => 'Type', 'enableCellEdit' => true,
            'editableCellTemplate' => 'ui-grid/dropdownEditor',
            'editDropdownValueLabel'=> 'fkey', 'editDropdownIdLabel'=>'id','cellFilter'=> 'griddropdown:this',
            'editDropdownOptionsArray'=>$fkeyData['wp_rmt_entry_resources'][0]
          ),
      array('field'=> 'qty','width'=>"100",'displayName'=>'Value','enableCellEdit'=>true),
      array('field'=> 'comment','displayName'=>'Comments','width'=>'30%'),
      array('field'=> 'controls','width'=>"5%",'headerCellTemplate'=>$addTemplate,'cellTemplate'=>$removeTemplate,'enableCellEdit'=>false)
      );

  //build type dropdown array
  //structure type
  $sql = "SELECT rc.ID as category_id, rc.category, res.ID as resource_ID, res.type FROM wp_rmt_resource_categories rc, `wp_rmt_resources` res where res.resource_category_id=rc.ID order by rc.category";
  $result = $mysqli->query( $sql );

  //create array of table data
  while ($row = $result->fetch_assoc()) {
    $data['resource']['typeSelect'][$row['category_id']][] = array('id'=>$row['resource_ID'],'fkey'=>$row['type']);
  }

  //build attribute data
  //get table setup
  $columnDefs = defTableInfo($table);
  $data['attribute']['columnDefs'] = $columnDefs[0];
  $pkey = $columnDefs[1];

  //get table data
  $query = "select * from ".$table;

  $result = $mysqli->query( $query );
  //create array of table data
  while ($row = $result->fetch_assoc()) {
    $data['attribute']['gridData'][]= $row;
  }

  //return data
  echo json_encode($data);exit;
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
  global $tableOptions;
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
    $width           = '150';
    if($row['Key']=='PRI'){
      $pkey = $row['Field'];
      $enableFiltering = false;
      $enableCellEdit  = false;
      $width           = '50';
    }
    //foreign key's have a different setup
    if(isset($fkeyData[$row['Field']])){ //is this the foreign key?
      $selectOptions = $fkeyData[$row['Field']][1];
      $options       = $fkeyData[$row['Field']][0];
      $displayName   = (isset($row['Comment']) && $row['Comment']!='' ? $row['Comment']:$row['Field']);
      //replace underscores and dashes with a space and then make capitalize the first letter of each word
      $displayName = str_replace('_', ' ', $displayName);
      $displayName = str_replace('-', ' ', $displayName);
      $displayName = ucwords($displayName);

      $columnDefs[] = array('name'            => $row['Field'],
                            'displayName'     => $displayName,
                            'editableCellTemplate'=>'ui-grid/dropdownEditor',
                            'headerCellClass' => '$scope.highlightFilteredHeader',
                            'editDropdownValueLabel'=> 'fkey',
                            'editDropdownIdLabel'=>'id',
                            'filter' => array('type' => 'uiGridConstants.filter.SELECT',
                                              'selectOptions' => $selectOptions),
                            'cellFilter'=> 'griddropdown:this',
                            'editDropdownOptionsArray'=>$options);
    }else{
      //tbd need to find a formatting that will work with a datetime field
      //date type does not save correctly in the database
      if($row['Type']=='datetime'){
        $type = "date";
      }else{
        $type = 'string';
      }
      $displayName   = (isset($row['Comment']) && $row['Comment']!='' ? $row['Comment']:$row['Field']);
      //replace underscores and dashes with a space and then make capitalize the first letter of each word
      $displayName = str_replace('_', ' ', $displayName);
      $displayName = str_replace('-', ' ', $displayName);
      $displayName = ucwords($displayName);

      $columnDefs[] = array('name'            => $row['Field'],
                            'displayName'     => $displayName,
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