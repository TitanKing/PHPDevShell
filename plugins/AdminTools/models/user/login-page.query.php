<?php

/**
 * Login Page - Clear Persistent Cookies.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_clearPersistentDBQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_session
		WHERE
			user_id = %s
	";
}