<?php

class NodeItemAdminList extends PHPDS_controller
{

	/**
	 * Node Access Logs
	 * @author Jason Schoeman
	 * @since 29 June 2010
	 */
	public function execute()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Load Extra Classes ////////////////////////////////////////////////////////////////////////////////////////////////////
		$node_structure = $this->factory('nodeStructure'); //////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// Header information
		$this->template->heading(__('Node Admin'));

		// Delete node item.
		if (!empty($this->security->get['dm']) && $this->user->isRoot()) {

			// Get name of item being deleted for log purposes.
			$deleted_node_item = $this->db->selectQuick('_db_core_node_items', 'node_link', 'node_id', $this->security->get['dm']);
			if ($deleted_node_item) {
				// Get redirect login.
				$settings = $this->db->getSettings(array('redirect_login'));
				// Check if we can delete this item and that it is not assigned to default settings.
				if ($this->security->get['dm'] != $this->configuration['front_page_id'] && $this->security->get['dm'] != $this->configuration['front_page_id_in'] && $this->security->get['dm'] != $settings['redirect_login']) {
					// Call the delete.
					($node_structure->getDelete()) ? $this->template->ok(sprintf(__('Node item %s (%s) was deleted!'), $deleted_node_item, $this->security->get['dm'])) : $this->template->warning(__('You cannot delete a core item, the system will be unable to function correctly. Switch force core changes on in General Settings GUI for bypass.'));
				} else {
					$this->template->warning(sprintf(__('You cannot delete node item "%s" as it is still set to be used in general settings.'), $this->security->get['dm']));
				}
			} else {
				$this->template->warning(sprintf(__('No node "%s" to delete.'), $this->security->get['dm']));
			}
		}
		// When save button is pushed...
		if (!empty($this->security->post['save']) && $this->user->isRoot()) {
			// Collect $this->security->post variables.
			$node_id_array = $this->security->post['node_id_array'];
			if (! empty($this->security->post['new_window_array'])) {
				$new_window_array = $this->security->post['new_window_array'];
			} else {
				$new_window_array = array();
			}
			$hide_array = $this->security->post['hide_array'];
			$rank_array = $this->security->post['rank_array'];
			$template_id_array = $this->security->post['template_id'];
			$layout_array = $this->security->post['layout_array'];

			// Run through arrays and create database insert.
			$this->db->invokeQuery("PHPDS_nodeItemListSaveQuery", $node_id_array, $new_window_array, $hide_array, $rank_array, $template_id_array, $layout_array);

			// Write node tree structure.
			$node_structure->writeNodeStructure();

			// Show success.
			$this->template->ok(__('Adjusted node settings.'));
		}

		$RESULTS = $this->db->invokeQuery('PHPDS_listNodesQuery');

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());

		// Output Template.
		$view->show();
	}
}

return 'NodeItemAdminList';
