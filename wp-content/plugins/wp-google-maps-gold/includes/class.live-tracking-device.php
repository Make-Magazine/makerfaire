<?php

namespace WPGMZA;

// define('WPGMZA_LOG_TRACKING_DEVICES', true);

class LiveTrackingDevice extends Crud
{
	public function __construct($id_or_fields=-1)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
		
		$isNewDevice = false;
		
		if(is_string($id_or_fields)){
			$id_or_fields = array('deviceID' => $id_or_fields);
		}
		
		if(is_object($id_or_fields) || is_array($id_or_fields)) {
			$arr = (array)$id_or_fields;
			
			if(isset($arr['deviceID'])) {
				$stmt = $wpdb->prepare("SELECT id FROM $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES WHERE deviceID = '%s'", array($arr['deviceID']));
				$id = $wpdb->get_var($stmt);

				if($id)
					$id_or_fields = (int)$id;
				else
					$isNewDevice = true;
			}
		}
		
		Crud::__construct($WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES, $id_or_fields);
		
		if($isNewDevice)
		{
			$r = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
			$g = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
			$b = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
			
			$this->polylineWeight = 2;
			$this->polylineColor = "#$r$g$b";
			
			$this->marker_id = -1;
			$this->polyline_id = -1;
		}
	}
	
	public function touch()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
		
		$stmt = $wpdb->prepare("UPDATE $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES SET last_contact = NOW() WHERE id=%d", array($this->id));
		$wpdb->query($stmt);
	}
	
	protected function log($str)
	{
		if(!defined('WPGMZA_LOG_TRACKING_DEVICES') || !WPGMZA_LOG_TRACKING_DEVICES)
			return;
		
		$file = ABSPATH . 'wpgmza-live-tracking.log';
		file_put_contents($file, date('Y-m-d H:i:s') . " :- " . $str . "\r\n", FILE_APPEND);
	}
	
	protected function get_arbitrary_data_column_name()
	{
		return 'other_data';
	}
	
	public function updateFromApp($data)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
		global $WPGMZA_TABLE_NAME_POLYLINES;
		
		$map_id = (empty($data->map_id) ? 1 : $data->map_id);
		
		$coords = $data->location->coords;
		
		try{ 
			$marker = Marker::createInstance((int)$this->marker_id);
		}catch(\Exception $ex){
			/* We must assume that the marker has been removed manually at some point, let's spin up a new one */
			$marker = Marker::createInstance(-1);
		}

		$prevPosition = $marker->getPosition();
		
		$marker->lat = $coords->latitude;
		$marker->lng = $coords->longitude;
		$marker->approved = 1;
		$marker->title = $this->name;
		$marker->map_id = $map_id;
		
		$this->marker_id = $marker->id;

		if(!empty($data->name)){
			$this->name = $data->name;
		}
		
		$currPosition = $marker->getPosition();
		
		$deltaPosition = Distance::between($prevPosition, $currPosition);
		$deltaMeters = $deltaPosition / 1000;
		
		$stmt = $wpdb->prepare("SELECT TIME_TO_SEC(TIMEDIFF(NOW(), last_contact)) FROM $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES WHERE id=%d", array($this->id));
		$deltaTime = $wpdb->get_var($stmt);
		
		$distanceThreshold = 10; // About 6 mi
		$timeThreshold = 60 * 60;
		
		$this->log("Updated position to {lat: {$coords->latitude}, lng: {$coords->longitude}}. Delta position was $deltaMeters m. Delta time was $deltaTime s.");
		
		if($this->drawPolylines == 1)
		{
			$overDistanceThreshold 	= $deltaPosition > $distanceThreshold;
			$overTimeThreshold 		= $deltaTime > $timeThreshold;
			$noCurrentPolyline 		= empty($this->polyline_id);
			$polylineDeleted 		= $wpdb->get_var("SELECT COUNT(*) FROM $WPGMZA_TABLE_NAME_POLYLINES WHERE id=" . (int)$this->polyline_id) < 1;
			
			$createNewPolyline = (
				$overDistanceThreshold ||
				$overTimeThreshold ||
				$noCurrentPolyline ||
				$polylineDeleted
			);
			
			if($createNewPolyline)
			{
				$status = 'Creating new polyline :- ';
				$reasons = array();
				
				if($overDistanceThreshold)
					$reasons[] = 'Over distance threshold';
				if($overTimeThreshold)
					$reasons[] = 'Over time threshold';
				if($noCurrentPolyline)
					$reasons[] = 'No polyline exists';
				if($polylineDeleted)
					$reasons[] = 'Polyline deleted';
				
				$this->log($status . implode(', ', $reasons));
			}
			else
				$this->log('Continuing old polyline');
			
			if($createNewPolyline)
			{
				$stmt = $wpdb->prepare("INSERT INTO $WPGMZA_TABLE_NAME_POLYLINES 
					(
						map_id, 
						polydata, 
						linecolor, 
						linethickness, 
						opacity, 
						polyname
					)
					VALUES
					(
						%d,
						'(%f, %f)',
						%s,
						%f,
						0.8,
						%s
					)
				", array(
					$map_id,
					$coords->latitude,
					$coords->longitude,
					preg_replace('/^#/', '', $this->polylineColor),
					$this->polylineWeight,
					$this->name
				));
			}
			else
			{
				$stmt = $wpdb->prepare("UPDATE $WPGMZA_TABLE_NAME_POLYLINES SET
					map_id = %d,
					polydata = CONCAT(polydata, %s),
					linecolor = %s,
					linethickness = %f,
					polyname = %s
					WHERE id = %d
				", array(
					$map_id,
					",({$coords->latitude}, {$coords->longitude})",
					preg_replace('/^#/', '', $this->polylineColor),
					$this->polylineWeight,
					$this->name,
					$this->polyline_id
				));
			}
			
			$wpdb->query($stmt);
			
			if($createNewPolyline)
				$this->polyline_id = $wpdb->insert_id;
		}
		
		$this->touch();
	}

	public function setVisibility($visibility){
		$id = intval($this->marker_id);
		if(!empty($id) && $id > 0){
			$marker = Marker::createInstance($id);
			$marker->approved = !empty($visibility) ? 1 : 0;
		}
	}
}
