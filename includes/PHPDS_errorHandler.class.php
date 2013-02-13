<?php

/**
 * Error handling function with use of FirePHP
 *
 * @author Grzegorz Godlewski redone by Jason Schoeman
 */


include_once dirname(__FILE__).'/PHPDS_exception.class.php';

interface iPHPDS_errorConduit
{
	/**
	 * Outpust a single message
	 *
	 * @see class PHPDS_debug
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param string $domain domain to which the message is related
	 * @param string $msg a text message to handle
	 * @param integer $level (optional) severity level (can be PHPDS_debug::DEBUG, PHPDS_debug::INFO, PHPDS_debug::WARN, PHPDS_debug::ERROR or PHPDS_debug::LOG)
	 * @param string $label (optional) a text label to given to the message
	 * @return null
	 */
	public function message($domain, $msg, $level = 0, $label = '');

	/**
	 * Outpust an exception
	 *
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param string $domain domain to which the message is related
	 * @param PHPDS_exception $ex a complete exception object
	 * @return string
	 */
	public function exception($domain, $ex);
}


/**
 * Error handler class
 *
 */
class PHPDS_errorHandler extends PHPDS_dependant
{
	/**
	 * Error handler options
	 */

	protected $ignore_notices = false; // - if set to true Error handler ignores notices
	protected $ignore_warnings = false; // - if set to true Error handler ignores warnings
	protected $warningsAreFatal = true; // if true warning are handled as Exceptions
	protected $noticesAreFatal = false; // if true, notices are handled as Exceptions

	protected $serverlog = true; // log to syslog using error_log()
	protected $file = ''; // - log file
	protected $mail = ''; // - log mail
	protected $display = true; // - if set to true Error handler display error to output
	protected $firebug = false; // - if set to true Error handler send error to firebug
	protected $firephp = null; // - firephp object

	protected $I_give_up = false; // if this is true something serious is wrong.
	protected $production = false; // is this a system in production

	public $error_backtrace = false; // - Should a backtrace be created. (Causes problems some times)

	protected $conduits = array(); // array of iPHPDS_errorConduit

	protected $crumbs = array(); // in case they are error AFTER the exception reported is triggered

	/**
	 * Construtor
	 *
	 * @date 20100402 (v1.0.1) (greg) fix a typo regarding firephp config field
	 * @date 20100927 (v1.0.2) (greg) using the new constructor
	 * @date 20110808 (v1.0.3) (greg) don't do anything if we're running embedded (for example unit testing)
	 *
	 * @version 1.0.3
	 */
	public function construct()
	{
		if ($this->PHPDS_dependance()->isEmbedded()) {
			return;
		}

		$flags = E_ALL;

		$this->production = !empty($this->configuration['production']);

		if (isset($this->configuration['error'])) {
			$configuration = $this->configuration['error'];

			if ($configuration['mask']) $flags = intval($configuration['mask']);

			if (isset($configuration['ignore_notices'])) $this->ignore_notices = !empty($configuration['ignore_notices']);
			if (isset($configuration['ignore_warnings'])) $this->ignore_warnings = !empty($configuration['ignore_warnings']);
			if (isset($configuration['warningsAreFatal'])) $this->warningsAreFatal = !empty($configuration['warningsAreFatal']);
			if (isset($configuration['noticesAreFatal'])) $this->noticesAreFatal = !empty($configuration['noticesAreFatal']);

			// Backends
			if (!empty($configuration['serverlog'])) $this->serverlog = !empty($configuration['serverlog']);
			if (!empty($configuration['file_log_dir'])) $this->file = $configuration['file_log_dir']; // TODO: check file is writable
			if (!empty($configuration['email_critical'])) $this->mail = $configuration['email_critical'];
			if (isset($configuration['display'])) $this->display = !empty($configuration['display']);
			if (isset($configuration['firePHP'])) $this->firebug = !empty($configuration['firePHP']);

			if ($this->firebug) {
				require_once ('debug/FirePHPCore/FirePHP.class.php');
				$this->firephp = FirePHP::getInstance(true);
			}

			if (! empty($this->ignore_notices)) {
				$flags = $flags ^ E_NOTICE;
				$flags = $flags ^ E_USER_NOTICE;
			}
			if (!empty($this->ignore_warnings)) {
				$flags = $flags ^ E_WARNING;
				$flags = $flags ^ E_USER_WARNING;
			}

			if (isset($configuration['conduits']) && is_array($configuration['conduits'])) {
				foreach($configuration['conduits'] as $conduit) {
					$this->addConduit($conduit);
				}
			}
		}

		error_reporting($flags);
		set_error_handler(array($this, "doHandleError"), $flags);
		set_exception_handler(array($this, "doHandleException"));
		register_shutdown_function(array($this, "doHandleShutdown"));
	}

