<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutMediaFormatEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}/formats/{key}.{_format}';
}
