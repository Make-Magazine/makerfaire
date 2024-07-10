<?php

namespace WPGMZA\BatchedImport;

class CSV extends \WPGMZA\BatchedImport{

	/**
	 * Constructor
	*/
	public function __construct($id=-1){
		\WPGMZA\BatchedImport::__construct($id);
		
		$this->seconds = 2;
	}

	/**
	 * Get the total rows in the CSV
	 * 
	 * @return int 
	*/
	protected function getTotalSteps(){
		$rows = 0;
		
		$source = !empty($this->sourceIsRemote) && !empty($this->source) && !empty($this->mirrorFile) ? $this->mirrorFile : $this->source;
		$fp = fopen($source, "r");
		
		if(!$fp){
			throw new \Exception('Invalid file');
		}
		
		while(($record = fgetcsv($fp)) !== false){
			$rows++;
		}
		
		return $rows;
	}
	
	/**
	 * Import a the next row
	 * 
	 * Delegated by the batch operation method
	 * 
	 * Calls the main import method, which then delegates off based on the import type
	 * 
	 * @param int $playhead The current working playhead
	 * 
	 * @return void
	*/
	protected function work($playhead){
		/* Load the file, or the mirror of the file */
		$source = !empty($this->sourceIsRemote) && !empty($this->source) && !empty($this->mirrorFile) ? $this->mirrorFile : $this->source;
		$fp = fopen($source, "r");
		
		if(!$fp){
			throw new \Exception('Invalid file');
		}
		
		$pointer = 0;
		
		while($pointer < $playhead){
			fgetcsv($fp);
			$pointer++;
		}
		
		$row = fgetcsv($fp, 65536);

		$this->import($pointer, $row);
	}

	/**
	 * On Complete method, delegated by batch operation
	 * 
	 * This is a great place to finish up a job before it is 'closed' from running further
	 * 
	 * @return void
	*/
	protected function onComplete(){
		if(!empty($this->delete_import) && !empty($this->import_id)){
			/* Was marked for deletion when completed, and has an import ID (WP Media Attachment ID) */
			$attachment = intval($this->import_id);
			if (!empty($attachment)) {
				try{
					wp_delete_attachment($attachment, true);
					$this->delete_complete = true;
				} catch (\Exception $ex){
					// Do nothing
				} catch (\Error $err){
					// Do nothing
				}

			}

			

		}	

		/* Has a mirror clone */
		if(!empty($this->mirrorFile)){
			if(file_exists($this->mirrorFile)){
				unlink($this->mirrorFile);
			}
		}
	}

	/**
	 * Primary import method, which delegates off to sub methods as needed, based on type
	 * 
	 * It will skip the first row and assume this is a header row, perhaps this is not ideal, but it's no different from the legacy importer
	 * 
	 * @param int $pointer The row index (starts at 0)
	 * @param array $row The row the worker is currently processing
	 * 
	 * @return void
	*/
	protected function import($pointer, $row){
		if(empty($pointer)){
			$this->log("Headers found, skipping row");
			return;
		}

		if(empty($row)){
			$this->log(__("Row is empty, skipping row", "wp-google-maps"), "error", $pointer);
			return;
		}

		$method = "import" . ucwords($this->type);
		if(method_exists($this, $method)){
			$this->{$method}($pointer, $row);
		} else {
			$this->log("Unknown import method '{$method}'");
		}
	} 

	/**
	 * Import a single marker, from a row
	 * 
	 * Handles remapping on the fly, deleting data, updating markers, geocoding etc
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importMarker($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(!empty($this->geocode)){
				/* Geocoding enabled */
				if(empty($fields->address) && empty($fields->lat) && empty($fields->lng)){
					/* We don't have any data to geocode from, we will need to skip this */
					$this->log(__('Geocode failed (Missing address, lat and lng)', 'wp-google-maps'), "error", $pointer);
					return;
				}

				if(!empty($fields->address) && (empty($fields->lat) || empty($fields->lng))){
					/* We have an address, missing lat or lng */
					$coords = $this->geocode($fields->address, $pointer);
					if(empty($coords)){
						/* It failed, errors will be logged by the geocoder */
						return;
					} 

					if(!empty($coords->lat) && !empty($coords->lng)){
						$fields->lat = $coords->lat;
						$fields->lng = $coords->lng;
					}
				} 