	public function getFirePHP()
	{
		return $this->firephp;
	}

	public function addConduit($conduitName)
	{
		if (!is_string($conduitName)) {
			throw new PHPDS_exception('New conduit name must be a string.');
		}
		if (empty($this->conduits[$conduitName])) {
			$this->conduits[$conduitName] = $this->factory($conduitName); //iPHPDS_errorConduit
		}
		return $this;
	}

	/**
	 * Handle critical errors (if set to)
	 */
	public function doHandleShutdown()
	{
		if ($this->I_give_up) return; // avoid re-entrancy

		$error = error_get_last();
		$errmask = error_reporting();
		if ($errmask & $error['type']) {
			$this->doHandleException(new PHPDS_fatalError());
		}

		$this->I_give_up = true;
	}

	/**
	 * Exception handler
	 *
	 * @date 20120511 (v1.1) (greg) handle extended report
	 * @date 20120724 (v1.2) (greg) the bottom-most exception is used in case of stacked PHPDS_exception's
	 *
	 * @version 1.2
	 *
	 * @param Exception $ex Exception
	 */
	public function doHandleException(Exception $ex)
	{
		if ($this->I_give_up) return;

		if (is_a($ex, 'PHPDS_exception')) {
			$ex = $ex->getRealException();
		}

		try {
			$errMsg = $ex->getMessage();
			$backtrace = $ex->getTrace();
			if (! $ex instanceof errorHandler) {
				$errMsg_subject = get_class($ex) . ': ' . $errMsg;
				$errMsg = $errMsg_subject . " file : {$ex->getFile()} (line {$ex->getLine()})";
				array_unshift($backtrace, array('file' => $ex->getFile(), 'line' => $ex->getLine(), 'function' => 'throw ' . get_class($ex), 'args' => array($errMsg, $ex->getCode())));
			}
			$errMsg .= ' | ' . date("Y-m-d H:i:s");
			if (empty($_SERVER['HTTP_HOST'])) {
				$errMsg .= ' | ' . implode(' ', $_SERVER['argv']);
			} else {
				$errMsg .= ' | ' . $_SERVER['HTTP_HOST'] . " (" . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . ")" . "\n";
			}
			if ($this->error_backtrace == true)	{
				$trace = PHPDS_backtrace::asText(2, $backtrace);
			} else {
				$trace = false;
			}

			// This will take care of Firebug (textual), in-page alerts, and syslog
			$this->conductor($errMsg, PHPDS_debug::ERROR);


			// SENDING THROUGH FIREBUG (extended info with backtrace)
			if ($this->firephp && !$this->production && !headers_sent()) {
				$this->firephp->fb($ex);
			}

			///// DISPLAYING ON THE WEB PAGE
			try {
				// in production we capture the whole output but display only a generic message
				$output = $this->showException($ex, !$this->production);
			} catch (Exception $e) {
				error_log('An exception occured in the exception display.'.$e);
			}


			///// WRITING TO A LOG FILE
			if ($this->file) {
				$dir = realpath(BASEPATH.$this->file).DIRECTORY_SEPARATOR;

				if ($dir) {
					$prefix = 'error.' . date('Y-m-d');
					$filepath = $dir . $prefix . '.log';

					$unique_html_name = $prefix . '.' . uniqid() . '.html';
					$detailedfilepath =  $dir . $unique_html_name;
					$detailedurlpath = $this->configuration['absolute_url'] . '/' . $this->configuration['error']['file_log_dir'] . $unique_html_name;

					$fp = fopen($filepath, "a+");

					if (flock($fp, LOCK_EX)) {
						fwrite($fp, "----\n$detailedfilepath | $detailedurlpath | " . $errMsg . "\n" . $trace . "\n");
						flock($fp, LOCK_UN);
					}

					fclose($fp);

					/// STORE EXTENDED REPORT
					$fp = fopen($detailedfilepath, "a+");
					if (flock($fp, LOCK_EX)) {
						fwrite($fp, $output);
						flock($fp, LOCK_UN);
					}
					fclose($fp);
				}
			}

			// SENDING AN EMAIL
			if ($this->mail) {
				$headers = 'MIME-Version: 1.0' . "\n" . 'Content-type: text/plain; charset=UTF-8' . "\n" . 'From: Error Handler <' . $this->mail . ">\n";
				@mail("$this->mail", "$errMsg_subject", "$errMsg\r\n$trace\r\n\r\n$detailedfilepath\r\n$detailedurlpath", $headers);
			}


		} catch (Exception $e) {
			// something bad happend in the exception handler, we build a new exception to describe that in the error page
			$this->I_give_up = true;
			$msg = _('An exception occured in the exception handler. URL was: "'.$_SERVER['REQUEST_URI'].'"');
			$ex = new PHPDS_Exception($msg, 0, $e);
			$this->notif->add($msg);
		}

		//restore_error_handler(); // we won't handle any more errors
		exit(); // bye bye
	}

