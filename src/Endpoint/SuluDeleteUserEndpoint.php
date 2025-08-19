<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteUserEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-delete-user-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/users/{id}.{_format}';
}
