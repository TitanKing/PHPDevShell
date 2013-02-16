<?php

class PHPDS_core extends PHPDS_dependant
{
	/**
	 * Contains controller content.
	 * @var string
	 */
	public $data;
	/**
	 * Used as a bridge between controller to view data.
	 * @var mixed
	 */
	public $toView;
	/**
	 * This variable is used to activate a stop script command, it will be used to end a script immediately while still finishing compiling the template.
	 *
	 * Usage Example :
	 * <code>
	 * // This wil make the script stop (not PHPDevShell) while still finishing the template to the end.
	 * $this->haltController = array('type'=>'auth','message'=>'The script stopped cause I wanted it to.');
	 * </code>
	 *
	 * @var array
	 */
	public $haltController;
	/**
	 * Signs the request as an ajax request.
	 * @var boolean
	 */
	public $ajaxType = null;
	/**
	 * The node structure that should be used "theme.php" for normal theme.
	 * @var string
	 */
	public $themeFile;
	/**
	 * Name of the theme folder to use
	 * @since v3.1.2
	 * @var string
	 */
	public $themeName;
	/**
	 * Use this to have global available variables throughout scripts. For instance in hooks.
	 *
	 * @var array
	 */
	public $skipLogin = false;

	/**
	 * Execute theme structure.
	 */
	public function setDefaultNodeParams()
	{
		$configuration = $this->configuration;
		$navigation = $this->navigation->navigation;

		$current_node = $navigation[$configuration['m']];

		if (! empty($current_node['node_id'])) {
			// Determine correct node theme.
			switch ($current_node['node_type']) {
				// HTML Widget.
				case 9:
					$this->themeFile = 'widget.php';
					$this->ajaxType = false;
					break;
				// HTML Ajax.
				case 10:
					$this->themeFile = 'ajax.php';
					$this->ajaxType = false;
				// HTML Ajax Lightbox.
				case 11:
					$this->themeFile = 'lightbox.php';
					$this->ajaxType = false;
					break;
				// Raw Ajax (json,xml,etc).
				case 12:
					$this->themeFile = '';
					$this->ajaxType = true;
					break;
				default:
					$this->ajaxType = PU_isAJAX();
					$this->themeFile = $this->ajaxType ? '' :  'theme.php';
					break;
			}
		} else {
			$this->themeFile = 'theme.php';
			$this->ajaxType = false;
		}
		if (!empty($this->themeFile)) {
			$this->loadMods();
		}
	}

	/**
	 * Load mods (html snippets for specific theme.)
	 * Creates object under $this->mod->...
	 * @date 20120227 V 1.0
	 * @author Jason Scheoman
	 */
	public function loadMods()
	{
		$configuration = $this->configuration;
		$template_dir = 'themes/' . $configuration['template_folder'] . '/';

		if (file_exists($template_dir . 'mods.php')) {
			include_once $template_dir . 'mods.php';
			if (class_exists($configuration['template_folder'])) {
				$this->template->mod = $this->factory($configuration['template_folder']);
			} else {
				$this->template->mod = $this->factory('themeMods');
			}
		} else {
			include_once 'themes/default/mods.php';
			$this->template->mod = $this->factory('themeMods');
		}
	}

	/**
	 * Loads and merges theme with controller.
	 * @date 20120227 V 1.0
	 * @author Jason Scheoman
	 */
	public function loadTheme()
	{
		$configuration = $this->configuration;
		$template_dir = 'themes/' . $configuration['template_folder'] . '/';

		if (! empty($this->themeName)) {
			$configuration['template_folder'] = $this->themeName;
			$template_dir = 'themes/' . $configuration['template_folder'] . '/';
		}

		try {
			ob_start();
			$result = $this->loadFile($template_dir . $this->themeFile, false, true, true, true);
			if (false === $result) {
				$result = $this->loadFile('themes/default/' . $this->themeFile, false, true, true, true);
			}
			if (false === $result) {
				throw new PHPDS_exception('Unable to find the custom template "' . $this->themeFile . '" in directory "' . $template_dir . '"');
			}
			ob_end_flush();
		} catch (Exception $e) {
			PU_cleanBuffers();
			throw $e;
		}
	}

