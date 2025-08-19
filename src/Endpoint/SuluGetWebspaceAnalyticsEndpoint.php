<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetWebspaceAnalyticsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-website-cget-webspace-analytics-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/analytics.{_format}';
}
