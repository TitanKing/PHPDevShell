<?php

/**
 * User Group Admin - Update Group
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeGroupQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_groups (user_group_id, user_group_name, user_group_note, parent_group_id, alias)
		VALUES
			(%u, '%s', '%s', '%s', '%s')
    ";
	protected $returnId = true;
}

/**
 * User Group Admin - Update Extra Group
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_writeExtraGroupQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_core_user_extra_groups (user_id, user_group_id)
		VALUES
			(%u, %u)
    ";
	protected $returnId = true;
}

/**
 * User Group Admin - Read Group
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readGroupQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_group_id, user_group_name, user_group_note, parent_group_id, alias
		FROM
			_db_core_user_groups
		WHERE
			user_group_id = %u
    ";
	protected $singleRow = true;
}

/**
 * User Group Admin - Read Group
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readParentGroupQuery extends PHPDS_query
{
	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		list($parent_group_id, $user_group_id) = $parameters;
		$parent_group_option = '';
		// Compile and list group tree.
		$group = $this->factory('groupTree');
		// Last but not least compile needed results.
		$group->compileResults(false, true, $this->user->setGroupQuery("WHERE user_group_id IN ({$this->user->getGroups()}) AND user_group_id != '$user_group_id'", "WHERE user_group_id != '$user_group_id'"));
		$parent_user_group_array = $group->groupArray;
		// Loop and see what groups needs to be selected and listed as parents.
		foreach ($parent_user_group_array as $user_group_id_ => $user_group_name_) {
			// Check Selected
			($parent_group_id == $user_group_id_) ? $parent_group_select = 'selected' : $parent_group_select = false;
			// Create parent options.
			$parent_group_option .= '<option value="' . $user_group_id_ . '" ' . $parent_group_select . '>' . $user_group_name_ . "&nbsp;($user_group_id_)</option>";
		}

		return $parent_group_option;
	}
}
