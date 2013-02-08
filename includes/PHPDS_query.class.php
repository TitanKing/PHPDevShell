<?php

class PHPDS_query extends PHPDS_dependant
{
	/**
	 * The explicit SQL query
	 *
	 * This value, if present, is used when not overiden by an array in the field named "fields"
	 * It can be accessed from the outside world thought the sql() method.
	 * @see $fields
	 * @see sql()
	 * @var string,
	 */
	protected $sql;

	/**
	 * The name of the field to use as a key.
	 * Use '__auto__' if you want the primary key to dictate the key of the array rows.
	 * When this field is left empty the array will be build normally.
	 *
	 * @var string
	 */
	protected $keyField = '';

	/**
	 * Make a field the point of interest
	 *
	 * This field changes the way some arrays are returned:
	 * - if $focus contains a field name, a row will be the value of this field (scalar) instead of an array of all values in the row
	 * - if the row doesn't contain a field an empty value is used for the row
	 * @var string
	 */
	protected $focus = ''; // can be empty for 'no' or any other value for field name

	/**
	 * strips any row with no content
	 * @var boolean
	 */
	protected $noEmptyRow = false;

	/**
	 * Guidelines to typecast/forcecast the result data
	 *
	 * @var string | array of strings
	 */
	protected $typecast;

	/**
	 * The first line of the result is returned instead of a one-line array
	 *
	 * @var boolean
	 */
	protected $singleRow = false;

	/**
	 * Automatically escape bad chars for all in-parameters
	 * @var boolean
	 */
	protected $autoProtect = false;
	
	/**
	 * If you want your non-numeric values to be quoted, set the quote character here
	 * @var string
	 */
	protected $autoQuote = null;

	/**
	 * Instead of the query result, returns the last_insert_id()
	 * @var boolean
	 */
	protected $returnId = false;

	/**
	 * Return one value from the asked field of the asked line
	 * @var boolean
	 */
	protected $singleValue = false;

	/**
	 * A link between the query and the actual database server.
	 *
	 * Set this to the connector class name if you want something else than the default one
	 *
	 * @var string|iPHPDS_dbConnector, the connector used to carry the query (either name or instance)
	 */
	protected $connector = null;

	/**
	 * The list of fields to study
	 *
	 * If present, this associative array contains the fields which will be present in the SELECT ... clause.
	 * This will override the $sql field; however if you use the sql('something') method after prebuild() the new query string will override the fields
	 * @see $sql
	 * @var array (optional)
	 */
	protected $fields;

	/**
	 * The WHERE clause
	 *
	 * A default WHERE clause; note you can use the addWhere() method to concatenate after this value
	 * @var string (optional)
	 */
	protected $where;
	protected $groupby = '';
	protected $orderby = '';
	protected $limit = '';

	/**
	 * In some specific case (namely debugging) this will contain a cached version of the results
	 * AVOID playing with that
	 * @date 20110218 (greg) added
	 * @var array
	 */
	protected $cachedResult;

	/**
	 * number of rows counted from fetching the result - only valid after the whole result has been fetched
	 * @var int
	 */
	protected $rowCount = -1;

	/**
	 * number of rows affected by the query - validity depends on the DB used
	 * @var int
	 */
	protected $affectedRows = -1;

	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	//////////// DEPRECATED DUE TO WRONG NAMING CONVENTION ///////////////////////
	//////////// DONT USE THESE METHODS !!!!!!!!!!!!!!!!! ////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	/**
	 * @deprecated
	 * @var boolean
	 */
	protected $single_value;
	/**
	 * @deprecated
	 * @var boolean
	 */
	protected $return_id;
	/**
	 * @deprecated
	 * @var boolean
	 */
	protected $auto_protect;
	/**
	 * @deprecated
	 * @var boolean
	 */
	protected $single_row;
	/**
	 * @deprecated
	 * @var boolean
	 */
	protected $no_empty_row;
	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 */
	public function construct ()
	{
		if (empty($this->connector)) {
			$this->connector($this->db->connector()); // use default connector
		} else {
			$this->connector($this->connector);
		}
		// Backwards compatible variables.
		if (isset($this->no_empty_row))
			$this->noEmptyRow = $this->no_empty_row;
		if (isset($this->single_row))
			$this->singleRow = $this->single_row;
		if (isset($this->auto_protect))
			$this->autoProtect = $this->auto_protect;
		if (isset($this->return_id))
			$this->returnId = $this->return_id;
		if (isset($this->single_value))
			$this->singleValue = $this->single_value;
	}

