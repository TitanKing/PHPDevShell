<?php

/**
 * Controller Class: User Role Administration
 * @author Jason Schoeman
 * @return string
 */
class UserRoleAdmin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		/* @var $crud crud */
		$crud = $this->factory('crud');
        $permission = array();

		if ($crud->GET('er') || $crud->POST('save')) {
			$this->template->heading(__('Edit User Role'));
		} else {
			$this->template->heading(__('Add User Role'));
		}

		if ($crud->GET('er') || $crud->POST('save') || $crud->POST('new')) {

			if ($crud->GET('er')) {
				$crud->importFields($this->db->invokeQuery('PHPDS_readRoleUserQuery', $crud->GET('er')));
				$permission = $this->db->invokeQuery('PHPDS_readRoleNodeQuery', $crud->GET('er'));
			}

			if ($crud->POST('save') || $crud->POST('new')) {

				$crud->addField('user_role_id');
				$crud->addField('user_role_note');

				if (!$crud->is('user_role_name'))
					$crud->error();

				if ($crud->POST('permission')) {
					$permission = $crud->POST('permission');
				} else {
					$permission = array();
				}

				if ($this->db->doesRecordExist('_db_core_user_roles', 'user_role_name', "{$crud->f->user_role_name}", 'user_role_id', "{$crud->f->user_role_id}") == true)
					$crud->errorElse(sprintf(__('%s exists already.'), $crud->f->user_role_name));

				if ($crud->ok()) {
					$crud->f->user_role_id = $this->db->invokeQuery('PHPDS_writeRoleQuery', $crud->f->user_role_id, $crud->f->user_role_name, $crud->f->user_role_note);
					$this->db->invokeQuery('PHPDS_deletePermissionsQuery', $crud->f->user_role_id);
					$this->db->invokeQuery('PHPDS_writePermissionsQuery', $crud->f->user_role_id, $permission);

					$this->template->ok(sprintf(__('Saved %s.'), $crud->f->user_role_name));
				} else {
					$crud->errorShow();
				}
			}
		}

		$node_item_options = $this->db->invokeQuery('PHPDS_readNodesQuery', $permission);

		$tagger = $this->tagger->tagArea('role', $crud->f->user_role_id, $this->P('tagger_name'), $this->P('tagger_value'), $this->P('tagger_id'), $this->P('tagger_delete'));

		if ($crud->POST('new')) {
			$crud->f->user_role_id = 0;
			$crud->f->user_role_name = '';
			$crud->f->user_role_note = '';
		}

		$view = $this->factory('views');

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('user_role_id', $crud->f->user_role_id);
		$view->set('user_role_name', $crud->f->user_role_name);
		$view->set('user_role_note', $crud->f->user_role_note);
		$view->set('tagger', $tagger);
		$view->set('nodes_select', $node_item_options);

		$view->show();
	}
}

return 'UserRoleAdmin';