<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutGroupEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-put-group-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/groups/{id}.{_format}';
}
