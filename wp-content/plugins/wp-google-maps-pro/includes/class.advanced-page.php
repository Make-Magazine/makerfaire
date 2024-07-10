<?php

namespace WPGMZA;

class AdvancedPage extends Page {
	public function __construct() {
		global $wpgmza;
		
		Page::__construct();
		
		$this->document->loadPHPFile($wpgmza->internalEngine->getTemplate('advanced-page.html.php', WPGMZA_PRO_DIR_PATH));

	    /* Developer Hook (Action) - Alter output of the tools page, passes DOMDocument for mutation, two variations available */     
		do_action("wpgmza_tools_page_created", $this->document);
		do_action("wpgmza_advanced_page_created", $this->document);

		echo $this->document->html;
	}
}

add_action('wpgmza_advanced_page_create_instance', function() {
	$advancedPage = AdvancedPage::createInstance();
});
