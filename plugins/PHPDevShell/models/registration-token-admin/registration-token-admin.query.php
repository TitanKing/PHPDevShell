<?php

/**
 * Registration Token Admin - Get Token Data
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getTokenQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			token_id, token_name, user_role_id, user_group_id, token_key, registration_option, available_tokens
		FROM
			_db_core_registration_tokens
		WHERE
			token_id = %u
	";
	protected $singleRow = true;
}

/**
 * Registration Token Admin - Save Registration Tokens
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_saveRegistrationTokensQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_registration_tokens (`token_id`, `token_name`, `user_role_id`, `user_group_id`, `token_key`, `registration_option`, `available_tokens`)
		VALUES
			(%u, '%s', '%s', '%s', '%s', '%s', '%s')
	";
	protected $returnId = true;
}

/**
 * Registration Token Admin - Get User Roles
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_tokensGetRolesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id, t1.user_role_name
		FROM
			_db_core_user_roles t1
		ORDER BY
			t1.user_role_id
		ASC
	";

	public function invoke($parameters = null)
	{
		$user_role_id = $parameters[0];
		$user_roles_db = parent::invoke();

		$user_roles_option_move = false;
		$user_groups_option_move = false;
		if (empty($user_roles_db)) $user_roles_db = array();
		foreach ($user_roles_db as $user_roles_array) {
			// Check selected.
			($user_roles_array['user_role_id'] == $user_role_id) ? $moved_role_selected = 'selected' : $moved_role_selected = '';
			$user_roles_option_move .= '<option value="' . $user_roles_array['user_role_id'] . '" ' . $moved_role_selected . '>' . $user_roles_array['user_role_name'] . '</option>';
		}

		return $user_roles_option_move;
	}
}

/**
 * Registration Token Admin - Get User Groups
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_tokensGetGroupsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_group_id, t1.user_group_name
		FROM
			_db_core_user_groups t1
		ORDER BY
			t1.user_group_id
		ASC
	";

	public function invoke($parameters = null)
	{
		$user_group_id = $parameters[0];
		$user_groups_db = parent::invoke();

		$user_groups_option_move = '';
		if (empty($user_groups_db)) $user_groups_db = array();
		foreach ($user_groups_db as $user_groups_array) {
			// Check selected.
			($user_groups_array['user_group_id'] == $user_group_id) ? $moved_group_selected = 'selected' : $moved_group_selected = '';
			$user_groups_option_move .= '<option value="' . $user_groups_array['user_group_id'] . '" ' . $moved_group_selected . '>' . $user_groups_array['user_group_name'] . '</option>';
		}

		return $user_groups_option_move;
	}
}