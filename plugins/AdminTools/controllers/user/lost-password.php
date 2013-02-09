<?php

/**
 * Controller Class: Recover lost passwords.
 * @author Jason Schoeman
 * @return string
 */
class LostPassword extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$email = $this->factory('mailer');
		$crud = $this->factory('crud');

		$reset_page_url = $this->navigation->buildURL('new-password', 'eun=');

		$username = '';

		$this->template->heading(__('Recover Lost Password'));

		// Sending password instructions.
		if ($crud->POST('send')) {

			if (!$crud->isAlphaNumeric('user_name') && !$crud->isEmail('user_name'))
				$crud->error();

			if ($crud->ok()) {
				$user_array = $this->db->invokeQuery('PHPDS_ReadUserQuery', $crud->f->user_name, $crud->f->user_name);
				$edit = $user_array;

				if (($edit['user_name'] === $crud->f->user_name) || ($edit['user_email'] == $crud->f->user_name)) {

					$reset_page_url = $reset_page_url . md5($edit['user_name'] . $edit['user_email'] . $edit['user_password']);

					$to = $edit['user_email'];
					$subject = sprintf(__('%s password recovery.'), $this->configuration['scripts_name_version']);
					$message = sprintf(__("Dear %s user, you requested for your password to be reset, please click on link to enter a new password %s. Use your new password to log in after it has been changed. Thank you, %s"), $this->configuration['scripts_name_version'], $reset_page_url, $this->configuration['absolute_url'], $this->configuration['absolute_url']);

					if ($email->sendmail("$to", $subject, $message)) {
						$this->template->ok(__('Instructions emailed.'));
						$_SESSION['hold'] = $this->configuration['time'];
					} else {
						$this->template->warning(__('Cannot send email.'));
					}

				} else {
					$this->template->warning(__('User not found.'));
				}

			} else {
				$crud->errorShow();
			}
		}

		$view = $this->factory('views');

		$view->set('send_recovery_instructions', __('Send Recovery Instructions'));

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('username', $crud->f->user_name);

		$view->show();
	}
}

return 'LostPassword';
