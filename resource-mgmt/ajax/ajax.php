<?php
/* This ajax is used to return table data for RMT to allow insert, edit and delete logic */
require_once 'config.php';
$selfaire = (isset ( $_POST ['selfaire'] ) ? $_POST ['selfaire'] : '');
require_once 'tableOptions.php';

$view_only = (isset ( $_POST ['viewOnly'] ) ? $_POST ['viewOnly'] : FALSE);
if (isset ( $_POST ['type'] ) && ! empty ( isset ( $_POST ['type'] ) )) {
	$type = $_POST ['type'];

	switch ($type) {
		case "deleteData" :
			deleteData ( $mysqli, $_POST ['table'], $_POST ['pKeyField'], $_POST ['id'] );
			break;
		case "tableData" :
			if ($selfaire == '') {
				getTableData ( $mysqli, $_POST ['table'] );
			} else {
				// pull data based on selected faire
				getDataByFaire ( $mysqli, $_POST ['table'], $selfaire );
			}
			break;
		case "insertData" :
		case "updateData" :
			save_data ( $mysqli, $_POST ['table'], $_POST ['data'], $_POST ['pKeyField'] );
			break;
		case "faires" :
			$sql = 'SELECT * FROM wp_mf_faire order by start_dt DESC';
			$data [$type] = $wpdb->get_results ( $sql );
			echo json_encode ( $data );
			exit ();

			break;
		case "forms" :
			if (isset ( $_POST ['faire'] )) {
				$sql = 'SELECT * ' . '  FROM wp_gf_form, wp_mf_faire ' . ' WHERE  find_in_set (wp_gf_form.id,wp_mf_faire.non_public_forms) > 0 ' . '   AND wp_mf_faire.id =' . $_POST ['faire'] . ' order by title';
			} else {
				$sql = 'SELECT * FROM wp_gf_form order by title';
			}

			$data [$type] = $wpdb->get_results ( $sql );
			echo json_encode ( $data );
			exit ();

			break;
		default :
			invalidRequest ();
	}
} else {
	invalidRequest ();
}

/**
 * This function will handle user add, update functionality
 *
 * @throws Exception
 */
function save_data($mysqli, $table = '', $data = '', $pkeyField = '') {
	global $tableOptions;
	try {
		$id = (isset ( $data ['ID'] ) ? $data ['ID'] : '');
		// get field names
		// set field names and values
		$field_names = array ();
		$field_values = array ();
		if (is_array ( $data )) {
			// loop thru the passed information
			foreach ( $data as $key => $value ) {
				// skip if the field is an additional field and not part of this table
				if (! isset ( $tableOptions[$table]['addlFields'][$key] )) {
					if ($key != $pkeyField && $key != '$$hashKey' && $key != 'sending') { // not valid table fields
						//check for NULL on blank
						if($value==''){																	
							$addKey = array_search($key, array_column($tableOptions[$table]['addlFields'] ?? array(), 'fieldName'));								
							if($addKey !== false && isset($tableOptions[$table]['addlFields'][$addKey]['null_on_blank']) ){
								if($tableOptions[$table]['addlFields'][$addKey]['null_on_blank'])	
									$value = NULL;																		
							}								
						}

					  // if $id is empty this is an insert not an update
						if (empty ( $id )) {
							$field_names [] = $key;														
							$field_values [] = ($value!=NULL? ( string ) $value : NULL);							
						} else {							
							$updateField [] = ($value!=NULL?$key . '="' . $value . '"':$key . '=NULL');
						}
					}
				}
			}
		}

		$query = '';
		if (empty ( $id )) {
			$query = "INSERT INTO " . $table . " (" . implode ( ",", $field_names ) . ') VALUES ("' . implode ( '", "', $field_values ) . '")';
		} else {
			if ($pkeyField != '') {
				// tbd update query
				$query = "Update " . $table . " SET " . implode ( ",", $updateField ) . " where " . $pkeyField . '=' . $id;
			}
		}
		
		if ($mysqli->query ( $query )) {
			$data ['success'] = true;
			if (! empty ( $id ))
				$data ['message'] = 'Data updated successfully.';
			else
				$data ['message'] = 'Data inserted successfully.';
			if (empty ( $id ))
				$data ['ID'] = ( int ) $mysqli->insert_id;
			else
				$data ['ID'] = ( int ) $id;
		} else {
			throw new Exception ( $mysqli->sqlstate . ' - ' . $mysqli->error . ' query=' . $query );
		}
		$mysqli->close ();
		echo json_encode ( $data );
		exit ();
	} catch ( Exception $e ) {
		$data = array ();
		$data ['success'] = false;
		$data ['message'] = $e->getMessage ();
		echo json_encode ( $data );
		exit ();
	}
}

