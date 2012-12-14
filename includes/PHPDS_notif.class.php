<?php

class PHPDS_notif extends PHPDS_dependant
{
	const SILENT = 0;
	const MESSAGE = 1;
	const URGENT = 2;
	/**
	 *
	 * @var array data saved from the previous request
	 */
	protected $legacy;

	/**
	 *
	 * @var array data added during the current request
	 */
	protected $heritage = array(
		PHPDS_notif::SILENT => array(),
		PHPDS_notif::MESSAGE => array(),
		PHPDS_notif::URGENT => array()
	);

	/**
	 *
	 * @var string name of the $_SESSION element used to store the data
	 */
	protected $varName = 'PHPDS_notifications';


	/*** public (API) methods) ***/

	/**
	 *
	 * @param string $message a message to add to the notifications
	 */
	public function add($message, $priority=PHPDS_notif::MESSAGE)
	{
		$this->heritage[$priority][] = $message;
	}

	/**
	 *
	 * @return array the array of strings messages
	 *
	 */
	public function fetch($priority=PHPDS_notif::MESSAGE)
	{
		$this->import();
		$notifications = array_merge($this->legacy[$priority], $this->heritage[$priority]);
		$this->clear();

		return $notifications;
	}

	/*** private methods ***/

	function __destruct()
	{
		$this->import();
		$this->save();
	}

	protected function import()
	{
		if (is_null($this->legacy)) {
			$this->legacy = !empty($_SESSION[$this->varName]) ? $_SESSION[$this->varName] : array(
				PHPDS_notif::SILENT => array(),
				PHPDS_notif::MESSAGE => array(),
				PHPDS_notif::URGENT => array()
			);
			$this->set(null);
		}
	}

	protected function save()
	{
		/*$s = session_name();
		$id = session_id();
		$p = session_save_path();*/

		$this->set(array_merge($this->legacy, $this->heritage));
	}

	public function waiting()
	{

	}

	public function clear()
	{
		$this->legacy = null;
		$this->heritage = array();

		$this->set(null);
	}

	protected function set($value = null)
	{
		$_SESSION[$this->varName] = $value;
	}
}
