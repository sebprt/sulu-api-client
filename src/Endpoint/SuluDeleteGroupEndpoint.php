<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteGroupEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/groups/{id}.{_format}';
}
