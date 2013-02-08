<?php

/**
 * Contains methods to handle pagination, searching and filtering.
 * PHPMailer could also be used.
 * @author Jason Schoeman
 */
class pagination extends PHPDS_dependant
{
	/**
	 * Contains filter variables.
	 * @var array
	 */
	public $filter = array();
	/**
	 * Contains Query
	 * @var string
	 */
	public $sql = '';
	/**
	 * Type of condition to be added when filter is applied. For instance, if a 'WHERE' already exist you will want to use 'AND' here.
	 * @var string
	 */
	public $condition = '';
	/**
	 * If a date column is provided, user will be able to search by dates.
	 * @todo Not finished.
	 * @var string
	 */
	public $dateColumn = '';
	/**
	 * Array of columns to be queried when looking for results.
	 * @var array
	 */
	public $columns = array();
	/**
	 * Add extra sql at end of query
	 * @var string
	 */
	public $extraCond = '';
	/**
	 * Split pages in this limit.
	 * @var int
	 */
	public $limitCount = 0;
	/**
	 * This simply allows you to disable the split 50 button links on pagingation.
	 * @var boolean
	 */
	public $showSplitLinks = true;
	/**
	 * When a user did not select an order this column will be used.
	 * @var string
	 */
	public $defaultOrderColumn = '';
	/**
	 * When a user did not select an order (asc|desc) order according to this.
	 * @var string
	 */
	public $defaultOrder = 'DESC';
	/**
	 * Total rows found.
	 * @var int
	 */
	protected $totalRows;
	/**
	 * Current page user is on.
	 * @var int
	 */
	protected $currentPage = 1;

	/**
	 * Replaces original query model to include pagination.
	 * @param string Actual SQL to be converted to pages.
	 * @return array
	 */
	public function query($sql)
	{
		// Get all parameters.
		$params = func_get_args();

		//array_shift($params); // first parameter of this function is query.
		$this->searchFilter();
		return $this->finalQuery($params);
	}

	/**
	 * Creates and stores filters to be used when searching or ordering.
	 *
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @return void
	 */
	public function searchFilter()
	{
		if (!empty($this->configuration['user_id'])) {
			// Lets query search filters from database first.
			$this->filter = $this->db->invokeQuery('PAGINATION_readFilterCacheQuery', $this->configuration['m'], $this->configuration['user_id']);
			// Default order.
			if (empty($this->filter['filter_by']) && !empty($this->defaultOrderColumn)) {
				$this->filter['filter_search'] = '';
				$this->filter['filter_by'] = $this->defaultOrderColumn;
				if (empty($this->filter['filter_order']))
					$this->filter['filter_order'] = $this->defaultOrder;
			}
		} else if (!empty($this->security->session['filter_search']) && empty($this->configuration['user_id'])) {
			$this->filter['filter_search'] = $this->security->session['filter_search'];
			$this->filter['filter_order'] = $this->security->session['filter_order'];
			$this->filter['filter_by'] = $this->security->session['filter_by'];
		}
		// Lets save the search string to the database.
		if (!empty($this->security->post['search']) || !empty($this->security->get['order'])) {
			// Set search filter string...
			if (!empty($this->security->post['search_field'])) {
				$this->filter['filter_search'] = $this->security->post['search_field'];
			} else if (isset($this->security->post['search_field'])) {
				$this->filter['filter_search'] = '';
			} else {
				$this->filter['filter_search'] = '';
			}
			// Set filter order...
			if (!empty($this->security->get['order'])) {
				$this->filter['filter_order'] = $this->security->get['order'];
			} else if (!empty($this->filter['filter_order'])) {
				// Just use stored filter order....
			} else {
				$this->filter['filter_order'] = '';
			}
			// Set filter by...
			if (!empty($this->security->get['by'])) {
				$this->filter['filter_by'] = $this->security->get['by'];
			} else if (!empty($this->filter['filter_by'])) {
				// Just use stored filter by....
			} else {
				$this->filter['filter_by'] = '';
			}
			// Default order.
			if (empty($this->filter['filter_by']) && !empty($this->defaultOrderColumn)) {
				$this->filter['filter_by'] = $this->defaultOrderColumn;
				if (empty($this->filter['filter_order']))
					$this->filter['filter_order'] = $this->defaultOrder;
			}
			// Define.
			if (empty($this->filter['search_id'])) $this->filter['search_id'] = false;
			// Lets update the database with the latest search filter.
			if ($this->configuration['user_id'] != 0) {
				$this->db->invokeQuery('PAGINATION_writeFilterCacheQuery', $this->filter['search_id'], $this->configuration['user_id'], $this->configuration['m'], $this->filter['filter_search'], $this->filter['filter_order'], $this->filter['filter_by'], 0);
			} else {
				$this->security->session['filter_search'] = $this->filter['filter_search'];
				$this->security->session['filter_order'] = $this->filter['filter_order'];
				$this->security->session['filter_by'] = $this->filter['filter_by'];
			}
		}
	}