	/**
	 * Error handler
	 *
	 * @param int $errno Error code
	 * @param string $errstr Error message
	 */
	public function doHandleError($errno, $errstr, $errfile, $errline)
	{
		$errmask = error_reporting();
		if (!($errmask & $errno)) { // if error has been masked with error_reporting() or suppressed with an @
			return;
		}

		if (!$this->I_give_up) {
			// in these two cases, an new exception is thrown so the catcher from the original code can be triggered
			if (((E_WARNING == $errno) || (E_STRICT == $errno)) && $this->warningsAreFatal) {
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
			if ((E_NOTICE == $errno) && $this->noticesAreFatal) {
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
		}

		/*$errorTypes = array(E_ERROR => 'ERROR', E_WARNING => 'WARNING', E_PARSE => 'PARSING ERROR', E_NOTICE => 'NOTICE', E_CORE_ERROR => 'CORE ERROR', E_CORE_WARNING => 'CORE WARNING', E_COMPILE_ERROR => 'COMPILE ERROR', E_COMPILE_WARNING => 'COMPILE WARNING', E_USER_ERROR => 'USER ERROR', E_USER_WARNING => 'USER WARNING', E_USER_NOTICE => 'USER NOTICE', E_STRICT => 'STRICT NOTICE', E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR');
		$errMsg = empty($errorTypes[$errno]) ? 'Unknown error' : $errorTypes[$errno];*/
		$errMsg = $errstr . " ($errfile line $errline )";

		switch ($errno) {
			case E_WARNING: $level = PHPDS_debug::WARN; break;
			case E_NOTICE: $level = PHPDS_debug::INFO; break;
			default: $level = 0;
		}

		$this->conductor($errMsg, $level);

		if ($this->I_give_up) {
			$this->crumbs[] = $errMsg;
		}


		return true; // to reset internal error
	}

	/**
	 * Sends a message through the various built-in and registered conduits
	 *
	 * @version 2.0
	 *
	 * @date 20120312 (v2.0) (greg) added support for registered conduits
	 *
	 * @param string $domain domain to which the message is related
	 * @param string $msg a text message to handle
	 * @param integer $level (optional) severity level (can be PHPDS_debug::DEBUG, PHPDS_debug::INFO, PHPDS_debug::WARN, PHPDS_debug::ERROR or PHPDS_debug::LOG)
	 * @param string $label (optional) a text label to given to the message
	 *
	 * @return PHPDS_errorHandler itself
	 */
	public function conductor($msg, $level = 0, $label = '', $code = null)
	{
		// first send through registered conduits, as they may report even in production
		foreach($this->conduits as $conduit) {
			$conduit->message('', $msg, $level, $label, $code);
		}
		// then report through built-in conduits, only if not in production
		if ($this->production) return;

        $template = $this->PHPDS_dependance()->PHPDS_template(false);

		if (empty($template)) {
            $template = false;
        }

		$emsg = empty($label) ? $msg : "($label) $msg";
        $ajax_error = (PU_isAJAX()) ? true : false;

		switch ($level) {
			case PHPDS_debug::ERROR:
					if ($this->display && !$ajax_error) {
						if (!method_exists($template,'error')) echo $this->message($emsg);
					}

					if ($this->firephp && !headers_sent()) $this->firephp->error($msg, $label);

					if ($this->serverlog) $this->error_log('ERROR', $emsg);
				break;

			case PHPDS_debug::WARN:
					if ($this->display && !$ajax_error) {
						if (method_exists($template,'warning')) echo $template->warning($emsg, 'return');
						else echo $this->message($emsg);
					}

					if ($this->firephp && !headers_sent()) $this->firephp->warn($msg, $label);

					if ($this->serverlog) $this->error_log('WARNING', $emsg);
				break;

			case PHPDS_debug::INFO:

					if ($this->display && !$ajax_error) {
						if (method_exists($template,'notice')) echo $template->notice($emsg, 'return');
						else echo $this->message($emsg);
					}

					if ($this->firephp && !headers_sent()) $this->firephp->info($msg, $label);

					if ($this->serverlog) $this->error_log('NOTICE', $emsg);
				break;

			case PHPDS_debug::DEBUG:
					if ($this->display && !$ajax_error) {
						if (method_exists($template,'debug')) echo $template->debug($emsg, 'return');
						else echo $this->message($emsg);
					}

					if ($this->firephp && !headers_sent()) $this->firephp->log($msg, $label);

					if ($this->serverlog) $this->error_log('DEBUG', $emsg);
				break;

			default:
					if ($this->display && !$ajax_error) {
						if (method_exists($template,'note')) echo $template->note($emsg, 'return');
						else echo $this->message($emsg);
					}

					if ($this->firephp && !headers_sent()) $this->firephp->log($msg, $label);

					if ($this->serverlog) $this->error_log('LOG', $emsg);
				break;
		}

		return $this;
	}


	/**
	 * Cleans a string for outputing on plain text devices (such as log files)
	 *
	 * @param $text		the string to clean
	 * @return $text
	 */
	function textualize($text)
	{
		$text = preg_replace('/[\x00-\x1F]+/', ' ', $text);
		return $text;
	}

	/**
	 * Write data to the error log using Apache flow
	 *
	 * @param $prefix 	A string to add at the beginning
	 * @param $data		An array of strings to output
	 * @return void
	 */
	function error_log($prefix, $data)
	{
		if (is_array($data)) foreach($data as $text) $this->error_log('-', $text);
		else error_log('[ PHPDS ] '.$prefix.': '.$this->textualize($data));
	}

	/**
	 * Converts variable into short text
	 *
	 * @param mixed $arg Variable
	 * @return string
	 */
	public static function getArgument ($arg)
	{
		switch (strtolower(gettype($arg))) {
			case 'string':
				return ('"' . str_replace(array("\n", "\""), array('', '"'), $arg) . '"');
			case 'boolean':
				return (bool) $arg;
			case 'object':
				return 'object(' . get_class($arg) . ')';
			case 'array':
				return 'array[' . count($arg) . ']';
			case 'resource':
				return 'resource(' . get_resource_type($arg) . ')';
			default:
				return var_export($arg, true);
		}
	}

	/**
	 * Quick independent message styling, just to make it look better yea.
	 *
	 * @param string $message
	 * @return string
	 */
	public function message ($message, $trace = '')
	{
		// Simple styled message.
		if (! empty($trace)) $trace = "=>[$trace]";
		return $this->textualize($message)."$trace";
	}


	/**
	 * Display an Exception
	 *
	 * This function will load a predefined template page (in PHP form) in order to warn the user something has gone wrong.
	 *
	 * If an exception is provided, it will be detailed as much as possible ; if not, only a generic message will be displayed
	 *
	 * @date 20100918
	 * @date 20120511 (v1.1) (greg) output is captured in case we want to save it
	 * @date 20120724 (v1.2) (greg) added "probable origin"
	 * @version 1.2
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @return string the whole output
	 *
	 * @param Exception $e (optional) the exception to explain
	 * @param boolean $detailed whether the details should be displayed or replaced y a generic message
	 */
	public function showException(Exception $e = null, $detailed = true)
	{
		// we stop on the first unhandled error
		$this->I_give_up = true;

		if ($this->PHPDS_dependance()->isEmbedded()) return;

		PU_cleanBuffers();

		if (is_a($e, 'Exception')) {
			$lineno = $e->getLine();
			$filepath = $e->getFile();

			$trace = (is_a($e, 'PHPDS_exception')) ? $e->getExtendedTrace() : $e->getTrace();
			$ignore = (is_a($e, 'PHPDS_exception')) ? $e->getIgnoreLines() : -1;

			$filefragment = PHPDS_backtrace::fetchCodeFragment($filepath, $lineno);
			if (isset($trace[$ignore])) {
				$frame = $trace[$ignore];
				$framefragment = PHPDS_backtrace::fetchCodeFragment($frame['file'], $frame['line']);
			} else {
				$ignore = -1;
			}

			$message = $e->getMessage();
			$code = $e->getCode();
			$extendedMessage = (is_a($e, 'PHPDS_exception')) ? $e->getExtendedMessage() : '';
			$config = $this->configuration;
			if (!empty($config)) {
				if (isset($config['config_files_used']))
					$conf['used'] = PU_dumpArray($config['config_files_used']);

				if (isset($config['config_files_missing']))
					$conf['missing'] = PU_dumpArray($config['config_files_missing']);
			}
			$bt = PHPDS_backtrace::asHTML($ignore, $trace);
		} else {
			$message = "Unknown exception...";
			$code = null;
		}

		// now use the theme's error page to format the actual display
		$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
		// Need this for absolute URL configuration to be sef safe.
		$aurl = $protocol . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['PHP_SELF']);

		ob_start();
		// Load error page: $e is the handled exception
		require BASEPATH.'themes/default/error.php';
		$output = ob_get_clean();

		if (!empty($this->crumbs)) {
			$output = str_replace('<crumbs/>', implode("\n", $this->crumbs), $output);
		}

		if (PU_isAJAX()) {
			// If the error occurred during an AJAX request, we'll send back a lightweight ouput
			$message = $this->display ? "$message - file $filepath line $lineno" : 'Error Concealed - Disabled in config';
			PU_silentHeader('Status: 500 '.$message);
			PU_silentHeader('HTTP/1.1 500 '.$message);
			print $message;
		} else {
			// for a regular request, we present a nicely formatted html page; if provided, an extended description of the error is displayed
			if ($detailed) {
				echo $output;
			} else {
				$message = '';
				require BASEPATH.'themes/default/error.php'; // $message being empty, only a genetic message is output
			}
		}

		return $output;
	}
}

/**
 * Generate a pretty (formatted to be read) backtrace, skipping the first lines if asked
 *
 * @param $ignore				integer, number of frames to skip (optional, defaults to 0)
 * @param $backtrace		the backtrace (optional)
 * @return string
 */
class PHPDS_backtrace
{

