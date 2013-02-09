<?php

/**
 * Class supports delete, approve and ban action on pending users.
 *
 */
class userPending extends PHPDS_dependant
{
	/**
	 * Array of needed settings.
	 *
	 * @var array
	 */
	public $settings;
	/**
	 * Tell the system what text domain to use for language conversion.
	 *
	 * @var string
	 */
	private $d = 'AdminTools';

	/**
	 * Perform delete, approve and ban action on a single user in class.
	 *
	 * @param integer The user id to perform pending task on.
	 * @param string Action to take on pending user, au = approve user, aue = approve and email, bu = ban user, du = delete user.
	 * @param array Setting from settings database as array.
	 */
	public function pendingAction ($user_id = false, $action = false)
	{
		$configuration = $this->configuration;
		$db = $this->db;
		$user = $this->user;
		$template = $this->template;

		// Set appropriate action.
		if (empty($user_id) || empty($action) || empty($this->settings)) return false;
		// Define.
		$data['au'] = false;
		$data['aue'] = false;
		$data['bu'] = false;
		$data['du'] = false;
		// Load extra eMail class.
		$email = $this->factory('mailer');
		// Define correct action.
		switch ($action) {
			case 'au':
				$data['au'] = true;
				break;
			case 'aue':
				$data['aue'] = true;
				$data['au'] = true;
				break;
			case 'bu':
				$data['bu'] = true;
				break;
			case 'du':
				$data['du'] = true;
				break;
			default:
				return false;
				break;
		}
		// Make sure this user is in queue to be approved.
		$check = $db->invokeQuery('PHPDS_readPendingQueueQuery', $user_id);

		// Make sure we have no empty user role/group ids.
		(empty($check['user_group_id'])) ? $gid = $this->settings['move_verified_group'] : $gid = $check['user_group_id'];
		// Do we have a naughty lower level admin that is trying to manipulate things he should not?
		if (! $user->belongsToGroup(false, $gid) || $configuration['user_id'] == $user_id) $error[0] = true;
		// Check if we have an Approve or Ban action.
		if (! empty($data['au']) || ! empty($data['bu'])) {
			// Error check.
			if (empty($check['registration_type'])) {
				// Print out error.
				$template->warning(sprintf(__('There is no user with user id %s in registration queue. Please see User Admin for a list of all users.'), $user_id));
				$error[1] = true;
			} else {
				if (! empty($data['au'])) {
					// Check and assign group and role.
					if (empty($check['user_role_id'])) $check['user_role_id'] = $this->settings['move_verified_role'];
					if (empty($check['user_group_id'])) $check['user_group_id'] = $this->settings['move_verified_group'];
				} else {
					// Set ban role.
					$check['user_role_id'] = $this->settings['banned_role'];
					$check['user_group_id'] = 0;
				}
			}
		}

		// Checking alter conditions.
		if (empty($error)) {
			// Update user status according to ban or approve.
			if (! empty($data['au']) || ! empty($data['bu'])) {
				// All went well, lets finally approve this user.
				// Update the users role and group status.
				$db->invokeQuery('PHPDS_updatePendingQueueQuery', $check['user_role_id'], $check['user_group_id'], $user_id);
				// It was updated so lets clear user from queue.
				$db->deleteQuick('_db_core_registration_queue', 'user_id', $user_id);
				// Set approve or ban ok.
				$process_complete = true;
			}
			// Approve User.
			if (! empty($data['au'])) {
				// Decuct token, if available.
				if (! empty($check['available_tokens'])) {
					$db->invokeQuery('PHPDS_updateTokensQueueQuery', $check['token_id']);
				}
				// Show ok message.
				if (! empty($process_complete)) {
					// Check if we have an email address before trying to send out notification email.
					if (! empty($check['user_email']) && ! empty($data['aue'])) {
						// Create email message for notification.
						$verification_message = sprintf(__("Dear %s, Your registration request was approved at %s. Thank you for registering at %s.",'core.lang'), $check['user_display_name'], $configuration['scripts_name_version'], $configuration['absolute_url']);
						$verification_subject = sprintf(__('Registration request approved at %s.','core.lang'), $configuration['scripts_name_version']);
						$verification_to = $check['user_email'];
						// Send the verification email.
						$email->sendmail("$verification_to", $verification_subject, $verification_message);

						// Create email message notification for admins.
						$verification_message_admin = sprintf(__("Dear Admin, Registration request was approved for %s. Thank you for registering at %s.",'core.lang'), $check['user_display_name'], $configuration['absolute_url']);
						$verification_subject_admin = sprintf(__('Approved confirmation for %s','core.lang'), $check['user_display_name']);
						$verification_to_admin = $this->settings['setting_admin_email'];
						// Send the verification email.
						$email->sendmail("$verification_to_admin", $verification_subject_admin, $verification_message_admin);
					}
					$template->ok(sprintf(__('User %s approved.','core.lang'), $check['user_display_name']));
				}
			} else if (! empty($data['bu'])) {
				// Show ok message.
				if ($process_complete) $template->ok(sprintf(__('User %s banned.','core.lang'), $check['user_display_name']));
			} else if (! empty($data['du'])) {
				// Delete user.
				$db->deleteQuick('_db_core_users', 'user_id', $user_id);
				// Delete all search filters.
				$db->deleteQuick('_db_core_filter', 'user_id', $user_id);
				// Clear user from queue.
				$db->deleteQuick('_db_core_registration_queue', 'user_id', $user_id);

				// Show ok message.
				$template->ok(sprintf(__('User %s deleted.','core.lang'), $check['user_display_name']));
			}
		} else {
			$template->warning(sprintf(__('You cannot complete this action on user %s, he was processed already, could not be found or you don\'t have the required permission.','core.lang'), $check['user_display_name']));
		}
	}
}
