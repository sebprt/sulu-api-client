<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutArticleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-put-article-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/articles/{id}.{_format}';
}
