<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluUrlPutWebspaceCustomUrlsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-custom-url-put-webspace-custom-urls-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/custom-urls/{id}.{_format}';
}
