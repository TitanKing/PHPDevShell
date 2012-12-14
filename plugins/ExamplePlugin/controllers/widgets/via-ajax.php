<?php

class viaAjaxExample extends PHPDS_controller
{

	/**
	 * We always start by overriding the execute method for the controller.
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$this->template->heading('a Simple live ajax search');
		$this->template->info('The idea is to show you that this <em>execute()</em> method will be skipped when we call this same controller via Ajax, the whole template engine will also be skipped and only the pure data will be handled.');
		$this->template->warning('LOG THIS AS PROOF : As soon as Ajax is detected it will jump to use the method <em>viaAjax()</em>');

		$view = $this->factory('views');
		$view->show();
	}

	/**
	 * This method will only be called when Ajax is used.
	 */
	public function viaAjax()
	{
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

return 'viaAjaxExample';