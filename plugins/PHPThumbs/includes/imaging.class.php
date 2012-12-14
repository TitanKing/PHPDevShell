<?php

/**
 * Contains methods to handle images.
 * @author Jason Schoeman
 */
class imaging extends PHPDS_dependant
{

	public function create($file_location, $options=array())
	{
		try {
			require_once 'plugins/PHPThumbs/resources/ThumbLib.inc.php';
			return PhpThumbFactory::create($file_location, $options);
		} catch (Exception $e) {
			throw new PHPDS_exception($e->getMessage());
		}
	}
}