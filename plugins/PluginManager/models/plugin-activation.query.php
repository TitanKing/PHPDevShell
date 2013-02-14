<?php

/**
 * Plugin Activation - Current classes available.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_availableClassesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			class_name, plugin_folder
		FROM
			_db_core_plugin_classes
		WHERE
			enable = 1
		ORDER BY
			rank
		ASC
	";

	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$cr = parent::invoke();
		$class = array();
		// Loop and assign available class names.
		foreach ($cr as $cr_) {
			$class[$cr_['class_name']] = $cr_['plugin_folder'];
		}

		return $class;
	}
}

/**
 * Plugin Activation - Current Plugin Status
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_currentPluginStatusQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			plugin_folder, status, version, use_logo
		FROM
			_db_core_plugin_activation
	";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$plugin_record_db = parent::invoke();

		// Compile results and save into array to compare against user selected options and already installed plugins.
		if (empty($plugin_record_db)) $plugin_record_db = array();
		foreach ($plugin_record_db as $plugin_record_array) {
			$activation_db[$plugin_record_array['plugin_folder']] = array('status' => $plugin_record_array['status'], 'version' => $plugin_record_array['version'], 'use_logo' => $plugin_record_array['use_logo']);
		}
		if (!empty($activation_db)) {
			return $activation_db;
		} else {
			return array();
		}
	}
}

/**
 * Plugin Activation - Check if url has update.
 * @author Jason Schoeman, Contact: titan@phpdevshell.org
 *
 */
class PHPDS_updateCheckPluginsQuery extends PHPDS_query
{
	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$template = $this->template;
		$security = $this->security;
		$db = $this->db;

		if (isset($security->post['latest_version'])) {
			// Assign version check url.
			$version_url = $security->post['versionurl'];
			$current = $security->post['current'];
			$version_check_xml['plugin'] = $security->post['plugin'];
			$version_xml = @simplexml_load_file($version_url);
			// Check if ftp is enabled.
			$settings = $db->getSettings('ftp_enable', 'AdminTools');

			// Execute plugin method.
			if (! empty($version_xml)) {
				$version_check_xml['latest'] = (string) $version_xml->version['latest'];
				$version_check_xml['version'] = (string) $version_xml->version;
				$version_check_xml['note'] = (string) $version_xml->note;
				$version_check_xml['download'] = (string) $version_xml->download;

				// Check if we have a newer version available.
				if ($version_check_xml['latest'] > $current) {
					$download_url = sprintf(__('<a href="%s" class="generic_button">' . __('Download Latest Version') . '</a>'), $version_check_xml['download']);
					$update_message = $template->warning(sprintf(__('%s %s is available, upgrade recommended.<br><i>%s</i> (%s)'), $version_check_xml['plugin'], $version_check_xml['version'], $version_check_xml['note'], $download_url), true);
					if (! empty($settings['ftp_enable'])) {
						if ($version_check_xml['plugin'] != 'AdminTools') {
							$aau = __('Attempt Automatic Upgrade');
							$update_message .= <<<HTML
								<button type="submit" name="auto_upgrade" value="auto_upgrade"><span class="save"></span><span>$aau</span></button>
HTML;
							$update_message .= $template->notice(sprintf(__('Attempt to upgrade %s %s (%s) automatically?'), $version_check_xml['plugin'], $version_check_xml['version'], $version_check_xml['download']), true);
							if (!empty($version_check_xml['download'])) {
								$update_message .= '<input type="hidden" name="download_url" value="' . $version_check_xml['download'] . '">';
							}
						}
					} else {
						$update_message .= $template->notice(sprintf(__('First enable FTP to use automatic upgrade for %s %s (%s)'), $version_check_xml['plugin'], $version_check_xml['version'], $version_check_xml['download']), true);
					}
				} else {
					$update_message = $template->ok(sprintf(__('<strong>%s %s is already latest version.</strong>'), $version_check_xml['plugin'], $version_check_xml['version']), true);
				}
			} else {
				// If we cannot find XML output error.
				$update_message = $template->warning(sprintf(__('Could not access url for version checking %s, looked using url "%s". Please try again later.'), $version_check_xml['plugin'], $version_url), true);
			}
			// Set final update message.
			$update_message_[$version_check_xml['plugin']] = $update_message;

			if (! empty($update_message_)) {
				return  $update_message_;
			} else {
				return array();
			}
		}
	}
}

