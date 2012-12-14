<?php

/**
 * New Password - Read User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_ReadUserMDCryptQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_display_name, user_password, user_name, user_email
		FROM
			_db_core_users
		WHERE
			md5(concat(user_name,user_email,user_password)) = '%s'
    ";
	protected $singleRow = true;
}

/**
 * New Password - Read User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_ReadUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_display_name, user_password, user_name, user_email
		FROM
			_db_core_users
		WHERE
			user_name = '%s'
    ";
	protected $singleRow = true;
}

/**
 * New Password - Update User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_UpdateUserQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_users
		SET
			user_password = '%s'
		WHERE
			user_name = '%s'
		AND
			user_email = '%s'
    ";
}