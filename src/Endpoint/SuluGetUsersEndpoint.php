<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetUsersEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-users-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/users.{_format}';
}
