<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint\Helper;

interface ContentTypeMatcherInterface
{
    public function isJson(string $contentType): bool;
}
