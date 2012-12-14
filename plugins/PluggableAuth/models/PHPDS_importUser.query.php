<?php

class PHPDS_AllGroupsQuery extends PHPDS_query
{
	protected $sql = '
		SELECT
			*
		FROM
			_db_core_user_groups
	';
}

/**
 * This query will give you an associative array (id => name) of possible roles for the current user
 *
 * @author greg
 *
 */
class PHPDS_RolesQuery extends PHPDS_query
{
	protected $sql = '
		SELECT
			user_role_id, user_role_name
		FROM
		_db_core_user_roles
	';
	protected $focus = 'user_role_name';
	protected $orderby = 'user_role_id ASC';

	public function extraBuild($parameters = null)
	{
		$roles = $this->db->getRoles();
		if ($roles) $this->where = $this->db->setRoleQuery(" user_role_id IN ($roles)");
		return parent::extra_build($parameters);
	}
}

/**
 * This query will give you a list of users according to the constraints
 *
 * Parameters can be either an ID, a username, or an associative array of constraints ('id', 'group')
 *
 * By default groups are matched either primary or secondary ; you can change to PHPDS original behavior (matching only primary)  by setting $primary_script to true (default is false)
 *
 * @author greg
 *
 */
class PHPDS_FindUsersQuery extends PHPDS_query
{
	protected $sql = 'SELECT * FROM _db_core_user_extra_groups LEFT JOIN  _db_core_users USING (user_id)';
	protected $where = '';
	protected $ID;
	protected $username;

	protected $groups; // can be empty (=> groups taken from the current user's groups), a single value, or an array of values ( always group IDs)
	protected $primary_script = false; // if true only primary group is checked

	/**
	 * Deal with constraints given as parameters of the query:
	 * - a single numeric parameter is considered as a user_id
	 * - a single string is considered as a user_name (ie login)
	 * - an array is searched for keys: 'id' (id or name, as above), 'group' (numerical group id)
	 *
	 * @see stable/phpdevshell/includes/PHPDS_query#check_parameters($parameters)
	 */
	public function checkParameters(&$parameters = null)
	{
		$this->where = ' 1 ';
		$main_group = '';

		if (!empty($parameters)) {
			if (!is_array($parameters)) $parameters = array('id' => $parameters);
			elseif (is_array($parameters)) {
				if (isset($parameters[0]) && is_array($parameters[0])) $parameters = $parameters[0];
			}

			foreach($parameters as $key => $value) {
				switch ($key) {
					case 'id' :
						if (is_numeric($value)) $this->where .= ' AND user_id = '.intval($value);
						else $this->where .= ' AND user_name =  "'.PU_CleanString($value).'"';
					break;
					case 'user_name' :
						$this->where .= ' AND user_name =  "'.PU_CleanString($value).'"';
					break;
					case 'group':
						$main_group = intval($value);
						$this->where .= ($this->primary_script) ?  " AND user_group = $main_group " : " AND (user_group = $main_group OR user_group_id = $main_group) ";
					break;
				}
			}
		}

		$this->where .= $this->sql_group();

		return true;
	}

	public function derive_groups($main_group)
	{
		$main_group = intval($main_group);
		return array($main_group);
	}

	/**
	 * Restrict the search to "accessible users", ie users of the belonging to the same groups/subgroups as the current user
	 *
	 * If $primary_script is true, relationship is done only through primary group (ie only users having one of the current user's group as his/hers primary group - PHPDS default behavior)
	 * If it's false, primary and secondary groups are matched
	 *
	 * @param array_or_id $additional_groups groups to add to the query
	 * @return nothing
	 */
	public function sql_group($additional_groups = null)
	{
		if (empty($this->groups)) $groups = $this->db->getGroups(null, true); // groups of the current user, as array
		elseif (!is_array($this->groups)) $groups = array(intval($this->groups));
		else $groups = $this->groups;

		if (!empty($additional_groups)) {
			if (!is_array($additional_groups)) $additional_groups = $this->derive_groups($additional_groups);
			$groups = array_merge($groups, $additional_groups);
		}

		if (is_array($groups)) {
			$groups = implode(',', $groups);

			if ($this->primary_script) return $this->db->setGroupQuery(" AND user_group IN ($groups)");
			else return  $this->db->setGroupQuery(" AND (user_group IN ($groups) OR user_group_id IN ($groups))");
		}
	}

}

/**
 * Same as the previous query but only the first result is returned.
 * Usefull for dealing with the current user
 *
 * @author greg
 *
 */
class PHPDS_FindUserQuery extends PHPDS_FindUsersQuery
{
	protected $singleRow = true;

	/*public function check_results(&$results)
	{
		if (count($results) >0) $results = array_shift($results);
		return true;
	}*/

}


class PHPDS_UserReplace extends PHPDS_query
{
	protected $sql = "REPLACE INTO _db_core_users (
			user_id,
			user_display_name,
			user_name,
			user_password,
			user_email,
			user_group,
			user_role,
			date_registered,
			language,
			timezone,
			region
		) VALUES (
			'%(existing_user)s',
			'%(user_display_name)s',
			'%(user_name)s',
			'%(user_password)s',
			'%(user_email)s',
			'%(user_group)s',
			'%(user_role)s',
			'%(date_registered)s',
			'%(language)s',
			'%(timezone)s',
			'%(region)s'
		)";

	protected $return_id = true;
	protected $auto_protect = true;
}

class PHPDS_UserSetGroupsQuery extends PHPDS_query
{
	protected $sql = 'REPLACE INTO _db_core_user_extra_groups SET user_id = %(user_id)d, user_group_id = %(user_group_id)d';

	public function invoke($parameters = null)
	{
		list($user_id, $groups) = $parameters;

		if (!is_array($groups)) $groups = array($group);

		foreach ($groups as $group) parent::invoke(array('user_id' => $user_id, 'user_group_id' => $group));

		return true;
	}
}

class PHPDS_UserSetRolesQuery extends PHPDS_query
{
	protected $sql = 'REPLACE INTO _db_core_user_extra_roles SET user_id = %(user_id)d, user_role_id = %(user_role_id)d';

	public function invoke($parameters = null)
	{
		list($user_id, $roles) = $parameters;

		if (!is_array($roles)) $roles = array($roles);

		foreach ($roles as $role) parent::invoke(array('user_id' => $user_id, 'user_role_id' => $role));

		return true;
	}
}

class PHPDS_UserGroupsQuery extends PHPDS_query
{
	protected $sql = 'SELECT * FROM _db_core_user_extra_groups WHERE user_id =  %d ORDER BY user_group_id ASC';
}

class PHPDS_UserRolesQuery extends PHPDS_query
{
	protected $sql = 'SELECT * FROM _db_core_user_extra_roles WHERE user_id =  %d ORDER BY user_role_id ASC';
}





