<?php

/**
 * Cronjob Admin List - Update last cron execution.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_updateExeCronQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_cron
		SET
			last_execution = '%s'
			%s
		WHERE
			node_id = '%s'
	";
}

/**
 * Cronjob Admin List - List Cronjob Admin
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_runCronQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			node_id, cron_desc, cron_type, log_cron, last_execution, year, month, day, hour, minute
		FROM
			_db_core_cron
		WHERE
			cron_type != 0
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$cronjobs_list_db = parent::invoke();
		// Set current server timestamp.
		$current_server_timestamp = time();

		// Loop results to check which crons should run on this cycle and which should be skipped.
		if (empty($cronjobs_list_db)) $cronjobs_list_db = array();
		foreach ($cronjobs_list_db as $cronjobs_array) {
			// Assign cronjob variables.
			$cron_ = $cronjobs_array;
			// Check if we have the plugin folder.
			if (!empty($this->navigation->navigation[$cron_['node_id']]['plugin_folder'])) {
				// Assign plugin folder.
				$plugin_folder = $this->navigation->navigation[$cron_['node_id']]['plugin_folder'];
			} else {
				// Assign error handling and message.
				$error[1] = true;
				$this->template->critical(sprintf(__('Cronjob %s (%s) - FAILED - No plugin found for cron node item. Also check access rights for this cron.'), $this->navigation->navigation[$cron_['node_id']]['node_name'], "({$cron_['node_id']})"));
			}
			// Check if we have the cron link.
			if (!empty($this->navigation->navigation[$cron_['node_id']]['node_link'])) {
				// Assign link.
				$node_link = $this->navigation->navigation[$cron_['node_id']]['node_link'];
			} else {
				// Assign error handling and message.
				$error[2] = true;
				$this->template->critical(sprintf(__('Cronjob %s (%s) - FAILED - No link found for cron node item.'), $this->navigation->navigation[$cron_['node_id']]['node_name'], "({$cron_['node_id']})"));
			}
			if (empty($error)) {

				// Log cron if required.
				($cron_['log_cron'] == 1) ? $log_cron = true : $log_cron = false;
				// Start handling cronjobs by type.
				switch ($cron_['cron_type']) {
					case 0:
						// Skip Cron.
						$execute_cron = false;
						break;
					case 1:
						// Calculate once of date.
						$execute_on_date = mktime("{$cron_['hour']}", "{$cron_['minute']}", 0, "{$cron_['month']}", "{$cron_['day']}", "{$cron_['year']}");
						// Check if item should run.
						if ($current_server_timestamp >= $execute_on_date) {
							// Execute Cron.
							$execute_cron = true;
							// Disable Cron.
							$update_cron_type = ', cron_type = 0';
						} else {
							// Skip Cron.
							$execute_cron = false;
							// Dont Disable Cron.
							$update_cron_type = '';
						}
						break;
					case 2:
						// Dont Disable Cron.
						$update_cron_type = false;
						// Calculate seconds.
						$year_in_seconds = $cron_['year'] * 31556926;
						$month_in_seconds = $cron_['month'] * 2629743.83;
						$day_in_seconds = $cron_['day'] * 86400;
						$hour_in_seconds = $cron_['hour'] * 3600;
						$minute_in_seconds = $cron_['minute'] * 60;
						// Total seconds.
						$sec_intervals = $year_in_seconds + $month_in_seconds + $day_in_seconds + $hour_in_seconds + $minute_in_seconds;
						// Check if cron should execute on this cycle.
						if (($current_server_timestamp - $cron_['last_execution']) >= $sec_intervals) {
							// Execute Cron.
							$execute_cron = true;
						} else {
							// Skip Cron.
							$execute_cron = false;
						}
						break;
					default:
						// Skip Cron.
						$execute_cron = false;
						break;
				}
			}
			// Continue executing cron if required.
			if ($execute_cron == true) {
				// Inlude script.

				// Measure execution time.
				$time = microtime();
				$time = explode(' ', $time);
				$time = $time[1] + $time[0];
				$start = $time;

				// Update Cron Runtime.
				$this->db->invokeQuery('PHPDS_updateExeCronQuery', $current_server_timestamp, $update_cron_type, $cron_['node_id']);

				if ($this->core->loadControllerFile($cron_['node_id'], true)) {
					// Lets tell the autoloader where he could also look for a class.
					if (!empty($plugin_folder))
						$this->configuration['plugin_alt'] = $plugin_folder;

					// Ok we do not plugin_alt anymore.
					$this->configuration['plugin_alt'] = false;

					// Do not output any HTML var.
					$HTML = false;

					// Measure execution time.
					$time = microtime();
					$time = explode(' ', $time);
					$time = $time[1] + $time[0];
					$finish = $time;
					$total_time = round(($finish - $start), 4);

					// Output for debug.
					$this->template->ok(sprintf(__('Cronjob %s - EXECUTED taking %s seconds.'), $this->navigation->navigation[$cron_['node_id']]['node_name'], $total_time), false, $log_cron);
				} else {
					// Output for debug.
					$this->template->critical(sprintf(__('Cronjob %s - FAILED - Could not locate/read file (%s), check if your permissions for cron is set to guest or if the file is readable and exists.'), $this->navigation->navigation[$cron_['node_id']]['node_name'], $node_link), false, $log_cron);
				}
			} else {
				// Output for debug.
				$this->template->notice(sprintf(__('Cronjob %s - SKIPPED!'), $this->navigation->navigation[$cron_['node_id']]['node_name']));
			}
			// Unset values that can be duplicated.
			unset($update_cron_type, $plugin_folder, $node_link);
		}
	}
}
