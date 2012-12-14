<?php

/**
 * Email Token - Get Token Data
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getTokenDataQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.token_id, t1.token_name, t1.user_role_id, t1.user_group_id, t1.token_key, t1.registration_option, t1.available_tokens,
			t2.user_role_name, t3.user_group_name
		FROM
			_db_core_registration_tokens t1
		LEFT JOIN
		    _db_core_user_roles t2
		ON
		    t1.user_role_id = t2.user_role_id
		LEFT JOIN
		    _db_core_user_groups t3
		ON
		    t1.user_group_id = t3.user_group_id
		WHERE
			t1.token_id = %u
	";
	protected $singleRow = true;
}