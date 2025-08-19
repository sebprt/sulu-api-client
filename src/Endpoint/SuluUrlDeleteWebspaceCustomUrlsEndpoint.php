<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluUrlDeleteWebspaceCustomUrlsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-custom-url-delete-webspace-custom-urls-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/custom-urls/{id}.{_format}';
}
