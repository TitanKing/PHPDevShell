<?php

class ajax extends PHPDS_controller
{
	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// We need to tell jquery what data type we will be sending back.
		PU_silentHeader("Content-Type: application/json");

		// Lets play around with RAW ajax.
		// In this example we will do a simple query to check if we have any data requested.
		if ($this->G('term')) {
			$data = $this->db->invokeQuery('ExamplePlugin_menuAjaxQuery', $this->G('term'));
			// Ok lets loop it and create a json string.
			if (! empty($data)) {
				$json = '';
				foreach ($data as $name) {
					$json[] = array('name'=>$name['menu_name']);
				}
				print $this->G("callback") . "(" . json_encode($json) . ")";
			} else {
				print '()';
			}
		} else {
			print '()';
		}
	}
}

return 'ajax';