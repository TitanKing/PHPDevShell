<?php
// In CRUD example we will show you how to use the CRUD system.

class crudExample extends PHPDS_controller
{
	public function execute()
	{
		$this->template->heading('CRUD Example');

		// Call ORM plugin.
		$this->factory('orm');
		
		// Will store data in table called crud_example
		// Note, you dont even have to create the table, this will be done automatically :)
		$orm = R::dispense('crud_example');
		
		// Call CRUD plugin.
		$crud = $this->factory('crud', $orm);
		
		// Is this an update or a new submission, if update we need to provide id.
		if ($crud->REQUEST('id'))
			$orm->id = $crud->REQUEST('id');
		if ($crud->POST('new'))
			$orm->id = null;

		// CREATE:
		// Here we receive new data being posted from the form.
		if ($crud->POST('submit_example')) {
			// Is means, it cant be empty, it must contain anything but empty.
			if (!$crud->isAlpha('example_name')) 
				$crud->error("This field cannot be empty, enter your name.");
			
			if (!$crud->isEmail('example_email')) 
				$crud->error("You need to provide an email here... pls, do you know what an email address looks like idiot? Bloody end users are stupid.");
			
			if (!$crud->isUrl('example_url')) 
				$crud->error("Please provide a website url here.");
			
			if (!$crud->isMinLength('example_note', 20)) 
				$crud->error("This is too short, it needs to be at least 100 characters.");
			
			if (!$crud->isLower('example_alias')) 
				$crud->error("Alias can only be lower case letters.");
			
			if (!$crud->is('example_select'))
				$crud->error("Pick dropdown selection.");
			
			if (!$crud->is('example_radio')) 
				$crud->error("Pick a radio button option.");
			
			$cb1 = $crud->is('example_checkbox1');
			$cb2 = $crud->is('example_checkbox2');
			
			if (!$cb1 && !$cb2)
				$crud->error("Select at least 1 checkbox.", 'example_checkbox2');
			
			// Multiple options require a seperate table to store values, lets treat this now.			
			if (!$crud->isMultipleOption('example_multi_select'))
				$crud->error("Please pick at least one option.");
			
			// There are about 54 different types of validations available in this CRUD :)
			
			// Now we save to database...
			if ($crud->ok()) {
				R::store($orm);
				// Below we indicate success and also log it to our logging system for safekeeping.
				$this->template->ok("Great, {$orm->example_name} was saved to database!");
			} else {
				$this->template->notice('Form was not saved due to errors.');
				// We can now show the errors and mark all fields automaticall... so simple, so clean, so much fun.
				$crud->errorShow();
			}	
		}
		
		// DELETE:
		// Simple, here we can delete a bean quickly when a request for a delete is passed.
		if ($crud->GET('delete')) {
			$orm->id = $crud->GET('delete');
			$this->template->notice("Great, {$orm->id} was deleted from database!");
			R::trash($orm);
		}
		
		// EDIT:
		// Here we can now load the results of a form that has been completed so it can be edited.
		if ($crud->GET('edit'))
			$orm = R::load('crud_example', $crud->GET('edit'));
		
		// LIST:
		// Lets do one more thing, lets list 5 entries for easy edit and delete.
		$listall = R::find('crud_example');
		
		// Load views plugin.
		$view = $this->factory('views');
		
		// Set Values.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('id', $orm->id);
		$view->set('example_name', $orm->example_name);
		$view->set('example_email', $orm->example_email);
		$view->set('example_url', $orm->example_url);
		$view->set('example_note', $orm->example_note);
		$view->set('example_alias', $orm->example_alias);
		
		// Just some example options.
		$options = array(1=>'Option 1', 2=>'Option 2', 3=>'Option 3');
		
		// Handling options are easy...
		$view->set('example_select', $crud->select($options, array($orm->example_select)));
		$view->set('example_checkbox1', $crud->checkbox('example_checkbox1',  array(1=>'Checkbox 1'), array($orm->example_checkbox1)));
		$view->set('example_checkbox2', $crud->checkbox('example_checkbox2',  array(1=>'Checkbox 2'), array($orm->example_checkbox2)));
		$view->set('example_radio', $crud->radio('example_radio', $options, array($orm->example_radio)));
		
		// For multiple option we can take this a step further...
		$view->set('example_multi_select', $crud->select($options, $crud->multiSelected('example_multi_select', $orm->id))); //  $crud->multiSelected can also be replaced with an array of selected items.
		
		// List posted results.
		$view->set('results', $listall);

		// Output to view.
		$view->show();
	}
}

return 'crudExample';

