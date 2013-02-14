<?php

/**
 * Cronjob Admin List - List Cronjob Admin
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_listCronjobAdminQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id, t1.node_name, t1.node_type, t1.node_link, t1.plugin,
			t2.node_id as cron_id, t2.cron_desc, t2.cron_type, t2.log_cron, t2.last_execution, t2.year, t2.month, t2.day, t2.hour, t2.minute
		FROM
			_db_core_node_items t1
		%s
			_db_core_cron t2
		ON
			t2.node_id = t1.node_id
		WHERE
			t1.node_type = 8
		OR
			t1.node_id IS NULL
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;

		$orphan_ = $parameters[0];
		$page_edit = $navigation->buildURL('1016054546', 'ec=');
		$dc = $navigation->buildURL(false, 'dc=');

		if ($orphan_ == false) {
			$orphan_sql = 'RIGHT JOIN';
		} else {
			$orphan_sql = 'LEFT JOIN';
		}

		// Get all available cronjobs.
		$cronjobs_list_db = parent::invoke($orphan_sql);

		// Icons.
		$broken_cron_icon = $template->icon('cross-circle', __('Broken Cron Orphan!'));
		$delete_cron_icon = $template->icon('clock--minus', __('Delete Cron'));
		$edit_cron_icon = $template->icon('clock--pencil', __('Edit Cron'));
		$run_cron_icon = $template->icon('clock--arrow', __('Run Cron'));
		$do_log_cron_icon = $template->icon('clipboard-task', __('Yes Log Cron'));
		$dont_log_cron_icon = $template->icon('clipboard--exclamation', __('Dont Log Cron'));
		$disable_cron_icon = $template->icon('clock--exclamation', __('Disable Cron'));
		$never_run_cron_icon = $template->icon('clock', __('Never Run Cron'));
		$run_cron_once_icon = $template->icon('clock-select-remain', __('Run Cron Once'));
		$repeat_cron_icon = $template->icon('clock-history', __('Repeat Cron'));
		$missed_cron_icon = $template->icon('clock--exclamation', __('Missed or Never Ran'));

		// Create cron list.
		if (empty($cronjobs_list_db)) $cronjobs_list_db = array();
		foreach ($cronjobs_list_db as $edit) {
			// Create cron rows.
			if (empty($edit['node_link'])) {
				$edit['cron_name'] = $broken_cron_icon;
				$edit['plugin'] = $broken_cron_icon;
				if (!empty($edit['node_id'])) {
					$edit['node_id'] = $edit['cron_id'];
				} else {
					$edit['node_id'] = '';
				}
				// script-import
				$edit_ = '<a href="' . $dc . $edit['node_id'] . '" class="button">' . $delete_cron_icon . '</a>';
				$run_ = $edit_;
			} else {
				$edit['cron_name'] = $navigation->determineNodeName($edit['node_name'], $edit['node_link'], $edit['node_id']);
				$edit_ = "<a href=\"{$page_edit}{$edit['node_id']}\" class=\"button\">{$edit_cron_icon}</a>";
				$run_ = "<a href=\"{$navigation->buildURL($edit['node_id'], false, true)}\" {$core->confirmLink(sprintf(__('Are you sure you want to EXECUTE cronjob %s?'), $edit['cron_name']))} class=\"button\">{$run_cron_icon}</a>";
			}
			// Log Cron Icon.
			($edit['log_cron'] == 1) ? $log_cron_icon = $do_log_cron_icon : $log_cron_icon = $dont_log_cron_icon;
			// Define.
			$hide_ = false;
			// Format expectancy and type icon.
			switch ($edit['cron_type']) {
				case 0:
					$type_icon = $disable_cron_icon;
					$expectancy = $never_run_cron_icon;
					break;
				case 1:
					$type_icon = $run_cron_once_icon;
					// Check empty values.
					if (empty($edit['hour'])) $edit['hour'] = 0;
					if (empty($edit['minute'])) $edit['minute'] = 0;
					$edit['second'] = 0;
					// Create confirm date.
					if (empty($error[2])) {
						$expectancy = sprintf(__('Once on %s'), $core->formatTimeDate(mktime($edit['hour'], $edit['minute'], $edit['second'], $edit['month'], $edit['day'], $edit['year'])));
					}
					break;
				case 2:
					$type_icon = $repeat_cron_icon;
					// Convert to hours.
					$year_in_hr = ($edit['year'] * 8765.81277);
					$month_in_hr = ($edit['month'] * 730.484398);
					$day_in_hr = ($edit['day'] * 24);
					$hr_in_hr = $edit['hour'];
					$minutes_in_hr = ($edit['minute'] / 60);
					// hours
					$hours = round($year_in_hr + $month_in_hr + $day_in_hr + $hr_in_hr + $minutes_in_hr, 2);
					// Create total Months/Days/Hours/Minutes.
					if ($hours >= 730.484398) {
						// months
						$months = round($hours / 730.484398, 2);
						$expectancy = sprintf(__('Every %s month(s)'), $months);
					} else if ($hours >= 24) {
						// days
						$days = round($hours / 24, 2);
						$expectancy = sprintf(__('Every %s day(s)'), $days);
					} else if ($hours >= 1) {
						$expectancy = sprintf(__('Every %s hour(s)'), $hours);
					} else if ($hours < 1) {
						// minutes
						$minutes = ($hours * 60);
						$expectancy = sprintf(__('Every %s minute(s)'), $minutes);
					}
					break;
				default:
					$type_icon = $disable_cron_icon;
					$expectancy = $never_run_cron_icon;
					break;
			}
			// Format last execution.
			if (!empty($edit['last_execution'])) {
				$last_execution_format = $core->formatTimeDate($edit['last_execution']);
			} else {
				$last_execution_format = $missed_cron_icon;
			}
			$RESULTS[] = array(
				'hide_' => $hide_,
				'e' => $edit,
				'type_icon' => $type_icon,
				'log_cron_icon' => $log_cron_icon,
				'last_execution_format' => $last_execution_format,
				'expectancy' => $expectancy,
				'edit' => $edit_,
				'run' => $run_,
			);
		}
		if ($RESULTS) return $RESULTS;
		else return false;
	}
}