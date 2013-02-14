<?php

class NodeItemAdmin extends PHPDS_controller
{

	/**
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 01 July 2010
	 */
	public function execute()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Load Extra Classes ////////////////////////////////////////////////////////////////////////////////////////////////////
		$node_structure = $this->factory('nodeStructure'); //////////////////////////////////////////////////////////////////////
		$node_array = $this->factory('nodeArray'); //////////////////////////////////////////////////////////////////////////////
		$template = $this->template;
		$configuration = $this->configuration;

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Define.
		$node_type_selected_1 = 'checked';
		$node_type_selected_2 = '';
		$node_type_selected_3 = '';
		$node_type_selected_4 = '';
		$node_type_selected_5 = '';
		$node_type_selected_6 = '';
		$node_type_selected_7 = '';
		$node_type_selected_8 = '';
		$node_type_selected_9 = '';
		$node_type_selected_10 = '';
		$node_type_selected_11 = '';
		$node_type_selected_12 = '';
		$hide_selected_1 = '';
		$hide_selected_2 = '';
		$hide_selected_3 = '';
		$hide_selected_4 = '';
		$hide_selected_5 = '';
		$new_window_selected_1 = '';
		$new_window_selected_2 = '';
		$edit['parent_node_id'] = 0;
		$edit['node_id'] = 0;
		$edit['plugin'] = '';
		$edit['node_type'] = false;
		$edit['new_window'] = 0;
		$edit['node_link'] = '';
		$edit['node_name'] = '';
		$edit['alias'] = '';
		$edit['height'] = '';
		$edit['layout'] = '';
		$edit['params'] = '';
		$default_name = '';
		$found = false;
		$current_ranking = ''; // Is an empty option
		$selected_user_roles = array();
		$found_check = false;
		$query_found = '';
		$view_found = '';
		$view_class_found = '';
		$controller_found = '';
		$replace_old_node = false;

