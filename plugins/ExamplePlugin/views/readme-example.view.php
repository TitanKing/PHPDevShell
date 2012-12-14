<?php

class readmeExampleView extends PHPDS_view
{
	public function execute()
	{
		$template = $this->template;

		$template->styleTables();
		$template->styleForms();
		$template->validateForms();
		$template->styleButtons();

		$var = $this->helloWorld();
		echo "<h2>{$var[0]} {$var[1]}</h2>";
		echo "<h3>{$this->get('foo')}</h3>";
	}

	public function helloWorld()
	{
		return $this->get('other');
	}
}

return 'readmeExampleView';
