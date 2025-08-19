<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteGroupEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-delete-group-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/groups/{id}.{_format}';
}
