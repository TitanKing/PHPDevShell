<?php

class cronAdminListView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleFloatHeaders();
		$template->styleTables();
		$template->styleButtons();

		$this->template->addJsFileToHead('themes/cloud/js/quickfilter/jquery.quickfilter.js');
	}
}

return 'cronAdminListView';
