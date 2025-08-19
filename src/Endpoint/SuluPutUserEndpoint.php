<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutUserEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-put-user-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/users/{id}.{_format}';
}
