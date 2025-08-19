<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutMediaFormatEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-put-media-format-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}/formats/{key}.{_format}';
}
