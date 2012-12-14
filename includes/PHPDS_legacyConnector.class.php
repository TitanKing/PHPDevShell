<?php

/**
 *
 * NOTE: you're not supposed to deal with connectors any way
 *
 * @author Greg
 *
*/
class PHPDS_legacyConnector extends PHPDS_dependant implements iPHPDS_dbConnector
{
	/**
	 * @var dbDSN string	A string containing the DSN (Data Source Name)
	 */
	protected $dbDSN = "";

	/**
	 * @var dbHost string	A string containing the hostname
	 */
	protected $dbHost = "";

	/**
	 * @var dbName string	A string containing the database name
	 */
	protected $dbName = "";

	/**
	 * @var dbUsername string	A string containing the database username
	 */
	protected $dbUsername = "";

	/**
	 * @var dbPassword string	A string containing the database password
	 */
	protected $dbPassword = "";

	/**
	 * @var dbPrefix string	A string containing the database prefix
	 */
	protected $dbPrefix = "";

	/**
	 * @var dbPersistent string	A string containing the database persistence setting
	 */
	protected $dbPersistent = false;

	/**
	 * @var dbCharset string	A string containing the database connection character set
	 */
	protected $dbCharset = "";

	/**
	 * @var php resource type	the link for the mysql connection (as returned by mysql_connect)
	 */
	private $link;

	/**
	 * @var php resource type	the result resource of a query (as returned by mysql_query)
	 */
	private $result;

	/**
	 * Dependence constructor.
	 *
	 * @param object $db the main db object
	 * @return nothing
	 */
	public function __construct ($dependance)
	{
		$this->PHPDS_dependance($dependance);
	}

	/**
	 * Clears the current connection (useful for example if we're fetching one row at a time and we give up before the end)
	 *
	 * @return boolean, TRUE on success or FALSE on failure
	 * @see includes/PHPDS_db_connector#free()
	 */
	public function free()
	{
		$result = false;
		if (!empty($this->result)) {
			$result = mysql_free_result($this->result);
			$this->result = null;
		}
		return $result;
	}

	/**
	 * Sets the configuration settings for this connector as per the configuration file.
	 *
	 * @date		20120308
	 * @version		1.0
	 * @author		Don Schoeman
	 */
	protected function applyConfig($db_config = '')
	{
		$db = $this->db;
		
		// Retrieve all the database settings
		$db_settings = PU_GetDBSettings($this->configuration, $db_config);

		// For backwards compatibility, set the database class's parameters here as we don't know if anyone references
		// db's properties somewhere else
		$db->server = $db_settings['host'];
		$db->dbName = $db_settings['database'];
		$db->dbUsername = $db_settings['username'];
		$db->dbPassword = $db_settings['password'];

		// Set our own internal properties for faster access and better accessibility.
		$this->dbDSN = $db_settings['dsn'];
		$this->dbHost = $db_settings['host'];
		$this->dbName = $db_settings['database'];
		$this->dbUsername = $db_settings['username'];
		$this->dbPassword = $db_settings['password'];
		$this->dbPersistent = $db_settings['persistent'];
		$this->dbPrefix = $db_settings['prefix'];
		$this->dbCharset = $db_settings['charset'];
	}

	/**
	 * Connect to the database server (compatibility method)
	 *
	 * @date				20100219
	 * @version		1.0
	 * @author		greg
	 * @see stable/phpdevshell/includes/PHPDS_db_connector#connect()
	 */
	public function connect($db_config = '')
	{
		// Apply database config settings to this instance of the connector
		$this->applyConfig($db_config);
		
		try {
			if ($this->dbPersistent == true) {
				$this->link = mysql_pconnect($this->dbHost, $this->dbUsername, $this->dbPassword);
			} else {
				$this->link = mysql_connect($this->dbHost, $this->dbUsername, $this->dbPassword);
			}
			// Create database link.
			$ok = mysql_select_db($this->dbName, $this->link);
			// Display error on link.
			if (empty($ok)) {
				throw $this->factory('PHPDS_databaseException', mysql_error(), mysql_errno());
			}
			if (!empty($this->dbCharset)) {
				mysql_set_charset($this->dbCharset);
			}
		} catch (PHPDS_databaseException $e) {
			throw $e;
		} catch (Exception $e) {
			throw $this->factory('PHPDS_databaseException', mysql_error(), mysql_errno(), $e);
		}
	}

