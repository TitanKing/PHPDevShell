<?php

/**
 * Controller Class: Handles Login Requests
 * @author Jason Schoeman
 * @return string
 */
class LoginPage extends PHPDS_controller
{
	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$configuration = $this->configuration;
		$db = $this->db;
		$security = $this->security;
		$template = $this->template;
		$navigation = $this->navigation;
		$user = $this->user;

		// Timestamp
		$timestamp = $configuration['time'];

		// Load views.
		$view = $this->factory('views');

		if ($user->isLoggedIn()) {
			// Header info.
			$template->heading(__('Log-out'));
			// Check if we have a login message to display.
			if (!empty($settings['login_message']))
				$template->message($settings['login_message']);
			// Login notice information.
			if (! empty($security->post['login']))
				$template->ok(__('You logged-in as ') . $configuration['user_display_name']);
			// Clears all persistent cookies from the database
			if ((!empty($security->post['pclear']) || !empty($security->get['pclear'])) && $user->isLoggedIn()) {
				// Clear the database
				$db->invokeQuery('PHPDS_clearPersistentDBQuery', $configuration['user_id']);
                $template->ok('Cleared from automatic login database.');
			}

			// Set Buttons.
			$view->set('log_out', $configuration['user_display_name']);
			$view->set('p_clear', __('Clear Persistent'));

			// Set Values.
			$view->set('self_url', $navigation->selfUrl());
			$view->set('post_validation', $security->postValidation());

			// Output Template.
			$view->show();

		} else {
            $template->heading(__('Log-in'));
			// Login template.
			$this->core->themeFile = 'login.php';
		}
	}
}

return 'LoginPage';
