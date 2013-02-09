<?php

/**
 * Admin: Information about server.
 * @author Jason Schoeman
 * @return string
 */
class Admin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Header information
		$this->template->heading(__('System Information'));

		// Get server variables.
		$php_uname = php_uname();
		$php_os = PHP_OS;
		$mysql_get_server_info = mysql_get_server_info();
		$mysql_get_host_info = mysql_get_host_info();
		$mysql_get_client_info = mysql_get_client_info();
		$mysql_client_encoding = mysql_client_encoding();
		$status = explode('  ', mysql_stat());
		if (function_exists('apache_get_version')) {
			$apache_get_version = apache_get_version();
		} else {
			$apache_get_version = __('Unknown');
		}
		if (function_exists('apache_get_modules')) {
			$apache_modules = apache_get_modules();
		} else {
			$apache_modules = __('Unknown');
		}
		$list_apache_modules = false;
		$list_php_extensions = false;
		$phpversion = phpversion();
		$php_loaded_extensions = get_loaded_extensions();
		// Do some basic checks. //////////////////////////////////////////////////////////
		if ($this->db->selectQuick('_db_core_plugin_activation', 'version', 'plugin_folder', 'AdminTools') == $this->configuration['phpdevshell_db_version']) {
			$this->template->ok(__('DATABASE : Your database is up to date.'), false, false);
		} else {
			$this->template->warning(__('DATABASE : Your database version seems to be out dated please read the UPGRADE instructions before attempting to upgrade through the GUI. Upgrade instructions may differ from version to version.'));
		}
		// Check if system down bypass is activated in config.
		if ($this->configuration['system_down_bypass'] == true)
				$this->template->warning(__('System down bypass is set true in configuration.php, you should set this to false.'));
		// Check if firePHP is running.
		if (!empty($this->configuration['debug']['firePHP']))
				$this->template->notice(__('Please be aware, FirePHP is currently switched on, this is a security risk.'));
		///////////////////////////////////////////////////////////////////////////////////


		// Load views.
		$view = $this->factory('views');

		$config = PU_dumpArray((array)$this->configuration, 'Configuration array');
		$view->set('CONFIG', $config);

		// Set Values.
		$view->set('phpdevshell_version', $this->configuration['phpdevshell_version']);
		$view->set('php_uname', $php_uname);
		$view->set('mysql_get_server_info', $mysql_get_server_info);
		$view->set('mysql_get_host_info', $mysql_get_host_info);
		$view->set('mysql_get_client_info', $mysql_get_client_info);
		$view->set('mysql_client_encoding', $mysql_client_encoding);
		$view->set('status', $status);
		$view->set('apache_get_version', $apache_get_version);
		$view->set('apache_modules', $apache_modules);
		$view->set('phpversion', $phpversion);
		$view->set('php_loaded_extensions', $php_loaded_extensions);

		// Output Template.
		$view->show();
	}
}

return 'Admin';