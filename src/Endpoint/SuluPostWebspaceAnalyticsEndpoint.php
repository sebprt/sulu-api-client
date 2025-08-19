<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostWebspaceAnalyticsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-website-post-webspace-analytics-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/analytics.{_format}';
}
