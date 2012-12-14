<?php

/**
 * User Admin List - Select User Data
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readUserQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_display_name, user_password, user_name, user_email, user_group, user_role, language, timezone as user_timezone, region
		FROM
			_db_core_users
		WHERE
			user_id = %u
    ";

	protected $singleRow = true;
}

/**
 * User Admin Pending - Default Role Name
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_defaultRoleName extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_role_name
			FROM
				_db_core_user_roles
			WHERE
				user_role_id = %u
    ";
	protected $singleValue = true;
}

/**
 * User Admin Pending - Default Group Name
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_defaultGroupName extends PHPDS_query
{
	protected $sql = "
			SELECT
				user_group_name
			FROM
				_db_core_user_groups
			WHERE
				user_group_id = %u
    ";
	protected $singleValue = true;
}

/**
 * User Admin Pending - Read Pending Users
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readPending extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id, t1.registration_type, t1.token_id,
			t2.token_name, t2.user_role_id, t2.user_group_id, t2.token_key, t2.available_tokens,
			t3.user_display_name, t3.user_name, t3.user_email, t3.date_registered,
			t4.user_role_name,
			t5.user_group_name
		FROM
			_db_core_registration_queue t1
		LEFT JOIN
			_db_core_registration_tokens t2
		ON
			t1.token_id = t2.token_id
		LEFT JOIN
			_db_core_users t3
		ON
			t1.user_id = t3.user_id
		LEFT JOIN
			_db_core_user_roles t4
		ON
			t2.user_role_id = t4.user_role_id
		LEFT JOIN
			_db_core_user_groups t5
		ON
			t2.user_group_id = t5.user_group_id
		%s
	";

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

		$settings['move_verified_role'] = $parameters[0];
		$settings['move_verified_group'] = $parameters[1];
		
		// Get default group and role names.
		$default_role_name = $this->db->invokeQuery('PHPDS_defaultRoleName', $settings['move_verified_role']);
		$default_group_name = $this->db->invokeQuery('PHPDS_defaultGroupName', $settings['move_verified_group']);
		
		// Check if this user can move default registrations.
		($this->user->belongsToGroup(false, $settings['move_verified_group'])) ? $can_move_default = true : $can_move_default = false;

		// Set page to load.
		$page_edit = $navigation->buildURL('885145814', 'eu=');
		$page_approve = $navigation->buildURL(false, 'au=');
		$page_approve_email = $navigation->buildURL(false, 'aue=');
		$page_ban = $navigation->buildURL(false, 'bu=');
		$page_delete = $navigation->buildURL(false, 'du=');

		// Icons.
		$pending_icon_1 = $template->icon('mail--exclamation', _('Awaiting email verification appoval'));
		$pending_icon_2 = $template->icon('table-import', _('Import awaiting approval'));
		$approve_icon = $template->icon('user--plus', _('Approve User'));
		$approve_email_icon = $template->icon('user--arrow', _('Approve User and Email Notification'));
		$ban_icon = $template->icon('user--exclamation', _('Ban User'));
		$edit_icon = $template->icon('user--pencil', _('Edit User'));
		$delete_user_icon = $template->icon('user--minus', _('Delete User'));

		// Initiate pagination plugin.
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('User ID') => 't1.user_id',
			_('User Name') => 't3.user_name',
			_('Display Name') => 't3.user_display_name',
			_('User Email') => 't3.user_email',
			_('User Roles') => 't4.user_role_name',
			_('User Groups') => 't5.user_group_name',
			_('Token Name') => 't2.token_name',
			_('Date Registered') => 't3.date_registered',
			_('Pending Type') => '',
			_('Approval') => '',
			_('Ban') => '',
			_('Edit') => '',
			_('Delete') => '');
		$pagination->condition = 'AND';
		$pagination->dateColumn = 't3.date_registered';
		$select_users = $pagination->query($this->sql, $this->user->setGroupQuery("WHERE t2.user_group_id IS NULL OR t2.user_group_id IN ({$this->user->getGroups()})", "WHERE t1.user_id != 'x'"));
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();
		// Define.
		foreach ($select_users as $select_users_array) {
			$user_id = $select_users_array['user_id'];
			$registration_type = $select_users_array['registration_type'];
			$token_name = $select_users_array['token_name'];
			$user_name = $select_users_array['user_name'];
			$user_display_name = $select_users_array['user_display_name'];
			$user_email = $select_users_array['user_email'];
			$user_role_name = $select_users_array['user_role_name'];
			$user_role_id = $select_users_array['user_role_id'];
			$user_group_name = $select_users_array['user_group_name'];
			$user_group_id = $select_users_array['user_group_id'];
			$date_registered = $core->formatTimeDate($select_users_array['date_registered']);
			// We are ASSUMING this is a default registration move and not a token!
			// Make sure we have the ids.
			if (empty($user_role_id)) $user_role_id = $settings['move_verified_role'];
			// Check if we have a pending default registration or token registration.
			if (empty($user_group_id)) {
				// Snap! This must be a normal registration pending!
				$user_group_id = $settings['move_verified_group'];
				// Lets see if this user has the right to move normal registrations.
				if (empty($can_move_default)) continue;
			}

			// Check if we have a token name.
			if (empty($token_name)) $token_name = _('Default Registration');
			// Make sure we have a role and group.
			if (empty($user_role_name)) $user_role_name = $default_role_name;
			if (empty($user_group_name)) $user_group_name = $default_group_name;
			// Check registration type.
			if ($registration_type == 1) {
				$pending_type = $pending_icon_1;
			} else {
				$pending_type = $pending_icon_2;
			}

			// Create results array.
			$RESULTS['list'][] = array(
				'user_id' => $user_id,
				'date_registered' => $date_registered,
				'user_name' => $user_name,
				'user_display_name' => $user_display_name,
				'user_email' => $user_email,
				'user_role_name' => $user_role_name,
				'user_role_id' => $user_role_id,
				'user_group_name' => $user_group_name,
				'user_group_id' => $user_group_id,
				'token_name' => $token_name,
				'pending_type' => $pending_type,
				'approve' => "<a href=\"{$page_approve}{$user_id}\" class=\"button\">{$approve_icon}</a>",
				'approve_email' => "<a href=\"{$page_approve_email}{$user_id}\" class=\"button\">{$approve_email_icon}</a>",
				'ban' => "<a href=\"{$page_ban}{$user_id}\" class=\"button\">{$ban_icon}</a>",
				'edit' => "<a href=\"{$page_edit}{$user_id}\" class=\"button\">{$edit_icon}</a>",
				'delete' => "<a href=\"{$page_delete}{$user_id}\" {$core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $user_display_name))} class=\"button\">{$delete_user_icon}</a>"
			);
		}
		if (! empty($RESULTS['list'])) {
			return $RESULTS;
		} else {
			$RESULTS['list'] = array();
			return $RESULTS;
		}
	}
}