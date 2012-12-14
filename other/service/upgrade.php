<?php

$HTMl = '';
$type = 'upgrade';

require ('service.inc.php');
$TITLE = "$package Upgrader";

// Store everything in cache...
preparations();

$errors = array();
$warnings = array();
$stage = intval(empty($_GET['stage']) ? 0 : $_GET['stage']);
if (($stage == 2) && empty($_POST)) $stage = 1;

/// main switch
headHTML();
// stage 0: display intro
// stage 1: check system & display fields
// stage 2 : check system & check fields & check config file, then install.
if ($doit == false) warningPrint('Upgrade is in developer mode, set $doit = true.');
switch ($stage) {
	case 2:
		headingPrint($package . " Upgrader Stage 2");
		doStage2();
		break;
	default:
		headingPrint($package . " Upgrader Stage 1");
		doStage1();
		break;
}

// end html
footHTML();

function displaySuccess()
{
	global $aurl;

	okPrint("The upgrade was successful.");
	notePrint("You might want to erase the upgrade.php file and <a href=\"$aurl\">check your upgrade.</a>. Also use the plugin manager and click on upgrade to upgrade your menus.");
}

function displayFields()
{
	okPrint(_('Your server meets all the upgrade requirements'));
	warningPrint(_('<strong>Please remeber to update your .htaccess file to the latest version else you will see a blank screen when trying to access your site.</strong>'));

	global $data;

	?>
	<form action="upgrade.php?stage=2" method="post" class="validate">
		<h1>Configuration Information</h1>
		<p>The following information should match the data found in the configuration file before the upgrade can start:</p>
		<div class="row">
			<div class="column grid_4">
				<fieldset>
					<legend>Configuration File</legend>
					<?php
					displayField('Configuration File', 'config_file');
					?>
				</fieldset>
			</div>
			<div class="column grid_4">
				<fieldset>
					<legend>Database Information</legend>
					<?php
						displayField('Database Name', 'db_name');
						displayField('Database User Name', 'db_username');
						displayField('Database User Password', 'db_password');
						displayField('Database Server', 'db_server');
						displayField('Database Prefix', 'db_prefix');
					?>
				</fieldset>
			</div>
			<div class="column grid_4 last">
				<fieldset>
					<legend>Upgrade</legend>
					<p>
						<button type="submit" name="step1" value="step1"><span class="save"></span><span>Continue Upgrade...</span></button>
						<button type="reset"><span class="reset"></span><span>Reset</span></button>
					</p>
				</fieldset>
			</div>
		</div>
	</form>
	<?php
}

function checkFields()
{
	global $data, $errors;

	checkField('db_name', _('Please supply the Database Name.'), 'phpdevdbname');
	checkField('db_username', _('Please supply the Database Username.'), 'phpdev');
	checkField('db_password', _('Please supply the Database Password.'), 'phpdev');
	checkField('db_server', _('Please supply the Database Server Address.'), 'localhost');
	checkField('db_prefix', _('Please supply theDatabase Prefix.'), 'pds_');
	checkField('config_file', _('Please supply the Config File.'), 'single-site.config.php');

	return (count($errors) == 0);
}

function get_queries()
{
	global $data, $db_version, $db_versions;
	$phpds_db_ver = get_db_version();
	$queries = '';
	foreach ($db_versions as $fetch_version) {
		if ($phpds_db_ver < $fetch_version) {
			$filename = 'PHPDevShell-db' . $fetch_version . '.sql';
			notePrint(_('Using SQL file named').' "'.$filename.'"');
			$fp = fopen($filename, 'r');
			$queries .= stream_get_contents($fp);
			fclose($fp);
		}
	}
	if (! empty($queries)) {
		$queries = preg_replace('/pds_core_/', $data['db_prefix'] . 'core_', $queries);
		$query = explode(';', $queries);
		array_pop($query);
	}

	// Update version at the end of the query batch
	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_plugin_activation` VALUES ('PHPDevShell', 'install', '" . $db_version . "', '1');";

	return $query;
}