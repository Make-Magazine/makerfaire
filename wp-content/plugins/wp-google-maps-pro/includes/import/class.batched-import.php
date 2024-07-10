<?php

namespace WPGMZA;

abstract class BatchedImport extends BatchedOperation
{
	protected $data;
	
	public function __construct($id=-1)
	{
		BatchedOperation::__construct($id);
	}
	
	static protected function getTableName()
	{
		global $WPGMZA_TABLE_NAME_BATCHED_IMPORTS;
		return $WPGMZA_TABLE_NAME_BATCHED_IMPORTS;
	}
	
	protected function geocode($data, $pointer){
		global $wpgmza;

		$type = false;
		if(is_string($data)){
			$type = "address";
		} else if(is_object($data)){
			if(!empty($data->lat) && !empty($data->lng)){
				$type = 'latlng';
				$data = "{$data->lat},{$data->lng}";
			} 
		} 

		if(!empty($type)){
			$this->log("Attempting to geocode {$data}");

			if(empty($wpgmza->settings->wpgmza_google_maps_api_key)){
				$this->log("Geocode failed (No API Key)");
				return false;
			}

			$apikey = $wpgmza->settings->wpgmza_google_maps_api_key;

			if(!empty($wpgmza->settings->importer_google_maps_api_key)){
				/* Load specific key for importing */
				$apikey = $wpgmza->settings->importer_google_maps_api_key;
			}

			if(empty($data)){
				$this->log("Geocode failed (Location missing)", "error", $pointer);
				return false;
			}

			$args = array();
			$options = array();

			$endpoint = false;
			if(CloudAPI::isCloudKey($apikey)){
				/* Use cloud API */
				$endpoint = CloudAPI::URL . "/geocode";
				$options['headers'] = array(
					'X-WPGMZA-CLOUD-API-KEY' => $apikey
				);

				$args[($type === 'latlng' ? 'location' : $type)] = rawurldecode($data);
			} else {
				$endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

				$args['key'] = $apikey;
				$args[$type] = urlencode(rawurldecode($data));
			}

			if(!empty($endpoint)){
				$this->log("Geocode key in use: {$apikey}");

				$url = add_query_arg($args, $endpoint);
				$startTime = microtime(true);

				$ip = 'unknown';
				if(array_key_exists('SERVER_ADDR', $_SERVER)){
					$ip = $_SERVER['SERVER_ADDR'];
				} else if(array_key_exists('LOCAL_ADDR', $_SERVER)) {
					$ip = $_SERVER['LOCAL_ADDR'];
				} else if(array_key_exists('SERVER_NAME', $_SERVER)){
					$ip = gethostbyname($_SERVER['SERVER_NAME']);
				} 

				$response = wp_remote_get($url, $options);
				if(is_wp_error($response)){
					$error = $response->get_error_message();
					
					
					
					if(preg_match('/refer(r?)er/i', $error)){
						$error = sprintf(
							__("HTTP referrer restrictions on your API key forbid geocoding from this server. This can happen when your server is behind a proxy, or does not set the HTTP referrer header correctly. We recommend temporarily de-restricting your key, or generating a second key with an IP restriction to switch to temporarily. We detected this servers IP as %s.", 'wp-google-maps'),
							$ip
						);
					}
					
					$this->log("Geocode failed ({$error})", "error", $pointer);
					return false;
				} else {
					try {
						$response = wp_remote_retrieve_body($response);
						$response = json_decode($response);

						if(isset($response->status)){
							switch($response->status){
								case "OK":
									break;

								case "OVER_DAILY_LIMIT":
									$this->log("Geocode failed (Over daily query limit)", "error", $pointer);
									return false;
									break;

								case "OVER_QUERY_LIMIT":
									$this->log("Geodoce failed (Over query limit)", "error", $pointer);
									return false;
									break;
									
								case "REQUEST_DENIED":
									$logMessage = "Geocode failed (Request denied";
									if(!empty($response->error_message)){
										$logMessage .= " - " . trim($response->error_message);
									} else {
										$logMessage .= " - IP Restricted key may be required in the 'Alternative Import API Key' field found within the global settings (IP: {$ip})";
									}

									$logMessage .= ")";

									$this->log($logMessage, "error", $pointer);
									return false;
									break;
									
								case "INVALID_REQUEST":
									$this->log("Geocode failed (Invalid request)", "error", $pointer);
									return false;
									break;
									
								case "ZERO_RESULTS":
									$this->log("Geocode failed (No results found)", "error", $pointer);
									return false;
									break;
								
								default:
									$this->log("Unknown geocode response status", "error", $pointer);
									return false;
									break;
							}

							if(is_array($response) && !isset($response->results)){
								$response = (object)array(
									'results' => $response
								);
							}

							$result = false;
							switch ($type) {
								case 'address':
									if (isset($response->results[0]->geometry->location->lat, $response->results[0]->geometry->location->lng)){
										$result = (object) array( 
											"lat" => $response->results[0]->geometry->location->lat, 
											"lng" => $response->results[0]->geometry->location->lng
										);

										$this->log("Geocode successful ({$result->lat}, {$result->lng})");
									}
									break;

								case 'latlng':
								case 'location':
									if(isset($response->results[0]->formatted_address)){
										$result = (object) array(
											"address" => $response->results[0]->formatted_address
										);

										$this->log("Geocode successful ($result->address)");
									}
									break;
							}

							$endTime = microtime( true );
							$deltaTime = $endTime - $startTime;
							$minTimeBetween = 1000000 / 10;

							if ($deltaTime < $minTimeBetween) {
								$delay = $minTimeBetween - $deltaTime;
								usleep($delay);
							}

							$result->type = $type;
							return $result; 
						}

					} catch (\Exception $ex){
						return false;
					} catch (\Error $err){
						return false;
					}
				}
			}
		} else {
			$this->log("Geocode failed (Invalid type)", "error", $pointer);
			return false;
		}
	}
	
