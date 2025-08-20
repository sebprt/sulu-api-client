<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Pagination\CursorPage;
use Sulu\ApiClient\Pagination\CursorPaginator;

final class CursorPaginatorTest extends TestCase
{
    public function testCursorPaginatorBasicFlow(): void
    {
        $pages = [
            [1, 2, 3],
            [4, 5, 6],
            [],
        ];
        $cursors = [null, 'c2', 'c3'];
        $idx = 0;
        $fetcher = function (?string $cursor, int $limit) use (&$idx, $pages, $cursors): CursorPage {
            $items = $pages[$idx] ?? [];
            $next = ($idx < count($pages) - 1) ? $cursors[$idx + 1] : null;
            ++$idx;

            return new CursorPage($items, $next);
        };

        $p = new CursorPaginator(3, $fetcher);
        $items = iterator_to_array($p);

        self::assertSame([1, 2, 3, 4, 5, 6], $items);
    }

    public function testInvalidLimitThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new CursorPaginator(0, function (?string $cursor, int $limit): CursorPage {
            return new CursorPage([], null);
        });
    }
}
