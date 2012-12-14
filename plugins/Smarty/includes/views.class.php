<?php

/**
 * Contains methods to handle templates.
 * @author Jason Schoeman
 */
class views extends PHPDS_dependant
{
	/**
	 * Compiling directory.
	 *
	 * @var string
	 */
	public $compile_dir = 'default';
	/**
	 * Cache directory.
	 *
	 * @var string
	 */
	public $cache_dir = 'default';
	/**
	 * Config directory.
	 *
	 * @var string
	 */
	public $config_dir = 'default';
	/**
	 * In seconds, how long should should a page be cached for.
	 *
	 * @var string
	 */
	public $cache_lifetime = 'default';
	/**
	 * Containst smarty object.
	 *
	 * @var object
	 */
	public $view;

	/**
	 * Construct required files.
	 *
	 */
	public function construct()
	{
		$configuration = $this->configuration;
		// Get Smarty.
		if (!is_object($this->template->view)) {
			require_once ('plugins/Smarty/resources/Smarty.class.php');
			$this->view = new Smarty();
		} else {
			$this->view = $this->template->view;
		}
		//Assign to parent class.
		$this->parent = $this->view;
		if ($this->compile_dir == 'default')
				$this->view->compile_dir = $configuration['absolute_path'] . $configuration['compile_path'];
		if ($this->cache_dir == 'default')
				$this->view->cache_dir = $configuration['absolute_path'] . $configuration['cache_path'];
		if ($this->config_dir == 'default')
				$this->view->config_dir = $configuration['absolute_path'] . 'config';
		// Is system in production.
		if ($configuration['production'] == false) {
			if ($configuration['force_views_compile'] == true)
				$this->view->force_compile = true;
		}
	}

	/**
	 * When called, it will aggresively cache view.
	 */
	public function cachePage()
	{
		$configuration = $this->configuration;
		// Remember setting must also be inabled in general settings ui.
		// Do aggresive static view caching.
		if ($configuration['views_cache'] == true) {
			if ($this->cache_lifetime == 'default')
				$this->view->cache_lifetime = $configuration['views_cache_lifetime'];
			$this->view->setCaching(true);
		}
	}

	/**
	 * Loads the default or custom template (tpl) file and prints it out.
	 * Enter the template file for appropriate script here.
	 *
	 * @param string The name of the template to be loaded
	 * @param string If another plugin is to be used in the directory.
	 */
	public function display($load_view=false, $plugin=false)
	{
		$this->show($load_view, $plugin);
	}

	/**
	 * Sets Smarty variables to be passed to it.
	 * Alias of assign.
	 *
	 * @param mixed $var
	 * @param mixed $value
	 */
	public function set($var, $value)
	{
		$this->view->assign($var, $value);
	}

	/**
	 * Sets Smarty variables to be passed to it.
	 * Alias of assign.
	 *
	 * @param mixed $var
	 * @param mixed $value
	 */
	public function assign($var, $value)
	{
		$this->view->assign($var, $value);
	}

	/**
	 * Loads the default or custom template (tpl) file and returns results.
	 * Enter the template file for appropriate script here.
	 *
	 * @param string The name of the template to be loaded
	 * @param string If another plugin is to be used in the directory.
	 */
	public function fetch($var, $value)
	{
		return $this->doFetch($var, $value);
	}

	/**
	 * Loads the default or custom template (tpl) file and prints it out.
	 * Enter the template file for appropriate script here.
	 *
	 * @param string The name of the template to be loaded
	 * @param string If another plugin is to be used in the directory.
	 */
	public function show($load_view=false, $plugin=false)
	{
		// Get correct tpl file.
		$tpl_dir = $this->getTpl($load_view, $plugin);
		if (empty($tpl_dir)) return false;
		// Execute template else just skip it.
		$this->view->display($tpl_dir);
	}

	/**
	 * Loads the default or custom template (tpl) file and returns results.
	 * Enter the template file for appropriate script here.
	 *
	 * @param string The name of the template to be loaded
	 * @param string If another plugin is to be used in the directory.
	 */
	public function doFetch($load_view=false, $plugin=false)
	{
		// Get correct tpl file.
		$tpl_dir = $this->getTpl($load_view, $plugin);
		if (empty($tpl_dir)) return false;
		// Execute template else just skip it.
		return $this->view->fetch($tpl_dir);
	}

	/**
	 * Gets the correct location of a tpl file.
	 *
	 * @param string $load_view
	 * @param string $plugin_override If another plugin is to be used in the directory.
	 */
	public function getTpl($load_view=false, $plugin_override=false)
	{
		return $this->template->getTpl($load_view, $plugin_override);
	}
}