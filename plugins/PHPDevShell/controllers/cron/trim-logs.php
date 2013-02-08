<?php

class TrimLogs extends PHPDS_controller
{

	/**
	 * Trims logs from the database.
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 24 June 2010
	 */
	public function execute()
	{
		$this->template->heading(__('Trim Logs'));
		$this->template->info(__('Trim logs to the given amount in the config gui, this helps keeping your database manageable.'));

		$trim_results = $this->db->invokeQuery('PHPDS_trimLogsQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Values.
		$view->set('general_records', $trim_results['general_records']);
		$view->set('trim_records_general', $trim_results['trim_records_general']);
		$view->set('job_status_general', $trim_results['job_status_general']);
		$view->set('access_records', $trim_results['access_records']);
		$view->set('trim_records_access', $trim_results['trim_records_access']);
		$view->set('job_status_general', $trim_results['job_status_general']);
		$view->set('access_records', $trim_results['access_records']);
		$view->set('trim_records_access', $trim_results['trim_records_access']);
		$view->set('job_status_access', $trim_results['job_status_access']);

		// Output Template.
		$view->show();
	}
}

return 'TrimLogs';