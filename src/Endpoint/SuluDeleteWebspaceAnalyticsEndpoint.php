<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteWebspaceAnalyticsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-website-cdelete-webspace-analytics-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/analytics.{_format}';
}
