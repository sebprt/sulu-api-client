<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetItemsEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/items.{_format}';
}
