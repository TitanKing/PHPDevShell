<?php

/**
 * Menu Access Logs - Reset logs internal pointer.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_resetMenuAccessLogsQuery extends PHPDS_query
{
	protected $sql = "
		ALTER TABLE
			_db_core_menu_access_logs
		AUTO_INCREMENT = 0;
	";
}

/**
 * Menu Access Logs - Clear Menu Access Logs
 * @author Jason Schoeman, Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
 *
 */
class PHPDS_clearMenuAccessLogsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_menu_access_logs
	";

	/**
	 * Initiate query invoke command.
	 */
	public function invoke($parameters = null)
	{
		// Delete all logs.
		parent::invoke();

		// Reset auto increment counter.
		$this->db->invokeQuery('PHPDS_resetMenuAccessLogsQuery');

		$this->template->ok(__('Logs table cleared.'));
	}
}

/**
 * Menu Access Logs - Get All Menu Access Logs
 * @author Jason Schoeman, Jason Schoeman [titan@phpdevshell.org], Ross Kuyper.
 *
 */
class PHPDS_getAllMenuAccessLogs extends PHPDS_query
{
	protected $sql = "
        SELECT
            t1.log_id, t1.menu_id, t1.user_id, t1.timestamp,
            t2.menu_name, t2.menu_link,
            t3.user_display_name
        FROM
            _db_core_menu_access_logs t1
        LEFT JOIN
            _db_core_menu_items t2
        ON
            t1.menu_id = t2.menu_id
        LEFT JOIN
            _db_core_users t3
        ON
            t1.user_id = t3.user_id
    ";

	/**
	 * Initiate query invoke command.
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;

		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('Log Type') => 't1.menu_id',
			_('Log ID') => 't1.log_id',
			_('Menu Name') => 't2.menu_name',
			_('Menu ID') => 't1.menu_id',
			_('Name') => 't3.user_display_name',
			_('Log Time') => 't1.timestamp'
		);
		$pagination->dateColumn = 't1.timestamp';
		$get_logs = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$log_type_not_found_icon = $template->icon('eye--exclamation', __('Page not Found!'));
		$log_type_found_icon = $template->icon('eye', __('Viewed'));

		foreach ($get_logs as $get_logs_array) {
			// Build values.
			$log_id = $get_logs_array['log_id'];
			$menu_link = (string) $get_logs_array['menu_link'];
			$menu_name = $navigation->determineMenuName($get_logs_array['menu_name'], $menu_link, $get_logs_array['menu_id']);
			$menu_id = $get_logs_array['menu_id'];
			$user_display_name = $get_logs_array['user_display_name'];
			$log_time = $core->formatTimeDate($get_logs_array['timestamp']);
			// Do we have a user display name?
			if (empty($user_display_name)) {
				$user_display_name = __('Guest User');
			}
			// If we dont have a menu item, the page is not know!
			if (empty($menu_name)) {
				$menu_name = __('Page not found!');
				$log_type = $log_type_not_found_icon;
			} else {
				$log_type = $log_type_found_icon;
			}
			$RESULTS['list'][] = array(
				'log_id' => $log_id,
				'log_type' => $log_type,
				'menu_name_url' => "<a href=\"{$navigation->buildURL($menu_id)}\">$menu_name</a>",
				'menu_id' => $menu_id,
				'user_display_name' => $user_display_name,
				'log_time' => $log_time
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