<?php

/**
 * This is a blueprint for a connector, ie an object which handles the basic I/O to a database.
 * Its main use it to add a layer of exception throwing to mysql functions
 *
 * All of these methods are based on php-mysql interface function of the same name
 *
 * @author Greg
 *
 */

interface iPHPDS_dbConnector
{
	public function free();
	public function connect();
	public function query($sql);
	public function protect($param);
	public function fetchAssoc();
	public function seek($row_number);
	public function numrows();
	public function affectedRows();
	public function lastId();
	public function rowResults();
	public function startTransaction();
	public function endTransaction($commit = true);
}

require_once 'PHPDS_legacyConnector.class.php';
require_once 'PHPDS_query.class.php';
/**
 * This is a new version of one the Big5: the db class
 *
 * This new version supports connectors and queries class and should be compatible with the old one
 *
 * @version		1.0
 * @date				20100219
 * @author 		greg
 *
 */
class PHPDS_db extends PHPDS_dependant
{
	/**
	 * Contains servers name where PHPDevShell runs on.
	 *
	 * @var string
	 */
	public $server;
	/**
	 * Contains database user name where PHPDevShell runs on.
	 *
	 * @var string
	 */
	public $dbUsername;
	/**
	 * Contains database user password where PHPDevShell runs on.
	 *
	 * @var string
	 */
	public $dbPassword;
	/**
	 * Contains database name where PHPDevShell runs on.
	 *
	 * @var string
	 */
	public $dbName;
	/**
	 * Contains connection data.
	 *
	 * @var object
	 */
	public $connection;
	/**
	 * Memcache object.
	 *
	 * @var object
	 */
	public $memcache;
	/**
	 * Array for log data to be written.
	 *
	 * @var string
	 */
	public $logArray;
	/**
	 * Count amount of queries used by the system.
	 * Currently it is on -2, we are not counting Start and End transaction.
	 *
	 * @var integer
	 */
	public $countQueries = -2;
	/**
	 * Contains array of all the plugins installed.
	 *
	 * @var array
	 */
	public $pluginsInstalled;
	/**
	 * Contains variable of logo.
	 *
	 * @var array
	 */
	public $pluginLogo;
	/**
	 * Essential settings array.
	 *
	 * @var array
	 */
	public $essentialSettings;
	/**
	 * Display erroneous sql statements
	 *
	 * @var boolean
	 */
	public $displaySqlInError = false;

	/**
	 * Stores results
	 *
	 * @var string
	 */
	public $result;

	/**
	 * Database connector.
	 * @var iPHPDS_dbConnector
	 */
	protected $connector;

	/**
	 * List of lternates connectors (i.e., not the default, primary connector)
	 * @var array of iPHPDS_dbConnector
	 */
	protected $connectors;

	/**
	 * For backward compatibility: a default query instance used for sending sql queries directly
	 * @var PHPDS_query
	 */
	protected $defaultQuery;

	/**
	 * Constructor.
	 *
	 */
	public function __construct($dependance)
	{
		$this->PHPDS_dependance($dependance);
		$this->connector = new PHPDS_legacyConnector($dependance);
	}

	/**
	 * Force database connection.
	 * Jason: Note this is used in core initiation to fix some dependent functions like mysql_real_escape_string requiring a DB connection.
	 * Only dbConnecter->query initiated the connection which was unfair to dependent functions.
	 *
	 * Don: The connector will apply the database settings itself since each connector may have different settings. For backwards
	 * compatibility the connector will set the database properties for the main db instance as well. In the feature the db
	 * class won't have public properties for the database settings such as $db->server, $db->dbName, etc. since each connector may
	 * have different settings.
	 *
	 * @date 20120308
	 */
	public function connect($db_config = '')
	{
		try {
			$this->connector->connect($db_config);
		} catch (Exception $e) {
			throw $this->factory('PHPDS_databaseException', '', 0, $e);
		}
	}

	/**
	 * Handle access to the alternate connector list
	 *
	 * Give a class name, the connector will be instantiated if needed
	 *
	 * @param string $connector, class name of the connector
	 * @return iPHPDS_dbConnector
	 */
	public function connector($connector = null)
	{
		if (is_null($connector)) {
			return $this->connector;
		}
		if (is_string($connector) && class_exists($connector)) {
			if (isset($this->connectors[$connector])) {
				return $this->connectors[$connector];
			} else {
				$new = $this->factory($connector);
				if (is_a($new, 'iPHPDS_dbConnector')) {
					$this->connectors[$connector] = $new;
					return $new;
				}
			}
		}
		throw new PHPDS_exception('Unable to factor such a connector.');
	}

