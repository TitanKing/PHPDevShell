<?php

class NodeItemAdminPermissions extends PHPDS_controller
{

	/**
	 * Node Item Admin Permissions
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 29 June 2010
	 */
	public function execute()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Load Extra Classes ////////////////////////////////////////////////////////////////////////////////////////////////////
		$node_structure = $this->factory('nodeStructure'); ///////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Header information
		$this->template->heading(__('Set Access Permission'));

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
		// When saved is submitted.
		if (!empty($this->security->post['save']) && $this->user->isRoot())
			$this->db->invokeQuery('PHPDS_writePermissionRowsQuery');

		// Get all user roles for information viewing.
		list($user_roles_info, $permissions, $user_role_field_info) = $this->db->invokeQuery("PHPDS_getAllPermissionsQuery");

		// Node list.
		$RESULTS = $this->db->invokeQuery('PHPDS_listNodesQuery', $permissions, $user_role_field_info);

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);

		// Set Tooltips.
		$view->set('i_urp', $user_roles_info);

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());

		// Output Template.
		$view->show();
	}
}

return 'NodeItemAdminPermissions';
