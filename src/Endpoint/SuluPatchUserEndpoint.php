<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchUserEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-patch-user-patch';

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/users/{id}.{_format}';
}
