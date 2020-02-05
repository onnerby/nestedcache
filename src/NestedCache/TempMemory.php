<?php
namespace Doe\NestedCache
{
	class TempMemory extends Base
	{
		private $cache = [];

		protected function getCache($cachePath)
		{
			if (isset($this->cache[$key])) {
				return $this->cache[$key];
			}
			return null;
		}

		protected function setCache($key, $cache)
		{
			$this->cache[$key] = $cache;
		}

		protected function unsetCache($key)
		{
			unset($this->cache[$key]);
		}

		protected function incrementCache($key)
		{
			if (!isset($this->cache[$key])) {
				$this->cache[$key] = 0;
			}
			return ++$this->cache[$key];
		}
	}
}
