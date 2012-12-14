<?php

/**
 * Silent blocks spam for forms.
 *
 * @author Jason Schoeman, maheshchari.com
 */
class botBlock extends PHPDS_dependant
{
	/**
	 * The time in seconds that is required for this form to be completed.
	 * If it is completed faster than human possible, it is probably automated.
	 *
	 * @var int
	 */
	public $timeLapse = 10;
	/**
	 * Allowed time in seconds between forms submission.
	 * If the same form is submitted again after a short perious of time.
	 *
	 * @var int
	 */
	public $throttle = 10;
	/**
	 * Before throttling, we should allow the user at least one correction before we start to check throttling time.
	 *
	 * @var int
	 */
	public $chancesBeforeThrottle = 2;
	/**
	 * a Name for fake forms fields to catch the bot out, if left default, it will be auto generated.
	 *
	 * @var string
	 */
	public $fakeName = 'default';
	/**
	 * The element to add the secret key field to the form.
	 *
	 * @var string
	 */
	public $addToDOM = 'form';
	/**
	 * Does a validation of the form by comparing secret keys inside it, bots will mosty not be able to figure this out and will require the secret key with javascript to do so.
	 *
	 * $var boolean
	 */
	public $validateKey = true;

	/**
	 * Initiate botBlock.
	 */
	public function construct()
	{
		if (empty($this->security->post) && empty($_SESSION['botBlockTimeLapse'])) $_SESSION['botBlockTimeLapse'] = time();

		$validatePost = $this->template->mod->botBlockSecret($this->validatePost(), $this->addToDOM);
		$this->template->addJsToHead($validatePost);
	}

	/**
	 * Adds fake fields to your forms for extra checking, spam bots will complete them so we can catch them.
	 *
	 * @return boolean
	 */
	public function botBlockFields()
	{
		if ($this->fakeName == 'default') {
			$this->fakeName = $this->configuration['m'];
		}

		return $this->template->mod->botBlockFields($this->fakeName);
	}

	/**
	 * This is called in controller with final condition to validate the form against spam.
	 * I am not really feeling good today, life is too much sometimes, my wife sometimes just wont understand me...
	 *
	 * @return boolean
	 */
	public function block ()
	{
		if (empty($this->configuration['spam_assassin'])) return true;

		if (empty($this->security->post)) {
			$_SESSION['botBlockTimeLapse'] = time();
		} else {
			// Check 1, can we have sessions (many bots can't).
			if (empty($_SESSION['botBlockTimeLapse']))
				return $this->redirectBot(__(sprintf('This client does not support sessions.'), 'BotBlock'));

			// Check 2, how fast was the form completed, a human can't do it in a few seconds.
			if ($this->timeLapse != 0) {
				if (! empty($_SESSION['botBlockTimeLapse'])) {
					$executed = time() - $_SESSION['botBlockTimeLapse'];
				} else {
					$executed = $this->timeLapse;
				}

				if ($executed < $this->timeLapse)
					return $this->redirectBot(__(sprintf('Too fast for a human (completed in %s seconds).', $executed), 'BotBlock'));
			}
			// Check 3, How fast was the same form submitted again?
			if ($this->throttle != 0) {
				if (! empty($_SESSION['botBlockSubmitted_' . $this->configuration['m']])) {
					if (empty($_SESSION['formErrorCount'])) {
						$_SESSION['formErrorCount'] = 1;
					} else {
						$_SESSION['formErrorCount'] = $_SESSION['formErrorCount'] + 1;
					}

					if ($_SESSION['formErrorCount'] >= $this->chancesBeforeThrottle) {
						$lastform = time() - $_SESSION['botBlockSubmitted_' . $this->configuration['m']];
						if ($lastform < $this->throttle) {
							$_SESSION['botBlockSubmitted_' . $this->configuration['m']] = time();
							return $this->redirectBot(__(sprintf('Same form submitted too quickly (re-submitted in %s seconds).', $lastform), 'BotBlock'));
						}
						$_SESSION['formErrorCount'] = 0;
						$_SESSION['botBlockSubmitted_' . $this->configuration['m']] = time();
					}
				} else {
					$_SESSION['botBlockSubmitted_' . $this->configuration['m']] = time();
				}
			}
			// Check 4, did the stupid bot fill in the hidden fields?
			if ($this->fakeName == 'default') {
				$this->fakeName = $this->configuration['m'];
			}
			$fakeText = 'text_' . $this->fakeName;
			$fakeCheck = 'check_' . $this->fakeName;
			if (! empty($this->security->post[$fakeText]) || ! empty($this->security->post[$fakeCheck])) {
				return $this->redirectBot(__('Hidden fields completed by bot.', 'BotBlock'));
			}
			// Check 5, can the bot unhide a field with javascript and then provide required secret key... I don't think so.
			if ($this->validateKey == true) {
				if (empty($this->security->post['token_validation'])) {
					return $this->redirectBot(__('Token validation field empty.', 'BotBlock'));
				} else {
					return $this->validateToken();
				}
			}
		}
		return true;
	}

	/**
	 * Use inside your form brackets to send through a token validation to limit $this->post received from external pages.
	 *
	 * @return string Returns hidden input field.
	 */
	protected function validatePost()
	{
		$key = md5($this->configuration['crypt_key']);
		if (empty($_SESSION['token_validation'][$key])) {
			$token = md5(uniqid(rand(), TRUE));
			$_SESSION['token_validation'][$key] = $token;
		} else {
			$token = $_SESSION['token_validation'][$key];
		}
		return $this->template->mod->securityToken($token);
	}

	/**
	 * Validates the posted key against system key, it is most probable that the spam bot wont know what it is.
	 *
	 * @return boolean
	 */
	protected function validateToken()
	{
		if (! empty($this->security->post)) {
			$key = md5($this->configuration['crypt_key']);
			if (!empty($_SESSION['token_validation'][$key])) {
				$token_validation_key = $_SESSION['token_validation'][$key];
			} else {
				$token_validation_key = 0;
			}

			if (!empty($this->security->post['token_validation'])) {
				$token_validation__ = $this->security->post['token_validation'];
			} else {
				$token_validation__ = 1;
			}

			if ($token_validation_key !== $token_validation__)
				return $this->redirectBot(__('Automated form submission, the secret token key was wrong.', 'BotBlock'));

			return true;
		}
	}

	/**
	 * Logs bot confirmed post attempt and forwards it to error page.
	 *
	 * @param string Spam detection in error message.
	 * @return boolean
	 */
	public function redirectBot ($message)
	{
		$log_type = 2; ////////////////////
		// Log the event //////////////////
		$this->db->logArray['spam'] = array('log_type' => $log_type , 'log_description' => __(sprintf('SPAM ALERT: Stopped for showing spambot like behaviour; %s', $message), 'BotBlock'));
		$this->core->haltController = array('type'=>'418','message'=>$message);
		return false;
	}
}