	/**
	 * Run default, custom or no template.
	 *
	 * @version 2.1.1
	 *
	 * @date 20120920 (v2.1.1) (greg) fixed a typo with $url
	 * @date 20120312 (v2.1) (greg) added loggin of access errors (404 and such)
	 * @date 20120223 (v2.0) (jason) rewrite
	 * @date 20110308 (v1.2) (greg) allows the new style controller to alter the current template to be used
	 * @date20100520 (v1.1) (greg) added merging with modules from the configuration array
	 *
	 * @author Jason Schoeman
	 */
	public function startController ()
	{
		$this->setDefaultNodeParams();
		try {

			ob_start();
			$this->db->startTransaction();
			$this->executeController();
			$this->db->endTransaction();

			if (empty($this->data)) {
				$this->data = ob_get_clean();
			} else {
				PU_cleanBuffers();
			}
		} catch (Exception $e) {
			PU_cleanBuffers();
			$this->themeFile = '';

			if (is_a($e, 'PHPDS_accessException')) {
				$logger = $this->factory('PHPDS_debug', 'PHPDS_accessException');
				$url = $this->configuration['absolute_url'].$_SERVER['REQUEST_URI'];

				switch ($e->HTTPcode) {
					case 401:
						if (!PU_isAJAX()) {
							$this->themeFile = 'login.php';
						}
						PU_silentHeader("HTTP/1.1 401 Unauthorized");
						PU_silentHeader("Status: 401");
						$logger->error('URL unauthorized: '.$url, '401');
					break;
					case 404:
						if (!PU_isAJAX()) {
							$this->themeFile = '404.php';
						}
						PU_silentHeader("HTTP/1.1 404 Not Found");
						PU_silentHeader("Status: 404");
						$logger->error('URL not found: '.$url, '404');
					break;
					case 403:
						if (!PU_isAJAX()) {
							$this->themeFile = '403.php';
						}
						PU_silentHeader("HTTP/1.1 403 Forbidden");
						PU_silentHeader("Status: 403");
						$logger->error('URL forbidden '.$url, '403');
					break;
					case 418:
						sleep(30); // don't make spambot life live in the fast lane
						if (!PU_isAJAX()) {
							$this->themeFile = '418.php';
						}
						PU_silentHeader("HTTP/1.1 418 I'm a teapot and you're a spambot");
						PU_silentHeader("Status: 418");
						$logger->error('Spambot for '.$url, '418');
					break;
					default:
						throw $e;
				}

			} else throw $e;
		}
		// Only if we need a theme.
		if (! empty($this->themeFile)) {
			$this->loadTheme();
		} else {
			print $this->data;
		}
	}

