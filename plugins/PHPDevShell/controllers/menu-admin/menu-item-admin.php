<?php

class MenuItemAdmin extends PHPDS_controller
{

	/**
	 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper
	 * @since 01 July 2010
	 */
	public function execute()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Load Extra Classes ////////////////////////////////////////////////////////////////////////////////////////////////////
		$menu_structure = $this->factory('menuStructure'); //////////////////////////////////////////////////////////////////////
		$menu_array = $this->factory('menuArray'); //////////////////////////////////////////////////////////////////////////////
		$template = $this->template;
		$configuration = $this->configuration;

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Define.
		$menu_type_selected_1 = 'checked';
		$menu_type_selected_2 = '';
		$menu_type_selected_3 = '';
		$menu_type_selected_4 = '';
		$menu_type_selected_5 = '';
		$menu_type_selected_6 = '';
		$menu_type_selected_7 = '';
		$menu_type_selected_8 = '';
		$menu_type_selected_9 = '';
		$menu_type_selected_10 = '';
		$menu_type_selected_11 = '';
		$menu_type_selected_12 = '';
		$hide_selected_1 = '';
		$hide_selected_2 = '';
		$hide_selected_3 = '';
		$hide_selected_4 = '';
		$hide_selected_5 = '';
		$new_window_selected_1 = '';
		$new_window_selected_2 = '';
		$edit['parent_menu_id'] = 0;
		$edit['menu_id'] = 0;
		$edit['plugin'] = '';
		$edit['menu_type'] = false;
		$edit['new_window'] = 0;
		$edit['menu_link'] = '';
		$edit['menu_name'] = '';
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
		$replace_old_menu = false;

