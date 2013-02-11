<?php

/**
 * DB - Does Table Exist.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_tableExistQuery extends PHPDS_query
{
	protected $sql = "SHOW TABLES LIKE '%s'";
}

/**
 * DB - Count Rows.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_countRowsQuery extends PHPDS_query
{
	protected $sql = "SELECT %s FROM %s";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		parent::invoke($parameters);
		return $this->count();
	}
}

/**
 * DB - General Logs.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_logThisQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_logs (id, log_type, log_description, log_time, user_id, user_display_name, menu_id, file_name, menu_name, user_ip)
		VALUES
			%s
		";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$log_array = $parameters[0];
		// Check if we need to log.
		if (! empty($log_array) && $this->configuration['system_logging'] == true) {
			// Set.
			$database_log_string = false;
			$navigation = $this->navigation->navigation;
			// Log types are :
			// 1 = OK
			// 2 = Warning
			// 3 = Critical
			// 4 = Log-in
			// 5 = Log-out
			foreach ($log_array as $logged_data) {
				// Check for empty variables, so we can create where empty.
				if (empty($logged_data['timestamp']))
					$logged_data['timestamp'] = $this->configuration['time'];
				if (empty($logged_data['user_id']))
					$logged_data['user_id'] = $this->configuration['user_id'];
				if (empty($logged_data['logged_by']))
					$logged_data['logged_by'] = $this->configuration['user_display_name'];
				if (empty($logged_data['menu_id']))
					$logged_data['menu_id'] = $this->configuration['m'];
				if (empty($logged_data['file_name']) && !empty($navigation[$this->configuration['m']]['menu_link'])) {
					$logged_data['file_name'] = $navigation[$this->configuration['m']]['menu_link'];
				} else {
					$logged_data['file_name'] = ___('N/A');
				}
				if (empty($logged_data['menu_name']) && !empty($navigation[$this->configuration['m']]['menu_name'])) {
					$logged_data['menu_name'] = $navigation[$this->configuration['m']]['menu_name'];
				} else {
					$logged_data['menu_name'] = ___('N/A');
				}
				if (empty($logged_data['user_ip']))
					$logged_data['user_ip'] = $this->user->userIp();
				// Replace certain characters in text otherwise the system crashes as soon as it is inserted in database.
				// Odd characters makes the system crash.
				$logged_data['log_description'] = $this->protectString($logged_data['log_description']);

				$logged_data = $this->protectArray($logged_data);

				if (!empty($logged_data['log_type']) || !empty($logged_data['log_description']))
					$database_log_string .= "(NULL, '{$logged_data['log_type']}', '{$logged_data['log_description']}', '{$logged_data['timestamp']}', '{$logged_data['user_id']}', '{$logged_data['logged_by']}', '{$logged_data['menu_id']}', '{$logged_data['file_name']}', '{$logged_data['menu_name']}', '{$logged_data['user_ip']}'),";
			}
			$database_log_string = rtrim($database_log_string, ',');
			if (!empty($database_log_string))
				parent::invoke($database_log_string);
		}
	}
}


/**
 * DB - Write settings to database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_writeSettingsQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_settings (setting_description, setting_value, note)
		VALUES
			%s
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$db = $this->db;
		list($write_settings, $custom_prefix, $notes) = $parameters;
		// Set prefix.
		if ($custom_prefix == '*') {
			$prefix = '%';
		} else {
			$prefix = $db->settingsPrefix($custom_prefix);
		}
		// Set.
		$db_replace = false;
		if (is_array($write_settings)) {
			// Lets insert values into database.
			foreach ($write_settings as $settings_id => $settings_value) {
				// Create setting id with prefix.
				if (! empty($notes[$settings_id])) {
					$note = $db->protect(trim($notes[$settings_id]));
				} else {
					$note = '';
				}
				$settings_id = $db->protect(trim($prefix . $settings_id));
				$settings_value = $db->protect(trim($settings_value));
				$db_replace .= "('$settings_id', '$settings_value', '$note'),";
			}
			$db_replace = rtrim($db_replace, ",");
			// Is there something to write.
			if (!empty($db_replace))
				$insert_settings = parent::invoke($db_replace);

			// Ok lets tell developer that it was inserted or not.
			if (! empty($insert_settings)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

/**
 * DB - Delete settings from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_deleteSettingsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_settings
		WHERE
			setting_description %s
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($settings_to_delete, $custom_prefix) = $parameters;

		// Set prefix.
		if ($custom_prefix == '*') {
			$prefix = '%%';
		} else {
			$prefix = $this->db->settingsPrefix($custom_prefix);
		}
		// Define.
		$db_delete_query = false;
		// Check what needs to be deleted.
		if (is_array($settings_to_delete)) {
			// Settings to delete.
			foreach ($settings_to_delete as $setting_from_db) {
				$db_delete_query .= "'$prefix" . "$setting_from_db',";
			}
			$db_delete_query = rtrim($db_delete_query, ",");
			// Load required settings.
			$db_delete_query = " IN ($db_delete_query) ";
		} else if ($settings_to_delete == '*') {
			$db_delete_query = " LIKE '$prefix%%' ";
		}
		if (! empty($db_delete_query))
			$delete_settings = parent::invoke($db_delete_query);
		// Was it successful.
		if (! empty($delete_settings)) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * DB - Get settings from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_getSettingsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT SQL_CACHE
			setting_description, setting_value
		FROM
			_db_core_settings
		WHERE
			setting_description	%s
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($settings_required, $custom_prefix) = $parameters;
		// Set prefix.
		if ($custom_prefix == '*') {
			$prefix = '%%';
		} else {
			$prefix = $this->db->settingsPrefix($custom_prefix);
		}
		// Check if multiple items is required.
		if (is_array($settings_required)) {
			// Define.
			$db_get_query = false;
			// Settings required.
			foreach ($settings_required as $setting_from_db) {
				if (! empty($setting_from_db)) {
					$db_get_query .= "'$prefix" . "$setting_from_db',";
					$settings[$setting_from_db] = null;
				}
			}
			$db_get_query = rtrim($db_get_query, ",");
			// Load required settings.
			$db_get_query = " IN ($db_get_query) ";
		} else {
			$db_get_query = " LIKE '$prefix%%' ";
		}

		// Do the query.
		// Load required single setting.
		if (!empty($db_get_query)) {
			$settings_db = parent::invoke($db_get_query);
		}

		// Ok lets return value.
		if (! empty($settings_db) && is_array($settings_db)) {
			// Loop settings results.
			foreach ($settings_db as $fetch_setting_array) {
				$description = $fetch_setting_array['setting_description'];
				$value = $fetch_setting_array['setting_value'];
				// Now lets just set description withou the prefix.
				$description = preg_replace("/$prefix/", '', $description);
				// Finally, we can now set the setting.
				$settings[$description] = $value;
			}
			return $settings;
		} else {
			return false;
		}
	}
}

/**
 * DB - Search and check if record exists in database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_doesRecordExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(*)
		FROM
		%s
		%s
		%s
	";

	protected $singleValue = true;

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($table_name, $search_column_names, $search_field_values, $column_name_for_exclusion, $exclude_field_value) = $parameters;
		// Check if we should exclude the id.
		if ($column_name_for_exclusion != false && $exclude_field_value != false) {
			$WHERE_in_db = " WHERE ($column_name_for_exclusion != '$exclude_field_value') AND ";
		} else {
			$WHERE_in_db = ' WHERE ';
		}
		// Check if we have an array or strings.
		if (is_array($search_column_names) && is_array($search_field_values)) {
			// Compile the column names to do checks in.
			foreach ($search_column_names as $key => $search_column_names_string) {
				$MATCH_in_db .= " $search_column_names_string = '$search_field_values[$key]' OR ";
			}
			// Trim
			$MATCH_in_db = $this->core->rightTrim($MATCH_in_db, ' OR ');
			$MATCH_in_db = "($MATCH_in_db)";
		} else {
			$MATCH_in_db = " $search_column_names = '$search_field_values' ";
		}
		// Create query.
		$result = parent::invoke(array($table_name, $WHERE_in_db, $MATCH_in_db));

		// Return Boolean.
		if (! empty($result)) {
			return $result;
		} else {
			return false;
		}
	}
}

/**
 * DB - Quick selecting single value from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_selectQuickQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			%s
		FROM
			%s
		WHERE
			%s = '%s'
	";

	protected $singleValue = true;
}

/**
 * DB - Delete row from database with option to return deleted column name.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_deleteQuickQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			%s
		WHERE
			%s = '%s';
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($from_table_name, $where_column_name, $is_equal_to_column_value, $return_column_value) = $parameters;
		if (! empty($return_column_value)) {
			$return_deleted = $this->db->selectQuick($from_table_name, $return_column_value, $where_column_name, $is_equal_to_column_value);
		}
		if (parent::invoke(array($from_table_name, $where_column_name, $is_equal_to_column_value))) {
			if (! empty($return_deleted)) {
				return $return_deleted;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}

/**
 * DB - This method is used to generate a new name value for a particular string in the database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_nameOfNewCopyQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			COUNT(%s)
		FROM
			%s
		WHERE
			%s = '%s'
	";

	protected $singleValue = true;

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($table_name, $name_field, $orig_name) = $parameters;
		// Define.
		$copy_count = 1;
		$new_name = $orig_name;
		while (1) {
			// Check if the new name already exists within one of the other records
			$row_count = parent::invoke(array($name_field, $table_name, $name_field, $new_name));
			if ($row_count > 0) {
				if ($row_count > $copy_count) {
					$copy_count = $row_count + 1;
				} else {
					$copy_count++;
				}
				if (stripos($new_name, 'Copy of') !== false) {
					// The name already exists, add a number, i.e. "Copy (1) of xxxxx"
					$new_name = preg_replace('/' . 'Copy of ' . ' /i', '', $orig_name);
					$new_name = sprintf('Copy (%d) of' . ' ', $copy_count + 1) . $new_name;
				} else if (stripos($new_name, 'Copy (') !== false) {
					// The name already exists, add a number, i.e. "Copy (1) of xxxxx"
					// but first remove the old "Copy (x) of" part.
					$copypos = stripos($new_name, 'Copy (');
					$ofpos = stripos($new_name, ' of ', $copypos);
					$copyofpart = substr($new_name, $new_name, $ofpos + strlen(' of '));
					$newcopyofpart = sprintf('Copy (%d) of', $copy_count + 1);
					$new_name = preg_replace("/$copyofpart/", $newcopyofpart, $new_name);
				} else {
					$new_name = 'Copy of ' . ' ' . $new_name;
				}
			} else {
				break;
			}
		}
		return $new_name;
	}
}

/**
 * DB - Lists all installed plugins on PHPDevShell.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class DB_installedPluginsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			plugin_folder, status, version, use_logo
		FROM
			_db_core_plugin_activation
	";

	/**
	 * Initiate query invoke command.
	 * @param array
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Get installed plugins with cache.
		if ($this->db->cacheEmpty('plugins_installed')) {
			// Query installed plugins.
			$installed_plugins_db = parent::invoke();
			// Loop array.
			// $plugins_installed = array();
			foreach ($installed_plugins_db as $installed_plugins_array) {
				$plugins_installed[$installed_plugins_array['plugin_folder']] = array(
					'plugin_folder' => $installed_plugins_array['plugin_folder'],
					'status' => $installed_plugins_array['status'],
					'version' => $installed_plugins_array['version'],
					'use_logo' => $installed_plugins_array['use_logo']
				);
				// Set plugin logo as well.
				if ($installed_plugins_array['use_logo']) {
					$plugin_logo = $installed_plugins_array['plugin_folder'];
				}
			}
			$this->db->pluginsInstalled = $plugins_installed;
			if (empty($plugin_logo))
				$plugin_logo = '';
			$this->db->pluginLogo = $plugin_logo;
			// Write essential plugin data to cache.

			$this->db->cacheWrite('plugins_installed', $plugins_installed);
			$this->db->cacheWrite('plugin_logo', $plugin_logo);
		} else {
			// Read installed plugins from cache.
			$this->db->pluginsInstalled = $this->db->cacheRead('plugins_installed');
			$this->db->pluginLogo = $this->db->cacheRead('plugin_logo');
		}
	}
}

/**
 * Reads plugin registry to know what classes needs to be checked for when called.
 *
 * @author Jason Schoem
 */
class DB_readPluginClassRegistryQuery extends PHPDS_query
{
	protected $sql = "
		SELECT SQL_CACHE
			t1.class_id, t1.class_name, t1.alias, t1.plugin_folder, t1.enable, t1.rank
		FROM
			_db_core_plugin_classes t1
		WHERE
			(t1.enable = 1)
		ORDER BY
			t1.rank
		ASC
	";
}