<?php

/**
 * Simple session cached based class.
 */
class PHPDS_sessionCache
{
	public $cacheObject;
	public $timeout;

	/**
	 * No real connection required if session cache is used.
	 */
	public function connectCacheServer($conf)
	{
		// Assign configuration options.
		$this->timeout = $conf['cache_refresh_intervals'];

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
		// Write newly set data time.
		$_SESSION['cache_write'][$unique_key] = time();
		// Check if cache data is empty.
		if (empty($cache_data)) $cache_data = false;
		// Check cache time.
		$_SESSION['cache'][$unique_key] = $cache_data;
	}

	/**
	 * Return exising cache result to required item.
	 *
	 * @param mixed $unique_key
	 * @return mixed
	 */
	public function cacheRead($unique_key)
	{
		// Return existing cache results.
		$cache = $_SESSION['cache'][$unique_key];
		return $cache;
	}

	/**
	 * Clear specific or all cache memory.
	 *
	 * @param mixed $unique_key
	 */
	public function cacheClear($unique_key = false)
	{
		// Clear only a specific cache item.
		if (!empty($unique_key)) {
			unset($_SESSION['cache'][$unique_key], $_SESSION['cache_write'][$unique_key]);
		} else {
			unset($_SESSION['cache'], $_SESSION['cache_write']);
		}
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
		if (isset($_SESSION['cache'][$unique_key])) {
			// Check if session data should be reset.
			if ((time() - $_SESSION['cache_write'][$unique_key]) > $this->timeout) {
				// We need to rewrite cache.
				return true;
			} else {
				// Cache is not empty.
				return false;
			}
		} else {
			// Cache is empty.
			return true;
		}
	}
}



