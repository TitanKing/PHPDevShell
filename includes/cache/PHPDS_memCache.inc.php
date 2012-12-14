<?php

/**
 * Class to handle memcached api.
 */
class PHPDS_memCache
{
	public $cacheObject;
	public $timeout;
	/**
	 * Does the connection to the memcache server.
	 * Currently memcache is the primary supported engine.
	 *
	 * @param array $conf
	 */
	public function connectCacheServer($conf)
	{
		// Assign configuration options.
		$this->timeout = $conf['cache_refresh_intervals'];

		if (! empty($conf['cache_host'])) {
			// Create object.
			$this->cacheObject = new Memcache;
			// Loop and create servers.
			foreach ($conf['cache_host'] as $server => $host) {
				$this->cacheObject->addserver($host, $conf['cache_port'][$server], $conf['cache_persistent'][$server], $conf['cache_weight'][$server], $conf['cache_timeout'][$server], $conf['cache_retry_interval'][$server], $conf['cache_status'][$server]);
			}
		} else {
			return false;
		}
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
		// Check if we have custom timeout.
		if ($timeout === false)
			$timeout = $this->timeout;
		
		if ($compress === true) {
			$this->cacheObject->setCompressThreshold(20000, 0.2);
			$compress = false;
		}

		// Replace cache else set it.
		if ($this->cacheObject->replace($unique_key, $cache_data, $compress, $timeout) == false) {
			// Set cache.
			$this->cacheObject->set($unique_key, $cache_data, $compress, $timeout);
			$this->cacheObject->set('time__' . $unique_key, time(), false, $timeout);
		} else {
			$this->cacheObject->replace('time__' . $unique_key, time(), false, $timeout);
		}
	}

	/**
	 * Return exising cache result for required key.
	 * @param mixed $unique_key
	 * @return mixed
	 */
	public function cacheRead($unique_key)
	{
		// Check caching type.
		return $this->cacheObject->get($unique_key);
	}

	/**
	 * Clear specific or all cache memory.
	 * @param mixed $unique_key
	 */
	public function cacheClear($unique_key = false)
	{
		if (empty($unique_key)) {
			// Flush memcache.
			$this->cacheObject->flush();
		} else {
			$this->cacheObject->delete($unique_key);
		}
	}

	/**
	 * Checks if we have an empty cache container.
	 * @param mixed $unique_key
	 * @return boolean
	 */
	public function cacheEmpty($unique_key)
	{
		if ($this->cacheObject->get($unique_key)) {
			// Check if session data should be reset.
			if ((time() - $this->cacheObject->get('time__' . $unique_key)) > $this->timeout) {
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













