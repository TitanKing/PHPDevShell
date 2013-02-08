<?php

/////////////////////////////////////////////////////////////////////////////
// DEFAULT VALUES FOR SYSTEM USE ONLY ///////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
// DONT MODIFY THIS FILE, CREATE YOUR OWN OR MODIFY single-site.config.php //
/////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////
// Multi-database Configuration                                         ////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


$master_database = array(
	/**
	 * Database DSN (Data Source Name) string. Used for PDO based connections.
	 * @global string
	 */
	'dsn' => 'mysql:host=localhost;dbname=phpdev',

	/**
	 * Database Server Hostname. Not required if using PDO.
	 * @global string
	 */
	'host' => 'localhost',

	/**
	 * Database Name. Not required if using PDO.
	 * @global string
	 */
	'database' => 'phpdev',

	/**
	 * Database Server Username.
	 * @global string
	 */
	'username' => 'root',

	/**
	 * Database Server Password.
	 * @global string
	 */
	'password' => 'root',

	/**
	 * Default prefix to use in front of table names.
	 * @global string
	 */
	'prefix' => 'pds_',

	/**
	 * Whether the database connection should be persistent or not.
	 * @global string
	 */
	'persistent' => false,

	/**
	 * Database connection character set
	 * @global string
	 */
	'charset' => 'utf8'
);

$master = 'master_db';

/**
 * Specifies the master database settings.
 * @global string
 */
$configuration['master_database'] = $master;

$configuration['databases'] = array(
	$master => $master_database
);

////////////////////////////////////////////////////////////////////////////////////////////
// Extra Settings, these settings should be changed only when required.        /////////////
////////////////////////////////////////////////////////////////////////////////////////////

/**
 * When you experience a delay in views updating after changes, enable this to correct it.
 * Note disable this in production as it uses allot of memory.
 *
 * @global integer
 */
$configuration['force_views_compile'] = false;
/**
 * Enables views caching.
 * When triggered in view, the page will be static. Note this is aggressive caching and will not work on dynamic pages without proper configuration.
 * @global integer
 */
$configuration['views_cache'] = false;
/**
 * Views cache refresh intervals in seconds.
 * When enabled, this will rewrites views cache every som many seconds.
 * @global integer
 */
$configuration['views_cache_lifetime'] = 360;
/**
 * Cache type, PHPDS currently support main cache systems, its very easy to write your own supporting class for your preferred cache system.
 * Currently PHPDevShell support three types of cache systems, 'PHPDS_sessionCache' and 'PHPDS_memCache'.
 * For no cache please use = 'noCache'
 * The cache names is relative the the class names in includes/cache and more custom cache engine can be added.
 * @global string
 */
$configuration['cache_type'] = 'PHPDS_sessionCache';
/**
 * Cache refresh intervals in seconds.
 * Helps with overall performance of your system. The higher the value the less queries will be done, but your settings will be slower to update.
 * @global integer
 */
$configuration['cache_refresh_intervals'] = 120;
/**
 * Memcache/APC server details.
 * Only complete this when you are using the memcache extention, this is not needed for file based caching.
 * Copy and paste cache server block to create more then one server.
 * @global mixed
 */
$cache_server = 1;
$configuration['cache_host'] = array($cache_server => 'localhost');
$configuration['cache_port'] = array($cache_server => 11211);
$configuration['cache_persistent'] = array($cache_server => true);
$configuration['cache_weight'] = array($cache_server => 1);
$configuration['cache_timeout'] = array($cache_server => 1);
$configuration['cache_retry_interval'] = array($cache_server => 15);
$configuration['cache_status'] = array($cache_server => true);
/**
 * If you are running a very large site, you might want to consider running a dedicated light http server (httpdlight, nginx) that
 * only serves static content like images and static files, call it a CDN if you like.
 * By adding a host here 'http://192.34.22.33/project/cdn', all images etc, of PHPDevShell will be loaded from this address.
 * NO TRAILING SLASH
 * @global string
 */
$configuration['static_content_host'] = '';
/**
 * If you have a website tracking, analytics or affiliate script you may add it here, it will be added at the end of the body tag.
 * @global string
 */
$configuration['footer_js'] = <<<JS
	<!-- Ending Javascript -->
JS;
/**
 * Login session life.
 * This is how long the session will be remembered with each new login.
 * To disable, create session life as 0.
 * @global integer
 */
$configuration['session_life'] = 1800;
/**
 * Sets the temp session data save path, false to use default.
 * (Needs to be writable)
 * @global string $configuration['session_path']
 */
$configuration['session_path'] = 'write/session/';
/**
 * Views compile path.
 * (Needs to be writable)
 * @global string $configuration['compile_path']
 */
$configuration['compile_path'] = 'write/compile/';
/**
 * Views cache path.
 * (Needs to be writable)
 * @global string $configuration['cache_path']
 */
$configuration['cache_path'] = 'write/cache/';
/**
 * Temporary writable folder path.
 * (Needs to be writable)
 * @global string $configuration['tmp_path']
 */
$configuration['tmp_path'] = 'write/tmp/';
/**
 * Files uploading folder path.
 * (Needs to be writable)
 * @global string $configuration['upload_path']
 */
$configuration['upload_path'] = 'write/upload/';
/**
 * Force system down bypass.
 * If your session expired while system was set to down/maintenance in the config gui, you can gain login access again by setting this option true.
 * @global boolean $configuration['system_down_bypass']
 */
