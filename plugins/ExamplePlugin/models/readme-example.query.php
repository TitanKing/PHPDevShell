<?php

/**
 * PluginExample - Show some data call.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class ExamplePlugin_someData extends PHPDS_query
{

	/**
	 * Override invoke
	 * @return string
	 */
	public function invoke($parameters = null)
	{
		return 'When working with MVC, you have a Model, which in our case is any form of data, you have a view, which is the template and the html side of things, then you have the controller that has some logic bringing the Model and the View together.';
	}
}