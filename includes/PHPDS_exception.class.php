<?php

/**
 * Exception extention.
 *
 * @version 1.1
 * @date 20120807 (v1.1) (greg) added "more info" ; support for factory (hence dependancy)
 */
class PHPDS_exception extends Exception
{
	protected $ignoreLines = -1;
	protected $extendedMessage = '';
	protected $previous;
	protected $seed;

	public function __construct($message = "", $code = 0, $previous = null, $dependancy = null)
	{
		$this->seed = $dependancy;

		$this->construct($message, $code, $previous);
	}

	public function construct($message = "", $code = 0, $previous = null)
	{
		if (is_a($previous, 'Exception')) {
			$this->previous = $previous;
		}
		$this->message = $message;
		$this->code = $code;
	}

	public function merge(Exception $e)
	{
		$this->construct($e->getMessage(), $e->getCode());
	}

	public function getRealException()
	{
		return is_a($this->previous, 'PHPDS_exception') ? $this->previous->getRealException() : $this;
	}

	public function getIgnoreLines()
	{
		return $this->ignoreLines;
	}

	public function getExtendedMessage()
	{
		$msg = $this->extendedMessage;

		$p = $this->previous;

		while (is_a($p, 'Exception')) {
			$msg .= $p->getMessage();
			$p = $p->getPrevious();
		}

		return $msg;
	}

	public function getExtendedTrace()
	{
		return empty($this->trace) ? $this->getTrace() : $this->trace;
	}

	public function extendMessage($str)
	{
		$this->extendedMessage .= $str;
	}

	/** the following methods are meant to be overriden */

	/**
	 * some Exception may choose to display some possible cause for the error, to help tracking down the error
	 */
	public function hasCauses()
	{
		return false;
	}

	/**
	 *  returns a special message and a list of possible causes
	 */
	public function getCauses()
	{
		return null;
	}

	/**
	 * some Exception may choose to display more info for the error, to help fixing the error
	 */
	public function hasMoreInfo()
	{
		return false;
	}

	/**
	 *  returns more information
	 */
	public function getMoreInfo()
	{
		return null;
	}
}

class PHPDS_fatalError extends PHPDS_exception
{
	public function __construct($message = "", $code = 0, $previous = null)
	{
		$error = error_get_last();

		if (isset($error['message'])) $this->message = $error['message'];
		if (isset($error['type'])) $this->code = $error['type'];
		if (isset($error['file'])) $this->file = $error['file'];
		if (isset($error['line'])) $this->line = $error['line'];

		//$this->ignoreLines = 2;
	}
}

class PHPDS_databaseException extends PHPDS_exception
{
	protected $ignoreLines = 4;

    // CAUTION this declaration is NOT correct but PHP insists on this declaration
	public function construct($message = "", $code = 0, $previous = null)
	{
		if ($code == 1045) {
			$this->ignoreLines = 5;
		}
		$msg = 'The MySQL database engine returned with an error' . ': "' . $message . '"';
		parent::construct($msg, $code, $previous);
	}

	public function hasCauses()
	{
		return in_array($this->getCode(), array(
			1044, 1045, // access denied
			0,  // unknown error
			1049,  // unknown database
			2002, // cannot connect
			1146 // table doesn't exist
			));
	}

	public function getCauses()
	{
		switch ($this->getCode()) {
			case 1044: case 1045: $special = 'db_access_denied'; break;
			case 0: $special = 'db_unknown'; break;
			case 1049: $special = 'db_unknown'; break;
			case 2002: $special = 'db_silent'; break;
			case 1146: $special = 'db_noexist'; break;
		}

		$coding_error = array(
			'PHP Coding error interrupted query model, see uncaught exception below.',
			'This is normally nothing too serious just check your code and find the mistake you made by following the exception below.'
		);
		$phpds_not_installed = array(
			'You did not run the install script',
			'If you haven\'t run the installation procedure yet, you should <a href="other/service/index.php">run it</a> now.'
		);
		$db_wrong_cred = array(
			'The wrong credentials have been given in the configuration file.',
			'Please check the content of your configuration file(s).'
		);
		$db_wrong_dbname = array(
			'The wrong database name has been given in the configuration file.',
			'Please check the content of your configuration file(s).'
		);
		$db_down = array(
			'The server is not running or is firewalled.',
			'Please check if the database server is up and running and reachable from the webserver.'
		);
		$db_denies = array(
			'The server won\'t accept the database connection.',
			'Please check if the database server is configured to accept connection from the webserver.'
		);

		switch ($special) {
			case 'db_access_denied': $result = array(
				'Access to the database was not granted using the parameters set in the configuration file.',
				array($phpds_not_installed, $db_wrong_cred)
			); break;
			case 'db_silent': $result = array(
				'Unable to connect to the database (the database server didn\'t answer our connection request)',
				array($db_down, $db_denies, $db_wrong_cred)
			); break;
			case 'db_unknown': $result = array(
				'The connection to the server is ok but the database could not be found.',
				array($coding_error, $phpds_not_installed, $db_wrong_dbname)
			); break;
			case 'db_noexist': $result = array(
				'The connection to the server is ok and the database is known but the table doesn\'t exists.',
				array($phpds_not_installed, $db_wrong_dbname)
			); break;
			default: $result = array(
				'Unknown special case.',
				array()
			);
		}
		return $result;
	}

