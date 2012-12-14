<?php

/**
 * LOGIN - Gets user detail from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class LOGIN_selectUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id, t1.user_display_name, t1.user_password, t1.user_name, t1.user_email, t1.user_group, t1.user_role, t1.language, t1.timezone as user_timezone, t1.region,
			t2.user_group_name, t3.user_role_name
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
		WHERE
			(t1.user_name = '%(username)s' OR t1.user_email = '%(username)s')
		AND
			IF('%(password)s' = '*', 1, t1.user_password = '%(password)s')
	";
	protected $singleRow = true;

	public function checkParameters(&$parameters = null)
	{
		list($username, $password) = $parameters;
		$parameters = $this->protectArray(array('username' => $username, 'password' => $password));
		// The parameters are considered as valid.
		return true;
	}

	public function checkResults(&$results = null)
	{
		if (empty($results))
			$results = false; //  to be consistent with the original API
		return true;
	}
}

/**
 * LOGIN - Checks if username exists.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class LOGIN_selectUserNameQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id
		FROM
			_db_core_users t1
		WHERE
			(t1.user_name = '%(username)s' OR t1.user_email = '%(username)s')
	";
	protected $singleRow = true;

	public function checkParameters(&$parameters = null)
	{
		list($username) = $parameters;
		$parameters = $this->protectArray(array('username' => $username));
		// The parameters are considered as valid.
		return true;
	}

	public function checkResults(&$results = null)
	{
		if (empty($results))
			$results = false; //  to be consistent with the original API
		return true;
	}
}

















