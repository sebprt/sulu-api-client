<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteCategoryKeywordsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-delete-category-keywords-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/categories/{categoryId}/keywords.{_format}';
}
