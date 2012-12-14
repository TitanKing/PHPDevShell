<?php

class userAdminImportView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();
	}
}

return 'userAdminImportView';