<?php

/**
 * Controller Class: Handles system control panel.
 * @author Jason Schoeman
 * @return string
 */
class cp extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Load control panel plugin.
		$cp = $this->factory('controlPanel');
		$cp->doPanel();
	}
}

return 'cp';
