<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetRoutesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-routes-get-routes-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/routes.{_format}';
}
