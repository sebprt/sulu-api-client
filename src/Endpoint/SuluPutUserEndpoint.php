<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutUserEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/users/{id}.{_format}';
}
