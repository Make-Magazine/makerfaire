<?php

namespace WPGMZA;

class WizardPage extends Page {
	public function __construct() {
		global $wpgmza;
		
		Page::__construct();
		
		$this->document->loadPHPFile($wpgmza->internalEngine->getTemplate('wizard-page.html.php', WPGMZA_PRO_DIR_PATH));

		/* Developer Hook (Action) - Alter output of the wizard page, passes DOMDocument for mutation */
		do_action("wpgmza_wizard_page_created", $this->document);
		
		echo $this->document->html;
	}
}

add_action('wpgmza_wizard_page_create_instance', function() {
	$wizardPage = WizardPage::createInstance();
});
