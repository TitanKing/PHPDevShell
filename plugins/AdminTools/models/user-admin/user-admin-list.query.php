<?php

/**
 * User Admin List - Select User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_display_name, user_password, user_name, user_email, user_group, user_role, language, timezone as user_timezone, region
		FROM
			_db_core_users
		WHERE
			user_id = %u
    ";

	protected $singleRow = true;
}

/**
 * User Admin List - Delete User
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_deleteUserQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return void
	 */
	public function invoke($parameters = null)
	{
		$user_id = $parameters[0];
		// Delete user.
		$this->db->deleteQuick('_db_core_users', 'user_id', $user_id);
		// Delete old user groups values.
		$this->db->deleteQuick('_db_core_user_extra_groups', 'user_id', $user_id);
		// Delete all search filters.
		$this->db->deleteQuick('_db_core_filter', 'user_id', $user_id);
		// Clear user from queue.
		$this->db->deleteQuick('_db_core_registration_queue', 'user_id', $user_id);
	}
}

/**
 * User Admin List - Read Role
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id, user_role_name
		FROM
			_db_core_user_roles
			%s
		ORDER BY
			user_role_id
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$extra_role_db_array_ = parent::invoke(array($this->user->setRoleQuery("WHERE user_role_id IN ({$this->user->getRoles()})")));
		if (empty($extra_role_db_array_)) $extra_role_db_array_ = array();
		foreach ($extra_role_db_array_ as $extra_role_db_array) {
			// Get constant name of user role.
			$extra_role_constant = preg_replace('/ /', '&nbsp;', $extra_role_db_array['user_role_name']);
			// Create role array.
			$extra_role_array[$extra_role_db_array['user_role_id']] = $extra_role_constant;
			// Assign values to groups to make sure they exist.
			$does_roles_id_exist[$extra_role_db_array['user_role_id']] = true;
		}
		if (!empty($extra_role_array)) {
			$role_detail['name'] = $extra_role_array;
			$role_detail['selected'] = $does_roles_id_exist;
			return $role_detail;
		} else {
			return array();
		}
	}
}

/**
 * User Admin List - Read Group
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readGroupQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id, user_group_name
		FROM
			_db_core_user_groups
			%s
		ORDER BY
			user_group_id
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$extra_group_db_array_ = parent::invoke(array($this->user->setGroupQuery("WHERE user_group_id IN ({$this->user->getGroups()})")));
		if (empty($extra_group_db_array_)) $extra_group_db_array_ = array();
		foreach ($extra_group_db_array_ as $extra_group_db_array) {
			// Get constant name of user group.
			$extra_group_constant = preg_replace('/ /', '&nbsp;', $extra_group_db_array['user_group_name']);
			// Create group array.
			$extra_group_array[$extra_group_db_array['user_group_id']] = $extra_group_constant;
			// Assign values to groups to make sure they exist.
			$does_groups_id_exist[$extra_group_db_array['user_group_id']] = true;
		}
		if (!empty($extra_group_array)) {
			$group_detail['name'] = $extra_group_array;
			$group_detail['selected'] = $does_groups_id_exist;
			return $group_detail;
		} else {
			return array();
		}
	}
}

/**
 * User Admin List - Updates user detail.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_updateUserQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_users
		SET
			user_group = %u,
			user_role = %u
		WHERE
			user_id = %u
    ";
}

/**
 * User Admin List - Write Primary Permission
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writePrimaryPermissionQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$user_group_ = $this->security->post['user_group'];
		$user_role_ = $this->security->post['user_role'];
		// Check if we have any users to update.
		if (!empty($this->security->post['user_id'])) {
			// Loop and update users.
			foreach ($this->security->post['user_id'] as $user_id_ => $user_id_token) {
				// Encrypt user id for compare, this prevents further replay attacks.
				$user_id_token_compare = md5($this->security->encrypt($user_id_));
				// Check that mand fields are not empty.
				if ($user_id_token == $user_id_token_compare) {
					// Is it a branch admin, and if it is, can he save to these groups?
					if ($this->user->belongsToGroup(false, $user_group_[$user_id_]) && $this->user->belongsToRole(false, $user_role_[$user_id_])) {
						// Updating in a loop goes against my rules, however, this case is special and we need all the security we can find!
						$this->db->invokeQuery('PHPDS_updateUserQuery', $user_group_[$user_id_], $user_role_[$user_id_], $user_id_);
					}
				}
			}
		}
	}
}

/**
 * User Admin List - Write Roles
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeRoleQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$user = $this->user;

		// Collect all $this->security->post values...
		$extra_roles_ = $this->security->post['extra_roles'];
		$user_id_for_roles_replacement = '';
		$final_role_database_insert_string = '';
		//////////////////////////////////////////////////////////////////////////////////////////////////
		// Lets update the extra roles...
		// Split role id results in array per user id.
		if (empty($extra_roles_)) $extra_roles_ = array();
		foreach ($extra_roles_ as $user_id_1_integer_role => $role_id_1_string) {
			$explode_roles_to_array[$user_id_1_integer_role] = explode(',', $role_id_1_string);
			// Collect user ID for database replacement.
			$user_id_for_roles_replacement .= "'$user_id_1_integer_role',";
		}
		// Now lets loop array to write database insert.
		if (empty($explode_roles_to_array)) $explode_roles_to_array = array();
		foreach ($explode_roles_to_array as $user_id_2_integer_role => $role_id_2_array) {
			if (empty($role_id_2_array)) $role_id_2_array = array();
			foreach ($role_id_2_array as $role_id_3_integer) {
				// Is it a branch admin?
				if ($user->belongsToRole(false, $role_id_3_integer)) {
					if (!empty($role_id_3_integer) && $user->roleExist($role_id_3_integer))
                        $final_role_database_insert_string .= "('$user_id_2_integer_role', '$role_id_3_integer'),";
				}
			}
		}
	}
}

/**
 * User Admin List - Delete extra groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_deleteExtraGroupsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_extra_groups
		WHERE
			user_id
		IN
			(%s)
    ";
}

/**
 * User Admin List - Write extra groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeExtraGroupsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_extra_groups (user_id, user_group_id)
		VALUES
			%s
    ";
}

/**
 * User Admin List - Write Groups
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeGroupQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$user = $this->user;

		// Collect all $this->security->post values...
		$extra_groups_ = $this->security->post['extra_groups'];
		$user_id_for_groups_replacement = '';
		$final_group_database_insert_string = '';
		// Lets update the user groups...
		// Split group id results in array per user id.
		if (empty($extra_groups_)) $extra_groups_ = array();
		foreach ($extra_groups_ as $user_id_1_integer_group => $group_id_1_string) {
			$explode_groups_to_array[$user_id_1_integer_group] = explode(',', $group_id_1_string);
			// Collect user ID for database replacement.
			$user_id_for_groups_replacement .= "'$user_id_1_integer_group',";
		}
		// Now lets loop array to write database insert.
		if (empty($explode_groups_to_array)) $explode_groups_to_array = array();
		foreach ($explode_groups_to_array as $user_id_2_integer_group => $group_id_2_array) {
			if (empty($group_id_2_array)) $group_id_2_array = array();
			foreach ($group_id_2_array as $group_id_3_integer) {
				// Is it a branch admin?
				if ($user->belongsToGroup(false, $group_id_3_integer)) {
					if (!empty($group_id_3_integer) && $user->groupExist($group_id_3_integer))
							$final_group_database_insert_string .= "('$user_id_2_integer_group', '$group_id_3_integer'),";
				}
			}
		}
		// Almost ready, lets trim the trailing comma.
		// Groups
		$user_id_for_groups_replacement = rtrim($user_id_for_groups_replacement, ',');
		$final_group_database_insert_string = rtrim($final_group_database_insert_string, ',');
		// Can we delete and insert?
		if (!empty($user_id_for_groups_replacement)) {
			// Ok everything seems safe now, lets delete the current records of these users.
			// Delete old groups values.
			$this->db->invokeQuery('PHPDS_deleteExtraGroupsQuery', $user_id_for_groups_replacement);

			if (!empty($final_group_database_insert_string)) {
				// And finally we can insert the new records.
				// Update user group detail.
				$this->db->invokeQuery('PHPDS_writeExtraGroupsQuery', $final_group_database_insert_string);
			}
		}
	}
}

/**
 * User Admin List - Read user groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserGroupsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_group_id
		FROM
			_db_core_user_extra_groups
		WHERE
			user_id
		IN
			(%s)
		ORDER BY
			user_group_id
		ASC
    ";

	protected $keyField = '';

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$extra_groups_results = parent::invoke($parameters);
		$extra_group_assignment_array = array();
		// Loop extra groups results.
		if (! empty($extra_groups_results)) {
			foreach ($extra_groups_results as $extra_groups_results_array) {
				// Define.
				if (empty($extra_group_assignment_array[$extra_groups_results_array['user_id']]))
						$extra_group_assignment_array[$extra_groups_results_array['user_id']] = false;
				$extra_group_assignment_array[$extra_groups_results_array['user_id']] .= "{$extra_groups_results_array['user_group_id']},";
			}
			return $extra_group_assignment_array;
		} else {
			return array();
		}
	}
}

/**
 * User Admin List - Read Users
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUsersQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id, t1.user_display_name, t1.user_name, t1.user_password, t1.user_email, t1.user_group, t1.user_role, t1.date_registered, t1.language,
			t2.user_group_name,
			t3.user_role_name
		FROM
			_db_core_users t1
		LEFT JOIN
			_db_core_user_groups t2
		ON
			t1.user_group = t2.user_group_id
		LEFT JOIN
			_db_core_user_roles t3
		ON
			t1.user_role = t3.user_role_id
		%s
	";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;
		$security = $this->security;

		$RESULTS['list'] = array();
		$extra_role_array = $parameters[0];
		$extra_group_array = $parameters[1];
		// Set page to load.
		$page_edit = $navigation->buildURL('885145814', 'eu=');
		$page_delete = $navigation->buildURL(false, 'du=');

		// Initiate pagination plugin.
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('User ID') => 't1.user_id',
			_('User Name') => 't1.user_name',
			_('Display Name') => 't1.user_display_name',
			_('User Email') => 't1.user_email',
			_('User Roles') => 't3.user_role_name',
			_('User Groups') => 't2.user_group_name',
			_('Date Registered') => 't1.date_registered',
			_('Edit') => '',
			_('Delete') => '');
		$pagination->condition = 'AND';
		$pagination->dateColumn = 't1.date_registered';
		$select_users = $pagination->query($this->sql, $this->user->setGroupQuery("WHERE user_group IN ({$this->user->getGroups()})", "WHERE user_id != 'x'"));
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$edit_user_icon = $template->icon('user--pencil', __('Edit User'));
		$delete_user_icon = $this->template->icon('user--minus', __('Delete User'));

		////////////////////////////////////////////////
		// Get user id's to limit following queries.
		$user_id_seek_db = '';
		foreach ($select_users as $select_users_id_array) {
			$user_id_seek_db .= "'{$select_users_id_array['user_id']}',";
		}
		////////////////////////////////////////////////
		// Make sure $user_id_seek_db is not empty.
		if (!empty($user_id_seek_db)) {
			$user_id_seek_db = rtrim($user_id_seek_db, ',');

			////////////////////////////////////////////////
			// Reset pointer.
			$alt = '';
			foreach ($select_users as $select_users_array) {
                $user_id           = $select_users_array['user_id'];
                $user_id_token     = md5($security->encrypt($select_users_array['user_id']));
                $user_name         = $select_users_array['user_name'];
                $user_password     = $select_users_array['user_password'];
                $user_display_name = $select_users_array['user_display_name'];
                $user_email        = $select_users_array['user_email'];
                $user_group        = $select_users_array['user_group'];
                $user_role         = $select_users_array['user_role'];
                $date_registered   = $core->formatTimeDate($select_users_array['date_registered']);
                $user_role_option  = false;
                $user_group_option = false;
				// Define extra groups and roles.
				(!empty($extra_group_assignment_array[$user_id])) ? $extra_groups = rtrim($extra_group_assignment_array[$user_id], ',') : $extra_groups = '';
				(!empty($extra_role_assignment_array[$user_id])) ? $extra_roles = rtrim($extra_role_assignment_array[$user_id], ',') : $extra_roles = '';
				$language = $select_users_array['language'];

				// Loop and see what role needs to be selected.
				foreach ($extra_role_array as $user_role_id_ => $user_role_name_) {
					// Check Selected
					($user_role_id_ == $user_role) ? $user_role_select = 'selected' : $user_role_select = false;
					$user_role_option .= '<option value="' . $user_role_id_ . '"' . $user_role_select . '>' . $user_role_name_ . " ($user_role_id_)</option>";
				}
				// Loop and see what group needs to be selected.
				foreach ($extra_group_array as $user_group_id_ => $user_group_name_) {
					// Check Selected
					($user_group_id_ == $user_group) ? $user_group_select = 'selected' : $user_group_select = false;
					$user_group_option .= '<option value="' . $user_group_id_ . '"' . $user_group_select . '>' . $user_group_name_ . " ($user_group_id_)</option>";
				}
				$RESULTS['list'][] = array(
					'user_id' => $user_id,
					'date_registered' => $date_registered,
					'user_name' => $user_name,
					'user_display_name' => $user_display_name,
					'user_email' => $user_email,
					'user_role_option' => $user_role_option,
					'extra_roles' => $extra_roles,
					'user_group_option' => $user_group_option,
					'extra_groups' => $extra_groups,
					'edit' => "<a href=\"{$page_edit}{$user_id}\" class=\"button\">{$edit_user_icon}</a>",
					'delete' => "<a href=\"{$page_delete}{$user_id}\" {$core->confirmLink(sprintf(__('Are you sure you want to DELETE : %s'), $user_display_name))} class=\"button\">{$delete_user_icon}</a>",
					'user_id_token' => $user_id_token
				);
				unset($user_role_option, $user_group_option);
			}
		}
		if (! empty($RESULTS['list'])) {
			return $RESULTS;
		} else {
			$RESULTS['list'] = array();
			return $RESULTS;
		}
	}
}