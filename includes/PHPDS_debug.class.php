<?php

/**
 * Adds FirePHP support to PHPDevShell.
 *
 * @author Greg
 */
class PHPDS_debug extends PHPDS_dependant
{
	/* levels of verbosity: the higher, the more data will be sent */
	const DEBUG = 4;
	const INFO = 3;
	const WARN = 2;
	const ERROR = 1;
	const LOG = 0;
	protected $enabled = false; /* boolean: is the data to be sent, anyway */
	protected $level = PHPDS_debug::LOG; /* level of verbosity */
	protected $domain; /* string (maybe null): to which semantic domain this instance is related */
	protected $conduits = null;

	/**
	 * @date 20110202 (v1.1) (greg) moved to construct() instead of __cosntruct()
	 * @date 20110216 (v1.1.1)  (greg) fixed a bug with enable
	 * @version 1.1.1
	 * @param $domain the semantic domain of the debug object (to match (or not) the filters are set in configuration)
	 */
	public function construct ($domain = null)
	{
		$this->conduits = $this->errorHandler;
		$this->domain(empty($domain) ? '!' : $domain);

		if ($this->enabled) {
			$configuration = $this->configuration['debug'];
			$this->enabled = !empty($configuration['enable']);
			$this->level = empty($configuration['level']) ? PHPDS_debug::LOG : intval($configuration['level']);
		}

		return true;
	}

	/**
	 * Accessor for the domain field: get (and possibily set) the domain
	 *
	 * The domain is the semantic field of the data sent to this instance ; it's used to filter which data will be actually sent
	 * Note: the object will enable/disabled itself based on the domain debug configuration: the domain MUST be in the array to be active.
	 *
	 * @version 1.1
	 * @author greg
	 *
	 * @date 20110216 (v1.1) (greg) domain filters can now be regex
	 *
	 * @param $domain string (optional) the semantic domain
	 * @return string, the domain (maybe null)
	 */
	public function domain ($domain = null)
	{
		if (! is_null($domain)) {
			if (isset($this->configuration['debug'])) {
				$configuration = $this->configuration['debug'];
				if (isset($configuration['domains'])) {
					if (is_array($configuration['domains'])) {
						foreach($configuration['domains'] as $possible_domain) {
							if (fnmatch($possible_domain, $domain)) {
								$this->enabled = true;
								$this->domain = $domain;
								return $domain;
							}
						}
						$this->enabled = false;
					}
				}
			}
		}
		return $domain;
	}

	/**
	 * Enable or disable the debugger output ; get the current state
	 *
	 * Note: at this time, the debugger has to be enabled at startup
	 *
	 * @param $doit				(optional) enable (true or disable (false)
	 * @return boolean		weither it's currently enabled
	 */
	function enable($doit = null)
	{
		if (!is_null($doit)) $this->enabled = (boolean)$doit;
		return $this->enabled;
	}

	/**
	 * Is this instance sending data?
	 *
	 * @return boolean
	 */
	public function isEnabled()
	{
		return ($this->enabled == true);
	}

	/**
	 * Magic method: shortcut to log($ata)
	 */
	public function __invoke($data, $label = null)
	{
		return $this->log($data);
	}

	/**
	 * Dump the content of a variable to the backends
	 *
	 * @param $data
	 * @param $label
	 */
	public function dump($data, $label = 'data')
	{
		if (!$this->enabled) return;

		if ($this->firephp) $this->firephp->dump($label, $data);
		$this->error_log('DUMP', $data);
	}

	/**
	 * Log the data to the backends with the LOG level (the smallest, most often seen)
	 *
	 * @param $data
	 * @param $label
	 */
	public function log($data, $label = null)
	{
		if (!$this->enabled || ($this->level < PHPDS_debug::LOG)) return;

		if (empty($label)) $label = $this->domain;


		$this->conduits->conductor($data, PHPDS_debug::LOG, $this->domain.': '.$label);
	}
	
	/**
	 * Push Firebug Debug Info
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function debug($data)
	{
		if (!$this->enabled || ($this->level < PHPDS_debug::DEBUG)) return;

		$this->conduits->conductor($data, PHPDS_debug::DEBUG, $this->domain);
	}
	
	/**
	 * Push Firebug Info
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function info($data)
	{
		if (!$this->enabled || ($this->level < PHPDS_debug::INFO)) return;

		$this->conduits->conductor($data, PHPDS_debug::INFO, $this->domain);
	}
	
	/**
	 * Push Firebug Warning
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function warn($data)
	{
		if (!$this->enabled || ($this->level < PHPDS_debug::WARN)) return;

		$this->conduits->conductor($data, PHPDS_debug::WARN, $this->domain);
	}
	
	/**
	 * Push Firebug Warning
	 *
	 * @param mixed $data
	 */
	public function warning($data)
	{
		return $this->warn($data);
	}
	
	/**
	 * Push the given error to the error system
	 * 
	 * @version 1.1
	 * 
	 * @date 20120312 (v1.1) (greg) added param $code
	 * 
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @param mixed $data
	 * @param mixed $code
	 * @return itself
	 */
	public function error($data, $code = null)
	{
		if (!$this->enabled || ($this->level < PHPDS_debug::ERROR)) return;

		$this->conduits->conductor($data, PHPDS_debug::ERROR, $this->domain, $code);
		
		return $this;
	}
	
	/**
	 * FirePHP Process
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function process($data)
	{
		$content = $data['sql'];
		$key = '['.$data['counter'].'] '.$data['key'].' ('.$data['result'].')';

		$this->log($content, $key);
	}

}

?>