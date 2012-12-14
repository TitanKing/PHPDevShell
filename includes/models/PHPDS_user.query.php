<?php

/**
 * User - The guests group name.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_guestGroupNameQuery extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_group_name
			FROM
				_db_core_user_groups
			WHERE
				user_group_id = %u
		";
	protected $singleValue = true;
	protected $focus = 'user_group_name';

	public function checkParameters(&$parameters = null)
	{
		$settings_array = $this->db->essentialSettings;
		$parameters = $settings_array['guest_group'];
		return true; // the parameters are considered as valid
	}
}

/**
 * User - The guest roles name.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_guestRoleNameQuery extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_role_name
			FROM
				_db_core_user_roles
			WHERE
				user_role_id = %u
		";
	protected $singleValue = true;
	protected $focus = 'user_role_name';

	public function checkParameters(&$parameters = null)
	{
		$settings_array = $this->db->essentialSettings;
		$parameters = $settings_array['guest_role'];
		return true; // the parameters are considered as valid
	}
}

/**
 * User - Queries if user is a root user.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_isRootQuery extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_role
			FROM
				_db_core_users
			WHERE
				user_id = %u
		";
	protected $singleValue = true;
	protected $focus = 'user_role';

	public function checkParameters(&$parameters = null)
	{
		list($user_id) = $parameters;
		return intval($user_id) > 0;
	}
}

/**
 * User - Count Rows.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_isSameGroupQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id
		FROM
			_db_core_users
		WHERE
			user_id = %u
		%s
		";
	protected $singleValue = true;
	protected $focus = 'user_id';

	public function checkParameters(&$parameters = null)
	{
		list($user_id) = $parameters;
		if (intval($user_id) > 0) {
			$parameters = array($user_id, $this->user->setGroupQuery("AND user_group IN ({$this->user->getGroups()})"));
		} else return false;
	}
}

/**
 * USER - Get extra users roles.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_getExtraRolesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id
		FROM
			_db_core_user_extra_roles
		WHERE
			user_id = %u
	";
}

/**
 * USER - Get users roles.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_getRolesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role
		FROM
			_db_core_users
		WHERE
			user_id = %u
	";

	protected $singleValue = true;

	/**
	 * Initiate query invoke command.
	 *
	 * Parameters are ($user_id, $return_array) - $user_id can be 0 for current user
	 *
	 * @version 1.0.2
	 *
	 * @date 20110215 (greg) (v1.0.1) added checks to avoid error if $select_user_roles_db is not an array
	 * @date 20110315 (jason) (v1.0.2) improved functionality and stability.
	 *
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($user_id, $return_array) = $parameters;
		$configuration = $this->configuration;
		$db = $this->db;
		$user = $this->user;

		// First try to assign default user id.
		if (empty($user_id))
			(! empty($configuration['user_id'])) ? $user_id = $configuration['user_id'] : $user_id = 0;
		
		// Check if user is a guest.
		if (! empty($user_id)) {
			// Check roles cache.
			if ($db->cacheEmpty("roles_{$user_id}")) {
				// Check if we have a saved array.
				if (empty($user->mergeRoles)) {
					// Pull user roles.
					if ($user_id == $configuration['user_id']) {
						$role_main = $configuration['user_role'];
					} else {
						$role_main = parent::invoke($user_id);
					}

					// Get all extra roles for user..
					$role_array = $db->invokeQuery('USER_getExtraRolesQuery', $user_id);

					// Set.
					$role_string = '';

					// Check if anything is selected.
					if (is_array($role_array)) {
						// Loop Roles and return string.
						foreach ($role_array as $role_arr) {
							$role_string .= "{$role_arr['user_role_id']},";
						}
					}
					// Merger primary and extra roles.
					$user->mergeRoles = rtrim("$role_main," . $role_string, ',');
				}
				// Write to cache.
				$db->cacheWrite("roles_{$user_id}", $this->user->mergeRoles);
			} else {
				// Read from cache.
				$user->mergeRoles = $db->cacheRead("roles_{$user_id}");
			}

			// Nothing there? Fallback to nothing.
			if (empty($user->mergeRoles))
				$user->mergeRoles = 0;

			// What should we return, array or , string.
			if ($return_array == false) {
				// Ok return string.
				return $user->mergeRoles;
			} else {
				// Ok return array.
				return explode(',', $user->mergeRoles);
			}

		} else {
			$settings = $db->essentialSettings;
			$guest_role = $settings['guest_role'];
			if (empty($guest_role)) throw new PHPDS_Exception('Unable to get the GUEST ROLE from the essential settings.');
			return $return_array ? array($guest_role) : $guest_role;
		}
	}
}

/**
 * USER - Get extra users groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_getExtraGroupsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id
		FROM
			_db_core_user_extra_groups
		%s
	";
}

/**
 * USER - Find group children.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 * @date 20110504 (jason) (v1.0.0)
 *
 */
class USER_findGroupChildren extends PHPDS_query
{
	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return string
	 */
	public function invoke($parameters = null)
	{
		list($group_id) = $parameters;
		$user = $this->user;
		$group_string = '';
		// Check what children belongs to parent.
		if (! empty($user->parentGroups[$group_id])) {
			foreach ($user->parentGroups[$group_id] as $group) {
				// Seek grand children.
				if (! empty($user->parentGroups[$group]))
					$group_string .= $user->findGroupChildren($group);
				$group_string .= "{$group},";
			}
		}
		// Return found results.
		return $group_string;
	}
}

/**
 * USER - Get users groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 * @date 20110504 (jason) (v1.0.3) improved functionality and stability.
 *
 */
