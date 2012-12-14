<?php

	// deprecated DO NOT USE

	require_once 'PHPUnit/Framework.php';


	define('BASEPATH', realpath(dirname(__FILE__).'/../..'));
	$path = ini_get('include_path').PATH_SEPARATOR.realpath(dirname(__FILE__));
	$includes = array('/includes/', '/includes/legacy/', '/includes/local', '/includes/models');
	foreach($includes as $partialpath) $path .= PATH_SEPARATOR.realpath(BASEPATH.$partialpath);
	ini_set('include_path', $path);


	$_SERVER['SERVER_NAME'] = 'TEST';
	$_SERVER['PHP_SELF'] = '/test.php';

	$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
	$_SERVER["REQUEST_URI"] = $_SERVER['PHP_SELF'];

	require_once 'PHPDS.inc.php';
	require_once BASEPATH.'/lib/PHPDSlib.php';

