<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluUrlCgetWebspaceCustomUrlsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-custom-url-cget-webspace-custom-urls-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/custom-urls.{_format}';
}
