<?php

/**
 * General Settings - Read default template id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_defaultTemplateIdQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			template_id
		FROM
			_db_core_templates
		WHERE
			template_folder = '%s'
    ";
	protected $singleValue = true;
}

/**
 * General Settings - Read empty template id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_emptyTemplateIdQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			template_id
		FROM
			_db_core_templates
		WHERE
			template_folder = 'empty'
    ";
	protected $singleValue = true;
}

/**
 * General Settings - Write empty template id.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_writeEmptyTemplateIdQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_templates (template_id, template_folder)
		VALUES
			('1757887940', 'empty')
    ";
}

/**
 * General Settings - Get users for certain group.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_getUserDbQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, user_name, user_display_name
		FROM
			_db_core_users
		WHERE
			user_group = %u
    ";
}

/**
 * General Settings - Get users roles.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_userRolesDbQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_role_id, t1.user_role_name
		FROM
			_db_core_user_roles t1
		ORDER BY
			t1.user_role_id
		ASC
    ";
}

/**
 * General Settings - Get users groups.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_userGroupsDbQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_group_id, t1.user_group_name
		FROM
			_db_core_user_groups t1
		ORDER BY
			t1.user_group_id
		ASC
    ";
}

/**
 * General Settings - Get all nodes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_nodeDbQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.node_id, t1.node_name, t1.node_link
		FROM
			_db_core_node_items t1
		LEFT JOIN
			_db_core_node_structure t2
		ON
			t1.node_id = t2.node_id
		ORDER BY
			t2.id
    ";
}

/**
 * General Settings - Get all templates.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_selectTemplateQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			template_id, template_folder
		FROM
			_db_core_templates
		ORDER BY
			template_id
    ";
}

