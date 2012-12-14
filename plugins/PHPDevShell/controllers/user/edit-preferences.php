<?php

/**
 * Controller Class: Edit user preferences.
 * @author Jason Schoeman
 * @return string
 */
class EditPreferences extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$email = $this->factory('mailer');
		$this->template->heading(_('Edit Accounts Preferences'));

		$userAction = $this->factory('userActions');
		$crud = $this->factory('crud');

		$crud->f->user_id = $this->configuration['user_id'];

		if ($this->user->isLoggedIn())
			$crud->importFields($this->db->invokeQuery('PHPDS_readUserDetailQuery', $crud->f->user_id));

		if ($crud->POST('save')) {
			$crud->addField('user_name');
			$crud->addField('user_display_name');
			$crud->addField('user_email');
			$crud->addField('language');
			$crud->addField('region');
			$crud->addField('user_timezone', $this->configuration['system_timezone']);

			if (!$crud->isAlphaNumeric('user_name') && !$crud->isEmail('user_name'))
				$crud->error(_('Please provide a clean alpha numeric string or email as username'));

			if (!$crud->is('user_display_name') && !$crud->isEmail('user_display_name'))
				$crud->error(_('Please provide a clean alpha numeric display name'));

			if (!$crud->isEmail('user_email'))
				$crud->error(_('Please provide a valid email'));

			if (!$crud->ok()) {
				$crud->errorShow();
			} else {
				$check_user_array = $this->db->invokeQuery('PHPDS_readUserDetailLightQuery', $crud->f->user_id, $crud->f->user_name, $crud->f->user_display_name, $crud->f->user_email);

				$user_name_ = $check_user_array['user_name'];
				$user_display_name_ = $check_user_array['user_display_name'];
				$user_email_ = $check_user_array['user_email'];

				// Check if user already exists.
				if ($user_name_ == $crud->f->user_name)
					$crud->error(_('The username you entered already exists'), 'user_name');

				// Check if this email already exists.
				if ($user_email_ == $crud->f->user_email)
					$crud->error(_('The user email you entered already exists'), 'user_email');

				// Check email string validity.
				if (!$crud->isEmail('user_email'))
					$crud->error(_('The email address you specified seems to be invalid'), 'user_email');

				if ($crud->ok()) {
					$this->db->invokeQuery('PHPDS_updateUserDetailQuery', $crud->f->user_display_name, $crud->f->user_name, $crud->f->user_email, $crud->f->language, $crud->f->user_timezone, $crud->f->region, $crud->f->user_id);

					$email->sendmail($crud->f->user_email, sprintf(_('User profile modified at %s.'), $this->configuration['absolute_url']), sprintf(_("%s you have modified your user settings at %s. Thank You, %s"), $crud->f->user_display_name, $this->configuration['scripts_name_version'], $this->configuration['absolute_url']));

					$this->template->ok(_('Your preferences was updated.'));

					$userAction->userEditPreferences($crud->f);
				} else {
					$crud->errorShow();
				}
			}
		}

		$iana = $this->factory('iana');
		$language_options = $iana->languageOptions($crud->f->language);
		$region_options = $iana->regionOptions($crud->f->region);

		$timezone = $this->factory('timeZone');
		$timezone_options = $timezone->timezoneOptions($crud->f->user_timezone);

		$view = $this->factory('views');

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('user_id', $crud->f->user_id);
		$view->set('user_group', $crud->f->user_group);
		$view->set('user_role', $crud->f->user_role);
		$view->set('user_name', $crud->f->user_name);
		$view->set('user_display_name', $crud->f->user_display_name);
		$view->set('user_email', $crud->f->user_email);
		$view->set('date_registered', $crud->f->date_registered);
		$view->set('language_options', $language_options);
		$view->set('region_options', $region_options);
		$view->set('timezone_options', $timezone_options);
		$view->set('date_format_show', $this->core->formatTimeDate($this->configuration['time'], 'default', $crud->f->user_timezone));
		$view->set('post_validation', $this->security->validatePost());

		$view->show();
	}
}

return 'EditPreferences';