/**
 * This function will handle user deletion
 *
 * @param string $id
 * @throws Exception
 */
function deleteData($mysqli, $table, $pKeyField = '', $id = '') {
	try {
		if (empty ( $id ))
			throw new Exception ( "Invalid ID." );
		if (empty ( $pKeyField ))
			throw new Exception ( "Invalid primary key." );
		$query = "DELETE FROM $table WHERE $pKeyField = $id";

		if ($mysqli->query ( $query )) {
			$data ['success'] = true;
			$data ['message'] = 'Data deleted successfully.';
			echo json_encode ( $data );
			exit ();
		} else {
			throw new Exception ( $mysqli->sqlstate . ' - ' . $mysqli->error );
		}
	} catch ( Exception $e ) {
		$data = array ();
		$data ['success'] = false;
		$data ['message'] = $e->getMessage ();
		echo json_encode ( $data );
		exit ();
	}
}

// return field names and table data of requested db table
function getTableData($mysqli, $table) {
	global $tableOptions;
	if ($table != '') {
		try {
			$data = array ();
			// get table setup
			$columnDefs = defTableInfo ( $table );
			$data ['columnDefs'] = $columnDefs [0];
			$pkey = $columnDefs [1];
			$sql = '';

			
			// any requested additional fields?
			if (isset ( $tableOptions [$table] ['addlFields'] )) {
				foreach ( $tableOptions [$table] ['addlFields'] as $addlFields ) {
					if (isset ( $addlFields ['dataSql'] ))
						$sql .= ',' . $addlFields ['dataSql'];
					if (isset ( $addlFields ['fkey'] )) {
						// retrieve any drop down informations
						$fkeyData = getFkeyData ( $addlFields ['fkey'] );
					}
					$options = array ();
					$selectOptions = array ();

					// retrieve dropdown info
					if (isset ( $addlFields ['fkey'] )) {
						$fkeyData = getFkeyData ( $addlFields ['fkey'] );
						$options = $fkeyData [0];
						$selectOptions = $fkeyData [1];
					}

					// additional select options outside of fkey
					if (isset ( $addlFields ['options'] )) {
						foreach ( $addlFields ['options'] as $optKey => $option ) {
							$options [] = array (
									'id' => $optKey,
									'fkey' => $option
							);
							$selectOptions [] = array (
									'value' => $optKey,
									'label' => $option
							);
						}
					}
					$enableCellEdit = (isset ( $addlFields ['enableCellEdit'] ) ? $addlFields ['enableCellEdit'] : true);
					$vars = array (
							'name' => $addlFields ['fieldName'],
							'displayName' => (isset ( $addlFields ['fieldLabel'] ) ? $addlFields ['fieldLabel'] : $addlFields ['fieldName']),														
							'enableCellEdit' => $enableCellEdit,							
							//'field' => $addlFields ['fieldName'],							
							'minWidth' => "100",							
							'width' => (isset ( $addlFields ['width'] ) ? $addlFields ['width'] : '100'),
							'headerCellClass' => '$scope.highlightFilteredHeader',
							'enableFiltering'=> true
					);
					
					if(isset($addlFields['filterType']) && $addlFields['filterType']=='dropdown'){
						$vars['cellFilter'] = 'griddropdown:this';						
						$vars['editDropdownValueLabel'] = 'fkey';
						$vars['editDropdownIdLabel'] = 'id';
						$vars['editDropdownOptionsArray'] = $options;
						$vars['editableCellTemplate'] = ($enableCellEdit == true ? 'ui-grid/dropdownEditor' : '');
					}
					if(!empty($selectOptions)){
						$vars['filter'] = array ('selectOptions' => $selectOptions);
					}
					
					if(isset($addlFields['sort'])){
						$vars['sort'] = $addlFields ['sort'];
					}			
					if(isset($addlFields['type'])){
						$vars['type'] = $addlFields ['type'];
					}	
					if(isset($addlFields['cellFilter'])){
						$vars['cellFilter'] = $addlFields ['cellFilter'];
					}
					
					// add the field to the column definitions
					$colDefFound = false;
					foreach($data ['columnDefs'] as $key=>$columnDef){
						if($columnDef['name'] == $vars['name']){
							$colDefFound = true;							
							//merge the two
							$data['columnDefs'][$key] = array_merge($data['columnDefs'][$key], $vars);							
						}
					}
					//not an existing field, add it
					if(!$colDefFound)
						$data['columnDefs'][] = $vars;
				}
			}
			// get table data
			$query = "select * " . $sql . " from " . $table;

			$result = $mysqli->query ( $query );
			// create array of table data
			while ( $row = $result->fetch_assoc () ) {
				$data ['data'] [] = $row;
			}
			$data ['pInfo'] = $pkey;
			$data ['success'] = true;
			echo json_encode ( $data );
			exit ();
		} catch ( Exception $e ) {
			$data = array ();
			$data ['success'] = false;
			$data ['message'] = $e->getMessage ();
			echo json_encode ( $data );
			exit ();
		}
	}
}
function invalidRequest() {
	$data = array ();
	$data ['success'] = false;
	$data ['message'] = "Invalid request.";
	echo json_encode ( $data );
	exit ();
}
function defTableInfo($table) {
	global $mysqli;
	global $tableOptions;
	global $view_only;
	$fkey = '';
	// does this table have foreign key drop downs that need to be set?
	if (isset ( $tableOptions [$table] ['fkey'] )) {
		if (is_array ( $tableOptions [$table] ['fkey'] )) {
			foreach ( $tableOptions [$table] ['fkey'] as $forKeyData ) {
				$fkeyData [$forKeyData ['fkey']] = getFkeyData ( $forKeyData );
			}
		}
	}

	// retrieve field names and primary key
	$pquery = "show full columns in " . $table;
	$presult = $mysqli->query ( $pquery );

	$columnDefs = array (
			array (
					'name' => 'delete',
					'displayName' => '',
					'sortable' => false,
					'enableColumnMenu' => false,
					'enableFiltering' => false,
					'enableCellEdit' => false,
					'cellTemplate' => '<span class="ui-grid-cell-contents ng-binding ng-scope" ng-click="grid.appScope.deleteRow(row)"><i class="fa fa-trash"></i></span>',
					'width' => '25'
			)
	);
	// create array of field names
	while ( $row = $presult->fetch_assoc () ) {
		// var_dump($row);
		$enableFiltering = true;
		$enableCellEdit = ($view_only ? false : true);
		$width = '150';
		if ($row ['Key'] == 'PRI') {
			$pkey = $row ['Field'];
			$enableFiltering = false;
			$enableCellEdit = false;
			$width = '50';
		}
		// foreign key's have a different setup
		if (isset ( $fkeyData [$row ['Field']] )) { // is this the foreign key?
			$selectOptions = $fkeyData [$row ['Field']] [1];
			$options = $fkeyData [$row ['Field']] [0];
			$displayName = (isset ( $row ['Comment'] ) && $row ['Comment'] != '' ? $row ['Comment'] : $row ['Field']);
			// replace underscores and dashes with a space and then make capitalize the first letter of each word
			$displayName = str_replace ( '_', ' ', $displayName );
			$displayName = str_replace ( '-', ' ', $displayName );
			$displayName = ucwords ( $displayName );

			$columnDefs [] = array (
					'field' => $row ['Field'],
					'displayName' => $displayName,
					'filter' => array (
							'selectOptions' => $selectOptions
					),
					'cellFilter' => 'griddropdown:this',
					'headerCellClass' => '$scope.highlightFilteredHeader',
					'editDropdownValueLabel' => 'fkey',
					'editDropdownIdLabel' => 'id',
					'editDropdownOptionsArray' => $options,
					'enableCellEdit' => $enableCellEdit,
					'editableCellTemplate' => ($enableCellEdit == true ? 'ui-grid/dropdownEditor' : '')
			);
		} else {
			// tbd need to find a formatting that will work with a datetime field
			// date type does not save correctly in the database
			if ($row ['Type'] == 'datetime') {
				$type = "date";
			} else {
				$type = 'string';
			}
			$displayName = (isset ( $row ['Comment'] ) && $row ['Comment'] != '' ? $row ['Comment'] : $row ['Field']);
			// replace underscores and dashes with a space and then make capitalize the first letter of each word
			$displayName = str_replace ( '_', ' ', $displayName );
			$displayName = str_replace ( '-', ' ', $displayName );
			$displayName = ucwords ( $displayName );

			$columnDefs [] = array (
					'name' => $row ['Field'],
					'displayName' => $displayName,
					'enableCellEdit' => $enableCellEdit,
					'enableFiltering' => $enableFiltering,
					'width' => $width,
					'headerCellClass' => '$scope.highlightFilteredHeader'
			);
		}
	}

	$pkey = 'ID';
	return (array (
			$columnDefs,
			$pkey
	));
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
  if($result){
	  while ($row = $result->fetch_assoc()) {
	    $options[]       = array('id'    => $row[$referenceField], 'fkey'   => $row[$referenceDisplay]);
	    $selectOptions[] = array('value' => $row[$referenceField], 'label' => $row[$referenceDisplay]);
	  }
  }
  return(array($options,$selectOptions));
}

