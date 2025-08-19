<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetPermissionsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-permissions-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/permissions.{_format}';
}
