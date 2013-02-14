<?php

/**
 * Node Stucture - Read structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readStructureQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id, t1.parent_node_id
		FROM
			_db_core_node_items t1
		ORDER BY
			t1.rank
		ASC
    ";
}

/**
 * Node Stucture - Delete structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteStructureQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_node_structure
    ";
}

/**
 * Node Stucture - Reset structure pointer.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_resetStructureQuery extends PHPDS_query
{
	protected $sql = "
		ALTER TABLE
			_db_core_node_structure
		AUTO_INCREMENT = 0;
    ";
}

/**
 * Node Stucture - Write new structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeStructureQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_node_structure (id, node_id, is_parent, type)
		VALUES
			%s
    ";
}

/**
 * Node Stucture - Delete Nodes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteNodesQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_node_items
		WHERE
			node_id
		%s
    ";
}

/**
 * Node Stucture - Delete node structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteNodeStructureQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_node_structure
		WHERE
			node_id
		%s
    ";
}

/**
 * Node Stucture - Delete role permissions.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			node_id
		%s
    ";
}

/**
 * Node Stucture - Delete filters.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteFiltersQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_filter
		WHERE
			node_id
		%s
    ";
}

/**
 * Node Stucture - Delete crons.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteCronsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_cron
		WHERE
			node_id
		%s
    ";
}

/**
 * Node Stucture - Delete one or many nodes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteNodeQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			node_id
		FROM
			_db_core_node_items
		WHERE
			plugin = '%s'
    ";

	/**
	 * Initiate invoke.
	 *
	 * @return boolean
	 */
	public function invoke($parameters = null)
	{
		$db = $this->db;

		list($node_id, $plugin, $delete_critical_only) = $parameters;
		// Define.
		$db_condition = '';
		// Check if plugin item should be deleted.
		if ($plugin != false && $node_id == false) {
			$node_id_db = parent::invoke(array($plugin));

            if (! empty($node_id_db)) {
                foreach ($node_id_db as $node_id_array) {
                    $db_condition .= "'{$node_id_array['node_id']}',";
                }
            }
			// Check if there is any condition.
			if (!empty($db_condition)) {
				// Correct for database condition.
				$db_condition = rtrim($db_condition, ",");
				// Complete condition.
				$condition = " IN ($db_condition)";
			}
		} // The user may want to give an array of items to be deleted.
		else if (is_array($node_id)) {
			foreach ($node_id as $item_to_delete) {
				// Check if item needs to be converted to node item.
				$db_condition .= "'$item_to_delete',";
			}
			// Check if there is any condition.
			if (!empty($db_condition)) {
				// Correct for database condition.
				$db_condition = rtrim($db_condition, ",");
				// Complete condition.
				$condition = " IN ($db_condition)";
			}
		} else {
			// Complete condition.
			$condition = " = '$node_id'";
		}
		// Only execute when not empty.
		if (!empty($condition)) {
			// Delete Node Items.
			$db->invokeQuery('PHPDS_deleteNodesQuery', $condition);

			// Delete Node Structure.
			$db->invokeQuery('PHPDS_deleteNodeStructureQuery', $condition);

			// Continue deleting?
			if ($delete_critical_only == false) {
				// Delete Node Permissions.
				$db->invokeQuery('PHPDS_deleteRolePermissionsQuery', $condition);

				// Delete all filters that belongs to this node item.
				$db->invokeQuery('PHPDS_deleteFiltersQuery', $condition);

				// Delete all cron items connected to this node.
				$db->invokeQuery('PHPDS_deleteCronsQuery', $condition);
			}
			return true;
		}
	}
}

/**
 * Node Stucture - Write node item.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeNodeQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_node_items (node_id, parent_node_id, node_name, node_link, plugin, node_type, extend, new_window, rank, hide, template_id, alias, layout, params)
		VALUES
			('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    ";
}

/**
 * Node Stucture - Get plugin name from node id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readPluginFromNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			plugin
		FROM
			_db_core_node_items
		WHERE
			node_id = '%s'
    ";

	protected $singleValue = true;
}

/**
 * Node Stucture - Get plugin name from node id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readNodeIdFromNodeLinkQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			node_id
		FROM
			_db_core_node_items
		WHERE
			node_link = '%s'
		AND
			plugin = '%s'
    ";

	protected $singleValue = true;
}

/**
 * Node Stucture - Delete one or many nodes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_doesNodeIdExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			node_id
		FROM
			_db_core_node_items
		WHERE
			node_id = '%s';
	";

	protected $singleValue = true;
}

/**
 * Node Stucture - Update a complete set of node ids all over with new node id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateNodeIdQuery extends PHPDS_query
{
	/**
	 * Initiate invoke.
	 *
	 * @return boolean
	 */
	public function invoke($parameters = null)
	{
		$db = $this->db;

		list($new_id, $old_id) = $parameters;

		$db->invokeQuery('PHPDS_updateNodeItemsIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateParentNodeItemsIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateNodeItemsExtendQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreCronNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreFilterNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreAccessLogsNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreNodeStructurNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreUploadLogsNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreUserRolePermissionsNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreSettingsNodeIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreTagsNodeIdQuery', $new_id, $old_id);

		return true;
	}
}

/**
 * Node Stucture - UPDATE `_db_core_node_items` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateNodeItemsIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_node_items` SET node_id='%s' WHERE node_id='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_node_items` (parent_node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateParentNodeItemsIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_node_items` SET parent_node_id='%s' WHERE parent_node_id='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_node_items` (extend)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateNodeItemsExtendQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_node_items` SET extend='%s' WHERE extend='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_cron` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreCronNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_cron` SET node_id='%s' WHERE node_id='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_filter` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreFilterNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_filter` SET node_id='%s' WHERE node_id='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_node_access_logs` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreAccessLogsNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_node_access_logs` SET node_id='%s' WHERE node_id='%s';
	";
}


/**
 * Node Stucture - UPDATE `_db_core_node_structure` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreNodeStructurNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_node_structure` SET node_id='%s' WHERE node_id='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_upload_logs` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreUploadLogsNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_upload_logs` SET node_id='%s' WHERE node_id='%s';
	";
}


/**
 * Node Stucture - UPDATE `_db_core_user_role_permissions` (node_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreUserRolePermissionsNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_user_role_permissions` SET node_id='%s' WHERE node_id='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_settings` (setting_value)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreSettingsNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_settings` SET setting_value='%s' WHERE setting_value='%s';
	";
}

/**
 * Node Stucture - UPDATE `_db_core_tags` (core_tags)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreTagsNodeIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_tags` SET tagTarget='%s' WHERE tagTarget='%s';
	";
}

