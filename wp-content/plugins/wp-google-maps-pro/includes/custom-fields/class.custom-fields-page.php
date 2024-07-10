<?php


namespace WPGMZA;

class CustomFieldsPage extends Page {
	public function __construct() {
		global $wpgmza;
		
		Page::__construct();
		
		$this->document->loadPHPFile($wpgmza->internalEngine->getTemplate('custom-fields-page/field-list.html.php', WPGMZA_PRO_DIR_PATH));

		$this->form = $this->document->querySelector('form');
		if(empty($_POST)) {
			$this->addFormNonces();
			$this->loadFields();
		} else {
			if(!$this->isNonceValid($this->form, $_POST['nonce'])){
				throw new \Exception("Invalid nonce");
			}

			if(!current_user_can('administrator')){
				http_response_code(401);
				exit;
			}

			$this->saveFields();

			wp_redirect($_SERVER['HTTP_REFERER']);
			return;
		}

	}

	private function loadFields(){
		global $wpdb, $wpgmza;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;

		$query = "SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS WHERE display_in_infowindows = 1 OR display_in_marker_listings = 1 ORDER BY stack_order ASC;";
		if(current_user_can('administrator')){
			$query = "SELECT * FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS ORDER BY stack_order ASC";
		}

		$fields = $wpdb->get_results($query);
		foreach($fields as $index => $field){
			$row = new DOMDocument();
			$row->loadPHPFile($wpgmza->internalEngine->getTemplate('custom-fields-page/field-row.html.php', WPGMZA_PRO_DIR_PATH));

			$row->populate(
				array(
					'id' => $field->id,
					'name' => addslashes($field->name),
					'stack_order' => isset($field->stack_order) ? $field->stack_order : $index,
					'icon' => $field->icon,
					'display_in_infowindows' => intval($field->display_in_infowindows) == 1 ? true : false,
					'display_in_marker_listings' =>  intval($field->display_in_marker_listings) == 1 ? true : false
				)
			);

			$row->querySelector('.filter-options')->import($this->filterDropdown($field->widget_type));

			$row->querySelector('input[name="display_in_infowindows"]')->setAttribute("name", "display_in_infowindows[{$field->id}]");
			$row->querySelector('input[name="display_in_marker_listings"]')->setAttribute("name", "display_in_marker_listings[{$field->id}]");

			$row->querySelector('.html-attributes')->import($this->attributesEditor($field->attributes));


			$this->document->querySelector('.custom-field-list-container')->import($row->html);			
		}

		$this->addRowTemplate();
	}

	private function addRowTemplate(){
		global $wpgmza;

		/* Creates a template for the JS module to use when making a new row */
		$row = new DOMDocument();
		$row->loadPHPFile($wpgmza->internalEngine->getTemplate('custom-fields-page/field-row.html.php', WPGMZA_PRO_DIR_PATH));

		$row->populate(
			array(
				'id' => "-1",
				'name' => "",
				'stack_order' => -1,
				'icon' => "",
				'display_in_infowindows' => false,
				'display_in_marker_listings' =>  false
			)
		);

		$row->querySelector('.filter-options')->import($this->filterDropdown());

		$row->querySelector('input[name="display_in_infowindows"]')->setAttribute("name", "display_in_infowindows[-1]");
		$row->querySelector('input[name="display_in_marker_listings"]')->setAttribute("name", "display_in_marker_listings[-1]");

		$row->querySelector('.html-attributes')->import($this->attributesEditor(false));

		$row->querySelector('.wpgmza-custom-field-item-row')->addClass('row-template');

		$this->document->querySelector('.custom-field-list-container')->import($row->html);			
	}

	private function attributesEditor($attributes){
		$attributes = !empty($attributes) ? json_decode($attributes) : false;
		
		if(empty($attributes)){
			$attributes = array("" => "");
		}

		$html = "<div class='attributes'>";
		$html .= 	"<input type='hidden' name='attributes[]'>";

		foreach($attributes as $key => $value){
			$html .= "<div class='wpgmza-row'>";

			$html .= 	"<div class='wpgmza-col'>"; 
			$html .= 		"<input type='text' placeholder='" . __("Name", "wp-google-maps") . "' class='attribute-name wpgmza-stretch' value='{$key}'>";
			$html .= 	"</div>"; 
			
			$html .= 	"<div class='wpgmza-col'>"; 
			$html .= 		"<input type='text' placeholder='" . __("Value", "wp-google-maps") . "' class='attribute-value wpgmza-stretch' value='{$value}'>";
			$html .= 	"</div>"; 
			
			$html .= "</div>"; 
		}

		$html .= "</div>";
		
		return $html;	
	}

