<?php

/**
 * Controller Class: Import users to system.
 * @author Jason Schoeman
 * @return string
 */
class UserAdminImport extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{

		/* @var $crud crud */
		$crud = $this->factory('crud');

		$this->template->heading(_('User Importing Admin'));
		$crud->addField('csv_order', 'name,email,username');
		$crud->addField('delimiter', ',');

		if ($crud->POST('import')) {

			$crud->addField('password_prefix');
			$crud->addField('email_username');
			$crud->addField('overwrite_dup');
			$crud->addField('overflow_table');
			$crud->addField('user_timezone');
			$crud->addField('language');
			$crud->addField('region');

			if (!$crud->is('token_id_option'))
				$crud->error(_('Please select a token to import too'));

			if (!$crud->is('delimiter'))
				$crud->error(_('Please select a delimiting character'));

			if (!$crud->is('csv_order'))
				$crud->error(_('Please select a comma seperated csv columned order'));

			if ($crud->ok()) {
				$userAction = $this->factory('userActions');
				$this->db->invokeQuery('PHPDS_doImportQuery', $crud->POST());
				$userAction->usersImportAction($crud->POST());
			} else {
				$crud->errorShow();
			}
		}

		$token_selection = $this->db->invokeQuery('PHPDS_readTokenOptionsQuery');

		$iana = $this->factory('iana');

		$language_options = $iana->languageOptions($crud->f->language);
		$region_options = $iana->regionOptions($crud->f->region);

		$timezone = $this->factory('timeZone');
		$timezone_options = $timezone->timezoneOptions($crud->f->user_timezone);

		$view = $this->factory('views');

		$view->set('password_prefix', $crud->f->password_prefix);
		$view->set('delimiter', $crud->f->delimiter);
		$view->set('csv_order', $crud->f->csv_order);
		$view->set('overflow_table', $crud->f->overflow_table);
		$view->set('prepare_import', _('Prepare Import'));
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('token_selection', $token_selection);
		$view->set('add_token_page', $this->navigation->buildURL(48580716));
		$view->set('email_username_checked', $crud->checkbox('email_username',  array(1=>'Use email address as username?'), array($crud->f->email_username)));
		$view->set('overwrite_dup_checked', $crud->checkbox('overwrite_dup',  array(1=>'Overwrite existing users on duplicate?'), array($crud->f->overwrite_dup)));
		$view->set('language_options', $language_options);
		$view->set('region_options', $region_options);
		$view->set('timezone_options', $timezone_options);

		$view->show();
	}
}

return 'UserAdminImport';
