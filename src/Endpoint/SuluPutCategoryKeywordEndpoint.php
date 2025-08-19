<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutCategoryKeywordEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/categories/{categoryId}/keywords/{id}.{_format}';
}
