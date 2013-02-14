<?php

/**
 * Class contains methods to calculate the structure and other elements of node items, the methods are dependent of each other.
 *
 */
class nodeStructure extends PHPDS_dependant
{
	protected $rootNodeFamily;
	protected $parentNodeFamily;
	protected $databaseInsert;
	protected $groupedNodeItems;

	/**
	 * This method requires data from the database and saves it an array into different groups.
	 *
	 */
	private function nodeArray()
	{

		$db = $this->db;

		// Run main query for all node items and save results in array.
		$main_query = $db->invokeQuery('PHPDS_readStructureQuery');

		foreach ($main_query as $main_query_array) {
			$parent_node_id = (string) $main_query_array['parent_node_id'];
			$node_id = (string) $main_query_array['node_id'];

			// Collect all root node items.
			if (!$parent_node_id) {
				$this->rootNodeFamily[] = (string) $node_id;
			} // Collect all root parents node items.
			else {
				$this->parentNodeFamily[] = (string) $parent_node_id;
			}
			// Save node items per group.
			$this->groupedNodeItems["$parent_node_id"][] = (string) $node_id;
		}

		// Structure start point.
		$this->divideRootNodeItems();
	}

	/**
	 * Compile and divide root group items.
	 *
	 */
	private function divideRootNodeItems()
	{
		foreach ($this->rootNodeFamily as $root_group_node_id) {
			// Divide root parents and root children from root group.
			// Root Parent -> continue loading children.
			if (in_array("$root_group_node_id", $this->parentNodeFamily)) {
				$this->databaseInsert .= "('', '$root_group_node_id', '1', '1'),";
				$this->nodeGroupExtract($root_group_node_id);
			} // Root Child -> stop.
			else {
				$this->databaseInsert .= "('', '$root_group_node_id', '0', '2'),";
			}
		}
	}

	/**
	 * Extract specific node group items in their relevant groups on group request.
	 *
	 * @param integer $parent_node_id
	 */
	private function nodeGroupExtract($parent_node_id)
	{
		// Loop through group and call sub node group divider.
		foreach ($this->groupedNodeItems["$parent_node_id"] as $node_id) {
			$this->divideSubNodeItems($node_id);
		}
	}

	/**
	 * Compile and divide sub node items as parents or children.
	 *
	 * @param integer $node_id
	 */
	private function divideSubNodeItems($node_id)
	{
		// Divide parents and children from requested group per node item.
		// Sub Parent -> write parent and continue loading children through next loop.
		if (in_array("$node_id", $this->parentNodeFamily)) {
			$this->databaseInsert .= "('', '$node_id', '1', '3'),";
			$this->nodeGroupExtract($node_id);
		} // Sub Child -> stop.
		else {
			$this->databaseInsert .= "('', '$node_id', '0', '4'),";
		}
	}

	/**
	 * Write generated structure to database.
	 */
	public function writeNodeStructure()
	{
		$db = $this->db;
		// Initiate starting point with node array.
		$this->nodeArray();
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
	 * Completely delete a node item and all its sub tables.
	 *
	 * @param mixed Node id, or could be left out.
	 * @param string Plugin, or delete node items by plugin which is always the folder the plugin lies in.
	 * @param boolean Checks if only critical node data needs to be deleted while ignoring data like permissions etc.
	 */
	public function deleteNode($node_id = false, $plugin = false, $delete_critical_only = false)
	{
		return $this->db->invokeQuery('PHPDS_deleteNodeQuery', $node_id, $plugin, $delete_critical_only);
	}

	/**
	 * Insert a new node item in database.
	 *
	 */
	public function insertNode($node_id = false, $parent_node_id, $node_name, $node_link, $plugin, $node_type, $extend = false, $new_window = false, $rank = false, $hide = false, $template_id = false, $alias = false, $layout = false, $params = false)
	{
		$db = $this->db;
		// Check and make sure we have a node id.
		if (!empty($node_id)) {
			////////////////////////////////
			// Save new item to database. //
			////////////////////////////////
			$db->invokeQuery('PHPDS_writeNodeQuery', $node_id, $parent_node_id, $node_name, $node_link, $plugin, $node_type, $extend, $new_window, $rank, $hide, $template_id, $alias, $layout, $params);

			// Write the node structure.
			$this->writeNodeStructure();
		}
	}

	/**
	 * Deletes a node item when the $_GET['dm'] variable is used.
	 *
	 * @return boolean
	 */
	public function getDelete()
	{
		$db = $this->db;
		$configuration = $this->configuration;
		$security = $this->security;

		// Call plugin name from database.
		$get_plugin = $db->invokeQuery('PHPDS_readPluginFromNodeIdQuery', $security->get['dm']);

		// Now we can see if a delete is possible.
		if (!empty($security->get['dm']) && !empty($get_plugin)) {
			// Do the actual node delete.
			$this->deleteNode($security->get['dm']);
			// Write the node structure.
			$this->writeNodeStructure();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Convert file location to unsigned CRC32 value. This is unique and allows one to locate a node item from location as well.
	 *
	 * @param string The plugin folder the file is in.
	 * @param string Actual file link.
	 * @return integer
	 * @author Jason Schoeman
	 */
	public function createNodeId($plugin_folder, $link)
	{
		$db = $this->db;
		$node_id_db = $db->invokeQuery('PHPDS_readNodeIdFromNodeLinkQuery', $link, $plugin_folder);

		if (! empty($node_id_db)) {
			return $node_id_db;
		} else {
			// Before we create a node id, we need to check if it is available already.
			// If it is, we need to get it and return this value rather.
			// Create node id from string.
			return sprintf('%u', crc32(str_ireplace('/', '', $plugin_folder . $link)));
		}
	}

	/**
	 * This method will update an existing node id with a new node id if the old id exists.
	 *
	 * @param int $new_id
	 * @param int $old_id
	 * @param boolean True checks if the node id already exists.
	 * @return mixed
	 * @author Jason Schoeman
	 * @since 10 May 2012
	 */
	public function updateNodeId ($new_id, $old_id, $skip_check=false)
	{
		// Check if we the node item exists.
		if (!$skip_check) {
			$exisiting_id = $this->nodeIdExist($old_id);
		} else {
			$exisiting_id = true;
		}

		if (! empty($exisiting_id)) {
			if ($this->db->invokeQuery('PHPDS_updateNodeIdQuery', $new_id, $old_id)) return $new_id;
		} else {
			return false;
		}
	}

	/**
	 * Check if a node exists.
	 *
	 * @param int $node_id
	 * @return mixed
	 */
	public function nodeIdExist ($node_id)
	{
		return $this->db->invokeQuery('PHPDS_doesNodeIdExistQuery', $node_id);
	}
}