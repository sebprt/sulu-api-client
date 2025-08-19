<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetUserEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-user-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/users/{id}.{_format}';
}