	/**
	 * Get and/or set the actual connector instance
	 *
	 * Note: can only be set if it was not set before
	 *
	 * @param PHPDS_dbConnector|string $connector
	 * @return PHPDS_dbConnector
	 */
	public function connector($connector = null)
	{
		if (!is_a($this->connector, 'iPHPDS_dbConnector')) {
			if (is_string($connector)) {
				$connector = $this->db->connector($connector);
			}
			if (is_a($connector, 'iPHPDS_dbConnector')) {
				$this->connector = $connector;
			}
		}
		return $this->connector;
	}

	/**
	 * The usual process of a query: check the parameters, send the query to the server, check the results
	 *
	 * Return the results as an array (for SELECT queries), true for other successfull queries, false on failure
	 *
	 * @version 1.1.1
	 * @date 20100709 (greg) (v1.1.1) changed is_resource() to !empty() because it may return something else
	 * @param mixed $parameters
	 * @return array or boolean
	 */
	public function invoke($parameters = null)
	{
		try {
			if ($this->checkParameters($parameters)) {
				$res = $this->query($parameters);
				// Fix to prevent invoke from returning false if INSERT REPLACE DELETE etc... is executed on success.
				if ($res === true && empty($this->returnId))
					return $this->connector->affectedRows();
				if (!empty($res)) {
					$results = $this->getResults();
					if ($this->checkResults($results))
						return $results;
				} else return $res;
			}
			return false;
		} catch (Exception $e) {
			$msg = '<p>The faulty query source sql is:<br /><pre class="ui-state-highlight ui-corner-all">'.$this->sql().'</pre><br />';
			if (!empty($parameters)) $msg .= '<tt>'.PU_dumpArray($parameters, _('The query parameters were:')).'</tt>';
			throw new PHPDS_databaseException($msg, 0, $e);
		}
	}

	/**
	 * Build and send the query to the database
	 *
	 * @since 20100219
	 * @version 1.0.3
	 * @date 20110216 (greg) (v1.0.3) added a log of the sql + the class name
	 * @date 20110731 (greg) altered to use $this->querySQL
	 * @author	greg
	 * @param mixed $parameters (optional)array, the parameters to inject into the query
	 * @return void
	 */
	public function query($parameters = null)
	{

		$sql = $this->build($parameters);
		return $this->querySQL($sql);
	}

	/**
	 * Direclty send the query to the database
	 *
	 * @since 20110731
	 * @version 1.0.1
	 * @date 20110731 (greg) added based on old $this->query
	 * @date 20120724 (v1.0.1) (greg) added PHPDS_queryException
	 * @author greg
	 * @param string $sql, the sql request
	 * @return void
	 */
	public function querySQL($sql)
	{
		try {
			$this->rowCount = -1;
			$result = $this->connector->query($sql);
			$this->affectedRows = $this->connector->numrows();

			$this->queryDebug($sql);

			return $result;
		} catch (Exception $e) {
			throw new PHPDS_queryException($sql, 0, $e);
		}
	}

	/**
	 * FIrephp-specific debug display of the query
	 *
	 * @param string $sql
	 */
	public function queryDebug($sql)
	{
		$debug = $this->debugInstance();
		$firephp = $this->errorHandler->getFirePHP();
		if ($debug->enable() && $firephp && !headers_sent()) {

			$flags =
				($this->singleRow ? ' singleRow' : '' ).($this->singleValue ? ' singleValue' : '' ).($this->noEmptyRow ? ' noEmptyRow ' : '' )
				.(empty($this->focus) ? '' :  ' focus='.$this->focus).(empty($this->keyField) ? '' :  ' keyField='.$this->keyField).(empty($this->typeCast) ? '' :  ' typeCast='.$this->typeCast);

			$table   = array();
			$table[] = array('','');
			$table[] = array('SQL', $sql);
			$table[] = array('count', $this->affectedRows. ' rows');
			$table[] = array('flags', $flags);

			$firephp->table('Query: '.get_class($this), $table);

			/*$firephp->group('Query: '.get_class($this),
								array('Collapsed' => true,
											'Color' => '#64c40a'));

			$firephp->log($sql, '[ SQL ]');
			$firephp->log($this->count(). ' rows', '[ count ]');
			$firephp->log($flags,'[ flags ]');
			//$firephp->log($this->asWhole(), '[ '.$this->count(). ' rows ]');
			$firephp->groupEnd();*/
		}
	}

