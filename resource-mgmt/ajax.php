<?php
/*  This ajax is used to return table data for RMT to allow insert, edit and delete logic */
require_once 'config.php';

//$tableOptions defines any foreign keys in the table that we need to pull additional data for
$tableOptions = array();
//resources
$tableOptions['wp_rmt_resources']['fkey'] = array(
        array('fkey' => 'resource_category_id', 'referenceTable'   => 'wp_rmt_resource_categories', 'referenceField'   => 'ID', 'referenceDisplay' => 'category'));
//vendor resources
$tableOptions['wp_rmt_vendor_resources']['fkey']  = array(
        array('fkey' => 'vendor_id',   'referenceTable' => 'wp_rmt_vendors',   'referenceField' => 'ID', 'referenceDisplay' => 'company_name'),
        array('fkey' => 'resource_id', 'referenceTable' => 'wp_rmt_resources', 'referenceField' => 'ID', 'referenceDisplay' => 'item'));
$tableOptions['wp_rmt_vendor_orders']['fkey']  = array(
        array('fkey' => 'vendor_resource_id', 'referenceTable'   => 'wp_rmt_vendor_resources', 'referenceField'   => 'ID', 'referenceDisplay' => 'ID'),
        array('fkey' => 'faire_id',           'referenceTable'   => 'wp_mf_faire',             'referenceField'   => 'ID', 'referenceDisplay' => 'faire'));
$tableOptions['wp_mf_faire_area']['fkey']    = array(
        array('fkey' => 'faire_id',     'referenceTable' => 'wp_mf_faire',      'referenceField'   => 'ID', 'referenceDisplay' => 'faire'));
$tableOptions['wp_mf_faire_subarea']['fkey']    = array(
        array('fkey' => 'area_id',      'referenceTable' => 'wp_mf_faire_area', 'referenceField'   => 'ID', 'referenceDisplay' => 'area'));

$tableOptions['wp_mf_faire_subarea']['addlFields']['faire'] = array('fieldName' => 'faire', 'filterType'=>'dropdown', 'fieldLabel' => 'Faire',
    'fkey' => array('fkey' => 'faire', 'referenceTable' => 'wp_mf_faire', 'referenceField'   => 'ID', 'referenceDisplay' => 'faire'),
    'dataSql' =>'(SELECT faire_id from wp_mf_faire_area where wp_mf_faire_area.ID = area_id) as faire'
    );
$tableOptions['wp_mf_faire_subarea']['addlFields']['assCount'] = array('fieldName' => 'assCount', 'fieldLabel' => 'Assigned',
    'dataSql' =>'(SELECT count(*) from wp_mf_location where wp_mf_faire_subarea.ID = subarea_id) as assCount'
    );

