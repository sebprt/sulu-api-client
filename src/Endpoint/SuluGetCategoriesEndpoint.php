<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetCategoriesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-get-categories-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/categories.{_format}';
}
