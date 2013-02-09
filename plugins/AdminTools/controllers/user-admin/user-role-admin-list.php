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
		$this->template->heading(__('Manage User Roles'));

        if ($this->P('checkrole')) {

            $idsarr = $this->P('checkrole');

            if ($this->P('deleterole'))  {
                $deleted_roles = '';
                if (! empty($idsarr)) {
                    foreach($idsarr as $iddelete => $val) {

                        $iddelete_condition =  (
                            $iddelete != 1 &&
                            $iddelete != 2 &&
                            $iddelete != 3 &&
                            $iddelete != 4 &&
                            $iddelete != 5 &&
                            $iddelete != 6 &&
                            $iddelete != 7 &&
                            $iddelete != 8 &&
                            $iddelete != 9
                        );

                        // Check if user is deleting core item.
                        if ($iddelete_condition || $this->configuration['force_core_changes'] == true) {

                            // Delete role.
                            $deleted_role = $this->db->deleteQuick('_db_core_user_roles', 'user_role_id',  $iddelete, 'user_role_name');
                            $this->db->deleteQuick('_db_core_user_role_permissions', 'user_role_id',  $iddelete);
                            $this->db->invokeQuery('PHPDS_updateUserQuery',  $iddelete);

                            if ($deleted_role) {
                                $deleted_roles .= sprintf(__("Role %s was deleted."), $deleted_role);
                            } else {
                                $this->template->warning(sprintf(__('No role "%s" to delete.'),  $iddelete));
                            }
                        } else {
                            $this->template->warning(__('You cannot delete a core item.'));
                        }
                    }
                    if (! empty($deleted_roles)) $this->template->ok($deleted_roles);
                }
            }

            if ($this->P('deleteusers'))  {
                $deleted_users = '';
                if (! empty($idsarr)) {
                    foreach($idsarr as $iddelete => $val) {
                        if ($iddelete) {
                            if (!$this->user->belongsToRole(false, $iddelete) && $this->configuration['user_role'] != $iddelete) {
                                $this->template->warning(__('Permission denied.'));
                                $error_[0] = true;
                            }
                            if (empty($error_)) {
                                if ($this->db->deleteQuick('_db_core_users', 'user_role', $iddelete)) {
                                    $deleted_users .= sprintf(__('Role %s users deleted.'), $iddelete);
                                } else {
                                    $this->template->warning(sprintf(__('No users for role "%s" to delete.'), $iddelete));
                                }
                            }
                        }
                    }
                    if (! empty($deleted_users)) $this->template->ok($deleted_users);
                }
            }
        }

		$RESULTS = $this->db->invokeQuery('PHPDS_readRoleQuery');

		$view = $this->factory('views');

		$view->set('self_url', $this->navigation->buildURL());
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		$view->show();
	}
}

return 'UserRoleAdminList';
