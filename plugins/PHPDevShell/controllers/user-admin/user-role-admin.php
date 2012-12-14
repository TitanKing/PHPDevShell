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

		$user_role_id_db = (int) 0;
		$selected_menu = array();

		/* @var $crud crud */
		$crud = $this->factory('crud');

		if ($crud->GET('er') || $crud->POST('save')) {
			$this->template->heading(_('Edit Role Names'));
		} else {
			$this->template->heading(_('Add User Role'));
		}

		if ($crud->GET('er') || $crud->POST('save') || $crud->POST('new')) {

			if ($crud->GET('er')) {
				$crud->importFields($this->db->invokeQuery('PHPDS_readRoleUserQuery', $crud->GET('er')));
				$selected_menu = $this->db->invokeQuery('PHPDS_readRoleMenuQuery', $crud->GET('er'));
			}

			if ($crud->POST('save') || $crud->POST('new')) {

				$crud->addField('user_role_id');
				$crud->addField('user_role_note');

				if (!$crud->is('user_role_name'))
					$crud->error(_('Please enter a name for this role'));

				if ($crud->POST('permission')) {
					$permission = $crud->POST('permission');
				} else {
					$permission = array();
				}

				if ($this->db->doesRecordExist('_db_core_user_roles', 'user_role_name', "{$crud->f->user_role_name}", 'user_role_id', "{$crud->f->user_role_id}") == true)
					$crud->errorElse(sprintf(_('There is already a role record named %s. Cannot duplicate.'), $crud->f->user_role_name));

				if ($crud->ok()) {
					$crud->f->user_role_id = $this->db->invokeQuery('PHPDS_writeRoleQuery', $crud->f->user_role_id, $crud->f->user_role_name, $crud->f->user_role_note);
					$this->db->invokeQuery('PHPDS_deletePermissionsQuery', $crud->f->user_role_id);
					$this->db->invokeQuery('PHPDS_writePermissionsQuery', $crud->f->user_role_id, $permission);

					if (!empty($permission)) {
						foreach ($permission as $menu_id)
							$selected_menu[$menu_id] = 'selected';
					}

					$this->template->ok(sprintf(_('You have saved user role %s.'), $crud->f->user_role_name));
				} else {
					$crud->errorShow();
				}
			}
		}

		$menu_item_options = $this->db->invokeQuery('PHPDS_readMenusQuery', $selected_menu);

		$tagger = $this->tagger->tagArea('role', $crud->f->user_role_id, $crud->f->user_role_name);

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
		$view->set('menus_select', $menu_item_options);

		$view->show();
	}
}

return 'UserRoleAdmin';