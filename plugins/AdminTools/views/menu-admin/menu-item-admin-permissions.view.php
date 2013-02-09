<?php

class menuItemAdminPermissionsView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();
		$template->styleFloatHeaders();
		$template->styleTables();

		$template->addJsFileToHead("themes/cloud/js/quickfilter/jquery.quickfilter.js");
	}
}

return 'menuItemAdminPermissionsView';
