<?php

/**
 * Contains methods to handle emails appropriate.
 * PHPMailer could also be used.
 * @author Jason Schoeman
 */
class mailer extends PHPDS_dependant
{
	/**
	 * Contains default database settings.
	 * @var array
	 */
	public $mailsetting;
	/**
	 * Limits the amount of emails to send out per outgoing cycle, this prevents timeouts.
	 * @var integer
	 */
	public $massmailLimit;

	/**
	 * Construct.
	 * @param <type> $dependance
	 */
	public function construct() {
		// Get PHPMailer.
		require_once ('plugins/PHPMailer/resources/class.phpmailer.php');
		$this->parent = new PHPMailer(true);
	}

	/**
	 * Loads all default PHPDevShell mail settings as defined in general settings for easy and quick sending.
	 *
	 */
	public function DefaultSettings ()
	{
		$db = $this->db;
		// Load email settings from Database...
		$this->mailsetting = $db->getSettings(array(
            'email_fromname' , 'from_email' , 'email_order' , 'setting_admin_email' , 'email_option' ,
            'sendmail_path' , 'smtp_secure' , 'smtp_host' , 'smtp_port' , 'smtp_username' , 'smtp_password' ,
            'smtp_timeout' , 'smtp_helo' , 'email_charset' , 'email_encoding' , 'email_hostname' , 'massmail_limit'),
            'AdminTools');
		// Charset
		$this->CharSet = $this->mailsetting['email_charset'];
		// Encoding
		$this->Encoding = $this->mailsetting['email_encoding'];
		// From
		if (empty($this->From)) $this->From = $this->mailsetting['from_email'];
		// FromName
		if (empty($this->FromName)) $this->FromName = $this->replaceAccents($this->mailsetting['email_fromname']);
		// Mailer
		$this->Mailer = $this->mailsetting['email_option'];
		// Sendmail
		$this->Sendmail = $this->mailsetting['sendmail_path'];
		// Hostname
		$this->Hostname = $this->mailsetting['email_hostname'];
		// Should we assign SMTP settings?
		if ($this->Mailer == 'smtp') {
			// Host
			$this->Host = $this->mailsetting['smtp_host'];
			// Port
			$this->Port = $this->mailsetting['smtp_port'];
			// Helo
			$this->Helo = $this->mailsetting['smtp_helo'];
			// SMTPSecure
			$this->SMTPSecure = $this->mailsetting['smtp_secure'];
			// Username
			if (! empty($this->mailsetting['smtp_username'])) {
				$this->SMTPAuth = true;
				$this->Username = $this->mailsetting['smtp_username'];
			} else {
				$this->SMTPAuth = false;
				$this->Username = false;
			}
			// Password
			$this->Password = $this->mailsetting['smtp_password'];
			// Timeout
			$this->Timeout = $this->mailsetting['smtp_timeout'];
		}
		// massmail_limit
		if (! empty($this->mailsetting['massmail_limit'])) {
			$this->massmailLimit = $this->mailsetting['massmail_limit'];
		} else {
			$this->massmailLimit = 100;
		}
	}

	/**
	 * Replaces accents with plain text for a given string.
	 *
	 * @param string $string
	 */
	public function replaceAccents($string)
	{
		$string = html_entity_decode($string, ENT_QUOTES, $this->configuration['charset']);
		return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
	}

	/**
	 * Simple linear email sending method. Use sendnow to send more advanced emails.
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param string $from
	 * @param string $headers
	 * @param string $cc
	 * @param string $bcc
	 * @param string $attachment
	 * @param string $text_only_message
	 * @param string $content_type
	 * @param integer $email_priority
	 * @return boolean
	 */
	public function sendmail ($to, $subject, $message, $from = null, $headers = null, $cc = null, $bcc = null, $attachment = null, $text_only_message = null, $content_type = null, $email_priority = null)
	{
		$template = $this->template;
		// Load default Settings.
		$this->DefaultSettings();
		try {
			// For backwards compatibility we need to be able to capture these.
			// Lets see if we have multiple to recipients.
			// to
			$to = $this->replaceAccents($to);
			if (! empty($to)) {
				if (stripos($to, ',')) {
					$to_ = str_replace(' ', '', explode(',', $to));
					// Loop to.
					foreach ($to_ as $email_address_to) {
						$this->AddAddress("$email_address_to");
					}
				} else {
					$this->AddAddress("$to");
				}
			}
			// subject
			if (! empty($subject)) $this->Subject = $this->replaceAccents($subject);

			// content_type
			if (! empty($content_type)) $this->ContentType = $content_type;
			// message
			if (! empty($message)) {
				// Check message type.
				if ($this->ContentType == 'text/html') {
					$this->MsgHTML($message);
				} else {
					$this->Body = $message;
				}
			}
			// from
			if (! empty($from)) $this->From = $from;
			// headers
			if (! empty($headers)) $this->AddCustomHeader($headers);
			// cc
			if (! empty($cc)) $this->AddCC($cc);
			// bcc
			if (! empty($bcc)) $this->AddBCC($bcc);
			// attachment
			if (! empty($attachment)) $this->AddAttachment($attachment);
			// text_only_message
			if (! empty($text_only_message)) $this->AltBody = $text_only_message;
			// bcc
			if (! empty($email_priority)) $this->Priority = $email_priority;
			// Send mail out.
			if ($this->Send()) {
				// Clear loaded mail.
				$this->ClearSend();
				return true;
			} else {
				// Clear loaded mail.
				$this->ClearSend();
				return false;
			}
		} catch (phpmailerException $e) {
			$template->warning($e->errorMessage());
		} catch (Exception $e) {
			$template->warning($e->getMessage());
		}
	}

	/**
	 * Clears all stored objects to prepare for new email send.
	 *
	 */
	public function ClearSend ()
	{
		// Clear...
		$this->ClearAddresses();
		$this->ClearAllRecipients();
		$this->ClearAttachments();
		$this->ClearBCCs();
		$this->ClearCCs();
		$this->ClearCustomHeaders();
		$this->ClearReplyTos();
	}

	/**
	 * Validates email address.
	 *
	 * @param string Email address.
	 * @return boolean
	 * @author Jason Schoeman
	 * @since 2007/01/03
	 */
	public function validate ($email_string)
	{
		if (filter_var($email_string, FILTER_VALIDATE_EMAIL) == TRUE) {
			return true;
		} else {
			return false;
		}
	}
}