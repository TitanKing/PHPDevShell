<?php

class PHPDS_controller extends PHPDS_dependant
{
	/**
	 * Stored POST information.
	 * @var array
	 */
	protected $_POST;
	/**
	 * Stored GET information.
	 * @var array
	 */
	protected $_GET;

	/**
	 * General construction.
	 *
	 * @return object
	 */
	public function construct()
	{
		unset($_REQUEST['_SESSION']);
		unset($_POST['_SESSION']);
		unset($_GET['_SESSION']);

		$this->_POST = empty($_POST) ? array() : $_POST;
		$this->_GET = empty($_GET) ? array() : $_GET;

		return parent::construct();
	}

	/**
	 * Set data for availability in view class.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value=null)
	{
		if (is_string($name)) {
			if (is_object($value)) {
				$this->core->toView = new stdClass();
				$this->core->toView->{$name} = $value;
			} else {
				$this->core->toView[$name] = $value;
			}
		}
	}

	/**
	 * Return a value from the _POST meta array
	 *
	 * @date 20101016 (v1.0) (greg) added
	 * @version 1.0
	 * @author greg
	 *
	 * @param string|null $key the name of the post variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the post variable is not set; when returning the entire array, an array can be given here with default values
	 * @param integer $options
	 *
	 * @return scalar|array the content of the post variable or the whole array, possibly with default value(s)
	 */
	public function POST($key = null, $default = null, $options = 0)
	{
		if (!empty($key)) {
			return (isset($this->_POST[$key])) ? $this->_POST[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $this->_POST);
			else return $this->_POST;
		}
	}

	/**
	 * Return a secured (preventing sql injection) value from the security->post meta array
	 *
	 * @date 20120227 (v1.0) (jason) added
	 * @version 1.0
	 * @author jason
	 *
	 * @param string|null $key the name of the post variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the post variable is not set; when returning the entire array, an array can be given here with default values
	 * @param integer $options
	 *
	 * @return scalar|array the content of the post variable or the whole array, possibly with default value(s)
	 */
	public function P($key = null, $default = null, $options = 0)
	{
		if (!empty($key)) {
			return (isset($this->security->post[$key])) ? $this->security->post[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $this->security->post);
			else return $this->security->post;
		}
	}

	/**
	 * Return a value from the _GET meta array
	 *
	 * @date 20101016 (v1.0) (greg) added
	 * @version 1.0
	 * @author greg
	 *
	 * @param string|null $key the name of the get variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the get variable is not set; when returning the entire array, an array can be given here with default values
	 * @param integer $options
	 *
	 * @return scalar|array the content of the get variable or the whole array, possibly with default value(s)
	 */
	public function GET($key = null, $default = null, $options = 0)
	{
		if (!empty($key)) {
			return (isset($this->_GET[$key])) ? $this->_GET[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $this->_GET);
			else return $this->_GET;
		}
	}

	/**
	 * Return a secured (preventing sql injection) value from the security->get meta array
	 *
	 * @date 20120227 (v1.0) (jason) added
	 * @version 1.0
	 * @author jason
	 *
	 * @param string|null $key the name of the get variable to fetch; if null, the entire array is returned
	 * @param mixed|array $default a default value to return when the get variable is not set; when returning the entire array, an array can be given here with default values
	 * @param integer $options
	 *
	 * @return scalar|array the content of the get variable or the whole array, possibly with default value(s)
	 */
	public function G($key = null, $default = null, $options = 0)
	{
		if (!empty($key)) {
			return (isset($this->security->get[$key])) ? $this->security->get[$key] : $default;
		} else {
			if (is_array($default)) return array_merge($default, $this->security->get);
			else return $this->security->get;
		}
	}

