<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutWebspaceAnalyticsEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/analytics/{id}.{_format}';
}
