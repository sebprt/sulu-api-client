<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Pagination;

use IteratorAggregate;
use Traversable;

/**
 * @template T
 * @implements IteratorAggregate<T>
 */
final class Paginator implements IteratorAggregate
{
    /**
     * @param callable(int $page, int $limit): Page<T> $pageFetcher
     */
    /** @var callable(int, int): Page<T> */
    private $pageFetcher;

    public function __construct(
        private readonly int $limit,
        callable $pageFetcher
    ) {
        $this->pageFetcher = $pageFetcher;
    }

    public function getIterator(): Traversable
    {
        $page = 1;
        while (true) {
            /** @var Page<T> $result */
            $result = ($this->pageFetcher)($page, $this->limit);
            foreach ($result->items as $item) {
                yield $item;
            }
            if ($result->total !== null) {
                $maxPage = (int)ceil($result->total / $this->limit);
                if ($page >= $maxPage) {
                    break;
                }
            } elseif (count($result->items) < $this->limit) {
                break;
            }
            $page++;
        }
    }
}
