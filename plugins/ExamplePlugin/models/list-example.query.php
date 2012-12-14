<?php

/**
 * Example Queries, you can add as many as you wish.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_ExampleQuery1 extends PHPDS_query
{
	protected $sql = "
       SOME SQL...
    ";
}

/**
 * Example Queries, you can add as many as you wish.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_ExampleQuery2 extends PHPDS_query
{
	protected $sql = "
       SOME SQL...
    ";
}

/**
 * Lets get all example values from the selected database.
 * Pagination is easy too, look at any of the examples for where pagination is used for examples. To keep it simple we left it out of this example.
 *
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_readExampleQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			id, example_name, example_note, alias
		FROM
			_db_ExamplePlugin_example
    ";

	/**
	 * Initiate query invoke command.
	 * @param int
	 * @return array
	 */
	public function invoke($parameters = null)
	{

		// Initiate and call pagination plugin to give us access to all its features.
		$pagination = $this->factory('pagination');
		
		// Assign columns and th headings.
		$pagination->columns = array(
			_('Example ID') => 'id',
			_('Example Name') => 'example_name',
			_('Example Notes') => 'example_note',
			_('Example Alias') => 'alias',
			_('Edit Example') => '',
			_('Delete Example') => '');

		// Note that you can also search a column without adding it to the th header by adding
		// '' => 'some_column_name' this will make it also search in column some_column_name

		// Send query to pagination class, pagination class refactors the sql.
		$get_results = $pagination->query($this->sql);

		// Get different html parts for the pagination, this includes, pages, header and search form.
		$RESULTS['pagination'] = $pagination->navPages();
		$RESULTS['searchForm'] = $pagination->searchForm();
		$RESULTS['th'] = $pagination->th();

		// Set page to load when edit or delete is selected.
		$page_edit = $this->navigation->buildURLFromPath('manage-example.symlink', 'ExamplePlugin', 'id=');
		$page_delete = $this->navigation->buildURL(false, 'de=');

		// Simply loop through the results and build values.
		foreach ($get_results as $e) {
			$id = $e['id'];
			$example_name = $e['example_name'];
			$example_note = $e['example_note'];
			$alias = $e['alias'];

			$RESULTS['list'][] = array(
				'id' => $id,
				'example_name' => $example_name,
				'example_note' => $example_note,
				'alias' => $alias,
				'edit_example' => "<a href=\"{$page_edit}{$id}\" class=\"button\">{$this->template->icon('key--pencil', _('Edit Example'))}</a>",
				'delete_example' => "<a href=\"{$page_delete}{$id}\" {$this->core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $example_name))} class=\"button\">{$this->template->icon('key--minus', _('Delete Example'))}</a>"
			);
		}
		if (! empty($RESULTS['list'])) {
			return $RESULTS;
		} else {
			$RESULTS['list'] = array();
			return $RESULTS;
		}
	}
}