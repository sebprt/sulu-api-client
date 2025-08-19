<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetArticlesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-get-articles-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/articles.{_format}';
}
