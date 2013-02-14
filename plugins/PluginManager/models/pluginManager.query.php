<?php

/**
 * Plugin Manager - Get max rank for node items.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readMaxNodesRankQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			MAX(t1.rank)
		FROM
			_db_core_node_items t1
    ";

	protected $singleValue = true;
}

/**
 * Plugin Manager - Get min rank for node items.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readMinNodesRankQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			MIN(t1.rank)
		FROM
			_db_core_node_items t1
    ";

	protected $singleValue = true;
}

/**
 * Plugin Manager - Create template.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_createTemplateQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_templates (template_id, template_folder)
		VALUES
			('%s', '%s')
    ";
}

/**
 * Plugin Manager - Delete role permissions.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteRolePermissionsPluginQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_user_role_permissions
		WHERE
			node_id = '%s'
		AND
			user_role_id = %u
    ";
}

/**
 * Plugin Manager - Write role permissions.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeRolePermissionsPluginQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_role_permissions (user_role_id, node_id)
		VALUES
			(%u, '%s')
    ";
}

/**
 * Plugin Manager - Write new node.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeNodePluginQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_node_items (node_id, parent_node_id, node_name, node_link, plugin, node_type, extend, new_window, rank, hide, template_id, alias, layout, params)
		VALUES
			('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    ";
}


/**
 * Plugin Manager - Select class rank.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_rankClassesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			MAX(rank)
		FROM
			_db_core_plugin_classes
		WHERE
			class_name = '%s'
    ";

	protected $singleValue = true;
}

/**
 * Plugin Manager - Write classes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeClassesQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_plugin_classes (class_id, class_name, alias, plugin_folder, enable, rank)
		VALUES
			%s
    ";

	/**
	 * Initiate invoke query.
	 */
	public function invoke($parameters = null)
	{
		list($classes_array, $plugin_folder) = $parameters;

		// Check if settings exists.
		$class_db = '';
		// Loop through all settings.
		if (empty($classes_array)) $classes_array = array();
		foreach ($classes_array as $class_array) {
			// Assign setting as string.
			if (! empty($class_array['name']))
				$name = (string) $class_array['name'];
			if (! empty($class_array['alias']))
				$alias = (string) $class_array['alias'];
			if (! empty($class_array['plugin']))
				$plugin = (string) $class_array['plugin'];
			if (! empty($class_array['rank']))
				$rank = $class_array['rank'];

			if (empty($name)) $name = $plugin_folder;
			if (empty($plugin)) $plugin = $plugin_folder;
			if (empty($alias)) $alias = '';
			if (empty($rank) || $rank == 'last') {
				$max_rank = $this->db->invokeQuery('PHPDS_rankClassesQuery', $name);
				(empty($max_rank)) ? $rank = 1 : $rank = $max_rank + 1;
			}
			// Assign settings array.
			$class_db .= "('', '$name', '$alias', '$plugin', 1, '$rank'),";
		}
		// Remove last comma.
		$class_db = rtrim($class_db, ',');
		// We can now insert the classes.
		if (! empty($class_db)) {
			// Write new classes to database.
			parent::invoke(array($class_db));
		}
	}
}

/**
 * Plugin Manager - Do plugin query.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_doQuery extends PHPDS_query
{
	protected $sql = "%s";
}

/**
 * Plugin Manager - Write plugin version.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writePluginVersionQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_plugin_activation (plugin_folder, status, version, use_logo)
		VALUES
			('%s', '%s', '%s', '0')
	";
}

/**
 * Plugin Manager - Delete classes from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteClassesQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_plugin_classes
		WHERE
			plugin_folder = '%s'
	";
}

/**
 * Plugin Manager - Delete version from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_deleteVersionQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_plugin_activation
		WHERE
			plugin_folder = '%s'
	";
}

/**
 * Plugin Manager - Upgrade version from database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_upgradeVersionQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_plugin_activation
		SET
			status        = '%s',
			version       = '%s'
		WHERE
			plugin_folder = '%s'
	";
}

/**
 * Plugin Manager - Unset all used logos.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_unsetLogoQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_plugin_activation
		SET
			use_logo = 0
	";
}

/**
 * Plugin Manager - Set default logo.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_setDefaultLogoQuery extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_plugin_activation
		SET
			use_logo = 1
		WHERE
			plugin_folder = '%s'
	";
}

/**
 * Plugin Manager - Does node actually exist.
 */
class PHPDS_doesNodeExist extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id
		FROM
			_db_core_node_items t1
		WHERE
			t1.node_id = '%s'
	";

	protected $singleValue = true;
}

/**
 * Plugin Manager - Update node link.
 */
class PHPDS_updateNodeLink extends PHPDS_query
{
	protected $sql = "
		UPDATE
			_db_core_node_items
		SET
			node_link = '%s'
		WHERE
			node_id = '%s'
	";
}

