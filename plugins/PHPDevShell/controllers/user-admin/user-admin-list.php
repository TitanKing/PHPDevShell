<?php

/**
 * Controller Class: List all users stored in database.
 * @author Jason Schoeman
 * @return string
 */
class UserAdminList extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Heading.
		$this->template->heading(__('Users Management'));

		// User actions.
		$userAction = $this->factory('userActions');

		// Delete user if so requested.
		if (!empty($this->security->get['du'])) {
			// Lets see what primary group this user belongs to.
			$get_delete_user_info_array = $this->db->invokeQuery('PHPDS_readUserQuery', $this->security->get['du']);
			// Do we have a naughty lower level admin that is trying to delete things he should not?
			if (!$this->user->belongsToGroup(false, $get_delete_user_info_array['user_group']) || $this->configuration['user_id'] == $this->security->get['du']) {
				$this->template->warning(sprintf(__('You cannot delete user id (%s)'), $this->security->get['du']));
				$error[0] = true;
			}
			// We should be safe to delete now.
			if (empty($error)) {
				// Delete user detail.
				$this->db->invokeQuery('PHPDS_deleteUserQuery', $this->security->get['du']);

				if ($get_delete_user_info_array['user_display_name']) {
					$this->template->ok(sprintf(__('User %s deleted.'), $get_delete_user_info_array['user_display_name']));
					// Send to user action class.
					$userAction->userDelete($get_delete_user_info_array);
				} else {
					$this->template->warning(sprintf(__('No user "%s" to delete.'), $this->security->get['du']));
				}
			}
		}
		//////////////////////////
		// Get all user roles.  //
		//////////////////////////
		$role_detail = $this->db->invokeQuery('PHPDS_readRoleQuery');
		$extra_role_array = $role_detail['name'];
		$does_roles_id_exist = $role_detail['selected'];
		//////////////////////////
		//////////////////////////
		//////////////////////////
		//////////////////////////
		// Get all user groups. //
		//////////////////////////
		$group_detail = $this->db->invokeQuery('PHPDS_readGroupQuery');
		$extra_group_array = $group_detail['name'];
		$does_groups_id_exist = $group_detail['selected'];
		//////////////////////////
		//////////////////////////
		//////////////////////////
		// Check to see if user is updating roles.
		if (!empty($this->security->post['save'])) {

			$this->db->invokeQuery('PHPDS_writePrimaryPermissionQuery');
			$this->db->invokeQuery('PHPDS_writeRoleQuery');
			$this->db->invokeQuery('PHPDS_writeGroupQuery');
			//////////////////////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////////////////////////
			// After database replacing show success message.
			$this->template->ok(__('All modified user settings was saved.'));
			$userAction->userMultipleUpdate($this->security->post);
		}
		$RESULTS = $this->db->invokeQuery('PHPDS_readUsersQuery', $extra_role_array, $extra_group_array);

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		// Set Button.
		$view->set('save_user_changes', __('Save User Changes'));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('post_validation', $this->security->postValidation());

		// Output Template.
		$view->show();
	}
}

return 'UserAdminList';
