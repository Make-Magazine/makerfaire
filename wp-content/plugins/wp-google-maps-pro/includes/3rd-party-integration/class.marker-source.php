<?php

namespace WPGMZA\Integration;

abstract class MarkerSource
{
	private static $cachedPostIDsByMetaID;
	
	public function __construct()
	{
		add_filter('wpgmza_get_integration_queries', array($this, 'onGetIntegrationQueries'), 10, 4);
		
		add_filter('wpgmza_import_export_document', array($this, 'onImportExportDocument'));
		add_filter('wpgmza_map_integration_panel', array($this, 'onMapIntegrationPanel'), 10, 2);
		add_action('wpgmza_map_saved', array($this, 'onMapSaved'));
		
		//add_action('wpgmza_get_integrated_markers_for_query_select', array($this, 'onGetIntegratedMarkersForQuerySelect'), 10, 3);
	}
	
	public static function createInstanceFromPOST()
	{
		$class = stripslashes($_POST['import_class']);
		
		if(!wpgmza_user_can_edit_maps() ||
			!isset( $_POST['wpgmaps_security'] ) ||
			!wp_verify_nonce( $_POST['wpgmaps_security'], 'wpgmaps_import' ))
		{
			wp_send_json_error( __( 'Security check failed.', 'wp-google-maps' ) );
		}
		
		if(!class_exists($class))
			wp_send_json_error(__('Specified import class does not exist', 'wp-google-maps'));
		
		$instance = new $class();
		
		if(!($instance instanceof MarkerSource))
			wp_send_json_error(__('Class must be an instance of \WPGMZA\Integration\MarkerSource', 'wp-google-maps'));
		
		return $instance;
	}
	
	/*
	 * This function will get a post ID for the given postmeta ID for a post.
	 * This is used to speed the process up rather than hitting the DB in ProMarker's constructor
	 */
	public static function getPostIDFromMetaID($meta_id)
	{
		global $wpdb;
		
		if(!MarkerSource::$cachedPostIDsByMetaID)
			MarkerSource::$cachedPostIDsByMetaID = array();
		
		if(!isset(MarkerSource::$cachedPostIDsByMetaID[$meta_id]))
			return $wpdb->get_var("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_id=" . (int)$meta_id);
		
		return MarkerSource::$cachedPostIDsByMetaID[$meta_id];
	}
	
	/*
	 * Adds an array of post IDs, indexed by postmeta ID
	 */
	public static function addPostIDFromMetaIDToCache($post_ids_by_meta_id)
	{
		if(!MarkerSource::$cachedPostIDsByMetaID)
			MarkerSource::$cachedPostIDsByMetaID = array();
		
		if(!is_array($post_ids_by_meta_id))
			throw new \Exception("Input must be an array");
		
		MarkerSource::$cachedPostIDsByMetaID = array_merge(MarkerSource::$cachedPostIDsByMetaID, $post_ids_by_meta_id);
	}

	/**
	 * Get additional fields for a specific integration
	 * 
	 * This static acts as a relay, meaning it trigger some events in classes that extend MarkerSource to
	 * find any additional fields that might be relevant to the marker we are working with
	 * 
	 * This allows that to be fed back to the core systems for output to the info-windows
	 * 
	 * @param string $metaId The meta ID (index) to hydrate off of the cache 
	 *  
	 * @return array
	 */
	public static function getAdditionalFieldsFromMetaID($metaId){
		$fields = array();
		if(!empty($metaId)){
			$postId = MarkerSource::getPostIDFromMetaID($metaId);
			if(!empty($postId)){
				/* Developer Hook (Filter) -  Add additional fields for use by the system, passes fields, postId and metaId */
				$fields = apply_filters('wpgmza_get_additional_integration_fields', $fields, $postId, $metaId);
			}
		}

		return $fields;

	}
	
	abstract public function getSettingName();
	abstract public function getQuery($fields=null, $markerFilter=null, $inputParams=null);
	
	abstract public function getCategoryFilteringClauseMarkerIDFieldName();

	abstract public function onMapIntegrationOptions($document, $map);
	
	public function isEnabled($map)
	{
		return !empty($map->{$this->getSettingName()});
	}
	