	/**
	 * Returns a formatted string with the last line of the backtrace
	 *
	 * @see http://php.net/manual/en/function.debug-backtrace.php
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param array $backtrace (optional) a backtrace array, like debug_backtrace() gives
	 * @return string
	 */
	public static function lastLine($backtrace = null)
	{
		if (empty($backtrace)) $backtrace = debug_backtrace();

		$b = $backtrace[1];
		$result = 'at line '.$b['line'].' of file "'.$b['file'].'"';
		//if ($b['function']) $result .= ' in function "'.$b['function'].'"';

		return $result;
	}

	/**
	 * Returns a text-only backtrace, suitable for text-only supports (like logfiles)
	 *
	 * @see http://php.net/manual/en/function.debug-backtrace.php
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param integer $ignore number of lines to ignore at the beginning of the backtrace (TODO not implemented)
	 * @param array $backtrace (optional) a backtrace array, like debug_backtrace() gives
	 * @return string
	 */
	public static function asText($ignore = 0, $backtrace = null)
	{
		if (empty($backtrace)) $backtrace = debug_backtrace();

		$ignore = intval($ignore);

		$trace = '';
		foreach ($backtrace as $v) {
			if (empty($v['file'])) $v['file'] = '';
			if (empty($v['line'])) $v['line'] = '';
			$v['file'] = preg_replace('!^'.$_SERVER['DOCUMENT_ROOT'].'!', '' ,$v['file']);
			$trace .= $v['file']."\t".$v['line']."\t";
			if (isset($v['class'])) {
					$trace .= $v['class'].'::'.$v['function'].'(';
					if (isset($v['args'])) {
							$errRow[] = $v['args'];
							$separator = '';
							foreach($v['args'] as $arg ) {
									$trace .= $separator.PHPDS_errorHandler::getArgument($arg);
									$separator = ', ';
							}
					}
					$trace .= ')';
			} elseif (isset($v['function'])) {
					$trace .= $v['function'].'(';
					$errRow[] = $v['function'];
					if (!empty($v['args'])) {
							$errRow[] = $v['args'];
							$separator = '';
							foreach($v['args'] as $arg ) {
									$trace .= $separator.PHPDS_errorHandler::getArgument($arg);
									$separator = ', ';
							}
					}
					$trace .= ')';
			}
			$trace .= "\n";
		}

		return $trace;
	}

