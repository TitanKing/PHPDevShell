<?php

/**
 * Manages plugin relations.
 *
 */
class pluginManager extends PHPDS_dependant
{
	protected $plugin;
	protected $action;
	protected $nodeStructure;
	protected $node;
	protected $pluginUpgraded;
	public $log;

	/**
	 * Runs all necesary queries to enter a plugin and its node items into the database.
	 *
	 * @param string Folder and unique name where plugin is copied.
	 * @param string Action taken to manipulate plugin.
	 * @param string Version of the current installed plugin.
	 */
	public function setPlugin ($plugin_folder, $action, $version = false)
	{
		// Include global variables.
		$configuration = $this->configuration;
		$db = $this->db;
		// First include the configuration file for processing.
		$xml = simplexml_load_file($configuration['absolute_path'] . "/plugins/$plugin_folder/config/plugin.config.xml");
		// Set plugin array.
		$this->plugin = $xml;
		$this->action = $action;
		// Extra class required.
		$this->nodeStructure = $this->factory('nodeStructure');
		/////////////////////////////////////////////////////////
		///////////////////// INSTALL ///////////////////////////
		/////////////////////////////////////////////////////////
		// Create actions that needs to be taken.
		// Complete Install
		if ($action == 'install') {
			// Install node items to database.
			$this->installNodes($plugin_folder);
			// Install settings.
			$this->installSettings($plugin_folder);
			// Install classes.
			$this->installClasses($plugin_folder);
			// For failed queries try to remove old database queries first.
			$this->uninstallQueries($plugin_folder);
			// Install custom database query.
			$this->installQueries($plugin_folder);
			// Write installed plugin.
			$this->installVersion($plugin_folder, 'install');
			// Write the node structure.
			$this->nodeStructure->writeNodeStructure();
			// Clear old cache.
			$db->cacheClear();
		} else //////////////////////////////////////////////////
		///////////////////// UNINSTALL /////////////////////////
		/////////////////////////////////////////////////////////
		// Complete Uninstall
		if ($action == 'uninstall') {
			// Remove node items from database.
			$this->uninstallNodes($plugin_folder);
			// Remove plugin settings if any.
			$this->uninstallSettings($plugin_folder);
			// Remove plugin classes if any.
			$this->uninstallClasses($plugin_folder);
			// Remove database.
			$this->uninstallQueries($plugin_folder);
			// Remove plugin from registry.
			$this->uninstallVersion($plugin_folder);
			// Write the node structure.
			$this->nodeStructure->writeNodeStructure();
			// Clear old cache.
			$db->cacheClear();
		} else //////////////////////////////////////////////////
		///////////////////// REINSTALL /////////////////////////
		/////////////////////////////////////////////////////////
		// Complete Re-Install Node Items.
		if ($action == 'reinstall') {
			// Install node items to database.
			$this->installNodes($plugin_folder, true);
			// Before we install anything lets first clear old hooks to this plugin.
			// Write the node structure.
			$this->nodeStructure->writeNodeStructure();
			// Clear old Cache.
			$db->cacheClear();
		} else //////////////////////////////////////////////////
		///////////////////// UPGRADE ///////////////////////////
		/////////////////////////////////////////////////////////
		// Complete Upgrade
		if ($action == 'upgrade') {
			// Upgrade custom database query.
			$this->upgradeQueries($plugin_folder, $version);
			// Install node items to database.
			$this->installNodes($plugin_folder, true);
			// Before we install anything lets first clear old classes to this plugin.
			$this->uninstallClasses($plugin_folder);
			// Install classes.
			$this->installClasses($plugin_folder);
			// Write database string.
			$this->upgradeDatabase($plugin_folder, 'install');
			// Write the node structure.
			$this->nodeStructure->writeNodeStructure();
			// Clear old Cache.
			$db->cacheClear();
		} else //////////////////////////////////////////////////
		///////////////////// SET LOGO //////////////////////////
		/////////////////////////////////////////////////////////
		// Set applications logo to plugin logo.
		if ($action == 'set_logo') {
			// Set the default logo to this plugins logo.
			$this->pluginSetLogo($plugin_folder);
			// Clear old cache.
			$db->cacheClear('plugins_installed');
		} else //////////////////////////////////////////////////
		///////////////////// AUTO UPGRADE //////////////////////
		/////////////////////////////////////////////////////////
		// Set applications logo to plugin logo.
		if ($action == 'auto_upgrade') {
			// Set the default logo to this plugins logo.
			$this->pluginAutoUpgrade($plugin_folder);
		}
	}