	/**
	 * Compatibility
	 * Do direct sql query without models.
	 *
	 * @date 20110512
	 * @param string
	 * @version	1.0
	 * @author jason
	 * @return mixed
	 */
	public function newQuery($query)
	{
		try {
			if (empty($this->defaultQuery))
					$this->defaultQuery = $this->makeQuery('PHPDS_query');
			$this->defaultQuery->sql($query);
			return $this->defaultQuery->query();
		} catch (Exception $e) {
			if (empty($this->defaultQuery))
				$msg = 'Unable to create default query: ' . $e->getMessage();
			else
				$msg = 'While running default query:<br /><pre>' . $this->defaultQuery->sql() . '</pre>' . $e->getMessage();
			throw new PHPDS_databaseException($msg, 0, $e);
		}
	}

		/**
		 * Alias to newQuery
		 *
		 * @param string $query
		 * @return mixed
		 */
		public function sqlQuery($query)
		{
			return $this->newQuery($query);
		}

	/**
	 * Locates the query class of the given name, loads it, instantiate it, send the query to the DB, and return the result
	 *
	 * @date 20100219
	 * @version 1.2
	 * @date 20100922 (1.2) (greg) now use invokeQueryWithArgs
	 * @author greg
	 * @param $query_name string, the name of the query class (descendant of PHPDS_query)
	 * @return array (usually), the result data of the query
	 */
	public function invokeQuery($query_name) // actually more parameters can be given
	{
		$params = func_get_args();
		array_shift($params); // first parameter of this function is $query_name
		return $this->invokeQueryWith($query_name, $params);
	}

	/**
	 * Locates the query class of the given name, loads it, instantiate it, send the query to the DB, and return the result
	 *
	 * @date 20100922 (1.0) (greg) added
	 * @version 1.0
	 * @author greg
	 * @param $query_name string, the name of the query class (descendant of PHPDS_query)
	 * @param $args array of parameters
	 * @return array (usually), the result data of the query
	 */
	public function invokeQueryWith($query_name, $params)
	{
		$query = $this->makeQuery($query_name);
		if (!is_a($query, 'PHPDS_query'))
				throw new PHPDS_databaseException('Error invoking query');
		return $query->invoke($params);
	}

	/**
	 * Locates the query class of the given name, loads it, intantiate it, and returns the query object
	 *
	 * @date 20100219 (greg) created
	 * @date 20110812 (v1.2) (greg) doesn't provide the query with the default connector anymore (let the query requests it at contruct time)
	 * @version 1.2
	 * @author greg
	 * @param $query_name string, the name of the query class (descendant of PHPDS_query)
	 * @return PHPDS_query descendant, the query object
	 */
	public function makeQuery($query_name)
	{
		$configuration = $this->configuration;
		$navigation = $this->navigation;
		$o = null;
		$good = (class_exists($query_name, false));
		if (!$good) {
			$phpds = $this->PHPDS_dependance();
			list($plugin, $node_link) = $navigation->nodePath();
			$query_file = 'models/' . $node_link;
			$query_file = preg_replace('/\.php$/', '.query.php', $query_file);
			$query_file = $configuration['absolute_path'] . 'plugins/' . $plugin . '/' . $query_file;
			$good = $phpds->sneakClass($query_name, $query_file);
			// Execute class file.
			if (!$good) {
				$node = $configuration['m'];
				if (!empty($navigation->navigation[$node])) {
					$plugin = $navigation->navigation[$node]['plugin'];
					$query_file = $configuration['absolute_path'] . 'plugins/' . $plugin . '/models/plugin.query.php';
					$good = $phpds->sneakClass($query_name, $query_file);
				}
			}
		}
		// All is good create class.
		if ($good) {
			$o = $this->factory($query_name);
			if (is_a($o, 'PHPDS_query')) {
				return $o;
			}
			throw new PHPDS_Exception('Error factoring query: object is not a PHPDS_query, maybe you mistyped the class superclass.');
		}
		throw new PHPDS_Exception('Error making query: unable to find class "' . $query_name . '".');
	}

	/**
	 * Set the starting point for a SQL transaction
	 *
	 * You should call end_transaction(true) for the queries to actually occur
	 */
	public function startTransaction()
	{
		return $this->connector->startTransaction();
	}

