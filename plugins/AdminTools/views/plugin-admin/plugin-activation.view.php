<?php

class pluginActivationView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleButtons();
		$template->styleTables();
		
		// Require JS.
		$template->addJsFileToHead("themes/cloud/js/showhide/jquery.showhide.js");
		$template->addJsFileToHead("themes/cloud/js/quickfilter/jquery.quickfilter.js");

	}
}

return 'pluginActivationView';


