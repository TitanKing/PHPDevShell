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
        $this->template->heading(__('Access Roles'));

		$RESULTS = $this->db->invokeQuery('PHPDS_readRoleQuery');

        /* @var $view views */
		$view = $this->factory('views');

		$view->set('self_url', $this->navigation->buildURL());
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		$view->show();
	}

    public function viaAJAX()
    {
        if ($this->G('delete-role')) return $this->deleteRole();
        return 'false';
    }

    public function deleteRole ()
    {
        $iddelete = $this->G('delete-role');
        // Delete role.
        $deleted_role = $this->db->deleteQuick('_db_core_user_roles', 'user_role_id',  $iddelete, 'user_role_name');
        $this->db->deleteQuick('_db_core_user_role_permissions', 'user_role_id',  $iddelete);
        $this->db->invokeQuery('PHPDS_updateUserQuery',  $iddelete);
        if ($deleted_role) {
            $this->template->ok(sprintf(__("Role %s was deleted."), $deleted_role));
            return 'true';
        } else {
            $this->template->note(sprintf(__('No role "%s" to delete.'),  $iddelete));
            return 'false';
        }
    }
}

return 'UserRoleAdminList';
