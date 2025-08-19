<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostCategoryKeywordEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-post-category-keyword-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/categories/{categoryId}/keywords.{_format}';
}
