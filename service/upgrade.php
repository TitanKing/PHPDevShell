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
switch ($stage) {
	case 2:
		headingPrint("Upgrade Stage Two");
		doStage2();
		break;
	default:
		headingPrint("Upgrade Stage One");
		doStage1();
		break;
}

// end html
footHTML();

function displaySuccess()
{
	global $aurl;

	okPrint("The upgrade was successful.");
	notePrint("You might want to erase the /service directory and <a href=\"$aurl\">check your upgrade.</a> Also use the plugin manager and click on upgrade to upgrade your nodes.");
}

function displayFields()
{
    global $data, $doit;

	okPrint(_('<i class="icon-ok"></i> Your server meets all the upgrade requirements.'));
    if ($doit == false) warningPrint('<i class="icon-warning-sign"></i> Upgrade is in developer mode, set $doit = true.');

	?>
	<form action="upgrade.php?stage=2" method="post" class="validate">
		<h2>Configuration Information</h2>
        <p class="text-warning">
            The following information <strong>should match</strong> the data found in the
            <strong>configuration file</strong> before the upgrade can start:
        </p>
		<div class="row">
			<div class="span4">
				<fieldset>
					<legend>Configuration File</legend>
					<?php
					displayField('Configuration File', 'config_file');
					?>
				</fieldset>
			</div>
			<div class="span4">
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
			<div class="span4">
				<fieldset>
					<legend>Upgrade</legend>
					<p>
						<button type="submit" name="step1" value="step1" class="btn btn-primary">Continue Upgrade</button>
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
	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_plugin_activation` VALUES ('AdminTools', 'install', '" . $db_version . "', '1');";

	return $query;
}