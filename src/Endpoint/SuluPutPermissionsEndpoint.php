<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutPermissionsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-put-permissions-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/permissions.{_format}';
}
