<?php

namespace WPGMZA;

if(!defined('ABSPATH'))
	return;

class GoldRestAPI
{
	public function __construct()
	{
		add_action('wpgmza_register_rest_api_routes', array($this, 'onRegisterRestAPIRoutes'));
	}
	
	public function onRegisterRestAPIRoutes()
	{
		$this->registerRoutes();
	}
	
	protected function registerRoutes()
	{
		global $wpgmza;
		
		$wpgmza->restAPI->registerRoute('/ratings/', array(
			'methods' => array('POST'),
			'callback' => array($this, 'ratings')
		));
	}
	
	public function ratings($request)
	{
		global $wpgmza;
		
		try{
			if(!isset($_POST['type']))
				throw new \Exception('No type specified');
			
			if($_POST['type'] != 'marker')
				throw new \Exception('Currently, the only supported type is "marker"');
			
			if(empty($_POST['id']))
				throw new \Exception('No ID specified');
			
			if(empty($_POST['amount']))
				throw new \Exception('No amount specified');
			
			if(!is_numeric($_POST['id']) || $_POST['id'] < 0)
				throw new \Exception('Invalid ID');
			
			$marker = Marker::createInstance($_POST['id']);
			$map = Map::createInstance($marker->map_id);
			
			if(empty($map->enable_marker_ratings))
				throw new \Exception('Marker ratings are not enabled for this markers map');
			
			$marker->rating->update($_POST['amount'], $_POST['userGuid']);
			
		}catch(\Exception $e) {
			return array(
				'success' => 0,
				'message' => $e->getMessage()
			);
		}
		
		return $marker->rating;
	}
}