<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteCategoryKeywordEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-delete-category-keyword-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/categories/{categoryId}/keywords/{id}.{_format}';
}