	public function asArray($ignore = 0, $backtrace = null)
	{
		// TODO
	}

	/**
	 * Returns a html backtrace, suitable for displaying in a browser
	 *
	 * TODO: link to online API documentation
	 *
	 * @see http://php.net/manual/en/function.debug-backtrace.php
	 * @version 1.1
	 * @date 20120724 (v1.1) (greg) $ignore is actually a marker for a frame to highlight
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param integer $ignore number of a stack frame to highlight
	 * @param array $backtrace (optional) a backtrace array, like debug_backtrace() gives
	 * @return string
	 */
	public static function asHTML($ignore = -1, $backtrace = null)
	{
		if (empty($backtrace)) $backtrace = debug_backtrace();

		$ignore = intval($ignore);

		$internals = get_defined_functions();
		$internals = $internals['internal'];

		$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
		// Need this for absolute URL configuration to be sef safe.
		$aurl = $protocol . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['PHP_SELF']);

		$trace = '';
        $i=0;
		foreach ($backtrace as $v) {
			//if ($ignore-- > 0) continue;
            $i++;
            $class_collapsbody = "accordionbody" . $i;
			$ignore--;

			$class = (0 == $ignore) ? 'active' : '';

			$trace .= '<tr class="'.$class.'">';

			if (empty($v['file'])) $v['file'] = '';
			if (empty($v['line'])) $v['line'] = '';
			$filepath = preg_replace('!^'.$_SERVER['DOCUMENT_ROOT'].'/!', '<span class="bt-line-number">...</span>' ,$v['file']);

			$trace .= '<td>'.$filepath.'</td><td>'.$v['line'].'</td><td>';

			if (isset($v['class'])) {
				$fct = $v['class'].'::'.$v['function'];
				$call = $fct.'(';
				if (isset($v['args'])) {
						$errRow[] = $v['args'];
						$separator = '';
						foreach($v['args'] as $arg ) {
								$call .= $separator.PHPDS_errorHandler::getArgument($arg);
								$separator = ', ';
						}
				}
				$call .= ')';
				$call = PHPDS_backtrace::highlightString(preg_replace("/,/", ", ", $call));
				if (substr($v['class'], 0, 5) == 'PHPDS') {
					// $call = '<a href="http://doc.phpdevshell.org/PHPDevShell/'.$v['class'].'.html#'.$v['function'].'" target="_blank"><img src="' . $aurl . '/themes/default/images/icons-16/book-question.png" /></a>&nbsp;'.$call;
				}
				$trace .= $call;
			} elseif (isset($v['function'])) {
				$fct = $v['function'];
				$call = $fct.'(';
				$errRow[] = $v['function'];
				if (!empty($v['args'])) {
						$errRow[] = $v['args'];
						$separator = '';
						foreach($v['args'] as $arg) {
								$call .= $separator . PHPDS_errorHandler::getArgument($arg);
								$separator = ', ';
						}
				}
				$call .= ')';
				$call = PHPDS_backtrace::highlightString(preg_replace("/,/", ", ", $call));
				/*if (!empty($internals[$fct]))*/ $call = '<a href="http://www.php.net/manual-lookup.php?lang=en&pattern='.urlencode($fct).'" target="_blank"><img src="' . $aurl . '/themes/default/images/icons-16/book-question.png" /></a>&nbsp;' . $call;
                $trace .= $call;

			}
            $backtrace__ = PHPDS_backtrace::fetchCodeFragment($v['file'], $v['line']);
			$trace .= '</td><td><button type="button" class="btn" data-toggle="collapse" data-target="#' . $class_collapsbody . '"><i class="icon-eye-open"></i></button></td></tr>';
			$trace .= '<tr class="'.$class.'">';
			$trace .= <<<HTML
                    <td colspan="4">
                        <div id="{$class_collapsbody}" class="accordion-body collapse">
                            <pre>{$backtrace__}</pre>
                        </div>
                    </td>
                </tr>
HTML;
		}

