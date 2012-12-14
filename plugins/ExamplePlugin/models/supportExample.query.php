<?php

/**
 * This model will just show some record for example purposes.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_someExampleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			example_name
		FROM
			_db_ExamplePlugin_example
    ";
	protected $singleValue = true;
}