				if(empty($fields->address) && !empty($fields->lat) && !empty($fields->lng)){
					$address = $this->geocode(
						(object) array(
							"lat" => $fields->lat,
							"lng" => $fields->lng
						),
						$pointer
					);

					if(!empty($address)){
						$fields->address = $address;
					}
				}
			}

			if(empty($fields->lat) || empty($fields->lng)){
				if(!is_numeric($fields->lat) && empty($fields->lat)){
					$this->log(__("No latitude available", "wp-google-maps"), "error", $pointer);
					return;
				}

				if(!is_numeric($fields->lng) && empty($fields->lng)){
					$this->log(__("No longitude available", "wp-google-maps"), "error", $pointer);
					return;
				}

			}

			if(!is_numeric($fields->lat) || !is_numeric($fields->lng)){
				if(!is_numeric($fields->lat)){
					$this->log(__("Invalid latitude supplied", "wp-google-maps"), "error", $pointer);
				}

				if(!is_numeric($fields->lng)){
					$this->log(__("Invalid longitude supplied", "wp-google-maps"), "error", $pointer);
				}

				return;
			}

			/* We have a lat/lng by some means, but we don't have an address yet */
			if(empty($fields->address)){
				$fields->address = "{$fields->lat},{$fields->lng}";
			}

			if(!empty($fields->icon)){
				try{
					$iconBase = json_decode($fields->icon);
					if(!empty($iconBase) && is_object($iconBase)){
						foreach($iconBase as $iconKey => $iconValue){
							switch($iconKey){
								case 'url':
									$fields->icon = $iconValue;
									break;
								case 'hover_url':
									$fields->hover_icon = $iconValue;
									break; 
								case 'hover_retina':
									$fields->hover_retina = $iconValue;
									break;
							}
						}
						if(empty($iconBase->url)){
							unset($fields->icon);
						}							
					}
				} catch (\Exception $ex){
					// Couldn't decode
				} catch (\Error $err){
					// Couldn't decode
				}
			}

			/* String casting */
			if(function_exists('iconv') && function_exists('mb_detect_encoding') && function_exists('mb_detect_order')){
				foreach($fields as $fieldName => $fieldValue){
					if(!is_string($fieldValue)){
						continue;
					}

					try{
						$convertedValue = iconv(mb_detect_encoding($fieldValue, mb_detect_order(), true), "UTF-8", $fieldValue);
						
						$fields->{$fieldName} = $convertedValue;
					} catch (\Exception $ex){
						// Don't alter it then
					} catch (\Error $err){
						// Don't alter it then
					}
				}
			}

			if(!empty($fields->title)){
				$fields->title = html_entity_decode(urldecode($fields->title));
			}

			/* Categories */
			if(!empty($fields->category)){
				if(preg_match('/([A-Za-z])\w+/', $fields->category, $m)){
					/* Contains some words */
					$categories = array();

					$parts = explode(",", $fields->category);
					if(!empty($parts)){
						foreach($parts as $part){
							if(is_numeric($part)){
								$categories[] = intval($part);
							} else {
								$part = trim($part);
								
								/**
								 * Find a category or create it 
								*/
								$id = $this->findOrCreateCategory($part);
								if(!empty($id)){
									$categories[] = $id;
								}
							}
						}
					}

					if(!empty($categories)){
						$fields->category = implode(',', $categories);
					} else {
						$fields->category = "";
					}

				}
			}

			/* Custom Field handling */
			$customFields = array();
			foreach($fields as $fieldName => $fieldValue){
				if(strpos($fieldName, 'custom_field__') !== FALSE){
					/* We need to remap this over to a custom field */
					$customFields[$fieldName] = $fieldValue;
					unset($fields->{$fieldName});
				}
			}

			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$marker = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Marker::createInstance($fields->id);
							$marker = $existing;
						} catch (\Exception $ex){
							// Marker doesn't exist
						}
					}

					if(empty($marker)){
						$marker = \WPGMZA\Marker::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$marker->set($fields);

					if(!empty($customFields)){
						/* We have some custom fields that need assigning */
						foreach($customFields as $fieldTag => $fieldValue){
							if(!empty($fieldValue)){
								$tag = str_replace("custom_field__", "", $fieldTag);
								if(is_numeric($tag)){
									$fieldId = intval($tag);
									$marker->customFields->{$fieldId} = $fieldValue; 
								} else if (strpos($tag, 'create_') !== FALSE){
									/* New field creation, but it may be there already */
									$slug = str_replace("create_", "", $tag);
									$marker->customFields->{$slug} = $fieldValue;
								}
							}
						}
					}

				}
			}
		}
	}

	/**
	 * Import a single map from the sheet
	 * 
	 * Deletes other maps if required and remaps columns 
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importMap($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			if($this->mode === 'replace'){
				$this->deleteAllMaps();
			}

			$map = false;
			if(isset($fields->id)){
				try {
					$existing = \WPGMZA\Map::createInstance($fields->id);
					if(!empty($existing->document)){
						$map = $existing;
					}
				} catch (\Exception $ex){
					// Map doesn't exist
				}
			}
			
			if(empty($map)){
				$map = \WPGMZA\Map::createInstance();
			}

			if(isset($fields->id)){
				/* Read only */
				unset($fields->id);
			}

			$map->set($fields);
		}
	}

	/**
	 * Import a single circle from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importCircle($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(empty($fields->center_x) || empty($fields->center_y)){
				if(empty($fields->center_x)){
					$this->log(__("No latitude supplied for center (center_x)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->center_y)){
					$this->log(__("No longitude supplied for center (center_y)", "wp-google-maps"), "error", $pointer);
				}
				return;
			}

			$center  = (object) array("lat" => $fields->center_x, "lng" => $fields->center_y);
			if(!empty($center)){
				$fields->center = $center;

				unset($fields->center_x);
				unset($fields->center_y);
			}

			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$circle = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Circle::createInstance($fields->id);
							$circle = $existing;
						} catch (\Exception $ex){
							// Circle doesn't exist
						}
					}

					if(empty($circle)){
						$circle = \WPGMZA\Circle::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$circle->set($fields);
				}
			}
		}	
	}

	/** 
	 * Import a single rectangle from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importRectangle($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(empty($fields->corner_ax) || empty($fields->corner_ay) || empty($fields->corner_bx) || empty($fields->corner_by)){
				if(empty($fields->corner_ax)){
					$this->log(__("No latitude supplied for corner A (corner_ax)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->corner_ay)){
					$this->log(__("No longitude supplied for corner A (corner_ay)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->corner_bx)){
					$this->log(__("No latitude supplied for corner B (corner_bx)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->corner_by)){
					$this->log(__("No longitude supplied for corner B (corner_by)", "wp-google-maps"), "error", $pointer);
				}
				return;
			}

			$cornerA  = (object) array("lat" => $fields->corner_ax, "lng" => $fields->corner_ay);
			if(!empty($cornerA)){
				$fields->cornerA = $cornerA;

				unset($fields->corner_ax);
				unset($fields->corner_ay);
			}

			$cornerB  = (object) array("lat" => $fields->corner_bx, "lng" => $fields->corner_by);
			if(!empty($cornerB)){
				$fields->cornerB = $cornerB;

				unset($fields->corner_bx);
				unset($fields->corner_by);
			}

			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$rectangle = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Rectangle::createInstance($fields->id);
							$rectangle = $existing;
						} catch (\Exception $ex){
							// Rectangle doesn't exist
						}
					}

					if(empty($rectangle)){
						$rectangle = \WPGMZA\Rectangle::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$rectangle->set($fields);
				}
			}
		}
	}

	/** 
	 * Import a single polygon from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importPolygon($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(empty($fields->polydata)){
				$this->log(__("Missing polygon data", "wp-google-maps"), "error", $pointer);
				return;
			}

			
			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$polygon = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Polygon::createInstance($fields->id);
							$polygon = $existing;
						} catch (\Exception $ex){
							// Polygon doesn't exist
						}
					}

					if(empty($polygon)){
						$polygon = \WPGMZA\Polygon::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$polygon->set($fields);
				}
			}
		}
	}

	/** 
	 * Import a single polyline from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importPolyline($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(empty($fields->polydata)){
				$this->log(__("Missing polyline data", "wp-google-maps"), "error", $pointer);
				return;
			}

			
			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$polyline = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Polyline::createInstance($fields->id);
							$polyline = $existing;
						} catch (\Exception $ex){
							// Polyline doesn't exist
						}
					}

					if(empty($polyline)){
						$polyline = \WPGMZA\Polyline::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$polyline->set($fields);
				}
			}
		}
	}

	/** 
	 * Import a single dataset (heatmap) from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importDataset($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$heatmap = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Heatmap::createInstance($fields->id);
							$heatmap = $existing;
						} catch (\Exception $ex){
							// Pointlabel doesn't exist
						}
					}

					if(empty($heatmap)){
						$heatmap = \WPGMZA\Heatmap::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$heatmap->set($fields);
				}
			}
		}
	}

	/** 
	 * Import a single point label from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importPointlabel($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(empty($fields->center_x) || empty($fields->center_y)){
				if(empty($fields->center_x)){
					$this->log(__("No latitude supplied for center (center_x)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->center_y)){
					$this->log(__("No longitude supplied for center (center_y)", "wp-google-maps"), "error", $pointer);
				}
				return;
			}

			$center  = (object) array("lat" => $fields->center_x, "lng" => $fields->center_y);
			if(!empty($center)){
				$fields->center = $center;

				unset($fields->center_x);
				unset($fields->center_y);
			}

			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$pointlabel = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Pointlabel::createInstance($fields->id);
							$pointlabel = $existing;
						} catch (\Exception $ex){
							// Pointlabel doesn't exist
						}
					}

					if(empty($pointlabel)){
						$pointlabel = \WPGMZA\Pointlabel::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$pointlabel->set($fields);
				}
			}
		}
	}

	/** 
	 * Import a single image overlay from a sheet
	 * 
	 * @param int $pointer The raw (0 index) row number
	 * @param array $row The row data
	 * 
	 * @return void
	*/
	protected function importImageoverlay($pointer, $row){
		$fields = $this->prepareFields($row);
		if(!empty($fields)){
			$maps = $this->prepareMapList($fields);

			if(empty($fields->corner_ax) || empty($fields->corner_ay) || empty($fields->corner_bx) || empty($fields->corner_by)){
				if(empty($fields->corner_ax)){
					$this->log(__("No latitude supplied for corner A (corner_ax)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->corner_ay)){
					$this->log(__("No longitude supplied for corner A (corner_ay)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->corner_bx)){
					$this->log(__("No latitude supplied for corner B (corner_bx)", "wp-google-maps"), "error", $pointer);
				}

				if(empty($fields->corner_by)){
					$this->log(__("No longitude supplied for corner B (corner_by)", "wp-google-maps"), "error", $pointer);
				}
				return;
			}

			$cornerA  = (object) array("lat" => $fields->corner_ax, "lng" => $fields->corner_ay);
			if(!empty($cornerA)){
				$fields->cornerA = $cornerA;

				unset($fields->corner_ax);
				unset($fields->corner_ay);
			}

			$cornerB  = (object) array("lat" => $fields->corner_bx, "lng" => $fields->corner_by);
			if(!empty($cornerB)){
				$fields->cornerB = $cornerB;

				unset($fields->corner_bx);
				unset($fields->corner_by);
			}

			if(!empty($maps)){
				foreach($maps as $mapId){
					if($this->mode === 'replace'){
						$this->truncateMap($mapId);
					}

					$imageoverlay = false;
					if(isset($fields->id)){
						try {
							$existing = \WPGMZA\Imageoverlay::createInstance($fields->id);
							$imageoverlay = $existing;
						} catch (\Exception $ex){
							// Image overlay doesn't exist
						}
					}

					if(empty($imageoverlay)){
						$imageoverlay = \WPGMZA\Imageoverlay::createInstance();
					}

					$fields->map_id = $mapId;

					if(isset($fields->id)){
						/* Read only */
						unset($fields->id);
					}

					$imageoverlay->set($fields);
				}
			}
		}
	}

	/**
	 * Truncate data based on the import mode and a specific map 
	 * 
	 * @param int $mapId The map ID
	 * 
	 * @return void
	*/
	private function truncateMap($mapId){
		global $wpdb, $WPGMZA_TABLE_NAME_MARKERS, $WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES,
				$WPGMZA_TABLE_NAME_POLYGONS, $WPGMZA_TABLE_NAME_POLYLINES, 
				$WPGMZA_TABLE_NAME_CIRCLES, $WPGMZA_TABLE_NAME_RECTANGLES, 
				$WPGMZA_TABLE_NAME_HEATMAPS, $WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS,
				$WPGMZA_TABLE_NAME_POINT_LABELS, $WPGMZA_TABLE_NAME_IMAGE_OVERLAYS;

		$mapId = intval($mapId);
		if(!empty($mapId)){
			$truncateComplete = "truncate_map_{$mapId}";
			if(empty($this->{$truncateComplete})){
				/* We haven't truncated this map yet */
				switch($this->type){
					case 'marker':
						$multiQuery = "DELETE a, b, c FROM `$WPGMZA_TABLE_NAME_MARKERS` a 
										LEFT JOIN `$WPGMZA_TABLE_NAME_MARKERS_HAS_CUSTOM_FIELDS` b 
											ON b.object_id = a.id 
										LEFT JOIN `$WPGMZA_TABLE_NAME_MARKERS_HAS_CATEGORIES` c 
											ON c.marker_id = a.id 
										WHERE a.map_id = $mapId";
						$wpdb->query($multiQuery);
						break;
					case 'circle':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_CIRCLES` WHERE `map_id` = $mapId" );
						break;
					case 'polygon':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_POLYGONS` WHERE `map_id` = $mapId" );
						break;
					case 'polyline':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_POLYLINES` WHERE `map_id` = $mapId" );
						break;
					case 'rectangle':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_RECTANGLES` WHERE `map_id` = $mapId" );
						break;
					case 'dataset':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_HEATMAPS` WHERE `map_id` = $mapId" );
						break;
					case 'pointlabel':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_POINT_LABELS` WHERE `map_id` = $mapId" );
						break;
					case 'imageoverlay':
						$wpdb->query( "DELETE FROM `$WPGMZA_TABLE_NAME_IMAGE_OVERLAYS` WHERE `map_id` = $mapId" );
						break;
				}

				$this->log("Truncate {$this->type} data for map '{$mapId}'");
				$this->{$truncateComplete} = true;
			} 
		}
	}

	/**
	 * Deletes maps permanently. This is not part of hybrid-truncate method as this removes all maps. 
	 * 
	 * At this time, it won't delete data for that map, but it might need to do that in the future, hence why it is separate
	 * 
	 * @return void
	*/
	private function deleteAllMaps(){
		global $wpdb, $WPGMZA_TABLE_NAME_MAPS;

		$deleteComplete = "deleted_all_maps";
		if(empty($this->{$deleteComplete})){
			$wpdb->query("DELETE FROM `$WPGMZA_TABLE_NAME_MAPS`");

			$this->log("Deleted all maps, usually as part of a replace map operation");
			$this->{$deleteComplete} = true;
		}
	}

	/**
	 * Creates a new map for the imported data
	 * 
	 * This would only occur if the user chose not to apply the import, or use existing ID's in the sheet
	 * 
	 * @return int 
	*/
	private function createContainerMap(){
		$name = !empty($this->source) ? basename($this->source) : __( 'New CSV Map Import', 'wp-google-maps' );
		if(!empty($this->sourceIsRemote) && !empty($this->source)){
			/* URL based import */
			try{
				$url = parse_url($this->source);

				if(!empty($url) && is_array($url)){
					$compiled = array();
					foreach($url as $key => $value){
						if($key === 'scheme' || $key === 'query' || !is_string($value)){
							continue;
						}



						if(strpos($value, "/") !== FALSE){
							$value = explode("/", $value);
							if(!empty($value)){
								foreach($value as $vI => $vV){
									if(strlen($vV) > 5){
										$compiled[] = $vV;
									}
								}
							}
						} else if(strpos($value, ".") !== FALSE){
							$value = explode(".", $value);
							if(!empty($value)){
								foreach($value as $vI => $vV){
									if(strlen($vV) > 3){
										$compiled[] = $vV;
									}
								}
							}
						} else {
							$compiled[] = $value;
						}

					}	

					if(!empty($compiled)){
						foreach($compiled as $key => $value){
							if(strlen($value) > 15){
								$value = substr($value, 0, 5) . "[...]" . substr($value, -5);
							}

							$compiled[$key] = $value;
						}

						$compiled = implode(" / ", $compiled);
						$name = ucwords($compiled);
					}
				}
			} catch (\Exception $ex){
				// Couldn't parse
			} catch (\Error $err){
				// Couldn't parse
			}
		}

		$map = \WPGMZA\Map::createInstance();
		$map->map_title = $name;

		return intval($map->id);
	}

	/**
	 * Find or create a category by it's name 
	 * 
	 * @param string $name The category name
	 * 
	 * @return int 
	*/
	private function findOrCreateCategory($name = false){
		global $wpdb, $WPGMZA_TABLE_NAME_CATEGORIES, $WPGMZA_TABLE_NAME_CATEGORY_MAPS;

		if(!empty($name)){
			$name = trim($name);
			$searchName = strtolower($name);
			$existing = $wpdb->get_col("SELECT `id` FROM `$WPGMZA_TABLE_NAME_CATEGORIES` WHERE `category_name` LIKE  '%$searchName%' LIMIT 1");
			if(!empty($existing) && is_array($existing)){
				$existing = array_pop($existing);

				$this->log("Category name '{$name}' matches ID '{$existing}'");
				
				return intval($existing);
			} else {
				/* Create it */
				$wpdb->query( 
					$wpdb->prepare(
                    	"INSERT INTO $WPGMZA_TABLE_NAME_CATEGORIES SET
                        	category_name = %s,
                        	active = %d,
                        	category_icon = %s,
							image = %s,
                        	retina = %d,
                        	parent = %d,
                        	priority = %d
                    	",
                    	sanitize_text_field(stripslashes($name)),
                    	0, "", "", 0, 0, 0
                	)
            	);
            
            	$id = intval($wpdb->insert_id);

				$wpdb->query( 
					$wpdb->prepare(
						"INSERT INTO $WPGMZA_TABLE_NAME_CATEGORY_MAPS SET
							cat_id = %d,
							map_id = %d
						",
						$id,
						0
					)
				);

				$this->log("Category name '{$name}' has no matches, created new category with ID '{$id}'");

            	return $id;
            }
		}
		return 0;
	}

	/**
	 * Prepare field map
	 * 
	 * This is a recurring bit of code run for all types, so we might as well segment it into a function
	 * 
	 * @param array $row The row data from the sheet
	 * 
	 * @return object
	*/
	private function prepareFields($row){
		if(!empty($this->headerRemap)){
			if(is_array($this->headerRemap) && is_array($row)){
				$fields = (object) array();

				foreach($this->headerRemap as $key => $name){
					if($name === 'ignore'){
						continue;
					}			

					if(isset($row[$key])){
						if(isset($fields->{$name}) && is_string($fields->{$name}) && is_string($row[$key])){
							/* These are strings, and can be combined */
							$fields->{$name} .= $row[$key];
						} else {
							/* Set it for the first time, or override it */
							$fields->{$name} = $row[$key];
						}
					}		
				}

				if(!empty($fields->map_id) && !empty($fields->id)){
					if(empty($this->keep_map_id) && $this->type !== 'map'){
						/* Row has a map ID and a dataset ID, but user hasn't opted in to using it, drop it here to prevent data being moved to a new import map */
						/* This means that even if the user selects 'create_update' mode, if they aren't retaining map ID's, at the least, it will be assumed as a new item */
						unset($fields->map_id);
						unset($fields->id);
					}
				}

				return $fields;
			}
		}
		return false;
	}

	/**
	 * Prepare the maps list for the dataset
	 * 
	 * This parses off the prepared fields, and returns an array of maps the data should be applied to 
	 * 
	 * @param object $fields
	 * 
	 * @return array
	*/
	private function prepareMapList($fields){
		$fields = is_object($fields) ? $fields : (object) array();

		$maps = array();
		if(!empty($this->keep_map_id) && !empty($fields->map_id)){
			/* The marker has a map ID attached */
			$maps[] = intval($fields->map_id);
		} else if (!empty($this->apply) && !empty($this->applys)){
			/* Being applied to a set list of maps */
			if(is_array($this->applys)){
				$maps = $this->applys;
			}
		} else {
			/* Should only be applied to a new map created for this import */
			if(!empty($this->import_map)){
				$maps[] = intval($this->import_map);
			} else {
				$mapId = $this->createContainerMap();
				$this->import_map = $mapId;
				$maps[] = $mapId;
			}
		}

		return $maps;
	}


}
