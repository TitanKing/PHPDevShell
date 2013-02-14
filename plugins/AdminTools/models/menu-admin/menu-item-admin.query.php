<?php

/**
 * Node Item Admin - Node Item Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAdminNodeItemPermissionsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id
		FROM
			_db_core_user_role_permissions t1
		WHERE
			t1.node_id = '%s'
		ORDER BY
			t1.user_role_id
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$permissions_query = parent::invoke($parameters[0]);

		if (is_array($permissions_query)) {
			foreach ($permissions_query as $permissions_array) {
				$selected_user_roles[$permissions_array['user_role_id']] = 'selected';
			}
		} else $selected_user_roles = false;

		return $selected_user_roles;
	}
}

/**
 * Node Item Admin - Last Rank Node Item
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_lastRankNodeItemQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			MAX(t1.rank)
		FROM
			_db_core_node_items t1
		WHERE
			t1.parent_node_id = '%s'
	";
	public $singleValue = true;

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return int
	 */
	public function invoke($parameters = null)
	{
		$last_rank = parent::invoke($parameters[0]);
		return $last_rank + 1;
	}
}

/**
 * Node Item Admin - Delete Old Node Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_deleteOldNodePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			node_id = '%s'
	";
}

/**
 * Node Item Admin - Insert Node Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_insertNodePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, node_id)
		VALUES
			%s
	";
}

/**
 * Node Item Admin - Get All Node Items
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllNodeItemsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id, t1.node_name, t1.parent_node_id, t1.node_link, t1.node_type, t1.extend, t1.alias, t1.layout, t1.params,
			t2.is_parent
		FROM
			_db_core_node_items t1
		LEFT JOIN
			_db_core_node_structure t2
		ON
			t1.node_id = t2.node_id
		ORDER BY
			t2.id
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$edit = $parameters[0];
		$select_parent = parent::invoke();
		// Define.
		$indent_group[0] = false;
		$show_parent = false;
		$show_existing_link = false;
		if (empty($select_parent)) $select_parent = array();
		foreach ($select_parent as $select_parent_array) {
			// Set node array.
			$parent = $select_parent_array;
			// Define.
			if (empty($edit['link_to'])) $edit['link_to'] = false;
			$indent = false;
			// Determine node name.
			$node_name = $this->navigation->determineNodeName($parent['node_name'], $parent['node_link'], $parent['node_id']);
			// Calculate folder indention.
			if ($parent['is_parent'] == 1) {
				// Define.
				if (empty($indent_item[$parent['parent_node_id']]))
						$indent_item[$parent['parent_node_id']] = false;
				$indent_item[$parent['node_id']] = $indent_item[$parent['parent_node_id']] + 1;
			}
			if (!empty($indent_item[$parent['parent_node_id']])) {
				$indent_integer = $indent_item[$parent['parent_node_id']];
			} else {
				$indent_integer = false;
			}
			// Check if item was already looped, ruling a loop to be created only once per node group.
			if (!key_exists($parent['parent_node_id'], $indent_group)) {
				// Loop and create indent string.
				for ($i = 0; $i <= $indent_integer; $i++) {
					$indent .= '&nbsp;';
				}
				$indent_group[$parent['parent_node_id']] = $indent;
			}
			// If user is editing choose correct selected option.
			($parent['node_id'] == $edit['parent_node_id']) ? $node_id_selected = 'selected' : $node_id_selected = false;
			// Create options.
			if ($parent['node_id'] != $edit['node_id'])
					$show_parent .= '<option value="' . $parent['node_id'] . '" ' . $node_id_selected . '>' . $indent_group[$parent['parent_node_id']] . $node_name . '</option>';
			// Create Existing Link.
			if ($parent['node_id'] == $edit['link_to']) {
				$existing_link_selected = 'selected';
				$existing_link_id = $parent['node_id'];
			} else {
				$existing_link_selected = false;
			}
			// Define.
			(empty($this->navigation->navigation[$parent['node_id']]['extend'])) ? $extend = false : $extend = $this->navigation->navigation[$parent['node_id']]['extend'];
			// Check if link should be shown.
			if (empty($edit['node_id']) && empty($extend)) {
				$show_elink = true;
			} else {
				if ($edit['node_id'] != $extend && empty($extend)) {
					$show_elink = true;
				} else {
					$show_elink = false;
				}
			}
			// Create existing options.
			if (!empty($show_elink) && ($parent['node_id'] != $edit['node_id'])) {
				$show_existing_link .= '<option value="' . $parent['node_id'] . '" ' . $existing_link_selected . '>' . $indent_group[$parent['parent_node_id']] . $node_name . '</option>';
			}
			// Clear indent.
			$indent = false;
		}
		if (empty($existing_link_id))
			$existing_link_id = 0;
		return array(
			'edit' => $edit,
			'show_existing_link' => $show_existing_link,
			'existing_link_id' => $existing_link_id,
			'show_parent' => $show_parent
		);
	}
}

/**
 * Node Item Admin - Get All User Roles
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllUserRolesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id, t1.user_role_name
		FROM
			_db_core_user_roles t1
		ORDER BY
			t1.user_role_id
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$user_roles_db = parent::invoke();

		$edit = $parameters[0];
		$selected_user_roles = $parameters[1];

		if (empty($selected_user_roles)) {
			$selected_user_roles[$this->configuration['root_role']] = 'selected';
		}
		// Define.
		$edit['permission_option'] = false;
		// Loop and see what needs to be selected.
		if (empty($user_roles_db)) $user_roles_db = array();
		foreach ($user_roles_db as $user_roles_array) {
			// Define.
			if (empty($selected_user_roles[$user_roles_array['user_role_id']]))
					$selected_user_roles[$user_roles_array['user_role_id']] = false;
			$edit['permission_option'] .= '<option value="' . $user_roles_array['user_role_id'] . '"' . $selected_user_roles[$user_roles_array['user_role_id']] . '>' . $user_roles_array['user_role_name'] . '</option>';
		}

		return array(
			'selected_user_roles' => $selected_user_roles,
			'edit' => $edit
		);
	}
}

/**
 * Node Item Admin - Get All Available Templates
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllAvailableTemplatesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			template_id, template_folder
		FROM
			_db_core_templates
		ORDER BY
			template_id
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$edit = $parameters[0];

		$select_template = parent::invoke();

		if (empty($edit['template_id'])) {
			$edit['template_id'] = $this->configuration['default_template_id'];
		}
		// Define.
		$template_option_ = false;
		// Loop template options.
		if (empty($select_template)) $select_template = array();
		foreach ($select_template as $select_template_array) {
			// Check if if item should be selected.
			($select_template_array['template_id'] == $edit['template_id']) ? $template_selected = 'selected' : $template_selected = false;
			$template_option_ .= '<option value="' . $select_template_array['template_id'] . '" ' . $template_selected . '>' . $select_template_array['template_folder'] . '</option>';
		}

		return array(
			'edit' => $edit,
			'template_option_' => $template_option_
		);
	}
}

/**
 * Node Item Admin - Check link query
 * @author Greg
 */
class PHPDS_findNodeLinkUnicity extends PHPDS_query
{
	protected $sql = "
		SELECT
			*
		FROM
			_db_core_node_items
		WHERE
			node_type = 2
		AND
			node_link = '%s'
		AND
			node_id != '%s'"
	;
	public $singleValue = true;
}

/**
 * Node Item Admin - Check alias query
 * @author Greg
 */
class PHPDS_findNodeAliasUnicity extends PHPDS_query
{
	protected $sql = "
		SELECT
			*
		FROM
			_db_core_node_items
		WHERE
			alias = '%s'"
	;
	public $singleValue = true;

	/**
	 * Override extraBuild.
	 */
	public function extraBuild($parameters = null)
	{
		return (count($parameters) > 1) ? " AND node_id != '%s'" : '';
	}
}