<?php
namespace Doe\NestedCache
{
	class APCu extends Base
	{
		private $ttl = 0;

		public function __construct($options)
		{
			parent::__construct($options);
			$this->ttl = $this->options['ttl'] ?? 0;
		}

		protected function getCache($key)
		{
			$cache = \apcu_fetch($key, $success);
			if ($success) {
				return $cache;
			}
			return null;
		}


		protected function setCache($key, $cache)
		{
			if (!\apcu_add($key, $cache, $this->ttl)) {
				\apcu_store($key, $cache, $this->ttl);
			}
		}

		protected function unsetCache($key)
		{
			\apcu_delete($key);
		}

		protected function incrementCache($key)
		{
			return \apcu_inc($key);
		}
	}
}
