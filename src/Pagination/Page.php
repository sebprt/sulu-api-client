<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Pagination;

/**
 * @template T
 */
final class Page
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $page,
        public readonly int $limit,
        public readonly ?int $total = null,
    ) {
    }
}