$tableOptions['wp_rmt_entry_attributes']['fkey']    = array(
        array('fkey' => 'attribute_id', 'referenceTable' => 'wp_rmt_entry_att_categories', 'referenceField'   => 'ID', 'referenceDisplay' => 'category'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',                    'referenceField'   => 'ID', 'referenceDisplay' => 'user_email'));
$tableOptions['wp_rmt_entry_attn']['fkey']    = array(
        array('fkey' => 'attn_id',      'referenceTable' => 'wp_rmt_attn', 'referenceField' => 'ID', 'referenceDisplay' => 'value'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',    'referenceField' => 'ID', 'referenceDisplay' => 'user_email'));
$tableOptions['wp_rmt_entry_resources']['fkey']    = array(
        array('fkey' => 'resource_id',  'referenceTable' => 'wp_rmt_resources', 'referenceField' => 'ID', 'referenceDisplay' => 'type'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',         'referenceField' => 'ID', 'referenceDisplay' => 'user_email'));

//Global Faire table
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
        'fieldName' => 'venue_address_region', 'filterType'=>'dropdown','fieldLabel'=>'Region', 'enableCellEdit' => true, 'width' => 150,
        'options' => array( 'Europe'        =>  'Europe',         'North America' =>  'North America',
                  'Asia'          =>  'Asia',           'Australia'     =>  'Australia',
                  'South America' =>  'South America',  'Middle East'   =>  'Middle East',
                  'PACIFIC'       =>  'Pacific',        'Africa'        =>  'Africa')
    );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
    'fieldName' => 'event_type', 'filterType'=>'dropdown','fieldLabel'=>'Event Type', 'enableCellEdit' => true,
    'options' => array('Mini' => 'Mini', 'Featured' => 'Featured', 'Flagship' => 'Flagship', 'School' => 'School')
  );
$view_only = (isset($_POST['viewOnly'])?$_POST['viewOnly']:FALSE);
if( isset($_POST['type']) && !empty( isset($_POST['type']) ) ){
	$type = $_POST['type'];

	switch ($type) {
		case "deleteData":
			deleteData($mysqli,$_POST['table'],$_POST['pKeyField'], $_POST['id']);
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
  global $tableOptions;
	try{
    $id = (isset($data['ID'])?$data['ID']:'');
    //get field names
    //set field names and values
    $field_names  = array();
    $field_values = array();
		if(is_array($data)){
      //loop thru the passed information
      foreach($data as $key=>$value){
        //skip if the field is an additional field and not part of this table
        if(!isset($tableOptions[$table]['addlFields'][$key])){
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

//return field names and table data of requested db table
function getTableData($mysqli,$table){
  global $tableOptions;
  if($table!=''){
    try{
      $data = array();
      //get table setup
      $columnDefs = defTableInfo($table);
      $data['columnDefs'] = $columnDefs[0];
      $pkey = $columnDefs[1];
      $sql ='';

      //any requested additional fields?
      if(isset($tableOptions[$table]['addlFields'] )){
        foreach($tableOptions[$table]['addlFields'] as $addlFields){
          if(isset($addlFields['dataSql'])) $sql   .= ','.$addlFields['dataSql'];
          if(isset($addlFields['fkey'])){
            //retrieve any drop down informations
            $fkeyData = getFkeyData($addlFields['fkey']);
          }
          $options = array();  $selectOptions=array();

          //retrieve dropdown info
          if(isset($addlFields['fkey'])){
            $fkeyData      = getFkeyData($addlFields['fkey']);
            $options       = $fkeyData[0];
            $selectOptions = $fkeyData[1];
          }
                    //additional select options outside of fkey
          if(isset($addlFields['options'])){
            foreach($addlFields['options'] as $optKey=>$option){
              $options[]       = array('id'    => $optKey, 'fkey'   => $option);
              $selectOptions[] = array('value' => $optKey, 'label' => $option);
            }
          }
          $enableCellEdit = (isset($addlFields['enableCellEdit'])?$addlFields['enableCellEdit']:false);
          $vars = array('displayName'=> (isset($addlFields['fieldLabel'])?$addlFields['fieldLabel']:$addlFields['fieldName']),
                      'filter'=> array('selectOptions'=>$selectOptions),
                      'cellFilter'               => 'griddropdown:this',
                      'headerCellClass'          => '$scope.highlightFilteredHeader',
                      'editDropdownValueLabel'   => 'fkey',
                      'editDropdownIdLabel'      => 'id',
                      'editDropdownOptionsArray' => $options,
                      'enableCellEdit'           => $enableCellEdit,
                      'editableCellTemplate'     =>($enableCellEdit==true?'ui-grid/dropdownEditor':''),
                      'field'                    => $addlFields['fieldName'],
                      'name'                     => $addlFields['fieldName'],
                      'minWidth'                 => 100,
                      'width'                    => (isset($addlFields['width'])?$addlFields['width']:'*')
              );

          $vars['displayName']  = (isset($addlFields['fieldLabel'])?$addlFields['fieldLabel']:$addlFields['fieldName']);
          $vars['field']        = $addlFields['fieldName'];
          $vars['name']         = $addlFields['fieldName'];
          //add the field to the column definitions
          $data['columnDefs'][] = $vars;

        }
      }
      //get table data
      $query = "select * ".$sql." from ".$table;

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
  global $tableOptions; global $view_only;
  $fkey='';
  //does this table have foreign key drop downs that need to be set?
  if(isset($tableOptions[$table]['fkey'] )){
    if(is_array($tableOptions[$table]['fkey'] )){
      foreach($tableOptions[$table]['fkey']  as $forKeyData){
        $fkeyData[$forKeyData['fkey']] = getFkeyData($forKeyData);
      }
    }
  }

  //retrieve field names and primary key
  $pquery = "show full columns in " . $table;
  $presult  = $mysqli->query( $pquery );

  $columnDefs = array(
                  array('name' => 'delete', 'displayName' =>'','sortable'=>false,'enableColumnMenu' => false,'enableFiltering'=> false,
                        'enableCellEdit' => false,
                        'cellTemplate' => '<span class="ui-grid-cell-contents ng-binding ng-scope" ng-click="grid.appScope.deleteRow(row)"><i class="fa fa-trash"></i></span>',
                        'width' => '25'
                  )
              );
  //create array of field names
  while ($row = $presult->fetch_assoc()) {
    //var_dump($row);
    $enableFiltering = true;
    $enableCellEdit = ($view_only?false:true);
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

      $columnDefs[] = array('field'            => $row['Field'],
                            'displayName'     => $displayName,
                            'filter' => array('selectOptions' => $selectOptions),
                            'cellFilter'=> 'griddropdown:this',
                            'headerCellClass' => '$scope.highlightFilteredHeader',
                            'editDropdownValueLabel'=> 'fkey',
                            'editDropdownIdLabel'=>'id',
                            'editDropdownOptionsArray'=>$options,
                            'enableCellEdit'  => $enableCellEdit,
                            'editableCellTemplate'=>($enableCellEdit==true?'ui-grid/dropdownEditor':'')
                           );
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