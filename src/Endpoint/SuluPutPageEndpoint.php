<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutPageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-put-page-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/pages/{id}.{_format}';
}
