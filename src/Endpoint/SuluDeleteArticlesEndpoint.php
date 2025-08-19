<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteArticlesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-delete-articles-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/articles.{_format}';
}
