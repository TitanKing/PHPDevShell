<?php

class simpleWidgetView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleForms();
		$template->styleButtons();
	}
}

return 'simpleWidgetView';