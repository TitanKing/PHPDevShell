<?php

/**
 * Config Manager: Simple and effective way in handling settings in a registry type format.
 * @author Jason Schoeman
 * @return string
 */
class ConfigManager extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		$this->template->heading(__('Class Registry Editor'));

		$self_url_ = $this->navigation->buildURL(false);

		if (!empty($this->security->get['enable'])) {
			$this->db->invokeQuery('PHPDS_enableClass', 1, $this->security->get['enable']);
			$this->template->ok(sprintf(__('Plugin id %s enabled'), $this->security->get['enable']), false, false);
		}

		if (!empty($this->security->get['disable'])) {
			$this->db->invokeQuery('PHPDS_enableClass', 0, $this->security->get['disable']);
			$this->template->ok(sprintf(__('Plugin id %s disabled'), $this->security->get['disable']), false, false);
		}

		if (!empty($this->security->get['dc'])) {
			$this->db->deleteQuick('_db_core_plugin_classes', 'class_id', $this->security->get['dc']);
			$this->template->ok(sprintf(__('Deleted class entry %s.'), $this->security->get['dc']));
		}

		if (! empty($this->security->post['save'])) {
			$this->db->invokeQuery('PHPDS_writeCoreClassQuery');
		}

		$RESULTS = $this->db->invokeQuery('PHPDS_readCoreClassQuery');

		$view = $this->factory('views');

		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);
		$view->set('self_url', $self_url_);

		$view->show();
	}
}

return 'ConfigManager';