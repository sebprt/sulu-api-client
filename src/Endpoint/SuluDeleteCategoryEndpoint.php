<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteCategoryEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
