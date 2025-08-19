<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchCategoryEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-patch-category-patch';

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