	/**
	 * This private method is used to attemp copying the newly downloaded plugin.
	 * @param $plugin_folder
	 * @return void
	 */
	private function pluginAutoUpgrade($plugin_folder)
	{
		$security = $this->security;
		$template = $this->template;
		$configuration = $this->configuration;

		// Initiate filemanager.
		$filemanager = $this->factory('fileManager');
		$a_path = $configuration['absolute_path'];

		// Set download url.
		$download_url = $security->post['download_url'];

		// Check if we have the upgrade url.
		if (empty($download_url)) {
			// First warning...
			$template->warning(__('The download URL is empty, cannot do anything now.','AdminTools'));
			return false;
		} else {
			try {
				// Good we have a download url, lets attempt to download file.
				$tmp_zip = $a_path . $configuration['tmp_path'] . $plugin_folder . '.zip';
				$tmp_folder = $a_path . $configuration['tmp_path'];
				$url_exists = @fopen($download_url,'r');
				if ($url_exists !== false) {
					$downloaded_to = $filemanager->downloadFile($download_url, $tmp_zip);
					$template->ok(sprintf(__('Success, file was downloaded to %s', 'AdminTools'), $downloaded_to));
					// So far so good, next lets attemt to extract our zip.
					if ($filemanager->zipExtract($downloaded_to, $tmp_folder)) {
						$template->ok(sprintf(__('Success, file was extracted to %s', 'AdminTools'), $tmp_folder . $plugin_folder));
						// Now we need to connect to the ftp server and try and copy the data over.
						if ($ftp_resource = $filemanager->establishFtp()) {
							$template->ok(__('Success, FTP server accepted connection.', 'AdminTools'));
							// Can we goto the root folder.
							if (ftp_size($ftp_resource, 'includes/PHPDS.inc.php') > 2) {
								$template->template->ok(sprintf(__('Success, FTP servers working root directory is correct : %s'), ftp_pwd($ftp_resource)));

								// Ok lets start to copy.
								if ($filemanager->ftpRcopy($ftp_resource, $configuration['tmp_path'] . $plugin_folder, 'plugins/' . $plugin_folder)) {
									$dir_c = '';
									foreach ($filemanager->uploadHistory as $directories_copied) {
										$dir_c .= '<br />' . $directories_copied['from'] . ' --> ' . $directories_copied['to'];
									}
									$template->ok(sprintf(__('The required files have been copied from %s to %s. Please check if there are any database upgrades available for %s.%s', 'AdminTools'), $configuration['tmp_path'] . $plugin_folder, 'plugins', $plugin_folder, $dir_c));
								}

								// Last but not least, delete temp install files.
								if (@unlink($a_path . $configuration['tmp_path'] . $plugin_folder . '.zip') && $filemanager->deleteDir($a_path . $configuration['tmp_path'] . $plugin_folder)) {
									$template->ok(__('Temporary install directories and files deleted.', 'AdminTools'));
								} else {
									$template->warning(__('Temporary install directories and files could NOT be deleted.', 'AdminTools'));
								}
							} else {
								$template->template->notice(sprintf(__('FTP connection was ok, but the root directory listing failed. Working directory is %s which is wrong.'), ftp_pwd($ftp_resource)));
								return false;
							}
						} else {
							$template->warning(__('Failed connecting to the local FTP server.', 'AdminTools'));
							return false;
						}
					} else {
						$template->warning(sprintf(__('Could not extract %s.', 'AdminTools'), $tmp_zip));
						return false;
					}
				} else {
					$template->warning(sprintf(__('Could not download file to %s, make sure the file (%s) exists and is readable.', 'AdminTools'), $tmp_zip, $download_url));
					return false;
				}
			} catch (Exception $e) {
				$template->error($e->getMessage());
			}
		}
	}

