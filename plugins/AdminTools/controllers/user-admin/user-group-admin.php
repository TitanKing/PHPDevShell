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
			$this->template->heading(__('Edit User Group'));
			if ($crud->GET('eg')) $crud->f->user_group_id = $crud->GET('eg');
		} else {
			$this->template->heading(__('Add User Group'));
		}

		if ($crud->POST('save') || $crud->POST('new')) {
			$crud->addField('user_group_id');
			$crud->addField('parent_group_id');
			$crud->addField('user_group_note');
			$crud->addField('alias');

			if (!$crud->is('user_group_name'))
				$crud->error();

			if ($this->db->doesRecordExist('_db_core_user_groups', 'user_group_name', "{$crud->f->user_group_name}", 'user_group_id', "{$crud->f->user_group_id}") == true)
				$crud->error(sprintf(__('%s exists already.'), $crud->f->user_group_name), 'user_group_name');

			if (!empty($crud->f->user_group_id)) {
				if (!$this->user->belongsToGroup(false, $crud->f->user_group_id))
					$crud->errorElse(__('Permission denied.'));
			}

			if ($crud->ok()) {
				$crud->f->user_group_id = $this->db->invokeQuery('PHPDS_writeGroupQuery', $crud->f->user_group_id, $crud->f->user_group_name, $crud->f->user_group_note, $crud->f->parent_group_id, $crud->f->alias);
				if (! empty($crud->f->user_group_id)) {
					$this->db->invokeQuery('PHPDS_writeExtraGroupQuery', $this->configuration['user_id'], $crud->f->user_group_id);
					$this->db->cacheClear("groups_{$this->configuration['user_id']}");
				}
				$this->template->ok(sprintf(__('Saved %s.'), $crud->f->user_group_name));
			}
		}

		if (!empty($crud->f->user_group_id) && $crud->GET('eg')) {
			if ($this->user->belongsToGroup(false, $crud->f->user_group_id)) {
				$crud->importFields($this->db->invokeQuery('PHPDS_readGroupQuery', $crud->f->user_group_id));
			} else {
				$this->template->warning(__('Permission denied.'));
			}
		}

		$parent_group_option = $this->db->invokeQuery('PHPDS_readParentGroupQuery', $crud->f->parent_group_id, $crud->f->user_group_id);

        $tagger = $this->tagger->tagArea('group', $crud->f->user_group_id, $this->P('tagger_name'), $this->P('tagger_value'), $this->P('tagger_id'), $this->P('tagger_delete'));

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
