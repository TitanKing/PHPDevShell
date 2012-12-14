<?php

/**
 * Lost Password - Read User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_ReadUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_password, user_name, user_email
		FROM
			_db_core_users
		WHERE
			user_name = '%s'
		OR
			user_email = '%s'
    ";
	protected $singleRow = true;
}