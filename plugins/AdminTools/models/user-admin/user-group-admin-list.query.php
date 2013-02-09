<?php

/**
 * User Group Admin List - Update user group data from deleted group.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_updateDeletedGroupUsersQuery extends PHPDS_query
{
	protected $sql = "
        UPDATE
            _db_core_users
        SET
            user_group = false
        WHERE
            user_group = %u
    ";
}

/**
 * User Group Admin List - Read group list.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_readGroupQuery extends PHPDS_query
{

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		// Compile and list group tree.
        /* @var $group groupTree */
		$group = $this->factory('groupTree');
		$group->compileResults(true, false, $this->user->setGroupQuery("WHERE user_group_id IN ({$this->user->getGroups()})"));
		return $group->RESULTS;
	}
}