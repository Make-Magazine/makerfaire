<?php

namespace WPGMZA;

class ProSettingsPage extends SettingsPage
{
	public function __construct()
	{
		global $wpgmza;

		SettingsPage::__construct();
		
		// Enable Pro controls
		// $this->querySelectorAll('.wpgmza-basic-only');
		
		// Remove basic only elements
		$this->document->querySelectorAll('.wpgmza-upsell')->remove();
		$this->document->querySelectorAll('.wpgmza-pro-feature')->removeClass('wpgmza-pro-feature');

		// $this->markerLibraryDialog = new MarkerLibraryDialog();
		// @$this->document->querySelector("#wpgmza-global-settings")->import($this->markerLibraryDialog->html());

		$this->markerIconEditor = new MarkerIconEditor();
		@$this->document->querySelector('#wpgmza-global-settings')->import($this->markerIconEditor->html);

		if(empty($_POST)){
			/* Temporary inclusiong, we should rework this to be a part of the script loader I think */
			if (function_exists('wp_enqueue_media')) {
				wp_enqueue_media();
			}
		}

	}
}

add_filter('wpgmza_create_WPGMZA\\SettingsPage', function() {	
	return new ProSettingsPage();
}, 10, 0);
