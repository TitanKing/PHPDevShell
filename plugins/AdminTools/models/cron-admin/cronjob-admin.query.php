<?php

/**
 * Cronjob Admin - Select Edit Node
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_selectEditNodeQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id, t1.node_name, t1.node_link, t1.node_type, t1.plugin,
			t2.cron_desc, t2.cron_type, t2.log_cron, t2.last_execution, t2.year, t2.month, t2.day, t2.hour, t2.minute
		FROM
			_db_core_node_items t1
		LEFT JOIN
			_db_core_cron t2
		ON
			t1.node_id = t2.node_id
		WHERE
			t1.node_type = 8
		AND
			t1.node_id = '%s'
	";
	protected $singleRow = true;
}

/**
 * Cronjob Admin - Save Cron
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_saveCronQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_cron (node_id, cron_desc, cron_type, log_cron, last_execution, year, month, day, hour, minute)
		VALUES
			('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
	";
}