	/**
	 * Actually send the query to MySQL (through $db)
	 * 
	 * May throw a PHPDS_databaseException
	 *
	 * @date		20100219
	 * @version 2.0.3
	 * @author greg <greg@phpdevshell.org>
	 * @date 20100305 (2.0.1) (greg) fixed a bug with the _db_ prefix subsitution
	 * @date 20100729 (2.0.2) (greg) throw error
	 * @date 20100729 (2.0.3) (greg) removed the outer exception throw
	 * @param		$sql string, the actual sql query
	 * @return 		php resource the resulting resource (or false is something bad happened)
	 * @see 		includes/PHPDS_db_connector#query()
	 */
	public function query($sql)
	{
		if (empty($this->link)) $this->connect();
		// Replace the DB prefix.
		$real_sql = preg_replace('/_db_/', $this->dbPrefix, $sql);
		// Run query.
		if (!empty($real_sql)) {
			// Count Queries Used...
			$this->db->countQueries ++;
			$this->_log($real_sql);
			$this->result = mysql_query($real_sql, $this->link);
			if (!$this->result) {
				throw $this->factory('PHPDS_databaseException', mysql_error($this->link), mysql_errno($this->link));
			}
			return $this->result;
		} else {
			return false;
		}
		// TODO: check result validity for non-select requests
	}

	/**
	 * Protect a single string from possible hacker (i.e. escape possible harmfull chars)
	 *
	 * @date			20100216
	 * @version 1.0
	 * @author	greg
	 * @param	$param		string, the parameter to espace
	 * @return string, the escaped string
	 * @see includes/PHPDS_db_connector#protect()
	 */
	public function protect($param)
	{
		return mysql_real_escape_string($param);
	}

	/**
	 * Return the next line as an associative array
	 *
	 * @date			20100216
	 * @version 1.0
	 * @author	greg
	 * @return array, the resulting line (or false is nothing is found)
	 * @see includes/PHPDS_db_connector#fetch_assoc()
	 */
	public function fetchAssoc()
	{
		if (is_resource($this->result)) return mysql_fetch_assoc($this->result);
		else return false;
	}

	/**
	 * Move the internal pointer to the asked line
	 *
	 * @date			20100216
	 * @version 1.0
	 * @author	greg
	 * @param	$row_number		integer, the line number
	 * @return boolean, TRUE on success or FALSE on failure
	 * @see includes/PHPDS_db_connector#seek()
	 */
	public function seek($row_number)
	{
		if (is_resource($this->result)) return mysql_data_seek($this->result, $row_number);
		else return false;
	}

	/**
	 * Return the number of rows in the result of the query
	 *
	 * @date			20100216
	 * @version 1.0
	 * @author	greg
	 * @return integer, the number of rows
	 * @see includes/PHPDS_db_connector#numrows()
	 */
	public function numrows()
	{
		if (is_resource($this->result)) return mysql_num_rows($this->result);
		else return false;
	}

	/**
	 * Return the number of affected rows in the result of the query
	 *
	 * @date 20101103
	 * @version 1.0
	 * @author	Jason
	 * @return integer, the number of affected rows
	 * @see includes/PHPDS_db_connector#affectedRows()
	 */
	public function affectedRows()
	{
		return mysql_affected_rows();
	}

	/**
	 * This method returns the last MySQL error as a string if there is any. It will also
	 * return the actual erroneous SQL statement if the display_sql_on_error property is
	 * set to true. This is very helpfull when debugging an SQL related problem.
	 *
	 * @param string The actual query string.
	 * @return string
	 * @version 1.0.1
	 * @date 20100329 prevent an exception if display_sql_on_error is not set
	 * @author Don Schoeman <titan@phpdevshell.org>
	 */
	public function returnSqlError ($query)
	{
		$result = mysql_error($this->link);
		if (empty($this->displaySqlOnError) && ! empty($result)) {
			$result = mysql_errno($this->link) . ": " . $result . '<br />' . $query;
		}
		return $result;
	}

	/**
	 * Debugging Instance.
	 *
	 * @return debug object
	 */
	public function debugInstance ($ignored = null)
	{
		return parent::debugInstance('db');
	}

	/**
	 * Simply returns last inserted id from database.
	 *
	 * @date 20100610 (greg) (v1.0.1) added $this->link
	 * @version 1.0.1
	 * @author jason
	 * @return int
	 */
	public function lastId () {
		return mysql_insert_id($this->link);
	}

	/**
	 * Will return a single row as a string depending on what column was selected.
	 *
	 * @date 17062010 (jason)
	 * @version 1.0
	 * @author jason
	 * @return string
	 */
	public function rowResults ($row = 0)
	{
		if (is_resource($this->result)) return mysql_result($this->result, $row);
		else return false;
	}

	/**
	 * Start SQL transaction.
	 */
	public function startTransaction()
	{
		return $this->query("START TRANSACTION");
	}

	/**
	 * Ends SQL transaction.
	 *
	 * @param <type> $commit
	 */
	public function endTransaction($commit = true)
	{
		if ($commit) {
			$this->query("COMMIT");
		} else {
			$this->query("ROLLBACK");
		}
	}
	
	
	/**
	 * magic method to get read-only access to various data
	 * 
	 * @since 3.2.1
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 * 
	 * @date 20120611 (v1.0) (greg) added
	 * 
	 * @param string $name name for the parameter to get (ie. "DSN", "Charset", "Host", ...)
	 */
	public function __get($name)
	{
		$localname = 'db'.$name;
		if (!empty($this->$localname)) {
			return $this->$localname;
		}
		return parent::__get($name);;
	}
}
