<?php

/**
 * CONTROLLER: Simple readme to introduce MVC in PHPDevShells plugins.
 *
 * @author Jason Schoeman
 * @return string
 */
class ReadMeExample extends PHPDS_controller
{

	/**
	 * We always start by overriding the execute method for the controller.
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// a Default heading.
		$this->template->heading(_('Oooh an Example Plugin'));
		// Some info regarding this node.
		$this->template->info(_('The best way to learn is to learn by example.'));
		/////////////////

		// invokeQuery is the model of PHPDevShell.
		// Each query is in its own class.
		// There can be a model for each controller. So the model will be under ExamplePlugin/models/readme-example.query.php
		$some_data_call = $this->db->invokeQuery('ExamplePlugin_someData');

		// Testing Notification Boxes.
		// PHPDevShell offers easy and simple configuration and log methods.
		// $this->template provides you with a vast number of methods to use.
		$error = $this->template->error('This is a sample error message, this can be written in log. ', 'return', 'nolog');
		$warning = $this->template->warning('This is a sample warning message, this can be written in log.', 'return', 'nolog');
		$critical = $this->template->critical('This is a sample critical message, this can be written in log and mailed.', 'return', 'nolog', 'nomail');
		$ok = $this->template->ok('This is a sample ok message, this can be written in log.', 'return', 'nolog');
		$notice = $this->template->notice('This is a sample notice message...', 'return');
		$busy = $this->template->busy('This is a sample busy message...', 'return');
		$message = $this->template->message('This is a sample message...', 'return');
		$note = $this->template->note('This is a sample note message...', 'return');
		$scripthead = $this->template->scripthead('Script Heading', 'return');

		// I want to display this in nice big letters, so lets send some style to the header of our template.
		$this->template->addCSSToHead('.bigger { font-size: 3em; line-height: 1; margin-bottom: 0.5em; } .big { font-size: 2em; margin-bottom: 0.75em; }');
		// You can really send anything from anywhere to the <head> of your template, look at these methods in API docs.
		// addCssFileToHead, addJsFileToHead, addToHead, addJsToHead, addCSSToHead

		// This is how you load a plugin, in this case we have registered 'views' plugin class and calling it.
		// This plugin is using Smarty to split HTML from code.
		$view = $this->factory('views');

		// We can also pass values to the readme-example.view.php class.
		$this->set('foo', 'Foo Bar');
		$this->set('other', array("Hello", "World"));

		// We now set some variables to the VIEW of PHPDevShell, this splits the Code and HTML.
		$view->set('error', $error);
		$view->set('warning', $warning);
		$view->set('critical', $critical);
		$view->set('ok', $ok);
		$view->set('notice', $notice);
		$view->set('busy', $busy);
		$view->set('message', $message);
		$view->set('note', $note);
		$view->set('scripthead', $scripthead);
		$view->set('urlbutton', "<a href=# class=button>{$this->template->icon('tick', _('a Image with a link.'))}</a>");
		$view->set('img1', $this->template->icon('alarm-clock', _('Image Example 1')));
		$view->set('img2', $this->template->icon('calendar-share', _('Image Example 2')));
		$view->set('img3', $this->template->icon('hammer--plus', _('Image Example 3')));
		$view->set('img4', $this->template->icon('truck--pencil', _('Image Example 4')));
		$view->set('smile', $this->template->icon('smiley-grin', _('Smile')));

		// This is how you use the helper class to reuse code.
		$helper = $this->factory('supportExample');
		$view->set('reused_htmlcode', $helper->someReusedMethodwithHTML());
		$view->set('reused_codewithmodel', $helper->someReusedModel());

		// The global array $this->configuration contains loads and loads of data you use to manage current user. Use print_r($this->configuration) to see what these are.
		$view->set('user_id', $this->configuration['user_id']);
		$view->set('username', $this->configuration['user_name']);
		$view->set('group', $this->configuration['user_group']);
		$view->set('role', $this->configuration['user_role']);
		$view->set('email', $this->configuration['user_email']);

		// $this->configuration is an array that contains a whole lot of system data, check it out.
		$view->set('mvc_exmplained', $some_data_call);
		$view->set('developers_name', $this->configuration['user_display_name']);

		// And many more, look at System Info to get a list of available session data.
		// I can use a simple method to call a menu item I want.
		$info_url = $this->navigation->buildURLFromPath('system-admin/admin.php', 'PHPDevShell');
		$view->set('info_url', $info_url);

		// Looking at array $this->navigation->navigation we can find anything about current users access to menus.
		$alias = $this->navigation->navigation[$this->configuration['m']]['alias'];
		$view->set('alias', $alias);

		// Looking at some $this->user and $this->tagger classes.
		// Locate all the groups this user has access to.
		$access_to_groups = $this->user->getGroups();
		$view->set('access_to_groups', $access_to_groups);

		// Looking at a simple $this->db method.
		// Lets get sample ExamplePlugin settings that we added during the install.
		$setting = $this->db->getSettings(array('sampleSetting1', 'sampleSetting2'), 'ExamplePlugin');
		$view->set('setting', $setting);

		// Continue to next example.
		$example2 = $this->navigation->buildURLFromPath('manage-example.php', 'ExamplePlugin');
		$view->set('example2', $example2);

		// Output View.
		// The view also has the same name as the controller, you can find the view in ExamplePlugin/views/readme-example.tpl
		$view->show();
	}
}

return 'ReadMeExample';