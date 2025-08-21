<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Endpoint\Helper;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Endpoint\Helper\DefaultContentTypeMatcher;

final class HelpersTest extends TestCase
{
    public function testContentTypeMatcher(): void
    {
        $m = new DefaultContentTypeMatcher();
        self::assertTrue($m->isJson('application/json'));
        self::assertTrue($m->isJson('application/problem+json; charset=utf-8'));
        self::assertTrue($m->isJson('application/vnd.api+json'));
        self::assertFalse($m->isJson('text/plain'));
    }

}