	/**
	 * Commits database transactions.
	 *
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function endTransaction()
	{
		$configuration = $this->configuration;
		// Should we commit or rollback?
		if (($configuration['demo_mode'] == true)) {
			if ($configuration['user_role'] != $configuration['root_role']) {
				// Roll back all database changes.
				return $this->connector->endTransaction(false);
			} else {
				// Commit all database changes.
				return $this->connector->endTransaction(true);
			}
		} else if ($configuration['demo_mode'] == false) {
			// Commit all database changes.
			return $this->connector->endTransaction(true);
		}
	}

	/**
	 * Protect a single string from possible hacker (i.e. escape possible harmfull chars)
	 *
	 * Actually deleguate the action to the connector
	 *
	 * @date 20100329
	 * @version 1.1
	 * @author greg
	 * @date 20111018 (v1.1) (greg) $param can now be an array
	 * @param $param mixed, the parameter to espace
	 * @return string, the escaped string/array
	 * @see includes/PHPDS_db_connector#protect()
	 */
	public function protect($param)
	{
		if (is_array($param)) {
			return $this->protectArray($param);
		} else {
			return $this->connector->protect($param);
		}
	}

	/**
	 * Protect a array of strings from possible hacker (i.e. escape possible harmfull chars)
	 * (this has been moved from PHPDS_query)
	 * @version 1.1
	 * @date 20111010 (v1.1) (greg) added "quote" parameter
	 * @author  greg
	 * @param $a    array, the strings to protect
	 * @param $quote string, the quotes to add to each non-numerical scalar value
	 * @return array, the same string but safe
	 */
	public function protectArray(array $a, $quote = '')
	{
		foreach($a as $index => $value) {
			$v = null;
			if (is_array($value)) {
				$v = $this->protectArray($value);
			}
			if (is_scalar($value)) {
				$v = $this->connector->protect($value);
				if (!is_numeric($v) && $quote) {
					$v = $quote.$v.$quote;
				}
			}
			if (!empty($v)) {
				$a[$index] = $v;
			}
		}

		return $a;
	}

	/**
	 * Will convert object configuration into array for parsing.
	 *
	 */
	public function debugConfig()
	{
		foreach ($this->configuration as $key => $extended_config) {
			$converted_config[$key] = $extended_config;
		}
		$this->_log($converted_config);
	}

	/**
	 * Checks if a database table exists.
	 *
	 * @param string $table
	 * @return boolean
	 */
	public function tableExist($table)
	{
		return $this->invokeQuery('DB_tableExistQuery', $table);
	}

	/**
	 * Simple method to count number of rows in a table.
	 *
	 * @param string $table_name
	 * @param string $column
	 * @return integer
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function countRows($table_name, $column = false)
	{
		// Check what to count.
		if (empty($column)) $column = '*';
		return $this->invokeQuery('DB_countRowsQuery', $column, $table_name);
	}

	/**
	 * This method logs error and success entries to the database.
	 *
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 *
	 * @version 1.0.1 Changed mysql_escape_string() to mysql_real_escape_string() [see http://www.php.net/manual/en/function.mysql-escape-string.php ]
	 */
	public function logThis()
	{
		$this->invokeQuery('DB_logThisQuery', $this->logArray);
	}

	/**
	 * This function gets all role id's for a given user id, while returning a string divided by ',' character or an array with ids.
	 * To pull multiple user roles, provide a string for $user_ids like so: '2,5,10,19'.
	 *
	 * @deprecated
	 * @param string $user_id
	 * @param boolean $return_array
	 * @return mixed If $return_array = false a comma delimited string will be returned, else an array.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function getRoles($user_id = false, $return_array = false)
	{
		return $this->user->getRoles($user_id, $return_array);
	}

	/**
	 * This function gets all group id's for given user ids, while returning a string divided by ',' character or an array with ids.
	 * To pull multiple user groups, provide a string for $user_ids like so : '2,5,10,19'.
	 *
	 * @deprecated
	 * @param string $user_id Leave this field empty if you want skip if user is root.
	 * @param boolean $return_array
	 * @param string $alias_only If you would like only items of a certain alias to be called.
	 * @return mixed If $return_array = false a comma delimited string will be returned, else an array.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function getGroups($user_id = false, $return_array = false, $alias_only = false)
	{
		return $this->user->getGroups($user_id, $return_array, $alias_only);
	}

	/**
	 * Simple check to see if a certain role exists.
	 *
	 * @deprecated
	 * @param integer $role_id
	 * @return boolean
	 */
	public function roleExist($role_id)
	{
		return $this->user->roleExist($role_id);
	}

	/**
	 * Simple check to see if a certain group exists.
	 *
	 * @deprecated
	 * @param integer $group_id
	 * @return boolean
	 */
	public function groupExist($group_id)
	{
		return $this->user->groupExist($group_id);
	}

