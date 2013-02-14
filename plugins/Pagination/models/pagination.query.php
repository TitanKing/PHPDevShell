<?php

/**
 * Calls filters per page regarding search.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PAGINATION_readFilterCacheQuery extends PHPDS_query
{
    protected $sql = "
        SELECT
            t1.search_id, t1.filter_search, t1.filter_order, t1.filter_by, t1.exact_match
        FROM
            _db_core_filter t1
        WHERE
            t1.node_id = '%s'
        AND
            t1.user_id = %u
        LIMIT
            0,1
    ";

    protected $singleRow = true;
}

/**
 * Replaces current search filters.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PAGINATION_writeFilterCacheQuery extends PHPDS_query
{
    protected $sql = "
        REPLACE INTO
            _db_core_filter
        VALUES
            (%u, %u, '%s', '%s', '%s', '%s', %u)
    ";
}

/**
 * Master query for pagination.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PAGINATION_readPaginationQuery extends PHPDS_query
{
    protected $sql = '';
    protected $keyField = '';

    /**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{
        // Get actual query.
		$this->sql = preg_replace("/select/i", 'SELECT SQL_CALC_FOUND_ROWS', $parameters[0], 1);

        if (! empty($parameters[1])) {
			$param = $parameters[1];
		} else {
			$param = array();
		}
        $results = parent::invoke($param);
		if (! empty($results)) {
			return $results;
		} else {
			return array ();
		}
    }
}

/**
 * Supportive class to count results.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 */
class PAGINATION_readFounRowsQuery extends PHPDS_query
{
    protected $sql = 'SELECT FOUND_ROWS()';

    protected $singleValue = true;
}