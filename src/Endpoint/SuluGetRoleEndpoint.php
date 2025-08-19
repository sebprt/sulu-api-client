<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetRoleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-role-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/roles/{id}.{_format}';
}
