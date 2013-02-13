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
		$this->template->heading(__('Manage User Groups'));

        if ($this->P('checkgroup')) {

            $idsarr = $this->P('checkgroup');

            if ($this->P('deletegroup')) {
                $deleted_groups = '';
                if (! empty($idsarr)) {
                    foreach($idsarr as $iddelete => $val) {
                        if (!$this->user->belongsToGroup(false, $iddelete) && $this->configuration['user_group'] != $iddelete) {
                            $this->template->warning(__('Permission denied.'));
                            $error[0] = true;
                        }
                        if (empty($error)) {
                            $group_deleted = $this->db->deleteQuick('_db_core_user_groups', 'user_group_id', $iddelete, 'user_group_name');
                            $this->db->deleteQuick('_db_core_user_extra_groups', 'user_group_id', $iddelete);
                            $this->db->invokeQuery('PHPDS_updateDeletedGroupUsersQuery', $iddelete);

                            if ($group_deleted) {
                                 $deleted_groups .= sprintf(__('Group %s was deleted.'), $group_deleted);
                            } else {
                                $this->template->warning(sprintf(__('No group "%s" to delete.'), $iddelete));
                            }
                        }
                    }
                    if (! empty($deleted_groups)) $this->template->ok($deleted_groups);
                }
            }

            if ($this->P('deleteusers'))  {
                $deleted_users = '';
                if (! empty($idsarr)) {
                    foreach($idsarr as $iddelete => $val) {
                        if ($iddelete) {
                            if (!$this->user->belongsToGroup(false, $iddelete) && $this->configuration['user_group'] != $iddelete) {
                                $this->template->warning(__('Permission denied.'));
                                $error_[0] = true;
                            }
                            if (empty($error_)) {
                                if ($this->db->deleteQuick('_db_core_users', 'user_group', $iddelete)) {
                                    $this->db->deleteQuick('_db_core_user_extra_groups', 'user_group_id', $iddelete);
                                    $deleted_users .= sprintf(__('Group %s users deleted.'), $iddelete);
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

		$RESULTS = $this->db->invokeQuery('PHPDS_readGroupQuery');

		$view = $this->factory('views');
		$view->set('RESULTS', $RESULTS);
        $view->set('self_url', $this->navigation->selfUrl());

		$view->show();
	}
}

return 'UserGroupAdminList';
