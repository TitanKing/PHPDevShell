<?php

/**
 * General Settings: Handles all general system settings.
 * @author Jason Schoeman
 * @return string
 */
class GeneralSettings extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Load Extra Classes ////////////////////////////////////////////////////////////////////////////////////////////////////
		$email = $this->factory('mailer'); ////////////////////////////////////////////////////////////////////////////////////////////////////
		$filemanager = $this->factory('fileManager'); ////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		// Header information
		$this->template->heading(__('General System Configuration'));

		// Extract all available settings.
		$sa = $this->db->getSettings();

		// If user requested a save... lets save it.
		if (!empty($this->security->post['save']) && $this->user->isRoot()) {
			// Collect post values.
			$sa = $this->security->post;

			// Create new crypt key if empty.
			if (empty($sa['crypt_key']))
					$sa['crypt_key'] = $this->core->createRandomString(40);
			// Decode HTML entity for footer.
			$sa['footer_notes'] = $this->template->htmlEntityDecode($sa['footer_notes']);
			// We also need to get the default templates id.
			$sa['default_template_id'] = $this->db->invokeQuery('PHPDS_defaultTemplateIdQuery', $sa['default_template']);

			// We also need to save the empty template id.
			$sa['empty_template_id'] = $this->db->invokeQuery('PHPDS_emptyTemplateIdQuery');

			// If it can't be found create a new one and give notice.
			if (empty($sa['empty_template_id'])) {
				$this->db->invokeQuery('PHPDS_writeEmptyTemplateIdQuery');
				$this->template->notice(__('Cannot find the empty template so a new entry was created.'));
			}
			// Define.
			$lang_code_inster = false;
			// Check if we have any languages_available saved.
			if (!empty($sa['languages_available'])) {
				// Do language tags.
				foreach ($sa['languages_available'] as $lang_code_string) {
					$lang_code_inster .= $lang_code_string . ',';
				}
				$sa['languages_available'] = rtrim($lang_code_inster, ',');
			} else {
				$sa['languages_available'] = false;
			}
			// Define.
			$region_code_inster = false;
			// Check if we have any regions_available saved.
			if (!empty($sa['regions_available'])) {
				// Do region tags.
				foreach ($sa['regions_available'] as $region_code_string) {
					$region_code_inster .= $region_code_string . ',';
				}
				$sa['regions_available'] = rtrim($region_code_inster, ',');
			} else {
				$sa['regions_available'] = false;
			}
			/////////////////////
			// Error Checking. //
			/////////////////////
			// Check for missing fields.
			if (
			// Server Settings
					empty($sa['system_down_message']) || empty($sa['redirect_login']) || empty($sa['root_id']) || empty($sa['root_role']) || empty($sa['root_group']) || empty($sa['crypt_key']) ||
					// System Settings
					empty($sa['locale_format']) || empty($sa['charset']) || empty($sa['language']) || empty($sa['region']) || empty($sa['date_format']) || empty($sa['date_format_short']) || empty($sa['split_results']) || empty($sa['system_timezone']) ||
					// Template Settings
					empty($sa['scripts_name_version']) || empty($sa['default_template']) || empty($sa['loginandout']) || empty($sa['front_page_id']) || empty($sa['front_page_id_in']) || empty($sa['footer_notes']) ||
					// Email Settings
					empty($sa['setting_admin_email']) || empty($sa['setting_support_email']) || empty($sa['email_fromname']) || empty($sa['from_email']) ||
					// Registration Settings
					empty($sa['registration_page']) || empty($sa['registration_group']) || empty($sa['move_verified_group']) || empty($sa['guest_group']) || empty($sa['registration_role']) || empty($sa['move_verified_role']) || empty($sa['guest_role']) || empty($sa['banned_role'])) { // Upload Settings
				$this->template->warning(__('You did not complete all required fields.    ' . $sa['allow_remember']));
				$error[1] = true;
			}

			if (empty($error)) {
				// All safe, save values in database.
				$insert_settings = $this->db->writeSettings($sa);

				// Send out a test email!
				$test_email_subject = sprintf(__('Test email from %s.'), $this->configuration['scripts_name_version']);
				$test_email_message = sprintf(__("%s was able to send a notification email: %s"), $this->configuration['scripts_name_version'], $this->configuration['absolute_url']);
				// Check test email...
				if (!empty($this->security->post['test_email'])) {
					if ($email->sendmail("{$sa['setting_admin_email']}", $test_email_subject, $test_email_message)) {
						$this->template->ok(sprintf(__('A test email was sent to %s.'), $sa['setting_admin_email']));
					} else {
						$this->template->warning(__('Could not send a test email.'));
					}
				}
				// Check test ftp...
				if (!empty($this->security->post['test_ftp'])) {
					try {
						if ($ftp = $filemanager->establishFtp()) {
							// Can we goto the root folder.
							if (ftp_size($ftp, 'includes/PHPDS.inc.php') > 2) {
								$this->template->ok(sprintf(__('FTP connection successful and test done, your FTP seems to be correctly configured and is able to access the root directory. Working root directory is : %s'), ftp_pwd($ftp)));
							} else {
								$this->template->notice(sprintf(__('FTP connection was ok, but the root directory listing failed, working directory is %s'), ftp_pwd($ftp)));
							}
						} else {
							$this->template->warning(__('FTP connection failed, please check your settings.'));
						}
					} catch (error $e) {
						$e->warning();
					}
				}
				// Success.
				if ($insert_settings) {
					$this->template->ok(__('System general settings saved. You might need to reload this page for settings to reflect.'));
					// Clear settings cache.
					$this->db->cacheClear();
				}
			}
		}
		///////////////////////////////////////////////////////////////
		// Load general used classes and databases. ///////////////////
		///////////////////////////////////////////////////////////////
		// Load language iana repository.
		$iana = $this->factory('iana');
		$iana_language_array = $iana->readIanaRegistry('language');
		// Load region iana repository.
		$iana_region_array = $iana->readIanaRegistry('region');

		// Query available users.
		$get_user_db = $this->db->invokeQuery('PHPDS_getUserDbQuery', $this->configuration['user_group']);

		// Query available roles.
		$user_roles_db = $this->db->invokeQuery('PHPDS_userRolesDbQuery');

		// Query available groups.
		$user_groups_db = $this->db->invokeQuery('PHPDS_userGroupsDbQuery');

		// Query available node items.
		$node_db = $this->db->invokeQuery('PHPDS_nodeDbQuery');

		// Query available themes.
		$select_template = $this->db->invokeQuery('PHPDS_selectTemplateQuery');

		///////////////////////////////////////////////////////////////
		// Set selected values. ///////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// ******************** SERVER SETTINGS ******************** //
		// system_down ////////////////////////////////////////////////
		$verify_system_down0 = false;
		$verify_system_down1 = false;
		($sa['system_down'] == '1') ? $verify_system_down0 = 'checked' : $verify_system_down1 = 'checked';
		///////////////////////////////////////////////////////////////
		// demo_mode //////////////////////////////////////////////////
		$verify_demo_mode0 = false;
		$verify_demo_mode1 = false;
		($sa['demo_mode'] == '1') ? $verify_demo_mode0 = 'checked' : $verify_demo_mode1 = 'checked';
		///////////////////////////////////////////////////////////////
		// spam_assassin //////////////////////////////////////////////
		$spam_assassin0 = false;
		$spam_assassin1 = false;
		($sa['spam_assassin'] == '1') ? $spam_assassin0 = 'checked' : $spam_assassin1 = 'checked';
		///////////////////////////////////////////////////////////////
		// Set.
		$redirect_option = '';
		$root_id_option = '';
		$root_role_option = '';
		$root_group_option = '';
		// redirect_login /////////////////////////////////////////////
		foreach ($node_db as $node_array) {
			// Determine node name.
			$node_name = $this->navigation->determineNodeName($node_array['node_name'], $node_array['node_link'], $node_array['node_id']);
			// Selected?
			($sa['redirect_login'] == $node_array['node_id']) ? $redirect_select = 'selected' : $redirect_select = false;
			$redirect_option .= '<option value="' . $node_array['node_id'] . "\" $redirect_select>" . $node_name . '</option>';
		}
		// root_id ////////////////////////////////////////////////////
		foreach ($get_user_db as $get_users_array) {
			// Create combined name.
			$combined_name = sprintf('%s (%s)', $get_users_array['user_name'], $get_users_array['user_display_name']);
			// Selected?
			($sa['root_id'] == $get_users_array['user_id']) ? $user_select = 'selected' : $user_select = false;
			$root_id_option .= '<option value="' . $get_users_array['user_id'] . "\" $user_select>" . $combined_name . '</option>';
		}
		///////////////////////////////////////////////////////////////
		// root_role //////////////////////////////////////////////////
		foreach ($user_roles_db as $user_roles_array) {
			// Check selected.
			($user_roles_array['user_role_id'] == $sa['root_role']) ? $root_role_selected = 'selected' : $root_role_selected = false;
			$root_role_option .= '<option value="' . $user_roles_array['user_role_id'] . '" ' . $root_role_selected . '>' . $user_roles_array['user_role_name'] . '</option>';
		}
		unset($user_roles_array);
		///////////////////////////////////////////////////////////////
		// root_group /////////////////////////////////////////////////
		foreach ($user_groups_db as $user_groups_array) {
			// Check selected.
			($user_groups_array['user_group_id'] == $sa['root_group']) ? $root_group_selected = 'selected' : $root_group_selected = false;
			$root_group_option .= '<option value="' . $user_groups_array['user_group_id'] . '" ' . $root_group_selected . '>' . $user_groups_array['user_group_name'] . '</option>';
		}
		unset($user_groups_array);
		///////////////////////////////////////////////////////////////
		// force_core_changes /////////////////////////////////////////
		$verify_force_core_changes0 = false;
		$verify_force_core_changes1 = false;
		($sa['force_core_changes'] == '1') ? $verify_force_core_changes0 = 'checked' : $verify_force_core_changes1 = 'checked';
		///////////////////////////////////////////////////////////////
		// queries_count //////////////////////////////////////////////
		$verify_queries_count0 = false;
		$verify_queries_count1 = false;
		($sa['queries_count'] == '1') ? $verify_queries_count0 = 'checked' : $verify_queries_count1 = 'checked';
		///////////////////////////////////////////////////////////////
		// system_logging /////////////////////////////////////////////
		$verify_system_logging0 = false;
		$verify_system_logging1 = false;
		($sa['system_logging'] == '1') ? $verify_system_logging0 = 'checked' : $verify_system_logging1 = 'checked';
		///////////////////////////////////////////////////////////////
		// access_logging /////////////////////////////////////////////
		$verify_access_logging0 = false;
		$verify_access_logging1 = false;
		($sa['access_logging'] == '1') ? $verify_access_logging0 = 'checked' : $verify_access_logging1 = 'checked';
		///////////////////////////////////////////////////////////////
		// email_critical /////////////////////////////////////////////
		$verify_email_critical0 = false;
		$verify_email_critical1 = false;
		($sa['email_critical'] == '1') ? $verify_email_critical0 = 'checked' : $verify_email_critical1 = 'checked';
		///////////////////////////////////////////////////////////////
		// sef_url ////////////////////////////////////////////////////
		$sef_url0 = false;
		$sef_url1 = false;
		($sa['sef_url'] == '1') ? $sef_url0 = 'checked' : $sef_url1 = 'checked';
		///////////////////////////////////////////////////////////////
		// ******************** SYSTEM SETTINGS ******************** //
		// Set.
		$language_option = false;
		$region_option = false;
		// language ///////////////////////////////////////////////////
		foreach ($iana_language_array as $lang_code => $lang_description) {
			// Check selected.
			($sa['language'] == $lang_code) ? $lang_selected = 'selected' : $lang_selected = false;
			// Create option strings.
			$language_option .= '<option value="' . $lang_code . '" ' . $lang_selected . '>' . $lang_description . '</option>';
		}
		// Clear language vars for re-use.
		unset($lang_code, $lang_description, $lang_selected);
		///////////////////////////////////////////////////////////////
		// region /////////////////////////////////////////////////////
		foreach ($iana_region_array as $region_code => $region_description) {
			// Check selected.
			($sa['region'] == $region_code) ? $region_selected = 'selected' : $region_selected = false;
			// Create option strings.
			$region_option .= '<option value="' . $region_code . '" ' . $region_selected . '>' . $region_description . '</option>';
		}
		// Clear region vars for re-use.
		unset($region_code, $region_description, $region_selected);
		///////////////////////////////////////////////////////////////
		// timezone ///////////////////////////////////////////////////
		$timezone = $this->factory('timeZone');
		$timezone_options = $timezone->timezoneOptions($sa['system_timezone'], true);
		///////////////////////////////////////////////////////////////
		// ******************** TEMPLATE SETTINGS ****************** //
		// Set.
		$template_option_ = false;
		$loginandout_option = false;
		$frontpage_id_option = false;
		$frontpage_id_in_option = false;
		$node_behaviour_dynamic = false;
		$node_behaviour_static = false;
		($sa['node_behaviour'] == 'dynamic') ? $node_behaviour_dynamic = 'checked' : $node_behaviour_static = 'checked';
		// default_template & printable_template//////////////////////////////
		foreach ($select_template as $select_template_array) {
			// Check if if item should be selected.
			($select_template_array['template_folder'] == $sa['default_template']) ? $template_selected = 'selected' : $template_selected = false;
			$template_option_ .= '<option value="' . $select_template_array['template_folder'] . '" ' . $template_selected . '>' . $select_template_array['template_folder'] . '</option>';
		}
		if (empty($sa['skin'])) {
			$skin_selected = $this->configuration['skin'];
		} else {
			$skin_selected = $sa['skin'];
		}
		$file = $this->factory('fileManager');
		$dir = $file->getDirListing("themes/{$this->configuration['template_folder']}/jquery/css");
		$skin_option_ = '';
		foreach ($dir as $skin) {
			($skin_selected == $skin['folder']) ? $skin_selected_ = 'selected' : $skin_selected_ = '';
			$skin_option_ .= '<option value="' . $skin['folder'] . "\" $skin_selected_>" . $skin['folder'] . '</option>';
		}
		// front_page_id //////////////////////////////////////////////
		foreach ($node_db as $node_array) {
			// Determine node name.
			$node_name = $this->navigation->determineNodeName($node_array['node_name'], $node_array['node_link'], $node_array['node_id']);
			// Logged in and out page?
			($sa['loginandout'] == $node_array['node_id']) ? $loginandout_select = 'selected' : $loginandout_select = false;
			$loginandout_option .= '<option value="' . $node_array['node_id'] . "\" $loginandout_select>" . $node_name . '</option>';
			// Logged Out Front Page Selected?
			($sa['front_page_id'] == $node_array['node_id']) ? $frontpage_select = 'selected' : $frontpage_select = false;
			$frontpage_id_option .= '<option value="' . $node_array['node_id'] . "\" $frontpage_select>" . $node_name . '</option>';
			// Logged IN Front Page Selected?
			($sa['front_page_id_in'] == $node_array['node_id']) ? $frontpage_select_in = 'selected' : $frontpage_select_in = false;
			$frontpage_id_in_option .= '<option value="' . $node_array['node_id'] . "\" $frontpage_select_in>" . $node_name . '</option>';
		}
		if (empty($sa['custom_css'])) $sa['custom_css'] = '';
		///////////////////////////////////////////////////////////////
		// ******************** EMAIL SETTINGS ********************* //
		// email_option ///////////////////////////////////////////////
		// Set.
		$email_option_mail = false;
		$email_option_sendmail = false;
		$email_option_smtp = false;
		$email_option_mail = false;
		// email_option
		switch ($sa['email_option']) {
			case 'mail':
				$email_option_mail = 'selected';
				break;
			case 'sendmail':
				$email_option_sendmail = 'selected';
				break;
			case 'smtp':
				$email_option_smtp = 'selected';
				break;
			default:
				$email_option_mail = 'selected';
				break;
		}
		///////////////////////////////////////////////////////////////
		// sendmail_path //////////////////////////////////////////////
		(empty($sa['sendmail_path'])) ? $sa['sendmail_path'] = '/usr/sbin/sendmail' : null;
		///////////////////////////////////////////////////////////////
		// smtp_secure ////////////////////////////////////////////////
		// Set.
		$smtp_secure_false = false;
		$smtp_secure_ssl = false;
		$smtp_secure_tls = false;
		$smtp_secure_false = false;
		switch ($sa['smtp_secure']) {
			case '':
				$smtp_secure_false = 'selected';
				break;
			case 'ssl':
				$smtp_secure_ssl = 'selected';
				break;
			case 'tls':
				$smtp_secure_tls = 'selected';
				break;
			default:
				$smtp_secure_false = 'selected';
				break;
		}
		// Default values.
		if (empty($sa['email_fromname'])) $sa['email_fromname'] = false;
		if (empty($sa['email_hostname'])) $sa['email_hostname'] = false;
		if (empty($sa['email_charset'])) $sa['email_charset'] = 'iso-8859-1';
		if (empty($sa['email_encoding'])) $sa['email_encoding'] = '8bit';
		if (empty($sa['smtp_timeout'])) $sa['smtp_timeout'] = 10;
		if (empty($sa['smtp_helo'])) $sa['smtp_helo'] = false;
		if (empty($sa['massmail_limit'])) $sa['massmail_limit'] = 100;
		///////////////////////////////////////////////////////////////
		// smtp_port //////////////////////////////////////////////////
		(empty($sa['smtp_port'])) ? $sa['smtp_port'] = 25 : null;
		///////////////////////////////////////////////////////////////
		// ******************** REGISTRATION SETTINGS ************** //
		// reg_email_direct ///////////////////////////////////////////
		if (empty($sa['reg_email_direct'])) $sa['reg_email_direct'] = __("Dear %1\$s, you completed the registration at %2\$s. Your registration was successful. This email is to verify that you requested to be registered, while confirming your email address at the same time. Thank you for registering at %3\$s.");
		if (empty($sa['reg_email_verify'])) $sa['reg_email_verify'] = __("Dear %1\$s, you requested registration at %2\$s. Your registration was successful but it is still pending. This email is to verify that you requested to be registered, while confirming your email address at the same time. Please click on the *link %3\$s to complete the registration process. Thank you for registering at %4\$s. *If you cannot click on the link, copy and paste the url in your browser's address bar.");
		if (empty($sa['reg_email_approve'])) $sa['reg_email_approve'] = __("Dear %1\$s, you completed the registration at %2\$s. Your registration was successful but is still pending. This email is to verify that you requested to be registered, while confirming your email address at the same time. Thank you for registering at %3\$s, an Admin will attend to your request soon.");
		if (empty($sa['reg_email_admin'])) $sa['reg_email_admin'] = __("Dear Admin, you have received a new registration at %1\$s. The user registered with the name %2\$s, on this date %3\$s, with the username %4\$s. Thank You, %5\$s.%6\$s %7\$s %8\$s. You must be logged-in to ban or approve users.");

		// allow_registration /////////////////////////////////////////
		// Set.
		$verify_allow_registration0 = false;
		$verify_allow_registration1 = false;
		$verify_allow_registration2 = false;
		$verify_allow_registration3 = false;
		switch ($sa['allow_registration']) {
			case 0:
				$verify_allow_registration0 = ' checked';
				break;
			case 1:
				$verify_allow_registration1 = ' checked';
				break;
			case 2:
				$verify_allow_registration2 = ' checked';
				break;
			case 3:
				$verify_allow_registration3 = ' checked';
				break;
		}
		///////////////////////////////////////////////////////////////
		// allow remember me //////////////////////////////////////////
		// Set.
		$verify_allow_remember_me0 = false;
		$verify_allow_remember_me1 = false;
		switch ($sa['allow_remember']) {
			case 0:
				$verify_allow_remember_me0 = ' checked="checked"';
				break;
			case 1:
				$verify_allow_remember_me1 = ' checked="checked"';
				break;
		}
		///////////////////////////////////////////////////////////////
		// registration_page //////////////////////////////////////////
		// Set.
		$registration_page_option = false;
		foreach ($node_db as $node_array) {
			// Determine node name.
			$node_name = $this->navigation->determineNodeName($node_array['node_name'], $node_array['node_link'], $node_array['node_id']);
			// Selected?
			($sa['registration_page'] == $node_array['node_id']) ? $registration_page_select = 'selected' : $registration_page_select = false;
			$registration_page_option .= '<option value="' . $node_array['node_id'] . "\" $registration_page_select>" . $node_name . '</option>';
		}
		///////////////////////////////////////////////////////////////
		// verify_registration ////////////////////////////////////////
		// Set.
		$verify_check0 = false;
		$verify_check1 = false;
		$verify_check2 = false;
		switch ($sa['verify_registration']) {
			case 0:
				$verify_check0 = ' checked';
				break;
			case 1:
				$verify_check1 = ' checked';
				break;
			case 2:
				$verify_check2 = ' checked';
				break;
		}
		///////////////////////////////////////////////////////////////
		// email_new_registrations ////////////////////////////////////
		$verify_email_new_registrations0 = false;
		$verify_email_new_registrations1 = false;
		($sa['email_new_registrations'] == '1') ? $verify_email_new_registrations0 = 'checked' : $verify_email_new_registrations1 = 'checked';
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// ROLE ///////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// registration_role //////////////////////////////////////////
		// Loop and see what needs to be selected.
		// Set.
		$user_roles_option_registration = false;
		$user_roles_option_move = false;
		$guest_role_option = false;
		$banned_role_option = false;
		foreach ($user_roles_db as $user_roles_array) {
			// Check selected.
			($user_roles_array['user_role_id'] == $sa['registration_role']) ? $registration_role_selected = 'selected' : $registration_role_selected = false;
			$user_roles_option_registration .= '<option value="' . $user_roles_array['user_role_id'] . '" ' . $registration_role_selected . '>' . $user_roles_array['user_role_name'] . '</option>';
		}
		unset($user_roles_array);
		///////////////////////////////////////////////////////////////
		// move_verified_role /////////////////////////////////////////
		foreach ($user_roles_db as $user_roles_array) {
			// Check selected.
			($user_roles_array['user_role_id'] == $sa['move_verified_role']) ? $moved_role_selected = 'selected' : $moved_role_selected = false;
			$user_roles_option_move .= '<option value="' . $user_roles_array['user_role_id'] . '" ' . $moved_role_selected . '>' . $user_roles_array['user_role_name'] . '</option>';
		}
		unset($user_roles_array);
		///////////////////////////////////////////////////////////////
		// guest_role /////////////////////////////////////////////////
		foreach ($user_roles_db as $user_roles_array) {
			// Check selected.
			($user_roles_array['user_role_id'] == $sa['guest_role']) ? $guest_selected = 'selected' : $guest_selected = false;
			$guest_role_option .= '<option value="' . $user_roles_array['user_role_id'] . '" ' . $guest_selected . '>' . $user_roles_array['user_role_name'] . '</option>';
		}
		unset($user_roles_array);
		///////////////////////////////////////////////////////////////
		// banned_role ////////////////////////////////////////////////
		foreach ($user_roles_db as $user_roles_array) {
			// Check selected.
			($user_roles_array['user_role_id'] == $sa['banned_role']) ? $banned_selected = 'selected' : $banned_selected = false;
			$banned_role_option .= '<option value="' . $user_roles_array['user_role_id'] . '" ' . $banned_selected . '>' . $user_roles_array['user_role_name'] . '</option>';
		}
		unset($user_roles_array);
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// GROUP //////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// registration_group /////////////////////////////////////////
		// Loop and see what needs to be selected.
		// Set.
		$user_groups_option_registration = false;
		$user_groups_option_move = false;
		$guest_group_option = false;
		foreach ($user_groups_db as $user_groups_array) {
			// Check selected.
			($user_groups_array['user_group_id'] == $sa['registration_group']) ? $registration_group_selected = 'selected' : $registration_group_selected = false;
			$user_groups_option_registration .= '<option value="' . $user_groups_array['user_group_id'] . '" ' . $registration_group_selected . '>' . $user_groups_array['user_group_name'] . '</option>';
		}
		unset($user_groups_array);
		///////////////////////////////////////////////////////////////
		// move_verified_group ////////////////////////////////////////
		foreach ($user_groups_db as $user_groups_array) {
			// Check selected.
			($user_groups_array['user_group_id'] == $sa['move_verified_group']) ? $moved_group_selected = 'selected' : $moved_group_selected = false;
			$user_groups_option_move .= '<option value="' . $user_groups_array['user_group_id'] . '" ' . $moved_group_selected . '>' . $user_groups_array['user_group_name'] . '</option>';
		}
		unset($user_groups_array);
		///////////////////////////////////////////////////////////////
		// guest_group ////////////////////////////////////////////////
		foreach ($user_groups_db as $user_groups_array) {
			// Check selected.
			($user_groups_array['user_group_id'] == $sa['guest_group']) ? $guest_selected = 'selected' : $guest_selected = false;
			$guest_group_option .= '<option value="' . $user_groups_array['user_group_id'] . '" ' . $guest_selected . '>' . $user_groups_array['user_group_name'] . '</option>';
		}
		unset($user_groups_array);
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		/////////////////// END GROUPS AND ROLES //////////////////////
		///////////////////////////////////////////////////////////////
		// languages_available ////////////////////////////////////////
		// Get lang values selected.
		$count_languages_selected = 0;
		// Set.
		$languages_available_option = false;
		$regions_available_option = false;
		if (!empty($sa['languages_available'])) {
			$lang_code_explode = explode(',', $sa['languages_available']);
			// Loop and assign selected values as true.
			foreach ($lang_code_explode as $lang_code_set_true) {
				$lang_code_is[$lang_code_set_true] = true;
				$count_languages_selected++;
			}
		}
		$languages_selected_template = sprintf(__('(%s selected)'), $count_languages_selected);
		// Loop repository.
		foreach ($iana_language_array as $lang_code => $lang_description) {
			// Check selected.
			(!empty($lang_code_is[$lang_code])) ? $lang_selected = 'selected' : $lang_selected = false;
			// Create option strings.
			$languages_available_option .= '<option value="' . $lang_code . '" ' . $lang_selected . '>' . $lang_description . " ($lang_code)" . '</option>';
		}
		///////////////////////////////////////////////////////////////
		// regions_available //////////////////////////////////////////
		// Get region values selected.
		$count_regions_selected = 0;
		if (!empty($sa['regions_available'])) {
			$region_code_explode = explode(',', $sa['regions_available']);
			// Loop and assign selected values as true.
			foreach ($region_code_explode as $region_code_set_true) {
				$region_code_is[$region_code_set_true] = true;
				$count_regions_selected++;
			}
		}
		$regions_selected_template = sprintf(__('(%s selected)'), $count_regions_selected);
		// Loop repository.
		foreach ($iana_region_array as $region_code => $region_description) {
			// Check selected.
			(!empty($region_code_is[$region_code])) ? $region_selected = 'selected' : $region_selected = false;
			// Create option strings.
			$regions_available_option .= '<option value="' . $region_code . '" ' . $region_selected . '>' . $region_description . " ($region_code)" . '</option>';
		}
		///////////////////////////////////////////////////////////////
		// ******************* UPLOAD SETTINGS ********************* //
		// log_uploads ////////////////////////////////////////////////
		$log_uploads0 = false;
		$log_uploads1 = false;
		($sa['log_uploads'] == '0') ? $log_uploads0 = 'checked' : $log_uploads1 = 'checked';
		///////////////////////////////////////////////////////////////
		// Check if folder exist and if writable.
		if (is_writable($this->configuration['upload_path'])) {
			$writable = $this->template->icon('disk--plus', __('Writable'));
		} else {
			$writable = $this->template->icon('disk--minus', __('NOT Writable'));
		}
		// max_filesize ///////////////////////////////////////////////
		$max_filesize_show = $filemanager->displayFilesize($sa['max_filesize']);
		// max_imagesize //////////////////////////////////////////////
		$max_imagesize_show = $filemanager->displayFilesize($sa['max_imagesize']);
		///////////////////////////////////////////////////////////////
		// ******************* THUMB SETTINGS ********************** //
		// log_uploads ////////////////////////////////////////////////
		// do_create_thumb ////////////////////////////////////////////
		$do_create_thumb0 = false;
		$do_create_thumb1 = false;
		($sa['do_create_thumb'] == '0') ? $do_create_thumb0 = 'checked' : $do_create_thumb1 = 'checked';
		///////////////////////////////////////////////////////////////
		// thumbnail_type /////////////////////////////////////////////
		$adaptive_op = false;
		$resize_op = false;
		$resizepercent_op = false;
		$cropfromcenter_op = false;
		$crop_op = false;
		switch ($sa['thumbnail_type']) {
			case 'adaptive':
				$adaptive_op = 'checked';
				break;
			case 'resize':
				$resize_op = 'checked';
				break;
			case 'resizepercent':
				$resizepercent_op = 'checked';
				break;
			case 'cropfromcenter':
				$cropfromcenter_op = 'checked';
				break;
			case 'crop':
				$crop = 'checked';
				break;
		}
		// do_thumb_reflect ///////////////////////////////////////////
		$do_thumb_reflect0 = false;
		$do_thumb_reflect1 = false;
		($sa['do_thumb_reflect'] == '0') ? $do_thumb_reflect0 = 'checked' : $do_thumb_reflect1 = 'checked';
		///////////////////////////////////////////////////////////////
		// graphics_engine ////////////////////////////////////////////
		$graphics_engine_gd = false;
		$graphics_engine_imagick = false;
		($sa['graphics_engine'] == 'imagick') ? $graphics_engine_imagick = 'checked' : $graphics_engine_gd = 'checked';
		///////////////////////////////////////////////////////////////
		// do_create_resize_image /////////////////////////////////////
		$do_create_resize_image0 = false;
		$do_create_resize_image1 = false;
		($sa['do_create_resize_image'] == '0') ? $do_create_resize_image0 = 'checked' : $do_create_resize_image1 = 'checked';
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// ******************** SERVER SETTINGS ******************** //
		// ftp_enable /////////////////////////////////////////////////
		$ftp_enable0 = false;
		$ftp_enable1 = false;
		($sa['ftp_enable'] == '0') ? $ftp_enable0 = 'checked' : $ftp_enable1 = 'checked';
		// ftp_username ///////////////////////////////////////////////
		if (empty($sa['ftp_username'])) $sa['ftp_username'] = '';
		// ftp_password ///////////////////////////////////////////////
		if (empty($sa['ftp_password'])) $sa['ftp_password'] = '';
		// ftp_host ///////////////////////////////////////////////////
		if (empty($sa['ftp_host'])) $sa['ftp_host'] = 'localhost';
		// ftp_port ///////////////////////////////////////////////////
		if (empty($sa['ftp_port'])) $sa['ftp_port'] = '21';
		// ftp_ssl ///////////////////////////////////////////////////
		$ftp_ssl0 = false;
		$ftp_ssl1 = false;
		($sa['ftp_ssl'] == '0') ? $ftp_ssl0 = 'checked' : $ftp_ssl1 = 'checked';
		// ftp_timout /////////////////////////////////////////////////
		if (empty($sa['ftp_timeout'])) $sa['ftp_timeout'] = '90';
		// ftp_root ///////////////////////////////////////////////////
		if (empty($sa['ftp_root']))
				$sa['ftp_root'] = $this->configuration['absolute_path'];
		///////////////////////////////////////////////////////////////
		///////////////////////////////////////////////////////////////
		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('sa', $sa);

		# <!-- ************************* SERVER SETTINGS ************************ -->
		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('verify_system_down0', $verify_system_down0);
		$view->set('verify_system_down1', $verify_system_down1);
		$view->set('verify_demo_mode0', $verify_demo_mode0);
		$view->set('verify_demo_mode1', $verify_demo_mode1);
		$view->set('spam_assassin0', $spam_assassin0);
		$view->set('spam_assassin1', $spam_assassin1);
		$view->set('redirect_option', $redirect_option);
		$view->set('root_id_option', $root_id_option);
		$view->set('root_role_option', $root_role_option);
		$view->set('root_group_option', $root_group_option);
		$view->set('verify_force_core_changes0', $verify_force_core_changes0);
		$view->set('verify_force_core_changes1', $verify_force_core_changes1);
		$view->set('verify_queries_count0', $verify_queries_count0);
		$view->set('verify_queries_count1', $verify_queries_count1);
		$view->set('verify_system_logging0', $verify_system_logging0);
		$view->set('verify_system_logging1', $verify_system_logging1);
		$view->set('verify_access_logging0', $verify_access_logging0);
		$view->set('verify_access_logging1', $verify_access_logging1);
		$view->set('verify_email_critical0', $verify_email_critical0);
		$view->set('verify_email_critical1', $verify_email_critical1);
		$view->set('sef_url0', $sef_url0);
		$view->set('sef_url1', $sef_url1);
		# <!-- ******************** SYSTEM SETTINGS ******************** -->
		// Set Values.
		$view->set('language_option', $language_option);
		$view->set('region_option', $region_option);
		$view->set('locale', $this->configuration['locale']);
		$view->set('timezone_options', $timezone_options);
		$view->set('date_format_show', $this->core->formatTimeDate($this->configuration['time'], 'default', $this->configuration['system_timezone']));
		$view->set('date_format_show_short', $this->core->formatTimeDate($this->configuration['time'], 'short', $this->configuration['system_timezone']));
		# <!-- ******************** TEMPLATE SETTINGS ******************** -->
		// Set Values.
		$view->set('template_option_', $template_option_);
		$view->set('node_behaviour_dynamic', $node_behaviour_dynamic);
		$view->set('node_behaviour_static', $node_behaviour_static);
		$view->set('skin_option_', $skin_option_);
		$view->set('loginandout_option', $loginandout_option);
		$view->set('frontpage_id_option', $frontpage_id_option);
		$view->set('frontpage_id_in_option', $frontpage_id_in_option);
		# <!-- ******************** EMAIL SETTINGS ******************** -->
		// Set Values.
		$view->set('email_option_mail', $email_option_mail);
		$view->set('email_option_sendmail', $email_option_sendmail);
		$view->set('email_option_smtp', $email_option_smtp);
		$view->set('smtp_secure_false', $smtp_secure_false);
		$view->set('smtp_secure_ssl', $smtp_secure_ssl);
		$view->set('smtp_secure_tls', $smtp_secure_tls);
		# <!-- ******************** REGISTRATION SETTINGS ******************** -->
		// Set Values.
		$view->set('verify_allow_registration1', $verify_allow_registration1);
		$view->set('verify_allow_registration2', $verify_allow_registration2);
		$view->set('verify_allow_registration3', $verify_allow_registration3);
		$view->set('verify_allow_registration0', $verify_allow_registration0);
		$view->set('verify_allow_remember_me0', $verify_allow_remember_me0);
		$view->set('verify_allow_remember_me1', $verify_allow_remember_me1);
		$view->set('registration_page_option', $registration_page_option);
		$view->set('verify_check0', $verify_check0);
		$view->set('verify_check1', $verify_check1);
		$view->set('verify_check2', $verify_check2);
		$view->set('verify_email_new_registrations0', $verify_email_new_registrations0);
		$view->set('verify_email_new_registrations1', $verify_email_new_registrations1);
		$view->set('user_roles_option_registration', $user_roles_option_registration);
		$view->set('user_roles_option_move', $user_roles_option_move);
		$view->set('guest_role_option', $guest_role_option);
		$view->set('banned_role_option', $banned_role_option);
		$view->set('user_groups_option_registration', $user_groups_option_registration);
		$view->set('user_groups_option_move', $user_groups_option_move);
		$view->set('guest_group_option', $guest_group_option);
		$view->set('languages_available_option', $languages_available_option);
		$view->set('languages_selected_template', $languages_selected_template);
		$view->set('regions_selected_template', $regions_selected_template);
		$view->set('regions_available_option', $regions_available_option);
		# <!-- ******************** UPLOAD OPTIONS ******************** -->
		// Set Values.
		$view->set('log_uploads1', $log_uploads1);
		$view->set('log_uploads0', $log_uploads0);
		$view->set('upload_path', $this->configuration['upload_path']);
		$view->set('writable', $writable);
		$view->set('max_filesize_show', $max_filesize_show);
		$view->set('max_imagesize_show', $max_imagesize_show);
		$view->set('do_create_thumb1', $do_create_thumb1);
		$view->set('do_create_thumb0', $do_create_thumb0);
		$view->set('graphics_engine_gd', $graphics_engine_gd);
		$view->set('graphics_engine_imagick', $graphics_engine_imagick);
		$view->set('adaptive_op', $adaptive_op);
		$view->set('resize_op', $resize_op);
		$view->set('resizepercent_op', $resizepercent_op);
		$view->set('cropfromcenter_op', $cropfromcenter_op);
		$view->set('crop_op', $crop_op);
		$view->set('do_thumb_reflect1', $do_thumb_reflect1);
		$view->set('do_thumb_reflect0', $do_thumb_reflect0);
		$view->set('do_create_resize_image1', $do_create_resize_image1);
		$view->set('do_create_resize_image0', $do_create_resize_image0);
		# <!-- ******************** FTP OPTIONS ******************** -->
		// Set Values.
		$view->set('ftp_enable0', $ftp_enable0);
		$view->set('ftp_enable1', $ftp_enable1);
		$view->set('ftp_ssl0', $ftp_ssl0);
		$view->set('ftp_ssl1', $ftp_ssl1);

		// Output Template.
		$view->show();
	}
}

return 'GeneralSettings';
