<?php

class SystemLogs extends PHPDS_controller
{

	/**
	 * Allows authorized personnel to view logs and to monitor user activities on the system
	 * @author Jason Schoeman, Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 01 July 2010
	 */
	public function execute()
	{
		$this->template->heading(__('General System Logs'));

		// Clear database if requested.
		if (!empty($this->security->post['clear']) && ($this->configuration['user_role'] == $this->configuration['root_role'])) {
			$this->db->invokeQuery('PHPDS_deleteAllSystemLogsQuery');
		}
		// Query all logs.
		$RESULTS = $this->db->invokeQuery('PHPDS_getAllSystemLogsQuery');

		// Only root users should be able to delete logs.
		($this->configuration['user_role'] == $this->configuration['root_role']) ? $DELETE_BUTTON = true : $DELETE_BUTTON = false;

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		// Set Button.
		$view->set('delete_all_logs', __('Delete all logs'));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('DELETE_BUTTON', $DELETE_BUTTON);

		// Output Template.
		$view->show();
	}
}

return 'SystemLogs';