	/**
	 * Executes the controller.
	 *
	 * @author Jason Schoeman
	 */
	public function executeController()
	{
		$navigation = $this->navigation->navigation;
		$configuration = $this->configuration;

		// Are we in demo mode?
		/**
		 * @todo Find a better place for this! Its not cool here.
		 */
		/*
		if ($configuration['demo_mode'] == true) {
			if ($configuration['user_role'] != $configuration['root_role']) {
				// Show demo mode message for end user.
				$this->template->notice(sprintf(___('%s is currently in a demo mode state, no actual database transactions will occur.'), $configuration['scripts_name_version']));
			} else {
				// Show demo mode message for root user.
				$this->template->notice(sprintf(___('%s is currently in a demo mode state, only Root users are able to save database transactions.'), $configuration['scripts_name_version']));
			}
		}
		 *
		 */

		// Node Types:
		// 1. Standard Page from Plugin
		// 2. Link to Existing Node
		// 3. Jump to Existing Node
		// 4. Simple Place Holder Link
		// 5. Load External File
		// 6. External HTTP URL
		// 7. iFrame (Very old fashioned)
		// 8. Automatic Cronjob
		// 9. HTML Ajax Widget (Serves as module inside web page)
		// 10. HTML Ajax (Used for ajax)
		// 11. HTML Ajax Lightbox (Floats overtop of web page)
		// 12. Raw Ajax (json, xml, etc.)
		// Load script to buffer.
		if (! empty($navigation[$configuration['m']]['node_id'])) {
			// We need to assign active node_id.
			$node_id = $configuration['m'];
			// Determine correct node action.
			switch ($navigation[$configuration['m']]['node_type']) {
				// Plugin File.
				case 1:
					$node_case = 1;
					break;
				// Link.
				case 2:
					break;
				// Jump.
				case 3:
					break;
				// External File.
				case 4:
					$node_case = 4;
					break;
				// HTTP URL.
				case 5:
					$node_case = 5;
					break;
				// Placeholder.
				case 6:
					break;
				// iFrame.
				case 7:
					$node_case = 7;
					break;
				// Cronjob.
				case 8:
					$node_case = 8;
					break;
				// HTML Widget.
				case 9:
					$node_case = 9;
					break;
				// HTML Ajax.
				case 10:
					$node_case = 10;
				// HTML Ajax Lightbox.
				case 11:
					$node_case = 11;
					break;
				// Raw Ajax (json,xml,etc).
				case 12:
					$node_case = 12;
					break;
				default:
					// Do case.
					$node_case = 1;
					break;
			}
			///////////////////////////////////
			// Do further checking on links. //
			///////////////////////////////////
			if (empty($node_case)) {
				// So we have some kind of link, we now need to see what kind of link we have.
				// Get node extended data.
				$extend = $navigation[$node_id]['extend'];
				// Get node type.
				if (!empty($navigation[$extend]['node_type'])) {
					$linked_node_type = $navigation[$extend]['node_type'];
				} else {
					throw new PHPDS_extendNodeException(array($navigation[$configuration['m']]['node_id'], $extend));
				}
				// We now have the linked node type and can now work accordingly.
				// Determine correct node action.
				switch ($linked_node_type) {
					// Plugin File.
					case 1:
						$node_case = 1;
						$node_id = $extend;
						break;
					// Link.
					case 2:
						$node_case = 2;
						$node_id = $this->navigation->extendNodeLoop($navigation[$extend]['extend']);
						break;
					// Jump.
					case 3:
						$node_case = 2;
						$node_id = $this->navigation->extendNodeLoop($navigation[$extend]['extend']);
						break;
					// External File.
					case 4:
						$node_case = 4;
						$node_id = $extend;
						break;
					// HTTP URL.
					case 5:
						$node_case = 5;
						$node_id = $extend;
						break;
					// Placeholder.
					case 6:
						$node_case = 2;
						$node_id = $this->navigation->extendNodeLoop($navigation[$extend]['extend']);
						break;
					// iFrame.
					case 7:
						$node_case = 7;
						$node_id = $extend;
						break;
					// Cronjob.
					case 8:
						$node_case = 8;
						$node_id = $extend;
						break;
					// HTML Ajax Widget.
					case 9:
						$node_case = 9;
						$node_id = $extend;
						break;
					// HTML Ajax.
					case 10:
						$node_case = 10;
						$node_id = $extend;
						break;
					// HTML Ajax Lightbox.
					case 11:
						$node_case = 11;
						$node_id = $extend;
						break;
					// Raw Ajax.
					case 12:
						$node_case = 12;
						$node_id = $extend;
						break;
					default:
						$node_case = 1;
						$node_id = $extend;
						break;
				}
			}
			// Execute repeated node cases.
			switch ($node_case) {
				// Plugin Script.
				case 1:
					$this->loadControllerFile($node_id);
					break;
				// Link, Jump, Placeholder.
				case 2:
					// Is this an empty node item?
					if (empty($node_id)) {
						// Lets take user to the front page as last option.
						// Get correct frontpage id.
						($this->user->isLoggedIn()) ? $node_id = $configuration['front_page_id_in'] : $node_id = $configuration['front_page_id'];
					}
					$this->loadControllerFile($node_id);
					break;
				// External File.
				case 4:
					// Require external file.
					if (!$this->loadFile($navigation[$node_id]['node_link'])) {
						throw new PHPDS_exception(sprintf(___('File could not be found after trying to execute filename : %s'), $navigation[$node_id]['node_link']));
					}
					break;
				// HTTP URL.
				case 5:
					// Redirect to external http url.
					$this->ok(sprintf(___('You are now being redirected to an external url, %s'), $navigation[$node_id]['node_link']), false, false);
					$this->navigation->redirect($navigation[$node_id]['node_link']);
					break;
				// iFrame.
				case 7:
					// Clean up height.
					$height = preg_replace('/px/i', '', $navigation[$node_id]['extend']);
					// Create Iframe.
					$this->data = $this->template->mod->iFrame($navigation[$node_id]['node_link'], $height, '100%');
					break;
				// Cronjob.
				case 8:
					// Require script.
					if (!$this->loadControllerFile($node_id)) {
						$time_now = time();
						// Update last execution.
						$this->db->invokeQuery('TEMPLATE_cronExecutionLogQuery', $time_now, $node_id);
						// Always log manual touched cronjobs.
						$this->template->ok(sprintf(___('Cronjob %s executed manually.'), $navigation[$node_id]['node_name']));
					}
					break;
				// HTML Ajax Widget.
				case 9:
					$this->loadControllerFile($node_id);
					break;
				// HTML Ajax.
				case 10:
					$this->loadControllerFile($node_id);
					break;
				// HTML Ajax Lightbox.
				case 11:
					$this->loadControllerFile($node_id);
					break;
				// HTML Ajax Lightbox.
				case 12:
					$this->loadControllerFile($node_id);
					break;
			}
		}

		if (isset($this->haltController)) {
			// Roll back current transaction.
			$this->db->invokeQuery('TEMPLATE_rollbackQuery');
			switch ($this->haltController['type']) {
				case 'auth':
					throw new PHPDS_securityException($this->haltController['message']);
				break;

				case '404':
					throw new PHPDS_pageException404($this->haltController['message'],$this->haltController['type']);
				break;

				case '403':
					throw new PHPDS_securityException403($this->haltController['message'],$this->haltController['type']);
				break;

				case '418':
					throw new PHPDS_pageException418($this->haltController['message'],$this->haltController['type']);
				break;

				default:
					throw new PHPDS_securityException($this->haltController['message'],$this->haltController['type']);
				break;
			}
		}
	}

