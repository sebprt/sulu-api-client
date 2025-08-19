<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetPreviewLinkEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-preview-get-preview-link-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/preview-links/{resourceId}.{_format}';
}
