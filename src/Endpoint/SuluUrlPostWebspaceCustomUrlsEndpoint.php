<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluUrlPostWebspaceCustomUrlsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-custom-url-post-webspace-custom-urls-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/webspaces/{webspace}/custom-urls.{_format}';
}
