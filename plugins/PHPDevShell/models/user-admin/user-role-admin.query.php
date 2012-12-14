<?php

/**
 * User Role Admin - Read Basic Role Information.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id, user_role_name, user_role_note
		FROM
			_db_core_user_roles
		WHERE
			user_role_id = %u
		";
	protected $singleRow = true;
}

/**
 * User Role Admin - Read Role Menu Permission.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleMenuQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			menu_id
		FROM
			_db_core_user_role_permissions
		WHERE
			user_role_id = '%u'
		";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$r = parent::invoke($parameters);
		if (empty($r)) $r = array();
		foreach ($r as $role_per_menu_array) {
			$selected_menu[$role_per_menu_array['menu_id']] = 'selected';
		}
		if (!empty($selected_menu)) {
			return $selected_menu;
		} else {
			return $selected_menu = array();
		}
	}
}

/**
 * User Role Admin - Write Role Data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeRoleQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_roles (user_role_id, user_role_name, user_role_note)
		VALUES
			('%u', '%s', '%s')
		";
	protected $returnId = true;
}

/**
 * User Role Admin - Delete old permission data for rewrite.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_deletePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			user_role_id = '%u'
		";
	protected $returnId = true;
}

/**
 * User Role Admin - Write Role Data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, menu_id)
		VALUES
			%s
		";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return boolean
	 */
	public function invoke($parameters = null)
	{
		$user_role_id = intval($parameters[0]);
		$permission = $parameters[1];

		if (!empty($permission)) {
			// Save permissions.
			foreach ($permission as $menu_id) {
				$cols[] = array($user_role_id, $menu_id);
				// Also set selected role menu items.
			}
			$user_role_id_db = $this->rows($cols);
		}
		// Set new assigned value.
		if (!empty($user_role_id_db)) {
			// Insert menu permissions.
			return parent::invoke(array($user_role_id_db));
		} else {
			return false;
		}
	}
}

/**
 * User Role Admin - Read Role Menu Permission.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readMenusQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.menu_id, t1.menu_name, t1.parent_menu_id, t1.menu_link, t1.menu_type,
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
		if (!empty($parameters[0])) {
			$selected_menu = $parameters[0];
		} else {
			$selected_menu = array();
		}
		$r = parent::invoke();
		$menu_item_options = '';
		$indent_group[0] = false;
		foreach ($r as $select_menu_array) {
			$menu['menu_id'] = $select_menu_array['menu_id'];
			$menu['menu_name'] = $select_menu_array['menu_name'];
			$menu['menu_link'] = $select_menu_array['menu_link'];
			$menu['menu_type'] = $select_menu_array['menu_type'];
			$menu['parent_menu_id'] = $select_menu_array['parent_menu_id'];
			$menu['is_parent'] = $select_menu_array['is_parent'];
			// Determine menu name.
			$menu_name = $this->navigation->determineMenuName($menu['menu_name'], $menu['menu_link'], $menu['menu_id']);
			// Calculate folder indention.
			if ($menu['is_parent'] == 1) {
				if (!empty($indent_item[$menu['parent_menu_id']])) {
					$indent_item[$menu['menu_id']] = $indent_item[$menu['parent_menu_id']] + 1;
				} else {
					$indent_item[$menu['menu_id']] = 1;
				}
			}
			if (!empty($indent_item[$menu['parent_menu_id']])) {
				$indent_integer = $indent_item[$menu['parent_menu_id']];
			} else {
				$indent_integer = 0;
			}
			// Check if item was already looped, ruling a loop to be created only once per menu group.
			if (!key_exists($menu['parent_menu_id'], $indent_group)) {
				// Loop and create indent string.
				$indent_group[$menu['parent_menu_id']] = str_repeat('&nbsp;', $indent_integer + 1);
			}
			if (empty($selected_menu[$menu['menu_id']])) {
				$selected_menu_ = false;
			} else {
				$selected_menu_ = $selected_menu[$menu['menu_id']];
			}
			// Create options.
			$menu_item_array[] = array('menu_id' => $menu['menu_id'], 'selected' => $selected_menu_, 'indent' => $indent_group[$menu['parent_menu_id']], 'menu_name' => $menu_name);
			// Clear indent.
			$indent = false;
		}
		if ($menu_item_array) {
			return $menu_item_array;
		} else {
			return array();
		}
	}
}