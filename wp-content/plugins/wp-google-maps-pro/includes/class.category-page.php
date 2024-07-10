<?php

namespace WPGMZA;

class CategoryPage extends Page {
	public function __construct() {
		
		Page::__construct();
		
		$action = !empty($_GET['action']) ? $_GET['action'] : false; 

		switch ($action) {
			case 'trash':
				$this->onTrashCategory();
				break;
			case 'new':
				$this->onAddCategory();
				break;
			case 'edit':
				$this->onEditCategory(!empty($_GET['cat_id']) ? $_GET['cat_id'] : false);
				break;
			default:
				$this->onListCategories();
				break;
		}


		echo $this->document->html;
	}

	private function onListCategories(){
		global $wpgmza;

		$this->document->loadPHPFile($wpgmza->internalEngine->getTemplate('category-page/categories-list.html.php', WPGMZA_PRO_DIR_PATH));

		$categoryTable = new CategoryTable();
		$this->document->querySelector("#category_list")->import($categoryTable);
	}

	private function onAddCategory(){
		global $wpgmza;

		$this->verifyParentColumn();

		$markerIconPicker = new MarkerIconPicker(
			array(
				'name' => 'upload_default_category_marker'
			)
		);

		$this->document->loadPHPFile($wpgmza->internalEngine->getTemplate('category-page/category-add.html.php', WPGMZA_PRO_DIR_PATH));

		$markerIconEditor = new MarkerIconEditor();
		@$this->document->querySelector('.wpgmza-wrap')->import($markerIconEditor->html);

		$this->document->querySelector('#marker_category_icon')->import($markerIconPicker->html);
		$this->document->querySelector('#parent_category')->import($this->getCategorySelectHtml());
		$this->document->querySelector('#assigned_to')->import($this->getMapSelectionSwitchHtml());

		
	}

	private function onEditCategory($id){
		global $wpdb;
		global $wpgmza_tblname_categories;
		global $wpgmza;

		$id = intval($id);


		if(!empty($id)){
			$results = $wpdb->get_results("SELECT * FROM $wpgmza_tblname_categories WHERE `id` = '{$id}' LIMIT 1");
			
			if(!empty($results)){
				$data = $results[0];

				$markerUrl = "";
				$retinaReady = false;
				$parent = 0;
				
				if (!empty($data->category_icon)) {
			        $markerUrl = $data->category_icon;
			    }
			    
			    if (!empty($data->retina) && intval($data->retina) === 1){
			        $retinaReady = true;
			    } 

			    if (!empty($data->parent) && intval($data->parent) > 0) {
			        $parent = intval($data->parent);
			    }
			
				$this->document->loadPHPFile($wpgmza->internalEngine->getTemplate('category-page/category-edit.html.php', WPGMZA_PRO_DIR_PATH));

				$markerIconEditor = new MarkerIconEditor();
				@$this->document->querySelector('.wpgmza-wrap')->import($markerIconEditor->html);

				$options = array(
					'name' => 'upload_default_category_marker'
				);
				
				if(!empty($markerUrl)){
					$options['value'] = $markerUrl;
				}
				
				$markerIconPicker = new MarkerIconPicker($options);
				
				$this->document->querySelector('#marker_category_icon')->import($markerIconPicker->html);

				$populationData = array(
					'wpgmaps_marker_category_id' => intval($data->id),
					'wpgmaps_marker_category_name' => $data->category_name,
					'category_image' => $data->image,
					'wpgmaps_marker_category_priority' => intval($data->priority)
				);

				$this->document->populate($populationData);

				$this->document->querySelector('#parent_category')->import($this->getCategorySelectHtml($parent, $id));
				$this->document->querySelector('#assigned_to')->import($this->getMapSelectionSwitchHtml($id));

			} else {
				$this->onListCategories();
			}
		} else {
			$this->onListCategories();
		}
	}

