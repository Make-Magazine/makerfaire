<?php

namespace WPGMZA\Integration;

require_once(plugin_dir_path(__FILE__) . 'class.marker-source.php');

/**
 * This is a new class which adds official support for WooCommerce Products as a marker source
 * 
 * It is important to note that this is similar to ACF, but adds widgets to the product page
 * 
 * This does not control other parts of Woo integration, but is specific native Woo Product integration
 * 
 * This is to say things like the checkout map are handled in a totally separate integration. This is a marker source integration by contrast
 * 
 * @since 9.0.0
 * @requires WooCommerce
*/

class WooCommerceProducts extends MarkerSource {
	
	/**
	 * Constructor 
	*/
	public function __construct(){
		MarkerSource::__construct();

		/* Product Editor Meta Controller Hooks
		 * 
		 * These hooks are all wrapped in a condition so that they can be fully disabled if needed by a customer
		 *
		 * This is to say these options won't be enabled unless the user opts into the WooCommerce specific settings in the global settings area
		 *
		*/

		add_action('add_meta_boxes', array($this, 'registerLocationEditorMetabox'));
		add_action('save_post', array($this, 'saveLocationEditorMetabox'));
	}

	/**
	 * Get the setting name related to this integration
	 *
	 * @return string
	*/
	public function getSettingName(){
		return "enable_woo_product_integration";
	}

	/**
	 * Check if the integration source is eabled for the map
	 *
	 * @param Map $map The maps being rendered
	 * 
	 * @return bool
	*/
	public function isEnabled($map){
		if(empty($map)){
			return false;
		}

		$option = $this->getSettingName();
		
		return !empty($map->{$option});
	}

	/**
	 * Integration option
	 * 
	 * @param DomDocument $document The document being attached to
	 * @param string $name The name of the control
	 * @param string $type The type of input being created
	 * @param string $class
	 * @param string $label
	 * 
	 * @return DomElement
	*/
	protected function getIntegrationControl($document, $name, $type = 'radio', $class = null, $label = null){
		global $wpgmza;

		$label = MarkerSource::getIntegrationControl(
			$document,
			$name,
			$type,
			'WPGMZA\Integration\WooCommerceProducts',
			__('WooCommerce Products', 'wp-google-maps')
		);
		
		if(!class_exists('WooCommerce')){
			/* WooCommerce Class missing, plugin not installed */
			$a = $document->createElement('a');
			$a->setAttribute('target', '_BLANK');
			$a->setAttribute('href', 'https://wordpress.org/plugins/woocommerce/');
			$a->appendText(__('WooCommerce','wp-google-maps'));
			$label->appendText(" (");
			$label->appendChild($a);
			$label->appendText(__(' missing)', 'wp-google-maps'));
			$label->querySelector('input')->setAttribute('disabled', 'disabled');
			$label->querySelector('input')->setAttribute('readonly', 'readonly');
			
		} else if (empty($wpgmza->settings->woo_product_location_editor_enabled)){
			/* User has not enabled the product editor, which renders the integration useless */

			$a = $document->createElement('a');
			$a->setAttribute('target', '_BLANK');
			$a->setAttribute('href', admin_url('admin.php?page=wp-google-maps-menu-settings#woocommerce'));
			$a->appendText(__('Product Location Editor','wp-google-maps'));

			$label->appendText(" (" . __("Enable", "wp-google-maps") . " ");
			$label->appendChild($a);
			$label->appendText(")");
			$label->querySelector('input')->setAttribute('disabled', 'disabled');
			$label->querySelector('input')->setAttribute('readonly', 'readonly');
		}
		
		return $label;
	}

	/**
	 * Get map integration options
	 * 
	 * @param DomDocument $document
	 * @param Map $map
	 * 
	 * @return $document
	*/
	public function onMapIntegrationOptions($document, $map){
		return $document;
	}

	/**
	 * Get Category Filtering Clause 
	 * 
	 * @return string
	*/
	public function getCategoryFilteringClauseMarkerIDFieldName(){
		global $wpdb;
		return "{$wpdb->prefix}posts.ID";
	}

