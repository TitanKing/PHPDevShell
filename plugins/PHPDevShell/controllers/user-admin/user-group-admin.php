<?php

/**
 * Controller Class: User Group Administration
 * @author Jason Schoeman
 * @return string
 */
class UserGroupAdmin extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{

		/* @var $crud crud */
		$crud = $this->factory('crud');

		if ($crud->GET('eg') || $crud->POST('save')) {
			$this->template->heading(_('Edit Group Names'));
			if ($crud->GET('eg')) $crud->f->user_group_id = $crud->GET('eg');
		} else {
			$this->template->heading(_('Add Groups'));
		}

		if ($crud->POST('save') || $crud->POST('new')) {
			$crud->addField('user_group_id');
			$crud->addField('parent_group_id');
			$crud->addField('user_group_note');
			$crud->addField('alias');

			if (!$crud->is('user_group_name'))
				$crud->error(_('Please enter a name for this group'));

			if ($this->db->doesRecordExist('_db_core_user_groups', 'user_group_name', "{$crud->f->user_group_name}", 'user_group_id', "{$crud->f->user_group_id}") == true)
				$crud->error(sprintf(_('There is already a role record named %s. Cannot duplicate.'), $crud->f->user_group_name), 'user_group_name');

			if (!empty($crud->f->user_group_id)) {
				if (!$this->user->belongsToGroup(false, $crud->f->user_group_id))
					$crud->errorElse(_('You cannot save this group since you are not allocated to it.'));
			}

			if ($crud->ok()) {
				$crud->f->user_group_id = $this->db->invokeQuery('PHPDS_writeGroupQuery', $crud->f->user_group_id, $crud->f->user_group_name, $crud->f->user_group_note, $crud->f->parent_group_id, $crud->f->alias);
				if (! empty($crud->f->user_group_id)) {
					$this->db->invokeQuery('PHPDS_writeExtraGroupQuery', $this->configuration['user_id'], $crud->f->user_group_id);
					$this->template->ok(sprintf(_('User id %s was added to group id %s.'), $this->configuration['user_id'], $crud->f->user_group_id));
					$this->db->cacheClear("groups_{$this->configuration['user_id']}");
				}
				$this->template->ok(sprintf(_('You have saved user group %s.'), $crud->f->user_group_name));
			}
		}

		if (!empty($crud->f->user_group_id) && $crud->GET('eg')) {
			if ($this->user->belongsToGroup(false, $crud->f->user_group_id)) {
				$crud->importFields($this->db->invokeQuery('PHPDS_readGroupQuery', $crud->f->user_group_id));
			} else {
				$this->template->warning(_('You cannot edit this group since you are not allocated to it.'));
			}
		}

		$parent_group_option = $this->db->invokeQuery('PHPDS_readParentGroupQuery', $crud->f->parent_group_id, $crud->f->user_group_id);

		$tagger = $this->tagger->tagArea('group', $crud->f->user_group_id, $crud->f->user_group_name);

		if ($crud->POST('new')) {
			$crud->f->user_group_id = 0;
			$crud->f->user_group_name = '';
			$crud->f->user_group_note = '';
			$crud->f->alias = '';
		}

		$view = $this->factory('views');

		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('user_group_id', $crud->f->user_group_id);
		$view->set('parent_group_option', $parent_group_option);
		$view->set('user_group_name', $crud->f->user_group_name);
		$view->set('user_group_note', $crud->f->user_group_note);
		$view->set('alias', $crud->f->alias);
		$view->set('tagger', $tagger);
		$view->set('post_validation', $this->security->postValidation());

		$view->show();
	}
}

return 'UserGroupAdmin';
