<?php

namespace WPGMZA;

class ProRestAPI extends RestAPI
{
	public function __construct()
	{
		RestAPI::__construct();
	}
	
	protected function registerRoutes()
	{
		global $wpgmza;
		
		if(method_exists(get_parent_class($this), 'registerRoutes'))
			RestAPI::registerRoutes(); // Failsafe for basic < 7.11.40 w/Pro >= 7.11.47 in which this method doesn't exist on the parent class
		
		if(!method_exists($this, 'registerRoute'))
			return; // Legacy basic failsafe
		
		$this->registerRoute('/marker-listing/', array(
			'methods'					=> array('GET'),
			'callback' 					=> array($this, 'markerListing'),
			'useCompressedPathVariable' => true,
		));
		
		$this->registerRoute('/marker-listing/', array(
			'methods'					=> array('POST'),
			'callback' 					=> array($this, 'markerListing')
		));
		
		$this->registerRoute('/categories/', array(
			'methods'					=> array('GET'),
			'callback'					=> array($this, 'categories')
		));
		
		$this->registerRoute('/maps/', array(
			'methods'					=> array('DELETE', 'POST'),
			'callback'					=> array($this, 'maps'),
			'permission_callback'		=> array($wpgmza, 'isUserAllowedToEdit')
		));
		
		$this->registerRoute('/heatmaps(\/\d+)?/', array(
			'methods'					=> array('GET'),
			'callback'					=> array($this, 'features'),
			'permission_callback'		=> '__return_true'
		));
		
		$this->registerRoute('/heatmaps(\/\d+)?/', array(
			'methods'					=> array('DELETE', 'POST'),
			'callback'					=> array($this, 'features'),
			'permission_callback'		=> array($wpgmza, 'isUserAllowedToEdit')
		));

		$this->registerRoute('/imageoverlays(\/\d+)?/', array(
			'methods'					=> array('GET'),
			'callback'					=> array($this, 'features'),
			'permission_callback'		=> '__return_true'
		));

		$this->registerRoute('/imageoverlays(\/\d+)?/', array(
			'methods'					=> array('DELETE', 'POST'),
			'callback'					=> array($this, 'features'),
			'permission_callback'		=> array($wpgmza, 'isUserAllowedToEdit')
		));

		$this->registerRoute('/integration-tools/', array(
			'methods'					=> array('POST'),
			'callback'					=> array($this, 'integrationTools'),
			'permission_callback'		=> array($wpgmza, 'isUserAllowedToEdit')
		));

		$this->registerRoute('/performance-tools/', array(
			'methods'					=> array('POST'),
			'callback'					=> array($this, 'performanceTools'),
			'permission_callback'		=> array($wpgmza, 'isUserAllowedToEdit')
		));
	}
	
	protected function getFeatureTables()
	{
		global $wpdb;
		
		$tables = RestAPI::getFeatureTables();
		
		$tables['heatmaps'] = "{$wpdb->prefix}wpgmza_datasets";
		$tables['imageoverlays'] = "{$wpdb->prefix}wpgmza_image_overlays";
		
		return $tables;
	}
	
	public function maps($request)
	{
		$this->checkForDeleteSimulation();
		
		switch($_SERVER['REQUEST_METHOD']){
			case 'DELETE':
			
				// Workaround for PHP not populating $_REQUEST
				$request = array();
				$body = file_get_contents('php://input');
				parse_str($body, $request);

				if(isset($request['id'])){
					$map = Map::createInstance($request['id']);
					$map->trash();
				} else if (isset($request['ids'])) {
					foreach ($request['ids'] as $key => $map_id) {
						$map = Map::createInstance($map_id);
						$map->trash();
					}
				}

				return array(
					'success' => true
				);

				break;

			case 'POST':
			
				if(isset($_POST['action'])){
					switch($_POST['action']){
						case "duplicate":
						
							$request = array();
							$body = file_get_contents('php://input');
							parse_str($body, $request);

							if(isset($request['id'])) {
								$map = Map::createInstance($request['id']);
								$map->duplicate();
							} 

							return array(
								'success' => true
							);
							
							break;
							
						default:
							throw new \Exception('Unknown action');
							break;
					}
				} else {
					$map = Map::createInstance($_POST);
					return $map;
				}

				break;
		}	

		return RestAPI::maps($request);
	}