	/**
	 * Will attempt to load controller file from various locations.
	 *
	 * @version 1.0.2
	 *
	 * @date 20100917 (v1.0) (Jason)
	 * @date 20110308 (v1.0.1) (greg) loadFile returns an exact false when the file is not found
	 * @date 20120606 (v1.0.2) (greg) add the "includes/" folder of the plugin in the include path
	 *
	 * @author Jason Schoeman
     *
	 * @param int $node_id
	 * @param mixed $include_model |boolean $include_model if set, load the model file before the controller is run (either a prefix or true for default "query" prefix) - default is not to
	 * @param mixed $include_view |boolean $include_view if set, run the view file after the controller is run (a prefix) ; default is the "view" prefix)
     *
     * @throws PHPDS_exception
     * @return string
	 */
	public function loadControllerFile ($node_id, $include_model = false, $include_view = 'view')
	{
		$navigation = $this->navigation->navigation;

		if (!empty($navigation[$node_id])) {
			$plugin_folder = $navigation[$node_id]['plugin_folder'];
			$old_include_path = PU_addIncludePath($plugin_folder.'/includes/');

			if ($include_model) {
				if ($include_model === true) $include_model = 'query';
				$this->loadFile($plugin_folder . 'models/' . preg_replace("/.php/", '.' . $include_model . '.php', $navigation[$node_id]['node_link']));
			}

			$active_dir = $plugin_folder . '%s' . $navigation[$node_id]['node_link'];
			$result_ = $this->loadFile(sprintf($active_dir, 'controllers/'));
			if ($result_ === false) {
				$result_ = $this->loadFile(sprintf($active_dir, ''));
			}

			if (is_string($result_) && class_exists($result_)) {
				$controller = $this->factory($result_);
				$controller->run();
			}

			// Load view class.
			if ($include_view && !empty($this->themeFile)) {
				$load_view = preg_replace("/.php/", '.' . $include_view . '.php', $navigation[$node_id]['node_link']);
				$view_result = $this->loadFile($plugin_folder . 'views/' . $load_view);
				if (is_string($view_result) && class_exists($view_result)) {
					$view = $this->factory($view_result);
					$view->run();
				}
			}
			set_include_path($old_include_path);
		}
		if ($result_ === false && empty($this->haltController)) {
			throw new PHPDS_exception(sprintf(___('The controller of node id %d could not be found after trying to execute filename : "%s"'), $node_id, sprintf($active_dir, '{controllers/}')));
		}
		return $result_;
	}