	/**
	 * Check if user belongs to given role. Returns true if user belongs to user role.
	 *
	 * @deprecated
	 * @param integer $user_id
	 * @param integer $user_role
	 * @return boolean Returns true if user belongs to user role.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function belongsToRole($user_id = false, $user_role)
	{
		return $this->user->belongsToRole($user_id, $user_role);
	}

	/**
	 * Check if user belongs to given group. Returns true if user belongs to user group.
	 *
	 * @deprecated
	 * @param integer $user_id
	 * @param integer $user_group
	 * @return boolean Returns true if user belongs to user group.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function belongsToGroup($user_id = false, $user_group)
	{
		return $this->user->belongsToGroup($user_id, $user_group);
	}

	/**
	 * Creates a query to extend a role query, it will return false if user is root so everything can get listed.
	 * This is meant to be used inside an existing role query.
	 *
	 * @deprecated
	 * @param string $query_request Normal query to be returned if user is not a root user.
	 * @param string $query_root_request If you want a query to be processed for a root user seperately.
	 * @return mixed
	 */
	public function setRoleQuery($query_request, $query_root_request = false)
	{
		return $this->user->setRoleQuery($query_request, $query_root_request);
	}

	/**
	 * Creates a query to extend a group query, it will return false if user is root so everything can get listed.
	 * This is meant to be used inside an existing group query.
	 *
	 * @deprecated
	 * @param string $query_request Normal query to be returned if user is not a root user.
	 * @param string $query_root_request If you want a query to be processed for a root user seperately.
	 * @return mixed
	 */
	public function setGroupQuery($query_request, $query_root_request = false)
	{
		return $this->user->setGroupQuery($query_request, $query_root_request);
	}

	/**
	 * Generates a prefix for plugin general settings.
	 *
	 * @param string $custom_prefix
	 * @return string Complete string with prefix.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function settingsPrefix($custom_prefix = false)
	{
		// Create prefix.
		if ($custom_prefix == false) {
			// Get active plugin.
			$active_plugin = $this->core->activePlugin();
			if (!empty($active_plugin)) {
				$prefix = $active_plugin . '_';
			} else {
				$prefix = 'PHPDevShell_';
			}
		} else {
			$prefix = $custom_prefix . '_';
		}
		return $prefix;
	}

	/**
	 * Used to write general plugin settings to the database.
	 * Class will always use plugin name as prefix for settings if no custom prefix is provided.
	 * <code>
	 * // Example:
	 * $db->writeSettings(array('setting_name'=>'value')[,'Example'][,array('setting_name'=>'note')]);
	 * </code>
	 * @param array $write_settings This array should contain settings to write.
	 * @param string $custom_prefix If you would like to have a custom prefix added to your settings.
	 * @param array $notes For adding notes about setting.
	 * @return boolean On success true will be returned.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function writeSettings($write_settings, $custom_prefix = '', $notes = array())
	{
		return $this->invokeQuery('DB_writeSettingsQuery', $write_settings, $custom_prefix, $notes);
	}

	/**
	 * Delete all settings stored by a given plugins name, is used when uninstalling a plugin.
	 *
	 * Example:
	 * <code>
	 * deleteSettings(false, 'SimplePhonebook')
	 * </code>
	 *
	 * @param array $settings_to_delete Use '*' to delete all settings for certain plugin.
	 * @param string $custom_prefix
	 * @return boolean
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function deleteSettings($settings_to_delete = false, $custom_prefix = false)
	{
		return $this->invokeQuery('DB_deleteSettingsQuery', $settings_to_delete, $custom_prefix);
	}

	/**
	 * Loads and returns required settings from database.
	 * Class will always use plugin name as prefix for settings if no custom prefix is provided.
	 *
	 * @param array $settings_required
	 * @param string $prefix This allows you to use a prefix value of your choice to select a setting from another plugin, otherwise PHPDevShell will be used.
	 * @return array An array will be returned containing all the values requested.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function getSettings($settings_required = false, $custom_prefix = false)
	{
		return $this->invokeQuery('DB_getSettingsQuery', $settings_required, $custom_prefix);
	}

	/**
	 * Used to get all essential system settings from the database, preventing multiple queries.
	 *
	 * @return array Contains array with essential settings.
	 */
	public function getEssentialSettings()
	{
		// Pull essential settings and assign it to essential_settings.
		if ($this->cacheEmpty('essential_settings')) {
			$this->essentialSettings = $this->getSettings($this->configuration['preloaded_settings'], 'AdminTools');
			// Write essential settings data to cache.
			$this->cacheWrite('essential_settings', $this->essentialSettings);
		} else {
			$this->essentialSettings = $this->cacheRead('essential_settings');
		}
	}