	/**
	 * Query logic
	 * 
	 * @param array $fields 
	 * @param MarkerFilter $markerFiler
	 * @param array $inputParamss
	 * 
	 * @return Query
	*/
	public function getQuery($fields=null, $markerFilter=null, $inputParams=null){
		global $wpdb;
		global $wpgmza;

		$query = new \WPGMZA\Query();
		$query->type = 'SELECT';
		$query->table = "{$wpdb->prefix}postmeta";

		$descColumn = 'post_content';
		if(!empty($wpgmza) && !empty($wpgmza->settings) && !empty($wpgmza->settings->woo_product_description_type)){
			if($wpgmza->settings->woo_product_description_type === 'excerpt'){
				$descColumn = 'post_excerpt';
			}
		}
				
		foreach($fields as $field){
			if(preg_match('/^COUNT\([\w*]+\)$/', $field)){
				$query->fields[$field] = $field;
				continue;
			}
			
			switch($field){
				case 'id':
					$query->fields[$field] = 'CONCAT("wcp_", meta_id) AS id';
					break;
				
				case 'title':
					$query->fields[$field] = "(
						SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = post_id AND post_status = 'publish'
					) AS $field";
					break;

				case 'description':
					$query->fields[$field] = "(
						SELECT {$descColumn} FROM {$wpdb->prefix}posts WHERE ID = post_id AND post_status = 'publish'
					) AS $field";
					break;
				
				case 'map_id':
					if(!empty($markerFilter->map->id)){
						$query->fields[$field] = (int)$markerFilter->map->id . " AS map_id";
					} else{
						$query->fields[$field] = "'' AS $field";
					}
					break;
					
				case 'address':
				case 'lat':
				case 'lng':
					$query->fields[$field] = $this->getExtractJSONStringSQL($field) . " AS $field";
					break;
				
				case 'link':
					$query->fields[$field] = 'guid AS link';
					break;
				
				case 'approved':
					$query->fields[$field] = '1 AS approved';
					break;
				
				case 'sticky':
					$query->fields[$field] = '0 AS sticky';
					break;
				
				case 'latlng':
					$query->fields[$field] = "POINT(
							" . $this->getExtractJSONStringSQL('lat') . "
							,
							" . $this->getExtractJSONStringSQL('lng') . "
					) AS $field";
					break;
				
