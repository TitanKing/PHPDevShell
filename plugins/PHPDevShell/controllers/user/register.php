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
        $registration_selection = '';

		switch ($settings['allow_registration']) {
			// No registrations accepted.
			case 0:
				$this->template->heading(__('Account Registration Disabled'));
				break;
			// All, allow default registrations and token registrations.
			case 1:
				$this->template->heading(__('Register Account'));
			// Default registrations only.
			case 2:
				$this->template->heading(__('Register Account'));
				break;
		}

		if (!empty($settings['registration_message']))
				$this->template->message($settings['registration_message']);

		if ((boolean) $settings['allow_registration'] == true) {
			if ($crud->POST('save')) {
				$crypt_user_password = (string) md5($crud->POST('user_password'));
				$crypt_verify_password = (string) md5($crud->POST('verify_password'));
				$crud->addField('language');
				$crud->addField('user_timezone');
				$crud->addField('region');

				if (!$crud->isAlphaNumeric('user_name') && !$crud->isEmail('user_name'))
					$crud->error(__('Alpha numeric characters only'));

				if (!$crud->is('user_password'))
					$crud->error();

				if (!$crud->is('verify_password'))
					$crud->error();

				if (!$crud->is('user_display_name') && !$crud->isEmail('user_display_name'))
					$crud->error();

				if (!$crud->isEmail('user_email'))
					$crud->error();

				if (!$crud->ok()) {
					$this->template->warning(__('Registration incorrect'));
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
						$crud->error(__('Username already exists'), 'user_name');

					// Check if this email already exists.
					if ($user_email_ == $crud->f->user_email)
						$crud->error(__('Email already exists'), 'user_email');

					// Check email string validity.
					if (!$crud->isEmail('user_email'))
						$crud->error(__('Email address invalid'), 'user_email');

					// Check if password is correct length.
					if (!$crud->isMinLength('user_password', 4))
						$crud->error(__('Password too short'), 'user_password');

					// Check if password compares with verification password.
					if ($crypt_user_password !== $crypt_verify_password)
						$crud->error(__('Password does not match'), 'user_password');


					if ($crud->ok() && $spam->block()) {
						// Phase 1 : Create data and save into db.
						switch ($settings['verify_registration']) {
							// Directly submitted.
							case 0:
								$db_user_role = $settings['move_verified_role'];
								$db_user_group = $settings['move_verified_group'];
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
                                        sprintf(__("Click on the url to DELETE this user: %s"), $delete_url);

									// Create email message for notification.
									$verification_message = sprintf(__($settings['reg_email_direct']),
                                        $crud->f->user_display_name,
                                        $this->configuration['scripts_name_version'],
                                        $this->configuration['absolute_url']);

									$verification_subject = sprintf(__('Registration approved at %s.'),
                                        $this->configuration['scripts_name_version']);

									$verification_to = $crud->f->user_email;

									$email->sendmail("$verification_to", $verification_subject, $verification_message);

									$this->template->ok(sprintf(__('%s, registration successful.'),
                                        $crud->f->user_display_name));

									break;
								// Needs email verification.
								case 1:

									$delete_url = $this->navigation->buildURL('manage-users', "du={$crud->f->user_id}");
									$delete_url_inset = "\r\n\r\n" . sprintf(__("DELETE this user: %s"), $delete_url);
									$encrypted_url = (string) md5($crud->f->user_id.$crud->f->user_name.$crud->f->user_email);

									$registration_url = $this->navigation->buildURL('finish-registration', "fa=$encrypted_url");
									$verification_message = sprintf(__($settings['reg_email_verify']),
                                        $crud->f->user_display_name,
                                        $this->configuration['scripts_name_version'],
                                        $registration_url,
                                        $this->configuration['absolute_url']);
									$verification_subject = sprintf(__('Registration verification at %s.'),
                                        $this->configuration['scripts_name_version']);
									$verification_to = $crud->f->user_email;

									// Send the verification email.
									if ($email->sendmail(
                                        "$verification_to",
                                        $verification_subject,
                                        $verification_message)) {
										$this->template->ok(sprintf(__('%s, email verification sent.'),
                                            $crud->f->user_display_name));
									} else {
										$this->template->warning(__('Email error'));
										$this->db->invokeQuery('PHPDS_RollbackQuery');
									}

									break;
								// Needs approval.
								case 2:
									$ban_url = $this->navigation->buildURL('pending-users', "bu={$crud->f->user_id}");
									$ban_url_inset = "\r\n\r\n" . sprintf(__("BAN this user: %s"), $ban_url);
									$approve_url = $this->navigation->buildURL('pending-users', "aue={$crud->f->user_id}");
									$approval_url_inset = "\r\n\r\n" . sprintf(__("APPROVE this user: %s"), $approve_url);
									$delete_url = $this->navigation->buildURL('pending-users', "du={$crud->f->user_id}");
									$delete_url_inset = "\r\n\r\n" . sprintf(__("DELETE this user: %s"), $delete_url);
									$verification_message = sprintf(__($settings['reg_email_approve']),
                                        $crud->f->user_display_name,
                                        $this->configuration['scripts_name_version'],
                                        $this->configuration['absolute_url']);
									$verification_subject = sprintf(__('Registration pending at %s.'),
                                        $this->configuration['scripts_name_version']);
									$verification_to = $crud->f->user_email;

									$email->sendmail("$verification_to", $verification_subject, $verification_message);

									$this->template->ok(sprintf(__('%s, awaiting approval.'), $crud->f->user_display_name));

									break;
							}
							// Send a email to the system administrator.
							if ((boolean) $settings['email_new_registrations'] == true) {
								// Create email to admin message.
								$message_reg = sprintf(__($settings['reg_email_admin']),
                                    $this->configuration['scripts_name_version'],
                                    $crud->f->user_display_name,
                                    $this->core->formatTimeDate($this->configuration['time']),
                                    $crud->f->user_name,
                                    $this->configuration['scripts_name_version'],
                                    $approval_url_inset,
                                    $ban_url_inset,
                                    $delete_url_inset);
								$subject_reg = sprintf(
                                    __('Registration received at %s.'),
                                    $this->configuration['scripts_name_version']);
								$admin_to = $settings['setting_admin_email'];
								// Send the verification email.
								if ($email->sendmail("$admin_to", $subject_reg, $message_reg))
									$this->template->ok(__('Registration send'), false, false);
								else
									$this->template->warning(__('Email error'));
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

			// Load views.
			$view = $this->factory('views');

			$view->set('self_url', $this->navigation->selfUrl());
			$view->set('user_name', $crud->f->user_name);
			$view->set('user_display_name', $crud->f->user_display_name);
			$view->set('user_email', $crud->f->user_email);
			$view->set('registration_selection', $registration_selection);
			$view->set('language_options', $language_options);
			$view->set('region_options', $region_options);
			$view->set('timezone_options', $timezone_options);
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
