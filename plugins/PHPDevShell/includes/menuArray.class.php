<?php

/**
 * This file extracts the compiled menu (with order) listing from the database where a complete menu structure is listed.
 * Simple extraction class to extract the complete menu.
 *
 * @author Jason Schoeman
 */
class menuArray extends PHPDS_dependant
{
	public $menuArray;

	/**
	 * Creates an array of the complete menu database.
	 *
	 * @param integer Gets data from specific menu id only.
	 * @param boolean Should the system try to determine the menu name.
	 * @author Jason Schoeman
	 */
	public function loadMenuArray($menu_id = false, $determine_menu_name = true)
	{
		$configuration = $this->configuration;
		$navigation = $this->navigation;
		$db = $this->db;
		$template = $this->template;

		// Set gettext domain.
		$d = 'PHPDevShell';

		$select_menu_items = $db->invokeQuery('PHPDS_readMenusQuery', $menu_id);

		$indent_group[0] = false;

		// Icons.
		$icon_1 = $template->icon('plug', _('Plugin Item'));
		$icon_2 = $template->icon('plug--plus', _('Link Existing Plugin Item'));
		$icon_3 = $template->icon('plug--arrow', _('Link Existing Plugin Item and Jump to its Group'));
		$icon_4 = $template->icon('script', _('Execute External File'));
		$icon_5 = $template->icon('chain', _('External http URL'));
		$icon_6 = $template->icon('chain-unchain', _('Unclickable Place Holder'));
		$icon_7 = $template->icon('ui-split-panel-vertical', _('iFrame Item'));
		$icon_8 = $template->icon('clock-select', _('Cronjob Item'));
		$icon_9 = $template->icon('layout-select-content', _('HTML Ajax Widget'));
		$icon_10 = $template->icon('application-block', _('HTML Ajax'));
		$icon_11 = $template->icon('applications-blue', _('HTML Ajax Lightbox'));
		$icon_12 = $template->icon('script-code', _('Raw Ajax'));

		foreach ($select_menu_items as $select_menu_items_array) {
			// Calculate folder indention.
			if ($select_menu_items_array['is_parent'] == 1) {
				if (empty($indent_item[$select_menu_items_array['parent_menu_id']])) {
					$indent_item_ = 1;
				} else {
					$indent_item_ = $indent_item[$select_menu_items_array['parent_menu_id']] + 1;
				}
				$indent_item[$select_menu_items_array['menu_id']] = $indent_item_;
			}
			// Define.
			if (!empty($indent_item[$select_menu_items_array['parent_menu_id']])) {
				$indent_integer = $indent_item[$select_menu_items_array['parent_menu_id']];
			} else {
				$indent_integer = false;
			}
			// Check if item was already looped, ruling a loop to be created only once per menu group.
			if (!key_exists($select_menu_items_array['parent_menu_id'], $indent_group)) {
				// Define.
				$indent = false;
				// Loop and create indent string.
				for ($i = 0; $i <= $indent_integer; $i++) {
					$indent .= '<span class="ui-icon ui-icon-grip-dotted-vertical left"></span>';
				}
				$indent_group[$select_menu_items_array['parent_menu_id']] = $indent;
			}
			// Check if menu item is a parent and assign correct div class.
			switch ($select_menu_items_array['type']) {
				case 1:
				case 8:
				case 9:
				case 10:
				case 11:
				case 12:
					$menu_indent = $indent_group[$select_menu_items_array['parent_menu_id']];
					$div_folder = '<span class="ui-icon ui-icon-arrowreturnthick-1-e left"></span>';
					break;
				case 2:
					$menu_indent = $indent_group[$select_menu_items_array['parent_menu_id']];
					$div_folder = '<span class="ui-icon ui-icon-triangle-1-e left"></span>';
					break;
				case 3:
					$menu_indent = $indent_group[$select_menu_items_array['parent_menu_id']];
					$div_folder = '<span class="ui-icon ui-icon-arrowreturn-1-e left"></span>';
					break;
				case 4:
					$menu_indent = $indent_group[$select_menu_items_array['parent_menu_id']];
					$div_folder = '<span class="ui-icon ui-icon-carat-1-e left"></span>';
					break;
				default:
					$menu_indent = '';
					$div_folder = false;
					break;
			}
			$indent = false;
			// Determine menu name.
			if ($determine_menu_name == true) {
				$menu_name = $navigation->determineMenuName($select_menu_items_array['menu_name'], $select_menu_items_array['menu_link'], $select_menu_items_array['menu_id']);
			} else {
				$menu_name = $select_menu_items_array['menu_name'];
			}
			// Get menu url name.
			switch ($select_menu_items_array['menu_type']) {
				// Plugin File.
				case 1:
					$type_name = 'page';
					// Remove first slash.
					$select_menu_items_array['menu_link'] = ltrim($select_menu_items_array['menu_link'], '/');
					// Create type icon.
					$select_menu_items_array['menu_type_d'] = $icon_1;
					// Menu URL Name.
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// Link Existing Menu Item.
				case 2:
					$type_name = 'link';
					$select_menu_items_array['menu_type_d'] = $icon_2;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['extend']) . '">' . $menu_name . '</a>';
					break;
				// Link Existing Menu Item (Jump To Link).
				case 3:
					$type_name = 'jump';
					$select_menu_items_array['menu_type_d'] = $icon_3;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['extend']) . '">' . $menu_name . '</a>';
					break;
				// External File.
				case 4:
					$type_name = 'external';
					$select_menu_items_array['menu_type_d'] = $icon_4;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// HTTP URL.
				case 5:
					$type_name = 'url';
					$select_menu_items_array['menu_type_d'] = $icon_5;
					// Menu URL Name
					$url_name = '<a href="' . $select_menu_items_array['menu_link'] . '">' . $menu_name . '</a>';
					break;
				// Empty Place Holder.
				case 6:
					$type_name = 'placeholder';
					$select_menu_items_array['menu_type_d'] = $icon_6;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// iFrame Weblink.
				case 7:
					$type_name = 'iframe';
					$select_menu_items_array['menu_type_d'] = $icon_7;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// Cronjob Item.
				case 8:
					$type_name = 'cronjob';
					$select_menu_items_array['menu_type_d'] = $icon_8;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// HTML Ajax Widget
				case 9:
					$type_name = 'widget';
					$select_menu_items_array['menu_type_d'] = $icon_9;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// HTML Ajax
				case 10:
					$type_name = 'htmlajax';
					$select_menu_items_array['menu_type_d'] = $icon_10;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// HTML Ajax Lightbox
				case 11:
					$type_name = 'lightbox';
					$select_menu_items_array['menu_type_d'] = $icon_11;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
				// RAW Lightbox
				case 12:
					$type_name = 'rawajax';
					$select_menu_items_array['menu_type_d'] = $icon_12;
					// Menu URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_menu_items_array['menu_id']) . '">' . $menu_name . '</a>';
					break;
			}
			$this->menuArray[$select_menu_items_array['menu_id']] = array('menu_id' => $select_menu_items_array['menu_id'], 'parent_menu_id' => $select_menu_items_array['parent_menu_id'], 'alias' => $select_menu_items_array['alias'], 'menu_name' => $menu_name, 'url_name' => $url_name, 'menu_link' => $select_menu_items_array['menu_link'], 'plugin' => $select_menu_items_array['plugin'], 'menu_type' => $select_menu_items_array['menu_type'], 'menu_type_d' => $select_menu_items_array['menu_type_d'], 'extend' => $select_menu_items_array['extend'], 'rank' => $select_menu_items_array['rank'], 'hide' => $select_menu_items_array['hide'], 'new_window' => $select_menu_items_array['new_window'], 'is_parent' => $select_menu_items_array['is_parent'], 'type' => $select_menu_items_array['type'], 'template_id' => $select_menu_items_array['template_id'], 'div_folder' => $div_folder, 'layout' => $select_menu_items_array['layout'], 'menu_indent' => $menu_indent, 'type_name'=>$type_name, 'params' => $select_menu_items_array['params']);
		}
	}
}