		$icon_found = $template->icon('tick-circle', _('Item Found'));
		$icon_notfound = $template->icon('cross-circle', _('Item Not Found'));
		// Head.
		if (! empty($this->security->post['save']) || !empty($edit_menu_id)) {
			$template->heading(_('Edit Menu Item'));
		} else {
			$template->heading(_('New Menu Item'));
		}
		// Saving menu item.
		if (! empty($this->security->get['em']))
			$edit_menu_id = $this->security->get['em'];
		else
			$edit_menu_id = null;
		if (! empty($this->security->post['save']) || ! empty($edit_menu_id) || ! empty($this->security->post['new'])) {
			// Check if it is a edit request or save request.
			if (! empty($edit_menu_id)) {
				// Get values of item to edit.
				$menu_array->loadMenuArray($edit_menu_id, false);
				// Save array value.
				$edit = $menu_array->menuArray[$edit_menu_id];
				// Define.
				if (empty($edit['height'])) $edit['height'] = false;
				if (empty($edit['new_window'])) $edit['new_window'] = 0;
				if (empty($edit['params'])) $edit['params'] = '';
				// Do extend column.
				switch ($edit['menu_type']) {
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
				$selected_user_roles = $this->db->invokeQuery('PHPDS_getAdminMenuItemPermissionsQuery', $edit['menu_id']);
			}
			// On save assign $this->security->post global variable.
			if (! empty($this->security->post['save']) || ! empty($this->security->post['new'])) {
				// Get the new variables on save.
				$edit = $this->security->post;
				  // menu_type
				  if (empty($edit['menu_type'])) $edit['menu_type'] = 1;
				  // new_window
				  if (empty($edit['new_window'])) $edit['new_window'] = 0;

				// permission
				if (!empty($edit['permission'])) {
					foreach ($edit['permission'] as $role_id_save) {
						$selected_user_roles[$role_id_save] = 'selected';
					}
				}
			}
			// menu_id
			if (! empty($edit['menu_id'])) {
				// We have a custom menu id it seems.
				$edit['menu_id'] = $this->core->safeName($edit['menu_id']);
			} else if (! empty($edit['plugin']) && ! empty($edit['menu_link'])) {
				$edit['menu_id'] = $menu_structure->createMenuId($edit['plugin'], $edit['menu_link']);
			} else {
				$edit['menu_id'] = '';
			}
			/////////////////////////////////////////////////////////////////////////////////////////////
			// menu_type.
			$menu_type_selected_1 = '';
			switch ($edit['menu_type']) {
				// Plugin File.
				case 1:
				case 8:
				case 9:
				case 10:
				case 11:
				case 12:
					switch ($edit['menu_type']) {
						case 1:
							$menu_type_selected_1 = 'checked';
							break;
						case 8:
							$menu_type_selected_8 = 'checked';
							break;
						case 9:
							$menu_type_selected_9 = 'checked';
							break;
						case 10:
							$menu_type_selected_10 = 'checked';
							break;
						case 11:
							$menu_type_selected_11 = 'checked';
							break;
						case 12:
							$menu_type_selected_12 = 'checked';
							break;
					}
					$found_check = true;
					// Remove first slash.
					$edit['menu_link'] = ltrim($edit['menu_link'], '/');
					$controller_file = 'plugins/' . $edit['plugin'] . '/controllers/' . $edit['menu_link'];
					if (file_exists($controller_file)) {
						$found = $icon_found;
						$controller_found = $template->icon('block--plus', _('Controller file available')) . ' ' . $controller_file;
					} else if (file_exists('plugins/' . $edit['plugin'] . '/' . $edit['menu_link'])) {
						$found = $icon_found;
						$controller_found = $template->icon('block--exclamation', _('Controller file NOT available')) . ' ' . $controller_file;
					} else {
						$found = $icon_notfound;
						$controller_found = $template->icon('block--exclamation', _('Controller file NOT available')) . ' ' . $controller_file;
					}

					$query_file = preg_replace('/\.php$/', '.query.php', $edit['menu_link']);
					$query_file = 'plugins/' . $edit['plugin'] . '/models/' . $query_file;
					if (is_file($query_file)) {
						$query_found = $template->icon('database--plus', _('Model file available')) . ' ' . $query_file;
					} else {
						$query_found = $template->icon('database--exclamation', _('Model file NOT available')) . ' ' . $query_file;
					}

					if (empty($edit['layout'])) {
						$view_file = preg_replace('/\.php$/', '.tpl', $edit['menu_link']);
						$view_file = 'plugins/' . $edit['plugin'] . '/views/' . $view_file;
						if (is_file($view_file)) {
							$view_found = $template->icon('eye--plus', _('View file available')) . ' ' . $view_file;
						} else {
							$view_found = $template->icon('eye--exclamation', _('View file NOT available')) . ' ' . $view_file;
						}
					} else {
						$custom_view = $edit['layout'];
						$foldernr = strrchr($edit['menu_link'], "/");
						if ($foldernr)
							$original_controller_file = substr($foldernr, 1);
						else
							$original_controller_file = $edit['menu_link'];

						$view_file = preg_replace("/{$original_controller_file}/", $custom_view . '.tpl', $edit['menu_link']);
						$view_file = preg_replace("/.tpl.tpl/", '.tpl', $view_file);
						$view_file = 'plugins/' . $edit['plugin'] . '/views/' . $view_file;
						if (is_file($view_file)) {
							$view_found = $template->icon('eye--plus', _('View template available')) . ' ' . $view_file;
						} else {
							$view_found = $template->icon('eye--exclamation', _('View template NOT available')) . ' ' . $view_file;
						}
					}

					// custom view class
					$view_class = preg_replace('/\.php$/', '.view.php', $edit['menu_link']);
					$view_class = 'plugins/' . $edit['plugin'] . '/views/' . $view_class;
					if (is_file($view_class)) {
						$view_class_found = $template->icon('paint-brush--plus', _('View class available')) . ' ' . $view_class;
					} else {
						$view_class_found = $template->icon('paint-brush--exclamation', _('View class NOT available')) . ' ' . $view_class;
					}
					break;
				// Link Existing Menu Item (Own Group).
				case 2:
					$menu_type_selected_2 = 'checked';
					$found = $icon_found;
					break;
				// Link Existing Menu Item (Jump To).
				case 3:
					$menu_type_selected_3 = 'checked';
					$found = $icon_found;
					break;
				// External File.
				case 4:
					$menu_type_selected_4 = 'checked';
					// Check if we can find file.
					if (file_exists($edit['menu_link'])) {
						$found = $icon_found;
					} else {
						$found = $icon_notfound;
					}
					break;
				// HTTP URL.
				case 5:
					$menu_type_selected_5 = 'checked';
					$found = $icon_found;
					break;
				// Empty Place Holder.
				case 6:
					$menu_type_selected_6 = 'checked';
					$found = $icon_found;
					break;
				// iFrame.
				case 7:
					$menu_type_selected_7 = 'checked';
					$found = $icon_found;
					break;
			}
			/////////////////////////////////////////////////////////////////////////////////////////////
			// rank
			switch ($edit['rank']) {
				case 'last':
					$last_rank = $this->db->invokeQuery('PHPDS_lastRankMenuItemQuery', $edit['parent_menu_id']);
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
			$current_ranking = '<option value="' . $edit['rank'] . '" selected>' . _('Leave Current') . '</option>';
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
				if (empty($edit['menu_link']) || empty($edit['plugin'])) {
					$template->warning(_('You did not complete all the required fields.'));
					$error[1] = true;
				}
				// Error 2 = See if menu item and link item is the same, that wont work!
				if ($edit['menu_id'] == $edit['link_to']) {
					$template->warning(_('You cannot link a menu item to itself.'));
					$error[2] = true;
				}
				// Error 3 = Check if menu already exists, we can prevent complications.
				if (! empty($edit['old_menu_id']) && $edit['old_menu_id'] != $edit['menu_id']) {
					$replace_old_menu = true;
					// Lets see if such a menu already exists, we cant override it, if it belongs to some other menu.
					$check_menu_existing = $menu_structure->menuIdExist($edit['menu_id']);
					if (! empty($check_menu_existing)) {
						$template->warning(sprintf(_('Another menu is already using menu id %s'), $edit['menu_id']));
						$edit['menu_id'] = $edit['old_menu_id'];
						$error[3] = true;
					}
				} else {
					$replace_old_menu = false;
				}
				// Error 4 = Check if menu is parent of itself.
				if ($edit['menu_id'] == $edit['parent_menu_id']) {
					$template->warning(_('You cannot have a menu item be a parent of itself. Please choose another parent for this item.'));
					$error[4] = true;
				}
				// Create file directory location.
				$menu_directory_c = $this->configuration['absolute_path'] . 'plugins/' . $edit['plugin'] . '/controllers/' . $edit['menu_link'];
				$menu_directory_n = $this->configuration['absolute_path'] . 'plugins/' . $edit['plugin'] . '/' . $edit['menu_link'];
				if (file_exists($menu_directory_c)) {
					$menu_directory = $menu_directory_c;
					$menu_dir_exists = true;
				} else if (file_exists($menu_directory_n)) {
					$menu_directory = $menu_directory_n;
					$menu_dir_exists = true;
				} else {
					$menu_dir_exists = false;
				}

				// Error Combined = Do appropriate checks on different menu types.
				switch ($edit['menu_type']) {
					// Plugin File.
					case 1:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						// Check if file exists.
						if (!$menu_dir_exists) {
							$template->warning(sprintf(_('Cannot find the plugin controller in the specified directory: %s'), $menu_directory_c));
							$error[3] = true;
						}
						break;
					// Link Existing Menu Item.
					case 2:
						// Check if linked item was selected.
						if (empty($edit['link_to'])) {
							$template->warning(_('You need to select a menu item to link with.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['link_to'];
						break;
					// Link Existing Menu Item (Jump To).
					case 3:
						// Check if linked item was selected.
						if (empty($edit['link_to'])) {
							$template->warning(_('You need to select a menu item to link with.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['link_to'];
						break;
					// External File.
					case 4:
						// Check if file exists.
						if (!file_exists($edit['menu_link'])) {
							$template->warning(sprintf(_('Cannot find the external file you wish to create a menu item with, looked in: %s'), $edit['menu_link']));
							$error[3] = true;
						}
						break;
					// HTTP URL.
					case 5:
						$template->notice(_('An external URL was selected.'));
						break;
					// Empty Place Holder.
					case 6:
						// Check if linked item was selected.
						if (empty($edit['link_to'])) {
							$template->warning(_('You need to select a menu item to link with.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['link_to'];
						break;
					// iFrame.
					case 7:
						if (empty($edit['height'])) {
							$template->warning(_('You must provide iFrame with a height.'));
							$error[3] = true;
						}
						// Set correct extend variable.
						$edit['extend'] = $edit['height'];
						break;
					// Cron File.
					case 8:
						// Check if file exists.
						if (!file_exists($menu_directory)) {
							$template->warning(sprintf(_('Cannot find the plugin cron file you wish to create a menu item with, looked in: %s'), $menu_directory));
							$error[3] = true;
						}
						break;
					// Plugin File.
					default:
						$template->warning(_('You need to select at least one menu type.'));
						$error[3] = true;
						break;
				}

				if (empty($error)) {

					// Check if we have an old menu id.
					if ($replace_old_menu) {
						$menu_structure->updateMenuId($edit['menu_id'], $edit['old_menu_id']);
						$template->ok(sprintf(_('Menu id changed, the new menu item id is %s and replaced old menu id %s.'), $edit['menu_id'], $edit['old_menu_id']));
					}

					// Insert new item into database.
					$menu_structure->insertMenu($edit['menu_id'], $edit['parent_menu_id'], $edit['menu_name'], $edit['menu_link'], $edit['plugin'], $edit['menu_type'], $edit['extend'], $edit['new_window'], $last_rank, $edit['hide'], $edit['template_id'], $edit['alias'], $edit['layout'], $edit['params']);

					////////////////////////////////////
					// END Save new item to database. //
					////////////////////////////////////
					/////////////////////////
					// Permissions saving. //
					/////////////////////////
					// Delete old menu permissions.
					$this->db->invokeQuery("PHPDS_deleteOldMenuPermissionsQuery", $edit['menu_id']);

					// Make sure we have a value for a loop!
					$user_role_id_db = false;
					if (!empty($edit['permission'])) {
						// Define.
						// Save permissions.
						foreach ($edit['permission'] as $user_role_id) {
							$user_role_id_db .= "('$user_role_id', '{$edit['menu_id']}'),";
						}
					}
					// Set new assigned value.
					$user_role_id_db = rtrim($user_role_id_db, ',');
					if (!empty($user_role_id_db)) {
						// Insert menu permissions.
						$this->db->invokeQuery('PHPDS_insertMenuPermissionsQuery', $user_role_id_db);
					}
					// Set variable global for hooks.
					$template->global['menu_id'] = $edit['menu_id'];

					/////////////////////////////
					// END Permissions saving. //
					/////////////////////////////
					// Give OK message according to edit or newly saved.
					$template->ok(sprintf(_('Menu item %s was saved.'), $edit['menu_name']));
				}
			}
		}

		//////////////////////////
		// Get all menu items. ///
		//////////////////////////
		$allMenuItems = $this->db->invokeQuery("PHPDS_getAllMenuItemsQuery", $edit);

		$edit = $allMenuItems['edit'];
		$show_existing_link = $allMenuItems['show_existing_link'];
		$existing_link_id = $allMenuItems['existing_link_id'];
		$show_parent = $allMenuItems['show_parent'];

		unset($allMenuItems);

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
		if (empty($edit['menu_name'])) {
			$tagname = $default_name;
		} else {
			$tagname = $edit['menu_name'];
		}

		$tagArea = $this->POST('tagger');
		$tagger = $this->tagger->tagArea('menu', $edit['menu_id'], $tagArea, $tagname);

		// Save and new.
		if (! empty($this->security->post['new'])) {
			$edit['menu_id'] = 0;
			$edit['menu_name'] = '';
			$edit['alias'] = '';
			$edit['params'] = '';
		}

		// Load views.
		$view = $this->factory('views');

		// Set Array.
		$view->set('e', $edit);

		$view->set('icon_1', $template->icon('plug', _('Plugin Item')));
		$view->set('icon_2', $template->icon('plug--plus', _('Link Existing Plugin Item')));
		$view->set('icon_3', $template->icon('plug--arrow', _('Link Existing Plugin Item and Jump to its Group')));
		$view->set('icon_4', $template->icon('script', _('Execute External File')));
		$view->set('icon_5', $template->icon('chain', _('External http URL')));
		$view->set('icon_6', $template->icon('chain-unchain', _('Unclickable Place Holder')));
		$view->set('icon_7', $template->icon('ui-split-panel-vertical', _('iFrame Item')));
		$view->set('icon_8', $template->icon('clock-select', _('Cronjob Item')));
		$view->set('icon_9', $template->icon('layout-select-content', _('HTML Ajax Widget')));
		$view->set('icon_10', $template->icon('application-block', _('HTML Ajax')));
		$view->set('icon_11', $template->icon('applications-blue', _('HTML Ajax Lightbox')));
		$view->set('icon_12', $template->icon('script-code', _('Raw Ajax')));

		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('edit_existing_link', $this->navigation->buildURL(false, 'em='));
		$view->set('default_name', $default_name);
		$view->set('show_parent', $show_parent);
		$view->set('menu_type_selected_1', $menu_type_selected_1);
		$view->set('menu_type_selected_2', $menu_type_selected_2);
		$view->set('menu_type_selected_3', $menu_type_selected_3);
		$view->set('menu_type_selected_4', $menu_type_selected_4);
		$view->set('menu_type_selected_5', $menu_type_selected_5);
		$view->set('menu_type_selected_6', $menu_type_selected_6);
		$view->set('menu_type_selected_7', $menu_type_selected_7);
		$view->set('menu_type_selected_8', $menu_type_selected_8);
		$view->set('menu_type_selected_9', $menu_type_selected_9);
		$view->set('menu_type_selected_10', $menu_type_selected_10);
		$view->set('menu_type_selected_11', $menu_type_selected_11);
		$view->set('menu_type_selected_12', $menu_type_selected_12);
		$view->set('show_existing_link', $show_existing_link);
		$view->set('existing_link_id', $existing_link_id);
		$view->set('edit_link', $template->icon('task--pencil', _('Edit Menu Item')));
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
		$menu_id = $this->GET('menu_id');
		$menu_type = $this->GET('menu_type');
		$plugin = $this->GET('plugin');

		$_target = $this->GET('_target');

		$result = array(
			'result' => 'na',
			'title' => _('URL/File Path Location/Linked Items (anything unique)')
		);

		switch ($_target) {
			case 'menu_link_check':
				switch ($menu_type) {
					case 1:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						$menu_link = $this->GET('menu_link');
						if ($menu_link && $plugin) {
							if (file_exists(BASEPATH . 'plugins/' . $plugin . '/controllers/' . $menu_link)) {
								$path1 = BASEPATH . 'plugins/' . $plugin . '/controllers';
								$path2 = $path1 . '/' . $menu_link;
							} else if (file_exists(BASEPATH . 'plugins/' . $plugin . '/' . $menu_link)) {
								$path1 = BASEPATH . 'plugins/' . $plugin;
								$path2 = $path1 . '/' . $menu_link;
							} else {
								$path1 = BASEPATH . 'plugins/' . $plugin;
								$path2 = $path1 . '/' . $menu_link;
							}

							$result = array(
								'result' => (is_readable($path1) && is_dir($path1) && is_readable($path2) && is_file($path2)) ? 'yes' : 'no',
								'tooltip' => $path2,
								'title' => 'Relative path (from selected plugin folder) to the PHP file'
							);
						}
						break;
					case 2:
						$menu_link = $this->GET('menu_link');
						if ($menu_id && $menu_link) {
							$exists = $this->db->invokeQuery('PHPDS_findMenuLinkUnicity', $menu_link, $menu_id);

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
					$exists = $this->db->invokeQuery('PHPDS_findMenuAliasUnicity', $alias, $menu_id);
					$result = array(
						'result' => $exists ? 'error' : 'yes'
					);
				}
				break;
		}

		return $result;
	}
}

return 'MenuItemAdmin';
