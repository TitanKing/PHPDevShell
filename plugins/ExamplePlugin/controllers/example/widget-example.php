<?php

class WidgetExample extends PHPDS_controller
{

	/**
	 * We always start by overriding the execute method for the controller.
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// a Default heading.
		$this->template->heading(_('I wanna add my widget!'));

		// Some info regarding this node.
		$this->template->info(_('So you just finished one of your widgets and you want to add it to your page, and some users should be able to see it.'));
		$this->template->note(_('Remember, an ajax node type must also have the correct permissions else it wont show.'));
		/////////////////

		// This is how you load a plugin, in this case we have registered 'views' plugin class and calling it.
		// This plugin is using Smarty to split HTML from code.
		$view = $this->factory('views');


		// Continue to next example.
		$this->template->requestWidget('2282118247', 'widget1', 'data=Hello World&moredata=Foobar');

		// If you are lazy and dont want to locate menus permanent id, locate it from path with.
		$this->template->requestWidget('1337263253', 'widget2', 'more_about=More about Linux...');

		// Ajax as main page example.
		$this->template->requestAjax('1821693117', 'ajax1', 'data=FooBar');

		// Again you can call multiple lightbox pages...
		$lightboxurl = $this->template->requestLightbox('1133107805', 'lightbox', 'data=Lightbox Foobar');

		$view->set('lightboxurl', $lightboxurl);

		// Output View.
		// The view also has the same name as the controller, you can find the view in ExamplePlugin/views/readme-example.tpl
		$view->show();
	}
}

return 'WidgetExample';