	/**
	 * Build a query combination of columns and rows specifically designed to write rows of data to the database.
	 *
	 * @since 20100226
	 * @since 20100226
	 * @version 1.0.0
	 * @param array Holds columns in the order they need to be written.
	 */
	public function rows($parameters = null)
	{
		$r = '';
		$build = '';
        if (empty($parameters)) return false;
		foreach ($parameters as $col) {
			foreach ($col as $row) {
				$r .= "'" . $row . "',";
			}
			$r = rtrim($r, ',');
			$build .= "($r),";
			$r = '';
		}
		$parameters = rtrim($build, ',');
		if (! empty($parameters)) {
			return $parameters;
		} else {
			return false;
		}
	}

	/**
	 * Get/set actual sql string.
	 *
	 * You may want to override this to alter the sql string as whole, and/or build it from various sources.
	 * Note this is only the first part of the query (SELECT ... FROM ...), NOT including WHERE, GROUP BY, ORDER BY, LIMIT
	 *
	 * @param string $sql (optional) if given, stored into the object's sql string
	 * @return string the sql text
	 * @version	1.0
	 * @author greg
	 */
	public function sql($sql = null)
	{
		if (!empty($sql)) $this->sql = $sql;
		return $this->sql;
	}

	/**
	 * Build the query based on the private sql and the parameters
	 *
	 * @since 20100216
	 * @since 20100428	(v1.0.1) (greg) use sql() instead of sql
	 * @date 20100630 (v1.0.2) (greg) use array_compact to avoid null values
	 * @date 20121014 (v1.0.3) (greg) removed used of array_compact
	 * @version 1.0.3
	 * @author	greg
	 * @param $parameters (optional)array, the parameters to inject into the query
	 * @return string, the sql query string
	 */
	public function build($parameters = null)
	{
		$sql = '';

		try {
			$this->preBuild();
			$sql = $this->sql() . $this->extraBuild($parameters);

			if (!empty($parameters)) {
				if (is_scalar($parameters)) {
					$parameters = array($parameters);
				}

				if (is_array($parameters)) {
					if ($this->autoProtect) {
						$parameters = $this->protectArray($parameters, $this->autoQuote);
					}
					$sql = PU_sprintfn($sql, $parameters);
				}
				//TODO is parameters is neither scalar nor array what should we do?
			}
		} catch (Exception $e) {
			throw new PHPDS_databaseException('Error building sql for <tt>' . get_class() . '</tt>', 0 ,$e);
		}
		return $sql;
	}

	/**
	 * Construct the extra part of the query (WHERE ... GROUP BY ... ORDER BY...)
	 * Doesn't change $this->sql
	 *
	 * @param array $parameters
	 * @return string (sql)
	 * @version	1.0
	 * @author greg
	 */
	public function extraBuild($parameters = null)
	{
		$extra_sql = '';

		if (!empty($this->where)) $extra_sql .= ' WHERE '.$this->where.' ';
		if (!empty($this->groupby)) $extra_sql .= ' GROUP BY '.$this->groupby.' ';
		if (!empty($this->orderby)) $extra_sql .= ' ORDER BY '.$this->orderby.' ';
		if (!empty($this->limit)) $extra_sql .= ' LIMIT '.$this->limit.' ';

		return $extra_sql;
	}

	/**
	 * If the fields list has been set, construct the SELECT statement (or else do nothing)
	 *
	 * @version 1.0.1
	 * @author greg
	 * @date 20100628 (v1.0.1) (greg) the build sql string replaces the obejct's sql field, instead of being appended
	 * @return nothing
	 */
	public function preBuild()
	{
		$fields = $this->fields;
		if (!empty($fields)) {
			$sql = '';
			$key = $this->getKey();
			if ($key && !in_array($key, $fields)) $fields[$key] = true;
			foreach(array_keys($fields) as $key) if (!is_numeric($key)) $sql .= $key.', ';
			$sql = 'SELECT '.rtrim($sql, ', ');

			if (!empty($this->tables)) $sql .= ' FROM ' . $this->tables;
			$this->sql = $sql;
		}
	}

	/**
	 * Add a subclause to the main WHERE clause of the query
	 *
	 * @param string $sql
	 * @return self
	 */
	public function addWhere($sql, $mode = 'AND')
	{
		if (empty($this->where)) $this->where = '1';
		$this->where .= " $mode $sql ";
		return $this;
	}

