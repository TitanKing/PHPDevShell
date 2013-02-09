<?php

class fileLogViewerView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleFloatHeaders();
		$template->styleTables();
		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();
	}
}

return 'fileLogViewerView';