	/**
	 * Gets the correct location of a tpl file, will return full path, can be a view.tpl or view.tpl.php files.
	 *
	 * @param string $load_view
	 * @param string $plugin_override If another plugin is to be used in the directory.
     *
     * @return string
	 */
	public function getTpl($load_view='', $plugin_override='')
	{
		$configuration = $this->configuration;
		$navigation = $this->navigation;

		// Node link.
		if (empty($navigation->navigation[$configuration['m']]['extend'])) {
			$node_link = $navigation->navigation[$configuration['m']]['node_link'];
		} else {
			$node_link = $navigation->navigation[$navigation->navigation[$configuration['m']]['extend']]['node_link'];
			// Set plugin for this node item.
			$plugin_extend = $navigation->navigation[$navigation->navigation[$configuration['m']]['extend']]['plugin'];
		}
		// Do template engine.
		if (empty($plugin_override) && empty($plugin_extend)) {
			$plugin_folder = $configuration['absolute_path'] . 'plugins/' . $this->activePlugin() . '/';
		} else if (! empty($plugin_override)) {
			$plugin_folder = $configuration['absolute_path'] . 'plugins/' . $plugin_override . '/';
		} else if (! empty($plugin_extend)) {
			$plugin_folder = $configuration['absolute_path'] . 'plugins/' . $plugin_extend . '/';
		}

		// Do we have a custom template file?
		if (empty($load_view) && !empty($navigation->navigation[$configuration['m']]['layout'])) {
			$load_view = $navigation->navigation[$configuration['m']]['layout'];
		}

		// Check if we have a custom layout otherwise use default.
		if (empty($load_view)) {
			$tpl_dir = str_replace($node_link, '%s/' . str_replace('.php', '.tpl', $node_link), $plugin_folder . $node_link);
		} else {
			$link = strrchr($node_link, '/');
			if (empty($link)) {
				$tpl_dir = $plugin_folder . '%s/' . $load_view;
			} else {
				$link = str_replace($link, '', $node_link);
				$tpl_dir = $plugin_folder . '%s/' . $link . '/' . $load_view;
			}
		}

		// Log to firephp.
		$this->_log('Loading Template Layout : ' . $tpl_dir);

		// Return file location.
		if (file_exists(sprintf($tpl_dir, 'views'))) {
			$tpldir = sprintf($tpl_dir, 'views');
			return $tpldir;
		// A custom layout added.
		} else if (file_exists(sprintf($tpl_dir . '.tpl', 'views'))) {
			$tpldir = sprintf($tpl_dir, 'views') . '.tpl';
			return $tpldir;
		// Perhaps we have a php template.
		} else if (file_exists(sprintf($tpl_dir . '.php', 'views'))) {
			return sprintf($tpl_dir . '.php', 'views');
		} else {
			return '';
		}
	}

	/**
	 * Check and returns constant if constant is defined or returns normal variable if no constant defined.
	 *
	 * @param string The string to check whether variable or constant.
	 * @return string The actual assigned constant value.
	 * @author Jason Schoeman
	 */
	public function isConstant ($is_variable_constant)
	{
		if (defined($is_variable_constant)) {
			return constant($is_variable_constant);
		} else {
			return $is_variable_constant;
		}
	}

	/**
	 * This method will return the correct user time taking DST and users timezone into consideration.
	 *
	 * @param integer $timestamp Unix timestamp if empty it will return the current users time.
	 * @param string $format_type_or_custom User can choose which of the formats to load from the $this->configuration settings, 'default', 'short' or have a custom format.
	 * @param string $custom_timezone You can also provide a custom timezone to this method, if false, it will use current users timezone.
	 * @return string Will return a formatted date string ex. 1 June 2011 18:05 PM
	 * @author Jason Schoeman
	 *
	 * @version 1.0.1	Converted to OOP
	 * @date	2009/05/19
	 */
	public function formatTimeDate ($time_stamp, $format_type_or_custom = 'default', $custom_timezone = false)
	{
		$configuration = $this->configuration;
		// Check if we habe an empty time stamp.
		if (empty($time_stamp)) return false;
		// Check if we have a custom timezone.
		if (! empty($custom_timezone)) {
			$timezone = $custom_timezone;
		} else if (! empty($configuration['user_timezone'])) {
			$timezone = $configuration['user_timezone'];
		} else {
			$timezone = $configuration['system_timezone'];
		}

		if ($format_type_or_custom == 'default') {
			$format = $configuration['date_format'];
		} else if ($format_type_or_custom == 'short') {
			$format = $configuration['date_format_short'];
		} else {
			$format = $format_type_or_custom;
		}
		if (phpversion() < '5.2.0') return strftime('%c', $time_stamp);
		try {
			$ut = new DateTime(date('Y-m-d H:i:s', $time_stamp));
			$tz = new DateTimeZone($timezone);
			$ut->setTimezone($tz);
		} catch (Exception $e) {
			// Work around error from old database column.
			$configuration['user_timezone'] = $configuration['system_timezone'];
			return date(DATE_RFC822);
		}

		return $ut->format($format);
	}

