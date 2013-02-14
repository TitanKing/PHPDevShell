<?php

$type = 'install';
require ('service.inc.php');

$TITLE = "$package Installer";
$HTMl = '';

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
		headingPrint("Install Stage Two");
		doStage2();
		break;
	default:
		headingPrint("Install Stage One");
		doStage1();
		break;
}

// end html
footHTML();

function displaySuccess()
{
	global $aurl;
	okPrint("The installation was successful.");
	notePrint("You might want to erase the /service directory and <a href=\"{$aurl}\">check your new installation.</a>");
}

function displayFields()
{
    global $data, $doit;
	okPrint(_('<i class="icon-ok"></i> Your server meets all the installation requirements.'));
    if ($doit == false) warningPrint('<i class="icon-warning-sign"></i> Installer is in developer mode, set $doit = true.');

	?>
	<form action="install.php?stage=2" method="post" class="validate">
		<h2>Configuration Information</h2>
		<p class="text-warning">
            The following information <strong>should match</strong> the data found in the
            <strong>configuration file</strong> before the installation can start:
        </p>
		<div class="row">
			<div class="span4">
				<fieldset>
					<legend>Configuration File</legend>
					<?php
					displayField('Configuration File', 'config_file');
					?>
				</fieldset>
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
					<legend>Application Information</legend>
					<?php
					displayField('Application Name', 'application_name');
					?>
				</fieldset>
				<fieldset>
					<legend>Admin User Information</legend>
					<?php
					displayField('Admin User Name', 'admin_username');
					displayField('Admin password', 'admin_password');
					displayField('Admin Email', 'admin_email');
					?>
				</fieldset>
			</div>
			<div class="span4">
				<fieldset>
					<legend>Action</legend>
					<p>
                        <label for="sample-data" class="checkbox">
                        <input id="sample-data" type="checkbox" name="sample-data"> Install sample data
                        </label><br>
						<button type="submit" name="step1" value="step1" class="btn btn-primary">Continue Install</button>
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

	checkField('application_name', _('Please supply the Application Name.'), _('Your Application Name V-1.0.0'));
	checkField('admin_username', _('Please supply the Admin Login Username.'), _('root'));
	checkField('admin_password', _('Please supply the Admin Login Password.'), _('root'));
	checkField('admin_email', _('Please supply the Admin Email.'), _('root@mydomain.com'));
	checkField('db_name', _('Please supply the Database Name.'), 'phpdevdbname');
	checkField('db_username', _('Please supply the Database Username.'), 'phpdev');
	checkField('db_password', _('Please supply the Database Password.'), 'phpdev');
	checkField('db_server', _('Please supply the Database Server Address.'), 'localhost');
	checkField('db_prefix', _('Please supply the Database Prefix.'), 'pds_');
	checkField('config_file', _('Please supply the Config File.'), 'single-site.config.php');

	if (filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL) == FALSE)
			addError('admin_email', _('Your email address seems to be invalid. Please make sure you entered your email address correctly.'));

	return (count($errors) == 0);
}

function get_queries()
{
	global $data, $db_version, $time;

	$fp = fopen('PHPDevShell-db' . $db_version . '-complete.sql', 'r');
	$queries = stream_get_contents($fp);
	fclose($fp);

    if (!empty($_POST['sample-data'])) {
        $fp = fopen('PHPDevShell-db-sample.sql', 'r');
        $queries .= stream_get_contents($fp);
        fclose($fp);
    }

	$queries = preg_replace('/pds_core_/', $data['db_prefix'] . 'core_', $queries);
	$query = explode(';', $queries);
	array_pop($query);
	$admin_password = md5($data['admin_password']);
	$crypt_key = create_random_string(30);

	// Other queries.
	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_settings` VALUES ('PHPDevShell_crypt_key', '" . $crypt_key . "', '');";

	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_settings` VALUES ('PHPDevShell_from_email', '" . $data['admin_email'] . "', '');";
	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_settings` VALUES ('PHPDevShell_scripts_name_version', '" . $data['application_name'] . "', '');";

	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_settings` VALUES ('PHPDevShell_setting_admin_email', '" . $data['admin_email'] . "', '');";
	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_users` VALUES ('1', 'Root User', '" . $data['admin_username'] . "', '" . $admin_password . "', '" . $data['admin_email'] . "', '1', '1', '" . $time . "', 'en', 'UTC', 'US');";

	// Update version.
	$query[] = 'REPLACE INTO `' . $data['db_prefix'] . "core_plugin_activation` VALUES ('AdminTools', 'install', '" . $db_version . "', '1');";

	return $query;
}