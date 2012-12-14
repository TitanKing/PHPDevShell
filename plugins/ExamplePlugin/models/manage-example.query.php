<?php

/**
 * This model will save new data to database.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_writeExampleQuery extends PHPDS_query
{
	protected $sql = "
		REPLACE INTO
			_db_ExamplePlugin_example (id, example_name, example_note, alias)
		VALUES
			(%u, '%s', '%s', '%s')
    ";
	protected $returnId = true;
}

/**
 * This model will show data when we are editing.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_editExampleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			id, example_name, example_note, alias
		FROM
			_db_ExamplePlugin_example
		WHERE
			id = %u
    ";
	protected $singleRow = true;
}
