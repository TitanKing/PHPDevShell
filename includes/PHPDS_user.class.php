<?php

class PHPDS_user extends PHPDS_dependant
{
	/**
	 * Stores string of groups for specific user.
	 *
	 * @var string
	 */
	public $mergeGroups;
	/**
	 * Stores string of roles for specific user.
	 *
	 * @var string
	 */
	public $mergeRoles;
	/**
	 * Set roles that exists.
	 *
	 * @var array
	 */
	public $rolesArray;
	/**
	 * Set groups that exists.
	 *
	 * @var array
	 */
	public $groupsArray;
	/**
	 * Array contains parent groups.
	 *
	 * @var array
	 */
	public $parentGroups;
	/**
	 * Array contains alias permission for groups.
	 *
	 * @var array
	 */
	public $userBelongsToGroupByAlias;

	/**
	 * This function gets all role id's for a given user id, while returning a string divided by ',' character or an array with ids.
	 * To pull multiple user roles, provide a string for $user_ids like so: '2,5,10,19'.
	 *
	 * @param string $user_id
	 * @param boolean $return_array
	 * @return mixed If $return_array = false a comma delimited string will be returned, else an array.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function getRoles($user_id = false, $return_array = false)
	{
		return $this->db->invokeQuery('USER_getRolesQuery', $user_id, $return_array);
	}

	/**
	 * This function gets all group id's for given user ids, while returning a string divided by ',' character or an array with ids.
	 * To pull multiple user groups, provide a string for $user_ids like so : '2,5,10,19'.
	 *
	 * @param string $user_id Leave this field empty if you want skip if user is root.
	 * @param boolean $return_array
	 * @return mixed If $return_array = false a comma delimited string will be returned, else an array.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function getGroups($user_id = false, $return_array = false, $alias_only = false)
	{
		$groups = $this->db->invokeQuery('USER_getGroupsQuery', $user_id, $return_array);
		return $groups;
	}

	/**
	 * Will dig deeper for children of groups.
	 *
	 * @param int $group_id
	 * @return string
	 */
	public function findGroupChildren($group_id)
	{
		return $this->db->invokeQuery('USER_findGroupChildren',$group_id);
	}

	/**
	 * Simple check to see if a certain role exists.
	 *
	 * @param integer $role_id
	 * @return boolean
	 */
	public function roleExist($role_id)
	{
		return $this->db->invokeQuery('USER_roleExistQuery', $role_id);
	}

	/**
	 * Simple check to see if a certain group exists.
	 *
	 * @param integer $group_id
	 * @return boolean
	 */
	public function groupExist($group_id)
	{
		return $this->db->invokeQuery('USER_groupExistQuery', $group_id);
	}

	/**
	 * Check if user belongs to given role. Returns true if user belongs to user role.
	 *
	 * @param integer $user_id
	 * @param integer $user_role
	 * @param string $alias
	 * @return boolean Returns true if user belongs to user role.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function belongsToRole($user_id = false, $user_role=null, $alias=null)
	{
		return $this->db->invokeQuery('USER_belongsToRoleQuery', $user_id, $user_role);
	}

	/**
	 * Check if user belongs to given group. Returns true if user belongs to user group.
	 *
	 * @param integer $user_id
	 * @param integer $user_group
	 * @param string $alias
	 * @return boolean Returns true if user belongs to user group.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function belongsToGroup($user_id = null, $user_group=null, $alias=null)
	{
		if ($this->user->isRoot($user_id)) return true;
		if (empty($user_id)) {
			(!empty($this->configuration['user_id'])) ? $user_id = $this->configuration['user_id'] : $user_id = false;
		}

		if (! empty($user_group)) {
			$forarrayflip = $this->getGroups($user_id, true);
			if (is_array($forarrayflip)) {
				$user_groups_arr = array_flip($this->getGroups($user_id, true));
			} else {
				return false;
			}
			if (array_key_exists($user_group, $user_groups_arr)) {
				return true;
			} else {
				return false;
			}
		}
		if (! empty($alias)) {
			if (! empty($this->userBelongsToGroupByAlias[$user_id][$alias]))
				return true;

			$groups = $this->getGroups($user_id);

			if (! empty($groups)) {
				$byalias = $this->db->invokeQuery('USER_getGroupsbyAliasQuery', $groups, $alias);
				if (! empty($byalias)) {
					$this->userBelongsToGroupByAlias[$user_id][$alias] = true;
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} return false;
	}

	/**
	 * Check if user belongs to given group by alias for performed actions on a page. Returns true if user belongs to user group.
	 *
	 * @param string $alias
	 * @param integer $user_id
     * @return boolean
	 */
	public function groupCan($alias, $user_id=0)
	{
		return $this->belongsToGroup($user_id, null, $alias);
	}

	/**
	 * Creates a query to extend a role query, it will return false if user is root so everything can get listed.
	 * This is meant to be used inside an existing role query.
	 *
	 * @param string $query_request Normal query to be returned if user is not a root user.
	 * @param string $query_root_request If you want a query to be processed for a root user seperately.
	 * @return mixed
	 */
	public function setRoleQuery($query_request, $query_root_request = null)
	{
		if ($this->user->isRoot()) {
			if (!empty($query_root_request)) {
				return " $query_root_request ";
			} else {
				return false;
			}
		} else {
			return " $query_request ";
		}
	}