	public function hasMoreInfo()
	{
		return is_a($this->seed, 'PHPDS_dependant');
	}
}


class PHPDS_accessException extends PHPDS_exception
{
	public $HTTPcode;
}

class PHPDS_securityException extends PHPDS_accessException
{
	public $HTTPcode = 401;
}

class PHPDS_securityException403 extends PHPDS_accessException
{
	public $HTTPcode = 403;
}

class PHPDS_pageException404 extends PHPDS_accessException
{
	public $HTTPcode = 404;
}

class PHPDS_pageException418 extends PHPDS_accessException
{
	public $HTTPcode = 418;
}


/**
 * This exception is sent when PU_sprintf() cannot operate
 */
class PHPDS_sprintfnException extends PHPDS_exception
{
	protected $ignoreLines = 5;

	public function __construct($message = "", $code = 0, $previous = null) // CAUTION this declaration is NOT correct but PHP insists on this declaration
	{
		if (is_array($message)) {
			list($format, $key) = $message;
			$msg = sprintf('Missing named argument: "%s"', $key);
		} else {
			$format = $message;
			$msg = 'Unable to build the string using sprintf';
		}
		$this->extendedMessage = '<p>The faulty string source is:</p><pre>' . htmlentities($format) . '</pre>';
		if (!empty($code)) $this->extendedMessage .=
            '<em>' . PU_dumpArray($code, _('The sprintfn parameters were:'), true) . '</em>';

		parent::__construct($msg, 0, $previous);
	}

	public function hasCauses()
	{
		return true;
	}

	public function getCauses()
	{
		$result = array(
			'Unable to build a string with <i>sprintfn</i>',
			array(
				array('Some template or theme file has altered a module which doesn\'t comply to the given parameters.',
                    'Try a different theme or check for possible typos in the theme module list'),
				array('You are using named parameters but you did not provided all parameters with their names',
                    'Check that you are using <b>invokeQueryWith</b> (since <b>invokeQuery</b> does not support named parameters)')
			)
		);

		return $result;
	}

}


/**
 * Exception extention.
 */
class PHPDS_extendNodeException extends PHPDS_exception
{
	/*protected $ignoreLines = 0;
	protected $extendedMessage = '';*/
	protected $extend = 0;
	protected $nodeid = 0;

	public function __construct($message = "", $code = 0, $previous = null) // CAUTION this declaration is NOT correct but PHP insists on this declaration
	{
		list($this->nodeid, $this->extend) = $message;
		$msg = sprintf('Problem occurred extending node item %s, it does not seem to exist.', $this->extend);

		parent::__construct($msg, 0, $previous);
	}

	public function getExtendedTrace()
	{
		return empty($this->trace) ? $this->getTrace() : $this->trace;
	}

	/**
	 * some Exception may choose to display some possible cause for the error, to help tracking down the error
	 */
	public function hasCauses()
	{
		return true;
	}

	/**
	 *  returns a special message and a list of possible causes
	 */
	public function getCauses()
	{
		$navigation = $this->navigation;
		$result = array(
			'The current node item is acually a link to a base node item, which cannot be accessed',
			array(
				array('The "extend" field of the node item maybe incorrect (wrong value, base node has been deleted...)',
						'Edit the node item and specify a base node item to extend from.'
				),
				array('The base node may exists but not be accessible for the current user',
						'Edit the base node item and check its authorizations.'
				)
			)
		);

		return $result;
	}
}


/**
 *  Exception for PHPDS_query
 *
 * @date 20120724 (v1.0) (greg) added
 */
class PHPDS_queryException extends PHPDS_exception
{
	protected $ignoreLines = 5;

	public function __construct($message = "", $code = 0, $previous = null) // CAUTION this declaration is NOT correct but PHP insists on this declaration
	{
		$msg = 'Error executing a query';
		$this->extendedMessage = '<p>The faulty query REAL SQL was:</p><pre>' . $message . '</pre>';
		if (is_a($previous, 'PHPDS_exception')) {
			$previous->extendMessage($this->extendedMessage);
		}

		parent::__construct($msg, 0, $previous);
	}

}

/**
 *  Exception for the session starter
 *
 * @date 20120724 (v1.0) (greg) added
 */
class PHPDS_sessionException extends PHPDS_exception
{
	protected $ignoreLines = 4;

	protected $path;

	public function __construct($message = "", $code = 0, $previous = null) // CAUTION this declaration is NOT correct but PHP insists on this declaration
	{
		$this->path = $message;
		$msg = 'Unable to start the session.';

		parent::__construct($msg, $code, $previous);
	}

	/**
	 * some Exception may choose to display some possible cause for the error, to help tracking down the error
	 */
	public function hasCauses()
	{
		return true;
	}

	/**
	 *  returns a special message and a list of possible causes
	 */
	public function getCauses()
	{
		$path = realpath($this->path);
		$result = array(
			'The session manager of PHP could not be started',
			array(
				array('The session folder is not writable or missing',
						'check that the folder "<em>' . $path . '</em>" is present and writable, then reload this page.'
				),
				array('The session file exists and is protected',
						'check that in the folder "<em>' . $path . '</em>", there is no file named as given below.'
				)
			)
		);

		return $result;
	}
}