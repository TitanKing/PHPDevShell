<?php
class CRUD_writeMultipleOptions extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_%s (%s, %s)
		VALUES %s
	";

	public function invoke($parameters = null)
	{
		list($val, $join_id_col, $join_id, $value_col, $array) = $parameters;
		$this->db->deleteQuick('_db_' . $val, $join_id_col, $join_id);
		if (! empty($array)) {
			$rows = '';
			foreach ($array as $value) {
				$rows .= "($join_id, $value),";
			}
			if (! empty($rows)) {
				$rows = rtrim($rows, ",");
				parent::invoke(array($val, $join_id_col, $value_col, $rows));
			}
		}
	}	
}

class CRUD_readMultipleOptions extends PHPDS_query
{
	protected $sql = "
		SELECT %s FROM
			_db_%s
		WHERE
			%s = '%s'
	";
}	

