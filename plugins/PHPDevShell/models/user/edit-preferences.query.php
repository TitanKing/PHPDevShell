<?php

/**
 * Edit Preferences - Read existing users preferences.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserDetailQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id, t1.user_display_name,  t1.user_name, t1.user_email, t1.user_group, t1.user_role, t1.date_registered, t1.language, t1.timezone as user_timezone, t1.region,
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
			t1.user_id = %u
	";
	protected $singleRow = true;
}

/**
 * Edit Preferences - Read lesser existing users preferences.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserDetailLightQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_name, user_display_name, user_email
		FROM
			_db_core_users
		WHERE
			(user_id != %u)
		AND
			(user_name = '%s'
		OR
			user_display_name = '%s'
		OR
			user_email = '%s')
	";
	protected $singleRow = true;
}

/**
 * Edit Preferences - Read lesser existing users preferences.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_updateUserDetailQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_users
		SET
			user_display_name = '%s',
			user_name         = '%s',
			user_email        = '%s',
			language          = '%s',
			timezone          = '%s',
			region            = '%s'
		WHERE
			user_id           = %u
	";
}