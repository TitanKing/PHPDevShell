<?php

/**
 * Controller Class: Handles registration.
 * @author Jason Schoeman
 * @return string
 * @var $crud crud
 */
class Register extends PHPDS_controller
{
	public function execute()
	{
		$email = $this->factory('mailer');

		/* @var $userAction userActions */
		$userAction = $this->factory('userActions');

		/* @var $crud crud */
		$crud = $this->factory('crud');

		/* @var $spam botBlock */
		$spam = $this->factory('botBlock');

		$settings = $this->db->getSettings(
            array(
                'allow_registration',
                'verify_registration',
                'move_verified_group',
                'move_verified_role',
                'registration_group',
                'registration_role',
                'email_new_registrations',
                'setting_admin_email',
                'languages_available',
                'registration_message',
                'reg_email_direct',
                'reg_email_verify',
                'reg_email_approve',
                'reg_email_admin'
            )
        );

		$approval_url_inset     = '';
        $ban_url_inset          = '';
        $token_key_field        = '';
        $token_key_field_type   = '';
        $token_id               = '';
        $registration_selection = '';
        $optional_token         = '';

		switch ($settings['allow_registration']) {
			// No registrations accepted.
			case 0:
				$this->template->heading(_('Account Registration Disabled'));
				break;
			// All, allow default registrations and token registrations.
			case 1:
				$this->template->heading(_('Register New Account'));
				$optional_token = _('(Optional)');

				if (!$crud->GET('token_key'))
					$registration_selection = $this->db->invokeQuery('PHPDS_SelectTokensQuery');

				if ($crud->GET('token_key'))
					$token_key_field_type = 'class="boxdisabled" readonly';
				else
					$token_key_field_type = 'class="boxnormal"';
				break;
			// Default registrations only.
			case 2:
				$this->template->heading(_('Register New Account'));

				break;
			// Token registrations only, only users with registration tokens can register.
			case 3:
				$this->template->heading(_('Register New Account'));

				if ($crud->GET('token_key'))
					$token_key_field_type = 'class="boxdisabled" readonly';
				else
					$token_key_field_type = 'class="boxmand" required="required"';

				break;
		}

		if (!empty($settings['registration_message']))
				$this->template->message($settings['registration_message']);
		if ($settings['allow_registration'] == 1 || $settings['allow_registration'] == 3) {
			if ($crud->REQUEST('token_key'))
				$token_key___ = '<input id="token_key" type="text" size="50" name="token_key" value="'
                                . $crud->REQUEST('token_key') . '" %s>';
			else
				$token_key___ = '<input id="token_key" type="text" size="50" name="token_key" value="" %s>';

			$token_key_field = $token_key___;
		} else {
			$token_key_field = false;
		}

		if ((boolean) $settings['allow_registration'] == true) {
			if ($crud->POST('save')) {
				$crypt_user_password = (string) md5($crud->POST('user_password'));
				$crypt_verify_password = (string) md5($crud->POST('verify_password'));
				$crud->addField('token_key');
				$crud->addField('language');
				$crud->addField('user_timezone');
				$crud->addField('region');

				if (!$crud->isAlphaNumeric('user_name') && !$crud->isEmail('user_name'))
					$crud->error(_('Invalid username'));

				if (!$crud->is('user_password'))
					$crud->error(_('Invalid password'));

				if (!$crud->is('verify_password'))
					$crud->error(_('Invalid password verification'));

				if (!$crud->is('user_display_name') && !$crud->isEmail('user_display_name'))
					$crud->error(_('Invalid display name'));

				if (!$crud->isEmail('user_email'))
					$crud->error(_('Invalid email'));

				if (!$crud->ok()) {
					$this->template->warning(_('Registration incorrect'));
					$crud->errorShow();
				} else {
					// Do the matching query to check if any users of this name, email or display name exists.
					$check_user_array = $this->db->invokeQuery('PHPDS_UserDetailQuery',
                        $crud->f->user_name,
                        $crud->f->user_display_name,
                        $crud->f->user_email);

					// Get the results.
					$user_name_ = $check_user_array['user_name'];
					$user_email_ = $check_user_array['user_email'];

					// Check if user already exists.
					if ($user_name_ == $crud->f->user_name)
						$crud->error(_('Username already exists'), 'user_name');

					// Check if this email already exists.
					if ($user_email_ == $crud->f->user_email)
						$crud->error(_('Email already exists'), 'user_email');

					// Check email string validity.
					if (!$crud->isEmail('user_email'))
						$crud->error(_('Email address invalid'), 'user_email');

					// Check if password is correct length.
					if (!$crud->isMinLength('user_password', 4))
						$crud->error(_('Password too short'), 'user_password');

					// Check if password compares with verification password.
					if ($crypt_user_password !== $crypt_verify_password)
						$crud->error(_('Password does not match'), 'user_password');

					// Switch registration types.
					switch ($settings['allow_registration']) {
						case 0:
							break;
						// All registrations allowed.
						case 1:
							if ($crud->is('token_key')) {
								$token_array = $this->db->invokeQuery(
                                    'PHPDS_CheckTokenQuery', $crud->f->token_key
                                    );
								if (empty($token_array['token_id'])) {
									$crud->error(_('Token is incorrect'));
								} else {
									$token_id = $token_array['token_id'];
									$token_user_role_id = $token_array['user_role_id'];
									$token_user_group_id = $token_array['user_group_id'];
								}
							} else if ($crud->is('token_id_option')) {
								$token_array = $this->db->invokeQuery(
                                    'PHPDS_CheckTokenByIdQuery', $crud->f->token_id_option
                                    );
								if (empty($token_array['token_id'])) {
									$crud->error(_('Tokens depleted'));
								} else {
									$token_id = $token_array['token_id'];
									$token_user_role_id = $token_array['user_role_id'];
									$token_user_group_id = $token_array['user_group_id'];
								}
							}
							break;
						// Default registrations only.
						case 2:
							$token_id = 0;
							break;
						// Token registrations only, only users with registration tokens can register.
						case 3:
							if ($crud->is('token_key')) {
								$token_array = $this->db->invokeQuery('PHPDS_CheckTokenQuery', $crud->f->token_key);
								if (empty($token_array['token_id'])) {
									$crud->error(_('Token is incorrect'));
								} else {
									$token_id = $token_array['token_id'];
									$token_user_role_id = $token_array['user_role_id'];
									$token_user_group_id = $token_array['user_group_id'];
								}
							} else {
								$crud->error(_('Token mandatory'));
							}
							break;
					}
					if ($crud->ok() && $spam->block()) {
						// Phase 1 : Create data and save into db.
						switch ($settings['verify_registration']) {
							// Directly submitted.
							case 0:

								if (empty($token_id)) {
									$db_user_role = $settings['move_verified_role'];
									$db_user_group = $settings['move_verified_group'];
								} else {
									$db_user_role = $token_user_role_id;
									$db_user_group = $token_user_group_id;
									$deduct_token = true;
								}
								break;
							// Needs email verification.
							case 1:
								$db_user_role = $settings['registration_role'];
								$db_user_group = $settings['registration_group'];
								break;
							// Needs approval.
							case 2:
								$db_user_role = $settings['registration_role'];
								$db_user_group = $settings['registration_group'];
								break;
						}
						// Set some time variables for database and time submition check.
						$time_now = $this->configuration['time'];

						$crud->f->user_id = $this->db->invokeQuery('PHPDS_WriteRegQuery',
                            $crud->f->user_display_name,
                            $crud->f->user_name,
                            $crypt_user_password,
                            $crud->f->user_email,
                            $db_user_group,
                            $db_user_role,
                            $time_now,
                            $crud->f->language,
                            $crud->f->user_timezone,
                            $crud->f->region);
						$userAction->userRegister($crud->f);

						if ($crud->f->user_id) {
							// Deduct token for direct registration.
							if (isset($deduct_token) && !empty($token_id) && $crud->f->user_id)
								$this->db->invokeQuery('PHPDS_UpdateTokensQuery', $token_id);

							// Insert registration approval queue.
							if ($crud->f->user_id && ! empty($settings['verify_registration']))
								$this->db->invokeQuery('PHPDS_UpdateRegQueueQuery',
                                    $crud->f->user_id,
                                    $settings['verify_registration'],
                                    $token_id);

							// Phase 2 : Send out mail for success.
							switch ($settings['verify_registration']) {
								// Directly submitted.
								case 0:
									$delete_url = $this->navigation->buildURL('manage-users', "du={$crud->f->user_id}");
									$delete_url_inset = "\r\n\r\n" .
                                        sprintf(_("Click on the url to DELETE this user: %s"), $delete_url);

									// Create email message for notification.
									$verification_message = sprintf(_($settings['reg_email_direct']),
                                        $crud->f->user_display_name,
                                        $this->configuration['scripts_name_version'],
                                        $this->configuration['absolute_url']);

									$verification_subject = sprintf(_('Registration approved at %s.'),
                                        $this->configuration['scripts_name_version']);

									$verification_to = $crud->f->user_email;

									$email->sendmail("$verification_to", $verification_subject, $verification_message);

									$this->template->ok(sprintf(_('%s, registration successful.'),
                                        $crud->f->user_display_name));

									break;
								// Needs email verification.
								case 1:

									$delete_url = $this->navigation->buildURL('manage-users', "du={$crud->f->user_id}");
									$delete_url_inset = "\r\n\r\n" . sprintf(_("DELETE this user: %s"), $delete_url);
									$encrypted_url = (string) md5($crud->f->user_id.$crud->f->user_name.$crud->f->user_email);

									$registration_url = $this->navigation->buildURL('finish-registration', "fa=$encrypted_url");
									$verification_message = sprintf(_($settings['reg_email_verify']),
                                        $crud->f->user_display_name,
                                        $this->configuration['scripts_name_version'],
                                        $registration_url,
                                        $this->configuration['absolute_url']);
									$verification_subject = sprintf(_('Registration verification at %s.'),
                                        $this->configuration['scripts_name_version']);
									$verification_to = $crud->f->user_email;

									// Send the verification email.
									if ($email->sendmail(
                                        "$verification_to",
                                        $verification_subject,
                                        $verification_message)) {
										$this->template->ok(sprintf(_('%s, email verification sent.'),
                                            $crud->f->user_display_name));
									} else {
										$this->template->warning(_('Email error'));
										$this->db->invokeQuery('PHPDS_RollbackQuery');
									}

									break;
								// Needs approval.
								case 2:
									$ban_url = $this->navigation->buildURL('pending-users', "bu={$crud->f->user_id}");
									$ban_url_inset = "\r\n\r\n" . sprintf(_("BAN this user: %s"), $ban_url);
									$approve_url = $this->navigation->buildURL('pending-users', "aue={$crud->f->user_id}");
									$approval_url_inset = "\r\n\r\n" . sprintf(_("APPROVE this user: %s"), $approve_url);
									$delete_url = $this->navigation->buildURL('pending-users', "du={$crud->f->user_id}");
									$delete_url_inset = "\r\n\r\n" . sprintf(_("DELETE this user: %s"), $delete_url);
									$verification_message = sprintf(_($settings['reg_email_approve']),
                                        $crud->f->user_display_name,
                                        $this->configuration['scripts_name_version'],
                                        $this->configuration['absolute_url']);
									$verification_subject = sprintf(_('Registration pending at %s.'),
                                        $this->configuration['scripts_name_version']);
									$verification_to = $crud->f->user_email;

									$email->sendmail("$verification_to", $verification_subject, $verification_message);

									$this->template->ok(sprintf(_('%s, awaiting approval.'), $crud->f->user_display_name));

									break;
							}
							// Send a email to the system administrator.
							if ((boolean) $settings['email_new_registrations'] == true) {
								// Create email to admin message.
								$message_reg = sprintf(_($settings['reg_email_admin']),
                                    $this->configuration['scripts_name_version'],
                                    $crud->f->user_display_name,
                                    $this->core->formatTimeDate($this->configuration['time']),
                                    $crud->f->user_name,
                                    $this->configuration['scripts_name_version'],
                                    $approval_url_inset,
                                    $ban_url_inset,
                                    $delete_url_inset);
								$subject_reg = sprintf(
                                    _('Registration received at %s.'),
                                    $this->configuration['scripts_name_version']);
								$admin_to = $settings['setting_admin_email'];
								// Send the verification email.
								if ($email->sendmail("$admin_to", $subject_reg, $message_reg))
									$this->template->ok(_('Registration send'), false, false);
								else
									$this->template->warning(_('Email error'));
							}
						}
					} else $crud->errorShow();
				}
			}

			$iana = $this->factory('iana');
			$language_options = $iana->languageOptions($crud->f->language);
			$region_options = $iana->regionOptions($crud->f->region);

			$timezone = $this->factory('timeZone');
			$timezone_options = $timezone->timezoneOptions($crud->f->user_timezone);

			$token_key_field = sprintf($token_key_field, $token_key_field_type);


			// Load views.
			$view = $this->factory('views');

			$view->set('self_url', $this->navigation->selfUrl());
			$view->set('user_name', $crud->f->user_name);
			$view->set('user_display_name', $crud->f->user_display_name);
			$view->set('user_email', $crud->f->user_email);
			$view->set('registration_selection', $registration_selection);
			$view->set('token_key_field', $token_key_field);
			$view->set('language_options', $language_options);
			$view->set('region_options', $region_options);
			$view->set('timezone_options', $timezone_options);
			$view->set('optional_token', $optional_token);
			$view->set('date_format_show', $this->core->formatTimeDate($this->configuration['time'],
                'default',
                $crud->f->user_timezone));
			$view->set('botBlockFields', $spam->botBlockFields());
			// Output Template.
			$view->show();
		}
	}
}

return 'Register';
