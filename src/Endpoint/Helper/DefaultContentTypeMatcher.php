<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint\Helper;

final class DefaultContentTypeMatcher implements ContentTypeMatcherInterface
{
    public function isJson(string $contentType): bool
    {
        $media = strtolower(trim(explode(';', $contentType)[0]));

        return 'application/json' === $media
            || 'application/problem+json' === $media
            || str_ends_with($media, '+json');
    }
}