	/**
	 * Determines whether the specified search string already exists in the specified field within the supplied table.
	 * Optional: Also looks at an id field (typically the primary key of a table) to make sure that the record you are working with
	 * is NOT included in the search.
	 * Usefull when modifying an existing record and you need first to check if another record with the same value doesn't already exist.
	 *
	 * @param string The name of the database table.
	 * @param mixed The array names of the columns in which to look for the search strings, a single value can also be given.
	 * @param mixed In the same order as $search_column_name array, the search strings in array that should not be duplicated, a single value can also be given.
	 * @param string The name of the primary key column name of the record you will be updating.
	 * @param string The value of the primary key of the record you will be updating that should not be included in the search.
	 * @return boolean If TRUE is returned it means the record already exists, FALSE means the record doesn't exist.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function doesRecordExist($table_name, $search_column_names, $search_field_values, $column_name_for_exclusion = false, $exclude_field_value = false)
	{
		return $this->invokeQuery('DB_doesRecordExistQuery', $table_name, $search_column_names, $search_field_values, $column_name_for_exclusion, $exclude_field_value);
	}

	/**
	 * Get a single result from database with minimal effort.
	 *
	 * @param string $from_table_name
	 * @param string $select_column_name
	 * @param string $where_column_name
	 * @param string $is_equal_to_column_value
	 * @return string
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function selectQuick($from_table_name, $select_column_name, $where_column_name, $is_equal_to_column_value)
	{
		return $this->invokeQuery('DB_selectQuickQuery', $select_column_name, $from_table_name, $where_column_name, $is_equal_to_column_value);
	}

	/**
	 * Delete data from the database with minimal effort.
	 *
	 * @param string $from_table_name
	 * @param string $where_column_name
	 * @param string $is_equal_to_column_value
	 * @param string $return_column_value
	 * @return string
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function deleteQuick($from_table_name, $where_column_name, $is_equal_to_column_value, $return_column_value = false)
	{
		return $this->invokeQuery('DB_deleteQuickQuery', $from_table_name, $where_column_name, $is_equal_to_column_value, $return_column_value);
	}

	/**
	 * Writes array of all the installed plugins on the system.
	 * @author Jason Schoeman <titan@phpdevshell.org>
	 */
	public function installedPlugins()
	{
		$this->invokeQuery('DB_installedPluginsQuery');
	}

	/**
	 * Does the connection to the memcache server.
	 * Currently memcache is the primary supported engine.
	 */
	public function connectCacheServer()
	{
		$configuration = $this->configuration;

		// Get cache configuration.
		$conf['cache_refresh_intervals'] = $configuration['cache_refresh_intervals'];

		// Assign configuration arrays.
		if ($configuration['cache_type'] != 'PHPDS_sessionCache') {
			$conf['cache_host'] = $configuration['cache_host'];
			$conf['cache_port'] = $configuration['cache_port'];
			$conf['cache_persistent'] = $configuration['cache_persistent'];
			$conf['cache_weight'] = $configuration['cache_weight'];
			$conf['cache_timeout'] = $configuration['cache_timeout'];
			$conf['cache_retry_interval'] = $configuration['cache_retry_interval'];
			$conf['cache_status'] = $configuration['cache_status'];
		}

		// Load Cache Class.
		require_once 'cache/' . $configuration['cache_type'] . '.inc.php';
		$this->memcache = new $configuration['cache_type'];

		// Check connection type.
		$this->memcache->connectCacheServer($conf);
	}

	/**
	 * Writes new data to cache.
	 * @param string $unique_key
	 * @param mixed $cache_data
	 * @param boolean $compress
	 * @param int $timeout
	 */
	public function cacheWrite($unique_key, $cache_data, $compress=false, $timeout=false)
	{
		// Check caching type.
		$this->memcache->cacheWrite($unique_key, $cache_data, $compress, $timeout);
	}

	/**
	 * Return exising cache result to required item.
	 * @param mixed $unique_key
	 * @return mixed
	 */
	public function cacheRead($unique_key)
	{
		return $this->memcache->cacheRead($unique_key);
	}

	/**
	 * Clear specific or all cache memory.
	 * @param mixed $unique_key
	 */
	public function cacheClear($unique_key = false)
	{
		$this->memcache->cacheClear($unique_key);
	}

	/**
	 * Checks if we have an empty cache container.
	 * @param mixed $unique_key
	 * @return boolean
	 */
	public function cacheEmpty($unique_key)
	{
		return $this->memcache->cacheEmpty($unique_key);
	}

}