	/* Returns the difference in seconds between the currently logged in user's timezone
	 * and the server's configured timezone (under General Settings). If the server
	 * timezone is 2 hours behind the user timezone, it will return -7200 for example. If
	 * the server timezone is 2 hours ahead of the user timezone, it will return 7200.
	 *
	 * @param integer $custom_timestamp Timestamp to compare dates timezones in the future or past.
	 * @return integer The difference between the user's timezone and server timezone (in seconds).
	 * @author Don Schoeman
	 */
	public function userServerTzDiff ($custom_timestamp = false)
	{
		$configuration = $this->configuration;
		if (empty($custom_timestamp)) {
			$timestamp = $configuration['time'];
		} else {
			$timestamp = $custom_timestamp;
		}
		if (phpversion() < '5.2.0')
			return 0;
		$ut = new DateTime(date('Y-m-d H:i:s', $timestamp));
		$tz = new DateTimeZone($configuration['user_timezone']);
		$ut->setTimezone($tz);
		$user_timezone_sec = $ut->format('Z');
		$tz = new DateTimeZone($configuration['system_timezone']);
		$ut->setTimezone($tz);
		$server_timezone_sec = $ut->format('Z');
		return $server_timezone_sec - $user_timezone_sec;
	}

	/**
	 * Function formats locale according to logged in user settings else will default to system.
	 *
	 * @param boolean $charset Whether the charset should be included in the format.
	 * @return string Will return formatted locale.
	 * @author Jason Schoeman
	 */
	public function formatLocale ($charset = true, $user_language = false, $user_region = false)
	{
		$configuration = $this->configuration;
		if (empty($configuration['charset_format'])) $configuration['charset_format'] = false;
		if (! empty($user_language)) $configuration['user_language'] = $user_language;
		if (! empty($user_region)) $configuration['user_region'] = $user_region;
		if (empty($configuration['user_language'])) $configuration['user_language'] = $configuration['language'];
		if (empty($configuration['user_region'])) $configuration['user_region'] = $configuration['region'];
		if ($charset && ! empty($configuration['charset_format'])) {
			$locale_format = preg_replace('/\{charset\}/', $configuration['charset_format'], $configuration['locale_format']);
			$locale_format = preg_replace('/\{lang\}/', $configuration['user_language'], $locale_format);
			$locale_format = preg_replace('/\{region\}/', $configuration['user_region'], $locale_format);
			$locale_format = preg_replace('/\{charset\}/', $configuration['charset'], $locale_format);
			return $locale_format;
		} else {
			$locale_format = preg_replace('/\{lang\}/', $configuration['user_language'], $configuration['locale_format']);
			$locale_format = preg_replace('/\{region\}/', $configuration['user_region'], $locale_format);
			$locale_format = preg_replace('/\{charset\}/', '', $locale_format);
			return $locale_format;
		}
	}

	/**
	 * This methods allows you to load translation by giving their locations and name.
	 *
	 * @param string This is the location where language mo file is found.
	 * @param string The mo filename the translation is compiled in.
	 * @param string The actual text domain identifier.
	 * @author Jason Schoeman
	 */
	public function loadTranslation ($mo_directory, $mo_filename, $textdomain)
	{
		$configuration = $this->configuration;
		$bindtextdomain = $configuration['absolute_path'] . $mo_directory;
		$loc_dir = $bindtextdomain . $configuration['locale_dir'] . '/LC_MESSAGES/' . $mo_filename;

		(file_exists($loc_dir)) ? $mo_ok = true : $mo_ok = false;
		if ($mo_ok) {
			$this->_log('Found Translation File : ' . $loc_dir);
			bindtextdomain($textdomain, $bindtextdomain);
			bind_textdomain_codeset($textdomain, $configuration['charset']);
			textdomain($textdomain);
		} else {
			$this->debugInstance()->warning('MISSING Translation File : ' . $loc_dir);
		}
	}

	/**
	 * This method loads the core language array and assigns it to a variable.
	 *
	 * @author Jason Schoeman
	 */
	public function loadCoreLanguage ()
	{
		$this->loadTranslation('language/', 'core.lang.mo', 'core.lang');
	}

