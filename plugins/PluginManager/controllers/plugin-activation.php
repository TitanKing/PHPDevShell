<?php

class PluginActivation extends PHPDS_controller
{

	/**
	 * Activates a plugin
	 * @author Jason Schoeman
	 * @since 06 July 2010
	 */
	public function execute()
	{
		// Require plugin manager ////////////////////////////////////////////////////////////////////////////////////////////////
		$pluginmanager = $this->factory('pluginManager');
		$template = $this->template;
		$log[] = '';

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Header information
		$template->heading(__('Plugin Activation'));

		// Plugin activation starts.
		if (isset($this->security->post) && $this->user->isRoot()) {
			$plugin = $this->security->post['plugin'];
			/////////////////////////////////////////////////////////////////////
			// When save is submitted... ////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////
			// Install   = 1 ////////////////////////////////////////////////////
			// Uninstall = 2 ////////////////////////////////////////////////////
			// Reinstall = 3 ////////////////////////////////////////////////////
			// Upgrade   = 4 ////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////
			if (isset($this->security->post['install'])) {
				// Execute plugin method.
				$pluginmanager->setPlugin($plugin, 'install');
			} else if (isset($this->security->post['uninstall'])) {
				// Execute plugin method.
				$pluginmanager->setPlugin($plugin, 'uninstall');
			} else if (isset($this->security->post['reinstall'])) {
				// Execute plugin method.
				$pluginmanager->setPlugin($plugin, 'reinstall');
			} else if (isset($this->security->post['upgrade'])) {
				// Execute plugin method.
				$pluginmanager->setPlugin($plugin, 'upgrade', $this->security->post['version']);
			} else if (isset($this->security->post['auto_upgrade'])) {
				// Execute plugin method.
				$pluginmanager->setPlugin($plugin, 'auto_upgrade');
			} else if (isset($this->security->post['set_logo'])) {
				// Execute plugin method.
				$pluginmanager->setPlugin($plugin, 'set_logo');
			}

			// Plugin log
			if (! empty($pluginmanager->log)) {
				$log["{$plugin}"] = $pluginmanager->log;
			} else {
				$log["{$plugin}"] = '';
			}

			/////////////////////////////////////////////////////////////////////
			// End save is submitted... /////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////
		}
		/////////////////////////////////////////////////
		// Call current plugins status from database. ///
		/////////////////////////////////////////////////
		// Read plugin directory.
		$RESULTS = $this->db->invokeQuery('PHPDS_readPluginsQuery', $this->db->invokeQuery('PHPDS_currentPluginStatusQuery'));

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);
		$view->set('log', $log);
		$view->set('logtext', $this->template->note(__('Dropdown to view log'), 'return'));

		// Output Template.
		$view->show();
	}
}

return 'PluginActivation';
