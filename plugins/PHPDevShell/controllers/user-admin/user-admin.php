<?php

/**
 * Controller Class: User Administration
 * @author Jason Schoeman
 * @return string
 */
class UserAdmin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$sa = $this->db->getSettings();
		$email = $this->factory('mailer');
		$userAction = $this->factory('userActions');

		/* @var $crud crud */
		$crud = $this->factory('crud');

		$user_role_option = '';
		$extra_roles_role_option = '';
		$user_role_id_form = array();
		$extra_groups_group_option = '';
		$user_group_id_form = array();
		$user_group_option = '';
		$role_id_db = 0;

		if ($crud->GET('eu') || $crud->POST('save')) {
			$this->template->heading(_('Edit User'));
		} else {
			$this->template->heading(_('Add Users'));
			$this->template->note(_('Leave the PASSWORD field *blank* to have password auto generated and emailed to the user.'));
		}

		if ($crud->GET('eu') || $crud->POST('save') || $crud->POST('new')) {

			if ($crud->GET('eu')) {
				$this->template->note(_('Leave the PASSWORD field *blank* to have password unchanged.'));
				$crud->importFields($this->db->invokeQuery('PHPDS_readUserQuery', $crud->GET('eu')));

				if (empty($crud->f->user_id))
					$this->template->warning(_('Access rights to the user record is not permitted. Please contact your system administrator.'));
			}

			if ($crud->POST('save') || $crud->POST('new')) {

				$crud->addField('user_id');
				$crud->addField('user_name');
				$crud->addField('user_password');
				$crud->addField('user_display_name');
				$crud->addField('user_email');
				$crud->addField('user_group');
				$crud->addField('user_role');
				$crud->addField('language');
				$crud->addField('region');
				$crud->addField('send_notification');
				$crud->addField('date_registered');
				$crud->addField('user_timezone', $this->configuration['system_timezone']);

				if (!$crud->isAlphaNumeric('user_name') && !$crud->isEmail('user_name'))
					$crud->error(_('Please provide a clean alpha numeric string or email as username'));

				if (!$crud->is('user_display_name') && !$crud->isEmail('user_display_name'))
					$crud->error(_('Please provide a clean alpha numeric display name'));

				if (!$crud->isEmail('user_email'))
					$crud->error(_('Please provide a valid email'));

				if (!$crud->ok()) {
					$crud->errorShow();
				}  else {
					$check_user_array = $this->db->invokeQuery('PHPDS_readUserDetailQuery', $crud->f->user_id, $crud->f->user_name, $crud->f->user_email);

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

					if (!$this->user->belongsToGroup(false, $crud->f->user_group) && !$this->user->belongsToRole(false, $crud->f->user_role))
						$crud->errorElse(_('You cannot edit a user that does not belong to you!?'));

					if (!$crud->is('user_role'))
						$crud->error(_('You cannot leave the primary role empty'));

					if (!$crud->is('user_group'))
						$crud->error(_('You cannot leave the primary group empty'));

					if ($crud->ok()) {
						if (empty($crud->f->user_id)) {
							if (!empty($crud->f->user_password)) {
								$insert_encrypt_pass = md5($crud->f->user_password);
								$send_user_pass = $crud->f->user_password;
							} else {
								$password_generate = $this->core->createRandomString(10, false);
								$insert_encrypt_pass = md5($password_generate);
								$send_user_pass = $password_generate;
							}

							$crud->f->user_id = $this->db->invokeQuery('PHPDS_writeUserQuery', $crud->f->user_display_name, $crud->f->user_name, $insert_encrypt_pass, $crud->f->user_email, $crud->f->user_group, $crud->f->user_role, $crud->f->date_registered, $crud->f->language, $crud->f->user_timezone, $crud->f->region);

							$userAction->userAdd($crud->f);

							if (!empty($crud->f->send_notification))
									$email_message = sprintf(_("%s, a Profile was created at %s"), $crud->f->user_display_name, $this->configuration['scripts_name_version']) . "\r\n";
									$email_message .= sprintf(_("Your username : %s"), $crud->f->user_name) . "\r\n";
									$email_message .= sprintf(_("Your password : %s"), $send_user_pass) . "\r\n";
									$email_message .= sprintf(_("It is a good idea to change your password every now and then."));
									$email->sendmail("{$crud->f->user_email}", sprintf(_('Login profile created at %s'), $this->configuration['absolute_url']), $email_message);
						} else {

							if (! empty($crud->f->user_password)) {
								$send_user_pass = $crud->f->user_password;
							} else {
								$this->template->notice(_('User password was left unchanged.'));
								$send_user_pass = _('Unchanged');
							}

							$this->db->invokeQuery('PHPDS_updateUserQuery',
									$crud->f->user_display_name,
									$crud->f->user_name,
									$crud->f->user_password,
									$crud->f->user_email,
									$crud->f->date_registered,
									$crud->f->user_group,
									$crud->f->user_role,
									$crud->f->language,
									$crud->f->user_timezone,
									$crud->f->region,
									$crud->f->user_id
							);

							$userAction->userUpdate($crud->f);

							if (!empty($crud->f->send_notification)) {
								$email_message = sprintf(_("%s, your profile was updated at %s"), $crud->f->user_display_name, $this->configuration['scripts_name_version']) . "\r\n";
								$email_message .= sprintf(_("Your username : %s"), $crud->f->user_name) . "\r\n";
								$email_message .= sprintf(_("Your password : %s"), $send_user_pass) . "\r\n";
								$email_message .= sprintf(_("It is a good idea to change your password every now and then."));
								$email->sendmail("{$crud->f->user_email}", sprintf(_('Login profile updated at %s'), $this->configuration['absolute_url']), $email_message);
							}
						}

						$this->db->invokeQuery('PHPDS_deleteExtraRolesQuery', $crud->f->user_id);
						if ($crud->POST('extra_roles'))
							$this->db->invokeQuery('PHPDS_replaceExtraRolesQuery', $crud->POST('extra_roles'), $crud->f->user_id);

						$this->db->invokeQuery('PHPDS_deleteExtraGroupsQuery', $crud->f->user_id);
						if ($crud->POST('extra_groups'))
							$this->db->invokeQuery('PHPDS_replaceExtraGroupsQuery', $crud->POST('extra_groups'), $crud->f->user_id);

						$this->template->ok(sprintf(_('User %s saved.'), $crud->f->user_display_name));
					} else {
						$crud->errorShow();
					}
				}
			}

			$user_role_id_form = $this->db->invokeQuery('PHPDS_selectedRolesQuery', $crud->f->user_id);
			$user_group_id_form = $this->db->invokeQuery('PHPDS_selectedGroupsQuery', $crud->f->user_id);
		}

		$roles = $this->db->invokeQuery('PHPDS_readRolesQuery');
		if (empty($crud->f->user_role)) $crud->f->user_role = $sa['move_verified_role'];
		$user_role_option = $crud->select($roles, array($crud->f->user_role));
		$extra_roles_role_option = $crud->select($roles, array_keys($user_role_id_form));


		$groups = $this->db->invokeQuery('PHPDS_PrimaryGroupTreeQuery');
		if (empty($crud->f->user_group))
			$crud->f->user_group = $sa['move_verified_group'];
		$user_group_option = $crud->select($groups, array($crud->f->user_group));
		$extra_groups_group_option = $crud->select($groups, array_keys($user_group_id_form));


		$iana = $this->factory('iana');
		$language_options = $iana->languageOptions($crud->f->language);
		$region_options = $iana->regionOptions($crud->f->region);

		$timezone = $this->factory('timeZone');
		$timezone_options = $timezone->timezoneOptions($crud->f->user_timezone);

		$tagger = $this->tagger->tagArea('user', $crud->f->user_id, $crud->f->user_name);

		if ($crud->POST('new')) {
			$crud->f->user_id = null;
			$crud->f->user_name = '';
			$crud->f->user_display_name = '';
			$crud->f->user_email = '';
			$crud->f->user_password = '';
		}

		$view = $this->factory('views');

		$view->set('user_id', $crud->f->user_id);
		$view->set('user_name', $crud->f->user_name);
		$view->set('user_display_name', $crud->f->user_display_name);
		$view->set('user_email', $crud->f->user_email);
		$view->set('date_registered', $crud->f->date_registered);

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('user_role_option', $user_role_option);
		$view->set('extra_roles_role_option', $extra_roles_role_option);
		$view->set('user_group_option', $user_group_option);
		$view->set('extra_groups_group_option', $extra_groups_group_option);
		$view->set('language_options', $language_options);
		$view->set('region_options', $region_options);
		$view->set('locale', $this->configuration['locale']);
		$view->set('timezone_options', $timezone_options);
		$view->set('tagger', $tagger);
		$view->set('date_format_show', $this->core->formatTimeDate($this->configuration['time'], 'default', $crud->f->user_timezone));
		$view->set('post_validation', $this->security->postValidation());

		$view->show();
	}
}

return 'UserAdmin';