	private function filterDropdown($selected = false){
		$options = $this->getFilterOptions();

		$html = '<select name="widget_types[]">';
		foreach($options as $value => $text){
			$selectedAttr = $selected == $value ? "selected='selected'" : "";

			$html .= "<option value='{$value}' {$selectedAttr}>";
			$html .= 	__($text, 'wp-google-maps');
			$html .= "</option>";
		}

		// Support for legacy filter
		/* Developer Hook (Filter) - Legacy filter, allowing widget type options */
		$legacyCustomOptions = apply_filters('wpgmza_custom_fields_widget_type_options', (object) array('widget_type' => $selected));
						
		if(is_string($legacyCustomOptions) && !empty($legacyCustomOptions)){
			$html .= $legacyCustomOptions;
		}
	
		$html .= '</select>';
		return $html;
	}

	private function getFilterOptions(){
		$options = array(
			'none'			=> 'None',
			'text'			=> 'Text',
			'dropdown'		=> 'Dropdown',
			'checkboxes'	=> 'Checkboxes',
			'time'			=> 'Time Range',
			'date'			=> 'Date Range'
		);

		/* Developer Hook (Filter) - Modify filter types */
		return apply_filters("wpgmza_custom_fields_filter_types", $options);
	}

	private function saveFields(){
		global $wpdb;
		global $WPGMZA_TABLE_NAME_CUSTOM_FIELDS;

		if(!empty($_POST)){
			$fieldCount = !empty($_POST['ids']) ? count($_POST['ids']) : 0;

			$deleteSql = "DELETE FROM $WPGMZA_TABLE_NAME_CUSTOM_FIELDS";
			if($fieldCount > 0){
				$retainIds = implode(',', array_map('intval', $_POST['ids']));
				$deleteSql .= " WHERE id NOT IN ({$retainIds})";
			}
			$wpdb->query($deleteSql);

			for($i = 0; $i < $fieldCount; $i++){
				$id 						= intval($_POST['ids'][$i]);
				$stack_order 				= $i;
				
				$name 						= sanitize_text_field($_POST['names'][$i]);
				$icon						= sanitize_text_field($_POST['icons'][$i]);
				
				$attributes					= stripslashes($_POST['attributes'][$i]);
				
				$widget_type				= sanitize_text_field($_POST['widget_types'][$i]);
				
				$display_in_infowindows		= isset($_POST['display_in_infowindows'][$id]) ? 1 : 0;
				$display_in_marker_listings	= isset($_POST['display_in_marker_listings'][$id]) ? 1 : 0;

				/*
				 * Increases the complexity, but a needed step due to how we submit this data
				*/ 
				try{
					$attributes = json_decode($attributes, true);
					$cleanAttributes = array();
					foreach($attributes as $aKey => $aValue){
						$aKey = sanitize_text_field($aKey);
						$aValue = sanitize_text_field($aValue);

						$cleanAttributes[$aKey] = $aValue;
					}

					if(!empty($cleanAttributes)){
						$attributes = $cleanAttributes;
					} else {
						$attributes = array("" => "");
					}
				} catch (\Exception $ex){
					$attributes = array("" => "");
				} catch (\Error $er){
					$attributes = array("" => "");
				}

				$attributes = json_encode($attributes);

				if(!json_decode($attributes)){
					throw new \Exception('Invalid attribute JSON');
				}
				
				if($id == -1 || empty($id)){
					$qstr = "INSERT INTO $WPGMZA_TABLE_NAME_CUSTOM_FIELDS (name, icon, attributes, widget_type, display_in_infowindows, display_in_marker_listings, stack_order) VALUES (%s, %s, %s, %s, %s, %s, %s)";
					$params = array($name, $icon, $attributes, $widget_type, $display_in_infowindows, $display_in_marker_listings, $stack_order);
				} else {
					$qstr = "UPDATE $WPGMZA_TABLE_NAME_CUSTOM_FIELDS SET name=%s, icon=%s, attributes=%s, widget_type=%s, display_in_infowindows=%s, display_in_marker_listings=%s, stack_order=%s WHERE id=%s";
					$params = array($name, $icon, $attributes, $widget_type, $display_in_infowindows, $display_in_marker_listings, $stack_order, $id);
				}
				
				$stmt = $wpdb->prepare($qstr, $params);
				$wpdb->query($stmt);
			}
		}
	}
}

add_action('admin_post_wpgmza_save_custom_fields', function() {
	$customFieldsPage = CustomFieldsPage::createInstance();
});

