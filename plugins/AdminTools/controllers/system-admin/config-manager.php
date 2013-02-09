<?php

/**
 * Config Manager: Simple and effective way in handling settings in a registry type format.
 * @author Jason Schoeman
 * @return string
 */
class ConfigManager extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$this->template->heading(__('Configuration Manager'));

		// Action url.
		$self_url_ = $this->navigation->buildURL(false);
		// Should we delete a setting?
		if (!empty($this->security->get['ds'])) {
			// Lets delete the entry.
			$this->db->deleteQuick('_db_core_settings', 'setting_description', $this->security->get['ds']);

			// Show ok deleted.
			$this->template->ok(sprintf(__('Deleted setting entry %s.'), $this->security->get['ds']));
		}
		// We have a save action, lets handle it.
		if (! empty($this->security->post['save'])) {
			$this->db->invokeQuery('PHPDS_writeCoreSettingsQuery');
		}
		// Lets create the list of all the entries.
		$RESULTS = $this->db->invokeQuery('PHPDS_readCoreSettingsQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Values to Template.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);
		$view->set('self_url', $self_url_);

		// Call template.
		$view->show();
	}
}

return 'ConfigManager';