<?php

class NAVIGATION_findMenuQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.menu_id
		FROM
			_db_core_menu_items t1
		WHERE
			t1.alias = '%s'
		OR
			t1.menu_id = '%s'
	";

	protected $singleValue = true;
}

class NAVIGATION_findAliasQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.alias
		FROM
			_db_core_menu_items t1
		WHERE
			t1.menu_id = '%s'
	";

	protected $singleValue = true;
}

class NAVIGATION_extractMenuQuery extends PHPDS_query
{
	protected $sql = "
		SELECT DISTINCT SQL_CACHE
			t1.menu_id, t1.parent_menu_id, t1.menu_name, t1.menu_link, t1.plugin, t1.menu_type, t1.extend, t1.new_window, t1.rank, t1.hide, t1.template_id, t1.alias, t1.layout, t1.params,
			t3.is_parent, t3.type,
			t6.template_folder
		FROM
			_db_core_menu_items t1
		LEFT JOIN
			_db_core_user_role_permissions t2
		ON
			t1.menu_id = t2.menu_id
		LEFT JOIN
			_db_core_menu_structure t3
		ON
			t1.menu_id = t3.menu_id
		LEFT JOIN
			_db_core_templates t6
		ON
			t1.template_id = t6.template_id
		WHERE
			(t2.user_role_id IN (%s))
		ORDER BY
			t3.id
		ASC
	";

	public function invoke($parameters = null)
	{
		$all_user_roles = $parameters[0];
		if (empty($all_user_roles)) throw new PHPDS_Exception('Cannot extract menus when no roles are given.');
		$select_menus = parent::invoke($all_user_roles);

		$navigation = $this->navigation;
		$aburl = $this->configuration['absolute_url'];
		$sef = ! empty($this->configuration['sef_url']);
		//$append = ! empty($this->configuration['url_append']);
		$append = $this->configuration['url_append'];
		$charset = $this->core->mangleCharset($this->charset());
		$father = $this->PHPDS_dependance();

		foreach ($select_menus as $mr) {
			////////////////////////
			// Create menu items. //
			////////////////////////
			$new_menu = array();
			$father->copyArray($mr, $new_menu, array('menu_id', 'parent_menu_id', 'alias', 'menu_link', 'rank', 'hide', 'new_window', 'is_parent', 'type', 'template_folder', 'layout', 'plugin', 'menu_type', 'extend'));
			$new_menu['menu_name'] = $navigation->determineMenuName($mr['menu_name'], $mr['menu_link'], $mr['menu_id'], $mr['plugin']);

			$new_menu['params'] = !empty($mr['params']) ? html_entity_decode($mr['params'], ENT_COMPAT, $charset) : '';
			$new_menu['plugin_folder'] = 'plugins/' . $mr['plugin'] . '/';
			if ($sef && ! empty($mr['alias'])) {
				$navigation->navAlias[$mr['alias']] = $mr['menu_type'] != PHPDS_navigation::node_jumpto_link ? $mr['menu_id'] : $mr['extend'];
				$new_menu['href'] = $aburl . '/' . $mr['alias'].$append;
			} else {
				$new_menu['href'] = $aburl.'/index.php?m='.($mr['menu_type'] != PHPDS_navigation::node_jumpto_link ? $mr['menu_id'] : $mr['extend']);
			}

			// Writing children for single level dropdown.
			if (! empty($mr['parent_menu_id'])) {
				$navigation->child[$mr['parent_menu_id']][] = $mr['menu_id'];
			}

			$navigation->navigation[$mr['menu_id']] = $new_menu;
		}
	}
}
