<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetWebspacesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-webspaces-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/webspaces.{_format}';
}
