<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetCategoryKeywordEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-get-category-keyword-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/categories/{categoryId}/keywords/{id}.{_format}';
}
