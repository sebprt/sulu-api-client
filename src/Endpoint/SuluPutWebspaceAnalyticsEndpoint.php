<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutWebspaceAnalyticsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-website-put-webspace-analytics-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/analytics/{id}.{_format}';
}
