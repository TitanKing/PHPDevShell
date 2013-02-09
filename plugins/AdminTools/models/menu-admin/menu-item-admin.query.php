<?php

/**
 * Menu Item Admin - Menu Item Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAdminMenuItemPermissionsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id
		FROM
			_db_core_user_role_permissions t1
		WHERE
			t1.menu_id = '%s'
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
 * Menu Item Admin - Last Rank Menu Item
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_lastRankMenuItemQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			MAX(t1.rank)
		FROM
			_db_core_menu_items t1
		WHERE
			t1.parent_menu_id = '%s'
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
 * Menu Item Admin - Delete Old Menu Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_deleteOldMenuPermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			menu_id = '%s'
	";
}

/**
 * Menu Item Admin - Insert Menu Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_insertMenuPermissionsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, menu_id)
		VALUES
			%s
	";
}

/**
 * Menu Item Admin - Get All Menu Items
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllMenuItemsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.menu_id, t1.menu_name, t1.parent_menu_id, t1.menu_link, t1.menu_type, t1.extend, t1.alias, t1.layout, t1.params,
			t2.is_parent
		FROM
			_db_core_menu_items t1
		LEFT JOIN
			_db_core_menu_structure t2
		ON
			t1.menu_id = t2.menu_id
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
			// Set menu array.
			$parent = $select_parent_array;
			// Define.
			if (empty($edit['link_to'])) $edit['link_to'] = false;
			$indent = false;
			// Determine menu name.
			$menu_name = $this->navigation->determineMenuName($parent['menu_name'], $parent['menu_link'], $parent['menu_id']);
			// Calculate folder indention.
			if ($parent['is_parent'] == 1) {
				// Define.
				if (empty($indent_item[$parent['parent_menu_id']]))
						$indent_item[$parent['parent_menu_id']] = false;
				$indent_item[$parent['menu_id']] = $indent_item[$parent['parent_menu_id']] + 1;
			}
			if (!empty($indent_item[$parent['parent_menu_id']])) {
				$indent_integer = $indent_item[$parent['parent_menu_id']];
			} else {
				$indent_integer = false;
			}
			// Check if item was already looped, ruling a loop to be created only once per menu group.
			if (!key_exists($parent['parent_menu_id'], $indent_group)) {
				// Loop and create indent string.
				for ($i = 0; $i <= $indent_integer; $i++) {
					$indent .= '&nbsp;';
				}
				$indent_group[$parent['parent_menu_id']] = $indent;
			}
			// If user is editing choose correct selected option.
			($parent['menu_id'] == $edit['parent_menu_id']) ? $menu_id_selected = 'selected' : $menu_id_selected = false;
			// Create options.
			if ($parent['menu_id'] != $edit['menu_id'])
					$show_parent .= '<option value="' . $parent['menu_id'] . '" ' . $menu_id_selected . '>' . $indent_group[$parent['parent_menu_id']] . $menu_name . '</option>';
			// Create Existing Link.
			if ($parent['menu_id'] == $edit['link_to']) {
				$existing_link_selected = 'selected';
				$existing_link_id = $parent['menu_id'];
			} else {
				$existing_link_selected = false;
			}
			// Define.
			(empty($this->navigation->navigation[$parent['menu_id']]['extend'])) ? $extend = false : $extend = $this->navigation->navigation[$parent['menu_id']]['extend'];
			// Check if link should be shown.
			if (empty($edit['menu_id']) && empty($extend)) {
				$show_elink = true;
			} else {
				if ($edit['menu_id'] != $extend && empty($extend)) {
					$show_elink = true;
				} else {
					$show_elink = false;
				}
			}
			// Create existing options.
			if (!empty($show_elink) && ($parent['menu_id'] != $edit['menu_id'])) {
				$show_existing_link .= '<option value="' . $parent['menu_id'] . '" ' . $existing_link_selected . '>' . $indent_group[$parent['parent_menu_id']] . $menu_name . '</option>';
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
 * Menu Item Admin - Get All User Roles
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
 * Menu Item Admin - Get All Available Templates
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
 * Menu Item Admin - Check link query
 * @author Greg
 */
class PHPDS_findMenuLinkUnicity extends PHPDS_query
{
	protected $sql = "
		SELECT
			*
		FROM
			_db_core_menu_items
		WHERE
			menu_type = 2
		AND
			menu_link = '%s'
		AND
			menu_id != '%s'"
	;
	public $singleValue = true;
}

/**
 * Menu Item Admin - Check alias query
 * @author Greg
 */
class PHPDS_findMenuAliasUnicity extends PHPDS_query
{
	protected $sql = "
		SELECT
			*
		FROM
			_db_core_menu_items
		WHERE
			alias = '%s'"
	;
	public $singleValue = true;

	/**
	 * Override extraBuild.
	 */
	public function extraBuild($parameters = null)
	{
		return (count($parameters) > 1) ? " AND menu_id != '%s'" : '';
	}
}