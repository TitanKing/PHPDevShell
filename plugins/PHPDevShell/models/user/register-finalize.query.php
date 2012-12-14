<?php

/**
 * Register Finalize - Read User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_ReadUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id, t1.user_display_name, t1.user_name, t1.user_email,
			t2.registration_type, t2.token_id,
			t3.token_name, t3.user_role_id, t3.user_group_id, t3.token_key, t3.available_tokens
		FROM
			_db_core_users t1
		LEFT JOIN
			_db_core_registration_queue t2
		ON
			t1.user_id = t2.user_id
		LEFT JOIN
			_db_core_registration_tokens t3
		ON
			t2.token_id = t3.token_id
		WHERE
			md5(concat(t1.user_id,t1.user_name,t1.user_email)) = '%s'
		AND
			t2.registration_type = 1
    ";
	protected $singleRow = true;
}

/**
 * Register Finalize - Update User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_UpdateUserQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_users
		SET
			user_role  = %u,
			user_group = %u
		WHERE
			user_id	= %u
    ";
}

/**
 * Register Finalize - Update Tokens
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_UpdateTokens extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_registration_tokens
		SET
			available_tokens = available_tokens - 1
		WHERE
			token_id = %u
    ";
}