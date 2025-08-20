<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Pagination;

/**
 * Cursor-based paginator that uses opaque cursors provided by the API.
 *
 * @template T
 *
 * @implements \IteratorAggregate<T>
 */
final class CursorPaginator implements \IteratorAggregate
{
    /**
     * @param callable(?string $cursor, int $limit): CursorPage<T> $pageFetcher
     */
    /** @var callable(?string, int): CursorPage<T> */
    private $pageFetcher;

    public function __construct(
        private readonly int $limit,
        callable $pageFetcher,
        private readonly ?string $initialCursor = null,
    ) {
        if ($this->limit <= 0) {
            throw new \InvalidArgumentException('CursorPaginator limit must be a positive integer.');
        }
        $this->pageFetcher = $pageFetcher;
    }

    public function getIterator(): \Traversable
    {
        $cursor = $this->initialCursor;
        while (true) {
            /** @var CursorPage<T> $page */
            $page = ($this->pageFetcher)($cursor, $this->limit);

            foreach ($page->items as $item) {
                yield $item;
            }

            if (null === $page->nextCursor) {
                break;
            }


            $cursor = $page->nextCursor;
        }
    }
}
