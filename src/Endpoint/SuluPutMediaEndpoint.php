<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutMediaEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-put-media-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}.{_format}';
}
