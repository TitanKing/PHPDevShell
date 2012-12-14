<?php

/**
 * Controller Class: New example and edit example.
 * Like always we start our node with the controller.
 * @author Jason Schoeman
 * @return string
 */
class manageExample extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Load CRUD to make form validation slightly easier...
		/* @var $crud crud */
		$crud = $this->factory('crud');

		// Do we have an id, this we check if url contains an &id=12 for editing for instance.
		if ($crud->REQUEST('id'))
			$crud->f->id = $crud->REQUEST('id');

		// Lets create header information.
		$this->template->heading(_('Example Simple Form'));
		$this->template->info(_('On this page we will show you how to do a simple form and save data to the database using Models. We will use <strong>Models</strong> instead of <strong>ORM</strong> here. There are other examples of easier forms using ORM.'));

		// If item gets posted, lets read it, validate it and save it to the database.
		if ($crud->POST('save')) {

			// For instance, name must be Alpha letters only, else write to fail log with message.
			if (!$crud->isAlpha('example_name'))
				$crud->error("This field cannot be empty and may not contain spaces");

			// For instance, must meet minimum lenght of 20 characters.
			if (!$crud->isMinLength('example_note', 20))
				$crud->error("This is too short, it needs to be at least 100 characters");

			// All must be lower case.
			if (!$crud->isLower('alias'))
				$crud->error("Alias can only be lower case letters");

			// Multiple options require a seperate table to store values, lets treat this now.
			if (!$crud->isMultipleOption('example_multi_select_crud'))
				$crud->error("Please pick at least one option");

			// If everything is ok and we have no errors written in $crud->error we can continue.
			if ($crud->ok()) {
				$crud->f->id = $this->db->invokeQuery('ExamplePlugin_writeExampleQuery', $crud->f->id, $crud->f->example_name, $crud->f->example_note, $crud->f->alias);
				// Show item updated.
				$this->template->ok(sprintf(_('Nice, %s was saved to the database.'), $crud->f->example_name));
			} else {
				$this->template->notice('Form was not saved due to errors.');
				// We can now show the errors and mark all fields automaticall... so simple, so clean, so much fun.
				$crud->errorShow();
			}
		}

		// Edit example and load data for example.
		// This is when we have an id var in the url and we want to show and edit the data.
		if ($crud->GET('id'))
			// The import fields is a nice way to write the results of the query array to the $crud tables $crud->f fields.
			$crud->importFields($this->db->invokeQuery('ExamplePlugin_editExampleQuery', $crud->GET('id')));

		// Load views plugin.
		$view = $this->factory('views');

		// Just some example options.
		$options = array(1=>'Option 1', 2=>'Option 2', 3=>'Option 3');

		// Set Values for Views variables.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('id', $crud->f->id);
		$view->set('example_name', $crud->f->example_name);
		$view->set('example_note', $crud->f->example_note);

		// For multiple option we can take this a step further...
		$view->set('example_multi_select_crud', $crud->select($options, $crud->multiSelected('example_multi_select_crud', $crud->f->id)));

		$view->set('alias', $crud->f->alias);

		// Output to view.
		$view->show();
	}
}

return 'manageExample';