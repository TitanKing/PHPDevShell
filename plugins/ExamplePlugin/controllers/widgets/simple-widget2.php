<?php

class widget extends PHPDS_controller
{
	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Load views plugin.
		$view = $this->factory('views');
		
		// Lets do something silly with this widget... like show a logged in as and logout link.
		$name = $this->configuration['user_display_name'];

		// Making it sleep so we can see busy animation...
		// DONT ADD TO SLEEP() WITH REAL MODULES!
		sleep(1);
		
		// Receiving some extra data.
		$more_about = $this->GET('more_about');
		
		// Pass vars.
		$view->set('more_about', $more_about);
		$view->set('name', $name);
		
		// Output to view.
		$view->show();
	}
}

return 'widget';