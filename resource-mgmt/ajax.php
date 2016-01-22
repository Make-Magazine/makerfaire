<?php
/*
Site : http:www.smarttutorials.net
Author :muni
*/
require_once 'config.php';

if( isset($_POST['type']) && !empty( isset($_POST['type']) ) ){
	$type = $_POST['type'];

	switch ($type) {
		case "save_data":
			save_user($mysqli, $_POST['table']);
			break;
		case "delete_row":
			delete_user($mysqli, $_POST['id']);
			break;
    case "getTables":
			getTables($mysqli);
			break;
    case "tableData":
      getTableData($mysqli, $_POST['table']);
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
function save_data($mysqli,$table=''){
	try{
		$data = array();
    $id = (isset($_POST['id']) ? $_POST['id']:'');

    //set field names and values
    $field_names  = array();
    $field_values = array();

    foreach($_POST as $key=>$value){
      if($key!='type'){
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
      $query   = "INSERT INTO " . $table . " (" .  implode(",", $field_names) . ') VALUES (' . implode(",",$field_values) . ')';
    }else{
      //tbd update query
      $query = "Update " . $table . " SET " . implode(",", $updateField) ;
		}
die($query);
/*
		if($name == '' || $companyName == '' || $designation == ''|| $email == '' ){
			throw new Exception( "Required fields missing, Please enter and submit" );
		}
*/



		if( $mysqli->query( $query ) ){
			$data['success'] = true;
			if(!empty($id))$data['message'] = 'User updated successfully.';
			else $data['message'] = 'User inserted successfully.';
			if(empty($id))$data['id'] = (int) $mysqli->insert_id;
			else $data['id'] = (int) $id;
		}else{
			throw new Exception( $mysqli->sqlstate.' - '. $mysqli->error );
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
function delete_user($mysqli, $id = ''){
	try{
		if(empty($id)) throw new Exception( "Invalid User." );
		$query = "DELETE FROM `employee` WHERE `id` = $id";
		if($mysqli->query( $query )){
			$data['success'] = true;
			$data['message'] = 'User deleted successfully.';
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
 * This function gets list of tables from database
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

		while ($row = $result->fetch_assoc()) {
      //unset the id field
      unset($row['id']);
      unset($row['ID']);

			$data['tableData'][] = $row;
		}
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