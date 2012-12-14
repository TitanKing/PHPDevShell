<?php


class exceptionExample extends PHPDS_controller
{
	function execute()
	{
		throw new PHPDS_exception('Intentional exception to showcase it.');
	}
}

return 'exceptionExample';
