<?php

/**
 * Initiate query invoke command.
 * @param int
 * @return array
 */
class PHPDS_logsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.id, t1.log_type, t1.log_description, t1.log_time
		FROM
			_db_core_logs t1
		WHERE
			t1.user_id = %u
		AND
			t1.user_id != 0
		AND
			t1.log_type IN (1,2,3)
		ORDER BY
			t1.log_time
		DESC
		LIMIT 0, %u
	";

	/**
	 * Loads array of log messages.
	 * @param int $limit_messages
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// create the parameter list for the query
		// we add an argument there, otherwise we could just pass the parameters to the query() method
		$limit_messages = $parameters[0];
		$parameters = array($this->configuration['user_id'], $limit_messages);
		$template = $this->template;
		// send the actual query to the database
		$log_enties = parent::invoke($parameters);

		if (!empty($log_enties)) {
			// ok now the real job, the data is here, no DB-related stuff anymore
			$message_ = array();
			foreach($log_enties as $get_logs_array) {
				$log_type = $get_logs_array['log_type'];
				$log_description = $get_logs_array['log_description'];
				$log_time = $this->core->formatTimeDate($get_logs_array['log_time']);
				// Set messages in correct categories.
				switch ($log_type) {
					// Ok.
					case 1:
						$message_[] = array('description'=>$template->mod->ok($log_description), 'log_time'=>$log_time);
						break;
					// Warning.
					case 2:
						$message_[] = array('description'=>$template->mod->warning($log_description), 'log_time'=>$log_time);
						break;
					// Critical.
					case 3:
						$message_[] = array('description'=>$template->mod->critical($log_description), 'log_time'=>$log_time);
						break;
				}
			}
		}
		if (! empty($message_)) {
			return $message_;
		} else {
			return false;
		}
	}
}

/**
 * Control Panel Model - Draw Control Panel
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_drawCPModel extends PHPDS_query
{
	public $cp = array();

	/**
	 * Initiate query invoke command.
	 * 
	 * @version 1.0.1
	 * 
	 * @date 20120611 (1.0.1) (greg) minor cleanup
	 * 
	 * @param int $parameter unused
	 * @return array array of menu item data
	 */
	public function invoke($parameters = null)
	{
		$navigation = $this->navigation;
		$nav = $navigation->navigation;
		$configuration = $this->configuration;
		$template = $this->template;

		$menu_id = $configuration['m'];
		$this->cp = array();

		$menu = $this->childCP($menu_id);

		if (empty($menu)) {
			$menu = $this->mainCP($menu_id);
		}

		// Loop through all menu items.
		$menu_type = array();
		foreach ($menu as $m) {
			$item = $nav[$m];

			// Get script image/logo.
			$image_url = $template->scriptLogo($item['menu_link'], $item['plugin'], $item['alias'], $item['is_parent']);

			// Open in new window.
			if ($item['new_window'] == 1) {
				$newWindow = 'target="_blank"';
			} else {
				$newWindow = false;
			}

			// Should we highlight
			if ($item['extend'] == "940041356" || $m == "940041356") {
				$class = 'ui-icon ui-icon-plusthick left';
			} else {
				$class = '';
			}
			// Set url.
			$url = $navigation->buildURL($item['menu_id'], '', true);

			// Check if menu item is a parent and assign correct div class.
			$menu_type[] = array('class'=>$class, 'url'=>$url, 'newWindow'=>$newWindow, 'menu_name'=>$item['menu_name'], 'image_url'=>$image_url);

		}

		return $menu_type;
	}

	/**
	 * Compiles control panel items in order.
	 *
	 * @return string
	 * @author Jason Schoeman
	 */
	public function mainCP ()
	{
		$navi = $this->navigation;
		$nav = $navi->navigation;
		$configuration = $this->configuration;

		if (! empty($nav)) {
			if (! empty($nav[$configuration['m']]['is_parent'])) {
				$menu_group = $configuration['m'];
			} else {
				if (! empty($nav[$configuration['m']]['parent_menu_id'])) {
					$menu_group = $nav[$configuration['m']]['parent_menu_id'];
				} else {
					$menu_group = '0';
				}
			}

			foreach ($nav as $m) {
				if ((string) $nav[$m['menu_id']]['parent_menu_id'] == (string) $menu_group) {
					if ($m['is_parent'] == 1) {
						if ($this->showItem($m['hide'], $m['menu_type']))
							$this->cp[$m['menu_id']] = $m['menu_id'];
					} else {
						if ($this->showItem($m['hide'], $m['menu_type']))
							$this->cp[$m['menu_id']] = $m['menu_id'];
					}
				}
			}
			
			return $this->cp;
		}
	}

	/**
	 * Simple method to find all children of a menu item.
	 *
	 * @param int $menu_id
	 */
	public function childCP ($menu_id = null, $level=1)
	{
		$menu = false;
		$configuration = $this->configuration;
		$navi = $this->navigation;
		$nav = $navi->navigation;

		if (! empty($navi->child[$menu_id])) {
			$child = $navi->child[$menu_id];
			foreach ($child as $m) {
				if ($nav[$m]['is_parent'] == 1) {
					if ($this->showItem($nav[$m]['hide'], $nav[$m]['menu_type']))
						$this->cp[$m] = $m;
					// Should we limit levels...
					if ($nav[$m]['extend'] == "940041356" || $m == "940041356")
						$dash = 2;
					else
						$dash = 1;
					if ($level == 1) {
						$this->childCP($m, $dash);
					}
				} else {
					if ($this->showItem($nav[$m]['hide'], $nav[$m]['menu_type']))
						$this->cp[$m] = $m;
				}
			}
		}
		
		return $this->cp;
	}

	/**
	 * Returns true if menu should show.
	 *
	 * @param integer $hide_type
	 * @param integer $menu_type
	 */
	public function showItem ($hide_type, $type=null)
	{
		if ($hide_type == 1 || $hide_type == 2 || $hide_type == 4 || $type == 6 || $type == 9 || $type == 10 || $type == 11) {
			return false;
		} else {
			return true;
		}
	}
}