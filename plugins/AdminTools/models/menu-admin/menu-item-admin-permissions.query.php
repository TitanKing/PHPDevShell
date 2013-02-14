<?php

/**
 * Node Item Admin Permissions - Reset role permissions primary key.
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
 * Node Item Admin Permissions - Write role permissions.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_writeRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, node_id)
		VALUES
			%s
	";
}

/**
 * Node Item Admin Permissions - Update Permissions
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
 * Node Item Admin Permissions - Get Role Permissions
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id, t1.node_id
		FROM
			_db_core_user_role_permissions t1
		ORDER BY
			user_role_id
		ASC
	";

	protected $keyField = '';
}

/**
 * Node Item Admin Permissions - Get All Permissions
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
			$node_id = $select_role_permission_array['node_id'];
			// Define.
			if (empty($permissions[$node_id])) $permissions[$node_id] = false;
			if (empty($user_role_field_info[$node_id]))
					$user_role_field_info[$node_id] = false;
			if (empty($user_role_description[$user_role_id]))
					$user_role_description[$user_role_id] = false;
			$permissions[$node_id] .= $user_role_id . ',';
			$user_role_field_info[$node_id] .= "[" . $user_role_id . '&#61;&#62;' . $user_role_description[$user_role_id] . "] ";
		}
		return array(rtrim($user_roles_info, ","), $permissions, $user_role_field_info);
	}
}

/**
 * Node Item Admin Permissions - Compile permission rows.
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
			$this->template->ok(__('Permissions saved.'));

			return true;
		} else {
			$this->template->warning(__('There was an unknown problem, permissions not saved.'));

			return false;
		}
	}
}

/**
 * Node Item Admin Permissions - List nodes.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_listNodesQuery extends PHPDS_query
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
		$node_array = $this->factory('nodeArray');

		// Page variables.
		$page_edit = $this->navigation->buildURL('3440897808', 'em=');
		$page_delete = $this->navigation->buildURL(false, 'dm=');

		// Icons.
		$delete_node_icon = $template->icon('task--minus', __('Delete Node Item'));
		$edit_node_icon = $template->icon('task--pencil', __('Edit Node Item'));

		// Node Array.
		$node_array->loadNodeArray();

		foreach ($node_array->nodeArray as $select_node_items_array) {
			// Set values.
			$item = $select_node_items_array;

			// Role permissions.
			if (!empty($permissions[$item['node_id']])) {
				$permissions_role = rtrim(trim($permissions[$item['node_id']]), ',');
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
			if (empty($user_role_field_info[$item['node_id']]))
					$user_role_field_info[$item['node_id']] = false;
			$RESULTS[] = array(
				'item' => $item,
				'hide_' => $hide_,
				'i_url_name' => "{$item['node_link']}<br>" . __('Alias: ') . "{$item['alias']}",
				'permissions_role' => $permissions_role,
				'i_item_permission' => $user_role_field_info[$item['node_id']],
				'edit' => "<a href=\"{$page_edit}{$item['node_id']}\" class=\"button\">{$edit_node_icon}</a>",
				'delete' => "<a href=\"{$page_delete}{$item['node_id']}\" {$core->confirmLink(sprintf(__('Are you sure you want to DELETE : %s'), $item['node_name']))} class=\"button\">{$delete_node_icon}</a>"
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
