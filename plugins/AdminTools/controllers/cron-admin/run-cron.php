<?php

class RunCron extends PHPDS_controller
{

	/**
	 * Runs the cronjobs.
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 24 June 2010
	 */
	public function execute()
	{
		// Dont merge content, we want to display as little as possible.
		$this->core->themeFile = '';
		// Get all available cronjobs to execute.
		$this->db->invokeQuery('PHPDS_runCronQuery');
	}
}

return 'RunCron';