	/**
	 * Runs all necesary queries to install a plugins nodes.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 */
	private function installNodes ($plugin_folder, $update=false)
	{
		$core = $this->core;
		$template = $this->template;
		$configuration = $this->configuration;
		$navigation = $this->navigation;
		$db = $this->db;
		$security = $this->security;

		// Get default and empty template id's.
		$templates_array = $db->getSettings(array('default_template_id' , 'empty_template_id'), 'AdminTools');
		// Assign nodes q to install.
		$nodes_array = $this->plugin->install->nodes;
		// Define.
		$last_node_template_insert = false;
		// Execute installation of node items.
		if (! empty($nodes_array)) {
			$this->installNodesDigger($nodes_array);
			$nodes_array = $this->node;
			if (count($nodes_array) > 0) {
				// Insert new node items into database.
				foreach ($nodes_array as $ranking => $node) {

					// Create node link.
					$node_link = (string) $node['link'];

					// Provide your own node id... not a problem.
					if (empty($node['nodeid'])) {
						// Create master node_id.
						$node_id = $this->nodeStructure->createNodeId($plugin_folder, $node_link);
					} else {
						$node_id = $node['nodeid'];
					}

					// Check if node is not just an update, we dont want to override custom settings.
					if ($update == true) {
						if ($db->invokeQuery('PHPDS_doesNodeExist', $node_id)) {
							// Before we continue, update the link incase it changed.
							$db->invokeQuery('PHPDS_updateNodeLink', $node_link, $node_id);

							$this->log .= $template->ok(sprintf(__("Checked/Updated node for %s with id (%s) and linked to %s.", 'AdminTools'), $plugin_folder, $node_id, $node_link), 'return');
							continue;
						}
					}
					// Check if node item should be installed to other node structure or if it is using different plugin.
					if (! empty($node['plugin'])) {
						$parent_plugin_folder = $node['plugin'];
					} else if (! empty($node['pluginauto'])) {
						$parent_plugin_folder = $node['pluginauto'];
					} else {
						$parent_plugin_folder = $plugin_folder;
					}
					// Compile parent node id from user input.
					if (! empty($node['parentlink'])) {
						// Create parent node id.
						$parent_id = $this->nodeStructure->createNodeId($parent_plugin_folder, $node['parentlink']);
					} else if (! empty($node['parentlinkauto'])) {
						if (empty($node['parentnodeid'])) {
							// Try compiling node from auto parent link.
							$parent_id = $this->nodeStructure->createNodeId($parent_plugin_folder, $node['parentlinkauto']);
						} else if (! empty($node['parentnodeid'])) {
							$parent_id = $node['parentnodeid'];
						} else {
							$parent_id = '0';
						}
					} else {
						// This must be a root item then.
						$parent_id = '0';
					}
					// Link to original plugin item or parent plugin item script.
					if (! empty($node['symlink'])) {
						$extend_to = $this->nodeStructure->createNodeId($parent_plugin_folder, $node['symlink']);
						if (empty($navigation->navigation["{$extend_to}"]))
							$extend_to = $this->nodeStructure->createNodeId($plugin_folder, $node['symlink']);
					} else {
						$extend_to = '';
					}

					// Create custom node name.
					$node_name = trim($security->sqlWatchdog($node['name']));

					// Create node type.
					if (! empty($node['type'])) {
						$node_type = (int) $node['type'];
					} else {
						$node_type = 1;
					}
					// Create sef alias.
					if (! empty($node_type)) {
						(! empty($node_name)) ? $node_name_ = $node_name : $node_name_ = $node_link;
						$alias = $core->safeName($navigation->determineNodeName($node['alias'], $core->replaceAccents($node_name_), $node_id));
					} else {
						$alias = false;
					}
					// What type of node extention should be used.
					switch ($node['type']) {
						case 2:
							$extend = $extend_to;
							break;
						case 3:
							$extend = $extend_to;
							break;
						case 6:
							$extend = $extend_to;
							break;
						case 7:
							$extend = (string) $node['height'];
							break;
						default:
							$extend = false;
							break;
					}
					// Should item be opened in new window.
					if (! empty($node['newwindow'])) {
						$new_window = (int) $node['newwindow'];
					} else {
						$new_window = 0;
					}
					// How should items be ranked.
					if (! empty($node['rank'])) {
						if ($node['rank'] == 'last') {
							$last_rank = $db->invokeQuery('PHPDS_readMaxNodesRankQuery');

							$rank = $last_rank + 1;
						} else if ($node['rank'] == 'first') {
							$last_rank = $db->invokeQuery('PHPDS_readMinNodesRankQuery');

							$rank = $last_rank - 1;
						} else {
							$rank = (int) $node['rank'];
						}
					} else {
						$rank = $ranking;
					}
					// How should item be hide.
					if (! empty($node['hide'])) {
						$hide = (int) $node['hide'];
					} else {
						$hide = 0;
					}
					// Create a template id or use default.
					if ($node['template'] == 'empty') {
						$node_template_insert = $templates_array['empty_template_id'];
					} else if (empty($node['template'])) {
						$node_template_insert = $templates_array['default_template_id'];
					} else {
						// Get a unique id.
						$node_template_insert = $core->nameToId($node['template']);
						// Check if item needs to be created.
						if ($last_node_template_insert != $node_template_insert) {
							// Create a new node item...
							if ($db->invokeQuery('PHPDS_createTemplateQuery', $node_template_insert, $node['template'])) {
								// Show execution.
								$template->ok(sprintf(__("Installed new template for %s.", 'AdminTools'), $node['template']));
							}
							// Assign so we dont create it twice.
							$last_node_template_insert = $node_template_insert;
						}
					}
					// Create template layout.
					if (! empty($node['layout'])) {
						$layout = (string) $node['layout'];
					} else {
						$layout = '';
					}

					// Params
					if (! empty($node['params'])) {
						$params = (string) $node['params'];
					} else {
						$params = '';
					}
					////////////////////////////////
					// Role Permissions.
					// Now we need to delete old values, if any, to prevent duplicates.
					// Delete Node Permissions.
					$db->invokeQuery('PHPDS_deleteRolePermissionsPluginQuery', $node_id, $configuration['user_role']);

					// Check if we should add_permission.
					if (empty($node['noautopermission'])) {
						// INSERT Node Permissions.
						$db->invokeQuery('PHPDS_writeRolePermissionsPluginQuery', $configuration['user_role'], $node_id);
					}
					////////////////////////////////
					// Save new item to database.
					// Although it is not my style doing queries inside a loop,
					// this situation is very unique and I think the only way getting the parent nodes id.
					if ($db->invokeQuery('PHPDS_writeNodePluginQuery', $node_id, $parent_id, $node_name, $node_link, $plugin_folder, $node_type, $extend, $new_window, $rank, $hide, $node_template_insert, $alias, $layout, $params)) {
						// Show execution.
						$this->log .= $template->ok(sprintf(__("Installed node for %s with id (%s) and linked to %s.", 'AdminTools'), $plugin_folder, $node_id, $node_link), 'return');
					}
				}
				// For safety unset node properties.
				unset($node);
			}
		}
	}

