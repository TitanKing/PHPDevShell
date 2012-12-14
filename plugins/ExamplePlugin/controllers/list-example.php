<?php

/**
 * Controller Class: List example.
 * Like always we start our node with the controller.
 * @author Jason Schoeman
 * @return string
 */
class listExample extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Header information
		$this->template->heading(_('Simple example to list items in a database.'));
		$this->template->info(_('You can always use the info heading to provide some info regarding current item.'));

		// Load CRUD to make form validation slightly easier...
		$crud = $this->factory('crud');

		// Should we delete group?
		if ($crud->GET('de')) {
			// Lets delete the item.
			$example_deleted = $this->db->deleteQuick('_db_ExamplePlugin_example', 'id', $crud->GET('de'), 'example_name');

			if ($example_deleted) {
				$this->template->ok(sprintf(_('Example %s was deleted.'), $example_deleted));
			} else {
				$this->template->warning(sprintf(_('No example "%s" to delete.'), $crud->GET('de')));
			}
		}

		// Lets pass the array to the view so he can loop and output the results.
		$RESULTS = $this->db->invokeQuery('ExamplePlugin_readExampleQuery');

		// Load views plugin.
		$view = $this->factory('views');

		// Assign different parts of our pagination to different variables.
		$view->set('pagination', $RESULTS['pagination']);
		$view->set('searchForm', $RESULTS['searchForm']);
		$view->set('th', $RESULTS['th']);
		$view->set('RESULTS', $RESULTS['list']);

		// Output to views.
		$view->show();
	}
}

return 'listExample';