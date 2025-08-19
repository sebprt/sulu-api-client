<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetArticleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-get-article-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/articles/{id}.{_format}';
}
