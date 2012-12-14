<?php

/**
 * Class contains methods to calculate the structure and other elements of menu items, the methods are dependent of each other.
 *
 */
class menuStructure extends PHPDS_dependant
{
	protected $rootMenuFamily;
	protected $parentMenuFamily;
	protected $databaseInsert;
	protected $groupedMenuItems;

	/**
	 * This method requires data from the database and saves it an array into different groups.
	 *
	 */
	private function menuArray()
	{

		$db = $this->db;

		// Run main query for all menu items and save results in array.
		$main_query = $db->invokeQuery('PHPDS_readStructureQuery');

		foreach ($main_query as $main_query_array) {
			$parent_menu_id = (string) $main_query_array['parent_menu_id'];
			$menu_id = (string) $main_query_array['menu_id'];

			// Collect all root menu items.
			if (!$parent_menu_id) {
				$this->rootMenuFamily[] = (string) $menu_id;
			} // Collect all root parents menu items.
			else {
				$this->parentMenuFamily[] = (string) $parent_menu_id;
			}
			// Save menu items per group.
			$this->groupedMenuItems["$parent_menu_id"][] = (string) $menu_id;
		}

		// Structure start point.
		$this->divideRootMenuItems();
	}

	/**
	 * Compile and divide root group items.
	 *
	 */
	private function divideRootMenuItems()
	{
		foreach ($this->rootMenuFamily as $root_group_menu_id) {
			// Divide root parents and root children from root group.
			// Root Parent -> continue loading children.
			if (in_array("$root_group_menu_id", $this->parentMenuFamily)) {
				$this->databaseInsert .= "('', '$root_group_menu_id', '1', '1'),";
				$this->menuGroupExtract($root_group_menu_id);
			} // Root Child -> stop.
			else {
				$this->databaseInsert .= "('', '$root_group_menu_id', '0', '2'),";
			}
		}
	}

	/**
	 * Extract specific menu group items in their relevant groups on group request.
	 *
	 * @param integer $parent_menu_id
	 */
	private function menuGroupExtract($parent_menu_id)
	{
		// Loop through group and call sub menu group divider.
		foreach ($this->groupedMenuItems["$parent_menu_id"] as $menu_id) {
			$this->divideSubMenuItems($menu_id);
		}
	}

	/**
	 * Compile and divide sub menu items as parents or children.
	 *
	 * @param integer $menu_id
	 */
	private function divideSubMenuItems($menu_id)
	{
		// Divide parents and children from requested group per menu item.
		// Sub Parent -> write parent and continue loading children through next loop.
		if (in_array("$menu_id", $this->parentMenuFamily)) {
			$this->databaseInsert .= "('', '$menu_id', '1', '3'),";
			$this->menuGroupExtract($menu_id);
		} // Sub Child -> stop.
		else {
			$this->databaseInsert .= "('', '$menu_id', '0', '4'),";
		}
	}

	/**
	 * Write generated structure to database.
	 *
	 * @param integer $menu_id
	 */
	public function writeMenuStructure()
	{
		$db = $this->db;
		// Initiate starting point with menu array.
		$this->menuArray();
		$this->databaseInsert = rtrim($this->databaseInsert, ',');
		// Submit results to database.
		if (!empty($this->databaseInsert)) {
			// Clear previous results.
			$db->invokeQuery('PHPDS_deleteStructureQuery');

			// Reset auto increment counter.
			$db->invokeQuery('PHPDS_resetStructureQuery');

			// Insert new results.
			$db->invokeQuery('PHPDS_writeStructureQuery', $this->databaseInsert);
		}
		// Clear old cache.
		$db->cacheClear('navigation');
	}

	/**
	 * Completely delete a menu item and all its sub tables.
	 *
	 * @param mixed Menu id, or could be left out.
	 * @param string Plugin, or delete menu items by plugin which is always the folder the plugin lies in.
	 * @param boolean Checks if only critical menu data needs to be deleted while ignoring data like permissions etc.
	 */
	public function deleteMenu($menu_id = false, $plugin = false, $delete_critical_only = false)
	{
		return $this->db->invokeQuery('PHPDS_deleteMenuQuery', $menu_id, $plugin, $delete_critical_only);
	}

	/**
	 * Insert a new menu item in database.
	 *
	 */
	public function insertMenu($menu_id = false, $parent_menu_id, $menu_name, $menu_link, $plugin, $menu_type, $extend = false, $new_window = false, $rank = false, $hide = false, $template_id = false, $alias = false, $layout = false, $params = false)
	{
		$db = $this->db;
		// Check and make sure we have a menu id.
		if (!empty($menu_id)) {
			////////////////////////////////
			// Save new item to database. //
			////////////////////////////////
			$db->invokeQuery('PHPDS_writeMenuQuery', $menu_id, $parent_menu_id, $menu_name, $menu_link, $plugin, $menu_type, $extend, $new_window, $rank, $hide, $template_id, $alias, $layout, $params);

			// Write the menu structure.
			$this->writeMenuStructure();
		}
	}

	/**
	 * Deletes a menu item when the $_GET['dm'] variable is used.
	 *
	 * @return boolean
	 */
	public function getDelete()
	{
		$db = $this->db;
		$configuration = $this->configuration;
		$security = $this->security;

		// Call plugin name from database.
		$get_plugin = $db->invokeQuery('PHPDS_readPluginFromMenuIdQuery', $security->get['dm']);

		// Now we can see if a delete is possible.
		if (!empty($security->get['dm']) && !empty($get_plugin)) {
			// Do the actual menu delete.
			$this->deleteMenu($security->get['dm']);
			// Write the menu structure.
			$this->writeMenuStructure();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Convert file location to unsigned CRC32 value. This is unique and allows one to locate a menu item from location as well.
	 *
	 * @param string The plugin folder the file is in.
	 * @param string Actual file link.
	 * @return integer
	 * @author Jason Schoeman
	 */
	public function createMenuId($plugin_folder, $link)
	{
		$db = $this->db;
		$menu_id_db = $db->invokeQuery('PHPDS_readMenuIdFromMenuLinkQuery', $link, $plugin_folder);

		if (! empty($menu_id_db)) {
			return $menu_id_db;
		} else {
			// Before we create a menu id, we need to check if it is available already.
			// If it is, we need to get it and return this value rather.
			// Create menu id from string.
			return sprintf('%u', crc32(str_ireplace('/', '', $plugin_folder . $link)));
		}
	}

	/**
	 * This method will update an existing menu id with a new menu id if the old id exists.
	 *
	 * @param int $new_id
	 * @param int $old_id
	 * @param boolean True checks if the menu id already exists.
	 * @return mixed
	 * @author Jason Schoeman
	 * @since 10 May 2012
	 */
	public function updateMenuId ($new_id, $old_id, $skip_check=false)
	{
		// Check if we the menu item exists.
		if (!$skip_check) {
			$exisiting_id = $this->menuIdExist($old_id);
		} else {
			$exisiting_id = true;
		}

		if (! empty($exisiting_id)) {
			if ($this->db->invokeQuery('PHPDS_updateMenuIdQuery', $new_id, $old_id)) return $new_id;
		} else {
			return false;
		}
	}

	/**
	 * Check if a menu exists.
	 *
	 * @param int $menu_id
	 * @return mixed
	 */
	public function menuIdExist ($menu_id)
	{
		return $this->db->invokeQuery('PHPDS_doesMenuIdExistQuery', $menu_id);
	}
}