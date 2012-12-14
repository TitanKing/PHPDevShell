<?php

/**
 * Controller Class: List of user roles.
 * @author Jason Schoeman
 * @return string
 */
class UserRoleAdminList extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Header information
		$this->template->heading(_('Manage User Roles'));

		// Should we delete groups users?
		if (!empty($this->security->get['dru'])) {
			// Do we have a naughty branch admin that is trying to delete things he should not?
			if (!$this->user->belongsToRole(false, $this->security->get['dru']) && $this->configuration['user_role'] != $this->security->get['dru']) {
				$this->template->warning(_('You do not have permission to remove this role.'));
				$error_[0] = true;
			}
			// We should be safe to delete now.
			if (empty($error_)) {

				if ($this->db->deleteQuick('_db_core_users', 'user_role', $this->security->get['dru'])) {
					// Delete old user roles values.
					$this->db->deleteQuick('_db_core_user_extra_roles', 'user_role_id', $this->security->get['dru']);
					$this->template->ok(sprintf(_('All user from role %s was deleted.'), $this->security->get['dru']));
				} else {
					$this->template->warning(sprintf(_('No users for role "%s" to delete.'), $this->security->get['dru']));
				}
			}
		}
		// Delete role.
		if (!empty($this->security->get['dr'])) {
			// Check if user is deleting core item.
			if (($this->security->get['dr'] != 1 && $this->security->get['dr'] != 2 && $this->security->get['dr'] != 3 && $this->security->get['dr'] != 4 && $this->security->get['dr'] != 5 && $this->security->get['dr'] != 6 && $this->security->get['dr'] != 7 && $this->security->get['dr'] != 8 && $this->security->get['dr'] != 9) || $this->configuration['force_core_changes'] == true) {
				// Delete role.
				$deleted_role = $this->db->deleteQuick('_db_core_user_roles', 'user_role_id', $this->security->get['dr'], 'user_role_name');
				// We also need to delete the extra roles from the list.
				$this->db->deleteQuick('_db_core_user_extra_roles', 'user_role_id', $this->security->get['dr']);
				// We also need to delete the user role permissions from the database.
				$this->db->deleteQuick('_db_core_user_role_permissions', 'user_role_id', $this->security->get['dr']);
				// We now need to set the user database to also delete this role from user if he belongs to it.
				$this->db->invokeQuery('PHPDS_updateUserQuery', $this->security->get['dr']);

				if ($deleted_role) {
					$this->template->ok(sprintf(_("Role %s was deleted."), $deleted_role));
				} else {
					$this->template->warning(sprintf(_('No role "%s" to delete.'), $this->security->get['dr']));
				}
			} else {
				$this->template->warning(_('You cannot delete a core item, the system will be unable to function correctly. Switch force core changes on in General Settings GUI for bypass.'));
			}
		}
		$RESULTS = $this->db->invokeQuery('PHPDS_readRoleQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		// Output Template.
		$view->show();
	}
}

return 'UserRoleAdminList';