	/*
	 * This class support installNodes providing a way of digging deeper into the node structure to locate child node items.
	 *
	 * @param $child Object containing XML node tree.
	 */
	private function installNodesDigger ($child)
	{
		// Looping through all XML node items while compiling values.
		foreach ($child->children() as $children) {
			// Create node values for each node item.
			$m['parentnodeid'] = (string) $child['nodeid'];
			$m['nodeid'] = (string) $children['nodeid'];
			$m['parentlinkauto'] = (string) $child['link'];
			$m['parentlink'] = (string) $children['parentlink'];
			$m['alias'] = (string) $children['alias'];
			$m['link'] = (string) $children['link'];
			$m['symlink'] = (string) $children['symlink'];
			$m['name'] = (string) $children['name'];
			$m['plugin'] = (string) $children['plugin'];
			$m['pluginauto'] = (string) $child['pluginauto'];
			$m['template'] = (string) $children['template'];
			$m['layout'] = (string) $children['layout'];
			$m['height'] = (string) $children['height'];
			$m['rank'] = (string) $children['rank'];
			$m['params'] = (string) $children['params'];
			$m['type'] = (int) $children['type'];
			$m['newwindow'] = (int) $children['newwindow'];
			$m['hide'] = (int) $children['hide'];
			$m['noautopermission'] = (int) $children['noautopermission'];
			// Assign node values.
			$this->node[] = array('nodeid'=>$m['nodeid'] , 'parentnodeid'=>$m['parentnodeid'] , 'parentlinkauto' => $m['parentlinkauto'] , 'parentlink' => $m['parentlink'] , 'alias' => $m['alias'] , 'link' => $m['link'] , 'symlink' => $m['symlink'] , 'name' => $m['name'] , 'pluginauto' => $m['pluginauto'] , 'plugin' => $m['plugin'] , 'template' => $m['template'] , 'layout' => $m['layout'] , 'height' => $m['height'] , 'type' => $m['type'] , 'newwindow' => $m['newwindow'] , 'rank' => $m['rank'] , 'hide' => $m['hide'] , 'noautopermission' => $m['noautopermission'] , 'params' => $m['params']);
			// Recall for children node items.
			$this->installNodesDigger($children);
		}
	}

