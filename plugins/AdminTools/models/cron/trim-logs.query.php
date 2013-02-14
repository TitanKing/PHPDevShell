<?php

/**
 * Trim Logs - Count general logs.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_countLogsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(*)
		FROM
			_db_core_logs
	";
	protected $singleValue = true;
}

/**
 * Trim Logs - Delete general logs.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_deleteLogsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_logs
		ORDER BY
			log_time
		ASC
		LIMIT
			%s
	";
}

/**
 * Trim Logs - Count node access logs.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_countAccessLogsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(*)
		FROM
			_db_core_node_access_logs
	";
	protected $singleValue = true;
}

/**
 * Trim Logs - Delete node access logs.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_deleteAccessLogsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_node_access_logs
		ORDER BY
			timestamp
		ASC
		LIMIT
			%s
	";
}

/**
 * Trim Logs - Trim Logs
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_trimLogsQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Get trimming setting.
		$settings = $this->db->getSettings(array('trim_logs'), 'AdminTools');

		/////////////////////////////////////////////////////
		/////////////// GENERAL LOGS ////////////////////////
		/////////////////////////////////////////////////////
		// Check how many general logs we have...
		$count_general_logs = $this->db->invokeQuery('PHPDS_countLogsQuery');

		// Check if anything needs to be trimmed.
		if ($count_general_logs > $settings['trim_logs']) {
			// Number that needs trimming from general logs.
			$trim_count_general = $count_general_logs - $settings['trim_logs'];
			// Trim general records!
			$this->db->invokeQuery('PHPDS_deleteLogsQuery', $trim_count_general);

			// Show ok message!
			$job_status_general = __('I have trimmed required <b>general</b> logs.');
		} else {
			$trim_count_general = 0;
			$job_status_general = __('Nothing to trim in <b>general</b> logs');
		}

		// How many general records.
		$general_records = sprintf(__('We have <b>%s</b> <b>general</b> logs.'), $count_general_logs);
		$trim_records_general = sprintf(__('I need to trim <b>%s</b> <b>general</b> logs.'), $trim_count_general);

		/////////////////////////////////////////////////////
		/////////////// ACCESS LOGS /////////////////////////
		/////////////////////////////////////////////////////
		// Check how many access logs we have...
		$count_access_logs = $this->db->invokeQuery('PHPDS_countAccessLogsQuery');

		// Check if anything needs to be trimmed.
		if ($count_access_logs > $settings['trim_logs']) {
			// Number that needs trimming from access logs.
			$trim_count_access = $count_access_logs - $settings['trim_logs'];

			// Trim access records!
			$this->db->invokeQuery('PHPDS_deleteAccessLogsQuery', $trim_count_access);

			// Show ok message!
			$job_status_access = __('I have trimmed required <b>access</b> logs.');
		} else {
			$trim_count_access = 0;
			$job_status_access = __('Nothing to trim in <b>access</b> logs');
		}
		// How many access records.
		$access_records = sprintf(__('We have <b>%s</b> <b>access</b> logs.'), $count_access_logs);
		$trim_records_access = sprintf(__('I need to trim <b>%s</b> <b>access</b> logs.'), $trim_count_access);

		return array(
			'general_records' => $general_records,
			'trim_records_general' => $trim_records_general,
			'job_status_general' => $job_status_general,
			'access_records' => $access_records,
			'trim_records_access' => $trim_records_access,
			'job_status_general' => $job_status_general,
			'access_records' => $access_records,
			'trim_records_access' => $trim_records_access,
			'job_status_access' => $job_status_access
		);
	}
}