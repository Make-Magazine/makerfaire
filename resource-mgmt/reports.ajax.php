<?php
/*
ajax to populate resource management table
*/
require_once 'config.php';

$tableOptions = array();
//single foreign key
/*
$tableOptions['wp_rmt_resources'] = array(
        array('fkey'             => 'resource_category_id',
              'referenceTable'   => 'wp_rmt_resource_categories',
              'referenceField'   => 'ID',
              'referenceDisplay' => 'category')
        );*/
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
      'fields'=>array('ID','entry_id','resource_id','qty','comment','user','update_stamp'),
        array('fkey'             => 'resource_id',
              'referenceTable'   => 'wp_rmt_resources',
              'referenceField'   => 'ID',
              'referenceDisplay' => 'type')
        );
/*
 * Create table definitons for returning data
 * layout - index = table name
 *  array('fieldName'   => 'resource_category_id',      //name of the field in the table
              'filterType'   => 'text',                  //filter type - text, drop down, etc
              'fieldLabel'  => 'Item',                  //label to display
              'fkey'        => array(                   //if field data is pulled from somewhere else use this section
                    'referenceTable'   => 'wp_rmt_resource_categories',   //fkey table
                    'referenceField'   => 'ID',                           //fkey ID
                    'referenceDisplay' => 'category')                     //fkey field to display
        ),
 */
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'entry_id',             'filterType'   => 'entrylink',     'fieldLabel' => 'Type');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'item', 'filterType'   => 'dropdown', 'fieldLabel' => 'Item',
    'dataSql' => '(select wp_rmt_resources.resource_category_id from wp_rmt_resources where wp_rmt_entry_resources.resource_id = wp_rmt_resources.ID) as item',
                                                 'fkey'      => array('referenceTable'   => 'wp_rmt_resource_categories',
                                                                      'referenceField'   => 'ID',
                                                                      'referenceDisplay' => 'category')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'resource_id',          'filterType'   => 'dropdown',    'fieldLabel'  => 'Type',
                                                 'fkey'      => array('referenceTable'   => 'wp_rmt_resources',
                                                                      'referenceField'   => 'ID',
                                                                      'referenceDisplay' => 'type')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'qty',     'filterType' => 'number', 'fieldLabel'  => 'Qty');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'comment', 'filterType' => 'text', 'fieldLabel'  => 'Comment');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'user',    'filterType' => 'dropdown',   'fieldLabel'  => 'User Updated',
                                                  'fkey'       => array('referenceTable'   => 'wp_users',
                                                                        'referenceField'   => 'ID',
                                                                        'referenceDisplay' => 'user_email'),
                                                  'options' =>array(null=>'Initial','0'=>'Payment')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'update_stamp', 'filterType'   => 'text',     'fieldLabel'  => 'Update Stamp');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'res_status',   'filterType'   => 'dropdown', 'fieldLabel' => 'Resource Status',
                                                 'dataSql' => "(SELECT meta_value FROM `wp_rg_lead_meta` where meta_key = 'res_status' and lead_id =entry_id ) as res_status",
                                                 'options' =>array('review'=>'Review','ready'=>'Ready')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'res_assign',   'filterType'   => 'text', 'fieldLabel' => 'Resource Assign To',
                                                 'dataSql' => "(SELECT meta_value FROM `wp_rg_lead_meta` where meta_key = 'res_assign' and lead_id =entry_id ) as res_assign"
                                                );
$view_only = (isset($_POST['viewOnly'])?$_POST['viewOnly']:FALSE);
$_POST['type']  = 'tableData';
$_POST['table'] = 'wp_rmt_entry_resources';
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
  global $tableOptions; global $view_only;
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
                            'filter' => array('type' => 'uiGridConstants.filter.SELECT',
                                              'selectOptions' => $selectOptions),
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