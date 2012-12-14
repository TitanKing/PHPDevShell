<?php

class PHPDS_view extends PHPDS_dependant
{
	/**
	 * Contains the current active theme.
	 *
	 * @var string
	 */
	public $theme;

	/**
	 * Constructor
	 *
	 * @return parent
	 */
	public function construct()
	{
		$this->theme = $this->core->activeTemplate();
		return parent::construct();
	}
	/**
	 * Looks up and returns data assigned to it in controller with $this->set();
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get($name)
	{
		if (! empty($this->core->toView->{$name}))
			return $this->core->toView->{$name};
		else
			return $this->core->toView[$name];
	}

	/**
	 * Main execution point for class view.
	 * Will execute automatically.
	 */
	public function run()
	{
		$this->execute();
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
}