		return $trace;
	}

	/**
	 * Format a html output of an code fragment (seven lines before and after) around the give line of the given source file
	 *
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param string $filepath path to the source file
	 * @param integer $lineno line number of the interesting line
	 * @return string html formated string
	 */
	public static function fetchCodeFragment($filepath, $lineno)
	{
		if (!empty($filepath) && file_exists($filepath)) {
			$filecontent = file($filepath);
			$start = max($lineno - 7, 0);
			$end = min($lineno + 7, count($filecontent));
			$line = '';

			$fragment = '';
			for($loop = $start; $loop < $end; $loop++) {
				if (!empty($filecontent[$loop])) {
					$line = $filecontent[$loop];
					$line = preg_replace('#\n$#', '', $line);
					$line = PHPDS_backtrace::highlightString($line, $loop + 1);
				}
				if ($loop == $lineno - 1) $line = '<span class="highlight-error">' . $line . '</span>';

				$fragment .= $line . "\n";
			}
			return $fragment;
		} else return null;
	}

	/**
	 * Format the given code string as pretty html
	 *
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param string $string the code string to format
	 * @param integer $lineno (optional) a line number to prefix
	 * @return string
	 */
	public static function highlightString($string, $lineno = null)
	{
		if ($lineno) $string = '<span class="bt-line-number">' . $lineno . '.&nbsp;</span>' . "<code class=\"prettyprint code-error-line language-php\">" . htmlentities($string) . "</code>";
		return $string;
	}

