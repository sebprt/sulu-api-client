<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluUrlGetWebspaceCustomUrlsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-custom-url-get-webspace-custom-urls-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/custom-urls/{id}.{_format}';
}
