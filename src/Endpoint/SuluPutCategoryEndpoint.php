<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutCategoryEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-put-category-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
