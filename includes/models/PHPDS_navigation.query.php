<?php

class NAVIGATION_findNodeQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id
		FROM
			_db_core_node_items t1
		WHERE
			t1.alias = '%s'
		OR
			t1.node_id = '%s'
	";

	protected $singleValue = true;
}

class NAVIGATION_findAliasQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.alias
		FROM
			_db_core_node_items t1
		WHERE
			t1.node_id = '%s'
	";

	protected $singleValue = true;
}

class NAVIGATION_extractNodeQuery extends PHPDS_query
{
	protected $sql = "
		SELECT DISTINCT SQL_CACHE
			t1.node_id, t1.parent_node_id, t1.node_name, t1.node_link, t1.plugin, t1.node_type, t1.extend, t1.new_window, t1.rank, t1.hide, t1.template_id, t1.alias, t1.layout, t1.params,
			t3.is_parent, t3.type,
			t6.template_folder
		FROM
			_db_core_node_items t1
		LEFT JOIN
			_db_core_user_role_permissions t2
		ON
			t1.node_id = t2.node_id
		LEFT JOIN
			_db_core_node_structure t3
		ON
			t1.node_id = t3.node_id
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
		if (empty($all_user_roles)) throw new PHPDS_Exception('Cannot extract nodes when no roles are given.');
		$select_nodes = parent::invoke($all_user_roles);

		$navigation = $this->navigation;
		$aburl = $this->configuration['absolute_url'];
		$sef = ! empty($this->configuration['sef_url']);
		//$append = ! empty($this->configuration['url_append']);
		$append = $this->configuration['url_append'];
		$charset = $this->core->mangleCharset($this->charset());
		$father = $this->PHPDS_dependance();

		foreach ($select_nodes as $mr) {
			////////////////////////
			// Create node items. //
			////////////////////////
			$new_node = array();
			$father->copyArray($mr, $new_node, array('node_id', 'parent_node_id', 'alias', 'node_link', 'rank', 'hide', 'new_window', 'is_parent', 'type', 'template_folder', 'layout', 'plugin', 'node_type', 'extend'));
			$new_node['node_name'] = $navigation->determineNodeName($mr['node_name'], $mr['node_link'], $mr['node_id'], $mr['plugin']);

			$new_node['params'] = !empty($mr['params']) ? html_entity_decode($mr['params'], ENT_COMPAT, $charset) : '';
			$new_node['plugin_folder'] = 'plugins/' . $mr['plugin'] . '/';
			if ($sef && ! empty($mr['alias'])) {
				$navigation->navAlias[$mr['alias']] = $mr['node_type'] != PHPDS_navigation::node_jumpto_link ? $mr['node_id'] : $mr['extend'];
				$new_node['href'] = $aburl . '/' . $mr['alias'].$append;
			} else {
				$new_node['href'] = $aburl.'/index.php?m='.($mr['node_type'] != PHPDS_navigation::node_jumpto_link ? $mr['node_id'] : $mr['extend']);
			}

			// Writing children for single level dropdown.
			if (! empty($mr['parent_node_id'])) {
				$navigation->child[$mr['parent_node_id']][] = $mr['node_id'];
			}

			$navigation->navigation[$mr['node_id']] = $new_node;
		}
	}
}