// pull data based on selected faire
function getDataByFaire($mysqli, $table, $selfaire) {
	$data = array ();
	$data ['columnDefs'] = array ();
	$data ['data'] = array ();

	// area options query
	$areaQuery = 'select * from wp_mf_faire_area where faire_id=' . $selfaire;
	$arearesult = $mysqli->query ( $areaQuery );

	// create array of table data
	$editOptions = array ();
	$selectOptions = array ();
	while ( $row = $arearesult->fetch_assoc () ) {
		$editOptions [] = array (
				'id' => $row ['ID'],
				'fkey' => $row ['area']
		);
		$selectOptions [] = array (
				'value' => $row ['ID'],
				'label' => $row ['area']
		);
	}

	// create array of subarea information
	if ($table == 'wp_mf_faire_subarea') {
		// build columndefs
		$data ['columnDefs'] [] = array (
				'cellTemplate' => "<span class='ui-grid-cell-contents ng-binding ng-scope' ng-click='grid.appScope.deleteRow(row)'><i class='fa fa-trash'></i></span>",
				'displayName' => "",
				'enableCellEdit' => false,
				'enableColumnMenu' => false,
				'enableFiltering' => false,
				'name' => "delete",
				'sortable' => false,
				'width' => "25"
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "ID",
				'enableCellEdit' => false,
				'enableFiltering' => false,
				'headerCellClass' => '$scope.highlightFilteredHeader',
				'name' => "ID",
				'width' => "50"
		);
		$data ['columnDefs'] [] = array (
				'cellFilter' => "griddropdown:this",
				'displayName' => "Area",
				'editDropdownIdLabel' => "id",
				'editDropdownOptionsArray' => $editOptions,
				'editDropdownValueLabel' => "fkey",
				'editableCellTemplate' => "ui-grid/dropdownEditor",
				'enableCellEdit' => true,
				'field' => "area_id",
				'filter' => array (
						'selectOptions' => $selectOptions
				),
				'headerCellClass' => '$scope.highlightFilteredHeader',
				'width' => 100
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "Internal Name",
				'enableCellEdit' => true,
				'enableFiltering' => true,
				'name' => "subarea",
				'width' => "300"
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "Public Name",
				'enableCellEdit' => true,
				'enableFiltering' => true,
				'name' => "nicename",
				'width' => "300"
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "Order",
				'enableCellEdit' => true,
				'enableFiltering' => true,
				'name' => "sort_order",
				'width' => "75"
		);
		$data ['columnDefs'] [] = array (
			'displayName' => "Exposure",
			'enableCellEdit' => true,
			'enableFiltering' => true,
			'name' => "exposure"
	);
		// build data
		$query = "select wp_mf_faire_subarea.*, wp_mf_faire_area.faire_id as faire,
                (SELECT count(*) from wp_mf_location where wp_mf_faire_subarea.ID = subarea_id) as assCount
              from wp_mf_faire_subarea
              left outer join wp_mf_faire_area on wp_mf_faire_area.ID = area_id
              left outer join wp_mf_faire on wp_mf_faire_area.faire_id = wp_mf_faire.id
              where wp_mf_faire_area.faire_id = " . $selfaire;
		$result = $mysqli->query ( $query );
		// create array of table data
		while ( $row = $result->fetch_assoc () ) {
			$data ['data'] [] = $row;
		}
		$data ['pInfo'] = 'ID';
		$data ['success'] = true;
	} elseif ($table == 'wp_mf_schedule') {
		// build columndefs
		$data ['columnDefs'] [] = array (
				'cellTemplate' => "<span class='ui-grid-cell-contents ng-binding ng-scope' ng-click='grid.appScope.deleteRow(row)'><i class='fa fa-trash'></i></span>",
				'displayName' => "",
				'enableCellEdit' => false,
				'enableColumnMenu' => false,
				'enableFiltering' => false,
				'name' => "delete",
				'sortable' => false,
				'width' => "25"
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "ID",
				'enableCellEdit' => false,
				'enableFiltering' => false,
				'name' => "ID",
				'visible' => "false",
				'width' => 50
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "Entry",
				'enableCellEdit' => false,
				'enableFiltering' => true,
				'name' => "entry_id",
				'width' => "80"
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "Exhibit",
				'enableCellEdit' => false,
				'enableFiltering' => true,
				'name' => "exName",
				'dataType' => 'external'
		);

		$data ['columnDefs'] [] = array (
				'displayName' => "SubArea",
				'enableCellEdit' => false,
				'enableFiltering' => true,
				'name' => "subarea",
				'width' => "110"
		);
		// $data['columnDefs'][] = array('displayName' => "Location", 'enableCellEdit' => false, 'enableFiltering' => true, 'name' => "location_id", 'width' =>"100");
		$data ['columnDefs'] [] = array (
				'displayName' => "Start",
				'enableCellEdit' => true,
				'enableFiltering' => true,
				'name' => "start_dt",
				'width' => "150"
		);
		$data ['columnDefs'] [] = array (
				'displayName' => "End",
				'enableCellEdit' => true,
				'enableFiltering' => true,
				'name' => "end_dt",
				'width' => "150"
		);
		// set type drop down
		$typeOptions = array (
				'demo',
				'performance',
				'talk',
				'workshop'
		);
		$typeEditOptions = array ();
		$typeSelectOptions = array ();
		foreach ( $typeOptions as $type ) {
			$typeEditOptions [] = array (
					'id' => $type,
					'fkey' => ucfirst ( $type )
			);
			$typeSelectOptions [] = array (
					'value' => $type,
					'label' => ucfirst ( $type )
			);
		}
		$data ['columnDefs'] [] = array (
				'cellFilter' => "griddropdown:this",
				'displayName' => "Type",
				'enableCellEdit' => true,
				'editDropdownIdLabel' => "id",
				'editDropdownOptionsArray' => $typeEditOptions,
				'editDropdownValueLabel' => "fkey",
				'editableCellTemplate' => "ui-grid/dropdownEditor",
				'field' => "type",
				'width' => "100",
				'filter' => array (
						'selectOptions' => $typeSelectOptions
				)
		);

		// build data
		$query = 'SELECT  wp_mf_schedule.*,' . '(SELECT nicename FROM `wp_mf_location`, wp_mf_faire_subarea WHERE wp_mf_location.ID = 6319 and wp_mf_location.subarea_id = wp_mf_faire_subarea.id) as subarea, ' . '(SELECT meta_value  as value FROM `wp_gf_entry_meta` where meta_key = "151" and wp_gf_entry_meta.entry_id = wp_mf_schedule.entry_id limit 1) as exName ' . ' from wp_mf_schedule, wp_mf_faire where wp_mf_faire.faire = wp_mf_schedule.faire and wp_mf_faire.id=' . $selfaire;

		$result = $mysqli->query ( $query );
		// create array of table data
		while ( $row = $result->fetch_assoc () ) {
			$data ['data'] [] = $row;
		}

		$data ['pInfo'] = 'ID';
		$data ['success'] = true;
	}
	echo json_encode ( $data );
	exit ();
}