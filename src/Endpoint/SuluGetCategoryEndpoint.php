<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetCategoryEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-get-category-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