		$icon_found = $template->icon('tick-circle', __('Item Found'));
		$icon_notfound = $template->icon('cross-circle', __('Item Not Found'));
		// Head.
		if (! empty($this->security->post['save']) || !empty($edit_node_id)) {
			$template->heading(__('Edit Node Item'));
		} else {
			$template->heading(__('New Node Item'));
		}
		// Saving node item.
		if (! empty($this->security->get['em']))
			$edit_node_id = $this->security->get['em'];
		else
			$edit_node_id = null;
		if (! empty($this->security->post['save']) || ! empty($edit_node_id) || ! empty($this->security->post['new'])) {
			// Check if it is a edit request or save request.
			if (! empty($edit_node_id)) {
				// Get values of item to edit.
				$node_array->loadNodeArray($edit_node_id, false);
				// Save array value.
				$edit = $node_array->nodeArray[$edit_node_id];
				// Define.
				if (empty($edit['height'])) $edit['height'] = false;
				if (empty($edit['new_window'])) $edit['new_window'] = 0;
				if (empty($edit['params'])) $edit['params'] = '';
				// Do extend column.
				switch ($edit['node_type']) {
					case 2: $edit['link_to'] = $edit['extend'];
						break;
					case 3: $edit['link_to'] = $edit['extend'];
						break;
					case 6: $edit['link_to'] = $edit['extend'];
						break;
					case 7: $edit['height'] = $edit['extend'];
						break;
				}
				// Permissions.
				$selected_user_roles = $this->db->invokeQuery('PHPDS_getAdminNodeItemPermissionsQuery', $edit['node_id']);
			}
			// On save assign $this->security->post global variable.
			if (! empty($this->security->post['save']) || ! empty($this->security->post['new'])) {
				// Get the new variables on save.
				$edit = $this->security->post;
				  // node_type
				  if (empty($edit['node_type'])) $edit['node_type'] = 1;
				  // new_window
				  if (empty($edit['new_window'])) $edit['new_window'] = 0;

				// permission
				if (!empty($edit['permission'])) {
					foreach ($edit['permission'] as $role_id_save) {
						$selected_user_roles[$role_id_save] = 'selected';
					}
				}
			}
			// node_id
			if (! empty($edit['node_id'])) {
				// We have a custom node id it seems.
				$edit['node_id'] = $this->core->safeName($edit['node_id']);
			} else if (! empty($edit['plugin']) && ! empty($edit['node_link'])) {
				$edit['node_id'] = $node_structure->createNodeId($edit['plugin'], $edit['node_link']);
			} else {
				$edit['node_id'] = '';
			}
			/////////////////////////////////////////////////////////////////////////////////////////////
			// node_type.
			$node_type_selected_1 = '';
			switch ($edit['node_type']) {
				// Plugin File.
				case 1:
				case 8:
				case 9:
				case 10:
				case 11:
				case 12:
					switch ($edit['node_type']) {
						case 1:
							$node_type_selected_1 = 'checked';
							break;
						case 8:
							$node_type_selected_8 = 'checked';
							break;
						case 9:
							$node_type_selected_9 = 'checked';
							break;
						case 10:
							$node_type_selected_10 = 'checked';
							break;
						case 11:
							$node_type_selected_11 = 'checked';
							break;
						case 12:
							$node_type_selected_12 = 'checked';
							break;
					}
					$found_check = true;
					// Remove first slash.
					$edit['node_link'] = ltrim($edit['node_link'], '/');
					$controller_file = 'plugins/' . $edit['plugin'] . '/controllers/' . $edit['node_link'];
					if (file_exists($controller_file)) {
						$found = $icon_found;
						$controller_found = $template->icon('block--plus', __('Controller file available')) . ' ' . $controller_file;
					} else if (file_exists('plugins/' . $edit['plugin'] . '/' . $edit['node_link'])) {
						$found = $icon_found;
						$controller_found = $template->icon('block--exclamation', __('Controller file NOT available')) . ' ' . $controller_file;
					} else {
						$found = $icon_notfound;
						$controller_found = $template->icon('block--exclamation', __('Controller file NOT available')) . ' ' . $controller_file;
					}

					$query_file = preg_replace('/\.php$/', '.query.php', $edit['node_link']);
					$query_file = 'plugins/' . $edit['plugin'] . '/models/' . $query_file;
					if (is_file($query_file)) {
						$query_found = $template->icon('database--plus', __('Model file available')) . ' ' . $query_file;
					} else {
						$query_found = $template->icon('database--exclamation', __('Model file NOT available')) . ' ' . $query_file;
					}

					if (empty($edit['layout'])) {
						$view_file = preg_replace('/\.php$/', '.tpl', $edit['node_link']);
						$view_file = 'plugins/' . $edit['plugin'] . '/views/' . $view_file;
						if (is_file($view_file)) {
							$view_found = $template->icon('eye--plus', __('View file available')) . ' ' . $view_file;
						} else {
							$view_found = $template->icon('eye--exclamation', __('View file NOT available')) . ' ' . $view_file;
						}
					} else {
						$custom_view = $edit['layout'];
						$foldernr = strrchr($edit['node_link'], "/");
						if ($foldernr)
							$original_controller_file = substr($foldernr, 1);
						else
							$original_controller_file = $edit['node_link'];

						$view_file = preg_replace("/{$original_controller_file}/", $custom_view . '.tpl', $edit['node_link']);
						$view_file = preg_replace("/.tpl.tpl/", '.tpl', $view_file);
						$view_file = 'plugins/' . $edit['plugin'] . '/views/' . $view_file;
						if (is_file($view_file)) {
							$view_found = $template->icon('eye--plus', __('View template available')) . ' ' . $view_file;
						} else {
							$view_found = $template->icon('eye--exclamation', __('View template NOT available')) . ' ' . $view_file;
						}
					}

					// custom view class
					$view_class = preg_replace('/\.php$/', '.view.php', $edit['node_link']);
					$view_class = 'plugins/' . $edit['plugin'] . '/views/' . $view_class;
					if (is_file($view_class)) {
						$view_class_found = $template->icon('paint-brush--plus', __('View class available')) . ' ' . $view_class;
					} else {
						$view_class_found = $template->icon('paint-brush--exclamation', __('View class NOT available')) . ' ' . $view_class;
					}
					break;
				// Link Existing Node Item (Own Group).
				case 2:
					$node_type_selected_2 = 'checked';
					$found = $icon_found;
					break;
				// Link Existing Node Item (Jump To).
				case 3:
					$node_type_selected_3 = 'checked';
					$found = $icon_found;
					break;
				// External File.
				case 4:
					$node_type_selected_4 = 'checked';
					// Check if we can find file.
					if (file_exists($edit['node_link'])) {
						$found = $icon_found;
					} else {
						$found = $icon_notfound;
					}
					break;
				// HTTP URL.
				case 5:
					$node_type_selected_5 = 'checked';
					$found = $icon_found;
					break;
				// Empty Place Holder.
				case 6:
					$node_type_selected_6 = 'checked';
					$found = $icon_found;
					break;
				// iFrame.
				case 7:
					$node_type_selected_7 = 'checked';
					$found = $icon_found;
					break;
			}
			/////////////////////////////////////////////////////////////////////////////////////////////
			// rank
			switch ($edit['rank']) {
				case 'last':
					$last_rank = $this->db->invokeQuery('PHPDS_lastRankNodeItemQuery', $edit['parent_node_id']);
					break;
				case 'first':
					$last_rank = 1;
					break;
				default:
					$last_rank = $edit['rank'];
					break;
			}
			// Set auto selected dropdown parameters.
			// Ranking.
			$current_ranking = '<option value="' . $edit['rank'] . '" selected>' . __('Leave Current') . '</option>';
			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////
			// hide
			switch ($edit['hide']) {
				case 0:
					$hide_selected_1 = 'selected';
					break;
				case 1:
					$hide_selected_2 = 'selected';
					break;
				case 2:
					$hide_selected_3 = 'selected';
					break;
				case 3:
					$hide_selected_4 = 'selected';
					break;
				case 4:
					$hide_selected_5 = 'selected';
					break;
			}
			/////////////////////////////////////////////////////////////////////////////////////////////
			// new_window
			switch ($edit['new_window']) {
				case 0:
					$new_window_selected_1 = 'checked';
					break;
				case 1:
					$new_window_selected_2 = 'checked';
					break;
			}
			////////////////////////////////
			// Error checking.            //
			////////////////////////////////
			if ((! empty($this->security->post['save']) || ! empty($this->security->post['new'])) && $this->user->isRoot()) {

				// Define.
				$edit['extend'] = false;
				$edit['alias'] = $this->core->safeName($edit['alias']);

				// Error 1 = Check for empty fields.
				if (empty($edit['node_link']) || empty($edit['plugin'])) {
					$template->warning(__('You did not complete all the required fields.'));
					$error[1] = true;
				}
				// Error 2 = See if node item and link item is the same, that wont work!
				if ($edit['node_id'] == $edit['link_to']) {
					$template->warning(__('You cannot link a node item to itself.'));
					$error[2] = true;
				}
				// Error 3 = Check if node already exists, we can prevent complications.
				if (! empty($edit['old_node_id']) && $edit['old_node_id'] != $edit['node_id']) {
					$replace_old_node = true;
					// Lets see if such a node already exists, we cant override it, if it belongs to some other node.
					$check_node_existing = $node_structure->nodeIdExist($edit['node_id']);
					if (! empty($check_node_existing)) {
						$template->warning(sprintf(__('Another node is already using node id %s'), $edit['node_id']));
						$edit['node_id'] = $edit['old_node_id'];
						$error[3] = true;
					}
				} else {
					$replace_old_node = false;
				}
				// Error 4 = Check if node is parent of itself.
				if ($edit['node_id'] == $edit['parent_node_id']) {
					$template->warning(__('You cannot have a node item be a parent of itself. Please choose another parent for this item.'));
					$error[4] = true;
				}
				// Create file directory location.
				$node_directory_c = $this->configuration['absolute_path'] . 'plugins/' . $edit['plugin'] . '/controllers/' . $edit['node_link'];
				$node_directory_n = $this->configuration['absolute_path'] . 'plugins/' . $edit['plugin'] . '/' . $edit['node_link'];
				if (file_exists($node_directory_c)) {
					$node_directory = $node_directory_c;
					$node_dir_exists = true;
				} else if (file_exists($node_directory_n)) {
					$node_directory = $node_directory_n;
					$node_dir_exists = true;
				} else {
					$node_dir_exists = false;
				}

				// Error Combined = Do appropriate checks on different node types.
				switch ($edit['node_type']) {
					// Plugin File.
					case 1:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						// Check if file exists.
						if (!$node_dir_exists) {
							$template->warning(sprintf(__('Cannot find the plugin controller in the specified directory: %s'), $node_directory_c));
							$error[3] = true;
						}
						break;
					// Link Existing Node Item.
					case 2:
						// Check if linked item was selected.
						if (empty($edit['link_to'])) {
							$template->warning(__('You need to select a node item to link with.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['link_to'];
						break;
					// Link Existing Node Item (Jump To).
					case 3:
						// Check if linked item was selected.
						if (empty($edit['link_to'])) {
							$template->warning(__('You need to select a node item to link with.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['link_to'];
						break;
					// External File.
					case 4:
						// Check if file exists.
						if (!file_exists($edit['node_link'])) {
							$template->warning(sprintf(__('Cannot find the external file you wish to create a node item with, looked in: %s'), $edit['node_link']));
							$error[3] = true;
						}
						break;
					// HTTP URL.
					case 5:
						$template->notice(__('An external URL was selected.'));
						break;
					// Empty Place Holder.
					case 6:
						// Check if linked item was selected.
						if (empty($edit['link_to'])) {
							$template->warning(__('You need to select a node item to link with.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['link_to'];
						break;
					// iFrame.
					case 7:
						if (empty($edit['height'])) {
							$template->warning(__('You must provide iFrame with a height.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['height'];
						break;
					// Cron File.
					case 8:
						// Check if file exists.
						if (!file_exists($node_directory)) {
							$template->warning(sprintf(__('Cannot find the plugin cron file you wish to create a node item with, looked in: %s'), $node_directory));
							$error[3] = true;
						}
						break;
					// Plugin File.
					default:
						$template->warning(__('You need to select at least one node type.'));
						$error[3] = true;
						break;
				}

				if (empty($error)) {

					// Check if we have an old node id.
					if ($replace_old_node) {
						$node_structure->updateNodeId($edit['node_id'], $edit['old_node_id']);
						$template->ok(sprintf(__('Node id changed, the new node item id is %s and replaced old node id %s.'), $edit['node_id'], $edit['old_node_id']));
					}

					// Insert new item into database.
					$node_structure->insertNode($edit['node_id'], $edit['parent_node_id'], $edit['node_name'], $edit['node_link'], $edit['plugin'], $edit['node_type'], $edit['extend'], $edit['new_window'], $last_rank, $edit['hide'], $edit['template_id'], $edit['alias'], $edit['layout'], $edit['params']);

					////////////////////////////////////
					// END Save new item to database. //
					////////////////////////////////////
					/////////////////////////
					// Permissions saving. //
					/////////////////////////
					// Delete old node permissions.
					$this->db->invokeQuery("PHPDS_deleteOldNodePermissionsQuery", $edit['node_id']);

					// Make sure we have a value for a loop!
					$user_role_id_db = false;
					if (!empty($edit['permission'])) {
						// Define.
						// Save permissions.
						foreach ($edit['permission'] as $user_role_id) {
							$user_role_id_db .= "('$user_role_id', '{$edit['node_id']}'),";
						}
					}
					// Set new assigned value.
					$user_role_id_db = rtrim($user_role_id_db, ',');
					if (!empty($user_role_id_db)) {
						// Insert node permissions.
						$this->db->invokeQuery('PHPDS_insertNodePermissionsQuery', $user_role_id_db);
					}
					// Set variable global for hooks.
					$template->global['node_id'] = $edit['node_id'];

					/////////////////////////////
					// END Permissions saving. //
					/////////////////////////////
					// Give OK message according to edit or newly saved.
					$template->ok(sprintf(__('Node item %s was saved.'), $edit['node_name']));
				}
			}
		}

		//////////////////////////
		// Get all node items. ///
		//////////////////////////
		$allNodeItems = $this->db->invokeQuery("PHPDS_getAllNodeItemsQuery", $edit);

		$edit = $allNodeItems['edit'];
		$show_existing_link = $allNodeItems['show_existing_link'];
		$existing_link_id = $allNodeItems['existing_link_id'];
		$show_parent = $allNodeItems['show_parent'];

		unset($allNodeItems);

		//////////////////////////
		// Get all user roles.  //
		//////////////////////////
		$allUserRoles = $this->db->invokeQuery('PHPDS_getAllUserRolesQuery', $edit, $selected_user_roles);

		$edit = $allUserRoles['edit'];
		$selected_user_roles = $allUserRoles['selected_user_roles'];

		unset($allUserRoles);

		//////////////////////////////////
		// Get all available templates. //
		//////////////////////////////////

		$allAvailTemplates = $this->db->invokeQuery('PHPDS_getAllAvailableTemplatesQuery', $edit);

		$edit = $allAvailTemplates['edit'];
		$template_option_ = $allAvailTemplates['template_option_'];

		unset($allAvailTemplates);

		// Load taglist.
		if (empty($edit['node_name'])) {
			$tagname = $default_name;
		} else {
			$tagname = $edit['node_name'];
		}

		$tagArea = $this->POST('tagger');
		$tagger = $this->tagger->tagArea('node', $edit['node_id'], $tagArea, $tagname);

		// Save and new.
		if (! empty($this->security->post['new'])) {
			$edit['node_id'] = 0;
			$edit['node_name'] = '';
			$edit['alias'] = '';
			$edit['params'] = '';
		}

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('e', $edit);

		$view->set('icon_1', $template->icon('plug', __('Plugin Item')));
		$view->set('icon_2', $template->icon('plug--plus', __('Link Existing Plugin Item')));
		$view->set('icon_3', $template->icon('plug--arrow', __('Link Existing Plugin Item and Jump to its Group')));
		$view->set('icon_4', $template->icon('script', __('Execute External File')));
		$view->set('icon_5', $template->icon('chain', __('External http URL')));
		$view->set('icon_6', $template->icon('chain-unchain', __('Unclickable Place Holder')));
		$view->set('icon_7', $template->icon('ui-split-panel-vertical', __('iFrame Item')));
		$view->set('icon_8', $template->icon('clock-select', __('Cronjob Item')));
		$view->set('icon_9', $template->icon('layout-select-content', __('HTML Ajax Widget')));
		$view->set('icon_10', $template->icon('application-block', __('HTML Ajax')));
		$view->set('icon_11', $template->icon('applications-blue', __('HTML Ajax Lightbox')));
		$view->set('icon_12', $template->icon('script-code', __('Raw Ajax')));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('edit_existing_link', $this->navigation->buildURL(false, 'em='));
		$view->set('default_name', $default_name);
		$view->set('show_parent', $show_parent);
		$view->set('node_type_selected_1', $node_type_selected_1);
		$view->set('node_type_selected_2', $node_type_selected_2);
		$view->set('node_type_selected_3', $node_type_selected_3);
		$view->set('node_type_selected_4', $node_type_selected_4);
		$view->set('node_type_selected_5', $node_type_selected_5);
		$view->set('node_type_selected_6', $node_type_selected_6);
		$view->set('node_type_selected_7', $node_type_selected_7);
		$view->set('node_type_selected_8', $node_type_selected_8);
		$view->set('node_type_selected_9', $node_type_selected_9);
		$view->set('node_type_selected_10', $node_type_selected_10);
		$view->set('node_type_selected_11', $node_type_selected_11);
		$view->set('node_type_selected_12', $node_type_selected_12);
		$view->set('show_existing_link', $show_existing_link);
		$view->set('existing_link_id', $existing_link_id);
		$view->set('edit_link', $template->icon('task--pencil', __('Edit Node Item')));
		$view->set('found', $found);
		$view->set('tagger', $tagger);
		$view->set('query_found', $query_found);
		$view->set('controller_found', $controller_found);
		$view->set('view_found', $view_found);
		$view->set('view_class_found', $view_class_found);
		$view->set('found_check', $found_check);
		$view->set('current_ranking', $current_ranking);
		$view->set('hide_selected_1', $hide_selected_1);
		$view->set('hide_selected_2', $hide_selected_2);
		$view->set('hide_selected_3', $hide_selected_3);
		$view->set('hide_selected_4', $hide_selected_4);
		$view->set('hide_selected_5', $hide_selected_5);
		$view->set('new_window_selected_1', $new_window_selected_1);
		$view->set('new_window_selected_2', $new_window_selected_2);
		$view->set('template_option_', $template_option_);

		// Output Template.
		$view->show();
	}

	/**
	 * Check via ajax if files exist.
	 *
	 * @return string
	 */
	public function viaAJAX()
	{
		$node_id = $this->GET('node_id');
		$node_type = $this->GET('node_type');
		$plugin = $this->GET('plugin');

		$_target = $this->GET('_target');

		$result = array(
			'result' => 'na',
			'title' => __('URL/File Path Location/Linked Items (anything unique)')
		);

		switch ($_target) {
			case 'node_link_check':
				switch ($node_type) {
					case 1:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						$node_link = $this->GET('node_link');
						if ($node_link && $plugin) {
							if (file_exists(BASEPATH . 'plugins/' . $plugin . '/controllers/' . $node_link)) {
								$path1 = BASEPATH . 'plugins/' . $plugin . '/controllers';
								$path2 = $path1 . '/' . $node_link;
							} else if (file_exists(BASEPATH . 'plugins/' . $plugin . '/' . $node_link)) {
								$path1 = BASEPATH . 'plugins/' . $plugin;
								$path2 = $path1 . '/' . $node_link;
							} else {
								$path1 = BASEPATH . 'plugins/' . $plugin;
								$path2 = $path1 . '/' . $node_link;
							}

							$result = array(
								'result' => (is_readable($path1) && is_dir($path1) && is_readable($path2) && is_file($path2)) ? 'yes' : 'no',
								'tooltip' => $path2,
								'title' => 'Relative path (from selected plugin folder) to the PHP file'
							);
						}
						break;
					case 2:
						$node_link = $this->GET('node_link');
						if ($node_id && $node_link) {
							$exists = $this->db->invokeQuery('PHPDS_findNodeLinkUnicity', $node_link, $node_id);

							$result = array(
								'result' => $exists ? 'error' : 'yes',
								'title' => 'Virtual path (should be unique to this script)'
							);
						}
						break;
				}
				break;
			case 'plugin_check':
				if ($plugin) {
					$path1 = BASEPATH . 'plugins/' . $plugin;
					$result = array(
						'result' => is_readable($path1) && is_dir($path1) ? 'yes' : 'no',
						'tooltip' => $path1
					);
				}
				break;
			case 'alias_check':
				$alias = $this->GET('alias');
				if ($alias) {
					$exists = $this->db->invokeQuery('PHPDS_findNodeAliasUnicity', $alias, $node_id);
					$result = array(
						'result' => $exists ? 'error' : 'yes'
					);
				}
				break;
		}

		return $result;
	}
}

return 'NodeItemAdmin';