	protected function load()
	{

		if(!empty($this->sourceIsRemote) && !empty($this->source)){
			/* We have a source, but it's a remote source for sure */
			if(empty($this->mirrorFile)){
				/* We don't have a mirror file available */
				$this->createRemoteMirror();
			}
		}


		/*
		 * Seems like this check is not needed?
		 *
		 * Tests reveal that this works fine without the is_file checks. 
		 * 
		 * I suppose we hang oto it for now, but no major changes for me here (Dylan Auty)
		*/

		/*if(empty($this->sourceIsRemote) && !is_file($this->source)){
			$this->halt("{$this->source} is not a file");
			return;
		} else {
			$this->halt("BEGIN CONVERTING");
			return;
		}*/
	}
	
	protected function iterate()
	{
		$this->load();
		
		BatchedOperation::iterate();
	}
	
	public function start()
	{
		BatchedOperation::start();
	}

	protected function log($message, $type = false, $pointer = false){
		$type = !empty($type) ? strtolower(str_replace(" ", "_", $type)) : 'log';
		$pointer = !empty($pointer) ? intval($pointer) : false;

		/* Build an output array for more in-depth returns */
		$output = false;
		try {
			$output = maybe_unserialize($this->output);
	
			if(!is_object($output) && !is_array($output)){
				$output = false;
			}
		} catch (\Exception $ex){
			$output = false;
		} catch(\Error $err){
			$output = false;
		}

		if(empty($output)){
			$output = array();
		}

		if(empty($output[$type])){
			$output[$type] = array();
		}

		if(!empty($pointer)){
			$words = explode(" ", $message);
			$stackSlug = implode("_", array_slice($words, 0, 5));
			$stackSlug = preg_replace('/([^a-z_])/', '', strtolower($stackSlug));

			/* Group it together, by dynamic stack slug, and row */
			if(empty($output[$type][$stackSlug])){
				$output[$type][$stackSlug] = array(
					'message' => $message,
					'rows' => array()
				);
			}

			$output[$type][$stackSlug]['rows'][] = $pointer + 1;
		} else {
			/* Add it to a generic log group */
			if(empty($output[$type]['generic'])){
				$output[$type]['generic'] = array();
			}

			$output[$type]['generic'][] = $message;
		}


		$this->output = serialize($output);

		$type = strtoupper($type);

		$rawLog = "[{$type}] ";
		if(!empty($pointer)){
			$rawLog .= "[" . ($pointer + 1) . "] ";
		}
		$rawLog .= date('Y-m-d H:i:s') . " :- " . $message . "\r\n";

		file_put_contents(WPGMZA_PRO_DIR_PATH . 'includes/import-export/import.log', $rawLog, FILE_APPEND);
	}

	public function getLogs($type = false){
		$list = array();

		$output = false;
		try {
			$output = maybe_unserialize($this->output);
		
			if(!is_object($output) && !is_array($output)){
				$output = false;
			}
		} catch (\Exception $ex){
			$output = false;
		} catch(\Error $err){
			$output = false;
		}

		if(!empty($output) && is_array($output)){
			foreach($output as $logType => $logData){
				if(!empty($type) && $logType !== $type){
					continue;
				}

				if(is_string($logData)){
					$list[] = $logData;
				} else if (is_array($logData)){
					foreach($logData as $subType => $subData){
						if(is_string($subData)){
							$list[] = $subData;
						} else if (is_array($subData)){
							/* This is a shared log, which applies to more than one element */
							if(!empty($subData['message'])){
								$message = $subData['message'];
								if(!empty($subData['rows']) && is_array($subData['rows'])){
									$message .= " (" . __("Rows", "wp-google-maps") . ": " . implode(", ", $subData['rows']) . ")";
								}

								$list[] = $message;
							} 
						}
					}
				}
			}
		}

		return !empty($list) ? $list : false;

	}

	public function createRemoteMirror(){
		$uploadDir = wp_upload_dir();
		if (!empty($uploadDir['basedir'])){
            $uploadDir = $uploadDir['basedir'];

			$path = implode("/", 
    			array($uploadDir,'wp-google-maps')
    		);

			if(!file_exists($path)){
				wp_mkdir_p($path);
			}

			$path = implode("/",
				array($path, 'imports')
			);

			if(!file_exists($path)){
				wp_mkdir_p($path);
			}

			$extension = strtolower(str_replace("WPGMZA\\BatchedImport\\", "", $this->class));
			$filename = "batched_import_{$this->id}.{$extension}";

			$path = $path . '/' . $filename;

			$contents = wp_remote_get($this->source);
			if(!is_wp_error($contents)){
				$contents = wp_remote_retrieve_body($contents);
				file_put_contents($path, $contents);
				$this->mirrorFile = $path;

				$this->log("Created mirror file: " . $path);
			} else {
				$this->log("Failed to create mirror file: " . $contents->get_error_message());
			}
		}
	}
}