	/**
	 * This method loads the default node language array and assigns it to a variable.
	 *
	 * @author Jason Schoeman
	 */
	public function loadNodeLanguage ()
	{
		// Lets loop the installed plugins.
		foreach ($this->db->pluginsInstalled as $installed_plugins_array) {
			$plugin_folder = $installed_plugins_array['plugin_folder'];
			$this->loadTranslation("plugins/$plugin_folder/language/", "$plugin_folder.mo", "$plugin_folder");
		}
	}

	/**
	 * This method loads the plugin language with default items and icons array.
	 *
	 * @author Jason Schoeman
	 */
	public function loadDefaultPluginLanguage ()
	{
		$active_plugin = $this->activePlugin();
		textdomain($active_plugin);
	}

	/**
	 * Function to return the current running/active plugin.
	 *
	 * @return string
	 */
	public function activePlugin ()
	{
		$navigation = $this->navigation;
		$configuration = $this->configuration;

		if (! empty($configuration['m']) && ! empty($navigation->navigation[$configuration['m']]['plugin'])) {
			return $navigation->navigation[$this->configuration['m']]['plugin'];
		} else {
			return 'AdminTools';
		}
	}

	/**
	 * Function to return the current running/active template.
	 *
	 * @return string
	 */
	public function activeTemplate ()
	{
		$settings = $this->configuration;
		$navigation = $this->navigation;
		if (! empty($navigation->navigation[$this->configuration['m']]['template_folder'])) {
			return $navigation->navigation[$this->configuration['m']]['template_folder'];
		} else {
			return $settings['default_template'];
		}
	}

	/**
	 * Convert string unsigned CRC32 value. This is unique and can help predict a entries id beforehand.
	 * Use for folder names insuring unique id's.
	 *
	 * @param string To convert to integer.
	 * @return integer
	 * @author Jason Schoeman
	 */
	public function nameToId ($convert_to_id)
	{
		return sprintf('%u', crc32($convert_to_id));
	}

	/**
	 * Turns any given relative path to the absolute version of the path.
	 * @param $relative_path Provide path like 'test/testpath'
	 * @return string
	 */
	public function absolutePath ($relative_path)
	{
		$absolute_path = $this->configuration['absolute_path'] . ltrim($relative_path, '/');
		return str_ireplace('//', '/', $absolute_path);
	}

	/**
	 * Assumes role of loading files.
	 *
	 * @date 20100106 (v1.1) (greg) moved from core to PHPDS_core and added a few checks
	 *
	 * @version 1.1
	 * @author jason
	 *
	 * @param string $file_location
	 * @param boolean Should the file be required or else included.
	 * @param boolean Is this a relative path, if true, it will be converted to absolute path.
	 * @param boolean Should it be called only once?
	 * @return mixed, whatever the file returned when executed or false if it couldn't be found
	 */
	public function loadFile($path, $required = false, $relative = true, $once_only = true, $from_template = false)
	{
		$core = $this->core;
		if ($from_template) $template = $this->template;
		$configuration = $this->configuration;
		$navigation = $this->navigation;
		$db = $this->db;
		$security = $this->security;

		if (empty($path)) throw new PHPDS_exception('Trying to load a file with an empty path.');

		if ($relative) $path = $configuration['absolute_path'] . $path;

		$this->log('Loading : ' . $path);

		// switch the domain to "user" so the developer can filter to see only its own output
		$debug = $this->debugInstance()->domain('user');

		$result = false;

		if (file_exists($path)) {
			if ($required) {
				if (! empty($once_only)) $result = require_once ($path); else $result = require ($path);
			} else {
				if (! empty($once_only)) $result = include_once ($path); else $result = include ($path);
			}
		} else {
			if ($required) throw new PHPDS_exception('Trying to load a non-existant file: "'.$path.'"');
		}

		// revert to the "core" domain since we're out of the developer's code
		$this->debugInstance()->domain('core');

		return $result;
	}

		/**
	 * Strip a string from the end of a string.
	 * Is there no such function in PHP?
	 *
	 * @param string $str      The input string.
	 * @param string $remove   OPTIONAL string to remove.
	 * @deprecated use PU_rightTrim instead
	 * @return string the modified string.
	 */
	public function rightTrim ($str, $remove = null)
	{
		return PU_rightTrim($str, $remove);
	}

	/**
	 * This method simply renames a string to safe unix standards.
	 *
	 * @param string $name
	 * @param string $replace Replace odd characters with what?
	 * @deprecated use PU_safeName instead
	 * @return string
	 */
	public function safeName ($name, $replace = '-')
	{
		return PU_safeName($name, $replace);
	}

