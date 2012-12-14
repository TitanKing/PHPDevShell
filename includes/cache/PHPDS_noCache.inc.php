<?php

/**
 * Simple noCache class.
 */
class PHPDS_noCache
{
	/**
	 * Do the connection to the server.
	 *
	 */
	public function connectCacheServer($conf)
	{
		// Nothing to do here right now.
		return false;
	}

	/**
	 * Writes new data to cache.
	 *
	 * @param string $unique_key
	 * @param mixed $cache_data
	 * @param boolean $compress
	 * @param int $timeout
	 */
	public function cacheWrite($unique_key, $cache_data, $compress=false, $timeout=false)
	{
		// Nothing to do.
	}

	/**
	 * Return exising cache result to required item.
	 *
	 * @param mixed $unique_key
	 * @return mixed
	 */
	public function cacheRead($unique_key)
	{
		// Nothing to do.
	}

	/**
	 * Clear specific or all cache memory.
	 *
	 * @param mixed $unique_key
	 */
	public function cacheClear($unique_key = false)
	{
		// Nothing to do.
	}

	/**
	 * Checks if we have an empty cache container.
	 *
	 * @param mixed $unique_key
	 * @return boolean
	 */
	public function cacheEmpty($unique_key)
	{
		// Checks if cache session exisits.
		return true;
	}
}



