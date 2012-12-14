<?php

/**
 * Contains methods to handle pagination, searching and filtering.
 * PHPMailer could also be used.
 * @author Jason Schoeman
 */
class controlPanel extends PHPDS_dependant
{
	/**
	 * Creates cool easy to navigate control panel.
	 */
	public function doPanel($behaviour = 'deprecated')
	{
		// Get settings values.
		$setting = $this->db->getSettings(array('limit_messages'), 'PHPDevShell');

		// Check what should be shown.
		if (!empty($setting['limit_messages'])) {
			// Should favorite message items be show?
			if (!empty($setting['limit_messages'])) {
				$message_ = $this->db->invokeQuery('PHPDS_logsQuery', $setting['limit_messages']);
			}
			// Generate system control panel.
			$panel = true;
		} else {
			$panel = false;
			$message_ = '';
		}

		// Loop through all menu items.
		$menu_type = $this->db->invokeQuery('PHPDS_drawCPModel');

		// Load views.
		$view = $this->factory('views');

		// Set Values.
		$view->set('menu_type', $menu_type);
		$view->set('message_', $message_);
		$view->set('panel', $panel);

		// Output Template.
		$view->show('control-panel.tpl', 'ControlPanel');
	}
	
}