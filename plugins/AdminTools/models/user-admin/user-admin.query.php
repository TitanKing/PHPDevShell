<?php

/**
 * User Admin - Read User
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserQuery extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_id, user_display_name, user_password, user_name, user_email, user_group, user_role, date_registered, language, timezone as user_timezone, region
			FROM
				_db_core_users
			WHERE
				user_id = %u
				%s
		";
	protected $singleRow = true;

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$user_id = $parameters[0];
		$groups = $this->user->setGroupQuery("AND user_group IN ({$this->user->getGroups()})");
		return parent::invoke(array($user_id, $groups));
	}
}

/**
 * User Admin - Read User Detail
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserDetailQuery extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_id, user_name, user_display_name, user_email
			FROM
				_db_core_users
			WHERE
				(user_id != %u)
			AND
				(user_name = '%s' OR user_email = '%s')
		";
	protected $singleRow = true;
}

/**
 * User Admin - Write User Detail
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeUserQuery extends PHPDS_query
{
	protected $sql = "
			INSERT INTO
				_db_core_users (user_id, user_display_name, user_name, user_password, user_email, user_group, user_role, date_registered, language, timezone, region)
			VALUES ('', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
		";
	protected $returnId = true;
}

/**
 * User Admin - Update User Detail
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_updateUserQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_users
		SET
			user_display_name = '%s',
			user_name = '%s',
			%s
			user_email = '%s',
			%s
			user_group = '%s',
			user_role  = '%s',
			language   = '%s',
			timezone   = '%s',
			region     = '%s'
		WHERE
			user_id = %u
		";
	protected $returnId = true;

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Set parameters
		$edit['user_display_name'] = $parameters[0];
		$edit['user_name'] = $parameters[1];
		$edit['user_password'] = $parameters[2];
		$edit['user_email'] = $parameters[3];
		$edit['date_registered'] = $parameters[4];
		$edit['user_group'] = $parameters[5];
		$edit['user_role'] = $parameters[6];
		$edit['language'] = $parameters[7];
		$edit['user_timezone'] = $parameters[8];
		$edit['region'] = $parameters[9];
		$edit['user_id'] = $parameters[10];

		// User password
		if (!empty($edit['user_password'])) {
			$insert_encrypt_pass = md5($edit['user_password']);
			$edit['user_password'] = "user_password = '$insert_encrypt_pass',";
		}

		// Check if a date registered is required.
		if (empty($edit['date_registered'])) {
			$time_reg = $this->configuration['time'];
			$edit['date_registered'] = "date_registered = '$time_reg',";
		} else {
			$edit['date_registered'] = '';
		}

		return parent::invoke($edit);
	}
}

/**
 * User Admin - Delete Extra Groups
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_deleteExtraGroupsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_extra_groups
		WHERE
			user_id = %u
		";
}

/**
 * User Admin - Replace Extra Groups
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_replaceExtraGroupsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_extra_groups (user_id, user_group_id)
		VALUES
		%s
		";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{

		// Make sure $edit['extra_roles'] does contain values.
		$edit['extra_groups'] = $parameters[0];
		$edit['user_id'] = $parameters[1];

		if (!empty($edit['extra_groups'])) {
			// Create user groups db values.
			$group_id_db = '';
			foreach ($edit['extra_groups'] as $group_id_) {
				// Is it a branch admin, and if it is, can he save to these groups?
				if ($this->user->belongsToGroup(false, $group_id_)) {
					$group_id_db .= "('{$edit['user_id']}', '$group_id_')" . ',';
				}
			}
			// Remove last comma from db insert for $group_id_db.
			$group_id_db = rtrim($group_id_db, ',');
		}
		if (!empty($group_id_db)) parent::invoke(array($group_id_db));
	}
}

/**
 * User Admin - Selected Extra Groups
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_selectedGroupsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id
		FROM
			_db_core_user_extra_groups
		WHERE
			user_id = %u
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
		$g = parent::invoke($parameters);
		if (!empty($g)) {
			foreach ($g as $extra_groups_results_array) {
				$user_group_id_form[$extra_groups_results_array['user_group_id']] = 'selected';
			}
			if (!empty($user_group_id_form)) {
				return $user_group_id_form;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}
}

/**
 * User Admin - Read Roles
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRolesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id, t1.user_role_name
		FROM
			_db_core_user_roles t1
			%s
		ORDER BY
			t1.user_role_id
		ASC
		";
	protected $keyField = 'user_role_id';
	protected $focus = 'user_role_name';

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		return parent::invoke(array($this->user->setRoleQuery("WHERE t1.user_role_id IN ({$this->user->getRoles()})")));
	}
}

/**
 * User Admin - Group Tree
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_GroupTreeQuery extends PHPDS_query
{
	protected $sql = '';

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$edit['user_group'] = $parameters[0];

		// Compile and list group tree.
		$group = $this->factory('groupTree');
		// Modify query to database.
		// Last but not least compile needed results.
		$group->compileResults(false, true, $this->user->setGroupQuery("WHERE user_group_id IN ({$this->user->getGroups()}) AND user_group_id != '{$edit['user_group']}'", "WHERE user_group_id != '{$edit['user_group']}'"), $edit['user_group']);
		return $group->groupArray;
	}
}

/**
 * User Admin - Primary Group Tree
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_PrimaryGroupTreeQuery extends PHPDS_query
{
	protected $sql = '';

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$group = $this->factory('groupTree');
		$group->compileResults(false, true, $this->user->setGroupQuery("WHERE user_group_id IN ({$this->user->getGroups()})"));
		return $group->groupArray;
	}
}