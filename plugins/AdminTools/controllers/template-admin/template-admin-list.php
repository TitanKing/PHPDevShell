<?php

/**
 * Controller Class: Handles system control panel.
 * @author Jason Schoeman
 * @return string
 */
class TemplateAdminList extends PHPDS_controller
{
	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Header information
		$this->template->heading(__('Theme Listing'));

		// Install template.
		if (!empty($this->security->get['it'])) {
			// First lets check if we have such a template.
			if (file_exists('themes/' . $this->security->get['it'] . '/theme.php') || file_exists('themes/' . $this->security->get['it'] . '/main.php')) {
				// Ok we have this template lets process.
				// Lets convert to template id.
				$template_folder_new = $this->security->get['it'];
				$template_id_new = $this->core->nameToId($template_folder_new);

				// Execute data save.
				$this->db->invokeQuery('PHPDS_writeTemplateQuery', $template_id_new, $template_folder_new);
				// Set variable global for hooks.
				$this->template->global['template_id'] = $template_id_new;

				// Show item updated.
				$this->template->ok(sprintf(__('You have installed theme "%s".'), $template_folder_new));
			}
		}
		// Delete template.
		if (!empty($this->security->get['ut'])) {
			// Convert template id.
			$template_folder_delete = $this->security->get['ut'];
			$template_id_delete = $this->core->nameToId("$template_folder_delete");
			// Check if we have node items assigned to this template.
			$count_node_items_dep = $this->db->invokeQuery('PHPDS_countTemplateQuery', $template_id_delete);

			// Check if it is safe to delete.
			if (empty($count_node_items_dep) && ($template_id_delete != $this->configuration['default_template_id'])) {
				// Now we can delete template item.
				$deleted_template = $this->db->deleteQuick('_db_core_templates', 'template_id', $template_id_delete, 'template_folder');

				if ($deleted_template) {
					$this->template->ok(sprintf(__('Theme %s was uninstalled.'), $deleted_template));
				} else {
					$this->template->warning(sprintf(__('No theme "%s" to delete.'), $template_folder_delete));
				}
			} else {
				$this->template->warning(sprintf(__('There are node items depending on theme "%s" or it is set as system default, please assign to another theme first.'), $template_folder_delete));
			}
		}

		$template_option_ar = $this->db->invokeQuery('PHPDS_selectTemplateQuery', $this->configuration['default_template_id']);
		$template_option_ = $template_option_ar['dropdown'];
		$template_id_db = $template_option_ar['selected'];

		// Set template default per node.
		if (! empty($this->security->post['set'])) {
			// Check if we have a complete form for changing templates.
			if (empty($this->security->post['set_to'])) {
				$this->template->notice(__('Please first select a theme to update.'));
			} else {
				$setting_template_id = $this->security->post['set_to'];
				$setting_template_name = $template_id_db[$setting_template_id];

				if (! empty($setting_template_id) && ! empty($setting_template_name)) {
					$this->db->writeSettings(array('default_template'=>$setting_template_name, 'default_template_id'=>$setting_template_id), 'AdminTools');
					$this->db->invokeQuery('PHPDS_updateTemplateQuery', $setting_template_id, $this->configuration['default_template_id']);
				}

				// Clear old cache.
				$this->db->cacheClear('navigation');
				$this->db->cacheClear('essential_settings');
				$this->template->ok(sprintf(__("%s is now the default theme... I will refresh the page."), $setting_template_name));
				$this->navigation->redirect($this->navigation->selfUrl(), 3);
			}
		}
		$R = $this->db->invokeQuery('PHPDS_readTemplateDir', $template_id_db);
		$RESULTS = $R[0];
		$RESULTS_ = $R[1];

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('RESULTS', $RESULTS);
		$view->set('RESULTS_', $RESULTS_);

		// Set Buttons.
		$view->set('set_template', __('Set Theme'));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('template_option_', $template_option_);

		// Output Template.
		$view->show();
	}
}

return 'TemplateAdminList';