    /**
     * Cleans up php info to an appropriate state.
     *
     * @return string
     */
    public static function phpInfo()
    {
        ob_start();
        phpinfo(INFO_VARIABLES + INFO_CONFIGURATION + INFO_ENVIRONMENT);
        $html = ob_get_contents();
        ob_end_clean();

        // Delete styles from output
        $html = preg_replace('#(\n?<style[^>]*?>.*?</style[^>]*?>)|(\n?<style[^>]*?/>)#is', '', $html);
        $html = preg_replace('#(\n?<head[^>]*?>.*?</head[^>]*?>)|(\n?<head[^>]*?/>)#is', '', $html);
        $html = preg_replace('/,/', ', ', $html);
        $html = preg_replace('/::/', ':: ', $html);
        $html = preg_replace('/width=\"600\"/', '', $html);
        $html = preg_replace('/<table/', '<table class="table table-bordered"', $html);
        $html = preg_replace('/\<h1/', '<h3', $html);
        $html = preg_replace('/\<\/h1\>/', '</h3>', $html);
        $html = preg_replace('/\<h2\>/', '<h4>', $html);
        $html = preg_replace('/\<\/h2\>/', '</h4>', $html);
        // Delete DOCTYPE from output
        $html = preg_replace('/<!DOCTYPE html PUBLIC.*?>/is', '', $html);
        // Delete body and html tags
        $html = preg_replace('/<html.*?>.*?<body.*?>/is', '', $html);
        $html = preg_replace('/<\/body><\/html>/is', '', $html);

        return $html;
    }
}



















