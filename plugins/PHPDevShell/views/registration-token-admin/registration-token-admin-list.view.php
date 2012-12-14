<?php

class registrationTokenAdminListView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleFloatHeaders();
		$template->stylePagination();
		$template->styleTables();
		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();

	}
}

return 'registrationTokenAdminListView';

