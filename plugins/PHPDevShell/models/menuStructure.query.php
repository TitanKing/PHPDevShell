<?php

/**
 * Menu Stucture - Read structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readStructureQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.menu_id, t1.parent_menu_id
		FROM
			_db_core_menu_items t1
		ORDER BY
			t1.rank
		ASC
    ";
}

/**
 * Menu Stucture - Delete structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteStructureQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_menu_structure
    ";
}

/**
 * Menu Stucture - Reset structure pointer.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_resetStructureQuery extends PHPDS_query
{
	protected $sql = "
		ALTER TABLE
			_db_core_menu_structure
		AUTO_INCREMENT = 0;
    ";
}

/**
 * Menu Stucture - Write new structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeStructureQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_menu_structure (id, menu_id, is_parent, type)
		VALUES
			%s
    ";
}

/**
 * Menu Stucture - Delete Menus.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteMenusQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_menu_items
		WHERE
			menu_id
		%s
    ";
}

/**
 * Menu Stucture - Delete menu structure.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteMenuStructureQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_menu_structure
		WHERE
			menu_id
		%s
    ";
}

/**
 * Menu Stucture - Delete role permissions.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteRolePermissionsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			menu_id
		%s
    ";
}

/**
 * Menu Stucture - Delete filters.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteFiltersQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_filter
		WHERE
			menu_id
		%s
    ";
}

/**
 * Menu Stucture - Delete crons.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteCronsQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_cron
		WHERE
			menu_id
		%s
    ";
}

/**
 * Menu Stucture - Delete one or many menus.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteMenuQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			menu_id
		FROM
			_db_core_menu_items
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

		list($menu_id, $plugin, $delete_critical_only) = $parameters;
		// Define.
		$db_condition = '';
		// Check if plugin item should be deleted.
		if ($plugin != false && $menu_id == false) {
			$menu_id_db = parent::invoke(array($plugin));

            if (! empty($menu_id_db)) {
                foreach ($menu_id_db as $menu_id_array) {
                    $db_condition .= "'{$menu_id_array['menu_id']}',";
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
		else if (is_array($menu_id)) {
			foreach ($menu_id as $item_to_delete) {
				// Check if item needs to be converted to menu item.
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
			$condition = " = '$menu_id'";
		}
		// Only execute when not empty.
		if (!empty($condition)) {
			// Delete Menu Items.
			$db->invokeQuery('PHPDS_deleteMenusQuery', $condition);

			// Delete Menu Structure.
			$db->invokeQuery('PHPDS_deleteMenuStructureQuery', $condition);

			// Continue deleting?
			if ($delete_critical_only == false) {
				// Delete Menu Permissions.
				$db->invokeQuery('PHPDS_deleteRolePermissionsQuery', $condition);

				// Delete all filters that belongs to this menu item.
				$db->invokeQuery('PHPDS_deleteFiltersQuery', $condition);

				// Delete all cron items connected to this menu.
				$db->invokeQuery('PHPDS_deleteCronsQuery', $condition);
			}
			return true;
		}
	}
}

/**
 * Menu Stucture - Write menu item.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeMenuQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_menu_items (menu_id, parent_menu_id, menu_name, menu_link, plugin, menu_type, extend, new_window, rank, hide, template_id, alias, layout, params)
		VALUES
			('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    ";
}

/**
 * Menu Stucture - Get plugin name from menu id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readPluginFromMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			plugin
		FROM
			_db_core_menu_items
		WHERE
			menu_id = '%s'
    ";

	protected $singleValue = true;
}

/**
 * Menu Stucture - Get plugin name from menu id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readMenuIdFromMenuLinkQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			menu_id
		FROM
			_db_core_menu_items
		WHERE
			menu_link = '%s'
		AND
			plugin = '%s'
    ";

	protected $singleValue = true;
}

/**
 * Menu Stucture - Delete one or many menus.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_doesMenuIdExistQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			menu_id
		FROM
			_db_core_menu_items
		WHERE
			menu_id = '%s';
	";

	protected $singleValue = true;
}

/**
 * Menu Stucture - Update a complete set of menu ids all over with new menu id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateMenuIdQuery extends PHPDS_query
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

		$db->invokeQuery('PHPDS_updateMenuItemsIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateParentMenuItemsIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateMenuItemsExtendQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreCronMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreFilterMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreAccessLogsMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreMenuStructurMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreUploadLogsMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreUserRolePermissionsMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreSettingsMenuIdQuery', $new_id, $old_id);
		$db->invokeQuery('PHPDS_updateCoreTagsMenuIdQuery', $new_id, $old_id);

		return true;
	}
}

/**
 * Menu Stucture - UPDATE `_db_core_menu_items` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateMenuItemsIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_menu_items` SET menu_id='%s' WHERE menu_id='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_menu_items` (parent_menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateParentMenuItemsIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_menu_items` SET parent_menu_id='%s' WHERE parent_menu_id='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_menu_items` (extend)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateMenuItemsExtendQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_menu_items` SET extend='%s' WHERE extend='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_cron` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreCronMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_cron` SET menu_id='%s' WHERE menu_id='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_filter` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreFilterMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_filter` SET menu_id='%s' WHERE menu_id='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_menu_access_logs` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreAccessLogsMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_menu_access_logs` SET menu_id='%s' WHERE menu_id='%s';
	";
}


/**
 * Menu Stucture - UPDATE `_db_core_menu_structure` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreMenuStructurMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_menu_structure` SET menu_id='%s' WHERE menu_id='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_upload_logs` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreUploadLogsMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_upload_logs` SET menu_id='%s' WHERE menu_id='%s';
	";
}


/**
 * Menu Stucture - UPDATE `_db_core_user_role_permissions` (menu_id)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreUserRolePermissionsMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_user_role_permissions` SET menu_id='%s' WHERE menu_id='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_settings` (setting_value)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreSettingsMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_settings` SET setting_value='%s' WHERE setting_value='%s';
	";
}

/**
 * Menu Stucture - UPDATE `_db_core_tags` (core_tags)
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_updateCoreTagsMenuIdQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE `_db_core_tags` SET tagTarget='%s' WHERE tagTarget='%s';
	";
}

