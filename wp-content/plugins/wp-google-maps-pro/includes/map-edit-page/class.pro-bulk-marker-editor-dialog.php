<?php

namespace WPGMZA;

$dir = wpgmza_get_basic_dir();

wpgmza_require_once($dir . 'includes/class.factory.php');
wpgmza_require_once($dir . 'includes/class.marker-filter.php');

class ProBulkMarkerEditorDialog extends BulkMarkerEditorDialog {
	public function __construct(){
		global $wpgmza;
		BulkMarkerEditorDialog::__construct();

		$this->populateCategoryOptions();
	}

	public function populateCategoryOptions(){

		$categoryPicker = new CategoryPicker(array(
			'ajaxName' => 'category',
			'map_id' => !empty($_GET['map_id']) && intval($_GET['map_id']) ? intval($_GET['map_id']) : false
		));

		$this->document->querySelector(".bulk-category-selector")->import($categoryPicker);

		
	}
}

add_filter('wpgmza_create_WPGMZA\\BulkMarkerEditorDialog', function() {
	return new ProBulkMarkerEditorDialog();
}, 10, 1);