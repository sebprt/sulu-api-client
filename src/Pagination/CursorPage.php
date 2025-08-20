<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Pagination;

/**
 * @template T
 */
final class CursorPage
{
    /**
     * @param list<T>     $items
     * @param string|null $nextCursor the cursor for the next page; null indicates the end
     */
    public function __construct(
        public readonly array $items,
        public readonly ?string $nextCursor,
    ) {
    }
}