	/**
	 * Combines filters and query together.
	 *
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @param Can receive as many parameter as possible...
	 * @return array
	 */
	public function finalQuery($params)
	{
		if (!empty($params[0])) {
			$this->sql = $params[0];
			array_shift($params);
		}
		if (empty($this->limitCount)) {
			// Get split results. ///////////////////////////////////////////////////////////////////////////////////////////////////////
			$settings = $this->db->essentialSettings;
			// Assign limit count. //////////////////////////////////////////////////////////////////////////////////////////////////////
			$this->limitCount = $settings['split_results'];
		}
		// This part returns the result inside the LIMIT command set by the user. ///////////////////////////////////////////////////
		$query_replace_prefix = $this->sql . "\n" . $this->filterQuery();

		// Get the p variable to select and calculate the LIMIT from where the database needs to select data from. //////////////////
		(isset($this->security->get['p']) && $this->security->get['p'] != false) ? $this->currentPage = $this->security->get['p'] : $this->currentPage = 1;
		// Simple calculation to see from what result the query needs to LIMIT. /////////////////////////////////////////////////////
		$from = (($this->currentPage - 1) * $this->limitCount);
		// Set limit property. //////////////////////////////////////////////////////////////////////////////////////////////////////
		$query_replace_prefix = $query_replace_prefix . "\n LIMIT $from, $this->limitCount ";
		// This here is the query where the LIMIT function is added. ////////////////////////////////////////////////////////////////
		$query = $this->db->invokeQuery('PAGINATION_readPaginationQuery', $query_replace_prefix, $params);

		// This part saves the total number of rows available without the LIMIT function effecting it. //////////////////////////////
		$this->totalRows = $this->db->invokeQuery('PAGINATION_readFounRowsQuery');

		// Return limit.
		return $query;
	}

	/**
	 * Checks if system should use where or and in condition.
	 *
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @return string
	 */
	public function queryCondition()
	{
		// Should we use where or and?
		if (empty($this->condition)) {
			if (preg_match("/where/i", $this->sql)) {
				$this->condition = "\n AND ";
			} else {
				$this->condition = "\n WHERE ";
			}
		}
	}

	/**
	 * Creates filter string to be included with query.
	 *
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @return string
	 */
	public function filterQuery()
	{
		// Set
		$search_columns = '';
		$th = '';
		$this->queryCondition();
		if (empty($this->filter['filter_search']))
			$this->filter['filter_search'] = '';
		if (empty($this->filter['filter_by']))
			$this->filter['filter_by'] = '';
		if (is_array($this->columns)) {
			// Create info and database query string.
			// Special wildcard replacement #***#.
			foreach ($this->columns as $name_key => $column) {
				if (!empty($column))
					$search_columns .= "\n $column LIKE '%%{$this->filter['filter_search']}%%' OR ";
			}
			// Glue and ready database string.
			if (strlen($this->filter['filter_search']) != 0) {
				// Remove last OR.
				$search_columns = $this->core->rightTrim($search_columns, ' OR ');
				// Glue final database string.
				$database_string = ' ' . $this->condition . ' (' . $search_columns . ') ';
			} else {
				// Return nothing if no $this->filter['filter_search'] value.
				$database_string = false;
			}
			// Set.
			$order_str = false;
			// Database order.
			if (!empty($this->filter['filter_by']) && ($this->filter['filter_order'] != 'asc' || $this->filter['filter_order'] != 'desc'))
				$order_by = "\n ORDER BY $order_str {$this->filter['filter_by']}\n {$this->filter['filter_order']} ";
		}
		// Define.
		if (empty($order_by)) $order_by = false;
		// Return database query string.
		return $database_string . $order_by . $this->extraCond;
	}

