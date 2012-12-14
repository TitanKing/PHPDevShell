<?php

/**
 * Interface a plugin must implement to be used in authentification
 */
interface iBaseLogin
{

	/**
	 * Loads the username & password html template form.
	 *
	 * @param boolean $return
	 */
	public function loginForm ($return = false);

	/**
	 * Check what to do with login action.
	 *
	 */
	public function controlLogin();

	/**
	 * Checks to see if user and password is correct and allowed. Then creates session data accordingly.
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function processLogin($username, $password);

	/**
	 * Search the database for the given credentials
	 *
	 * @param string $username
	 * @param string $password
	 * @return array or false the user record
	 */
	public function lookupUser($username, $password = '');

	/**
	 * Make the given user the logged in user
	 *
	 * @param array $select_user_array
	 * @return nothing
	 */
	public function setLogin($select_user_array, $persistent = false);

	/**
	 * Destroys login session data.
	 *
	 */
	public function clearLogin($set_guest = true);

	/**
	 * Sets all settings to guest account.
	 *
	 */
	public function setGuest();

	/**
	 * Check is user is logged in, return false if not.
	 *
	 * @return boolean
	 */
	public function isLoggedIn();
}




/**
 * This base class implements the fundations for an authentification plugin
 * It doesn't actually provides authentification (it will reject any request) but provides structure, cookie support ("remember me") and writing to the system log
 * 
 * Note: it doesn't in any deal with template or GUI, the auth plugin must do that
 * 
 * @author Jason Schoeman
 */
class PHPDS_login extends PHPDS_dependant implements iBaseLogin
{

	/**
	 * Search the database for the given credentials from a persistent cookie
	 *
	 * @param string $cookie
	 * @return array or false the user record
	 * @date 20100702 (v1.0.0) (ross) created from the lookup_user fct by jason and greg
	 * @version 1.0.0
	 * @author jason, greg, ross
	 */
	public function lookupCookieLogin($cookie)
	{
		$id_crypt = substr($cookie, 0, 6);
		$pass_crypt = substr($cookie, 6, 32);

		$found = false;

		$persistent_array = $this->selectCookie($id_crypt);

		if (!empty($persistent_array)) {
			$persistent_item = end($persistent_array);
			if ($pass_crypt == $persistent_item['pass_crypt']) {
				$cookie_id = $persistent_item['cookie_id'];
				$user_id = $persistent_item['user_id'];
				$found = true;
			}
		}

		if (!empty($found)) {
			$this->deleteCookie($cookie_id);
			$this->setUserCookie($user_id);
			$user_array = $this->selectUserFromCookie($user_id);
			$this->setLogin($user_array, "Persistent Login");
		} else {
			$this->setGuest();
			return false;
		}
	}

	/**
	 * Selects user details from provided cookie.
	 * 
	 * @param varchar $cookie
	 * @return array
	 */
	public function selectUserFromCookie ($cookie)
	{
		return $this->db->invokeQuery('LOGIN_selectUserPersistentQuery', $cookie);
	}

	/**
	 * Select cookie data by providing cookie crypt key.
	 *
	 * @param varchar $id_crypt
	 * @return array
	 */
	public function selectCookie ($id_crypt)
	{
		return $this->db->invokeQuery('LOGIN_selectCookieQuery', $id_crypt);
	}

	/**
	 * Set a persistent cookie to be used as a remember me function
	 *
	 * @param int $user_id
	 * @return array or false the user record
	 * @date 20100702 (v1.0.0) (ross) created function
	 * @version	1.0.0
	 * @author ross
	 */
	public function setUserCookie($user_id)
	{
		return $this->db->invokeQuery('LOGIN_setPersistentCookieQuery', $user_id);
	}

	/**
	 * Delete cookie from database.
	 *
	 * @param int $cookie_id
	 */
	public function deleteCookie($cookie_id)
	{
		$this->db->invokeQuery('LOGIN_deleteCookieQuery', $cookie_id);
	}

	/**
	 * Delete the current persistent cookie from the db and kill the cookie on the user end.
	 *
	 * @return boolean
	 * @date 20100702 (v1.0.0) (ross) created function
	 * @version 1.0.0
	 * @author ross
	 */
	public function clearUserCookie($user_id)
	{
		return $this->db->invokeQuery('LOGIN_deletePersistentCookieQuery', $user_id);
	}

	/**
	 * Loads the username & password html template form.
	 *
	 * @param boolean $return
	 */
	public function loginForm ($return = false)
	{
		return false;
	}

	/**
	 * Check what to do with login action.
	 *
	 * @verion 1.0.0
	 * @date 2011-06-20
	 * @author Jason Schoeman
	 */
	public function controlLogin()
	{		
		if (! empty($this->configuration['allow_remember']) && empty($_SESSION['user_id']) && isset($_COOKIE['pdspc']) && empty($_REQUEST['logout']) && empty($_POST['login'])) {
			$this->lookupCookieLogin($_COOKIE['pdspc']);
		} else
		if (! empty($_REQUEST['logout']) && $this->isLoggedIn()) {
			$this->clearLogin(true);
			$this->setGuest();
		} else
		if (! empty($_POST['login'])) {
			$user_name = empty($_POST['user_name']) ? '' : $_POST['user_name'];
			$user_password = empty($_POST['user_password']) ? '' : $_POST['user_password'];
			$this->processLogin($user_name, $user_password);
		} else {
			$this->setGuest();
		}
	}

