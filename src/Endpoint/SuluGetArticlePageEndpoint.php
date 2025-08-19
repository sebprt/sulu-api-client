<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetArticlePageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-get-article-page-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/articles/{articleUuid}/pages/{uuid}.{_format}';
}
