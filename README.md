# NestedCache

A nested cache that can be invalidated through relations and/or hierarchy

Lets start with an simple example:
```php
\Doe\NestedCache::init();

if ($cache = \Doe\NestedCache::get(['book-gallery'])) {
	// Do stuff with book gallery cache
} else {
	\Doe\NestedCache::start(['book-gallery']);
	$newCache = 'Lots of generated data';
	...
	\Doe\NestedCache::end($newCache);
}

// Remove the cache
\Doe\NestedCache::invalidate(['book-gallery']);

```

## How about nesting?
Let's try nesting our book gallery by caching books inside
```php
\Doe\NestedCache::init();

if ($cache = \Doe\NestedCache::get(['book-gallery'])) {
	// Do stuff with book gallery cache
} else {
	\Doe\NestedCache::start(['book-gallery']);
	$newCache = "";
	foreach ($books as $book) {
		if (!($bookOutput = \Doe\NestedCache::get(['book-listing', $book->id]))) {
			$bookOutput = "book: " . $book->name . "\n";
			\Doe\NestedCache::set(['book-listing', $book->id], $bookOutput);
		}
		$newCache .= $bookOutput;
	}

	\Doe\NestedCache::end($newCache);
}

// Remove the cache for a single book will also remove cache for "book-gallery" (but not for other book listings)
\Doe\NestedCache::invalidate(['book-listing', 123]);
```

## Engines
There are so far only 2 options.
 - APCu: (default) `\Doe\NestedCache::init(['engine' => 'APCu']);` 
 - TempMemory: A in memory cache only for current request (mostly for debug purpose) `\Doe\NestedCache::init(['engine' => 'TempMemory']);`



