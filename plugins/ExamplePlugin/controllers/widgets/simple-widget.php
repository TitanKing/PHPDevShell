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
		$logout_page = $this->navigation->buildURL('3682403894', 'logout=1');

		// Making it sleep so we can see busy animation...
		// DONT ADD TO SLEEP() WITH REAL MODULES!
		sleep(1);

		// Receiving some extra data.
		$data = $this->GET('data');
		$moredata = $this->GET('moredata');

		// Pass vars.
		$view->set('name', $name);
		$view->set('moredata', $moredata );
		$view->set('data', $data );
		$view->set('logout_page', $logout_page);

		// Output to view.
		$view->show();
	}
}

return 'widget';