$configuration['system_down_bypass'] = false;
/**
 * If true $lang variables will also be converted to constants.
 * @global boolean $configuration['constant_conversion']
 */
$configuration['constant_conversion'] = false;
/**
 * Select extra functions to load in engine. Functions in these files will always be available.
 * Example : utils.php
 * @global array
 */
$configuration['function_files'] = array();
/**
 * Default charset to use - note this is php htmlentities coding, not PDO's or mysql's
 * @see  http://www.php.net/manual/en/function.htmlentities.php
 * @global string
 */
$configuration['charset'] = 'UTF-8';
/**
 * The ID of the main node, usually a dashboard
 * @global string
 */
$configuration['dashboard'] = 'readme';

////////////////////////////////////////////////////////////////////////////////////////////
// Debuggin Support. ///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

/**
 * When your system goes to production, set this to TRUE to avoid informations leaks.
 * Will force compile on template engine.
 *
 * Overrides most 'debug' and 'error' settings
 *
 * @var boolean
 */
$configuration['production'] = true;

/**
 * Enable Debugging.
 *
 * @global boolean
 */
$configuration['debug']['enable'] = false;

/**
 * Debug domains filter to include in debugging output, domains must be listed here for the messages to appear.
 * This will control what to monitor indepently on how the message will be delivered (see below).
 * Example:
 * $configuration['debug']['domains'] = array('core', 'db', 'navigation', 'security', 'template', 'user', '!');
 * There is a special domain: exclamation mark ('!') which refers to the low-level skel
 * Note that you can use star ('*') as a wildcard
 *
 * @global array
 */
$configuration['debug']['domains'] = array('core', 'db', 'navigation', 'security', 'template', 'user', '!');

/**
 * Debug level.
 * DEBUG = 4;
 * INFO = 3;
 * WARN = 2;
 * ERROR = 1;
 * LOG = 0;
 *
 * @global integer
 */
$configuration['debug']['level'] = 0;


// Error settings
/**
 * Use FirePHP as debugging platform.
 * Overriden by 'production' = true.
 *
 * @global boolean
 */
$configuration['error']['firePHP'] = false;

/**
 * Do server debugging logs.
 * Overriden by 'production' = true.
 * Default: true (recommended)
 *
 * @global boolean
 */
$configuration['error']['serverlog'] = true;

/**
 * To what directory should errors file logs be written to.
 * (Needs to be writable)
 *
 * @global string
 */
$configuration['error']['file_log_dir'] = 'write/logs/';

/**
 * To what email should critical errors be emailed to, make sure php can send mail, this does not use the PHPDevShell mailing engine.
 *
 * @global string
 */
$configuration['error']['email_critical'] = '';

/**
 * Should messages be shown onscreen in the web browser?
 * Note that messages generated before the View is created will be outputed in a very raw manner.
 * Overriden by 'production' = true.
 * Default: true
 *
 * @global boolean
 */
$configuration['error']['display'] = true;

/**
 * Ignore notices?
 * If this is true, the error handler will NOT handle notices, which may lead to your site being broken
 * Default: false (recommended)
 *
 * @global boolean
 */
$configuration['error']['ignore_notices'] = false;

/**
 * Ignore warnings?
 * If this is true, the error handler will NOT handle warnings, which may lead to your site being broken
 * Default: false (recommended)
 *
 * @global boolean
 */
$configuration['error']['ignore_warnings'] = false;

/**
 * If true, a warning will be handled as an exception (and stops the cycle if not handled)
 * Default: true (recommended)
 *
 * @global boolean
 */
$configuration['error']['warningsAreFatal'] = true;

/**
 * If true, a notice will be handled as an exception (and stops the cycle if not handled)
 * If both this and 'ignore_notices' are false, and 'display' is true, an inline notice message will be included in the page
 * Default: false (recommended)
 *
 * @global boolean
 */
$configuration['error']['noticesAreFatal'] = false;

/**
 * Set error handler reporting.
 *
 * @global string
 */
$configuration['error']['mask'] = E_ALL | E_STRICT; //  you should change to  E_ALL | E_STRICT to be clean


/**
 * Enable some development-related features.
 * 1. Change this to true if you would like to set the theme to use the normal css and javascript instead of minified.
 *
 * @global boolean
 */
$configuration['development'] = false;

/**
 * This is all the settings that will be available in $configuration['value'] loaded from database.
 * In general this would never be changed, however a developer might need to add their own variables they would need on every page.
 */
$configuration['preloaded_settings'] = array(
	'scripts_name_version',
	'redirect_login',
	'footer_notes',
	'front_page_id',
	'front_page_id_out',
	'front_page_id_in',
	'loginandout',
	'custom_logo',
	'custom_css',
	'system_down',
	'demo_mode',
	'charset_format',
	'locale_format',
	'charset',
	'language',
	'debug_language',
	'region',
	'root_id',
	'root_role',
	'root_group',
	'force_core_changes',
	'system_logging',
	'access_logging',
	'crypt_key',
	'date_format',
	'date_format_short',
	'default_template',
	'default_template_id',
	'printable_template',
	'split_results',
	'guest_role',
	'guest_group',
	'system_timezone',
	'setting_admin_email',
	'email_critical',
	'sef_url',
	'queries_count',
	'allow_registration',
	'registration_page',
	'allow_remember',
	'url_append',
	'skin',
	'meta_keywords',
	'meta_description',
	'menu_behaviour',
	'spam_assassin',
	'custom_css'
);
