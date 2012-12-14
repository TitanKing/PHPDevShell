<?php

	//require_once 'PHPUnit/Framework.php';
		
	date_default_timezone_set('America/Los_Angeles'); // this is stupid, but *required* by PHP :(   TODO: make it better! if possible...

	$my_path = 'other/tests/unittests/framework';
	$basepath = realpath(substr(__FILE__, 0, strripos(__FILE__, $my_path)));
	define('BASEPATH', $basepath.'/');
	
	$path = ini_get('include_path').PATH_SEPARATOR.__FILE__.PATH_SEPARATOR.BASEPATH;
	$includes = array('/includes/', '/includes/legacy/', '/includes/local', '/includes/models');
	foreach($includes as $partialpath) $path .= PATH_SEPARATOR.realpath(BASEPATH.$partialpath);
	ini_set('include_path', $path);


	$_SERVER['SERVER_NAME'] = 'TEST';
	$_SERVER['PHP_SELF'] = '/test.php';

	$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
	$_SERVER["REQUEST_URI"] = $_SERVER['PHP_SELF'];

	require_once 'PHPDS.inc.php';
	require_once 'PHPDS_exception.class.php'; // need for the various exception objects
	
	/*require_once 'db.class.php';
	require_once 'PHPDS_db.class.php';
	require_once 'PHPDS_pdoConnector.class.php';
	
	class TEST_sqlite extends PHPDS_pdoConnector
	{
		public function construct()
		{
			$dsn = 'sqlite:'.BASEPATH.'other/tests/unittests/';
		}
	}*/
	
	
	class TEST_main extends PHPDS
	{
		protected static $_instance;

		public function __construct()
		{
			parent::__construct(true);
		}
				
		public static function instance()
		{
			if (empty(TEST_main::$_instance)) {
				TEST_main::$_instance = new TEST_main(true);
			}
			return TEST_main::$_instance;
		}
		
		protected function config()
		{
			$success = spl_autoload_register(array($this, "PHPDS_autoloader"));

			//$configuration = new PHPDS_array();
			$this->loadConfig(); //////////////////////////////////////////////////
			
			$this->configuration['database_name'] = 'PHPDS_test';
			$this->configuration['database_user_name'] = 'PHPDS_test';
			$this->configuration['database_password'] = 'PHPDS_test';
			$this->configuration['server_address'] = 'localhost';
			$this->configuration['persistent_db_connection'] = false;
			$this->configuration['database_prefix'] = '';
			
			/*$configuration['debug']['enable'] = true;
			$configuration['debug']['level'] = 4; // 	DEBUG = 4;INFO = 3;WARN = 2;ERROR = 1;LOG = 0;
			$configuration['debug']['firePHP'] = true;
			$configuration['debug']['serverlog'] = true;
			//$configuration['debug']['serverlog'] = false;
			//$configuration['debug']['domains'] = array('authlib', 'test', 'user', 'db', 'security', 'skel', 'core', '!');
			$configuration['debug']['domains'] = array('authlib', 'test', 'user', 'security');

			$configuration['error']['display'] = true;
			$configuration['error']['firePHP'] = true;
			$configuration['error']['ignore_notices'] = false;
			$configuration['error']['ignore_warnings'] = false;
			//$configuration['error']['file'] = '/tmp/phpdevshell.'.date('Y-m-d').'.log';
			//$configuration['error']['mail']= 'root@vecteurm.com';

			$configuration['production'] = false;

			$configuration['gzip'] = false;

			error_reporting(E_ALL);*/
			
			//$this->configuration = $configuration;
			
			//$this->configDb();
			$this->configuration['m'] = 'dummy';
			
			$this->classes = $this->PHPDS_classFactory();
		}
	}
	