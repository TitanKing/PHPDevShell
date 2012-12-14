<?php

/**
 * Controller Class: Users that are listed in pending queue for approval.
 * @author Jason Schoeman
 * @return string
 */
class UserAdminPending extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Heading.
		$this->template->heading(_('User Registration Pending'));

		// Get default verified role and group.
		$settings = $this->db->getSettings(array('move_verified_group', 'move_verified_role', 'banned_role', 'setting_admin_email'));
		// Load pending support class.
		$user_pending = $this->factory('userPending');
		// User actions.
		$userAction = $this->factory('userActions');
		
		$user_pending->settings = $settings;
		
		// Single user action needs to be performed.
		// approve
		if (!empty($this->security->get['au'])) {
			$user_pending->pendingAction($this->security->get['au'], 'au');
			$get_user_info_array = $this->db->invokeQuery('PHPDS_readUserQuery', $this->security->get['au']);
			$userAction->pendingUserApproved($get_user_info_array);
		} // approve & email
		else if (!empty($this->security->get['aue'])) {
			$user_pending->pendingAction($this->security->get['aue'], 'aue');
			$get_user_info_array = $this->db->invokeQuery('PHPDS_readUserQuery', $this->security->get['aue']);
			$userAction->pendingUserApproved($get_user_info_array);
		} // ban user
		else if (!empty($this->security->get['bu'])) {
			$user_pending->pendingAction($this->security->get['bu'], 'bu');
			$get_user_info_array = $this->db->invokeQuery('PHPDS_readUserQuery', $this->security->get['bu']);
			$userAction->pendingUserBanned($get_user_info_array);
		} // delete user
		else if (!empty($this->security->get['du'])) {
			$user_pending->pendingAction($this->security->get['du'], 'du');
			$get_user_info_array = $this->db->invokeQuery('PHPDS_readUserQuery', $this->security->get['du']);
			$userAction->pendingUserDeleted($get_user_info_array);
		}
		// Do we have a post situation to do mass action on users?
		if (!empty($this->security->post)) {
			// Define.
			if (!empty($this->security->post['user_id_array'])) {
				$user_id_array = $this->security->post['user_id_array'];
			} else {
				$user_id_array = 0;
			}
			// Start checking post type.
			// approve
			if (!empty($this->security->post['au'])) {
				$action = 'au';
			} // approve & email
			else if (!empty($this->security->post['aue'])) {
				$action = 'aue';
			} // ban user
			else if (!empty($this->security->post['bu'])) {
				$action = 'bu';
			} // delete user
			else if (!empty($this->security->post['du'])) {
				$action = 'du';
			}
			// Do a loop and see what we have to cook.
			if (!empty($user_id_array)) {
				$userAction->pendingUserMassAction($user_id_array, $action);
				// Start looping and doing actions.
				foreach ($user_id_array as $user_id__) {
					$user_pending->pendingAction($user_id__, $action);
				}
			}
		}
		
		$RESULTS = $this->db->invokeQuery('PHPDS_readPending', $settings['move_verified_role'], $settings['move_verified_group']);

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		// Set Buttons.
		$view->set('approve_users', _('Approve Users'));
		$view->set('approve_users_email_notification', _('Approve Users & Email Notification'));
		$view->set('ban_users', _('Ban Users'));
		$view->set('delete_users', _('Delete Users'));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('post_validation', $this->security->postValidation());
		$view->set('confirm_bu', $this->core->confirmLink(sprintf('Are you sure you want to BAN all users on this page?')));
		$view->set('confirm_du', $this->core->confirmLink(sprintf('Are you sure you want to DELETE all users on this page?')));

		// Output Template.
		$view->show();
	}
}

return 'UserAdminPending';