/**
 * Plugin Activation - Read plugin directories
 * @author Jason Schoeman, Contact: titan@phpdevshell.org
 *
 */
class PHPDS_readPluginsQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;
		$security = $this->security;
		$db = $this->db;
		$configuration = $this->configuration;

		$active_template = $core->activeTemplate();

		$activation_db = $parameters[0];
		$self_url = $navigation->selfUrl();

		// Load available and installed classes.
		$installed_classes = $db->invokeQuery('PHPDS_availableClassesQuery');

		// Check update url.
		$update_message_ = $db->invokeQuery('PHPDS_updateCheckPluginsQuery');

		////////////////////////////////
		// Plugin lookup starts here. //
		////////////////////////////////
		$directory = $configuration['absolute_path'] . '/plugins';
		$level_deduct = substr_count($directory . '/', '/') - 1;
		$original_base = $directory;
		$base = $directory . '/';
		$subdirectories = opendir($base);
		$level = substr_count($base, '/') - ($level_deduct);

		// Icons
		$lang_available_icon = $template->icon('locale', __('Translation file available.'));
		$lang_not_available_icon = $template->icon('script--exclamation', __('Translation file missing.'));
		$set_logo_icon = $template->icon('tag-label-green');
		$set_default_logo_icon = $template->icon('tag-label-green', __('Selected as Default Logo'));
		$uninstall_icon = $template->icon('plug--minus');
		$upgrade_icon = $template->icon('database--exclamation');
		$upgrade_core_icon = $template->icon('lightning--exclamation');
		$latest_version_icon = $template->icon('globe-network-ethernet');
		$reinstall_icon = $template->icon('arrow-circle-315');
		$install_icon = $template->icon('plug-disconnect');
		$install_missing_icon = $template->icon('plug--exclamation', __('Install file missing.'));

		// Status icons.
		$inactive_default_icon = '<img src="' . $configuration['absolute_url'] . "/themes/" . $active_template . "/images/plugin-disabled.png" . '" alt="Not Installed" title="Not Installed" />';
		$active_default_icon = '<img src="' . $configuration['absolute_url'] . "/themes/" . $active_template . "/images/plugin-installed.png" . '" alt="Installed" title="Installed" />';
		$broken_default_icon = '<img src="' . $configuration['absolute_url'] . "/themes/" . $active_template . "/images/plugin-broken.png" . '" alt="Installed" title="Broken" />';
		$upgrade_default_icon = '<img src="' . $configuration['absolute_url'] . "/themes/" . $active_template . "/images/plugin-upgrade.png" . '" alt="Upgrade" title="Upgrade" />';

		// Set.
		$alt = false;
		$RESULTS = false;
		// Loop through plugins.
		while (false !== ($object = readdir($subdirectories))) {
			$path = $base . $object;
			if (($object != '.') && ($object != '..') && ($object != '.svn')) {
				// Unset previous configuration files.
				$plugin = array();
				$install_ok = false;
				$plugin_message = false;
				$action = false;
				$show_part1 = false;
				$depends_on = false;
				$unique_dependency = array();
				$dependecy = array();
				$dependencies_array = array();
				$classes_array = array();
				$dependencies_not_met = false;
				$version_check = false;
				$dependencies_not_met_uninstall = false;
				$class_call = '';

				if ($level == 1 && !preg_match('/index.php/', $object)) {
					// Read Config Language File.
					if (file_exists("plugins/$object/language/gettext.lang.php")) {
						$plugin_lang_message = $lang_available_icon;
					} else {
						$plugin_lang_message = $lang_not_available_icon;
					}
					// Get logo.
					if (file_exists("plugins/$object/images/logo.png")) {
						$logo = '<img src="plugins/' . $object . '/images/logo.png" border="0" alt="' . $object . '" title="' . $object . '">';
						$set_logo_button = '<button type="submit" value="set_logo" name="set_logo" title="' . __('Set Default Logo') . '">' . $set_logo_icon . '</button>';
					} else {
						$logo = $object;
						$set_logo_button = false;
					}
					// See if the active plugin logo is selected.
					if (!empty($activation_db[$object]['use_logo']) && $activation_db[$object]['use_logo'] == 1) {
						$logo_selected = $set_default_logo_icon;
						$set_logo_button = false;
					} else {
						$logo_selected = false;
					}
					// Read Config Installation File.
					if (file_exists("plugins/$object/config/plugin.config.xml")) {
						$plugin_config_message = $template->icon('puzzle', sprintf(__('Installation file in %s found.'), $object));
						$xml = simplexml_load_file("plugins/$object/config/plugin.config.xml");
						$install_ok = true;
					} else {
						$plugin_config_message = $template->icon('puzzle--exclamation', sprintf(__('Installation file in %s NOT found.'), $object));
						$status_icon = $broken_default_icon;
					}
					// Check if item hide is a core script.
					if ($install_ok == true) {
						// Call required database version.
						$plugin['database_version'] = (int) $xml->install['version'];
						$plugin['name'] = (string) $xml->name;
						$plugin['version'] = (string) $xml->version;
						$plugin['description'] = (string) $xml->description;
						$plugin['versionurl'] = (string) $xml->versionurl;
						$plugin['current'] = (string) $xml->versionurl['current'];
						$plugin['founder'] = (string) $xml->founder;
						$plugin['author'] = (string) $xml->author;
						$plugin['email'] = (string) $xml->email;
						$plugin['homepage'] = (string) $xml->homepage;
						$plugin['date'] = (string) $xml->date;
						$plugin['copyright'] = (string) $xml->copyright;
						$plugin['license'] = (string) $xml->license;
						$plugin['info'] = (string) $xml->info;
						// Get required dependencies.
						$dependencies_array = $xml->install->dependencies[0];
						// Set.
						$depends_on = '';
						// Do we have dependencies for this plugin?
						if (!empty($dependencies_array)) {
							// Lets find out what plugins this plugin depends on.
							foreach ($dependencies_array as $dependecy) {
								// Assign plugin name.
								$pl = (string) $dependecy['plugin'];
								$cl = (string) $dependecy['class'];
								// Create unique items only.
								if (empty($unique_dependency[$cl])) {
									// Next we need to check what is installed and what not.
									if (!empty($installed_classes[$cl])) {
										$depends_on .= $template->ok(sprintf(__('Found class call -> (%s) from plugin -> (%s)'), $cl, $installed_classes[$cl]), true, false);
										$unique_dependency[$cl] = true;
									} else {
										$depends_on .= $template->warning(sprintf(__('Missing class call -> (%s) from plugin -> (%s)'), $cl, $pl), true, false);
										$unique_dependency[$cl] = true;
										if (empty($activation_db[$object])) {
											$dependencies_not_met = true;
										} else {
											$dependencies_not_met_uninstall = true;
										}
									}
								}
							}
						} else {
							$depends_on .= $template->ok(__('Standalone'), true, false);
							// Set.
							$dependencies_not_met = false;
						}
						// Get available classes.
						$classes_array = $xml->install->classes[0];
						// Do we have classes for this plugin?
						if (!empty($classes_array)) {
							foreach ($classes_array as $class) {
								// Assign class values.
								$name_c = (string) $class['name'];
								$alias_c = (string) $class['alias'];
								$plugin_c = (string) $class['plugin'];
								$class_call .= "\$this->factory('$name_c') || \$this->factory('$alias_c')<br>";
							}
						}
						// We need to find out if there are any updates available for this plugin.
						if (!empty($dependencies_not_met)) {
							$plugin_message = $template->warning(__('Dependency not met!'), true, false);
							$status_icon = $broken_default_icon;
						} else if ($dependencies_not_met_uninstall == true) {
							$plugin_message = $template->warning(__('Dependency not met!'), true, false);
							$status_icon = $broken_default_icon;
							$uninstall_text = __('Uninstall Plugin');
							// Check if it is core plugin.
							($object != 'AdminTools') ? $uninstall_button = '<button type="submit" value="uninstall" name="uninstall" title="' . $uninstall_text . '">' . $uninstall_icon . '</button>' : $uninstall_button = false;

							$action = <<<ACTION
								$uninstall_button
								<input type="hidden" name="plugin" value="$object">
								<input type="hidden" name="version" value="{$plugin['database_version']}">
ACTION;
						}// We need to find out if there are any updates available for this plugin.
						else if (!empty($activation_db[$object]['status']) && ($plugin['database_version'] > $activation_db[$object]['version'])) {
							$plugin_message = $template->notice(sprintf(__('Upgrade (%s) Available'), "DB{$plugin['database_version']}"), true);
							$status_icon = $upgrade_default_icon;
							// Set.
							$upgrade_core_text = __('Upgrade Core');
							$upgrade_core_url = $configuration['absolute_url'] . '/other/service/upgrade.php';
							$upgrade_text = __('Upgrade Database');
							if ($object == 'AdminTools') {
								$action = <<<ACTION
									<button type="button" name="upgrade" onClick="parent.location='$upgrade_core_url'" title="$upgrade_core_text">{$upgrade_core_icon}</button>
ACTION;
							} else {
								$action = <<<ACTION
									<button type="submit" value="upgrade" name="upgrade" title="$upgrade_text">{$upgrade_icon}</button>
									<input type="hidden" name="plugin" value="$object">
									<input type="hidden" name="version" value="{$activation_db[$object]['version']}">
ACTION;
							}
						} // Now we check what queries this plugin has access to.
						else if (!empty($activation_db[$object]['status']) && $activation_db[$object]['status'] == 'install') {
							$plugin_message = $template->ok(__('Installed'), true, false);
							if (file_exists("plugins/$object/images/plugin.png")) {
								$status_icon = '<img src="' . $configuration['absolute_url'] . "/plugins/$object/images/plugin.png" . '" alt="Installed" title="Installed" />';;
							} else {
								$status_icon = $active_default_icon;
							}
							$uninstall_text = __('Uninstall Plugin');
							// Check if it is core plugin.
							($object != 'AdminTools') ? $uninstall_button = '<button type="submit" value="uninstall" name="uninstall" title="' . $uninstall_text . '">' . $uninstall_icon . '</button>' : $uninstall_button = false;
							// Check if we have a version check url.
							if (!empty($plugin['versionurl']) && !empty($plugin['current'])) {
								// Create version check button.
								$latest_version_title = __('Check latest available version');
								$version_check = <<<VERSION
									<button type="submit" value="latest_version" name="latest_version" title="$latest_version_title">{$latest_version_icon}</button>
									<input type="hidden" name="versionurl" value="{$plugin['versionurl']}">
									<input type="hidden" name="current" value="{$plugin['current']}">
VERSION;
							} else {
								$version_check = false;
							}
							// Set.
							if (!empty($update_message_[$object])) {
								$update_message_object = $update_message_[$object];
							} else {
								$update_message_object = false;
							}

							// Set.
							$reinstall_nodes_text = __('Reinstall Nodes and Items');
							$action = <<<ACTION
								$update_message_object
								$uninstall_button
								<button type="submit" value="reinstall" name="reinstall" title="$reinstall_nodes_text">{$reinstall_icon}</button>
								$version_check
								<input type="hidden" name="plugin" value="$object">
								<input type="hidden" name="version" value="{$plugin['database_version']}">
								$set_logo_button
ACTION;
						} else if (empty($activation_db[$object]['status'])) {
							$status_icon = $inactive_default_icon;
							$plugin_message = $template->notice(__('Inactive'), true, false);
							// Set.
							$install_plugin_text = __('Install Plugin');
							$action = <<<ACTION
								<button type="submit" value="install" name="install" title="$install_plugin_text">{$install_icon}</button>
								<input type="hidden" name="plugin" value="$object">
ACTION;
						}
						// Input forms HTML (Checkbox).
						$show_part1 = "<form action=\"{$self_url}#$object\" method=\"post\">" . $action . "</form>";
					} else {
						$show_part1 = $install_missing_icon;
					}
					// Set.
					if (!empty($activation_db[$object]['version'])) {
						$activation_db_version = $activation_db[$object]['version'];
					} else {
						$activation_db_version = false;
					}
					$RESULTS[] = array(
						'plugin' => $plugin,
						'status_icon' => $status_icon,
						'object' => $object,
						'class' => $class_call,
						'show_part1' => $show_part1,
						'status' => $plugin_message,
						'logo' => $logo,
						'logo_selected' => $logo_selected,
						'depends_on' => $depends_on,
						'plugin_lang_message' => $plugin_lang_message,
						'plugin_config_message' => $plugin_config_message,
						'activation_db_version' => $activation_db_version
					);
				} else {
					continue;
				}
			}
		}
		// Close directories.
		closedir($subdirectories);
		if (!empty($RESULTS)) {
			return $RESULTS;
		} else {
			return array();
		}
	}
}
