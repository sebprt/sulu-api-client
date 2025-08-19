<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetGroupEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-group-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/groups/{id}.{_format}';
}