	/**
	 * Replaces accents with plain text for a given string.
	 * @deprecated use PU_replaceAccents instead
	 * @param string $string
	 */
	public function replaceAccents($string)
	{
		return PU_replaceAccents($string);
	}

	/**
	 * This method creates a random string with mixed alphabetic characters.
	 *
	 * @param integer $length The lenght the string should be.
	 * @param boolean $uppercase_only Should the string be uppercase.
	 * @deprecated use PU_createRandomString instead
	 * @return string Will return required random string.
	 * @author Andy Shellam, andy [at] andycc [dot] net
	 */
	public function createRandomString ($length = 4, $uppercase_only = false)
	{
		return PU_createRandomString($length, $uppercase_only);
	}

	/**
	 * This is a handy little function to strip out a string between two specified pieces of text.
	 * This could be used to parse XML text, bbCode, or any other delimited code/text for that matter.
	 * Can also return all text with replaced string between tags.
	 *
	 * @param string $string
	 * @param string $start
	 * @param string $end
	 * @param string $replace Use %s to be replaced with the string between tags.
	 * @deprecated use PU_SearchAndReplaceBetween instead
	 * @return string
	 */
	public function SearchAndReplaceBetween ($string, $start, $end, $replace = '', $replace_char='%')
	{
		return PU_SearchAndReplaceBetween($string, $start, $end, $replace, $replace_char);
	}

		/**
	 * This creates a simple confirmation box to ask users input before performing a critical link click.
	 *
	 * @param string What is the question to be asked in the confirmation box.
	 * @return string Javascript popup confirmation box.
	 * @author Jason Schoeman
	 * @deprecated
	 */
	public function confirmLink ($confirm_what)
	{
		return $this->template->confirmLink($confirm_what);
	}

	/**
	 * This creates a simple confirmation box to ask users input before performing a critical submit.
	 *
	 * @param string What is the question to be asked in the confirmation box.
	 * @return string Javascript popup confirmation box.
	 * @author Jason Schoeman
	 * @deprecated use $this->template instead
	 */
	public function confirmSubmit ($confirm_what)
	{
		return $this->template->confirmSubmit($confirm_what);
	}

	/**
	 * This shows a simple "alert" box which notifies the user about a specified condition.
	 *
	 * @param string The actual warning message.
	 * @return string Javascript popup warning box.
	 * @author Don Schoeman
	 * @deprecated use $this->template instead
	 */
	public function alertSubmit ($alert_msg)
	{
		return $this->template->alertSubmit($alert_msg);
	}

	/**
	 * This shows a simple "alert" box which notifies the user about a specified condition.
	 *
	 * @param string The actual warning message.
	 * @return string Javascript popup warning box.
	 * @author Don Schoeman
	 * @deprecated use $this->template instead
	 */
	public function alertLink($alert_msg)
	{
		return $this->template->alertSubmit($alert_msg);
	}

	/**
	 * Method is used to wrap the gettext international language conversion tool inside PHPDevShell.
	 * Converts text to use gettext PO system.
	 *
	 * @param string $say_what The string required to output or convert.
	 * @param string $domain Override textdomain that should be looked under for this text string.
	 * @return string Will return converted string or same string if not available.
	 * @author Jason Schoeman
	 */
	public function __ ($say_what, $domain = false)
	{
		return __ ($say_what, $domain);
	}

	/**
	 * Will log current configuration data to firephp.
	 * @return void
	 */
	public function logConfig ()
	{
		$this->_log((array) $this->configuration);
	}



	public function mangleCharset($charset)
	{
		$configuration = $this->configuration;

		$charsetList = !empty($configuration['charsetList']) ? $configuration['charsetList'] :
			array(
					'utf8' => 'UTF-8',
					'latin1' => 'ISO-8859-1',
					'latin5' => 'ISO-8859-5',
					'big5' => 'BIG5',
					'koi8r' => 'KOI8-R',
					'macroman' => 'MacRoman',
					'sjis' => 'Shift_JIS',

					'UTF-8' => 'utf8',
					'ISO-8859-1' => 'latin1',
					'ISO-8859-5' => 'latin5',
					'BIG5' => 'big5',
					'KOI8-R' => 'koir8r',
					'MacRoman' => 'macroman',
					'Shift_JIS' => 'sjis'
			);
		return empty($charsetList[$charset]) ? null : $charsetList[$charset];
	}

}
