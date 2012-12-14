<?php

/**
 * Group Tree - Read group data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readGroupTreeQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id, user_group_name, user_group_note, parent_group_id, alias
		FROM
			_db_core_user_groups
		%s
		ORDER BY
			parent_group_id, user_group_name
    ";
}


