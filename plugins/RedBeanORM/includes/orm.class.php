<?php

/**
 * RedBeanPHP ORM plugin.
 *
 * @author Jason Schoeman
 */
class orm extends PHPDS_dependant
{
	public $dsn;

	public function construct()
	{
		$configuration = $this->configuration;

		// Get RedBeanPHP.
		require_once ('plugins/RedBeanORM/resources/rb.php');

		// Setup ReadBeanPHP.
		if (empty($this->dsn)) {
			$db_settings = PU_GetDBSettings($configuration);
			R::setup("mysql:host={$db_settings['host']};dbname={$db_settings['database']}", $db_settings['username'], $db_settings['password'], $configuration['production']);
		} else {
			R::setup($this->dsn);
		}
	}
}