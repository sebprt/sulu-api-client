<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetPageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-page-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/pages/{id}.{_format}';
}
