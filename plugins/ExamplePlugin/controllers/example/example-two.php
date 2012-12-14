<?php
// In example 2, we load the view without using the Smarty plugin, just a view in good old PHP.

class exampleTwo extends PHPDS_controller
{
	/**
	 * We always start by overriding the execute method for the controller.
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Lets do it without Smarty.
		$title = "Some Title";
		$some_text = "This is some text to show that you can have a view in PHP.";

		PU_printr($this->configuration);

		// Load view.
		// View will be looking for in plugins/ExamplePlugin/views/example/example-two.tpl.php notice its related to controller.
		include_once $this->template->getTpl();
	}
}

return 'exampleTwo';