class USER_getGroupsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group
		FROM
			_db_core_users
		%s
	";

	protected $singleValue = true;

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($user_id, $return_array) = $parameters;
		$configuration = $this->configuration;
		$user = $this->user;
		$db = $this->db;
		$group_string = '';

		// First try to assign default user id.
		if (empty($user_id)) {
			(!empty($configuration['user_id'])) ? $user_id = $configuration['user_id'] : $user_id = false;
		}
		
		// Check if user is a guest.
		if (!empty($user_id)) {
			// Check groups cache.
			if ($db->cacheEmpty("groups_{$user_id}")) {
				// Check if we have a saved variable.
				if (empty($user->mergeGroups)) {
					// Get all available groups.
					$all_groups = $db->invokeQuery('USER_getGroupsChildrenQuery');

					// Get requested users group user groups.
					if ($user_id == $configuration['user_id']) {
						$group_main = $configuration['user_group'];
					} else {
						$query = " WHERE user_id = $user_id ";
						// Get main group for user.
						$group_main = parent::invoke($query);
					}

					// Do we have a root user.
					if ($user->isRoot($user_id)) {
						// get all groups.
						$group_array = $all_groups;

						// Check if anything is selected.
						if (! empty($group_array) && is_array($group_array)) {
							// Loop Groups and return string.
							foreach ($group_array as $group_arr) {
								// All groups for root.
								$group_string .= "{$group_arr['user_group_id']},";
							}
						}
					} else {
						// Find all group parents.
						if (! empty($all_groups)) {
							foreach ($all_groups as $group_) {
								// Assign parent.
								if (! empty($group_['parent_group_id'])) {
									$parent_group[$group_['parent_group_id']][] = $group_['user_group_id'];
								}
							}
							if (! empty($parent_group)) {
								$user->parentGroups = $parent_group;
							} else {
								$user->parentGroups = array();
							}
						}

						// Get all extra groups for user.
						$query = " WHERE user_id = $user_id ";
						$group_array = $db->invokeQuery('USER_getExtraGroupsQuery', $query);
						
						// Also check children of main group.
						$group_array[$group_main] = array('user_group_id'=>$group_main);

						// Check if anything is selected.
						if (! empty($group_array) && is_array($group_array)) {
							// Loop Groups and return string.
							foreach ($group_array as $group_arr) {
								// Check if group is a parent, if it is, we need to tree down.
								if (! empty($parent_group[$group_arr['user_group_id']]))
									// We have a parent, who is the children.
									$group_string .= $user->findGroupChildren($group_arr['user_group_id']);

								$group_string .= "{$group_arr['user_group_id']},";
							}
						}
					}

					// Merger primary and extra groups.
					$user->mergeGroups = rtrim("$group_main," . $group_string, ',');
				}
				// Write to cache.
				$db->cacheWrite("groups_{$user_id}", $user->mergeGroups);
			} else {
				// Read from cache.
				$user->mergeGroups = $db->cacheRead("groups_{$user_id}");
			}

			// Nothing there? Fallback to nothing.
			if (empty($user->mergeGroups))
				$user->mergeGroups = 0;
	
			// What should we return, array or , string.
			if ($return_array == false) {
				// Ok return string.
				return $user->mergeGroups;
			} else {
				// Ok return array.
				return explode(',', $user->mergeGroups);
			}
		} else {
			$settings = $db->essentialSettings;
			return $settings['guest_group'];
		}
	}
}

/**
 * USER - Get users groups children.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_getGroupsChildrenQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id, parent_group_id
		FROM
			_db_core_user_groups
	";
}

/**
 * USER - Get users groups by alias.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_getGroupsbyAliasQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id
		FROM
			_db_core_user_groups
		WHERE 
			user_group_id IN (%s)
		AND
			alias = '%s'
		LIMIT 0,1
	";
	
	protected $singleValue = true;
}

/**
 * USER - Get user roles.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_roleExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id
		FROM
			_db_core_user_roles
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$role_id = $parameters[0];
		$user = $this->user;

		if (empty($user->rolesArray)) {
			// Do roles query.
			$roles = parent::invoke();
			// Write array.
			foreach ($roles as $results_array) {
				$user->rolesArray[$results_array['user_role_id']] = true;
			}
		}
		// Do we have a role like this?
		if (! empty($user->rolesArray[$role_id])) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * USER - Get user groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_groupExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id
		FROM
			_db_core_user_groups
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke ($parameters = null)
	{
		$group_id = $parameters[0];
		$user = $this->user;
		
		if (empty($user->groupsArray)) {
			// Do groups query.
			$groups = parent::invoke();
			// Write array.
			foreach ($groups as $results_array) {
				$user->groupsArray[$results_array['user_group_id']] = true;
			}
		}
		// Do we have a group like this?
		if (!empty($user->groupsArray[$group_id])) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * USER - Check if user belongs to role.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class USER_belongsToRoleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id
		FROM
			_db_core_users t1
		LEFT JOIN
			_db_core_user_extra_roles t2
		ON
			t1.user_id = t2.user_id
		WHERE
			(t1.user_role = %u or t2.user_role_id = %u)
		AND
			(t1.user_id = %u or t2.user_id = %u)
	";

	protected $singleRow = true;

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($user_id, $user_role) = $parameters;
		// We need to check what user role this user belongs to.
		// This user belongs to root role, so it is safe to allow.
		if ($this->user->isRoot($user_id)) return true;
		// Check if we have a user id.
		if (empty($user_id)) {
			// Use default id.
			(!empty($this->configuration['user_id'])) ? $user_id = $this->configuration['user_id'] : $user_id = false;
		}
		// First lets query if this user belongs to given role.
		$check_user_in_role_db = parent::invoke(array($user_role, $user_role, $user_id, $user_id));

		// Lets check if this user exists.
		if ($check_user_in_role_db['user_id'] == $user_id) {
			return true;
		} else {
			return false;
		}
	}
}


