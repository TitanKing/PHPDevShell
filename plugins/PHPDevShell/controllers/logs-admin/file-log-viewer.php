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
		$this->template->heading(__('View Log Files'));

		// Scan directory and list files.
		$files = $this->db->invokeQuery('PHPDS_fileLogOptions');

		if (empty($files)) {
			$this->template->note(sprintf(__('There are no log files written in %s.'), $this->configuration['error']['file_log_dir']));
			$files = array();
		}

		// Query all logs.
		$RESULTS = $this->db->invokeQuery('PHPDS_getAllFileLogsQuery', $files);

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('files', $files);

		$view->set('RESULTS', $RESULTS);

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());

		// Output Template.
		$view->show();
	}
}

return 'SystemLogs';
