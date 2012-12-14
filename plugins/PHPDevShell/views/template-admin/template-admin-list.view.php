<?php

class configManagerView extends PHPDS_view
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

		$this->template->addJsFileToHead('themes/cloud/js/quickfilter/jquery.quickfilter.js');
	}
}

return 'configManagerView';