<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteMediaVersionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-delete-media-version-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}/versions/{version}.{_format}';
}
