<?php

/**
 * Controller Class: Simple readme to introduce PHPDevShell.
 * @author Jason Schoeman
 * @return string
 */
class ReadMe extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$this->template->heading(__('Starting with PHPDevShell'));

		// Testing Notification Boxes.
		$warning = $this->template->warning('This is a sample warning message, this can be written in log.', 'return', 'nolog');
        $note = $this->template->note('This is a sample notice message... ', 'return');
		$ok = $this->template->ok('This is a sample ok message, this can be written in log.', 'return', 'nolog');
		$info = $this->template->info('This is a sample info message...', 'return');

        $view = $this->factory('views');

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('aurl', $this->configuration['absolute_url']);
		$view->set('note', $note);
		$view->set('warning', $warning);
		$view->set('ok', $ok);
		$view->set('info', $info);
		$view->set('urlbutton', "<a href=# class=\"button\">{$this->template->icon('tick', __('a Image with a link.'))}</a>");
		$view->set('img1', $this->template->icon('alarm-clock', __('Image Example 1')));
		$view->set('img2', $this->template->icon('calendar-share', __('Image Example 2')));
		$view->set('img3', $this->template->icon('hammer--plus', __('Image Example 3')));
		$view->set('img4', $this->template->icon('truck--pencil', __('Image Example 4')));
		$view->set('script_name', $this->configuration['phpdevshell_version']);

		$view->show();
	}
}

return 'ReadMe';