	/**
	 * Protect a array of strings from possible hacker (i.e. escape possible harmfull chars)
	 *
	 * @since 20100216
	 * @version 1.1
	 * @date 20111010 (v1.1) (greg) added "quote" parameter
	 * @author  greg
	 * @param $a    array, the strings to protect
	 * @param $quote string, the quotes to add to each non-numerical scalar value
	 * @return array, the same string but safe
	 */
	public function protectArray(array $a, $quote = '')
	{
		return $this->db->protectArray($a, $quote);
	}

	/**
	 * Protect a strings from possible hacker (i.e. escape possible harmfull chars)
	 *
	 * @since 20101109
	 * @version 1.0
	 * @author  Jason
	 * @param string the strings to protect
	 * @return string the same string but safe
	 */
	public function protectString($string)
	{
		$clean = $this->connector->protect($string);
		return $clean;
	}

	/**
	 * Try to figure out which is the key field.
	 * 
	 * TODO: we assume first column is a key field, this is wrong!!!
	 *
	 * @param array $row, a sample row to study
	 * @return string (or null), the key field name
	 * @version	1.0
	 * @author greg
	 */
	public function getKey($row = null)
	{
		$key = $this->keyField;
		if (is_array($row)) {
			if ('__auto__' == $key) {
				$keys = array_keys($row);
				$key = array_shift($keys);
			}
			return ($key && !empty($row[$key])) ? $row[$key] : null;
		} else {
			return '__auto__' != $key ? $key : null;
		}
	}

	/**
	 * Returns all lines from the result as a big array of arrays
	 *
	 * @since 20100216	(v1.0) (greg)
	 * @since 20100428	(v1.1) (greg) use the focus parameter; use the smart key
	 * @since 20100607 (v1.2) (greg) renamed compact to focus, use the noEmptyRow/single_line parameters
	 * @version 1.1
	 * @author	greg
	 * @return array, all the lines as arrays
	 */
	public function asWhole()
	{
		$result = array();
		$count = 0;

		while ($row = $this->asLine()) {
			$count++;
			$key = $this->getKey($row);
			if (!empty($this->focus)) {
				$row = (isset($row[$this->focus])) ?  $row[$this->focus] : null;
			}
			if ($row || !empty($this->noEmptyRow)) {
				if ($key) {
					$result[$key] = $row;
				} else {
					$result[] = $row;
				}
			}
		}
		$this->rowCount = $count;
		return $result;
	}

	/**
	 *
	 * @param <type> $values
	 * @param <type> $key
	 * @return <type>
	 */
	public function typeCast($values, $key = null)
	{
		if (! empty($this->typecast)) {
			if (is_array($values)) {
				foreach($values as $key => $value) {
					$values[$key] = $this->typeCast($value, $key);
				}
			} else {
				$type = is_array($this->typecast) ? (!empty($this->typecast[$key]) ? $this->typecast[$key] : null) : $this->typecast;
				switch ($type) {
					case 'string':
						$values = (string) $values; break;
					case 'int':
					case 'integer':
						$values = (int) $values; break;
					case 'bool':
					case 'boolean':
						$values = (bool) $values; break;
					case 'float':
					case 'double':
						$values = (float) $values; break;
					// default is to NOT change the $value
				}
			}
		}
		return $values;
	}

	/**
	 * Deal with all special cases (i.e flags) regarding how results should be returned
	 *
	 * The special cases handled are these (in order of precedence):
	 * - returnId (instead of the actual result, lastId is returned)
	 * - singleValue (only the first value is returned as a scalar)
	 * - singleRow (the first row is returned as a an one-dimension array)
	 *
	 * Cell-specific handling is done elsewhere
	 *
	 * In the absence of special case, the whole result is returned as an array of arrays (by calling as_whole() )
	 *
	 * @version 1.1.1
	 *
	 * @date 20100610 (greg) (v1.0) added, based on Jason's work
	 * @date 20100617 (jason) (v1.0.1) added support for "string" setting
	 * @date 20100620 (greg) (v1.0.2) cleaned up using class methods
	 * @date 20100708 (greg) (v1.1) clean up with definitive API
	 * @date 20110812 (greg) (v1.1.1) removed special "empty" case since only MySQL supports it
	 *
	 * @return usually an array, although can be false, or int for an ID
	 */
	public function getResults()
	{
		if (! empty($this->returnId)) {
			return $this->connector->lastId();
		}

		if (!empty($this->singleValue)) {
			return $this->asOne();
		}

		if (!empty($this->singleRow)) {
			return $this->asLine();
		}
		return  $this->asWhole();
	}

