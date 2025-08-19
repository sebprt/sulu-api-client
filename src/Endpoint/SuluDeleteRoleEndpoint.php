<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteRoleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-delete-role-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/roles/{id}.{_format}';
}
