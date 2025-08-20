<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutCategoryEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
