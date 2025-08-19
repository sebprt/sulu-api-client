<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteArticlePageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-delete-article-page-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/articles/{articleUuid}/pages/{uuid}.{_format}';
}
