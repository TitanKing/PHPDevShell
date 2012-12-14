<?php

class ajax extends PHPDS_controller
{
	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Load views plugin.
		$view = $this->factory('views');

		// Making it sleep so we can see busy animation...
		// DONT ADD TO SLEEP() WITH REAL MODULES!
		sleep(1);
		
		// Receiving some extra data.
		$name = $this->configuration['user_display_name'];
		$data = $this->security->get['data'];
		
		// Pass vars.
		$view->set('name', $name);
		$view->set('data', $data);
		
		// Output to view.
		$view->show();
	}
}

return 'ajax';