	private function onTrashCategory(){
		$html = "";
		if (!empty($_GET['s']) && intval($_GET['s']) === 1 && !empty($_GET['cat_id'])) {
            if (wpgmaps_trash_cat(intval($_GET['cat_id']))) {
                echo "<script>window.location = '" . get_option('siteurl') . "/wp-admin/admin.php?page=wp-google-maps-menu-categories'; </script>";
            } else {
                $html = __("There was a problem deleting the category.");
            }
        } else {
        	$html = "<div class='wpgmza-wrap'>";
            $html .= 	"<h2>" . __("Delete Category","wp-google-maps") . "</h2>";
            $html .= 	"<div class='wpgmza-card wpgmza-shadow-high wpgmza-fit-content wpgmza-pad-20'>";
            $html .= 		__("Are you sure you want to delete the category","wp-google-maps") . "?";
            $html .= 		"<br><br>";
            $html .= 		"<div class='wpgmza-inline-field'>";
            $html .= 			"<a class='wpgmza-button wpgmza-button-primary' href='" . esc_attr("admin.php?page=wp-google-maps-menu-categories&action=trash&cat_id=" . intval($_GET['cat_id']) . "&s=1" ) . "'>";
            $html .=				__("Yes","wp-google-maps");
            $html .=			"</a> ";
            $html .= 			"<a class='wpgmza-button' href=\"admin.php?page=wp-google-maps-menu-categories\">" . __("No","wp-google-maps") . "</a>";
            $html .= 		"</div>";
            $html .= 	"</div>";
            $html .= "</div>";
        }

        $this->document->loadHtml($html);
	}

	private function getCategorySelectHtml($selected = false, $exclude = false){
		$categorySelectHtml = "";
		
		$categories = wpgmza_return_all_categories();
	    if (!empty($categories)) {
	        foreach ($categories as $category) {
	            $id = intval($category->id);

	            if(!empty($exclude) && intval($exclude) === $id){
	            	continue;
	            }

	            $name = "";
	            if (!empty($category->category_name)) { 
	            	$name = $category->category_name; 
	            }

	            $name .= " (#{$id})";

	            $selectedFlag = "";
	            if(!empty($selected) && intval($selected) === $id){
	            	$selectedFlag = "selected";
	            }

	            $categorySelectHtml .= "<option value='{$id}' {$selectedFlag}>{$name}</option>";
	        }
	    }

	    return $categorySelectHtml;
	}

	private function getMapSelectionSwitchHtml($forCategory = false){
    	$mapSwitchesHtml = "";

    	$checkedFlag = "";
    	if(!empty($forCategory)){
    		$checkedFlag = wpgmza_check_cat_map('ALL', $forCategory);
    	}

		$mapSwitchesHtml .= "<li>";
	    $mapSwitchesHtml .= 	"<div class='switch switch-inline'>";
	    $mapSwitchesHtml .= 		"<input class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='map-cat-all' name='assigned_to_map[]' value='ALL' {$checkedFlag}>";
	    $mapSwitchesHtml .= 		"<label for='map-cat-all'></label>";
	    $mapSwitchesHtml .= 		"<label for='map-cat-all'>";
	    $mapSwitchesHtml .= 			__("All Maps", "wp-google-maps");
	    $mapSwitchesHtml .= 		"</label>";
	    $mapSwitchesHtml .= 	"</div>";
	    $mapSwitchesHtml .= "</li>";

    	$mapIds = wpgmza_return_all_map_ids();
    	 foreach ($mapIds as $id) {
		    $data = wpgmza_get_map_data($id);

		    $checkedFlag = "";
	    	if(!empty($forCategory)){
	    		$checkedFlag = wpgmza_check_cat_map($id, $forCategory);
	    	}

		    $mapSwitchesHtml .= "<li>";
		    $mapSwitchesHtml .= 	"<div class='switch switch-inline'>";

		    $mapSwitchesHtml .= 		"<input class='cmn-toggle cmn-toggle-round-flat' type='checkbox' id='map-cat-{$id}' name='assigned_to_map[]' value='{$id}' {$checkedFlag}>";
		    $mapSwitchesHtml .= 		"<label for='map-cat-{$id}'></label>";
		    $mapSwitchesHtml .= 		"<label for='map-cat-{$id}'>";
		    $mapSwitchesHtml .= 			"{$data->map_title}  (#{$id})";
		    $mapSwitchesHtml .= 		"</label>";
		    $mapSwitchesHtml .= 	"</div>";
		    $mapSwitchesHtml .= "</li>";
		}

		return $mapSwitchesHtml;
	}

	private function verifyParentColumn(){
		global $wpdb;
		if(!$wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}wpgmza_categories LIKE 'parent'")){
			$wpdb->query("ALTER TABLE {$wpdb->prefix}wpgmza_categories ADD COLUMN parent int(11)");
		}
	}
}

add_action('wpgmza_category_page_create_instance', function() {
	$categoryPage = CategoryPage::createInstance();
});
