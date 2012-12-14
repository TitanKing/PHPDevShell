<?php

class RegistrationTokenAdmin extends PHPDS_controller
{

	/**
	 * Administration of regisrtation tokens
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 06 July 2010
	 */
	public function execute()
	{
		$verify_registration_option0 = false;
		$verify_registration_option1 = false;

		/* @var $crud crud */
		$crud = $this->factory('crud');

		if ($crud->GET('erg') || $crud->POST('save')) {
			$this->template->heading(_('Edit Registration Token'));
		} else {
			$this->template->heading(_('Add Registration Token'));
		}

		$crud->addField('token_key', $this->core->createRandomString(42));

		if ($crud->GET('erg') || $crud->POST('save') || $crud->POST('new')) {

			if ($crud->GET('erg'))
				$crud->importFields ($this->db->invokeQuery('PHPDS_getTokenQuery', $crud->GET('erg')));

			if ($crud->POST('save') || $crud->POST('new')) {

				$crud->addField('token_id');
				$crud->addField('token_key');
				$crud->addField('registration_option');
				$crud->addField('available_tokens');

				if (!$crud->is('token_name'))
					$crud->error(_('Please give this token a name'));

				if (!$crud->is('user_role_id'))
					$crud->error(_('Please select role for this token'));

				if (!$crud->is('user_group_id'))
					$crud->error(_('Please select group for this token'));

				if ($this->db->doesRecordExist('_db_core_registration_tokens', 'token_name', "{$crud->f->token_name}", 'token_id', "{$crud->f->token_id}") == true)
					$crud->errorElse(sprintf(_('The registration token "%s" you specified already exists, please use a different name.'), $crud->f->token_name));

				if ($crud->ok()) {
					$crud->f->token_id = $this->db->invokeQuery('PHPDS_saveRegistrationTokensQuery', $crud->f->token_id, $crud->f->token_name, $crud->f->user_role_id, $crud->f->user_group_id, $crud->f->token_key, $crud->f->registration_option, $crud->f->available_tokens);
					$this->template->ok(sprintf(_('The registration token "%s" was saved as required.'), $crud->f->token_name));
				} else {
					$crud->errorShow();
				}
			}
		}

		$user_roles_option_move = $this->db->invokeQuery('PHPDS_tokensGetRolesQuery', $crud->f->user_role_id);
		$user_groups_option_move = $this->db->invokeQuery('PHPDS_tokensGetGroupsQuery', $crud->f->user_group_id);

		if (!empty($this->security->post['new'])) {
			$crud->f->token_id = 0;
			$crud->f->token_name = '';
			$crud->f->token_key = $this->core->createRandomString(42);
		}

		$view = $this->factory('views');

		$view->set('token_id', $crud->f->token_id);
		$view->set('token_name', $crud->f->token_name);
		$view->set('token_key', $crud->f->token_key);
		$view->set('available_tokens', $crud->f->available_tokens);
		$view->set('registration_option', $crud->radio('registration_option', array(1=>_('Yes'), 0=>_('No')), array($crud->f->registration_option)));

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('user_roles_option_move', $user_roles_option_move);
		$view->set('user_groups_option_move', $user_groups_option_move);

		$view->show();
	}
}

return 'RegistrationTokenAdmin';