	public function markers($request){
		switch($_SERVER['REQUEST_METHOD']){
			case 'POST':
				if(isset($_POST['action'])){
					switch($_POST['action']){
						case "duplicate":
							$request = array();
							$body = file_get_contents('php://input');
							parse_str($body, $request);

							if(isset($request['id'])){
								$marker = Marker::createInstance($request['id']);
								$marker->duplicate();
							}

							return array(
								'success' => true
							);
							
							break;
						case 'move_map':
							$request = array();
							$body = file_get_contents('php://input');
							parse_str($body, $request);

							if(!empty($request['id']) && !empty($request['map_id'])){
								$marker = Marker::createInstance($request['id']);
								$marker->map_id = intval($request['map_id']);
							}

							return array(
								'success' => true
							);

							break;
						case 'bulk_edit':
							$request = array();
							$body = file_get_contents('php://input');
							parse_str($body, $request);

							if(isset($request['ids'])) {
								foreach ($request['ids'] as $key => $marker_id) {
									$marker = Marker::createInstance($marker_id);

									foreach($request as $key => $value){
										if($key == 'id' || $key == 'ids'){
											continue;
										}
										
										$marker->{$key} = stripslashes($value);
									}
								}
							}

							return array(
								'success' => true
							);
							break;
					}
				}
				break;
		}
		return RestAPI::markers($request);
	}
	
	public function markerListing($request)
	{
		$request = $this->getRequestParameters();
		$map_id = $request['map_id'];
		
		if(RestAPI::isRequestURIUsingCompressedPathVariable())
			$class = '\\' . $request['phpClass'];
		else
			$class = '\\' . stripslashes( $request['phpClass'] );
		
		if(isset($request['overrideMarkerIDs']) && is_string($request['overrideMarkerIDs']))
			$request['overrideMarkerIDs'] = explode(',', $request['overrideMarkerIDs']);

		$instance = $class::createInstance($map_id);
		
		if(!($instance instanceof MarkerListing))
			return WP_Error('wpgmza_invalid_datatable_class', 'Specified PHP class must extend WPGMZA\\MarkerListing', array('status' => 403));
		
		$response = $instance->getAjaxResponse($request);
		
		return $response;
	}
	
	public function categories($request)
	{
		$params = $this->getRequestParameters();
		$map = null;
		
		if(!empty($params['filter']))
		{
			if(is_object($params['filter']))
				$filteringParameters = (array)$params['filter'];
			else if(is_array($params['filter']))
				$filteringParameters = $params['filter'];
			else if(is_string($params['filter']))
				$filteringParameters = json_decode( stripslashes($params['filter']) );
			else
				throw new \Exception("Failed to interpret filtering parameters");
			
			if($filteringParameters['map_id'])
				$map = \WPGMZA\Map::createInstance( $filteringParameters['map_id'] );
		}
		
		$categoryTree = CategoryTree::createInstance($map);
		return $categoryTree;
	}

	public function integrationTools($request){
		$response = array(
			'success' => false,
			'message' => false
		);

		$params = $this->getRequestParameters();
		if(!empty($params['type'])){
			$type = $params['type'];
			switch($type){
				case 'test_collation':
					$mismatch = Integration\MarkerSource::checkTableCollationMismatch();
					if(!empty($mismatch)){
						// Leave success false, but set a message
						$response['message'] = __("Mismatched table collations have been found, this may result in integration sources (ACF/Woo) not functioning properly.", "wp-google-maps");
					} else {
						$response['message'] = __("Tables appear to be set up correctly for integration sources, no additional action required", "wp-google-maps");
						$response['success'] = true;
					}
					break;
				case 'resolve_collation':
					$resolve = Integration\MarkerSource::resolveTableCollationMismatch();
					if($resolve){
						$response['message'] = __("Table collations have been corrected and integrations (ACF/Woo) should function normally", "wp-google-maps");
						$response['success'] = true;
					} else {
						$response['message'] = __("Table collations could not be corrected, or are already aligned", "wp-google-maps");
					}
					break;
				default:
					$response['message'] = __("Unkown integration tool, we could not complete your request", "wp-google-maps");
					$type = 'unknown';
					break;
			}

			$response['type'] = $type;
		}

		return $response;
	}

	public function performanceTools($request){
		global $wpgmza;

		$response = array(
			'success' => false,
			'message' => false
		);

		$params = $this->getRequestParameters();
		if(!empty($params['type'])){
			$type = $params['type'];
			switch($type){
				case 'add_db_indexing':
					$indexed = $wpgmza->proDatabase->addPerformanceIndexes();
					if(!empty($indexed)){
						$response['message'] = __("Performance indexes have been added!", "wp-google-maps");
						$response['success'] = true;
					} else {
						$response['message'] = __("We could not alter the database indexes", "wp-google-maps");
					}
					break;
				case 'remove_db_indexing':
					$indexed = $wpgmza->proDatabase->removePerformanceIndexes();
					if(!empty($indexed)){
						$response['message'] = __("Performance indexes have been remove!", "wp-google-maps");
						$response['success'] = true;
					} else {
						$response['message'] = __("We could not alter the database indexes", "wp-google-maps");
					}
					break;
				default:
					$response['message'] = __("Unkown performance tool, we could not complete your request", "wp-google-maps");
					$type = 'unknown';
					break;
			}

			$response['type'] = $type;
		}

		return $response;
	}
}

add_filter('wpgmza_create_WPGMZA\\RestAPI', function() {
	
	return new ProRestAPI();
	
}, 10, 0);
