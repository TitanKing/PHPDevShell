<?php

/**
 * System Logs - Resets logs internal pointer.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_resetSystemLogsQuery extends PHPDS_query
{
	protected $sql = "
		ALTER TABLE
			_db_core_logs
		AUTO_INCREMENT = 0;
	";
}

/**
 * System Logs - Delete logs.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_deleteAllSystemLogsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_logs
	";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Delete all logs.
		parent::invoke();

		// Reset auto increment counter.
		$this->db->invokeQuery('PHPDS_resetSystemLogsQuery');

		$this->template->ok(__('Logs table cleared.'));
	}
}

/**
 * System Logs - Get All System Logs
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_getAllSystemLogsQuery extends PHPDS_query
{
	/**
	 * Pagination for logs.
	 * @var array
	 */
	protected $sql = "
        SELECT
            t1.id, t1.log_type, t1.log_description, t1.log_time, t1.user_id, t1.user_display_name, t1.node_id, t1.file_name, t1.node_name, t1.user_ip
        FROM
            _db_core_logs t1
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;

		// Initiate pagination plugin.
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('Type of log (1,2,3)') => 't1.log_type',
			_('Node Link') => 't1.node_id',
			_('Description') => 't1.log_description',
			_('Node ID') => 't1.node_id',
			_('In File') => 't1.file_name',
			_('User ID') => 't1.user_id',
			_('User') => 't1.user_display_name',
			_('IP') => 't1.user_ip',
			_('Time') => 't1.log_time');
		$pagination->dateColumn = 't1.log_time';
		$get_logs = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$log_type_1_icon = $template->icon('tick', __('Success Log'));
		$log_type_2_icon = $template->icon('exclamation--frame', __('Warning Log'));
		$log_type_3_icon = $template->icon('cross-script', __('Critical Log'));
		$log_type_4_icon = $template->icon('key', __('User Logged In'));
		$log_type_5_icon = $template->icon('key--arrow', __('User Logged Out'));

		foreach ($get_logs as $get_logs_array) {
			$id = $get_logs_array['id'];
			$log_type = $get_logs_array['log_type'];
			$log_description = $get_logs_array['log_description'];
			$log_time = $get_logs_array['log_time'];
			$user_id = $get_logs_array['user_id'];
			$user_display_name = $get_logs_array['user_display_name'];
			$node_id = $get_logs_array['node_id'];
			$file_name = $get_logs_array['file_name'];
			$node_name = $get_logs_array['node_name'];
			$user_ip = $get_logs_array['user_ip'];
			// Write log types out/
			switch ($log_type) {
				case 1:
					$log_type = $log_type_1_icon;
					break;
				case 2:
					$log_type = $log_type_2_icon;
					break;
				case 3:
					$log_type = $log_type_3_icon;
					break;
				case 4:
					$log_type = $log_type_4_icon;
					break;
				case 5:
					$log_type = $log_type_5_icon;
					break;
			}
			$RESULTS['list'][] = array(
				'log_type' => $log_type,
				'node_name_url' => "<a href=\"{$navigation->buildURL($node_id)}\">$node_name</a>",
				'log_description' => $log_description,
				'node_id' => $node_id,
				'file_name' => $file_name,
				'user_id' => $user_id,
				'user_display_name' => $user_display_name,
				'user_ip' => $user_ip,
				'log_time_convert' => $core->formatTimeDate($log_time)
			);
		}
		if (! empty($RESULTS['list'])) {
			return $RESULTS;
		} else {
			$RESULTS['list'] = array();
			return $RESULTS;
		}
	}
}