    /**
     * Creates a query to extend a group query, it will return false if user is root so everything can get listed.
     * This is meant to be used inside an existing group query.
     *
     * @param string $query_request Normal query to be returned if user is not a root user.
     * @param string $query_root_request If you want a query to be processed for a root user seperately.
     * @return mixed
     */
	public function setGroupQuery($query_request, $query_root_request = null)
	{
		if ($this->user->isRoot()) {
			if (!empty($query_root_request)) {
				return " $query_root_request ";
			} else {
				return false;
			}
		} else {
			return " $query_request ";
		}
	}

	/**
	 * Check if user is a root user.
	 *
	 * @date 20100608 (v1.0.1) (greg) moved to query system
	 * @param mixed $user_id If not logged in user, what user should be checked (primary role check only).
	 * @return boolean
	 */
	public function isRoot($user_id = false)
	{
		if (!empty($user_id)) {
			if ($this->configuration['user_id'] == $user_id) {
				if ($this->configuration['user_role'] == $this->configuration['root_role']) {
					return true;
				} else {
					return false;
				}
			} else {
				$check_role_id = $this->db->invokeQuery('USER_isRootQuery', $user_id);
				if ($check_role_id == $this->configuration['root_role']) {
					return true;
				} else {
					return false;
				}
			}
		} else if (($this->configuration['user_role'] == $this->configuration['root_role'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns current logged in user id.
	 *
	 * @return integer
	 */
	public function currentUserID()
	{
		if (! empty($this->configuration['user_id'])) {
			return $this->configuration['user_id'];
		} else {
			return false;
		}
	}

	/**
	 * Check if the currently logged in user is the same group as the given user.
	 *
	 * This can be used to check if the current user is allowed access to the given user's data
	 *
	 * @date 20100222
	 * @version	1.0
	 * @author greg
	 * @param $user_id integer, the ID of the other user
	 * @return boolean, whether the current user is in the same group
	 */
	public function isSameGroup($user_id)
	{
		$edit = $this->db->invokeQuery('USER_isSameGroupQuery', $user_id);
		return (!empty($edit['user_id']));
	}

	/**
	 * Simple method to return users IP, this method will be improved in the future if needed.
	 *
	 * @return string
	 */
	public function userIp()
	{
		return $this->getUserIp();
	}

	/**
	 * Simple method to return users IP, this method will be improved in the future if needed.
	 *
	 * @version 1.0.1
	 * @date 20110315 (v1.0.1) (greg) fix a possible undef when not used through a webserver
	 *
	 * @return string
	 */
	public function getUserIp()
	{
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
		}
	}

	/**
	 * Check is user is logged in, return false if not.
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		if (empty($_SESSION['user_id'])) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check if a user has access to a given menu id.
	 *
	 * @version 1.0.1
	 * @date 20091105 fixed a possible warning when the menu is not in the list (i.e. the user is not allowed)
	 *
	 * @param mixed This can have both the menu id as an integer or as a string.
	 * @param string The type of item requested, menu_id, menu_name etc...
	 * @return boolean Will return requested variable if user has access to requested menu item menu item.
	 */
	public function canAccessMenu($menu_id, $type = 'menu_id')
	{
		if (!empty($this->navigation->navigation[$menu_id][$type])) {
			return $this->navigation->navigation[$menu_id][$type];
		} else {
			return false;
		}
	}

	/**
	 * Simply writes user session data.
	 *
	 * @date 20110622
	 * @version 1.1
	 * @author Jason Schoeman
	 */
	public function userConfig()
	{
		$conf = $this->configuration;

        $conf['user_id']           = empty($_SESSION['user_id']) ? 0 : $_SESSION['user_id'];
        $conf['user_name']         = empty($_SESSION['user_name']) ? '' : $_SESSION['user_name'];
        $conf['user_display_name'] = empty($_SESSION['user_display_name']) ? '' : $_SESSION['user_display_name'];
        $conf['user_group']        = empty($_SESSION['user_group']) ? 0 : $_SESSION['user_group'];
        $conf['user_role']         = empty($_SESSION['user_role']) ? 0 : $_SESSION['user_role'];
        $conf['user_email']        = empty($_SESSION['user_email']) ? '' : $_SESSION['user_email'];
        $conf['user_language']     = empty($_SESSION['user_language']) ? '' : $_SESSION['user_language'];
        $conf['user_region']       = empty($_SESSION['user_region']) ? '' : $_SESSION['user_region'];
        $conf['user_timezone']     = empty($_SESSION['user_timezone']) ? '' : $_SESSION['user_timezone'];
        $conf['user_locale']       = empty($_SESSION['user_locale']) ? $this->core->formatLocale() : $_SESSION['user_locale'];
	}

	/**
	 * Actual processing of login page.
	 *
	 * @verion 1.0.0
	 * @date 2011-06-20
	 * @author Jason Schoeman
	 */
	public function controlLogin()
	{
		if (! isset($_SESSION['user_id']) || ! empty($_POST['login']) || ! empty($_REQUEST['logout'])) {
			$this->factory('StandardLogin')->controlLogin();
		}
		$this->userConfig();
	}
}
