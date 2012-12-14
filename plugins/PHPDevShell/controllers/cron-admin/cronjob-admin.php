<?php

/**
 * Controller Class: Handles system cron job admin.
 * @author Jason Schoeman
 * @return string
 */
class cronjobAdmin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$this->template->heading(_('Edit Cronjob'));

		$crud = $this->factory('crud');

		if ($crud->REQUEST('ec'))
			$crud->f->menu_id = $crud->REQUEST('ec');
		if ($crud->REQUEST('menu_id'))
			$crud->f->menu_id = $crud->REQUEST('menu_id');

		$crud->importFields($this->db->InvokeQuery('PHPDS_selectEditMenuQuery', $crud->f->menu_id));

		$crud->f->menu_name = $this->navigation->determineMenuName($crud->f->menu_name, $crud->f->menu_link, $crud->f->menu_id);

		if ($crud->f->menu_id) {

			if ($crud->POST('save')) {
				$crud->addField('second', 0);

				// Error checking.
				if (!$crud->is('menu_id'))
					$crud->error(_("No menu id present"));

				$crud->addField('cron_type', 0);
				$crud->addField('log_cron', 0);
				$crud->addField('cron_desc');

				if ($crud->f->cron_type == 1) {
					if (!$crud->isRangeValue('year', date('Y'), date('Y')+10))
						$crud->error(sprintf(_('Please select year from %s - %s'), date('Y'), date('Y')+10));

					if (!$crud->isRangeValue('month', 1, 12))
						$crud->error(_('Please select month from 1 - 12'));

					if (!$crud->isRangeValue('day', 1, 31))
						$crud->error(_('Please select day from 1 - 31'));

					if (!$crud->isRangeValue('hour', 0, 23, 0))
						$crud->error(_('Please select hour from 0 - 23'));

					if (!$crud->isRangeValue('minute', 0, 59))
						$crud->error(_('Please select minute from 0 - 59'));

					$cron_time = mktime($crud->f->hour, $crud->f->minute, $crud->f->second, $crud->f->month, $crud->f->day, $crud->f->year);

					if ($cron_time <= time()) {
						$crud->errorElse(_('Cron is set in the past, please correct.'));
					}
				} else if ($crud->f->cron_type == 2) {
					if (!$crud->isMinValue('year', 10))
						$crud->error(_('Max allowed every 10 years'));

					if (!$crud->isMinValue('month', 128))
						$crud->error(_('Max allowed every 128 months'));

					if (!$crud->isMinValue('day', 2048))
						$crud->error(_('Max allowed every 1024 days'));

					if (!$crud->isMinValue('hour', 2048))
						$crud->error(_('Max allowed every 2048 hours'));

					if (!$crud->isMinValue('minute', 2048))
						$crud->error(_('Max allowed every 2048 minutes'));

					$cron_time = mktime($crud->f->hour, $crud->f->minute, $crud->f->second, $crud->f->month, $crud->f->day, $crud->f->year);
				}

				if ($crud->ok()) {
					$this->db->invokeQuery('PHPDS_saveCronQuery',
							$crud->f->menu_id,
							$crud->f->cron_desc,
							$crud->f->cron_type,
							$crud->f->log_cron,
							$crud->f->last_execution,
							$crud->f->year,
							$crud->f->month,
							$crud->f->day,
							$crud->f->hour,
							$crud->f->minute);
					$this->template->ok(sprintf(_('Cronjob %s saved.'), $crud->f->menu_name));
				} else {
					$crud->errorShow();
				}
			}
			if ($crud->is('last_execution')) {
				$last_executed = $this->core->formatTimeDate($crud->f->last_execution);
			} else {
				$last_executed = $expectancy = _('Never!');
			}
			switch ($crud->f->cron_type) {
				case 0:
					$expectancy = _('Never!');
					break;
				case 1:
					$expectancy = sprintf(_('Expected to run once on %s.'), $this->core->formatTimeDate(mktime($crud->f->hour, $crud->f->minute, $crud->f->second, $crud->f->month, $crud->f->day, $crud->f->year)));
					break;
				case 2:
					// Convert to hours.
					$year_in_hr = ($crud->f->year * 8765.81277);
					$month_in_hr = ($crud->f->month * 730.484398);
					$day_in_hr = ($crud->f->day * 24);
					$hr_in_hr = $crud->f->hour;
					$minutes_in_hr = ($crud->f->minute / 60);

					$hours = round($year_in_hr + $month_in_hr + $day_in_hr + $hr_in_hr + $minutes_in_hr, 2);
					// Create total Months/Days/Hours/Minutes.
					if ($hours >= 730.484398) {
						$months = round($hours / 730.484398, 2);
						$expectancy = sprintf(_('Will run every %s month(s).'), $months);
					} else if ($hours >= 24) {
						$days = round($hours / 24, 2);
						$expectancy = sprintf(_('Will run every %s day(s).'), $days);
					} else if ($hours >= 1) {
						$expectancy = sprintf(_('Will run every %s hour(s).'), $hours);
					} else if ($hours < 1) {
						$minutes = ($hours * 60);
						$expectancy = sprintf(_('Will run every %s minute(s).'), $minutes);
					}
					break;
			}

			$view = $this->factory('views');

			$view->set('self_url', $this->navigation->selfUrl());
			$view->set('menu_id', $crud->f->menu_id);
			$view->set('menu_name', $crud->f->menu_name);
			$view->set('plugin', $crud->f->plugin);
			$view->set('cron_desc', $crud->f->cron_desc);
			$view->set('last_execution', $crud->f->last_execution);
			$view->set('year', $crud->f->year);
			$view->set('month', $crud->f->month);
			$view->set('day', $crud->f->day);
			$view->set('hour', $crud->f->hour);
			$view->set('minute', $crud->f->minute);
			$view->set('last_executed', $last_executed);
			$view->set('expectancy', $expectancy);
			$view->set('cron_type', $crud->radio('cron_type', array(0=>_('Disable'), 1=>_('Once On Date'), 2=>_('Repeat Indefinitely')), array($crud->f->cron_type)));
			$view->set('log_cron', $crud->radio('log_cron', array(0=>_('No'), 1=>_('Yes')), array($crud->f->log_cron)));

			$view->show();
		} else {
			$this->template->warning('The cronjob you wish to edit does not exist or it was deleted from the menu system.');
		}
	}
}

return 'cronjobAdmin';