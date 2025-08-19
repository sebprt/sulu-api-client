<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutArticlePageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-put-article-page-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/articles/{articleUuid}/pages/{uuid}.{_format}';
}
