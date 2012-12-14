<?php

/**
 * This model will just show some record for example purposes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_menuAjaxQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			menu_name
		FROM
			_db_core_menu_items
		WHERE
			menu_name LIKE '%%%s%%'
    ";
}

