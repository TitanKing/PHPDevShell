<?php

/**
 * Group Tree - Read group data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readNodesQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			 t1.node_id, t1.parent_node_id, t1.node_name, t1.node_link, t1.plugin, t1.node_type, t1.extend, t1.new_window, t1.rank, t1.hide, t1.template_id, t1.alias, t1.layout, t1.params,
			 t2.is_parent, t2.type
		FROM
			_db_core_node_items t1
		LEFT JOIN
			_db_core_node_structure t2
		ON
			t1.node_id = t2.node_id
		%s
		ORDER BY
			t2.id
		ASC
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
		$node_id = $parameters[0];

		// Do we need only a single node id item?
		if (! empty($node_id)) {
			$WHERE = " WHERE t1.node_id = '$node_id' ";
		} else {
			$WHERE = '';
		}

		$node = parent::invoke(array($WHERE));

		if (! empty($node)) {
			return $node;
		} else {
			return array();
		}
	}
}


