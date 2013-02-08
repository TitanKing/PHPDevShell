<?php

class OptimizeDatabase extends PHPDS_controller
{

	/**
	 * Simply optimizes Database.
	 * @author Greg
	 * @since 24 June 2010
	 */
	public function execute()
	{
		$this->template->heading(__('Optimize Database'));
		$this->template->info(__('Does a table repair and a key analysis, and also sorts the index tree so that key lookups are faster.'));
		
		// Get all tables from database.
		$all_tables = $this->db->invokeQuery('PHPDS_fetchTablesToOptimizeQuery', $this->db->dbName);

		// Optimize the tables
		$RESULTS = $this->db->invokeQuery('PHPDS_optimizeTablesQuery', $all_tables);
		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);

		// Output Template.
		$view->show();
	}
}

return 'OptimizeDatabase';