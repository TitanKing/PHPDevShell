<?php

class FileuploadLogs extends PHPDS_controller
{

	/**
	 * Hooks Administration
	 * @author Jason Schoeman, Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 29 June 2010
	 */
	public function execute()
	{
		$this->template->heading(_('Files Uploaded Logs'));

		// Load class.
		$filemanager = $this->factory('fileManager');

		// Clear database if requested.
		if (!empty($this->security->post['clear']) && ($this->configuration['user_role'] == $this->configuration['root_role'])) {
			$this->db->invokeQuery("PHPDS_clearFileuploadLogsQuery");
		}
		// Delete a file.
		if (!empty($this->security->get['df'])) {
			$this->db->invokeQuery("PHPDS_deleteFileuploadQuery", $this->security->get['df']);
		}
		// Query all logs.
		$RESULTS = $this->db->invokeQuery('PHPDS_getAllUploadLogsQuery', $filemanager);

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

return 'FileuploadLogs';