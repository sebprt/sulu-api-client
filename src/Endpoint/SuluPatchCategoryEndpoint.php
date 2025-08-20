<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchCategoryEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
