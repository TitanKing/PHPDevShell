<?php

/**
 * Registration Token List - Read available tokens.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readTokenListQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.token_id, t1.token_name, t1.user_role_id, t1.user_group_id, t1.token_key, t1.registration_option, t1.available_tokens,
			t2.user_role_name, t3.user_group_name
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

		// Set page to load.
		$page_edit = $navigation->buildURL(2200445609, 'erg=');
		$page_delete = $navigation->buildURL(false, 'drg=');
		$page_check = $navigation->buildURL(false, 'ck=');
		$page_token = $navigation->buildURL(1886139891, 'token_id=');

		// Initiate pagination plugin.
		$pagination = $this->factory('pagination');
		$pagination->columns = array(
			_('Token ID') => 't1.token_id',
			_('Token Name') => 't1.token_name',
			_('Move User to Role') => 't2.user_role_name',
			_('Move User to Group') => 't3.user_group_name',
			_('Token Key') => 't1.token_key',
			_('Display for Registration') => '',
			_('Available Tokens') => '',
			_('Token URL') => '',
			_('Email Invitation') => '',
			_('Edit') => '',
			_('Delete') => '');
		$select_registration_tokens = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Icons.
		$registration_icon_1 = $template->icon('eye', _('Show as registration option'));
		$registration_icon_2 = $template->icon('eye--exclamation', _('Dont show as registration option'));
		$run_icon = $template->icon('chain--arrow', _('View registration URL'));
		$mail_icon = $template->icon('mail--arrow', _('Mail Token'));
		$edit_icon = $template->icon('key--pencil', _('Edit Token'));
		$delete_icon = $template->icon('key--minus', _('Delete Token'));

		foreach ($select_registration_tokens as $select_registration_tokens_array) {
			$token_id = $select_registration_tokens_array['token_id'];
			$token_name = $select_registration_tokens_array['token_name'];
			$registration_option = $select_registration_tokens_array['registration_option'];
			$token_key = $select_registration_tokens_array['token_key'];
			$user_role_name = $select_registration_tokens_array['user_role_name'];
			$user_group_name = $select_registration_tokens_array['user_group_name'];
			$available_tokens = $select_registration_tokens_array['available_tokens'];

			// Check if we allow this Token as a registration option.
			if ($registration_option) {
				$registration_option = $registration_icon_1;
			} else {
				$registration_option = $registration_icon_2;
			}
			// Check how many tokens are available and color it.
			if (empty($available_tokens)) {
				$available_tokens_ = $template->notice('<strong>' . $available_tokens . '</strong>', true);
			} else {
				$available_tokens_ = $template->ok('<strong>' . $available_tokens . '</strong>', true, false);
			}
			$RESULTS['list'][] = array(
				'token_id' => $token_id,
				'token_name' => $token_name,
				'registration_option' => $registration_option,
				'user_role_name' => $user_role_name,
				'user_group_name' => $user_group_name,
				'token_key' => $token_key,
				'available_tokens_' => $available_tokens_,
				'run' => "<a href=\"{$page_check}{$token_id}\" class=\"button\">{$run_icon}</a>",
				'mail' => "<a href=\"{$page_token}{$token_id}\" class=\"button\">{$mail_icon}</a>",
				'edit' => "<a href=\"{$page_edit}{$token_id}\" class=\"button\">{$edit_icon}</a>",
				'delete' => "<a href=\"{$page_delete}{$token_id}\" {$core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $token_name))} class=\"button\">{$delete_icon}</a>"
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