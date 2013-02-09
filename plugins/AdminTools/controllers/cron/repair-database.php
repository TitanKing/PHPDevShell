<?php

class RepairDatabase extends PHPDS_controller
{

	/**
	 * Simply optimizes Database.
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 24 June 2010
	 */
	public function execute()
	{
		$this->template->heading(__('Repair Database'));
		$this->template->info(__('Repairs corrupted and broken tables from core and all installed plugins.'));

		// Get all tables from database.\
		$all_tables = $this->db->invokeQuery('PHPDS_fetchTablesToRepairQuery', $this->db->dbName);

		// Repair the tables
		$RESULTS = $this->db->invokeQuery('PHPDS_repairTablesQuery', $all_tables);
		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);

		// Output Template.
		$view->show();
	}
}

return 'RepairDatabase';