	/**
	 * Creates row heading for rows of tubular data. Also ads order options.
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @return string
	 */
	public function th()
	{
		$th = '';
		$asc = '';
		$desc = '';
		$mod = $this->template->mod;
		$nav = $this->navigation;

		if (empty($this->filter['filter_by']))
			$this->filter['filter_by'] = '';
		if (empty($this->filter['filter_order']))
			$this->filter['filter_order'] = '';
		if (!empty($this->columns)) {
			foreach ($this->columns as $th_ => $column) {
				if (!empty($th_)) {
					if ($column == $this->filter['filter_by']) {
						if ($this->filter['filter_order'] == 'desc') {
							$desc = 'selectedorder';
							$asc = '';
						} else if ($this->filter['filter_order'] == 'asc') {
							$asc = 'selectedorder';
							$desc = '';
						}
					} else {
						$asc = '';
						$desc = '';
					}
					// url
					if (!empty($column)) {
						$order_url = $nav->buildURL(false, "p=1&by={$column}");
                        $th .= $mod->paginationTh($th_, $order_url, $asc, $desc);
					} else {
						$sort_html = '';
                        $th .= $mod->paginationTh($th_);
					}
				}
			}
			return $th;
		}
		return false;
	}

	/**
	 * Creates HTML navigation pages to be used in templates.
	 *
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @return string
	 */
	public function navPages()
	{
		$mod = $this->template->mod;

		// Calculated the total number of pages. ////////////////////////////////////////////////////////////////////////////////////
		$total_pages = ceil($this->totalRows / $this->limitCount); ////////////////////////////////////////////////////////////////
		// END DATABASE SET /////////////////////////////////////////////////////////////////////////////////////////////////////////
		// Check to see if there is only 1 page, if so, give the end of results message.
		if ($total_pages <= 1) {
			// Show message if only 1 page exists.
			$SEARCH_RESULTS = $mod->noResults('');
		} else {
			// Other paging calculations.
			$previous_page = $this->currentPage - 1;
			$next_page = $this->currentPage + 1;
			// FAST-FORWARD and REWIND functionality.
			$ff_ = floor(($total_pages / 2) + ($this->currentPage / 2));
			$rw_ = floor($this->currentPage / 2);
			// Make sure FF is not empty.
			if (($ff_ != 0) && ($ff_ != $total_pages) && ($ff_ != $next_page) && ($ff_ != $this->currentPage)) {
				$ff = $mod->paginationNav($this->navigation->buildURL(false, "p=$ff_"), 'ff');
			} else {
				$ff = $mod->paginationNavEmpty('ff');
			}
			// Make sure RW is not empty.
			if (($rw_ != 0) && ($rw_ != 1) && ($rw_ != $previous_page) && ($rw_ != $this->currentPage)) {
				$rw = $mod->paginationNav($this->navigation->buildURL(false, "p=$rw_"), 'rw');
			} else {
				$rw = $mod->paginationNavEmpty('rw');
			}
			// URL control for [First].
			if ($this->currentPage == 1) {
				$first_page = $mod->paginationNavEmpty('first');
				$previous_page = $mod->paginationNavEmpty('previous');
			} else {
				$first_page = $mod->paginationNav($this->navigation->buildURL(false, "p=1"), 'first');
				$previous_page = $mod->paginationNav($this->navigation->buildURL(false, "p=$previous_page"), 'previous');
			}
			// URL control for [Last].
			if ($this->currentPage == $total_pages) {
				$last_page = $mod->PaginationNavEmpty('last');
				$next_page = $mod->paginationNavEmpty('next');
				// Current records.
				$current_records = $this->totalRows;
			} else {
				$last_page = $mod->PaginationNav($this->navigation->buildURL(false, "p=$total_pages"), 'last');
				$next_page = $mod->paginationNav($this->navigation->buildURL(false, "p=$next_page"), 'next');
				// Current records.
				$current_records = $this->currentPage * $this->limitCount;
			}

			// Show splitlinks.
			if ($this->showSplitLinks == false) {
				$rw = '';
				$ff = '';
			}

			// Compile result strings.
			$currentPage_ = $this->currentPage;
			$total_pages_ = $total_pages;
			$current_records_ = $current_records;
			$totalRows_ = $this->totalRows;
			// This part creates the links to the pages.
			$SEARCH_RESULTS = $mod->results(
							$first_page,
							$rw,
							$previous_page,
							$currentPage_,
							$total_pages_,
							$current_records_,
							$totalRows_,
							$next_page,
							$ff,
							$last_page);
		}
		// Check to see if pages links needs to be print out to the browser.
		return $SEARCH_RESULTS;
	}

	/**
	 * Creates search form to be used in templates for searching and navigating.
	 *
	 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
	 * @return string
	 */
	public function searchForm()
	{
		// Check if search field is active...
		(!empty($this->filter['filter_search'])) ? $class = 'active' : $class = '';
		// Create HTML form.
		return $this->template->mod->search(
				$this->navigation->selfUrl(),
				$this->filter['filter_search'],
				$class,
				$this->security->ValidatePost());
	}
}