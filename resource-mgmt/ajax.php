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
      save_data($mysqli, $_POST['table'], $_POST['data']);
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
function save_data($mysqli,$table='',$data=''){
	try{
    $id = (isset($_POST['id']) ? $_POST['id']:'');
    //set field names and values
    $field_names  = array();
    $field_values = array();
		if(is_array($data)){
      //loop thru the passed information
      foreach($data as $key=>$value){
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


		if(empty($id)){
      $query   = "INSERT INTO " . $table . " (" .  implode(",", $field_names) . ') VALUES ("' . implode('", "',$field_values) . '")';
    }else{
      //tbd update query
      $query = "Update " . $table . " SET " . implode(",", $updateField) ;
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
	try{
    $data = array();
		$query = "show tables like '%wp_resource_%'";
		$result = $mysqli->query( $query );
    while($cRow = mysqli_fetch_array($result))
    {
      $data['tables'][] = array('name'=>$cRow[0]);
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

		$query = "select * from ".$table;
		$result = $mysqli->query( $query );

    //create array of field names
    while ($finfo = mysqli_fetch_field($result)) {
      if(strtolower($finfo->name)!='id')  //do not return the id field
        $data['fieldNames'][]=$finfo->name;
    }

    $pquery   = "SHOW KEYS FROM ".$table." WHERE Key_name = 'PRIMARY'";
    $presult  = $mysqli->query( $pquery );
    $prow     = $presult->fetch_assoc();
    $pkey     = $prow['Column_name'];
    $pvalue   = '';

		while ($row = $result->fetch_assoc()) {
      //do not send the primary key of the table, but save it so we can identify the record for edit or delete
      if($pkey!=''){
        $pvalue = $row[$pkey];
        //unset the id field
        unset($row[$pkey]);
      }
			$data['tableData'][$pvalue] = $row;
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