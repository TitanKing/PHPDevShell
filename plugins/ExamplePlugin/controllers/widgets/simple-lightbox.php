<?php

class lightbox extends PHPDS_controller
{
	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Load views plugin.
		$view = $this->factory('views');
		
		// Receiving some extra data.
		$name = $this->configuration['user_display_name'];
		$data = $this->GET('data');
		
		// Pass vars.
		$view->set('name', $name);
		$view->set('data', $data);
		
		// Output to view.
		$view->show();
	}
}

return 'lightbox';