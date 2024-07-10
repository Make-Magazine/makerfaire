<?php
namespace WPGMZA;
 
/**
 * CSV Exporter
 *
 * @since 9.0.0
 * 
*/
class ExportCSV extends Export{
	/**
	 * Constructor
	*/
	public function __construct( $args = array() ) {
		$options = wp_parse_args($args, 
			array(
				'maps'      => array(),
				'type'      => 'markers'
			) 
		);

		if (is_array($options['maps'])) {
			$options['maps'] = $this->sanitize_map_ids($options['maps']);
		} else {
			$options['maps'] = array();
		}

		$this->options = (object) $options;
	}

	/**
	 * Generates and dowload th CSV (SHOULD BE EXTENDED FROM ABSTRACTION)
	 *
	 * @return void
	*/
	public function download() {
		$data = $this->getData();

		$filename = "wpgmza_export_{$this->options->type}.csv";

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Expires: 0");
        header("Pragma: public");

        $handle = @fopen('php://output', 'w');
        
		if(!empty($data)){
			if(!empty($data->headers)){
	            fputcsv($handle, $data->headers, ",", '"');
			}

			$rows = $data->rows;
			foreach($rows as $row){
				$row = is_array($row) ? $row : (array) $row;
            	fputcsv($handle, $row, ",", '"');
            }            
		}
        fclose($handle);
	}

	/**
	 * Get the data for the export
	 * 
	 * This dynamically calls a sub-method of the class based on the export type being executed
	 * 
	 * Returned object is directly from the sub method and should include both rows/headers
	 * 
	 * @return object
	*/
	public function getData(){
		$method = "get" . ucwords($this->options->type);
		if(method_exists($this, $method)){
			return $this->{$method}();
		} else {
			throw new \Error("Unknown import method '{$method}'");
		}
	}