	/**
	 * Does security check and runs controller.
	 *
	 * @version 1.2
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @date 20110307 (v1.1) (greg) calls via Ajax don't exit anymore but empty the template output instead
	 * @date 20120717 (v1.2) (greg) non-ajax request are handled via runRegular()
	 *
	 * @return mixed
	 */
	public function run()
	{
		(is_object($this->security)) ? $this->security->securityIni() : exit('Access Denied!');

		$result = null;
		if (PU_isAJAX ()) {
			/**
			 * This allows to load a widget/ajax theme controller via ajax without triggering the runAjax.
			 * Now runAjax can still be used within the widget/ajax node type controller.
			 */
			if ($this->core->ajaxType == false || ! empty($this->_GET['widget']) || ! empty($this->_GET['ajax']) || ! empty($this->_GET['lightbox'])) {
				$result = $this->execute();
			} else {
				$result = $this->runAJAX();
			}
		} else {
			$result = $this->runRegular();
		}
		return $result;
	}

	/**
	 * Run a controller when called with ajax
	 *
	 * @version 1.0
	 * @date 20120717 (1.0) (greg) added
	 * @since 3.2.1
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @return mixed
	 */
	public function runRegular()
	{
		$raw_data = $this->execute();

		return $this->handleResult($raw_data);
	}

	/**
	 * Run a controller when called with ajax
	 *
	 * @version 2.0
	 * @date 20120608 (2.0) (greg) added support for PHP5 reflection (ie parameters passed by name)
	 * @since 3.0.5
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @return mixed
	 */
	public function runAJAX()
	{
		// TODO: catch exception and signal back to the ajax caller
		$raw_data = '';
		if (isset($_SERVER["HTTP_X_REMOTE_CALL"])) {
			$f = 'ajax'.$_SERVER["HTTP_X_REMOTE_CALL"];
			if (method_exists($this, $f)) {
				if (class_exists('ReflectionMethod')) {
					$classname = get_class($this);
					$method = new ReflectionMethod($classname, $f);
					$parameter_list = $method->getParameters();
					$parameter_array = array();
					foreach($parameter_list as $parameter) {
						$key = $parameter->getName();
						$parameter_array[] = isset($this->_POST[$key]) ? $this->_POST[$key]: null;
					}
					$raw_data = $method->invokeArgs($this, $parameter_array);
				} else {
					$raw_data = call_user_func_array(array($this, $f), $this->POST());
				}
			} else {
				throw new PHPDS_exception('Ajax call for an unknown method "'.$f.'"');
			}
		} else {
			$raw_data = $this->viaAJAX();
		}

		return $this->handleResult($raw_data);
	}

	/**
	 * Deal with the controller's output
	 *
	 * @version 1.1
	 * @since 3.0.5
	 * @author greg <greg@phpdevshell.org>
	 *
	 * @date 20120717 (v1.1) (greg) now used for both ajax and regular requests
	 *
	 * @param mixed $raw_data
	 * @return mixed
	 */
	public function handleResult($raw_data)
	{
		$core = $this->core;

		$encoded_data = PU_isJSON($raw_data);
		if (false !== $encoded_data) {
			$core->themeFile = '';
			$core->data = $encoded_data;
			return true;
		} else {
			if (is_null($raw_data)) { // deal with it the usual way (normal template)
				return true;
			} else {
				$core->themeFile = '';

				if (false === $raw_data) { //  we consider it's an error
					return false;
				} elseif (true === $raw_data) { // controller handled output
					return true;
				} elseif (is_string($raw_data)) { // bare data, using empty template
					$core->data = $raw_data;
					return true;
				} else {
					throw new PHPDS_exception(sprintf('The return value of controller %d is invalid.', $this->configuration['m']));
				}
			}
		}
	}

	/**
	 * This method is meant to be the entry point of your class. Most checks and cleanup should have been done by the time it's executed
	 *
	 * @return whatever, if you return "false" output will be truncated
	 */
	public function execute()
	{
		// Your code here
	}

	/**
	 * This method is run if your controller is called in an ajax context
	 *
	 * @return mixed, there are 3 cases: "true" (or nothing)  the output will be handled by the template the usual way, "false" it's an error, otherwise the result data will be displayed in an empty template
	 */
	public function viaAJAX()
	{
		// Your code here
	}
}
