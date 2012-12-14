<?php

/**
 * User Pending - Read pending user.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readPendingQueueQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.token_id, t1.registration_type,
			t2.user_role_id, t2.user_group_id, t2.available_tokens,
			t3.user_display_name, t3.user_email
		FROM
			_db_core_registration_queue t1
		LEFT JOIN
			_db_core_registration_tokens t2
		ON
			t1.token_id = t2.token_id
		LEFT JOIN
			_db_core_users t3
		ON
			t1.user_id = t3.user_id
		WHERE
			t1.user_id = %u
    ";

	protected $singleRow = true;
}

/**
 * User Pending - Update pending user.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updatePendingQueueQuery extends PHPDS_query
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
 * User Pending - Update registration tokes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateTokensQueueQuery extends PHPDS_query
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


