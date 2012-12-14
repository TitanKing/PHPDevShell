<?php

/**
 * Menu Item Admin Permissions - Reset role permissions primary key.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_resetRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		ALTER TABLE
			_db_core_user_role_permissions
		AUTO_INCREMENT = 0;
	";
}

/**
 * Menu Item Admin Permissions - Write role permissions.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_writeRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, menu_id)
		VALUES
			%s
	";
}

/**
 * Menu Item Admin Permissions - Update Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_updatePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 */
	public function invoke($parameters = null)
	{
		$final_database_insert_string = $parameters[0];
		// Clear Database of old values.
		parent::invoke();

		// Replace new permissions.
		$this->db->invokeQuery('PHPDS_writeRolePermissionsQuery', $final_database_insert_string);
	}
}

/**
 * Menu Item Admin Permissions - Get Role Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id, t1.menu_id
		FROM
			_db_core_user_role_permissions t1
		ORDER BY
			user_role_id
		ASC
	";

	protected $keyField = '';
}

/**
 * Menu Item Admin Permissions - Get All Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllPermissionsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id, user_role_name
		FROM
			_db_core_user_roles
	";

	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$query_user_roles = parent::invoke();
		$user_roles_info = '';
		if (empty($query_user_roles)) $query_user_roles = array();
		foreach ($query_user_roles as $query_user_roles_array) {
			$user_roles_info .= '[' . $query_user_roles_array['user_role_id'] . '&#61;&#62;' . $query_user_roles_array['user_role_name'] . '] ';
			$user_role_description[$query_user_roles_array['user_role_id']] = $query_user_roles_array['user_role_name'];
		}
		// Select all permissions.
		$select_role_permission = $this->db->invokeQuery('PHPDS_getRolePermissionsQuery');

		// Assign permissions array.
		if (empty($select_role_permission)) $select_role_permission = array();
		foreach ($select_role_permission as $select_role_permission_array) {
			$user_role_id = $select_role_permission_array['user_role_id'];
			$menu_id = $select_role_permission_array['menu_id'];
			// Define.
			if (empty($permissions[$menu_id])) $permissions[$menu_id] = false;
			if (empty($user_role_field_info[$menu_id]))
					$user_role_field_info[$menu_id] = false;
			if (empty($user_role_description[$user_role_id]))
					$user_role_description[$user_role_id] = false;
			$permissions[$menu_id] .= $user_role_id . ',';
			$user_role_field_info[$menu_id] .= "[" . $user_role_id . '&#61;&#62;' . $user_role_description[$user_role_id] . "] ";
		}
		return array(rtrim($user_roles_info, ","), $permissions, $user_role_field_info);
	}
}

/**
 * Menu Item Admin Permissions - Compile permission rows.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_writePermissionRowsQuery extends PHPDS_query
{
	/**
	 * Initiate query invoke command.
	 * @param array
	 */
	public function invoke($parameters = null)
	{
		$user = $this->user;
		$post_item_permission_array = $this->security->post['item_permission'];
		$final_database_insert_string = '';

		$id = 1;
		if (! empty($post_item_permission_array)) {
			foreach ($post_item_permission_array as $item_id_parent => $permissions_parent) {
				$explode_to_array[$item_id_parent] = explode(',', $permissions_parent);
			}
		}
		if (! empty($explode_to_array)) {
			foreach ($explode_to_array as $explode_item_id_parent => $explode_role_id_parent) {
				foreach ($explode_role_id_parent as $explode_role_id_child) {
					if ($explode_role_id_child != false && $user->roleExist($explode_role_id_child)) {
						$final_database_insert_string .= "('$explode_role_id_child', '$explode_item_id_parent'),";
					}
				}
			}
		}
		$final_database_insert_string = rtrim($final_database_insert_string, ',');
		// Are we allowed to update?
		if (!empty($final_database_insert_string)) {
			$this->db->invokeQuery("PHPDS_updatePermissionsQuery", $final_database_insert_string);

			// Clear old cache.
			$this->db->cacheClear('navigation');

			// If ok saved, show ok.
			$this->template->ok(_('Permissions saved.'));

			return true;
		} else {
			$this->template->warning(_('There was an unknown problem, permissions not saved.'));

			return false;
		}
	}
}

/**
 * Menu Item Admin Permissions - List menus.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_listMenusQuery extends PHPDS_query
{
	/**
	 * Initiate query invoke command.
	 * @param array
	 */
	public function invoke($parameters = null)
	{
		list($permissions, $user_role_field_info) = $parameters;

		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;
		$menu_array = $this->factory('menuArray');

		// Page variables.
		$page_edit = $this->navigation->buildURL('3440897808', 'em=');
		$page_delete = $this->navigation->buildURL(false, 'dm=');

		// Icons.
		$delete_menu_icon = $template->icon('task--minus', _('Delete Menu Item'));
		$edit_menu_icon = $template->icon('task--pencil', _('Edit Menu Item'));

		// Menu Array.
		$menu_array->loadMenuArray();

		foreach ($menu_array->menuArray as $select_menu_items_array) {
			// Set values.
			$item = $select_menu_items_array;

			// Role permissions.
			if (!empty($permissions[$item['menu_id']])) {
				$permissions_role = rtrim(trim($permissions[$item['menu_id']]), ',');
			} else {
				$permissions_role = false;
			}
			// Check if the item is visible or not.
			if ($item['hide'] == 0 || $item['hide'] == 2) {
				$hide_ = '';
			} else {
				$hide_ = 'ui-state-disabled';
			}
			// Define.
			if (empty($user_role_field_info[$item['menu_id']]))
					$user_role_field_info[$item['menu_id']] = false;
			$RESULTS[] = array(
				'item' => $item,
				'hide_' => $hide_,
				'i_url_name' => "{$item['menu_link']}<br>" . _('Alias: ') . "{$item['alias']}",
				'permissions_role' => $permissions_role,
				'i_item_permission' => $user_role_field_info[$item['menu_id']],
				'edit' => "<a href=\"{$page_edit}{$item['menu_id']}\" class=\"button\">{$edit_menu_icon}</a>",
				'delete' => "<a href=\"{$page_delete}{$item['menu_id']}\" {$core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $item['menu_name']))} class=\"button\">{$delete_menu_icon}</a>"
			);
		}
		if (! empty($RESULTS)) {
			return $RESULTS;
		} else {
			$RESULTS[] = array();
			return $RESULTS;
		}
	}
}
