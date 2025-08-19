<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetWebspaceEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-webspace-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspaceKey}.{_format}';
}
