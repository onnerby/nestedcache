<?php
declare(strict_types=1);

namespace Doe\Tests
{

	use PHPUnit\Framework\TestCase;

	final class NestTest extends TestCase
	{

		private function init()
		{
			return \Doe\NestedCache::init([
//				'engine' => 'tempmemory'
			]);
		}

		public function testInit(): void
		{
			$cache = $this->init();
			$this->assertIsObject(
				$cache
			);
		}

		public function testEmptyCache(): void
		{
			$this->init();
			$cache = \Doe\NestedCache::get(['emptycache', 0]);


			$this->assertNull(
				$cache
			);
		}

		public function testSimpleCache(): void
		{
			$this->init();
			
			\Doe\NestedCache::start(['mycache', 1]);
			$saveToCache = "stuff" . uniqid();	
			\Doe\NestedCache::end($saveToCache);

			$cache = \Doe\NestedCache::get(['mycache', 1]);

			$this->assertSame(
				$cache,
				$saveToCache
			);
		}


		public function testNestedCache(): void
		{
			$this->init();

			$parentCache = "stuff" . uniqid();
			\Doe\NestedCache::start(['mycache', 2]);

			// Nested cache
			\Doe\NestedCache::start(['mycache', 3]);
			$childCache = "child" . uniqid();
			$parentCache .= $childCache;
			\Doe\NestedCache::end($childCache);

			\Doe\NestedCache::end($parentCache);

			$parentTest = \Doe\NestedCache::get(['mycache', 2]);
			$childTest = \Doe\NestedCache::get(['mycache', 3]);

			$this->assertSame(
				$parentCache,
				$parentTest
			);
			$this->assertSame(
				$childCache,
				$childTest
			);
		}

		public function testRewriteCache(): void
		{
			$this->init();

			$content = "stuff" . uniqid();
			\Doe\NestedCache::set(['mycache', 4], 'Temporary');
			\Doe\NestedCache::set(['mycache', 4], $content);

			$contentTest = \Doe\NestedCache::get(['mycache', 4]);

			$this->assertSame(
				$content,
				$contentTest
			);
		}


		public function testInvalidateCache(): void
		{
			$this->init();

			$content = "stuff" . uniqid();
			\Doe\NestedCache::set(['mycache', 5], $content);

			$contentTest = \Doe\NestedCache::get(['mycache', 5]);
			\Doe\NestedCache::invalidate(['mycache', 5]);
			$invalidContentTest = \Doe\NestedCache::get(['mycache', 5]);

			$this->assertSame(
				$content,
				$contentTest
			);
			$this->assertNull(
				$invalidContentTest
			);
		}

		public function testInvalidateNestedCache(): void
		{
			$this->init();

			$parentCache = "stuff" . uniqid();
			\Doe\NestedCache::start(['mycache', 6]);

			// Nested cache
			\Doe\NestedCache::start(['mycache', 7]);
			$childCache = "child" . uniqid();
			$parentCache .= $childCache;
			\Doe\NestedCache::end($childCache);

			\Doe\NestedCache::end($parentCache);

			$parentTest = \Doe\NestedCache::get(['mycache', 6]);
			\Doe\NestedCache::invalidate(['mycache', 7]);
			$invalidParentTest = \Doe\NestedCache::get(['mycache', 6]);

			$this->assertSame(
				$parentCache,
				$parentTest
			);
			$this->assertNull(
				$invalidParentTest
			);
		}

		public function testInvalidateRelations(): void
		{
			$this->init();

			$content = "stuff" . uniqid();
			\Doe\NestedCache::set(['mycache', 8], $content, ['myobject' => 44, 'secondrel' => 55]);
			$contentTest = \Doe\NestedCache::get(['mycache', 8]);

			\Doe\NestedCache::invalidateRelated(['secondrel' => 55]);
			$invalidContentTest = \Doe\NestedCache::get(['mycache', 8]);

			$this->assertSame(
				$content,
				$contentTest
			);
			$this->assertNull(
				$invalidContentTest
			);
		}

		public function testInvalidateNestedRelationsCache(): void
		{
			$this->init();

			$parentCache = "stuff" . uniqid();
			\Doe\NestedCache::start(['mycache', 16], ['myobject' => 77, 'secondrel' => 88]);

			// Nested cache
			\Doe\NestedCache::start(['mycache', 17]);
			$childCache = "child" . uniqid();
			$parentCache .= $childCache;
			\Doe\NestedCache::end($childCache);

			\Doe\NestedCache::end($parentCache);

			$parentTest = \Doe\NestedCache::get(['mycache', 16]);
			\Doe\NestedCache::invalidateRelated(['secondrel' => 88]);
			$invalidParentTest = \Doe\NestedCache::get(['mycache', 16]);

			$this->assertSame(
				$parentCache,
				$parentTest
			);
			$this->assertNull(
				$invalidParentTest
			);
		}

	}
}