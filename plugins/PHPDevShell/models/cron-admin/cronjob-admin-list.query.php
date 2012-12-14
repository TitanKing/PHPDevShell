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
			t1.menu_id, t1.menu_name, t1.menu_type, t1.menu_link, t1.plugin,
			t2.menu_id as cron_id, t2.cron_desc, t2.cron_type, t2.log_cron, t2.last_execution, t2.year, t2.month, t2.day, t2.hour, t2.minute
		FROM
			_db_core_menu_items t1
		%s
			_db_core_cron t2
		ON
			t2.menu_id = t1.menu_id
		WHERE
			t1.menu_type = 8
		OR
			t1.menu_id IS NULL
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
		$broken_cron_icon = $template->icon('cross-circle', _('Broken Cron Orphan!'));
		$delete_cron_icon = $template->icon('clock--minus', _('Delete Cron'));
		$edit_cron_icon = $template->icon('clock--pencil', _('Edit Cron'));
		$run_cron_icon = $template->icon('clock--arrow', _('Run Cron'));
		$do_log_cron_icon = $template->icon('clipboard-task', _('Yes Log Cron'));
		$dont_log_cron_icon = $template->icon('clipboard--exclamation', _('Dont Log Cron'));
		$disable_cron_icon = $template->icon('clock--exclamation', _('Disable Cron'));
		$never_run_cron_icon = $template->icon('clock', _('Never Run Cron'));
		$run_cron_once_icon = $template->icon('clock-select-remain', _('Run Cron Once'));
		$repeat_cron_icon = $template->icon('clock-history', _('Repeat Cron'));
		$missed_cron_icon = $template->icon('clock--exclamation', _('Missed or Never Ran'));

		// Create cron list.
		if (empty($cronjobs_list_db)) $cronjobs_list_db = array();
		foreach ($cronjobs_list_db as $edit) {
			// Create cron rows.
			if (empty($edit['menu_link'])) {
				$edit['cron_name'] = $broken_cron_icon;
				$edit['plugin'] = $broken_cron_icon;
				if (!empty($edit['menu_id'])) {
					$edit['menu_id'] = $edit['cron_id'];
				} else {
					$edit['menu_id'] = '';
				}
				// script-import
				$edit_ = '<a href="' . $dc . $edit['menu_id'] . '" class="button">' . $delete_cron_icon . '</a>';
				$run_ = $edit_;
			} else {
				$edit['cron_name'] = $navigation->determineMenuName($edit['menu_name'], $edit['menu_link'], $edit['menu_id']);
				$edit_ = "<a href=\"{$page_edit}{$edit['menu_id']}\" class=\"button\">{$edit_cron_icon}</a>";
				$run_ = "<a href=\"{$navigation->buildURL($edit['menu_id'], false, true)}\" {$core->confirmLink(sprintf(_('Are you sure you want to EXECUTE cronjob %s?'), $edit['cron_name']))} class=\"button\">{$run_cron_icon}</a>";
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
						$expectancy = sprintf(_('Once on %s'), $core->formatTimeDate(mktime($edit['hour'], $edit['minute'], $edit['second'], $edit['month'], $edit['day'], $edit['year'])));
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
						$expectancy = sprintf(_('Every %s month(s)'), $months);
					} else if ($hours >= 24) {
						// days
						$days = round($hours / 24, 2);
						$expectancy = sprintf(_('Every %s day(s)'), $days);
					} else if ($hours >= 1) {
						$expectancy = sprintf(_('Every %s hour(s)'), $hours);
					} else if ($hours < 1) {
						// minutes
						$minutes = ($hours * 60);
						$expectancy = sprintf(_('Every %s minute(s)'), $minutes);
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