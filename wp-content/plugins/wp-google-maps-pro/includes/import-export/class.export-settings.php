<?php
namespace WPGMZA;
 
/**
 * Settings Exporter
 *
 * @since 9.0.0
 * 
*/
class ExportSettings extends Export{
	/**
	 * Constructor
	*/
	public function __construct($types) {
		$this->types = !empty($types) && is_array($types) ? $types : false;
	}

	/**
	 * Generates and dowload the JSON file
	 *
	 * @return void
	*/
	public function download() {
        global $wpgmza_pro_version;

		$siteName = sanitize_key(get_bloginfo('name'));
		if (!empty($siteName)) {
			$siteName .= '.';
		}

		$filename = $siteName . current_time('Y-m-d') . '.wpgmza-settings';

		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . $filename );
		header('Content-Type: application/json; charset=' . get_option('blog_charset'), true);
		
		$prettyPrint = version_compare(PHP_VERSION, '5.4', '>=') && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;

		try{
			$data = $this->getData();
			echo wp_json_encode($data, $prettyPrint);
		} catch (\Exception $ex){

		} catch (\Error $err){

		}
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
		if(!empty($this->types)){
			$data = array(
				'creator'      => 'WPGoogleMapsPro',
				'json_version' => ExportJSON::JSON_VERSION,
				'type' => 'configuration',
			);

			foreach($this->types as $type){
				$method = "get" . ucwords($type) . 'Config';

				if(method_exists($this, $method)){
					$subData = $this->{$method}();
					if(!empty($subData)){
						// Only set if the data is not empty
						$data[$type] = $subData;
					}
				}
			}

			return $data;
		} else {
			throw new \Error("No export types selected");
		}	
	}

	/**
	 * Get global options from the database, then turn it into JSON compatible data
	 * 
	 * @return object
	*/
	public function getGlobalConfig(){
		$data = get_option('wpgmza_global_settings', false);
		if(!empty($data)){
			try{
				$data = json_decode($data);

				if(!in_array('keys', $this->types)){
					/* Keys are excluded */
					$keys = array('google_maps_api_key', 'wpgmza_google_maps_api_key', 'importer_google_maps_api_key', 'open_layers_api_key', 'open_route_service_key');
					foreach($keys as $key){
						if(isset($data->{$key})){
							unset($data->{$key});
						}
					}
				}

				return array(
					'wpgmza_global_settings' => $data
				);
			} catch (\Exception $ex){

			} catch (\Error $err){

			}
		}
		return false;
	}

	/**
	 * Get global options from the database, then turn it into JSON compatible data
	 * 
	 * @return object
	*/
	public function getStylingConfig(){
		$data = get_option('wpgmza_component_styling');
		if(!empty($data)){
			try{
				$data = json_decode($data);
				return array(
					'wpgmza_component_styling' => $data
				);
			} catch (\Exception $ex){

			} catch (\Error $err){

			}
		}
		return false;
	}

	/**
	 * Get global options from the database, then turn it into JSON compatible data
	 * 
	 * @return object
	*/
	public function getKeysConfig(){
		$data = get_option('wpgmza_google_maps_api_key', false);

		if(!empty($data)){
			return array(
				'wpgmza_google_maps_api_key' => $data
			);
		}
		return false;
	}
}