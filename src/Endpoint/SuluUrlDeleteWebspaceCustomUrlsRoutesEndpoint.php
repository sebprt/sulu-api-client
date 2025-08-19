<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluUrlDeleteWebspaceCustomUrlsRoutesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-custom-url-delete-webspace-custom-urls-routes-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/custom-urls/{id}/routes.{_format}';
}
