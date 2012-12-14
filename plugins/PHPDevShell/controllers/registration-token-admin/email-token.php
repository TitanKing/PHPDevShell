<?php

class EmailToken extends PHPDS_controller
{

	/**
	 * Emails Tokens
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 06 July 2010
	 */
	public function execute()
	{
		$email = $this->factory('mailer');
		/* @var $crud crud */
		$crud = $this->factory('crud');

		$this->template->heading(_('Email Token'));

		$settings = $this->db->essentialSettings;

		$crud->importFields($this->db->invokeQuery('PHPDS_getTokenDataQuery', $crud->REQUEST('token_id')));

		if (empty($crud->f->token_id)) {
			$this->template->warning('A token ID is required in order to send tokens.');
		} else {
            $crud->addField('token_subject', sprintf(_("%s Registration Token Credit."), $this->configuration['scripts_name_version']));

            $token_url = $this->navigation->buildURL("{$settings['registration_page']}", "token_key={$crud->f->token_key}");
            $registration_url = $this->navigation->buildURL("{$settings['registration_page']}");
            $crud->addField('token_message', sprintf(_("Dear User, you have received a Registration Token Key, enabling you to register for %1\$s on %2\$s. To activate your key please click on the link, %3\$s. If you cannot click on the link, copy and paste the URL to your browser. Alternatively you can type in your key, %4\$s here, %5\$s Thank You, %6\$s."), $crud->f->token_name, $this->configuration['scripts_name_version'], $token_url, $crud->f->token_key, $registration_url, $this->configuration['user_display_name']));

            if ($crud->POST('send_mail')) {
                if (!$crud->is('email_token_to'))
                    $crud->error('Please provide token recipients email address');

                if (!$crud->is('token_subject'))
                    $crud->error('Please provide token recipients email subject');

				if (!$crud->is('token_message'))
                    $crud->error('Please provide token recipients message');

				if ($crud->ok()) {
					$mail_to_recipients = "{$this->configuration['user_email']},{$crud->f->email_token_to}";
					$recipients_array = str_replace(' ', '', explode(',', $mail_to_recipients));
					foreach ($recipients_array as $email_address_to) {
						if ($email->sendmail("$email_address_to", $crud->f->token_subject, html_entity_decode($crud->f->token_message))) {
							$this->template->ok(sprintf(_('Registration token "%s" was successfully send to %s.'), $crud->f->token_name, $email_address_to));
						} else {
							$this->template->warning(sprintf(_('An unknown error occurred while trying to send registration token email to %s.'), $email_address_to));
						}
					}
				} else {
					$crud->errorElse();
				}
			}

			$view = $this->factory('views');

			$view->set('token_id', $crud->f->token_id);
			$view->set('email_token_to', $crud->f->email_token_to);
			$view->set('token_name', $crud->f->token_name);
			$view->set('user_role_name', $crud->f->user_role_name);
			$view->set('user_group_name', $crud->f->user_group_name);
			$view->set('token_subject', $crud->f->token_subject);
			$view->set('token_message', $crud->f->token_message);

			$view->set('email_token', _('Email Token'));

			$view->set('self_url', $this->navigation->selfUrl());

			$view->show();
		}
	}
}

return 'EmailToken';