				default:
					$query->fields[$field] = "'' AS $field";
					break;
			}
		}
		
		if($markerFilter){
			if(@$markerFilter->wcp_post_id){
				$query->where['wcp_post_id'] = "{$wpdb->prefix}posts.ID = " . (int)$markerFilter->wcp_post_id;
			}
			
			// TODO: Merge markerIDs and overrideMarkerIDs
			if(isset($markerFilter->markerIDs)){
				$query->in('CONCAT("wcp_", meta_id)', $markerFilter->markerIDs, '%s');
			}
			
			if(isset($inputParams['overrideMarkerIDs'])){
				$ids = $inputParams['overrideMarkerIDs'];
				
				if(is_string($ids)){
					$ids = explode(',', $ids);
				}
				
				$query->in('CONCAT("wcp_", meta_id)', $ids, '%s');
			}
			
			if(!empty($inputParams['filteringParams']['center']) && $markerFilter->map->order_markers_by == \WPGMZA\MarkerListing::ORDER_BY_DISTANCE){
				$lat1 = floatval($inputParams['filteringParams']['center']['lat']) / 180 * 3.1415926;
				$lng1 = floatval($inputParams['filteringParams']['center']['lng']) / 180 * 3.1415926;
				
				$lat2 = $this->getExtractJSONStringSQL("lat");
				$lng2 = $this->getExtractJSONStringSQL("lng");
				
				$query->fields['distance'] = "
					(
						6371 *
					
						2 *
					
						ATAN2(
							SQRT(
								POW( SIN( ( (($lat2) / 180 * 3.1415926) - $lat1 ) / 2 ), 2 ) +
								COS( ($lat2) / 180 * 3.1415926 ) * COS( $lat1 ) *
								POW( SIN( ( (($lng2) / 180 * 3.1415926) - $lng1 ) / 2 ), 2 )
							),
							
							SQRT(1 - 
								(
									POW( SIN( ( (($lat2) / 180 * 3.1415926) - $lat1 ) / 2 ), 2 ) +
									COS( ($lat2) / 180 * 3.1415926 ) * COS( $lat1 ) *
									POW( SIN( ( (($lng2) / 180 * 3.1415926) - $lng1 ) / 2 ), 2 )
								)
							)
						)
					) AS distance
				";
			}
		}
		
		$query->where["meta_key"]		= "meta_key = '_wpgmza_wcp_loc'";
		
		$query->where["meta_value"]		= "LENGTH(meta_value) > 0";
		
		$query->join["posts"] 			= "{$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = post_id";
		$query->where["post_status"] 	= "post_status = 'publish'";
		
		// NB: Cache post_id here for ProMarker, this is done here for performance reasons
		$this->cachePostIDs($query);

		return $query;
	}

	/**
	 * Cache posts IDs for later refernce
	 * 
	 * @param Query $postmeta_query The query
	 * 
	 * @return void
	*/
	protected function cachePostIDs($postmeta_query){
		global $wpdb;
		
		$query = new \WPGMZA\Query();
		
		$query->type			= 'SELECT';
		$query->fields[]		= 'meta_id';
		$query->fields[]		= 'post_id';
		$query->table			= "{$wpdb->prefix}postmeta";
		
		$query->join["posts"]	= "{$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = post_id";
		
		foreach($postmeta_query->where as $key => $value){
			$query->where[$key] = $value;
		}
		
		$stmt					= $query->build();
		$sql					= $wpdb->prepare($stmt, $postmeta_query->params->toArray());
		$results				= $wpdb->get_results($sql);
			
		$cache					= array();
		
		foreach($results as $obj){
			$cache[$obj->meta_id] = $obj->post_id;
		}
		
		MarkerSource::addPostIDFromMetaIDToCache($cache);
	}

	/**
	 * Extract JSON from meta
	 * 
	 * @param string $field The key to find
	 * 
	 * @return string
	*/
	protected function getExtractJSONStringSQL($field){
		return 'JSON_EXTRACT(meta_value, \'$.' . $field . '\')';
	}

	/**
	 * Registers the product editor meta box for adding product locations
	 * 
	 * @return void
	*/
	public function registerLocationEditorMetabox(){
		global $wpgmza;

		if(!empty($wpgmza->settings->woo_product_location_editor_enabled)){
			add_meta_box(
		        'wpgmza_wcp_location_editor',
		        __('Product Location Editor (WP Go Maps)', 'wp-google-maps'),
		        array($this, 'renderLocationEditorMetabox'),
		        'product',
				'normal',
				'low'
		    );
		}
	}

	/**
	 * Renders the product editor meta box for adding product locatios
	 * 
	 * @param object $post
	 * 
	 * @return string
	*/
	public function renderLocationEditorMetabox($post){
		global $wpdb, $wpgmza, $WPGMZA_TABLE_NAME_MAPS;

		$address = "";
		$lat = "";
		$lng = "";
		if(!empty($post) && !empty($post->ID)){
			$loc = get_post_meta($post->ID, '_wpgmza_wcp_loc', true);
			if(!empty($loc)){
				try{
					$loc = json_decode($loc);

					if(!empty($loc->address)){
						$address = $loc->address;
					}

					if(!empty($loc->lat)){
						$lat = $loc->lat;
					}

					if(!empty($loc->lng)){
						$lng = $loc->lng;
					}

				} catch(\Exception $ex){

				} catch(\Error $err){

				}
			}
		}

		$metabox = new \WPGMZA\DOMDocument();

		/* Wrap content */
		$metabox->loadHTML("<div class='wpgmza-wcp-editor'></div>");
		$wrapper = $metabox->querySelector('.wpgmza-wcp-editor');

		/* Address wrapper */
		$addressWrapper = $metabox->createElement('div');
		$addressWrapper->addClass('wpgmza-flex-row');
		$addressWrapper->addClass('wpgmza-margin-b-10');

		/* Address label */
		$addressLabel = $metabox->createElement("label");
		$addressLabel->appendText(__("Location", "wp-google-maps"));
		$addressLabel->addClass('wpgmza-pad-10');
		$addressLabel->setInlineStyle('width', '125px');

		/* Address input */
		$addressInput = $metabox->createElement('input');
		$addressInput->setAttribute('type', 'text');
		$addressInput->addClass('wpgmza-address');

		$addressInput->setAttribute('name', '_wpgmza_wcp_loc_address');
		$addressInput->setAttribute('value', $address);

		$clearCoordBtn = $metabox->createElement('button');
		$clearCoordBtn->addClass('wpgmza-wcp-clear-coords-button');
		$clearCoordBtn->addClass('wpgmza-margin-l-10');
		$clearCoordBtn->addClass('wpgmza-button');
		$clearCoordBtn->appendText(__("Clear", "wp-google-maps"));


		$toggleCoordsBtn = $metabox->createElement('button');
		$toggleCoordsBtn->addClass('wpgmza-wcp-toggle-coords-button');
		$toggleCoordsBtn->addClass('wpgmza-margin-l-10');
		$toggleCoordsBtn->addClass('wpgmza-button');
		$toggleCoordsBtn->appendText(__("Advanced", "wp-google-maps"));

		$addressWrapper->appendChild($addressLabel);
		$addressWrapper->appendChild($addressInput);
		$addressWrapper->appendChild($clearCoordBtn);
		$addressWrapper->appendChild($toggleCoordsBtn);

		/* Coord wrapper */
		$coordWrapper = $metabox->createElement('div');
		$coordWrapper->addClass('wpgmza-wcp-coords-block');
		$coordWrapper->addClass('wpgmza-hidden');

		/* Coord inputs */
		$coordInputNames = array('lat' => __("Latitude", "wp-google-maps"), 'lng' => __("Longitude", "wp-google-maps"));
		foreach($coordInputNames as $coordName => $coordPrint){
			$coordRow = $metabox->createElement('div');
			
			$coordRow->addClass('wpgmza-flex-row');
			$coordRow->addClass('wpgmza-margin-b-10');
			
			$coordLabel = $metabox->createElement('label');
			$coordLabel->appendText($coordPrint);
			$coordLabel->addClass('wpgmza-pad-10');

			$coordLabel->setInlineStyle('width', '125px');

			$coordInput = $metabox->createElement('input');
			$coordInput->setAttribute('type', 'text');

			$coordInput->setAttribute('name', '_wpgmza_wcp_loc_' . $coordName);

			$coordInput->setAttribute('value', $coordName === 'lat' ? $lat : $lng);


			$coordRow->appendChild($coordLabel);
			$coordRow->appendChild($coordInput);

			$coordWrapper->appendChild($coordRow);
		}

		/* Map wrapper */
		$mapWrapper = $metabox->createElement('div');
		$mapWrapper->setAttribute('id', 'wpgmza-wcp-map-container');

		/* Map */
		$maps = $wpdb->get_results("SELECT id FROM $WPGMZA_TABLE_NAME_MAPS WHERE active = 0 ORDER BY id ASC LIMIT 1");
		if(!empty($maps)){
			$firstMap = array_pop($maps);
			$mapId = !empty($firstMap) ? $firstMap->id : false;

			if(!empty($mapId)){
				$map = \WPGMZA\Map::createInstance($mapId);

				$map->element->setInlineStyle('min-height', '400px');	// Safeguard for map edit page zero height
				$map->element->setAttribute('id', 'wpgmza_map');	

				$settings = $map->element->getAttribute('data-settings');

				if(!empty($settings)){
					$settings = json_decode($settings);
					if(empty($settings->autoFetchFeatures)){
						/* Disable the data loading */
						$settings->autoFetchFeatures = false;

						/* Now rebake */
						$map->element->setAttribute('data-settings', json_encode($settings));
					}
				}

				$mapWrapper->import($map->element);

				$note = $metabox->createElement('small');
				$note->appendText(__("Note: You can right-click on the map to place the location manually, or drag an existig marker to a preferred location", "wp-google-maps"));

				$mapWrapper->appendChild($note);
			}
		}


		/* Append all to wrapper */
		$wrapper->appendChild($addressWrapper);
		$wrapper->appendChild($coordWrapper);
		$wrapper->appendChild($mapWrapper);

		echo $metabox->html;

		/* Load base scripts */
		$wpgmza->loadScripts(true);
	}

	/**
	 * Store the metabox data for a product, this is encoded as JSON
	 * 
	 * @param int $postId 
	 * 
	 * @return void
	*/
	public function saveLocationEditorMetabox($postId){
		if(!empty($postId) && !empty($_POST) && !empty($_POST['save'])){
			$address = "";
			$lat = 0;
			$lng = 0;

			if(!empty($_POST['_wpgmza_wcp_loc_address'])){
				$address = sanitize_text_field(str_replace(array("\'", "'"), '', $_POST['_wpgmza_wcp_loc_address']));
			}

			if(!empty($_POST['_wpgmza_wcp_loc_lat'])){
				$lat = sanitize_text_field($_POST['_wpgmza_wcp_loc_lat']);
				$lat = floatval($_POST['_wpgmza_wcp_loc_lat']);
			}

			if(!empty($_POST['_wpgmza_wcp_loc_lng'])){
		        $lng = sanitize_text_field($_POST['_wpgmza_wcp_loc_lng']);
		        $lng = floatval($_POST['_wpgmza_wcp_loc_lng']);
			}

			$loc = (object) array(
				'address' => !empty($address) ? $address : "",
				'lat' => !empty($lat) ? $lat : 0,
				'lng' => !empty($lng) ? $lng : 0 
			);

			if(!empty($loc->address) && !empty($loc->lat) && !empty($loc->lng)){
				update_post_meta($postId, '_wpgmza_wcp_loc', json_encode($loc));
			} else {
				delete_post_meta($postId, "_wpgmza_wcp_loc");
			}
		}
	}
}