<?php

/**
 * Menu Item Admin - Save Item List
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_menuItemListSaveQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_menu_items
		SET
			new_window  = '%s',
			hide        = '%s',
			rank        = '%s',
			template_id = '%s',
			layout 		= '%s'
		WHERE
			menu_id = '%s'
	";

	/**
	 * Initiate query invoke command.
	 */
	public function invoke($parameters = null)
	{
		// Assign
		$menu_id_array = $parameters[0];
		$new_window_array = $parameters[1];
		$hide_array = $parameters[2];
		$rank_array = $parameters[3];
		$template_id_array = $parameters[4];
		$layout_array = $parameters[5];

		// Loop
		if (empty($menu_id_array)) $menu_id_array = array();
		foreach ($menu_id_array as $menu_id__) {
			// new_window
			(!empty($new_window_array[$menu_id__])) ? $new_window = '1' : $new_window = '0';
			// Replace in database.
			parent::invoke(array($new_window, $hide_array[$menu_id__], $rank_array[$menu_id__], $template_id_array[$menu_id__], $layout_array[$menu_id__], $menu_id__));
		}
	}
}

/**
 * Menu Item Admin - Get All Templates
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_getAllTemplatesListQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			template_id, template_folder
		FROM
			_db_core_templates
		ORDER BY
			template_folder
	";

	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$select_template = parent::invoke();
		if (empty($select_template)) $select_template = array();
		foreach ($select_template as $select_template_array) {
			$template_option[$select_template_array['template_id']] = $select_template_array['template_folder'];
		}

		return $template_option;
	}
}

/**
 * Menu Item Admin - List all menus.
 * @author Jason Schoeman [titan@phpdevshell.org], Ross Kuyper, Contact: rosskuyper@gmail.com.
 *
 */
class PHPDS_listMenusQuery extends PHPDS_query
{
	/**
	 * Initiate query invoke command.
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$core = $this->core;
		$template = $this->template;
		$navigation = $this->navigation;
		$menu_array = $this->factory('menuArray');

		// Page variables.
		$page_edit = $this->navigation->buildURL('3440897808', 'em=');
		$page_delete = $this->navigation->buildURL(false, 'dm=');

		// Get all available templates.
		$template_option = $this->db->invokeQuery("PHPDS_getAllTemplatesListQuery");

		// Icons.
		$icon_found = $template->icon('tick-circle', _('Item Found'));
		$icon_notfound = $template->icon('cross-circle', _('Item Not Found'));
		$delete_menu_icon = $template->icon('task--minus', _('Delete Menu Item'));
		$edit_menu_icon = $template->icon('task--pencil', _('Edit Menu Item'));

		$menu_array->loadMenuArray();

		foreach ($menu_array->menuArray as $select_menu_items_array) {
			// Set menu array.
			$item = $select_menu_items_array;
			// Define.
			$template_option_ = false;
			$hide_selected_1 = false;
			$hide_selected_2 = false;
			$hide_selected_3 = false;
			$hide_selected_4 = false;
			$hide_selected_5 = false;

			// Check if the item is visible or not.
			if ($item['hide'] == 0 || $item['hide'] == 2) {
				$hide_ = '';
			} else {
				$hide_ = 'ui-state-disabled';
			}
			// Check if box needs to be checked or not.
			if ($item['new_window'] == 1) {
				$check_new_window = 'checked';
			} else {
				$check_new_window = false;
			}
			// hide
			switch ($item['hide']) {
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
			// Loop available templates selection dropdown.
			foreach ($template_option as $template_id_ => $template_folder_) {
				// Check if if item should be selected.
				($template_id_ == $item['template_id']) ? $template_selected = 'selected' : $template_selected = false;
				$template_option_ .= '<option value="' . $template_id_ . '" ' . $template_selected . '><small>' . $template_folder_ . '</small></option>';
			}

			// Check if the file could be located
			switch ($item['menu_type']) {
				// Plugin File.
				case 1:
				case 8:
				case 9:
				case 10:
				case 11:
				case 12:
					// Remove first slash.
					$item['menu_link'] = ltrim($item['menu_link'], '/');

					// Check if we can find file.
					if (file_exists('plugins/' . $item['plugin'] . '/controllers/' . $item['menu_link'])) {
						$found = $icon_found;
					} else if (file_exists('plugins/' . $item['plugin'] . '/' . $item['menu_link'])) {
						$found = $icon_found;
					} else {
						$found = $icon_notfound;
					}
					break;
				// Link Existing Menu Item.
				case 2:
					// Check if we have a menu item from link.
					if (!empty($menu_array->menuArray[$item['extend']])) {
						$found = $icon_found;
					} else {
						$found = $icon_notfound;
					}
					break;
				// Link Existing Menu Item (Jump To Link).
				case 3:
					// Check if we have a menu item from link.
					if (!empty($menu_array->menuArray[$item['extend']])) {
						$found = $icon_found;
					} else {
						$found = $icon_notfound;
					}
					break;
				// External File.
				case 4:
					// Check if we can find file.
					if (file_exists($item['menu_link'])) {
						$found = $icon_found;
					} else {
						$found = $icon_notfound;
					}
					break;
				// HTTP URL.
				case 5:
					$found = $icon_found;
					break;
				// Empty Place Holder.
				case 6:
					$found = $icon_found;
					break;
				// iFrame.
				case 7:
					$found = $icon_found;
					break;
			}
			$RESULTS[] = array(
				'hide_' => $hide_,
				'item' => $item,
				'i_url_name' => "[{$item['menu_link']}]" . " [{$item['alias']}]",
				'check_new_window' => $check_new_window,
				'hide_selected_1' => $hide_selected_1,
				'hide_selected_2' => $hide_selected_2,
				'hide_selected_3' => $hide_selected_3,
				'hide_selected_4' => $hide_selected_4,
				'hide_selected_5' => $hide_selected_5,
				'template_option_' => $template_option_,
				'type_name' => $item['type_name'],
				'found' => $found,
				'edit' => "<a href=\"{$page_edit}{$item['menu_id']}\" class=\"button\">{$edit_menu_icon}</a>",
				'delete' => "<a href=\"{$page_delete}{$item['menu_id']}\" {$core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $item['menu_name']))} class=\"button\">{$delete_menu_icon}</a>"
			);

			// Clear variables.
			unset($template_option_, $hide_selected_1, $hide_selected_2, $hide_selected_3, $hide_selected_4, $hide_selected_5);
		}
		if (! empty($RESULTS)) {
			return $RESULTS;
		} else {
			$RESULTS[] = array();
			return $RESULTS;
		}
	}
}