	protected function getIntegrationControl($document, $name, $type='radio', $class=null, $caption=null)
	{
		if(!class_exists($class))
			throw new \Exception("Specified class does not exist");
		
		if(empty($caption))
			throw new \Exception("Caption cannot be empty");
		
		$label = $document->createElement('label');
		$input = $document->createElement('input');
		
		$input->addClass($name);
		$input->setAttribute('type', $type);
		$input->setAttribute('name', $name);
		$input->setAttribute('value', $class);
		$input->setAttribute('data-wpgmza-integration-class', $class);
		
		$label->appendChild($input);
		$label->appendText(' ' . $caption);
		
		return $label;
	}
	
	public function getIntegratedMarkers()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		$query = $this->getQuery(
			$wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS")
		);
		
		$stmt = $query->build();
		$results = $wpdb->get_results($stmt);

		$results = $this->setFeaturedImages($results);
		$results = $this->setPermalinks($results);

		return $results;
	}
	
	public function onGetIntegrationQueries($input, $fields, $markerFilter, $inputParams)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_MARKERS;
		
		if(empty($markerFilter->map) || !$this->isEnabled($markerFilter->map))
			return $input;
		
		if(empty($fields))
			$fields = $wpdb->get_col("SHOW COLUMNS FROM $WPGMZA_TABLE_NAME_MARKERS");
		
		$query = $this->getQuery($fields, $markerFilter, $inputParams);
		$query->integrationSource = $this;
		
		$input[] = $query;
		
		return $input;
	}
	
	public function importMarkers($map_id, $replace_map_data=false)
	{
		$data = $this->getIntegratedMarkers();
		
		if($replace_map_data)
		{
			$map = \WPGMZA\Map::createInstance($map_id);
			$markers = $map->getMarkers();
			
			foreach($markers as $marker)
				$marker->trash();
		}
		
		foreach($data as $obj)
		{
			unset($obj->id);
			$obj->map_id = $map_id;
			
			$marker = \WPGMZA\Marker::createInstance($obj);
		}
	}
	
	public function setPermalinks($markers)
	{
		foreach($markers as $key => $data)
		{
			if(!preg_match('/\d+$/', $data->id, $m))
				throw new \Exception('Cannot determine post ID from integrated marker data');
			
			$postId = MarkerSource::$cachedPostIDsByMetaID[$key];
			$markers[$key]->link = get_permalink($postId);
		}
		
		return $markers;
	}

	public function setFeaturedImages($markers){
		foreach($markers as $key => $data){
			if(!preg_match('/\d+$/', $data->id, $m))
				throw new \Exception('Cannot determine post ID from integrated marker data');
			
			$postId = MarkerSource::$cachedPostIDsByMetaID[$key];
			$thumbnail = get_post_thumbnail_id($postId);
			if(!empty($thumbnail)){
				$src = wp_get_attachment_image_src($thumbnail);
				$markers[$key]->pic = $src[0];
			}
		}
		return $markers;
	}
	
	public function onMapIntegrationPanel($document, $map)
	{
		global $wpgmza;

		$container = $document->querySelector('#wpgmza-integration-panel');
		$settingName = $this->getSettingName();
		
		$checkbox = $this->getIntegrationControl($document, $settingName, 'checkbox');
		
		if(!empty($map->{$settingName})){
			$checkbox->querySelector('input')->setAttribute('checked', 'checked');
		}

		if($wpgmza->internalEngine->isLegacy()){
			$container->appendChild($document->createElement('br'));
			$container->appendChild($checkbox);
		} else {
			$li = $document->createElement('li');
			$li->appendChild($checkbox);
			$container->appendChild($li);
		}

		$this->onMapIntegrationOptions($document, $map);
		
		return $document;
	}
	
	public function onImportExportDocument($document)
	{
		$import_via = $document->querySelector("#import_via");
		
		$import_via->appendChild($document->createElement('br'));
		$import_via->appendChild($this->getIntegrationControl($document, 'import_data_type', 'radio'));
		
		return $document;
	}
	
	public function onImportExportOptions()
	{
		$document = new \WPGMZA\DOMDocument();
		$document->loadPHPFile(WPGMZA_PRO_DIR_PATH . 'html/import-export/integration-import-options-panel.html.php');
		
		$select = new \WPGMZA\MapSelect();
		$document->querySelector('#map-select-container')->import($select->html());
				
		return $document;
	}
	
	public function onMapSaved($map)
	{
		$settingName = $this->getSettingName();
		$enabled = !empty($_POST[$settingName]);
		$map->{$settingName} = $enabled;
	}

	/**
	 * Checks if tables align collations with wp_postmeta tables 
	 * 
	 * This is an actionable check, which can fix issues with some marker sources (ACF/Woo) not loading markers
	 * 
	 * Although, this check is only a test of mismatched tables, and does not resolve the mismatch
	 * 
	 * @return object
	*/
	public static function checkTableCollationMismatch(){
		global $wpdb, $WPGMZA_TABLE_NAME_MAPS, $WPGMZA_TABLE_NAME_MARKERS,
				$WPGMZA_TABLE_NAME_CATEGORIES, $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES,
				$WPGMZA_TABLE_NAME_CATEGORY_MAPS, $WPGMZA_TABLE_NAME_CUSTOM_FIELDS, $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS;

		$postMetaTable = $wpdb->prefix . "postmeta";
		$baseResult = $wpdb->get_results("SHOW TABLE STATUS WHERE name LIKE '{$postMetaTable}'");
		if(!empty($baseResult) && is_array($baseResult)){
			$baseResult = array_shift($baseResult);
			if(!empty($baseResult->Collation)){
				$baseCollation = $baseResult->Collation;

				$tables = array(
					$WPGMZA_TABLE_NAME_MAPS,
					$WPGMZA_TABLE_NAME_MARKERS,
					$WPGMZA_TABLE_NAME_CATEGORIES,
					$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES,
					$WPGMZA_TABLE_NAME_CATEGORY_MAPS,
					$WPGMZA_TABLE_NAME_CUSTOM_FIELDS,
					$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS
				);

				$mismatch = array();
				foreach($tables as $name){
					$mismatchResult = $wpdb->get_results("SHOW TABLE STATUS WHERE name LIKE '{$name}'");
					if(!empty($mismatchResult) && is_array($mismatchResult)){
						$mismatchResult = array_shift($mismatchResult);
						if(!empty($mismatchResult->Collation)){
							if($mismatchResult->Collation !== $baseCollation){
								$mismatch[] = $name;
							}
						}
					}
				}

				if(!empty($mismatch)){
					return (object) array(
						'tables' => $mismatch,
						'collation' => $baseCollation
					);
				}
			}
		}
		return false;
	}

	/**
	 * Resolves table mismatches between our tables and the wp_postmeta table
	 * 
	 * This is done using the check collation mismatch method again, although this may not be needed, it seems to be safer to do this in a two-step action
	 * 
	 * @return bool
	*/
	public static function resolveTableCollationMismatch(){
		global $wpdb;
		$mismatch = MarkerSource::checkTableCollationMismatch();
		if(!empty($mismatch) && !empty($mismatch->tables) && !empty($mismatch->collation)){
			$collation = $mismatch->collation;
			$charset = false;

			if(strpos($collation, "_") !== FALSE){
				$charset = substr($collation, 0, strpos($collation, "_"));
			}

			if(!empty($charset)){
				foreach($mismatch->tables as $table){
					$sql = "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}";
					
					$wpdb->query($sql);					
				}
			}
			return true;
		}
		return false;
	}
}

add_action('wp_ajax_wpgmza_import_integration_options', function() {

	$instance = MarkerSource::createInstanceFromPOST();
	
	wp_send_json_success(array(
		'options_html'	=> $instance->onImportExportOptions()->html
	));
	
	exit;
	
});

add_action('wp_ajax_wpgmza_import_integration', function() {
	
	if(!isset($_POST['map_id']))
	{
		wp_send_json_error('No map ID specified');
		exit;
	}
	
	$instance = MarkerSource::createInstanceFromPOST();
	
	if(is_string($_POST['replace_map_data']))
		$replace_map_data = $_POST['replace_map_data'] == 'true';
	else
		$replace_map_data = !empty($_POST['replace_map_data']);
	
	$instance->importMarkers($_POST['map_id'], $replace_map_data);
	
	wp_send_json_success(array(
		'success' => 1
	));
	
});