	/**
	 * Returns a single field from every line, resulting in an array of values (ie some kind of "vertical" fetching)
	 *
	 * Note: this is different from as_whole, since only ONE value is present in each line
	 *
	 * @since 20100216
	 * @version 1.0.2
	 * @date 20110816 (v1.0.1) (greg) added a count field
	 * @date 20120202 (v1.0.2) (greg) using $this::getKey() to get the key column
	 * @author greg
	 * @param $field	string, the field to extract on each line
	 * @return array, all the values
	 */
	public function asArray($field)
	{
		$a = array();
		$count = 0;

		while ($row = $this->connector->fetchAssoc()) {
			$count++;
			if (!empty($row[$field])) {
				$value = $row[$field];
				$key = $this->getKey($row);

				if (!empty($key) && !empty($row[$key])) {
					$a[$row[$key]] = $value;
				} else {
					$a[] = $value;
				}
			}
		}

		$this->rowCount = $count;
		return $a;
	}

	/**
	 * Returns the asked line as an array
	 *
	 * You can either ask for the next line (no parameter) or given a row number in the result.
	 *
	 * Note: the row number is based on the result, it may not be same as the row number in the complete table
	 *
	 * @since 3.0
	 * @version 1.0.1
	 * @author greg
	 *
	 * @date 20100810 (v1.0.1) (greg) return null if the resultset is empty

	 * @param integer $row_number (optional) - NOT USED ANYMORE
	 * @return array| null, the line or null if the resultset is empty
	 */
	public function asLine($row_number = null)
	{
		if ($this->count() != 0) {
			$row = $this->connector->fetchAssoc();
			return $this->typeCast($row);
		}
	}

	/**
	 * Return one value from the asked field of the asked line
	 *
	 * @since 3.0
	 * @version 1.0.4
	 * @author greg
	 *
	 * @date 20100620 (v1.0.1) (greg) made parameters optional (no "field" means first field)
	 * @date 20100630 (v1.0.2) (greg) object's focus is used if "$field" parameter is empty
	 * @date 20100810 (v1.0.3) (greg) return null if the resultset is empty
	 * @date 20110908 (v1.0.4) (greg) fixed a bug when dealing with an empty result line
	 *
	 * @param integer $row_number (optional)
	 * @param string $field field name (optional)
	 * @return string | null
	 */
	public function asOne($row_number = null, $field = null)
	{
		if ($this->count() != 0) {
			$row = $this->asLine($row_number);
			if (!is_array($row)) {
				return null;
			}
			if (!empty($field)) {
				$field = $this->focus;
			}
			if (!empty($field)) {
				return (isset($row[$field]) ? $row[$field] : null);
			} else {
				return array_shift($row);
			}
		}
	}

	/**
	 * Return the number of lines in a result
	 *
	 * @since 20100216
	 * @version 2.0
	 * @date 20110816 (v2.0) (greg) changed not use a call to the connector since a PDO might give a wrong result
	 * @author greg
	 * @return integer, the number of rows, or -1 if it cannot be evaluated
	 */
	public function count()
	{
		return $this->rowCount;
	}

	public function total()
	{
		return $this->affectedRows;
	}

	/**
	 * Limits query.
	 *
	 * @param int $limit
	 */
	public function limit($limit)
	{
		// TODO: check parameter
		$this->limit = $limit;
	}

	/* THESE METHODS ARE MEANT TO BE OVERRIDEN */

	/**
	 * Allows daughter classes to check the parameters array before the query is sent
	 *
	 * @param $parameters array, the unprotected parameters
	 * @return boolean true is it's ok to sent, false otherwise
	 */
	public function checkParameters(&$parameters = null)
	{
		return true;
	}

	/**
	 * Allows daughter classes to check the results array before it's sent back
	 *
	 * @param $parameters array, the unprotected results
	 * @return boolean true is it's ok to sent, false otherwise
	 */
	public function checkResults(&$results = null)
	{
		return true;
	}


	public function debugInstance($domain = null)
	{
		return parent::debugInstance(empty($domain) ? 'QUERY%'.get_class($this): $domain);
	}
	
