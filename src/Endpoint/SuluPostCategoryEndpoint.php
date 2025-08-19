<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostCategoryEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-post-category-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/categories.{_format}';
}
