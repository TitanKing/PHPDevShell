<?php

/**
 * Sometimes you have some repetive tasks you need do over and over again.
 * This is where support classes and files comes in, you just call them from wherever and reuse its methods.
 * Support classes can have their own models.
 *
 * @author Jason Schoeman
 */
class supportExample extends PHPDS_dependant
{
	/**
	 * The cool thing about supporting/helper classes is the ability to reuse code over and over again,
	 * PHPDevShell offers and extemely easy way to do this.
	 * Extend with PHPDS_dependant to share core object from PHPDevShell.
	 */

	/**
	 * Properties are used like normal.
	 * @var string
	 */
	public $somevar = 'Hello World';

	/**
	 * This just shows that you can reuse html too, this is ethically correct.
	 * @return string
	 */
	public function someReusedMethodwithHTML()
	{
		// Well what do you know, we have access to all PHPDevShell object.
		$name = $this->configuration['user_display_name'];
		$date = $this->core->formatTimeDate(time());

		// Obviously this method can be extended, but lets keep it simple.
		return sprintf('
			<span style="font-size: 2.2em">
				Hi %s, as you can see, this can now be reused. Todays date is %s.
			</span>', $name, $date);
	}

	/**
	 * Support classes can have their own models. Models will be looked for in the models folder,
	 * with relation to the class file name, in this case it will be models/supportExample.query.php
	 * @return string
	 */
	public function someReusedModel()
	{
		// Obviously this method can be extended, but lets keep it simple.
		// Calling a model from within a support class.
		return $this->db->invokeQuery('ExamplePlugin_someExampleQuery');
	}
}
