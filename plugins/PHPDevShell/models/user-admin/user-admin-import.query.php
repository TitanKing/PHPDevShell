<?php

/**
 * User Admin Import - Check if user exists.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_existingUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id
		FROM
			_db_core_users
		WHERE
			user_name = '%s'
	";
	protected $singleValue = true;
}

/**
 * User Admin Import - Insert all imported users into database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeUserQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_users (user_id, user_display_name, user_name, user_password, user_email, user_group, user_role, date_registered, language, timezone, region)
		VALUES
			%s
	";
	protected $returnId = true;
}

/**
 * User Admin Import - Insert all imported users into queue.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeRegistrationQueueQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_registration_queue (user_id, registration_type, token_id)
		VALUES
			(%u, '2', '%s')
	";
}

/**
 * User Admin Import - Create overflow data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeOverflowTableQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			%s
		VALUES
			(%u, %s)
	";
}

/**
 * User Admin Import - Do Import
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_doImportQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Get setting values.
		$email = $this->factory('mailer');
		$filemanager = $this->factory('fileManager');
		$settings = $this->db->getSettings(array('registration_group', 'registration_role'));
		$edit['token_id_option'] = '';
		$edit['user_timezone'] = '';
		$edit['region'] = '';
		$edit['language'] = '';
		$edit['overflow_table'] = '';
		$edit['email_username'] = '';
		$edit['overwrite_dup'] = '';
		$edit = $parameters[0];

		// Madatory fields.
		if (empty($edit['token_id_option']) || empty($edit['csv_order']) || empty($edit['delimiter'])) {
			$this->template->warning(_('Please complete all mandatory fields. Select a token option, also remember your CSV order and delimiter.'));
			$error[0] = true;
		}
		// Check if custom table exists.
		if (!empty($edit['overflow_table'])) {
			if (!$this->db->tableExist($edit['overflow_table'])) {
				$this->template->warning(sprintf(_('Cannot find a overflow table called %s.'), $edit['overflow_table']));
				$error[1] = true;
			}
		}

		// No errors, we can continue.
		if (empty($error)) {
			// Upload CSV.
			$filemanager->allowedExt = 'csv';
			$filemanager->alias = 'csv_import';
			// Uploaded file.
			if ($csv_file = $filemanager->autoUpload('csv_file')) {
				// We now have the file, we could now start using it.
				if ($open_csv_file = fopen($csv_file, "r")) {
					// Define default set of values.
					$import_errors = 0;
					$import_errors_custom = 0;
					$import_count = 0;
					$import_count_custom = 0;
					$import_['token_id_option'] = $edit['token_id_option'];
					$import_['overflow_table'] = $edit['overflow_table'];
					if (!empty($edit['email_username']))
							$import_['email_username'] = $edit['email_username'];
					if (!empty($edit['overwrite_dup']))
							$import_['overwrite_dup'] = $edit['overwrite_dup'];
					$import_['user_role'] = $settings['registration_role'];
					$import_['user_group'] = $settings['registration_group'];
					$import_['date_registered'] = $this->configuration['time'];
					// language
					if (empty($edit['language'])) {
						$import_['language'] = $this->configuration['language'];
					} else {
						$import_['language'] = $edit['language'];
					}
					// region
					if (empty($edit['region'])) {
						$import_['region'] = $this->configuration['region'];
					} else {
						$import_['region'] = $edit['region'];
					}
					// timezone
					if (empty($edit['user_timezone'])) {
						$import_['timezone'] = $this->configuration['system_timezone'];
					} else {
						$import_['timezone'] = $edit['user_timezone'];
					}
					// timezone
					if (empty($edit['user_timezone'])) {
						$import_['timezone'] = $this->configuration['system_timezone'];
					} else {
						$import_['timezone'] = $edit['user_timezone'];
					}
					// Read the csv file.
					$csv_file_contents = fread($open_csv_file, filesize($csv_file));
					fclose($open_csv_file);
					// Get and split into first level array.
					$import_row_array = explode("\n", $csv_file_contents);
					// Create csv_order_array this will be used to create a combined array.
					$csv_order_array = explode(",", str_replace(' ', '', $edit['csv_order']));

					// Loop array and split into sub columns.
					if (empty($import_row_array)) $import_row_array = array();
					foreach ($import_row_array as $import_row) {
						// We now need to split it into columns.
						if (!empty($import_row)) {
							// Try to create row array.
							$import_row_ = explode($edit['delimiter'], $import_row);
							// Try and combine array!
							if ($import_column_array = @array_combine($csv_order_array, $import_row_)) {
								// We now have the whole list, lets prepare it for insertion.
								// Basic error checking.
								// Set default status to true, this means it will be processed.
								$import_['import_status'] = true;
								// Check if we have a mandatory name.
								// user_display_name
								if (empty($import_column_array['name'])) {
									// We dont! We will not process this one.
									$import_['user_display_name'] = _('MISSING???');
									$import_['import_status'] = false;
								} else {
									$import_['user_display_name'] = str_replace(array("\n", "\r", "\r\n", "\n\r", ",", ";", "'", '"'), '', trim($import_column_array['name']));
								}
								// user_email
								if (empty($import_column_array['email'])) {
									// We dont! We will not process this one.
									$import_['user_email'] = _('MISSING???');
									$import_['import_status'] = false;
								} else {
									$import_['user_email'] = str_replace(array("\n", "\r", "\r\n", "\n\r", ",", ";", "'", '"'), '', trim($import_column_array['email']));
								}
								// Check if we have a mandatory email.
								// user_name
								if (empty($import_column_array['username'])) {
									// Ok lets create a username from the name shall we?
									if (!empty($import_['import_status']) || !empty($import_['email_username'])) {
										if (!empty($import_['email_username'])) {
											$import_['user_name'] = $import_['user_email'];
										} else {
											$import_['user_name'] = $this->core->safeName($import_['user_display_name']);
										}
									} else {
										$import_['user_name'] = _('MISSING???');
									}
								} else {
									$import_['user_name'] = str_replace(array("\n", "\r", "\r\n", "\n\r", ",", ";", "'", '"'), '', trim($import_column_array['username']));
								}
								// user_password
								if (empty($import_column_array['password'])) {
									// Ok lets create a username from the name shall we?
									if (!empty($import_['import_status'])) {
										$import_['user_password'] = trim($import_['user_name']);
									} else {
										$import_['user_password'] = 'password';
									}
								} else {
									$import_['user_password'] = str_replace(array("\n", "\r", "\r\n", "\n\r", ",", ";", "'", '"'), '', trim($import_column_array['password']));
								}
								// add prefix
								$import_['user_password'] = $edit['password_prefix'] . $import_['user_password'];
								// md5 password.
								$import_['user_password'] = md5($import_['user_password']);
								// We need to check if user already exists, this will prevent double importing.
								$existing_user = $this->db->invokeQuery('PHPDS_existingUserQuery', $import_['user_name']);

								// Create database insert string.
								$import_database = "(
										'$existing_user',
										'{$import_['user_display_name']}',
										'{$import_['user_name']}',
										'{$import_['user_password']}',
										'{$import_['user_email']}',
										'{$import_['user_group']}',
										'{$import_['user_role']}',
										'{$import_['date_registered']}',
										'{$import_['language']}',
										'{$import_['timezone']}',
										'{$import_['region']}')";
								// Finally check if user exists.
								if (empty($existing_user) || !empty($import_['overwrite_dup'])) {
									// Looping to place user into database, I do not like it but we have to loop to get each user id.
									$user_id = $this->db->invokeQuery('PHPDS_writeUserQuery', $import_database);
									if (!empty($existing_user)) $user_id = $existing_user;
									if (!empty($user_id)) {
										// Now we have the user id, lets continue saving to pending database.
										if ($this->db->invokeQuery('PHPDS_writeRegistrationQueueQuery', $user_id, $import_['token_id_option'])) {
											// We need to strip away system columns now.
											unset($import_column_array['name'], $import_column_array['username'], $import_column_array['email'], $import_column_array['password']);
											// Add overflow table.
											if (!empty($import_['overflow_table'])) {
												// Define.
												$import_overflow_values = false;
												// Lets see what columns we have left in $import_column_array.
												if (empty($import_column_array)) $import_column_array= array();
												foreach ($import_column_array as $custom_column => $custom_column_value) {
													// Strip whitespaces and add column.
													$custom_column_value_ = str_replace(array("\n", "\r", "\r\n", "\n\r", ",", ";", "'", '"'), '', trim($custom_column_value));
													$import_overflow_values .= "'$custom_column_value_',";
												}
												// Strip away last coma.
												$import_overflow_values = rtrim($import_overflow_values, ",");
												if ($this->db->invokeQuery('PHPDS_writeOverflowTableQuery', $import_['overflow_table'], $user_id, $import_overflow_values)) {
													$import_count_custom++;
												} else {
													$import_custom++;
												}
											}
											// Do a general import count.
											$import_count++;
											// Count import errors.
											if ($import_['import_status'] == false) {
												$import_errors++;
											}
										}
									}
								} else {
									// User already exists.
									$this->template->warning(sprintf(_('Skipping %s, this user already exists in database.'), $import_['user_name']), false, false);
								}
							} else {
								// Try to determine column.
								if (!empty($import_row)) // Oops, this wont work! Skip it.
										$this->template->notice(sprintf(_('CSV columns (%s) count does not match, (%s).'), $import_row, $edit['csv_order']));
							}
						}
					}
					// Show import messages.
					if (!empty($import_count))
							$this->template->ok(sprintf(_('Imported %s users. You can now approve these users under pending users to complete the import process.'), $import_count));
					if (!empty($import_errors))
							$this->template->notice(sprintf(_('There are %s import notices!'), $import_errors));
					if (!empty($import_count_custom))
							$this->template->ok(sprintf(_('Imported %s custom column rows into table %s.'), $import_count_custom, $import_['overflow_table']));
					if (!empty($import_errors_custom))
							$this->template->notice(sprintf(_('Failed imported %s custom column rows into table %s.'), $import_errors_custom, $import_['overflow_table']));
				} else {
					$this->template->warning(sprintf(_('The uploaded CSV file could not be read in %s.'), $csv_file));
				}
			} else {
				$this->template->warning(_('You have not selected a CSV file to import, please select one.'));
			}
		}
	}
}

/**
 * User Admin Import - Read Token Options
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readTokenOptionsQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.token_id, t1.token_name, t1.user_role_id, t1.user_group_id, t1.token_key, t1.registration_option, t1.available_tokens,
			t2.user_role_name,
			t3.user_group_name
		FROM
			_db_core_registration_tokens t1
		LEFT JOIN
			_db_core_user_roles t2
		ON
			t1.user_role_id = t2.user_role_id
		LEFT JOIN
			_db_core_user_groups t3
		ON
			t1.user_group_id = t3.user_group_id
		WHERE
			t1.available_tokens > 0
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Set parameters
		$select_tokens = parent::invoke($parameters);
		// Fetch results and create token options..
		$token_selection = '';
		$edit['token_id_option'] = '';
		if (! empty($select_tokens)) {
			foreach ($select_tokens as $tokens_array) {
				// Is token selected.
				($tokens_array['token_id'] == $edit['token_id_option']) ? $token_selected = 'selected' : $token_selected = false;
				// Check if we should list this as an item.
				$token_selection .= <<<HTML
					<option value="{$tokens_array['token_id']}" $token_selected>
						{$tokens_array['token_name']} (Role: {$tokens_array['user_role_name']}) (Group: {$tokens_array['user_group_name']})
					</option>"
HTML;
			}
			return $token_selection;
		} else {
			return '';
		}
	}
}
