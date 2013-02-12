<?php

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
		return true;
	}
}

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
		return true;
	}
}

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
			$parameters = array($user_id, $this->user->setGroupQuery("AND user_group = {$this->user->getGroups()}"));
		} else return false;
	}
}

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

    public function invoke($parameters = null)
    {
        list($user_id) = $parameters;
        $configuration = $this->configuration;
        $db = $this->db;

        if (empty($user_id))
            $user_id = (! empty($configuration['user_id'])) ? $configuration['user_id'] : 0;

        // Check if user is a guest.
        if (! empty($user_id)) {
            if ($user_id == $configuration['user_id']) {
                return $configuration['user_role'];
            } else {
                $role = parent::invoke($user_id);
                return $role;
            }
        } else {
            $settings = $db->essentialSettings;
            $guest_role = $settings['guest_role'];
            if (empty($guest_role)) throw new PHPDS_Exception('Unable to get the GUEST ROLE from essential settings.');
            return $guest_role;
        }
    }
}

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

    public function invoke($parameters = null)
    {
        list($user_id) = $parameters;
        $configuration = $this->configuration;
        $db = $this->db;

        if (empty($user_id))
            $user_id = (! empty($configuration['user_id'])) ? $configuration['user_id'] : 0;

        // Check if user is a guest.
        if (! empty($user_id)) {
            if ($user_id == $configuration['user_id']) {
                return $configuration['user_group'];
            } else {
                $role = parent::invoke($user_id);
                return $role;
            }
        } else {
            $settings = $db->essentialSettings;
            $guest_role = $settings['guest_group'];
            if (empty($guest_role)) throw new PHPDS_Exception('Unable to get the GUEST GROUP from essential settings.');
            return $guest_role;
        }
    }
}

class USER_roleExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id
		FROM
			_db_core_user_roles
	";

	public function invoke($parameters = null)
	{
		$role_id = $parameters[0];
		$user = $this->user;

		if (empty($user->rolesArray)) {
			$roles = parent::invoke();
			foreach ($roles as $results_array) {
				$user->rolesArray[$results_array['user_role_id']] = true;
			}
		}
		if (! empty($user->rolesArray[$role_id])) {
			return true;
		} else {
			return false;
		}
	}
}

class USER_groupExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id
		FROM
			_db_core_user_groups
	";

	public function invoke ($parameters = null)
	{
		$group_id = $parameters[0];
		$user = $this->user;

		if (empty($user->groupsArray)) {
			$groups = parent::invoke();
			foreach ($groups as $results_array) {
				$user->groupsArray[$results_array['user_group_id']] = true;
			}
		}

		if (!empty($user->groupsArray[$group_id])) {
			return true;
		} else {
			return false;
		}
	}
}

class USER_belongsToRoleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id
		FROM
			_db_core_users t1
		WHERE
			(t1.user_role = %u)
		AND
			(t1.user_id = %u)
	";

	protected $singleRow = true;

	public function invoke($parameters = null)
	{
		list($user_id, $user_role) = $parameters;
		if ($this->user->isRoot($user_id)) return true;

		if (empty($user_id)) {
			(!empty($this->configuration['user_id'])) ? $user_id = $this->configuration['user_id'] : $user_id = null;
		}

		$check_user_in_role_db = parent::invoke(array($user_role, $user_id));

		if ($check_user_in_role_db['user_id'] == $user_id) {
			return true;
		} else {
			return false;
		}
	}
}

class USER_belongsToGroupQuery extends PHPDS_query
{
    protected $sql = "
		SELECT
			t1.user_id
		FROM
			_db_core_users t1
		WHERE
			(t1.user_group = %u)
		AND
			(t1.user_id = %u)
	";

    protected $singleRow = true;

    public function invoke($parameters = null)
    {
        list($user_id, $user_group) = $parameters;
        if ($this->user->isRoot($user_id)) return true;

        if (empty($user_id)) {
            (!empty($this->configuration['user_id'])) ? $user_id = $this->configuration['user_id'] : $user_id = null;
        }

        $check_user_in_group_db = parent::invoke(array($user_group, $user_id));

        if ($check_user_in_group_db['user_id'] == $user_id) {
            return true;
        } else {
            return false;
        }
    }
}


