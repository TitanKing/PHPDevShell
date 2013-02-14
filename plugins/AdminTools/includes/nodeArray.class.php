<?php

/**
 * This file extracts the compiled node (with order) listing from the database where a complete node structure is listed.
 * Simple extraction class to extract the complete node.
 *
 * @author Jason Schoeman
 */
class nodeArray extends PHPDS_dependant
{
	public $nodeArray;

	/**
	 * Creates an array of the complete node database.
	 *
	 * @param integer $node_id Gets data from specific node id only.
	 * @param boolean $determine_node_name Should the system try to determine the node name.
	 * @author Jason Schoeman
	 */
	public function loadNodeArray($node_id = 0, $determine_node_name = true)
	{
		$configuration      = $this->configuration;
		$navigation         = $this->navigation;
		$db                 = $this->db;
		$template           = $this->template;

		$select_node_items  = $db->invokeQuery('PHPDS_readNodesQuery', $node_id);

		$indent_group[0]    = false;

		// Icons.
		$icon_1     = $template->icon('plug', __('Plugin Item'));
		$icon_2     = $template->icon('plug--plus', __('Link Existing Plugin Item'));
		$icon_3     = $template->icon('plug--arrow', __('Link Existing Plugin Item and Jump to its Group'));
		$icon_4     = $template->icon('script', __('Execute External File'));
		$icon_5     = $template->icon('chain', __('External http URL'));
		$icon_6     = $template->icon('chain-unchain', __('Unclickable Place Holder'));
		$icon_7     = $template->icon('ui-split-panel-vertical', __('iFrame Item'));
		$icon_8     = $template->icon('clock-select', __('Cronjob Item'));
		$icon_9     = $template->icon('layout-select-content', __('HTML Ajax Widget'));
		$icon_10    = $template->icon('application-block', __('HTML Ajax'));
		$icon_11    = $template->icon('applications-blue', __('HTML Ajax Lightbox'));
		$icon_12    = $template->icon('script-code', __('Raw Ajax'));

		foreach ($select_node_items as $select_node_items_array) {
			// Calculate folder indention.
			if ($select_node_items_array['is_parent'] == 1) {
				if (empty($indent_item[$select_node_items_array['parent_node_id']])) {
					$indent_item_ = 1;
				} else {
					$indent_item_ = $indent_item[$select_node_items_array['parent_node_id']] + 1;
				}
				$indent_item[$select_node_items_array['node_id']] = $indent_item_;
			}
			// Define.
			if (!empty($indent_item[$select_node_items_array['parent_node_id']])) {
				$indent_integer = $indent_item[$select_node_items_array['parent_node_id']];
			} else {
				$indent_integer = false;
			}
			// Check if item was already looped, ruling a loop to be created only once per node group.
			if (!array_key_exists($select_node_items_array['parent_node_id'], $indent_group)) {
				// Define.
				$indent = false;
				// Loop and create indent string.
				for ($i = 0; $i <= $indent_integer; $i++) {
					$indent .= '<span class="ui-icon ui-icon-grip-dotted-vertical left"></span>';
				}
				$indent_group[$select_node_items_array['parent_node_id']] = $indent;
			}
			// Check if node item is a parent and assign correct div class.
			switch ($select_node_items_array['type']) {
				case 1:
				case 8:
				case 9:
				case 10:
				case 11:
				case 12:
					$node_indent = $indent_group[$select_node_items_array['parent_node_id']];
					$div_folder = '<span class="ui-icon ui-icon-arrowreturnthick-1-e left"></span>';
					break;
				case 2:
					$node_indent = $indent_group[$select_node_items_array['parent_node_id']];
					$div_folder = '<span class="ui-icon ui-icon-triangle-1-e left"></span>';
					break;
				case 3:
					$node_indent = $indent_group[$select_node_items_array['parent_node_id']];
					$div_folder = '<span class="ui-icon ui-icon-arrowreturn-1-e left"></span>';
					break;
				case 4:
					$node_indent = $indent_group[$select_node_items_array['parent_node_id']];
					$div_folder = '<span class="ui-icon ui-icon-carat-1-e left"></span>';
					break;
				default:
					$node_indent = '';
					$div_folder = false;
					break;
			}
			$indent = false;
			// Determine node name.
			if ($determine_node_name == true) {
				$node_name = $navigation->determineNodeName($select_node_items_array['node_name'], $select_node_items_array['node_link'], $select_node_items_array['node_id']);
			} else {
				$node_name = $select_node_items_array['node_name'];
			}
			// Get node url name.
			switch ($select_node_items_array['node_type']) {
				// Plugin File.
				case 1:
					$type_name = 'page';
					// Remove first slash.
					$select_node_items_array['node_link'] = ltrim($select_node_items_array['node_link'], '/');
					// Create type icon.
					$select_node_items_array['node_type_d'] = $icon_1;
					// Node URL Name.
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// Link Existing Node Item.
				case 2:
					$type_name = 'link';
					$select_node_items_array['node_type_d'] = $icon_2;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['extend']) . '">' . $node_name . '</a>';
					break;
				// Link Existing Node Item (Jump To Link).
				case 3:
					$type_name = 'jump';
					$select_node_items_array['node_type_d'] = $icon_3;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['extend']) . '">' . $node_name . '</a>';
					break;
				// External File.
				case 4:
					$type_name = 'external';
					$select_node_items_array['node_type_d'] = $icon_4;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// HTTP URL.
				case 5:
					$type_name = 'url';
					$select_node_items_array['node_type_d'] = $icon_5;
					// Node URL Name
					$url_name = '<a href="' . $select_node_items_array['node_link'] . '">' . $node_name . '</a>';
					break;
				// Empty Place Holder.
				case 6:
					$type_name = 'placeholder';
					$select_node_items_array['node_type_d'] = $icon_6;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// iFrame Weblink.
				case 7:
					$type_name = 'iframe';
					$select_node_items_array['node_type_d'] = $icon_7;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// Cronjob Item.
				case 8:
					$type_name = 'cronjob';
					$select_node_items_array['node_type_d'] = $icon_8;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// HTML Ajax Widget
				case 9:
					$type_name = 'widget';
					$select_node_items_array['node_type_d'] = $icon_9;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// HTML Ajax
				case 10:
					$type_name = 'htmlajax';
					$select_node_items_array['node_type_d'] = $icon_10;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// HTML Ajax Lightbox
				case 11:
					$type_name = 'lightbox';
					$select_node_items_array['node_type_d'] = $icon_11;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
				// RAW Lightbox
				case 12:
					$type_name = 'rawajax';
					$select_node_items_array['node_type_d'] = $icon_12;
					// Node URL Name
					$url_name = '<a href="' . $navigation->buildURL($select_node_items_array['node_id']) . '">' . $node_name . '</a>';
					break;
			}
			$this->nodeArray[$select_node_items_array['node_id']] = array('node_id' => $select_node_items_array['node_id'], 'parent_node_id' => $select_node_items_array['parent_node_id'], 'alias' => $select_node_items_array['alias'], 'node_name' => $node_name, 'url_name' => $url_name, 'node_link' => $select_node_items_array['node_link'], 'plugin' => $select_node_items_array['plugin'], 'node_type' => $select_node_items_array['node_type'], 'node_type_d' => $select_node_items_array['node_type_d'], 'extend' => $select_node_items_array['extend'], 'rank' => $select_node_items_array['rank'], 'hide' => $select_node_items_array['hide'], 'new_window' => $select_node_items_array['new_window'], 'is_parent' => $select_node_items_array['is_parent'], 'type' => $select_node_items_array['type'], 'template_id' => $select_node_items_array['template_id'], 'div_folder' => $div_folder, 'layout' => $select_node_items_array['layout'], 'node_indent' => $node_indent, 'type_name'=>$type_name, 'params' => $select_node_items_array['params']);
		}
	}
}