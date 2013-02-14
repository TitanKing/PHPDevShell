<?php

/**
 * Class is used by group listing to create group tree.
 *
 */
class groupTree extends PHPDS_dependant
{
	protected $groupListingArray;
	protected $groupPListingArray;
	protected $parentGroupListingArray;
	protected $outputGroupArray;
	public $groupArray;
	public $RESULTS;

	/**
	 * Queries group database and starts tree listing.
	 *
	 * @param string $extra_sql Allows one to add an additional sql string.
	 * @param boolean $skip_group_id Select group to skip in listing.
	 */
	public function callGroups($extra_sql = '', $skip_group_id = false)
	{
		$db = $this->db;
		if (!empty($extra_sql)) $extra_sql = " $extra_sql ";
		// Call all groups from database.
		$select_user_group = $db->invokeQuery('PHPDS_readGroupTreeQuery', $extra_sql);

		// Assign groups to array, this will be used by tree compiling methods.
		foreach ($select_user_group as $g_arr) {
			// Assign group data.
			$this->groupListingArray[$g_arr['user_group_id']] = array('parent_group_id' => $g_arr['parent_group_id'], 'user_group_name' => $g_arr['user_group_name'], 'user_group_note' => $g_arr['user_group_note'], 'alias' => $g_arr['alias']);
			// Assign parent groups.
			if (!empty($g_arr['parent_group_id'])) {
				// Assing parent items with children.
				$this->groupPListingArray[$g_arr['parent_group_id']][] = $g_arr['user_group_id'];
			}
			if (empty($g_arr['parent_group_id'])) {
				// Assign all root items.
				$this->parentGroupListingArray[] = $g_arr['user_group_id'];
			}
		}
		// Lasty check for broken parent with no root items, make them root items.
		if (!empty($this->groupPListingArray)) {
			foreach ($this->groupPListingArray as $parent_group_id => $array) {
				// Should this parent be listed?
				if ($skip_group_id != $parent_group_id) {
					// Loop through all broken children.
					foreach ($array as $user_group_id) {
						if (empty($this->groupListingArray[$parent_group_id])) {
							$this->parentGroupListingArray[] = $user_group_id;
						}
					}
				}
			}
		}
		// Start listing root items.
		$this->callRootGroup();
	}

	/**
	 * Calls and list all root items and root items with children.
	 *
	 */
	public function callRootGroup()
	{
		// Start listing root and root with children.
		if (!empty($this->parentGroupListingArray)) {
			// Loop all root parents.
			foreach ($this->parentGroupListingArray as $user_group_id) {
				// Check if root group is parent or not.
				if (empty($this->groupPListingArray[$user_group_id])) {
					// Just a root item.
					$this->outputGroupArray[$user_group_id] = array('parent_group_id' => $this->groupListingArray[$user_group_id]['parent_group_id'], 'user_group_name' => $this->groupListingArray[$user_group_id]['user_group_name'], 'user_group_note' => $this->groupListingArray[$user_group_id]['user_group_note'], 'alias' => $this->groupListingArray[$user_group_id]['alias'], 'type' => 'folder_root', 'is_parent' => 0);
				} else {
					// Root item with children.
					$this->outputGroupArray[$user_group_id] = array('parent_group_id' => $this->groupListingArray[$user_group_id]['parent_group_id'], 'user_group_name' => $this->groupListingArray[$user_group_id]['user_group_name'], 'user_group_note' => $this->groupListingArray[$user_group_id]['user_group_note'], 'alias' => $this->groupListingArray[$user_group_id]['alias'], 'type' => 'folder_root_node', 'is_parent' => 1);
					// Call its children.
					$this->callChildGroup($user_group_id);
				}
			}
		}
	}

