<?php

/**
 * Contains methods to handle standard logins.
 * @author Jason Schoeman
 */
class StandardLogin extends PHPDS_login
{
	/**
	 * The original login page.
	 * @var int
	 */
	public $loginPageId = 'login';

	/**
	 * The original registration page.
	 * @var int
	 */
	public $registrationPageId = 'register-account';

	/**
	 * The original lost password page.
	 * @var int
	 */
	public $lostPasswordPageId = 'lost-password';

	/**
	 * Loads the username & password html template form.
	 *
	 * @param boolean $return
	 */
	public function loginForm ($return = false)
	{
		$settings = $this->db->essentialSettings;
		$navigation = $this->navigation;
		$security = $this->security;
		$template = $this->template;
		$configuration = $this->configuration;
		$redirect_page = '';

		if (empty($configuration['m']))
			$configuration['m'] == $this->loginPageId;

		// Determine page to post form too.
		if ($configuration['m'] == $this->loginPageId) {
			$post_login_url = $navigation->buildURL($settings['redirect_login']);
		} else {
			$post_login_url = $_SERVER['REQUEST_URI'];
		}

		// Assign reusable username.
		$user_name = (empty($_POST['user_name'])) ? '' : $_POST['user_name'];

		// Determine what to show as per request.
		if (! $this->isLoggedIn()) {
			// Check if not registered link should appear.
			if ((boolean) $settings['allow_registration'] == true) {
				// Check if we have a custom registration page.
				$registration = (! empty($settings['registration_page'])) ?  $navigation->buildURL($settings['registration_page']) : $navigation->buildURL($this->registrationPageId);
			} else {
				$registration = false;
			}

			// Create the "remember me" checkbox, if needed
			if (! empty($settings['allow_remember'])) {
				$remember = ___('Remember Me?');
			} else {
				$remember = false;
			}
			// Create HTML login field.
			return $template->mod->loginForm($post_login_url, ___('Username or Email'), ___('Password'), $redirect_page, $navigation->buildURL($this->lostPasswordPageId), ___('Lost Password?'), $registration, ___('Not registered yet?'), $remember, $security->postValidation(), ___('Account Detail'), $user_name, __('Submit'));
		}
	}

	/**
	 * Checks to see if user and password is correct and allowed. Then creates session data accordingly.
	 *
	 * @param string $username
	 * @param string $password
	 * @date 20100204 greg: split into pieces
	 * @version	1.1
	 * @author jason, greg
	 */
	public function processLogin($username, $password)
	{
		if (empty($username) || empty($password)) {
			$this->template->notice(___('You did not complete required username and password fields.'));
		} else {
			if ($this->lookupUsername($username)) {
				// Simple method to lookup user by providing username and password.
				$user_array = $this->lookupUser($username, $password);

				// Check if we have a login to process.
				if (! empty($user_array)) {
					$this->setLogin($user_array);
					if ($this->db->essentialSettings['allow_remember'] && isset($_POST['user_remember'])) {
						$this->setUserCookie($user_array['user_id']);
					}
				} else {
					$this->core->haltController = array('type'=>'auth','message'=>___('Incorrect Password'));
					$this->template->notice(___('You used a valid username with a <strong>wrong password</strong>. Remember, it is Case Sensitive.'));
				}
			} else {
				$this->core->haltController = array('type'=>'auth','message'=>___('Incorrect Login Data'));
				$this->template->notice(___('Your <strong>username</strong> could not be found. Remember, it is Case Sensitive.'));
			}
		}
	}

	/**
	 * Search the database for the given credentials
	 *
	 * If don't give the password (not the same an empty string), only the username will be checked
	 *
	 * @param string $username
	 * @param string $password
	 * @return array or false the user record
	 * @date 20100204 (v1.0) (greg) created from Jason's original fct
	 * @date 20100608 (v1.0.1) (greg) moved to query system
	 * @date 20110804 (v1.0.2) (greg) handle null password as name-only lookup
	 * @version 1.0.2
	 * @author jason, greg
	 */
	public function lookupUser($username, $password = '')
	{
		$password = is_null($password) ? '*' : $this->security->hashPassword($password);
		return $this->db->invokeQuery('LOGIN_selectUserQuery', $username, $password);
	}

	/**
	 * Check if the username exists.
	 *
	 * @param string $username
	 * @version 1.0.0
	 * @author jason
	 */
	public function lookupUsername($username)
	{
		return $this->db->invokeQuery('LOGIN_selectUserNameQuery', $username);
	}

}