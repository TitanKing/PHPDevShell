<?php

class editPreferencesView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();
		$template->styleSelect();
	}
}

return 'editPreferencesView';