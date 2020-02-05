<?php
namespace Doe\NestedCache
{
	class Base
	{
		protected $options = [];
		private $prefix = '';
		protected $stack = [];

		/**
		 * Constructor
		 */
		public function __construct($options)
		{
			$this->options = $options;
			$this->prefix = isset($this->options['prefix']) ? $this->options['prefix'] . '::' : '';
		}

		protected function getCache($key)
		{
		}
		protected function setCache($key, $cache)
		{
		}
		protected function unsetCache($key)
		{
		}
		protected function incrementCache($key)
		{
		}

		public function get($cachePath)
		{
			$key = $this->keySerialize($cachePath);
			if ($cache = $this->getCache($key)) {

				/* @todo Check if parent is correct, or add new parent */

				return $cache['content'];
			}
			return null;
		}


		public function start($cachePath, $relations = [])
		{
			$this->stack[] = [
				'key' => $this->keySerialize($cachePath),
				'relations' => $relations,
			];
		}

		public function end($content)
		{
			$cache = array_pop($this->stack);
			$cache['content'] = $content;

			// Check parent
			if ($parent = end($this->stack)) {
				$cache['parents'] = [$parent['key']];
			}

			$this->setCache($cache['key'], $cache);

			// Save relations
			foreach ($cache['relations'] as $relKey => $relVal) {
				$key = $this->keySerialize(['__rel', $relKey, $relVal]);
				$i = $this->incrementCache($key);
				$this->setCache($this->keySerialize(['__data', $relKey, $relVal, $i]), $cache['key']);
			}
		}

		public function set($cachePath, $content, $relations)
		{
			$this->start($cachePath, $relations);
			return $this->end($content);
		}

		protected function keySerialize($key)
		{
			return $this->prefix . implode(':', $key);
		}

		public function invalidate($cachePath)
		{
			$this->invalidateKey($this->keySerialize($cachePath));
		}

		private function invalidateKey($key)
		{
			if ($cache = $this->getCache($key)) {
				$parents = $cache['parents'] ?? [];
				$this->unsetCache($key);
				foreach ($parents as $parent) {
					$this->invalidateKey($parent);
				}
			}
		}

		public function invalidateRelated($relations)
		{
			foreach ($relations as $relKey => $relVal) {
				$countKey = $this->keySerialize(['__rel', $relKey, $relVal]);
				$count = (int)$this->getCache($countKey);
				for ($i = 1; $i <= $count; $i++) {
					$k = $this->keySerialize(['__data', $relKey, $relVal, $i]);
					if ($rel = $this->getCache($k)) {
						$this->invalidateKey($rel);
						$this->unsetCache($k);
					}
				}
				$this->unsetCache($countKey);
			}
		}
	}
}
