<?php

class crudExampleView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleTables();
		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();
	}
}

return 'crudExampleView';