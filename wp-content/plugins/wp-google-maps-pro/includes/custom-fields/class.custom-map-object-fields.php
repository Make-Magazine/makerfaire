<?php

namespace WPGMZA;

global $wpdb;

/**
 * This class deals with custom fields on a specific map object (marker, polygon, polyline, etc.)
 */
class CustomFeatureFields implements \IteratorAggregate, \JsonSerializable, \Countable
{
	private static $installed = null;
	
	protected static $field_names_by_id = null;
	protected static $field_ids_by_name = null;
	
	private $object_id;
	private $meta_table_name;
	
	private $meta;
	private $attributes;
	private $icon;
	private $widget_types;
	private $display_in_infowindows;
	private $display_in_marker_listings;
	
	/**
	 * Constructor. DO NOT call this directly. Use the hooks, for example wpgmza_get_marker_custom_fields
	 * @return WPGMZA\CustomFields
	 */
	public function __construct($object_id, $table_name)
	{
		global $wpgmza;
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		if(!CustomFields::installed())
			CustomFields::install();
		
		if(!CustomFeatureFields::$field_names_by_id)
		{
			CustomFeatureFields::$field_names_by_id = array();
			
			$fields = $wpdb->get_results("SELECT id, name FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");
			
			foreach($fields as $obj)
				CustomFeatureFields::$field_names_by_id[(int)$obj->id] = $obj->name;
				
			CustomFeatureFields::$field_ids_by_name = array_flip(CustomFeatureFields::$field_names_by_id);
		}
		
		$this->meta_table_name = $table_name;
		
		if(!$this->meta_table_name)
			throw new \Exception('No table name');
		
		$this->object_id = (int)$object_id;
		$this->meta = array();
		
		$qstr = "
			SELECT
			name, 
			value,
			icon,
			attributes,
			widget_type,
			display_in_infowindows,
			display_in_marker_listings
			FROM `$WPGMZA_TABLE_NAME_CUSTOM_FIELDS`
			LEFT JOIN `{$this->meta_table_name}`
			ON `id`=`{$this->meta_table_name}`.`field_id`
			WHERE `object_id`=%d";
		
		if(!$wpgmza->isUserAllowedToEdit())
			$qstr .= " AND (display_in_infowindows = '1' OR display_in_marker_listings = '1') ";
			
		$qstr .= "
			AND LENGTH(value)
			";

		$qstr .= " ORDER BY stack_order ASC";
			
		$params = array($object_id);
		$stmt = $wpdb->prepare($qstr, $params);
		
		$results = $wpdb->get_results($stmt);
		
		// TODO: Custom Fields: This really should be in a class
		// TODO: Custom Fields: Serialize these for the JS infowindows
		$this->display_in_infowindows = array();
		$this->display_in_marker_listings = array();
		
		foreach($results as $obj)
		{
			$this->meta[$obj->name] = $obj->value;
			
			if(!empty($obj->icon))
				$this->icon[$obj->name] = $obj->icon;

			$this->display_in_infowindows[$obj->name] = $obj->display_in_infowindows;
			$this->display_in_marker_listings[$obj->name] = $obj->display_in_marker_listings;
			$this->widget_types[$obj->name] = $obj->widget_type;
			
			if(!empty($obj->attributes)){
				try{
					$this->attributes[$obj->name] = json_decode($obj->attributes);
				} catch(\Exception $ex){
					$this->attributes = (object)array();
				} catch(\Error $error){
					$this->attributes = (object)array();
				}
			} else{
				$this->attributes = (object)array();
			}
		}
	}
	
	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->meta);
	}
	
	/**
	 * Get iterator for looping over fields with foreach
	 * @return \ArrayIterator
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator($this->meta);
	}
	
	/**
	 * Gets the fields to be serialized as JSON, useful for export
	 * @return array
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		$result = array();
		
		foreach($this->meta as $name => $value)
		{
			$id = CustomFeatureFields::$field_ids_by_name[$name];
			$result[] = array(
				'id'		=> $id,
				'name'		=> $name,
				'value'		=> $value
			);
		}
		
		return $result;
	}
	
	/**
	 * Returns true if the named meta field is set
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->meta[$name]);
	}
	
	/**
	 * Gets the named meta field from this objects cache
	 * @return mixed
	 */
	public function __get($name)
	{
		if($name == 'object_id')
			return $this->object_id;
		
		if(isset($this->meta[$name]))
			return $this->meta[$name];
		
		return null;
	}
	
	/**
	 * Sets the named meta field in this objects cache and the database
	 * @return void
	 */
	public function __set($name, $value)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		if($name == 'object_id')
			throw new \Exception('Property is read only');
		
		// TODO: I'm not sure how these 0 key values are creeping in, but they're invalid. We safeguard against it here so notices aren't generated
		if(is_numeric($name) && $name != '0')
		{
			$field_id = (int)$name;
			$name = CustomFeatureFields::$field_names_by_id[ (int)$name ];
		}
		else
		{
			// Create the field if it doesn't exist
			
			if(!isset(CustomFeatureFields::$field_ids_by_name[$name]))
			{
				$stmt = $wpdb->prepare("INSERT INTO $WPGMZA_TABLE_NAME_CUSTOM_FIELDS (name) VALUES (%s)", array($name));
				
				$wpdb->query($stmt);
				
				CustomFeatureFields::$field_ids_by_name[$name] = $wpdb->insert_id;
			}
			
			$field_id = CustomFeatureFields::$field_ids_by_name[$name];
		}
		
		$this->meta[$name] = $value;
		
		$stmt = $wpdb->prepare("INSERT INTO {$this->meta_table_name}
			(field_id, object_id, value)
			VALUES
			(%d, %d, %s)
			ON DUPLICATE KEY UPDATE value = %s",
			array(
				$field_id,
				$this->object_id,
				$value,
				$value
			)
		);
		
		$wpdb->query($stmt);
	}
	
	/**
	 * Removes the named meta field from the cache and deletes it from the database
	 * @return void
	 */
	public function __unset($name)
	{
		global $wpdb;
		
		unset($this->meta[$name]);
		
		$stmt = $wpdb->prepare("DELETE FROM {$this->meta_table_name} WHERE name=%s AND object_id=%d", array($name, $this->object_id));
		$wpdb->query($stmt);
	}
	
	public function remove()
	{
		global $wpdb;
		
		$this->meta = array();
		
		$stmt = $wpdb->prepare("DELETE FROM {$this->meta_table_name} WHERE object_id=%d", array($this->object_id));
		$wpdb->query($stmt);
	}

	/**
	 * Returns the default HTML for the custom fields (front end)
	 * TODO: This should be changed to use DOMDocument instead of plain strings, it's vulnerable to XSS attacks through UGM at the moment.
	 * @return string
	 */
	public function html(){
		global $wpgmza;

		$html = '';

		foreach($this->meta as $key => $value)
		{
			if(empty($key) || (empty($value) && intval($value) !== 0))
				continue;
			
			$id = CustomFeatureFields::$field_ids_by_name[$key];
			if(empty($this->display_in_infowindows)){
				continue;
			}

			$html_data = $this->display_in_infowindows[$key] !== '1' ? 'data-hide-in-infowindows="true" ' : '';
			$html_data .= $this->display_in_marker_listings[$key] !== '1' ? 'data-hide-in-marker-listings="true" ' : '';
			
			$wrapTag = 'div';
			if($wpgmza->internalEngine->isLegacy()){
				$wrapTag = "p";
			}
			$item = "<{$wrapTag} " . $html_data . ' data-custom-field-id="' . $id . '" data-custom-field-name="' . htmlspecialchars($key) . '" ';
			$widget_type = isset($this->widget_types[$key]) ? $this->widget_types[$key] : 'none';
			
			if(!empty($this->attributes))
			{
				$arr = (array)$this->attributes;
				
				if(isset($arr[$key]))
					foreach($arr[$key] as $attr_name => $attr_value)
					{
						// NB: Temporary fix for blank attribute names
						if(empty($attr_name))
							continue;
						
						$item .= "$attr_name=\"" . addcslashes($attr_value, '"') . "\"";
					}
			}

			$item .= '>';
			
			if(!empty($this->icon[$key])){
				$icon = $this->icon[$key];
				$icon = str_replace(array('fa-', 'fa '), '', $icon);
				$item .= '<i class="wpgmza-custom-field fa fa-' . $icon . '"></i>';
			}

			$label = ucwords(str_replace("_", " ", $key));
			$item .= "<span class='custom-field-label'>{$label}:</span> ";


			if ($widget_type == 'time') {
				$hour = date('H', strtotime($value));
				$suffix = $hour <= 12 ? 'AM' : 'PM';
				$value .= ' ' . $suffix;
			}
			
			$item .= __($value, 'wp-google-maps');
			
			$item .= "</{$wrapTag}>";
			
			/* Developer Hook (Filter) - Modify custom field row HTML */
			$html .= apply_filters('wpgmza_custom_fields_row_html', $item);
		}
		
		/* Developer Hook (Filter) - Modify custom field block HTML */
		return apply_filters('wpgmza_custom_fields_html', $html);
	}
	
	/**
	 * Shows the admin controls for these custom fields
	 * @return string
	 */
	public static function adminHtml($useLegacyHTML=false)
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;
		
		
		if(!CustomFields::installed())
			CustomFields::install();
		
		$fields = $wpdb->get_results("SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS");
		$html = '';
		
		foreach($fields as $field)
		{
			$attributes = '';
			
			$json = !empty($field->attributes) ? json_decode($field->attributes) : false;
			
			if($json)
				foreach($json as $attr_name => $attr_value)
				{
					// NB: Temporary fix for blank attribute names
					if(empty($attr_name))
						continue;
					
					$attributes .= " $attr_name=\"" . addcslashes($attr_value, '"') . "\"";
				}
			
			$type = $field->widget_type == 'time' || $field->widget_type == 'date' ? $field->widget_type : 'text';

			if($useLegacyHTML){ 
				$item = '<tr data-custom-field-id="' . $field->id . '">
					<td>
						' . htmlspecialchars($field->name) . '
					</td>
					<td>
						<input placeholder="' . htmlspecialchars($field->name) . '" data-custom-field-name="' . addcslashes($field->name, '"') . '" name="wpgmza-custom-field-' . $field->id . '" ' . $attributes . '/>
					</td>
				</tr>';

			}
			else
			{
				$item = '<fieldset data-custom-field-id="' . $field->id . '">
				  <legend>
					  ' . (!empty($field->name) ? htmlspecialchars($field->name) : __("Field", "wp-google-maps") . " {$field->id}"). '
				  </legend>
				  <input type="' . $type . '" placeholder="' . htmlspecialchars($field->name) . '"
					  data-ajax-name="custom_field_' . $field->id . '" ' . 
					  $attributes . '/>
				  </fieldset>';
			}
      
		  $html .= $item;
		}
		
		return $html;
	}
}
