<?php

class PHPDS_security extends PHPDS_dependant
{
	/**
	 * Cleaned up $_GET.
	 *
	 * @var mixed
	 */
	public $get;
	/**
	 * Cleaned up $_POST.
	 *
	 * @var mixed
	 */
	public $post;
	/**
	 * Cleaned up $_REQUEST.
	 *
	 * @var mixed
	 */
	public $request;
	/**
	 * Cleaned up $_SESSION.
	 *
	 * @var mixed
	 */
	public $session;
	/**
	 * Cleaned up $_COOKIE.
	 *
	 * @var mixed
	 */
	public $cookie;

	/**
	 * This method does the actual security check, other security checks are done on a per call basis to this method in specific scripts.
	 * Improved version reduces the cost of queries by 3, I also believe that this is a more secure method.
	 *
	 * @param boolean $validate_crypt_key Set if you would like the system to verify an encryption before accepting global $_POST variables. Use with method send_crypt_key_validation in your form.
	 * @return string
	 * @author Jason Schoeman
	 */
	public function securityIni($validate_token = false)
	{
		if (isset($_SESSION['user_id']))
			$this->_log(sprintf(___('Security check for user id %s'), $_SESSION['user_id']));

		if (!empty($this->configuration['system_down'])) {
			if ($this->configuration['user_role'] == $this->configuration['root_role']) {
				if ($this->configuration['system_down_bypass'] == false) {
					$this->template->warning(___('System is switched off for normal users, only root can access the system.'), false, false);
				}
			} else if ($this->configuration['system_down_bypass'] == false) {
				$settings_message = $this->db->getSettings(array('system_down_message'));
				$this->core->skipLogin = true;
				$this->core->haltController = sprintf($settings_message['system_down_message'], $this->configuration['scripts_name_version']);
			}
		}

		if (!empty($_POST)) $this->post = $this->sqlWatchdog($_POST);
		if (!empty($_GET)) $this->get = $this->sqlWatchdog($_GET);
		if (!empty($_COOKIE)) $this->cookie = $this->sqlWatchdog($_COOKIE);
		if (!empty($_SESSION)) $this->session = $_SESSION;
		if (!empty($_REQUEST)) $this->request = array_merge((array)$this->post,(array)$this->get);
	}

	/**
	 * Function just like mysql_real_escape_string, but does so recursive through array.
	 *
	 * @param mixed $input
	 */
	public function sqlWatchdog($input)
	{
		if (is_array($input)) {
			foreach ($input as $k => $i) {
				$output[$k] = $this->sqlWatchdog($i);
			}
		} else {
			$output = trim(htmlentities(str_replace('\\', '', $input), ENT_QUOTES, $this->configuration['charset']));
		}
		return $output;
	}

	/**
	 * Use inside your form brackets to send through a token validation to limit $this->post received from external pages.
	 *
	 * @return string Returns hidden input field.
	 */
	public function postValidation()
	{
		return $this->validatePost();
	}

	/**
	 * Use inside your form brackets to send through a token validation to limit $this->post received from external pages.
	 *
	 * @return string Returns hidden input field.
	 */
	public function validatePost()
	{
		$token = md5(uniqid(rand(), TRUE));
		$key = md5($this->configuration['crypt_key']);
		$_SESSION['token_validation'][$key] = $token;
		return $this->template->mod->securityToken($token);
	}

	/**
	 * This is used in the search filter to validate $this->post made by the search form.
	 *
	 * @return string Returns hidden input field.
	 */
	public function searchFormValidation()
	{
		$search_token = md5(uniqid(rand(), TRUE));
		$search_key = md5(sha1($this->configuration['crypt_key']));
		$_SESSION['token_validation'][$search_key] = $search_token;
		return $this->template->mod->searchToken($search_token);
	}

	/**
	 * Check if user is a root user.
	 *
	 * @deprecated
	 * @date 20100608 (v1.0.1) (greg) moved to query system
	 * @param mixed $user_id If not logged in user, what user should be checked (primary role check only).
	 * @return boolean
	 */
	public function isRoot($user_id = false)
	{
		return $this->user->isRoot($user_id);
	}

	/**
	 * Returns current logged in user id.
	 *
	 * @deprecated
	 * @return integer
	 */
	public function currentUserID()
	{
		return $this->configuration['user_id'];
	}

	/**
	 * Check if the currently logged in user is the same group as the given user
	 *
	 * This can be used to check if the current user is allowed access to the given user's data
	 *
	 * @deprecated
	 * @date 20100222
	 * @version	1.0
	 * @author greg
	 * @param $user_id integer, the ID of the other user
	 * @return boolean, whether the current user is in the same group
	 * @see	http://wiki.phpdevshell.org/wiki/Security_with_groups_and_roles
	 */
	public function isSameGroup($user_id)
	{
		return $this->user->isSameGroup($user_id);
	}

	/**
	 * Encrypts a string with the configuration key provided.
	 *
	 * @param string $string
	 * @return string
	 */
	public function encrypt($string)
	{
		$result = false;
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($this->configuration['crypt_key'], ($i % strlen($this->configuration['crypt_key'])) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return urlencode(base64_encode($result));
	}

	/**
	 * Decrypts a string with the configuration key provided.
	 *
	 * @param string $string
	 * @return string
	 */
	public function decrypt($string)
	{
		$result = false;
		$string = base64_decode(urldecode($string));
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($this->configuration['crypt_key'], ($i % strlen($this->configuration['crypt_key'])) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}

	/**
	 * Check if a user has access to a given node id.
	 *
	 * @version 1.0.1
	 * @date 20091105 fixed a possible warning when the node is not in the list (i.e. the user is not allowed)
	 * @deprecated
	 * @param mixed This can have both the node id as an integer or as a string.
	 * @param string The type of item requested, node_id, node_name etc...
	 * @return boolean Will return requested variable if user has access to requested node item node item.
	 */
	public function canAccessNode($node_id, $type = 'node_id')
	{
		if (!empty($this->navigation->navigation[$node_id][$type])) {
			return $this->navigation->navigation[$node_id][$type];
		} else {
			return false;
		}
	}

	/**
	 * Creates a "secret" version of the password
	 *
	 * @param string $password, the clear password
	 * @return string the hashed password
	 * @date 20100204 greg: created from Jason's original fct
	 * @version	1.0
	 * @author jason, greg
	 */
	public function hashPassword($password = '')
	{
		return empty($password) ? '*' : md5($password);
	}

	/**
	 * Simple method to return users IP, this method will be improved in the future if needed.
	 *
	 * @deprecated
	 * @return string
	 */
	public function userIp()
	{
		return $this->user->getUserIp();
	}

	/**
	 * Simple method to return users IP, this method will be improved in the future if needed.
	 *
	 * @deprecated
	 * @return string
	 */
	public function getUserIp()
	{
		return $this->user->getUserIp();
	}

	/**
	 * Validates email address.
	 *
	 * @param string Email address.
	 * @return boolean
	 * @author Jason Schoeman
	 */
	public function validateEmail($email_string)
	{
		if (filter_var($email_string, FILTER_VALIDATE_EMAIL) == TRUE) {
			return true;
		} else {
			return false;
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