	/**
	 * Get map data, as is
	 * 
	 * @return object
	*/
	public function getMaps(){
		global $wpdb, $WPGMZA_TABLE_NAME_MAPS;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT * FROM `{$WPGMZA_TABLE_NAME_MAPS}` {$where}");

		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get marker data, and hydrate fields and categories correctly
	 * 
	 * This method also removed any spatial columns in favor of human-readable data
	 * 
	 * @return object
	*/
	public function getMarkers(){
		global $wpdb, $wpgmza, 
				$WPGMZA_TABLE_NAME_MARKERS, $WPGMZA_TABLE_NAME_CUSTOM_FIELDS, 
				$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS, $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES;

        CustomFields::install();

		$where = $this->buildWhereSql();

        $data = $wpdb->get_results("SELECT *, {$wpgmza->spatialFunctionPrefix}X(latlng) AS lat, {$wpgmza->spatialFunctionPrefix}Y(latlng) AS lng FROM `{$WPGMZA_TABLE_NAME_MARKERS}` {$where}");
        $customFields = $wpdb->get_results("SELECT `id`, `name` FROM `{$WPGMZA_TABLE_NAME_CUSTOM_FIELDS}`");
        $hasCategories = $wpdb->get_results( "SELECT * FROM `{$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES}`");

        $data = $this->removeSpatialColumns($data);
        $headers = $this->getHeadersFromData($data);

        foreach($data as $key => $row){
        	$id = intval($row->id);

        	/* Hydrate Custom Fields */
        	if(!empty($customFields)){
    			$row = (array) $row;
        		
        		$customFieldData = $wpdb->get_results("SELECT `field_id`, `value` FROM `{$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS}` WHERE `object_id` = {$id}");
        		foreach($customFields as $fieldData){
        			$fieldValue = "";
        			foreach($customFieldData as $storedKey => $storedField){
        				if(intval($storedField->field_id) === intval($fieldData->id)){
        					/* This is the same field */
        					$fieldValue = $storedField->value;
        					unset($customFieldData->{$storedKey});
        				}
        			}

        			$row[] = $fieldValue;
        		}

        		$row = (object) $row;
        		$data[$key] = $row;
        	}

        	/* Hydrate Categories */
        	if(isset($row->category)){
        		$categories = array();
        		foreach($hasCategories as $category){
        			if(!in_array($category->category_id, $categories) && $id === intval($category->marker_id)){
        				$categories[] = $category->category_id;
        			}
        		}

        		if(!empty($categories)){
        			$row->category = implode(', ', $categories);
        		}
        	}
        }

        /* Append Custom fields to headers */
       	if(!empty($customFields) && !empty($data)){
       		foreach($customFields as $fieldData){
       			if(!empty($fieldData->name)){
       				$headers[] = "Field: {$fieldData->name}";
       			}
       		}
       	}

       	return (object) array(
       		'headers' => $headers,
       		'rows' => $data
       	);
	}

	/**
	 * Get polygons
	 * 
	 * @return object
	*/
	public function getPolygons(){
		global $wpdb, $WPGMZA_TABLE_NAME_POLYGONS;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT * FROM `{$WPGMZA_TABLE_NAME_POLYGONS}` {$where}");

		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get polylines
	 * 
	 * @return object
	*/
	public function getPolylines(){
		global $wpdb, $WPGMZA_TABLE_NAME_POLYLINES;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT * FROM `{$WPGMZA_TABLE_NAME_POLYLINES}` {$where}");

		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get circles
	 * 
	 * This method also removes spatial columns and replaces them with human readable alternatives
	 * 
	 * @return object
	*/
	public function getCircles(){
		global $wpdb, $wpgmza, $WPGMZA_TABLE_NAME_CIRCLES;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT *, {$wpgmza->spatialFunctionPrefix}X(center) AS center_x, {$wpgmza->spatialFunctionPrefix}Y(center) AS center_y FROM `{$WPGMZA_TABLE_NAME_CIRCLES}` {$where}");
		$data = $this->removeSpatialColumns($data);

		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get rectangles
	 * 
	 * This method also removes spatial columns and replaces them with human readable alternatives
	 * 
	 * @return object
	*/
	public function getRectangles(){
		global $wpdb, $wpgmza, $WPGMZA_TABLE_NAME_RECTANGLES;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT *, {$wpgmza->spatialFunctionPrefix}X(cornerA) AS corner_ax, {$wpgmza->spatialFunctionPrefix}Y(cornerA) AS corner_ay,
											{$wpgmza->spatialFunctionPrefix}X(cornerB) AS corner_bx, {$wpgmza->spatialFunctionPrefix}Y(cornerB) AS corner_by FROM `{$WPGMZA_TABLE_NAME_RECTANGLES}` {$where}");
		
		$data = $this->removeSpatialColumns($data);
			
		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get point labels
	 * 
	 * This method also removes spatial columns and replaces them with human readable alternatives
	 * 
	 * @return object
	*/
	public function getPointlabels(){
		global $wpdb, $wpgmza, $WPGMZA_TABLE_NAME_POINT_LABELS;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT *, {$wpgmza->spatialFunctionPrefix}X(center) AS center_x, {$wpgmza->spatialFunctionPrefix}Y(center) AS center_y FROM `{$WPGMZA_TABLE_NAME_POINT_LABELS}` {$where}");
		$data = $this->removeSpatialColumns($data);

		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get image overlays
	 * 
	 * This method also removes spatial columns and replaces them with human readable alternatives
	 * 
	 * @return object
	*/
	public function getImageoverlays(){
		global $wpdb, $wpgmza, $WPGMZA_TABLE_NAME_IMAGE_OVERLAYS;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT *, {$wpgmza->spatialFunctionPrefix}X(cornerA) AS corner_ax, {$wpgmza->spatialFunctionPrefix}Y(cornerA) AS corner_ay,
											{$wpgmza->spatialFunctionPrefix}X(cornerB) AS corner_bx, {$wpgmza->spatialFunctionPrefix}Y(cornerB) AS corner_by FROM `{$WPGMZA_TABLE_NAME_IMAGE_OVERLAYS}` {$where}");
		
		$data = $this->removeSpatialColumns($data);
			
		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data,
		);
	}

	/**
	 * Get heatmap datasets
	 * 
	 * @return object
	*/
	public function getDatasets(){
		global $wpdb, $WPGMZA_TABLE_NAME_HEATMAPS;

		$where = $this->buildWhereSql();

		$data = $wpdb->get_results("SELECT * FROM `{$WPGMZA_TABLE_NAME_HEATMAPS}` {$where}");

		return (object) array(
			'headers' => $this->getHeadersFromData($data),
			'rows' => $data
		);
	}

	/**
	 * Automatically generates headers from the queried data
	 * 
	 * This works by sampling the first row and converting the keys to column names
	 *
	 * @param array $data The query data
	 *  
	 * @return array
	*/
	public function getHeadersFromData($data){
		$headers = array();
		if(!empty($data) && is_array($data)){
			$row = !empty($data[0]) ? (array) $data[0] : false;
			if(!empty($row)){
				$headers = array_keys($row);
			}
		}

		return $headers;
	}

	/**
	 * Remove spatial column data from the results
	 * 
	 * These data types simply do not work with CSV due to the way they are encoded. This cleans things up so that the export/import flow is not affected
	 * 
	 * @param array $data The query data
	 * 
	 * @return array
	*/
	public function removeSpatialColumns($data){
		$spatial = array('latlng', 'center', 'cornerA', 'cornerB');

		if(!empty($data) && is_array($data)){
			foreach($data as $row){
				foreach($spatial as $column){
					if(is_object($row)){
						if(isset($row->{$column})){
							unset($row->{$column});
						}
					} else if(is_array($row)){
						if(isset($row[$column])){
							unset($row[$column]);
						}
					}
				}
			}
		}
		return $data;
	}

	/**
	 * Builds the where condition for any data query in this class
	 * 
	 * This uses the internal 'type' option to determine how it should be handling this export
	 * 
	 * It may return an empty string, in which case, the standard statement will be run as it is
	 * 
	 * @return string
	*/
	public function buildWhereSql(){
		$where = array();

		$mapColumn = 'map_id';
		if(!empty($this->options)){
			if(!empty($this->options->type) && $this->options->type === 'maps'){
				$mapColumn = 'id';
			}

			if(!empty($this->options->maps)){
				if(is_array($this->options->maps)){
					/* At least one map to filter */
					$where[] = "`{$mapColumn}` IN (" . implode(",", $this->options->maps) . ")";
				}
			}
		} 

		return !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
	}

}