<?php

class MenuAccessLogs extends PHPDS_controller
{

	/**
	 * Menu Access Logs
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 29 June 2010
	 */
	public function execute()
	{
		$this->template->heading(_('Menu Access Logs'));

		// Clear database if requested.
		if (!empty($this->security->post['clear']) && ($this->configuration['user_role'] == $this->configuration['root_role'])) {
			// Delete all logs.
			$this->db->invokeQuery('PHPDS_clearMenuAccessLogsQuery');
		}
		$RESULTS = $this->db->invokeQuery('PHPDS_getAllMenuAccessLogs');

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
		$view->set('delete_all_logs', _('Delete all logs'));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('DELETE_BUTTON', $DELETE_BUTTON);

		// Output Template.
		$view->show();
	}
}

return 'MenuAccessLogs';
