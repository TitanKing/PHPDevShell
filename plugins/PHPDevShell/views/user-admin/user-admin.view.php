<?php

class userAdminPendingView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleButtons();
		$template->styleForms();
		$template->validateForms();
		$template->styleSelect();
	}
}

return 'userAdminPendingView';