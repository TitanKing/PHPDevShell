<?php

/**
 * Optimize Database - Fetch Tables
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_fetchTablesToOptimizeQuery extends PHPDS_query
{
	protected $sql = "
		SHOW TABLES FROM
			%s
	";

	protected $keyField = '__auto__';
}

/**
 * Optimize Database - Optimize Tables
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_optimizeTablesQuery extends PHPDS_query
{
	protected $sql = "
		OPTIMIZE TABLE
			%s
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Define.
		$table_data = $parameters[0];
		$tables_ = '';
		$tables = '';
		$tables_to_optimize = '';

		// Fetch table data.
		foreach ($table_data as $table_name => $all_tables_array) {
			$tables = $table_name;
			$tables_ .= $tables . ',';
		}

		// Write DB string.
		$tables_to_optimize = rtrim($tables_, ',');

		// Query the database
		$optimize_results = parent::invoke($tables_to_optimize);

		// Print Results
		foreach ($optimize_results as $optimize_results_array) {
			$table = $optimize_results_array['Table'];
			$Op = $optimize_results_array['Op'];
			$msg_type = $optimize_results_array['Msg_type'];
			$msq_text = $optimize_results_array['Msg_text'];
			// This is used for template preview when debugging.
			$RESULTS[] = array(
				'table' => $table,
				'Op' => $Op,
				'msg_type' => $msg_type,
				'msq_text' => $msq_text
			);
		}
		if ($RESULTS) return $RESULTS;
		else return false;
	}
}