	/**
	 * Checks to see if user and password is correct and allowed. Then creates session data accordingly.
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	public function processLogin($username, $password)
	{
		return false;
	}

	/**
	 * Search the database for the given credentials
	 *
	 * @param string $username
	 * @param string $password
	 * @return array or false the user record
	 * @date 20100204 (v1.0) (greg) created from Jason's original fct
	 * @date 20100608 (v1.0.1) (greg) moved to query system
	 * @version 1.0.1
	 * @author jason, greg
	 */
	public function lookupUser($username, $password = '')
	{
		return false;
	}

	/**
	 * Make the given user the logged in user
	 *
	 * @param array $select_user_array
	 * @return nothing
	 * @date 20100204 greg: created from Jason's original fct
	 * @version	1.0
	 * @author jason, greg
	 */
	public function setLogin($select_user_array, $persistent = false)
	{
		$conf = $this->configuration;
		$db = $this->db;

		$user_name_db = $select_user_array['user_name'];
		$user_user_password_db = $select_user_array['user_password'];
		$user_display_name_db = $select_user_array['user_display_name'];
		$user_email_db = $select_user_array['user_email'];
		$user_id_db = $select_user_array['user_id'];
		$user_group_db = $select_user_array['user_group'];
		$user_role_db = $select_user_array['user_role'];
		$user_group_name_db = $select_user_array['user_group_name'];
		$user_role_name_db = $select_user_array['user_role_name'];
		$user_language_db = $select_user_array['language'];
		$user_region_db = $select_user_array['region'];
		if (!empty($select_user_array['user_timezone'])) {
			$user_timezone_db = $select_user_array['user_timezone'];
		} else if (!empty($conf['system_timezone'])) {
			$user_timezone_db = $conf['system_timezone'];
		} else {
			$user_timezone_db = date_default_timezone_get();
		}

		$_SESSION['user_display_name'] = $user_display_name_db;
		$_SESSION['user_email'] = $user_email_db;
		$_SESSION['user_id'] = $user_id_db;
		$_SESSION['user_name'] = $user_name_db;
		$_SESSION['user_group'] = $user_group_db;
		$_SESSION['user_role'] = $user_role_db;
		$_SESSION['user_role_name'] = $user_role_name_db;
		$_SESSION['user_group_name'] = $user_group_name_db;
		$_SESSION['user_timezone'] = $user_timezone_db;

		if (!empty($user_language_db)) {
			$user_language = $user_language_db;
		} else {
			$user_language = $conf['language'];
		}

		if (!empty($user_region_db)) {
			$user_region = $user_region_db;
		} else {
			$user_region = $conf['region'];
		}

		$_SESSION['user_language'] = $user_language;
		$_SESSION['user_region'] = $user_region;

		$_SESSION['user_locale'] = $this->core->formatLocale(true, $user_language, $user_region);
		if (! empty($this->configuration['m'])) {

			if (! $persistent) {
				$db->logArray[] = array('log_type' => 4, 'user_id' => $user_id_db, 'logged_by' => $user_display_name_db, 'log_description' => ___('Logged-in'));
			} else {
				$db->logArray[] = array('log_type' => 4, 'user_id' => $user_id_db, 'logged_by' => $user_display_name_db, 'log_description' => $persistent);
			}
		}

		$this->db->cacheClear();
	}

	/**
	 * Destroys login session data.
	 *
	 * @author Jason Schoeman
	 */
	public function clearLogin($set_guest = true)
	{
		$db = $this->db;

		$this->clearUserCookie($_SESSION['user_id']);

		$db->logArray[] = array('log_type' => 5, 'log_description' => ___('Logged-out'));

		unset($_SESSION['user_email']);
		unset($_SESSION['user_id']);
		unset($_SESSION['user_name']);
		unset($_SESSION['user_group']);
		unset($_SESSION['user_role']);
		unset($_SESSION['user_display_name']);
		unset($_SESSION['user_group_name']);
		unset($_SESSION['user_role_name']);
		unset($_SESSION['user_language']);
		unset($_SESSION['user_timezone']);
		unset($_SESSION['user_region']);
		unset($_SESSION['user_locale']);

		$db->cacheClear();

		$_SESSION = array();

		session_destroy();

		if ($set_guest) $this->setGuest();
	}

	/**
	 * Sets all settings to guest account.
	 *
	 * @date 20100608 (v1.0.1) (greg) moved to query system
	 * @return string
	 * @author Jason Schoeman, greg
	 */
	public function setGuest()
	{
		$conf = $this->configuration;
		$db = $this->db;

		if (empty($_SESSION['user_name'])) {
			$settings_array = $db->essentialSettings;

			if (!empty($conf['system_timezone'])) {
				$user_timezone = $conf['system_timezone'];
			} else {
				$user_timezone = date_default_timezone_get();
			}
			$_SESSION['user_name'] = 'guest';
			$_SESSION['user_display_name'] = 'Guest User';
			$_SESSION['user_role_name'] = '';
			$_SESSION['user_group_name'] = '';
			$_SESSION['user_group'] = $settings_array['guest_group'];
			$_SESSION['user_role'] = $settings_array['guest_role'];
			$_SESSION['user_email'] = '';
			$_SESSION['user_language'] = $conf['language'];
			$_SESSION['user_region'] = $conf['region'];
			$_SESSION['user_timezone'] = $user_timezone;
			$_SESSION['user_locale'] = $this->core->formatLocale();
			$_SESSION['user_id'] = 0;
		}
	}

	/**
	 * Check is user is logged in, return false if not.
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		if (empty($_SESSION['user_id'])) {
			return false;
		} else {
			return true;
		}
	}
}