<?php

/**
 * Controller Class: Email admin for queries.
 * @author Jason Schoeman
 * @return string
 */
class EmailAdmin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Load Extra Classes ////////////////////////////////////////////////////////////////////////////////////////////////////
		$email = $this->factory('mailer');

		/* @var $crud crud */
		$crud = $this->factory('crud');

		/* @var $spam botBlock */
		$spam = $this->factory('botBlock');

		$this->template->heading(_('Query Assistance'));

		$crud->addField('user_email', $this->configuration['user_email']);
		$crud->addField('name', $this->configuration['user_display_name']);
		$crud->addField('priority', 3);
		$crud->addField('query_type', 1);

		$settings_ = $this->db->getSettings(array('setting_support_email', 'setting_admin_email'), 'PHPDevShell');

		$admin_email = explode(",", $settings_['setting_admin_email']);

		if (! empty($settings_['setting_support_email'])) {
			$options = explode(",", $settings_['setting_support_email']);
			$i = 0;
			if (! empty($options)) {
				foreach	($options as $option) {
					$row = explode(":", trim($option));
					$i++;
					if (! empty($row[0]) && ! empty($row[1])) {
						$mail_arr = explode(";", trim($row[0]));
						foreach($mail_arr as $mail_addy) {
							if ($mail_addy == 'default') {
								foreach($admin_email as $admin_email_) {
									if (! empty($admin_email_)) $email_[] = trim($admin_email_);
								}
							} else {
								if (! empty($mail_addy)) $email_[] = trim($mail_addy);
							}
						}
						$email_options[$i] = $email_;
						$type_options[$i] = trim($row[1]);
						$email_ = array();
					}
				}
			}
		}

		if ($crud->POST('send_mail')) {

			if (!$crud->isEmail('email_from'))
				$crud->error(_('Please provide a valid email address'));

			if (!$crud->is('query_type'))
				$crud->error(_('Please select type of query'));

			if (!$crud->is('name'))
				$crud->error(_('Please provide name for query'));

			if (!$crud->is('subject'))
				$crud->error(_('Please provide a subject'));

			if (!$crud->is('message'))
				$crud->error(_('Please provide a query message'));

			$crud->is('priority');

			if ($crud->ok() && $spam->block()) {
				$message = $this->template->htmlEntityDecode($crud->f->message);

				if (! empty($email_options[$crud->f->query_type])) {

					$emailtoarr = $email_options[$crud->f->query_type];

					if (! empty($admin_email)) {
						foreach($admin_email as $admin_email_) {
							if(! in_array($admin_email_, $emailtoarr)) {
								if (! empty($admin_email_))
									$emailtoarr[] = trim($admin_email_);
							}
						}
					}

					if (! empty($emailtoarr)) {
						$email___ = '';
						foreach ($emailtoarr as $email__) {
							$email___ .= "$email__,";
						}

						$email___ = rtrim($email___, ",");

						if (! empty($email___) && ! empty($type_options[$crud->f->query_type])) {
							$email->FromName = $crud->f->name;
							if ($email->sendmail("$email___", $type_options[$crud->f->query_type] . ": " . $crud->f->subject, $message, "{$crud->f->email_from}", null, null, null, null, null, 'text/html', $crud->f->priority)) {
								$this->template->ok(sprintf(_('Thank you %s, The query (%s) was sent successfully.'), "{$crud->f->email_from} \"{$crud->f->name}\"", $type_options[$crud->f->query_type]));
							}
						}
					}
				}
			} else {
				$crud->errorShow();
			}
		}

		$view = $this->factory('views');

		$view->set('send_message', _('Send Message'));
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('email_from', $crud->f->email_from);
		$view->set('name', $crud->f->name);
		$view->set('id', $this->configuration['user_id']);
		$view->set('user_email', $crud->f->user_email);
		$view->set('subject', $crud->f->subject);
		$view->set('message', $crud->f->message);
		$view->set('query_type', $crud->radio('query_type', $type_options, array($crud->f->query_type)));
		$view->set('priority', $crud->radio('priority', array(1=>_('High'), 3=>_('Normal'), 5=>_('Low')), array($crud->f->priority)));
		$view->set('botBlockFields', $spam->botBlockFields());

		$view->show();
	}
}

return 'EmailAdmin';