	/**
	 * Recursive function that loops through all children groups.
	 *
	 * @param int $user_group_id
	 */
	public function callChildGroup($user_group_id)
	{
		// Loop and list groups.
		if (!empty($this->groupPListingArray[$user_group_id])) {
			foreach ($this->groupPListingArray[$user_group_id] as $user_group_id_) {
				// Check deeper parents.
				if (empty($this->groupPListingArray[$user_group_id_])) {
					// List child group.
					$this->outputGroupArray[$user_group_id_] = array('parent_group_id' => $this->groupListingArray[$user_group_id_]['parent_group_id'], 'user_group_name' => $this->groupListingArray[$user_group_id_]['user_group_name'], 'user_group_note' => $this->groupListingArray[$user_group_id_]['user_group_note'], 'alias' => $this->groupListingArray[$user_group_id_]['alias'], 'type' => 'folder_open_node', 'is_parent' => 0);
				} else {
					// List Children - Parent group.
					$this->outputGroupArray[$user_group_id_] = array('parent_group_id' => $this->groupListingArray[$user_group_id_]['parent_group_id'], 'user_group_name' => $this->groupListingArray[$user_group_id_]['user_group_name'], 'user_group_note' => $this->groupListingArray[$user_group_id_]['user_group_note'], 'alias' => $this->groupListingArray[$user_group_id_]['alias'], 'type' => 'folder_parent_open_node', 'is_parent' => 1);
					// Call its children.
					$this->callChildGroup($user_group_id_);
				}
			}
		}
	}

	/**
	 * Will compile the needed HTML for group tree structure ready for output.
	 *
	 * @param boolean $create_html If it should create HTML results.
	 * @param boolean $create_array If it should create short array of node items in order.
	 * @param string $extra_sql Additional sql data to be processed.
	 * @param int Select group to skip in listing.
	 */
	public function compileResults($create_html = true, $create_array = false, $extra_sql = false, $skip_group_id = false)
	{
		$navigation = $this->navigation;

		// Define.
		$alt = false;
		$indent_item = false;

		// Start rolling.
		$this->callGroups($extra_sql, $skip_group_id);

		// Set page to load.
		$page_edit = $navigation->buildURL('edit-group', 'eg=');

		// Will use later to indent groups.
		$indent_group[0] = '';
		// Last loop we would use to finally output the results as HTML.
		if (!empty($this->outputGroupArray)) {
			foreach ($this->outputGroupArray as $user_group_id => $user_groupArray) {
				// Assign basic values.
				$user_group_name = $user_groupArray['user_group_name'];
				$user_group_note = $user_groupArray['user_group_note'];
				$parent_group_id = $user_groupArray['parent_group_id'];
				$alias = $user_groupArray['alias'];
				$type = $user_groupArray['type'];
				$is_parent = $user_groupArray['is_parent'];
				// Calculate folder indention.
				if (!empty($is_parent)) {
					// Define.
					if (empty($indent_item[$parent_group_id]))
							$indent_item[$parent_group_id] = 0;
					$indent_item[$user_group_id] = $indent_item[$parent_group_id] + 1;
				}
				if (!empty($indent_item[$parent_group_id])) {
					$indent_integer = $indent_item[$parent_group_id];
				} else {
					$indent_integer = false;
				}
				// Check if item was already looped, ruling a loop to be created only once per node group.
				if (!array_key_exists($parent_group_id, $indent_group)) {
					// Define.
					$indent = '';
					// Loop and create indent string.
					for ($i = 0; $i <= $indent_integer; $i++) {
						$indent .= '&nbsp;&nbsp;';
					}
					$indent_group[$parent_group_id] = $indent;
				}

				// Should we create a group array in this order?
				if ($create_array == true) {
					$this->groupArray[$user_group_id] = $indent_group[$parent_group_id] . $user_groupArray['user_group_name'];
				}
				// Should we create array for HTML?
				if ($create_html == true) {
					// Build array for html creation.
					$this->RESULTS[] = array(
						'user_group_id' => $user_group_id,
						'indent' => $indent_group[$parent_group_id],
						'type' => $type,
						'user_group_name' => $user_group_name,
						'user_group_note' => $user_group_note,
						'alias' => $alias,
						'edit' => '<a href="' . $page_edit . $user_group_id . '" class="btn click-elegance"><i class="icon-pencil"></i></a>'
					);
				}
				// Reset indention.
				$indent = '';
			}
		}
		if (empty($this->groupArray)) // Set array incase it could be empty.
				$this->groupArray[0] = __('Not Available');
	}
}