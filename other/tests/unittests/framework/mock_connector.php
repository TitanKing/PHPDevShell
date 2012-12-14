<?php

	require_once 'PHPDS_db.class.php';
	require_once 'PHPDS_query.class.php';

	class TEST_mock_connector extends PHPDS_dependant implements iPHPDS_dbConnector
	{
		/*private $link;
		private $result;*/

		public $data = array();
		private $pointer = 0;
		private $lastid = 0;

		public function free()
		{
			$this->data = array();
			$this->pointer = 0;
			$this->lastid = 0;
		}
		
		public function stubdata(array $data)
		{
			$this->free();
			$this->data = $data;
		}

		public function connect()
		{
			$this->link = true;
		}

		public function query($sql)
		{
			if (empty($this->link)) $this->connect();
			$this->pointer = 0;
			$this->lastid++;
			
			return $this->data; // it should be an object, but this should be ok
		}

		public function protect($param)
		{
			return mysql_real_escape_string($param);
		}

		public function fetchAssoc()
		{
			if ($this->pointer >= count($this->data)) return false;
			return $this->data[$this->pointer++];
		}

		public function seek($row_number)
		{
			$this->pointer = $row_number;
			if ($this->pointer < 0) $this->pointer = 0;
			if ($this->pointer > count($this->data)) $this->pointer = count($this->data) - 1;
		}

		public function numrows()
		{
			return count($this->data);
		}	
		
		public function affectedRows()
		{
			return -1;
		}

		public function returnSqlError($query)
		{
			return '';
		}

		public function debugInstance ($domain = null)
		{
			return parent::debugInstance('db');
		}

		public function lastId($reset = false) {
			if ($reset) $this->lastid = 0;
			return $this->lastid;
		}

		public function rowResults($row = 0) {
			return $this->data[$row];
		}

		public function startTransaction()
		{
			// do nothing
		}

		public function endTransaction($commit = true)
		{
			// do nothing
		}

	}



	class TEST_stubQuery  extends PHPDS_query
	{
		protected $connector = 'TEST_mock_connector';
		protected $sql = '';
		
		public function stubdata(array $data)
		{
			$this->connector->stubdata($data);
		}
		
		// allow easy access from the test scripts to the fields (make them public)
		public $singleRow;
		public $singleValue;
		public $typecast;
		public $keyField;
		public $focus;
		//public $getLastID;
	}
