<?php

/**
 *
 * NOTE: you're not supposed to deal with connectors any way
 *
 * @author Greg
 *
*/
class PHPDS_pdoConnector extends PHPDS_dependant implements iPHPDS_dbConnector
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
	 * @var dbCharset string	A string containing the database connection character set.
	 *							Ignored by pdoConnector since the character set must be
	 *							specified in the DSN.
	 */
	protected $dbCharset = "";

	/**
	 * @var php resource type,	the link for the mysql connection (as returned by new PDO())
	 */
	protected $link = null;

	/**
	 * @var php resource type,	the result resource of a query (as returned by a PDO query)
	 */
	protected $result;

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
	 * Note that there is no mysql_free_result() equivalent for PDO. The closest method to free resources for PDO is
	 * PDOStatement::closeCursor(). closeCursor() frees up the connection to the server so that other SQL statements may
	 * be issued, but leaves the statement in a state that enables it to be executed again.
	 * @return boolean, TRUE on success or FALSE on failure
	 * @see includes/PHPDS_db_connector#free()
	 */
	public function free()
	{
		$result = false;
		if (!empty($this->result)) {
			$result = $this->link->closeCursor();
			$this->result = null;
		}
		return $result;
	}

	/**
	 * Sets the configuration settings for this connector as per the configuration file.
	 *
	 * @date		20120321
	 * @version		1.0
	 * @author		Don Schoeman
	 */
	private function applyConfig($db_config = '')
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
	 * @date		20120321
	 * @version		1.0
	 * @author		don schoeman
	 */
	public function connect($db_config = '')
	{
		if (empty($this->link)) {
			try {
				// Apply database config settings to this instance of the connector
				$this->applyConfig($db_config);
				
				// Set the PDO driver options
				$driver_options = null;
				if ($this->dbPersistent) {
					$driver_options = array(PDO::ATTR_PERSISTENT => true);  // Connection must be persistent
				}

				// Connect to the server and database
				$this->link = new PDO($this->dbDSN, $this->dbUsername, $this->dbPassword, $driver_options);

				// Set the error reporting attribute so that SQL errors also generates exceptions
				$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			} catch(PDOException $e) {
				// TODO: For now throw an unknown error database exception since the driver will be returning with the
				// error code and we don't know how to deal with all of them yet. We have to deal with this properly
				// at some point in the future.
				throw new PHPDS_databaseException($e->getMessage(), 0, $e);
			}
		}
	}

	/**
	 * Executes a query the old fashioned way. Each query is automatically prepared.
	 *
	 * @date		20120321
	 * @version		1.0
	 * @author		don schoeman
	 * @param	 $sql string, the actual sql query
	 * @return php resource, the resulting resource (or false if an error occured)
	 */
	public function query($sql)
	{
		try {
			if (empty($this->link)) $this->connect();
			// Replace the DB prefix.
			$real_sql = preg_replace('/_db_/', $this->dbPrefix, $sql);
			// Run query.
			if (!empty($real_sql)) {
				// Count Queries Used...
				$this->db->countQueries ++;
				$this->_log($real_sql);

				// Since we don't know whether modifier query is passed we don't know wether to use exec() or query().
				// The alternative option is to prepare the statement and then call execute.
				$statement = $this->link->prepare($real_sql);
				$statement->execute();

				if ($statement->columnCount () == 0) {
					return true;  // This was an INSERT/UPDATE/DELETE query
				} else {					
					$this->result = $statement;
					return $this->result; // This was a SELECT query, we need to return the result set
				}
			} else {
				return false;
			}
		} catch (Exception $e) {
			$msg = '<p>The PDO database engine returned with an error (code '. $e->getCode() . ' - ' . $e->getMessage() . '</p>';
			throw new PHPDS_databaseException($msg, 0, $e);
		}
	}

	/**
	 * Protect a string from SQL injection. This function shouldn't really be used since preparing a statement
	 * will protect any parameters passed to the statement automatically within PDO. This function simulates
	 * the mysql_real_escape_string() function since it is not available within PDO.
	 *
	 * TODO: Modify all phpds code to run prepared statements together with parameterised queries to
	 * protect the query from SQL injection instead of using protect()
	 *
	 * @date			20120321
	 * @version 1.0
	 * @author	don schoeman
	 * @param	$param		string, the parameter to escape
	 * @return string, the escaped string
	 */
	public function protect($param)
	{
		return strtr($param, array("\x00" => '\x00', "\n" => '\n', "\r" => '\r', '\\' => '\\\\', "'" => "\'", '"' => '\"', "\x1a" => '\x1a'));
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
		return (is_a($this->result, 'PDOStatement')) ? $this->result->fetch(PDO::FETCH_ASSOC) : false;
	}

	/**
	 * Move the internal pointer to the asked line. Not available for PDO connections, will raise an exception if called.
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
		throw new PHPDS_exception('pdoConnector seek() function not implemented.');
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
		return (is_a($this->result, 'PDOStatement')) ? $this->result->rowCount() : 0;
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
		return (is_a($this->result, 'PDOStatement')) ? $this->result->rowCount() : 0;
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
	 * @author don schoeman
	 */
	public function returnSqlError ($query)
	{
		$error = $this->link->errorInfo();
		if (empty($this->displaySqlOnError) && ! empty($error[0])) {
			$result = $error[0] . ": " . $error[2] . ' ['.$error[1].' <br />' . $query;
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
	public function lastId ()
	{
		return $this->link->lastInsertId();
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
		throw new PHPDS_exception('pdoConnector rowResults() function not implemented.');
	}

	/**
	 * Start SQL transaction.
	 */
	public function startTransaction()
	{
		$this->link->beginTransaction();
	}

	/**
	 * Ends SQL transaction.
	 *
	 * @param <type> $commit
	 */
	public function endTransaction($commit = true)
	{
		if ($commit) {
			$this->link->commit();
		} else {
			$this->link->rollBack();
		}
	}
}