	/**
	 * Install requested settings to database.
	 *
	 * @param string $plugin_folder
	 */
	private function installSettings ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// Assign settings q to install.
		$settings_array = $this->plugin->install->settings->setting;
		// Check if settings exists.
		if ($settings_array) {
			// Loop through all settings.
			foreach ($settings_array as $setting_array) {
				// Assign setting as string.
				$param = (string) $setting_array['write'];
				if (! empty($setting_array['note'])) {
					$note = (string) $setting_array['note'];
				} else {
					$note = '';
				}
				$setting = (string) $setting_array;
				// Assign settings array.
				$param_write[$param] = $setting;
				$notes[$param] = $note;
			}
			// Make sure setting is not empty.
			if (isset($param_write)) {
				// Finally write setting.
				if ($db->writeSettings($param_write, $plugin_folder, $notes)) // Show execution.
				$this->log .= $template->ok(sprintf(__("Installed settings for %s.", 'AdminTools'), $plugin_folder), 'return');
			}
		}
	}

	/**
	 * Install requested classes to database.
	 *
	 * @param string $plugin_folder
	 */
	private function installClasses ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// Assign settings q to install.
		$classes_array = $this->plugin->install->classes->class;
		if ($classes_array) {
			$db->invokeQuery('PHPDS_writeClassesQuery', $classes_array, $plugin_folder);
			// Show execution.
			$this->log .= $template->ok(sprintf(__("Installed classes for %s.", 'AdminTools'), $plugin_folder), 'return');
		}
	}

	/**
	 * Execute provided database query for plugin install.
	 *
	 * @param string Folder and unique name where plugin is installed.
	 */
	private function installQueries ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// Assign queries q.
		$queries_array = $this->plugin->install->queries->query;
		// Check if install query exists.
		if (! empty($queries_array)) {
			// Run all queries in database.
			foreach ($queries_array as $query_array) {
				// Assign query as string.
				$query_array = (string) trim($query_array);
				// Make sure query is not empty.
				if (! empty($query_array)) {
					// Execute query.
					$db->invokeQuery('PHPDS_doQuery', $query_array);
					// Show execution.
					$this->log .= $template->ok(sprintf(__("Executed query for %s : %s.", 'AdminTools'), $plugin_folder, $query_array), 'return');
				}
			}
		}
	}

	/**
	 * Install new plugin database version information.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 * @param string Last status required from plugin manager.
	 */
	private function installVersion ($plugin_folder, $status)
	{
		$db = $this->db;
		$template = $this->template;
		// Set version.
		$version = (int) $this->plugin->install['version'];
		$version_human = (string) $this->plugin->version;
		// Do we have a version?
		if (! empty($version)) {
			// Replace in database.
			if ($db->invokeQuery('PHPDS_writePluginVersionQuery', $plugin_folder, $status, $version)) {
				// Show execution.
				$template->ok(sprintf(__("Installed plugin %s-V-%s.", 'AdminTools'), $plugin_folder, $version_human));
			}
		}
	}

	/**
	 * Runs all necesary queries to uninstall a plugins nodes from the database.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 */
	private function uninstallNodes ($plugin_folder, $delete_critical_only = false)
	{
		$db = $this->db;
		$template = $this->template;
		if (! empty($plugin_folder)) {
			if ($this->nodeStructure->deleteNode(false, $plugin_folder, $delete_critical_only)) // Show execution.
			$this->log .= $template->ok(sprintf(__("Uninstall nodes for %s.", 'AdminTools'), $plugin_folder), 'return');
		}
	}

	/**
	 * Uninstall classes for a specific plugin from database.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 */
	public function uninstallClasses ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// Delete all hooks for this plugin.
		if ($db->invokeQuery('PHPDS_deleteClassesQuery', $plugin_folder)) {
			// Show execution.
			$this->log .= $template->ok(sprintf(__("Uninstalled classes for %s.", 'AdminTools'), $plugin_folder), 'return');
		}
	}

	/**
	 * Uninstall all settings from database for specific plugin.
	 *
	 * @param string $plugin_folder
	 */
	public function uninstallSettings ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// Remove plugin settings if any.
		if ($db->deleteSettings('*', $plugin_folder)) // Show execution.
			$this->log .= $template->ok(sprintf(__("Uninstalled settings for %s.", 'AdminTools'), $plugin_folder), 'return');
	}

	/**
	 * Execute provided database query for plugin uninstall.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 */
	private function uninstallQueries ($plugin_folder = false)
	{
		$db = $this->db;
		$template = $this->template;
		// Check if Uninstall query exists.
		if (isset($this->plugin->uninstall->queries->query)) {
			// Assign queries to uninstall.
			$queries_array = $this->plugin->uninstall->queries->query;
			if (! empty($queries_array)) {
				// Run all queries in database.
				foreach ($queries_array as $query_array) {
					// Assign query as string.
					$query_array = (string) $query_array;
					// Make sure query is not empty.
					if (! empty($query_array)) {
						// Execute query.
						if ($db->newQuery($query_array)) {
							// Show execution.
							if ($this->action != 'install') $this->log .= $template->ok(sprintf(__("Uninstalled query for %s : %s", 'AdminTools'), $plugin_folder, $query_array), 'return');
						}
					}
				}
			}
		}
	}

	/**
	 * Uninstall plugin database version.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 */
	private function uninstallVersion ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// Delete database activation.
		if ($db->invokeQuery('PHPDS_deleteVersionQuery', $plugin_folder)) {
			// Show execution.
			$template->ok(sprintf(__("Uninstalled plugin %s.", 'AdminTools'), $plugin_folder));
		}
	}

	/**
	 * Execute provided database query for plugin upgrade.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 * @param string Upgrade database against this version.
	 */
	private function upgradeQueries ($plugin_folder, $installed_version)
	{
		$db = $this->db;
		$template = $this->template;
		// Assign queries q.
		$upgrade_array = $this->plugin->upgrade;
		$latest_version = $this->plugin->install['version'];
		// Loop through all upgrade objects.
		foreach ($upgrade_array as $upgrade) {
			// Active loop version.
			$active_loop_version = (int) $upgrade['version'];
			// Check if version is in correct loop to execute upgrade.
			if (($active_loop_version <= $latest_version) && ($active_loop_version > $installed_version)) {
				// Make sure query upgrades is not empty.
				if (! empty($upgrade->queries->query)) {
					// Lets loop through upgrade queries and do them one at a time.
					foreach ($upgrade->queries->query as $upgrade_query) {
						// Turn upgrade query into a string.
						$upgrade_query_ = (string) trim($upgrade_query);
						// Make sure query is not empty.
						if (! empty($upgrade_query_)) {
							// Execute upgrade query.
							if ($db->invokeQuery('PHPDS_doQuery', $upgrade_query_)) // Show execution.
							$this->log .= $template->ok(sprintf(__("Query upgraded for %s : %s", 'AdminTools'), $plugin_folder, $upgrade_query_), 'return');
						}
					}
				}
				// Make sure settings upgrades is not empty.
				if (isset($upgrade->settings->setting)) {
					// Lets loop through upgrade settings and do them one at a time.
					foreach ($upgrade->settings->setting as $setting) {
						// Check if setting needs to be deleted or written.
						if (isset($setting['write'])) {
							// Assign setting as string.
							$param = (string) $setting['write'];
							$setting_value = (string) $setting;
							// Assign settings array.
							$param_write[$param] = $setting_value;
						}
						// Check if we have a delete item.
						if (isset($setting['delete'])) {
							$p_delete = (string) $setting['delete'];
							$param_delete[] = $p_delete;
						}
					}
				}
				// Check if we have settings to write in array.
				if (isset($param_write)) {
					// Write settings string.
					if ($db->writeSettings($param_write, $plugin_folder)) // Show execution.
					$this->log .= $template->ok(sprintf(__("Upgraded settings for %s.", 'AdminTools'), $plugin_folder), 'return');
				}
				// Is there settings to delete in array?
				if (isset($param_delete)) {
					// Delete settings string.
					if ($db->deleteSettings($param_delete, $plugin_folder)) // Show execution.
					$this->log .= $template->ok(sprintf(__("Uninstalled some setting on upgrade for %s.", 'AdminTools'), $plugin_folder), 'return');
				}
				// Make sure node upgrades is not empty.
				if (isset($upgrade->nodes->node)) {
					// Lets loop through nodes and see what needs to be deleted.
					foreach ($upgrade->nodes->node as $node) {
						// Node link to delete.
						$delete_node_link = (string) $node['delete'];
						// Create node id for deletion.
						$node_id_to_delete[] = $this->nodeStructure->createNodeId($plugin_folder, $delete_node_link);
					}
				}
				// Check if we have items to delete.
				if (! empty($node_id_to_delete)) {
					// Delete node item.
					if ($this->nodeStructure->deleteNode($node_id_to_delete)) // Show execution.
					$this->log .= $template->ok(sprintf(__("Uninstalled some nodes on upgrade for %s.", 'AdminTools'), $plugin_folder), 'return');
				}
				// Unset items for new loop.
				unset($node_id_to_delete, $param_write, $param_delete);
				// Assign last version executed.
				$this->pluginUpgraded = $active_loop_version;
			}
		}
	}

	/**
	 * Upgrade existing plugin database.
	 *
	 * @param string Folder and unique name where plugin was copied.
	 * @param string Last status required from plugin manager.
	 */
	private function upgradeDatabase ($plugin_folder, $status)
	{
		$db = $this->db;
		$template = $this->template;
		// Check if we have a database value.
		if (! empty($this->pluginUpgraded)) {
			// Update database.
			if ($db->invokeQuery('PHPDS_upgradeVersionQuery', $status, $this->pluginUpgraded, $plugin_folder)) {
				// Show execution.
				$template->ok(sprintf(__("Upgraded plugin %s database to version %s.", 'AdminTools'), $plugin_folder, $this->pluginUpgraded));
			}
		}
	}

	/**
	 * Set to use plugin logo as default logo for your system.
	 *
	 * @param string $plugin_folder
	 */
	private function pluginSetLogo ($plugin_folder)
	{
		$db = $this->db;
		$template = $this->template;
		// We first need to set the current default logo to empty.
		$db->invokeQuery('PHPDS_unsetLogoQuery');

		// Now we can update the plugin database and set logo to 1.
		if ($db->invokeQuery('PHPDS_setDefaultLogoQuery', $plugin_folder)) {
			// Show execution.
			$template->ok(sprintf(__("Set logo for plugin %s.", 'AdminTools'), $plugin_folder));
		}
	}
}
