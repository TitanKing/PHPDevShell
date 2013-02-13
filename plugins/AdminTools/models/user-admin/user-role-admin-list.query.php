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
			_('Id') => 'user_role_id',
			_('Name') => 'user_role_name',
			_('Notes') => 'user_role_note'
        );
		$select_user_role = $pagination->query($this->sql);
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Set page to load.
        $page_edit   = $navigation->buildURL('edit-role', 'edit-role=');
        $page_delete = $navigation->buildURL(null, 'delete-role=');

		foreach ($select_user_role as $select_user_role_array) {
			$user_role_id = $select_user_role_array['user_role_id'];
			$user_role_name = $select_user_role_array['user_role_name'];
			$user_role_note = $select_user_role_array['user_role_note'];
			$translated_role_name = $user_role_name;

			$RESULTS['list'][] = array(
				'user_role_id' => $user_role_id,
				'translated_role_name' => '<a href="' . $page_edit . $user_role_id . '">' . $translated_role_name . '</a>',
				'user_role_note' => $user_role_note,
				'delete_role' => '<a href="' . $page_delete . $user_role_id . '" class="btn btn-mini first-click"><i class="icon-remove"></i></a>'
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
