<?php

/**
 * Controller Class: List groups.
 * @author Jason Schoeman
 * @return string
 */
class UserGroupAdminList extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Header information
		$this->template->heading(_('User Group Management'));

		// Should we delete groups users?
		if (!empty($this->security->get['dgu'])) {
			// Do we have a naughty branch admin that is trying to delete things he should not?
			if (!$this->user->belongsToGroup(false, $this->security->get['dgu']) && $this->configuration['user_group'] != $this->security->get['dgu']) {
				$this->template->warning(_('You cannot remove users under this group. You do not have the required permissions or you are in the same group.'));
				$error_[0] = true;
			}
			// We should be safe to delete now.
			if (empty($error_)) {
				if ($this->db->deleteQuick('_db_core_users', 'user_group', $this->security->get['dgu'])) {
					// Delete old user groups values.
					$this->db->deleteQuick('_db_core_user_extra_groups', 'user_group_id', $this->security->get['dgu']);
					$this->template->ok(sprintf(_('All user from group %s was deleted.'), $this->security->get['dgu']));
				} else {
					$this->template->warning(sprintf(_('No users for group "%s" to delete.'), $this->security->get['dgu']));
				}
			}
		}
		// Should we delete group?
		if (!empty($this->security->get['dg'])) {
			// Do we have a naughty branch admin that is trying to delete things he should not?
			if (!$this->user->belongsToGroup(false, $this->security->get['dg']) && $this->configuration['user_group'] != $this->security->get['dg']) {
				$this->template->warning(_('You cannot delete this group. You do not have the required permissions or you fall under this group.'));
				$error[0] = true;
			}
			// We should be safe to delete now.
			if (empty($error)) {
				// Lets delete the item.
				$group_deleted = $this->db->deleteQuick('_db_core_user_groups', 'user_group_id', $this->security->get['dg'], 'user_group_name');
				// We also need to delete the extra groups from the list.
				$this->db->deleteQuick('_db_core_user_extra_groups', 'user_group_id', $this->security->get['dg']);
				// We now need to set the user database to also delete this group from user if he belongs to it.
				$this->db->invokeQuery('PHPDS_updateDeletedGroupUsersQuery', $this->security->get['dg']);

				if ($group_deleted) {
					$this->template->ok(sprintf(_('Group %s was deleted.'), $group_deleted));
				} else {
					$this->template->warning(sprintf(_('No group "%s" to delete.'), $this->security->get['dg']));
				}
			}
		}
		$RESULTS = $this->db->invokeQuery('PHPDS_readGroupQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);

		// Output Template.
		$view->show();
	}
}

return 'UserGroupAdminList';
