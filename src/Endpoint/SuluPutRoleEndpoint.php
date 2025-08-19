<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutRoleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-put-role-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/roles/{id}.{_format}';
}
