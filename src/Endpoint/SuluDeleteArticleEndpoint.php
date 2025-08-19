<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteArticleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-delete-article-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/articles/{id}.{_format}';
}
