<?php

/**
 * Ok this is what it is all about, start PHPDevShell engine and let the games begin.
 */

// Enable this only if you are debugging the early stages of PHPDS's initialization.
// Really only used for core PHPDevShell development.
$early_debug = false;

$start_time = microtime(true);

date_default_timezone_set('America/Los_Angeles'); // this is stupid, but *required* by PHP :(   TODO: make it better! if possible...

// Super high level exception if all else truly fails.
try {
	define('BASEPATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
	$includes = array('/includes/', '/includes/legacy/', '/includes/local');
	foreach ($includes as $path) {
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . realpath(BASEPATH . $path));
	}

	if (file_exists('./index.local.php')) {
		require './index.local.php';
	} else {
		require 'includes/PHPDS.inc.php';
		$PHPDS = new PHPDS;
		$PHPDS->run();
	}
} catch (Exception $e) {
	if ($early_debug) {
		error_log('Uncaught exception!' . $e);
	}
	print '<h1>Uncaught exception!</h1>';
	print '<p>PHPDevShell encountered a serious error, please check all files and their permissions. Some components could be missing.</p>';
	print '<div style="color: red">' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . '</div>';
	if (!empty($PHPDS) && (is_a($PHPDS, 'PHPDS'))) {
		$config = $PHPDS->PHPDS_configuration();
		if (!empty($config['error']['display'])) print "<pre>$e</pre>";
	}
	print '<p>You might want to run the <a href="other/service.php">installation script.</a></p>';
}