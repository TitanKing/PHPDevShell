<?php

/**
 * Controller Class: Handles registration finalization.
 * @author Jason Schoeman
 * @return string
 */
class NewPassword extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$email = $this->factory('mailer');
		$userAction = $this->factory('userActions');

		/* @var $crud crud */
		$crud = $this->factory('crud');
		
		$this->template->heading(_('Create new Password'));

		if (! empty($this->configuration['user_id'])) {
			$username = $this->configuration['user_name'];
			$crud->importFields($this->db->invokeQuery('PHPDS_ReadUserQuery', $username));
			$eun = '';
		} else if ($crud->REQUEST('eun')) {
			$md5 = $crud->REQUEST('eun');
			$crud->importFields($this->db->invokeQuery('PHPDS_ReadUserMDCryptQuery', $md5));
			if (! empty($crud->f->user_name))
				$username = $crud->f->user_name;
			else
				$username = '';

			$eun = $md5;
		} else {
			$username = '';
			$eun = '';
		}

		if (empty($username)) {
			$this->core->themeFile = 'login.php';
		} else {
			if (empty($crud->f->user_name)) {
				$this->template->warning(_('User cannot be found. Please try resetting your password again.'));
			} else {
				if ($crud->POST('replace')) {
					if (!$crud->is('password1'))
						$crud->error(_('You need to enter both password fields.'));

					if (!$crud->is('password2'))
						$crud->error(_('You need to enter both password fields.'));

					if ($crud->POST('password1') !== $crud->POST('password2'))
						$crud->error(_('Your passwords do not match, please try again.'), 'password1');

					if (!$crud->isMinLength('password1', 4))
						$crud->error(_('Your password is too short, make it at least 4 characters.'));

					if ($crud->ok()) {
						if ($crud->f->user_name == $username) {
							// MD5 Password.
							$md5_password = md5($crud->POST('password1'));
							// Update the database
							$this->db->invokeQuery('PHPDS_UpdateUserQuery', $md5_password, $crud->f->user_name, $crud->f->user_email);
							$userAction->userChangedPassword($crud->f);
							$to = $crud->f->user_email;
							$subject = sprintf(_('%s password changed.'), $this->configuration['scripts_name_version']);
							$message = sprintf(_("Dear %s user, you have changed your password successfully. Thank you, %s"), $this->configuration['scripts_name_version'], $this->configuration['absolute_url']);
							// Send new password email.
							if ($email->sendmail("$to", $subject, $message)) {
								$this->template->ok(sprintf(_('You have changed your password successfully %s'), $crud->f->user_display_name));
							}
						} else {
							$this->template->warning(_('The specified user was not found. It is possible that you may have been removed from the system.'));
						}
					} else {
						$crud->errorShow();
					}
				}

				$view = $this->factory('views');

				$view->set('change_password', _('Change Password'));

				$view->set('self_url', $this->navigation->selfUrl());
				$view->set('user_name', $crud->f->user_name);
				$view->set('post_validation', $this->security->postValidation());
				$view->set('eun', $eun);

				$view->show();
			}
		}
	}
}

return 'NewPassword';
