<?php

/**
 * User Role Admin List - Update User
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_updateUserQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_users
		SET
			user_role = false
		WHERE
			user_role = %u
    ";
	protected $returnId = true;
}

/**
 * User Role Admin List - Read Roles
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readRoleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_role_id, user_role_name, user_role_note
		FROM
			_db_core_user_roles
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$navigation = $this->navigation;
		$template = $this->template;
		$core = $this->core;

		// Initiate pagination plugin.
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('User Role ID') => 'user_role_id',
			_('User Role Name') => 'user_role_name',
			_('User Role Notes') => 'user_role_note',
			_('Edit') => '',
			_('Delete Role') => '',
			_('Delete Role Users') => '');
		$select_user_role = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Set page to load.
		$page_edit = $navigation->buildURL(1405303115, 'er=');
		$page_delete = $navigation->buildURL(false, 'dr=');
		$page_delete_users = $navigation->buildURL(false, 'dru=');

		// Icons.
		$edit_role_icon = $template->icon('key--pencil', _('Edit Role'));
		$delete_role_icon = $template->icon('key--minus', _('Delete Role'));
		$delete_role_users = $template->icon('user--minus', _('Delete Role Users'));

		foreach ($select_user_role as $select_user_role_array) {
			$user_role_id = $select_user_role_array['user_role_id'];
			$user_role_name = $select_user_role_array['user_role_name'];
			$user_role_note = $select_user_role_array['user_role_note'];
			$translated_role_name = $user_role_name;

			$RESULTS['list'][] = array(
				'user_role_id' => $user_role_id,
				'translated_role_name' => $translated_role_name,
				'user_role_note' => $user_role_note,
				'edit_role' => "<a href=\"{$page_edit}{$user_role_id}\" class=\"button\">{$edit_role_icon}</a>",
				'delete_role' => "<a href=\"{$page_delete}{$user_role_id}\" {$core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $translated_role_name))} class=\"button\">{$delete_role_icon}</a>",
				'delete_role_users' => "<a href=\"{$page_delete_users}{$user_role_id}\" {$core->confirmLink(sprintf(_('Delete ALL USERS of role %s?'), $translated_role_name))} class=\"button\">{$delete_role_users}</a>"
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
