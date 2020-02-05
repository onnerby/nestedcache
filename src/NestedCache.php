<?php
namespace Doe
{
	class NestedCache
	{
		private static $instance = null;

		/**
		 * Initialize NestedCache
		 * Options:
		 *    engine: can be "APCu" or "tempmemory" (fallback if no match)
		 *    prefix: What to prefix all keys in cache. Usefull if you have many systems on same server.
		 *
		 * @param array $options
		 * @return \Doe\NestedCache\Base cache instance returned mostly for debug purpose
		 */
		public static function init(array $options = [])
		{
			$options = array_merge([
				'engine' => 'APCu',
			], $options);

			switch ($options['engine']) {
				case 'APCu':
					return self::$instance = new NestedCache\APCu($options);
			}
			return self::$instance = new NestedCache\TempMemory($options);
		}

		/**
		 * Get cache from "cachepath"
		 *
		 * @param array $cachePath
		 * @return mixed cache or NULL
		 */
		public static function get(array $cachePath)
		{
			return self::$instance->get($cachePath);
		}

		/**
		 * Start the current nested cache on the stack
		 *
		 * @param array $cachePath
		 * @param array $relations (optional). Key/val pairs with relations
		 */
		public static function start(array $cachePath, array $relations = []) : void
		{
			self::$instance->start($cachePath, $relations);
		}

		/**
		 * Start output buffering the current nested cache on the stack
		 *
		 * @param array $cachePath
		 * @param array $relations (optional). Key/val pairs with relations
		 */
		public static function obStart(array $cachePath, array $relations = []) : void
		{
			ob_start();
			self::$instance->start($cachePath, $relations);
		}

		/**
		 * End output buffering the current nested cache
		 *
		 * @return string The output buffer
		 */
		public static function obEnd() : string
		{
			$content = ob_get_clean();
			self::$instance->end($content);
			return $content;
		}

		/**
		 * End output buffering the current nested cache
		 *
		 * @param mixed $content Content to set in cache
		 */
		public static function end($content) : void
		{
			self::$instance->end($content);
		}

		/**
		 * Set cache
		 *
		 * @param array $cachePath
		 * @param mixed $content Content to set in cache
		 * @param array $relations (optional). Key/val pairs with relations
		 * @return \Doe\NestedCache\Base instance for chaining
		 */
		public static function set(array $cachePath, $content, array $relations = []) : void
		{
			self::$instance->set($cachePath, $content, $relations);
		}

		/**
		 * Invalidate a specific cachePath
		 *
		 * @param array $cachePath
		 */
		public static function invalidate(array $cachePath) : void
		{
			self::$instance->invalidate($cachePath);
		}

		/**
		 * Invalidate relation data
		 *
		 * @param array $relations
		 */
		public static function invalidateRelated(array $relations) : void
		{
			self::$instance->invalidateRelated($relations);
		}
	}
}
