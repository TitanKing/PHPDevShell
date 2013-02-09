<?php

class generalSettingsView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();

		// Require JS.
		// Why call it biscuit and not cookie you ask? Well filename cookie gets blocked by mod_security.
		$template->addJsFileToHead('themes/cloud/jquery/js/jquery.ui.tabs.min.js');
		$template->addJsFileToHead('themes/cloud/js/biscuit/jquery.biscuit.js');
		$template->addJsFileToHead('themes/cloud/js/tabs/jquery.tabs.js');
		$template->styleSelect();

	}
}

return 'generalSettingsView';