<?php

$HTMl = '';
$type = 'service';

require ('service.inc.php');
$TITLE = "$package Installer/Upgrader";

// Store everything in cache...
preparations();

/// main switch
headHTML();

displayIntro();

// end html
footHTML();

function displayIntro()
{
	global $package, $version;

	?>
		<div class="row">
			<div class="span6">
				<h2>Fresh install</h2>
				<p><strong>If you want a fresh install of PHPDevShell</strong>, click "Install New Copy".
				    The installer will check your system for minimal requirements and install the database tables.
				    To enforce that only the owner of the site can continue with the installation, you will be asked several parameters found in the configuration file.
				</p>
				<p><button onClick="parent.location='install.php'" value="install" class="btn btn-large btn-success">Install New Copy</button></p>
				<div class="alert alert-info">
					<strong><a href="http://www.host1plus.com" style="padding: 3px;" class="img_right">Host1Plus!</a></strong>
					Special thanks goes to our sponsoring hosting provider, who provides reliable VPS/Cloud solutions and is a vivid pro open-source company.
                    We can honestly and unbiased recommend their services.
				</div>
			</div>
			<div class="span6">
				<h2>Upgrade existing</h2>
				<p><strong>If you already have a previous version of PHPDevShell installed</strong> and want to update to the new version <?php echo $version ?>, click "Upgrade Existing Installation".
				The upgrade script will run a series of upgrade SQL commands and do changes to multiple tables making it compatible with latest version of PHPDevShell codebase.
				</p>
				<p><button onClick="parent.location='install.php'" value="upgrade" class="btn btn-large btn-primary">Upgrade Existing Installation</button></p>
                <div class="alert alert-warning">
                    <strong>NOTE!</strong> PHPDevShell is fully licensed and protected under the <a href="http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html">GNU/LGPL</a> license. By installing or upgrading this software you automatically agree to its license. Copyright (C) 2013 Jason Schoeman - Reserves All Rights
                </div>
			</div>
		</div>
	<?php
}