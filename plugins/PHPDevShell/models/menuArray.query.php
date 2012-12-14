<?php

/**
 * Group Tree - Read group data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PHPDS_readMenusQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			 t1.menu_id, t1.parent_menu_id, t1.menu_name, t1.menu_link, t1.plugin, t1.menu_type, t1.extend, t1.new_window, t1.rank, t1.hide, t1.template_id, t1.alias, t1.layout, t1.params,
			 t2.is_parent, t2.type
		FROM
			_db_core_menu_items t1
		LEFT JOIN
			_db_core_menu_structure t2
		ON
			t1.menu_id = t2.menu_id
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
		$menu_id = $parameters[0];
		
		// Do we need only a single menu id item?
		if (! empty($menu_id)) {
			$WHERE = " WHERE t1.menu_id = '$menu_id' ";
		} else {
			$WHERE = '';
		}

		$menu = parent::invoke(array($WHERE));

		if (! empty($menu)) {
			return $menu;
		} else {
			return array();
		}
	}
}