	/**
	 * Returns the desired charset for the db link
	 */
	public function charset()
	{
		return $this->connector()->Charset;
	}

	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	//////////// DEPRECATED DUE TO WRONG NAMING CONVENTION ///////////////////////
	//////////// DONT USE THESE METHODS !!!!!!!!!!!!!!!!! ////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	/**
	 * Construct the extra part of the query (WHERE ... GROUP BY ... ORDER BY...)
	 * Doesn't change $this->sql
	 *
	 * @deprecated
	 * @param array $parameters
	 * @return string (sql)
	 * @version	1.0
	 * @author greg
	 */
	public function extra_build($parameters = null)
	{
		return $this->extraBuild($parameters);
	}

	/**
	 * Protect a array of strings from possible hacker (i.e. escape possible harmfull chars)
	 *
	 * @deprecated
	 * @since 20100216
	 * @version 1.0
	 * @author  greg
	 * @param $a    array, the strings to protect
	 * @return array, the same string but safe
	 */
	public function protect_array(array $a)
	{
		return $this->protectArray($a);
	}

	/**
	 * Try to figure out which is the key field.
	 *
	 * @deprecated
	 * @param array $row, a sample row to study
	 * @return string (or null), the key field name
	 * @version	1.0
	 * @author greg
	 */
	public function get_key($row = null)
	{
		return $this->getKey($row);
	}

	/**
	 * Returns all lines from the result as a big array of arrays
	 *
	 * @deprecated
	 * @since 20100216	(v1.0) (greg)
	 * @since 20100428	(v1.1) (greg) use the focus parameter; use the smart key
	 * @since 20100607 (v1.2) (greg) renamed compact to focus, use the noEmptyRow/single_line parameters
	 * @version 1.1
	 * @author	greg
	 * @return array, all the lines as arrays
	 */
	public function as_whole()
	{
		return $this->asWhole();
	}

	/**
	 * Deal with all special cases (i.e flags) regarding how results should be returned
	 *
	 * The special cases handled are these (in order of precedence):
	 * - returnId (instead of the actual result, last_id is returned)
	 * - single_value (only the first value is returned as a scalar)
	 * - singleRow (the first row is returned as a an one-dimension array)
	 *
	 * Cell-specific handling is done elsewhere
	 *
	 * In the absence of special case, the whole result is returned as an array of arrays (by calling as_whole() )
	 *
	 * @deprecated
	 * @version 1.1
	 *
	 * @date 20100610 (greg) (v1.0) added, based on Jason's work
	 * @date 20100617 (jason) (v1.0.1) added support for "string" setting
	 * @date 20100620 (greg) (v1.0.2) cleaned up using class methods
	 * @date 20100708 (greg) (v1.1) clean up with definitive API
	 *
	 * @return usually an array, although can be false, or int for an ID
	 */
	public function get_results()
	{
		return $this->getResults();
	}

	/**
	 * Returns a single field from every line, resulting in an array of values (ie some kind of "vertical" fetching)
	 *
	 * Note: this is different from as_whole, since only ONE value is present in each line
	 *
	 * @deprecated
	 * @since 20100216
	 * @version 1.0
	 * @author	greg
	 * @param $field	string, the field to extract on each line
	 * @return array, all the values
	 */
	public function as_array($field)
	{
		return $this->asArray($field);
	}

	/**
	 * Returns the asked line as an array
	 *
	 * You can either ask for the next line (no parameter) or given a row number in the result.
	 *
	 * Note: the row number is based on the result, it may not be same as the row number in the complete table
	 *
	 * @deprecated
	 * @since 3.0
	 * @version 1.0.1
	 * @author greg
	 *
	 * @date 20100810 (v1.0.1) (greg) return null if the resultset is empty

	 * @param integer $row_number (optional)
	 * @return array| null, the line or null if the resultset is empty
	 */
	public function as_line($row_number = null)
	{
		return $this->asLine($row_number);
	}

	/**
	 * Return one value from the asked field of the asked line
	 *
	 * @deprecated
	 * @since 3.0
	 * @version 1.0.3
	 * @author greg
	 *
	 * @date 20100620 (v1.0.1) (greg) made parameters optional (no "field" means first field)
	 * @date 20100630 (v1.0.2) (greg) object's focus is used if "$field" parameter is empty
	 * @date 20100810 (v1.0.3) (greg) return null if the resultset is empty
	 *
	 *
	 * @param integer $row_number (optional)
	 * @param string $field field name (optional)
	 * @return string | null
	 */
	public function as_one($row_number = null, $field = null)
	{
		return $this->asOne($row_number, $field);
	}
	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	//////////// END DEPRECATED NAMING CONVENTIONS ///////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
}





